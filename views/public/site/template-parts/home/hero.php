<?php
/**
 * Home hero carousel
 *
 * @package VR_Doctors
 */

$hero   = vr_get_home_hero();
$slides = $hero['slides'];

if (empty($slides) || !is_array($slides)) {
	return;
}

// Ensure first slide keys exist (defensive).
$first = $slides[0];
?>
<section class="relative h-[50vh] md:h-[85vh] overflow-hidden" data-hero-carousel>
	<?php foreach ($slides as $i => $slide) : ?>
		<?php
		$slide_title = isset($slide['title']) ? $slide['title'] : '';
		$slide_sub   = isset($slide['subtitle']) ? $slide['subtitle'] : '';
		$slide_desc  = isset($slide['description']) ? $slide['description'] : '';
		$slide_img   = !empty($slide['image']) ? $slide['image'] : '';
		if ($slide_img === '' && $slide_title === '') {
			continue;
		}
		?>
		<div
			class="absolute inset-0 z-0 transition-opacity duration-700 <?php echo 0 === (int) $i ? 'opacity-100' : 'opacity-0'; ?>"
			data-hero-slide
			data-title="<?php echo esc_attr($slide_title); ?>"
			data-subtitle="<?php echo esc_attr($slide_sub); ?>"
			data-description="<?php echo esc_attr($slide_desc); ?>"
			aria-hidden="<?php echo 0 === (int) $i ? 'false' : 'true'; ?>"
		>
			<?php if ($slide_img !== '') : ?>
				<img src="<?php echo esc_url(vr_media_url($slide_img)); ?>" alt="<?php echo esc_attr($slide_title); ?>" class="absolute inset-0 w-full h-full object-cover" decoding="async" <?php echo 0 === (int) $i ? 'fetchpriority="high"' : 'loading="lazy"'; ?> />
			<?php else : ?>
				<div class="absolute inset-0 bg-blue-950"></div>
			<?php endif; ?>
		</div>
	<?php endforeach; ?>

	<!-- Dark Overlay — above images so white text stays readable -->
	<div class="absolute inset-0 z-[1] bg-black/65"></div>
	<div class="absolute inset-0 z-[1] bg-gradient-to-br from-blue-950/50 via-transparent to-blue-900/40"></div>

	<div class="relative z-10 max-w-6xl mx-auto h-full flex items-center px-4 md:px-6" data-hero-content>
		<div class="max-w-2xl text-white drop-shadow-sm">
			<p class="uppercase tracking-[3px] md:tracking-[4px] text-orange-400 font-semibold text-sm md:text-base" data-hero-subtitle>
				<?php echo esc_html($first['subtitle'] ?? ''); ?>
			</p>
			<h1 class="text-3xl md:text-7xl font-bold mt-4 leading-tight text-white" data-hero-title>
				<?php echo esc_html($first['title'] ?? ''); ?>
			</h1>
			<p class="mt-4 md:mt-6 text-sm md:text-lg text-gray-100 max-w-xl" data-hero-desc>
				<?php echo esc_html($first['description'] ?? ''); ?>
			</p>
			<div class="flex flex-col sm:flex-row gap-3 mt-6 md:mt-8">
				<?php if (!empty($hero['primary_cta_text'])) : ?>
					<a href="<?php echo vr_esc_url($hero['primary_cta_url'] ?: home_url('/contact/')); ?>" class="bg-orange-500 hover:bg-orange-600 transition px-6 py-3 md:px-8 md:py-4 rounded-xl font-semibold text-center text-white">
						<?php echo esc_html($hero['primary_cta_text']); ?>
					</a>
				<?php endif; ?>
				<?php if (!empty($hero['secondary_cta_text'])) : ?>
					<a href="<?php echo vr_esc_url($hero['secondary_cta_url'] ?: '#approach'); ?>" class="border border-white hover:bg-white hover:text-blue-900 transition px-6 py-3 md:px-8 md:py-4 rounded-xl font-semibold text-center text-white">
						<?php echo esc_html($hero['secondary_cta_text']); ?>
					</a>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<button type="button" data-hero-prev class="absolute left-2 md:left-4 top-1/2 -translate-y-1/2 z-20 bg-white/20 backdrop-blur-sm text-white w-10 h-10 md:w-12 md:h-12 rounded-full hover:bg-white/40 transition" aria-label="Previous">←</button>
	<button type="button" data-hero-next class="absolute right-2 md:right-4 top-1/2 -translate-y-1/2 z-20 bg-white/20 backdrop-blur-sm text-white w-10 h-10 md:w-12 md:h-12 rounded-full hover:bg-white/40 transition" aria-label="Next">→</button>

	<div class="absolute bottom-4 md:bottom-8 left-1/2 -translate-x-1/2 flex gap-3 z-20">
		<?php foreach ($slides as $i => $slide) : ?>
			<button type="button" data-hero-dot class="w-6 h-6 p-1.5 box-border bg-clip-content rounded-full transition <?php echo 0 === (int) $i ? 'bg-orange-500' : 'bg-white/50'; ?>" aria-label="<?php echo esc_attr(sprintf(/* translators: slide number */ __('Slide %d', 'vr-doctors'), (int) $i + 1)); ?>"></button>
		<?php endforeach; ?>
	</div>
</section>
