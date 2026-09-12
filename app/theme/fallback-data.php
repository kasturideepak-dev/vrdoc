<?php
/**
 * Hardcoded fallback content matching the Next.js site (used when CPT empty).
 *
 * @package VR_Doctors
 */

if (!defined('ABSPATH')) {
	exit;
}

function vr_fallback_testimonials() {
	return array(
		array(
			'title' => 'Anumalla Akshitha',
			'image' => vr_img('students/anumalla.webp'),
			'meta'  => array(
				'college'    => 'Osmania Medical College',
				'rank_badge' => 'Score 617/720',
				'quote'      => 'VR Doctors Academy gave me the discipline, faculty support and confidence to achieve my dream.',
			),
		),
		array(
			'title' => 'Annangi Akhila',
			'image' => vr_img('students/akhila.webp'),
			'meta'  => array(
				'college'    => 'Osmania Medical College',
				'rank_badge' => 'Govt Seat Secured',
				'quote'      => 'Excellent faculty, stress-free learning and a wonderful residential environment helped me succeed.',
			),
		),
		array(
			'title' => 'Bisaoi Vamshi Krishna',
			'image' => vr_img('students/vamshi.webp'),
			'meta'  => array(
				'college'    => 'Gandhi Medical College',
				'rank_badge' => 'AIR 507',
				'quote'      => 'The study culture and mentoring at VR Doctors made all the difference in my preparation.',
			),
		),
		array(
			'title' => 'Dodla Vaishnavi',
			'image' => vr_img('students/vaishnavi.webp'),
			'meta'  => array(
				'college'    => 'Government Medical College, Siddipet',
				'rank_badge' => 'MBBS Seat Secured',
				'quote'      => 'Healthy food, experienced lecturers and constant motivation helped me achieve my goal.',
			),
		),
	);
}

function vr_fallback_faculty() {
	return array(
		array('title' => 'KVR Sir', 'image' => vr_img('faculty/KVR Sir.webp'), 'meta' => array('subject' => 'Academic Head', 'experience' => '37+ Years Experience')),
		array('title' => 'G Ashok', 'image' => vr_img('faculty/G Ashok.webp'), 'meta' => array('subject' => 'Sr. Physics', 'experience' => '22+ Years Experience')),
		array('title' => 'B Nagesh', 'image' => vr_img('faculty/B Nagesh.webp'), 'meta' => array('subject' => 'Sr. Botany', 'experience' => '20+ Years Experience')),
		array('title' => 'M Srinath', 'image' => vr_img('faculty/M Srinath.webp'), 'meta' => array('subject' => 'Sr. Zoology', 'experience' => '8+ Years Experience')),
		array('title' => 'P Malyadri', 'image' => vr_img('faculty/P Malyadri.webp'), 'meta' => array('subject' => 'Sr Chemisty', 'experience' => '18+ Years Experience')),
		array('title' => 'K Prabhakar Reddy', 'image' => vr_img('faculty/K Prabhakar Reddy.webp'), 'meta' => array('subject' => 'Sr Zoology', 'experience' => '16+ Years Experience')),
	);
}

function vr_fallback_achievers() {
	return array(
		array('title' => 'Inamul Hussian', 'image' => vr_img('results/Asset 5.png'), 'meta' => array('rank' => '569 Marks', 'college' => 'Bidar institute of medical sciences')),
		array('title' => 'Gopika Rani', 'image' => vr_img('results/Asset 15.png'), 'meta' => array('rank' => '597 Marks', 'college' => 'Osmania Medical College - Hyd')),
		array('title' => 'VVNB Kireeti', 'image' => vr_img('results/Asset 22.png'), 'meta' => array('rank' => '588 Marks', 'college' => 'Apollo Medical College')),
		array('title' => 'Chandra Mohan Reddy', 'image' => vr_img('results/Asset 6.png'), 'meta' => array('rank' => '582 Marks', 'college' => 'Kakatiya Medical College')),
		array('title' => 'G Anshika Varshini', 'image' => vr_img('results/Asset 20.png'), 'meta' => array('rank' => '556 Marks', 'college' => 'Gandhi Medical College')),
		array('title' => 'K Sai Devi Sree', 'image' => vr_img('results/Asset 10.png'), 'meta' => array('rank' => '551 Marks', 'college' => 'Governamanet Medical College')),
		array('title' => 'D Sai Kumar', 'image' => vr_img('results/Asset 14.png'), 'meta' => array('rank' => '525 Marks', 'college' => 'Osmania Medical College')),
	);
}

/**
 * Default About-page journey tabs (year, title, copy, theme image path).
 *
 * @return array<int, array{year:string,title:string,content:string,image:string}>
 */
