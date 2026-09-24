<?php
/**
 * BiPC NEET residential — facilities
 *
 * @package VR_Doctors
 */

$s     = vr_get_page_section('best-bipc-college-in-hyderabad-neet-residential', 'facilities');
$items = vr_neet_facility_items($s);
?>
<section class="py-12 md:py-16 bg-white">
	<div class="max-w-6xl mx-auto px-4 md:px-6">
		<div class="grid lg:grid-cols-2 gap-8 md:gap-12 items-center">
			<div>
				<p class="text-orange-500 font-semibold uppercase tracking-widest text-sm md:text-base"><?php echo esc_html($s['eyebrow']); ?></p>
				<h2 class="mt-3 md:mt-4 text-3xl md:text-4xl font-bold text-blue-900 leading-tight">
					<?php echo esc_html($s['title']); ?>
				</h2>
				<p class="mt-4 text-sm md:text-base text-gray-600 leading-relaxed">
					<?php echo esc_html($s['description']); ?>
				</p>

				<?php if (!empty($items)) : ?>
				<div class="mt-6 grid sm:grid-cols-2 gap-3">
					<?php foreach ($items as $item) : ?>
						<?php
						$icon = trim((string) ($item['icon'] ?? ''));
						$text = trim((string) ($item['text'] ?? ''));
						if ($text === '') {
							continue;
						}
						?>
						<div class="flex items-start gap-3 rounded-xl border border-gray-200 bg-blue-50 p-3.5">
							<div class="w-9 h-9 rounded-lg bg-orange-100 flex items-center justify-center shrink-0">
								<i class="fa-solid <?php echo esc_attr(vr_sanitize_fa_icon($icon, 'fa-check')); ?> text-orange-600 text-sm"></i>
							</div>
							<p class="text-sm text-gray-700 leading-snug"><?php echo esc_html($text); ?></p>
						</div>
					<?php endforeach; ?>
				</div>
				<?php endif; ?>
			</div>

			<div class="grid grid-cols-2 gap-3 md:gap-4">
				<img
					src="<?php echo esc_url(vr_media_url($s['image_1'])); ?>"
					alt="<?php echo esc_attr($s['image_1_alt'] ?: 'Residential campus facilities at VR Doctors Academy'); ?>"
					class="w-full h-full min-h-[180px] md:min-h-[240px] rounded-2xl object-cover shadow-md col-span-1 row-span-2" decoding="async" loading="lazy"<?php $__ss = vr_srcset($s['image_1']); if ($__ss !== '') : ?> srcset="<?php echo esc_attr($__ss); ?>" sizes="(max-width: 768px) 100vw, 50vw"<?php endif; ?> />
				<img
					src="<?php echo esc_url(vr_media_url($s['image_2'])); ?>"
					alt="<?php echo esc_attr($s['image_2_alt'] ?: 'Hostel facilities at VR Doctors Academy'); ?>"
					class="w-full h-full min-h-[120px] md:min-h-[160px] rounded-2xl object-cover shadow-md" decoding="async" loading="lazy"<?php $__ss = vr_srcset($s['image_2']); if ($__ss !== '') : ?> srcset="<?php echo esc_attr($__ss); ?>" sizes="(max-width: 768px) 100vw, 50vw"<?php endif; ?> />
				<img
					src="<?php echo esc_url(vr_media_url($s['image_3'])); ?>"
					alt="<?php echo esc_attr($s['image_3_alt'] ?: 'Air-conditioned classrooms and hostel rooms'); ?>"
					class="w-full h-full min-h-[120px] md:min-h-[160px] rounded-2xl object-cover shadow-md" decoding="async" loading="lazy"<?php $__ss = vr_srcset($s['image_3']); if ($__ss !== '') : ?> srcset="<?php echo esc_attr($__ss); ?>" sizes="(max-width: 768px) 100vw, 50vw"<?php endif; ?> />
			</div>
		</div>
	</div>
</section>
