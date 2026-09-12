<?php
/**
 * Page custom fields for all non-home pages (no ACF).
 * Schema-driven meta boxes: text, textarea, image, and simple item lists.
 *
 * @package VR_Doctors
 */

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Page slugs that get custom field meta boxes (not the static front page).
 *
 * @return array<string, string> slug => admin label
 */
function vr_editable_page_slugs() {
	return array(
		'about'                                              => __('About', 'vr-doctors'),
		'contact'                                            => __('Contact', 'vr-doctors'),
		'courses'                                            => __('Courses', 'vr-doctors'),
		'results'                                            => __('Results', 'vr-doctors'),
		'bipc-careers'                                       => __('World of BiPC', 'vr-doctors'),
		'best-bipc-college-in-hyderabad-neet-residential'    => __('BiPC NEET Residential', 'vr-doctors'),
		'long-term-neet-program-hyderabad'                   => __('Long-Term NEET', 'vr-doctors'),
		'short-term-neet-program-hyderabad'                  => __('Short-Term NEET', 'vr-doctors'),
	);
}

/**
 * Alternate permalink slugs that still map to a schema key.
 *
 * @return array<string, string[]> canonical => aliases
 */
function vr_schema_slug_aliases() {
	return array(
		'about'   => array('about', 'about-us', 'aboutus'),
		'contact' => array('contact', 'contact-us', 'contactus'),
		'courses' => array('courses', 'course', 'our-courses'),
		'results' => array('results', 'our-results'),
	);
}

/**
 * Page titles that still map to a schema key (case-insensitive).
 *
 * @return array<string, string[]> canonical => titles
 */
function vr_schema_slug_titles() {
	return array(
		'about'   => array('About', 'About Us'),
		'contact' => array('Contact', 'Contact Us'),
		'courses' => array('Courses', 'Our Courses'),
		'results' => array('Results', 'Our Results'),
	);
}

/**
 * Selectable page templates that map to a schema key.
 *
 * @return array<string, string> template file => canonical slug
 */
function vr_schema_slug_templates() {
	return array(
		'page-about.php'                                          => 'about',
		'page-contact.php'                                        => 'contact',
		'page-courses.php'                                        => 'courses',
		'page-results.php'                                        => 'results',
		'page-bipc-careers.php'                                   => 'bipc-careers',
		'page-best-bipc-college-in-hyderabad-neet-residential.php' => 'best-bipc-college-in-hyderabad-neet-residential',
		'page-long-term-neet-program-hyderabad.php'                => 'long-term-neet-program-hyderabad',
		'page-short-term-neet-program-hyderabad.php'               => 'short-term-neet-program-hyderabad',
	);
}

/**
 * Whether a page slug matches a schema key or one of its aliases.
 * Also matches WordPress duplicate suffixes (about-2, about-us-3).
 *
 * @param string   $name      post_name.
 * @param string   $canonical Schema slug.
 * @param string[] $aliases   Alternate slugs.
 * @return bool
 */
function vr_schema_name_matches($name, $canonical, $aliases = array()) {
	$name = (string) $name;
	if ($name === '' || $canonical === '') {
		return false;
	}
	$needles = array_values(array_unique(array_merge(array($canonical), is_array($aliases) ? $aliases : array())));
	foreach ($needles as $needle) {
		$needle = (string) $needle;
		if ($needle === '') {
			continue;
		}
		if ($name === $needle) {
			return true;
		}
		// Duplicate slugs (about-2, about-us-3). Skip short generic aliases like "course".
		$allow_suffix = ($needle === $canonical) || false !== strpos($needle, '-');
		if ($allow_suffix && preg_match('/^' . preg_quote($needle, '/') . '-\d+$/', $name)) {
			return true;
		}
	}
	return false;
}

/**
 * Field schemas per page slug.
 * Each section becomes one meta box. Field types: text, textarea, url, image, list (newline items), items (repeater rows).
 *
 * @return array
 */
