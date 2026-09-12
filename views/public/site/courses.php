<?php
/**
 * Template for page slug: courses
 *
 * @package VR_Doctors
 */

get_header();
?>
<main>
	<?php
	get_template_part('template-parts/courses/hero');
	get_template_part('template-parts/courses/intermediate');
	get_template_part('template-parts/courses/long-term');
	get_template_part('template-parts/courses/short-term');
	get_template_part('template-parts/courses/selector');
	get_template_part('template-parts/courses/journey');
	get_template_part('template-parts/shared/campus-visit-cta');
	?>
</main>
<?php
get_footer();
