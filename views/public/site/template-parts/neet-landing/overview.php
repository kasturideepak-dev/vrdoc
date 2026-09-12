<?php
/**
 * NEET landing — program overview table
 *
 * @package VR_Doctors
 */

global $vr_neet_slug;
$slug = vr_neet_landing_slug();
if ($slug === '') {
	return;
}
$s     = vr_get_page_section($slug, 'overview');
$items = vr_neet_filter_rows_any($s['items'] ?? array(), array('label', 'value'));
if (empty($items)) {
	return;
}
?>
<section class="py-12 md:py-16 bg-blue-50">
	<div class="max-w-6xl mx-auto px-4 md:px-6">
		<div class="text-center max-w-3xl mx-auto">
			<p class="text-orange-500 font-semibold uppercase tracking-widest text-sm md:text-base"><?php echo esc_html($s['eyebrow']); ?></p>
			<h2 class="mt-3 md:mt-4 text-3xl md:text-4xl font-bold text-blue-900"><?php echo esc_html($s['title']); ?></h2>
		</div>
		<?php if (!empty($items)) : ?>
		<div class="mt-8 md:mt-10 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-lg">
			<div class="bg-blue-900 px-5 py-3.5 grid grid-cols-1 md:grid-cols-[200px_1fr] gap-1 md:gap-4">
				<p class="text-sm md:text-base font-bold text-white"><?php echo esc_html($s['table_col_1'] ?: 'Program Detail'); ?></p>
				<p class="text-sm md:text-base font-bold text-white hidden md:block"><?php echo esc_html($s['table_col_2'] ?: 'Information'); ?></p>
			</div>
			<div class="divide-y divide-gray-100">
				<?php foreach ($items as $index => $row) : ?>
					<div class="grid grid-cols-1 md:grid-cols-[200px_1fr] gap-1 md:gap-4 px-5 py-4 <?php echo 0 === $index % 2 ? 'bg-white' : 'bg-blue-50'; ?>">
						<p class="text-sm font-semibold text-blue-900"><?php echo esc_html($row['label'] ?? ''); ?></p>
						<p class="text-sm md:text-base text-gray-700 leading-relaxed"><?php echo esc_html($row['value'] ?? ''); ?></p>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php endif; ?>
	</div>
</section>
