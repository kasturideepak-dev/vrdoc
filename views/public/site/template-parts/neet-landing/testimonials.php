<?php
/**
 * NEET landing — testimonials
 *
 * @package VR_Doctors
 */

$slug = vr_neet_landing_slug();
if ($slug === '') {
	return;
}
$s     = vr_get_page_section($slug, 'testimonials');
$items = vr_neet_filter_testimonial_items($s['items'] ?? array());
if (empty($items)) {
	return;
}
?>
<section class="py-12 md:py-16 bg-white">
	<div class="max-w-6xl mx-auto px-4 md:px-6">
		<div class="text-center">
			<p class="text-orange-500 font-semibold uppercase tracking-widest text-sm md:text-base"><?php echo esc_html($s['eyebrow']); ?></p>
			<h2 class="mt-3 md:mt-4 text-3xl md:text-4xl font-bold text-blue-900"><?php echo esc_html($s['title']); ?></h2>
		</div>
		<div class="mt-8 md:mt-10 grid <?php echo esc_attr(vr_neet_grid_cols_class(count($items))); ?> gap-5 md:gap-6 max-w-5xl mx-auto">
			<?php foreach ($items as $t) : ?>
				<article class="bg-blue-50/40 rounded-2xl border border-gray-200 p-6 shadow-lg">
					<div class="flex items-center gap-3">
						<?php if (!empty($t['image'])) : ?>
							<img src="<?php echo esc_url(vr_media_url($t['image'])); ?>" alt="<?php echo esc_attr($t['name'] ?? ''); ?>" class="rounded-full object-cover border-2 border-orange-500 w-14 h-14" width="56" height="56" decoding="async" loading="lazy"<?php $__ss = vr_srcset($t['image']); if ($__ss !== '') : ?> srcset="<?php echo esc_attr($__ss); ?>" sizes="(max-width: 768px) 100vw, 50vw"<?php endif; ?> />
						<?php else : ?>
							<div class="rounded-full border-2 border-orange-500 w-14 h-14 bg-orange-100 flex items-center justify-center shrink-0" aria-hidden="true">
								<i class="fa-solid fa-user text-orange-600"></i>
							</div>
						<?php endif; ?>
						<div>
							<?php if (!empty($t['name'])) : ?>
								<h3 class="font-bold text-blue-900"><?php echo esc_html($t['name']); ?></h3>
							<?php endif; ?>
							<?php if (!empty($t['college'])) : ?>
								<p class="text-sm text-orange-600 font-medium"><?php echo esc_html($t['college']); ?></p>
							<?php endif; ?>
						</div>
					</div>
					<p class="mt-5 text-sm text-gray-600 italic leading-relaxed">&ldquo;<?php echo esc_html($t['quote']); ?>&rdquo;</p>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
