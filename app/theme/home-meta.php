<?php
/**
 * Homepage custom fields (no ACF).
 * Edit when the static front page is open in the admin.
 *
 * @package VR_Doctors
 */

if (!defined('ABSPATH')) {
	exit;
}

/** Max items per repeater (edge-case caps). */
define('VR_HOME_MAX_SLIDES', 5);
define('VR_HOME_MAX_STATS', 6);
define('VR_HOME_MAX_CAMPUS_TABS', 5);
define('VR_HOME_MAX_PHOTOS_PER_TAB', 6);
define('VR_HOME_MAX_FEATURES_PER_TAB', 4);
define('VR_HOME_MAX_APPROACH_STEPS', 8);

/**
 * Front page post ID used for homepage meta (0 if not set).
 */
function vr_home_page_id() {
	$id = (int) get_option('page_on_front');
	return $id > 0 ? $id : 0;
}

/**
 * Whether the current admin screen is the static homepage (or a page with slug home).
 * When a static front page is set, ONLY that page shows/saves homepage fields.
 *
 * @param int|WP_Post|null $post Post object or ID.
 */
function vr_is_home_edit_screen($post = null) {
	$post = get_post($post);
	if (!$post || 'page' !== $post->post_type) {
		return false;
	}
	$front = vr_home_page_id();
	// Prefer the Reading → Homepage page exclusively when configured.
	if ($front > 0) {
		return (int) $post->ID === $front;
	}
	// No static front page yet — allow pages named/slugged as home so content can be prepared.
	if (in_array($post->post_name, array('home', 'homepage', 'front-page'), true)) {
		return true;
	}
	$title = strtolower(trim($post->post_title));
	return in_array($title, array('home', 'homepage', 'front page'), true);
}

/**
 * Get raw homepage section meta from the front page.
 *
 * @param string $key Meta key without prefix, e.g. 'hero'.
 * @return array
 */
function vr_home_meta($key) {
	$page_id = vr_home_page_id();
	if (!$page_id) {
		// Fallback: page with slug home.
		$page = get_page_by_path('home');
		$page_id = $page ? (int) $page->ID : 0;
	}
	if (!$page_id) {
		return array();
	}
	$raw = get_post_meta($page_id, '_vr_home_' . $key, true);
	return is_array($raw) ? $raw : array();
}

/**
 * Resolve attachment ID or URL to a usable image URL.
 *
 * @param int|string $id_or_url Attachment ID or full URL.
 * @param string     $fallback  Fallback URL.
 */
function vr_home_image_url($id_or_url, $fallback = '') {
	if (is_numeric($id_or_url) && (int) $id_or_url > 0) {
		$url = wp_get_attachment_image_url((int) $id_or_url, 'full');
		if ($url) {
			return $url;
		}
	}
	if (is_string($id_or_url) && $id_or_url !== '') {
		$url = esc_url_raw($id_or_url);
		if ($url) {
			return $url;
		}
	}
	return $fallback;
}

/**
 * Non-empty string or default.
 */
function vr_home_str($value, $default = '') {
	if (!is_string($value) && !is_numeric($value)) {
		return $default;
	}
	$value = trim((string) $value);
	return $value !== '' ? $value : $default;
}

/* ─────────────────────────────────────────────
 * Public getters (meta + fallbacks)
 * ───────────────────────────────────────────── */

/**
 * Hero slides for the homepage carousel.
 *
 * @return array{slides: array, primary_cta_text: string, primary_cta_url: string, secondary_cta_text: string, secondary_cta_url: string}
 */
function vr_get_home_hero() {
	$defaults = vr_default_home_hero();
	$meta     = vr_home_meta('hero');

	$slides = array();
	if (!empty($meta['slides']) && is_array($meta['slides'])) {
		foreach (array_slice($meta['slides'], 0, VR_HOME_MAX_SLIDES) as $i => $slide) {
			if (!is_array($slide)) {
				continue;
			}
			$def = $defaults['slides'][ $i ] ?? $defaults['slides'][0];
			$title = vr_home_str($slide['title'] ?? '', '');
			// Skip completely empty rows (user added then left blank).
			$has_any = $title !== ''
				|| vr_home_str($slide['subtitle'] ?? '', '') !== ''
				|| vr_home_str($slide['description'] ?? '', '') !== ''
				|| !empty($slide['image_id'])
				|| vr_home_str($slide['image_url'] ?? '', '') !== '';
			if (!$has_any) {
				continue;
			}
			$image = vr_home_image_url(
				!empty($slide['image_id']) ? $slide['image_id'] : ($slide['image_url'] ?? ''),
				$def['image']
			);
			// Require at least a title or keep default title so carousel text is never blank.
			$slides[] = array(
				'image'       => $image,
				'title'       => vr_home_str($slide['title'] ?? '', $def['title']),
				'subtitle'    => vr_home_str($slide['subtitle'] ?? '', $def['subtitle']),
				'description' => vr_home_str($slide['description'] ?? '', $def['description']),
			);
		}
	}

	if (empty($slides)) {
		$slides = $defaults['slides'];
	}

	return array(
		'slides'             => $slides,
		'primary_cta_text'   => vr_home_str($meta['primary_cta_text'] ?? '', $defaults['primary_cta_text']),
		'primary_cta_url'    => vr_home_str($meta['primary_cta_url'] ?? '', $defaults['primary_cta_url']),
		'secondary_cta_text' => vr_home_str($meta['secondary_cta_text'] ?? '', $defaults['secondary_cta_text']),
		'secondary_cta_url'  => vr_home_str($meta['secondary_cta_url'] ?? '', $defaults['secondary_cta_url']),
	);
}

/**
 * Journey / stats section.
 */
function vr_get_home_journey() {
	$defaults = vr_default_home_journey();
	$meta     = vr_home_meta('journey');

	$stats = array();
	if (!empty($meta['stats']) && is_array($meta['stats'])) {
		foreach (array_slice($meta['stats'], 0, VR_HOME_MAX_STATS) as $i => $stat) {
			if (!is_array($stat)) {
				continue;
			}
			$label = vr_home_str($stat['label'] ?? '', '');
			// Allow target of 0; skip only fully empty rows.
			$raw_target = isset($stat['target']) ? $stat['target'] : '';
			if ($label === '' && ($raw_target === '' || $raw_target === null)) {
				continue;
			}
			$def = $defaults['stats'][ $i ] ?? array('target' => 0, 'suffix' => '', 'label' => '');
			$target = is_numeric($raw_target) ? (int) $raw_target : (int) $def['target'];
			if ($target < 0) {
				$target = 0;
			}
			$stats[] = array(
				'target' => $target,
				'suffix' => vr_home_str($stat['suffix'] ?? '', $def['suffix']),
				'label'  => vr_home_str($label, $def['label']),
			);
		}
	}
	if (empty($stats)) {
		$stats = $defaults['stats'];
	}

	return array(
		'eyebrow'     => vr_home_str($meta['eyebrow'] ?? '', $defaults['eyebrow']),
		'title'       => vr_home_str($meta['title'] ?? '', $defaults['title']),
		'description' => vr_home_str($meta['description'] ?? '', $defaults['description']),
		'footer_note' => vr_home_str($meta['footer_note'] ?? '', $defaults['footer_note']),
		'stats'       => $stats,
	);
}

