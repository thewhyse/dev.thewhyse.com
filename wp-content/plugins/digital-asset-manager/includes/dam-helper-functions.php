<?php
/**
 * The file that defines the functions to used through out the plugin.
 *
 * @link       https://2bytecode.com/
 * @since      1.0.0
 *
 * @package    Digital_Asset_Manager
 * @subpackage Digital_Asset_Manager/includes
 */

if ( ! function_exists( 'dam_settings_default_options' ) ) {

	/**
	 * Default options for setting fields.
	 *
	 * @return array
	 */
	function dam_settings_default_options() {

		return array(
			'asset_post_type_capability'     => 'page',
			'asset_post_type_visibility'     => 'public',
			'asset_post_type_fields'         => array(
				'title'                 => 'title',
				'description'           => 'description',
				'version_number'        => 'version_number',
				'live_url'              => 'live_url',
				'featured_image'        => 'featured_image',
				'associated_asset_type' => 'associated_asset_type',
			),
			'asset_type_taxonomy_visibility' => 'public',
		);

	}
}
