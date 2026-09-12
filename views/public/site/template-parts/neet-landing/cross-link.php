<?php
/**
 * NEET landing — cross-link banner
 *
 * @package VR_Doctors
 */

$slug = vr_neet_landing_slug();
if ($slug === '') {
	return;
}
$s = vr_get_page_section($slug, 'cross_link');
if (in_array(strtolower(trim((string) ($s['show'] ?? 'yes'))), array('no', '0', 'false', 'hide'), true)) {
	return;
}
if (empty($s['title'])) {
	return;
}
?>
<section class="py-10 md:py-12 bg-white">
	<div class="max-w-6xl mx-auto px-4 md:px-6">
		<div class="rounded-2xl border border-orange-200 bg-gradient-to-br from-orange-50 to-white p-6 md:p-8 shadow-sm">
			<h2 class="text-2xl md:text-3xl font-bold text-blue-900 leading-tight"><?php echo esc_html($s['title']); ?></h2>
			<?php if (!empty($s['description'])) : ?>
				<p class="mt-3 text-sm md:text-base text-gray-600 leading-relaxed max-w-3xl"><?php echo esc_html($s['description']); ?></p>
			<?php endif; ?>
			<?php if (!empty($s['link_text']) && !empty($s['link_url'])) : ?>
				<a href="<?php echo vr_esc_url($s['link_url']); ?>" class="inline-flex items-center gap-2 mt-5 font-semibold text-orange-600 hover:text-orange-700 transition text-sm md:text-base">
					<?php echo esc_html($s['link_text']); ?>
					<i class="fa-solid fa-arrow-right text-sm"></i>
				</a>
			<?php endif; ?>
		</div>
	</div>
</section>
