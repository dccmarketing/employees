<?php
/**
 * The metabox-specific functionality of the plugin.
 *
 * @link 		http://slushman.com
 * @since 		1.0.0
 *
 * @package 	Employees
 * @subpackage 	Employees/classes
 */

/**
 * The metabox-specific functionality of the plugin.
 *
 * @package 	Employees
 * @subpackage 	Employees/classes
 * @author 		Slushman <chris@slushman.com>
 */
class Employees_Admin_Metaboxes {

	/**
	 * The post meta data
	 *
	 * @since 		1.0.0
	 * @access 		private
	 * @var 		string 			$meta    			The post meta data.
	 */
	private $meta;

	/**
	 * The ID of this plugin.
	 *
	 * @since 		1.0.0
	 * @access 		private
	 * @var 		string 			$plugin_name 		The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since 		1.0.0
	 * @access 		private
	 * @var 		string 			$version 			The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 		1.0.0
	 * @param 		string 			$Now_Hiring 		The name of this plugin.
	 * @param 		string 			$version 			The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Registers metaboxes with WordPress
	 *
	 * @since 	1.0.0
	 * @access 	public
	 */
	public function add_metaboxes() {

		//remove_meta_box( 'postimagediv', 'employee', 'side' );

		// add_meta_box( $id, $title, $callback, $screen, $context, $priority, $callback_args );

		add_meta_box(
			'employees_location_info',
			apply_filters( $this->plugin_name . '-metabox-location-info-title', esc_html__( 'Location Info', 'employees' ) ),
			array( $this, 'metabox' ),
			'employee',
			'normal',
			'default',
			array(
				'file' => 'location-info'
			)
		);

		add_meta_box(
			'employees_contact_info',
			apply_filters( $this->plugin_name . '-metabox-contact-info-title', esc_html__( 'Contact Info', 'employees' ) ),
			array( $this, 'metabox' ),
			'employee',
			'normal',
			'default',
			array(
				'file' => 'contact-info'
			)
		);

		add_meta_box(
			'employees_display_order',
			apply_filters( $this->plugin_name . '-metabox-display-order-title', esc_html__( 'Display Order', 'employees' ) ),
			array( $this, 'metabox' ),
			'employee',
			'side',
			'low',
			array(
				'file' => 'display-order'
			)
		);

		add_meta_box(
			'employees_links',
			apply_filters( $this->plugin_name . '-metabox-links-title', esc_html__( 'Links', 'employees' ) ),
			array( $this, 'metabox' ),
			'employee',
			'side',
			'low',
			array(
				'file' => 'links'
			)
		);

	} // add_metaboxes()

	/**
	 * Changes strings referencing Featured Images
	 *
	 * @see 		https://developer.wordpress.org/reference/hooks/post_type_labels_post_type/
	 *
	 * @param 		object 		$labels 		Current post type labels
	 * @return 		object 						Modified post type labels
	 */
	public function change_featured_image_labels( $labels ) {

		$labels->featured_image 		= 'Headshot';
		$labels->set_featured_image 	= 'Set headshot';
		$labels->remove_featured_image 	= 'Remove headshot';
		$labels->use_featured_image 	= 'Use as headshot';

		return $labels;

	} // change_featured_image_labels()

	/**
	 * Check each nonce. If any don't verify, $nonce_check is increased.
	 * If all nonces verify, returns 0.
	 *
	 * @since 		1.0.0
	 * @access 		public
	 * @return 		int 		The value of $nonce_check
	 */
	private function check_nonces( $posted ) {

		$nonces 		= array();
		$nonce_check 	= 0;

		$nonces[] 	= 'nonce_employees_contact_info';
		$nonces[] 	= 'nonce_employees_links';
		$nonces[] 	= 'nonce_employees_location_info';
		$nonces[] 	= 'nonce_employees_name';
		$nonces[] 	= 'nonce_employees_job_title';
		$nonces[] 	= 'nonce_employees_display_order';

		foreach ( $nonces as $nonce ) {

			if ( ! isset( $posted[$nonce] ) ) { $nonce_check++; }
			if ( isset( $posted[$nonce] ) && ! wp_verify_nonce( $posted[$nonce], $this->plugin_name ) ) { $nonce_check++; }

		}

		return $nonce_check;

	} // check_nonces()

