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
class Digital_Asset_Manager {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Digital_Asset_Manager_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

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
		if ( defined( 'DIGITAL_ASSET_MANAGER_VERSION' ) ) {
			$this->version = DIGITAL_ASSET_MANAGER_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'digital-asset-manager';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Digital_Asset_Manager_Loader. Orchestrates the hooks of the plugin.
	 * - Digital_Asset_Manager_I18n. Defines internationalization functionality.
	 * - Digital_Asset_Manager_Admin. Defines all hooks for the admin area.
	 * - Digital_Asset_Manager_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-digital-asset-manager-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-digital-asset-manager-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-digital-asset-manager-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-digital-asset-manager-public.php';

		$this->loader = new Digital_Asset_Manager_Loader();

		// Includes Helper FUnction File.
		require_once DAM_INCLUDES_PATH . 'dam-helper-functions.php';

		// Includes post type.
		require_once DAM_ADMIN_PATH . 'post-types/class-dam-posttype-assets.php';

		// Includes taxonomies.
		require_once DAM_ADMIN_PATH . 'taxonomies/class-dam-taxonomy-asset-type.php';
		require_once DAM_ADMIN_PATH . 'taxonomies/class-dam-taxonomy-tokens.php';

		// Includes settings page.
		require_once DAM_ADMIN_PATH . 'settings/class-dam-settings-page.php';

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Digital_Asset_Manager_I18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Digital_Asset_Manager_I18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Digital_Asset_Manager_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		$post_type_assets = new DAM_Posttype_Assets();
		$this->loader->add_action( 'init', $post_type_assets, 'dam_post_type_assets', 11 );
		$this->loader->add_action( 'save_post', $post_type_assets, 'dam_save_asset_custom_fields', 99, 2 );
		$this->loader->add_filter( 'use_block_editor_for_post_type', $post_type_assets, 'dam_disable_gutenberg_for_asset_pt', 99, 2 );
		$this->loader->add_filter( 'manage_dam-asset_posts_columns', $post_type_assets, 'dam_manage_assest_table_columns', 99, 1 );
		$this->loader->add_filter( 'manage_dam-asset_posts_custom_column', $post_type_assets, 'dam_manage_assest_table_custom_columns', 99, 2 );
		$this->loader->add_filter( 'post_column_taxonomy_links', $post_type_assets, 'dam_manage_assest_token_column', 99, 3 );

		// $this->loader->add_filter( 'template_include', $post_type_assets, 'dam_asset_post_type_templates', 999, 1 );

		$taxonomy_asset_type = new DAM_Taxonomy_Asset_Type();
		$this->loader->add_action( 'init', $taxonomy_asset_type, 'dam_taxonomy_asset_type', 11 );

		$taxonomy_tokens = new DAM_Taxonomy_Tokens();
		$this->loader->add_action( 'init', $taxonomy_tokens, 'dam_taxonomy_tokens', 11 );

		$dam_settings = new DAM_Settings_Page();
		$this->loader->add_action( 'admin_menu', $dam_settings, 'dam_settings_options_page' );
		$this->loader->add_action( 'admin_init', $dam_settings, 'dam_register_settings' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Digital_Asset_Manager_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Digital_Asset_Manager_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
