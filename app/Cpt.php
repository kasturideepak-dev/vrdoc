<?php
declare(strict_types=1);

final class Cpt
{
    public static function activeTypes(): array
    {
        return Database::all('SELECT * FROM post_types WHERE status = "active" ORDER BY sort_order, name');
    }

    public static function publicTypes(): array
    {
        return Database::all('SELECT * FROM post_types WHERE status = "active" AND public = 1 ORDER BY sort_order, name');
    }

    public static function type(string $slug): ?array
    {
        return Database::one('SELECT * FROM post_types WHERE slug = ? AND status = "active"', [$slug]);
    }

    public static function typeById(int $id): ?array
    {
        return Database::one('SELECT * FROM post_types WHERE id = ?', [$id]);
    }

    public static function fields(int $typeId): array
    {
        return Database::all('SELECT * FROM post_type_fields WHERE post_type_id = ? ORDER BY sort_order, id', [$typeId]);
    }

    public static function published(string $typeSlug): array
    {
        $type = self::type($typeSlug);
        if (!$type) {
            return [];
        }
        $rows = Database::all(
            'SELECT * FROM cpt_entries WHERE post_type_id = ? AND status = "published" AND deleted_at IS NULL
             ORDER BY sort_order, id',
            [(int) $type['id']]
        );
        foreach ($rows as &$r) {
            $r['_type'] = $type;
            $r['_fields'] = json_decode($r['fields_json'] ?: '{}', true) ?: [];
        }
        return $rows;
    }

    public static function entry(string $typeSlug, string $entrySlug): ?array
    {
        $type = self::type($typeSlug);
        if (!$type) {
            return null;
        }
        $row = Database::one(
            'SELECT * FROM cpt_entries WHERE post_type_id = ? AND slug = ? AND deleted_at IS NULL',
            [(int) $type['id'], $entrySlug]
        );
        if (!$row) {
            return null;
        }
        $row['_type'] = $type;
        $row['_fields'] = json_decode($row['fields_json'] ?: '{}', true) ?: [];
        return $row;
    }

    public static function field(array $entry, string $name, mixed $default = ''): mixed
    {
        $fields = $entry['_fields'] ?? (json_decode($entry['fields_json'] ?? '{}', true) ?: []);
        return $fields[$name] ?? $default;
    }

    public static function permalink(array $entry, ?array $type = null): string
    {
        $type = $type ?? ($entry['_type'] ?? self::typeById((int) $entry['post_type_id']));
        if (!$type || !(int) $type['public']) {
            return '#';
        }
        return path_url($type['slug'] . '/' . $entry['slug']);
    }

    public static function archiveUrl(array $type): string
    {
        return path_url($type['slug']);
    }

    public static function decode(array $row): array
    {
        $row['_fields'] = json_decode($row['fields_json'] ?: '{}', true) ?: [];
        return $row;
    }

    public static function saveFieldsFromRequest(array $fieldDefs): array
    {
        $out = [];
        foreach ($fieldDefs as $f) {
            $name = $f['name'];
            $type = $f['type'];
            if ($type === 'checkbox') {
                $out[$name] = Request::bool('f_' . $name) ? '1' : '0';
                continue;
            }
            if ($type === 'repeater') {
                $raw = $_POST['f_' . $name] ?? [];
                $out[$name] = is_array($raw) ? array_values($raw) : [];
                continue;
            }
            if ($type === 'gallery') {
                $raw = Request::str('f_' . $name);
                $out[$name] = array_values(array_filter(array_map('trim', preg_split("/\r\n|\n|\r/", $raw) ?: [])));
                continue;
            }
            if ($type === 'richtext') {
                $out[$name] = Html::allowedHtml((string) ($_POST['f_' . $name] ?? ''));
                continue;
            }
            $out[$name] = Request::str('f_' . $name);
        }
        return $out;
    }

    public static function validateRequired(array $fieldDefs, array $values): array
    {
        $errors = [];
        foreach ($fieldDefs as $f) {
            if (!(int) $f['is_required']) {
                continue;
            }
            $v = $values[$f['name']] ?? '';
            $empty = $v === '' || $v === [] || $v === null;
            if ($empty) {
                $errors[] = $f['label'] . ' is required.';
            }
        }
        return $errors;
    }

