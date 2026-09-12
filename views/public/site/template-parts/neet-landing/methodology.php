<?php
/**
 * NEET landing — teaching methodology cards
 *
 * @package VR_Doctors
 */

$slug = vr_neet_landing_slug();
if ($slug === '') {
	return;
}
$s     = vr_get_page_section($slug, 'methodology');
$items = vr_neet_filter_rows_any($s['items'] ?? array(), array('title', 'description'));
if (empty($items)) {
	return;
}
?>
<section class="py-12 md:py-16 bg-white">
	<div class="max-w-6xl mx-auto px-4 md:px-6">
		<div class="text-center max-w-3xl mx-auto">
			<p class="text-orange-500 font-semibold uppercase tracking-widest text-sm md:text-base"><?php echo esc_html($s['eyebrow']); ?></p>
			<h2 class="mt-3 md:mt-4 text-3xl md:text-4xl font-bold text-blue-900 leading-tight"><?php echo esc_html($s['title']); ?></h2>
		</div>
		<div class="mt-8 grid md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-5">
			<?php foreach ($items as $card) : ?>
				<div class="rounded-2xl border border-gray-200 p-5 md:p-6 shadow-md">
					<h3 class="text-lg font-bold text-blue-900"><?php echo esc_html($card['title'] ?? ''); ?></h3>
					<?php
					$desc = (string) ($card['description'] ?? '');
					if (false !== strpos($desc, "\n")) :
						$lines = array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $desc)));
						?>
						<ul class="mt-3 space-y-2 text-sm text-gray-600">
							<?php foreach ($lines as $line) : ?>
								<li>• <?php echo esc_html($line); ?></li>
							<?php endforeach; ?>
						</ul>
					<?php else : ?>
						<p class="mt-3 text-sm text-gray-600 leading-relaxed"><?php echo esc_html($desc); ?></p>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