/**
 * Testimonials section labels (items still come from CPT).
 */
function vr_get_home_testimonials_section() {
	$defaults = vr_default_home_testimonials_section();
	$meta     = vr_home_meta('testimonials');

	return array(
		'eyebrow'  => vr_home_str($meta['eyebrow'] ?? '', $defaults['eyebrow']),
		'title'    => vr_home_str($meta['title'] ?? '', $defaults['title']),
		'cta_text' => vr_home_str($meta['cta_text'] ?? '', $defaults['cta_text']),
		'cta_url'  => vr_home_str($meta['cta_url'] ?? '', $defaults['cta_url']),
	);
}

/**
 * Campus life section + tabs.
 */
function vr_get_home_campus() {
	$defaults = vr_default_home_campus();
	$meta     = vr_home_meta('campus');

	$categories = array();
	if (!empty($meta['categories']) && is_array($meta['categories'])) {
		foreach (array_slice($meta['categories'], 0, VR_HOME_MAX_CAMPUS_TABS) as $i => $cat) {
			if (!is_array($cat)) {
				continue;
			}
			$tab = vr_home_str($cat['tab'] ?? '', '');
			$def = $defaults['categories'][ $i ] ?? null;

			$photos = array();
			if (!empty($cat['photos']) && is_array($cat['photos'])) {
				foreach (array_slice($cat['photos'], 0, VR_HOME_MAX_PHOTOS_PER_TAB) as $pi => $photo) {
					if (!is_array($photo)) {
						continue;
					}
					$def_photo = ($def && !empty($def['photos'][ $pi ])) ? $def['photos'][ $pi ] : array('src' => '', 'alt' => '');
					$src       = vr_home_image_url(
						!empty($photo['image_id']) ? $photo['image_id'] : ($photo['image_url'] ?? ''),
						$def_photo['src']
					);
					if ($src === '') {
						continue;
					}
					$photos[] = array(
						'src' => $src,
						'alt' => vr_home_str($photo['alt'] ?? '', $def_photo['alt']),
					);
				}
			}
			if (empty($photos) && $def) {
				$photos = $def['photos'];
			}

			$features = array();
			if (!empty($cat['features']) && is_array($cat['features'])) {
				foreach (array_slice($cat['features'], 0, VR_HOME_MAX_FEATURES_PER_TAB) as $fi => $feature) {
					if (!is_array($feature)) {
						continue;
					}
					$ft = vr_home_str($feature['title'] ?? '', '');
					$fd = vr_home_str($feature['description'] ?? '', '');
					if ($ft === '' && $fd === '') {
						continue;
					}
					$def_f = ($def && !empty($def['features'][ $fi ])) ? $def['features'][ $fi ] : array('title' => '', 'description' => '');
					$features[] = array(
						'title'       => vr_home_str($ft, $def_f['title']),
						'description' => vr_home_str($fd, $def_f['description']),
					);
				}
			}
			if (empty($features) && $def) {
				$features = $def['features'];
			}

			// Skip tab with no name and no content.
			if ($tab === '' && empty($photos) && empty($features)) {
				continue;
			}
			if ($tab === '' && $def) {
				$tab = $def['tab'];
			}

			$categories[] = array(
				'tab'      => $tab,
				'photos'   => $photos,
				'features' => $features,
			);
		}
	}
	if (empty($categories)) {
		$categories = $defaults['categories'];
	}

	return array(
		'eyebrow'     => vr_home_str($meta['eyebrow'] ?? '', $defaults['eyebrow']),
		'title'       => vr_home_str($meta['title'] ?? '', $defaults['title']),
		'description' => vr_home_str($meta['description'] ?? '', $defaults['description']),
		'categories'  => $categories,
	);
}

/**
 * Approach section + steps.
 */
function vr_get_home_approach() {
	$defaults = vr_default_home_approach();
	$meta     = vr_home_meta('approach');

	$steps = array();
	if (!empty($meta['steps']) && is_array($meta['steps'])) {
		foreach (array_slice($meta['steps'], 0, VR_HOME_MAX_APPROACH_STEPS) as $i => $step) {
			if (!is_array($step)) {
				continue;
			}
			$title   = vr_home_str($step['title'] ?? '', '');
			$heading = vr_home_str($step['heading'] ?? '', '');
			$has_any = $title !== '' || $heading !== '' || !empty($step['image_id']) || vr_home_str($step['image_url'] ?? '', '') !== '' || vr_home_str($step['description'] ?? '', '') !== '';
			if (!$has_any) {
				continue;
			}
			$def = $defaults['steps'][ $i ] ?? $defaults['steps'][0];
			$steps[] = array(
				'title'       => vr_home_str($title, $def['title']),
				'heading'     => vr_home_str($heading, $def['heading']),
				'image'       => vr_home_image_url(
					!empty($step['image_id']) ? $step['image_id'] : ($step['image_url'] ?? ''),
					$def['image']
				),
				'description' => vr_home_str($step['description'] ?? '', $def['description']),
			);
		}
	}
	if (empty($steps)) {
		$steps = $defaults['steps'];
	}

	return array(
		'eyebrow'     => vr_home_str($meta['eyebrow'] ?? '', $defaults['eyebrow']),
		'title'       => vr_home_str($meta['title'] ?? '', $defaults['title']),
		'description' => vr_home_str($meta['description'] ?? '', $defaults['description']),
		'steps'       => $steps,
	);
}

/**
 * Campus visit CTA band.
 */
function vr_get_home_cta() {
	$defaults = vr_default_home_cta();
	$meta     = vr_home_meta('cta');

	return array(
		'eyebrow'             => vr_home_str($meta['eyebrow'] ?? '', $defaults['eyebrow']),
		'title'              => vr_home_str($meta['title'] ?? '', $defaults['title']),
		'description'        => vr_home_str($meta['description'] ?? '', $defaults['description']),
		'primary_cta_text'   => vr_home_str($meta['primary_cta_text'] ?? '', $defaults['primary_cta_text']),
		'primary_cta_url'    => vr_home_str($meta['primary_cta_url'] ?? '', $defaults['primary_cta_url']),
		'secondary_cta_text' => vr_home_str($meta['secondary_cta_text'] ?? '', $defaults['secondary_cta_text']),
		'secondary_cta_url'  => vr_home_str($meta['secondary_cta_url'] ?? '', $defaults['secondary_cta_url']),
	);
}

/**
 * Contact strip section labels (phones/email still from Settings).
 */