function vr_page_meta_schemas() {
	static $schemas = null;
	if (null !== $schemas) {
		return $schemas;
	}

	$schemas = array(

		/* ───────────── About ───────────── */
		'about' => array(
			'hero' => array(
				'label'  => __('About — Hero', 'vr-doctors'),
				'fields' => array(
					'image'              => array('type' => 'image', 'label' => __('Background image', 'vr-doctors'), 'default' => 'Campus/infra-1.webp'),
					'image_alt'          => array('type' => 'text', 'label' => __('Image alt text', 'vr-doctors'), 'default' => 'VR Doctors Campus'),
					'eyebrow'            => array('type' => 'text', 'label' => __('Eyebrow', 'vr-doctors'), 'default' => 'About VR Doctors Academy'),
					'title'              => array('type' => 'textarea', 'label' => __('Heading (use line breaks)', 'vr-doctors'), 'default' => "Transforming Aspirations\nInto Medical Careers\nSince 2019"),
					'description'        => array('type' => 'textarea', 'label' => __('Description', 'vr-doctors'), 'default' => 'From a humble beginning with 95 students to a thriving academic community supporting 1000+ aspirants every year, VR Doctors Academy continues to help students turn their vision of becoming a doctor into reality.'),
					'primary_cta_text'   => array('type' => 'text', 'label' => __('Primary button text', 'vr-doctors'), 'default' => 'Explore Results'),
					'primary_cta_url'    => array('type' => 'text', 'label' => __('Primary button URL', 'vr-doctors'), 'default' => '/results/'),
					'secondary_cta_text' => array('type' => 'text', 'label' => __('Secondary button text', 'vr-doctors'), 'default' => 'Book an appointment'),
					'secondary_cta_url'  => array('type' => 'text', 'label' => __('Secondary button URL', 'vr-doctors'), 'default' => '/contact/'),
				),
			),
			'who_we_are' => array(
				'label'  => __('About — Who We Are', 'vr-doctors'),
				'fields' => array(
					'image_1'     => array('type' => 'image', 'label' => __('Image 1', 'vr-doctors'), 'default' => 'about/Set 1.webp'),
					'image_1_alt' => array('type' => 'text', 'label' => __('Image 1 alt', 'vr-doctors'), 'default' => 'VR Doctors Campus'),
					'image_2'     => array('type' => 'image', 'label' => __('Image 2', 'vr-doctors'), 'default' => 'about/Set 2.webp'),
					'image_2_alt' => array('type' => 'text', 'label' => __('Image 2 alt', 'vr-doctors'), 'default' => 'Students'),
					'image_3'     => array('type' => 'image', 'label' => __('Image 3', 'vr-doctors'), 'default' => 'about/Set 3.webp'),
					'image_3_alt' => array('type' => 'text', 'label' => __('Image 3 alt', 'vr-doctors'), 'default' => 'Faculty'),
					'image_4'     => array('type' => 'image', 'label' => __('Image 4', 'vr-doctors'), 'default' => 'about/Set 4.webp'),
					'image_4_alt' => array('type' => 'text', 'label' => __('Image 4 alt', 'vr-doctors'), 'default' => 'Academic Environment'),
					'eyebrow'     => array('type' => 'text', 'label' => __('Eyebrow', 'vr-doctors'), 'default' => 'Who We Are'),
					'title'       => array('type' => 'text', 'label' => __('Heading', 'vr-doctors'), 'default' => 'A Vision That Became Reality'),
					'paragraph_1' => array('type' => 'textarea', 'label' => __('Paragraph 1', 'vr-doctors'), 'default' => 'VR Doctors Academy was founded in 2019 with a simple vision — helping students transform their dream of becoming a doctor into reality through disciplined preparation, expert mentoring and a focused learning environment.'),
					'paragraph_2' => array('type' => 'textarea', 'label' => __('Paragraph 2', 'vr-doctors'), 'default' => 'What started with just 95 students has grown into a thriving academic ecosystem supporting more than 1000 students every year across Hyderabad. Through consistent results, residential learning and student-centric mentoring, VR Doctors Academy continues to guide aspiring medical professionals across India.'),
				),
			),
			'timeline' => array(
				'label'  => __('About — Journey / Year Tabs', 'vr-doctors'),
				'fields' => array(
					'eyebrow'     => array('type' => 'text', 'label' => __('Eyebrow', 'vr-doctors'), 'default' => 'Our Journey'),
					'title'       => array('type' => 'text', 'label' => __('Heading', 'vr-doctors'), 'default' => 'VR Doctors Story'),
					'description' => array('type' => 'textarea', 'label' => __('Description', 'vr-doctors'), 'default' => 'Every milestone represents our commitment to helping students achieve success in NEET and beyond.'),
					'items'       => array(
						'type'        => 'items',
						'label'       => __('Year tabs', 'vr-doctors'),
						'add_label'   => __('Add year tab', 'vr-doctors'),
						'row_label'   => __('Tab %d', 'vr-doctors'),
						'description' => __('Each row is one year tab on the About page. Click “Add year tab” to add a new milestone. Empty rows are ignored on the site.', 'vr-doctors'),
						'max'         => 16,
						'keys'        => array('year', 'title', 'content', 'image'),
						'types'       => array(
							'year'    => 'text',
							'title'   => 'text',
							'content' => 'textarea',
							'image'   => 'image',
						),
						'labels'      => array(
							__('Year (tab label)', 'vr-doctors'),
							__('Title', 'vr-doctors'),
							__('Description', 'vr-doctors'),
							__('Background image', 'vr-doctors'),
						),
						'default'     => function_exists('vr_default_about_timeline_items') ? vr_default_about_timeline_items() : array(),
					),
				),
			),
			'impact' => array(
				'label'  => __('About — Impact', 'vr-doctors'),
				'fields' => array(
					'eyebrow'     => array('type' => 'text', 'label' => __('Eyebrow', 'vr-doctors'), 'default' => 'Our Impact'),
					'title'       => array('type' => 'text', 'label' => __('Heading', 'vr-doctors'), 'default' => 'Beyond The Numbers'),
					'description' => array('type' => 'textarea', 'label' => __('Description', 'vr-doctors'), 'default' => "Every statistic tells a human story. Here's what these numbers really mean."),
					'quote'       => array('type' => 'text', 'label' => __('Footer quote', 'vr-doctors'), 'default' => '"Numbers tell stories. Stories inspire futures. We\'re proud of both."'),
					'items'       => array(
						'type'    => 'items',
						'label'   => __('Impact cards', 'vr-doctors'),
						'max'     => 8,
						'keys'    => array('stat', 'title', 'story'),
						'labels'  => array(__('Stat', 'vr-doctors'), __('Title', 'vr-doctors'), __('Story', 'vr-doctors')),
						'default' => array(
							array('stat' => '600+', 'title' => 'Medical Careers Guided', 'story' => 'Each number represents a student who achieved their dream. Families changed, futures transformed, and the healthcare field strengthened with every success.'),
							array('stat' => '150+', 'title' => 'Doctors Produced Every Year', 'story' => 'From first day to final NEET attempt, we walk alongside every student. Our residential environment creates a community of support and disciplined preparation.'),
							array('stat' => '6+', 'title' => 'Years of Excellence', 'story' => "Since 2019, we've built more than just results. We've created a legacy of trust, consistency, and excellence that parents and students rely on."),
							array('stat' => '100+', 'title' => 'Scholarships Awarded', 'story' => "Merit and dedication matter. We invest in deserving students, believing talent shouldn't be limited by finances. Education is for everyone."),
						),
					),
				),
			),
			'trust' => array(
				'label'  => __('About — Why Parents Trust Us', 'vr-doctors'),
				'fields' => array(
					'eyebrow'     => array('type' => 'text', 'label' => __('Eyebrow', 'vr-doctors'), 'default' => 'Why Parents Trust Us'),
					'title'       => array('type' => 'text', 'label' => __('Heading', 'vr-doctors'), 'default' => 'Building Confidence Through Results'),
					'description' => array('type' => 'textarea', 'label' => __('Description', 'vr-doctors'), 'default' => 'Trust is earned through consistency, discipline and a genuine commitment to student success.'),
					'items'       => array(
						'type'    => 'items',
						'label'   => __('Trust factors', 'vr-doctors'),
						'max'     => 9,
						'keys'    => array('title', 'description'),
						'labels'  => array(__('Title', 'vr-doctors'), __('Description', 'vr-doctors')),
						'default' => array(
							array('title' => 'Residential Learning Environment', 'description' => 'A focused residential ecosystem designed to minimize distractions and maximize academic growth.'),
							array('title' => 'Experienced Faculty', 'description' => 'Dedicated subject experts committed to helping students build strong concepts and confidence.'),
							array('title' => 'Personal Mentoring', 'description' => 'Regular guidance, performance reviews and individual support throughout the journey.'),
							array('title' => 'Scholarship Opportunities', 'description' => 'Supporting deserving students through merit-based scholarships every year.'),
							array('title' => 'Academic Discipline', 'description' => 'Structured schedules, supervised study hours and continuous evaluation systems.'),
							array('title' => 'Proven Results', 'description' => 'Consistent success stories and medical admissions across multiple batches.'),
						),
					),
				),
			),
			'faculty' => array(
				'label'  => __('About — Faculty Section', 'vr-doctors'),
				'fields' => array(
					'eyebrow'     => array('type' => 'text', 'label' => __('Eyebrow', 'vr-doctors'), 'default' => 'Meet Our Faculty'),
					'title'       => array('type' => 'text', 'label' => __('Heading', 'vr-doctors'), 'default' => 'Expert Educators, Personal Mentors'),
					'description' => array('type' => 'textarea', 'label' => __('Description', 'vr-doctors'), 'default' => 'Experienced educators dedicated to guiding students through every stage of their academic journey.'),
				),
			),
			'chairman' => array(
				'label'  => __('About — Founder\'s Note', 'vr-doctors'),
				'fields' => array(
					'image'       => array('type' => 'image', 'label' => __('Founder photo', 'vr-doctors'), 'default' => 'Chairman.webp'),
					'image_alt'   => array('type' => 'text', 'label' => __('Image alt', 'vr-doctors'), 'default' => "Founder's Note"),
					'eyebrow'     => array('type' => 'text', 'label' => __('Eyebrow', 'vr-doctors'), 'default' => "Founder's Note"),
					'title'       => array('type' => 'textarea', 'label' => __('Heading / quote', 'vr-doctors'), 'default' => 'విద్యా దదాతి వినయం వినయాద్ యాతి పాత్రతామ్'),
					'paragraph_1' => array('type' => 'textarea', 'label' => __('Paragraph 1', 'vr-doctors'), 'default' => 'ఈ రోజు పోటీ ప్రపంచంలో, విద్యను వ్యాపారంగా మార్చేసారు, ప్రతి విద్యార్థికి నాణ్యమైన విద్య అని నమ్మిన నేను, అందరికీ ప్రశాంతమైన వాతావరణంలో, ఉత్తమమైన విద్య, ఆరోగ్యమైన ఆహారం, ఉన్నతమైన ప్రమాణాలు కలిగిన ఉపాధ్యాయులతో శిక్షణ అందిస్తూ ప్రతి విద్యార్థిని వ్యక్తి నుంచి వ్యవస్థను తీర్చిదిద్దే పౌరుడిగా మార్చాలని సదుద్దేశంతో VR స్థాపించాను.'),
					'paragraph_2' => array('type' => 'textarea', 'label' => __('Paragraph 2', 'vr-doctors'), 'default' => 'మా VRకి రండి, మా అధ్యాపకులతో మాట్లాడండి, మా విద్యార్థులని కలవండి, మీ పిల్లల బంగారు భవిష్యత్తుపై నిర్ణయం మీరే తీసుకోండి. VR మిమ్మల్ని ఆహ్వానించడానికి సిద్ధంగా ఉంది. మీ పిల్లల బంగారు భవిష్యత్తు ఈ రోజు సరైన నిర్ణయంతో ప్రారంభమవుతుంది.'),
					'role_label'  => array('type' => 'text', 'label' => __('Role label', 'vr-doctors'), 'default' => 'Founder/Chairman'),
					'name'        => array('type' => 'text', 'label' => __('Name', 'vr-doctors'), 'default' => 'Jawan Ramesh'),
				),
			),
		),

		/* ───────────── Contact ───────────── */
		'contact' => array(
			'hero' => array(
				'label'  => __('Contact — Hero', 'vr-doctors'),
				'fields' => array(
					'image'              => array('type' => 'image', 'label' => __('Background image', 'vr-doctors'), 'default' => 'contact hero.webp'),
					'image_alt'          => array('type' => 'text', 'label' => __('Image alt', 'vr-doctors'), 'default' => 'Contact VR Doctors'),
					'eyebrow'            => array('type' => 'text', 'label' => __('Eyebrow', 'vr-doctors'), 'default' => 'Contact VR Doctors'),
					'title'              => array('type' => 'text', 'label' => __('Heading', 'vr-doctors'), 'default' => "Let's Plan Your Medical Journey"),
					'title_highlight'    => array('type' => 'text', 'label' => __('Highlighted words in heading', 'vr-doctors'), 'default' => 'Medical Journey'),
					'description'        => array('type' => 'textarea', 'label' => __('Description', 'vr-doctors'), 'default' => "Whether you're a student exploring BiPC, a parent seeking guidance, or someone preparing for NEET, our team is here to help you make the right decision."),
					'primary_cta_text'   => array('type' => 'text', 'label' => __('Primary button text', 'vr-doctors'), 'default' => '📞 Call Now'),
					'primary_cta_url'    => array('type' => 'text', 'label' => __('Primary button URL (tel:…)', 'vr-doctors'), 'default' => 'tel:+919256925640'),
					'secondary_cta_text' => array('type' => 'text', 'label' => __('Secondary button text', 'vr-doctors'), 'default' => '💬 WhatsApp Us'),
				),
			),
			'form' => array(
				'label'  => __('Contact — Enquiry Form Section', 'vr-doctors'),
				'fields' => array(
					'eyebrow'     => array('type' => 'text', 'label' => __('Eyebrow', 'vr-doctors'), 'default' => 'Enquiry Form'),
					'title'       => array('type' => 'text', 'label' => __('Heading', 'vr-doctors'), 'default' => 'Request A Callback'),
					'description' => array('type' => 'textarea', 'label' => __('Description', 'vr-doctors'), 'default' => 'Fill in your details and our admissions team will get in touch with you shortly.'),
				),
			),
			'campus' => array(
				'label'  => __('Contact — Campus Info', 'vr-doctors'),
				'fields' => array(
					'eyebrow'          => array('type' => 'text', 'label' => __('Eyebrow', 'vr-doctors'), 'default' => 'Visit Our Campus'),
					'title'            => array('type' => 'text', 'label' => __('Heading', 'vr-doctors'), 'default' => 'Connect With VR Doctors'),
					'description'      => array('type' => 'textarea', 'label' => __('Description', 'vr-doctors'), 'default' => 'Visit our campus, speak with our admissions team, and experience the learning environment firsthand.'),
					'location_text'    => array('type' => 'text', 'label' => __('Location text', 'vr-doctors'), 'default' => 'Hyderabad, Telangana'),
					'map_embed_url'    => array('type' => 'textarea', 'label' => __('Google Maps embed URL (iframe src)', 'vr-doctors'), 'default' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d7610.530788602768!2d78.36287829999999!3d17.4948401!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3bcb937ee93c897b%3A0x8e7c4b7dc00d672a!2sVR%20Doctors%20Academy!5e0!3m2!1sen!2sin!4v1781867913509!5m2!1sen!2sin'),
				),
			),
			'cta' => array(
				'label'  => __('Contact — Bottom CTA', 'vr-doctors'),
				'fields' => array(
					'eyebrow'            => array('type' => 'text', 'label' => __('Eyebrow', 'vr-doctors'), 'default' => 'Admissions Open'),
					'title'              => array('type' => 'text', 'label' => __('Heading', 'vr-doctors'), 'default' => 'Ready To Begin Your Medical Journey?'),
					'title_highlight'    => array('type' => 'text', 'label' => __('Highlighted words', 'vr-doctors'), 'default' => 'Medical Journey?'),
					'description'        => array('type' => 'textarea', 'label' => __('Description', 'vr-doctors'), 'default' => "Whether you're aiming for NEET, exploring the world of BiPC, or looking for the right residential academic environment, our team is ready to guide you every step of the way."),
					'primary_cta_text'   => array('type' => 'text', 'label' => __('Primary button text', 'vr-doctors'), 'default' => '📞 Call Admissions'),
					'primary_cta_url'    => array('type' => 'text', 'label' => __('Primary button URL', 'vr-doctors'), 'default' => 'tel:+919256925640'),
					'secondary_cta_text' => array('type' => 'text', 'label' => __('Secondary button text', 'vr-doctors'), 'default' => '💬 WhatsApp Now'),
					'card_1_title'       => array('type' => 'text', 'label' => __('Card 1 title', 'vr-doctors'), 'default' => 'Residential'),
					'card_1_text'        => array('type' => 'text', 'label' => __('Card 1 text', 'vr-doctors'), 'default' => 'Campus Environment'),
					'card_2_title'       => array('type' => 'text', 'label' => __('Card 2 title', 'vr-doctors'), 'default' => 'NEET'),
					'card_2_text'        => array('type' => 'text', 'label' => __('Card 2 text', 'vr-doctors'), 'default' => 'Focused Programs'),
					'card_3_title'       => array('type' => 'text', 'label' => __('Card 3 title', 'vr-doctors'), 'default' => 'BiPC'),
					'card_3_text'        => array('type' => 'text', 'label' => __('Card 3 text', 'vr-doctors'), 'default' => 'Specialized Courses'),
				),
			),
		),

		/* ───────────── Courses ───────────── */
		'courses' => array(
			'hero' => array(
				'label'  => __('Courses — Hero', 'vr-doctors'),
				'fields' => array(
					'image'       => array('type' => 'image', 'label' => __('Background image', 'vr-doctors'), 'default' => 'course hero.webp'),
					'image_alt'   => array('type' => 'text', 'label' => __('Image alt', 'vr-doctors'), 'default' => 'Programs and Courses'),
					'eyebrow'     => array('type' => 'text', 'label' => __('Eyebrow', 'vr-doctors'), 'default' => 'Courses At VR Doctors'),
					'title'       => array('type' => 'textarea', 'label' => __('Heading', 'vr-doctors'), 'default' => "Programs Designed For\nFuture Doctors"),
					'description' => array('type' => 'textarea', 'label' => __('Description', 'vr-doctors'), 'default' => 'Whether you are beginning your NEET journey, preparing for a dedicated long-term program or looking for focused revision before the exam, VR Doctors offers structured pathways designed for medical aspirants.'),
				),
			),
			'intermediate' => array(
				'label'  => __('Courses — Intermediate + NEET', 'vr-doctors'),
				'fields' => array(
					'image'       => array('type' => 'image', 'label' => __('Section image', 'vr-doctors'), 'default' => 'Campus/academics-1.webp'),
					'image_alt'   => array('type' => 'text', 'label' => __('Image alt', 'vr-doctors'), 'default' => 'Intermediate + NEET'),
					'eyebrow'     => array('type' => 'text', 'label' => __('Eyebrow', 'vr-doctors'), 'default' => 'Intermediate + NEET'),
					'title'       => array('type' => 'text', 'label' => __('Heading', 'vr-doctors'), 'default' => 'Balanced Academics & Medical Prep'),
					'description' => array('type' => 'textarea', 'label' => __('Description', 'vr-doctors'), 'default' => 'Complete your Intermediate education while building a strong foundation for NEET. Our integrated curriculum ensures you excel in board exams and competitive entrance tests.'),
					'features'    => array('type' => 'list', 'label' => __('Features (one per line)', 'vr-doctors'), 'default' => "Intermediate Academics + NEET Coaching\nResidential Campus Environment\nStructured Daily Study Plan\nRegular Assessments & Analysis\nPersonal Faculty Mentoring"),
					'cta_text'    => array('type' => 'text', 'label' => __('Button text', 'vr-doctors'), 'default' => 'Enroll In Intermediate + NEET →'),
					'cta_url'     => array('type' => 'text', 'label' => __('Button URL', 'vr-doctors'), 'default' => '/contact/'),
				),
			),
			'long_term' => array(
				'label'  => __('Courses — Long-Term NEET', 'vr-doctors'),
				'fields' => array(
					'image'       => array('type' => 'image', 'label' => __('Section image', 'vr-doctors'), 'default' => 'Campus/academics-3.webp'),
					'image_alt'   => array('type' => 'text', 'label' => __('Image alt', 'vr-doctors'), 'default' => 'Long Term NEET Program'),
					'eyebrow'     => array('type' => 'text', 'label' => __('Eyebrow', 'vr-doctors'), 'default' => 'Long-Term NEET Program'),
					'title'       => array('type' => 'text', 'label' => __('Heading', 'vr-doctors'), 'default' => 'Complete Medical Preparation'),
					'description' => array('type' => 'textarea', 'label' => __('Description', 'vr-doctors'), 'default' => 'Our flagship program combines concept mastery, intensive practice, regular testing and personalized mentoring. Perfect for committed students aiming for top medical colleges.'),
					'features'    => array('type' => 'list', 'label' => __('Features (one per line)', 'vr-doctors'), 'default' => "Complete NEET Syllabus Coverage\nConcept Building & Application\nWeekly Tests & Grand Tests\nPerformance Tracking & Analysis\nPersonalized Academic Support"),
					'cta_text'    => array('type' => 'text', 'label' => __('Button text', 'vr-doctors'), 'default' => 'Start Your NEET Journey →'),
					'cta_url'     => array('type' => 'text', 'label' => __('Button URL', 'vr-doctors'), 'default' => '/contact/'),
				),
			),
			'short_term' => array(
				'label'  => __('Courses — Short-Term Revision', 'vr-doctors'),
				'fields' => array(
					'image'       => array('type' => 'image', 'label' => __('Section image', 'vr-doctors'), 'default' => 'Campus/academics-2.webp'),
					'image_alt'   => array('type' => 'text', 'label' => __('Image alt', 'vr-doctors'), 'default' => 'Short Term NEET Revision'),
					'eyebrow'     => array('type' => 'text', 'label' => __('Eyebrow', 'vr-doctors'), 'default' => 'Short-Term Revision Program'),
					'title'       => array('type' => 'text', 'label' => __('Heading', 'vr-doctors'), 'default' => 'Last-Minute NEET Mastery'),
					'description' => array('type' => 'textarea', 'label' => __('Description', 'vr-doctors'), 'default' => 'Perfect for students who need focused revision and exam strategy before the NEET examination. This intensive program emphasizes high-yield concepts, mock exams and confidence-building.'),
					'features'    => array('type' => 'list', 'label' => __('Features (one per line)', 'vr-doctors'), 'default' => "Fast-Track Concept Revision\nIntensive Mock Exams\nHigh-Yield Question Practice\nExam Strategy Sessions\nFinal Week Preparation Support"),
					'cta_text'    => array('type' => 'text', 'label' => __('Button text', 'vr-doctors'), 'default' => 'Join Revision Program →'),
					'cta_url'     => array('type' => 'text', 'label' => __('Button URL', 'vr-doctors'), 'default' => '/contact/'),
				),
			),
			'selector' => array(
				'label'  => __('Courses — Choose Your Program', 'vr-doctors'),
				'fields' => array(
					'eyebrow'     => array('type' => 'text', 'label' => __('Eyebrow', 'vr-doctors'), 'default' => 'Find Your Path'),
					'title'       => array('type' => 'text', 'label' => __('Heading', 'vr-doctors'), 'default' => 'Choose Your Program'),
					'description' => array('type' => 'textarea', 'label' => __('Description', 'vr-doctors'), 'default' => 'Every student is at a different stage. Select the program that matches your goals.'),
					'items'       => array(
						'type'    => 'items',
						'label'   => __('Program cards', 'vr-doctors'),
						'max'     => 6,
						'keys'    => array('icon', 'title', 'description', 'tag'),
						'labels'  => array(__('Icon class (e.g. fa-graduation-cap)', 'vr-doctors'), __('Title', 'vr-doctors'), __('Description', 'vr-doctors'), __('Tag', 'vr-doctors')),
						'default' => array(
							array('icon' => 'fa-graduation-cap', 'title' => 'Intermediate + NEET', 'description' => 'Begin your Intermediate education while preparing systematically for NEET.', 'tag' => 'For Class 10 Passed Students'),
							array('icon' => 'fa-book-open', 'title' => 'Long-Term NEET', 'description' => 'Comprehensive residential program focused on achieving medical seat admission.', 'tag' => 'For Serious Medical Aspirants'),
							array('icon' => 'fa-bolt', 'title' => 'Revision Program', 'description' => 'Intensive preparation focused on revision, testing and exam readiness.', 'tag' => 'For Final Year Preparation'),
						),
					),
				),
			),
			'journey' => array(
				'label'  => __('Courses — Student Journey', 'vr-doctors'),
				'fields' => array(
					'eyebrow'     => array('type' => 'text', 'label' => __('Eyebrow', 'vr-doctors'), 'default' => 'Your Journey At VR Doctors'),
					'title'       => array('type' => 'text', 'label' => __('Heading', 'vr-doctors'), 'default' => 'From Admission To Success'),
					'description' => array('type' => 'textarea', 'label' => __('Description', 'vr-doctors'), 'default' => 'Structured pathway designed to maximize learning, confidence and NEET performance.'),
					'items'       => array(
						'type'    => 'items',
						'label'   => __('Journey steps', 'vr-doctors'),
						'max'     => 8,
						'keys'    => array('number', 'title', 'description', 'icon'),
						'labels'  => array(__('Number', 'vr-doctors'), __('Title', 'vr-doctors'), __('Description', 'vr-doctors'), __('Icon class', 'vr-doctors')),
						'default' => array(
							array('number' => '01', 'title' => 'Join VR Doctors', 'description' => 'Begin your journey by choosing the program that best matches your academic goals and medical aspirations.', 'icon' => 'fa-rocket'),
							array('number' => '02', 'title' => 'Personalized Study Plan', 'description' => 'Students follow a structured academic roadmap designed to ensure consistent progress throughout the year.', 'icon' => 'fa-book-open'),
							array('number' => '03', 'title' => 'Faculty Mentoring', 'description' => 'Experienced faculty provide regular guidance, doubt clarification and motivation to keep students on track.', 'icon' => 'fa-graduation-cap'),
							array('number' => '04', 'title' => 'Weekly Tests', 'description' => 'Frequent assessments help students measure their understanding and improve exam readiness.', 'icon' => 'fa-clipboard-list'),
							array('number' => '05', 'title' => 'Performance Review', 'description' => 'Detailed analysis helps identify strengths, improve weak areas and refine preparation strategies.', 'icon' => 'fa-chart-column'),
							array('number' => '06', 'title' => 'NEET Success', 'description' => 'The result of disciplined preparation, expert guidance and consistent effort is success in NEET and admission into top medical colleges.', 'icon' => 'fa-trophy'),
						),
					),
				),
			),
		),

		/* ───────────── Results ───────────── */
		'results' => array(
			'hero' => array(
				'label'  => __('Results — Hero', 'vr-doctors'),
				'fields' => array(
					'image'              => array('type' => 'image', 'label' => __('Background image', 'vr-doctors'), 'default' => 'results hero.webp'),
					'image_alt'          => array('type' => 'text', 'label' => __('Image alt', 'vr-doctors'), 'default' => 'Results and Achievements'),
					'eyebrow'            => array('type' => 'text', 'label' => __('Eyebrow', 'vr-doctors'), 'default' => 'Results That Matter'),
					'title'              => array('type' => 'textarea', 'label' => __('Heading', 'vr-doctors'), 'default' => "600+ Medical Careers\nGuided Since 2019"),
					'description'        => array('type' => 'textarea', 'label' => __('Description', 'vr-doctors'), 'default' => 'Every result represents a student, a dream and years of disciplined preparation. Our commitment to excellence has helped hundreds of students begin their medical journey.'),
					'primary_cta_text'   => array('type' => 'text', 'label' => __('Primary button text', 'vr-doctors'), 'default' => 'View Top Achievers'),
					'primary_cta_url'    => array('type' => 'text', 'label' => __('Primary button URL', 'vr-doctors'), 'default' => '#achievers'),
					'secondary_cta_text' => array('type' => 'text', 'label' => __('Secondary button text', 'vr-doctors'), 'default' => 'Contact Admissions'),
					'secondary_cta_url'  => array('type' => 'text', 'label' => __('Secondary button URL', 'vr-doctors'), 'default' => '/contact/'),
				),
			),
			'stats' => array(
				'label'  => __('Results — Stats', 'vr-doctors'),
				'fields' => array(
					'highlight' => array('type' => 'textarea', 'label' => __('Achievement highlight note', 'vr-doctors'), 'default' => 'These numbers represent 2025 results. Every statistic reflects the hard work, dedication, and guidance our students received.'),
					'items'     => array(
						'type'    => 'items',
						'label'   => __('Stat cards', 'vr-doctors'),
						'max'     => 6,
						'keys'    => array('number', 'label', 'icon', 'context'),
						'labels'  => array(__('Number', 'vr-doctors'), __('Label', 'vr-doctors'), __('Icon class', 'vr-doctors'), __('Context', 'vr-doctors')),
						'default' => array(
							array('number' => '101+', 'label' => 'Medical Seats in 2025', 'icon' => 'fa-graduation-cap', 'context' => 'Current year admissions'),
							array('number' => '50%', 'label' => 'Success Rate Every Year', 'icon' => 'fa-star', 'context' => 'Students achieving seats'),
							array('number' => 'AIR 206', 'label' => 'Top Rank', 'icon' => 'fa-trophy', 'context' => 'NEET All India Rankings'),
						),
					),
				),
			),
			'admissions' => array(
				'label'  => __('Results — Medical Admissions Gallery', 'vr-doctors'),
				'fields' => array(
					'eyebrow'     => array('type' => 'text', 'label' => __('Eyebrow', 'vr-doctors'), 'default' => 'Medical Admissions'),
					'title'       => array('type' => 'text', 'label' => __('Heading', 'vr-doctors'), 'default' => 'Top Medical Colleges'),
					'description' => array('type' => 'textarea', 'label' => __('Description', 'vr-doctors'), 'default' => 'Our students have secured admissions into respected medical institutions across Telangana and India.'),
					'image_1'     => array('type' => 'image', 'label' => __('Gallery image 1', 'vr-doctors'), 'default' => 'medical admissions/Universities-07.webp'),
					'image_2'     => array('type' => 'image', 'label' => __('Gallery image 2', 'vr-doctors'), 'default' => 'medical admissions/Universities-02.webp'),
					'image_3'     => array('type' => 'image', 'label' => __('Gallery image 3', 'vr-doctors'), 'default' => 'medical admissions/Universities-03.webp'),
					'image_4'     => array('type' => 'image', 'label' => __('Gallery image 4', 'vr-doctors'), 'default' => 'medical admissions/Universities-04.webp'),
					'image_5'     => array('type' => 'image', 'label' => __('Gallery image 5', 'vr-doctors'), 'default' => 'medical admissions/Universities-01.webp'),
					'image_6'     => array('type' => 'image', 'label' => __('Gallery image 6', 'vr-doctors'), 'default' => 'medical admissions/Universities-06.webp'),
				),
			),
			'achievers' => array(
				'label'  => __('Results — Top Achievers Section', 'vr-doctors'),
				'fields' => array(
					'eyebrow'     => array('type' => 'text', 'label' => __('Eyebrow', 'vr-doctors'), 'default' => 'Hall Of Fame'),
					'title'       => array('type' => 'text', 'label' => __('Heading', 'vr-doctors'), 'default' => 'Meet Our Top Achievers'),
					'description' => array('type' => 'textarea', 'label' => __('Description', 'vr-doctors'), 'default' => 'Students who transformed dedication into medical college admissions.'),
				),
			),
			'stories' => array(
				'label'  => __('Results — Success Stories', 'vr-doctors'),
				'fields' => array(
					'eyebrow'     => array('type' => 'text', 'label' => __('Eyebrow', 'vr-doctors'), 'default' => 'Success Stories'),
					'title'       => array('type' => 'text', 'label' => __('Heading', 'vr-doctors'), 'default' => 'Real Stories, Real Results'),
					'description' => array('type' => 'textarea', 'label' => __('Description', 'vr-doctors'), 'default' => 'Hear from students and parents who trusted VR Doctors Academy on their journey toward medical careers.'),
					'cta_text'    => array('type' => 'text', 'label' => __('YouTube button text', 'vr-doctors'), 'default' => 'Watch More Stories on YouTube →'),
					'items'       => array(
						'type'    => 'items',
						'label'   => __('Videos', 'vr-doctors'),
						'max'     => 8,
						'keys'    => array('title', 'description', 'embedId'),
						'labels'  => array(__('Title', 'vr-doctors'), __('Description', 'vr-doctors'), __('YouTube video ID', 'vr-doctors')),
						'default' => array(
							array('title' => 'Parent Testimonial', 'description' => 'A parent shares their experience and trust in VR Doctors Academy.', 'embedId' => '7IiUv26FzAQ'),
							array('title' => 'Parent Success Story', 'description' => 'Feedback from a parent whose child achieved medical admission.', 'embedId' => 'pVV7zBMnM8g'),
							array('title' => 'Student Testimonial', 'description' => 'A student shares their preparation journey and success story.', 'embedId' => 'pWwbn-vnsRE'),
							array('title' => 'Student Success Story', 'description' => 'From aspiration to medical college admission.', 'embedId' => 'gPJySylrNyQ'),
						),
					),
				),
			),
		),

		/* ───────────── BiPC Careers ───────────── */
		'bipc-careers' => array(
			'hero' => array(
				'label'  => __('BiPC — Hero', 'vr-doctors'),
				'fields' => array(
					'image'       => array('type' => 'image', 'label' => __('Background image', 'vr-doctors'), 'default' => 'bipc_hero.webp'),
					'image_alt'   => array('type' => 'text', 'label' => __('Image alt', 'vr-doctors'), 'default' => 'BiPC Program'),
					'eyebrow'     => array('type' => 'text', 'label' => __('Eyebrow', 'vr-doctors'), 'default' => 'World Of BiPC'),
					'title'       => array('type' => 'textarea', 'label' => __('Heading', 'vr-doctors'), 'default' => "One Decision.\nA Lifetime Of Impact."),
					'description' => array('type' => 'textarea', 'label' => __('Description', 'vr-doctors'), 'default' => 'Most students choose BiPC with a dream of becoming doctors. Few realize that BiPC opens the door to an entire world of healthcare, science, research and service — where every path has the power to improve lives.'),
				),
			),
			'what_is' => array(
				'label'  => __('BiPC — What Is BiPC', 'vr-doctors'),
				'fields' => array(
					'image'       => array('type' => 'image', 'label' => __('Section image', 'vr-doctors'), 'default' => 'bipc-intro.webp'),
					'image_alt'   => array('type' => 'text', 'label' => __('Image alt', 'vr-doctors'), 'default' => 'World of BiPC'),
					'eyebrow'     => array('type' => 'text', 'label' => __('Eyebrow', 'vr-doctors'), 'default' => 'What Is BiPC?'),
					'title'       => array('type' => 'text', 'label' => __('Heading', 'vr-doctors'), 'default' => 'More Than Just Biology, Physics & Chemistry'),
					'paragraph_1' => array('type' => 'textarea', 'label' => __('Paragraph 1', 'vr-doctors'), 'default' => 'BiPC is often viewed as the pathway to becoming a doctor. In reality, it opens doors to a vast ecosystem of careers focused on improving human life, animal welfare, healthcare innovation and scientific discovery.'),
					'paragraph_2' => array('type' => 'textarea', 'label' => __('Paragraph 2', 'vr-doctors'), 'default' => 'Students who choose BiPC are stepping into a world of medicine, pharmacy, veterinary sciences, public health, biotechnology, agriculture and many other fields that contribute to society in meaningful ways.'),
				),
			),
			'ecosystem' => array(
				'label'  => __('BiPC — Ecosystem Section', 'vr-doctors'),
				'fields' => array(
					'eyebrow'     => array('type' => 'text', 'label' => __('Eyebrow', 'vr-doctors'), 'default' => 'Healthcare Ecosystem'),
					'title'       => array('type' => 'text', 'label' => __('Heading', 'vr-doctors'), 'default' => 'Explore Careers In BiPC'),
					'description' => array('type' => 'textarea', 'label' => __('Description', 'vr-doctors'), 'default' => 'BiPC opens doors to diverse healthcare and life sciences careers beyond just medicine.'),
				),
			),
			'reality' => array(
				'label'  => __('BiPC — Reality Check', 'vr-doctors'),
				'fields' => array(
					'eyebrow'     => array('type' => 'text', 'label' => __('Eyebrow', 'vr-doctors'), 'default' => 'Reality Check'),
					'title'       => array('type' => 'text', 'label' => __('Heading', 'vr-doctors'), 'default' => 'Every Family Should Understand This'),
					'paragraph_1' => array('type' => 'textarea', 'label' => __('Paragraph 1', 'vr-doctors'), 'default' => 'Every year, thousands of students choose BiPC with a dream of becoming doctors. While MBBS remains one of the most respected professions, healthcare is much larger than a single degree or entrance examination.'),
					'paragraph_2' => array('type' => 'textarea', 'label' => __('Paragraph 2', 'vr-doctors'), 'default' => 'Students who understand the healthcare ecosystem early are able to make informed decisions, discover opportunities and build meaningful careers aligned with their interests and strengths.'),
					'stat_1'      => array('type' => 'text', 'label' => __('Card 1 number', 'vr-doctors'), 'default' => '1'),
					'stat_1_title'=> array('type' => 'text', 'label' => __('Card 1 title', 'vr-doctors'), 'default' => 'One Stream'),
					'stat_1_text' => array('type' => 'text', 'label' => __('Card 1 text', 'vr-doctors'), 'default' => 'BiPC is a gateway to an entire world of healthcare careers.'),
					'stat_2'      => array('type' => 'text', 'label' => __('Card 2 number', 'vr-doctors'), 'default' => '100+'),
					'stat_2_title'=> array('type' => 'text', 'label' => __('Card 2 title', 'vr-doctors'), 'default' => 'Career Opportunities'),
					'stat_2_text' => array('type' => 'text', 'label' => __('Card 2 text', 'vr-doctors'), 'default' => 'Medicine, pharmacy, veterinary sciences, agriculture, biotechnology and more.'),
					'stat_3'      => array('type' => 'text', 'label' => __('Card 3 number', 'vr-doctors'), 'default' => '1 Goal'),
					'stat_3_title'=> array('type' => 'text', 'label' => __('Card 3 title', 'vr-doctors'), 'default' => 'Serve Living Beings'),
					'stat_3_text' => array('type' => 'text', 'label' => __('Card 3 text', 'vr-doctors'), 'default' => 'Different paths. Same mission of improving lives.'),
				),
			),
			'why_guides' => array(
				'label'  => __('BiPC — Why VR Doctors Guides', 'vr-doctors'),
				'fields' => array(
					'eyebrow'     => array('type' => 'text', 'label' => __('Eyebrow', 'vr-doctors'), 'default' => 'Our Philosophy'),
					'title'       => array('type' => 'text', 'label' => __('Heading', 'vr-doctors'), 'default' => 'Every Student Deserves Clarity'),
					'paragraph_1' => array('type' => 'textarea', 'label' => __('Paragraph 1', 'vr-doctors'), 'default' => 'At VR Doctors, we believe healthcare is much bigger than a single examination or a single profession. Every student deserves the knowledge and guidance needed to understand the opportunities available through BiPC.'),
					'paragraph_2' => array('type' => 'textarea', 'label' => __('Paragraph 2', 'vr-doctors'), 'default' => 'Whether a student becomes a doctor, dentist, veterinarian, pharmacist or researcher, our goal remains the same: helping young minds build meaningful careers that serve people, animals and society.'),
				),
			),
		),

	);

	if (function_exists('vr_neet_landing_page_schemas')) {
		$schemas = array_merge($schemas, vr_neet_landing_page_schemas());
	}

	return $schemas;
}

/* ─────────────────────────────────────────────
 * Core helpers
 * ───────────────────────────────────────────── */

/**
 * Resolve page ID for a known slug.
 *
 * @param string $slug Page slug.
 * @return int
 */
function vr_page_id_by_slug($slug) {
	$slug = sanitize_title((string) $slug);
	if ($slug === '') {
		return 0;
	}

	$candidates = array($slug);
	$aliases    = vr_schema_slug_aliases();
	if (!empty($aliases[ $slug ]) && is_array($aliases[ $slug ])) {
		$candidates = array_values(array_unique(array_merge($candidates, $aliases[ $slug ])));
	}

	foreach ($candidates as $path) {
		$page = get_page_by_path($path);
		if ($page) {
			return (int) $page->ID;
		}
	}

	$templates = vr_schema_slug_templates();
	$template  = array_search($slug, $templates, true);
	if (is_string($template) && $template !== '') {
		$found = get_posts(
			array(
				'post_type'              => 'page',
				'post_status'            => 'publish',
				'posts_per_page'         => 1,
				'fields'                 => 'ids',
				'meta_key'               => '_wp_page_template',
				'meta_value'             => $template,
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
			)
		);
		if (!empty($found)) {
			return (int) $found[0];
		}
	}

	$titles = vr_schema_slug_titles();
	if (!empty($titles[ $slug ]) && is_array($titles[ $slug ])) {
		foreach ($titles[ $slug ] as $title) {
			$found = get_posts(
				array(
					'post_type'              => 'page',
					'post_status'            => 'publish',
					'posts_per_page'         => 1,
					'fields'                 => 'ids',
					'title'                  => $title,
					'no_found_rows'          => true,
					'update_post_meta_cache' => false,
					'update_post_term_cache' => false,
				)
			);
			if (!empty($found)) {
				return (int) $found[0];
			}
		}
	}

	return 0;
}

/**
 * Whether the given post is an editable themed page (not homepage).
 *
 * @param int|WP_Post|null $post        Post.
 * @param bool             $match_title Also match by page title (admin). Off for front-end routing so a generic “About Us” page is not forced into the marketing layout.
 * @return string|false Matched slug or false.
 */
function vr_get_editable_page_slug($post = null, $match_title = true) {
	$post = get_post($post);
	if (!$post || 'page' !== $post->post_type) {
		return false;
	}
	// Homepage uses home-meta.php exclusively.
	if (function_exists('vr_is_home_edit_screen') && vr_is_home_edit_screen($post)) {
		return false;
	}

	$name  = (string) $post->post_name;
	$slugs = vr_editable_page_slugs();
	if ($name !== '' && isset($slugs[ $name ])) {
		return $name;
	}

	$aliases = vr_schema_slug_aliases();
	foreach ($slugs as $canonical => $_label) {
		$canon_aliases = $aliases[ $canonical ] ?? array();
		if (vr_schema_name_matches($name, $canonical, $canon_aliases)) {
			return $canonical;
		}
	}

	$template = get_page_template_slug($post);
	$map      = vr_schema_slug_templates();
	if (is_string($template) && $template !== '' && isset($map[ $template ])) {
		return $map[ $template ];
	}

	if ($match_title) {
		$title = strtolower(trim(wp_strip_all_tags((string) $post->post_title)));
		if ($title !== '') {
			foreach (vr_schema_slug_titles() as $canonical => $titles) {
				foreach ($titles as $candidate) {
					if ($title === strtolower($candidate)) {
						return $canonical;
					}
				}
			}
		}
	}

	return false;
}

/**
 * Raw section meta for a page ID.
 *
 * @param int    $page_id Page ID.
 * @param string $section Section key.
 * @return array
 */
function vr_page_meta_raw($page_id, $section) {
	$raw = get_post_meta((int) $page_id, '_vr_page_' . $section, true);
	return is_array($raw) ? $raw : array();
}

/**
 * Resolve image field to full URL.
 *
 * @param array  $meta     Section meta.
 * @param string $key      Field key (stores image_id as {key}_id in meta).
 * @param string $fallback Theme image path or full URL.
 * @return string
 */
function vr_page_resolve_image($meta, $key, $fallback = '') {
	$id_key = $key . '_id';
	$id     = isset($meta[ $id_key ]) ? absint($meta[ $id_key ]) : 0;
	if ($id > 0) {
		$url = wp_get_attachment_image_url($id, 'full');
		if ($url) {
			return $url;
		}
	}
	// Also accept legacy plain image_id under key if stored that way.
	if (isset($meta[ $key ]) && is_int($meta[ $key ]) && $meta[ $key ] > 0) {
		$url = wp_get_attachment_image_url($meta[ $key ], 'full');
		if ($url) {
			return $url;
		}
	}
	$fallback = (string) $fallback;
	if ($fallback === '') {
		return '';
	}
	if (preg_match('#^https?://#i', $fallback)) {
		return $fallback;
	}
	return vr_img($fallback);
}

/**
 * Non-empty string or default.
 *
 * @param mixed  $value   Value.
 * @param string $default Default.
 * @return string
 */
function vr_page_str($value, $default = '') {
	if (!is_string($value) && !is_numeric($value)) {
		return $default;
	}
	$value = trim((string) $value);
	return $value !== '' ? $value : $default;
}

/**
 * Parse list field (newline-separated) into array.
 *
 * @param mixed $value   Saved string or array.
 * @param array $default Default lines.
 * @return array
 */
function vr_page_list($value, $default = array()) {
	if (is_array($value)) {
		$lines = array_values(array_filter(array_map('trim', $value)));
		return !empty($lines) ? $lines : $default;
	}
	if (is_string($value) && trim($value) !== '') {
		$lines = preg_split('/\r\n|\r|\n/', $value);
		$lines = array_values(array_filter(array_map('trim', $lines)));
		return !empty($lines) ? $lines : $default;
	}
	return $default;
}

/**
 * Field type for a repeater column.
 *
 * @param array  $field Item field schema.
 * @param string $key   Column key.
 * @return string
 */
function vr_page_item_key_type($field, $key) {
	$types = $field['types'] ?? array();
	if (isset($types[ $key ]) && is_string($types[ $key ])) {
		return $types[ $key ];
	}
	if ('image' === $key) {
		return 'image';
	}
	return 'text';
}

/**
 * Whether a repeater column should render as a textarea.
 *
 * @param array  $field Item field schema.
 * @param string $key   Column key.
 * @param string $value Current value.
 * @return bool
 */
function vr_page_item_is_long($field, $key, $value = '') {
	$type = vr_page_item_key_type($field, $key);
	if ('textarea' === $type) {
		return true;
	}
	if ('image' === $type) {
		return false;
	}
	return in_array($key, array('description', 'story', 'paragraph', 'content'), true) || strlen((string) $value) > 120;
}

/**
 * Attachment ID stored on a repeater row for an image column.
 *
 * @param array  $row Row.
 * @param string $key Column key.
 * @return int
 */
function vr_page_row_image_id($row, $key) {
	if (!is_array($row)) {
		return 0;
	}
	if (isset($row[ $key . '_id' ]) && $row[ $key . '_id' ] !== '' && $row[ $key . '_id' ] !== null) {
		return absint($row[ $key . '_id' ]);
	}
	if (isset($row[ $key ]) && is_array($row[ $key ]) && isset($row[ $key ]['image_id'])) {
		return absint($row[ $key ]['image_id']);
	}
	// Integer attachment IDs only — never treat year-like strings ("2019") as IDs.
	if (isset($row[ $key ]) && is_int($row[ $key ])) {
		return absint($row[ $key ]);
	}
	return 0;
}

/**
 * Resolve saved repeater rows (text + optional images) against schema defaults.
 *
 * @param array $saved        Saved rows.
 * @param array $field        Item field schema.
 * @param bool  $use_defaults When saved is empty, return schema defaults (true) or [].
 * @return array
 */
function vr_page_resolve_items($saved, $field, $use_defaults = true) {
	$keys      = $field['keys'] ?? array();
	$def_items = is_array($field['default'] ?? null) ? $field['default'] : array();
	$saved     = is_array($saved) ? $saved : array();
	$has_year  = in_array('year', $keys, true);

	$defaults_by_year = array();
	foreach ($def_items as $d) {
		if (is_array($d) && isset($d['year']) && (string) $d['year'] !== '') {
			$defaults_by_year[ (string) $d['year'] ] = $d;
		}
	}

	$items = array();
	foreach ($saved as $i => $row) {
		if (!is_array($row)) {
			continue;
		}
		$item    = array();
		$has_any = false;
		$year    = isset($row['year']) && !is_array($row['year']) ? trim((string) $row['year']) : '';

		foreach ($keys as $k) {
			$ktype = vr_page_item_key_type($field, $k);
			if ('image' === $ktype) {
				$id      = vr_page_row_image_id($row, $k);
				$def_img = '';
				if ($has_year && $year !== '' && isset($defaults_by_year[ $year ][ $k ]) && is_string($defaults_by_year[ $year ][ $k ])) {
					$def_img = $defaults_by_year[ $year ][ $k ];
				} elseif (!$has_year && isset($def_items[ $i ][ $k ]) && is_string($def_items[ $i ][ $k ])) {
					$def_img = $def_items[ $i ][ $k ];
				}
				$item[ $k ]         = vr_page_resolve_image(array( $k . '_id' => $id ), $k, $def_img);
				$item[ $k . '_id' ] = $id;
				if ($id > 0) {
					$has_any = true;
				}
				continue;
			}

			$val        = isset($row[ $k ]) && !is_array($row[ $k ]) ? trim((string) $row[ $k ]) : '';
			$item[ $k ] = $val;
			if ($val !== '') {
				$has_any = true;
			}
		}

		if (!$has_any) {
			continue;
		}

		// Fill blank text from defaults: year-keyed rows only match the same year
		// (a new "2027" tab must not inherit the 2019 title from index 0).
		$def_row = null;
		if ($has_year) {
			if ($year !== '' && isset($defaults_by_year[ $year ])) {
				$def_row = $defaults_by_year[ $year ];
			}
		} elseif (isset($def_items[ $i ]) && is_array($def_items[ $i ])) {
			$def_row = $def_items[ $i ];
		}
		if (is_array($def_row)) {
			foreach ($keys as $k) {
				if ('image' === vr_page_item_key_type($field, $k)) {
					continue;
				}
				if (($item[ $k ] ?? '') === '' && isset($def_row[ $k ])) {
					$item[ $k ] = (string) $def_row[ $k ];
				}
			}
		}

		$items[] = $item;
	}

	if (!empty($items)) {
		return $items;
	}

	if (!$use_defaults) {
		return array();
	}

	$resolved = array();
	foreach ($def_items as $row) {
		if (!is_array($row)) {
			continue;
		}
		$item = array();
		foreach ($keys as $k) {
			$ktype = vr_page_item_key_type($field, $k);
			if ('image' === $ktype) {
				$def_img            = isset($row[ $k ]) && is_string($row[ $k ]) ? $row[ $k ] : '';
				$item[ $k ]         = vr_page_resolve_image(array(), $k, $def_img);
				$item[ $k . '_id' ] = 0;
			} else {
				$item[ $k ] = isset($row[ $k ]) ? (string) $row[ $k ] : '';
			}
		}
		$resolved[] = $item;
	}

	return $resolved;
}

/**
 * Get fully resolved section data for a page slug.
 *
 * @param string $slug    Page slug.
 * @param string $section Section key.
 * @return array
 */
function vr_get_page_section($slug, $section) {
	$schemas = vr_page_meta_schemas();
	if (empty($schemas[ $slug ][ $section ]['fields'])) {
		return array();
	}

	$fields  = $schemas[ $slug ][ $section ]['fields'];
	$page_id = vr_page_id_by_slug($slug);
	// On singular page, prefer current post if it maps to this schema.
	if (is_singular('page')) {
		$current = get_queried_object();
		if ($current && vr_get_editable_page_slug($current) === $slug) {
			$page_id = (int) $current->ID;
		}
	}
	$meta = $page_id ? vr_page_meta_raw($page_id, $section) : array();
	$out  = array();

	foreach ($fields as $key => $field) {
		$type    = $field['type'] ?? 'text';
		$default = $field['default'] ?? '';

		if ('image' === $type) {
			$out[ $key ] = vr_page_resolve_image($meta, $key, is_string($default) ? $default : '');
			continue;
		}

		if ('list' === $type) {
			$def_list = is_string($default)
				? array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $default))))
				: (array) $default;
			if (array_key_exists($key, $meta)) {
				$out[ $key ] = vr_page_list($meta[ $key ], array());
			} else {
				$out[ $key ] = vr_page_list('', $def_list);
			}
			continue;
		}

		if ('items' === $type) {
			$use_defaults = !array_key_exists($key, $meta);
			$out[ $key ]  = vr_page_resolve_items(
				is_array($meta[ $key ] ?? null) ? $meta[ $key ] : array(),
				$field,
				$use_defaults
			);
			continue;
		}

		// text / textarea / url
		$out[ $key ] = vr_page_str($meta[ $key ] ?? '', is_string($default) || is_numeric($default) ? (string) $default : '');
	}

	return $out;
}

