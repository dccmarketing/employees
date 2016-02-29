<?php
/**
 * The view for the employee job title used in the loop
 */

if ( ! empty( $meta['phone-office'][0] ) ) {

	?><p class="employee-list-phone-office" itemprop="telephone">
		<a href="tel:<?php echo esc_html( $meta['phone-office'][0] ); ?>"><?php

			echo apply_filters( $this->plugin_name . '-loop-text-phone-office', sanitize_email( $meta['phone-office'][0] ) );

		?></a>
	</p><?php

}