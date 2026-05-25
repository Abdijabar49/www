<?php
/**
 * PriceFeed: pulls daily GBP/USD OHLC from Yahoo Finance (free chart endpoint),
 * falls back to Stooq CSV. Computes daily/weekly/monthly returns plus 20-EMA,
 * 50-EMA, and ATR(14) — these feed the momentum component of the buy/sell score.
 */

namespace ForexSignal;

class PriceFeed
{
    private Fetcher $fetcher;
    private string $yahooUrl  = 'https://query1.finance.yahoo.com/v7/finance/chart/GBPUSD=X?range=3mo&interval=1d';
    private string $stooqUrl  = 'https://stooq.com/q/d/l/?s=gbpusd&i=d';

    public function __construct(Fetcher $fetcher)
    {
        $this->fetcher = $fetcher;
    }

    public function snapshot(): array
    {
        $rows = $this->fetchYahoo();
        if (count($rows) < 25) {
            // Fallback to Stooq if Yahoo failed
            $rows = $this->fetchStooq();
        }
        if (count($rows) < 25) {
            return ['ok' => false, 'error' => 'Insufficient price history (Yahoo + Stooq both unavailable)'];
        }

        $rows = array_slice($rows, -60);
        $closes = array_map(fn($r) => (float) $r['close'], $rows);
        $highs  = array_map(fn($r) => (float) $r['high'],  $rows);
        $lows   = array_map(fn($r) => (float) $r['low'],   $rows);

        $last     = end($closes);
        $prev     = $closes[count($closes) - 2];
        $weekAgo  = $closes[count($closes) - 6] ?? $prev;
        $monthAgo = $closes[count($closes) - 22] ?? $prev;

        $ema20 = $this->ema($closes, 20);
        $ema50 = $this->ema($closes, 50);
        $atr14 = $this->atr($highs, $lows, $closes, 14);

        return [
            'ok'           => true,
            'as_of'        => end($rows)['date'],
            'last'         => $last,
            'prev_close'   => $prev,
            'change_d'     => $last - $prev,
            'change_d_pct' => ($last - $prev) / $prev * 100,
            'change_w_pct' => ($last - $weekAgo) / $weekAgo * 100,
            'change_m_pct' => ($last - $monthAgo) / $monthAgo * 100,
            'ema20'        => $ema20,
            'ema50'        => $ema50,
            'atr14'        => $atr14,
            'above_ema20'  => $last > $ema20,
            'above_ema50'  => $last > $ema50,
        ];
    }

    private function fetchYahoo(): array
    {
        $resp = $this->fetcher->get($this->yahooUrl);
        if (!$resp['ok']) return [];

        $json = json_decode($resp['body'], true);
        $result = $json['chart']['result'][0] ?? null;
        if (!$result) return [];

        $ts     = $result['timestamp']                              ?? [];
        $quote  = $result['indicators']['quote'][0]                 ?? [];
        $closes = $quote['close'] ?? [];
        $highs  = $quote['high']  ?? [];
        $lows   = $quote['low']   ?? [];

        $rows = [];
        $count = count($ts);
        for ($i = 0; $i < $count; $i++) {
            if (!isset($closes[$i]) || $closes[$i] === null) continue;
            $rows[] = [
                'date'  => date('Y-m-d', (int) $ts[$i]),
                'close' => $closes[$i],
                'high'  => $highs[$i]  ?? $closes[$i],
                'low'   => $lows[$i]   ?? $closes[$i],
            ];
        }
        return $rows;
    }

    private function fetchStooq(): array
    {
        $resp = $this->fetcher->get($this->stooqUrl);
        if (!$resp['ok']) return [];
        $lines = preg_split('/\r?\n/', trim($resp['body']));
        if (!$lines || count($lines) < 2) return [];
        $header = array_map('strtolower', str_getcsv(array_shift($lines)));
        $rows = [];
        foreach ($lines as $line) {
            if ($line === '') continue;
            $cols = str_getcsv($line);
            if (count($cols) !== count($header)) continue;
            $row = array_combine($header, $cols);
            if (isset($row['close']) && is_numeric($row['close'])) {
                $rows[] = [
                    'date'  => $row['date']  ?? '',
                    'close' => (float) $row['close'],
                    'high'  => (float) ($row['high'] ?? $row['close']),
                    'low'   => (float) ($row['low']  ?? $row['close']),
                ];
            }
        }
        return $rows;
    }

    private function ema(array $values, int $period): float
    {
        if (count($values) < $period) return end($values);
        $k = 2 / ($period + 1);
        $seed = array_slice($values, 0, $period);
        $ema = array_sum($seed) / $period;
        for ($i = $period; $i < count($values); $i++) {
            $ema = ($values[$i] - $ema) * $k + $ema;
        }
        return $ema;
    }

    private function atr(array $highs, array $lows, array $closes, int $period): float
    {
        $trs = [];
        for ($i = 1; $i < count($closes); $i++) {
            $tr = max(
                $highs[$i] - $lows[$i],
                abs($highs[$i] - $closes[$i - 1]),
                abs($lows[$i]  - $closes[$i - 1])
            );
            $trs[] = $tr;
        }
        if (count($trs) < $period) return 0.0;
        $window = array_slice($trs, -$period);
        return array_sum($window) / $period;
    }
}
