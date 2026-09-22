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

    /**
     * Contact details for the public site, in one place.
     *
     * Templates used `$s['phone_primary'] ?? '…'`, which only catches a MISSING
     * key — an empty setting slipped through and left "Call" with nothing under
     * it. The fallbacks were also stale numbers that no longer belong to the
     * academy. Here empty counts as missing, and phone/WhatsApp/email fall back
     * to the real details; head office and hours are left empty so templates
     * can hide the row instead of printing a bare label.
     *
     * @return array{phone1:string,phone1tel:string,phone2:string,phone2tel:string,whatsapp:string,email:string,head_office:string,hours:string}
     */
    public static function contact(): array
    {
        $all = self::all();
        $get = static function (string $k, string $fallback = '') use ($all): string {
            $v = trim((string) ($all[$k] ?? ''));
            return $v !== '' ? $v : $fallback;
        };
        $phone1 = $get('phone_primary', '+91 9256 9256 40');
        $phone2 = $get('phone_secondary', '+91 9256 9256 41');
        return [
            'phone1' => $phone1,
            'phone1tel' => preg_replace('/\D+/', '', $phone1) ?: '919256925640',
            'phone2' => $phone2,
            'phone2tel' => preg_replace('/\D+/', '', $phone2) ?: '919256925641',
            'whatsapp' => preg_replace('/\D+/', '', $get('whatsapp', '919256925640')) ?: '919256925640',
            'email' => $get('email', 'admissions@vrdoctors.in'),
            'head_office' => $get('head_office'),
            'hours' => $get('hours'),
        ];
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
