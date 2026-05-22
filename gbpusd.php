<?php
/**
 * gbpusd.php — Live GBP/USD Buy vs. Sell probability dashboard.
 *
 * Pulls data from gbpusd_api.php (which only consumes the user-approved sources)
 * and refreshes the UI second-by-second. The backend is cached for 10s, so the
 * dashboard stays responsive without overwhelming the source publishers.
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>GBP/USD Live Probability Dashboard</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
<style>
    :root {
        --bg: #0b1220;
        --panel: #121a2b;
        --panel-2: #1a2440;
        --accent: #4ea1ff;
        --buy: #16c784;
        --sell: #ea3943;
        --neutral: #a0a4ad;
        --text: #e6edf3;
        --muted: #8b94a7;
        --border: #1f2a44;
    }
    * { box-sizing: border-box; }
    body {
        background: linear-gradient(180deg, #07101f 0%, #0b1220 100%);
        color: var(--text);
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        margin: 0;
        padding: 0;
        min-height: 100vh;
    }
    .wrap { max-width: 1280px; margin: 0 auto; padding: 24px; }
    .topbar {
        display: flex; align-items: center; justify-content: space-between;
        flex-wrap: wrap; gap: 12px; margin-bottom: 24px;
    }
    .brand { display: flex; align-items: center; gap: 14px; }
    .brand .pair {
        font-size: 28px; font-weight: 700; letter-spacing: 0.5px;
    }
    .brand .sub { color: var(--muted); font-size: 13px; }
    .pulse {
        width: 10px; height: 10px; border-radius: 50%;
        background: var(--buy); box-shadow: 0 0 0 0 rgba(22,199,132,.7);
        animation: pulse 1.6s infinite;
    }
    @keyframes pulse {
        0%   { box-shadow: 0 0 0 0 rgba(22,199,132,.7); }
        70%  { box-shadow: 0 0 0 12px rgba(22,199,132,0); }
        100% { box-shadow: 0 0 0 0 rgba(22,199,132,0); }
    }
    .meta {
        font-size: 12px; color: var(--muted);
        display: flex; align-items: center; gap: 16px; flex-wrap: wrap;
    }
    .meta b { color: var(--text); font-weight: 600; }

    .grid { display: grid; gap: 20px; grid-template-columns: 1fr; }
    @media (min-width: 980px) { .grid { grid-template-columns: 1.1fr 1fr; } }

    .panel {
        background: var(--panel);
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 22px;
    }
    .panel h2 { font-size: 14px; text-transform: uppercase; letter-spacing: 1.2px; color: var(--muted); margin: 0 0 16px; }

    /* Probability hero */
    .hero { display: flex; flex-direction: column; gap: 14px; }
    .verdict {
        display: flex; align-items: baseline; gap: 14px;
    }
    .verdict .label {
        font-size: 38px; font-weight: 800; letter-spacing: 1px;
    }
    .verdict .horizon { color: var(--muted); font-size: 13px; }
    .verdict.buy .label  { color: var(--buy); }
    .verdict.sell .label { color: var(--sell); }
    .verdict.neutral .label { color: var(--neutral); }

    .probrow { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .prob {
        background: var(--panel-2);
        border-radius: 12px;
        padding: 18px;
        border: 1px solid var(--border);
        position: relative; overflow: hidden;
    }
    .prob .name { font-size: 12px; letter-spacing: 1.5px; color: var(--muted); text-transform: uppercase; }
    .prob .pct  { font-size: 44px; font-weight: 800; margin: 4px 0 8px; font-variant-numeric: tabular-nums; }
    .prob.buy  .pct { color: var(--buy); }
    .prob.sell .pct { color: var(--sell); }
    .bar { height: 8px; background: #0a1224; border-radius: 8px; overflow: hidden; }
    .bar > span {
        display: block; height: 100%;
        transition: width 0.8s cubic-bezier(.2,.8,.2,1);
    }
    .prob.buy  .bar > span { background: linear-gradient(90deg, #16c784, #2bd2a0); }
    .prob.sell .bar > span { background: linear-gradient(90deg, #ea3943, #ff6b73); }

    .stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin-top: 6px; }
    .stat {
        background: var(--panel-2); border: 1px solid var(--border);
        border-radius: 10px; padding: 12px; text-align: center;
    }
    .stat .k { font-size: 11px; text-transform: uppercase; color: var(--muted); letter-spacing: 1px; }
    .stat .v { font-size: 22px; font-weight: 700; font-variant-numeric: tabular-nums; }

    /* Sources */
    .sources-list { display: flex; flex-direction: column; gap: 8px; }
    .source-row {
        display: flex; align-items: center; justify-content: space-between;
        background: var(--panel-2); border: 1px solid var(--border);
        border-radius: 10px; padding: 10px 14px; font-size: 13px;
    }
    .source-row .left { display: flex; align-items: center; gap: 10px; }
    .dot { width: 8px; height: 8px; border-radius: 50%; background: var(--neutral); }
    .dot.ok   { background: var(--buy); }
    .dot.fail { background: var(--sell); }
    .source-row .right { color: var(--muted); font-size: 12px; }

    /* Headlines */
    .news { display: flex; flex-direction: column; gap: 10px; max-height: 580px; overflow: auto; }
    .news::-webkit-scrollbar { width: 8px; }
    .news::-webkit-scrollbar-thumb { background: #243352; border-radius: 8px; }
    .item {
        background: var(--panel-2); border: 1px solid var(--border);
        border-radius: 10px; padding: 12px 14px;
        border-left: 3px solid var(--neutral);
    }
    .item.bullish { border-left-color: var(--buy); }
    .item.bearish { border-left-color: var(--sell); }
    .item a { color: var(--text); text-decoration: none; font-weight: 600; line-height: 1.35; display: block; }
    .item a:hover { color: var(--accent); }
    .item .meta-row {
        display: flex; align-items: center; gap: 10px;
        margin-top: 6px; font-size: 11px; color: var(--muted);
        flex-wrap: wrap;
    }
    .badge { padding: 2px 8px; border-radius: 999px; font-size: 10px; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase; }
    .badge.bullish { background: rgba(22,199,132,.15); color: var(--buy); }
    .badge.bearish { background: rgba(234,57,67,.15); color: var(--sell); }
    .badge.neutral { background: rgba(160,164,173,.15); color: var(--neutral); }
    .badge.source  { background: rgba(78,161,255,.15); color: var(--accent); }

    .empty { color: var(--muted); font-size: 13px; padding: 18px; text-align: center; }
    .footer-note { color: var(--muted); font-size: 11px; margin-top: 18px; text-align: center; }
    .err { color: var(--sell); font-size: 12px; }

    .countdown { font-variant-numeric: tabular-nums; }
</style>
</head>
<body>
<div class="wrap">

    <div class="topbar">
        <div class="brand">
            <div class="pulse" id="pulseDot"></div>
            <div>
                <div class="pair">GBP / USD</div>
                <div class="sub">Live sentiment-based probability dashboard &middot; 24h horizon</div>
            </div>
        </div>
        <div class="meta">
            <span>Last fetch: <b id="lastFetch">--:--:--</b></span>
            <span>Next refresh in <b class="countdown" id="countdown">1s</b></span>
            <span>Page time: <b id="clock">--:--:--</b></span>
        </div>
    </div>

    <div class="grid">

        <div class="panel hero">
            <h2>Probability &mdash; next 24 hours</h2>

            <div class="verdict neutral" id="verdict">
                <span class="label" id="verdictLabel">--</span>
                <span class="horizon">based on aggregated headlines</span>
            </div>

            <div class="probrow">
                <div class="prob buy">
                    <div class="name">Buy probability</div>
                    <div class="pct" id="buyPct">--%</div>
                    <div class="bar"><span id="buyBar" style="width:0%"></span></div>
                </div>
                <div class="prob sell">
                    <div class="name">Sell probability</div>
                    <div class="pct" id="sellPct">--%</div>
                    <div class="bar"><span id="sellBar" style="width:0%"></span></div>
                </div>
            </div>

            <div class="stats">
                <div class="stat"><div class="k">Relevant items</div><div class="v" id="mRelevant">--</div></div>
                <div class="stat"><div class="k">Bullish hits</div>  <div class="v" id="mBull">--</div></div>
                <div class="stat"><div class="k">Bearish hits</div>  <div class="v" id="mBear">--</div></div>
                <div class="stat"><div class="k">Sources online</div><div class="v" id="mSrc">--</div></div>
            </div>

            <div id="apiError" class="err"></div>
        </div>

        <div class="panel">
            <h2>Source feed status</h2>
            <div class="sources-list" id="sources"></div>
            <div class="footer-note">
                Sources used: Forex Factory, Investing.com, DailyFX, FXStreet, Reuters,
                Bloomberg, Trading Economics, Action Forex, Kitco News, MarketWatch, Myfxbook.
            </div>
        </div>

        <div class="panel" style="grid-column: 1 / -1;">
            <h2>Latest GBP/USD-relevant headlines</h2>
            <div class="news" id="news">
                <div class="empty">Loading headlines&hellip;</div>
            </div>
        </div>

    </div>

    <div class="footer-note">
        Data is sentiment-derived from public RSS feeds of the listed sources only.
        This is informational and is <b>not financial advice</b>.
    </div>

</div>

<script>
(function() {
    const API_URL = 'gbpusd_api.php';
    const REFRESH_MS = 1000;     // UI tick every second
    const FETCH_EVERY = 1;       // backend call every 1 tick (server caches 10s)

    const $ = (id) => document.getElementById(id);

    let lastData = null;
    let tickCount = 0;
    let secondsSinceFetch = 0;

    function fmtClock(d) {
        const pad = (n) => String(n).padStart(2, '0');
        return pad(d.getHours()) + ':' + pad(d.getMinutes()) + ':' + pad(d.getSeconds());
    }
    function escapeHtml(s) {
        return String(s || '').replace(/[&<>"']/g, c => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
        }[c]));
    }
    function relativeAge(ts) {
        if (!ts) return 'time unknown';
        const seconds = Math.floor(Date.now() / 1000) - ts;
        if (seconds < 60) return seconds + 's ago';
        if (seconds < 3600) return Math.floor(seconds / 60) + 'm ago';
        if (seconds < 86400) return Math.floor(seconds / 3600) + 'h ago';
        return Math.floor(seconds / 86400) + 'd ago';
    }

    function render(data) {
        const buy = data.buyProbability;
        const sell = data.sellProbability;
        $('buyPct').textContent  = buy.toFixed(1) + '%';
        $('sellPct').textContent = sell.toFixed(1) + '%';
        $('buyBar').style.width  = buy + '%';
        $('sellBar').style.width = sell + '%';

        // Verdict
        const v = $('verdict');
        v.classList.remove('buy', 'sell', 'neutral');
        let cls = 'neutral';
        if (data.direction === 'BUY' || data.direction === 'LEAN BUY') cls = 'buy';
        else if (data.direction === 'SELL' || data.direction === 'LEAN SELL') cls = 'sell';
        v.classList.add(cls);
        $('verdictLabel').textContent = data.direction;

        // Stats
        $('mRelevant').textContent = data.metrics.relevantHeadlines;
        $('mBull').textContent     = data.metrics.bullishHits;
        $('mBear').textContent     = data.metrics.bearishHits;

        // Sources
        const srcEl = $('sources');
        srcEl.innerHTML = '';
        let onlineCount = 0;
        Object.entries(data.sources).forEach(([name, info]) => {
            if (info.ok) onlineCount++;
            const row = document.createElement('div');
            row.className = 'source-row';
            row.innerHTML = `
                <div class="left">
                    <span class="dot ${info.ok ? 'ok' : 'fail'}"></span>
                    <span>${escapeHtml(name)}</span>
                </div>
                <div class="right">
                    ${info.ok ? (info.items + ' relevant items') : escapeHtml(info.reason)}
                </div>
            `;
            srcEl.appendChild(row);
        });
        $('mSrc').textContent = onlineCount + '/' + Object.keys(data.sources).length;

        // Headlines
        const news = $('news');
        if (!data.headlines.length) {
            news.innerHTML = '<div class="empty">No GBP/USD-relevant headlines parsed from the source feeds right now.</div>';
        } else {
            news.innerHTML = data.headlines.map(h => `
                <div class="item ${h.sentiment}">
                    <a href="${escapeHtml(h.link)}" target="_blank" rel="noopener noreferrer">${escapeHtml(h.title)}</a>
                    <div class="meta-row">
                        <span class="badge source">${escapeHtml(h.source)}</span>
                        <span class="badge ${h.sentiment}">${h.sentiment}</span>
                        <span>${escapeHtml(relativeAge(h.pubTs))}</span>
                        ${h.pubDate ? '<span>&middot; ' + escapeHtml(h.pubDate) + '</span>' : ''}
                    </div>
                </div>
            `).join('');
        }
    }

    async function fetchData() {
        try {
            const res = await fetch(API_URL + '?t=' + Date.now(), { cache: 'no-store' });
            if (!res.ok) throw new Error('HTTP ' + res.status);
            const data = await res.json();
            lastData = data;
            $('apiError').textContent = '';
            $('lastFetch').textContent = fmtClock(new Date());
            render(data);
        } catch (err) {
            $('apiError').textContent = 'Feed error: ' + err.message + ' (will retry)';
        } finally {
            secondsSinceFetch = 0;
        }
    }

    function tick() {
        tickCount++;
        $('clock').textContent = fmtClock(new Date());
        secondsSinceFetch++;
        $('countdown').textContent = (FETCH_EVERY - (secondsSinceFetch % FETCH_EVERY)) + 's';
        if (secondsSinceFetch >= FETCH_EVERY) {
            fetchData();
        }
    }

    // Kick off
    fetchData();
    setInterval(tick, REFRESH_MS);
})();
</script>
</body>
</html>
