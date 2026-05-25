# GBP/USD Buy/Sell Signal Tool

A small, dependency-free PHP tool that pulls live GBP/USD market data and recent headlines from your specified sources and outputs a **buy vs. sell probability for the next ~24 hours**.

## What it does

1. **Price feed** — pulls the daily GBP/USD CSV from [Stooq](https://stooq.com/) (free, no API key) and computes the latest close, day/week/month change, 20-EMA, 50-EMA, and ATR(14).
2. **News feeds** — fetches RSS feeds from FXStreet, Investing.com, DailyFX, Action Forex, Reuters, MarketWatch, plus Bloomberg / Trading Economics / Kitco / Forex Factory via Google News RSS, plus the Myfxbook crowd sentiment page.
3. **Scoring** — each headline is scored against a bullish/bearish lexicon (e.g., "rate hike", "rallies", "fiscal stress", "dollar surges"). Recent items get a recency boost. Myfxbook crowd positioning is read contrarian (heavy short = mild bullish).
4. **Output** — combines a **momentum** component (price vs. 20/50-EMA + day return) and a **sentiment** component (weighted average of scored items) into a final 20%–80% buy probability with a labelled verdict (`BUY (strong)` / `BUY (moderate)` / `NEUTRAL` / `SELL (moderate)` / `SELL (strong)`).

## Usage

### CLI

```bash
php tools/forex/gbpusd_signal.php                   # plain text
php tools/forex/gbpusd_signal.php --format=json     # JSON
php tools/forex/gbpusd_signal.php --format=html > /tmp/signal.html
```

### Browser

Drop the project on a PHP-capable web server (you already serve PHP from this repo) and visit:

```
/tools/forex/gbpusd_signal.php           → HTML report (default)
/tools/forex/gbpusd_signal.php?format=json
/tools/forex/gbpusd_signal.php?format=text
```

### Cron (recommended for the "London open refresh")

To get a fresh signal every weekday at 06:55 UTC (just before the London open):

```cron
55 6 * * 1-5 /usr/bin/php /var/www/tools/forex/gbpusd_signal.php --format=json > /var/www/tools/forex/cache/latest.json
```

## Files

| File | Purpose |
|---|---|
| `gbpusd_signal.php` | Entry point (CLI + web). |
| `sources.json` | Editable list of feeds + bullish/bearish lexicon. |
| `lib/Fetcher.php` | cURL wrapper with timeout, retry, UA. |
| `lib/PriceFeed.php` | Stooq CSV → OHLC + EMA/ATR. |
| `lib/NewsFeed.php` | RSS / HTML / Myfxbook sentiment parser + headline scorer. |
| `lib/Scorer.php` | Combines momentum + sentiment → buy probability. |
| `lib/Report.php` | Renders text, HTML, JSON. |
| `cache/` | Optional output cache (used by cron example). |

## Customising

- **Add a source:** append to `sources.json → feeds`. Set `type: "rss"` (preferred) or `type: "html"`. `filter` is a regex applied to the title; use `.*` to keep all entries.
- **Tune the scoring:** edit the keyword arrays in `sources.json → lexicon.bullish_gbp` / `lexicon.bearish_gbp`. Or edit the weights in `lib/Scorer.php` (`0.55 sentiment + 0.45 momentum`, `+/- 0.25` for EMA20 bias, `+/- 0.15` for EMA50 bias, etc.).
- **Adjust horizon:** the time window for "fresh" headlines is in `NewsFeed::fetch()` (`$cutoff = time() - 72*3600`). Tighten to 24h for a stricter next-day signal.

## Why a free price feed and RSS?

Several of the original sites (Investing.com, DailyFX, Bloomberg, Forex Factory, Myfxbook) actively block plain server-side fetches with 403/Cloudflare challenges. The tool routes around this with:
- **Stooq** for OHLC (no key, cURL-friendly).
- **Native RSS** where the site publishes it (FXStreet, Investing.com, DailyFX, Action Forex, MarketWatch).
- **Google News RSS** as a relay for sites that block direct scraping (Bloomberg, Trading Economics, Kitco, Forex Factory).
- **Myfxbook** is attempted directly; if blocked the tool simply skips that one source and notes it in the feed-status panel.

If you want to upgrade any source to a paid/auth API later, just swap the URL in `sources.json` and (if needed) adjust the parser in `lib/NewsFeed.php`.

## Disclaimer

Informational only. **Not financial advice.** Keyword-based sentiment is a heuristic; major moves are usually triggered by data releases and central-bank communication that benefit from human judgement on top of the machine score.
