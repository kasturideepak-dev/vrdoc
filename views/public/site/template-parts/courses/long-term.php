<?php
/**
 * Long-term NEET program
 *
 * @package VR_Doctors
 */

$s        = vr_get_page_section('courses', 'long_term');
$features = !empty($s['features']) && is_array($s['features']) ? $s['features'] : array();
?>
<section id="Longterm" class="py-12 md:py-16 bg-blue-50 scroll-mt-24">
	<div class="max-w-6xl mx-auto px-4 md:px-6 grid md:grid-cols-2 gap-10 items-center">
		<div>
			<p class="text-orange-500 font-semibold uppercase tracking-widest"><?php echo esc_html($s['eyebrow']); ?></p>
			<h2 class="text-3xl md:text-4xl font-bold text-blue-900 mt-3"><?php echo esc_html($s['title']); ?></h2>
			<p class="mt-4 text-gray-600 leading-relaxed">
				<?php echo esc_html($s['description']); ?>
			</p>
			<ul class="mt-6 space-y-3">
				<?php foreach ($features as $f) : ?>
					<li class="flex items-start gap-2 text-gray-700"><span class="text-blue-900 font-bold">✓</span> <?php echo esc_html($f); ?></li>
				<?php endforeach; ?>
			</ul>
			<a href="<?php echo vr_esc_url($s['cta_url']); ?>" class="inline-block mt-8 bg-gradient-to-r from-blue-900 to-blue-800 text-white px-6 py-3 rounded-xl font-semibold hover:opacity-90 transition">
				<?php echo esc_html($s['cta_text']); ?>
			</a>
		</div>
		<div>
			<img src="<?php echo esc_url($s['image']); ?>" alt="<?php echo esc_attr($s['image_alt']); ?>" class="w-full rounded-3xl object-cover shadow-lg" />
		</div>
	</div>
</section>
