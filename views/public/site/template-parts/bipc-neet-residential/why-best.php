<?php
/**
 * BiPC NEET residential — why best BiPC college
 *
 * @package VR_Doctors
 */

$slug    = 'best-bipc-college-in-hyderabad-neet-residential';
$s       = vr_get_page_section($slug, 'why_best');
$reasons = vr_neet_filter_rows_any($s['items'] ?? array(), array('text'));
if (empty($reasons)) {
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
			<?php if (!empty($s['description'])) : ?>
			<p class="mt-4 text-sm md:text-base text-gray-600 leading-relaxed">
				<?php echo esc_html($s['description']); ?>
			</p>
			<?php endif; ?>
		</div>

		<div class="mt-8 md:mt-10 grid sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-5">
			<?php foreach ($reasons as $reason) : ?>
				<div class="bg-white rounded-2xl border border-gray-200 p-5 md:p-6 shadow-md hover:shadow-lg transition">
					<?php if (!empty($reason['icon'])) : ?>
					<div class="w-11 h-11 rounded-xl bg-orange-100 flex items-center justify-center">
						<i class="fa-solid <?php echo esc_attr(vr_sanitize_fa_icon($reason['icon'] ?? '')); ?> text-orange-600"></i>
					</div>
					<?php endif; ?>
					<p class="mt-4 text-sm md:text-base text-gray-700 leading-relaxed">
						<?php echo esc_html($reason['text'] ?? ''); ?>
					</p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
