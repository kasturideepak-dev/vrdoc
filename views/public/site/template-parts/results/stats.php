<?php
/**
 * Results stats
 *
 * @package VR_Doctors
 */

$s     = vr_get_page_section('results', 'stats');
$stats = !empty($s['items']) && is_array($s['items']) ? $s['items'] : array();
?>
<section class="py-10 md:py-12 bg-white">
	<div class="max-w-6xl mx-auto px-4 md:px-6">
		<div class="grid md:grid-cols-3 gap-6">
			<?php foreach ($stats as $stat) : ?>
				<div class="border border-gray-200 rounded-2xl p-6 text-center bg-gray-50">
					<div class="w-12 h-12 mx-auto rounded-full bg-blue-900 text-white flex items-center justify-center">
						<i class="fa-solid <?php echo esc_attr($stat['icon'] ?? 'fa-star'); ?>"></i>
					</div>
					<p class="mt-4 text-3xl font-bold text-blue-900"><?php echo esc_html($stat['number'] ?? ''); ?></p>
					<p class="mt-2 font-semibold text-blue-900"><?php echo esc_html($stat['label'] ?? ''); ?></p>
					<p class="mt-1 text-sm text-gray-500"><?php echo esc_html($stat['context'] ?? ''); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
		<div class="mt-8 bg-blue-50 rounded-2xl border-l-4 border-orange-500 p-5">
			<p class="text-gray-700">
				<strong>Achievement Highlights:</strong>
				<?php echo esc_html($s['highlight']); ?>
			</p>
		</div>
	</div>
</section>