function vr_get_home_contact_strip() {
	$defaults = vr_default_home_contact_strip();
	$meta     = vr_home_meta('contact');

	return array(
		'eyebrow'         => vr_home_str($meta['eyebrow'] ?? '', $defaults['eyebrow']),
		'title'          => vr_home_str($meta['title'] ?? '', $defaults['title']),
		'description'    => vr_home_str($meta['description'] ?? '', $defaults['description']),
		'call_title'     => vr_home_str($meta['call_title'] ?? '', $defaults['call_title']),
		'email_title'    => vr_home_str($meta['email_title'] ?? '', $defaults['email_title']),
		'location_title' => vr_home_str($meta['location_title'] ?? '', $defaults['location_title']),
		'location_text'  => vr_home_str($meta['location_text'] ?? '', $defaults['location_text']),
		'maps_label'     => vr_home_str($meta['maps_label'] ?? '', $defaults['maps_label']),
	);
}

/* ─────────────────────────────────────────────
 * Defaults (mirrors current site copy)
 * ───────────────────────────────────────────── */

function vr_default_home_hero() {
	return array(
		'slides'             => function_exists('vr_hero_slides_fallback') ? vr_hero_slides_fallback() : array(),
		'primary_cta_text'   => 'Start Your Journey',
		'primary_cta_url'    => home_url('/contact/'),
		'secondary_cta_text' => 'Explore Our Approach',
		'secondary_cta_url'  => '#approach',
	);
}

function vr_default_home_journey() {
	return array(
		'eyebrow'     => 'Our Journey So Far',
		'title'       => 'Turning Vision Into Reality Since 2019',
		'description' => 'Since 2019, VR Doctors has been transforming aspirations into medical careers through disciplined preparation, residential learning and consistent results.',
		'footer_note' => 'Including MBBS, BDS and AYUSH admissions across India.',
		'stats'       => array(
			array('target' => 6, 'suffix' => '+', 'label' => 'Years of excellence'),
			array('target' => 6, 'suffix' => '', 'label' => 'Successful batches'),
			array('target' => 600, 'suffix' => '+', 'label' => 'Doctors produced'),
			array('target' => 101, 'suffix' => '+', 'label' => 'Medical seats in 2025'),
		),
	);
}

function vr_default_home_testimonials_section() {
	return array(
		'eyebrow'  => 'Testimonials',
		'title'    => 'What our students have to say',
		'cta_text' => 'View All Results →',
		'cta_url'  => home_url('/results/'),
	);
}

function vr_default_home_campus() {
	return array(
		'eyebrow'     => 'Student Experience',
		'title'       => 'Life At VR Doctors',
		'description' => 'More than a coaching institute. A residential ecosystem designed to help students learn, grow and achieve.',
		'categories'  => function_exists('vr_campus_categories_fallback') ? vr_campus_categories_fallback() : array(),
	);
}

function vr_default_home_approach() {
	return array(
		'eyebrow'     => 'Our Approach',
		'title'       => 'Learn → Practise → Perform → Analyse → Achieve',
		'description' => 'A structured methodology designed to transform student aspirations into medical careers.',
		'steps'       => function_exists('vr_approach_steps_fallback') ? vr_approach_steps_fallback() : array(),
	);
}

function vr_default_home_cta() {
	$phone = vr_phones();
	$tel   = !empty($phone[0]) ? 'tel:+' . preg_replace('/\D+/', '', $phone[0]) : 'tel:+919256925640';
	// Prefer display-formatted default if bare digits.
	if ($tel === 'tel:+9256925640' || $tel === 'tel:+919256925640' || strlen(preg_replace('/\D+/', '', $phone[0] ?? '')) === 10) {
		$digits = preg_replace('/\D+/', '', $phone[0] ?? '9256925640');
		if (strlen($digits) === 10) {
			$tel = 'tel:+91' . $digits;
		}
	}
	return array(
		'eyebrow'             => 'Admissions Open',
		'title'              => 'Experience VR Doctors In Person',
		'description'        => 'Visit our campus, interact with faculty, explore hostel facilities, experience the learning environment and discover why hundreds of students have transformed their dreams into medical careers.',
		'primary_cta_text'   => 'Book A Campus Visit',
		'primary_cta_url'    => home_url('/contact/'),
		'secondary_cta_text' => 'Call Admissions',
		'secondary_cta_url'  => $tel,
	);
}

function vr_default_home_contact_strip() {
	return array(
		'eyebrow'         => 'Visit & Connect',
		'title'          => 'Connect With VR Doctors',
		'description'    => "Whether you're a student, parent, alumnus or visitor, we'd love to hear from you and welcome you to our campus.",
		'call_title'     => 'Call Us',
		'email_title'    => 'Email Us',
		'location_title' => 'Campus Location',
		'location_text'  => 'Hyderabad, Telangana',
		'maps_label'     => 'Open In Maps',
	);
}

/* ─────────────────────────────────────────────
 * Admin: meta boxes
 * ───────────────────────────────────────────── */

function vr_doctors_add_home_meta_boxes() {
	global $post;
	if (!$post || !vr_is_home_edit_screen($post)) {
		// When adding meta boxes WP may not have $post yet on add_meta_boxes_page.
		$post_id = isset($_GET['post']) ? (int) $_GET['post'] : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if (!$post_id || !vr_is_home_edit_screen($post_id)) {
			return;
		}
	}

	$boxes = array(
		'vr_home_hero'         => array(__('Homepage — Hero Carousel', 'vr-doctors'), 'vr_home_hero_meta_cb'),
		'vr_home_journey'      => array(__('Homepage — Journey & Stats', 'vr-doctors'), 'vr_home_journey_meta_cb'),
		'vr_home_testimonials' => array(__('Homepage — Testimonials Section', 'vr-doctors'), 'vr_home_testimonials_meta_cb'),
		'vr_home_campus'       => array(__('Homepage — Campus Life', 'vr-doctors'), 'vr_home_campus_meta_cb'),
		'vr_home_approach'     => array(__('Homepage — Our Approach', 'vr-doctors'), 'vr_home_approach_meta_cb'),
		'vr_home_cta'          => array(__('Homepage — Campus Visit CTA', 'vr-doctors'), 'vr_home_cta_meta_cb'),
		'vr_home_contact'      => array(__('Homepage — Contact Strip', 'vr-doctors'), 'vr_home_contact_meta_cb'),
	);

	foreach ($boxes as $id => $cfg) {
		add_meta_box($id, $cfg[0], $cfg[1], 'page', 'normal', 'high');
	}
}
add_action('add_meta_boxes_page', 'vr_doctors_add_home_meta_boxes');

/**
 * Admin styles + media for homepage meta.
 */
