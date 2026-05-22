<?php
/**
 * gbpusd_api.php
 *
 * Aggregates GBP/USD-relevant headlines from the user-approved sources only,
 * scores their sentiment via keyword analysis, and returns a JSON payload with
 * buy/sell probability percentages for the next 24 hours.
 *
 * Sources used (RSS where publicly available):
 *   - Forex Factory     (calendar XML)
 *   - Investing.com     (forex news RSS)
 *   - DailyFX           (all news RSS)
 *   - FXStreet          (news RSS)
 *   - Reuters           (business news RSS)
 *   - Bloomberg         (no public RSS - listed as unavailable)
 *   - Trading Economics (UK news RSS)
 *   - Action Forex      (site RSS)
 *   - Kitco News        (news RSS)
 *   - MarketWatch       (markets RSS)
 *   - Myfxbook          (economic calendar RSS)
 *
 * Output: JSON
 *
 * NOTE: News feeds publish on the order of minutes/hours, not seconds.
 *       The frontend can poll this endpoint as often as it likes; the response
 *       is cached for ~10 seconds server-side to be respectful to publishers.
 */

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
header('Access-Control-Allow-Origin: *');

// --- Configuration ---------------------------------------------------------

$CACHE_FILE = sys_get_temp_dir() . '/gbpusd_dashboard_cache.json';
$CACHE_TTL  = 10; // seconds

// Each source can list multiple candidate URLs; the first one that returns
// usable XML wins. Setting the value to null marks a source as having no
// publicly available machine-readable feed.
$SOURCES = [
    'Forex Factory'     => ['https://nfs.faireconomy.media/ff_calendar_thisweek.xml'],
    'Investing.com'     => [
        'https://www.investing.com/rss/news_1.rss',
        'https://www.investing.com/rss/news_285.rss', // forex news
        'https://www.investing.com/rss/forex.rss',
    ],
    'DailyFX'           => [
        'https://www.dailyfx.com/feeds/all',
        'https://www.dailyfx.com/feeds/market-news',
    ],
    'FXStreet'          => [
        'https://www.fxstreet.com/news/feed',
        'https://www.fxstreet.com/rss/news',
    ],
    'Reuters'           => [
        'https://www.reuters.com/world/uk/rss',
        'https://feeds.reuters.com/reuters/businessNews',
    ],
    'Bloomberg'         => null, // No public RSS feed for currencies
    'Trading Economics' => [
        'https://tradingeconomics.com/rss/news.aspx?i=united+kingdom',
        'https://tradingeconomics.com/rss/news.aspx?i=forex',
    ],
    'Action Forex'      => ['https://www.actionforex.com/feed/'],
    'Kitco News'        => [
        'https://www.kitco.com/rss/KitcoNews.xml',
        'https://www.kitco.com/news/feed',
    ],
    'MarketWatch'       => [
        'https://feeds.content.dowjones.io/public/rss/mw_marketpulse',
        'https://feeds.content.dowjones.io/public/rss/mw_topstories',
    ],
    'Myfxbook'          => ['https://www.myfxbook.com/rss/forex-economic-calendar-events'],
];

// Keywords scoped to the GBP/USD pair, BoE/Fed, UK/US macro.
$RELEVANT_TERMS = [
    'gbp', 'usd', 'dollar', 'pound', 'sterling', 'cable',
    'boe', 'bank of england', 'fed', 'federal reserve', 'fomc',
    ' uk ', 'u.k.', 'britain', 'british', 'gilt', 'treasury yield',
];

// Bullish for GBP/USD = pound up OR dollar down
$BULLISH_TERMS = [
    'pound rises','pound up','pound gains','pound rallies','pound climbs','pound surges','pound jumps','pound strengthens','pound advances','pound firmer',
    'sterling rises','sterling up','sterling gains','sterling rallies','sterling climbs','sterling strengthens','sterling firmer',
    'cable rises','cable up','cable gains','cable rallies','cable climbs',
    'gbp rises','gbp gains','gbp rallies','gbp climbs','gbp strengthens',
    'gbp/usd rises','gbp/usd gains','gbp/usd rallies','gbp/usd climbs','gbp/usd advances','gbp/usd higher',
    'hawkish boe','boe hawkish','boe hike','rate hike boe','bank of england hike','strong uk','uk beats','uk inflation rises',
    'dollar weakens','dollar falls','dollar drops','dollar slips','dollar declines','dollar slumps','dollar tumbles','dollar lower','dollar softer',
    'usd weakens','usd falls','usd drops','usd lower','usd softer',
    'dovish fed','fed dovish','fed cuts','fed cut','rate cut fed','weak us data','us data misses',
];

