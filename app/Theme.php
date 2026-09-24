<?php
declare(strict_types=1);

/**
 * VR Doctors public theme: MySQL-backed data for the PHP templates.
 */
final class Theme
{
    public static function boot(): void
    {
        if (defined('VRDR_INSTALLING') && VRDR_INSTALLING) {
            return;
        }
        require_once ROOT . '/app/WpShim.php';
        foreach ([
            'fallback-data.php',
            'home-meta.php',
            'page-meta.php',
            'neet-landing-schemas.php',
            'neet-landing-helpers.php',
        ] as $file) {
            $path = ROOT . '/app/theme/' . $file;
            if (is_file($path)) {
                require_once $path;
            }
        }
    }

    public static function wpSettings(): array
    {
        $s = class_exists('Settings') ? Settings::all() : [];
        $phones = array_filter([
            $s['phone_primary'] ?? '',
            $s['phone_secondary'] ?? '',
            $s['phone_3'] ?? '',
            $s['phone_4'] ?? '',
        ]);
        if (!$phones) {
            $phones = ['9256925640', '9256925641', '9256925642', '9256925643'];
        }
        $digits = [];
        foreach ($phones as $p) {
            $digits[] = preg_replace('/\D+/', '', (string) $p);
        }
        $addr = $s['head_office'] ?? "VR Doctors Academy\nPlot No 29, Mathrusree Nagar\nHafeezpet, Miyapur\nHyderabad, Telangana 500049";
        return [
            'phones' => implode("\n", $digits),
            'email' => $s['email'] ?? 'admissions@vrdoctors.in',
            'address' => $addr,
            'maps_url' => $s['maps_url'] ?? 'https://maps.app.goo.gl/3qrkSSCw6kiYNkwH8',
            'whatsapp' => preg_replace('/\D+/', '', $s['whatsapp'] ?? '919256925640') ?: '919256925640',
            'whatsapp_text' => $s['whatsapp_text'] ?? 'Hi, I want to know about VR Doctors admissions',
            'facebook' => $s['facebook'] ?? 'https://www.facebook.com/VR.Jr.College',
            'instagram' => $s['instagram'] ?? 'https://www.instagram.com/vr_junior.college/',
            'youtube' => $s['youtube'] ?? 'https://www.youtube.com/@VR_JuniorCollege',
            'cf7_shortcode' => '',
            'cf7_hero_shortcode' => '',
        ];
    }

    public static function printHead(): void
    {
        $seo = $GLOBALS['vr_view_seo'] ?? [];
        $s = class_exists('Settings') ? Settings::all() : [];
        $title = $seo['seo_title'] ?? ($s['default_seo_title'] ?? 'VR Doctors Academy');
        $desc = $seo['meta_description'] ?? ($s['default_seo_description'] ?? '');
        $canon = $seo['canonical_url'] ?? (class_exists('Request') ? url(Request::path()) : '/');
        $og = $seo['og_image'] ?? ($s['og_image'] ?? (class_exists('Settings') ? Settings::logoUrl() : '/assets/img/brand/logo.webp'));
        if ($og && !str_starts_with((string) $og, 'http')) {
            $og = url((string) $og);
        }
        $robots = $seo['robots'] ?? 'index,follow';
        $e = static fn (string $v): string => htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
        echo '<title>' . $e($title) . '</title>' . "\n";
        if ($desc !== '') {
            echo '<meta name="description" content="' . $e($desc) . '">' . "\n";
        }
        echo '<link rel="canonical" href="' . $e($canon) . '">' . "\n";
        echo '<meta name="robots" content="' . $e($robots) . '">' . "\n";
        echo '<meta property="og:type" content="website">' . "\n";
        echo '<meta property="og:title" content="' . $e($seo['og_title'] ?? $title) . '">' . "\n";
        echo '<meta property="og:description" content="' . $e($seo['og_description'] ?? $desc) . '">' . "\n";
        echo '<meta property="og:image" content="' . $e((string) $og) . '">' . "\n";
        echo '<meta property="og:url" content="' . $e($canon) . '">' . "\n";
        $favicon = class_exists('Settings') ? Settings::faviconUrl() : ($s['favicon'] ?? '/assets/img/brand/favicon.webp');
        echo '<link rel="icon" href="' . $e($favicon) . '">' . "\n";
        echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
        echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
        // The hero image is the largest element on the homepage; the browser
        // only discovers it after the HTML parses, so point at it early.
        if ((Request::path() === '/' || Request::path() === '') && function_exists('vr_get_home_hero')) {
            $slides = vr_get_home_hero()['slides'] ?? [];
            $firstImage = (string) ($slides[0]['image'] ?? '');
            if ($firstImage !== '') {
                // Same URL the <img> will request (versioned), or it downloads twice.
                echo '<link rel="preload" as="image" fetchpriority="high" href="' . $e(vr_media_url($firstImage)) . '">' . "\n";
            }
        }
        // Fonts and icons load without blocking the first paint: the browser
        // paints with system fonts, then swaps. Tailwind is a prebuilt file
        // (see build/README.md); the Play CDN used to ship a compiler here.
        $async = static function (string $href) use ($e): void {
            echo '<link rel="stylesheet" href="' . $e($href) . '" media="print" onload="this.media=\'all\';this.onload=null">' . "\n";
            echo '<noscript><link rel="stylesheet" href="' . $e($href) . '"></noscript>' . "\n";
        };
        $async('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap');
        echo '<link rel="stylesheet" href="' . $e(site_asset('/assets/', 'site/css/tailwind.css')) . '">' . "\n";
        echo '<link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>' . "\n";
        $async('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css');
        echo '<link rel="stylesheet" href="' . $e(VR_DOCTORS_URI . '/style.css?v=' . VR_DOCTORS_VERSION) . '">' . "\n";
        foreach ($GLOBALS['vr_extra_stylesheets'] ?? [] as $href) {
            if (is_string($href) && $href !== '') {
                echo '<link rel="stylesheet" href="' . $e($href) . '">' . "\n";
            }
        }
        if (!empty($s['schema_json'])) {
            echo '<script type="application/ld+json">' . $s['schema_json'] . '</script>' . "\n";
        }
        if (class_exists('Csrf')) {
            echo '<meta name="csrf-token" content="' . $e(Csrf::token()) . '">' . "\n";
        }
        if (class_exists('Snippets')) {
            Snippets::emit('header', $GLOBALS['snippetCtx'] ?? []);
        }
    }

