<?php
declare(strict_types=1);

/**
 * Blog seeding and helpers.
 */
final class Blog
{
    /** Blog posts keep their FAQ items in a JSON column. */
    public static function ensureSchema(): void
    {
        static $done = false;
        if ($done) {
            return;
        }
        $done = true;
        try {
            if (!Database::all("SHOW COLUMNS FROM blog_posts LIKE 'faq_json'")) {
                Database::pdo()->exec('ALTER TABLE blog_posts ADD COLUMN faq_json TEXT NULL AFTER body_html');
            }
        } catch (Throwable $e) {
            error_log('Blog::ensureSchema: ' . $e->getMessage());
        }
    }

    /**
     * FAQ items for a post, as [['question' => ..., 'answer' => ...], ...].
     *
     * @return list<array{question:string,answer:string}>
     */
    public static function faqItems(array $post): array
    {
        $raw = $post['faq_json'] ?? '';
        if (!is_string($raw) || trim($raw) === '') {
            return [];
        }
        $rows = json_decode($raw, true);
        if (!is_array($rows)) {
            return [];
        }
        $out = [];
        foreach ($rows as $r) {
            $q = trim((string) ($r['question'] ?? ''));
            $a = trim((string) ($r['answer'] ?? ''));
            if ($q !== '' && $a !== '') {
                $out[] = ['question' => $q, 'answer' => $a];
            }
        }
        return $out;
    }

    /**
     * Build the stored value from posted faq_q[]/faq_a[] pairs. Rows where
     * either side is blank are dropped, so an empty row just disappears.
     */
    public static function faqFromRequest(): ?string
    {
        $qs = (array) ($_POST['faq_q'] ?? []);
        $as = (array) ($_POST['faq_a'] ?? []);
        $items = [];
        foreach ($qs as $i => $q) {
            $q = trim((string) $q);
            $a = trim((string) ($as[$i] ?? ''));
            if ($q === '' || $a === '') {
                continue;
            }
            $items[] = ['question' => mb_substr($q, 0, 300), 'answer' => mb_substr($a, 0, 2000)];
            if (count($items) >= 30) {
                break;
            }
        }
        return $items ? Html::json($items) : null;
    }

    public static function ensureCategories(): void
    {
        $cats = [
            ['name' => 'NEET Coaching', 'slug' => 'neet-coaching', 'description' => 'NEET preparation tips, strategies and exam updates.'],
            ['name' => 'Admissions', 'slug' => 'admissions', 'description' => 'Admission guidance, eligibility and counselling support.'],
            ['name' => 'Campus Life', 'slug' => 'campus-life', 'description' => 'Residential facilities, student life and campus updates.'],
        ];
        foreach ($cats as $c) {
            $exists = Database::one('SELECT id FROM blog_categories WHERE slug = ?', [$c['slug']]);
            if (!$exists) {
                Database::insert('blog_categories', $c);
            }
        }
    }

    /**
     * Seed the three demo posts once, on a site that has never had them.
     *
     * This runs on every public request, so it must be cheap and it must never
     * be able to take the site down: a duplicate-key error in here returned
     * 500 for every page once the demo posts were trashed. After the first
     * run a settings flag short-circuits it, which also means posts the client
     * deletes stay deleted.
     */
    public static function ensurePosts(): void
    {
        static $done = false;
        if ($done) {
            return;
        }
        $done = true;
        try {
            if (Settings::get('blog_demo_seeded_v1') === '1') {
                return;
            }
            self::seedDemoPosts();
            Settings::set('blog_demo_seeded_v1', '1');
        } catch (Throwable $e) {
            error_log('Blog::ensurePosts: ' . $e->getMessage());
        }
    }

    private static function seedDemoPosts(): void
    {
        self::ensureCategories();
        $authorId = (int) (Database::one('SELECT id FROM users ORDER BY id LIMIT 1')['id'] ?? 0);
        $posts = self::demoPosts();
        foreach ($posts as $p) {
            // The slug is unique across ALL rows, trashed ones included, so this
            // must not filter on deleted_at: trashing a demo post made this try
            // to re-insert it, and the duplicate-key error took the site down.
            $exists = Database::one('SELECT id FROM blog_posts WHERE slug = ?', [$p['slug']]);
            if ($exists) {
                continue;
            }
            $cat = Database::one('SELECT id FROM blog_categories WHERE slug = ?', [$p['category_slug']]);
            $id = Database::insert('blog_posts', [
                'title' => $p['title'],
                'slug' => $p['slug'],
                'excerpt' => $p['excerpt'],
                'body_html' => $p['body_html'],
                'featured_image' => $p['featured_image'],
                'author_id' => $authorId ?: null,
                'status' => 'published',
                'published_at' => $p['published_at'],
            ]);
            if ($cat) {
                Database::insert('blog_post_categories', [
                    'post_id' => $id,
                    'category_id' => (int) $cat['id'],
                ]);
            }
            $seo = Content::fillCanonical([
                'seo_title' => $p['title'] . ' | VR Doctors Blog',
                'meta_description' => $p['excerpt'],
                'og_image' => $p['featured_image'],
            ], 'blog/' . $p['slug']);
            Database::upsertSeo('blog', $id, $seo);
        }
    }

