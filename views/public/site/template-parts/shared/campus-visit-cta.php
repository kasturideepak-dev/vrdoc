<?php
/**
 * Shared campus visit CTA
 *
 * @package VR_Doctors
 */

$cta = function_exists('vr_get_home_cta') ? vr_get_home_cta() : array(
	'eyebrow'             => 'Admissions Open',
	'title'              => 'Experience VR Doctors In Person',
	'description'        => 'Visit our campus, interact with faculty, explore hostel facilities, experience the learning environment and discover why hundreds of students have transformed their dreams into medical careers.',
	'primary_cta_text'   => 'Book A Campus Visit',
	'primary_cta_url'    => home_url('/contact/'),
	'secondary_cta_text' => 'Call Admissions',
	'secondary_cta_url'  => 'tel:+919256925640',
);
?>
<section class="py-10 md:py-16 bg-blue-900 text-white">
	<div class="max-w-4xl mx-auto px-6 text-center">
		<?php if (!empty($cta['eyebrow'])) : ?>
			<p class="text-orange-400 font-semibold uppercase tracking-widest text-sm md:text-base"><?php echo esc_html($cta['eyebrow']); ?></p>
		<?php endif; ?>
		<?php if (!empty($cta['title'])) : ?>
			<h2 class="text-3xl md:text-5xl font-bold mt-3 md:mt-4"><?php echo esc_html($cta['title']); ?></h2>
		<?php endif; ?>
		<?php if (!empty($cta['description'])) : ?>
			<p class="mt-4 md:mt-6 text-base md:text-lg text-blue-100 leading-relaxed"><?php echo esc_html($cta['description']); ?></p>
		<?php endif; ?>
		<div class="flex flex-col sm:flex-row justify-center gap-3 md:gap-4 mt-6 md:mt-10">
			<?php if (!empty($cta['primary_cta_text'])) : ?>
				<a href="<?php echo vr_esc_url($cta['primary_cta_url'] ?: home_url('/contact/')); ?>" class="bg-orange-500 hover:bg-orange-600 px-6 md:px-8 py-3 md:py-4 rounded-xl font-semibold transition text-sm md:text-base">
					<?php echo esc_html($cta['primary_cta_text']); ?>
				</a>
			<?php endif; ?>
			<?php if (!empty($cta['secondary_cta_text'])) : ?>
				<a href="<?php echo vr_esc_url($cta['secondary_cta_url'] ?: 'tel:+919256925640'); ?>" class="border border-white hover:bg-white hover:text-blue-900 px-6 md:px-8 py-3 md:py-4 rounded-xl font-semibold transition text-sm md:text-base">
					<?php echo esc_html($cta['secondary_cta_text']); ?>
				</a>
			<?php endif; ?>
		</div>
	</div>
</section>
