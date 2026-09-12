<?php
/**
 * Template for page slug: results
 *
 * @package VR_Doctors
 */

get_header();
?>
<main>
	<?php
	get_template_part('template-parts/results/hero');
	get_template_part('template-parts/results/stats');
	get_template_part('template-parts/results/admissions');
	get_template_part('template-parts/results/achievers');
	get_template_part('template-parts/results/stories');
	?>
</main>
<?php
get_footer();