	/**
	 * Creates a new title based on the name metabox values
	 *
	 * @param 		int 		$post_id 		The post ID
	 * @param 		object 		$post 			The post object
	 */
	public function create_title_from_name( $data, $post ) {

		//wp_die( print_r( $post ) );

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) { return $data; }
		if ( empty( $post['post_ID'] ) ) { return $data; }
		if ( ! current_user_can( 'edit_post', $post['post_ID'] ) ) { return $data; }
		if ( 'employee' != $data['post_type'] ) { return $data; }
		if ( empty( $post['honorific-prefix'] ) && empty( $post['first-name'] ) && empty( $post['last-name'] ) && empty( $post['honorific-suffix'] ) ) { return $data; }

		$title = array();

		if ( ! empty( $post['honorific-prefix'] ) ) {

			$period 	= '.';
			$specials 	= $this->get_special_honorifics();

			if ( in_array( $post['honorific-prefix'], $specials ) ) {

				$period = '';

			}

			$title[] = $post['honorific-prefix'] . $period;

		}

		if ( ! empty( $post['first-name'] ) ) {

			$title[] = $post['first-name'];

		}

		if ( ! empty( $post['last-name'] ) ) {

			$title[] = $post['last-name'];

		}

		if ( ! empty( $post['honorific-suffix'] ) ) {

			$title[] = ', ' . $post['honorific-suffix'];

		}

		if ( ! empty( $title ) ) {

			$newtitle = implode( ' ', $title );

		}

		if ( empty( $post['honorific-suffix'] ) ) {

			$data['post_title'] = $newtitle;

		} else {

			$data['post_title'] = str_replace( ' , ', ', ', $newtitle );

		}

