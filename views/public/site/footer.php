<?php
$phones  = vr_phones();
$email   = vr_option('email', 'admissions@vrdoctors.in');
$address = vr_option('address', "VR Doctors Academy\nPlot No 29, Mathrusree Nagar\nHafeezpet, Miyapur\nHyderabad, Telangana 500049");
?>
<footer class="bg-blue-950 text-white">
	<div class="max-w-6xl mx-auto px-6 py-12">
		<div class="grid md:grid-cols-4 gap-8 md:gap-12">
			<div>
				<h3 class="text-2xl font-bold text-orange-400">VR Doctors</h3>
				<p class="mt-4 text-gray-300 leading-relaxed">
					VR Doctors Academy helps BiPC students build successful
					careers in medicine, healthcare and life sciences through
					academic excellence, discipline and mentorship.
				</p>
			</div>
			<div>
				<h3 class="font-bold text-lg mb-4 text-white">Quick Links</h3>
				<ul class="space-y-3 text-gray-300">
					<?php foreach ((class_exists('Menu') ? Menu::tree('footer') : []) as $it): ?>
						<li><a href="<?php echo esc_url($it['url']); ?>" class="hover:text-orange-400 transition"><?php echo esc_html($it['label']); ?></a></li>
					<?php endforeach; ?>
					</ul>
			</div>
			<div>
				<h3 class="font-bold text-lg mb-4 text-white">Contact Information</h3>
				<div class="space-y-4 text-gray-300">
					<div class="flex items-start gap-3">
						<i class="fa-solid fa-phone mt-1 text-orange-400 shrink-0"></i>
						<div>
							<?php if (count($phones) >= 2) : ?>
								<p><?php echo esc_html($phones[0] . '/' . $phones[1]); ?></p>
							<?php endif; ?>
							<?php if (count($phones) >= 4) : ?>
								<p><?php echo esc_html($phones[2] . '/' . $phones[3]); ?></p>
							<?php elseif (!empty($phones)) : ?>
								<p><?php echo esc_html(implode(' / ', $phones)); ?></p>
							<?php endif; ?>
						</div>
					</div>
					<div class="flex items-start gap-3">
						<i class="fa-solid fa-envelope mt-1 text-orange-400 shrink-0"></i>
						<span><?php echo esc_html($email); ?></span>
					</div>
					<div class="flex items-start gap-3">
						<i class="fa-solid fa-location-dot mt-1 text-orange-400 shrink-0"></i>
						<span><?php echo nl2br(esc_html($address)); ?></span>
					</div>
				</div>
			</div>
			<div>
				<h3 class="font-bold text-lg mb-4 text-white">Connect With Us</h3>
				<p class="text-gray-300 mb-5">Follow student achievements, campus life and important updates.</p>
				<div class="flex gap-5 text-2xl">
					<a href="<?php echo esc_url(vr_option('facebook', 'https://www.facebook.com/VR.Jr.College')); ?>" target="_blank" rel="noopener noreferrer" class="hover:text-orange-400 transition" aria-label="Facebook"><i class="fa-brands fa-facebook"></i></a>
					<a href="<?php echo esc_url(vr_option('instagram', 'https://www.instagram.com/vr_junior.college/')); ?>" target="_blank" rel="noopener noreferrer" class="hover:text-orange-400 transition" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
					<a href="<?php echo esc_url(vr_option('youtube', 'https://www.youtube.com/@VR_JuniorCollege')); ?>" target="_blank" rel="noopener noreferrer" class="hover:text-orange-400 transition" aria-label="YouTube"><i class="fa-brands fa-youtube"></i></a>
				</div>
			</div>
		</div>
		<div class="border-t border-blue-800 mt-12 pt-6 text-center text-gray-400 text-sm">
			© <?php echo esc_html(gmdate('Y')); ?> VR Doctors Academy. All Rights Reserved.
		</div>
	</div>
</footer>

<a
	href="<?php echo esc_url(vr_whatsapp_url()); ?>"
	target="_blank"
	rel="noopener noreferrer"
	class="fixed bottom-6 right-6 bg-green-500 text-white w-14 h-14 flex items-center justify-center rounded-full shadow-lg hover:scale-105 transition z-50"
	aria-label="WhatsApp"
>
	<i class="fa-brands fa-whatsapp text-3xl"></i>
</a>

<?php wp_footer(); ?>
</body>
</html>
