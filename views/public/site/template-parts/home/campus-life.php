<?php
/**
 * Campus life tabs
 *
 * @package VR_Doctors
 */

$campus     = vr_get_home_campus();
$categories = !empty($campus['categories']) && is_array($campus['categories']) ? $campus['categories'] : array();

if (empty($categories)) {
	return;
}
?>
<section class="py-8 md:py-16 bg-white" data-tabs>
	<div class="max-w-6xl mx-auto px-4 md:px-6">
		<div class="text-center mb-10">
			<?php if (!empty($campus['eyebrow'])) : ?>
				<p class="text-orange-500 font-semibold uppercase tracking-widest text-sm"><?php echo esc_html($campus['eyebrow']); ?></p>
			<?php endif; ?>
			<?php if (!empty($campus['title'])) : ?>
				<h2 class="text-3xl md:text-5xl font-bold text-blue-900 mt-3"><?php echo esc_html($campus['title']); ?></h2>
			<?php endif; ?>
			<?php if (!empty($campus['description'])) : ?>
				<p class="max-w-2xl mx-auto mt-4 text-gray-600"><?php echo esc_html($campus['description']); ?></p>
			<?php endif; ?>
		</div>

		<div role="tablist" class="flex justify-center gap-3 mb-8 flex-wrap">
			<?php foreach ($categories as $i => $cat) : ?>
				<button
					type="button"
					role="tab"
					data-tab-btn
					data-active-class="bg-blue-900 text-white shadow-md"
					data-inactive-class="bg-gray-100 text-blue-900 hover:bg-gray-200"
					class="px-6 py-3 rounded-xl font-semibold text-sm transition-colors duration-300 <?php echo 0 === (int) $i ? 'bg-blue-900 text-white shadow-md' : 'bg-gray-100 text-blue-900 hover:bg-gray-200'; ?>"
				>
					<?php echo esc_html($cat['tab'] ?? ''); ?>
				</button>
			<?php endforeach; ?>
		</div>

		<?php foreach ($categories as $i => $cat) : ?>
			<div data-tab-panel role="tabpanel" class="<?php echo 0 === (int) $i ? '' : 'hidden'; ?>">
				<?php if (!empty($cat['photos']) && is_array($cat['photos'])) : ?>
					<div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-8">
						<?php foreach ($cat['photos'] as $photo) : ?>
							<?php if (empty($photo['src'])) { continue; } ?>
							<div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-800 to-blue-600 aspect-square transition-transform duration-300 ease-out hover:scale-125 hover:z-20 hover:shadow-2xl">
								<img src="<?php echo esc_url(vr_media_url($photo['src'])); ?>" alt="<?php echo esc_attr($photo['alt'] ?? ''); ?>" class="absolute inset-0 w-full h-full object-cover" decoding="async" loading="lazy"<?php $__ss = vr_srcset($photo['src']); if ($__ss !== '') : ?> srcset="<?php echo esc_attr($__ss); ?>" sizes="(max-width: 768px) 50vw, 25vw"<?php endif; ?> />
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
				<?php if (!empty($cat['features']) && is_array($cat['features'])) : ?>
					<div class="grid md:grid-cols-2 gap-4">
						<?php foreach ($cat['features'] as $feature) : ?>
							<?php if (empty($feature['title']) && empty($feature['description'])) { continue; } ?>
							<div class="bg-gray-50 border border-gray-200 rounded-2xl p-6">
								<?php if (!empty($feature['title'])) : ?>
									<h3 class="text-lg font-bold text-blue-900"><?php echo esc_html($feature['title']); ?></h3>
								<?php endif; ?>
								<?php if (!empty($feature['description'])) : ?>
									<p class="mt-2 text-gray-600 text-sm leading-relaxed"><?php echo esc_html($feature['description']); ?></p>
								<?php endif; ?>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
	</div>
</section>
