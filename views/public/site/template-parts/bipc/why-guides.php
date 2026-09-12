<?php
/**
 * Why VR Doctors guides — matches Next.js WhyVRDoctorsGuides.
 *
 * @package VR_Doctors
 */

$s = vr_get_page_section('bipc-careers', 'why_guides');
?>
<section class="py-16 md:py-24 bg-blue-900 text-white">
	<div class="max-w-5xl mx-auto px-4 md:px-6 text-center">
		<p class="text-orange-400 font-semibold uppercase tracking-widest"><?php echo esc_html($s['eyebrow']); ?></p>
		<h2 class="mt-4 text-3xl md:text-5xl font-bold"><?php echo esc_html($s['title']); ?></h2>
		<p class="mt-8 text-lg text-blue-100 leading-relaxed">
			<?php echo esc_html($s['paragraph_1']); ?>
		</p>
		<p class="mt-6 text-lg text-blue-100 leading-relaxed">
			<?php echo esc_html($s['paragraph_2']); ?>
		</p>
	</div>
</section>