function vr_doctors_home_admin_assets($hook) {
	if (!in_array($hook, array('post.php', 'post-new.php'), true)) {
		return;
	}
	$screen = get_current_screen();
	if (!$screen || 'page' !== $screen->post_type) {
		return;
	}
	global $post;
	if (!$post || !vr_is_home_edit_screen($post)) {
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
			'maxSlides'   => VR_HOME_MAX_SLIDES,
			'maxStats'    => VR_HOME_MAX_STATS,
			'maxCampus'   => VR_HOME_MAX_CAMPUS_TABS,
			'maxApproach' => VR_HOME_MAX_APPROACH_STEPS,
			'i18n'        => array(
				'selectImage' => __('Select image', 'vr-doctors'),
				'useImage'    => __('Use this image', 'vr-doctors'),
				'remove'      => __('Remove', 'vr-doctors'),
			),
		)
	);
}
add_action('admin_enqueue_scripts', 'vr_doctors_home_admin_assets');

/**
 * Shared nonce for homepage meta (once per edit screen).
 */
function vr_home_meta_nonce() {
	static $printed = false;
	if ($printed) {
		return;
	}
	$printed = true;
	wp_nonce_field('vr_save_home_meta', 'vr_home_meta_nonce');
}

/**
 * Ensure homepage save nonce is present even if meta boxes are hidden via Screen Options.
 */
function vr_doctors_home_nonce_field() {
	global $post;
	if (!$post || !vr_is_home_edit_screen($post)) {
		return;
	}
	vr_home_meta_nonce();
}
add_action('edit_form_after_title', 'vr_doctors_home_nonce_field');

function vr_home_field_text($name, $label, $value, $placeholder = '') {
	printf(
		'<p class="vr-field"><label><strong>%1$s</strong><br /><input type="text" class="widefat" name="%2$s" value="%3$s" placeholder="%4$s" /></label></p>',
		esc_html($label),
		esc_attr($name),
		esc_attr($value),
		esc_attr($placeholder)
	);
}

function vr_home_field_textarea($name, $label, $value, $rows = 3) {
	printf(
		'<p class="vr-field"><label><strong>%1$s</strong><br /><textarea class="widefat" rows="%4$d" name="%2$s">%3$s</textarea></label></p>',
		esc_html($label),
		esc_attr($name),
		esc_textarea($value),
		(int) $rows
	);
}

function vr_home_field_url($name, $label, $value, $placeholder = '') {
	printf(
		'<p class="vr-field"><label><strong>%1$s</strong><br /><input type="url" class="widefat" name="%2$s" value="%3$s" placeholder="%4$s" /></label></p>',
		esc_html($label),
		esc_attr($name),
		esc_attr($value),
		esc_attr($placeholder)
	);
}

/**
 * Image picker field (stores attachment ID + shows preview).
 *
 * @param string $name_base e.g. vr_home_hero[slides][0]
 * @param int    $image_id  Attachment ID.
 * @param string $label     Field label.
 */
function vr_home_field_image($name_base, $image_id, $label = 'Image') {
	$image_id = absint($image_id);
	$url      = $image_id ? wp_get_attachment_image_url($image_id, 'medium') : '';
	?>
	<div class="vr-image-field" data-vr-image>
		<label><strong><?php echo esc_html($label); ?></strong></label>
		<input type="hidden" name="<?php echo esc_attr($name_base); ?>[image_id]" value="<?php echo esc_attr((string) $image_id); ?>" data-vr-image-id />
		<div class="vr-image-preview" data-vr-image-preview>
			<?php if ($url) : ?>
				<img src="<?php echo esc_url($url); ?>" alt="" />
			<?php endif; ?>
		</div>
		<p class="vr-image-actions">
			<button type="button" class="button" data-vr-image-select><?php esc_html_e('Select image', 'vr-doctors'); ?></button>
			<button type="button" class="button" data-vr-image-clear <?php echo $image_id ? '' : 'style="display:none"'; ?>><?php esc_html_e('Remove', 'vr-doctors'); ?></button>
		</p>
		<p class="description"><?php esc_html_e('Leave empty to keep the theme default image for this row.', 'vr-doctors'); ?></p>
	</div>
	<?php
}

function vr_home_hero_meta_cb($post) {
	// Nonce also printed on edit_form_after_title so save works if this box is hidden.
	$saved    = get_post_meta($post->ID, '_vr_home_hero', true);
	$saved    = is_array($saved) ? $saved : array();
	$defaults = vr_default_home_hero();
	$slides   = !empty($saved['slides']) && is_array($saved['slides']) ? $saved['slides'] : array();
	// Seed empty admin form with one blank row if nothing saved (defaults still show on front).
	if (empty($slides)) {
		foreach ($defaults['slides'] as $d) {
			$slides[] = array(
				'image_id'    => 0,
				'title'       => $d['title'],
				'subtitle'    => $d['subtitle'],
				'description' => $d['description'],
			);
		}
	}
	$primary_text   = $saved['primary_cta_text'] ?? $defaults['primary_cta_text'];
	$primary_url    = $saved['primary_cta_url'] ?? $defaults['primary_cta_url'];
	$secondary_text = $saved['secondary_cta_text'] ?? $defaults['secondary_cta_text'];
	$secondary_url  = $saved['secondary_cta_url'] ?? $defaults['secondary_cta_url'];
	?>
	<div class="vr-home-meta">
		<p class="description"><?php esc_html_e('Carousel slides (max 5). Empty rows are ignored. If all slides are empty, built-in defaults are used on the site.', 'vr-doctors'); ?></p>
		<div data-vr-repeater data-vr-max="<?php echo esc_attr((string) VR_HOME_MAX_SLIDES); ?>" data-vr-key="slides">
			<div data-vr-rows>
				<?php foreach ($slides as $i => $slide) : ?>
					<div class="vr-repeater-row" data-vr-row>
						<div class="vr-row-header">
							<strong><?php printf(esc_html__('Slide %d', 'vr-doctors'), (int) $i + 1); ?></strong>
							<button type="button" class="button-link-delete" data-vr-remove-row><?php esc_html_e('Remove slide', 'vr-doctors'); ?></button>
						</div>
						<?php
						vr_home_field_image('vr_home_hero[slides][' . (int) $i . ']', (int) ($slide['image_id'] ?? 0));
						vr_home_field_text('vr_home_hero[slides][' . (int) $i . '][title]', __('Title', 'vr-doctors'), $slide['title'] ?? '');
						vr_home_field_text('vr_home_hero[slides][' . (int) $i . '][subtitle]', __('Subtitle', 'vr-doctors'), $slide['subtitle'] ?? '');
						vr_home_field_textarea('vr_home_hero[slides][' . (int) $i . '][description]', __('Description', 'vr-doctors'), $slide['description'] ?? '');
						?>
					</div>
				<?php endforeach; ?>
			</div>
			<p><button type="button" class="button" data-vr-add-row><?php esc_html_e('Add slide', 'vr-doctors'); ?></button></p>
			<template data-vr-template>
				<div class="vr-repeater-row" data-vr-row>
					<div class="vr-row-header">
						<strong><?php esc_html_e('Slide', 'vr-doctors'); ?></strong>
						<button type="button" class="button-link-delete" data-vr-remove-row><?php esc_html_e('Remove slide', 'vr-doctors'); ?></button>
					</div>
					<?php
					vr_home_field_image('vr_home_hero[slides][__i__]', 0);
					vr_home_field_text('vr_home_hero[slides][__i__][title]', __('Title', 'vr-doctors'), '');
					vr_home_field_text('vr_home_hero[slides][__i__][subtitle]', __('Subtitle', 'vr-doctors'), '');
					vr_home_field_textarea('vr_home_hero[slides][__i__][description]', __('Description', 'vr-doctors'), '');
					?>
				</div>
			</template>
		</div>
		<hr />
		<h4><?php esc_html_e('Buttons (shared across slides)', 'vr-doctors'); ?></h4>
		<div class="vr-grid-2">
			<?php
			vr_home_field_text('vr_home_hero[primary_cta_text]', __('Primary button text', 'vr-doctors'), $primary_text);
			vr_home_field_text('vr_home_hero[primary_cta_url]', __('Primary button URL', 'vr-doctors'), $primary_url, '/contact/');
			vr_home_field_text('vr_home_hero[secondary_cta_text]', __('Secondary button text', 'vr-doctors'), $secondary_text);
			vr_home_field_text('vr_home_hero[secondary_cta_url]', __('Secondary button URL', 'vr-doctors'), $secondary_url, '#approach');
			?>
		</div>
	</div>
	<?php
}

