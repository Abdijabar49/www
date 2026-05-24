# GBP/USD 24/7 Buy/Sell Probability — TradingView source

A two-piece kit that produces a live, 24/7 buy-vs-sell probability on
GBP/USD inside TradingView, blending the technical signals TradingView
can compute natively with a fundamental-news score derived from the
sources listed below.

```
   ┌────────────────────────────┐         ┌─────────────────────────────┐
   │  news_sentiment.py         │         │  gbpusd_probability.pine    │
   │  (Python, GH Actions)      │ ──────▶ │  (Pine Script v5, on TV)    │
   │                            │  score  │                             │
   │  scrapes 11 RSS feeds      │  -1..+1 │  trend + momentum + DXY +   │
   │  → latest.json             │         │  yields + vol + news        │
   └────────────────────────────┘         │  → Buy% / Sell%             │
                                          └─────────────────────────────┘
```

## Why two pieces?

TradingView's Pine Script sandbox **cannot make outbound HTTP requests**,
so it cannot scrape Reuters, ForexFactory, FXStreet, etc. directly. The
honest 24/7 architecture is therefore:

1. A small Python service does the scraping + sentiment scoring on a cron
   (GitHub Action included). Output is a single `score` in [-1, +1].
2. The Pine Script does everything else live in TradingView and folds the
   external news score into the final probability.

