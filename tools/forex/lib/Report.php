<?php
/**
 * Report: renders the signal as plain text (CLI), HTML (browser), or JSON.
 */

namespace ForexSignal;

class Report
{
    public static function text(array $price, array $feedResults, array $score): string
    {
        $out  = "==============================================\n";
        $out .= "  GBP/USD Buy/Sell Signal — next 24 hours\n";
        $out .= "  Generated: " . date('Y-m-d H:i:s') . " UTC\n";
        $out .= "==============================================\n\n";

        if (!empty($price['ok'])) {
            $out .= sprintf("Spot (as of %s):  %.4f\n", $price['as_of'], $price['last']);
            $out .= sprintf("Day change:        %+0.2f%%\n", $price['change_d_pct']);
            $out .= sprintf("Week change:       %+0.2f%%\n", $price['change_w_pct']);
            $out .= sprintf("Month change:      %+0.2f%%\n", $price['change_m_pct']);
            $out .= sprintf("20-EMA / 50-EMA:   %.4f / %.4f\n", $price['ema20'], $price['ema50']);
            $out .= sprintf("ATR(14):           %.4f (~%d pips)\n", $price['atr14'], (int) round($price['atr14'] * 10000));
        } else {
            $out .= "Price feed: UNAVAILABLE (" . ($price['error'] ?? 'unknown') . ")\n";
        }

        $out .= "\n----- VERDICT -----\n";
        $out .= sprintf("BUY:  %d%%   SELL: %d%%   →  %s\n",
            $score['buy_pct'], $score['sell_pct'], $score['verdict']);
        $out .= sprintf("Momentum: %+0.2f   Sentiment: %+0.2f   Items scored: %d\n",
            $score['momentum_score'], $score['sentiment_score'], $score['items_scored']);

        $out .= "\n----- WHY -----\n";
        foreach ($score['reasons'] as $r) $out .= "  - {$r}\n";

        $out .= "\n----- TOP HEADLINES (relevant + scored) -----\n";
        $all = [];
        foreach ($feedResults as $fr) {
            if (empty($fr['ok'])) continue;
            foreach ($fr['items'] as $it) {
                $it['source'] = $fr['name'];
                $all[] = $it;
            }
        }
        usort($all, fn($a, $b) => ($b['published_ts'] ?? 0) <=> ($a['published_ts'] ?? 0));
        foreach (array_slice($all, 0, 12) as $it) {
            $sign = $it['score'] > 0 ? '+' : ($it['score'] < 0 ? '-' : '0');
            $out .= sprintf("  [%s %+0.2f] %s\n", $sign, $it['score'], $it['title']);
            $out .= sprintf("           (%s)\n", $it['source']);
        }

        $out .= "\n----- FEED STATUS -----\n";
        foreach ($feedResults as $fr) {
            $tag = $fr['ok'] ? "OK ({$fr['count']})" : "FAIL ({$fr['error']})";
            $out .= sprintf("  %-40s %s\n", substr($fr['name'], 0, 40), $tag);
        }

        $out .= "\nDisclaimer: Informational only. Not financial advice. FX trading carries risk.\n";
        return $out;
    }

