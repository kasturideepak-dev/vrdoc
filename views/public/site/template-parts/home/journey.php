<?php
/**
 * Journey stats section
 *
 * @package VR_Doctors
 */

$journey = vr_get_home_journey();
$stats   = !empty($journey['stats']) && is_array($journey['stats']) ? $journey['stats'] : array();
?>
<section class="py-8 md:py-16 bg-white">
	<div class="max-w-4xl mx-auto px-4 md:px-4">
		<div class="text-center mb-10">
			<?php if (!empty($journey['eyebrow'])) : ?>
				<p class="text-orange-500 font-semibold uppercase tracking-widest"><?php echo esc_html($journey['eyebrow']); ?></p>
			<?php endif; ?>
			<?php if (!empty($journey['title'])) : ?>
				<h2 class="text-3xl md:text-5xl font-bold text-blue-900 mt-4"><?php echo esc_html($journey['title']); ?></h2>
			<?php endif; ?>
			<?php if (!empty($journey['description'])) : ?>
				<p class="max-w-3xl mx-auto mt-4 text-gray-600"><?php echo esc_html($journey['description']); ?></p>
			<?php endif; ?>
		</div>

		<?php if (!empty($stats)) : ?>
			<div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
				<?php foreach ($stats as $stat) : ?>
					<div
						class="text-center border border-gray-200 rounded-2xl p-6 bg-gray-50"
						data-counter
						data-target="<?php echo esc_attr((string) (int) ($stat['target'] ?? 0)); ?>"
						data-suffix="<?php echo esc_attr($stat['suffix'] ?? ''); ?>"
					>
						<h3 class="text-3xl md:text-5xl font-bold text-blue-900">
							<span data-counter-num>0</span>
						</h3>
						<?php if (!empty($stat['label'])) : ?>
							<p class="mt-2 text-gray-600"><?php echo esc_html($stat['label']); ?></p>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php if (!empty($journey['footer_note'])) : ?>
			<p class="text-center text-gray-500 mt-8 text-sm"><?php echo esc_html($journey['footer_note']); ?></p>
		<?php endif; ?>
	</div>
</section>
