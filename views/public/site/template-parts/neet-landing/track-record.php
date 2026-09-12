<?php
/**
 * NEET landing — track record stats
 *
 * @package VR_Doctors
 */

$slug = vr_neet_landing_slug();
if ($slug === '') {
	return;
}
$s     = vr_get_page_section($slug, 'track_record');
$items = vr_neet_filter_rows_any($s['items'] ?? array(), array('stat', 'text'));
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
			<?php foreach ($items as $stat) : ?>
				<div class="rounded-2xl bg-blue-900 text-white p-6 md:p-8 shadow-xl">
					<div class="w-11 h-11 rounded-xl bg-orange-500 flex items-center justify-center">
						<i class="fa-solid <?php echo esc_attr($stat['icon'] ?? 'fa-star'); ?> text-white"></i>
					</div>
					<p class="mt-5 text-2xl md:text-3xl font-bold text-orange-300"><?php echo esc_html($stat['stat'] ?? ''); ?></p>
					<p class="mt-3 text-sm md:text-base text-blue-100 leading-relaxed"><?php echo esc_html($stat['text'] ?? ''); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
