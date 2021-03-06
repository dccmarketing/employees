<?php
/**
 * The view for the employee image used in the loop
 */

$images = $this->get_featured_images( $item->ID );

if ( empty( $images ) ) { return; }

if ( array_key_exists( 'medium', $images['sizes'] ) ) {

	$source = apply_filters( 'employees-list-image', $images['sizes']['medium']['url'], $images );

} else {

	$source = apply_filters( 'employees-list-image', $images['sizes']['full']['url'], $images );

}

?><div class="employee-list-thumb" style="background-image:url(<?php echo esc_url( $source ); ?>)"></div>