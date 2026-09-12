<?php
/**
 * Meta box schemas for NEET landing pages (Short-Term, Long-Term, BiPC Residential).
 *
 * @package VR_Doctors
 */

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Shared hero fields for NEET program landing pages.
 *
 * @param array<string, string> $defaults Field defaults.
 * @return array
 */
function vr_neet_hero_fields($defaults) {
	return array(
		'image'              => array('type' => 'image', 'label' => __('Background image', 'vr-doctors'), 'default' => $defaults['image'] ?? 'course hero.webp'),
		'image_alt'          => array('type' => 'text', 'label' => __('Image alt', 'vr-doctors'), 'default' => $defaults['image_alt'] ?? ''),
		'eyebrow'            => array('type' => 'text', 'label' => __('Eyebrow', 'vr-doctors'), 'default' => $defaults['eyebrow'] ?? ''),
		'title'              => array('type' => 'textarea', 'label' => __('Heading', 'vr-doctors'), 'default' => $defaults['title'] ?? ''),
		'paragraph_1'        => array('type' => 'textarea', 'label' => __('Paragraph 1', 'vr-doctors'), 'default' => $defaults['paragraph_1'] ?? ''),
		'paragraph_2'        => array('type' => 'textarea', 'label' => __('Paragraph 2', 'vr-doctors'), 'default' => $defaults['paragraph_2'] ?? ''),
		'primary_cta_text'   => array('type' => 'text', 'label' => __('Mobile primary CTA', 'vr-doctors'), 'default' => 'Book a Campus Visit'),
		'secondary_cta_text' => array('type' => 'text', 'label' => __('Secondary CTA', 'vr-doctors'), 'default' => 'Call Admissions'),
		'secondary_cta_url'  => array('type' => 'text', 'label' => __('Secondary CTA URL', 'vr-doctors'), 'default' => 'tel:+917097098877'),
		'phone_display'      => array('type' => 'text', 'label' => __('Phone display', 'vr-doctors'), 'default' => $defaults['phone_display'] ?? '+91-7097098877 / +91-7097098811'),
		'phone_url'          => array('type' => 'text', 'label' => __('Phone URL', 'vr-doctors'), 'default' => 'tel:+917097098877'),
		'email'              => array('type' => 'text', 'label' => __('Email', 'vr-doctors'), 'default' => 'info@vrdoctorsacademy.com'),
		'form_eyebrow'       => array('type' => 'text', 'label' => __('Form eyebrow', 'vr-doctors'), 'default' => 'Admission Enquiry'),
		'form_title'         => array('type' => 'text', 'label' => __('Form title', 'vr-doctors'), 'default' => 'Book a Campus Visit'),
		'form_description'   => array('type' => 'text', 'label' => __('Form description', 'vr-doctors'), 'default' => 'Share your details and our counsellors will call you back.'),
		'form_cf7_shortcode' => array('type' => 'textarea', 'label' => __('Contact Form 7 shortcode (optional)', 'vr-doctors'), 'default' => '', 'description' => __('Leave empty to use the site-wide hero form from Settings → VR Doctors.', 'vr-doctors')),
	);
}

/**
 * SEO fields for NEET landing pages.
 *
 * @param array<string, string> $defaults Default SEO copy.
 * @return array
 */
function vr_neet_seo_fields($defaults) {
	return array(
		'meta_title'       => array('type' => 'text', 'label' => __('Browser title (meta title)', 'vr-doctors'), 'default' => $defaults['meta_title'] ?? ''),
		'meta_description' => array('type' => 'textarea', 'label' => __('Meta description', 'vr-doctors'), 'default' => $defaults['meta_description'] ?? ''),
		'og_title'         => array('type' => 'text', 'label' => __('Open Graph / social title', 'vr-doctors'), 'default' => $defaults['og_title'] ?? ''),
		'og_description'   => array('type' => 'textarea', 'label' => __('Open Graph / social description', 'vr-doctors'), 'default' => $defaults['og_description'] ?? ''),
		'canonical_url'    => array('type' => 'text', 'label' => __('Canonical URL (optional)', 'vr-doctors'), 'default' => ''),
	);
}

/**
 * Facility bullets with editable icons (BiPC facilities section).
 *
 * @param array<int, array{icon: string, text: string}> $items Default rows.
 * @return array
 */
function vr_neet_facility_items_field($items) {
	return array(
		'type'      => 'items',
		'label'     => __('Facility items', 'vr-doctors'),
		'add_label' => __('Add facility', 'vr-doctors'),
		'row_label' => __('Facility %d', 'vr-doctors'),
		'max'       => 16,
		'keys'      => array('icon', 'text'),
		'labels'    => array(
			__('Icon class (e.g. fa-building)', 'vr-doctors'),
			__('Facility text', 'vr-doctors'),
		),
		'default'   => $items,
	);
}

/**
 * Overview table repeater field config.
 *
 * @param array<int, array{label: string, value: string}> $rows Default rows.
 * @return array
 */
function vr_neet_overview_items_field($rows) {
	return array(
		'type'        => 'items',
		'label'       => __('Program detail rows', 'vr-doctors'),
		'add_label'   => __('Add row', 'vr-doctors'),
		'row_label'   => __('Row %d', 'vr-doctors'),
		'description' => __('Each row is one line in the program overview table.', 'vr-doctors'),
		'max'         => 20,
		'keys'        => array('label', 'value'),
		'types'       => array('label' => 'text', 'value' => 'textarea'),
		'labels'      => array(__('Detail label', 'vr-doctors'), __('Information', 'vr-doctors')),
		'default'     => $rows,
	);
}

/**
 * FAQ repeater field config.
 *
 * @param array<int, array{question: string, answer: string}> $items Default FAQs.
 * @return array
 */
function vr_neet_faq_items_field($items) {
	return array(
		'type'      => 'items',
		'label'     => __('FAQ items', 'vr-doctors'),
		'add_label' => __('Add FAQ', 'vr-doctors'),
		'row_label' => __('FAQ %d', 'vr-doctors'),
		'max'       => 30,
		'keys'      => array('question', 'answer'),
		'types'     => array('question' => 'text', 'answer' => 'textarea'),
		'labels'    => array(__('Question', 'vr-doctors'), __('Answer', 'vr-doctors')),
		'default'   => $items,
	);
}

/**
 * Testimonials repeater field config.
 *
 * @param array<int, array<string, string>> $items Default testimonials.
 * @return array
 */
function vr_neet_testimonial_items_field($items) {
	return array(
		'type'      => 'items',
		'label'     => __('Testimonials', 'vr-doctors'),
		'add_label' => __('Add testimonial', 'vr-doctors'),
		'row_label' => __('Testimonial %d', 'vr-doctors'),
		'max'       => 8,
		'keys'      => array('image', 'name', 'college', 'quote'),
		'types'     => array(
			'image'   => 'image',
			'name'    => 'text',
			'college' => 'text',
			'quote'   => 'textarea',
		),
		'labels'    => array(
			__('Photo', 'vr-doctors'),
			__('Student name', 'vr-doctors'),
			__('College / affiliation', 'vr-doctors'),
			__('Quote', 'vr-doctors'),
		),
		'default'   => $items,
	);
}

/**
 * Track record stat cards repeater.
 *
 * @param array<int, array<string, string>> $items Default stats.
 * @return array
 */
function vr_neet_track_record_items_field($items) {
	return array(
		'type'      => 'items',
		'label'     => __('Stat cards', 'vr-doctors'),
		'add_label' => __('Add stat', 'vr-doctors'),
		'row_label' => __('Stat %d', 'vr-doctors'),
		'max'       => 6,
		'keys'      => array('icon', 'stat', 'text'),
		'labels'    => array(
			__('Icon class (e.g. fa-percent)', 'vr-doctors'),
			__('Stat headline', 'vr-doctors'),
			__('Description', 'vr-doctors'),
		),
		'default'   => $items,
	);
}

/**
 * Admission steps repeater.
 *
 * @param array<int, array<string, string>> $items Default steps.
 * @return array
 */
function vr_neet_admission_items_field($items) {
	return array(
		'type'      => 'items',
		'label'     => __('Admission steps', 'vr-doctors'),
		'add_label' => __('Add step', 'vr-doctors'),
		'row_label' => __('Step %d', 'vr-doctors'),
		'max'       => 6,
		'keys'      => array('step', 'title', 'description'),
		'labels'    => array(__('Step number', 'vr-doctors'), __('Title', 'vr-doctors'), __('Description', 'vr-doctors')),
		'default'   => $items,
	);
}

/**
 * Curriculum / methodology cards repeater.
 *
 * @param array<int, array<string, string>> $items Default cards.
 * @return array
 */
