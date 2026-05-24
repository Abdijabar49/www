"""
GBP/USD news-sentiment service
==============================

Pulls headlines from the user-specified sources and produces a single
sentiment score in the range [-1, +1] that can be fed back into the
TradingView indicator (see gbpusd_probability.pine, "External news score").

Sources covered (RSS where available, HTML scrape otherwise):

    forexfactory.com         - https://www.forexfactory.com/news.rss
    investing.com            - https://www.investing.com/rss/news_285.rss   (forex)
    dailyfx.com              - https://www.dailyfx.com/feeds/market-news
    fxstreet.com             - https://www.fxstreet.com/rss/news
    reuters.com              - https://feeds.reuters.com/reuters/businessNews
                               (forex section is paywalled / JS-rendered)
    bloomberg.com            - https://feeds.bloomberg.com/markets/news.rss
    tradingeconomics.com     - https://tradingeconomics.com/rss/news.aspx?i=forex
    actionforex.com          - https://www.actionforex.com/feed/
    kitco.com                - https://www.kitco.com/rss/KitcoNews.xml
    marketwatch.com          - https://feeds.content.dowjones.io/public/rss/mw_marketpulse
    myfxbook.com             - https://www.myfxbook.com/rss/forex-news.xml

Scoring approach (intentionally simple and transparent):

  * Filter to items whose title or summary mentions GBP-related, USD-related
    or macro keywords.
  * Apply a small bullish/bearish keyword lexicon. Each match contributes
    +1 / -1, with sign flipped for USD-bullish keywords (because rising USD
    is bearish for cable).
  * Average across all matched items, clamp to [-1, +1].
  * Output a JSON file (latest.json) with score, components, headlines,
    and a timestamp; optionally POST to a webhook (Discord / Slack).

This is *not* a black-box NLP model. It is a transparent, auditable
heuristic. Swap in a real sentiment model (FinBERT, Claude, etc.) by
overriding the score_headline() function.

Run hourly (or more often) on a server, GitHub Action, or cron. Paste
the resulting score into the TradingView indicator's "External news
score" input, or wire it via webhook to a TradingView-compatible bot.
"""

from __future__ import annotations

import argparse
import datetime as dt
import json
import os
import re
import sys
import time
from dataclasses import dataclass, asdict, field
from typing import Iterable
from urllib.parse import urlparse
from xml.etree import ElementTree as ET

import urllib.request
import urllib.error

USER_AGENT = (
    "Mozilla/5.0 (compatible; gbpusd-news-sentiment/1.0; "
    "+https://github.com/Abdijabar49/www)"
)

# ---------------------------------------------------------------------------
# Source list
# ---------------------------------------------------------------------------

SOURCES: dict[str, str] = {
    "forexfactory":      "https://www.forexfactory.com/news.rss",
    "investing":         "https://www.investing.com/rss/news_285.rss",
    "dailyfx":           "https://www.dailyfx.com/feeds/market-news",
    "fxstreet":          "https://www.fxstreet.com/rss/news",
    "reuters_business":  "https://feeds.reuters.com/reuters/businessNews",
    "bloomberg_markets": "https://feeds.bloomberg.com/markets/news.rss",
    "trading_economics": "https://tradingeconomics.com/rss/news.aspx?i=forex",
    "actionforex":       "https://www.actionforex.com/feed/",
    "kitco":             "https://www.kitco.com/rss/KitcoNews.xml",
    "marketwatch":       "https://feeds.content.dowjones.io/public/rss/mw_marketpulse",
    "myfxbook":          "https://www.myfxbook.com/rss/forex-news.xml",
}

# ---------------------------------------------------------------------------
# Lexicon
# ---------------------------------------------------------------------------

GBP_KEYWORDS = (
    "gbp", "pound", "sterling", "cable", "uk ", "british", "boe",
    "bank of england", "gilt", "ftse",
)
USD_KEYWORDS = (
    "usd", "dollar", "greenback", "fed", "fomc", "powell", "u.s.",
    "treasury", "dxy",
)
MACRO_KEYWORDS = (
    "cpi", "inflation", "retail sales", "unemployment", "payrolls",
    "rate", "rates", "hike", "cut", "dovish", "hawkish", "yield",
    "gdp", "pmi", "ppi", "jobless",
)

