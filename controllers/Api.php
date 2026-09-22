<?php
declare(strict_types=1);

/**
 * Public JSON API for the Next.js frontend.
 * GET is open (published content only). POST forms use honeypot + rate limit.
 */
final class Api
{
    public static function preflight(): never
    {
        self::cors();
        http_response_code(204);
        exit;
    }

    public static function cors(): void
    {
        $origin = Request::header('Origin');
        $allow = Settings::get(
            'api_cors_origins',
            'http://localhost:3000,http://127.0.0.1:3000,https://vr-doctors-clone.vercel.app,https://vrdoctors.in'
        );
        $list = array_filter(array_map('trim', explode(',', $allow)));
        if ($origin !== '' && in_array($origin, $list, true)) {
            header('Access-Control-Allow-Origin: ' . $origin);
            header('Vary: Origin');
        } elseif ($origin === '' && $list) {
            header('Access-Control-Allow-Origin: ' . $list[0]);
        }
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Accept, X-Requested-With');
        header('Access-Control-Max-Age: 86400');
    }

    /**
     * Per-IP limits for the public API: reads are cheap but not free (bootstrap
     * runs ~15 queries), and form posts can trigger captcha checks and email.
     */
    public static function throttle(): void
    {
        $post = Request::method() === 'POST';
        [$max, $window] = $post ? [30, 600] : [120, 60];
        if (!RateLimit::hit(($post ? 'api-post:' : 'api:') . Request::ip(), $max, $window)) {
            return;
        }
        self::cors();
        header('Retry-After: ' . RateLimit::retryAfter($window));
        View::json(['ok' => false, 'error' => 'rate_limited', 'message' => 'Too many requests. Please try again shortly.'], 429);
    }

    public static function bootstrap(): void
    {
        self::cors();
        // Cached for a minute; every admin save calls Cache::flush(), so edits
        // still show straight away.
        $cached = Cache::get('api:bootstrap');
        if ($cached !== null) {
            header('Content-Type: application/json; charset=utf-8');
            header('X-Cache: HIT');
            echo $cached;
            exit;
        }
        $payload = [
            'ok' => true,
            'site' => self::sitePayload(),
            'menus' => self::menuPayload(),
            'testimonials' => self::testimonialRows(),
            'faculty' => self::cptRows('faculty'),
            'courses' => self::cptRows('courses'),
            'achievers' => self::cptRows('achievers'),
            'careers' => self::cptRows('careers'),
            'departments' => self::cptRows('departments'),
            'campuses' => self::cptRows('campuses'),
            'colleges' => self::cptRows('colleges'),
            'videos' => self::cptRows('videos'),
            'timeline' => self::cptRows('timeline'),
            'faqs' => self::faqRows(),
            'pages' => self::pageIndex(),
        ];
        $json = Html::json($payload);
        Cache::set('api:bootstrap', $json, 60);
        header('Content-Type: application/json; charset=utf-8');
        echo $json;
        exit;
    }

    public static function settings(): void
    {
        self::cors();
        View::json(['ok' => true, 'data' => self::sitePayload()]);
    }

    public static function menus(): void
    {
        self::cors();
        View::json(['ok' => true, 'data' => self::menuPayload()]);
    }

    public static function testimonials(): void
    {
        self::cors();
        View::json(['ok' => true, 'data' => self::testimonialRows()]);
    }

    public static function faqs(): void
    {
        self::cors();
        View::json(['ok' => true, 'data' => self::faqRows()]);
    }

    public static function pages(): void
    {
        self::cors();
        View::json(['ok' => true, 'data' => self::pageIndex()]);
    }

    public static function page(string $slug): void
    {
        self::cors();
        $slug = $slug === 'home' ? '/' : trim($slug, '/');
        $page = Database::one(
            'SELECT * FROM pages WHERE slug = ? AND status = "published" AND deleted_at IS NULL',
            [$slug]
        );
        if (!$page) {
            View::json(['ok' => false, 'error' => 'not_found'], 404);
        }
        $seo = Database::one(
            'SELECT * FROM seo_metadata WHERE entity_type = "page" AND entity_id = ?',
            [(int) $page['id']]
        ) ?: [];
        $sections = Database::all(
            'SELECT type, heading, content_json, sort_order, is_visible
             FROM content_sections WHERE owner_type = "page" AND owner_id = ? AND is_visible = 1
             ORDER BY sort_order, id',
            [(int) $page['id']]
        );
        foreach ($sections as &$s) {
            $s['content'] = json_decode((string) $s['content_json'], true) ?: [];
            unset($s['content_json']);
        }
        View::json([
            'ok' => true,
            'data' => [
                'id' => (int) $page['id'],
                'title' => $page['title'],
                'slug' => $page['slug'],
                'seo' => self::seoOut($seo),
                'sections' => $sections,
                'updated_at' => $page['updated_at'],
            ],
        ]);
    }

