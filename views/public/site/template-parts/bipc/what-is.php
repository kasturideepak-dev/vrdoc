<?php
/**
 * What is BiPC — matches Next.js WhatIsBipc.
 *
 * @package VR_Doctors
 */

$s = vr_get_page_section('bipc-careers', 'what_is');
?>
<section class="py-16 md:py-24 bg-white">
	<div class="max-w-6xl mx-auto px-4 md:px-6">
		<div class="grid md:grid-cols-2 gap-12 items-center">
			<div>
				<p class="text-orange-500 font-semibold uppercase tracking-widest"><?php echo esc_html($s['eyebrow']); ?></p>
				<h2 class="mt-4 text-3xl md:text-5xl font-bold text-blue-900"><?php echo esc_html($s['title']); ?></h2>
				<p class="mt-6 text-gray-600 leading-relaxed">
					<?php echo esc_html($s['paragraph_1']); ?>
				</p>
				<p class="mt-6 text-gray-600 leading-relaxed">
					<?php echo esc_html($s['paragraph_2']); ?>
				</p>
			</div>
			<div>
				<img src="<?php echo esc_url(vr_media_url($s['image'])); ?>" alt="<?php echo esc_attr($s['image_alt']); ?>" class="w-full rounded-3xl shadow-xl object-cover" decoding="async" loading="lazy"<?php $__ss = vr_srcset($s['image']); if ($__ss !== '') : ?> srcset="<?php echo esc_attr($__ss); ?>" sizes="(max-width: 768px) 100vw, 50vw"<?php endif; ?> />
			</div>
		</div>
	</div>
</section>
