<?php
declare(strict_types=1);

/**
 * VR Doctors Academy — starter content matching https://vrdoctors.in/
 */

$permMap = require ROOT . '/config/permissions.php';
$permIds = [];
foreach ($permMap as $group => $actions) {
    foreach ($actions as $act) {
        $slug = $group . '.' . $act;
        $permIds[$slug] = Database::insert('permissions', [
            'slug' => $slug,
            'name' => ucfirst($group) . ' ' . $act,
            'group_name' => $group,
        ]);
    }
}

$roleIds = [];
foreach ([
    'super-admin' => 'Super Admin',
    'editor' => 'Editor',
    'staff' => 'Staff',
    'viewer' => 'Viewer',
] as $slug => $name) {
    $roleIds[$slug] = Database::insert('roles', ['slug' => $slug, 'name' => $name, 'is_system' => 1]);
}
foreach ($permIds as $pid) {
    Database::insert('role_permissions', ['role_id' => $roleIds['super-admin'], 'permission_id' => $pid]);
}
foreach ($permIds as $slug => $pid) {
    if (str_ends_with($slug, '.view') || in_array($slug, [
        'pages.create', 'pages.edit', 'pages.publish',
        'entries.create', 'entries.edit', 'entries.publish',
        'blog.create', 'blog.edit', 'blog.publish',
        'media.upload', 'faqs.edit', 'testimonials.edit', 'forms.edit', 'leads.edit', 'menus.edit',
    ], true)) {
        Database::insert('role_permissions', ['role_id' => $roleIds['editor'], 'permission_id' => $pid]);
    }
    if (in_array($slug, [
        'pages.view', 'entries.view', 'blog.view', 'media.view', 'faqs.view',
        'testimonials.view', 'forms.view', 'leads.view', 'leads.edit', 'leads.export',
    ], true)) {
        Database::insert('role_permissions', ['role_id' => $roleIds['staff'], 'permission_id' => $pid]);
    }
    if (str_ends_with($slug, '.view')) {
        Database::insert('role_permissions', ['role_id' => $roleIds['viewer'], 'permission_id' => $pid]);
    }
}

$adminId = Database::insert('users', [
    'role_id' => $roleIds['super-admin'],
    'name' => 'Site Admin',
    'email' => 'admin@vrdoctors.in',
    'password_hash' => password_hash('ChangeMe_VRDR2026', PASSWORD_DEFAULT),
    'status' => 'active',
]);

$schema = [
    '@context' => 'https://schema.org',
    '@type' => 'EducationalOrganization',
    'name' => 'VR Doctors Academy',
    'url' => 'https://vrdoctors.in/',
    'email' => 'admissions@vrdoctors.in',
    'telephone' => ['+91 9256 9256 40', '+91 9256 9256 41', '+91 9256 9256 42', '+91 9256 9256 43'],
    'address' => [
        '@type' => 'PostalAddress',
        'streetAddress' => 'Plot No 29, Mathrusree Nagar, Hafeezpet, Miyapur',
        'addressLocality' => 'Hyderabad',
        'addressRegion' => 'Telangana',
        'postalCode' => '500049',
        'addressCountry' => 'IN',
    ],
    'sameAs' => [
        'https://www.facebook.com/VR.Jr.College',
        'https://www.instagram.com/vr_junior.college/',
        'https://www.youtube.com/@VR_JuniorCollege',
    ],
];

$stats = [
    ['target' => 6, 'suffix' => '+', 'label' => 'Years of excellence'],
    ['target' => 6, 'suffix' => '', 'label' => 'Successful batches'],
    ['target' => 600, 'suffix' => '+', 'label' => 'Doctors produced'],
    ['target' => 101, 'suffix' => '+', 'label' => 'Medical seats in 2025'],
];

