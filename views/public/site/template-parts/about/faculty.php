<?php
/**
 * Faculty section
 *
 * @package VR_Doctors
 */

$s     = vr_get_page_section('about', 'faculty');
$items = vr_get_ordered_posts('faculty', vr_fallback_faculty());
?>
<section class="py-12 md:py-16 bg-blue-50">
	<div class="max-w-6xl mx-auto px-4 md:px-6">
		<div class="text-center mb-10">
			<p class="text-orange-500 font-semibold uppercase tracking-widest"><?php echo esc_html($s['eyebrow']); ?></p>
			<h2 class="text-3xl md:text-5xl font-bold text-blue-900 mt-3"><?php echo esc_html($s['title']); ?></h2>
			<p class="mt-4 text-gray-600 max-w-2xl mx-auto"><?php echo esc_html($s['description']); ?></p>
		</div>
		<div class="grid grid-cols-2 lg:grid-cols-3 gap-6">
			<?php foreach ($items as $item) : ?>
				<div class="bg-white rounded-2xl shadow border border-gray-100 overflow-hidden text-center p-6">
					<?php if (!empty($item['image'])) : ?>
						<img src="<?php echo esc_url(vr_media_url($item['image'])); ?>" alt="<?php echo esc_attr($item['title']); ?>" class="w-32 h-32 mx-auto rounded-full object-cover border-4 border-orange-400" decoding="async" loading="lazy"<?php $__ss = vr_srcset($item['image']); if ($__ss !== '') : ?> srcset="<?php echo esc_attr($__ss); ?>" sizes="(max-width: 768px) 100vw, 50vw"<?php endif; ?> />
					<?php endif; ?>
					<h3 class="mt-4 text-lg font-bold text-blue-900"><?php echo esc_html($item['title']); ?></h3>
					<p class="text-orange-500 font-semibold text-sm mt-1"><?php echo esc_html($item['meta']['subject']); ?></p>
					<p class="text-gray-500 text-sm mt-1"><?php echo esc_html($item['meta']['experience']); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
