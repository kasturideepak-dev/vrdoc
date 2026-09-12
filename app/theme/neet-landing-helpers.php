<?php
/**
 * Front-end helpers for NEET landing page partials.
 *
 * @package VR_Doctors
 */

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Active NEET landing page slug from the including template.
 *
 * @return string
 */
function vr_neet_landing_slug() {
	global $vr_neet_slug;
	if (is_string($vr_neet_slug) && trim($vr_neet_slug) !== '') {
		return trim($vr_neet_slug);
	}

	// get_template_part() runs in a separate include scope — resolve from the current page.
	if (function_exists('vr_get_editable_page_slug') && is_singular('page')) {
		$current = get_queried_object();
		if ($current) {
			$slug = vr_get_editable_page_slug($current);
			if (is_string($slug) && in_array($slug, vr_neet_landing_page_slugs(), true)) {
				return $slug;
			}
		}
	}

	return '';
}

/**
 * Tailwind grid column class for a variable number of cards (1–3).
 *
 * @param int $count Item count.
 * @return string
 */
function vr_neet_grid_cols_class($count) {
	$count = max(1, min(3, (int) $count));
	$map   = array(
		1 => 'md:grid-cols-1',
		2 => 'md:grid-cols-2',
		3 => 'md:grid-cols-3',
	);
	return $map[ $count ];
}

/**
 * Drop FAQ rows missing a question or answer.
 *
 * @param array<int, array<string, string>> $items FAQ rows.
 * @return array<int, array<string, string>>
 */
function vr_neet_filter_faq_items($items) {
	$out = array();
	foreach ((array) $items as $item) {
		if (!is_array($item)) {
			continue;
		}
		$question = trim((string) ($item['question'] ?? ''));
		$answer   = trim((string) ($item['answer'] ?? ''));
		if ($question === '' || $answer === '') {
			continue;
		}
		$out[] = array(
			'question' => $question,
			'answer'   => $answer,
		);
	}
	return $out;
}

/**
 * Drop testimonial rows missing a quote.
 *
 * @param array<int, array<string, string>> $items Testimonial rows.
 * @return array<int, array<string, string>>
 */
function vr_neet_filter_testimonial_items($items) {
	$out = array();
	foreach ((array) $items as $item) {
		if (!is_array($item)) {
			continue;
		}
		$quote = trim((string) ($item['quote'] ?? ''));
		if ($quote === '') {
			continue;
		}
		$out[] = $item;
	}
	return $out;
}

/**
 * Drop repeater rows where every text column is empty.
 *
 * @param array<int, array<string, string>> $items  Rows.
 * @param string[]                          $keys   Required non-empty keys (any one).
 * @return array<int, array<string, string>>
 */
function vr_neet_filter_rows_any($items, $keys) {
	$out  = array();
	$keys = array_values(array_filter((array) $keys));
	foreach ((array) $items as $item) {
		if (!is_array($item)) {
			continue;
		}
		foreach ($keys as $key) {
			if (trim((string) ($item[ $key ] ?? '')) !== '') {
				$out[] = $item;
				break;
			}
		}
	}
	return $out;
}

/**
 * Set NEET landing context for shared partials (survives get_template_part scope).
 *
 * @param string      $slug       Page slug.
 * @param string|null $section    Optional bullet-section key.
 * @param string|null $section_bg Optional bullet-section background class.
 */
function vr_neet_set_context($slug, $section = null, $section_bg = null) {
	$GLOBALS['vr_neet_slug'] = trim((string) $slug);
	if (null !== $section) {
		$GLOBALS['vr_neet_section'] = trim((string) $section);
	}
	if (null !== $section_bg) {
		$GLOBALS['vr_neet_section_bg'] = trim((string) $section_bg);
	}
}

/**
 * Slugs for the three NEET course landing pages.
 *
 * @return string[]
 */
function vr_neet_landing_page_slugs() {
	return array(
		'best-bipc-college-in-hyderabad-neet-residential',
		'long-term-neet-program-hyderabad',
		'short-term-neet-program-hyderabad',
	);
}

/**
 * Active NEET landing page slug on the current request, if any.
 *
 * @return string
 */
function vr_neet_landing_slug_for_request() {
	foreach (vr_neet_landing_page_slugs() as $slug) {
		if (is_page($slug)) {
			return $slug;
		}
	}
	return '';
}

/**
 * Resolved SEO meta for a NEET landing page.
 *
 * @param string $slug Page slug.
 * @return array{meta_title: string, meta_description: string, og_title: string, og_description: string, canonical_url: string}
 */
function vr_neet_get_seo_meta($slug) {
	$seo = vr_get_page_section($slug, 'seo');
	return array(
		'meta_title'       => trim((string) ($seo['meta_title'] ?? '')),
		'meta_description' => trim((string) ($seo['meta_description'] ?? '')),
		'og_title'         => trim((string) ($seo['og_title'] ?? '')),
		'og_description'   => trim((string) ($seo['og_description'] ?? '')),
		'canonical_url'    => esc_url_raw(trim((string) ($seo['canonical_url'] ?? ''))) ?: home_url('/' . $slug . '/'),
	);
}

/**
 * Facility rows with backward compatibility for legacy list-only saves.
 *
 * @param array<string, mixed> $section Facilities section data.
 * @return array<int, array{icon: string, text: string}>
 */
function vr_neet_facility_items($section) {
	$items = vr_neet_filter_rows_any($section['items'] ?? array(), array('text'));
	if (!empty($items)) {
		return $items;
	}

	$features = !empty($section['features']) && is_array($section['features']) ? $section['features'] : array();
	$icons    = array('fa-building', 'fa-snowflake', 'fa-star', 'fa-chair', 'fa-heart-pulse', 'fa-utensils', 'fa-bus', 'fa-calendar-check');
	$out      = array();
	foreach ($features as $i => $text) {
		$text = trim((string) $text);
		if ($text === '') {
			continue;
		}
		$out[] = array(
			'icon' => $icons[ $i % count($icons) ],
			'text' => $text,
		);
	}
	return $out;
}

/**
 * Render hero CF7 form — per-page override or site-wide default.
 *
 * @param array<string, mixed> $hero Hero section data.
 */
function vr_neet_render_hero_cf7($hero) {
	if (class_exists('Theme')) {
		$course = (string) ($hero['interested_course'] ?? ($GLOBALS['vr_neet_slug'] ?? 'NEET Program'));
		Theme::enquiryForm('Landing hero', $course);
		return;
	}
	$override = trim((string) ($hero['form_cf7_shortcode'] ?? ''));
	if ($override !== '' && function_exists('vr_safe_cf7_shortcode') && vr_safe_cf7_shortcode($override, '')) {
		if (!shortcode_exists('contact-form-7')) {
			echo '<p class="text-sm text-red-600">' . esc_html__('Contact Form 7 is not active. Please activate the plugin.', 'vr-doctors') . '</p>';
			return;
		}
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CF7 renders its own escaped markup.
		echo do_shortcode($override);
		return;
	}
	vr_render_cf7('cf7_hero_shortcode');
}
