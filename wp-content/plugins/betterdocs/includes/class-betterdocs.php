<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://wpdeveloper.com
 * @since      1.0.0
 *
 * @package    BetterDocs
 * @subpackage BetterDocs/includes
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
 * @package    BetterDocs
 * @subpackage BetterDocs/includes
 * @author     WPDeveloper <support@wpdeveloper.com>
 */
class BetterDocs
{

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      BetterDocs_Loader    $loader    Maintains and registers all hooks for the plugin.
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
	public function __construct()
	{
		if (defined('BETTERDOCS_VERSION')) {
			$this->version = BETTERDOCS_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'betterdocs';
        $this->db();
        add_action('init', array($this, 'search_migration'));
		$this->load_dependencies();
		$this->set_locale();
		// $this->start_plugin_tracking();
		$this->define_admin_hooks();
		$this->define_public_hooks();
        if (is_admin()) {
			add_action( 'in_plugin_update_message-betterdocs/betterdocs.php', [$this, 'plugin_update_message'], 10, 2 );
            new Betterdocs_Role_Management_Lite();
        }
		add_action('admin_init', array($this, 'redirect'));
		add_action('wp_ajax_optin_wizard_action_betterdocs', array($this, 'wizard_action'));
	}

    public function plugin_update_message( $plugin_data, $response ) {
        if ( isset( $response->upgrade_notice ) ) {
            $new_version                = $plugin_data['new_version'];
            $current_version            = $this->version;
            $current_version_minor_part = explode( '.', $current_version )[0];
            $new_version_minor_part     = explode( '.', $new_version )[0];
			$major = ! ( $current_version_minor_part === $new_version_minor_part );

			include BETTERDOCS_ADMIN_DIR_PATH . 'partials/upgrade.php';
        }
    }

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - BetterDocs_Loader. Orchestrates the hooks of the plugin.
	 * - BetterDocs_i18n. Defines internationalization functionality.
	 * - BetterDocs_Admin. Defines all hooks for the admin area.
	 * - BetterDocs_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies()
	{
		/**
		 * Quick Setup Wizard
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/setup-wizard/betterdocs-setup-wizard-config.php';
		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-betterdocs-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-betterdocs-i18n.php';
		require_once BETTERDOCS_DIR_PATH . 'includes/class-betterdocs-usage-tracker.php';
		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once BETTERDOCS_ADMIN_DIR_PATH . 'class-betterdocs-admin.php';
		require_once BETTERDOCS_ADMIN_DIR_PATH . 'class-betterdocs-admin-notice.php';
		require_once BETTERDOCS_ADMIN_DIR_PATH . 'class-betterdocs-admin-screen.php';
		require_once BETTERDOCS_ADMIN_DIR_PATH . 'partials/class-betterdocs-list-table.php';
		require_once BETTERDOCS_ADMIN_DIR_PATH . 'includes/class-betterdocs-db.php';
		require_once BETTERDOCS_ADMIN_DIR_PATH . 'includes/class-betterdocs-metabox.php';
		require_once BETTERDOCS_ADMIN_DIR_PATH . 'includes/class-betterdocs-settings.php';
		require_once BETTERDOCS_ADMIN_DIR_PATH . 'includes/class-betterdocs-role-management-lite.php';
		require_once BETTERDOCS_ADMIN_DIR_PATH . 'reports/class-betterdocs-email-template.php';
		require_once BETTERDOCS_ADMIN_DIR_PATH . 'reports/class-betterdocs-report-email.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-betterdocs-public.php';

		/**
		 * The functions responsible for betterdocs helpers
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-betterdocs-helpers.php';

		/**
		 * The class responsible for registering docs post type and it's category and tags taxonomy
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-betterdocs-docs-post-type.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-betterdocs-faq.php';

		/**
		 * The functions responsible for betterdocs shortcodes
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/betterdocs-shortcodes.php';

		/**
		 * The Class Is Responsible For Loading TOC Class
		 */

		require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-betterdocs-toc.php';

		/**
		 * The functions responsible for betterdocs breadcrumbs
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/betterdocs-breadcrumbs.php';

		/**
		 * The functions responsible for betterdocs customizer
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/customizer/customizer.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/customizer/defaults.php';

		/**
		 * The class responsible for registering widget in elementor and extend single page functionality
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/elementor/class-betterdocs-elementor.php';

		/**
		 * The class responsible for registering widget in elementor and extend single page functionality
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/gutenberg/class-betterdocs-gutenberg.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/gutenberg/class-style-handler.php';

		$this->loader = new BetterDocs_Loader();
	}

    public static function db() {
        global $wpdb;
        $installed_ver = get_site_option( "betterdocs_db_version" );
        if ( $installed_ver != BETTERDOCS_DB_VERSION ) {
            $search_keyword_table = $wpdb->prefix . 'betterdocs_search_keyword';
            $search_keyword = "CREATE TABLE $search_keyword_table (
                id bigint NOT NULL AUTO_INCREMENT,
                keyword text NOT NULL,
                PRIMARY KEY (id)
            )";

            $search_log_table = $wpdb->prefix . 'betterdocs_search_log';
            $search_log = "CREATE TABLE $search_log_table (
                id bigint NOT NULL AUTO_INCREMENT,
                keyword_id bigint NOT NULL,
                count mediumint(9) NULL,
                not_found_count mediumint(9) NULL,
                created_at date DEFAULT '0000-00-00' NOT NULL,
                PRIMARY KEY (id),
                KEY keyword_id (keyword_id),
                KEY created_at (created_at)
            )";

            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

            dbDelta( $search_keyword );
            dbDelta( $search_log );

            update_option( "betterdocs_db_version", BETTERDOCS_DB_VERSION );
        }
    }

    public static function search_migration() {
        global $wpdb;
        if (get_site_option( "betterdocs_search_data_migration" ) == false) {
            $search_data = get_site_option( 'betterdocs_search_data' );
            if (!empty($search_data)) {
                $search_data_arr = unserialize($search_data);
                foreach ($search_data_arr as $key=>$value) {
                    $args = array(
                        'post_type'      => 'docs',
                        'post_status'      => 'publish',
                        'posts_per_page'      => -1,
                        'suppress_filters' => true,
                        's' => $key
                    );

                    $loop = new WP_Query($args);
                    if ($loop->have_posts()) {
                        $count = $value;
                        $not_found_count = 0;
                    } else {
                        $count = 0;
                        $not_found_count = $value;
                    }

                    $keyword = $wpdb->get_var(
                        $wpdb->prepare( "
                            SELECT keyword
                            FROM {$wpdb->prefix}betterdocs_search_keyword
                            WHERE keyword = %s",
                            $key
                        )
                    );

                    if ( $keyword == NUll ) {
                        $insert = $wpdb->query(
                            $wpdb->prepare(
                                "INSERT INTO {$wpdb->prefix}betterdocs_search_keyword
                                ( keyword )
                                VALUES ( %s )",
                                array(
                                    $key
                                )
                            )
                        );

                        if ($insert) {
                            $wpdb->query(
                                $wpdb->prepare(
                                    "INSERT INTO {$wpdb->prefix}betterdocs_search_log
                                    (keyword_id, count, not_found_count, created_at)
                                    VALUES (%d, %d, %d, %s)",
                                    array(
                                        $wpdb->insert_id,
                                        $count,
                                        $not_found_count,
                                        date('Y-m-d')
                                    )
                                )
                            );
                        }
                    }
                }
                update_option( "betterdocs_search_data_migration", '1.0' );
            }
        }
    }

	/**
	 * Optional usage tracker
	 *
	 * @since v1.0.0
	 */
	public function start_plugin_tracking()
	{
		$tracker = BetterDocs_Plugin_Usage_Tracker::get_instance(BETTERDOCS_FILE, [
			'opt_in'       => true,
			'goodbye_form' => true,
			'item_id'      => 'c7b16777b4f1b83f6083'
		]);
		$tracker->set_notice_options(array(
			'notice' => __('Want to help make <strong>BetterDocs</strong> even more awesome? You can get a <strong>10% discount coupon</strong> for Premium extensions if you allow us to track the usage.', 'betterdocs'),
			'extra_notice' => __('We collect non-sensitive diagnostic data and plugin usage information.
			Your site URL, WordPress & PHP version, plugins & themes and email address to send you the
			discount coupon. This data lets us make sure this plugin always stays compatible with the most
			popular plugins and themes. No spam, I promise.', 'betterdocs'),
		));
		$tracker->init();
	}

	public function wizard_action()
	{
		if (!check_ajax_referer('betterdocsqswnonce', 'nonce')) {
			return;
		}
		if ($this->do_wizard_tracking(true, $_POST)) {
			wp_send_json_success('done');
		}
		wp_send_json_error('Something went wrong.');
	}

	public function do_wizard_tracking($force = false, $data = [])
	{
		if (!class_exists('BetterDocs_Plugin_Usage_Tracker')) {
			require_once BETTERDOCS_DIR_PATH . 'includes/class-betterdocs-usage-tracker.php';
		}
		$tracker = BetterDocs_Plugin_Usage_Tracker::get_instance(BETTERDOCS_FILE, [
			'opt_in'       => true,
			'goodbye_form' => true,
			'item_id'      => 'c7b16777b4f1b83f6083'
		]);
		// If the home site hasn't been defined, we just drop out. Nothing much we can do.
		if (empty($tracker::API_URL)) {
			return false;
		}
		// Get our data
		$body = $tracker->get_data();
		if (isset($data['admin_email']) && !empty($data['admin_email'])) {
			$body['email'] = $data['admin_email'];
		}
		// Send the data
		return $tracker->send_data($body);
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the BetterDocs_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale()
	{
		$plugin_i18n = new BetterDocs_i18n();
		$this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks()
	{
		$plugin_admin = new BetterDocs_Admin($this->get_plugin_name(), $this->get_version());
		add_action('admin_enqueue_scripts', array($plugin_admin, 'enqueue_styles'));
		add_action('admin_enqueue_scripts', array($plugin_admin, 'enqueue_scripts'));
		add_action('admin_bar_menu', array($plugin_admin, 'toolbar_menu'), 32);

		$this->loader->add_filter('admin_body_class', $plugin_admin, 'body_classes');
		$admin_screen = new Betterdocs_Admin_Screen();
		$this->loader->add_action('betterdocs_listing_header', $admin_screen, 'header_template');

		BetterDocs_Settings::init();
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks()
	{
		$plugin_public = new BetterDocs_Public($this->get_plugin_name(), $this->get_version());
		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'load_assets');
//		$this->loader->add_action('enqueue_block_assets', $plugin_public, 'load_assets');
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run()
	{
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name()
	{
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    BetterDocs_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader()
	{
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version()
	{
		return $this->version;
	}

	public function is_pro_active()
	{
		return defined('BETTERDOCS_PRO_VERSION');
	}

	public function redirect()
	{
		// Bail if no activation transient is set.
		if (!get_transient('_betterdocs_meta_activation_notice')) {
			return;
		}
		// Delete the activation transient.
		delete_transient('_betterdocs_meta_activation_notice');

		if (!is_multisite()) {
			// Redirect to the welcome page.
			wp_safe_redirect(add_query_arg(array(
				'page'		=> 'betterdocs-setup'
			), admin_url('admin.php?page=betterdocs-setup')));
		}
	}
}
