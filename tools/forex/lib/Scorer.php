<?php
/**
 * Scorer: combines momentum (price feed) and sentiment (news feeds + crowd
 * positioning) into a single buy probability for the next ~24 hours.
 *
 * Output: { buy_pct, sell_pct, momentum_score, sentiment_score, reasons }
 *
 * Math (transparent on purpose):
 *   momentum = clamp( day_change_pct/0.6 + ema20_bias + ema50_bias, -1, +1 )
 *     where ema_bias contributes +0.25 if price above EMA, else -0.25.
 *   sentiment = weighted average of all (item.score * feed.weight)
 *   blended  = 0.55 * sentiment + 0.45 * momentum
 *   buy_pct  = round( 50 + blended * 30 )    // bounded ~20%..80%
 */

namespace ForexSignal;

class Scorer
{
    public function score(array $price, array $feedResults): array
    {
        $reasons = [];

        $momentum = 0.0;
        if (!empty($price['ok'])) {
            $dayPct = $price['change_d_pct'];
            $momentum += max(-0.6, min(0.6, $dayPct / 0.6));
            $reasons[] = sprintf("Daily change %+0.2f%% contributes %+0.2f to momentum.", $dayPct, max(-0.6, min(0.6, $dayPct / 0.6)));

            if ($price['above_ema20']) {
                $momentum += 0.25;
                $reasons[] = sprintf("Price %.4f above 20-EMA %.4f → +0.25 momentum.", $price['last'], $price['ema20']);
            } else {
                $momentum -= 0.25;
                $reasons[] = sprintf("Price %.4f below 20-EMA %.4f → -0.25 momentum.", $price['last'], $price['ema20']);
            }

            if ($price['above_ema50']) {
                $momentum += 0.15;
                $reasons[] = sprintf("Price above 50-EMA %.4f → +0.15 momentum.", $price['ema50']);
            } else {
                $momentum -= 0.15;
                $reasons[] = sprintf("Price below 50-EMA %.4f → -0.15 momentum.", $price['ema50']);
            }
        } else {
            $reasons[] = "Price feed unavailable: momentum component skipped.";
        }
        $momentum = max(-1.0, min(1.0, $momentum));

        // Sentiment: weighted average of all scored items across feeds.
        $sentNumerator = 0.0;
        $sentDenominator = 0.0;
        $itemsScored = 0;

        foreach ($feedResults as $fr) {
            if (empty($fr['ok'])) continue;
            $w = $fr['weight'] ?? 1.0;
            foreach ($fr['items'] as $item) {
                if (!isset($item['score'])) continue;
                // Slight recency boost: items in the last 24h get a 1.3x boost.
                $age = time() - ($item['published_ts'] ?? time());
                $recency = $age < 24 * 3600 ? 1.3 : 1.0;
                $sentNumerator   += $item['score'] * $w * $recency;
                $sentDenominator += $w * $recency;
                $itemsScored++;
            }
        }
        $sentiment = $sentDenominator > 0 ? $sentNumerator / $sentDenominator : 0.0;
        $sentiment = max(-1.0, min(1.0, $sentiment));
        $reasons[] = sprintf("Sentiment from %d scored items = %+0.2f.", $itemsScored, $sentiment);

        $blended = 0.55 * $sentiment + 0.45 * $momentum;
        $buyPct  = (int) round(50 + $blended * 30);
        $buyPct  = max(20, min(80, $buyPct));
        $sellPct = 100 - $buyPct;

        // Verdict label
        if ($buyPct >= 65)        $verdict = 'BUY (strong)';
        elseif ($buyPct >= 55)    $verdict = 'BUY (moderate)';
        elseif ($buyPct >= 45)    $verdict = 'NEUTRAL';
        elseif ($buyPct >= 35)    $verdict = 'SELL (moderate)';
        else                       $verdict = 'SELL (strong)';

        return [
            'buy_pct'         => $buyPct,
            'sell_pct'        => $sellPct,
            'verdict'         => $verdict,
            'momentum_score'  => round($momentum, 3),
            'sentiment_score' => round($sentiment, 3),
            'items_scored'    => $itemsScored,
            'reasons'         => $reasons,
        ];
    }
}
