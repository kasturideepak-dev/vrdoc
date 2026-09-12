<?php
/**
 * Journey timeline
 *
 * @package VR_Doctors
 */

$s     = vr_get_page_section('about', 'timeline');
$items = function_exists('vr_get_about_timeline_items')
	? vr_get_about_timeline_items()
	: vr_get_ordered_posts('timeline_event', vr_fallback_timeline());

if (!is_array($items)) {
	$items = array();
}

$items = array_values(
	array_filter(
		$items,
		static function ($item) {
			if (!is_array($item)) {
				return false;
			}
			$year  = trim((string) ($item['meta']['year'] ?? ''));
			$title = trim((string) ($item['title'] ?? ''));
			return $year !== '' || $title !== '';
		}
	)
);

if (empty($items)) {
	return;
}
?>
<section class="py-12 md:py-16 bg-white" data-tabs>
	<div class="max-w-6xl mx-auto px-4 md:px-6">
		<div class="text-center mb-10">
			<p class="text-orange-500 font-semibold uppercase tracking-widest"><?php echo esc_html($s['eyebrow'] ?? ''); ?></p>
			<h2 class="text-3xl md:text-5xl font-bold text-blue-900 mt-3"><?php echo esc_html($s['title'] ?? ''); ?></h2>
			<p class="mt-4 text-gray-600 max-w-2xl mx-auto"><?php echo esc_html($s['description'] ?? ''); ?></p>
		</div>

		<div class="flex justify-start md:justify-center flex-nowrap md:flex-wrap gap-3 mb-8 overflow-x-auto pb-2" role="tablist">
			<?php foreach ($items as $i => $item) : ?>
				<?php
				$year  = (string) ($item['meta']['year'] ?? '');
				$title = (string) ($item['title'] ?? '');
				?>
				<button
					type="button"
					role="tab"
					data-tab-btn
					data-active-class="bg-orange-500 text-white"
					data-inactive-class="bg-blue-50 text-blue-900"
					class="shrink-0 px-5 py-2 rounded-full font-semibold transition <?php echo 0 === $i ? 'bg-orange-500 text-white' : 'bg-blue-50 text-blue-900'; ?>"
				>
					<?php echo esc_html($year !== '' ? $year : $title); ?>
				</button>
			<?php endforeach; ?>
		</div>

		<?php foreach ($items as $i => $item) : ?>
			<?php
			$year    = (string) ($item['meta']['year'] ?? '');
			$title   = (string) ($item['title'] ?? '');
			$content = (string) ($item['content'] ?? '');
			$image   = (string) ($item['image'] ?? '');
			?>
			<div data-tab-panel role="tabpanel" class="<?php echo 0 === $i ? '' : 'hidden'; ?>">
				<div class="relative rounded-3xl overflow-hidden min-h-[320px] md:min-h-[420px]">
					<?php if ($image !== '') : ?>
						<img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($title); ?>" class="absolute inset-0 w-full h-full object-cover" />
					<?php endif; ?>
					<div class="absolute inset-0 bg-gradient-to-t from-black/95 via-black/50 to-transparent"></div>
					<div class="absolute bottom-0 left-0 right-0 z-10 p-8 md:p-12 text-white">
						<?php if ($year !== '') : ?>
							<p class="text-orange-400 font-bold text-xl"><?php echo esc_html($year); ?></p>
						<?php endif; ?>
						<?php if ($title !== '') : ?>
							<h3 class="text-2xl md:text-4xl font-bold mt-2"><?php echo esc_html($title); ?></h3>
						<?php endif; ?>
						<?php if ($content !== '') : ?>
							<p class="mt-3 text-gray-200 max-w-2xl"><?php echo nl2br(esc_html($content), false); ?></p>
						<?php endif; ?>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</section>
