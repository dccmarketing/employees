<?php
/**
 * The view for the employee image used in the single-employee template
 */

if ( ! has_post_thumbnail() ) { return; }

$thumb_atts['class'] 	= 'alignleft img-employee photo';
$thumb_atts['itemtype'] = 'image';

apply_filters( 'employees-single-post-featured-image-attributes', $thumb_atts );

the_post_thumbnail( 'medium', $thumb_atts );