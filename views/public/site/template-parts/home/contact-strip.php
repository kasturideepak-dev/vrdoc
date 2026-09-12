<?php
/**
 * Home contact strip
 *
 * @package VR_Doctors
 */

$section  = vr_get_home_contact_strip();
$phones   = vr_phones();
$email    = vr_option('email', 'admissions@vrdoctors.in');
$maps_url = vr_option('maps_url', 'https://maps.app.goo.gl/3qrkSSCw6kiYNkwH8');

// Display labels for phones: use settings digits with +91 formatting when 10 digits.
$phone_labels = array();
foreach ($phones as $p) {
	$digits = preg_replace('/\D+/', '', $p);
	if (strlen($digits) === 10) {
		$phone_labels[] = '+91 ' . substr($digits, 0, 4) . ' ' . substr($digits, 4, 4) . ' ' . substr($digits, 8, 2);
	} elseif ($digits !== '') {
		$phone_labels[] = $p;
	}
}
if (empty($phone_labels)) {
	$phone_labels = array('+91 9256 9256 40', '+91 9256 9256 41', '+91 9256 9256 42', '+91 9256 9256 43');
}
?>
<section id="contact" class="scroll-mt-24 py-12 md:py-16 bg-gray-50">
	<div class="max-w-6xl mx-auto px-4 md:px-6">
		<div class="text-center mb-12">
			<?php if (!empty($section['eyebrow'])) : ?>
				<p class="text-orange-500 font-semibold uppercase tracking-widest"><?php echo esc_html($section['eyebrow']); ?></p>
			<?php endif; ?>
			<?php if (!empty($section['title'])) : ?>
				<h2 class="text-3xl md:text-5xl font-bold text-blue-900 mt-4"><?php echo esc_html($section['title']); ?></h2>
			<?php endif; ?>
			<?php if (!empty($section['description'])) : ?>
				<p class="mt-4 text-gray-600 max-w-2xl mx-auto"><?php echo esc_html($section['description']); ?></p>
			<?php endif; ?>
		</div>

		<div class="grid md:grid-cols-3 gap-5">
			<div class="bg-white border border-gray-200 rounded-2xl p-5 hover:shadow-lg transition">
				<div class="flex items-start gap-4">
					<i class="fa-solid fa-phone text-2xl text-orange-500 mt-1 shrink-0"></i>
					<div>
						<h3 class="font-bold text-blue-900"><?php echo esc_html($section['call_title'] ?? 'Call Us'); ?></h3>
						<p class="mt-1 text-gray-600 text-sm leading-relaxed">
							<?php
							foreach ($phone_labels as $label) {
								echo esc_html($label) . '<br />';
							}
							?>
						</p>
					</div>
				</div>
			</div>

			<div class="bg-white border border-gray-200 rounded-2xl p-5 hover:shadow-lg transition">
				<div class="flex items-start gap-4">
					<i class="fa-solid fa-envelope text-2xl text-orange-500 mt-1 shrink-0"></i>
					<div>
						<h3 class="font-bold text-blue-900"><?php echo esc_html($section['email_title'] ?? 'Email Us'); ?></h3>
						<p class="mt-1 text-gray-600 text-sm break-words"><?php echo esc_html($email); ?></p>
					</div>
				</div>
			</div>

			<div class="bg-white border border-gray-200 rounded-2xl p-5 hover:shadow-lg transition">
				<div class="flex items-start gap-4">
					<i class="fa-solid fa-location-dot text-2xl text-orange-500 mt-1 shrink-0"></i>
					<div class="w-full">
						<h3 class="font-bold text-blue-900"><?php echo esc_html($section['location_title'] ?? 'Campus Location'); ?></h3>
						<p class="mt-1 text-gray-600 text-sm"><?php echo esc_html($section['location_text'] ?? 'Hyderabad, Telangana'); ?></p>
						<?php if (!empty($maps_url)) : ?>
							<a href="<?php echo esc_url($maps_url); ?>" target="_blank" rel="noopener noreferrer" class="inline-block mt-3 bg-blue-900 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-800 transition">
								<?php echo esc_html($section['maps_label'] ?? 'Open In Maps'); ?>
							</a>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