    public static function contentIndex(string $type): void
    {
        self::cors();
        $pt = Cpt::type($type);
        if (!$pt || $pt['status'] !== 'active') {
            View::json(['ok' => false, 'error' => 'not_found'], 404);
        }
        View::json(['ok' => true, 'type' => self::typeOut($pt), 'data' => self::cptRows($type)]);
    }

    public static function contentOne(string $type, string $slug): void
    {
        self::cors();
        $pt = Cpt::type($type);
        if (!$pt || $pt['status'] !== 'active') {
            View::json(['ok' => false, 'error' => 'not_found'], 404);
        }
        $row = Cpt::entry($type, $slug);
        if (!$row || $row['status'] !== 'published') {
            View::json(['ok' => false, 'error' => 'not_found'], 404);
        }
        $seo = Database::one(
            'SELECT * FROM seo_metadata WHERE entity_type = "cpt" AND entity_id = ?',
            [(int) $row['id']]
        ) ?: [];
        $sections = Database::all(
            'SELECT type, heading, content_json, sort_order
             FROM content_sections WHERE owner_type = "cpt" AND owner_id = ? AND is_visible = 1
             ORDER BY sort_order, id',
            [(int) $row['id']]
        );
        foreach ($sections as &$s) {
            $s['content'] = json_decode((string) $s['content_json'], true) ?: [];
            unset($s['content_json']);
        }
        View::json([
            'ok' => true,
            'type' => self::typeOut($pt),
            'data' => self::entryOut($row) + ['seo' => self::seoOut($seo), 'sections' => $sections],
        ]);
    }

    public static function blogIndex(): void
    {
        self::cors();
        $rows = Database::all(
            'SELECT id, title, slug, excerpt, featured_image, published_at, updated_at
             FROM blog_posts WHERE status = "published" AND deleted_at IS NULL
             ORDER BY published_at DESC, id DESC'
        );
        View::json(['ok' => true, 'data' => $rows]);
    }

    public static function blogPost(string $slug): void
    {
        self::cors();
        $row = Database::one(
            'SELECT * FROM blog_posts WHERE slug = ? AND status = "published" AND deleted_at IS NULL',
            [$slug]
        );
        if (!$row) {
            View::json(['ok' => false, 'error' => 'not_found'], 404);
        }
        $seo = Database::one(
            'SELECT * FROM seo_metadata WHERE entity_type = "blog" AND entity_id = ?',
            [(int) $row['id']]
        ) ?: [];
        View::json(['ok' => true, 'data' => $row + ['seo' => self::seoOut($seo)]]);
    }

    public static function formDef(string $slug): void
    {
        self::cors();
        $form = Database::one('SELECT id, name, slug, success_message, honeypot_field FROM forms WHERE slug = ? AND is_active = 1', [$slug]);
        if (!$form) {
            View::json(['ok' => false, 'error' => 'not_found'], 404);
        }
        $fields = Database::all(
            'SELECT name, label, type, options_json, placeholder, width, is_required, sort_order
             FROM form_fields WHERE form_id = ? ORDER BY sort_order, id',
            [(int) $form['id']]
        );
        foreach ($fields as &$f) {
            $f['options'] = json_decode((string) ($f['options_json'] ?? ''), true) ?: [];
            unset($f['options_json']);
            $f['is_required'] = (int) $f['is_required'] === 1;
        }
        View::json(['ok' => true, 'data' => $form + ['fields' => $fields]]);
    }

    public static function submitForm(string $slug): void
    {
        self::cors();
        $input = Request::json();
        if (!$input) {
            $input = $_POST;
        }
        $result = AdminForms::ingest($slug, $input, Request::ip());
        View::json(
            ['ok' => $result['ok'], 'message' => $result['message'], 'id' => $result['id']],
            $result['ok'] ? 200 : 422
        );
    }

    private static function sitePayload(): array
    {
        $keys = [
            'brand_name', 'tagline', 'logo', 'favicon',
            'phone_primary', 'phone_secondary', 'phone_3', 'phone_4',
            'email', 'email_secondary', 'whatsapp', 'hours',
            'head_office', 'facebook', 'instagram', 'youtube', 'linkedin',
            'maps_url', 'default_seo_title', 'default_seo_description', 'og_image',
            'schema_json', 'stats_json',
        ];
        $out = [];
        foreach ($keys as $k) {
            $out[$k] = Settings::get($k, '');
        }
        if ($out['stats_json'] !== '') {
            $decoded = json_decode($out['stats_json'], true);
            $out['stats'] = is_array($decoded) ? $decoded : [];
        } else {
            $out['stats'] = [];
        }
        unset($out['stats_json']);
        if ($out['schema_json'] !== '') {
            $schema = json_decode($out['schema_json'], true);
            $out['schema'] = is_array($schema) ? $schema : null;
        }
        $out['phones'] = array_values(array_filter([
            $out['phone_primary'], $out['phone_secondary'], $out['phone_3'], $out['phone_4'],
        ]));
        return $out;
    }

