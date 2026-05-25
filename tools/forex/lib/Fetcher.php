<?php
/**
 * Fetcher: simple cURL wrapper with sane defaults, timeouts, retries, and a
 * realistic User-Agent. Used by all feed adapters.
 */

namespace ForexSignal;

class Fetcher
{
    private int $timeout;
    private int $retries;
    private string $userAgent;

    public function __construct(int $timeout = 6, int $retries = 1)
    {
        $this->timeout = $timeout;
        $this->retries = $retries;
        $this->userAgent = 'Mozilla/5.0 (compatible; GBPUSD-Signal/1.0; +https://example.local)';
    }

    /**
     * Fetch a URL. Returns ['ok'=>bool,'status'=>int,'body'=>string,'error'=>?string].
     */
    public function get(string $url, array $headers = []): array
    {
        $attempt = 0;
        $lastError = null;
        $defaultHeaders = [
            'Accept: text/html,application/xhtml+xml,application/xml,application/rss+xml;q=0.9,*/*;q=0.8',
            'Accept-Language: en-US,en;q=0.9',
            'Cache-Control: no-cache',
        ];
        $allHeaders = array_merge($defaultHeaders, $headers);

        while ($attempt <= $this->retries) {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_MAXREDIRS      => 5,
                CURLOPT_CONNECTTIMEOUT => 4,
                CURLOPT_TIMEOUT        => $this->timeout,
                CURLOPT_USERAGENT      => $this->userAgent,
                CURLOPT_HTTPHEADER     => $allHeaders,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_ENCODING       => '',
            ]);
            $body   = curl_exec($ch);
            $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error  = curl_error($ch);
            curl_close($ch);

            if ($body !== false && $status >= 200 && $status < 400) {
                return ['ok' => true, 'status' => $status, 'body' => $body, 'error' => null];
            }
            $lastError = $error ?: ('HTTP ' . $status);
            $attempt++;
            if ($attempt <= $this->retries) {
                usleep(300_000); // 0.3s backoff
            }
        }

        return ['ok' => false, 'status' => 0, 'body' => '', 'error' => $lastError];
    }
}
