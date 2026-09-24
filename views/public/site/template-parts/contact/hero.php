<?php
/**
 * Contact hero
 *
 * @package VR_Doctors
 */

$hero = vr_get_page_section('contact', 'hero');
?>
<section class="relative py-12 md:py-20 overflow-hidden">
	<img src="<?php echo esc_url(vr_media_url($hero['image'])); ?>" alt="<?php echo esc_attr($hero['image_alt']); ?>" class="absolute inset-0 w-full h-full object-contain -z-10 bg-blue-950" decoding="async" fetchpriority="high"<?php $__ss = vr_srcset($hero['image']); if ($__ss !== '') : ?> srcset="<?php echo esc_attr($__ss); ?>" sizes="100vw"<?php endif; ?> />
	<div class="absolute inset-0 bg-gradient-to-b from-black/40 via-black/55 to-black/80 -z-10"></div>
	<div class="max-w-5xl mx-auto px-5 text-center relative z-10">
		<p class="text-orange-400 font-semibold uppercase tracking-widest"><?php echo esc_html($hero['eyebrow']); ?></p>
		<h1 class="text-4xl md:text-6xl font-bold text-white mt-4 leading-tight">
			<?php echo vr_page_title_highlight_html($hero['title'], $hero['title_highlight'] ?? ''); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</h1>
		<p class="mt-6 text-gray-200 max-w-3xl mx-auto text-base md:text-lg">
			<?php echo esc_html($hero['description']); ?>
		</p>
		<div class="flex flex-col sm:flex-row justify-center gap-3 mt-8">
			<a href="<?php echo vr_esc_url($hero['primary_cta_url']); ?>" class="bg-orange-500 hover:bg-orange-600 transition text-white px-6 py-3 rounded-xl font-semibold"><?php echo esc_html($hero['primary_cta_text']); ?></a>
			<a href="<?php echo esc_url(vr_whatsapp_url()); ?>" target="_blank" rel="noopener noreferrer" class="bg-green-500 hover:bg-green-600 transition text-white px-6 py-3 rounded-xl font-semibold"><?php echo esc_html($hero['secondary_cta_text']); ?></a>
		</div>
	</div>
</section>