/**
 * Journey tabs for the About page.
 * Uses About-page custom fields when saved; otherwise Timeline CPT, then theme defaults.
 *
 * @return array
 */
function vr_get_about_timeline_items() {
	$page_id = 0;
	if (is_singular('page')) {
		$current = get_queried_object();
		if ($current && vr_get_editable_page_slug($current) === 'about') {
			$page_id = (int) $current->ID;
		}
	}
	if (!$page_id) {
		$page_id = vr_page_id_by_slug('about');
	}

	$raw = $page_id ? vr_page_meta_raw($page_id, 'timeline') : array();
	// Distinguish "never saved" (use CPT / theme defaults) from "saved empty" (show no tabs).
	if (array_key_exists('items', $raw) && is_array($raw['items'])) {
		$schemas = vr_page_meta_schemas();
		$field   = $schemas['about']['timeline']['fields']['items'] ?? array();
		$resolved = vr_page_resolve_items($raw['items'], $field, false);
		$out      = array();
		foreach ($resolved as $row) {
			$year  = trim((string) ($row['year'] ?? ''));
			$title = trim((string) ($row['title'] ?? ''));
			if ($year === '' && $title === '') {
				continue;
			}
			$out[] = array(
				'title'   => $title,
				'content' => (string) ($row['content'] ?? ''),
				'image'   => (string) ($row['image'] ?? ''),
				'meta'    => array(
					'year' => $year !== '' ? $year : $title,
				),
			);
		}
		return $out;
	}

	$cpt = vr_get_ordered_posts('timeline_event', array());
	if (!empty($cpt)) {
		return $cpt;
	}

	return vr_fallback_timeline();
}