    /** Slugs that ship with the site and must never be deleted. */
    public static function systemSlugs(): array
    {
        return ['courses', 'campus', 'faculty'];
    }

    /** Seed campus entries with images when missing (for grids and footer). */
    public static function ensureCampuses(): void
    {
        static $done = false;
        if ($done) {
            return;
        }
        $done = true;
        try {
            $type = self::type('campuses');
            if (!$type) {
                return;
            }
            $typeId = (int) $type['id'];
            $admin = Database::one('SELECT id FROM users ORDER BY id LIMIT 1');
            $authorId = (int) ($admin['id'] ?? 1);
            $campuses = [
                ['Hafeezpet / Miyapur', 'hafeezpet-miyapur', 'Plot No 29, Mathrusree Nagar, Hafeezpet, Miyapur, Hyderabad, Telangana 500049', '/assets/img/campus/hafeezpet.jpg'],
                ['Bowrampet', 'bowrampet', 'VR Doctors Campus, Bowrampet, Hyderabad, Telangana', '/assets/img/campus/bowrampet.jpg'],
                ['Hayathnagar', 'hayathnagar', 'VR Doctors Campus, Hayathnagar, Hyderabad, Telangana', '/assets/img/campus/hayathnagar.jpg'],
                ['Miyapur (A)', 'miyapur-a', 'Miyapur, Hyderabad, Telangana', '/assets/img/campus/miyapur-a.jpg'],
                ['Miyapur (B)', 'miyapur-b', 'Miyapur, Hyderabad, Telangana', '/assets/img/campus/miyapur-b.jpg'],
            ];
            foreach ($campuses as $i => $c) {
                $row = Database::one(
                    'SELECT * FROM cpt_entries WHERE post_type_id = ? AND slug = ? AND deleted_at IS NULL',
                    [$typeId, $c[1]]
                );
                if (!$row) {
                    Database::insert('cpt_entries', [
                        'post_type_id' => $typeId,
                        'title' => $c[0],
                        'slug' => $c[1],
                        'status' => 'published',
                        'excerpt' => $c[2],
                        'featured_image' => $c[3],
                        'body_html' => null,
                        'fields_json' => Html::json(['address' => $c[2]]),
                        'published_at' => date('Y-m-d H:i:s'),
                        'author_id' => $authorId,
                        'sort_order' => $i + 1,
                    ]);
                    continue;
                }
                if (($row['featured_image'] ?? '') === '') {
                    Database::update('cpt_entries', [
                        'featured_image' => $c[3],
                        'status' => 'published',
                    ], 'id = ?', [(int) $row['id']]);
                }
            }
        } catch (Throwable $e) {
            error_log('Cpt::ensureCampuses: ' . $e->getMessage());
        }
    }

