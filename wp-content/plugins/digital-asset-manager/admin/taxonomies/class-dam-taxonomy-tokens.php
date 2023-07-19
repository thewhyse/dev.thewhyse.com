<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://2bytecode.com/
 * @since      1.0.0
 *
 * @package    Digital_Asset_Manager
 * @subpackage Digital_Asset_Manager/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Digital_Asset_Manager
 * @subpackage Digital_Asset_Manager/includes
 * @author     2ByteCode <support@2bytecode.com>
 */
class DAM_Taxonomy_Tokens {

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
	}

	/**
	 * Regsiter Custom Taxonomy - Token.
	 *
	 * @return void
	 */
	public function dam_taxonomy_tokens() {

		$labels = array(
			'name'                       => _x( 'Tokens', 'Taxonomy General Name', 'digital-asset-manager' ),
			'singular_name'              => _x( 'Token', 'Taxonomy Singular Name', 'digital-asset-manager' ),
			'menu_name'                  => __( 'Tokens', 'digital-asset-manager' ),
			'all_items'                  => __( 'All Tokens', 'digital-asset-manager' ),
			'parent_item'                => __( 'Parent Token', 'digital-asset-manager' ),
			'parent_item_colon'          => __( 'Parent Token:', 'digital-asset-manager' ),
			'new_item_name'              => __( 'New Token', 'digital-asset-manager' ),
			'add_new_item'               => __( 'Add New Token', 'digital-asset-manager' ),
			'edit_item'                  => __( 'Edit Token', 'digital-asset-manager' ),
			'update_item'                => __( 'Update Token', 'digital-asset-manager' ),
			'view_item'                  => __( 'View Token', 'digital-asset-manager' ),
			'separate_items_with_commas' => __( 'Separate Tokens with commas', 'digital-asset-manager' ),
			'add_or_remove_items'        => __( 'Add or remove Tokens', 'digital-asset-manager' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'digital-asset-manager' ),
			'popular_items'              => __( 'Popular Tokens', 'digital-asset-manager' ),
			'search_items'               => __( 'Search Tokens', 'digital-asset-manager' ),
			'not_found'                  => __( 'Not Found', 'digital-asset-manager' ),
			'no_terms'                   => __( 'No Tokens', 'digital-asset-manager' ),
			'items_list'                 => __( 'Tokens list', 'digital-asset-manager' ),
			'items_list_navigation'      => __( 'Tokens list navigation', 'digital-asset-manager' ),
		);
		$args   = array(
			'labels'            => $labels,
			'hierarchical'      => false,
			'public'            => false,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_nav_menus' => false,
			'show_tagcloud'     => true,
			'rewrite'           => false,
			'show_in_rest'      => false,
		);
		register_taxonomy( 'dam_token', array( 'dam-asset' ), $args );

	}

}
