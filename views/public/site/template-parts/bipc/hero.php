<?php
/**
 * BiPC hero — matches Next.js BipcHero.
 *
 * @package VR_Doctors
 */

$hero = vr_get_page_section('bipc-careers', 'hero');
?>
<section class="relative py-12 md:py-20 overflow-hidden">
	<img src="<?php echo esc_url(vr_media_url($hero['image'])); ?>" alt="<?php echo esc_attr($hero['image_alt']); ?>" class="absolute inset-0 w-full h-full object-cover -z-10" decoding="async" fetchpriority="high" />
	<div class="absolute inset-0 bg-black/60 -z-10"></div>
	<div class="absolute inset-0 bg-gradient-to-br from-blue-950/40 via-transparent to-blue-900/40 -z-10"></div>
	<div class="max-w-6xl mx-auto px-4 md:px-6 text-center relative z-10">
		<p class="text-orange-400 font-semibold uppercase tracking-widest text-sm md:text-base"><?php echo esc_html($hero['eyebrow']); ?></p>
		<h1 class="mt-3 md:mt-5 text-3xl md:text-6xl font-bold text-white">
			<?php echo vr_page_title_html($hero['title']); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</h1>
		<p class="max-w-3xl mx-auto mt-4 md:mt-6 text-base md:text-lg text-gray-100">
			<?php echo esc_html($hero['description']); ?>
		</p>
	</div>
</section>