    /** Refresh demo content on the AI landing page entry. */
    public static function refreshAiLandingContent(): void
    {
        static $done = false;
        if ($done) {
            return;
        }
        $done = true;
        try {
            $type = self::type('ai');
            if (!$type) {
                return;
            }
            $entry = self::entry('ai', 'ai-neet-coaching');
            if (!$entry) {
                return;
            }
            $entryId = (int) $entry['id'];
            $rich1 = '<p>VR Doctors Academy brings <strong>AI-powered learning</strong> to NEET preparation — combining expert faculty, adaptive study plans, and proven residential discipline. This programme is built for serious aspirants who want personalised guidance at every step of their medical entrance journey.</p>'
                . '<h2>What you will learn</h2>'
                . '<ul><li>NCERT-first foundation for Physics, Chemistry, Botany and Zoology</li>'
                . '<li>AI-guided weekly study plans based on your mock-test performance</li>'
                . '<li>Daily practice, chapter tests and full-length NEET simulations</li>'
                . '<li>Residential campus life with structured mentoring and doubt clearance</li></ul>'
                . '<h2>Who is this programme for?</h2>'
                . '<p>BiPC students preparing for NEET(UG) — including Intermediate students and repeaters who want a focused, technology-enabled coaching environment in Hyderabad.</p>';
            $rich2 = '<h2>Why choose VR Doctors for AI NEET coaching?</h2>'
                . '<p>With years of producing successful medical aspirants, VR Doctors combines traditional academic rigour with modern learning tools. Our faculty understand the NEET pattern deeply, and our campuses across Hyderabad give families flexible residential and day-scholar options.</p>'
                . '<h3>Programme highlights</h3>'
                . '<ul><li><strong>Expert faculty</strong> — Decades of NEET teaching experience across all four subjects.</li>'
                . '<li><strong>AI study insights</strong> — Identify weak topics early and revise smarter, not harder.</li>'
                . '<li><strong>Residential option</strong> — Safe hostels, mess, and round-the-clock academic support.</li>'
                . '<li><strong>Proven track record</strong> — Hundreds of students placed in top medical colleges.</li></ul>'
                . '<p>Ready to take the next step? Apply now or book a campus visit to meet our counsellors and explore the facilities firsthand.</p>';
            Database::update('cpt_entries', [
                'title' => 'AI NEET Coaching at VR Doctors',
                'excerpt' => 'Personalised study plans, expert faculty, and intelligent learning insights for every NEET aspirant.',
                'featured_image' => '/assets/img/banner/campuses-hero.jpg',
            ], 'id = ?', [$entryId]);
            $updates = [
                'ai_banner' => [
                    'image' => '',
                    'kicker' => 'VR Doctors Academy',
                ],
                'rich_text' => [$rich1, $rich2],
                'youtube_reels' => [
                    'kicker' => 'Student voices',
                    'heading' => 'Hear from our students',
                    'lede' => 'Real stories from students and parents who trained with VR Doctors Academy.',
                    'videos' => "Student Reel|https://www.youtube.com/shorts/UxDWczLjHpM\nStudent Reel|https://www.youtube.com/shorts/fvCx14jXvUo\nStudent Reel|https://www.youtube.com/shorts/6ieGDybRX4o\nStudent Reel|https://www.youtube.com/shorts/Soe0N0k5elw",
                ],
                'campuses' => [
                    'kicker' => 'Our campuses',
                    'heading' => 'Study at a campus near you',
                    'note' => 'Residential and day-scholar options available across Hyderabad.',
                    'bg' => '/assets/img/banner/campus-life-bg.jpg',
                ],
            ];
            $sections = Database::all(
                'SELECT * FROM content_sections WHERE owner_type = "cpt" AND owner_id = ? ORDER BY sort_order, id',
                [$entryId]
            );
            $richIdx = 0;
            foreach ($sections as $sec) {
                $type = $sec['type'];
                if ($type === 'ai_banner') {
                    Database::update('content_sections', [
                        'content_json' => Html::json($updates['ai_banner']),
                    ], 'id = ?', [(int) $sec['id']]);
                } elseif ($type === 'rich_text' && isset($updates['rich_text'][$richIdx])) {
                    Database::update('content_sections', [
                        'content_json' => Html::json(['html' => $updates['rich_text'][$richIdx]]),
                    ], 'id = ?', [(int) $sec['id']]);
                    $richIdx++;
                } elseif ($type === 'youtube_reels') {
                    Database::update('content_sections', [
                        'content_json' => Html::json($updates['youtube_reels']),
                    ], 'id = ?', [(int) $sec['id']]);
                } elseif ($type === 'campuses') {
                    Database::update('content_sections', [
                        'content_json' => Html::json($updates['campuses']),
                    ], 'id = ?', [(int) $sec['id']]);
                }
            }
            $hasEnquire = false;
            foreach ($sections as $sec) {
                if ($sec['type'] === 'enquire') {
                    $hasEnquire = true;
                    break;
                }
            }
            if (!$hasEnquire) {
                $maxOrder = 0;
                foreach ($sections as $sec) {
                    $maxOrder = max($maxOrder, (int) $sec['sort_order']);
                }
                Database::insert('content_sections', [
                    'owner_type' => 'cpt',
                    'owner_id' => $entryId,
                    'type' => 'enquire',
                    'content_json' => Html::json([
                        'kicker' => 'Admissions open',
                        'heading' => 'Enquire about this programme',
                        'lede' => 'Tell us about the student and we will help you choose the right campus and batch.',
                    ]),
                    'sort_order' => $maxOrder + 1,
                    'is_visible' => 1,
                ]);
            }
            Content::publish('cpt', $entryId);
            Settings::ensureBrandAssets();
        } catch (Throwable $e) {
            error_log('Cpt::refreshAiLandingContent: ' . $e->getMessage());
        }
    }