function vr_home_journey_meta_cb($post) {
	vr_home_meta_nonce();
	$saved    = get_post_meta($post->ID, '_vr_home_journey', true);
	$saved    = is_array($saved) ? $saved : array();
	$defaults = vr_default_home_journey();
	$stats    = !empty($saved['stats']) && is_array($saved['stats']) ? $saved['stats'] : $defaults['stats'];
	?>
	<div class="vr-home-meta">
		<?php
		vr_home_field_text('vr_home_journey[eyebrow]', __('Eyebrow label', 'vr-doctors'), $saved['eyebrow'] ?? $defaults['eyebrow']);
		vr_home_field_text('vr_home_journey[title]', __('Heading', 'vr-doctors'), $saved['title'] ?? $defaults['title']);
		vr_home_field_textarea('vr_home_journey[description]', __('Description', 'vr-doctors'), $saved['description'] ?? $defaults['description']);
		vr_home_field_text('vr_home_journey[footer_note]', __('Footer note', 'vr-doctors'), $saved['footer_note'] ?? $defaults['footer_note']);
		?>
		<h4><?php esc_html_e('Stats counters', 'vr-doctors'); ?></h4>
		<p class="description"><?php esc_html_e('Target must be a non-negative integer. Suffix is optional (e.g. +).', 'vr-doctors'); ?></p>
		<div data-vr-repeater data-vr-max="<?php echo esc_attr((string) VR_HOME_MAX_STATS); ?>">
			<div data-vr-rows>
				<?php foreach ($stats as $i => $stat) : ?>
					<div class="vr-repeater-row vr-repeater-row--compact" data-vr-row>
						<div class="vr-row-header">
							<strong><?php printf(esc_html__('Stat %d', 'vr-doctors'), (int) $i + 1); ?></strong>
							<button type="button" class="button-link-delete" data-vr-remove-row><?php esc_html_e('Remove', 'vr-doctors'); ?></button>
						</div>
						<div class="vr-grid-3">
							<p class="vr-field"><label><strong><?php esc_html_e('Number', 'vr-doctors'); ?></strong><br />
								<input type="number" min="0" step="1" class="widefat" name="vr_home_journey[stats][<?php echo (int) $i; ?>][target]" value="<?php echo esc_attr((string) ($stat['target'] ?? 0)); ?>" />
							</label></p>
							<p class="vr-field"><label><strong><?php esc_html_e('Suffix', 'vr-doctors'); ?></strong><br />
								<input type="text" class="widefat" name="vr_home_journey[stats][<?php echo (int) $i; ?>][suffix]" value="<?php echo esc_attr($stat['suffix'] ?? ''); ?>" placeholder="+" />
							</label></p>
							<p class="vr-field"><label><strong><?php esc_html_e('Label', 'vr-doctors'); ?></strong><br />
								<input type="text" class="widefat" name="vr_home_journey[stats][<?php echo (int) $i; ?>][label]" value="<?php echo esc_attr($stat['label'] ?? ''); ?>" />
							</label></p>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
			<p><button type="button" class="button" data-vr-add-row><?php esc_html_e('Add stat', 'vr-doctors'); ?></button></p>
			<template data-vr-template>
				<div class="vr-repeater-row vr-repeater-row--compact" data-vr-row>
					<div class="vr-row-header">
						<strong><?php esc_html_e('Stat', 'vr-doctors'); ?></strong>
						<button type="button" class="button-link-delete" data-vr-remove-row><?php esc_html_e('Remove', 'vr-doctors'); ?></button>
					</div>
					<div class="vr-grid-3">
						<p class="vr-field"><label><strong><?php esc_html_e('Number', 'vr-doctors'); ?></strong><br />
							<input type="number" min="0" step="1" class="widefat" name="vr_home_journey[stats][__i__][target]" value="0" />
						</label></p>
						<p class="vr-field"><label><strong><?php esc_html_e('Suffix', 'vr-doctors'); ?></strong><br />
							<input type="text" class="widefat" name="vr_home_journey[stats][__i__][suffix]" value="" placeholder="+" />
						</label></p>
						<p class="vr-field"><label><strong><?php esc_html_e('Label', 'vr-doctors'); ?></strong><br />
							<input type="text" class="widefat" name="vr_home_journey[stats][__i__][label]" value="" />
						</label></p>
					</div>
				</div>
			</template>
		</div>
	</div>
	<?php
}

function vr_home_testimonials_meta_cb($post) {
	$saved    = get_post_meta($post->ID, '_vr_home_testimonials', true);
	$saved    = is_array($saved) ? $saved : array();
	$defaults = vr_default_home_testimonials_section();
	?>
	<div class="vr-home-meta">
		<p class="description"><?php esc_html_e('Section headings only. Individual testimonials are managed under Testimonials in the admin menu.', 'vr-doctors'); ?></p>
		<?php
		vr_home_field_text('vr_home_testimonials[eyebrow]', __('Eyebrow label', 'vr-doctors'), $saved['eyebrow'] ?? $defaults['eyebrow']);
		vr_home_field_text('vr_home_testimonials[title]', __('Heading', 'vr-doctors'), $saved['title'] ?? $defaults['title']);
		vr_home_field_text('vr_home_testimonials[cta_text]', __('Button text', 'vr-doctors'), $saved['cta_text'] ?? $defaults['cta_text']);
		vr_home_field_text('vr_home_testimonials[cta_url]', __('Button URL', 'vr-doctors'), $saved['cta_url'] ?? $defaults['cta_url']);
		?>
	</div>
	<?php
}