function vr_neet_curriculum_items_field($items) {
	return array(
		'type'      => 'items',
		'label'     => __('Cards', 'vr-doctors'),
		'add_label' => __('Add card', 'vr-doctors'),
		'row_label' => __('Card %d', 'vr-doctors'),
		'max'       => 12,
		'keys'      => array('number', 'title', 'description'),
		'types'     => array('number' => 'text', 'title' => 'text', 'description' => 'textarea'),
		'labels'    => array(__('Number (e.g. 01)', 'vr-doctors'), __('Title', 'vr-doctors'), __('Description', 'vr-doctors')),
		'default'   => $items,
	);
}

/**
 * Final CTA section fields.
 *
 * @param array<string, string> $defaults Defaults.
 * @return array
 */
function vr_neet_final_cta_fields($defaults) {
	return array(
		'eyebrow'            => array('type' => 'text', 'label' => __('Eyebrow', 'vr-doctors'), 'default' => 'Admissions Open'),
		'title'              => array('type' => 'text', 'label' => __('Heading', 'vr-doctors'), 'default' => $defaults['title'] ?? ''),
		'description'        => array('type' => 'textarea', 'label' => __('Description', 'vr-doctors'), 'default' => $defaults['description'] ?? ''),
		'primary_cta_text'   => array('type' => 'text', 'label' => __('Primary button text', 'vr-doctors'), 'default' => 'Book a Campus Visit'),
		'primary_cta_url'    => array('type' => 'text', 'label' => __('Primary button URL', 'vr-doctors'), 'default' => '/contact/'),
		'secondary_cta_text' => array('type' => 'text', 'label' => __('Secondary button text', 'vr-doctors'), 'default' => 'Call Admissions'),
		'secondary_cta_url'  => array('type' => 'text', 'label' => __('Secondary button URL', 'vr-doctors'), 'default' => 'tel:+917097098877'),
		'address'            => array('type' => 'textarea', 'label' => __('Address', 'vr-doctors'), 'default' => 'VR Doctors Academy, Plot No. 30, Near SBI Bank, Mathrusree Nagar, Hafeezpet, Miyapur, Hyderabad, Telangana 500049'),
		'phone_display'      => array('type' => 'text', 'label' => __('Phone display', 'vr-doctors'), 'default' => '+91-7097098877 / +91-7097098811'),
		'phone_url'          => array('type' => 'text', 'label' => __('Phone URL', 'vr-doctors'), 'default' => 'tel:+917097098877'),
		'email'              => array('type' => 'text', 'label' => __('Email', 'vr-doctors'), 'default' => 'info@vrdoctorsacademy.com'),
	);
}

/**
 * Shared sections used by Short-Term and Long-Term NEET pages.
 *
 * @param string $prefix Admin label prefix.
 * @param array  $config Section-specific defaults.
 * @return array
 */
function vr_neet_program_shared_sections($prefix, $config) {
	return array(
		'overview' => array(
			'label'  => $prefix . ' — Program Overview',
			'fields' => array(
				'eyebrow'     => array('type' => 'text', 'label' => __('Eyebrow', 'vr-doctors'), 'default' => 'Program Details'),
				'title'       => array('type' => 'text', 'label' => __('Heading', 'vr-doctors'), 'default' => 'Program Overview'),
				'table_col_1' => array('type' => 'text', 'label' => __('Table column 1 header', 'vr-doctors'), 'default' => 'Program Detail'),
				'table_col_2' => array('type' => 'text', 'label' => __('Table column 2 header', 'vr-doctors'), 'default' => 'Information'),
				'items'       => vr_neet_overview_items_field($config['overview_rows'] ?? array()),
			),
		),
		'who_should_join' => array(
			'label'  => $prefix . ' — Who Should Join',
			'fields' => array(
				'title'    => array('type' => 'text', 'label' => __('Heading', 'vr-doctors'), 'default' => $config['who_title'] ?? 'Who Should Join?'),
				'features' => array('type' => 'list', 'label' => __('Bullet points (one per line)', 'vr-doctors'), 'default' => implode("\n", $config['who_bullets'] ?? array())),
			),
		),
		'curriculum' => array(
			'label'  => $prefix . ' — Curriculum',
			'fields' => array(
				'eyebrow'     => array('type' => 'text', 'label' => __('Eyebrow', 'vr-doctors'), 'default' => 'Curriculum'),
				'title'       => array('type' => 'text', 'label' => __('Heading', 'vr-doctors'), 'default' => $config['curriculum_title'] ?? ''),
				'description' => array('type' => 'textarea', 'label' => __('Intro paragraph', 'vr-doctors'), 'default' => $config['curriculum_intro'] ?? ''),
				'items'       => vr_neet_curriculum_items_field($config['curriculum_cards'] ?? array()),
			),
		),
		'residential' => array(
			'label'  => $prefix . ' — Residential Campus',
			'fields' => array(
				'title'    => array('type' => 'text', 'label' => __('Heading', 'vr-doctors'), 'default' => 'Residential Campus & Hostel Facilities'),
				'features' => array('type' => 'list', 'label' => __('Facility bullets (one per line)', 'vr-doctors'), 'default' => implode("\n", $config['residential_bullets'] ?? array())),
			),
		),
		'track_record' => array(
			'label'  => $prefix . ' — Track Record',
			'fields' => array(
				'eyebrow' => array('type' => 'text', 'label' => __('Eyebrow', 'vr-doctors'), 'default' => 'Results'),
				'title'   => array('type' => 'text', 'label' => __('Heading', 'vr-doctors'), 'default' => 'Our Track Record'),
				'items'   => vr_neet_track_record_items_field($config['track_record'] ?? array()),
			),
		),
		'testimonials' => array(
			'label'  => $prefix . ' — Testimonials',
			'fields' => array(
				'eyebrow' => array('type' => 'text', 'label' => __('Eyebrow', 'vr-doctors'), 'default' => 'Testimonials'),
				'title'   => array('type' => 'text', 'label' => __('Heading', 'vr-doctors'), 'default' => 'What Our Students Say'),
				'items'   => vr_neet_testimonial_items_field($config['testimonials'] ?? array()),
			),
		),
		'admission' => array(
			'label'  => $prefix . ' — Admission Process',
			'fields' => array(
				'eyebrow' => array('type' => 'text', 'label' => __('Eyebrow', 'vr-doctors'), 'default' => 'Admissions'),
				'title'   => array('type' => 'text', 'label' => __('Heading', 'vr-doctors'), 'default' => 'Admission Process'),
				'items'   => vr_neet_admission_items_field($config['admission_steps'] ?? array()),
			),
		),
		'cross_link' => array(
			'label'  => $prefix . ' — Cross-Link Banner',
			'fields' => array(
				'show'        => array('type' => 'text', 'label' => __('Show this banner (yes/no)', 'vr-doctors'), 'default' => 'yes'),
				'title'       => array('type' => 'text', 'label' => __('Heading', 'vr-doctors'), 'default' => $config['cross_link_title'] ?? ''),
				'description' => array('type' => 'textarea', 'label' => __('Description', 'vr-doctors'), 'default' => $config['cross_link_desc'] ?? ''),
				'link_text'   => array('type' => 'text', 'label' => __('Link text', 'vr-doctors'), 'default' => $config['cross_link_text'] ?? ''),
				'link_url'    => array('type' => 'text', 'label' => __('Link URL', 'vr-doctors'), 'default' => $config['cross_link_url'] ?? ''),
			),
		),
		'faq' => array(
			'label'  => $prefix . ' — FAQ',
			'fields' => array(
				'eyebrow' => array('type' => 'text', 'label' => __('Eyebrow', 'vr-doctors'), 'default' => 'FAQ'),
				'title'   => array('type' => 'text', 'label' => __('Heading', 'vr-doctors'), 'default' => 'Frequently Asked Questions'),
				'items'   => vr_neet_faq_items_field($config['faqs'] ?? array()),
			),
		),
		'final_cta' => array(
			'label'  => $prefix . ' — Final CTA',
			'fields' => vr_neet_final_cta_fields($config['final_cta'] ?? array()),
		),
	);
}

/**
 * Residential facility bullets shared across pages.
 *
 * @return string[]
 */
function vr_neet_default_residential_bullets() {
	return array(
		'Separate, secure hostels for boys and girls',
		'Fully air-conditioned classrooms and hostel rooms',
		'High priority on hygiene, cleanliness, and regular maintenance',
		'Spacious, well-lit classrooms with ergonomically designed furniture',
		'In-house sick room with basic medical facilities and doctor-on-call support',
		'Canteen facility with quality, hygienic meals through the week',
		'Transport facility available',
	);
}

