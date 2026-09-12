<?php
/**
 * Contact bottom CTA
 *
 * @package VR_Doctors
 */

$s = vr_get_page_section('contact', 'cta');
?>
<section class="py-20 md:py-24 bg-gradient-to-r from-blue-950 via-blue-900 to-blue-800 text-white">
	<div class="max-w-4xl mx-auto px-6 text-center">
		<p class="text-orange-400 font-semibold uppercase tracking-widest"><?php echo esc_html($s['eyebrow']); ?></p>
		<h2 class="text-3xl md:text-5xl font-bold mt-3">
			<?php echo vr_page_title_highlight_html($s['title'], $s['title_highlight'] ?? ''); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</h2>
		<p class="mt-6 text-blue-100 leading-relaxed">
			<?php echo esc_html($s['description']); ?>
		</p>
		<div class="flex flex-col sm:flex-row justify-center gap-3 mt-8">
			<a href="<?php echo vr_esc_url($s['primary_cta_url']); ?>" class="bg-orange-500 hover:bg-orange-600 px-6 py-3 rounded-xl font-semibold transition"><?php echo esc_html($s['primary_cta_text']); ?></a>
			<a href="<?php echo esc_url(vr_whatsapp_url()); ?>" target="_blank" rel="noopener noreferrer" class="bg-green-500 hover:bg-green-600 px-6 py-3 rounded-xl font-semibold transition"><?php echo esc_html($s['secondary_cta_text']); ?></a>
		</div>
		<div class="grid md:grid-cols-3 gap-4 mt-12">
			<div class="bg-white/10 rounded-2xl p-5">
				<p class="font-bold text-lg"><?php echo esc_html($s['card_1_title']); ?></p>
				<p class="text-blue-100 text-sm mt-1"><?php echo esc_html($s['card_1_text']); ?></p>
			</div>
			<div class="bg-white/10 rounded-2xl p-5">
				<p class="font-bold text-lg"><?php echo esc_html($s['card_2_title']); ?></p>
				<p class="text-blue-100 text-sm mt-1"><?php echo esc_html($s['card_2_text']); ?></p>
			</div>
			<div class="bg-white/10 rounded-2xl p-5">
				<p class="font-bold text-lg"><?php echo esc_html($s['card_3_title']); ?></p>
				<p class="text-blue-100 text-sm mt-1"><?php echo esc_html($s['card_3_text']); ?></p>
			</div>
		</div>
	</div>
</section>
