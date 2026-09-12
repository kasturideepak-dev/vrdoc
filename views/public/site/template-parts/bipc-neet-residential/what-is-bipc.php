<?php
/**
 * BiPC NEET residential — what is BiPC
 *
 * @package VR_Doctors
 */

$slug = 'best-bipc-college-in-hyderabad-neet-residential';
$s    = vr_get_page_section($slug, 'what_is_bipc');
?>
<section class="py-12 md:py-16 bg-white">
	<div class="max-w-6xl mx-auto px-4 md:px-6">
		<div class="rounded-3xl border border-gray-200 bg-gradient-to-br from-blue-50 to-white p-6 md:p-10 shadow-lg">
			<p class="text-orange-500 font-semibold uppercase tracking-widest text-sm md:text-base">Understanding BiPC</p>
			<h2 class="mt-3 md:mt-4 text-3xl md:text-4xl font-bold text-blue-900 leading-tight max-w-3xl">
				<?php echo esc_html($s['title']); ?>
			</h2>
			<div class="mt-6 space-y-4 text-sm md:text-base text-gray-600 leading-relaxed max-w-4xl">
				<p><?php echo esc_html($s['paragraph_1']); ?></p>
				<p><?php echo esc_html($s['paragraph_2']); ?></p>
			</div>
		</div>
	</div>
</section>
