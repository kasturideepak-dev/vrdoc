<?php
/**
 * Approach / Dreamers section
 *
 * @package VR_Doctors
 */

$approach = vr_get_home_approach();
$steps    = !empty($approach['steps']) && is_array($approach['steps']) ? $approach['steps'] : array();

if (empty($steps)) {
	return;
}
?>
<section id="approach" class="py-8 md:py-16 bg-gray-50" data-tabs>
	<div class="max-w-6xl mx-auto px-4 md:px-6">
		<div class="text-center mb-12">
			<?php if (!empty($approach['eyebrow'])) : ?>
				<p class="text-orange-500 font-semibold uppercase tracking-widest"><?php echo esc_html($approach['eyebrow']); ?></p>
			<?php endif; ?>
			<?php if (!empty($approach['title'])) : ?>
				<h2 class="text-3xl md:text-6xl font-bold text-blue-900 mt-4"><?php echo esc_html($approach['title']); ?></h2>
			<?php endif; ?>
			<?php if (!empty($approach['description'])) : ?>
				<p class="max-w-3xl mx-auto mt-6 text-gray-600"><?php echo esc_html($approach['description']); ?></p>
			<?php endif; ?>
		</div>

		<div role="tablist" class="flex flex-wrap justify-center gap-3 md:gap-4 mb-10">
			<?php foreach ($steps as $i => $step) : ?>
				<button
					type="button"
					role="tab"
					data-tab-btn
					data-active-class="bg-blue-900 text-white shadow-lg"
					data-inactive-class="bg-white text-blue-900 border border-gray-200 hover:border-blue-900"
					class="px-4 md:px-6 py-3 rounded-xl font-semibold transition-all duration-300 <?php echo 0 === (int) $i ? 'bg-blue-900 text-white shadow-lg' : 'bg-white text-blue-900 border border-gray-200 hover:border-blue-900'; ?>"
				>
					<?php echo esc_html($step['title'] ?? ''); ?>
				</button>
			<?php endforeach; ?>
		</div>

		<?php foreach ($steps as $i => $step) : ?>
			<div data-tab-panel role="tabpanel" class="border border-gray-100 rounded-3xl bg-gray-50 p-4 md:p-8 <?php echo 0 === (int) $i ? '' : 'hidden'; ?>">
				<div class="grid md:grid-cols-2 gap-8 md:gap-12 items-center">
					<div class="relative h-[220px] md:h-[380px] rounded-3xl overflow-hidden shadow-lg">
						<?php if (!empty($step['image'])) : ?>
							<img src="<?php echo esc_url(vr_media_url($step['image'])); ?>" alt="<?php echo esc_attr($step['heading'] ?? $step['title'] ?? ''); ?>" class="absolute inset-0 w-full h-full object-cover" decoding="async" loading="lazy"<?php $__ss = vr_srcset($step['image']); if ($__ss !== '') : ?> srcset="<?php echo esc_attr($__ss); ?>" sizes="(max-width: 768px) 100vw, 50vw"<?php endif; ?> />
						<?php else : ?>
							<div class="absolute inset-0 bg-blue-900"></div>
						<?php endif; ?>
						<div class="absolute inset-0 bg-black/35"></div>
						<div class="absolute inset-0 flex flex-col items-center justify-center text-center text-white px-6">
							<?php if (!empty($step['title'])) : ?>
								<p class="uppercase tracking-widest text-sm"><?php echo esc_html($step['title']); ?></p>
							<?php endif; ?>
							<?php if (!empty($step['heading'])) : ?>
								<h3 class="text-3xl md:text-5xl font-bold mt-4"><?php echo esc_html($step['heading']); ?></h3>
							<?php endif; ?>
						</div>
					</div>
					<div>
						<?php if (!empty($step['title'])) : ?>
							<p class="text-orange-500 font-semibold uppercase tracking-widest"><?php echo esc_html($step['title']); ?></p>
						<?php endif; ?>
						<?php if (!empty($step['heading'])) : ?>
							<h3 class="text-3xl md:text-5xl font-bold text-blue-900 mt-3"><?php echo esc_html($step['heading']); ?></h3>
						<?php endif; ?>
						<?php if (!empty($step['description'])) : ?>
							<p class="mt-6 text-base md:text-lg text-gray-600 leading-relaxed"><?php echo esc_html($step['description']); ?></p>
						<?php endif; ?>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</section>
