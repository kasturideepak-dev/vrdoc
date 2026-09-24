<?php
/**
 * Top achievers marquee
 *
 * @package VR_Doctors
 */

$s        = vr_get_page_section('results', 'achievers');
$items    = vr_get_ordered_posts('achiever', vr_fallback_achievers());
$loop     = array_merge($items, $items);
?>
<section id="achievers" class="py-12 md:py-16 bg-white overflow-hidden">
	<div class="max-w-6xl mx-auto px-4 md:px-6 mb-10 text-center">
		<p class="text-orange-500 font-semibold uppercase tracking-widest"><?php echo esc_html($s['eyebrow']); ?></p>
		<h2 class="text-3xl md:text-5xl font-bold text-blue-900 mt-3"><?php echo esc_html($s['title']); ?></h2>
		<p class="mt-4 text-gray-600"><?php echo esc_html($s['description']); ?></p>
	</div>
	<div class="overflow-hidden">
		<div class="flex marquee gap-5 w-max px-4">
			<?php foreach ($loop as $item) : ?>
				<div class="w-64 shrink-0 bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
					<div class="flex items-center gap-3">
						<?php if (!empty($item['image'])) : ?>
							<img src="<?php echo esc_url(vr_media_url($item['image'])); ?>" alt="<?php echo esc_attr($item['title']); ?>" class="w-[60px] h-[60px] rounded-full object-cover" width="60" height="60" decoding="async" loading="lazy" />
						<?php endif; ?>
						<div>
							<p class="font-bold text-blue-900 text-sm"><?php echo esc_html($item['title']); ?></p>
							<p class="text-orange-500 text-xs font-semibold"><?php echo esc_html($item['meta']['rank']); ?></p>
						</div>
					</div>
					<p class="mt-3 text-xs text-gray-500"><?php echo esc_html($item['meta']['college']); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
