<?php
/**
 * BiPC NEET residential — Focus-45 program
 *
 * @package VR_Doctors
 */

$slug       = 'best-bipc-college-in-hyderabad-neet-residential';
$s          = vr_get_page_section($slug, 'focus_45');
$details    = vr_neet_filter_rows_any($s['overview_items'] ?? array(), array('label', 'value'));
$curriculum = !empty($s['curriculum_features']) && is_array($s['curriculum_features']) ? $s['curriculum_features'] : array();
if (empty($details) && empty($curriculum) && trim((string) ($s['title'] ?? '')) === '') {
	return;
}
?>
<section id="focus-45" class="py-12 md:py-16 bg-blue-50 scroll-mt-24">
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

		<?php if (!empty($details)) : ?>
		<div class="mt-8 md:mt-10 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-lg">
			<div class="bg-blue-900 px-5 py-3.5 grid grid-cols-1 md:grid-cols-[200px_1fr] gap-1 md:gap-4">
				<p class="text-sm md:text-base font-bold text-white"><?php echo esc_html($s['table_col_1'] ?: 'Program Detail'); ?></p>
				<p class="text-sm md:text-base font-bold text-white hidden md:block"><?php echo esc_html($s['table_col_2'] ?: 'Information'); ?></p>
			</div>
			<div class="divide-y divide-gray-100">
				<?php foreach ($details as $index => $row) : ?>
					<div class="grid grid-cols-1 md:grid-cols-[200px_1fr] gap-1 md:gap-4 px-5 py-4 <?php echo 0 === $index % 2 ? 'bg-white' : 'bg-blue-50'; ?>">
						<p class="text-sm font-semibold text-blue-900"><?php echo esc_html($row['label'] ?? ''); ?></p>
						<p class="text-sm md:text-base text-gray-700 leading-relaxed"><?php echo esc_html($row['value'] ?? ''); ?></p>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php endif; ?>

		<?php if (!empty($curriculum)) : ?>
		<div class="mt-10 md:mt-12">
			<h3 class="text-2xl md:text-3xl font-bold text-blue-900"><?php echo esc_html($s['curriculum_title']); ?></h3>
			<ul class="mt-5 md:mt-6 space-y-3.5">
				<?php foreach ($curriculum as $item) : ?>
					<li class="flex items-start gap-3 bg-white rounded-xl border border-gray-200 px-4 py-3.5 shadow-sm">
						<span class="mt-0.5 w-5 h-5 rounded-full bg-orange-100 flex items-center justify-center shrink-0">
							<i class="fa-solid fa-check text-orange-600 text-xs"></i>
						</span>
						<span class="text-sm md:text-base text-gray-700 leading-relaxed"><?php echo esc_html($item); ?></span>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php endif; ?>
	</div>
</section>