function vr_default_about_timeline_items() {
	return array(
		array(
			'year'    => '2019',
			'title'   => 'VR Doctors Academy Founded',
			'content' => 'Started with a vision to help aspiring medical students transform their dreams into reality.',
			'image'   => 'journey/2019.webp',
		),
		array(
			'year'    => '2020',
			'title'   => 'Produced Our First Batch Of Doctors',
			'content' => 'The first successful batch marked the beginning of a journey that would impact hundreds of future medical professionals.',
			'image'   => 'journey/2020.webp',
		),
		array(
			'year'    => '2022',
			'title'   => 'Strengthened Residential NEET Programs',
			'content' => 'Built a disciplined residential ecosystem designed to maximize student success and consistency.',
			'image'   => 'journey/2022.webp',
		),
		array(
			'year'    => '2023',
			'title'   => 'Launched VRIIT',
			'content' => 'Expanded academic offerings to support IIT aspirants through a dedicated program.',
			'image'   => 'journey/2023.webp',
		),
		array(
			'year'    => '2024',
			'title'   => 'Expanded Through VR Junior College',
			'content' => 'Broadened academic pathways and strengthened the educational ecosystem for students.',
			'image'   => 'journey/2024.webp',
		),
		array(
			'year'    => '2026',
			'title'   => 'Building The Next Chapter',
			'content' => 'Continuing our efforts to provide quality education to future healthcare professionals.',
			'image'   => 'journey/2026.webp',
		),
	);
}

function vr_fallback_timeline() {
	$items = array();
	foreach (vr_default_about_timeline_items() as $row) {
		$items[] = array(
			'title'   => $row['title'],
			'image'   => vr_img($row['image']),
			'content' => $row['content'],
			'meta'    => array('year' => $row['year']),
		);
	}
	return $items;
}

/**
 * Default hero slides (theme images). Prefer vr_get_home_hero() on the front end.
 */
function vr_hero_slides_fallback() {
	return array(
		array(
			'image'       => vr_img('hero-dream.webp'),
			'title'       => 'Every Doctor Begins With A Dream',
			'subtitle'    => 'Vision Into Reality',
			'description' => 'Do you dream of wearing a white coat? Every successful doctor starts with a vision for the future.',
		),
		array(
			'image'       => vr_img('hero-preparation.webp'),
			'title'       => 'The Journey Starts Here',
			'subtitle'    => 'Vision Into Reality',
			'description' => 'Expert faculty, structured preparation, mentoring and a focused residential environment help students reach their goals.',
		),
		array(
			'image'       => vr_img('hero-success.webp'),
			'title'       => 'Dreams Become Reality',
			'subtitle'    => 'Vision Into Reality',
			'description' => 'NEET success, medical seats and future doctors. Your journey begins with the choices you make today.',
		),
	);
}

/**
 * @deprecated Use vr_get_home_hero()['slides'].
 */
function vr_hero_slides() {
	if (function_exists('vr_get_home_hero')) {
		return vr_get_home_hero()['slides'];
	}
	return vr_hero_slides_fallback();
}

/**
 * Default campus life tabs.
 */
function vr_campus_categories_fallback() {
	return array(
		array(
			'tab'      => 'Academics',
			'photos'   => array(
				array('src' => vr_img('Campus/academics 1.webp'), 'alt' => 'Classroom'),
				array('src' => vr_img('Campus/academics-2.webp'), 'alt' => 'Study Area'),
				array('src' => vr_img('Campus/academics-3.webp'), 'alt' => 'Library'),
				array('src' => vr_img('Campus/academics-4.webp'), 'alt' => 'Laboratory'),
			),
			'features' => array(
				array('title' => 'Expert Faculty', 'description' => 'Experienced educators who understand competitive exam preparation and guide every student personally.'),
				array('title' => 'Study Culture', 'description' => 'Structured schedules, supervised study hours and disciplined preparation for NEET success.'),
			),
		),
		array(
			'tab'      => 'Infrastructure',
			'photos'   => array(
				array('src' => vr_img('Campus/infra-1.webp'), 'alt' => 'Hostel Building'),
				array('src' => vr_img('Campus/infra-2.webp'), 'alt' => 'Student Rooms'),
				array('src' => vr_img('Campus/infra-3.webp'), 'alt' => 'Mess Hall'),
				array('src' => vr_img('Campus/infra-4.webp'), 'alt' => 'Dining Area'),
			),
			'features' => array(
				array('title' => 'Residential Campus', 'description' => 'A focused residential environment designed to minimize distractions and maximize academic growth.'),
				array('title' => 'Nutritious Food', 'description' => 'Healthy and balanced meals prepared to support student wellbeing and long study hours.'),
			),
		),
		array(
			'tab'      => 'Sports & Events',
			'photos'   => array(
				array('src' => vr_img('Campus/events-1.webp'), 'alt' => 'Cultural Festival'),
				array('src' => vr_img('Campus/events-2.webp'), 'alt' => 'Sports Day'),
				array('src' => vr_img('Campus/events-3.webp'), 'alt' => 'Student Events'),
				array('src' => vr_img('Campus/events-4.webp'), 'alt' => 'Celebrations'),
			),
			'features' => array(
				array('title' => 'Events & Festivals', 'description' => 'Celebrating important occasions and cultural events to create memorable student experiences.'),
				array('title' => 'Safe Environment', 'description' => 'A secure and supportive campus where students can focus on their goals with confidence.'),
			),
		),
	);
}

/**
 * @deprecated Use vr_get_home_campus()['categories'].
 */
