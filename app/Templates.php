<?php
declare(strict_types=1);

final class Templates
{
    /**
     * Bump when a new starter template ships.
     *
     * Seeding only ever inserts what is missing, so raising this adds the new
     * starters to an install that was already seeded without touching (or
     * resurrecting) anything the client has edited or deleted since.
     *   1 — original seven starters
     *   2 — adds "AI Post Template"
     */
    private const SEED_VERSION = 2;

    public static function apply(string $ownerType, int $ownerId, int $templateId): int
    {
        $tpl = Database::one('SELECT * FROM page_templates WHERE id = ?', [$templateId]);
        if (!$tpl) {
            return 0;
        }
        $secs = json_decode($tpl['sections_json'] ?: '[]', true) ?: [];
        $n = 0;
        foreach ($secs as $i => $s) {
            if (empty($s['type'])) {
                continue;
            }
            Database::insert('content_sections', [
                'owner_type' => $ownerType,
                'owner_id' => $ownerId,
                'type' => $s['type'],
                'content_json' => Html::json($s['content'] ?? []),
                'sort_order' => $i,
                'is_visible' => 1,
            ]);
            $n++;
        }
        return $n;
    }

    public static function sectionCount(?string $json): int
    {
        return count(json_decode($json ?: '[]', true) ?: []);
    }

    public static function landingId(): ?int
    {
        $row = Database::one('SELECT id FROM page_templates WHERE slug = ?', ['landing-page']);
        return $row ? (int) $row['id'] : null;
    }

    public static function aiLandingId(): ?int
    {
        $row = Database::one('SELECT id FROM page_templates WHERE slug = ?', ['ai-landing']);
        return $row ? (int) $row['id'] : null;
    }

    /**
     * The template a new entry of this post type starts from.
     *
     * Reads the mapping the admin picked on the post type. Falls back to the
     * historic slug-based guess only while a type has no mapping of its own.
     */
    public static function defaultTemplateIdForType(string $typeSlug): ?int
    {
        $type = Database::one('SELECT * FROM post_types WHERE slug = ?', [$typeSlug]);
        $mapped = (int) ($type['default_template_id'] ?? 0);
        if ($mapped > 0 && Database::one('SELECT id FROM page_templates WHERE id = ?', [$mapped])) {
            return $mapped;
        }
        if ($typeSlug === 'ai') {
            return self::aiLandingId() ?? self::landingId();
        }
        return self::landingId();
    }

    /** All page templates, newest mapping first, for pickers. */
    public static function allPages(): array
    {
        return Database::all('SELECT * FROM page_templates ORDER BY name');
    }

    public static function page(int $id): ?array
    {
        return Database::one('SELECT * FROM page_templates WHERE id = ?', [$id]);
    }

    /** Section types a template lays down, in order. */
    public static function sectionTypes(?int $templateId): array
    {
        if (!$templateId) {
            return [];
        }
        $tpl = self::page($templateId);
        return $tpl ? self::sectionTypesFromJson($tpl['sections_json'] ?? '[]') : [];
    }

    /** Same, for callers that already hold the sections JSON. */
    public static function sectionTypesFromJson(?string $json): array
    {
        $out = [];
        foreach (json_decode($json ?: '[]', true) ?: [] as $s) {
            if (!empty($s['type'])) {
                $out[] = (string) $s['type'];
            }
        }
        return $out;
    }

    /**
     * Everything that points at a page template.
     *
     * @return array{post_types:list<array<string,mixed>>,pages:int}
     */
    public static function usage(int $templateId): array
    {
        $types = [];
        try {
            $types = Database::all(
                'SELECT id, name, slug, status FROM post_types WHERE default_template_id = ? ORDER BY sort_order, name',
                [$templateId]
            );
        } catch (Throwable $e) {
            error_log('Templates::usage: ' . $e->getMessage());
        }
        return [
            'post_types' => $types,
            'pages' => (int) (Database::one(
                'SELECT COUNT(*) c FROM pages WHERE template_id = ? AND deleted_at IS NULL',
                [$templateId]
            )['c'] ?? 0),
        ];
    }

