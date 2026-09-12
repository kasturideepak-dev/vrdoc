<?php
/**
 * Impact section
 *
 * @package VR_Doctors
 */

$s       = vr_get_page_section('about', 'impact');
$impacts = !empty($s['items']) && is_array($s['items']) ? $s['items'] : array();
?>
<section class="py-12 md:py-16 bg-gradient-to-br from-blue-950 via-blue-900 to-blue-800 text-white">
	<div class="max-w-6xl mx-auto px-4 md:px-6">
		<div class="text-center mb-12">
			<p class="text-orange-400 font-semibold uppercase tracking-widest"><?php echo esc_html($s['eyebrow']); ?></p>
			<h2 class="text-3xl md:text-5xl font-bold mt-3"><?php echo esc_html($s['title']); ?></h2>
			<p class="mt-4 text-blue-100 max-w-2xl mx-auto"><?php echo esc_html($s['description']); ?></p>
		</div>
		<div class="space-y-8">
			<?php foreach ($impacts as $i => $impact) : ?>
				<?php $reverse = $i % 2 === 1; ?>
				<div class="grid md:grid-cols-[300px_1fr] gap-6 items-center <?php echo $reverse ? 'md:[grid-template-columns:1fr_300px]' : ''; ?>">
					<div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl p-8 text-center <?php echo $reverse ? 'md:order-2' : ''; ?>">
						<p class="text-4xl md:text-5xl font-bold"><?php echo esc_html($impact['stat'] ?? ''); ?></p>
						<p class="mt-2 font-semibold"><?php echo esc_html($impact['title'] ?? ''); ?></p>
					</div>
					<div class="<?php echo $reverse ? 'md:order-1' : ''; ?>">
						<p class="text-blue-100 leading-relaxed text-lg"><?php echo esc_html($impact['story'] ?? ''); ?></p>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
		<p class="text-center mt-12 text-orange-300 italic text-lg"><?php echo esc_html($s['quote']); ?></p>
	</div>
</section>
