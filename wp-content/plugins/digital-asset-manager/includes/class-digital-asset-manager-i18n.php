<?php
/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://2bytecode.com/
 * @since      1.0.0
 *
 * @package    Digital_Asset_Manager
 * @subpackage Digital_Asset_Manager/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Digital_Asset_Manager
 * @subpackage Digital_Asset_Manager/includes
 * @author     2ByteCode <support@2bytecode.com>
 */
class Digital_Asset_Manager_I18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'digital-asset-manager',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}
}