# Words that lift GBP (or are USD-bearish, which lifts cable).
BULLISH_GBP = {
    # GBP positive
    "beats", "strong", "surge", "rallies", "rally", "rises", "rose",
    "climb", "climbs", "jump", "jumps", "advance", "advances",
    "robust", "boost", "boosts", "outperform", "upbeat",
    "hawkish boe", "boe hawkish", "rate hike", "hawkish bailey",
    "wages rise", "wage growth",
    # USD negative -> cable up
    "dollar slumps", "dollar slides", "dollar falls", "dollar drops",
    "dollar weakens", "weak dollar", "dovish fed", "fed dovish",
    "rate cut bets", "cuts bets", "yields fall",
}
BEARISH_GBP = {
    # GBP negative
    "miss", "weak", "slumps", "slide", "slides", "tumbles", "tumble",
    "falls", "fell", "drops", "drop", "plunges", "plunge",
    "selloff", "sell-off", "downgrade", "downgrades", "recession",
    "contracts", "contract", "shrinks", "warning", "weakest",
    "boe dovish", "dovish boe", "rate cut", "cuts rate", "rate cuts",
    "labour turmoil", "political uncertainty", "starmer pressure",
    # USD positive -> cable down
    "dollar surges", "dollar rallies", "dollar climbs", "dollar jumps",
    "strong dollar", "hawkish fed", "fed hawkish", "yields surge",
    "yields jump", "yields rise", "safe haven", "risk off",
}

# ---------------------------------------------------------------------------
# Data classes
# ---------------------------------------------------------------------------


@dataclass
class Headline:
    source: str
    title: str
    link: str
    published: str
    score: float = 0.0
    matches: list[str] = field(default_factory=list)


@dataclass
class Result:
    timestamp: str
    score: float            # final clamped score in [-1, +1]
    buy_probability: float  # 0..100
    sell_probability: float
    sources_used: list[str]
    matched_count: int
    headlines: list[Headline]


# ---------------------------------------------------------------------------
# Fetch / parse
# ---------------------------------------------------------------------------


def http_get(url: str, timeout: int = 15) -> str | None:
    req = urllib.request.Request(url, headers={"User-Agent": USER_AGENT})
    try:
        with urllib.request.urlopen(req, timeout=timeout) as resp:
            return resp.read().decode("utf-8", errors="replace")
    except (urllib.error.URLError, urllib.error.HTTPError, TimeoutError) as exc:
        print(f"[warn] {urlparse(url).netloc}: {exc}", file=sys.stderr)
        return None


def parse_rss(xml_text: str, source: str) -> list[Headline]:
    """Minimal RSS / Atom parser. Returns headlines with title/link/pubDate."""
    out: list[Headline] = []
    try:
        root = ET.fromstring(xml_text)
    except ET.ParseError as exc:
        print(f"[warn] {source}: parse error {exc}", file=sys.stderr)
        return out

    # Strip namespace from tags for easier matching
    def local(tag: str) -> str:
        return tag.split("}", 1)[-1]

    items: Iterable[ET.Element]
    items = (e for e in root.iter() if local(e.tag) in ("item", "entry"))

    for it in items:
        title = link = pub = ""
        for child in it:
            t = local(child.tag)
            if t == "title" and child.text:
                title = child.text.strip()
            elif t == "link":
                link = (child.text or child.attrib.get("href") or "").strip()
            elif t in ("pubDate", "published", "updated") and child.text:
                pub = child.text.strip()
        if title:
            out.append(Headline(source=source, title=title, link=link, published=pub))
    return out


def fetch_all() -> list[Headline]:
    headlines: list[Headline] = []
    for name, url in SOURCES.items():
        body = http_get(url)
        if not body:
            continue
        items = parse_rss(body, name)
        print(f"[info] {name}: {len(items)} items", file=sys.stderr)
        headlines.extend(items)
        # be polite
        time.sleep(0.3)
    return headlines


# ---------------------------------------------------------------------------
# Scoring
# ---------------------------------------------------------------------------


_RE_SPACES = re.compile(r"\s+")


def _norm(text: str) -> str:
    return _RE_SPACES.sub(" ", text.lower()).strip()


def is_relevant(title: str) -> bool:
    n = _norm(title)
    has_pair = any(k in n for k in GBP_KEYWORDS) or "gbp/usd" in n or "gbpusd" in n
    has_usd  = any(k in n for k in USD_KEYWORDS)
    has_macro = any(k in n for k in MACRO_KEYWORDS)
    # GBP mention alone, OR USD mention plus macro context
    return has_pair or (has_usd and has_macro)


def _kw_pattern(kw: str) -> re.Pattern[str]:
    """Word-boundary pattern that also tolerates phrases of multiple words."""
    parts = [re.escape(p) for p in kw.split()]
    return re.compile(r"\b" + r"\s+".join(parts) + r"\b")


