<?php
/**
 * Front page (Home)
 *
 * @package VR_Doctors
 */

get_header();
?>
<main id="top">
	<?php
	get_template_part('template-parts/home/hero');
	get_template_part('template-parts/home/journey');
	get_template_part('template-parts/home/testimonials');
	get_template_part('template-parts/home/campus-life');
	get_template_part('template-parts/home/approach');
	get_template_part('template-parts/shared/campus-visit-cta');
	get_template_part('template-parts/home/contact-strip');
	?>
</main>
<?php
get_footer();