		return $data;

	} // create_title_from_name()

	/**
	 * Returns the display order
	 *
	 * @return [type] [description]
	 */
	public function get_display_order() {

		$options = get_option( $this->plugin_name . '-options' );

		if ( empty( $options['display-order'] ) ) {

			$order = array();

		} else {

			$order = $options['display-order'];

		}

		return $order;

	} // get_display_order()

	/**
	 * Returns a list of honorific prefixes that will change
	 * the punctuation to just a space.
	 *
	 * @return 		array 			An array of honorifics
	 */
	public static function get_special_honorifics() {

		$return = array();

		$return[] = 'Airman';
		$return[] = 'Brother';
		$return[] = 'Chef';
		$return[] = 'Dame';
		$return[] = 'Elder';
		$return[] = 'Father';
		$return[] = 'Imam';
		$return[] = 'Maestro';
		$return[] = 'Pastor';
		$return[] = 'Rabbi';
		$return[] = 'Seaman';
		$return[] = 'Sir';
		$return[] = 'Sister';

		$return = apply_filters( 'employees-special-honorifics', $return );

		return $return;

	} // get_special_honorifics()

	/**
	 * Returns an array of the all the metabox fields and their respective types
	 *
	 * $fields[] 	= array( 'field-name', 'field-type', 'Field Label' );
	 *
	 * @since 		1.0.0
	 * @access 		public
	 * @return 		array 		Metabox fields and types
	 */
	public static function get_metabox_fields() {

		$fields = array();

		$fields[] 	= array( 'honorific-prefix', 'text', 'Prefix' );
		$fields[] 	= array( 'first-name', 'text', 'First Name' );
		$fields[] 	= array( 'last-name', 'text', 'Last Name' );
		$fields[] 	= array( 'honorific-suffix', 'text', 'Suffix' );
		$fields[] 	= array( 'job-title', 'text', 'Job Title' );
		$fields[] 	= array( 'phone-office', 'text', 'Office Phone' );
		$fields[] 	= array( 'phone-cell', 'text', 'Cell Phone' );
		$fields[] 	= array( 'fax-number', 'text', 'Fax Number' );
		$fields[] 	= array( 'email-address', 'email', 'Email Address' );
		$fields[] 	= array( 'url-facebook', 'url', 'Facebook' );
		$fields[] 	= array( 'url-flickr', 'url', 'Flickr' );
		$fields[] 	= array( 'url-github', 'url', 'Github' );
		$fields[] 	= array( 'url-google', 'url', 'Google+' );
		$fields[] 	= array( 'url-instagram', 'url', 'Instagram' );
		$fields[] 	= array( 'url-linkedin', 'url', 'LinkedIn' );
		$fields[] 	= array( 'url-pinterest', 'url', 'Pinterest' );
		$fields[] 	= array( 'url-tumblr', 'url', 'tumblr' );
		$fields[] 	= array( 'url-twitter', 'url', 'Twitter' );
		$fields[] 	= array( 'url-website', 'url', 'Website' );
		$fields[] 	= array( 'url-wordpress', 'url', 'WordPress' );
		$fields[] 	= array( 'url-vcard', 'url', 'vCard URL' );
		$fields[] 	= array( 'address', 'text', 'Street Address' );
		$fields[] 	= array( 'address2', 'text', 'Street Address 2' );
		$fields[] 	= array( 'city', 'text', 'City' );
		$fields[] 	= array( 'state', 'text', 'State' );
		$fields[] 	= array( 'zipcode', 'text', 'Zip Code' );
		$fields[] 	= array( 'building', 'text', 'Building' );
		$fields[] 	= array( 'office', 'text', 'Office Number' );
		$fields[] 	= array( 'display-order', 'number', 'Display Order' );

		return $fields;

	} // get_metabox_fields()

	/**
	 * Returns an array of the all the metabox fields and their respective types
	 *
	 * $fields[] 	= array( 'field-name', 'field-type', 'Field Label' );
	 *
	 * @since 		1.0.0
	 * @access 		public
	 * @return 		array 		Metabox fields and types
	 */
	public static function get_metabox_autosave_fields() {

		$fields = array();

		$fields[] 	= array( 'honorific-prefix', 'text', 'Prefix' );
		$fields[] 	= array( 'first-name', 'text', 'First Name' );
		$fields[] 	= array( 'last-name', 'text', 'Last Name' );
		$fields[] 	= array( 'honorific-suffix', 'text', 'Suffix' );

		return $fields;

	} // get_metabox_autosave_fields()

	/**
	 * Calls a metabox file specified in the add_meta_box args.
	 *
	 * @since 	1.0.0
	 * @access 	public
	 * @return 	void
	 */
	public function metabox( $post, $params ) {

		if ( ! is_admin() ) { return; }
		if ( 'employee' != $post->post_type ) { return; }

		include( plugin_dir_path( __FILE__ ) . 'views/view-metabox-' . $params['args']['file'] . '.php' );

	} // metabox()

	/**
	 * Adds the name metabox to the post editor
	 */
	public function metabox_name( $post ) {

		if ( ! is_admin() ) { return; }
		if ( 'employee' !== $post->post_type ) { return; }

		include( plugin_dir_path( __FILE__ ) . 'views/view-metabox-name.php' );

	} // metabox_name()

	/**
	 * Add subtitle field to post editor
	 */
	public function metabox_job_title( $post ) {

		if ( ! is_admin() ) { return; }
		if ( 'employee' !== $post->post_type ) { return; }

		include( plugin_dir_path( __FILE__ ) . 'views/view-metabox-job-title.php' );

	} // metabox_job_title()

	/**
	 * Sets the class variable $options
	 */
	public function set_meta() {

		global $post;

		if ( empty( $post ) ) { return; }
		if ( 'employee' != $post->post_type ) { return; }

		$this->meta = get_post_custom( $post->ID );

	} // set_meta()

	/**
	 * Updates the display order, independently of the other metadata
	 *
	 * @param  [type] $post_id [description]
	 * @param  [type] $post    [description]
	 * @param  [type] $update  [description]
	 * @return [type]          [description]
	 */
	public function update_display_order( $post_id, $post, $update ) {

		if ( ! isset( $_POST['nonce_employees_display_order'] ) ) { return; }
		if ( ! wp_verify_nonce( $_POST['nonce_employees_display_order'], $this->plugin_name ) ) { return; }
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) { return $post_id; }
		if ( ! current_user_can( 'edit_post', $post_id ) ) { return $post_id; }
		if ( 'employee' != $post->post_type ) { return; }

		//wp_die( '<pre>' . print_r( $_POST ) . '</pre>' );

		$sanitizer 	= new Employees_Sanitize();

		$sanitizer->set_data( $_POST['display-order'] );
		$sanitizer->set_type( 'number' );

		$new_value 	= $sanitizer->clean();
		$order 		= $this->get_display_order();

		//wp_die( '<pre>' . print_r( $order ) . '</pre>' );

		if ( in_array( $new_value, $order ) ) {



		} else {

			$key = $new_value - 1;

			$order[$key]['id'] 		= $post->ID;
			$order[$key]['title'] 	= $post->post_title;
			$order[$key]['order'] 	= $new_value;

		}

		wp_die( '<pre>' . print_r( $order ) . '</pre>' );

		/*$updated 	= array();

		foreach ( $options as $key => $option ) {

			if ( 'display-order' === $key ) {

				$updated['display-order'] = $new_value;

			} else {

				$updated[$key] = $option;

			}

		}

		update_option( $this->plugin_name . '-options', $updated );

		unset( $sanitizer );
*/




		/*$order 		= $options['display-order'];
		$diff 		= array_diff( $order, $new_value );

		if ( empty( $diff ) ) { wp_die( 'nothing changed' ); }



		//

		if ( $update ) {

			// what to do if its an updated post

		} else {

			// what to do if its a new post

		}
*/



		/*
		$value 		= ( empty( $this->meta['display-order'][0] ) ? '' : $this->meta['display-order'][0] );
		$options 	= get_option( $this->plugin_name . '-options' );

		if ( empty( $value ) ) {

			// add one number higher to the option

		}

		$sanitizer 	= new Employees_Sanitize();

		$sanitizer->set_data( $_POST['display-order'] );
		$sanitizer->set_type( 'int' );

		$new_value = $sanitizer->clean();

		if ( in_array( $new_value, $order ) ) {

			$new = array( $new_value );
			$save = array_diff( $new, $option['display-order'] );

		}


		*/

	} // update_display_order()

	/**
	 * Saves some metabox data on auto-draft/autosave.
	 *
	 * @since 		1.0.0
	 * @access 		public
	 *
	 * @param 		int 		$post_id 		The post ID
	 * @param 		object 		$post 			The post object
	 */
	public function validate_autosave_meta( $post_id, $post ) {

		if ( ! current_user_can( 'edit_post', $post_id ) ) { return $post_id; }
		if ( 'employee' != $post->post_type ) { return $post_id; }

		$metas = $this->get_metabox_autosave_fields();

		foreach ( $metas as $meta ) {

			$value 		= ( empty( $this->meta[$meta[0]][0] ) ? '' : $this->meta[$meta[0]][0] );
			$sanitizer 	= new Employees_Sanitize();

			$sanitizer->set_data( $_POST[$meta[0]] );
			$sanitizer->set_type( $meta[1] );

			$new_value = $sanitizer->clean();

			update_post_meta( $post_id, $meta[0], $new_value );

			unset( $sanitizer );

		} // foreach

	} // validate_autosave_meta()

	/**
	 * Saves metabox data
	 *
	 * @since 		1.0.0
	 * @access 		public
	 *
	 * @param 		int 		$post_id 		The post ID
	 * @param 		object 		$post 			The post object
	 */
	public function validate_meta( $post_id, $post ) {

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) { return $post_id; }
		if ( ! current_user_can( 'edit_post', $post_id ) ) { return $post_id; }
		if ( 'employee' != $post->post_type ) { return $post_id; }

		$nonce_check = $this->check_nonces( $_POST );

		if ( 0 < $nonce_check ) { return $post_id; }

		$metas = $this->get_metabox_fields();

		foreach ( $metas as $meta ) {

			$value 		= ( empty( $this->meta[$meta[0]][0] ) ? '' : $this->meta[$meta[0]][0] );
			$sanitizer 	= new Employees_Sanitize();

			$sanitizer->set_data( $_POST[$meta[0]] );
			$sanitizer->set_type( $meta[1] );

			$new_value = $sanitizer->clean();

			update_post_meta( $post_id, $meta[0], $new_value );

			unset( $sanitizer );

		} // foreach

	} // validate_meta()

} // class