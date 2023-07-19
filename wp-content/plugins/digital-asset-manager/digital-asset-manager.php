<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://2bytecode.com/
 * @since             1.0.0
 * @package           Digital_Asset_Manager
 *
 * @wordpress-plugin
 * Plugin Name:       Digital Asset Manager
 * Plugin URI:        https://2bytecode.com/
 * Description:       Keep record of all purchased themes and plugins from third party marketplace in one place.
 * Version:           1.0.2
 * Author:            2ByteCode
 * Author URI:        https://profiles.wordpress.org/2bytecode/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       digital-asset-manager
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'DIGITAL_ASSET_MANAGER_VERSION', '1.0.2' );


/**
 * Define plugin wise constant.
 */
if ( ! defined( 'DAM_ADMIN_PATH' ) ) {
	define( 'DAM_ADMIN_PATH', plugin_dir_path( __FILE__ ) . 'admin/' );
}

if ( ! defined( 'DAM_INCLUDES_PATH' ) ) {
	define( 'DAM_INCLUDES_PATH', plugin_dir_path( __FILE__ ) . 'includes/' );
}

if ( ! defined( 'DAM_PUBLIC_PATH' ) ) {
	define( 'DAM_PUBLIC_PATH', plugin_dir_path( __FILE__ ) . 'public/' );
}

if ( ! defined( 'DAM_ADMIN_URL' ) ) {
	define( 'DAM_ADMIN_URL', plugin_dir_url( __FILE__ ) . 'admin/' );
}

if ( ! defined( 'DAM_INCLUDES_URL' ) ) {
	define( 'DAM_INCLUDES_URL', plugin_dir_url( __FILE__ ) . 'includes/' );
}

if ( ! defined( 'DAM_PUBLIC_URL' ) ) {
	define( 'DAM_PUBLIC_URL', plugin_dir_url( __FILE__ ) . 'public/' );
}

if ( ! defined( 'DAM_BASE_NAME' ) ) {
	define( 'DAM_BASE_NAME', plugin_basename( __FILE__ ) );
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-digital-asset-manager-activator.php
 */
function activate_digital_asset_manager() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-digital-asset-manager-activator.php';
	Digital_Asset_Manager_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-digital-asset-manager-deactivator.php
 */
function deactivate_digital_asset_manager() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-digital-asset-manager-deactivator.php';
	Digital_Asset_Manager_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_digital_asset_manager' );
register_deactivation_hook( __FILE__, 'deactivate_digital_asset_manager' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-digital-asset-manager.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_digital_asset_manager() {

	$plugin = new Digital_Asset_Manager();
	$plugin->run();

}
run_digital_asset_manager();
