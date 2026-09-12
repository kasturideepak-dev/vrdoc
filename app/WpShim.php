<?php
declare(strict_types=1);

/**
 * Minimal WordPress API shims so the VR Doctors theme templates run
 * on this CMS (MySQL) without WordPress.
 */
if (!defined('ABSPATH')) {
    define('ABSPATH', ROOT . '/');
}
if (!defined('VR_DOCTORS_VERSION')) {
    define('VR_DOCTORS_VERSION', '1.5.1');
}
if (!defined('VR_DOCTORS_DIR')) {
    define('VR_DOCTORS_DIR', ROOT . '/views/public/site');
}
if (!defined('VR_DOCTORS_URI')) {
    define('VR_DOCTORS_URI', '/assets/site');
}

function get_template_directory(): string
{
    return VR_DOCTORS_DIR;
}

function get_template_directory_uri(): string
{
    return VR_DOCTORS_URI;
}

function get_stylesheet_uri(): string
{
    return VR_DOCTORS_URI . '/style.css';
}

function trailingslashit($value): string
{
    return rtrim((string) $value, '/\\') . '/';
}

function home_url($path = '/'): string
{
    $path = (string) $path;
    if ($path === '' || $path === '/') {
        return path_url('/');
    }
    return path_url($path);
}

function esc_html($text): string
{
    return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
}

function esc_attr($text): string
{
    return esc_html($text);
}

function esc_url($url, $protocols = null): string
{
    return htmlspecialchars((string) $url, ENT_QUOTES, 'UTF-8');
}

function esc_url_raw($url): string
{
    return trim((string) $url);
}

function esc_textarea($text): string
{
    return esc_html($text);
}

function esc_html__($text, $domain = null): string
{
    return (string) $text;
}

function esc_html_e($text, $domain = null): void
{
    echo esc_html($text);
}

function __($text, $domain = null): string
{
    return (string) $text;
}

function _e($text, $domain = null): void
{
    echo (string) $text;
}

function sanitize_text_field($str): string
{
    return trim(strip_tags((string) $str));
}

function sanitize_textarea_field($str): string
{
    return trim((string) $str);
}

function sanitize_title($title): string
{
    $title = strtolower(trim((string) $title));
    $title = preg_replace('/[^a-z0-9]+/', '-', $title) ?? $title;
    return trim($title, '-');
}

function wp_strip_all_tags($string): string
{
    return trim(strip_tags((string) $string));
}

function wp_unslash($value)
{
    return $value;
}

function absint($maybeint): int
{
    return abs((int) $maybeint);
}

function wp_parse_args($args, $defaults = []): array
{
    $args = is_array($args) ? $args : [];
    $defaults = is_array($defaults) ? $defaults : [];
    return array_merge($defaults, $args);
}

function get_option($option, $default = false)
{
    if ($option === 'vr_doctors_settings' && class_exists('Theme')) {
        return Theme::wpSettings();
    }
    if ($option === 'page_on_front') {
        return 0;
    }
    return $default;
}

function update_option($option, $value): bool
{
    return true;
}

function get_post_meta($post_id, $key = '', $single = false)
{
    return $single ? '' : [];
}

function update_post_meta($post_id, $meta_key, $meta_value): bool
{
    return true;
}

function get_page_by_path($page_path)
{
    return null;
}

function get_post($post = null)
{
    return null;
}

function get_posts($args = []): array
{
    return [];
}

function get_queried_object()
{
    return null;
}

function is_singular($post_types = ''): bool
{
    return false;
}

function is_front_page(): bool
{
    return class_exists('Request') && Request::path() === '/';
}

function is_page($page = ''): bool
{
    if (!class_exists('Request')) {
        return false;
    }
    $path = trim(Request::path(), '/');
    if (is_array($page)) {
        return in_array($path, $page, true);
    }
    return $path === trim((string) $page, '/');
}

function is_admin(): bool
{
    return false;
}

function add_action($hook, $callback, $priority = 10, $accepted_args = 1): void
{
}

function add_filter($hook, $callback, $priority = 10, $accepted_args = 1): void
{
}

function add_meta_box($id, $title, $callback, $screen = null): void
{
}

function add_theme_support($feature, ...$args): void
{
}

function register_nav_menus($locations = []): void
{
}

function add_image_size($name, $width, $height, $crop = false): void
{
}

function add_options_page(...$args): void
{
}

function wp_enqueue_style(...$args): void
{
}

function wp_enqueue_script(...$args): void
{
}

function wp_localize_script(...$args): void
{
}

function wp_nonce_field(...$args): void
{
}

function wp_verify_nonce($nonce, $action): bool
{
    return true;
}

function shortcode_exists($tag): bool
{
    return false;
}

function do_shortcode($content): string
{
    return '';
}

function language_attributes(): void
{
    echo 'lang="en"';
}

function bloginfo($show = ''): void
{
    if ($show === 'charset') {
        echo 'UTF-8';
    }
}

function body_class($class = ''): void
{
    echo 'class="min-h-full flex flex-col"';
}

function wp_body_open(): void
{
}

function wp_head(): void
{
    Theme::printHead();
}

function wp_footer(): void
{
    echo '<script src="' . htmlspecialchars(VR_DOCTORS_URI . '/js/main.js?v=' . VR_DOCTORS_VERSION, ENT_QUOTES, 'UTF-8') . '"></script>' . "\n";
    foreach ($GLOBALS['vr_extra_scripts'] ?? [] as $src) {
        if (is_string($src) && $src !== '') {
            echo '<script src="' . htmlspecialchars($src, ENT_QUOTES, 'UTF-8') . '" defer></script>' . "\n";
        }
    }
    if (class_exists('Snippets')) {
        Snippets::emit('footer', $GLOBALS['snippetCtx'] ?? []);
    }
}

function get_header($name = null): void
{
    require ROOT . '/views/public/site/header.php';
}

function get_footer($name = null): void
{
    require ROOT . '/views/public/site/footer.php';
}

function get_template_part($slug, $name = null, $args = []): void
{
    $file = ROOT . '/views/public/site/' . $slug;
    if ($name) {
        $file .= '-' . $name;
    }
    $file .= '.php';
    if (is_file($file)) {
        if ($args) {
            extract($args, EXTR_SKIP);
        }
        require $file;
    }
}

function wp_get_attachment_image_url($id, $size = 'full')
{
    return false;
}

function wp_reset_postdata(): void
{
}

function get_the_ID(): int
{
    return 0;
}

function get_the_title($post = 0): string
{
    return '';
}

function get_the_content($more_link_text = null, $strip_teaser = false): string
{
    return '';
}

function get_the_post_thumbnail_url($post = null, $size = 'post-thumbnail')
{
    return false;
}

function plugin_dir_url($file): string
{
    return '/';
}

function current_user_can($cap): bool
{
    return false;
}

function checked($checked, $current = true, $display = true)
{
    $out = ((string) $checked === (string) $current) ? ' checked="checked"' : '';
    if ($display) {
        echo $out;
    }
    return $out;
}

function selected($selected, $current = true, $display = true)
{
    $out = ((string) $selected === (string) $current) ? ' selected="selected"' : '';
    if ($display) {
        echo $out;
    }
    return $out;
}

function wp_die($message = '', $title = '', $args = []): void
{
    throw new RuntimeException((string) $message);
}
