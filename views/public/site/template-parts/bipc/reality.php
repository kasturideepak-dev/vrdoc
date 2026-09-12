<?php
/**
 * Reality check section — matches Next.js RealitySection.
 *
 * @package VR_Doctors
 */

$s = vr_get_page_section('bipc-careers', 'reality');
?>
<section class="py-16 md:py-24 bg-white">
	<div class="max-w-5xl mx-auto px-4 md:px-6 text-center">
		<p class="text-orange-500 font-semibold uppercase tracking-widest"><?php echo esc_html($s['eyebrow']); ?></p>
		<h2 class="mt-4 text-3xl md:text-5xl font-bold text-blue-900"><?php echo esc_html($s['title']); ?></h2>
		<p class="mt-8 text-lg text-gray-600 leading-relaxed">
			<?php echo esc_html($s['paragraph_1']); ?>
		</p>
		<p class="mt-6 text-lg text-gray-600 leading-relaxed">
			<?php echo esc_html($s['paragraph_2']); ?>
		</p>

		<div class="grid md:grid-cols-3 gap-6 mt-14">
			<div class="bg-blue-50 rounded-3xl p-5 md:p-8">
				<h3 class="text-4xl font-bold text-blue-900"><?php echo esc_html($s['stat_1']); ?></h3>
				<p class="mt-4 font-semibold"><?php echo esc_html($s['stat_1_title']); ?></p>
				<p class="mt-3 text-gray-600"><?php echo esc_html($s['stat_1_text']); ?></p>
			</div>
			<div class="bg-blue-50 rounded-3xl p-5 md:p-8">
				<h3 class="text-4xl font-bold text-blue-900"><?php echo esc_html($s['stat_2']); ?></h3>
				<p class="mt-4 font-semibold"><?php echo esc_html($s['stat_2_title']); ?></p>
				<p class="mt-3 text-gray-600"><?php echo esc_html($s['stat_2_text']); ?></p>
			</div>
			<div class="bg-blue-50 rounded-3xl p-5 md:p-8">
				<h3 class="text-4xl font-bold text-blue-900"><?php echo esc_html($s['stat_3']); ?></h3>
				<p class="mt-4 font-semibold"><?php echo esc_html($s['stat_3_title']); ?></p>
				<p class="mt-3 text-gray-600"><?php echo esc_html($s['stat_3_text']); ?></p>
			</div>
		</div>
	</div>
</section>
