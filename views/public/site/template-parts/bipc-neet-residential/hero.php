<?php
/**
 * BiPC NEET residential — hero with right-column CF7 enquiry form
 *
 * @package VR_Doctors
 */

$hero  = vr_get_page_section('best-bipc-college-in-hyderabad-neet-residential', 'hero');
$phone = !empty($hero['phone_url']) ? $hero['phone_url'] : 'tel:+917097098877';
$email = !empty($hero['email']) ? $hero['email'] : 'info@vrdoctorsacademy.com';
?>
<section class="relative overflow-hidden py-12 md:py-16 lg:py-20">
	<img
		src="<?php echo esc_url(vr_media_url($hero['image'])); ?>"
		alt="<?php echo esc_attr($hero['image_alt']); ?>"
		class="absolute inset-0 w-full h-full object-cover -z-10" decoding="async" fetchpriority="high"<?php $__ss = vr_srcset($hero['image']); if ($__ss !== '') : ?> srcset="<?php echo esc_attr($__ss); ?>" sizes="100vw"<?php endif; ?> />
	<div class="absolute inset-0 bg-blue-950/85 -z-10"></div>
	<div class="absolute inset-0 bg-gradient-to-br from-blue-950/60 via-transparent to-orange-900/25 -z-10"></div>

	<div class="max-w-6xl mx-auto px-4 md:px-6 relative z-10">
		<div class="grid lg:grid-cols-[1.2fr_0.8fr] gap-8 lg:gap-10 items-start">
			<!-- Left — content -->
			<div class="max-w-3xl">
				<p class="text-orange-400 font-semibold uppercase tracking-widest text-sm md:text-base">
					<?php echo esc_html($hero['eyebrow']); ?>
				</p>

				<h1 class="mt-4 text-3xl md:text-4xl lg:text-5xl font-bold text-white leading-tight">
					<?php echo esc_html($hero['title']); ?>
				</h1>

				<p class="mt-5 text-sm md:text-base text-blue-50 leading-relaxed">
					<?php echo esc_html($hero['paragraph_1']); ?>
				</p>

				<p class="mt-4 text-sm md:text-base text-blue-100 leading-relaxed">
					<?php echo esc_html($hero['paragraph_2']); ?>
				</p>

				<div class="mt-6 flex flex-wrap gap-3">
					<a href="#hero-enquiry" class="inline-flex items-center justify-center bg-gradient-to-r from-orange-500 to-orange-600 text-white px-6 py-3 rounded-xl font-semibold text-sm md:text-base shadow-lg hover:opacity-90 transition lg:hidden">
						<?php echo esc_html($hero['primary_cta_text']); ?>
					</a>
					<a href="<?php echo vr_esc_url($hero['secondary_cta_url'] ?: $phone); ?>" class="inline-flex items-center justify-center border border-white/40 text-white px-6 py-3 rounded-xl font-semibold text-sm md:text-base hover:bg-white hover:text-blue-900 transition">
						<?php echo esc_html($hero['secondary_cta_text']); ?>
					</a>
				</div>

				<div class="mt-5 flex flex-col sm:flex-row sm:flex-wrap gap-3 sm:gap-5 text-sm md:text-base text-blue-100">
					<a href="<?php echo vr_esc_url($phone); ?>" class="inline-flex items-center gap-2 hover:text-orange-300 transition">
						<i class="fa-solid fa-phone text-orange-400"></i>
						<span><?php echo esc_html($hero['phone_display']); ?></span>
					</a>
					<a href="mailto:<?php echo esc_attr($email); ?>" class="inline-flex items-center gap-2 hover:text-orange-300 transition">
						<i class="fa-solid fa-envelope text-orange-400"></i>
						<span><?php echo esc_html($email); ?></span>
					</a>
				</div>
			</div>

			<!-- Right — CF7 enquiry form -->
			<div id="hero-enquiry" class="w-full max-w-md mx-auto lg:mx-0 lg:max-w-none lg:sticky lg:top-24">
				<div class="w-full rounded-2xl bg-white p-5 md:p-6 shadow-2xl border border-white/20">
					<p class="text-orange-500 font-semibold uppercase tracking-widest text-xs"><?php echo esc_html($hero['form_eyebrow']); ?></p>
					<h2 class="mt-1.5 text-xl md:text-2xl font-bold text-blue-900 leading-snug"><?php echo esc_html($hero['form_title']); ?></h2>
					<p class="mt-2 text-sm text-gray-600"><?php echo esc_html($hero['form_description']); ?></p>

					<div class="mt-5 vr-cf7 vr-cf7--hero">
						<?php vr_neet_render_hero_cf7($hero); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
