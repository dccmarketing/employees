<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines a taxonomy and other related functionality.
 *
 * @link       http://slushman.com
 * @since      1.0.0
 *
 * @package    Employees
 * @subpackage Employees/classes
 * @author     Slushman <chris@slushman.com>
 */
class Employees_Tax_Department {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Constructor
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Creates a new taxonomy for a custom post type
	 *
	 * @since 	1.0.0
	 * @access 	public
	 * @uses 	register_taxonomy()
	 */
	public static function new_taxonomy_department() {

		$plural 	= 'Departments';
		$single 	= 'Department';
		$tax_name 	= 'department';
		$cpt_name 	= 'employee';

		$opts['hierarchical']							= TRUE;
		//$opts['meta_box_cb'] 							= '';
		$opts['public']									= TRUE;
		$opts['query_var']								= $tax_name;
		$opts['show_admin_column'] 						= FALSE;
		$opts['show_in_nav_menus']						= TRUE;
		$opts['show_tag_cloud'] 						= TRUE;
		$opts['show_ui']								= TRUE;
		$opts['sort'] 									= '';
		//$opts['update_count_callback'] 					= '';

		$opts['capabilities']['assign_terms'] 			= 'edit_posts';
		$opts['capabilities']['delete_terms'] 			= 'manage_categories';
		$opts['capabilities']['edit_terms'] 			= 'manage_categories';
		$opts['capabilities']['manage_terms'] 			= 'manage_categories';

		$opts['labels']['add_new_item'] 				= esc_html__( "Add New {$single}", 'employees' );
		$opts['labels']['add_or_remove_items'] 			= esc_html__( "Add or remove {$plural}", 'employees' );
		$opts['labels']['all_items'] 					= esc_html__( $plural, 'employees' );
		$opts['labels']['choose_from_most_used'] 		= esc_html__( "Choose from most used {$plural}", 'employees' );
		$opts['labels']['edit_item'] 					= esc_html__( "Edit {$single}" , 'employees');
		$opts['labels']['menu_name'] 					= esc_html__( $plural, 'employees' );
		$opts['labels']['name'] 						= esc_html__( $plural, 'employees' );
		$opts['labels']['new_item_name'] 				= esc_html__( "New {$single} Name", 'employees' );
		$opts['labels']['not_found'] 					= esc_html__( "No {$plural} Found", 'employees' );
		$opts['labels']['parent_item'] 					= esc_html__( "Parent {$single}", 'employees' );
		$opts['labels']['parent_item_colon'] 			= esc_html__( "Parent {$single}:", 'employees' );
		$opts['labels']['popular_items'] 				= esc_html__( "Popular {$plural}", 'employees' );
		$opts['labels']['search_items'] 				= esc_html__( "Search {$plural}", 'employees' );
		$opts['labels']['separate_items_with_commas'] 	= esc_html__( "Separate {$plural} with commas", 'employees' );
		$opts['labels']['singular_name'] 				= esc_html__( $single, 'employees' );
		$opts['labels']['update_item'] 					= esc_html__( "Update {$single}", 'employees' );
		$opts['labels']['view_item'] 					= esc_html__( "View {$single}", 'employees' );

		$opts['rewrite']['ep_mask']						= EP_NONE;
		$opts['rewrite']['hierarchical']				= FALSE;
		$opts['rewrite']['slug']						= esc_html__( $tax_name, 'employees' );
		$opts['rewrite']['with_front']					= FALSE;

		$opts = apply_filters( 'employees-taxonomy-department-options', $opts );

		register_taxonomy( $tax_name, $cpt_name, $opts );

	} // new_taxonomy_department()

} // class