    /** Ensure the AI programme post type and starter entry exist on upgraded installs. */
    public static function ensureAiType(): void
    {
        static $done = false;
        if ($done) {
            return;
        }
        $done = true;
        try {
            $type = Database::one('SELECT * FROM post_types WHERE slug = ?', ['ai']);
            if (!$type) {
                $typeId = Database::insert('post_types', [
                    'name' => 'AI Posts',
                    'singular_name' => 'AI Post',
                    'slug' => 'ai',
                    'description' => 'AI landing pages with fixed layout — title and description show on the banner',
                    'has_archive' => 0,
                    'public' => 1,
                    'template_mode' => 'builder',
                    'sort_order' => 10,
                    'status' => 'active',
                    'is_system' => 1,
                ]);
                $type = self::typeById($typeId);
            }
            if (!$type) {
                return;
            }
            $hasEntry = Database::one(
                'SELECT id FROM cpt_entries WHERE post_type_id = ? AND deleted_at IS NULL LIMIT 1',
                [(int) $type['id']]
            );
            self::ensureCampuses();
            if ($hasEntry) {
                return;
            }
            $tplId = Templates::aiLandingId();
            if (!$tplId) {
                return;
            }
            $admin = Database::one('SELECT id FROM users ORDER BY id LIMIT 1');
            $entryId = Database::insert('cpt_entries', [
                'post_type_id' => (int) $type['id'],
                'title' => 'AI NEET Coaching Programme',
                'slug' => 'ai-neet-coaching',
                'status' => 'published',
                'excerpt' => 'AI-powered NEET preparation with personalised learning paths at VR Doctors Academy.',
                'featured_image' => '',
                'body_html' => null,
                'fields_json' => '{}',
                'published_at' => date('Y-m-d H:i:s'),
                'author_id' => (int) ($admin['id'] ?? 1),
                'sort_order' => 1,
            ]);
            Templates::apply('cpt', $entryId, $tplId);
            Database::upsertSeo('cpt', $entryId, [
                'seo_title' => 'AI NEET Coaching Programme | VR Doctors Academy',
                'meta_description' => 'Experience AI-powered NEET coaching with personalised study plans, expert faculty, and proven results at VR Doctors Academy, Hyderabad.',
                'canonical_url' => path_url('ai/ai-neet-coaching'),
                'robots' => 'index,follow',
            ]);
            Content::publish('cpt', $entryId);
        } catch (Throwable $e) {
            error_log('Cpt::ensureAiType: ' . $e->getMessage());
        }
    }

    public static function ensureSchema(): void
    {
        static $done = false;
        if ($done) {
            return;
        }
        $done = true;
        try {
            $col = Database::all("SHOW COLUMNS FROM post_types LIKE 'is_system'");
            if (!$col) {
                Database::pdo()->exec(
                    'ALTER TABLE post_types ADD COLUMN is_system TINYINT(1) NOT NULL DEFAULT 0 AFTER status'
                );
            }
            $in = implode(',', array_fill(0, count(self::systemSlugs()), '?'));
            Database::query(
                'UPDATE post_types SET is_system = 1 WHERE slug IN (' . $in . ')',
                self::systemSlugs()
            );
            $st = Database::one("SHOW COLUMNS FROM post_types LIKE 'status'");
            $type = strtolower((string) ($st['Type'] ?? ''));
            if ($st && !str_contains($type, 'archived')) {
                Database::pdo()->exec(
                    "ALTER TABLE post_types MODIFY status ENUM('active','inactive','archived') NOT NULL DEFAULT 'active'"
                );
            }
        } catch (Throwable $e) {
            error_log('Cpt::ensureSchema: ' . $e->getMessage());
        }
    }

    public static function isSystem(array $type): bool
    {
        if ((int) ($type['is_system'] ?? 0) === 1) {
            return true;
        }
        return in_array((string) ($type['slug'] ?? ''), self::systemSlugs(), true);
    }

    public static function entries(int $typeId, bool $includeTrashed = false): array
    {
        $sql = 'SELECT * FROM cpt_entries WHERE post_type_id = ?';
        if (!$includeTrashed) {
            $sql .= ' AND deleted_at IS NULL';
        }
        $sql .= ' ORDER BY sort_order, id';
        return Database::all($sql, [$typeId]);
    }