function vr_home_campus_meta_cb($post) {
	$saved    = get_post_meta($post->ID, '_vr_home_campus', true);
	$saved    = is_array($saved) ? $saved : array();
	$defaults = vr_default_home_campus();
	$cats     = !empty($saved['categories']) && is_array($saved['categories']) ? $saved['categories'] : array();

	if (empty($cats)) {
		// Pre-fill from defaults so editors can tweak without retyping.
		foreach ($defaults['categories'] as $d) {
			$photos = array();
			foreach ($d['photos'] as $ph) {
				$photos[] = array('image_id' => 0, 'alt' => $ph['alt']);
			}
			$cats[] = array(
				'tab'      => $d['tab'],
				'photos'   => $photos,
				'features' => $d['features'],
			);
		}
	}
	?>
	<div class="vr-home-meta">
		<?php
		vr_home_field_text('vr_home_campus[eyebrow]', __('Eyebrow label', 'vr-doctors'), $saved['eyebrow'] ?? $defaults['eyebrow']);
		vr_home_field_text('vr_home_campus[title]', __('Heading', 'vr-doctors'), $saved['title'] ?? $defaults['title']);
		vr_home_field_textarea('vr_home_campus[description]', __('Description', 'vr-doctors'), $saved['description'] ?? $defaults['description']);
		?>
		<p class="description"><?php esc_html_e('Each tab can have photos and feature cards. Leave image empty to keep the theme default photo for that slot.', 'vr-doctors'); ?></p>
		<div data-vr-repeater data-vr-max="<?php echo esc_attr((string) VR_HOME_MAX_CAMPUS_TABS); ?>">
			<div data-vr-rows>
				<?php foreach ($cats as $i => $cat) : ?>
					<div class="vr-repeater-row" data-vr-row>
						<div class="vr-row-header">
							<strong><?php printf(esc_html__('Tab %d', 'vr-doctors'), (int) $i + 1); ?></strong>
							<button type="button" class="button-link-delete" data-vr-remove-row><?php esc_html_e('Remove tab', 'vr-doctors'); ?></button>
						</div>
						<?php vr_home_field_text('vr_home_campus[categories][' . (int) $i . '][tab]', __('Tab label', 'vr-doctors'), $cat['tab'] ?? ''); ?>
						<h4><?php esc_html_e('Photos', 'vr-doctors'); ?></h4>
						<?php
						$photos = !empty($cat['photos']) && is_array($cat['photos']) ? $cat['photos'] : array(array(), array(), array(), array());
						$photos = array_slice(array_pad($photos, 4, array()), 0, VR_HOME_MAX_PHOTOS_PER_TAB);
						foreach ($photos as $pi => $photo) :
							?>
							<div class="vr-subrow">
								<?php
								vr_home_field_image('vr_home_campus[categories][' . (int) $i . '][photos][' . (int) $pi . ']', (int) ($photo['image_id'] ?? 0), sprintf(/* translators: photo index */ __('Photo %d', 'vr-doctors'), $pi + 1));
								vr_home_field_text('vr_home_campus[categories][' . (int) $i . '][photos][' . (int) $pi . '][alt]', __('Alt text', 'vr-doctors'), $photo['alt'] ?? '');
								?>
							</div>
						<?php endforeach; ?>
						<h4><?php esc_html_e('Feature cards', 'vr-doctors'); ?></h4>
						<?php
						$features = !empty($cat['features']) && is_array($cat['features']) ? $cat['features'] : array(array('title' => '', 'description' => ''), array('title' => '', 'description' => ''));
						$features = array_slice(array_pad($features, 2, array('title' => '', 'description' => '')), 0, VR_HOME_MAX_FEATURES_PER_TAB);
						foreach ($features as $fi => $feature) :
							?>
							<div class="vr-subrow">
								<?php
								vr_home_field_text('vr_home_campus[categories][' . (int) $i . '][features][' . (int) $fi . '][title]', sprintf(/* translators: feature index */ __('Feature %d title', 'vr-doctors'), $fi + 1), $feature['title'] ?? '');
								vr_home_field_textarea('vr_home_campus[categories][' . (int) $i . '][features][' . (int) $fi . '][description]', sprintf(/* translators: feature index */ __('Feature %d description', 'vr-doctors'), $fi + 1), $feature['description'] ?? '', 2);
								?>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endforeach; ?>
			</div>
			<p class="description"><?php esc_html_e('To add a new tab, save after editing existing ones, or duplicate structure carefully. Max 5 tabs.', 'vr-doctors'); ?></p>
		</div>
	</div>
	<?php
}

function vr_home_approach_meta_cb($post) {
	$saved    = get_post_meta($post->ID, '_vr_home_approach', true);
	$saved    = is_array($saved) ? $saved : array();
	$defaults = vr_default_home_approach();
	$steps    = !empty($saved['steps']) && is_array($saved['steps']) ? $saved['steps'] : array();
	if (empty($steps)) {
		foreach ($defaults['steps'] as $d) {
			$steps[] = array(
				'image_id'    => 0,
				'title'       => $d['title'],
				'heading'     => $d['heading'],
				'description' => $d['description'],
			);
		}
	}
	?>
	<div class="vr-home-meta">
		<?php
		vr_home_field_text('vr_home_approach[eyebrow]', __('Eyebrow label', 'vr-doctors'), $saved['eyebrow'] ?? $defaults['eyebrow']);
		vr_home_field_text('vr_home_approach[title]', __('Heading', 'vr-doctors'), $saved['title'] ?? $defaults['title']);
		vr_home_field_textarea('vr_home_approach[description]', __('Description', 'vr-doctors'), $saved['description'] ?? $defaults['description']);
		?>
		<div data-vr-repeater data-vr-max="<?php echo esc_attr((string) VR_HOME_MAX_APPROACH_STEPS); ?>">
			<div data-vr-rows>
				<?php foreach ($steps as $i => $step) : ?>
					<div class="vr-repeater-row" data-vr-row>
						<div class="vr-row-header">
							<strong><?php printf(esc_html__('Step %d', 'vr-doctors'), (int) $i + 1); ?></strong>
							<button type="button" class="button-link-delete" data-vr-remove-row><?php esc_html_e('Remove step', 'vr-doctors'); ?></button>
						</div>
						<?php
						vr_home_field_image('vr_home_approach[steps][' . (int) $i . ']', (int) ($step['image_id'] ?? 0));
						vr_home_field_text('vr_home_approach[steps][' . (int) $i . '][title]', __('Tab / short title (e.g. LEARN)', 'vr-doctors'), $step['title'] ?? '');
						vr_home_field_text('vr_home_approach[steps][' . (int) $i . '][heading]', __('Heading', 'vr-doctors'), $step['heading'] ?? '');
						vr_home_field_textarea('vr_home_approach[steps][' . (int) $i . '][description]', __('Description', 'vr-doctors'), $step['description'] ?? '');
						?>
					</div>
				<?php endforeach; ?>
			</div>
			<p><button type="button" class="button" data-vr-add-row><?php esc_html_e('Add step', 'vr-doctors'); ?></button></p>
			<template data-vr-template>
				<div class="vr-repeater-row" data-vr-row>
					<div class="vr-row-header">
						<strong><?php esc_html_e('Step', 'vr-doctors'); ?></strong>
						<button type="button" class="button-link-delete" data-vr-remove-row><?php esc_html_e('Remove step', 'vr-doctors'); ?></button>
					</div>
					<?php
					vr_home_field_image('vr_home_approach[steps][__i__]', 0);
					vr_home_field_text('vr_home_approach[steps][__i__][title]', __('Tab / short title (e.g. LEARN)', 'vr-doctors'), '');
					vr_home_field_text('vr_home_approach[steps][__i__][heading]', __('Heading', 'vr-doctors'), '');
					vr_home_field_textarea('vr_home_approach[steps][__i__][description]', __('Description', 'vr-doctors'), '');
					?>
				</div>
			</template>
		</div>
	</div>
	<?php
}

