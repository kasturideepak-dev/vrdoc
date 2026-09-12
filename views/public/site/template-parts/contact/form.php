<?php
/**
 * Contact Form 7 enquiry form section
 *
 * @package VR_Doctors
 */

$s = vr_get_page_section('contact', 'form');
?>
<section class="py-16 md:py-24 bg-gray-50">
	<div class="max-w-5xl mx-auto px-6">
		<div class="text-center mb-12">
			<p class="text-orange-500 font-semibold uppercase tracking-widest"><?php echo esc_html($s['eyebrow']); ?></p>
			<h2 class="mt-4 text-4xl md:text-5xl font-bold text-blue-900"><?php echo esc_html($s['title']); ?></h2>
			<p class="mt-4 text-gray-600 max-w-2xl mx-auto">
				<?php echo esc_html($s['description']); ?>
			</p>
		</div>

		<div class="vr-cf7">
			<?php vr_render_cf7('cf7_shortcode'); ?>
		</div>
	</div>
</section>