/**
 * Convert multi-line title to HTML with <br />.
 *
 * @param string $title Title with newlines.
 * @return string Escaped HTML.
 */
function vr_page_title_html($title) {
	$parts = preg_split('/\r\n|\r|\n/', (string) $title);
	$parts = array_map('esc_html', array_map('trim', $parts));
	$parts = array_filter($parts, static function ($p) {
		return $p !== '';
	});
	return implode('<br />', $parts);
}

/**
 * Highlight a substring in a title with orange span (escaped).
 *
 * @param string $title     Full title.
 * @param string $highlight Substring to wrap.
 * @return string HTML.
 */
function vr_page_title_highlight_html($title, $highlight) {
	$title     = (string) $title;
	$highlight = (string) $highlight;
	if ($highlight === '' || false === mb_strpos($title, $highlight)) {
		return esc_html($title);
	}
	$pos    = mb_strpos($title, $highlight);
	$before = mb_substr($title, 0, $pos);
	$after  = mb_substr($title, $pos + mb_strlen($highlight));
	return esc_html($before) . '<span class="text-orange-400">' . esc_html($highlight) . '</span>' . esc_html($after);
}

/* ─────────────────────────────────────────────
 * Admin
 * ───────────────────────────────────────────── */

function vr_doctors_add_page_meta_boxes() {
	global $post;
	$post_id = 0;
	if ($post) {
		$post_id = (int) $post->ID;
	} elseif (isset($_GET['post'])) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$post_id = (int) $_GET['post'];
	}
	if (!$post_id) {
		return;
	}
	$slug = vr_get_editable_page_slug($post_id);
	if (!$slug) {
		return;
	}
	$schemas = vr_page_meta_schemas();
	if (empty($schemas[ $slug ])) {
		return;
	}
	foreach ($schemas[ $slug ] as $section => $cfg) {
		add_meta_box(
			'vr_page_' . $section,
			$cfg['label'] ?? $section,
			'vr_page_meta_box_render',
			'page',
			'normal',
			'high',
			array(
				'slug'                               => $slug,
				'section'                            => $section,
				'__block_editor_compatible_meta_box' => true,
				'__back_compat_meta_box'             => false,
			)
		);
	}
}
add_action('add_meta_boxes_page', 'vr_doctors_add_page_meta_boxes');