    /**
     * Menus, page sections, and other records that point at this type or its entries.
     *
     * @return array{header_blocking:bool,locations:list<array<string,mixed>>,menu_items:list<array<string,mixed>>}
     */
    public static function references(array $type): array
    {
        $typeId = (int) $type['id'];
        $slug = (string) $type['slug'];
        $entries = self::entries($typeId, true);
        $entryIds = array_map(static fn ($e) => (int) $e['id'], $entries);
        $locations = [];
        $menuItems = [];
        $headerBlocking = false;

        $items = Database::all(
            'SELECT mi.*, m.slug AS menu_slug, m.name AS menu_name
             FROM menu_items mi JOIN menus m ON m.id = mi.menu_id
             ORDER BY m.id, mi.sort_order, mi.id'
        );
        $byId = [];
        foreach ($items as $it) {
            $byId[(int) $it['id']] = $it;
        }

        $archivePath = '/' . trim($slug, '/') . '/';

        foreach ($items as $it) {
            $hit = false;
            $lt = (string) $it['link_type'];
            $oid = (int) ($it['object_id'] ?? 0);
            if ($lt === 'cpt_archive' && $oid === $typeId) {
                $hit = true;
            } elseif ($lt === 'cpt_entry' && in_array($oid, $entryIds, true)) {
                $hit = true;
            } else {
                $url = (string) ($it['url'] ?? '');
                $path = parse_url($url, PHP_URL_PATH);
                $path = is_string($path) && $path !== '' ? $path : $url;
                $norm = '/' . trim((string) $path, '/');
                $norm = $norm === '/' ? '/' : $norm . '/';
                if ($norm === $archivePath || str_starts_with($norm, $archivePath)) {
                    $hit = true;
                }
            }
            if (!$hit) {
                continue;
            }
            $menuItems[] = $it;
            $parent = !empty($it['parent_id']) ? ($byId[(int) $it['parent_id']] ?? null) : null;
            $detail = ($it['menu_name'] ?? 'Menu');
            if ($parent) {
                $detail .= ' → ' . $parent['label'] . ' dropdown';
            }
            $detail .= ' → ' . $it['label'];
            $isHeader = ($it['menu_slug'] ?? '') === 'header' && (int) $it['is_active'] === 1;
            if ($isHeader) {
                $headerBlocking = true;
            }
            $locations[] = [
                'kind' => 'menu',
                'header' => $isHeader,
                'detail' => $detail,
            ];
        }

        $like = '%/' . $slug . '%';
        foreach (Database::all(
            'SELECT s.id, s.owner_type, s.owner_id, s.type FROM content_sections s WHERE s.content_json LIKE ?',
            [$like]
        ) as $sec) {
            $where = $sec['type'] . ' section';
            if ($sec['owner_type'] === 'page') {
                $p = Database::one('SELECT title, slug FROM pages WHERE id = ?', [(int) $sec['owner_id']]);
                $where = ($p['title'] ?? 'Page') . ' → ' . $where;
            } elseif ($sec['owner_type'] === 'cpt') {
                $e = Database::one('SELECT title FROM cpt_entries WHERE id = ?', [(int) $sec['owner_id']]);
                $where = ($e['title'] ?? 'Entry') . ' → ' . $where;
            }
            $locations[] = ['kind' => 'content', 'header' => false, 'detail' => $where];
        }

        if ($entryIds && Database::all("SHOW TABLES LIKE 'snippet_targets'")) {
            $in = implode(',', array_fill(0, count($entryIds), '?'));
            $n = Database::one(
                'SELECT COUNT(*) c FROM snippet_targets WHERE target_type = "cpt" AND target_id IN (' . $in . ')',
                $entryIds
            );
            if ((int) ($n['c'] ?? 0) > 0) {
                $locations[] = [
                    'kind' => 'snippet',
                    'header' => false,
                    'detail' => (int) $n['c'] . ' code snippet target(s)',
                ];
            }
        }

        return [
            'header_blocking' => $headerBlocking,
            'locations' => $locations,
            'menu_items' => $menuItems,
        ];
    }

    public static function archive(array $type): void
    {
        $id = (int) $type['id'];
        Database::update('post_types', ['status' => 'archived'], 'id = ?', [$id]);
        foreach (self::references($type)['menu_items'] as $it) {
            Database::update('menu_items', ['is_active' => 0], 'id = ?', [(int) $it['id']]);
        }
        Cache::flush();
    }

