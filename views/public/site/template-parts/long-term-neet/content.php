<?php
/**
 * Long-Term NEET Program page content
 *
 * @package VR_Doctors
 */

vr_neet_set_context('long-term-neet-program-hyderabad');

get_template_part('template-parts/neet-landing/hero');
get_template_part('template-parts/neet-landing/overview');
get_template_part('template-parts/neet-landing/who-should-join');
get_template_part('template-parts/neet-landing/curriculum');
get_template_part('template-parts/neet-landing/methodology');

vr_neet_set_context('long-term-neet-program-hyderabad', 'residential', 'bg-blue-50');
get_template_part('template-parts/neet-landing/bullet-section');

get_template_part('template-parts/neet-landing/track-record');
get_template_part('template-parts/neet-landing/testimonials');
get_template_part('template-parts/neet-landing/admission');
get_template_part('template-parts/neet-landing/cross-link');
get_template_part('template-parts/neet-landing/faq');
get_template_part('template-parts/neet-landing/final-cta');