/**
 * Themed landing pages are edited via meta boxes, not Gutenberg.
 *
 * @param bool    $use  Whether to use the block editor.
 * @param WP_Post $post Post.
 * @return bool
 */
function vr_doctors_use_classic_editor($use, $post) {
	if (!$post instanceof WP_Post) {
		$post = get_post($post);
	}
	if (!$post || 'page' !== $post->post_type) {
		return $use;
	}
	if (function_exists('vr_is_home_edit_screen') && vr_is_home_edit_screen($post)) {
		return false;
	}
	if (vr_get_editable_page_slug($post)) {
		return false;
	}
	return $use;
}
add_filter('use_block_editor_for_post', 'vr_doctors_use_classic_editor', 100, 2);

/**
 * Shared nonce for page meta.
 */
function vr_page_meta_nonce() {
	static $printed = false;
	if ($printed) {
		return;
	}
	$printed = true;
	wp_nonce_field('vr_save_page_meta', 'vr_page_meta_nonce');
}

function vr_doctors_page_nonce_field() {
	global $post;
	if (!$post || !vr_get_editable_page_slug($post)) {
		return;
	}
	vr_page_meta_nonce();
}
add_action('edit_form_after_title', 'vr_doctors_page_nonce_field');

/**
 * Point editors at the section meta boxes (easy to miss below the content editor).
 */
