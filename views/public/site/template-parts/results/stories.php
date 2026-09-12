<?php
/**
 * Success stories YouTube embeds
 *
 * @package VR_Doctors
 */

$s      = vr_get_page_section('results', 'stories');
$videos = !empty($s['items']) && is_array($s['items']) ? $s['items'] : array();
?>
<section class="py-12 md:py-16 bg-gray-50">
	<div class="max-w-6xl mx-auto px-4 md:px-6">
		<div class="text-center mb-10">
			<p class="text-orange-500 font-semibold uppercase tracking-widest"><?php echo esc_html($s['eyebrow']); ?></p>
			<h2 class="text-3xl md:text-5xl font-bold text-blue-900 mt-3"><?php echo esc_html($s['title']); ?></h2>
			<p class="mt-4 text-gray-600 max-w-2xl mx-auto"><?php echo esc_html($s['description']); ?></p>
		</div>
		<div class="grid md:grid-cols-2 gap-6">
			<?php foreach ($videos as $video) : ?>
				<?php
				$embed = $video['embedId'] ?? '';
				// Accept full YouTube URLs and extract ID.
				if (preg_match('/(?:v=|youtu\.be\/|embed\/)([A-Za-z0-9_-]{6,})/', $embed, $m)) {
					$embed = $m[1];
				}
				if ($embed === '') {
					continue;
				}
				?>
				<div class="bg-white rounded-2xl overflow-hidden shadow border border-gray-100">
					<div class="aspect-video">
						<iframe
							class="w-full h-full"
							src="https://www.youtube.com/embed/<?php echo esc_attr($embed); ?>"
							title="<?php echo esc_attr($video['title'] ?? ''); ?>"
							frameborder="0"
							allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
							allowfullscreen
							loading="lazy"
						></iframe>
					</div>
					<div class="p-5">
						<h3 class="font-bold text-blue-900"><?php echo esc_html($video['title'] ?? ''); ?></h3>
						<p class="mt-2 text-sm text-gray-600"><?php echo esc_html($video['description'] ?? ''); ?></p>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
		<div class="text-center mt-10">
			<a href="<?php echo esc_url(vr_option('youtube', 'https://www.youtube.com/@VR_JuniorCollege')); ?>" target="_blank" rel="noopener noreferrer" class="inline-block bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded-xl font-semibold transition">
				<?php echo esc_html($s['cta_text']); ?>
			</a>
		</div>
	</div>
</section>
