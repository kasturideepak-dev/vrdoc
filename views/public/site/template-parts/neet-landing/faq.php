<?php
/**
 * NEET landing — FAQ accordion
 *
 * @package VR_Doctors
 */

$slug = vr_neet_landing_slug();
if ($slug === '') {
	return;
}
$s     = vr_get_page_section($slug, 'faq');
$items = vr_neet_filter_faq_items($s['items'] ?? array());
if (empty($items)) {
	return;
}
?>
<section class="py-12 md:py-16 bg-blue-50" data-faq-accordion>
	<div class="max-w-4xl mx-auto px-4 md:px-6">
		<div class="text-center">
			<p class="text-orange-500 font-semibold uppercase tracking-widest text-sm md:text-base"><?php echo esc_html($s['eyebrow']); ?></p>
			<h2 class="mt-3 md:mt-4 text-3xl md:text-4xl font-bold text-blue-900"><?php echo esc_html($s['title']); ?></h2>
		</div>
		<div class="mt-8 md:mt-10 space-y-3">
			<?php foreach ($items as $index => $faq) : ?>
				<?php $is_open = 0 === $index; ?>
				<div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden" data-faq-item>
					<button type="button" class="w-full flex items-start justify-between gap-4 px-5 py-4 md:px-6 md:py-5 text-left" data-faq-toggle aria-expanded="<?php echo $is_open ? 'true' : 'false'; ?>">
						<span class="text-sm md:text-base font-bold text-blue-900 leading-snug"><?php echo esc_html((string) ($index + 1)); ?>. <?php echo esc_html($faq['question']); ?></span>
						<i class="fa-solid fa-chevron-down text-orange-500 mt-1 shrink-0 transition-transform duration-200 <?php echo $is_open ? 'rotate-180' : ''; ?>" data-faq-icon></i>
					</button>
					<div class="px-5 pb-5 md:px-6 md:pb-6 -mt-1 <?php echo $is_open ? '' : 'hidden'; ?>" data-faq-panel>
						<p class="text-sm md:text-base text-gray-600 leading-relaxed"><?php echo esc_html($faq['answer']); ?></p>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