    /** Post type id => default template row, for dashboard and list mapping. */
    public static function mapByPostType(): array
    {
        try {
            $rows = Database::all(
                'SELECT t.id AS type_id, p.* FROM post_types t
                 JOIN page_templates p ON p.id = t.default_template_id'
            );
        } catch (Throwable $e) {
            error_log('Templates::mapByPostType: ' . $e->getMessage());
            return [];
        }
        $out = [];
        foreach ($rows as $r) {
            $typeId = (int) $r['type_id'];
            unset($r['type_id']);
            $out[$typeId] = $r;
        }
        return $out;
    }

    /** Start a blank template, optionally wired up as a post type's default. */
    public static function createBlank(string $name, string $pageType = 'standard', ?int $forTypeId = null): int
    {
        $id = Database::insert('page_templates', [
            'slug' => Slug::uniqueInTable('page_templates', $name !== '' ? $name : 'new-template'),
            'name' => $name !== '' ? $name : 'Untitled page template',
            'description' => '',
            'page_type' => $pageType,
            'sections_json' => '[]',
        ]);
        if ($forTypeId) {
            Database::update('post_types', ['default_template_id' => $id], 'id = ?', [$forTypeId]);
            Cache::flush();
        }
        return $id;
    }

    public static function packFromPost(array $existing = []): array
    {
        $idxs = $_POST['section_idx'] ?? [];
        $types = $_POST['section_type'] ?? [];
        $out = [];
        foreach ($idxs as $i => $idx) {
            $idx = (int) $idx;
            $type = (string) ($types[$i] ?? ($existing[$idx]['type'] ?? ''));
            if ($type === '' || !isset(SectionRegistry::all()[$type])) {
                continue;
            }
            $content = $existing[$idx]['content'] ?? SectionRegistry::defaults($type);
            foreach (SectionRegistry::fields($type) as $f) {
                $key = 't' . $idx . '_' . $f['k'];
                if ($f['t'] === 'html') {
                    $content[$f['k']] = Html::allowedHtml((string) ($_POST[$key] ?? ''));
                } else {
                    $content[$f['k']] = trim((string) ($_POST[$key] ?? ''));
                }
            }
            $out[] = ['type' => $type, 'content' => $content];
        }
        return $out;
    }

    public static function saveSectionAsTemplate(array $sec, string $name): int
    {
        $name = trim($name) !== '' ? trim($name) : SectionRegistry::label($sec['type']);
        return Database::insert('section_templates', [
            'name' => $name,
            'type' => $sec['type'],
            'content_json' => $sec['content_json'] ?? Html::json($sec['content'] ?? []),
            'created_by' => Auth::id(),
        ]);
    }

    /**
     * Add missing columns on installs that pre-date the template mapping.
     */
    public static function ensureSchema(): void
    {
        static $done = false;
        if ($done) {
            return;
        }
        $done = true;
        try {
            if (!Database::all("SHOW COLUMNS FROM post_types LIKE 'default_template_id'")) {
                Database::pdo()->exec(
                    'ALTER TABLE post_types ADD COLUMN default_template_id SMALLINT UNSIGNED NULL AFTER template_mode'
                );
            }
            if (!Database::all("SHOW COLUMNS FROM page_templates LIKE 'is_starter'")) {
                Database::pdo()->exec(
                    'ALTER TABLE page_templates ADD COLUMN is_starter TINYINT(1) NOT NULL DEFAULT 0 AFTER thumbnail'
                );
                $slugs = self::starterSlugs();
                Database::query(
                    'UPDATE page_templates SET is_starter = 1 WHERE slug IN ('
                        . implode(',', array_fill(0, count($slugs), '?')) . ')',
                    $slugs
                );
            }
        } catch (Throwable $e) {
            error_log('Templates::ensureSchema: ' . $e->getMessage());
        }
    }

