<?php
/**
 * Courses hero
 *
 * @package VR_Doctors
 */

$hero = vr_get_page_section('courses', 'hero');
?>
<section class="relative py-12 md:py-20 overflow-hidden">
	<img src="<?php echo esc_url(vr_media_url($hero['image'])); ?>" alt="<?php echo esc_attr($hero['image_alt']); ?>" class="absolute inset-0 w-full h-full object-contain -z-10 bg-blue-950" decoding="async" fetchpriority="high" />
	<div class="absolute inset-0 bg-black/60 -z-10"></div>
	<div class="max-w-5xl mx-auto px-5 text-center relative z-10">
		<p class="text-orange-400 font-semibold uppercase tracking-widest"><?php echo esc_html($hero['eyebrow']); ?></p>
		<h1 class="text-4xl md:text-6xl font-bold text-white mt-4 leading-tight">
			<?php echo vr_page_title_html($hero['title']); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</h1>
		<p class="mt-6 text-gray-200 max-w-3xl mx-auto text-base md:text-lg">
			<?php echo esc_html($hero['description']); ?>
		</p>
	</div>
</section>
