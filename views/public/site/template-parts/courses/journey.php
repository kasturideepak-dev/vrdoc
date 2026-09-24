<?php
/**
 * Student journey steps
 *
 * @package VR_Doctors
 */

$s     = vr_get_page_section('courses', 'journey');
$steps = !empty($s['items']) && is_array($s['items']) ? $s['items'] : array();
?>
<section class="py-12 md:py-16 bg-blue-50" data-tabs>
	<div class="max-w-6xl mx-auto px-4 md:px-6">
		<div class="text-center mb-10">
			<p class="text-orange-500 font-semibold uppercase tracking-widest"><?php echo esc_html($s['eyebrow']); ?></p>
			<h2 class="text-3xl md:text-5xl font-bold text-blue-900 mt-3"><?php echo esc_html($s['title']); ?></h2>
			<p class="mt-4 text-gray-600"><?php echo esc_html($s['description']); ?></p>
		</div>

		<div role="tablist" class="grid grid-cols-2 md:grid-cols-3 gap-3 mb-8">
			<?php foreach ($steps as $i => $step) : ?>
				<button
					type="button"
					role="tab"
					data-tab-btn
					data-active-class="bg-blue-900 text-white"
					data-inactive-class="bg-white text-blue-900"
					class="p-4 rounded-xl font-semibold text-left transition <?php echo 0 === $i ? 'bg-blue-900 text-white' : 'bg-white text-blue-900'; ?>"
				>
					<span class="text-xs opacity-70"><?php echo esc_html($step['number'] ?? ''); ?></span>
					<span class="block mt-1"><?php echo esc_html($step['title'] ?? ''); ?></span>
				</button>
			<?php endforeach; ?>
		</div>

		<?php foreach ($steps as $i => $step) : ?>
			<div data-tab-panel role="tabpanel" class="bg-white rounded-2xl p-8 shadow border border-gray-100 <?php echo 0 === $i ? '' : 'hidden'; ?>">
				<div class="flex items-start gap-4">
					<div class="w-14 h-14 rounded-full bg-orange-500 text-white flex items-center justify-center text-xl shrink-0">
						<i class="fa-solid <?php echo esc_attr($step['icon'] ?? 'fa-circle'); ?>"></i>
					</div>
					<div>
						<p class="text-orange-500 font-semibold text-sm">STEP <?php echo esc_html($step['number'] ?? ''); ?></p>
						<h3 class="text-2xl font-bold text-blue-900 mt-1"><?php echo esc_html($step['title'] ?? ''); ?></h3>
						<p class="mt-3 text-gray-600 leading-relaxed"><?php echo esc_html($step['description'] ?? ''); ?></p>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</section>