    /** Slugs shipped by the installer. */
    public static function starterSlugs(): array
    {
        return ['homepage', 'inner-standard', 'ai-landing', 'ai-post', 'landing-page', 'contact-page', 'blog-post', 'gallery-page'];
    }

    /**
     * Put the shipped starter templates in place, once per install.
     *
     * Seeding is deliberately one-shot. This is reached from Templates, Pages →
     * New and the entry forms, so re-running it would either revert templates
     * the client has edited or resurrect ones they deleted on purpose.
     */
    public static function ensureStarters(): void
    {
        static $done = false;
        if ($done) {
            return;
        }
        $done = true;
        self::ensureSchema();
        try {
            if ((int) Settings::get('templates_seeded', '0') < self::SEED_VERSION) {
                self::seedStarters();
                self::grantTemplatePerms();
                Settings::set('templates_seeded', (string) self::SEED_VERSION);
            }
            Cpt::ensureAiType();
        } catch (Throwable $e) {
            error_log('Templates::ensureStarters: ' . $e->getMessage());
        }
    }

    /** Grant the editor role the template permissions the starters assume. */
    private static function grantTemplatePerms(): void
    {
        $editor = Database::one('SELECT id FROM roles WHERE slug = ?', ['editor']);
        if (!$editor) {
            return;
        }
        foreach (['templates.view', 'templates.create', 'templates.edit'] as $slug) {
            $perm = Database::one('SELECT id FROM permissions WHERE slug = ?', [$slug]);
            if (!$perm) {
                continue;
            }
            $has = Database::one(
                'SELECT role_id FROM role_permissions WHERE role_id = ? AND permission_id = ?',
                [(int) $editor['id'], (int) $perm['id']]
            );
            if (!$has) {
                Database::insert('role_permissions', [
                    'role_id' => (int) $editor['id'],
                    'permission_id' => (int) $perm['id'],
                ]);
            }
        }
    }

