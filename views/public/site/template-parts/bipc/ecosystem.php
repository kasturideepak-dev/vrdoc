<?php
/**
 * Healthcare ecosystem explorer — matches Next.js HealthcareEcosystem layout.
 *
 * Layout: left sidebar (fields) + right stack (career grid + detail cards).
 *
 * @package VR_Doctors
 */

$s          = vr_get_page_section('bipc-careers', 'ecosystem');
$ecosystems = vr_bipc_ecosystems();
if (empty($ecosystems) || !is_array($ecosystems)) {
	return;
}

$first = $ecosystems[0];
$first_career = !empty($first['careers'][0]) ? $first['careers'][0] : null;
?>
<section class="py-12 md:py-16 bg-blue-50" data-bipc-ecosystem>
	<div class="max-w-7xl mx-auto px-4 md:px-6">
		<div class="text-center mb-10">
			<p class="text-orange-500 font-semibold uppercase tracking-widest text-sm md:text-base"><?php echo esc_html($s['eyebrow']); ?></p>
			<h2 class="mt-3 md:mt-4 text-3xl md:text-5xl font-bold text-blue-900"><?php echo esc_html($s['title']); ?></h2>
			<p class="max-w-3xl mx-auto mt-4 md:mt-6 text-sm md:text-base text-gray-600">
				<?php echo esc_html($s['description']); ?>
			</p>
		</div>

		<div class="grid grid-cols-1 md:grid-cols-[300px_1fr] gap-6">

			<!-- L1: Field of Study sidebar -->
			<div class="bg-white rounded-2xl p-4 shadow-md">
				<h3 class="font-bold text-blue-900 mb-3 text-sm">Field of Study</h3>
				<div class="space-y-2">
					<?php foreach ($ecosystems as $i => $eco) : ?>
						<button
							type="button"
							data-bipc-field
							class="w-full flex items-center gap-2.5 p-3 rounded-lg transition text-sm <?php echo 0 === (int) $i ? 'bg-blue-900 text-white' : 'bg-blue-50 text-blue-900 hover:bg-blue-100'; ?>"
						>
							<i class="fa-solid <?php echo esc_attr($eco['icon'] ?? 'fa-circle'); ?> flex-shrink-0"></i>
							<span class="font-medium truncate"><?php echo esc_html($eco['title'] ?? ''); ?></span>
						</button>
					<?php endforeach; ?>
				</div>
			</div>

			<!-- L2 + L3: Careers grid + detail card -->
			<div class="space-y-5">

				<!-- Career Options -->
				<div data-bipc-careers-panel class="scroll-mt-32 bg-white rounded-2xl p-4 shadow-md">
					<h3 class="font-bold text-blue-900 mb-3 text-sm">Career Options</h3>
					<?php foreach ($ecosystems as $i => $eco) : ?>
						<div
							data-bipc-careers
							class="grid grid-cols-2 md:grid-cols-3 gap-2 <?php echo 0 === (int) $i ? '' : 'hidden'; ?>"
						>
							<?php
							$careers = !empty($eco['careers']) && is_array($eco['careers']) ? $eco['careers'] : array();
							foreach ($careers as $j => $career) :
								?>
								<button
									type="button"
									data-bipc-career
									title="<?php echo esc_attr($career['title'] ?? ''); ?>"
									class="p-3 rounded-lg transition text-xs md:text-sm font-medium <?php echo 0 === (int) $j ? 'bg-orange-500 text-white' : 'bg-blue-50 text-blue-900 hover:bg-blue-100'; ?>"
								>
									<?php echo esc_html($career['title'] ?? ''); ?>
								</button>
							<?php endforeach; ?>
						</div>
					<?php endforeach; ?>
				</div>

				<!-- Career Details -->
				<div data-bipc-detail-panel class="scroll-mt-24">
					<?php foreach ($ecosystems as $i => $eco) : ?>
						<?php
						$careers = !empty($eco['careers']) && is_array($eco['careers']) ? $eco['careers'] : array();
						foreach ($careers as $j => $career) :
							$is_active = (0 === (int) $i && 0 === (int) $j);
							?>
							<div
								data-bipc-detail
								data-eco="<?php echo esc_attr((string) $i); ?>"
								data-car="<?php echo esc_attr((string) $j); ?>"
								class="bg-white rounded-2xl p-5 md:p-6 shadow-md <?php echo $is_active ? '' : 'hidden'; ?>"
							>
								<h3 class="text-2xl md:text-3xl font-bold text-blue-900">
									<?php echo esc_html($career['title'] ?? ''); ?>
								</h3>

								<div class="grid sm:grid-cols-2 gap-3 mt-5">
									<div class="bg-blue-50 rounded-lg p-4">
										<p class="text-xs text-gray-600 font-semibold mb-1">Duration</p>
										<p class="font-bold text-blue-900 text-sm"><?php echo esc_html($career['duration'] ?? ''); ?></p>
									</div>
									<div class="bg-blue-50 rounded-lg p-4">
										<p class="text-xs text-gray-600 font-semibold mb-1">Entrance Exam</p>
										<p class="font-bold text-blue-900 text-sm"><?php echo esc_html($career['entrance'] ?? ''); ?></p>
									</div>
								</div>

								<div class="mt-3 bg-orange-50 rounded-lg p-4 border border-orange-200">
									<p class="text-xs text-gray-600 font-semibold mb-1">Focus Area</p>
									<p class="font-bold text-blue-900 text-sm"><?php echo esc_html($career['focus'] ?? ''); ?></p>
								</div>

								<div class="mt-3 bg-green-50 rounded-lg p-4 border border-green-200">
									<p class="text-xs text-gray-600 font-semibold mb-1">Career Paths</p>
									<p class="font-bold text-blue-900 text-sm"><?php echo esc_html($career['paths'] ?? ''); ?></p>
								</div>
							</div>
						<?php endforeach; ?>
					<?php endforeach; ?>
				</div>

			</div>
		</div>
	</div>
</section>
