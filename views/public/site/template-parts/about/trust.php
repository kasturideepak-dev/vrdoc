<?php
/**
 * Trust section
 *
 * @package VR_Doctors
 */

$s       = vr_get_page_section('about', 'trust');
$factors = !empty($s['items']) && is_array($s['items']) ? $s['items'] : array();
?>
<section class="py-12 md:py-16 bg-white">
	<div class="max-w-6xl mx-auto px-4 md:px-6">
		<div class="text-center mb-10">
			<p class="text-orange-500 font-semibold uppercase tracking-widest"><?php echo esc_html($s['eyebrow']); ?></p>
			<h2 class="text-3xl md:text-5xl font-bold text-blue-900 mt-3"><?php echo esc_html($s['title']); ?></h2>
			<p class="mt-4 text-gray-600 max-w-2xl mx-auto"><?php echo esc_html($s['description']); ?></p>
		</div>
		<div class="grid md:grid-cols-3 gap-6">
			<?php foreach ($factors as $factor) : ?>
				<div class="bg-gray-50 border border-gray-200 rounded-2xl p-6 hover:shadow-lg transition">
					<h3 class="text-lg font-bold text-blue-900"><?php echo esc_html($factor['title'] ?? ''); ?></h3>
					<p class="mt-2 text-gray-600 text-sm leading-relaxed"><?php echo esc_html($factor['description'] ?? ''); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