function vr_doctors_page_editor_help() {
	global $post;
	$slug = $post ? vr_get_editable_page_slug($post) : false;
	if (!$slug) {
		return;
	}
	$labels = vr_editable_page_slugs();
	$label  = $labels[ $slug ] ?? $slug;
	echo '<div class="notice notice-info inline" style="margin:12px 0 8px"><p>';
	echo '<strong>' . esc_html(sprintf(/* translators: page label */ __('%s page content is edited in the boxes below this editor.', 'vr-doctors'), $label)) . '</strong> ';
	if ('about' === $slug) {
		echo esc_html__('Open “About — Journey / Year Tabs” to change years or click “Add year tab” for a new milestone.', 'vr-doctors');
		$tpl  = get_page_template_slug($post);
		$name = (string) $post->post_name;
		if ('page-about.php' !== $tpl && !vr_schema_name_matches($name, 'about', vr_schema_slug_aliases()['about'] ?? array())) {
			echo ' ' . esc_html__('To show the About layout on the site, set Template to “About” (Page Attributes) or use slug about / about-us.', 'vr-doctors');
		}
	} elseif (in_array($slug, vr_neet_landing_page_slugs(), true)) {
		echo esc_html__('Each section has its own meta box below. Leave a field empty to use the theme default. SEO, table column headers, facility icons, and the hero Contact Form 7 shortcode can all be edited from their respective sections.', 'vr-doctors');
	} else {
		echo esc_html__('Leave a field empty to keep the theme default.', 'vr-doctors');
	}
	echo '</p></div>';
}
add_action('edit_form_after_title', 'vr_doctors_page_editor_help', 5);