function vr_campus_categories() {
	if (function_exists('vr_get_home_campus')) {
		return vr_get_home_campus()['categories'];
	}
	return vr_campus_categories_fallback();
}

/**
 * Default approach steps.
 */
function vr_approach_steps_fallback() {
	return array(
		array('title' => 'LEARN', 'heading' => 'Concept Building', 'image' => vr_img('approach/learn.webp'), 'description' => 'Strong concepts form the foundation of every successful NEET aspirant. Our faculty ensures students understand subjects deeply rather than relying on memorization.'),
		array('title' => 'PRACTISE', 'heading' => 'Smart Material', 'image' => vr_img('approach/practise.webp'), 'description' => 'Carefully designed study material and assignments help students strengthen concepts and build confidence through continuous practice.'),
		array('title' => 'PERFORM', 'heading' => 'Skill Tests', 'image' => vr_img('approach/perform.webp'), 'description' => 'Regular assessments simulate exam conditions and help students improve speed, accuracy and time management.'),
		array('title' => 'ANALYSE', 'heading' => 'Paper Discussions', 'image' => vr_img('approach/analyse.webp'), 'description' => 'Detailed discussions after every test help students identify mistakes, improve weak areas and develop better exam strategies.'),
		array('title' => 'ACHIEVE', 'heading' => 'NEET Success', 'image' => vr_img('approach/achieve.webp'), 'description' => 'The result of disciplined preparation, expert mentoring and consistent effort is success in NEET and admission into top medical colleges.'),
	);
}

/**
 * @deprecated Use vr_get_home_approach()['steps'].
 */
function vr_approach_steps() {
	if (function_exists('vr_get_home_approach')) {
		return vr_get_home_approach()['steps'];
	}
	return vr_approach_steps_fallback();
}

function vr_bipc_ecosystems() {
	return array(
		array(
			'title'   => 'Patient Care',
			'icon'    => 'fa-user-doctor',
			'careers' => array(
				array('title' => 'MBBS', 'duration' => '5.5 Years', 'entrance' => 'NEET', 'focus' => 'Diagnosis and treatment of patients', 'paths' => 'Doctor • Surgeon • Specialist'),
				array('title' => 'BDS', 'duration' => '5 Years', 'entrance' => 'NEET', 'focus' => 'Oral health and dentistry', 'paths' => 'Dentist • Orthodontist'),
				array('title' => 'Nursing', 'duration' => '4 Years', 'entrance' => 'State Entrance / Merit', 'focus' => 'Patient care and healthcare support', 'paths' => 'Nurse • Clinical Coordinator'),
			),
		),
		array(
			'title'   => 'Animal Care',
			'icon'    => 'fa-paw',
			'careers' => array(
				array('title' => 'BVSc', 'duration' => '5.5 Years', 'entrance' => 'NEET', 'focus' => 'Animal diagnosis and treatment', 'paths' => 'Veterinary Doctor • Research Officer'),
				array('title' => 'Animal Husbandry', 'duration' => '4 Years', 'entrance' => 'State Entrance', 'focus' => 'Livestock management', 'paths' => 'Consultant • Livestock Manager'),
			),
		),
		array(
			'title'   => 'Medicines & Treatment',
			'icon'    => 'fa-pills',
			'careers' => array(
				array('title' => 'B.Pharmacy', 'duration' => '4 Years', 'entrance' => 'EAPCET', 'focus' => 'Medicines and pharmaceuticals', 'paths' => 'Pharmacist • Drug Research'),
				array('title' => 'Pharm D', 'duration' => '6 Years', 'entrance' => 'EAPCET', 'focus' => 'Clinical pharmacy', 'paths' => 'Clinical Pharmacist'),
			),
		),
		array(
			'title'   => 'Traditional Medicine',
			'icon'    => 'fa-leaf',
			'careers' => array(
				array('title' => 'BAMS', 'duration' => '5.5 Years', 'entrance' => 'NEET', 'focus' => 'Ayurvedic medicine', 'paths' => 'Ayurvedic Practitioner'),
				array('title' => 'BHMS', 'duration' => '5.5 Years', 'entrance' => 'NEET', 'focus' => 'Homeopathy', 'paths' => 'Homeopathic Doctor'),
			),
		),
		array(
			'title'   => 'Food & Agriculture',
			'icon'    => 'fa-seedling',
			'careers' => array(
				array('title' => 'B.Sc Agriculture', 'duration' => '4 Years', 'entrance' => 'EAPCET', 'focus' => 'Crop sciences and agriculture', 'paths' => 'Agriculture Officer • Scientist'),
			),
		),
		array(
			'title'   => 'Research & Life Sciences',
			'icon'    => 'fa-flask',
			'careers' => array(
				array('title' => 'Biotechnology', 'duration' => '4 Years', 'entrance' => 'Merit / Entrance', 'focus' => 'Biological research', 'paths' => 'Research Scientist • Biotech Professional'),
				array('title' => 'Microbiology', 'duration' => '3 Years', 'entrance' => 'Merit', 'focus' => 'Microorganisms and disease', 'paths' => 'Microbiologist'),
			),
		),
	);
}
