<?php
/**
 * NEET landing — final CTA
 *
 * @package VR_Doctors
 */

$slug = vr_neet_landing_slug();
if ($slug === '') {
	return;
}
$s = vr_get_page_section($slug, 'final_cta');
if (trim((string) ($s['title'] ?? '')) === '') {
	return;
}
?>
<section class="py-12 md:py-16 bg-blue-900 text-white">
	<div class="max-w-4xl mx-auto px-4 md:px-6 text-center">
		<p class="text-orange-400 font-semibold uppercase tracking-widest text-sm md:text-base"><?php echo esc_html($s['eyebrow']); ?></p>
		<h2 class="mt-3 md:mt-4 text-3xl md:text-4xl font-bold leading-tight"><?php echo esc_html($s['title']); ?></h2>
		<?php if (!empty($s['description'])) : ?>
			<p class="mt-4 md:mt-5 text-sm md:text-base text-blue-100 leading-relaxed max-w-2xl mx-auto"><?php echo esc_html($s['description']); ?></p>
		<?php endif; ?>
		<div class="mt-8 flex flex-col sm:flex-row justify-center gap-3 md:gap-4">
			<?php if (!empty($s['primary_cta_text'])) : ?>
				<a href="<?php echo vr_esc_url($s['primary_cta_url'] ?? '/contact/'); ?>" class="bg-orange-500 hover:bg-orange-600 px-6 md:px-8 py-3 rounded-xl font-semibold transition text-sm md:text-base"><?php echo esc_html($s['primary_cta_text']); ?></a>
			<?php endif; ?>
			<?php if (!empty($s['secondary_cta_text'])) : ?>
				<a href="<?php echo vr_esc_url($s['secondary_cta_url'] ?? 'tel:+917097098877'); ?>" class="border border-white hover:bg-white hover:text-blue-900 px-6 md:px-8 py-3 rounded-xl font-semibold transition text-sm md:text-base"><?php echo esc_html($s['secondary_cta_text']); ?></a>
			<?php endif; ?>
		</div>
		<div class="mt-8 md:mt-10 space-y-3 text-sm md:text-base text-blue-100">
			<?php if (!empty($s['address'])) : ?>
				<p class="inline-flex items-start justify-center gap-2 max-w-2xl mx-auto">
					<i class="fa-solid fa-location-dot text-orange-400 mt-1 shrink-0"></i>
					<span><?php echo esc_html($s['address']); ?></span>
				</p>
			<?php endif; ?>
			<div class="flex flex-col sm:flex-row items-center justify-center gap-3 sm:gap-6">
				<?php if (!empty($s['phone_display'])) : ?>
					<a href="<?php echo vr_esc_url($s['phone_url'] ?? 'tel:+917097098877'); ?>" class="inline-flex items-center gap-2 hover:text-orange-300 transition">
						<i class="fa-solid fa-phone text-orange-400"></i><?php echo esc_html($s['phone_display']); ?>
					</a>
				<?php endif; ?>
				<?php if (!empty($s['email'])) : ?>
					<a href="mailto:<?php echo esc_attr($s['email']); ?>" class="inline-flex items-center gap-2 hover:text-orange-300 transition">
						<i class="fa-solid fa-envelope text-orange-400"></i><?php echo esc_html($s['email']); ?>
					</a>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
