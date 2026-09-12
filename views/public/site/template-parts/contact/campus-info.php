<?php
/**
 * Campus info + map
 *
 * @package VR_Doctors
 */

$s      = vr_get_page_section('contact', 'campus');
$phones = vr_phones();
$email  = vr_option('email', 'admissions@vrdoctors.in');
$map    = !empty($s['map_embed_url']) ? $s['map_embed_url'] : 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d7610.530788602768!2d78.36287829999999!3d17.4948401!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3bcb937ee93c897b%3A0x8e7c4b7dc00d672a!2sVR%20Doctors%20Academy!5e0!3m2!1sen!2sin!4v1781867913509!5m2!1sen!2sin';
?>
<section class="py-16 md:py-24 bg-white">
	<div class="max-w-6xl mx-auto px-4 md:px-6">
		<div class="text-center mb-12">
			<p class="text-orange-500 font-semibold uppercase tracking-widest"><?php echo esc_html($s['eyebrow']); ?></p>
			<h2 class="text-3xl md:text-5xl font-bold text-blue-900 mt-3"><?php echo esc_html($s['title']); ?></h2>
			<p class="mt-4 text-gray-600 max-w-2xl mx-auto"><?php echo esc_html($s['description']); ?></p>
		</div>

		<div class="grid lg:grid-cols-2 gap-10">
			<div class="space-y-5">
				<div class="bg-gray-50 border border-gray-200 rounded-2xl p-6">
					<div class="flex items-start gap-4">
						<i class="fa-solid fa-phone text-2xl text-orange-500 mt-1"></i>
						<div>
							<h3 class="font-bold text-blue-900">Admissions Helpline</h3>
							<p class="text-sm text-gray-500 mt-1">Speak directly with our admissions team</p>
							<div class="mt-3 space-y-1">
								<?php
								$defaults = array('9256925640', '9256925641', '9256925642', '9256925643');
								$list     = !empty($phones) ? $phones : $defaults;
								foreach ($list as $phone) :
									$tel = preg_replace('/\D+/', '', $phone);
									if (10 === strlen($tel)) {
										$tel = '91' . $tel;
									}
									?>
									<a href="tel:+<?php echo esc_attr($tel); ?>" class="block text-blue-900 font-semibold hover:text-orange-500"><?php echo esc_html($phone); ?></a>
								<?php endforeach; ?>
							</div>
						</div>
					</div>
				</div>

				<div class="bg-gray-50 border border-gray-200 rounded-2xl p-6">
					<div class="flex items-start gap-4">
						<i class="fa-brands fa-whatsapp text-2xl text-green-500 mt-1"></i>
						<div>
							<h3 class="font-bold text-blue-900">WhatsApp Admissions</h3>
							<p class="text-sm text-gray-500 mt-1">Get quick answers from our team</p>
							<a href="<?php echo esc_url(vr_whatsapp_url()); ?>" target="_blank" rel="noopener noreferrer" class="inline-block mt-3 bg-green-500 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-green-600">Start WhatsApp Chat</a>
						</div>
					</div>
				</div>

				<div class="bg-gray-50 border border-gray-200 rounded-2xl p-6">
					<div class="flex items-start gap-4">
						<i class="fa-solid fa-envelope text-2xl text-orange-500 mt-1"></i>
						<div>
							<h3 class="font-bold text-blue-900">Email</h3>
							<a href="mailto:<?php echo esc_attr($email); ?>" class="mt-2 block text-blue-900 hover:text-orange-500"><?php echo esc_html($email); ?></a>
						</div>
					</div>
				</div>

				<div class="bg-gray-50 border border-gray-200 rounded-2xl p-6">
					<div class="flex items-start gap-4">
						<i class="fa-solid fa-location-dot text-2xl text-orange-500 mt-1"></i>
						<div>
							<h3 class="font-bold text-blue-900">Campus Location</h3>
							<p class="mt-2 text-gray-600"><?php echo esc_html($s['location_text']); ?></p>
						</div>
					</div>
				</div>
			</div>

			<div class="rounded-2xl overflow-hidden shadow-lg min-h-[400px]">
				<iframe
					src="<?php echo esc_url($map); ?>"
					width="100%"
					height="600"
					style="border:0;"
					allowfullscreen=""
					loading="lazy"
					referrerpolicy="no-referrer-when-downgrade"
					title="VR Doctors Academy Map"
				></iframe>
			</div>
		</div>
	</div>
</section>
