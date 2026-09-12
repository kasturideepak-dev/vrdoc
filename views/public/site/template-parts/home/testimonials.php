<?php
/**
 * Testimonials carousel
 *
 * @package VR_Doctors
 */

$section = vr_get_home_testimonials_section();
$items   = vr_get_ordered_posts('testimonial', vr_fallback_testimonials());

if (empty($items) || !is_array($items)) {
	return;
}
?>
<section class="py-12 md:py-16 bg-white" data-testimonials>
	<div class="max-w-6xl mx-auto px-4 md:px-6">
		<div class="grid grid-cols-1 md:grid-cols-[38%_62%] gap-10 items-start">
			<div>
				<?php if (!empty($section['eyebrow'])) : ?>
					<p class="text-orange-500 font-semibold uppercase tracking-widest text-sm"><?php echo esc_html($section['eyebrow']); ?></p>
				<?php endif; ?>
				<?php if (!empty($section['title'])) : ?>
					<h2 class="text-3xl md:text-4xl font-bold text-blue-900 mt-3 leading-tight"><?php echo esc_html($section['title']); ?></h2>
				<?php endif; ?>
				<div class="w-12 h-1 bg-orange-500 rounded-full mt-4"></div>
				<?php if (!empty($section['cta_text'])) : ?>
					<a href="<?php echo vr_esc_url($section['cta_url'] ?: home_url('/results/')); ?>" class="inline-block mt-6 bg-orange-500 hover:bg-orange-600 transition text-white px-6 py-3 rounded-xl font-semibold">
						<?php echo esc_html($section['cta_text']); ?>
					</a>
				<?php endif; ?>
			</div>

			<div class="hidden md:grid grid-cols-2 gap-6" data-testimonials-desktop></div>
			<div class="md:hidden" data-testimonials-mobile></div>
		</div>

		<div class="flex justify-center items-center gap-6 mt-10">
			<button type="button" data-testimonial-prev class="w-11 h-11 rounded-full bg-white border shadow hover:bg-orange-500 hover:text-white transition" aria-label="Previous">
				<i class="fa-solid fa-chevron-left"></i>
			</button>
			<div class="flex gap-2">
				<?php foreach ($items as $i => $item) : ?>
					<button type="button" data-testimonial-dot class="w-3 h-3 rounded-full transition-all <?php echo 0 === (int) $i ? 'bg-orange-500 scale-125' : 'bg-gray-300'; ?>" aria-label="<?php echo esc_attr(sprintf(/* translators: testimonial number */ __('Testimonial %d', 'vr-doctors'), (int) $i + 1)); ?>"></button>
				<?php endforeach; ?>
			</div>
			<button type="button" data-testimonial-next class="w-11 h-11 rounded-full bg-white border shadow hover:bg-orange-500 hover:text-white transition" aria-label="Next">
				<i class="fa-solid fa-chevron-right"></i>
			</button>
		</div>

		<div class="hidden" data-testimonials-source>
			<?php foreach ($items as $item) : ?>
				<?php
				$meta = isset($item['meta']) && is_array($item['meta']) ? $item['meta'] : array();
				?>
				<div class="bg-white rounded-3xl shadow-lg border border-gray-200 p-6 h-full flex flex-col" data-testimonial-card>
					<div class="flex justify-between items-start">
						<?php if (!empty($item['image'])) : ?>
							<img src="<?php echo esc_url($item['image']); ?>" alt="<?php echo esc_attr($item['title'] ?? ''); ?>" width="96" height="96" class="rounded-full object-cover border-[3px] border-orange-500 shadow-md w-24 h-24" />
						<?php endif; ?>
						<?php if (!empty($meta['rank_badge'])) : ?>
							<span class="bg-orange-500 text-white text-xs font-semibold px-3 py-2 rounded-full">
								<?php echo esc_html($meta['rank_badge']); ?>
							</span>
						<?php endif; ?>
					</div>
					<?php if (!empty($meta['quote'])) : ?>
						<p class="mt-6 text-gray-700 leading-relaxed italic flex-grow">
							"<?php echo esc_html($meta['quote']); ?>"
						</p>
					<?php endif; ?>
					<div class="border-t my-6"></div>
					<p class="text-xs uppercase tracking-wider text-gray-500">Now Studying At</p>
					<?php if (!empty($meta['college'])) : ?>
						<h3 class="text-lg font-bold text-blue-900 mt-1"><?php echo esc_html($meta['college']); ?></h3>
					<?php endif; ?>
					<?php if (!empty($item['title'])) : ?>
						<p class="text-sm text-gray-500 mt-1"><?php echo esc_html($item['title']); ?></p>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
