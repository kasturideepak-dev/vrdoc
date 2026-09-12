<?php
/**
 * Course selector cards
 *
 * @package VR_Doctors
 */

$s        = vr_get_page_section('courses', 'selector');
$programs = !empty($s['items']) && is_array($s['items']) ? $s['items'] : array();
?>
<section class="py-12 md:py-16 bg-blue-50">
	<div class="max-w-6xl mx-auto px-4 md:px-6">
		<div class="text-center mb-10">
			<p class="text-orange-500 font-semibold uppercase tracking-widest"><?php echo esc_html($s['eyebrow']); ?></p>
			<h2 class="text-3xl md:text-5xl font-bold text-blue-900 mt-3"><?php echo esc_html($s['title']); ?></h2>
			<p class="mt-4 text-gray-600"><?php echo esc_html($s['description']); ?></p>
		</div>
		<div class="grid md:grid-cols-3 gap-6">
			<?php foreach ($programs as $p) : ?>
				<div class="bg-white rounded-2xl p-8 shadow border border-gray-100 text-center hover:shadow-lg transition">
					<div class="w-14 h-14 mx-auto rounded-full bg-blue-900 text-white flex items-center justify-center text-xl">
						<i class="fa-solid <?php echo esc_attr($p['icon'] ?? 'fa-circle'); ?>"></i>
					</div>
					<h3 class="mt-4 text-xl font-bold text-blue-900"><?php echo esc_html($p['title'] ?? ''); ?></h3>
					<p class="mt-3 text-gray-600 text-sm"><?php echo esc_html($p['description'] ?? ''); ?></p>
					<p class="mt-4 text-xs font-semibold uppercase tracking-wider text-orange-500"><?php echo esc_html($p['tag'] ?? ''); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
