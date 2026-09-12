<?php
/**
 * NEET landing — curriculum cards
 *
 * @package VR_Doctors
 */

$slug = vr_neet_landing_slug();
if ($slug === '') {
	return;
}
$s     = vr_get_page_section($slug, 'curriculum');
$items = vr_neet_filter_rows_any($s['items'] ?? array(), array('title', 'description'));
if (empty($items) && trim((string) ($s['title'] ?? '')) === '') {
	return;
}
?>
<section class="py-12 md:py-16 bg-blue-50">
	<div class="max-w-6xl mx-auto px-4 md:px-6">
		<div class="text-center max-w-3xl mx-auto">
			<p class="text-orange-500 font-semibold uppercase tracking-widest text-sm md:text-base"><?php echo esc_html($s['eyebrow']); ?></p>
			<h2 class="mt-3 md:mt-4 text-3xl md:text-4xl font-bold text-blue-900 leading-tight"><?php echo esc_html($s['title']); ?></h2>
			<?php if (!empty($s['description'])) : ?>
				<p class="mt-4 text-sm md:text-base text-gray-600 leading-relaxed"><?php echo esc_html($s['description']); ?></p>
			<?php endif; ?>
		</div>
		<?php if (!empty($items)) : ?>
		<div class="mt-8 md:mt-10 grid md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-5">
			<?php foreach ($items as $card) : ?>
				<div class="rounded-2xl border border-gray-200 bg-white p-5 md:p-6 shadow-md">
					<?php if (!empty($card['number'])) : ?>
						<p class="text-xs font-semibold text-orange-500 tracking-wide"><?php echo esc_html($card['number']); ?></p>
					<?php endif; ?>
					<h3 class="mt-2 text-lg md:text-xl font-bold text-blue-900"><?php echo esc_html($card['title'] ?? ''); ?></h3>
					<p class="mt-3 text-sm md:text-base text-gray-600 leading-relaxed"><?php echo esc_html($card['description'] ?? ''); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
		<?php endif; ?>
	</div>
</section>
