<?php
declare(strict_types=1);

final class Slug
{
    /**
     * Slug columns are VARCHAR(190) at their widest (80 for post types and
     * taxonomies), so slugs are capped on a word boundary: a long title used
     * to produce a 479-character slug and saving died with "Data too long".
     * $max leaves room for the "-2" suffix uniqueness may append.
     */
    public static function make(string $text, int $max = 150): string
    {
        $s = strtolower(trim($text));
        // Strip accents first; //TRANSLIT alone turns "é" into "'e" on macOS.
        if (function_exists('transliterator_transliterate')) {
            $s = transliterator_transliterate('Any-Latin; Latin-ASCII; Lower()', $s) ?: $s;
        }
        $s = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s) ?: $s;
        $s = preg_replace('/[^a-z0-9]+/i', '-', $s) ?? '';
        $s = trim($s, '-');
        if ($max > 0 && strlen($s) > $max) {
            $cut = substr($s, 0, $max);
            $lastDash = strrpos($cut, '-');
            $s = trim($lastDash !== false && $lastDash > (int) ($max * 0.6) ? substr($cut, 0, $lastDash) : $cut, '-');
        }
        return $s !== '' ? $s : 'item';
    }

    public static function normalizePath(string $path): string
    {
        $path = strtolower(trim($path));
        $path = '/' . trim($path, '/');
        return $path === '/' ? '/' : $path . '/';
    }

    public static function isReserved(string $slug): bool
    {
        $slug = strtolower(trim($slug, '/'));
        if ($slug === '') {
            return false;
        }
        $app = app_config();
        if (in_array($slug, $app['reserved_slugs'], true)) {
            return true;
        }
        return false;
    }

    /** First URL segment collisions: pages, public CPT archives, reserved. */
    public static function reservedRoutes(): array
    {
        $list = app_config('reserved_slugs', []);
        foreach (Database::all('SELECT slug FROM pages WHERE deleted_at IS NULL') as $p) {
            $s = trim($p['slug'], '/');
            if ($s !== '' && $s !== '/') {
                $list[] = explode('/', $s)[0];
            }
        }
        foreach (Database::all('SELECT slug FROM post_types WHERE status = "active" AND public = 1') as $t) {
            $list[] = $t['slug'];
        }
        return array_values(array_unique($list));
    }

    public static function firstSegmentTaken(string $slug, array $except = []): bool
    {
        $slug = strtolower(trim($slug, '/'));
        if ($slug === '' || $slug === '/') {
            return false;
        }
        $first = explode('/', $slug)[0];
        if (self::isReserved($first) && empty($except['allow_reserved'])) {
            return true;
        }
        $pageExcept = $except['page_id'] ?? 0;
        $sql = 'SELECT id FROM pages WHERE deleted_at IS NULL AND (slug = ? OR slug = ?)';
        $params = [$first, $first . '/'];
        if ($pageExcept) {
            $sql .= ' AND id <> ?';
            $params[] = $pageExcept;
        }
        if (Database::one($sql, $params)) {
            return true;
        }
        $typeExcept = $except['post_type_id'] ?? 0;
        $tsql = 'SELECT id FROM post_types WHERE status = "active" AND public = 1 AND slug = ?';
        $tparams = [$first];
        if ($typeExcept) {
            $tsql .= ' AND id <> ?';
            $tparams[] = $typeExcept;
        }
        return (bool) Database::one($tsql, $tparams);
    }

    public static function uniqueInTable(string $table, string $slug, ?int $ignoreId = null, string $idCol = 'id'): string
    {
        $base = self::make($slug);
        $try = $base;
        $i = 2;
        while (true) {
            $sql = "SELECT `$idCol` FROM `$table` WHERE slug = ?";
            $params = [$try];
            if ($ignoreId) {
                $sql .= " AND `$idCol` <> ?";
                $params[] = $ignoreId;
            }
            if (!Database::one($sql, $params)) {
                return $try;
            }
            $try = $base . '-' . $i;
            $i++;
        }
    }

    public static function uniquePage(string $slug, ?int $ignoreId = null): string
    {
        if ($slug === '/') {
            return '/';
        }
        $base = self::make($slug);
        $try = $base;
        $i = 2;
        while (true) {
            // Trashed rows still hold their slug in the unique index, so they
            // count as taken here — otherwise saving throws a duplicate key.
            $sql = 'SELECT id FROM pages WHERE slug = ?';
            $params = [$try];
            if ($ignoreId) {
                $sql .= ' AND id <> ?';
                $params[] = $ignoreId;
            }
            $taken = Database::one($sql, $params) || self::firstSegmentTaken($try, ['page_id' => $ignoreId ?? 0, 'allow_reserved' => false]);
            if (!$taken) {
                return $try;
            }
            $try = $base . '-' . $i;
            $i++;
        }
    }

    public static function uniqueEntry(int $typeId, string $slug, ?int $ignoreId = null): string
    {
        $base = self::make($slug);
        $try = $base;
        $i = 2;
        while (true) {
            // Trashed entries keep their slug in the unique index (see uniquePage).
            $sql = 'SELECT id FROM cpt_entries WHERE post_type_id = ? AND slug = ?';
            $params = [$typeId, $try];
            if ($ignoreId) {
                $sql .= ' AND id <> ?';
                $params[] = $ignoreId;
            }
            if (!Database::one($sql, $params)) {
                return $try;
            }
            $try = $base . '-' . $i;
            $i++;
        }
    }

    public static function uniquePostType(string $slug, ?int $ignoreId = null): string
    {
        $base = self::make($slug, 60);
        $try = $base;
        $i = 2;
        while (true) {
            if (self::isReserved($try)) {
                $try = $base . '-' . $i;
                $i++;
                continue;
            }
            $sql = 'SELECT id FROM post_types WHERE slug = ?';
            $params = [$try];
            if ($ignoreId) {
                $sql .= ' AND id <> ?';
                $params[] = $ignoreId;
            }
            $page = Database::one('SELECT id FROM pages WHERE deleted_at IS NULL AND slug = ?', [$try]);
            if (!Database::one($sql, $params) && !$page) {
                return $try;
            }
            $try = $base . '-' . $i;
            $i++;
        }
    }
}