    /** CPT rows in the shape the WordPress templates expect. */
    public static function orderedPosts(string $postType, array $fallback = []): array
    {
        $map = [
            'faculty' => 'faculty',
            'testimonial' => 'testimonials',
            'achiever' => 'achievers',
            'timeline_event' => 'timeline',
        ];
        if ($postType === 'testimonials' || $postType === 'testimonial') {
            $rows = Database::all('SELECT * FROM testimonials WHERE is_visible = 1 ORDER BY sort_order, id');
            if (!$rows) {
                return $fallback;
            }
            $out = [];
            foreach ($rows as $r) {
                $role = (string) ($r['role'] ?? '');
                $parts = array_map('trim', explode('·', $role));
                $out[] = [
                    'id' => (int) $r['id'],
                    'title' => $r['name'],
                    'content' => $r['quote'],
                    'image' => '',
                    'meta' => [
                        'college' => $parts[0] ?? $role,
                        'rank_badge' => $parts[1] ?? '',
                        'quote' => $r['quote'],
                    ],
                ];
            }
            return $out;
        }
        $slug = $map[$postType] ?? $postType;
        if (!class_exists('Cpt')) {
            return $fallback;
        }
        $rows = Cpt::published($slug);
        if (!$rows) {
            return $fallback;
        }
        $out = [];
        foreach ($rows as $r) {
            $f = $r['_fields'] ?? [];
            $img = (string) ($r['featured_image'] ?? '');
            if ($img !== '' && !str_starts_with($img, 'http')) {
                $img = '/' . ltrim($img, '/');
                if (!str_starts_with($img, '/assets/site/')) {
                    $img = '/assets/site/images' . $img;
                }
            }
            $out[] = [
                'id' => (int) $r['id'],
                'title' => $r['title'],
                'content' => $r['excerpt'] ?? '',
                'image' => $img,
                'meta' => [
                    'subject' => (string) ($f['designation'] ?? $f['department'] ?? $f['subject'] ?? ''),
                    'experience' => (string) ($f['experience'] ?? ''),
                    'college' => (string) ($f['college'] ?? ''),
                    'rank_badge' => (string) ($f['marks'] ?? $f['rank'] ?? ''),
                    'quote' => (string) ($f['quote'] ?? ''),
                    'rank' => (string) ($f['marks'] ?? $f['rank'] ?? ''),
                    'year' => (string) ($f['year'] ?? ''),
                    'order' => (string) ($r['sort_order'] ?? ''),
                    'category' => (string) ($f['category'] ?? ''),
                    'duration' => (string) ($f['duration'] ?? ''),
                    'entrance' => (string) ($f['entrance'] ?? ''),
                    'focus' => (string) ($f['focus'] ?? ''),
                    'paths' => (string) ($f['paths'] ?? ''),
                ],
            ];
        }
        return $out;
    }