    public static function json(array $price, array $feedResults, array $score): string
    {
        $payload = [
            'generated_at' => gmdate('c'),
            'pair'         => 'GBPUSD',
            'horizon'      => '24h',
            'verdict'      => $score['verdict'],
            'buy_pct'      => $score['buy_pct'],
            'sell_pct'     => $score['sell_pct'],
            'momentum'     => $score['momentum_score'],
            'sentiment'    => $score['sentiment_score'],
            'items_scored' => $score['items_scored'],
            'price'        => $price,
            'reasons'      => $score['reasons'],
            'feeds'        => array_map(function ($fr) {
                return [
                    'name'   => $fr['name'],
                    'ok'     => $fr['ok'],
                    'count'  => $fr['count'] ?? 0,
                    'error'  => $fr['error'] ?? null,
                    'items'  => array_map(fn($i) => [
                        'title' => $i['title'],
                        'link'  => $i['link'],
                        'score' => $i['score'] ?? 0,
                        'published' => $i['published'] ?? '',
                    ], $fr['items'] ?? []),
                ];
            }, $feedResults),
        ];
        return json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    public static function html(array $price, array $feedResults, array $score): string
    {
        $color = $score['buy_pct'] >= 55 ? '#1b8b3a'
               : ($score['buy_pct'] <= 45 ? '#c0392b' : '#7f8c8d');
        $h = '<!doctype html><html><head><meta charset="utf-8"><title>GBP/USD Signal</title>';
        $h .= '<style>body{font-family:-apple-system,Segoe UI,sans-serif;max-width:880px;margin:24px auto;padding:0 16px;color:#222}';
        $h .= 'h1{margin-bottom:0}.verdict{font-size:32px;font-weight:700;color:' . $color . '}';
        $h .= 'table{border-collapse:collapse;width:100%;margin:12px 0}td,th{border:1px solid #ddd;padding:6px 10px;text-align:left;font-size:14px}';
        $h .= '.pos{color:#1b8b3a}.neg{color:#c0392b}.muted{color:#888}.bar{height:18px;border-radius:9px;background:#eee;overflow:hidden;display:flex}';
        $h .= '.bar>div{height:100%;display:flex;align-items:center;justify-content:center;color:#fff;font-size:12px;font-weight:600}';
        $h .= '</style></head><body>';
        $h .= '<h1>GBP/USD signal — next 24h</h1>';
        $h .= '<p class="muted">Generated ' . htmlspecialchars(gmdate('Y-m-d H:i:s')) . ' UTC</p>';

        $h .= '<div class="verdict">' . htmlspecialchars($score['verdict']) . '</div>';
        $h .= '<div class="bar"><div style="width:' . $score['buy_pct'] . '%;background:#1b8b3a">'
            . 'BUY ' . $score['buy_pct'] . '%</div>'
            . '<div style="width:' . $score['sell_pct'] . '%;background:#c0392b">'
            . 'SELL ' . $score['sell_pct'] . '%</div></div>';

        if (!empty($price['ok'])) {
            $h .= '<h3>Price snapshot</h3><table>';
            $h .= '<tr><th>As of</th><td>' . htmlspecialchars($price['as_of']) . '</td></tr>';
            $h .= '<tr><th>Last</th><td>' . number_format($price['last'], 4) . '</td></tr>';
            $h .= '<tr><th>Day / Week / Month</th><td>'
                . sprintf('%+.2f%% / %+.2f%% / %+.2f%%', $price['change_d_pct'], $price['change_w_pct'], $price['change_m_pct'])
                . '</td></tr>';
            $h .= '<tr><th>20-EMA / 50-EMA</th><td>'
                . number_format($price['ema20'], 4) . ' / ' . number_format($price['ema50'], 4) . '</td></tr>';
            $h .= '<tr><th>ATR(14)</th><td>~' . (int) round($price['atr14'] * 10000) . ' pips</td></tr>';
            $h .= '</table>';
        }

        $h .= '<h3>Reasoning</h3><ul>';
        foreach ($score['reasons'] as $r) $h .= '<li>' . htmlspecialchars($r) . '</li>';
        $h .= '</ul>';

        $h .= '<h3>Top headlines (last 72h)</h3><table><tr><th>Score</th><th>Headline</th><th>Source</th></tr>';
        $all = [];
        foreach ($feedResults as $fr) {
            if (empty($fr['ok'])) continue;
            foreach ($fr['items'] as $it) {
                $it['source'] = $fr['name']; $all[] = $it;
            }
        }
        usort($all, fn($a, $b) => ($b['published_ts'] ?? 0) <=> ($a['published_ts'] ?? 0));
        foreach (array_slice($all, 0, 20) as $it) {
            $cls = $it['score'] > 0 ? 'pos' : ($it['score'] < 0 ? 'neg' : 'muted');
            $h .= '<tr><td class="' . $cls . '">' . sprintf('%+0.2f', $it['score']) . '</td>';
            $h .= '<td><a href="' . htmlspecialchars($it['link']) . '" target="_blank" rel="noopener">'
                . htmlspecialchars($it['title']) . '</a></td>';
            $h .= '<td class="muted">' . htmlspecialchars($it['source']) . '</td></tr>';
        }
        $h .= '</table>';

        $h .= '<h3>Feed status</h3><table><tr><th>Source</th><th>Status</th><th>Items</th></tr>';
        foreach ($feedResults as $fr) {
            $h .= '<tr><td>' . htmlspecialchars($fr['name']) . '</td>';
            $h .= '<td class="' . ($fr['ok'] ? 'pos' : 'neg') . '">'
                . ($fr['ok'] ? 'OK' : htmlspecialchars($fr['error'] ?? 'fail')) . '</td>';
            $h .= '<td>' . (int) ($fr['count'] ?? 0) . '</td></tr>';
        }
        $h .= '</table>';

        $h .= '<p class="muted"><em>Informational only. Not financial advice. FX trading carries substantial risk.</em></p>';
        $h .= '</body></html>';
        return $h;
    }
}
