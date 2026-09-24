<?php
/**
 * Who we are
 *
 * @package VR_Doctors
 */

$s = vr_get_page_section('about', 'who_we_are');
?>
<section class="py-12 md:py-20 bg-white">
	<div class="max-w-6xl mx-auto px-4 md:px-6 grid grid-cols-1 md:grid-cols-2 gap-10 items-center">
		<div class="grid grid-cols-2 gap-3 md:gap-4">
			<img src="<?php echo esc_url(vr_media_url($s['image_1'])); ?>" alt="<?php echo esc_attr($s['image_1_alt']); ?>" class="aspect-square object-cover rounded-2xl w-full" decoding="async" loading="lazy"<?php $__ss = vr_srcset($s['image_1']); if ($__ss !== '') : ?> srcset="<?php echo esc_attr($__ss); ?>" sizes="(max-width: 768px) 50vw, 25vw"<?php endif; ?> />
			<img src="<?php echo esc_url(vr_media_url($s['image_2'])); ?>" alt="<?php echo esc_attr($s['image_2_alt']); ?>" class="aspect-square object-contain rounded-2xl md:mt-8 w-full" decoding="async" loading="lazy"<?php $__ss = vr_srcset($s['image_2']); if ($__ss !== '') : ?> srcset="<?php echo esc_attr($__ss); ?>" sizes="(max-width: 768px) 50vw, 25vw"<?php endif; ?> />
			<img src="<?php echo esc_url(vr_media_url($s['image_3'])); ?>" alt="<?php echo esc_attr($s['image_3_alt']); ?>" class="aspect-square object-contain rounded-2xl md:mt-8 w-full" decoding="async" loading="lazy"<?php $__ss = vr_srcset($s['image_3']); if ($__ss !== '') : ?> srcset="<?php echo esc_attr($__ss); ?>" sizes="(max-width: 768px) 50vw, 25vw"<?php endif; ?> />
			<img src="<?php echo esc_url(vr_media_url($s['image_4'])); ?>" alt="<?php echo esc_attr($s['image_4_alt']); ?>" class="aspect-square object-contain rounded-2xl w-full" decoding="async" loading="lazy"<?php $__ss = vr_srcset($s['image_4']); if ($__ss !== '') : ?> srcset="<?php echo esc_attr($__ss); ?>" sizes="(max-width: 768px) 50vw, 25vw"<?php endif; ?> />
		</div>
		<div>
			<p class="text-orange-500 font-semibold uppercase tracking-widest"><?php echo esc_html($s['eyebrow']); ?></p>
			<h2 class="text-3xl md:text-5xl font-bold text-blue-900 mt-3"><?php echo esc_html($s['title']); ?></h2>
			<p class="mt-6 text-gray-600 leading-relaxed">
				<?php echo esc_html($s['paragraph_1']); ?>
			</p>
			<p class="mt-4 text-gray-600 leading-relaxed">
				<?php echo esc_html($s['paragraph_2']); ?>
			</p>
		</div>
	</div>
</section>
