<?php
/**
 * NewsFeed: parses RSS feeds (preferred) and HTML pages (fallback) to extract
 * GBP/USD-relevant headlines, then scores them against a bullish/bearish lexicon.
 *
 * Each feed entry yields:
 *   { source, title, link, published, score (-1..+1), matched_terms }
 */

namespace ForexSignal;

class NewsFeed
{
    private Fetcher $fetcher;
    private array $bullish;
    private array $bearish;

    public function __construct(Fetcher $fetcher, array $bullish, array $bearish)
    {
        $this->fetcher = $fetcher;
        $this->bullish = array_map('strtolower', $bullish);
        $this->bearish = array_map('strtolower', $bearish);
    }

    /**
     * Fetch one configured feed. Returns ['ok'=>bool,'items'=>[],'error'=>?string,...].
     */
    public function fetch(array $feed): array
    {
        $url    = $feed['url'];
        $type   = $feed['type'] ?? 'rss';
        $filter = $feed['filter'] ?? '.*';
        $weight = (float) ($feed['weight'] ?? 1.0);
        $name   = $feed['name'] ?? $url;

        $resp = $this->fetcher->get($url);
        if (!$resp['ok']) {
            return [
                'ok' => false, 'name' => $name, 'weight' => $weight,
                'items' => [], 'error' => $resp['error'],
            ];
        }

        if ($type === 'rss') {
            $items = $this->parseRss($resp['body']);
        } elseif ($type === 'html' && ($feed['extract'] ?? '') === 'sentiment') {
            $items = $this->parseMyfxbookSentiment($resp['body']);
        } else {
            $items = $this->parseHtmlHeadlines($resp['body']);
        }

        // Filter by relevance keywords on title (and extract sentiment items pass-through).
        $filtered = [];
        $cutoff = time() - 72 * 3600; // 3-day window
        foreach ($items as $item) {
            $title = $item['title'] ?? '';
            $isSentiment = !empty($item['is_sentiment']);
            if (!$isSentiment && !preg_match('~' . str_replace('~', '\~', $filter) . '~i', $title)) continue;
            if (!empty($item['published_ts']) && $item['published_ts'] < $cutoff) continue;

            if ($isSentiment) {
                $item['score'] = $item['score'] ?? 0.0;
                $item['matched'] = $item['matched'] ?? [];
            } else {
                [$score, $matched] = $this->scoreText($title);
                $item['score']   = $score;
                $item['matched'] = $matched;
            }
            $filtered[] = $item;
        }

        return [
            'ok' => true, 'name' => $name, 'weight' => $weight,
            'items' => $filtered, 'error' => null, 'count' => count($filtered),
        ];
    }

    private function parseRss(string $body): array
    {
        $items = [];
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($body);
        if (!$xml) return $items;

        // RSS 2.0
        if (isset($xml->channel->item)) {
            foreach ($xml->channel->item as $it) {
                $items[] = $this->normalizeRssItem(
                    (string) $it->title,
                    (string) $it->link,
                    (string) ($it->pubDate ?? '')
                );
            }
        }
        // Atom
        if (isset($xml->entry)) {
            foreach ($xml->entry as $entry) {
                $link = '';
                if (isset($entry->link['href'])) $link = (string) $entry->link['href'];
                $items[] = $this->normalizeRssItem(
                    (string) $entry->title,
                    $link,
                    (string) ($entry->updated ?? $entry->published ?? '')
                );
            }
        }
        return $items;
    }

    private function normalizeRssItem(string $title, string $link, string $pub): array
    {
        $ts = $pub ? strtotime($pub) : 0;
        return [
            'title'        => trim(html_entity_decode(strip_tags($title), ENT_QUOTES | ENT_HTML5)),
            'link'         => $link,
            'published'    => $pub,
            'published_ts' => $ts ?: time(),
        ];
    }

    private function parseHtmlHeadlines(string $body): array
    {
        $items = [];
        // Generic: pull all <a> with href that look like article links and at least 5 words of text.
        if (preg_match_all('/<a[^>]+href="([^"]+)"[^>]*>([^<]{20,200})<\/a>/i', $body, $m, PREG_SET_ORDER)) {
            foreach (array_slice($m, 0, 30) as $hit) {
                $title = trim(html_entity_decode(strip_tags($hit[2]), ENT_QUOTES | ENT_HTML5));
                if (str_word_count($title) < 5) continue;
                $items[] = [
                    'title'        => $title,
                    'link'         => $hit[1],
                    'published'    => '',
                    'published_ts' => time(),
                ];
            }
        }
        return $items;
    }

    /**
     * Myfxbook publishes the % long / % short for GBPUSD on the community outlook
     * page. We try a few patterns; if blocked we just return empty and the scorer
     * will silently skip this source.
     */
    private function parseMyfxbookSentiment(string $body): array
    {
        $long = null; $short = null;
        if (preg_match('/(\d{1,3})\s*%\s*(?:of\s+)?(?:traders|forex traders)?\s*(?:are\s+)?(?:currently\s+)?going\s+long/i', $body, $m)) {
            $long = (int) $m[1];
        }
        if (preg_match('/(\d{1,3})\s*%\s*(?:of\s+)?(?:traders|forex traders)?\s*(?:are\s+)?(?:currently\s+)?going\s+short/i', $body, $m)) {
            $short = (int) $m[1];
        }
        if ($long === null || $short === null) {
            // Fallback: look for paired digits like "Short 68% / Long 32%"
            if (preg_match('/short[^0-9]{0,20}(\d{1,3})/i', $body, $sm)) $short = (int) $sm[1];
            if (preg_match('/long[^0-9]{0,20}(\d{1,3})/i', $body, $lm))  $long  = (int) $lm[1];
        }
        if ($long === null || $short === null) return [];

        // Contrarian read: heavy short positioning is mildly bullish, and vice versa.
        // Map (short - long) from -100..+100 to score -1..+1.
        $contrarian = ($short - $long) / 100.0;
        $contrarian = max(-1.0, min(1.0, $contrarian));

        return [[
            'title'        => "Myfxbook crowd: long {$long}% / short {$short}% (contrarian read)",
            'link'         => 'https://www.myfxbook.com/community/outlook/GBPUSD',
            'published'    => date('r'),
            'published_ts' => time(),
            'is_sentiment' => true,
            'score'        => $contrarian,
            'matched'      => ["long={$long}%", "short={$short}%"],
        ]];
    }

    /**
     * Score a headline against the lexicon. Uses \b word-boundary regex so that
     * "drop", "fall", "rise" match cleanly without bleeding into other words.
     * Returns [-1..+1, matched_terms].
     */
    private function scoreText(string $text): array
    {
        $t = strtolower($text);
        $bull = 0; $bear = 0; $matched = [];
        foreach ($this->bullish as $term) {
            $pattern = '/\b' . preg_quote($term, '/') . '\b/i';
            if (preg_match($pattern, $t)) {
                $bull++; $matched[] = "+{$term}";
            }
        }
        foreach ($this->bearish as $term) {
            $pattern = '/\b' . preg_quote($term, '/') . '\b/i';
            if (preg_match($pattern, $t)) {
                $bear++; $matched[] = "-{$term}";
            }
        }
        $total = $bull + $bear;
        if ($total === 0) return [0.0, []];
        $score = ($bull - $bear) / $total;
        return [$score, $matched];
    }
}