There are three ways to bridge the score into the Pine indicator —
listed from simplest to most automated — see [Bridging the news score](#bridging-the-news-score) below.

## Files

| File | Purpose |
|---|---|
| `gbpusd_probability.pine` | Pine Script v5 indicator. Plots Buy% / Sell% lines, status table, and emits 3 alert conditions. |
| `news_sentiment.py` | Python 3.10+ stdlib-only news aggregator. Pulls RSS, scores headlines, writes `latest.json`. |
| `requirements.txt` | Empty by default (stdlib only). Lists optional upgrades (feedparser, FinBERT, etc.). |
| `.github/workflows/news-sentiment.yml` | Hourly GitHub Action that runs the scraper and commits `latest.json`. |

## Sources scraped

The Python service pulls these feeds, all from the list you specified:

- [Forex Factory](https://www.forexfactory.com) — `/news.rss`
- [Investing.com](https://www.investing.com) — forex news RSS (id 285)
- [DailyFX](https://www.dailyfx.com) — `/feeds/market-news`
- [FXStreet](https://www.fxstreet.com) — `/rss/news`
- [Reuters](https://www.reuters.com) — business news RSS (forex section is JS-only)
- [Bloomberg](https://www.bloomberg.com) — markets news RSS
- [Trading Economics](https://tradingeconomics.com) — forex RSS
- [Action Forex](https://www.actionforex.com) — site feed
- [Kitco](https://www.kitco.com) — Kitco News RSS
- [MarketWatch](https://www.marketwatch.com) — market pulse RSS
- [Myfxbook](https://www.myfxbook.com) — forex news RSS

A few of those (Bloomberg, Reuters forex) are partly paywalled or
JS-rendered; the script will still pick up the public RSS items and
log a warning for the rest.

## Quick start

### 1. Install the indicator

1. In TradingView, open Pine Editor.
2. Paste the contents of `gbpusd_probability.pine`.
3. Click **Save**, then **Add to chart**.
4. Open a GBP/USD chart on any timeframe. The indicator panel shows
   Buy% / Sell% lines and a status table in the top-right.

### 2. Run the news service locally

```bash
cd tradingview-gbpusd
python news_sentiment.py --out latest.json
```

`latest.json` contains:

```json
{
  "timestamp": "2026-05-24T13:00:00+00:00",
  "score": -0.18,
  "buy_probability": 41.0,
  "sell_probability": 59.0,
  "sources_used": ["fxstreet", "investing", "reuters_business", "..."],
  "matched_count": 12,
  "headlines": [ ... ]
}
```

### 3. Run it 24/7

#### Option A — GitHub Actions (included)

The bundled workflow runs every hour, scrapes, and commits the updated
`latest.json` back to the branch. It picks up an optional secret
`NEWS_WEBHOOK` to post a summary to Discord / Slack.

To enable:

1. Merge this PR.
2. Settings → Actions → General → "Workflow permissions" → **Read and write**.
3. Optionally add a `NEWS_WEBHOOK` repo secret.

#### Option B — cron on a VPS

```cron
0 * * * * cd /opt/gbpusd-news && /usr/bin/python3 news_sentiment.py \
          --out /var/www/html/latest.json
```

## Bridging the news score

The Pine indicator has an `External news score` numeric input
(default `0.0`, range `-1..+1`). You feed the score from `latest.json`
into it. Three options, in order of effort:

1. **Manual** — Open the indicator settings once a day, paste the value.
   Good enough for swing trading.
2. **Browser script** — A small Tampermonkey / userscript on
   `tradingview.com` polls the raw URL of `latest.json` from your
   GitHub repo and updates the input field via the indicator's
   settings dialog. Sample skeleton at the bottom of this README.
3. **Pine + alert webhook bot** — A self-hosted bridge (e.g. small
   Node/Python service) takes the score and triggers a TradingView
   alert with the value embedded; PineConnector or 3Commas-style
   bots can then act on it.

## Methodology — what the probability means

The Pine indicator combines the following components, each normalised to
`[-1, +1]` and weighted (defaults shown):

| Component | Weight | Bullish if … |
|---|---:|---|
| Trend (D1 EMAs 20/50/200) | 0.25 | Price > EMAs and EMAs aligned upward |
| Momentum (RSI14, MACD) | 0.20 | RSI > 50 and MACD line above signal |
| DXY divergence (TVC:DXY) | 0.20 | DXY falling over last 20 bars |
| UK-US 10Y yield spread | 0.15 | Spread widening over last 10 bars |
| Volatility regime (ATR ratio) | 0.05 | Calm regime adds small bullish bias |
| External news score | 0.15 | Provided by `news_sentiment.py` |

The weighted sum is multiplied by a session-liquidity factor (0.6
outside London/NY, 1.0 during the overlap) and mapped onto
`Buy% = 50 + score * 50`, `Sell% = 100 − Buy%`.

This is a transparent heuristic, **not a prediction**. The probability
is a directional bias score, not a statistical claim about price.

## Alerts

Three alert conditions are emitted by the Pine script:

1. `Buy bias activated` — Buy% crosses above the buy zone (default 60%).
2. `Sell bias activated` — Buy% crosses below the sell zone (default 40%).
3. `Bias flip (50% line cross)` — neutral line cross, useful for grid bots.

Right-click the indicator → **Add alert** → choose any of the above.

## Limitations & honest caveats

- **TradingView cannot reach the news sites directly.** The two-piece
  architecture is the honest workaround.
- The bundled lexicon is intentionally simple; a transformer-based
  sentiment model (FinBERT, Claude, etc.) will give better results —
  override `score_headline()` and add the dependency to `requirements.txt`.
- Reuters / Bloomberg public RSS feeds are general business news,
  not GBP-specific. They contribute fewer matched items than
  FXStreet / Action Forex / DailyFX.
- This is a decision-support tool; do not run it on live capital
  without your own backtest and risk management.

## Optional Tampermonkey skeleton

```js
// ==UserScript==
// @name         GBPUSD news bridge
// @match        https://*.tradingview.com/*
// @grant        GM_xmlhttpRequest
// @connect      raw.githubusercontent.com
// ==/UserScript==
(function () {
  const URL = "https://raw.githubusercontent.com/<owner>/<repo>/<branch>/tradingview-gbpusd/latest.json";
  setInterval(() => {
    GM_xmlhttpRequest({
      method: "GET", url: URL,
      onload: r => {
        try {
          const { score } = JSON.parse(r.responseText);
          window.__gbpusdNewsScore = score;
          console.log("GBPUSD news score", score);
        } catch (e) { console.warn(e); }
      },
    });
  }, 5 * 60 * 1000);
})();
```

The script just exposes the latest score on `window.__gbpusdNewsScore`;
plug it into your own DOM-poke code to update the indicator's input
field as you prefer.