$settings = [
    'brand_name' => 'VR Doctors Academy',
    'tagline' => 'Vision Into Reality',
    'logo' => '/assets/img/brand/logo.webp',
    'favicon' => '/assets/img/brand/favicon.webp',
    'phone_primary' => '+91 9256 9256 40',
    'phone_secondary' => '+91 9256 9256 41',
    'phone_3' => '+91 9256 9256 42',
    'phone_4' => '+91 9256 9256 43',
    'email' => 'admissions@vrdoctors.in',
    'email_secondary' => 'info@vrdoctorsacademy.com',
    'whatsapp' => '919256925640',
    'hours' => 'Admissions desk: Monday – Saturday',
    'head_office' => 'Plot No 29, Mathrusree Nagar, Hafeezpet, Miyapur, Hyderabad, Telangana 500049',
    'facebook' => 'https://www.facebook.com/VR.Jr.College',
    'instagram' => 'https://www.instagram.com/vr_junior.college/',
    'youtube' => 'https://www.youtube.com/@VR_JuniorCollege',
    'linkedin' => '',
    'maps_url' => 'https://maps.app.goo.gl/3qrkSSCw6kiYNkwH8',
    'default_seo_title' => 'BiPC Junior College in Hyderabad with NEET Coaching | VR Doctors',
    'default_seo_description' => 'VR Doctors Academy is a top residential BiPC junior college in Hyderabad offering integrated NEET coaching, expert faculty & proven results. Book a visit today.',
    'og_image' => '/og-image.jpg',
    'schema_json' => json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
    'stats_json' => json_encode($stats, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
    'api_cors_origins' => 'http://localhost:3000,http://127.0.0.1:3000,https://vr-doctors-clone.vercel.app,https://vrdoctors.in',
    'robots_txt' => "User-agent: *\nAllow: /\nDisallow: /admin/\nDisallow: /login/\nDisallow: /preview/\nDisallow: /api/\nDisallow: /cron/\nSitemap: https://vrdoctors.in/sitemap.xml\n",
    'smtp_from_name' => 'VR Doctors Academy',
    'smtp_from_email' => 'admissions@vrdoctors.in',
    'backup_auto' => '0',
    'backup_interval' => 'daily',
    'backup_retention' => '14',
    'maintenance_mode' => '0',
    '404_heading' => 'Page not found',
    '404_text' => 'That address isn’t on this site. Head home or talk to admissions.',
];
foreach ($settings as $k => $v) {
    Database::insert('settings', ['setting_key' => $k, 'setting_value' => $v]);
}

$addField = static function (int $typeId, string $name, string $label, string $type, int $req = 0, ?array $opts = null, int $order = 0) {
    Database::insert('post_type_fields', [
        'post_type_id' => $typeId,
        'name' => $name,
        'label' => $label,
        'type' => $type,
        'is_required' => $req,
        'options_json' => $opts ? json_encode($opts) : null,
        'sort_order' => $order,
    ]);
};

$coursesType = Database::insert('post_types', [
    'name' => 'Courses',
    'singular_name' => 'Course',
    'slug' => 'courses',
    'description' => 'NEET programmes shown on /courses and landing pages',
    'has_archive' => 1,
    'public' => 1,
    'template_mode' => 'both',
    'archive_title' => 'NEET Coaching Courses in Hyderabad',
    'archive_intro' => 'Intermediate + NEET, Long-Term NEET, and Short-Term NEET Revision programmes.',
    'sort_order' => 1,
    'status' => 'active',
    'is_system' => 1,
]);
$addField($coursesType, 'tagline', 'Tagline', 'text', 0, null, 1);
$addField($coursesType, 'duration', 'Duration', 'text', 0, null, 2);
$addField($coursesType, 'eligibility', 'Eligibility', 'textarea', 0, null, 3);
$addField($coursesType, 'structure', 'Structure', 'textarea', 0, null, 4);
$addField($coursesType, 'nav_label', 'Nav label', 'text', 0, null, 5);
$addField($coursesType, 'landing_url', 'Landing URL', 'url', 0, null, 6);
$addField($coursesType, 'highlights', 'Highlights', 'repeater', 0, ['subfields' => [['name' => 'item', 'label' => 'Highlight', 'type' => 'text']]], 7);

$facultyType = Database::insert('post_types', [
    'name' => 'Faculty',
    'singular_name' => 'Faculty member',
    'slug' => 'faculty',
    'description' => 'Teachers shown on About and home',
    'has_archive' => 0,
    'public' => 0,
    'template_mode' => 'fields',
    'sort_order' => 2,
    'status' => 'active',
    'is_system' => 1,
]);
$addField($facultyType, 'designation', 'Designation', 'text', 1, null, 1);
$addField($facultyType, 'department', 'Department', 'text', 0, null, 2);
$addField($facultyType, 'experience', 'Experience', 'text', 0, null, 3);
$addField($facultyType, 'bio', 'Bio', 'textarea', 0, null, 4);

$achieverType = Database::insert('post_types', [
    'name' => 'Achievers',
    'singular_name' => 'Achiever',
    'slug' => 'achievers',
    'description' => 'Results hall of fame',
    'has_archive' => 0,
    'public' => 0,
    'template_mode' => 'fields',
    'sort_order' => 3,
    'status' => 'active',
    'is_system' => 1,
]);
$addField($achieverType, 'marks', 'Marks / rank', 'text', 0, null, 1);
$addField($achieverType, 'college', 'College', 'text', 0, null, 2);
$addField($achieverType, 'year', 'Year', 'text', 0, null, 3);
$addField($achieverType, 'quote', 'Quote', 'textarea', 0, null, 4);

$careerType = Database::insert('post_types', [
    'name' => 'BiPC Careers',
    'singular_name' => 'Career path',
    'slug' => 'careers',
    'description' => 'World of BiPC explorer',
    'has_archive' => 0,
    'public' => 0,
    'template_mode' => 'fields',
    'sort_order' => 4,
    'status' => 'active',
    'is_system' => 1,
]);
$addField($careerType, 'category', 'Category', 'text', 1, null, 1);
$addField($careerType, 'duration', 'Duration', 'text', 0, null, 2);
$addField($careerType, 'entrance', 'Entrance exam', 'text', 0, null, 3);
$addField($careerType, 'focus', 'Focus area', 'text', 0, null, 4);
$addField($careerType, 'paths', 'Career paths', 'text', 0, null, 5);

$deptType = Database::insert('post_types', [
    'name' => 'Departments',
    'singular_name' => 'Department',
    'slug' => 'departments',
    'description' => 'Academic departments (Physics, Chemistry, Botany, Zoology)',
    'has_archive' => 0,
    'public' => 0,
    'template_mode' => 'fields',
    'sort_order' => 5,
    'status' => 'active',
    'is_system' => 0,
]);
$addField($deptType, 'subject', 'Subject', 'text', 1, null, 1);
$addField($deptType, 'summary', 'Summary', 'textarea', 0, null, 2);

$campusType = Database::insert('post_types', [
    'name' => 'Campuses',
    'singular_name' => 'Campus',
    'slug' => 'campuses',
    'description' => 'Campus NAP used in contact and footer',
    'has_archive' => 0,
    'public' => 0,
    'template_mode' => 'fields',
    'sort_order' => 6,
    'status' => 'active',
    'is_system' => 1,
]);
$addField($campusType, 'address', 'Address', 'textarea', 1, null, 1);
$addField($campusType, 'phone', 'Phone', 'text', 0, null, 2);
$addField($campusType, 'email', 'Email', 'email', 0, null, 3);
$addField($campusType, 'map_url', 'Map URL', 'url', 0, null, 4);

$collegeType = Database::insert('post_types', [
    'name' => 'Admission colleges',
    'singular_name' => 'College image',
    'slug' => 'colleges',
    'description' => 'Medical college gallery on Results',
    'has_archive' => 0,
    'public' => 0,
    'template_mode' => 'fields',
    'sort_order' => 7,
    'status' => 'active',
    'is_system' => 0,
]);
$addField($collegeType, 'alt', 'Alt text', 'text', 0, null, 1);

$videoType = Database::insert('post_types', [
    'name' => 'Videos',
    'singular_name' => 'Video',
    'slug' => 'videos',
    'description' => 'YouTube result / campus videos',
    'has_archive' => 0,
    'public' => 0,
    'template_mode' => 'fields',
    'sort_order' => 8,
    'status' => 'active',
    'is_system' => 0,
]);
$addField($videoType, 'youtube_id', 'YouTube ID', 'text', 1, null, 1);
$addField($videoType, 'caption', 'Caption', 'text', 0, null, 2);

$timelineType = Database::insert('post_types', [
    'name' => 'Timeline',
    'singular_name' => 'Milestone',
    'slug' => 'timeline',
    'description' => 'About page journey',
    'has_archive' => 0,
    'public' => 0,
    'template_mode' => 'fields',
    'sort_order' => 9,
    'status' => 'active',
    'is_system' => 0,
]);
$addField($timelineType, 'year', 'Year', 'text', 1, null, 1);
$addField($timelineType, 'summary', 'Summary', 'textarea', 0, null, 2);

$aiType = Database::insert('post_types', [
    'name' => 'AI Posts',
    'singular_name' => 'AI Post',
    'slug' => 'ai',
    'description' => 'AI landing pages — post title and description show on the banner',
    'has_archive' => 0,
    'public' => 1,
    'template_mode' => 'builder',
    'sort_order' => 10,
    'status' => 'active',
    'is_system' => 1,
]);

$addEntry = static function (int $typeId, string $title, string $slug, array $fields, array $meta = []) use ($adminId): int {
    $existing = Database::one(
        'SELECT id FROM cpt_entries WHERE post_type_id = ? AND slug = ? AND deleted_at IS NULL',
        [$typeId, $slug]
    );
    if ($existing) {
        return (int) $existing['id'];
    }
    $id = Database::insert('cpt_entries', [
        'post_type_id' => $typeId,
        'title' => $title,
        'slug' => $slug,
        'status' => 'published',
        'excerpt' => $meta['excerpt'] ?? ($fields['summary'] ?? ''),
        'featured_image' => $meta['image'] ?? '',
        'body_html' => $meta['body'] ?? null,
        'fields_json' => json_encode($fields, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        'published_at' => date('Y-m-d H:i:s'),
        'author_id' => $adminId,
        'sort_order' => $meta['sort'] ?? 0,
    ]);
    Database::insert('seo_metadata', [
        'entity_type' => 'cpt',
        'entity_id' => $id,
        'seo_title' => $meta['seo_title'] ?? ($title . ' | VR Doctors Academy'),
        'meta_description' => $meta['meta_description'] ?? ($fields['tagline'] ?? ''),
        'canonical_url' => $meta['canonical'] ?? url($slug),
        'robots' => 'index,follow',
    ]);
    return $id;
};

$courseIds = [];
$courseIds['integrated'] = $addEntry($coursesType, 'BiPC + NEET Residential', 'bipc-neet-residential', [
    'tagline' => 'Two-year Intermediate (BiPC) with integrated NEET coaching',
    'duration' => '2 academic years',
    'eligibility' => 'Students joining after Class 10',
    'structure' => 'TSBIE Intermediate curriculum paired with in-house NEET coaching under one timetable.',
    'nav_label' => 'BiPC + NEET Residential',
    'landing_url' => '/best-bipc-college-in-hyderabad-neet-residential/',
    'highlights' => [
        ['item' => 'Intermediate Academics + NEET Coaching'],
        ['item' => 'Residential Campus Environment'],
        ['item' => 'Structured Daily Study Plan'],
        ['item' => 'Regular Assessments & Analysis'],
        ['item' => 'Personal Faculty Mentoring'],
    ],
], [
    'excerpt' => 'A two-year residential BiPC junior college programme in Hyderabad that combines board exams with NEET(UG).',
    'sort' => 1,
    'seo_title' => 'Best BiPC College (NEET Residential) - VR Doctors',
    'meta_description' => 'VR Doctors Academy is a top residential BiPC junior college in Hyderabad offering integrated NEET coaching, expert faculty & proven results. Book a visit today.',
    'canonical' => url('best-bipc-college-in-hyderabad-neet-residential'),
]);

$courseIds['long-term'] = $addEntry($coursesType, 'Long-Term NEET Program', 'long-term-neet', [
    'tagline' => '8–10 month residential NEET programme for Intermediate completers (Focus-45)',
    'duration' => '8–10 months',
    'eligibility' => 'Students who have completed Intermediate (BiPC) and are preparing for NEET(UG)',
    'structure' => 'NEET-focused programme building on completed Intermediate concepts — efficiency and speed based on NCERT. No IPE.',
    'nav_label' => 'Long-Term NEET (2-Year)',
    'landing_url' => '/long-term-neet-program-hyderabad/',
    'highlights' => [
        ['item' => 'Complete NEET Syllabus Coverage'],
        ['item' => 'Concept Building & Application'],
        ['item' => 'Weekly Tests & Grand Tests'],
        ['item' => 'Performance Tracking & Analysis'],
        ['item' => 'Personalized Academic Support'],
    ],
], [
    'excerpt' => 'Intensive 8–10 month residential NEET coaching in Hyderabad for Intermediate completers.',
    'sort' => 2,
    'seo_title' => 'Long-Term NEET Coaching in Hyderabad | VR Doctors Academy',
    'meta_description' => 'VR Doctors Academy\'s Long-Term NEET Coaching in Hyderabad is an 8–10 month residential NEET course (Focus-45) in Hyderabad for Intermediate completers. Book a campus visit today.',
    'canonical' => url('long-term-neet-program-hyderabad'),
]);

$courseIds['short-term'] = $addEntry($coursesType, 'Short-Term NEET Program', 'short-term-neet', [
    'tagline' => '45–60 day residential NEET crash course (Focus-40)',
    'duration' => '45–60 days (typically January to April)',
    'eligibility' => 'Students who have completed Class 12 / Intermediate (BiPC), including repeaters',
    'structure' => 'Intensive NEET-only crash course — complete focus on NCERT with no IPE component.',
    'nav_label' => 'Short-Term NEET (1-Year)',
    'landing_url' => '/short-term-neet-program-hyderabad/',
    'highlights' => [
        ['item' => 'Fast-track NCERT revision'],
        ['item' => '6 hours of daily classes'],
        ['item' => '5–6 hours supervised study'],
        ['item' => 'Daily, weekly and monthly tests'],
        ['item' => 'Senior faculty mentoring (~15:1)'],
    ],
], [
    'excerpt' => '45–60 day fully residential NEET crash course in Hyderabad.',
    'sort' => 3,
    'seo_title' => 'Short-Term NEET Coaching in Hyderabad | VR Doctors Academy',
    'meta_description' => 'VR Doctors Academy\'s Short-Term NEET Coaching in Hyderabad is a 45–60 day residential NEET crash course in Hyderabad. Book a campus visit today.',
    'canonical' => url('short-term-neet-program-hyderabad'),
]);

$faculty = [
    ['KVR Sir', 'kvr-sir', 'Academic Head', 'Academics', '37+ Years Experience', '/faculty/KVR Sir.webp'],
    ['G Ashok', 'g-ashok', 'Sr. Physics', 'Physics', '22+ Years Experience', '/faculty/G Ashok.webp'],
    ['B Nagesh', 'b-nagesh', 'Sr. Botany', 'Botany', '20+ Years Experience', '/faculty/B Nagesh.webp'],
    ['M Srinath', 'm-srinath', 'Sr. Zoology', 'Zoology', '8+ Years Experience', '/faculty/M Srinath.webp'],
    ['P Malyadri', 'p-malyadri', 'Sr. Chemistry', 'Chemistry', '18+ Years Experience', '/faculty/P Malyadri.webp'],
    ['K Prabhakar Reddy', 'k-prabhakar-reddy', 'Sr. Zoology', 'Zoology', '16+ Years Experience', '/faculty/K Prabhakar Reddy.webp'],
];
foreach ($faculty as $i => $f) {
    $addEntry($facultyType, $f[0], $f[1], [
        'designation' => $f[2],
        'department' => $f[3],
        'experience' => $f[4],
    ], ['image' => $f[5], 'sort' => $i + 1]);
}

$achievers = [
    ['Inamul Hussian', 'inamul-hussian', '569 Marks', 'Bidar Institute of Medical Sciences', '/results/Asset 5.png'],
    ['Gopika Rani', 'gopika-rani', '597 Marks', 'Osmania Medical College - Hyd', '/results/Asset 15.png'],
    ['VVNB Kireeti', 'vvnb-kireeti', '588 Marks', 'Apollo Medical College', '/results/Asset 22.png'],
    ['Chandra Mohan Reddy', 'chandra-mohan-reddy', '582 Marks', 'Kakatiya Medical College', '/results/Asset 6.png'],
    ['G Anshika Varshini', 'g-anshika-varshini', '556 Marks', 'Gandhi Medical College', '/results/Asset 20.png'],
    ['K Sai Devi Sree', 'k-sai-devi-sree', '551 Marks', 'Government Medical College', '/results/Asset 10.png'],
    ['D Sai Kumar', 'd-sai-kumar', '525 Marks', 'Osmania Medical College', '/results/Asset 14.png'],
];
foreach ($achievers as $i => $a) {
    $addEntry($achieverType, $a[0], $a[1], [
        'marks' => $a[2],
        'college' => $a[3],
        'year' => '2025',
    ], ['image' => $a[4], 'sort' => $i + 1]);
}

$careers = [
    ['MBBS', 'mbbs', 'Patient Care', '5.5 Years', 'NEET', 'Diagnosis and treatment of patients', 'Doctor • Surgeon • Specialist'],
    ['BDS', 'bds', 'Patient Care', '5 Years', 'NEET', 'Oral health and dentistry', 'Dentist • Orthodontist'],
    ['Nursing', 'nursing', 'Patient Care', '4 Years', 'State Entrance / Merit', 'Patient care and healthcare support', 'Nurse • Clinical Coordinator'],
    ['BVSc', 'bvsc', 'Animal Care', '5.5 Years', 'NEET', 'Animal diagnosis and treatment', 'Veterinary Doctor • Research Officer'],
    ['Animal Husbandry', 'animal-husbandry', 'Animal Care', '4 Years', 'State Entrance', 'Livestock management', 'Consultant • Livestock Manager'],
    ['B.Pharmacy', 'b-pharmacy', 'Medicines & Treatment', '4 Years', 'EAPCET', 'Medicines and pharmaceuticals', 'Pharmacist • Drug Research'],
    ['Pharm D', 'pharm-d', 'Medicines & Treatment', '6 Years', 'EAPCET', 'Clinical pharmacy', 'Clinical Pharmacist'],
    ['BAMS', 'bams', 'Traditional Medicine', '5.5 Years', 'NEET', 'Ayurvedic medicine', 'Ayurvedic Physician'],
    ['BHMS', 'bhms', 'Traditional Medicine', '5.5 Years', 'NEET', 'Homeopathic medicine', 'Homeopathic Physician'],
    ['B.Sc Agriculture', 'bsc-agriculture', 'Food & Agriculture', '4 Years', 'State Entrance', 'Crop science and agri-systems', 'Agronomist • Agri Officer'],
    ['Biotechnology', 'biotechnology', 'Research & Life Sciences', '4 Years', 'Merit / Entrance', 'Applied biological research', 'Research Scientist'],
    ['Microbiology', 'microbiology', 'Research & Life Sciences', '3–4 Years', 'Merit', 'Microbes and lab science', 'Lab Scientist • QA'],
];
foreach ($careers as $i => $c) {
    $addEntry($careerType, $c[0], $c[1], [
        'category' => $c[2],
        'duration' => $c[3],
        'entrance' => $c[4],
        'focus' => $c[5],
        'paths' => $c[6],
    ], ['sort' => $i + 1]);
}

foreach ([
    ['Physics', 'physics', 'NEET Physics with an applicative, problem-solving approach.'],
    ['Chemistry', 'chemistry', 'Physical, organic and inorganic chemistry aligned to NCERT and NEET.'],
    ['Botany', 'botany', 'Plant biology with weekly recall tests and NCERT-first teaching.'],
    ['Zoology', 'zoology', 'Human and animal biology for NEET(UG).'],
] as $i => $d) {
    $addEntry($deptType, $d[0], $d[1], ['subject' => $d[0], 'summary' => $d[2]], ['sort' => $i + 1]);
}

$addEntry($campusType, 'Hafeezpet / Miyapur', 'hafeezpet-miyapur', [
    'address' => 'Plot No 29, Mathrusree Nagar, Hafeezpet, Miyapur, Hyderabad, Telangana 500049',
    'phone' => '+91 9256 9256 40',
    'email' => 'admissions@vrdoctors.in',
    'map_url' => 'https://maps.app.goo.gl/3qrkSSCw6kiYNkwH8',
], ['sort' => 1]);

foreach ([
    ['Universities 07', 'universities-07', '/medical admissions/Universities-07.webp'],
    ['Universities 02', 'universities-02', '/medical admissions/Universities-02.webp'],
    ['Universities 03', 'universities-03', '/medical admissions/Universities-03.webp'],
    ['Universities 04', 'universities-04', '/medical admissions/Universities-04.webp'],
    ['Universities 01', 'universities-01', '/medical admissions/Universities-01.webp'],
    ['Universities 06', 'universities-06', '/medical admissions/Universities-06.webp'],
] as $i => $c) {
    $addEntry($collegeType, $c[0], $c[1], ['alt' => 'Medical college admission — ' . $c[0]], ['image' => $c[2], 'sort' => $i + 1]);
}

$timeline = [
    ['2019', '2019', 'VR Doctors Academy Founded', 'Started with a vision to help aspiring medical students transform their dreams into reality.', '/journey/2019.webp'],
    ['2020', '2020', 'Produced Our First Batch Of Doctors', 'The first successful batch marked the beginning of a journey that would impact hundreds of future medical professionals.', '/journey/2020.webp'],
    ['2022', '2022', 'Strengthened Residential NEET Programs', 'Built a disciplined residential ecosystem designed to maximize student success and consistency.', '/journey/2022.webp'],
    ['2023', '2023', 'Launched VRIIT', 'Expanded academic offerings to support IIT aspirants through a dedicated program.', '/journey/2023.webp'],
    ['2024', '2024', 'Expanded Through VR Junior College', 'Broadened academic pathways and strengthened the educational ecosystem for students.', '/journey/2024.webp'],
    ['2026', '2026', 'Building The Next Chapter', 'Continuing our efforts to provide quality education to future healthcare professionals.', '/journey/2026.webp'],
];
foreach ($timeline as $i => $t) {
    $addEntry($timelineType, $t[2], $t[1], ['year' => $t[0], 'summary' => $t[3]], ['image' => $t[4], 'sort' => $i + 1, 'excerpt' => $t[3]]);
}

$homeTestimonials = [
    ['Anumalla Akshitha', 'Osmania Medical College', 'Score 617/720', 'VR Doctors Academy gave me the discipline, faculty support and confidence to achieve my dream.', 'AA'],
    ['Annangi Akhila', 'Osmania Medical College', 'Govt Seat Secured', 'Excellent faculty, stress-free learning and a wonderful residential environment helped me succeed.', 'AA'],
    ['Bisaoi Vamshi Krishna', 'Gandhi Medical College', 'AIR 507', 'The study culture and mentoring at VR Doctors made all the difference in my preparation.', 'BV'],
    ['Dodla Vaishnavi', 'Government Medical College, Siddipet', 'MBBS Seat Secured', 'Healthy food, experienced lecturers and constant motivation helped me achieve my goal.', 'DV'],
];
foreach ($homeTestimonials as $i => $t) {
    Database::insert('testimonials', [
        'name' => $t[0],
        'role' => $t[1] . ' · ' . $t[2],
        'quote' => $t[3],
        'initials' => $t[4],
        'sort_order' => $i + 1,
        'is_visible' => 1,
    ]);
}

$faqs = [
    ['global', 'Why choose VR Doctors Academy for NEET in Hyderabad?', 'VR Doctors Academy is a residential BiPC junior college built around NEET: integrated board + NEET teaching, senior faculty, daily tests, and a supervised hostel campus in Hafeezpet, Miyapur.'],
    ['global', 'Where is the campus?', 'Plot No 29, Mathrusree Nagar, Hafeezpet, Miyapur, Hyderabad, Telangana 500049.'],
    ['global', 'How do I apply?', 'Call +91 9256 9256 40 / 41 / 42 / 43, email admissions@vrdoctors.in, or submit the callback form. Counselling and enrolment follow a campus visit.'],
    ['long-term', 'What is the Long-Term NEET Program at VR Doctors Academy?', 'The Long-Term NEET Program is an intensive 8–10 month residential NEET coaching program designed for students who have completed Intermediate or Class 12 and want focused preparation for NEET (UG).'],
    ['long-term', 'Who is eligible for the Long-Term NEET Program?', 'Students who have completed Intermediate / Class 12 are eligible to join the program.'],
    ['long-term', 'How is the Long-Term Program different from the Short-Term NEET Program?', 'The Long-Term Program provides an extended 8–10 month preparation period. The Short-Term Program is a 45–60 day intensive crash course.'],
    ['long-term', 'What is the duration of the Long-Term NEET Program?', 'The program runs for approximately 8–10 months, depending on the academic schedule and NEET examination timeline.'],
    ['short-term', 'What is the Short-Term NEET Program at VR Doctors Academy?', 'The Short-Term NEET Program is an intensive NEET crash course designed for students who have completed Class 12 / Intermediate and want focused preparation during the final stage before the NEET examination.'],
    ['short-term', 'Does the Short-Term Program include Intermediate or IPE preparation?', 'No. Students joining this program have already completed Intermediate. The program focuses entirely on NEET (UG) preparation.'],
    ['short-term', 'What is the duration of the Short-Term NEET Program?', '45–60 days, typically January to April, aligned to the NEET examination timeline.'],
];
foreach ($faqs as $i => $f) {
    Database::insert('faqs', [
        'question' => $f[1],
        'answer' => $f[2],
        'entity_type' => $f[0],
        'sort_order' => $i + 1,
        'is_visible' => 1,
    ]);
}

$formId = Database::insert('forms', [
    'name' => 'Appointment / callback',
    'slug' => 'appointment',
    'success_message' => 'Thank you! Our team of education counsellors will contact you shortly.',
    'notify_email' => 'admissions@vrdoctors.in',
    'honeypot_field' => 'website',
    'is_active' => 1,
]);
Database::insert('form_recipients', ['form_id' => $formId, 'email' => 'admissions@vrdoctors.in']);
$ff = [
    ['student_name', 'Student Name', 'text', 1, 'half', ''],
    ['parent_name', 'Parent Name', 'text', 1, 'half', ''],
    ['mobile', 'Mobile Number', 'tel', 1, 'half', ''],
    ['current_class', 'Current Class', 'select', 1, 'half', json_encode(['choices' => ['10th Appearing', 'Completed 10th', 'Inter 1st Year', 'Inter 2nd Year', 'Repeater']])],
    ['interested_course', 'Interested Course', 'select', 1, 'half', json_encode(['choices' => ['Intermediate + NEET', 'Long-Term NEET', 'Short-Term Revision']])],
    ['residential', 'Residential Program', 'select', 0, 'half', json_encode(['choices' => ['Yes', 'No']])],
    ['message', 'Message', 'textarea', 0, 'full', ''],
];
foreach ($ff as $i => $f) {
    Database::insert('form_fields', [
        'form_id' => $formId,
        'name' => $f[0],
        'label' => $f[1],
        'type' => $f[2],
        'is_required' => $f[3],
        'width' => $f[4],
        'options_json' => $f[5] ?: null,
        'sort_order' => $i + 1,
    ]);
}

$contactForm = Database::insert('forms', [
    'name' => 'Contact enquiry',
    'slug' => 'contact',
    'success_message' => 'Thank you. Please call +91 9256 9256 40 so our admissions team can guide you.',
    'notify_email' => 'admissions@vrdoctors.in',
    'honeypot_field' => 'website',
    'is_active' => 1,
]);
Database::insert('form_recipients', ['form_id' => $contactForm, 'email' => 'admissions@vrdoctors.in']);
foreach ([
    ['name', 'Your name', 'text', 1, 'half'],
    ['email', 'Your email', 'email', 0, 'half'],
    ['phone', 'Phone', 'tel', 1, 'half'],
    ['subject', 'Subject', 'text', 0, 'half'],
    ['message', 'Your message', 'textarea', 0, 'full'],
] as $i => $f) {
    Database::insert('form_fields', [
        'form_id' => $contactForm,
        'name' => $f[0],
        'label' => $f[1],
        'type' => $f[2],
        'is_required' => $f[3],
        'width' => $f[4],
        'sort_order' => $i + 1,
    ]);
}

$addPage = static function (string $slug, string $title, array $sections, array $seo = []) use ($adminId): int {
    $id = Database::insert('pages', [
        'type' => 'standard',
        'title' => $title,
        'slug' => $slug,
        'status' => 'published',
        'published_at' => date('Y-m-d H:i:s'),
        'created_by' => $adminId,
        'updated_by' => $adminId,
    ]);
    foreach ($sections as $i => $sec) {
        Database::insert('content_sections', [
            'owner_type' => 'page',
            'owner_id' => $id,
            'type' => $sec[0],
            'content_json' => json_encode($sec[1], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'sort_order' => $i,
            'is_visible' => 1,
        ]);
    }
    Database::insert('seo_metadata', array_merge([
        'entity_type' => 'page',
        'entity_id' => $id,
        'seo_title' => $title,
        'canonical_url' => url($slug === '/' ? '/' : $slug),
        'robots' => 'index,follow',
    ], $seo));
    Content::snapshot('page', $id, true, 'Initial publish');
    return $id;
};

$pageIds = [];
$pageIds['home'] = $addPage('/', 'Home', [
    ['page_hero', [
        'kicker' => 'Vision Into Reality',
        'heading' => 'Every Doctor Begins With A Dream',
        'lead' => 'Do you dream of wearing a white coat? Every successful doctor starts with a vision for the future.',
    ]],
    ['stats', ['items' => "6+|Years of excellence\n6|Successful batches\n600+|Doctors produced\n101+|Medical seats in 2025"]],
    ['testimonials', ['label' => 'What our students have to say']],
    ['enquire', [
        'heading' => 'Experience VR Doctors In Person',
        'lede' => 'Visit our campus, interact with faculty, explore hostel facilities, and discover why hundreds of students have transformed their dreams into medical careers.',
    ]],
], [
    'seo_title' => 'BiPC Junior College in Hyderabad with NEET Coaching | VR Doctors',
    'meta_description' => 'VR Doctors Academy is a top residential BiPC junior college in Hyderabad offering integrated NEET coaching, expert faculty & proven results. Book a visit today.',
]);

$pageIds['about'] = $addPage('about', 'About', [
    ['page_hero', [
        'kicker' => 'About VR Doctors Academy',
        'heading' => 'Transforming Aspirations Into Medical Careers Since 2019',
        'lead' => 'From a humble beginning with 95 students to a thriving academic community supporting 1000+ aspirants every year.',
    ]],
    ['faculty', ['kicker' => 'Meet Our Faculty', 'heading' => 'Expert Educators, Personal Mentors']],
], [
    'seo_title' => 'About VR Doctors Academy',
    'meta_description' => 'Learn about the journey of VR Doctors Academy, our mission, faculty, values and commitment to helping students succeed in NEET and medical careers.',
]);

$pageIds['bipc'] = $addPage('bipc-careers', 'World of BiPC', [
    ['page_hero', [
        'heading' => 'One Decision. A Lifetime Of Impact.',
        'lead' => 'Most students choose BiPC with a dream of becoming doctors. BiPC also opens healthcare, science, research and service careers.',
    ]],
], [
    'seo_title' => 'Career in BiPC: Explore Career Options After BiPC | VR Doctors',
    'meta_description' => 'Curious about a career in BiPC? Explore the career options after BiPC — MBBS, BDS, BVSc, Pharmacy, Nursing and more — and find the path that fits you.',
]);

$pageIds['courses'] = $addPage('courses', 'Courses', [
    ['page_hero', [
        'heading' => 'Programs Designed For Future Doctors',
        'lead' => 'Intermediate + NEET, Long-Term NEET, and Short-Term NEET Revision — pick the path that matches your stage.',
    ]],
    ['programs', ['kicker' => 'Our programmes', 'heading' => 'Choose Your Program']],
], [
    'seo_title' => 'NEET Coaching Courses in Hyderabad | VR Doctors Academy',
    'meta_description' => 'Explore NEET coaching courses in Hyderabad at VR Doctors Academy — Intermediate + NEET, Long-Term NEET, and Short-Term NEET Revision programs. Book a visit today.',
]);

$pageIds['results'] = $addPage('results', 'Results', [
    ['page_hero', [
        'heading' => '600+ Medical Careers Guided Since 2019',
        'lead' => 'Every result represents a student, a dream and years of disciplined preparation.',
    ]],
], [
    'seo_title' => 'NEET Results & Medical Admissions | VR Doctors Academy',
    'meta_description' => 'Discover the success stories, top achievers and medical college admissions of students from VR Doctors Academy since 2019.',
]);

$pageIds['contact'] = $addPage('contact', 'Contact', [
    ['page_hero', [
        'heading' => "Let's Plan Your Medical Journey",
        'lead' => 'Whether you\'re a student exploring BiPC, a parent seeking guidance, or someone preparing for NEET, our team is here to help.',
    ]],
    ['enquire', ['heading' => 'Request A Callback', 'lede' => 'Fill in your details and our admissions team will get in touch with you shortly.']],
], [
    'seo_title' => 'Contact Admissions | VR Doctors Academy',
    'meta_description' => 'Contact VR Doctors Academy for admissions, course information, campus visits and guidance for your medical career journey.',
]);

$pageIds['bipc-landing'] = $addPage('best-bipc-college-in-hyderabad-neet-residential', 'Best BiPC College (NEET Residential)', [
    ['page_hero', [
        'kicker' => 'Residential BiPC + NEET · Hyderabad',
        'heading' => 'Best BiPC College in Hyderabad with NEET Coaching & Residential Campus',
        'lead' => 'Integrated BiPC + NEET under one roof, one faculty team, and one daily routine — without commuting between school and a separate coaching center.',
    ]],
], [
    'seo_title' => 'Best BiPC College (NEET Residential) - VR Doctors',
    'meta_description' => 'VR Doctors Academy is a top residential BiPC junior college in Hyderabad offering integrated NEET coaching, expert faculty & proven results. Book a visit today.',
]);

$pageIds['long'] = $addPage('long-term-neet-program-hyderabad', 'Long-Term NEET Program', [
    ['page_hero', [
        'kicker' => 'Long-Term NEET Program in Hyderabad | 8–10 Month',
        'heading' => 'NEET Long-Term Coaching in Hyderabad — Long-Term NEET Program (8–10 Months for Intermediate Completers)',
        'lead' => 'Have you completed Intermediate and are now preparing seriously for NEET? An intensive 8–10 month residential coaching program focused entirely on NEET.',
    ]],
], [
    'seo_title' => 'Long-Term NEET Coaching in Hyderabad | VR Doctors Academy',
    'meta_description' => 'VR Doctors Academy\'s Long-Term NEET Coaching in Hyderabad is an 8–10 month residential NEET course (Focus-45) in Hyderabad for Intermediate completers. Book a campus visit today.',
]);

$pageIds['short'] = $addPage('short-term-neet-program-hyderabad', 'Short-Term NEET Program', [
    ['page_hero', [
        'kicker' => 'Short-Term NEET Program in Hyderabad | 45–60 Day',
        'heading' => 'NEET Short-Term Coaching in Hyderabad — Short-Term NEET Program (45–60 Day Crash Course)',
        'lead' => 'A 45–60 day, fully residential crash course built for students who want to crack NEET in the next 2–3 months.',
    ]],
], [
    'seo_title' => 'Short-Term NEET Coaching in Hyderabad | VR Doctors Academy',
    'meta_description' => 'VR Doctors Academy\'s Short-Term NEET Coaching in Hyderabad is a 45–60 day residential NEET crash course in Hyderabad. Book a campus visit today.',
]);

$addPage('404', 'Page not found', [
    ['page_hero', ['kicker' => '404', 'heading' => 'Page not found', 'lead' => 'That address isn’t on this site.', 'cta_label' => 'Back to home', 'cta_url' => '/']],
], ['robots' => 'noindex,follow', 'seo_title' => 'Page not found | VR Doctors Academy']);

$headerId = Database::insert('menus', ['slug' => 'header', 'name' => 'Header']);
$footerId = Database::insert('menus', ['slug' => 'footer', 'name' => 'Footer']);
$mi = static function (int $menu, string $label, string $url, int $order, ?int $parent = null) {
    return Database::insert('menu_items', [
        'menu_id' => $menu,
        'parent_id' => $parent,
        'label' => $label,
        'url' => $url,
        'link_type' => 'custom',
        'sort_order' => $order,
        'is_active' => 1,
    ]);
};
$mi($headerId, 'Home', '/', 1);
$mi($headerId, 'World of Bipc', '/bipc-careers/', 2);
$mi($headerId, 'About', '/about/', 3);
$coursesNav = $mi($headerId, 'Courses', '/courses/', 4);
$mi($headerId, 'BiPC + NEET Residential', '/best-bipc-college-in-hyderabad-neet-residential/', 1, $coursesNav);
$mi($headerId, 'Long-Term NEET (2-Year)', '/long-term-neet-program-hyderabad/', 2, $coursesNav);
$mi($headerId, 'Short-Term NEET (1-Year)', '/short-term-neet-program-hyderabad/', 3, $coursesNav);
$mi($headerId, 'Results', '/results/', 5);
$mi($headerId, 'Contact', '/contact/', 6);
foreach ([['Home', '/', 1], ['World of BiPC', '/bipc-careers/', 2], ['About', '/about/', 3], ['Courses', '/courses/', 4], ['Results', '/results/', 5], ['Contact', '/contact/', 6]] as $item) {
    $mi($footerId, $item[0], $item[1], $item[2]);
}

foreach ([
    ['hello-world', '/', 'WordPress default post'],
    ['category/blog', '/', 'Empty blog category'],
    ['contact-us', '/contact/', 'Legacy contact slug'],
    ['about-neet.html', '/bipc-careers/', 'Legacy HTML about-NEET page'],
    ['our-methodology.html', '/courses/', 'Legacy methodology URL'],
] as $rd) {
    Database::insert('redirects', [
        'from_path' => $rd[0],
        'to_path' => $rd[1],
        'status_code' => 301,
        'is_active' => 1,
        'note' => $rd[2],
    ]);
}

Templates::ensureStarters();
Snippets::migrateFromSettings();
Cpt::ensureAiType();
Cpt::refreshAiLandingContent();

Database::insert('seo_metadata', [
    'entity_type' => 'post_type',
    'entity_id' => $coursesType,
    'seo_title' => 'NEET Coaching Courses in Hyderabad | VR Doctors Academy',
    'meta_description' => 'Explore NEET coaching courses in Hyderabad at VR Doctors Academy — Intermediate + NEET, Long-Term NEET, and Short-Term NEET Revision programs.',
    'canonical_url' => url('courses'),
    'robots' => 'index,follow',
]);