    /** @return list<array<string, string>> */
    private static function demoPosts(): array
    {
        $img = static fn (string $file): string => '/assets/img/gallery/' . $file;

        return [
            [
                'slug' => 'neet-2026-preparation-roadmap',
                'category_slug' => 'neet-coaching',
                'title' => 'NEET 2026 Preparation Roadmap for BiPC Students',
                'excerpt' => 'A structured month-by-month plan for Class 11 and 12 students targeting NEET 2026 — covering Biology, Physics, Chemistry and revision cycles.',
                'featured_image' => $img('classroom.jpg'),
                'published_at' => '2025-08-15 10:00:00',
                'body_html' => <<<'HTML'
<p>Preparing for NEET while managing Intermediate board exams is one of the biggest challenges BiPC students face. At VR Doctors Academy, we help students build a <strong>disciplined, exam-oriented routine</strong> from day one.</p>
<h2>Foundation phase (Class 11)</h2>
<p>Focus on NCERT Biology line-by-line. Allocate daily slots for Physics numericals and Organic Chemistry mechanisms. Weekly full-length topic tests help identify weak areas early.</p>
<h2>Intensive phase (Class 12)</h2>
<p>Shift to MCQ practice with timed drills. Use previous-year NEET papers and institute mock tests. Maintain a mistake notebook — revisiting wrong answers is more valuable than solving new questions blindly.</p>
<h2>Revision &amp; exam temperament</h2>
<p>In the final 60 days, reduce new topics. Prioritise high-yield chapters: Human Physiology, Genetics, Thermodynamics, and Coordination Compounds. Sleep, nutrition and a calm mindset matter as much as syllabus coverage.</p>
<p><strong>Need a personalised study plan?</strong> Speak to our academic counsellors about our Long-Term and Short-Term NEET programmes in Hyderabad.</p>
HTML,
            ],
            [
                'slug' => 'how-to-choose-neet-residential-college-hyderabad',
                'category_slug' => 'admissions',
                'title' => 'How to Choose the Right NEET Residential College in Hyderabad',
                'excerpt' => 'What parents should evaluate — faculty credentials, daily schedules, hostel safety, mock-test frequency and past results — before enrolling.',
                'featured_image' => $img('building-hafeezpet.jpg'),
                'published_at' => '2025-09-01 09:30:00',
                'body_html' => <<<'HTML'
<p>Hyderabad offers many coaching options for NEET aspirants. Choosing a <strong>residential programme</strong> is a significant decision — students spend 10–12 months away from home, so the environment must support focus and wellbeing.</p>
<h2>Academic rigour</h2>
<p>Look for daily classroom hours, regular assessments, and faculty with proven NEET track records. Ask how doubt-clearing sessions are structured and whether individual mentoring is available.</p>
<h2>Residential facilities</h2>
<p>Visit the campus if possible. Check hostel rooms, dining, study halls and security. A structured daily timetable — classes, self-study, recreation — prevents burnout.</p>
<h2>Results &amp; transparency</h2>
<p>Request recent rank lists and admission statistics. VR Doctors publishes achiever stories and maintains open communication with parents through PTMs and progress reports.</p>
<h2>Next steps</h2>
<p>Book a campus visit at our Hafeezpet, Miyapur or other Hyderabad centres. Our admissions team will walk you through programmes, fees and scholarship options.</p>
HTML,
            ],
            [
                'slug' => 'life-at-vr-doctors-residential-campus',
                'category_slug' => 'campus-life',
                'title' => 'Life at VR Doctors Residential Campus: A Day in the Life',
                'excerpt' => 'From morning study hours to evening recreation — how our residential students balance academics, discipline and community on campus.',
                'featured_image' => $img('hostel-room.jpg'),
                'published_at' => '2025-09-08 11:00:00',
                'body_html' => <<<'HTML'
<p>Residential NEET coaching is not just about longer study hours — it is about building habits that last through medical college and beyond. Here is what a typical day looks like for VR Doctors residential students.</p>
<h2>Morning: Focus &amp; classes</h2>
<p>Students begin with supervised self-study, followed by subject-wise classroom sessions. Biology, Physics and Chemistry are taught by specialist faculty with emphasis on NCERT and previous-year patterns.</p>
<h2>Afternoon: Practice &amp; doubt clearing</h2>
<p>Post-lunch sessions include problem-solving workshops and one-on-one doubt clearing. Weekly tests simulate NEET timing and difficulty.</p>
<h2>Evening: Balance &amp; community</h2>
<p>Structured recreation, nutritious meals in our dining halls, and quiet study hours in hostel rooms help students recharge without losing momentum.</p>
<h2>Parent connect</h2>
<p>Regular parent-teacher meetings and digital progress updates keep families informed. Many parents tell us the residential environment gives their child the discipline they could not replicate at home.</p>
<p>Interested in our BiPC + NEET residential programme? <strong>Request a callback</strong> and we will arrange a campus tour.</p>
HTML,
            ],
        ];
    }
}
