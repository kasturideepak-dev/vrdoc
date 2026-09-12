<?php
/**
 * BiPC NEET residential — weekly schedule
 *
 * @package VR_Doctors
 */

$slug     = 'best-bipc-college-in-hyderabad-neet-residential';
$s        = vr_get_page_section($slug, 'schedule');
$schedule = vr_neet_filter_rows_any($s['items'] ?? array(), array('day', 'focus', 'details'));
if (empty($schedule)) {
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

		<div class="mt-8 md:mt-10 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-lg">
			<div class="hidden md:grid md:grid-cols-[140px_1.1fr_1.4fr] bg-blue-900 px-5 py-3.5 gap-4">
				<p class="text-sm font-bold text-white"><?php echo esc_html($s['col_day'] ?: 'Day'); ?></p>
				<p class="text-sm font-bold text-white"><?php echo esc_html($s['col_focus'] ?: 'Focus'); ?></p>
				<p class="text-sm font-bold text-white"><?php echo esc_html($s['col_details'] ?: 'Details'); ?></p>
			</div>
			<div class="divide-y divide-gray-100">
				<?php foreach ($schedule as $index => $row) : ?>
					<div class="grid grid-cols-1 md:grid-cols-[140px_1.1fr_1.4fr] gap-1 md:gap-4 px-5 py-4 <?php echo 0 === $index % 2 ? 'bg-white' : 'bg-blue-50'; ?>">
						<p class="text-sm font-bold text-blue-900"><?php echo esc_html($row['day'] ?? ''); ?></p>
						<p class="text-sm md:text-base font-semibold text-gray-800"><?php echo esc_html($row['focus'] ?? ''); ?></p>
						<p class="text-sm md:text-base text-gray-600 leading-relaxed"><?php echo esc_html($row['details'] ?? ''); ?></p>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</section>