    public static function restore(array $type): void
    {
        $id = (int) $type['id'];
        Database::update('post_types', ['status' => 'active'], 'id = ?', [$id]);
        foreach (self::references($type)['menu_items'] as $it) {
            Database::update('menu_items', ['is_active' => 1], 'id = ?', [(int) $it['id']]);
        }
        Cache::flush();
    }

    public static function purge(array $type, string $redirectTo = '/'): array
    {
        $id = (int) $type['id'];
        $slug = (string) $type['slug'];
        $entries = self::entries($id, true);
        $entryIds = array_map(static fn ($e) => (int) $e['id'], $entries);
        $live = array_values(array_filter($entries, static fn ($e) => $e['deleted_at'] === null));
        $to = $redirectTo !== '' ? $redirectTo : '/';
        if (!str_starts_with($to, '/') && !str_starts_with($to, 'http')) {
            $to = '/' . $to;
        }
        $to = path_url($to);

        $note = 'Deleted post type “' . $type['name'] . '”';
        if ((int) ($type['public'] ?? 0) === 1 || (int) ($type['has_archive'] ?? 0) === 1) {
            Redirects::save($slug, $to, 301, $note . ' (archive)');
            foreach ($entries as $e) {
                Redirects::save($slug . '/' . $e['slug'], $to, 301, $note . ' (entry)');
            }
        }

        self::rewriteUrls($slug, array_column($entries, 'slug'), $to);

        $refs = self::references($type);
        foreach ($refs['menu_items'] as $it) {
            $mid = (int) $it['id'];
            Database::query('UPDATE menu_items SET parent_id = NULL WHERE parent_id = ?', [$mid]);
            Database::delete('menu_items', 'id = ?', [$mid]);
        }

        if ($entryIds) {
            $in = implode(',', array_fill(0, count($entryIds), '?'));
            Database::query('DELETE FROM content_sections WHERE owner_type = "cpt" AND owner_id IN (' . $in . ')', $entryIds);
            Database::query('DELETE FROM content_revisions WHERE owner_type = "cpt" AND owner_id IN (' . $in . ')', $entryIds);
            Database::query('DELETE FROM preview_tokens WHERE owner_type = "cpt" AND owner_id IN (' . $in . ')', $entryIds);
            Database::query('DELETE FROM seo_metadata WHERE entity_type = "cpt" AND entity_id IN (' . $in . ')', $entryIds);
            if (Database::all("SHOW TABLES LIKE 'snippet_targets'")) {
                Database::query('DELETE FROM snippet_targets WHERE target_type = "cpt" AND target_id IN (' . $in . ')', $entryIds);
            }
        }
        Database::delete('seo_metadata', 'entity_type = ? AND entity_id = ?', ['post_type', $id]);

        Database::delete('post_types', 'id = ?', [$id]);
        Cache::flush();

        return [
            'entries' => count($live),
            'redirects' => 1 + count($entries),
        ];
    }

    private static function rewriteUrls(string $typeSlug, array $entrySlugs, string $to): void
    {
        $needles = [];
        foreach ($entrySlugs as $es) {
            $needles[] = path_url($typeSlug . '/' . $es);
        }
        $needles[] = path_url($typeSlug);
        usort($needles, static fn ($a, $b) => strlen($b) <=> strlen($a));

        $swap = static function (string $html) use ($needles, $to): string {
            $out = $html;
            foreach ($needles as $n) {
                $out = str_replace([rtrim(BASE_URL, '/') . $n, $n], $to, $out);
            }
            return $out;
        };

        foreach (Database::all(
            'SELECT id, content_json FROM content_sections WHERE content_json LIKE ?',
            ['%/' . $typeSlug . '%']
        ) as $row) {
            $next = $swap((string) $row['content_json']);
            if ($next !== $row['content_json']) {
                Database::update('content_sections', ['content_json' => $next], 'id = ?', [(int) $row['id']]);
            }
        }
        foreach (Database::all(
            'SELECT id, body_html FROM blog_posts WHERE body_html LIKE ?',
            ['%/' . $typeSlug . '%']
        ) as $row) {
            $next = $swap((string) ($row['body_html'] ?? ''));
            if ($next !== ($row['body_html'] ?? '')) {
                Database::update('blog_posts', ['body_html' => $next], 'id = ?', [(int) $row['id']]);
            }
        }
    }
}
