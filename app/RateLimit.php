<?php
declare(strict_types=1);

/**
 * Fixed-window request counters kept in MySQL, so limits hold across PHP
 * workers. One row per bucket ("api:<ip>", "forgot:email:<hash>" …); a hit
 * is a single upsert.
 */
final class RateLimit
{
    /**
     * Count one hit against $bucket and report whether it is over $max hits
     * per $window seconds. Fails open: if the table is unavailable the site
     * keeps working rather than locking everyone out.
     */
    public static function hit(string $bucket, int $max, int $window): bool
    {
        try {
            self::ensureSchema();
            $start = intdiv(time(), $window) * $window;
            $key = substr($bucket, 0, 120);
            Database::query(
                'INSERT INTO rate_limits (bucket, window_start, hits) VALUES (?, ?, 1)
                 ON DUPLICATE KEY UPDATE
                   hits = IF(window_start = VALUES(window_start), hits + 1, 1),
                   window_start = VALUES(window_start)',
                [$key, $start]
            );
            $row = Database::one('SELECT hits FROM rate_limits WHERE bucket = ?', [$key]);
            if (random_int(1, 200) === 1) {
                Database::query('DELETE FROM rate_limits WHERE window_start < ?', [time() - 86400]);
            }
            return (int) ($row['hits'] ?? 0) > $max;
        } catch (Throwable $e) {
            error_log('RateLimit: ' . $e->getMessage());
            return false;
        }
    }

    /** Seconds until the current window for $window resets (for Retry-After). */
    public static function retryAfter(int $window): int
    {
        return $window - (time() % $window);
    }

    /** Emails and other personal values are hashed before they become keys. */
    public static function key(string $value): string
    {
        return substr(hash('sha256', strtolower(trim($value))), 0, 32);
    }

    private static function ensureSchema(): void
    {
        static $done = false;
        if ($done) {
            return;
        }
        $done = true;
        Database::pdo()->exec(
            'CREATE TABLE IF NOT EXISTS rate_limits (
               bucket VARCHAR(120) NOT NULL,
               window_start INT UNSIGNED NOT NULL,
               hits INT UNSIGNED NOT NULL DEFAULT 0,
               PRIMARY KEY (bucket),
               KEY idx_rl_window (window_start)
             ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
        );
    }
}
