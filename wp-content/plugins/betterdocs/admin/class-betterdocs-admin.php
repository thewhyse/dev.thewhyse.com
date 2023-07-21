<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://wpdeveloper.com
 * @since      1.0.0
 *
 * @package    BetterDocs
 * @subpackage BetterDocs/admin
 * @author     WPDeveloper <support@wpdeveloper.com>
 */

class BetterDocs_Admin
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;
	private $menu_slug;
	/**
	 * All builder args
	 *
	 * @var array
	 */
	private $builder_args;
	/**
	 * Builder Metabox ID
	 *
	 * @var string
	 */
	private $metabox_id;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The type.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var string the post type of betterdocs.
	 */
	public $type = 'docs';

	public $metabox;

	public static $prefix = 'betterdocs_meta_';

	public static $settings;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public static $counts;

	public static $enabled_types = [];
	public static $active_items = [];

	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->metabox = new BetterDocs_MetaBox();
		self::$settings = BetterDocs_DB::get_settings();
		add_action('wp_ajax_betterdocs_dark_mode', array($this, 'dark_mode'));
		add_action('wp_ajax_update_doc_cat_order', array($this, 'update_doc_cat_order'));
		add_action('wp_ajax_update_doc_order_by_category', array($this, 'update_doc_order_by_category'));
		add_action('wp_ajax_update_docs_term', array($this, 'update_docs_term'));
		add_action('save_post_docs', array($this, 'update_new_post_doc_order_by_category'));
		add_filter('betterdocs_articles_args', array($this, 'docs_args'), 11, 2);
		add_action('new_to_auto-draft', array($this, 'auto_add_category'));
		add_action('wp_ajax_test_email_report', array($this, 'test_email_report'));
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles($hook)
	{
		wp_enqueue_style(
			$this->plugin_name . '-admin-global',
			BETTERDOCS_ADMIN_URL . 'assets/css/betterdocs-global.css',
			array(),
			$this->version,
			'all'
		);

		$tax = function_exists('get_current_screen') ? get_current_screen() : '';
		if (!in_array($hook, array('toplevel_page_betterdocs-admin', 'betterdocs_page_betterdocs-settings', 'betterdocs_page_betterdocs-analytics', 'betterdocs_page_betterdocs-faq')) && $tax->taxonomy !== 'doc_category') {
			return;
		}

		wp_enqueue_style(
			$this->plugin_name . '-select2',
			BETTERDOCS_ADMIN_URL . 'assets/css/select2.min.css',
			array(),
			$this->version,
			'all'
		);

		wp_enqueue_style(
			'daterangepicker',
			BETTERDOCS_ADMIN_URL . 'assets/css/daterangepicker.css',
			array(),
			$this->version,
			'all'
		);

		wp_enqueue_style(
			$this->plugin_name,
			BETTERDOCS_ADMIN_URL . 'assets/css/betterdocs-admin.css',
			array(),
			$this->version,
			'all'
		);

		wp_enqueue_style(
			$this->plugin_name . '-faq',
			BETTERDOCS_ADMIN_URL . 'assets/css/betterdocs-faq.css',
			array(),
			$this->version,
			'all'
		);
	}
	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts($hook)
	{
		wp_enqueue_script(
			'betterdocs-admin-global',
			BETTERDOCS_ADMIN_URL . 'assets/js/admin-global.js',
			array('jquery'),
			$this->version,
			true
		);

		if( $hook === 'betterdocs_page_betterdocs-faq') {
			$assets_faq = require_once BETTERDOCS_DIR_PATH . 'admin/assets/js/betterdocs-faq.asset.php';
	
			wp_enqueue_script(
				'betterdocs-faq',
				BETTERDOCS_ADMIN_URL . 'assets/js/betterdocs-faq.js',
				$assets_faq['dependencies'],
				$assets_faq['version'],
				true
			);

			wp_set_script_translations( 
				'betterdocs-faq',
				'betterdocs',
				BETTERDOCS_URL . 'languages' 
		   );
			
			// removing emoji support
			remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
			remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	
			wp_localize_script(
					'betterdocs-faq',
					'betterdocs',
					array(
							'dir_url' => BETTERDOCS_URL,
							'rest_url' => esc_url_raw( rest_url() ),
							'free_version' => BETTERDOCS_VERSION,
							'nonce' => wp_create_nonce( 'wp_rest' )
					)
			);
		}

		wp_localize_script(
			'betterdocs-admin-global',
			'betterdocs_admin',
			array(
				'menu_title' => __('Switch to BetterDocs UI', 'betterdocs'),
				'site_address' => get_bloginfo('url'),
				'betterdocs_pro_plugin' => is_plugin_active('betterdocs-pro/betterdocs-pro.php') ? 'true' : 'false'
			)
		);

		$tax = function_exists('get_current_screen') ? get_current_screen() : '';

		if (!in_array($hook, array('toplevel_page_betterdocs-admin', 'betterdocs_page_betterdocs-settings', 'betterdocs_page_betterdocs-analytics', 'betterdocs_page_betterdocs-setup')) && $tax->taxonomy !== 'doc_category') {
			return;
		}

		wp_enqueue_script('wp-color-picker');

		wp_enqueue_script(
			$this->plugin_name . '-select2',
			BETTERDOCS_ADMIN_URL . 'assets/js/select2.min.js',
			array('jquery'),
			$this->version,
			true
		);

		wp_enqueue_script(
			$this->plugin_name . '-sweetalert',
			BETTERDOCS_ADMIN_URL . 'assets/js/sweetalert.min.js',
			array('jquery'),
			$this->version,
			true
		);

		wp_enqueue_script(
			'moment',
			BETTERDOCS_ADMIN_URL . 'assets/js/moment.min.js',
			array('jquery'),
			$this->version,
			true
		);

		wp_enqueue_script(
			'daterangepicker',
			BETTERDOCS_ADMIN_URL . 'assets/js/daterangepicker.min.js',
			array('jquery'),
			$this->version,
			true
		);


		wp_enqueue_script(
			$this->plugin_name,
			BETTERDOCS_ADMIN_URL . 'assets/js/betterdocs-admin.js',
			array('jquery'),
			$this->version,
			true
		);

		$dark_mode = false;

		if (class_exists('BetterDocs_DB')) {
			$dark_mode = BetterDocs_DB::get_settings('dark_mode');
		}

		wp_localize_script(
			$this->plugin_name,
			'betterdocs_admin',
			array(
				'ajaxurl'             => admin_url('admin-ajax.php'),
				'doc_cat_order_nonce' => wp_create_nonce('doc_cat_order_nonce'),
				'knowledge_base_order_nonce' => wp_create_nonce('knowledge_base_order_nonce'),
				'paged'               => isset($_GET['paged']) ? absint(wp_unslash($_GET['paged'])) : 0,
				'per_page_id'         => "edit_{$tax->taxonomy}_per_page",
				'menu_title'          => __('Switch to BetterDocs UI', 'betterdocs'),
				'dark_mode'           => !empty($dark_mode) ? boolval($dark_mode) : false,
				'text' => esc_html__('Copied!', 'betterdocs'),
				'test_report' => esc_html__('Test Report!', 'betterdocs'),
				'sending' => esc_html__('Sending...', 'betterdocs'),
			)
		);

		wp_localize_script($this->plugin_name, 'betterdocsAdminConfig', self::toggleFields());
	}

	public function body_classes($classes)
	{
		$saved_settings = get_option('betterdocs_settings', false);
		$dark_mode = isset($saved_settings['dark_mode']) ? $saved_settings['dark_mode'] : false;
		$dark_mode = !empty($dark_mode) ? boolval($dark_mode) : false;
		if ($dark_mode === true) {
			$classes .= ' betterdocs-dark-mode ';
		}
		return $classes;
	}

	/**
	 * AJAX Handler to update terms' tax position.
	 */
	public function dark_mode()
	{
		if (!check_ajax_referer('doc_cat_order_nonce', 'nonce', false)) {
			wp_send_json_error();
		}

		if (isset($_POST['mode'])) {
			$saved_settings = BetterDocs_DB::get_settings();
			$saved_settings['dark_mode'] = $_POST['mode'];

			if (BetterDocs_DB::update_settings($saved_settings)) {
				wp_send_json_success();
			}
		}

		wp_send_json_error();
	}

	public function toggleFields($builder = false)
	{

		$args = BetterDocs_Settings::settings_args();

		$toggleFields = $hideFields = $conditions = array();

		$tabs = $args;
		if (!empty($tabs)) {
			foreach ($tabs as $tab_id => $tab) {
				$sections = isset($tab['sections']) ? $tab['sections'] : [];
				if (!empty($sections)) {
					foreach ($sections as $section_id => $section) {
						$fields = isset($section['fields']) ? $section['fields'] : [];
						if (isset($section['tabs']) && !empty($section['tabs'])) {
							foreach ($section['tabs'] as $inner_field_tab_key => $inner_field_tab) {
								if (isset($inner_field_tab['fields'])) {
									foreach ($inner_field_tab['fields'] as $inner_tab_field_key => $inner_tab_field_tab) {
										if (isset($inner_tab_field_tab['hide']) && !empty($inner_tab_field_tab['hide']) && is_array($inner_tab_field_tab['hide'])) {
											$hideFields = $this->a_walk($inner_tab_field_tab['hide'], $inner_tab_field_key, $hideFields);
										}
										if (isset($inner_tab_field_tab['dependency']) && !empty($inner_tab_field_tab['dependency']) && is_array($inner_tab_field_tab['dependency'])) {
											$conditions = $this->a_walk($inner_tab_field_tab['dependency'], $inner_tab_field_key, $conditions);
										}
									}
								}
							}
						}
						if (!empty($fields)) {
							foreach ($fields as $field_key => $field) {
								if (isset($field['fields'])) {
									$iFields =  $field['fields'];
									foreach ($iFields as $inner_field_key => $inner_field) {
										if (isset($inner_field['hide']) && !empty($inner_field['hide']) && is_array($inner_field['hide'])) {
											$hideFields = $this->a_walk($inner_field['hide'], $inner_field_key, $hideFields);
										}
										if (isset($inner_field['dependency']) && !empty($inner_field['dependency']) && is_array($inner_field['dependency'])) {
											$conditions = $this->a_walk($inner_field['dependency'], $inner_field_key, $conditions);
										}
									}
								}
								if (isset($field['hide']) && !empty($field['hide']) && is_array($field['hide'])) {
									$hideFields = $this->a_walk($field['hide'], $field_key, $hideFields);
								}
								if (isset($field['dependency']) && !empty($field['dependency']) && is_array($field['dependency'])) {
									$conditions = $this->a_walk($field['dependency'], $field_key, $conditions);
								}
							}
						}
					}
				}
			}
		}

		return array(
			'toggleFields' => $conditions, // TODO: toggling system has to be more optimized! 
			'hideFields' => $hideFields,
		);
	}

	public function a_walk($array, $field_key, &$returned_array = [])
	{
		array_walk($array, function ($value, $key) use ($field_key, &$returned_array) {
			$returned_array[$field_key][$key] = $value;
		});

		return $returned_array;
	}

	public function quick_builder()
	{
		$builder_args = $this->builder_args;
		$tabs         = $this->builder_args['tabs'];
		$prefix       = self::$prefix;
		$metabox_id   = $this->metabox_id;
		/**
		 * This lines of code is for editing a notification in simple|quick builder
		 *
		 * @var  [type]
		 */
		$idd = null;
		if (isset($_GET['post_id']) && !empty($_GET['post_id'])) {
			$idd = intval($_GET['post_id']);
		}
		include_once BETTERDOCS_ADMIN_DIR_PATH . 'partials/betterdocs-quick-builder-display.php';
	}
	/**
	 * Generate the builder data acording to default meta data
	 *
	 * @param array $data
	 * @return array
	 */
	protected function builder_data($data)
	{
		$post_data   = [];
		$prefix      = self::$prefix;
		$meta_fields = BetterDocs_MetaBox::get_metabox_fields($prefix);
		foreach ($meta_fields as $meta_key => $meta_field) {
			if (in_array($meta_key, array_keys($data))) {
				$post_data[$meta_key] = $data[$meta_key];
			} else {
				$post_data[$meta_key] = '';

				if (isset($meta_field['defaults'])) {
					$post_data[$meta_key] = $meta_field['defaults'];
				}
				if (isset($meta_field['default'])) {
					$post_data[$meta_key] = $meta_field['default'];
				}
			}
		}

		return array_merge($post_data, $data);
	}

	public static function get_form_action($query_var = '', $builder_form = false)
	{
		$page = '/edit.php?post_type=docs&page=betterdocs-settings';

		if (is_network_admin()) {
			return network_admin_url($page . $query_var);
		} else {
			return admin_url($page . $query_var);
		}
	}


	/**
	 * Admin Init For User Interactions
	 * @return void
	 */
	public function admin_init($hook)
	{
		/**
		 * BetterDocs Admin URL
		 */
		$current_url = admin_url('edit.php?post_type=docs&page=betterdocs-settings');
	}
	public function toolbar_menu($admin_bar)
	{
		if (!is_admin() || !is_admin_bar_showing()) {
			return;
		}

		// Show only when the user is a member of this site, or they're a super admin.
		if (!is_user_member_of_blog() && !is_super_admin()) {
			return;
		}

		$saved_settings = BetterDocs_DB::get_settings();
		$docs_url = '';
		if (isset($saved_settings['builtin_doc_page']) && intval($saved_settings['builtin_doc_page'])) {
			$docs_url = get_post_type_archive_link(BetterDocs_Docs_Post_Type::$post_type);
		} elseif (isset($saved_settings['docs_page']) && intval($saved_settings['docs_page'])) {
			$docs_url = !empty($saved_settings['docs_page']) ? get_page_link($saved_settings['docs_page']) : false;
		}

		if ($docs_url) {
			$admin_bar->add_node(
				array(
					'parent' => 'site-name',
					'id'     => 'view-docs',
					'title'  => __('Visit Documentation', 'betterdocs'),
					'href'   => $docs_url,
				)
			);
		}
	}

	/**
	 * Auto Add in Category, Adding from Sorting
	 *
	 * @param WP_Post $post
	 * @return void
	 */
	public function auto_add_category($post)
	{
		if (!strpos($_SERVER['REQUEST_URI'], 'wp-admin/post-new.php')) {
			return;
		}
		if (empty($_GET['cat'])) {
			return;
		}
		$cat = wp_unslash($_GET['cat']);
		if (false === ($cat = get_term_by('term_id', $cat, 'doc_category'))) {
			return;
		}
		wp_set_post_terms($post->ID, array($cat->term_id), 'doc_category', false);
	}

	/**
	 *
	 * AJAX Handler to update terms' tax position.
	 *
	 */
	public function update_doc_cat_order()
	{
		if (!check_ajax_referer('doc_cat_order_nonce', 'doc_cat_order_nonce', false)) {
			wp_send_json_error();
		}

		$taxonomy_ordering_data = filter_var_array(wp_unslash($_POST['taxonomy_ordering_data']), FILTER_SANITIZE_NUMBER_INT);
		$base_index             = filter_var(wp_unslash($_POST['base_index']), FILTER_SANITIZE_NUMBER_INT);

		foreach ($taxonomy_ordering_data as $order_data) {
			if ($base_index > 0) {
				$current_position = get_term_meta($order_data['term_id'], 'doc_category_order', true);

				if ((int) $current_position < (int) $base_index) {
					continue;
				}
			}
			update_term_meta($order_data['term_id'], 'doc_category_order', ((int) $order_data['order'] + (int) $base_index));
		}
		wp_send_json_success();
	}

	/**
	 * AJAX Handler to update docs position.
	 */
	public function update_doc_order_by_category()
	{
		if (!check_ajax_referer('doc_cat_order_nonce', 'doc_cat_order_nonce', false)) {
			wp_send_json_error();
		}

		$docs_ordering_data = isset( $_POST['docs_ordering_data'] ) ? implode( ',', filter_var_array( $_POST['docs_ordering_data'],  FILTER_SANITIZE_NUMBER_INT ) ) : '' ;
		$term_id = intval($_POST['list_term_id']);

		if (!$term_id) {
			wp_send_json_error();
		}
		
		if ( update_term_meta( $term_id, '_docs_order', $docs_ordering_data ) ) {
			wp_send_json_success();
		}
	}

	/**
	 * AJAX Handler to update docs position.
	 */
	public function update_docs_term()
	{
		if (!check_ajax_referer('doc_cat_order_nonce', 'doc_cat_order_nonce', false)) {
			wp_send_json_error();
		}

		$object_id = intval($_POST['object_id']);
		$term_id = intval($_POST['list_term_id']);
		$prev_term_id = intval(isset($_POST['prev_term_id']) ? $_POST['prev_term_id'] : 0);

		if (!$term_id || !$object_id) {

			wp_send_json_error();
		}

		global $wpdb;

		if ($prev_term_id) {

			wp_remove_object_terms($object_id, $prev_term_id, 'doc_category');
		}

		$terms_added = wp_set_object_terms($object_id, $term_id, 'doc_category');

		if (!is_wp_error($terms_added)) {

			wp_send_json_success();
		}

		wp_send_json_error();
	}

	/**
	 * Update docs_term meta when new post created
	 */

	public function update_new_post_doc_order_by_category($post_id)
	{
		$term_list = wp_get_post_terms($post_id, 'doc_category', array('fields' => 'ids'));

		if (!empty($term_list)) {
			foreach ($term_list as $term_id) {
				$term = get_term($term_id, 'doc_category');
				$term_slug = $term->slug;
				$term_meta = get_term_meta($term_id, '_docs_order');
				if (!empty($term_meta)) {
					$term_meta_arr = explode(",", $term_meta[0]);

					if (!in_array($post_id, $term_meta_arr)) {
						array_unshift($term_meta_arr, $post_id);
						$docs_ordering_data = filter_var_array(wp_unslash($term_meta_arr), FILTER_SANITIZE_NUMBER_INT);
						$val = implode(',', $docs_ordering_data);
						update_term_meta($term_id, '_docs_order', implode(',', $docs_ordering_data));
					}
				}
			}
		}
	}

	/**
	 *
	 * Update docs query arguments
	 *
	 */

	public function docs_args($args, $term_id = null)
	{
		if (is_null($term_id) || isset($args['orderby'])) {
			return $args;
		}

		$docs_order = get_term_meta($term_id, '_docs_order', true);

		global $wpdb;

		if (!empty($docs_order)) {

			$docs_order = explode(',', $docs_order);

			$new_ids = [];
			$results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}term_relationships WHERE term_taxonomy_id = $term_id");

			if (!is_null($results) && !empty($results) && is_array($results)) {

				$object_ids = array_filter($results, function ($value) use ($docs_order) {
					return !in_array($value->object_id, $docs_order);
				});

				if (!empty($object_ids)) {

					array_walk($object_ids, function ($value) use (&$new_ids) {
						$new_ids[] = $value->object_id;
					});
				}
			}

			$args['orderby'] = 'post__in';
			$args['post__in'] = array_merge($new_ids, $docs_order);
		}

		return $args;
	}

	/**
	 *
	 * AJAX Handler to update terms' tax position.
	 *
	 */
	public function test_email_report()
	{
		$template = new BetterDocs_Report_Email();
		$reporting_frequency = apply_filters('betterdocs_test_reporting_frequency', 'betterdocs_weekly');
		$email = $template->send_email($reporting_frequency);

		if ($email) {
			wp_send_json_success();
		} else {
			wp_send_json_error();
		}
	}
}
