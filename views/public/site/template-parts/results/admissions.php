<?php
/**
 * Medical admissions gallery + lightbox
 *
 * @package VR_Doctors
 */

$s      = vr_get_page_section('results', 'admissions');
$images = array();
for ($i = 1; $i <= 6; $i++) {
	$key = 'image_' . $i;
	if (!empty($s[ $key ])) {
		$images[] = $s[ $key ];
	}
}
?>
<section class="py-12 md:py-16 bg-blue-50" data-lightbox-root>
	<div class="max-w-6xl mx-auto px-4 md:px-6">
		<div class="text-center mb-10">
			<p class="text-orange-500 font-semibold uppercase tracking-widest"><?php echo esc_html($s['eyebrow']); ?></p>
			<h2 class="text-3xl md:text-5xl font-bold text-blue-900 mt-3"><?php echo esc_html($s['title']); ?></h2>
			<p class="mt-4 text-gray-600 max-w-2xl mx-auto"><?php echo esc_html($s['description']); ?></p>
		</div>
		<div class="grid grid-cols-2 md:grid-cols-3 gap-4">
			<?php foreach ($images as $i => $url) : ?>
				<button type="button" class="block w-full overflow-hidden rounded-2xl shadow hover:shadow-xl transition p-0 border-0 cursor-pointer" data-lightbox-src="<?php echo esc_url($url); ?>">
					<img src="<?php echo esc_url($url); ?>" alt="<?php echo esc_attr(sprintf(/* translators: image number */ __('Medical Admission %d', 'vr-doctors'), $i + 1)); ?>" class="w-full h-auto object-cover pointer-events-none" />
				</button>
			<?php endforeach; ?>
		</div>
	</div>

	<div class="hidden fixed inset-0 z-50 bg-black/90 flex items-center justify-center p-4" data-lightbox-overlay>
		<button type="button" class="absolute top-4 right-4 text-white text-3xl w-12 h-12" data-lightbox-close aria-label="Close">×</button>
		<img src="" alt="College" class="max-w-full max-h-[90vh] object-contain" data-lightbox-img />
	</div>
</section>
