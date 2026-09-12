<?php
/**
 * BiPC NEET residential — choose program
 *
 * @package VR_Doctors
 */

$slug     = 'best-bipc-college-in-hyderabad-neet-residential';
$s        = vr_get_page_section($slug, 'choose_program');
$programs = vr_neet_filter_rows_any($s['items'] ?? array(), array('title', 'description'));
if (empty($programs)) {
	return;
}
?>
<section class="py-12 md:py-16 bg-blue-50">
	<div class="max-w-6xl mx-auto px-4 md:px-6">
		<div class="text-center max-w-3xl mx-auto">
			<p class="text-orange-500 font-semibold uppercase tracking-widest text-sm md:text-base"><?php echo esc_html($s['eyebrow']); ?></p>
			<h2 class="mt-3 md:mt-4 text-3xl md:text-4xl font-bold text-blue-900"><?php echo esc_html($s['title']); ?></h2>
			<?php if (!empty($s['description'])) : ?>
				<p class="mt-4 text-sm md:text-base text-gray-600 leading-relaxed"><?php echo esc_html($s['description']); ?></p>
			<?php endif; ?>
		</div>

		<div class="mt-8 md:mt-10 grid <?php echo esc_attr(vr_neet_grid_cols_class(count($programs))); ?> gap-5 md:gap-6">
			<?php foreach ($programs as $program) : ?>
				<?php
				$featured = in_array(strtolower((string) ($program['featured'] ?? '')), array('yes', '1', 'true'), true);
				$href     = !empty($program['cta_url']) ? $program['cta_url'] : '#';
				?>
				<div class="rounded-2xl p-6 md:p-8 border shadow-lg transition hover:shadow-xl <?php echo $featured ? 'bg-blue-900 border-blue-900 text-white' : 'bg-white border-gray-200'; ?>">
					<?php if (!empty($program['icon'])) : ?>
					<div class="w-12 h-12 md:w-14 md:h-14 rounded-2xl flex items-center justify-center <?php echo $featured ? 'bg-orange-500' : 'bg-orange-100'; ?>">
						<i class="fa-solid <?php echo esc_attr($program['icon']); ?> text-xl <?php echo $featured ? 'text-white' : 'text-orange-600'; ?>"></i>
					</div>
					<?php endif; ?>

					<h3 class="mt-5 text-lg md:text-xl font-bold leading-snug <?php echo $featured ? 'text-white' : 'text-blue-900'; ?>">
						<?php echo esc_html($program['title'] ?? ''); ?>
					</h3>

					<?php if (!empty($program['description'])) : ?>
					<p class="mt-3 text-sm md:text-base leading-relaxed <?php echo $featured ? 'text-blue-100' : 'text-gray-600'; ?>">
						<?php echo esc_html($program['description']); ?>
					</p>
					<?php endif; ?>

					<?php if (!empty($program['cta_text']) && !empty($program['cta_url'])) : ?>
					<a href="<?php echo vr_esc_url($href); ?>" class="inline-flex items-center gap-2 mt-6 font-semibold text-sm md:text-base transition <?php echo $featured ? 'text-orange-300 hover:text-orange-200' : 'text-orange-600 hover:text-orange-700'; ?>">
						<?php echo esc_html($program['cta_text']); ?>
						<i class="fa-solid fa-arrow-right text-sm"></i>
					</a>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