/**
 * Admin assets for page meta (reuse homepage picker CSS/JS).
 *
 * @param string $hook Hook.
 */
function vr_doctors_page_admin_assets($hook) {
	if (!in_array($hook, array('post.php', 'post-new.php'), true)) {
		return;
	}
	$screen = get_current_screen();
	if (!$screen || 'page' !== $screen->post_type) {
		return;
	}
	global $post;
	$post_obj = $post;
	if (!$post_obj && isset($_GET['post'])) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$post_obj = get_post((int) $_GET['post']);
	}
	if (!$post_obj || !vr_get_editable_page_slug($post_obj)) {
		return;
	}

	wp_enqueue_media();
	wp_enqueue_style(
		'vr-home-admin',
		VR_DOCTORS_URI . '/assets/css/home-admin.css',
		array(),
		VR_DOCTORS_VERSION
	);
	wp_enqueue_script(
		'vr-home-admin',
		VR_DOCTORS_URI . '/assets/js/home-admin.js',
		array('jquery'),
		VR_DOCTORS_VERSION,
		true
	);
	wp_localize_script(
		'vr-home-admin',
		'vrHomeAdmin',
		array(
			'maxSlides'   => 10,
			'maxStats'    => 10,
			'maxCampus'   => 10,
			'maxApproach' => 10,
			'i18n'        => array(
				'selectImage' => __('Select image', 'vr-doctors'),
				'useImage'    => __('Use this image', 'vr-doctors'),
				'remove'      => __('Remove', 'vr-doctors'),
			),
		)
	);
}
add_action('admin_enqueue_scripts', 'vr_doctors_page_admin_assets');

/**
 * Render one column of a repeater row (text, textarea, or image picker).
 *
 * @param string     $name  Input name prefix (e.g. vr_page_timeline).
 * @param string     $key   Repeater key (e.g. items).
 * @param int|string $index Row index or __i__ placeholder.
 * @param string     $k     Column key.
 * @param array      $field Repeater field schema.
 * @param array      $row   Saved/default row.
 */
function vr_page_render_item_column($name, $key, $index, $k, $field, $row) {
	$keys   = $field['keys'] ?? array();
	$labels = $field['labels'] ?? $keys;
	$ki     = array_search($k, $keys, true);
	$flabel = (false !== $ki && isset($labels[ $ki ])) ? $labels[ $ki ] : $k;
	$ktype  = vr_page_item_key_type($field, $k);
	$index  = (string) $index;
	$row    = is_array($row) ? $row : array();

	if ('image' === $ktype) {
		$id = vr_page_row_image_id($row, $k);
		if (function_exists('vr_home_field_image')) {
			vr_home_field_image($name . '[' . $key . '][' . $index . '][' . $k . ']', $id, $flabel);
		} else {
			printf(
				'<p class="vr-field"><label><strong>%1$s</strong><br /><input type="number" class="widefat" name="%2$s[%3$s][%4$s][%5$s][image_id]" value="%6$d" /></label></p>',
				esc_html($flabel),
				esc_attr($name),
				esc_attr($key),
				esc_attr($index),
				esc_attr($k),
				$id
			);
		}
		return;
	}

	$fval = isset($row[ $k ]) && !is_array($row[ $k ]) ? (string) $row[ $k ] : '';
	if (vr_page_item_is_long($field, $k, $fval)) {
		printf(
			'<p class="vr-field"><label><strong>%1$s</strong><br /><textarea class="widefat" rows="2" name="%2$s[%3$s][%4$s][%5$s]">%6$s</textarea></label></p>',
			esc_html($flabel),
			esc_attr($name),
			esc_attr($key),
			esc_attr($index),
			esc_attr($k),
			esc_textarea($fval)
		);
		return;
	}

	printf(
		'<p class="vr-field"><label><strong>%1$s</strong><br /><input type="text" class="widefat" name="%2$s[%3$s][%4$s][%5$s]" value="%6$s" /></label></p>',
		esc_html($flabel),
		esc_attr($name),
		esc_attr($key),
		esc_attr($index),
		esc_attr($k),
		esc_attr($fval)
	);
}

/**
 * Render a page section meta box.
 *
 * @param WP_Post $post Post.
 * @param array   $box  Box args.
 */