// Bearish for GBP/USD = pound down OR dollar up
$BEARISH_TERMS = [
    'pound falls','pound down','pound drops','pound slips','pound declines','pound slumps','pound tumbles','pound weakens','pound softer',
    'sterling falls','sterling down','sterling drops','sterling slips','sterling declines','sterling slumps','sterling weakens','sterling softer',
    'cable falls','cable down','cable drops','cable slips','cable declines',
    'gbp falls','gbp drops','gbp weakens','gbp lower',
    'gbp/usd falls','gbp/usd drops','gbp/usd declines','gbp/usd lower','gbp/usd slumps',
    'dovish boe','boe dovish','boe cut','rate cut boe','weak uk','uk misses','uk inflation falls','retail sales fall','retail sales drop',
    'dollar rises','dollar up','dollar gains','dollar rallies','dollar climbs','dollar surges','dollar jumps','dollar strengthens','dollar advances','dollar firmer','dollar higher',
    'usd rises','usd gains','usd rallies','usd climbs','usd strengthens','usd higher','usd firmer',
    'hawkish fed','fed hawkish','fed hike','rate hike fed','strong us data','us data beats','us inflation rises',
];

// --- Cache short-circuit ---------------------------------------------------

if (file_exists($CACHE_FILE) && (time() - filemtime($CACHE_FILE)) < $CACHE_TTL) {
    readfile($CACHE_FILE);
    exit;
}

// --- Parallel HTTP fetch ---------------------------------------------------

function fetch_all_parallel(array $sources): array {
    // Flatten candidate URLs while preserving source name -> list mapping
    $multi   = curl_multi_init();
    $handles = []; // [(source, url, ch)]
    foreach ($sources as $name => $urls) {
        if (!$urls) continue;
        foreach ((array)$urls as $url) {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => 6,
                CURLOPT_CONNECTTIMEOUT => 4,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_MAXREDIRS      => 4,
                CURLOPT_USERAGENT      => 'Mozilla/5.0 (compatible; GBPUSD-Dashboard/1.0; +https://example.com)',
                CURLOPT_HTTPHEADER     => ['Accept: application/rss+xml, application/xml, text/xml, */*'],
                CURLOPT_SSL_VERIFYPEER => false,
            ]);
            curl_multi_add_handle($multi, $ch);
            $handles[] = ['source' => $name, 'url' => $url, 'ch' => $ch];
        }
    }
    $running = null;
    do {
        curl_multi_exec($multi, $running);
        if ($running > 0) curl_multi_select($multi, 1.0);
    } while ($running > 0);

    // Group results: pick first 2xx response per source
    $bySource = [];
    foreach ($handles as $h) {
        $body   = curl_multi_getcontent($h['ch']);
        $status = (int)curl_getinfo($h['ch'], CURLINFO_HTTP_CODE);
        $err    = curl_error($h['ch']);
        curl_multi_remove_handle($multi, $h['ch']);
        curl_close($h['ch']);

        $name = $h['source'];
        if (!isset($bySource[$name])) {
            $bySource[$name] = [
                'body'   => '',
                'status' => $status,
                'error'  => $err,
                'url'    => $h['url'],
            ];
        }
        // Prefer the first successful response for each source
        $isOk = ($status >= 200 && $status < 300 && $body !== '');
        if ($isOk && $bySource[$name]['body'] === '') {
            $bySource[$name] = [
                'body'   => $body,
                'status' => $status,
                'error'  => '',
                'url'    => $h['url'],
            ];
        }
    }
    curl_multi_close($multi);
    return $bySource;
}

// --- Feed parsing ----------------------------------------------------------

function parse_feed_items(string $xmlString): array {
    if ($xmlString === '') return [];
    libxml_use_internal_errors(true);
    $xml = simplexml_load_string($xmlString);
    libxml_clear_errors();
    if (!$xml) return [];

    $items = [];

    // RSS 2.0
    if (isset($xml->channel->item)) {
        foreach ($xml->channel->item as $it) {
            $items[] = [
                'title'   => trim((string)$it->title),
                'link'    => trim((string)$it->link),
                'desc'    => trim((string)$it->description),
                'pubDate' => trim((string)$it->pubDate),
            ];
        }
        return $items;
    }

    // Atom
    if (isset($xml->entry)) {
        foreach ($xml->entry as $e) {
            $link = '';
            if (isset($e->link['href'])) $link = (string)$e->link['href'];
            elseif (isset($e->link)) $link = (string)$e->link;
            $items[] = [
                'title'   => trim((string)$e->title),
                'link'    => $link,
                'desc'    => trim((string)($e->summary ?? $e->content ?? '')),
                'pubDate' => trim((string)($e->updated ?? $e->published ?? '')),
            ];
        }
        return $items;
    }

    // Forex Factory calendar (custom)
    if (isset($xml->event)) {
        foreach ($xml->event as $ev) {
            $country = strtolower((string)$ev->country);
            if (!in_array($country, ['gbp','usd','uk','us'], true)) continue;
            $items[] = [
                'title'   => trim((string)$ev->title) . ' [' . strtoupper($country) . ']',
                'link'    => 'https://www.forexfactory.com/calendar',
                'desc'    => 'Impact: ' . (string)$ev->impact . ' | Forecast: ' . (string)$ev->forecast . ' | Previous: ' . (string)$ev->previous,
                'pubDate' => trim((string)$ev->date . ' ' . (string)$ev->time),
            ];
        }
        return $items;
    }

    return $items;
}

// --- Sentiment scoring -----------------------------------------------------