    private static function menuPayload(): array
    {
        $menus = Database::all('SELECT * FROM menus ORDER BY id');
        $out = [];
        foreach ($menus as $m) {
            $items = Database::all(
                'SELECT * FROM menu_items WHERE menu_id = ? AND is_active = 1 ORDER BY sort_order, id',
                [(int) $m['id']]
            );
            $out[$m['slug']] = array_map(static function (array $i): array {
                return [
                    'id' => (int) $i['id'],
                    'parent_id' => $i['parent_id'] ? (int) $i['parent_id'] : null,
                    'label' => $i['label'],
                    'url' => $i['url'],
                    'link_type' => $i['link_type'],
                ];
            }, $items);
        }
        return $out;
    }

    private static function testimonialRows(): array
    {
        $rows = Database::all('SELECT id, name, role, quote, initials, sort_order FROM testimonials WHERE is_visible = 1 ORDER BY sort_order, id');
        return array_map(static function (array $r): array {
            $fields = [];
            // role is used as college / badge text; quote is the body
            return [
                'id' => (int) $r['id'],
                'name' => $r['name'],
                'role' => $r['role'],
                'college' => $r['role'],
                'quote' => $r['quote'],
                'initials' => $r['initials'],
            ];
        }, $rows);
    }

    private static function faqRows(): array
    {
        return Database::all(
            'SELECT id, question, answer, entity_type, entity_id, sort_order
             FROM faqs WHERE is_visible = 1 ORDER BY entity_type, sort_order, id'
        );
    }

    private static function pageIndex(): array
    {
        $rows = Database::all(
            'SELECT p.id, p.title, p.slug, p.updated_at, s.seo_title, s.meta_description, s.canonical_url, s.robots, s.og_title, s.og_description, s.og_image
             FROM pages p
             LEFT JOIN seo_metadata s ON s.entity_type = "page" AND s.entity_id = p.id
             WHERE p.status = "published" AND p.deleted_at IS NULL
             ORDER BY p.slug'
        );
        $out = [];
        foreach ($rows as $r) {
            $key = $r['slug'] === '/' ? 'home' : $r['slug'];
            $out[$key] = [
                'id' => (int) $r['id'],
                'title' => $r['title'],
                'slug' => $r['slug'],
                'seo' => [
                    'title' => $r['seo_title'] ?: $r['title'],
                    'description' => $r['meta_description'],
                    'canonical' => $r['canonical_url'],
                    'robots' => $r['robots'] ?: 'index,follow',
                    'og_title' => $r['og_title'],
                    'og_description' => $r['og_description'],
                    'og_image' => $r['og_image'],
                ],
                'updated_at' => $r['updated_at'],
            ];
        }
        return $out;
    }

    private static function cptRows(string $type): array
    {
        $pt = Cpt::type($type);
        if (!$pt) {
            return [];
        }
        $rows = Database::all(
            'SELECT * FROM cpt_entries WHERE post_type_id = ? AND status = "published" AND deleted_at IS NULL ORDER BY sort_order, id',
            [(int) $pt['id']]
        );
        return array_map([self::class, 'entryOut'], $rows);
    }

    private static function entryOut(array $row): array
    {
        $fields = json_decode((string) ($row['fields_json'] ?? ''), true) ?: [];
        return [
            'id' => (int) $row['id'],
            'title' => $row['title'],
            'slug' => $row['slug'],
            'excerpt' => $row['excerpt'],
            'featured_image' => $row['featured_image'],
            'body_html' => $row['body_html'],
            'fields' => $fields,
            'sort_order' => (int) $row['sort_order'],
            'published_at' => $row['published_at'],
            'updated_at' => $row['updated_at'],
        ];
    }

    private static function typeOut(array $pt): array
    {
        return [
            'id' => (int) $pt['id'],
            'name' => $pt['name'],
            'singular_name' => $pt['singular_name'],
            'slug' => $pt['slug'],
            'public' => (int) $pt['public'] === 1,
            'has_archive' => (int) $pt['has_archive'] === 1,
        ];
    }

    private static function seoOut(array $seo): array
    {
        return [
            'title' => $seo['seo_title'] ?? null,
            'description' => $seo['meta_description'] ?? null,
            'canonical' => $seo['canonical_url'] ?? null,
            'robots' => $seo['robots'] ?? 'index,follow',
            'og_title' => $seo['og_title'] ?? null,
            'og_description' => $seo['og_description'] ?? null,
            'og_image' => $seo['og_image'] ?? null,
        ];
    }
}
