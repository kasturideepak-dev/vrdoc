<?php
/**
 * About hero
 *
 * @package VR_Doctors
 */

$hero = vr_get_page_section('about', 'hero');
?>
<section class="relative py-12 md:py-20 overflow-hidden">
	<img src="<?php echo esc_url($hero['image']); ?>" alt="<?php echo esc_attr($hero['image_alt']); ?>" class="absolute inset-0 w-full h-full object-cover -z-10" />
	<div class="absolute inset-0 bg-black/60 -z-10"></div>
	<div class="absolute inset-0 bg-gradient-to-br from-blue-950/40 via-transparent to-blue-900/40 -z-10"></div>
	<div class="max-w-5xl mx-auto px-5 text-center relative z-10">
		<p class="text-orange-400 font-semibold uppercase tracking-widest"><?php echo esc_html($hero['eyebrow']); ?></p>
		<h1 class="text-4xl md:text-6xl font-bold text-white mt-4 leading-tight">
			<?php echo vr_page_title_html($hero['title']); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in helper ?>
		</h1>
		<p class="mt-6 text-gray-200 max-w-3xl mx-auto text-base md:text-lg">
			<?php echo esc_html($hero['description']); ?>
		</p>
		<div class="flex flex-col sm:flex-row justify-center gap-3 mt-8">
			<a href="<?php echo vr_esc_url($hero['primary_cta_url']); ?>" class="bg-orange-500 hover:bg-orange-600 transition text-white px-6 py-3 rounded-xl font-semibold"><?php echo esc_html($hero['primary_cta_text']); ?></a>
			<a href="<?php echo vr_esc_url($hero['secondary_cta_url']); ?>" class="border border-white hover:bg-white hover:text-blue-900 transition text-white px-6 py-3 rounded-xl font-semibold"><?php echo esc_html($hero['secondary_cta_text']); ?></a>
		</div>
	</div>
</section>
