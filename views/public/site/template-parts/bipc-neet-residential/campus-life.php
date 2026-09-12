<?php
/**
 * BiPC NEET residential — campus life
 *
 * @package VR_Doctors
 */

$slug    = 'best-bipc-college-in-hyderabad-neet-residential';
$s       = vr_get_page_section($slug, 'campus_life');
$pillars = vr_neet_filter_rows_any($s['items'] ?? array(), array('title', 'description'));
if (empty($pillars)) {
	return;
}
?>
<section class="py-12 md:py-16 bg-blue-50">
	<div class="max-w-6xl mx-auto px-4 md:px-6">
		<div class="text-center max-w-3xl mx-auto">
			<p class="text-orange-500 font-semibold uppercase tracking-widest text-sm md:text-base"><?php echo esc_html($s['eyebrow']); ?></p>
			<h2 class="mt-3 md:mt-4 text-3xl md:text-4xl font-bold text-blue-900 leading-tight">
				<?php echo esc_html($s['title']); ?>
			</h2>
		</div>

		<div class="mt-8 md:mt-10 grid <?php echo esc_attr(vr_neet_grid_cols_class(count($pillars))); ?> gap-5 md:gap-6">
			<?php foreach ($pillars as $pillar) : ?>
				<div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg">
					<?php if (!empty($pillar['icon'])) : ?>
					<div class="w-12 h-12 rounded-2xl bg-blue-900 flex items-center justify-center">
						<i class="fa-solid <?php echo esc_attr($pillar['icon']); ?> text-white text-lg"></i>
					</div>
					<?php endif; ?>
					<h3 class="mt-5 text-xl font-bold text-blue-900"><?php echo esc_html($pillar['title'] ?? ''); ?></h3>
					<p class="mt-3 text-sm md:text-base text-gray-600 leading-relaxed">
						<?php echo esc_html($pillar['description'] ?? ''); ?>
					</p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
