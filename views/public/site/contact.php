<?php
/**
 * Template for page slug: contact
 *
 * @package VR_Doctors
 */

get_header();
?>
<main>
	<?php
	get_template_part('template-parts/contact/hero');
	get_template_part('template-parts/contact/form');
	get_template_part('template-parts/contact/campus-info');
	get_template_part('template-parts/contact/cta');
	?>
</main>
<?php
get_footer();
