<?php
/**
 * BiPC NEET residential — intermediate overview
 *
 * @package VR_Doctors
 */

$slug       = 'best-bipc-college-in-hyderabad-neet-residential';
$s          = vr_get_page_section($slug, 'intermediate');
$highlights = !empty($s['features']) && is_array($s['features']) ? $s['features'] : array();
if (empty($highlights) && trim((string) ($s['title'] ?? '')) === '') {
	return;
}
?>
<section class="py-12 md:py-16 bg-white">
	<div class="max-w-6xl mx-auto px-4 md:px-6">
		<div class="grid md:grid-cols-2 gap-8 md:gap-12 items-center">
			<?php if (!empty($s['image'])) : ?>
			<div>
				<img
					src="<?php echo esc_url($s['image']); ?>"
					alt="<?php echo esc_attr($s['image_alt']); ?>"
					class="w-full rounded-2xl shadow-lg object-cover aspect-[4/3]"
				/>
			</div>
			<?php endif; ?>
			<div>
				<p class="text-orange-500 font-semibold uppercase tracking-widest text-sm md:text-base"><?php echo esc_html($s['eyebrow']); ?></p>
				<h2 class="mt-3 md:mt-4 text-3xl md:text-4xl font-bold text-blue-900 leading-tight">
					<?php echo esc_html($s['title']); ?>
				</h2>
				<?php if (!empty($s['description'])) : ?>
				<p class="mt-4 md:mt-5 text-sm md:text-base text-gray-600 leading-relaxed">
					<?php echo esc_html($s['description']); ?>
				</p>
				<?php endif; ?>
				<?php if (!empty($highlights)) : ?>
				<ul class="mt-6 space-y-3">
					<?php foreach ($highlights as $item) : ?>
						<li class="flex items-start gap-3 text-sm md:text-base text-gray-700">
							<span class="mt-0.5 w-5 h-5 rounded-full bg-orange-100 flex items-center justify-center shrink-0">
								<i class="fa-solid fa-check text-orange-600 text-xs"></i>
							</span>
							<span><?php echo esc_html($item); ?></span>
						</li>
					<?php endforeach; ?>
				</ul>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
