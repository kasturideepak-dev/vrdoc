<?php
/**
 * Template Name: About
 * Template Post Type: page
 *
 * Used automatically for slug `about` / `about-us`, or when this template is selected.
 *
 * @package VR_Doctors
 */

get_header();
?>
<main>
	<?php
	get_template_part('template-parts/about/hero');
	get_template_part('template-parts/about/who-we-are');
	get_template_part('template-parts/about/timeline');
	get_template_part('template-parts/about/impact');
	get_template_part('template-parts/about/trust');
	get_template_part('template-parts/about/faculty');
	get_template_part('template-parts/about/chairman');
	?>
</main>
<?php
get_footer();