function score_text(string $text, array $bull, array $bear): array {
    $t = strtolower(' ' . $text . ' ');
    $bullHits = 0; $bearHits = 0;
    foreach ($bull as $kw) if (strpos($t, $kw) !== false) $bullHits++;
    foreach ($bear as $kw) if (strpos($t, $kw) !== false) $bearHits++;
    return [$bullHits, $bearHits];
}

function is_relevant(string $text, array $terms): bool {
    $t = strtolower(' ' . $text . ' ');
    foreach ($terms as $kw) if (strpos($t, $kw) !== false) return true;
    return false;
}

// --- Main pipeline ---------------------------------------------------------

$fetched = fetch_all_parallel($SOURCES);

$allHeadlines = [];
$sourceStatus = [];
$bullTotal = 0; $bearTotal = 0; $relevantTotal = 0;

foreach ($SOURCES as $name => $urls) {
    if (!$urls) {
        $sourceStatus[$name] = ['ok' => false, 'reason' => 'No public RSS feed available', 'items' => 0];
        continue;
    }
    $resp = $fetched[$name] ?? ['body' => '', 'status' => 0, 'error' => 'no response', 'url' => ''];
    if ($resp['body'] === '') {
        $sourceStatus[$name] = [
            'ok'     => false,
            'reason' => 'HTTP ' . $resp['status'] . ($resp['error'] ? ' - ' . $resp['error'] : ''),
            'items'  => 0,
        ];
        continue;
    }
    $items = parse_feed_items($resp['body']);
    $relevantCount = 0;

    foreach ($items as $it) {
        $combined = $it['title'] . ' ' . $it['desc'];
        if (!is_relevant($combined, $RELEVANT_TERMS)) continue;
        [$b, $br] = score_text($combined, $BULLISH_TERMS, $BEARISH_TERMS);
        $net = $b - $br;
        $sentiment = $net > 0 ? 'bullish' : ($net < 0 ? 'bearish' : 'neutral');

        $bullTotal     += $b;
        $bearTotal     += $br;
        $relevantTotal++;
        $relevantCount++;

        $allHeadlines[] = [
            'source'    => $name,
            'title'     => $it['title'],
            'link'      => $it['link'],
            'pubDate'   => $it['pubDate'],
            'pubTs'     => $it['pubDate'] ? (strtotime($it['pubDate']) ?: 0) : 0,
            'score'     => $net,
            'sentiment' => $sentiment,
        ];
    }
    $sourceStatus[$name] = ['ok' => true, 'reason' => 'OK', 'items' => $relevantCount];
}

// Probability calculation: weight relevant headlines, with mild time decay (newer counts more).
$now = time();
$weightedBull = 0.0; $weightedBear = 0.0;
foreach ($allHeadlines as $h) {
    $ageHours = $h['pubTs'] > 0 ? max(0, ($now - $h['pubTs']) / 3600.0) : 24.0;
    $weight   = exp(-$ageHours / 24.0); // half-life ~16h
    if ($h['score'] > 0) $weightedBull += $h['score'] * $weight;
    elseif ($h['score'] < 0) $weightedBear += abs($h['score']) * $weight;
}

$buyProb = 50.0;
$totalWeighted = $weightedBull + $weightedBear;
if ($totalWeighted > 0) {
    $rawBuy = ($weightedBull / $totalWeighted) * 100.0;
    // Pull probabilities toward 50 when we have very few signals (regularization)
    $confidence = min(1.0, $totalWeighted / 8.0);
    $buyProb = 50.0 + ($rawBuy - 50.0) * $confidence;
    $buyProb = max(15.0, min(85.0, $buyProb));
}
$sellProb = 100.0 - $buyProb;

// Sort headlines newest first, keep top 30
usort($allHeadlines, fn($a, $b) => $b['pubTs'] <=> $a['pubTs']);
$topHeadlines = array_slice($allHeadlines, 0, 30);

// Determine direction label and rationale
$direction = 'NEUTRAL';
if ($buyProb >= 60) $direction = 'BUY';
elseif ($buyProb >= 53) $direction = 'LEAN BUY';
elseif ($buyProb <= 40) $direction = 'SELL';
elseif ($buyProb <= 47) $direction = 'LEAN SELL';

$payload = [
    'pair'              => 'GBP/USD',
    'updatedAt'         => gmdate('c'),
    'updatedAtUnix'     => $now,
    'horizonHours'      => 24,
    'buyProbability'    => round($buyProb, 1),
    'sellProbability'   => round($sellProb, 1),
    'direction'         => $direction,
    'metrics'           => [
        'relevantHeadlines' => $relevantTotal,
        'bullishHits'       => $bullTotal,
        'bearishHits'       => $bearTotal,
        'weightedBull'      => round($weightedBull, 2),
        'weightedBear'      => round($weightedBear, 2),
    ],
    'sources'           => $sourceStatus,
    'headlines'         => $topHeadlines,
    'disclaimer'        => 'Sentiment-based probability derived from public RSS feeds of approved sources. Not financial advice.',
];

$json = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
@file_put_contents($CACHE_FILE, $json, LOCK_EX);
echo $json;