    public static function enquiryForm(string $leadSource = 'Website', string $interested = ''): void
    {
        $csrf = class_exists('Csrf') ? Csrf::field() : '';
        $src = htmlspecialchars($leadSource, ENT_QUOTES, 'UTF-8');
        $course = htmlspecialchars($interested, ENT_QUOTES, 'UTF-8');
        echo '<form method="post" action="/enquire/" class="space-y-4" autocomplete="on">';
        echo $csrf;
        echo '<input type="hidden" name="form" value="appointment">';
        echo '<input type="hidden" name="message" value="' . $src . '">';
        echo '<p class="hidden" aria-hidden="true"><label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label></p>';
        echo '<div class="grid md:grid-cols-2 gap-4">';
        echo '<label class="block text-sm font-semibold text-blue-900">Student Name *<input class="mt-1 w-full border border-gray-300 rounded-xl px-3 py-2.5" type="text" name="student_name" required maxlength="80"></label>';
        echo '<label class="block text-sm font-semibold text-blue-900">Parent Name *<input class="mt-1 w-full border border-gray-300 rounded-xl px-3 py-2.5" type="text" name="parent_name" required maxlength="80"></label>';
        echo '<label class="block text-sm font-semibold text-blue-900">Mobile Number *<input class="mt-1 w-full border border-gray-300 rounded-xl px-3 py-2.5" type="tel" name="mobile" required pattern="[6-9][0-9]{9}" maxlength="10" inputmode="numeric"></label>';
        echo '<label class="block text-sm font-semibold text-blue-900">Current Class *<select class="mt-1 w-full border border-gray-300 rounded-xl px-3 py-2.5 bg-white" name="current_class" required>';
        echo '<option value="">Select Current Class</option>';
        foreach (['10th Appearing', 'Completed 10th', 'Inter 1st Year', 'Inter 2nd Year', 'Repeater'] as $opt) {
            echo '<option value="' . htmlspecialchars($opt, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($opt, ENT_QUOTES, 'UTF-8') . '</option>';
        }
        echo '</select></label>';
        echo '<label class="block text-sm font-semibold text-blue-900">Interested Course *<select class="mt-1 w-full border border-gray-300 rounded-xl px-3 py-2.5 bg-white" name="interested_course" required>';
        echo '<option value="">Select Course</option>';
        $courses = ['Intermediate + NEET', 'Long-Term NEET', 'Short-Term Revision'];
        foreach ($courses as $opt) {
            $sel = ($course !== '' && str_contains($course, explode(' ', $opt)[0])) ? ' selected' : '';
            echo '<option value="' . htmlspecialchars($opt, ENT_QUOTES, 'UTF-8') . '"' . $sel . '>' . htmlspecialchars($opt, ENT_QUOTES, 'UTF-8') . '</option>';
        }
        echo '</select></label>';
        echo '<label class="block text-sm font-semibold text-blue-900">Residential Program<select class="mt-1 w-full border border-gray-300 rounded-xl px-3 py-2.5 bg-white" name="residential">';
        echo '<option value="">Select Option</option><option value="Yes">Yes</option><option value="No">No</option></select></label>';
        echo '</div>';
        echo '<button type="submit" class="w-full md:w-auto bg-orange-500 hover:bg-orange-600 text-white font-semibold px-8 py-3 rounded-xl">Request Callback</button>';
        echo '</form>';
    }
}

function vr_img($path): string
{
    $path = ltrim(str_replace('\\', '/', (string) $path), '/');
    return trailingslashit(VR_DOCTORS_URI) . 'images/' . $path;
}

function vr_img_e($path): void
{
    echo esc_url(vr_img($path));
}

function vr_option($key, $default = '')
{
    $opts = get_option('vr_doctors_settings', []);
    if (isset($opts[$key]) && $opts[$key] !== '') {
        return $opts[$key];
    }
    return $default;
}

function vr_phones(): array
{
    $raw = vr_option('phones', "9256925640\n9256925641\n9256925642\n9256925643");
    $lines = preg_split('/\r\n|\r|\n/', (string) $raw) ?: [];
    return array_values(array_filter(array_map('trim', $lines)));
}

function vr_whatsapp_url(): string
{
    $num = preg_replace('/\D+/', '', (string) vr_option('whatsapp', '919256925640'));
    $text = rawurlencode((string) vr_option('whatsapp_text', 'Hi, I want to know about VR Doctors admissions'));
    return 'https://wa.me/' . $num . '?text=' . $text;
}

function vr_esc_url($url): string
{
    $url = trim((string) $url);
    if ($url === '') {
        return '';
    }
    if (str_starts_with($url, '#')) {
        $frag = preg_replace('/[^A-Za-z0-9\-_.:]/', '', substr($url, 1)) ?? '';
        return '#' . $frag;
    }
    return esc_url($url);
}

function vr_sanitize_fa_icon($icon, $fallback = 'fa-circle'): string
{
    $icon = trim((string) $icon);
    if (preg_match('/^fa-[a-z0-9-]+$/i', $icon)) {
        return strtolower($icon);
    }
    return 'fa-circle';
}

function vr_get_ordered_posts($post_type, $fallback = []): array
{
    return Theme::orderedPosts((string) $post_type, is_array($fallback) ? $fallback : []);
}

function vr_render_cf7($option_key = 'cf7_shortcode', $fallback = ''): void
{
    Theme::enquiryForm('Website Contact Page');
}

function vr_default_cf7_shortcode(): string
{
    return '';
}

function vr_safe_cf7_shortcode($raw, $fallback = ''): string
{
    return '';
}
