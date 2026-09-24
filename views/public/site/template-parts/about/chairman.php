<?php
/**
 * Chairman message
 *
 * @package VR_Doctors
 */

$s = vr_get_page_section('about', 'chairman');
?>
<section class="py-12 md:py-16 bg-white">
	<div class="max-w-6xl mx-auto px-4 md:px-6 grid grid-cols-1 md:grid-cols-2 gap-10 items-center">
		<div>
			<img src="<?php echo esc_url(vr_media_url($s['image'])); ?>" alt="<?php echo esc_attr($s['image_alt']); ?>" class="w-full rounded-3xl object-cover shadow-lg" decoding="async" loading="lazy" />
		</div>
		<div>
			<p class="text-orange-500 font-semibold uppercase tracking-widest"><?php echo esc_html($s['eyebrow']); ?></p>
			<h2 class="text-2xl md:text-3xl font-bold text-blue-900 mt-3 leading-snug">
				<?php echo esc_html($s['title']); ?>
			</h2>
			<p class="mt-6 text-gray-700 leading-relaxed">
				<?php echo esc_html($s['paragraph_1']); ?>
			</p>
			<p class="mt-4 text-gray-700 leading-relaxed">
				<?php echo esc_html($s['paragraph_2']); ?>
			</p>
			<p class="mt-6 text-sm text-gray-500 uppercase tracking-wider"><?php echo esc_html($s['role_label']); ?></p>
			<p class="text-xl font-bold text-blue-900"><?php echo esc_html($s['name']); ?></p>
		</div>
	</div>
</section>
