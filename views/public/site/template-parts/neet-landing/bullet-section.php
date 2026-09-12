<?php
/**
 * NEET landing — bullet list section (skill tests, regulations, residential)
 *
 * Expects global $vr_neet_section to be set before include.
 *
 * @package VR_Doctors
 */

global $vr_neet_section;
$slug = vr_neet_landing_slug();
if ($slug === '' || empty($vr_neet_section)) {
	return;
}
$s        = vr_get_page_section($slug, $vr_neet_section);
$features = !empty($s['features']) && is_array($s['features']) ? $s['features'] : array();
$bg       = !empty($GLOBALS['vr_neet_section_bg']) ? $GLOBALS['vr_neet_section_bg'] : 'bg-white';
if (empty($features)) {
	return;
}
?>
<section class="py-12 md:py-16 <?php echo esc_attr($bg); ?>">
	<div class="max-w-6xl mx-auto px-4 md:px-6">
		<h2 class="text-3xl md:text-4xl font-bold text-blue-900 leading-tight"><?php echo esc_html($s['title']); ?></h2>
		<ul class="mt-6 md:mt-8 grid md:grid-cols-2 gap-3 md:gap-4">
			<?php foreach ($features as $item) : ?>
				<li class="flex items-start gap-3 bg-white rounded-xl border border-gray-200 px-4 py-3.5 shadow-sm">
					<span class="mt-0.5 w-5 h-5 rounded-full bg-orange-100 flex items-center justify-center shrink-0"><i class="fa-solid fa-check text-orange-600 text-xs"></i></span>
					<span class="text-sm md:text-base text-gray-700 leading-relaxed"><?php echo esc_html($item); ?></span>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
