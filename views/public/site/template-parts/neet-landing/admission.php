<?php
/**
 * NEET landing — admission process
 *
 * @package VR_Doctors
 */

$slug = vr_neet_landing_slug();
if ($slug === '') {
	return;
}
$s     = vr_get_page_section($slug, 'admission');
$items = vr_neet_filter_rows_any($s['items'] ?? array(), array('title', 'description'));
if (empty($items)) {
	return;
}
?>
<section class="py-12 md:py-16 bg-blue-50">
	<div class="max-w-6xl mx-auto px-4 md:px-6">
		<div class="text-center">
			<p class="text-orange-500 font-semibold uppercase tracking-widest text-sm md:text-base"><?php echo esc_html($s['eyebrow']); ?></p>
			<h2 class="mt-3 md:mt-4 text-3xl md:text-4xl font-bold text-blue-900"><?php echo esc_html($s['title']); ?></h2>
		</div>
		<div class="mt-8 md:mt-10 grid <?php echo esc_attr(vr_neet_grid_cols_class(count($items))); ?> gap-5 md:gap-6">
			<?php foreach ($items as $step) : ?>
				<div class="rounded-2xl border border-gray-200 bg-white p-6 md:p-7 shadow-lg">
					<?php if (!empty($step['step'])) : ?>
						<span class="text-3xl md:text-4xl font-bold text-orange-500"><?php echo esc_html($step['step']); ?></span>
					<?php endif; ?>
					<h3 class="mt-4 text-xl font-bold text-blue-900"><?php echo esc_html($step['title'] ?? ''); ?></h3>
					<p class="mt-3 text-sm md:text-base text-gray-600 leading-relaxed"><?php echo esc_html($step['description'] ?? ''); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