function vr_home_cta_meta_cb($post) {
	$saved    = get_post_meta($post->ID, '_vr_home_cta', true);
	$saved    = is_array($saved) ? $saved : array();
	$defaults = vr_default_home_cta();
	?>
	<div class="vr-home-meta">
		<?php
		vr_home_field_text('vr_home_cta[eyebrow]', __('Eyebrow label', 'vr-doctors'), $saved['eyebrow'] ?? $defaults['eyebrow']);
		vr_home_field_text('vr_home_cta[title]', __('Heading', 'vr-doctors'), $saved['title'] ?? $defaults['title']);
		vr_home_field_textarea('vr_home_cta[description]', __('Description', 'vr-doctors'), $saved['description'] ?? $defaults['description']);
		vr_home_field_text('vr_home_cta[primary_cta_text]', __('Primary button text', 'vr-doctors'), $saved['primary_cta_text'] ?? $defaults['primary_cta_text']);
		vr_home_field_text('vr_home_cta[primary_cta_url]', __('Primary button URL', 'vr-doctors'), $saved['primary_cta_url'] ?? $defaults['primary_cta_url']);
		vr_home_field_text('vr_home_cta[secondary_cta_text]', __('Secondary button text', 'vr-doctors'), $saved['secondary_cta_text'] ?? $defaults['secondary_cta_text']);
		vr_home_field_text('vr_home_cta[secondary_cta_url]', __('Secondary button URL (e.g. tel:+919256925640)', 'vr-doctors'), $saved['secondary_cta_url'] ?? $defaults['secondary_cta_url']);
		?>
	</div>
	<?php
}

function vr_home_contact_meta_cb($post) {
	$saved    = get_post_meta($post->ID, '_vr_home_contact', true);
	$saved    = is_array($saved) ? $saved : array();
	$defaults = vr_default_home_contact_strip();
	?>
	<div class="vr-home-meta">
		<p class="description"><?php esc_html_e('Section copy only. Phone numbers, email and maps URL are edited under Settings → VR Doctors.', 'vr-doctors'); ?></p>
		<?php
		vr_home_field_text('vr_home_contact[eyebrow]', __('Eyebrow label', 'vr-doctors'), $saved['eyebrow'] ?? $defaults['eyebrow']);
		vr_home_field_text('vr_home_contact[title]', __('Heading', 'vr-doctors'), $saved['title'] ?? $defaults['title']);
		vr_home_field_textarea('vr_home_contact[description]', __('Description', 'vr-doctors'), $saved['description'] ?? $defaults['description']);
		vr_home_field_text('vr_home_contact[call_title]', __('Call card title', 'vr-doctors'), $saved['call_title'] ?? $defaults['call_title']);
		vr_home_field_text('vr_home_contact[email_title]', __('Email card title', 'vr-doctors'), $saved['email_title'] ?? $defaults['email_title']);
		vr_home_field_text('vr_home_contact[location_title]', __('Location card title', 'vr-doctors'), $saved['location_title'] ?? $defaults['location_title']);
		vr_home_field_text('vr_home_contact[location_text]', __('Location text', 'vr-doctors'), $saved['location_text'] ?? $defaults['location_text']);
		vr_home_field_text('vr_home_contact[maps_label]', __('Maps button label', 'vr-doctors'), $saved['maps_label'] ?? $defaults['maps_label']);
		?>
	</div>
	<?php
}

/* ─────────────────────────────────────────────
 * Save
 * ───────────────────────────────────────────── */

