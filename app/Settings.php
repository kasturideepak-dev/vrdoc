<?php
declare(strict_types=1);

final class Settings
{
    private static ?array $all = null;

    public static function all(): array
    {
        if (self::$all !== null) {
            return self::$all;
        }
        $rows = Database::all('SELECT setting_key, setting_value FROM settings');
        self::$all = [];
        foreach ($rows as $r) {
            self::$all[$r['setting_key']] = $r['setting_value'];
        }
        return self::$all;
    }

    public static function get(string $key, string $default = ''): string
    {
        return (string) (self::all()[$key] ?? $default);
    }

    public static function set(string $key, string $value): void
    {
        Database::query(
            'INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)
             ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)',
            [$key, $value]
        );
        self::$all = null;
        Cache::flush();
    }

    public static function many(array $pairs): void
    {
        foreach ($pairs as $k => $v) {
            self::set((string) $k, (string) $v);
        }
    }

    public static function reload(): void
    {
        self::$all = null;
    }

    public static function brandLogoPath(): string
    {
        return '/assets/img/brand/logo.webp';
    }

    public static function brandFaviconPath(): string
    {
        return '/assets/img/brand/favicon.webp';
    }

    /** Resolve logo URL — matches vrdoctors.in brand asset. */
    public static function logoUrl(string $asset = '/assets/'): string
    {
        $logo = trim(self::get('logo', ''));
        $fallback = self::brandLogoPath();
        $legacy = [
            '',
            '/logo.webp',
            'logo.webp',
            '/assets/img/brand/logo.png',
            'assets/img/brand/logo.png',
            '/assets/site/images/logo.webp',
        ];
        if (in_array($logo, $legacy, true)) {
            return $fallback;
        }
        if (str_starts_with($logo, 'http://') || str_starts_with($logo, 'https://')) {
            return $logo;
        }
        if (str_starts_with($logo, '/')) {
            $path = ROOT . '/public' . $logo;
            return is_file($path) ? $logo : $fallback;
        }
        return $logo;
    }

    public static function faviconUrl(string $asset = '/assets/'): string
    {
        $icon = trim(self::get('favicon', ''));
        $fallback = self::brandFaviconPath();
        $legacy = [
            '',
            '/favicon.ico',
            '/assets/img/brand/favicon.png',
        ];
        if (in_array($icon, $legacy, true)) {
            return $fallback;
        }
        if (str_starts_with($icon, 'http://') || str_starts_with($icon, 'https://')) {
            return $icon;
        }
        if (str_starts_with($icon, '/')) {
            $path = ROOT . '/public' . $icon;
            return is_file($path) ? $icon : $fallback;
        }
        return $icon;
    }

    /** Upsert vrdoctors.in logo + favicon on upgraded installs. */
    public static function ensureBrandAssets(): void
    {
        static $done = false;
        if ($done) {
            return;
        }
        $done = true;
        $logo = trim(self::get('logo', ''));
        $legacyLogo = ['', '/logo.webp', '/assets/img/brand/logo.png', '/assets/site/images/logo.webp'];
        if (in_array($logo, $legacyLogo, true)) {
            self::set('logo', self::brandLogoPath());
        }
        $fav = trim(self::get('favicon', ''));
        if ($fav === '' || $fav === '/favicon.ico' || $fav === '/assets/img/brand/favicon.png') {
            self::set('favicon', self::brandFaviconPath());
        }
    }
}
