<?php
/**
 * GBP/USD next-24h Buy/Sell signal generator.
 *
 * USAGE
 *   CLI:      php tools/forex/gbpusd_signal.php [--format=text|json]
 *   Browser:  open tools/forex/gbpusd_signal.php in a web browser (HTML output by default)
 *             Add ?format=json or ?format=text to override.
 *
 * Pulls price data from Stooq (free) and headlines from the configured feeds in
 * sources.json (FXStreet, Investing.com, DailyFX, Action Forex, Reuters,
 * MarketWatch, Bloomberg/Trading Economics/Kitco/Forex Factory via Google News
 * RSS, plus Myfxbook crowd sentiment). Combines momentum + sentiment into a
 * 0-100 buy probability for the next 24 hours.
 */

declare(strict_types=1);

require_once __DIR__ . '/lib/Fetcher.php';
require_once __DIR__ . '/lib/PriceFeed.php';
require_once __DIR__ . '/lib/NewsFeed.php';
require_once __DIR__ . '/lib/Scorer.php';
require_once __DIR__ . '/lib/Report.php';

use ForexSignal\Fetcher;
use ForexSignal\PriceFeed;
use ForexSignal\NewsFeed;
use ForexSignal\Scorer;
use ForexSignal\Report;

$isCli = (PHP_SAPI === 'cli');

// ---- Format selection ----
$format = 'html';
if ($isCli) {
    $format = 'text';
    foreach ($argv as $arg) {
        if (preg_match('/^--format=(text|json|html)$/', $arg, $m)) $format = $m[1];
    }
} else {
    if (isset($_GET['format']) && in_array($_GET['format'], ['text', 'json', 'html'], true)) {
        $format = $_GET['format'];
    }
}

// ---- Load config ----
$configPath = __DIR__ . '/sources.json';
if (!file_exists($configPath)) {
    fwrite(STDERR, "Missing sources.json\n");
    exit(1);
}
$config = json_decode((string) file_get_contents($configPath), true);
if (!$config) {
    fwrite(STDERR, "Invalid sources.json\n");
    exit(1);
}
$bullish = $config['lexicon']['bullish_gbp'] ?? [];
$bearish = $config['lexicon']['bearish_gbp'] ?? [];
$feeds   = $config['feeds'] ?? [];

// ---- Fetch ----
$fetcher  = new Fetcher(timeout: 6, retries: 1);
$priceFeed = new PriceFeed($fetcher);
$newsFeed = new NewsFeed($fetcher, $bullish, $bearish);

$price = $priceFeed->snapshot();

$feedResults = [];
foreach ($feeds as $feed) {
    $feedResults[] = $newsFeed->fetch($feed);
}

// ---- Score ----
$scorer = new Scorer();
$score  = $scorer->score($price, $feedResults);

// ---- Output ----
switch ($format) {
    case 'json':
        if (!$isCli) header('Content-Type: application/json');
        echo Report::json($price, $feedResults, $score);
        break;
    case 'html':
        if (!$isCli) header('Content-Type: text/html; charset=utf-8');
        echo Report::html($price, $feedResults, $score);
        break;
    case 'text':
    default:
        if (!$isCli) header('Content-Type: text/plain; charset=utf-8');
        echo Report::text($price, $feedResults, $score);
        break;
}
