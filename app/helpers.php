<?php
declare(strict_types=1);

/**
 * Root-relative path with the site-wide trailing-slash convention.
 * Files with extensions (sitemap.xml, robots.txt, assets) are left as-is.
 */
function path_url(string $path): string
{
    if ($path === '' || $path === '/') {
        return '/';
    }
    if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
        return $path;
    }
    $path = '/' . ltrim($path, '/');
    $last = basename(parse_url($path, PHP_URL_PATH) ?: $path);
    if (str_contains($last, '.') && !str_ends_with($last, '.html')) {
        return $path;
    }
    return rtrim($path, '/') . '/';
}

/** Absolute production URL using BASE_URL. */
function url(string $path = '/'): string
{
    if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
        return $path;
    }
    return rtrim(BASE_URL, '/') . path_url($path);
}

function asset_url(string $path): string
{
    $path = ltrim($path, '/');
    return rtrim(BASE_URL, '/') . '/' . $path;
}

/** Admin CSS/JS. Lives in public/assets-admin; also copied to /assets-admin on Hostinger. */
function admin_asset(string $path): string
{
    $path = ltrim($path, '/');
    return '/assets-admin/' . $path . asset_version(ROOT . '/public/assets-admin/' . $path);
}

/**
 * "?v=<mtime>" for a file on disk. The CDN tells browsers to keep CSS/JS for
 * a week, so an unversioned URL keeps serving the old file after a deploy.
 */
function asset_version(string $file): string
{
    $t = @filemtime($file);
    return $t ? '?v=' . base_convert((string) $t, 10, 36) : '';
}

/** Versioned URL for a file under assets/ (public site). */
function site_asset(string $asset, string $path): string
{
    $path = ltrim($path, '/');
    return $asset . $path . asset_version(ROOT . '/assets/' . $path);
}

/**
 * Serve /assets-admin and /uploads from disk when LiteSpeed sends them through index.php.
 * Hostinger’s document root is the project folder, so those files sit under public/.
 */
function serve_static_if_needed(): void
{
    if (!defined('ROOT') || PHP_SAPI === 'cli' || PHP_SAPI === 'phpdbg') {
        return;
    }
    $method = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
    if ($method !== 'GET' && $method !== 'HEAD') {
        return;
    }
    $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $uri = rawurldecode($uri);

    $map = [
        '/assets-admin/' => [ROOT . '/assets-admin/', ROOT . '/public/assets-admin/'],
        '/public/assets-admin/' => [ROOT . '/public/assets-admin/', ROOT . '/assets-admin/'],
        '/assets/' => [ROOT . '/assets/', ROOT . '/public/assets/'],
        '/public/assets/' => [ROOT . '/public/assets/', ROOT . '/assets/'],
        '/uploads/' => [ROOT . '/public/uploads/'],
        '/public/uploads/' => [ROOT . '/public/uploads/'],
    ];

    $rel = null;
    $bases = [];
    foreach ($map as $prefix => $dirs) {
        if (str_starts_with($uri, $prefix)) {
            $rel = substr($uri, strlen($prefix));
            $bases = $dirs;
            break;
        }
    }
    if ($rel === null || $rel === '' || str_contains($rel, '..') || str_contains($rel, "\0")) {
        return;
    }

    $file = null;
    foreach ($bases as $base) {
        $candidate = rtrim($base, '/\\') . '/' . $rel;
        if (!is_file($candidate)) {
            continue;
        }
        $real = realpath($candidate);
        $baseReal = realpath(rtrim($base, '/\\'));
        if ($real === false || $baseReal === false) {
            continue;
        }
        $basePrefix = $baseReal . DIRECTORY_SEPARATOR;
        if (!str_starts_with($real, $basePrefix)) {
            continue;
        }
        $file = $real;
        break;
    }
    if ($file === null) {
        return;
    }

    $ext = strtolower((string) pathinfo($file, PATHINFO_EXTENSION));
    $mimes = [
        'css' => 'text/css; charset=utf-8',
        'js' => 'application/javascript; charset=utf-8',
        'mjs' => 'application/javascript; charset=utf-8',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'webp' => 'image/webp',
        'svg' => 'image/svg+xml',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf' => 'font/ttf',
        'pdf' => 'application/pdf',
        'ico' => 'image/x-icon',
        'map' => 'application/json',
    ];
    header('Content-Type: ' . ($mimes[$ext] ?? 'application/octet-stream'));
    header('X-Content-Type-Options: nosniff');
    header('Cache-Control: public, max-age=604800');
    header('Content-Length: ' . (string) filesize($file));
    if ($method === 'HEAD') {
        exit;
    }
    readfile($file);
    exit;
}

function app_config(?string $key = null, mixed $default = null): mixed
{
    static $cfg = null;
    if ($cfg === null) {
        $cfg = require ROOT . '/config/app.php';
    }
    if ($key === null) {
        return $cfg;
    }
    return $cfg[$key] ?? $default;
}

/**
 * Inline 16px stroke icon for admin buttons. Decorative: the button beside it
 * always carries the text (visible or as aria-label).
 */
function admin_icon(string $name): string
{
    $paths = [
        'view' => '<path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/>',
        'hide' => '<path d="M3 3l18 18"/><path d="M10.6 5.1A10.4 10.4 0 0 1 12 5c6.4 0 10 7 10 7a17 17 0 0 1-3.2 4.2M6.6 6.6C3.8 8.4 2 12 2 12s3.6 7 10 7a9.7 9.7 0 0 0 5.4-1.6"/><path d="M9.9 9.9a3 3 0 0 0 4.2 4.2"/>',
        'publish' => '<path d="M20 6L9 17l-5-5"/>',
        'edit' => '<path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/>',
        'trash' => '<path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/>',
        'restore' => '<path d="M3 12a9 9 0 1 0 3-6.7L3 8"/><path d="M3 3v5h5"/>',
        'delete' => '<path d="M18 6L6 18M6 6l12 12"/>',
        'inbox' => '<path d="M22 12h-6l-2 3h-4l-2-3H2"/><path d="M5.5 5h13L22 12v6a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2v-6z"/>',
    ];
    return '<svg class="act__ico" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">'
        . ($paths[$name] ?? '') . '</svg>';
}