function vr_doctors_save_home_meta($post_id) {
	// Autosave / revisions / bulk.
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return;
	}
	if (wp_is_post_revision($post_id) || wp_is_post_autosave($post_id)) {
		return;
	}
	if (!isset($_POST['vr_home_meta_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['vr_home_meta_nonce'])), 'vr_save_home_meta')) {
		return;
	}
	if (!current_user_can('edit_page', $post_id)) {
		return;
	}
	if ('page' !== get_post_type($post_id)) {
		return;
	}
	// Only persist on homepage (or slug home).
	if (!vr_is_home_edit_screen($post_id)) {
		return;
	}

	// Hero.
	if (isset($_POST['vr_home_hero']) && is_array($_POST['vr_home_hero'])) {
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized in helper.
		$raw = wp_unslash($_POST['vr_home_hero']);
		update_post_meta($post_id, '_vr_home_hero', vr_sanitize_home_hero($raw));
	}

	// Journey.
	if (isset($_POST['vr_home_journey']) && is_array($_POST['vr_home_journey'])) {
		$raw = wp_unslash($_POST['vr_home_journey']); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		update_post_meta($post_id, '_vr_home_journey', vr_sanitize_home_journey($raw));
	}

	// Testimonials section.
	if (isset($_POST['vr_home_testimonials']) && is_array($_POST['vr_home_testimonials'])) {
		$raw = wp_unslash($_POST['vr_home_testimonials']); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		update_post_meta($post_id, '_vr_home_testimonials', vr_sanitize_home_simple_section($raw, array('eyebrow', 'title', 'cta_text', 'cta_url')));
	}

	// Campus.
	if (isset($_POST['vr_home_campus']) && is_array($_POST['vr_home_campus'])) {
		$raw = wp_unslash($_POST['vr_home_campus']); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		update_post_meta($post_id, '_vr_home_campus', vr_sanitize_home_campus($raw));
	}

	// Approach.
	if (isset($_POST['vr_home_approach']) && is_array($_POST['vr_home_approach'])) {
		$raw = wp_unslash($_POST['vr_home_approach']); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		update_post_meta($post_id, '_vr_home_approach', vr_sanitize_home_approach($raw));
	}

	// CTA.
	if (isset($_POST['vr_home_cta']) && is_array($_POST['vr_home_cta'])) {
		$raw = wp_unslash($_POST['vr_home_cta']); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		update_post_meta(
			$post_id,
			'_vr_home_cta',
			vr_sanitize_home_simple_section(
				$raw,
				array('eyebrow', 'title', 'description', 'primary_cta_text', 'primary_cta_url', 'secondary_cta_text', 'secondary_cta_url')
			)
		);
	}

	// Contact strip.
	if (isset($_POST['vr_home_contact']) && is_array($_POST['vr_home_contact'])) {
		$raw = wp_unslash($_POST['vr_home_contact']); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		update_post_meta(
			$post_id,
			'_vr_home_contact',
			vr_sanitize_home_simple_section(
				$raw,
				array('eyebrow', 'title', 'description', 'call_title', 'email_title', 'location_title', 'location_text', 'maps_label')
			)
		);
	}
}
add_action('save_post_page', 'vr_doctors_save_home_meta');

/**
 * Sanitize a flat section of text fields.
 *
 * @param array $raw  Input.
 * @param array $keys Allowed keys.
 */
function vr_sanitize_home_simple_section($raw, $keys) {
	$out = array();
	if (!is_array($raw)) {
		return $out;
	}
	$url_keys = array('cta_url', 'primary_cta_url', 'secondary_cta_url');
	$long     = array('description');
	foreach ($keys as $key) {
		if (!isset($raw[ $key ])) {
			$out[ $key ] = '';
			continue;
		}
		$val = is_string($raw[ $key ]) || is_numeric($raw[ $key ]) ? (string) $raw[ $key ] : '';
		if (in_array($key, $url_keys, true)) {
			// Allow anchors (#approach) and tel: / relative paths.
			$val = trim($val);
			if ($val === '') {
				$out[ $key ] = '';
			} elseif (str_starts_with($val, '#') || str_starts_with($val, 'tel:') || str_starts_with($val, 'mailto:')) {
				$out[ $key ] = sanitize_text_field($val);
			} elseif (str_starts_with($val, '/')) {
				// Keep site-relative paths as-is (e.g. /contact/).
				$out[ $key ] = sanitize_text_field($val);
			} else {
				$san = esc_url_raw($val);
				$out[ $key ] = $san ? $san : sanitize_text_field($val);
			}
		} elseif (in_array($key, $long, true)) {
			$out[ $key ] = sanitize_textarea_field($val);
		} else {
			$out[ $key ] = sanitize_text_field($val);
		}
	}
	return $out;
}

function vr_sanitize_home_hero($raw) {
	$out = vr_sanitize_home_simple_section(
		$raw,
		array('primary_cta_text', 'primary_cta_url', 'secondary_cta_text', 'secondary_cta_url')
	);
	$out['slides'] = array();
	if (empty($raw['slides']) || !is_array($raw['slides'])) {
		return $out;
	}
	foreach (array_slice($raw['slides'], 0, VR_HOME_MAX_SLIDES) as $slide) {
		if (!is_array($slide)) {
			continue;
		}
		$out['slides'][] = array(
			'image_id'    => absint($slide['image_id'] ?? 0),
			'title'       => sanitize_text_field($slide['title'] ?? ''),
			'subtitle'    => sanitize_text_field($slide['subtitle'] ?? ''),
			'description' => sanitize_textarea_field($slide['description'] ?? ''),
		);
	}
	return $out;
}

function vr_sanitize_home_journey($raw) {
	$out = vr_sanitize_home_simple_section($raw, array('eyebrow', 'title', 'description', 'footer_note'));
	$out['stats'] = array();
	if (empty($raw['stats']) || !is_array($raw['stats'])) {
		return $out;
	}
	foreach (array_slice($raw['stats'], 0, VR_HOME_MAX_STATS) as $stat) {
		if (!is_array($stat)) {
			continue;
		}
		$target = isset($stat['target']) && is_numeric($stat['target']) ? (int) $stat['target'] : 0;
		if ($target < 0) {
			$target = 0;
		}
		// Cap absurd values (counter UX).
		if ($target > 999999) {
			$target = 999999;
		}
		$out['stats'][] = array(
			'target' => $target,
			'suffix' => sanitize_text_field($stat['suffix'] ?? ''),
			'label'  => sanitize_text_field($stat['label'] ?? ''),
		);
	}
	return $out;
}

function vr_sanitize_home_campus($raw) {
	$out = vr_sanitize_home_simple_section($raw, array('eyebrow', 'title', 'description'));
	$out['categories'] = array();
	if (empty($raw['categories']) || !is_array($raw['categories'])) {
		return $out;
	}
	foreach (array_slice($raw['categories'], 0, VR_HOME_MAX_CAMPUS_TABS) as $cat) {
		if (!is_array($cat)) {
			continue;
		}
		$photos = array();
		if (!empty($cat['photos']) && is_array($cat['photos'])) {
			foreach (array_slice($cat['photos'], 0, VR_HOME_MAX_PHOTOS_PER_TAB) as $photo) {
				if (!is_array($photo)) {
					continue;
				}
				$photos[] = array(
					'image_id' => absint($photo['image_id'] ?? 0),
					'alt'      => sanitize_text_field($photo['alt'] ?? ''),
				);
			}
		}
		$features = array();
		if (!empty($cat['features']) && is_array($cat['features'])) {
			foreach (array_slice($cat['features'], 0, VR_HOME_MAX_FEATURES_PER_TAB) as $feature) {
				if (!is_array($feature)) {
					continue;
				}
				$features[] = array(
					'title'       => sanitize_text_field($feature['title'] ?? ''),
					'description' => sanitize_textarea_field($feature['description'] ?? ''),
				);
			}
		}
		$out['categories'][] = array(
			'tab'      => sanitize_text_field($cat['tab'] ?? ''),
			'photos'   => $photos,
			'features' => $features,
		);
	}
	return $out;
}

function vr_sanitize_home_approach($raw) {
	$out = vr_sanitize_home_simple_section($raw, array('eyebrow', 'title', 'description'));
	$out['steps'] = array();
	if (empty($raw['steps']) || !is_array($raw['steps'])) {
		return $out;
	}
	foreach (array_slice($raw['steps'], 0, VR_HOME_MAX_APPROACH_STEPS) as $step) {
		if (!is_array($step)) {
			continue;
		}
		$out['steps'][] = array(
			'image_id'    => absint($step['image_id'] ?? 0),
			'title'       => sanitize_text_field($step['title'] ?? ''),
			'heading'     => sanitize_text_field($step['heading'] ?? ''),
			'description' => sanitize_textarea_field($step['description'] ?? ''),
		);
	}
	return $out;
}