    /** Insert the shipped starter templates. Never touches existing rows. */
    private static function seedStarters(): void
    {
        $A = '/assets/img/';
        $apply = '/contact-us/#enquire';
        $brochure = 'https://drive.google.com/file/d/1ObbqqmENMGtRyrQQIbtI8uGv4Qq_6arj/view';

        $pages = [
            [
                'slug' => 'homepage',
                'name' => 'Homepage Template',
                'description' => 'Approved VR homepage layout: hero, programmes, stats, faculty, testimonials, enquire, FAQ.',
                'page_type' => 'standard',
                'sections' => [
                    ['type' => 'hero', 'content' => [
                        'kicker' => 'Admissions Open 2026–27 · MPC & BiPC',
                        'heading' => "Hyderabad’s leading residential junior college for MPC & BiPC.",
                        'lead' => 'Expert NEET and IIT-JEE coaching backed by personal mentorship — VR guides every student toward the right path, not just one exam.',
                        'image' => $A . 'banner/hero-class.jpg',
                        'figure' => $A . 'life/campus-1.jpg',
                        'cta_label' => 'Apply now',
                        'cta_url' => $apply,
                        'video_url' => 'https://www.youtube.com/watch?v=_3sTInMS-f4',
                        'stats' => "2000+|Satisfied students\n310+|MBBS Pvt. colleges\n215+|MBBS Govt. colleges\n5|Golden years",
                    ]],
                    ['type' => 'marquee', 'content' => ['items' => "Admissions Open 2026–27\nMPC with IIT-JEE\nBiPC with NEET\nNEET Long Term\nResidential & day scholar"]],
                    ['type' => 'about', 'content' => [
                        'kicker' => 'About VR',
                        'heading' => 'About VR Doctors',
                        'quote' => 'At VR Doctors — Vision Into Reality — we believe no child is built for one exam.',
                        'image_main' => $A . 'campus/bowrampet.jpg',
                        'image_card' => $A . 'gallery/g8.jpg',
                        'badge_value' => '5',
                        'badge_label' => 'Golden years',
                        'points' => "Personal guidance|Every student is understood individually.\nVR Doctors Academy|BiPC for NEET aspirants.\nVRiiT|MPC for future engineers.",
                        'cta_label' => 'Discover VR',
                        'cta_url' => '/about/',
                    ]],
                    ['type' => 'programs', 'content' => [
                        'kicker' => 'Our programmes',
                        'heading' => 'MPC and BiPC, with integrated coaching',
                        'lede' => 'Two-year Intermediate with integrated IIT-JEE or NEET coaching, plus a one-year NEET long-term option.',
                        'split_left_kicker' => '2-year Intermediate',
                        'split_left_title' => 'Integrated coaching',
                        'split_left_text' => 'IPE + competitive exams — MPC with IIT-JEE, and BiPC with NEET.',
                        'split_right_kicker' => '1-year Long Term',
                        'split_right_title' => 'Competitive exams only',
                        'split_right_text' => 'NEET long-term for Intermediate-completed students and repeaters.',
                    ]],
                    ['type' => 'stats', 'content' => ['items' => "2000+|Satisfied students\n310+|MBBS Pvt. colleges\n215+|MBBS Govt. colleges\n5|Golden years"]],
                    ['type' => 'faculty', 'content' => ['kicker' => 'Our faculty', 'heading' => 'The minds behind VR students’ success']],
                    ['type' => 'testimonials', 'content' => ['label' => 'Testimonials']],
                    ['type' => 'enquire', 'content' => [
                        'kicker' => 'Admissions 2026–27',
                        'heading' => 'Shape your future with VR Doctors',
                        'lede' => 'Admissions open for MPC & BiPC.',
                    ]],
                    ['type' => 'faq', 'content' => ['kicker' => 'FAQs', 'heading' => 'Questions families ask']],
                ],
            ],
            [
                'slug' => 'inner-standard',
                'name' => 'Inner Page Template',
                'description' => 'Generic inner page (About, Campuses, team): hero, text + image, features, enquire.',
                'page_type' => 'standard',
                'sections' => [
                    ['type' => 'page_hero', 'content' => [
                        'kicker' => 'VR Doctors',
                        'heading' => 'Page title',
                        'lead' => 'Replace this with a short introduction for the page.',
                        'image' => $A . 'banner/campus-life-bg.jpg',
                        'crumb' => 'Page',
                    ]],
                    ['type' => 'split', 'content' => [
                        'kicker' => 'Overview',
                        'heading' => 'Tell the story',
                        'lede' => 'A two-column text + image block for the main message.',
                        'points' => "Point one|Add a supporting line.\nPoint two|Add a supporting line.",
                        'image' => $A . 'life/campus-1.jpg',
                        'cta_label' => 'Apply now',
                        'cta_url' => $apply,
                    ]],
                    ['type' => 'features', 'content' => [
                        'kicker' => 'Highlights',
                        'heading' => 'Why families choose VR',
                        'cards' => "01|Personal guidance|Every student is understood individually.\n02|Specialised streams|MPC with IIT-JEE and BiPC with NEET.\n03|Residential campuses|Safe hostels across Hyderabad.",
                    ]],
                    ['type' => 'enquire', 'content' => [
                        'heading' => 'Talk to admissions',
                        'lede' => 'Visit a campus or leave a message and we’ll call you back.',
                    ]],
                ],
            ],
            [
                'slug' => 'ai-landing',
                'name' => 'AI Program Landing',
                'description' => 'Fixed layout for AI programmes: banner + title, rich text, YouTube reels (8), rich text, campuses grid, enquire form.',
                'page_type' => 'landing',
                'sections' => [
                    ['type' => 'ai_banner', 'content' => [
                        'image' => '',
                        'kicker' => 'VR Doctors Academy',
                    ]],
                    ['type' => 'rich_text', 'content' => [
                        'html' => '<p>VR Doctors Academy brings <strong>AI-powered learning</strong> to NEET preparation — combining expert faculty, adaptive study plans, and proven residential discipline.</p><h2>What you will learn</h2><ul><li>NCERT-first foundation for Physics, Chemistry, Botany and Zoology</li><li>AI-guided weekly study plans based on mock-test performance</li><li>Daily practice, chapter tests and full-length NEET simulations</li></ul>',
                    ]],
                    ['type' => 'youtube_reels', 'content' => [
                        'kicker' => 'Student voices',
                        'heading' => 'Hear from our students',
                        'lede' => 'Real stories from students who trained with VR Doctors Academy.',
                        'videos' => "Student Reel|https://www.youtube.com/shorts/UxDWczLjHpM\nStudent Reel|https://www.youtube.com/shorts/fvCx14jXvUo\nStudent Reel|https://www.youtube.com/shorts/6ieGDybRX4o\nStudent Reel|https://www.youtube.com/shorts/Soe0N0k5elw",
                    ]],
                    ['type' => 'rich_text', 'content' => [
                        'html' => '<h2>Why choose this programme</h2><p>Add more detail about faculty, methodology, residential options, or admission process. This section sits between the student reels and the campus grid.</p>',
                    ]],
                    ['type' => 'campuses', 'content' => [
                        'kicker' => 'Our campuses',
                        'heading' => 'Study at a campus near you',
                        'note' => 'Residential and day-scholar options available across Hyderabad.',
                        'bg' => $A . 'banner/campus-life-bg.jpg',
                    ]],
                    ['type' => 'enquire', 'content' => [
                        'kicker' => 'Admissions open',
                        'heading' => 'Enquire about this programme',
                        'lede' => 'Tell us about the student and we will help you choose the right campus and batch.',
                    ]],
                ],
            ],
            [
                'slug' => 'ai-post',
                'name' => 'AI Post Template',
                'description' => 'Banner with the post title, post body beside a latest-AI-posts rail, student reel reviews mid-article, campus grid and FAQ.',
                'page_type' => 'landing',
                'sections' => [
                    ['type' => 'ai_banner', 'content' => [
                        'image' => '',
                        'kicker' => 'VR Doctors Academy',
                    ]],
                    ['type' => 'post_body', 'content' => [
                        'html' => '<p>Open with the story. This column is the article body — headings, lists and links all work here.</p><h2>Key points</h2><ul><li>Replace this with the first part of the post.</li><li>The student reel reviews sit directly below this block.</li></ul>',
                        'sidebar_title' => 'Latest AI posts',
                        'source' => 'ai',
                        'limit' => '5',
                    ]],
                    ['type' => 'youtube_reels', 'content' => [
                        'kicker' => 'Student voices',
                        'heading' => 'Reel reviews from our students',
                        'lede' => 'Short clips from students who trained with VR Doctors Academy.',
                        'videos' => "Student Reel|https://www.youtube.com/shorts/UxDWczLjHpM\nStudent Reel|https://www.youtube.com/shorts/fvCx14jXvUo\nStudent Reel|https://www.youtube.com/shorts/6ieGDybRX4o\nStudent Reel|https://www.youtube.com/shorts/Soe0N0k5elw",
                    ]],
                    ['type' => 'rich_text', 'content' => [
                        'html' => '<h2>Continue the article</h2><p>The rest of the post goes here, after the student reels. Add as many rich-text blocks as the article needs.</p>',
                    ]],
                    ['type' => 'campuses', 'content' => [
                        'kicker' => 'Our campuses',
                        'heading' => 'Study at a campus near you',
                        'note' => 'Residential and day-scholar options available across Hyderabad.',
                        'bg' => $A . 'banner/campus-life-bg.jpg',
                    ]],
                    ['type' => 'faq', 'content' => [
                        'kicker' => 'FAQs',
                        'heading' => 'Questions families ask',
                        'items' => "Who is this programme for?|Replace with eligibility.\nIs hostel available?|Yes — residential and day-scholar options.",
                    ]],
                    ['type' => 'enquire', 'content' => [
                        'kicker' => 'Admissions open',
                        'heading' => 'Enquire about this programme',
                        'lede' => 'Tell us about the student and we will help you choose the right campus and batch.',
                    ]],
                ],
            ],
            [
                'slug' => 'landing-page',
                'name' => 'Landing Page Template',
                'description' => 'Default for new courses / CPT entries: campaign hero, highlights, stats, testimonials, FAQ, form.',
                'page_type' => 'landing',
                'sections' => [
                    ['type' => 'campaign_hero', 'content' => [
                        'kicker' => 'New programme',
                        'heading' => 'Landing page headline',
                        'lead' => 'A focused landing layout for a course, event or AI Program.',
                        'chips' => "IPE\nCoaching\nPersonal guidance",
                        'image' => $A . 'life/campus-2.jpg',
                        'figure' => $A . 'life/campus-1.jpg',
                        'crumb' => 'Programme',
                        'offer_kicker' => 'At a glance',
                        'offer_title' => 'What this programme includes',
                        'offer_text' => 'Replace with duration, stream and who it’s for.',
                        'offer_points' => "Personal mentoring\nStructured tests\nResidential option",
                        'cta_label' => 'Apply now',
                        'cta_url' => $apply,
                        'cta2_label' => 'Download brochure',
                        'cta2_url' => $brochure,
                    ]],
                    ['type' => 'proof', 'content' => [
                        'items' => "Experienced faculty|Guide students through board and entrance exams.\nPersonal attention|A plan around each student’s strengths.\nClear pathways|One exam is not the only destination.",
                    ]],
                    ['type' => 'stats', 'content' => ['items' => "2000+|Satisfied students\n5|Campuses\n37 yrs|Academic leadership"]],
                    ['type' => 'testimonials', 'content' => ['label' => 'Testimonials']],
                    ['type' => 'faq', 'content' => [
                        'kicker' => 'FAQs',
                        'heading' => 'Questions families ask',
                        'items' => "Who is this programme for?|Replace with eligibility.\nIs hostel available?|Yes — residential and day-scholar options.",
                    ]],
                    ['type' => 'enquire', 'content' => [
                        'kicker' => 'Admissions open',
                        'heading' => 'Enquire about this programme',
                        'lede' => 'Tell us a little about the student and we’ll help you choose campus and batch.',
                    ]],
                ],
            ],
            [
                'slug' => 'contact-page',
                'name' => 'Contact Page Template',
                'description' => 'Hero plus the admissions enquiry form and campus contact details.',
                'page_type' => 'standard',
                'sections' => [
                    ['type' => 'page_hero', 'content' => [
                        'kicker' => 'Admissions 2026–27',
                        'heading' => 'Contact us',
                        'lead' => 'Get in touch for admissions, course details, or a campus visit.',
                        'image' => $A . 'campus/hafeezpet.jpg',
                        'crumb' => 'Contact',
                    ]],
                    ['type' => 'enquire', 'content' => [
                        'heading' => 'Let us help you take the next step',
                        'lede' => 'We’re here to guide you on your journey to success.',
                    ]],
                ],
            ],
            [
                'slug' => 'blog-post',
                'name' => 'Blog / Notice Post Template',
                'description' => 'Inner hero, rich text body, and a related-posts grid.',
                'page_type' => 'standard',
                'sections' => [
                    ['type' => 'page_hero', 'content' => [
                        'kicker' => 'Insights',
                        'heading' => 'Article title',
                        'lead' => 'A short standfirst for the notice or blog post.',
                        'image' => $A . 'gallery/g6.jpg',
                        'crumb' => 'Blog',
                    ]],
                    ['type' => 'rich_text', 'content' => [
                        'html' => '<p>Write the article here. You can add headings, lists and links.</p><h2>Key takeaway</h2><p>Replace this with the body of the notice or post.</p>',
                    ]],
                    ['type' => 'blog', 'content' => ['kicker' => 'More', 'heading' => 'Latest from the blog']],
                ],
            ],
            [
                'slug' => 'gallery-page',
                'name' => 'Gallery Page Template',
                'description' => 'Hero plus the campus photo grid used on the live gallery page.',
                'page_type' => 'standard',
                'sections' => [
                    ['type' => 'page_hero', 'content' => [
                        'kicker' => 'Campus gallery',
                        'heading' => 'Life at VR, in pictures.',
                        'lead' => 'Classrooms, hostels, dining and campuses across Hyderabad.',
                        'image' => $A . 'gallery/classroom-empty.jpg',
                        'crumb' => 'Gallery',
                    ]],
                    ['type' => 'gallery', 'content' => [
                        'images' => implode("\n", [
                            $A . 'gallery/corridor.jpg',
                            $A . 'gallery/hostel-study.jpg',
                            $A . 'gallery/dining-portrait.jpg',
                            $A . 'gallery/hostel-room.jpg',
                            $A . 'gallery/dining-hall.jpg',
                            $A . 'gallery/classroom.jpg',
                            $A . 'gallery/building-bowrampet.jpg',
                            $A . 'gallery/building-hafeezpet.jpg',
                        ]),
                    ]],
                ],
            ],
        ];

        foreach ($pages as $p) {
            if (Database::one('SELECT id FROM page_templates WHERE slug = ?', [$p['slug']])) {
                continue;
            }
            Database::insert('page_templates', [
                'slug' => $p['slug'],
                'name' => $p['name'],
                'description' => $p['description'],
                'page_type' => $p['page_type'],
                'sections_json' => Html::json($p['sections']),
                'is_starter' => 1,
            ]);
        }

        $sections = [
            ['Hero — Admissions Open', 'hero', [
                'kicker' => 'Admissions Open 2026–27 · MPC & BiPC',
                'heading' => "Hyderabad’s leading residential junior college for MPC & BiPC.",
                'lead' => 'Expert NEET and IIT-JEE coaching backed by personal mentorship.',
                'image' => $A . 'banner/hero-class.jpg',
                'figure' => $A . 'life/campus-1.jpg',
                'cta_label' => 'Apply now',
                'cta_url' => $apply,
                'video_url' => 'https://www.youtube.com/watch?v=_3sTInMS-f4',
                'stats' => "2000+|Satisfied students\n5|Golden years",
            ]],
            ['Stats Counter', 'stats', [
                'items' => "2000+|Satisfied students\n310+|MBBS Pvt. colleges\n215+|MBBS Govt. colleges\n5|Golden years",
            ]],
            ['Testimonials Row', 'testimonials', ['label' => 'Testimonials']],
            ['FAQ Accordion', 'faq', [
                'kicker' => 'FAQs',
                'heading' => 'Questions families ask',
                'items' => "What courses does VR offer?|MPC with IIT-JEE, BiPC with NEET, and NEET long-term.\nIs VR residential?|Yes — hostel and day-scholar options.",
            ]],
            ['CTA Banner', 'cta_banner', [
                'kicker' => 'Admissions 2026',
                'heading' => 'Shape your future with the best junior college in Hyderabad',
                'text' => 'Admissions open for MPC & BiPC.',
                'image' => $A . 'banner/hero-ceremony.jpg',
                'cta_label' => 'Apply now',
                'cta_url' => $apply,
                'cta2_label' => 'Book a campus visit',
                'cta2_url' => '/contact-us/',
            ]],
            ['Faculty / Team Grid', 'faculty', [
                'kicker' => 'Our faculty',
                'heading' => 'The minds behind VR students’ success',
            ]],
        ];

        foreach ($sections as $s) {
            if (Database::one('SELECT id FROM section_templates WHERE name = ? AND type = ?', [$s[0], $s[1]])) {
                continue;
            }
            Database::insert('section_templates', [
                'name' => $s[0],
                'type' => $s[1],
                'content_json' => Html::json($s[2]),
                'created_by' => Auth::id(),
            ]);
        }
    }
}