# Sort longest first so multi-word phrases consume their tokens before
# single words can re-match them.
_BULL_PATTERNS = [(_kw_pattern(k), k) for k in sorted(BULLISH_GBP, key=len, reverse=True)]
_BEAR_PATTERNS = [(_kw_pattern(k), k) for k in sorted(BEARISH_GBP, key=len, reverse=True)]


def score_headline(h: Headline) -> Headline:
    n = _norm(h.title)
    s = 0.0
    matches: list[str] = []

    # Greedy non-overlapping pass: replace each match with a marker so a
    # shorter keyword (e.g. "weak") cannot also fire inside a longer one
    # already counted (e.g. "dollar weakens").
    for pat, kw in _BULL_PATTERNS:
        if pat.search(n):
            s += 1
            matches.append("+" + kw)
            n = pat.sub(" \x00 ", n)
    for pat, kw in _BEAR_PATTERNS:
        if pat.search(n):
            s -= 1
            matches.append("-" + kw)
            n = pat.sub(" \x00 ", n)

    h.score = s
    h.matches = matches
    return h


def aggregate(headlines: list[Headline]) -> Result:
    relevant = [score_headline(h) for h in headlines if is_relevant(h.title)]
    matched = [h for h in relevant if h.score != 0]

    if matched:
        avg = sum(h.score for h in matched) / len(matched)
        # Most articles will fire 1-3 keyword matches, so /3 keeps it in range.
        score = max(-1.0, min(1.0, avg / 3.0))
    else:
        score = 0.0

    buy = round(50.0 + score * 50.0, 1)
    sell = round(100.0 - buy, 1)

    return Result(
        timestamp=dt.datetime.now(dt.timezone.utc).isoformat(timespec="seconds"),
        score=round(score, 4),
        buy_probability=buy,
        sell_probability=sell,
        sources_used=sorted({h.source for h in relevant}),
        matched_count=len(matched),
        headlines=relevant[:50],   # cap output
    )


# ---------------------------------------------------------------------------
# Output sinks
# ---------------------------------------------------------------------------


def write_json(result: Result, path: str) -> None:
    payload = {
        **{k: v for k, v in asdict(result).items() if k != "headlines"},
        "headlines": [asdict(h) for h in result.headlines],
    }
    with open(path, "w", encoding="utf-8") as f:
        json.dump(payload, f, indent=2, ensure_ascii=False)
    print(f"[info] wrote {path}", file=sys.stderr)


def post_webhook(result: Result, url: str) -> None:
    bias = "BUY" if result.score > 0.1 else "SELL" if result.score < -0.1 else "NEUTRAL"
    text = (
        f"GBP/USD news bias: *{bias}*\n"
        f"Buy {result.buy_probability}% / Sell {result.sell_probability}%  "
        f"(score {result.score:+.2f}, {result.matched_count} matched headlines, "
        f"{len(result.sources_used)} sources)"
    )
    body = json.dumps({"content": text, "text": text}).encode()
    req = urllib.request.Request(
        url, data=body,
        headers={"Content-Type": "application/json", "User-Agent": USER_AGENT},
    )
    try:
        with urllib.request.urlopen(req, timeout=10) as resp:
            print(f"[info] webhook -> HTTP {resp.status}", file=sys.stderr)
    except (urllib.error.URLError, urllib.error.HTTPError) as exc:
        print(f"[warn] webhook failed: {exc}", file=sys.stderr)


# ---------------------------------------------------------------------------
# CLI
# ---------------------------------------------------------------------------


def build_arg_parser() -> argparse.ArgumentParser:
    p = argparse.ArgumentParser(description="GBP/USD news sentiment aggregator")
    p.add_argument("--out",     default="latest.json", help="output JSON path")
    p.add_argument("--webhook", default=os.environ.get("NEWS_WEBHOOK", ""),
                   help="optional Discord/Slack-compatible webhook URL")
    p.add_argument("--quiet",   action="store_true", help="suppress stdout summary")
    return p


def main(argv: list[str] | None = None) -> int:
    args = build_arg_parser().parse_args(argv)
    headlines = fetch_all()
    result = aggregate(headlines)
    write_json(result, args.out)
    if args.webhook:
        post_webhook(result, args.webhook)
    if not args.quiet:
        print(json.dumps({
            "score":           result.score,
            "buy_probability": result.buy_probability,
            "sell_probability":result.sell_probability,
            "matched":         result.matched_count,
            "sources":         result.sources_used,
            "timestamp":       result.timestamp,
        }, indent=2))
    return 0


if __name__ == "__main__":
    sys.exit(main())