/**
 * Default track record stats.
 *
 * @param string $seats_text Seats description (A/B category wording).
 * @return array<int, array<string, string>>
 */
function vr_neet_default_track_record($seats_text = '52 students secured A-category medical seats and 15 students secured B-category seats in our pilot batch of 100 students') {
	return array(
		array('icon' => 'fa-percent', 'stat' => '50%+', 'text' => 'Minimum 50% success rate in NEET (MBBS) among VR Doctors Academy students'),
		array('icon' => 'fa-award', 'stat' => '52 + 15', 'text' => $seats_text),
		array('icon' => 'fa-users', 'stat' => 'Top Ranks', 'text' => 'Faculty team experienced in guiding students to All-India top ranks in NEET, AIIMS, and JIPMER'),
	);
}

/**
 * Full schemas for NEET landing pages.
 *
 * @return array
 */
function vr_neet_landing_page_schemas() {
	$residential = vr_neet_default_residential_bullets();

	$short_overview = array(
		array('label' => 'Program Name', 'value' => 'Focus-40 — Short-Term NEET Program (45–60 Day Crash Course)'),
		array('label' => 'Eligibility', 'value' => 'Students who have completed Class 12 / Intermediate (BiPC) and are preparing for NEET(UG)'),
		array('label' => 'Batch Size', 'value' => '40 students per batch, for focused, individual attention'),
		array('label' => 'Duration', 'value' => '45–60 days (typically January to April)'),
		array('label' => 'Structure', 'value' => 'Intensive NEET-only crash course — complete focus on NCERT concepts with no IPE component'),
		array('label' => 'Target Exam', 'value' => 'NEET (UG)'),
		array('label' => 'Residential Option', 'value' => 'Fully residential — separate, air-conditioned hostels for boys and girls'),
		array('label' => 'Class Timings', 'value' => '6 hours of daily classes plus 5–6 hours of supervised study, Monday to Saturday'),
		array('label' => 'Mentor Ratio', 'value' => 'Senior lecturers assigned at approximately 15:1 to clear doubts during study hours'),
	);

	$long_overview = array(
		array('label' => 'Program Name', 'value' => 'Focus-45 — Long-Term NEET Program (8–10 Months)'),
		array('label' => 'Eligibility', 'value' => 'Students who have completed Intermediate (BiPC) and are preparing for NEET(UG)'),
		array('label' => 'Batch Size', 'value' => '45 students per batch, for focused, individual attention'),
		array('label' => 'Duration', 'value' => '8–10 months intensive NEET preparation'),
		array('label' => 'Structure', 'value' => 'NEET-focused program building on completed Intermediate concepts — tips, tricks, efficiency and speed based on NCERT'),
		array('label' => 'Target Exam', 'value' => 'NEET (UG)'),
		array('label' => 'Residential Option', 'value' => 'Fully residential — separate, air-conditioned hostels for boys and girls'),
		array('label' => 'College Timings', 'value' => 'Structured daily timetable: 5 hours of classes plus 5–6 hours of supervised study, Monday to Saturday'),
	);

	$short_faqs = array(
		array('question' => 'What is the Short-Term NEET Program at VR Doctors Academy?', 'answer' => 'It is a 45–60 day, fully residential NEET crash course (Focus-40) for students who have completed Intermediate and want an intensive preparation to crack NEET in the next 2–3 months.'),
		array('question' => 'Who is eligible for the Short-Term NEET Program?', 'answer' => 'Students who have completed Class 12 / Intermediate (BiPC) — including repeaters attempting NEET again — are eligible to enroll.'),
		array('question' => 'How is the Short-Term Program different from the Long-Term Program?', 'answer' => 'The Short-Term Program (Focus-40) is a 45–60 day intensive crash course for students who have finished Intermediate. The Long-Term Program (Focus-45) is an 8–10 month residential course for Intermediate completers who need a longer preparation window.'),
		array('question' => 'What is the batch size for the Short-Term Program?', 'answer' => 'Each Focus-40 batch has 40 students, with senior lecturers assigned at roughly a 15:1 ratio to keep doubt-clearing personal.'),
		array('question' => 'Is the Short-Term NEET Program residential?', 'answer' => 'Yes. It is fully residential, with separate, air-conditioned hostels for boys and girls, hygienic meals, and an in-house sick room with doctor-on-call support.'),
		array('question' => 'How many hours of study are involved each day?', 'answer' => 'Students attend 6 hours of daily classes, Monday to Friday, plus 5–6 hours of supervised study, with additional revision and testing built into weekends.'),
		array('question' => 'How are students assessed during the program?', 'answer' => 'Through Daily Review Concept Tests, Weekly Conceptual NEET Tests, Saturday Biology tests, monthly major tests, grand tests, logical cumulative tests, and tricky unit tests — each followed by paper discussion and mentorship.'),
		array('question' => 'How does the academy track individual student progress?', 'answer' => 'Through tech-enabled analysis of each student\'s strengths and weaknesses, combined with personalized assignments and practice tests.'),
		array('question' => 'How often are parents updated?', 'answer' => 'Parent–teacher meetings are held every 6 weeks to review academic progress, alongside regular performance updates from faculty mentors.'),
		array('question' => 'What is VR Doctors Academy\'s success rate in NEET?', 'answer' => 'We maintain a minimum 50% success rate in NEET (MBBS) among our students, with 52 A-category and 15 B-category medical seats secured in our pilot batch.'),
		array('question' => 'Can I join the Short-Term Program if I\'ve never attended a coaching institute before?', 'answer' => 'Yes. The program is designed for anyone who has completed Intermediate, whether it\'s a first NEET attempt or a repeat attempt.'),
		array('question' => 'How do I apply for the Short-Term NEET Program?', 'answer' => 'Start with a phone enquiry, email, or a campus visit, followed by academic counselling and enrollment. Call +91-7097098877 / +91-7097098811 or email info@vrdoctorsacademy.com.'),
		array('question' => 'Where can I find NEET short-term coaching in Hyderabad?', 'answer' => 'VR Doctors Academy offers NEET short-term coaching in Hyderabad through its 45–60 day, fully residential Focus-40 crash course, built for Intermediate-completed students at its Hafeezpet, Miyapur campus.'),
	);

	$long_faqs = array(
		array('question' => 'What is the Long-Term NEET Program at VR Doctors Academy?', 'answer' => 'It is an 8–10 month, fully residential NEET program (Focus-45) for students who have completed Intermediate (BiPC) and want structured, in-depth NEET preparation with daily assessments and personal mentoring.'),
		array('question' => 'Who is eligible for the Long-Term NEET Program?', 'answer' => 'Students who have completed Intermediate (BiPC) and are preparing for NEET(UG) are eligible to enroll.'),
		array('question' => 'How is the Long-Term Program different from the Short-Term NEET Program?', 'answer' => 'The Long-Term Program (Focus-45) runs for 8–10 months, ideal for Intermediate completers who need extended preparation. The Short-Term Program (Focus-40) is a 45–60 day intensive crash course for students targeting NEET in the next 2–3 months.'),
		array('question' => 'What is the batch size for the Long-Term Program?', 'answer' => 'Each Focus-45 batch has 45 students, kept intentionally small so faculty can give individual attention to every student.'),
		array('question' => 'Is the Long-Term NEET Program residential?', 'answer' => 'Yes. The program is fully residential, with separate, air-conditioned hostels for boys and girls, hygienic meals, and an in-house sick room with doctor-on-call support.'),
		array('question' => 'How many hours of study are involved each day?', 'answer' => 'Students attend 5 hours of daily classes, Monday to Friday, plus 5–6 hours of supervised study right after classes, with additional revision and testing built into weekends.'),
		array('question' => 'How does the program prepare students for NEET?', 'answer' => 'Since Intermediate concepts are already completed, the program focuses on tips and tricks to improve efficiency and speed for solving NEET questions based on NCERT concepts, with daily tests and performance analysis.'),
		array('question' => 'How are students tested and tracked?', 'answer' => 'Through Daily Review Concept Tests, Weekly Conceptual NEET Tests, Saturday Biology tests, monthly major tests, and grand tests — each followed by paper discussion and mentorship.'),
		array('question' => 'What exams does the Long-Term Program prepare students for?', 'answer' => 'The program prepares students exclusively for NEET (UG).'),
		array('question' => 'What is VR Doctors Academy\'s success rate in NEET?', 'answer' => 'We maintain a minimum 50% success rate in NEET (MBBS) among our students, with 52 A-category and 15 B-category medical seats secured in our pilot batch.'),
		array('question' => 'How does the academy keep parents informed of student progress?', 'answer' => 'Parents receive regular updates through SMS alerts and the VR Doctors app, along with performance analysis from faculty mentors.'),
		array('question' => 'How do I apply for the Long-Term NEET Program?', 'answer' => 'Start with a phone enquiry, email, or a campus visit, followed by academic counselling and enrollment. Call +91-7097098877 / +91-7097098811 or email info@vrdoctorsacademy.com.'),
		array('question' => 'Where can I find NEET long-term coaching in Hyderabad?', 'answer' => 'VR Doctors Academy offers NEET long-term coaching in Hyderabad through its 8–10 month, fully residential Focus-45 program for Intermediate completers at its Hafeezpet, Miyapur campus.'),
	);

	$bipc_faqs = array(
		array('question' => 'Why is VR Doctors Academy considered one of the best BiPC colleges in Hyderabad with NEET coaching and residential facilities?', 'answer' => 'VR Doctors Academy is considered one of the best residential BiPC colleges in Hyderabad because it combines the TSBIE Intermediate curriculum with structured NEET (UG) coaching, comfortable hostel facilities, and personalized faculty mentoring through its Focus-45 program.'),
		array('question' => 'Does VR Doctors Academy provide hostel accommodation for BiPC students?', 'answer' => 'Yes. We offer separate, fully air-conditioned hostels for boys and girls, with hygienic meals, an in-house sick room, and doctor-on-call support.'),
		array('question' => 'Is NEET coaching integrated with the BiPC syllabus, or taught separately?', 'answer' => 'NEET preparation is fully integrated with the BiPC Intermediate curriculum through our Focus-45 program, so students are not juggling two separate institutions or timetables.'),
		array('question' => 'What is the Focus-45 program?', 'answer' => 'Focus-45 is our two-year, 45-student batch program that combines NEET(UG) preparation with IPE (Intermediate board) requirements, using daily classes, structured study hours, and regular testing.'),
		array('question' => 'What is VR Doctors Academy\'s NEET success rate?', 'answer' => 'We maintain a minimum 50% success rate in NEET (MBBS) among our students, with 35 A-category and 15 B-category medical seats secured in our batch of 100 students.'),
		array('question' => 'How are students assessed during the course?', 'answer' => 'Students go through Daily Review Concept Tests, Weekly Conceptual NEET Tests, Saturday Biology tests, monthly major tests, and grand tests every six months, followed by paper discussion and mentorship.'),
		array('question' => 'Are BiPC classes and NEET coaching handled by the same faculty?', 'answer' => 'Yes. Our senior-most faculty team teaches both the Intermediate BiPC syllabus and NEET-focused content, ensuring consistency between board exam and entrance exam preparation.'),
		array('question' => 'What facilities are available on campus?', 'answer' => 'Air-conditioned classrooms and hostels, separate boys\' and girls\' accommodation, a canteen, transport facility, an in-house sick room, and a structured daily routine.'),
		array('question' => 'How does VR Doctors Academy keep parents updated on student progress?', 'answer' => 'Parents receive regular updates through SMS alerts and the VR Doctors app, along with performance analysis from faculty mentors.'),
		array('question' => 'How can I apply for BiPC admission at VR Doctors Academy?', 'answer' => 'You can start with a phone enquiry, email, or a campus visit, followed by academic counselling and enrollment. Call +91-7097098877 / +91-7097098811 or email info@vrdoctorsacademy.com.'),
	);

	$short_shared = vr_neet_program_shared_sections(__('Short-Term NEET', 'vr-doctors'), array(
		'overview_rows'     => $short_overview,
		'who_title'         => 'Who Should Join the Short-Term NEET Program?',
		'who_bullets'       => array(
			'Students who have completed Intermediate and want to crack NEET in the next 2–3 months',
			'Repeaters who want a focused, intensive crash course before NEET',
			'Students who need a fully residential, distraction-free environment for intensive preparation',
			'Students who want senior faculty mentoring and tech-enabled performance analysis',
			'Out-of-town families who want hostel accommodation for the preparation period',
		),
		'curriculum_title'  => 'How the Short-Term Curriculum Is Structured',
		'curriculum_intro'  => 'Focus-40 is a 45–60 day, NEET-only crash course. The entire program is structured to build, test, and refine NEET concepts, efficiency, and exam temperament — with complete focus on NCERT and no IPE component.',
		'curriculum_cards'  => array(
			array('number' => '01', 'title' => 'Daily Classes', 'description' => 'Classes run Monday to Friday for 6 hours a day, covering Physics, Chemistry, Botany and Zoology with a focus on solving questions efficiently.'),
			array('number' => '02', 'title' => 'Study Hours', 'description' => '5–6 hours of study every day, supervised by lecturers who teach students how to solve questions efficiently rather than re-teaching concepts.'),
			array('number' => '03', 'title' => 'Teaching Approach', 'description' => 'Built on an applicative, problem-solving approach — especially in Physics and Chemistry — to sharpen exam-day accuracy and speed.'),
			array('number' => '04', 'title' => 'Saturday: Study Hours, Revision & Biology Test', 'description' => 'Students review their own progress and take a fill-in-the-blanks Biology test to reinforce recall.'),
			array('number' => '05', 'title' => 'Sunday: Revise, WCNT Exam & Play', 'description' => 'A Weekly Conceptual NEET Test (WCNT) followed by supervised recreation time to help students recover before the next week.'),
		),
		'residential_bullets' => $residential,
		'track_record'        => vr_neet_default_track_record(),
		'testimonials'        => array(
			array('image' => 'students/vamshi.webp', 'name' => 'Bisaoi Vamshi Krishna', 'college' => 'Osmania University', 'quote' => 'I have learned a lot from here. The academics are excellent, with a set of standardized and supportive lecturers.'),
			array('image' => 'students/vaishnavi.webp', 'name' => 'Dodla Vaishnavi', 'college' => 'Govt. College, Siddipet', 'quote' => 'They provide well-experienced faculty and a good, hygienic environment. Healthy food, stress-free education — thank you VR.'),
		),
		'admission_steps'     => array(
			array('step' => '01', 'title' => 'Step 1 – Enquiry', 'description' => 'Contact VR Doctors Academy by phone, email, or a campus visit to learn about the Short-Term NEET Program'),
			array('step' => '02', 'title' => 'Step 2 – Counselling', 'description' => 'Meet our academic counsellors to review your previous NEET attempt, the Focus-40 structure, and whether the crash course is the right fit'),
			array('step' => '03', 'title' => 'Step 3 – Enrollment', 'description' => 'Complete document verification and admission formalities to confirm the seat and hostel accommodation'),
		),
		'cross_link_title'    => 'Need a Longer Preparation Window?',
		'cross_link_desc'     => 'If you have completed Intermediate and want an 8–10 month residential NEET program with structured daily preparation, explore our Long-Term NEET Program (Focus-45).',
		'cross_link_text'     => 'Explore the Long-Term NEET Program',
		'cross_link_url'      => '/long-term-neet-program-hyderabad/',
		'faqs'                => $short_faqs,
		'final_cta'           => array(
			'title'       => 'Ready to Start Your Short-Term NEET Attempt?',
			'description' => 'Give yourself a focused, residential 45–60 day crash course built entirely around NEET — structured classes, daily assessments, and senior faculty mentoring. Book a campus visit or speak with our academic counsellors today.',
		),
	));

	$long_shared = vr_neet_program_shared_sections(__('Long-Term NEET', 'vr-doctors'), array(
		'overview_rows'     => $long_overview,
		'who_title'         => 'Who Should Join the Long-Term NEET Program?',
		'who_bullets'       => array(
			'Students who have just completed Intermediate (BiPC) and want to prep for NEET',
			'Parents who want their child to crack NEET with structured, residential preparation',
			'Students who prefer building exam efficiency and speed over an 8–10 month window',
			'Out-of-town families who want a supervised, residential environment for NEET preparation',
			'Students who want continuous performance tracking and mentoring through the program',
		),
		'curriculum_title'  => 'How the Long-Term Curriculum Is Structured',
		'curriculum_intro'  => 'Focus-45 is an 8–10 month NEET-focused program. Since Intermediate concepts are already completed, the entire program is built around tips, tricks, efficiency, and speed for solving NEET questions based on NCERT foundations.',
		'curriculum_cards'  => array(
			array('number' => '01', 'title' => 'NEET Preparation Phase', 'description' => 'The program builds on completed Intermediate concepts, working through NCERT foundations, previous years\' NEET question papers, and practicing logical and tricky test formats.'),
			array('number' => '02', 'title' => 'Daily Classes', 'description' => 'Five hours of concept-building classes every day, Monday to Friday, delivered in 50-minute sessions across Physics, Chemistry, Botany and Zoology by experienced faculty.'),
			array('number' => '03', 'title' => 'Daily Study Hours', 'description' => 'Study hours follow teaching classes immediately — students practise what they learnt and the same faculty clears doubts raised during study hours.'),
			array('number' => '04', 'title' => 'Teaching Approach', 'description' => 'Strong emphasis on an applicative, problem-solving approach — especially in Physics and Chemistry — rather than rote memorisation.'),
		),
		'residential_bullets' => array_merge($residential, array('A structured daily routine that balances academics, meals, rest, and recreation')),
		'track_record'        => vr_neet_default_track_record(),
		'testimonials'        => array(
			array('image' => 'students/anumalla.webp', 'name' => 'Anumalla Akshitha', 'college' => 'Osmania University', 'quote' => 'I got a very good opportunity to fulfill my dreams in such a pandemic situation. I am very proud to be a part of VR Doctors Academy — we have homely food and a hygienic room.'),
			array('image' => 'students/akhila.webp', 'name' => 'Annangi Akhila', 'college' => 'Osmania University', 'quote' => 'I am very happy to be a part of VR Academy. They provide good infrastructure with well-experienced faculty and stress-free education.'),
		),
		'admission_steps'     => array(
			array('step' => '01', 'title' => 'Step 1 – Enquiry', 'description' => 'Contact VR Doctors Academy by phone, email, or a campus visit to learn about the Long-Term NEET Program'),
			array('step' => '02', 'title' => 'Step 2 – Counselling', 'description' => 'Meet our academic counsellors to review the Focus-45 curriculum, residential facilities, and whether the 8–10 month track is the right fit'),
			array('step' => '03', 'title' => 'Step 3 – Enrollment', 'description' => 'Complete document verification and admission formalities to confirm the seat and hostel accommodation'),
		),
		'cross_link_title'    => 'Looking for a Crash Course Instead?',
		'cross_link_desc'     => 'If you have completed Intermediate and want a focused 45–60 day residential NEET intensive before the exam, explore our Short-Term NEET Program (Focus-40).',
		'cross_link_text'     => 'Explore the Short-Term NEET Program',
		'cross_link_url'      => '/short-term-neet-program-hyderabad/',
		'faqs'                => $long_faqs,
		'final_cta'           => array(
			'title'       => 'Ready to Start the Long-Term NEET Journey?',
			'description' => 'Give yourself 8–10 months of structured, residential NEET preparation — under one roof, one faculty team, one plan. Book a campus visit or speak with our academic counsellors today.',
		),
	));

	return array(
		'short-term-neet-program-hyderabad' => array_merge(
			array(
				'seo' => array(
					'label'  => __('Short-Term NEET — SEO', 'vr-doctors'),
					'fields' => vr_neet_seo_fields(array(
						'meta_title'       => 'Short-Term NEET Program in Hyderabad | 45–60 Day Focus-40',
						'meta_description' => "VR Doctors Academy's Short-Term NEET Program is a 45–60 day residential NEET crash course (Focus-40) in Hyderabad. Book a campus visit today.",
						'og_title'         => 'Short-Term NEET Program (45–60 Day Crash Course) | VR Doctors Academy',
						'og_description'   => 'A residential 45–60 day NEET crash course in Hyderabad with daily assessments, senior faculty mentoring, and tech-enabled performance analysis.',
					)),
				),
				'hero' => array(
					'label'  => __('Short-Term NEET — Hero', 'vr-doctors'),
					'fields' => vr_neet_hero_fields(array(
						'image_alt'     => 'Short-Term NEET Program in Hyderabad | 45–60 Day Focus-40',
						'eyebrow'       => 'Short-Term NEET · Focus-40 · Hyderabad',
						'title'         => 'NEET Short-Term Coaching in Hyderabad — Short-Term NEET Program (45–60 Day Crash Course / Focus-40)',
						'paragraph_1'   => 'Looking for NEET short-term coaching in Hyderabad after completing Intermediate? The Short-Term NEET Program at VR Doctors Academy is a 45–60 day, fully residential crash course (Focus-40) built for students who want to crack NEET in the next 2–3 months. Students get a structured daily timetable, senior faculty mentoring, and continuous performance tracking — all in one residential campus in Hyderabad.',
						'paragraph_2'   => 'This program starts in January and ends by April, with complete focus on NCERT concepts and no IPE. The majority of time is devoted to NEET tests, improving efficiency, and time management for exam day.',
					)),
				),
				'skill_tests' => array(
					'label'  => __('Short-Term NEET — Skill Tests', 'vr-doctors'),
					'fields' => array(
						'title'    => array('type' => 'text', 'label' => __('Heading', 'vr-doctors'), 'default' => 'Skill Tests & Assessment'),
						'features' => array('type' => 'list', 'label' => __('Bullets (one per line)', 'vr-doctors'), 'default' => "Daily Review Concept Tests (DRCT) — 30 minutes on Physics and Chemistry\nWeekly Conceptual NEET Tests (WCNT) aligned with the NEET module\nFill-in-the-blanks Biology test every Saturday\nMonthly major tests (covering the previous 4 weeks) and grand tests every six months\nLogical cumulative tests every two weeks and tricky unit tests every month\nPaper discussion and mentorship after every exam, with error notes maintained by each student"),
					),
				),
				'regulations' => array(
					'label'  => __('Short-Term NEET — Regulations & Support', 'vr-doctors'),
					'fields' => array(
						'title'    => array('type' => 'text', 'label' => __('Heading', 'vr-doctors'), 'default' => 'Our Regulations & Support System'),
						'features' => array('type' => 'list', 'label' => __('Bullets (one per line)', 'vr-doctors'), 'default' => "Tech-enabled, personalized assignments and practice tests for every student\nStudents are assigned to senior lecturers at approximately a 15:1 ratio to clear doubts during study hours\nTech-enabled analysis of each student's strengths and weaknesses\nParent–teacher meetings every 6 weeks to review progress\nOnline classes offered if in-person classes are ever disrupted"),
					),
				),
			),
			$short_shared
		),

		'long-term-neet-program-hyderabad' => array_merge(
			array(
				'seo' => array(
					'label'  => __('Long-Term NEET — SEO', 'vr-doctors'),
					'fields' => vr_neet_seo_fields(array(
						'meta_title'       => 'Long-Term NEET Program in Hyderabad | 8–10 Month Focus-45',
						'meta_description' => "VR Doctors Academy's Long-Term NEET Program is an 8–10 month residential NEET course (Focus-45) in Hyderabad for Intermediate completers. Book a campus visit today.",
						'og_title'         => 'Long-Term NEET Program (8–10 Months) | VR Doctors Academy',
						'og_description'   => 'A residential 8–10 month NEET program for Intermediate completers with structured coaching, daily assessments and personal mentoring.',
					)),
				),
				'hero' => array(
					'label'  => __('Long-Term NEET — Hero', 'vr-doctors'),
					'fields' => vr_neet_hero_fields(array(
						'image_alt'     => 'Long-Term NEET Program in Hyderabad | 8–10 Month Focus-45',
						'eyebrow'       => 'Long-Term NEET · Focus-45 · Hyderabad',
						'title'         => 'NEET Long-Term Coaching in Hyderabad — Long-Term NEET Program (8–10 Months for Intermediate Completers)',
						'paragraph_1'   => 'The Long-Term NEET Program at VR Doctors Academy is an 8–10 month, fully residential course in Hyderabad for students who have completed Intermediate (BiPC) and are preparing for NEET(UG). Students follow one integrated timetable — Focus-45 — built to sharpen NEET concepts, efficiency, and exam temperament under the same faculty and campus.',
						'paragraph_2'   => 'Since concepts are already completed in Intermediate, long-term students focus on the tips and tricks needed to improve efficiency and speed — crucial for solving NEET questions based on NCERT concepts.',
					)),
				),
				'methodology' => array(
					'label'  => __('Long-Term NEET — Teaching Methodology', 'vr-doctors'),
					'fields' => array(
						'eyebrow' => array('type' => 'text', 'label' => __('Eyebrow', 'vr-doctors'), 'default' => 'Methodology'),
						'title'   => array('type' => 'text', 'label' => __('Heading', 'vr-doctors'), 'default' => 'Our Teaching Methodology: Learn – Practise – Perform – Analyse – Achieve'),
						'items'   => array(
							'type'      => 'items',
							'label'     => __('Methodology cards', 'vr-doctors'),
							'add_label' => __('Add card', 'vr-doctors'),
							'row_label' => __('Card %d', 'vr-doctors'),
							'max'       => 8,
							'keys'      => array('title', 'description'),
							'types'     => array('title' => 'text', 'description' => 'textarea'),
							'labels'    => array(__('Title', 'vr-doctors'), __('Description', 'vr-doctors')),
							'default'   => array(
								array('title' => 'Concept Building', 'description' => 'Concepts are introduced through visual representation, live examples, and interactive lectures, with practical sessions and ongoing evaluation of each student\'s strengths and weaknesses.'),
								array('title' => 'Smart Study Material', 'description' => 'NCERT-based, cyclostyle study material aligned with the NEET syllabus, comprehensive question banks, and previous NEET model papers.'),
								array('title' => 'Skill Tests', 'description' => "Daily Review Concept Tests (DRCT) — 30 minutes on Physics and Chemistry\nWeekly Conceptual NEET Tests (WCNT) aligned with the NEET pattern\nFill-in-the-blanks Biology test every Saturday\nMonthly major tests and grand tests every six months"),
								array('title' => 'Paper Discussion & Mentorship', 'description' => 'After every exam, students go through paper discussion, maintain error notes, and receive one-on-one mentorship every Monday to analyse performance and plan corrective action.'),
								array('title' => 'Achieve', 'description' => '8–10 months of consistent, personalized guidance turns disciplined daily preparation into exam-day results.'),
							),
						),
					),
				),
			),
			$long_shared
		),

		'best-bipc-college-in-hyderabad-neet-residential' => array(
			'seo' => array(
				'label'  => __('Landing — SEO', 'vr-doctors'),
				'fields' => vr_neet_seo_fields(array(
					'meta_title'       => 'Best BiPC College in Hyderabad | NEET + Residential Campus',
					'meta_description' => 'VR Doctors Academy is a top residential BiPC college in Hyderabad offering integrated NEET coaching, expert faculty & proven results. Book a visit today.',
					'og_title'         => 'Best BiPC College in Hyderabad with Integrated NEET Coaching | VR Doctors Academy',
					'og_description'   => 'A residential BiPC junior college in Hyderabad combining Intermediate academics with structured NEET preparation, hostel facilities, and personal mentoring.',
				)),
			),
			'hero' => array(
				'label'  => __('Landing — Hero', 'vr-doctors'),
				'fields' => vr_neet_hero_fields(array(
					'image_alt'     => 'Best BiPC College in Hyderabad with NEET Coaching & Residential Campus',
					'eyebrow'       => 'Residential BiPC + NEET · Hyderabad',
					'title'         => 'Best BiPC College in Hyderabad with NEET Coaching & Residential Campus',
					'paragraph_1'   => 'Looking for the best BiPC college in Hyderabad that combines Intermediate academics with focused, residential NEET preparation? VR Doctors Academy is a residential BiPC junior college in Hyderabad built specifically for students who want to crack NEET. Our integrated BiPC + NEET program pairs the TSBIE Intermediate curriculum with a structured, in-house NEET coaching system, so students prepare for board exams and NEET(UG) under one roof, one faculty team, and one daily routine — without commuting between school and a separate coaching center.',
					'paragraph_2'   => 'As a residential BiPC college in Hyderabad, VR Doctors Academy gives students a distraction-free hostel environment, disciplined study hours, daily and weekly assessments, and personal faculty mentoring — the combination most NEET aspirants and their parents are searching for when they look for the best BiPC + NEET residential college in Hyderabad.',
					'phone_display' => '+91-9256925641 / +91-9256925642',
				)),
			),
			'choose_program' => array(
				'label'  => __('Landing — Choose Your Program', 'vr-doctors'),
				'fields' => array(
					'eyebrow'     => array('type' => 'text', 'label' => __('Eyebrow', 'vr-doctors'), 'default' => 'Programs'),
					'title'       => array('type' => 'text', 'label' => __('Heading', 'vr-doctors'), 'default' => 'Choose Your NEET Program'),
					'description' => array('type' => 'textarea', 'label' => __('Description', 'vr-doctors'), 'default' => 'Not every student needs the same path. Pick the program that fits your stage of preparation — each card below links to its own dedicated program page.'),
					'items'       => array(
						'type'      => 'items',
						'label'     => __('Program cards', 'vr-doctors'),
						'add_label' => __('Add program card', 'vr-doctors'),
						'row_label' => __('Card %d', 'vr-doctors'),
						'max'       => 6,
						'keys'      => array('icon', 'title', 'description', 'cta_text', 'cta_url', 'featured'),
						'labels'    => array(
							__('Icon class (e.g. fa-graduation-cap)', 'vr-doctors'),
							__('Title', 'vr-doctors'),
							__('Description', 'vr-doctors'),
							__('Button text', 'vr-doctors'),
							__('Button URL', 'vr-doctors'),
							__('Featured style (yes/no)', 'vr-doctors'),
						),
						'default'   => array(
							array(
								'icon'        => 'fa-graduation-cap',
								'title'       => 'Integrated NEET with Intermediate (2-Year Course)',
								'description' => 'For students starting after Class 10. Integrates the full TSBIE Intermediate (BiPC) curriculum with NEET(UG) coaching in one residential, two-year track (Focus-45).',
								'cta_text'    => 'Explore Focus-45 Program',
								'cta_url'     => '#focus-45',
								'featured'    => 'yes',
							),
							array(
								'icon'        => 'fa-book-open',
								'title'       => 'Long-Term NEET Program (8–10 Months)',
								'description' => 'For students who have completed Intermediate and are preparing for NEET. An 8–10 month residential program focused on efficiency, speed, and NEET exam temperament.',
								'cta_text'    => 'Explore the Long-Term Program',
								'cta_url'     => '/long-term-neet-program-hyderabad/',
								'featured'    => 'no',
							),
							array(
								'icon'        => 'fa-bolt',
								'title'       => 'Short-Term NEET Program (45–60 Day Crash Course)',
								'description' => 'For students who have completed Intermediate and want a focused 45–60 day residential NEET crash course to secure or improve their rank (Focus-40).',
								'cta_text'    => 'Explore the Short-Term Program',
								'cta_url'     => '/short-term-neet-program-hyderabad/',
								'featured'    => 'no',
							),
						),
					),
				),
			),
			'intermediate' => array(
				'label'  => __('Landing — Intermediate Section', 'vr-doctors'),
				'fields' => array(
					'image'       => array('type' => 'image', 'label' => __('Section image', 'vr-doctors'), 'default' => 'Campus/academics-1.webp'),
					'image_alt'   => array('type' => 'text', 'label' => __('Image alt', 'vr-doctors'), 'default' => 'Intermediate + NEET at VR Doctors'),
					'eyebrow'     => array('type' => 'text', 'label' => __('Eyebrow', 'vr-doctors'), 'default' => 'Intermediate + NEET'),
					'title'       => array('type' => 'text', 'label' => __('Heading', 'vr-doctors'), 'default' => 'Balanced Academics & Medical Prep'),
					'description' => array('type' => 'textarea', 'label' => __('Description', 'vr-doctors'), 'default' => 'Complete your Intermediate education while building a strong foundation for NEET. Our integrated curriculum ensures you excel in board exams and competitive entrance tests.'),
					'features'    => array('type' => 'list', 'label' => __('Highlights (one per line)', 'vr-doctors'), 'default' => "Intermediate Academics + NEET Coaching\nResidential Campus Environment\nStructured Daily Study Plan\nRegular Assessments & Analysis\nPersonal Faculty Mentoring"),
				),
			),
			'why_best' => array(
				'label'  => __('Landing — Why Best BiPC College', 'vr-doctors'),
				'fields' => array(
					'eyebrow'     => array('type' => 'text', 'label' => __('Eyebrow', 'vr-doctors'), 'default' => 'Why Choose Us'),
					'title'       => array('type' => 'text', 'label' => __('Heading', 'vr-doctors'), 'default' => 'Why VR Doctors Academy Is the Best BiPC College in Hyderabad'),
					'description' => array('type' => 'textarea', 'label' => __('Description', 'vr-doctors'), 'default' => "Choosing the right BiPC college in Hyderabad shapes a student's entire NEET journey. Here is what sets VR Doctors Academy apart:"),
					'items'       => array(
						'type'      => 'items',
						'label'     => __('Reason cards', 'vr-doctors'),
						'add_label' => __('Add reason', 'vr-doctors'),
						'row_label' => __('Reason %d', 'vr-doctors'),
						'max'       => 12,
						'keys'      => array('icon', 'text'),
						'labels'    => array(__('Icon class', 'vr-doctors'), __('Text', 'vr-doctors')),
						'default'   => array(
							array('icon' => 'fa-book-open', 'text' => 'Integrated BiPC + NEET curriculum under the Focus-45 program, designed around both TSBIE Intermediate requirements and the NEET(UG) syllabus'),
							array('icon' => 'fa-house', 'text' => 'Fully residential campus with separate, secure hostels for boys and girls'),
							array('icon' => 'fa-trophy', 'text' => 'Minimum 50% success rate in NEET (MBBS) among our students since 2019'),
							array('icon' => 'fa-users', 'text' => 'Senior-most faculty team with a strong record of All-India top ranks in NEET, AIIMS and JIPMER'),
							array('icon' => 'fa-user-check', 'text' => 'Small, limited-intake batches for focused, personalized teaching'),
							array('icon' => 'fa-microchip', 'text' => 'Digitalised teaching with AI-based in-depth student performance analysis'),
							array('icon' => 'fa-clipboard-list', 'text' => 'Daily study hours, weekly tests, and monthly major/grand tests to track real progress'),
							array('icon' => 'fa-mobile-screen', 'text' => 'Continuous parent communication on academic performance via SMS alerts'),
						),
					),
				),
			),
			'what_is_bipc' => array(
				'label'  => __('Landing — What Is BiPC', 'vr-doctors'),
				'fields' => array(
					'title'       => array('type' => 'text', 'label' => __('Heading', 'vr-doctors'), 'default' => 'What Is BiPC and Why a Residential BiPC College Matters'),
					'paragraph_1' => array('type' => 'textarea', 'label' => __('Paragraph 1', 'vr-doctors'), 'default' => 'BiPC (Biology, Physics, Chemistry) is the Intermediate stream students choose after Class 10 to pursue medicine and life-science careers. It builds the scientific foundation required for NEET(UG) and other medical entrance exams. Because the BiPC syllabus and the NEET syllabus overlap heavily, studying them separately — one at a day college, another at an evening coaching center — wastes time and dilutes focus.'),
					'paragraph_2' => array('type' => 'textarea', 'label' => __('Paragraph 2', 'vr-doctors'), 'default' => 'A residential BiPC college in Hyderabad removes that inefficiency. Students live on campus, follow one structured timetable that covers both board and NEET preparation, and get uninterrupted study hours in the evening instead of losing time in traffic between two institutions. For serious NEET aspirants and out-of-town families, residential BiPC colleges with integrated NEET coaching are consistently the preferred choice in Hyderabad.'),
				),
			),
			'focus_45' => array(
				'label'  => __('Landing — Focus-45 Program', 'vr-doctors'),
				'fields' => array(
					'eyebrow'           => array('type' => 'text', 'label' => __('Eyebrow', 'vr-doctors'), 'default' => 'Focus-45'),
					'title'             => array('type' => 'text', 'label' => __('Heading', 'vr-doctors'), 'default' => 'Our Integrated BiPC + NEET Program: Focus-45'),
					'description'       => array('type' => 'textarea', 'label' => __('Description', 'vr-doctors'), 'default' => 'Focus-45 is our two-year, limited-batch (45 students) classroom program that balances NEET preparation with IPE (Intermediate Public Examination) requirements, so students are equally ready for board exams and NEET(UG).'),
					'table_col_1'       => array('type' => 'text', 'label' => __('Table column 1 header', 'vr-doctors'), 'default' => 'Program Detail'),
					'table_col_2'       => array('type' => 'text', 'label' => __('Table column 2 header', 'vr-doctors'), 'default' => 'Information'),
					'overview_items'    => vr_neet_overview_items_field(array(
						array('label' => 'Program Name', 'value' => 'Focus-45 — Integrated BiPC + NEET Program'),
						array('label' => 'Batch Size', 'value' => '45 students per batch, for focused attention'),
						array('label' => 'Duration', 'value' => 'Two academic years (1st & 2nd year Intermediate)'),
						array('label' => 'Structure', 'value' => 'NEET-focused training for the first phase of each academic year, followed by dedicated IPE (board exam) preparation before exams'),
						array('label' => 'Target Exams', 'value' => 'NEET (UG), and Intermediate Public Examination (IPE / TSBIE)'),
						array('label' => 'Residential Option', 'value' => 'Fully residential — separate hostels for boys and girls'),
					)),
					'curriculum_title'  => array('type' => 'text', 'label' => __('Curriculum heading', 'vr-doctors'), 'default' => 'How the Curriculum Is Structured'),
					'curriculum_features' => array('type' => 'list', 'label' => __('Curriculum bullets (one per line)', 'vr-doctors'), 'default' => "NEET Phase: The first months of each academic year (1st and 2nd year) are spent building the NEET syllabus from NCERT foundations, working through core concepts, comparing previous years' question papers, and practicing logical and tricky test formats\nIPE Phase: The following months focus on completing the TSBIE board syllabus, practicing the full range of question types, and developing descriptive/writing skills to help students score high in board exams\nDaily Classes: Five hours of concept-building classes every day, Monday to Friday, in 50-minute sessions across Physics, Chemistry, Botany and Zoology\nDaily Study Hours: 5–6 hours of supervised study every day, with senior faculty available to clarify doubts and reinforce concepts\nApproach: Strong emphasis on application and problem-solving, especially in Physics and Chemistry"),
				),
			),
			'methodology' => array(
				'label'  => __('Landing — Teaching Methodology', 'vr-doctors'),
				'fields' => array(
					'eyebrow' => array('type' => 'text', 'label' => __('Eyebrow', 'vr-doctors'), 'default' => 'Methodology'),
					'title'   => array('type' => 'text', 'label' => __('Heading', 'vr-doctors'), 'default' => 'Our Teaching Methodology: Learn – Practise – Perform – Analyse – Achieve'),
					'items'   => array(
						'type'      => 'items',
						'label'     => __('Methodology cards', 'vr-doctors'),
						'add_label' => __('Add card', 'vr-doctors'),
						'row_label' => __('Card %d', 'vr-doctors'),
						'max'       => 8,
						'keys'      => array('title', 'description'),
						'types'     => array('title' => 'text', 'description' => 'textarea'),
						'labels'    => array(__('Title', 'vr-doctors'), __('Description', 'vr-doctors')),
						'default'   => array(
							array('title' => 'Concept Building', 'description' => 'Concepts are introduced through visual representation, live examples, and interactive lectures from experienced faculty, with practical sessions and continuous evaluation of student strengths and weaknesses.'),
							array('title' => 'Smart Study Material', 'description' => 'NCERT-based, cyclostyle study material aligned with the NEET syllabus, along with comprehensive question banks and previous NEET model papers. Full IPE syllabus material is also provided so students can score well in board exams.'),
							array('title' => 'Skill Tests', 'description' => "Daily Review Concept Tests (DRCT) — 30 minutes on Physics and Chemistry\nWeekly Conceptual NEET Tests (WCNT) aligned with the NEET pattern\nFill-in-the-blanks Biology test every Saturday\nMonthly major tests and grand tests every 6 months"),
							array('title' => 'Paper Discussion & Mentorship', 'description' => 'After every exam, students go through paper discussions, maintain error notes, and receive mentorship every Monday to analyse performance, identify gaps, and plan corrective action.'),
							array('title' => 'Achieve', 'description' => 'Consistent, timely, personalized guidance keeps students on track through the full two-year journey — turning disciplined preparation into NEET results.'),
						),
					),
				),
			),
			'schedule' => array(
				'label'  => __('Landing — Weekly Schedule', 'vr-doctors'),
				'fields' => array(
					'eyebrow'     => array('type' => 'text', 'label' => __('Eyebrow', 'vr-doctors'), 'default' => 'Weekly Rhythm'),
					'title'       => array('type' => 'text', 'label' => __('Heading', 'vr-doctors'), 'default' => 'Weekly Assessment & Study Schedule'),
					'col_day'     => array('type' => 'text', 'label' => __('Table column: Day', 'vr-doctors'), 'default' => 'Day'),
					'col_focus'   => array('type' => 'text', 'label' => __('Table column: Focus', 'vr-doctors'), 'default' => 'Focus'),
					'col_details' => array('type' => 'text', 'label' => __('Table column: Details', 'vr-doctors'), 'default' => 'Details'),
					'items'       => array(
						'type'      => 'items',
						'label'     => __('Schedule rows', 'vr-doctors'),
						'add_label' => __('Add row', 'vr-doctors'),
						'row_label' => __('Row %d', 'vr-doctors'),
						'max'       => 12,
						'keys'      => array('day', 'focus', 'details'),
						'labels'    => array(__('Day', 'vr-doctors'), __('Focus', 'vr-doctors'), __('Details', 'vr-doctors')),
						'default'   => array(
							array('day' => 'Sunday', 'focus' => 'Full 3 hours NEET exam', 'details' => 'Complete NEET-pattern mock exam'),
							array('day' => 'Monday', 'focus' => 'Results & Analysis', 'details' => 'Paper discussion, error notes, and performance analysis'),
							array('day' => 'Tuesday', 'focus' => 'Faculty Explanation', 'details' => 'Faculty explanation for common mistakes and hard topics based on analysis'),
							array('day' => 'Wednesday', 'focus' => 'Error Test', 'details' => 'Targeted test on previously identified weak areas'),
							array('day' => 'Thu – Fri', 'focus' => 'New Topics & Exam Prep', 'details' => 'New topics and preparation for Sunday exam'),
							array('day' => 'Saturday', 'focus' => 'Self Preparation', 'details' => 'Self-study and revision for the upcoming weekly exam'),
						),
					),
				),
			),
			'facilities' => array(
				'label'  => __('Landing — Facilities', 'vr-doctors'),
				'fields' => array(
					'eyebrow'     => array('type' => 'text', 'label' => __('Eyebrow', 'vr-doctors'), 'default' => 'Campus & Hostel'),
					'title'       => array('type' => 'text', 'label' => __('Heading', 'vr-doctors'), 'default' => 'Residential Campus & Hostel Facilities'),
					'description' => array('type' => 'textarea', 'label' => __('Description', 'vr-doctors'), 'default' => 'VR Doctors Academy operates as a fully residential BiPC college in Hyderabad, giving students a safe, disciplined, and focused environment to prepare for NEET without daily commuting or distraction.'),
					'image_1'     => array('type' => 'image', 'label' => __('Image 1', 'vr-doctors'), 'default' => 'Campus/infra-1.webp'),
					'image_1_alt' => array('type' => 'text', 'label' => __('Image 1 alt text', 'vr-doctors'), 'default' => 'Residential campus facilities at VR Doctors Academy'),
					'image_2'     => array('type' => 'image', 'label' => __('Image 2', 'vr-doctors'), 'default' => 'Campus/infra-2.webp'),
					'image_2_alt' => array('type' => 'text', 'label' => __('Image 2 alt text', 'vr-doctors'), 'default' => 'Hostel facilities at VR Doctors Academy'),
					'image_3'     => array('type' => 'image', 'label' => __('Image 3', 'vr-doctors'), 'default' => 'Campus/infra-3.webp'),
					'image_3_alt' => array('type' => 'text', 'label' => __('Image 3 alt text', 'vr-doctors'), 'default' => 'Air-conditioned classrooms and hostel rooms'),
					'items'       => vr_neet_facility_items_field(array(
						array('icon' => 'fa-building', 'text' => 'Separate, secure hostels for boys and girls'),
						array('icon' => 'fa-snowflake', 'text' => 'Fully air-conditioned classrooms and hostel rooms'),
						array('icon' => 'fa-star', 'text' => 'High priority on hygiene, cleanliness, and regular maintenance'),
						array('icon' => 'fa-chair', 'text' => 'Spacious, well-lit classrooms with ergonomically designed furniture'),
						array('icon' => 'fa-heart-pulse', 'text' => 'In-house sick room with basic medical facilities and doctor-on-call support'),
						array('icon' => 'fa-utensils', 'text' => 'Canteen facility with quality, hygienic meals through the week'),
						array('icon' => 'fa-bus', 'text' => 'Transport facility available'),
						array('icon' => 'fa-calendar-check', 'text' => 'Structured daily routine that balances academics, meals, rest, and recreation'),
					)),
				),
			),
			'campus_life' => array(
				'label'  => __('Landing — Campus Life', 'vr-doctors'),
				'fields' => array(
					'eyebrow' => array('type' => 'text', 'label' => __('Eyebrow', 'vr-doctors'), 'default' => 'Campus Life'),
					'title'   => array('type' => 'text', 'label' => __('Heading', 'vr-doctors'), 'default' => 'Campus Life at VR Doctors Academy'),
					'items'   => array(
						'type'      => 'items',
						'label'     => __('Campus life pillars', 'vr-doctors'),
						'add_label' => __('Add pillar', 'vr-doctors'),
						'row_label' => __('Pillar %d', 'vr-doctors'),
						'max'       => 6,
						'keys'      => array('icon', 'title', 'description'),
						'labels'    => array(__('Icon class', 'vr-doctors'), __('Title', 'vr-doctors'), __('Description', 'vr-doctors')),
						'default'   => array(
							array('icon' => 'fa-school', 'title' => 'College Life', 'description' => 'College life is one of the most memorable phases in a student\'s journey — a residential BiPC campus helps students grow academically, emotionally, and socially alongside peers who share the same NEET goal.'),
							array('icon' => 'fa-utensils', 'title' => 'Food & Fun', 'description' => 'Our hostel provides quality, hygienic meals through the week, including student favourites. Weekly movie/music time (30 mins/day), 2-hour lunch break, and a calm atmosphere at mealtimes to help students recharge.'),
							array('icon' => 'fa-handshake-angle', 'title' => 'Mentorship', 'description' => 'Regular counselling and motivational sessions keep students focused and confident throughout the demanding NEET preparation journey.'),
						),
					),
				),
			),
			'track_record' => array(
				'label'  => __('Landing — Track Record', 'vr-doctors'),
				'fields' => array(
					'eyebrow' => array('type' => 'text', 'label' => __('Eyebrow', 'vr-doctors'), 'default' => 'Results'),
					'title'   => array('type' => 'text', 'label' => __('Heading', 'vr-doctors'), 'default' => 'Our Track Record'),
					'items'   => vr_neet_track_record_items_field(vr_neet_default_track_record('35 students secured A-category medical seats and 15 students secured B-category seats in our batch of 100 students')),
				),
			),
			'testimonials' => array(
				'label'  => __('Landing — Testimonials', 'vr-doctors'),
				'fields' => array(
					'eyebrow' => array('type' => 'text', 'label' => __('Eyebrow', 'vr-doctors'), 'default' => 'Testimonials'),
					'title'   => array('type' => 'text', 'label' => __('Heading', 'vr-doctors'), 'default' => 'What Our Students Say'),
					'items'   => vr_neet_testimonial_items_field(array(
						array('image' => 'students/anumalla.webp', 'name' => 'Anumalla Akshitha', 'college' => 'Osmania University', 'quote' => 'I got a very good opportunity to fulfill my dreams in such a pandemic situation. I am very proud to be a part of VR Doctors Academy — we have homely food and a hygienic room.'),
						array('image' => 'students/akhila.webp', 'name' => 'Annangi Akhila', 'college' => 'Osmania University', 'quote' => 'I am very happy to be a part of VR Academy. They provide good infrastructure with well-experienced faculty and stress-free education.'),
						array('image' => 'students/vamshi.webp', 'name' => 'Bisaoi Vamshi Krishna', 'college' => 'Osmania University', 'quote' => 'I have learned a lot from here. The academics are excellent, with a set of standardized and supportive lecturers.'),
					)),
				),
			),
			'admission' => array(
				'label'  => __('Landing — Admission Process', 'vr-doctors'),
				'fields' => array(
					'eyebrow' => array('type' => 'text', 'label' => __('Eyebrow', 'vr-doctors'), 'default' => 'Admissions'),
					'title'   => array('type' => 'text', 'label' => __('Heading', 'vr-doctors'), 'default' => 'Admission Process'),
					'items'   => vr_neet_admission_items_field(array(
						array('step' => '01', 'title' => 'Step 1 – Enquiry', 'description' => 'Contact VR Doctors Academy by phone, email, or a campus visit to understand the BiPC + NEET residential program'),
						array('step' => '02', 'title' => 'Step 2 – Counselling', 'description' => 'Meet our academic counsellors to review the Focus-45 curriculum, residential facilities, and the right fit based on the student\'s current level'),
						array('step' => '03', 'title' => 'Step 3 – Enrollment', 'description' => 'Complete document verification and admission formalities to confirm your seat and hostel accommodation'),
					)),
				),
			),
			'faq' => array(
				'label'  => __('Landing — FAQ', 'vr-doctors'),
				'fields' => array(
					'eyebrow' => array('type' => 'text', 'label' => __('Eyebrow', 'vr-doctors'), 'default' => 'FAQ'),
					'title'   => array('type' => 'text', 'label' => __('Heading', 'vr-doctors'), 'default' => 'Frequently Asked Questions'),
					'items'   => vr_neet_faq_items_field($bipc_faqs),
				),
			),
			'final_cta' => array(
				'label'  => __('Landing — Final CTA', 'vr-doctors'),
				'fields' => vr_neet_final_cta_fields(array(
					'title'       => 'Ready to Join the Best BiPC College in Hyderabad with NEET and Residential Support?',
					'description' => 'Give your child a focused, residential BiPC + NEET environment built around one goal: a medical seat. Book a campus visit or speak with our academic counsellors today.',
				)),
			),
		),
	);
}