function vr_page_meta_box_render($post, $box) {
	$slug    = $box['args']['slug'] ?? '';
	$section = $box['args']['section'] ?? '';
	$schemas = vr_page_meta_schemas();
	if (empty($schemas[ $slug ][ $section ]['fields'])) {
		return;
	}
	vr_page_meta_nonce();

	$fields = $schemas[ $slug ][ $section ]['fields'];
	$saved  = vr_page_meta_raw($post->ID, $section);
	$name   = 'vr_page_' . $section;

	echo '<div class="vr-home-meta">';
	echo '<p class="description">' . esc_html__('Leave a field empty to keep the theme default. Images: use Select image from the Media Library.', 'vr-doctors') . '</p>';

	foreach ($fields as $key => $field) {
		$type  = $field['type'] ?? 'text';
		$label = $field['label'] ?? $key;
		$def   = $field['default'] ?? '';

		if ('image' === $type) {
			$id = isset($saved[ $key . '_id' ]) ? absint($saved[ $key . '_id' ]) : 0;
			if (function_exists('vr_home_field_image')) {
				vr_home_field_image($name . '[' . $key . ']', $id, $label);
			} else {
				printf(
					'<p class="vr-field"><label><strong>%1$s</strong><br /><input type="number" class="widefat" name="%2$s[%3$s_id]" value="%4$d" placeholder="Attachment ID" /></label></p>',
					esc_html($label),
					esc_attr($name),
					esc_attr($key),
					$id
				);
			}
			if (is_string($def) && $def !== '') {
				echo '<p class="description">' . esc_html(sprintf(/* translators: theme image path */ __('Default theme image: %s', 'vr-doctors'), $def)) . '</p>';
			}
			continue;
		}

		if ('list' === $type) {
			$val = '';
			if (isset($saved[ $key ])) {
				$val = is_array($saved[ $key ]) ? implode("\n", $saved[ $key ]) : (string) $saved[ $key ];
			} elseif (is_string($def)) {
				$val = $def;
			}
			printf(
				'<p class="vr-field"><label><strong>%1$s</strong><br /><textarea class="widefat" rows="5" name="%2$s[%3$s]">%4$s</textarea></label></p>',
				esc_html($label),
				esc_attr($name),
				esc_attr($key),
				esc_textarea($val)
			);
			continue;
		}

		if ('items' === $type) {
			$keys      = $field['keys'] ?? array();
			$max       = isset($field['max']) ? (int) $field['max'] : 10;
			$add_label = $field['add_label'] ?? __('Add item', 'vr-doctors');
			$row_label = $field['row_label'] ?? __('Item %d', 'vr-doctors');
			$rows      = !empty($saved[ $key ]) && is_array($saved[ $key ]) ? $saved[ $key ] : (is_array($def) ? $def : array());
			if (empty($rows)) {
				$rows = array(array_fill_keys($keys, ''));
			}
			echo '<h4>' . esc_html($label) . '</h4>';
			if (!empty($field['description'])) {
				echo '<p class="description">' . esc_html($field['description']) . '</p>';
			}
			echo '<div data-vr-repeater data-vr-max="' . esc_attr((string) $max) . '">';
			echo '<div data-vr-rows>';
			foreach ($rows as $i => $row) {
				$row = is_array($row) ? $row : array();
				echo '<div class="vr-repeater-row" data-vr-row>';
				echo '<div class="vr-row-header"><strong>' . esc_html(sprintf($row_label, $i + 1)) . '</strong>';
				echo ' <button type="button" class="button-link-delete" data-vr-remove-row>' . esc_html__('Remove', 'vr-doctors') . '</button></div>';
				foreach ($keys as $k) {
					vr_page_render_item_column($name, $key, (int) $i, $k, $field, $row);
				}
				echo '</div>';
			}
			echo '</div>';
			echo '<p><button type="button" class="button" data-vr-add-row>' . esc_html($add_label) . '</button></p>';
			echo '<template data-vr-template><div class="vr-repeater-row" data-vr-row>';
			echo '<div class="vr-row-header"><strong>' . esc_html(sprintf($row_label, 0)) . '</strong>';
			echo ' <button type="button" class="button-link-delete" data-vr-remove-row>' . esc_html__('Remove', 'vr-doctors') . '</button></div>';
			foreach ($keys as $k) {
				vr_page_render_item_column($name, $key, '__i__', $k, $field, array());
			}
			echo '</div></template>';
			echo '</div>';
			continue;
		}

		// text / textarea / url
		$val = isset($saved[ $key ]) ? (string) $saved[ $key ] : (is_string($def) ? $def : '');
		if ('textarea' === $type) {
			printf(
				'<p class="vr-field"><label><strong>%1$s</strong><br /><textarea class="widefat" rows="4" name="%2$s[%3$s]">%4$s</textarea></label></p>',
				esc_html($label),
				esc_attr($name),
				esc_attr($key),
				esc_textarea($val)
			);
		} else {
			printf(
				'<p class="vr-field"><label><strong>%1$s</strong><br /><input type="text" class="widefat" name="%2$s[%3$s]" value="%4$s" /></label></p>',
				esc_html($label),
				esc_attr($name),
				esc_attr($key),
				esc_attr($val)
			);
		}
	}

	echo '</div>';
}

/**
 * Sanitize and save page meta.
 *
 * @param int $post_id Post ID.
 */
function vr_doctors_save_page_meta($post_id) {
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return;
	}
	if (wp_is_post_revision($post_id) || wp_is_post_autosave($post_id)) {
		return;
	}
	if (!isset($_POST['vr_page_meta_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['vr_page_meta_nonce'])), 'vr_save_page_meta')) {
		return;
	}
	if (!current_user_can('edit_page', $post_id)) {
		return;
	}
	if ('page' !== get_post_type($post_id)) {
		return;
	}

	$slug = vr_get_editable_page_slug($post_id);
	if (!$slug) {
		return;
	}

	$schemas = vr_page_meta_schemas();
	if (empty($schemas[ $slug ])) {
		return;
	}

	$url_like = array('cta_url', 'primary_cta_url', 'secondary_cta_url', 'phone_url', 'map_embed_url', 'link_url', 'canonical_url');

	foreach ($schemas[ $slug ] as $section => $cfg) {
		$post_key = 'vr_page_' . $section;
		if (!isset($_POST[ $post_key ]) || !is_array($_POST[ $post_key ])) {
			continue;
		}
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$raw    = wp_unslash($_POST[ $post_key ]);
		$fields = $cfg['fields'] ?? array();
		$clean  = array();

		foreach ($fields as $key => $field) {
			$type = $field['type'] ?? 'text';

			if ('image' === $type) {
				// Image picker stores name[key][image_id].
				$id = 0;
				if (isset($raw[ $key ]['image_id'])) {
					$id = absint($raw[ $key ]['image_id']);
				} elseif (isset($raw[ $key . '_id' ])) {
					$id = absint($raw[ $key . '_id' ]);
				}
				$clean[ $key . '_id' ] = $id;
				continue;
			}

			if ('list' === $type) {
				$val = isset($raw[ $key ]) ? (string) $raw[ $key ] : '';
				$clean[ $key ] = sanitize_textarea_field($val);
				continue;
			}

			if ('items' === $type) {
				if (!array_key_exists($key, $raw)) {
					$prev = vr_page_meta_raw($post_id, $section);
					$clean[ $key ] = (isset($prev[ $key ]) && is_array($prev[ $key ])) ? $prev[ $key ] : array();
					continue;
				}
				$keys  = $field['keys'] ?? array();
				$max   = isset($field['max']) ? (int) $field['max'] : 10;
				if ($max < 1) {
					$max = 1;
				}
				$items = array();
				if (is_array($raw[ $key ])) {
					foreach (array_slice($raw[ $key ], 0, $max) as $row_key => $row) {
						if (is_string($row_key) && false !== strpos($row_key, '__')) {
							continue;
						}
						if (!is_array($row)) {
							continue;
						}
						$item    = array();
						$has_any = false;
						foreach ($keys as $k) {
							$ktype = vr_page_item_key_type($field, $k);
							if ('image' === $ktype) {
								$id = 0;
								if (isset($row[ $k ]['image_id'])) {
									$id = absint($row[ $k ]['image_id']);
								} elseif (isset($row[ $k . '_id' ])) {
									$id = absint($row[ $k . '_id' ]);
								}
								$item[ $k . '_id' ] = $id;
								if ($id > 0) {
									$has_any = true;
								}
								continue;
							}
							$v = isset($row[ $k ]) && !is_array($row[ $k ]) ? (string) $row[ $k ] : '';
							if ('icon' === $k) {
								$item[ $k ] = vr_sanitize_fa_icon($v);
							} elseif ('textarea' === $ktype || in_array($k, array('description', 'story', 'content', 'paragraph', 'answer', 'quote', 'value', 'text'), true)) {
								$item[ $k ] = sanitize_textarea_field($v);
							} else {
								$item[ $k ] = sanitize_text_field($v);
							}
							if (trim($item[ $k ]) !== '') {
								$has_any = true;
							}
						}
						if ($has_any) {
							$items[] = $item;
						}
					}
				}
				$clean[ $key ] = $items;
				continue;
			}

			$val = isset($raw[ $key ]) ? (string) $raw[ $key ] : '';
			if ('form_cf7_shortcode' === $key) {
				$val = trim(sanitize_textarea_field($val));
				$clean[ $key ] = ($val !== '' && function_exists('vr_safe_cf7_shortcode'))
					? vr_safe_cf7_shortcode($val, '')
					: '';
			} elseif ('textarea' === $type) {
				$clean[ $key ] = sanitize_textarea_field($val);
			} elseif (in_array($key, $url_like, true) || 'url' === $type) {
				$val = trim($val);
				if ($val === '') {
					$clean[ $key ] = '';
				} elseif (str_starts_with($val, '#') || str_starts_with($val, 'tel:') || str_starts_with($val, 'mailto:') || str_starts_with($val, '/')) {
					$clean[ $key ] = sanitize_text_field($val);
				} else {
					$san = esc_url_raw($val);
					$clean[ $key ] = $san ? $san : sanitize_text_field($val);
				}
			} else {
				$clean[ $key ] = sanitize_text_field($val);
			}
		}

		update_post_meta($post_id, '_vr_page_' . $section, $clean);
	}
}
add_action('save_post_page', 'vr_doctors_save_page_meta');
