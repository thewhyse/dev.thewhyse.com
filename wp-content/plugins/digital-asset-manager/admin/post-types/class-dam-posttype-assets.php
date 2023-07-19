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
class DAM_Posttype_Assets {

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
		$this->options = get_option( 'dam_settings_options', dam_settings_default_options() );
		add_filter( 'dam_asset_post_type_args', array( $this, 'dam_set_dam_post_type_visibility' ), 99, 1 );
	}

	/**
	 * Register CPT named Asset.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function dam_post_type_assets() {

		$labels = array(
			'name'                  => _x( 'Assets', 'Post Type General Name', 'digital-asset-manager' ),
			'singular_name'         => _x( 'Asset', 'Post Type Singular Name', 'digital-asset-manager' ),
			'menu_name'             => __( 'Assets', 'digital-asset-manager' ),
			'name_admin_bar'        => __( 'Assets', 'digital-asset-manager' ),
			'archives'              => __( 'Asset Archives', 'digital-asset-manager' ),
			'attributes'            => __( 'Asset Attributes', 'digital-asset-manager' ),
			'parent_item_colon'     => __( 'Parent Asset:', 'digital-asset-manager' ),
			'all_items'             => __( 'All Assets', 'digital-asset-manager' ),
			'add_new_item'          => __( 'Add New Asset', 'digital-asset-manager' ),
			'add_new'               => __( 'Add Asset', 'digital-asset-manager' ),
			'new_item'              => __( 'New Asset', 'digital-asset-manager' ),
			'edit_item'             => __( 'Edit Asset', 'digital-asset-manager' ),
			'update_item'           => __( 'Update Asset', 'digital-asset-manager' ),
			'view_item'             => __( 'View Asset', 'digital-asset-manager' ),
			'view_items'            => __( 'View Assets', 'digital-asset-manager' ),
			'search_items'          => __( 'Search Asset', 'digital-asset-manager' ),
			'not_found'             => __( 'Not found', 'digital-asset-manager' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'digital-asset-manager' ),
			'featured_image'        => __( 'Featured Image', 'digital-asset-manager' ),
			'set_featured_image'    => __( 'Set featured image', 'digital-asset-manager' ),
			'remove_featured_image' => __( 'Remove featured image', 'digital-asset-manager' ),
			'use_featured_image'    => __( 'Use as featured image', 'digital-asset-manager' ),
			'insert_into_item'      => __( 'Insert into Asset', 'digital-asset-manager' ),
			'uploaded_to_this_item' => __( 'Uploaded to this Asset', 'digital-asset-manager' ),
			'items_list'            => __( 'Assets list', 'digital-asset-manager' ),
			'items_list_navigation' => __( 'Assets list navigation', 'digital-asset-manager' ),
			'filter_items_list'     => __( 'Filter Assets list', 'digital-asset-manager' ),
		);
		$args   = array(
			'label'                => __( 'Asset', 'digital-asset-manager' ),
			'description'          => __( 'Post type to manage assets like themes, plugins and other', 'digital-asset-manager' ),
			'labels'               => $labels,
			'supports'             => array( 'title', 'editor', 'thumbnail' ),
			'hierarchical'         => false,
			'public'               => false,
			'show_ui'              => true,
			'show_in_menu'         => true,
			'menu_position'        => 20,
			'menu_icon'            => 'dashicons-database-add',
			'show_in_admin_bar'    => false,
			'show_in_nav_menus'    => false,
			'can_export'           => true,
			'has_archive'          => false,
			'exclude_from_search'  => true,
			'publicly_queryable'   => false,
			'capability_type'      => 'post',
			'show_in_rest'         => true,
			'register_meta_box_cb' => array( $this, 'dam_meta_box_on_asset_pt' ),
		);

		register_post_type( 'dam-asset', apply_filters( 'dam_asset_post_type_args', $args ) );

	}

	/**
	 * Configured the visibility of the Asset post type base on the settings.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Array of args to create asset post type.
	 * @return array
	 */
	public function dam_set_dam_post_type_visibility( $args ) {
		if ( isset( $this->options['asset_post_type_visibility'] ) && 'public' === $this->options['asset_post_type_visibility'] ) {
			$args['public']              = true;
			$args['show_in_admin_bar']   = true;
			$args['show_in_nav_menus']   = true;
			$args['has_archive']         = true;
			$args['exclude_from_search'] = false;
			$args['publicly_queryable']  = true;
		}
		if ( isset( $this->options['asset_post_type_capability'] ) && 'page' === $this->options['asset_post_type_capability'] ) {
			$args['supports'][]      = 'page-attributes';
			$args['hierarchical']    = true;
			$args['capability_type'] = 'page';
		}
		return $args;
	}

	/**
	 * Disable Gutenburg Editor For CPT Asset.
	 *
	 * @since 1.0.0
	 *
	 * @param boolean $gutenberg_filter Either the current CPT is comaptible with GutenBurg Builder or not.
	 * @param string  $post_type Slug of the post type.
	 * @return boolean
	 */
	public function dam_disable_gutenberg_for_asset_pt( $gutenberg_filter, $post_type ) {
		if ( 'dam-asset' === $post_type ) {
			return false;
		}
		return $gutenberg_filter;
	}

	/**
	 * Add Custom Meta Box on Asset Post Type.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function dam_meta_box_on_asset_pt() {
		add_meta_box(
			'dam_asset_pt_metabox',
			'Assets Details',
			array( $this, 'dam_meta_box_on_asset_pt_html' ),
			'dam-asset'
		);
	}

	/**
	 * Display HTML form field to custom meta box on Asset post type.
	 *
	 * @since 1.0.0
	 *
	 * @param object $post Current post object.
	 * @return void
	 */
	public function dam_meta_box_on_asset_pt_html( $post ) {

		// retrieve all custom meta data of the post.
		$custom_fields       = get_post_custom( $post->ID );
		$record_number       = isset( $custom_fields['record_number'] ) ? $custom_fields['record_number'][0] : '';
		$asset_version       = isset( $custom_fields['asset_version'] ) ? $custom_fields['asset_version'][0] : '';
		$drive_url           = isset( $custom_fields['drive_url'] ) ? $custom_fields['drive_url'][0] : '';
		$live_url            = isset( $custom_fields['live_url'] ) ? $custom_fields['live_url'][0] : '';
		$download_url        = isset( $custom_fields['download_url'] ) ? $custom_fields['download_url'][0] : '';
		$license_key         = isset( $custom_fields['license_key'] ) ? $custom_fields['license_key'][0] : '';
		$total_view          = isset( $custom_fields['total_view'] ) ? $custom_fields['total_view'][0] : 0;
		$total_download      = isset( $custom_fields['total_download'] ) ? $custom_fields['total_download'][0] : 0;
		$account_credentials = isset( $custom_fields['account_credentials'] ) ? $custom_fields['account_credentials'][0] : '';
		$private_note        = isset( $custom_fields['private_note'] ) ? $custom_fields['private_note'][0] : '';

		// Add nonce.
		wp_nonce_field( 'dam_asset_fields_nonce', 'asset_fields_nonce' );

		// Includes metabox HTML content.
		require_once DAM_ADMIN_PATH . 'partials/digital-asset-manager-asset-metabox.php';
	}

	/**
	 * Save custom meta box fields on saivng the post type.
	 *
	 * @since 1.0.0
	 *
	 * @param int    $post_id Post ID of current post.
	 * @param object $post_obj Current post object.
	 * @return void
	 */
	public function dam_save_asset_custom_fields( $post_id, $post_obj ) {

		// Bail if we're doing an auto save.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// if our nonce isn't there, or we can't verify it, bail.
		if ( ! isset( $_POST['asset_fields_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['asset_fields_nonce'] ) ), 'dam_asset_fields_nonce' ) ) {
			return;
		}

		// if our current user can't edit this post, bail.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// if post type is not equal to dam-asset, bail.
		if ( 'dam-asset' !== $post_obj->post_type ) {
			return;
		}

		// record_number.
		if ( isset( $_POST['record_number'] ) && '' !== $_POST['record_number'] ) {
			update_post_meta( $post_id, 'record_number', sanitize_text_field( wp_unslash( $_POST['record_number'] ) ) );
		} else {
			delete_post_meta( $post_id, 'record_number' );
		}

		// asset_version.
		if ( isset( $_POST['asset_version'] ) && '' !== $_POST['asset_version'] ) {
			update_post_meta( $post_id, 'asset_version', sanitize_text_field( wp_unslash( $_POST['asset_version'] ) ) );
		} else {
			delete_post_meta( $post_id, 'asset_version' );
		}

		// drive_url.
		if ( isset( $_POST['drive_url'] ) && '' !== $_POST['drive_url'] ) {
			update_post_meta( $post_id, 'drive_url', esc_url_raw( wp_unslash( $_POST['drive_url'] ) ) );
		} else {
			delete_post_meta( $post_id, 'drive_url' );
		}

		// live_url.
		if ( isset( $_POST['live_url'] ) && '' !== $_POST['live_url'] ) {
			update_post_meta( $post_id, 'live_url', esc_url_raw( wp_unslash( $_POST['live_url'] ) ) );
		} else {
			delete_post_meta( $post_id, 'live_url' );
		}

		// download_url.
		if ( isset( $_POST['download_url'] ) && '' !== $_POST['download_url'] ) {
			update_post_meta( $post_id, 'download_url', esc_url_raw( wp_unslash( $_POST['download_url'] ) ) );
		} else {
			delete_post_meta( $post_id, 'download_url' );
		}

		// license_key.
		if ( isset( $_POST['license_key'] ) && '' !== $_POST['license_key'] ) {
			update_post_meta( $post_id, 'license_key', sanitize_text_field( wp_unslash( $_POST['license_key'] ) ) );
		} else {
			delete_post_meta( $post_id, 'license_key' );
		}

		// total_view.
		if ( isset( $_POST['total_view'] ) && '' !== $_POST['total_view'] ) {
			update_post_meta( $post_id, 'total_view', sanitize_text_field( wp_unslash( $_POST['total_view'] ) ) );
		} else {
			delete_post_meta( $post_id, 'total_view' );
		}

		// total_download.
		if ( isset( $_POST['total_download'] ) && '' !== $_POST['total_download'] ) {
			update_post_meta( $post_id, 'total_download', sanitize_text_field( wp_unslash( $_POST['total_download'] ) ) );
		} else {
			delete_post_meta( $post_id, 'total_download' );
		}

		$allowed_html = array(
			'a'      => array(
				'href'  => array(),
				'title' => array(),
			),
			'br'     => array(),
			'em'     => array(),
			'strong' => array(),
		);

		// account_credentials.
		if ( isset( $_POST['account_credentials'] ) && '' !== $_POST['account_credentials'] ) {
			update_post_meta( $post_id, 'account_credentials', wp_kses( wp_unslash( $_POST['account_credentials'] ), $allowed_html ) );
		} else {
			delete_post_meta( $post_id, 'account_credentials' );
		}

		// private_note.
		if ( isset( $_POST['private_note'] ) && '' !== $_POST['private_note'] ) {
			update_post_meta( $post_id, 'private_note', wp_kses( wp_unslash( $_POST['private_note'] ), $allowed_html ) );
		} else {
			delete_post_meta( $post_id, 'private_note' );
		}
	}

	/**
	 * Added custom columns to the Asset Custom Post Type.
	 *
	 * @since 1.0.0
	 *
	 * @param array $columns Array of columns name with keys.
	 * @return array Array of columns name with keys.
	 */
	public function dam_manage_assest_table_columns( $columns ) {

		$columns['dam_record']   = __( 'Record No.', 'tti-platform' );
		$columns['dam_version']  = __( 'Version', 'tti-platform' );
		$columns['dam_license']  = __( 'License Key', 'tti-platform' );
		$columns['dam_status']     = __( 'Status', 'tti-platform' );
		$columns['dam_actions']  = __( 'Actions', 'tti-platform' );

		return $columns;
	}

	/**
	 * Undocumented function
	 *
	 * @param string $column Key of currently displaying column.
	 * @param string $post_id Id of the current post.
	 * @return void
	 */
	public function dam_manage_assest_table_custom_columns( $column, $post_id ) {

		$record_number = get_post_meta( $post_id, 'record_number', true );
		$record_number = ( ! empty( $record_number ) ) ? $record_number : '-';

		$asset_version = get_post_meta( $post_id, 'asset_version', true );
		$asset_version = ( ! empty( $asset_version ) ) ? $asset_version : '-';

		$license_key = get_post_meta( $post_id, 'license_key', true );

		$total_view = get_post_meta( $post_id, 'total_view', true );
		$total_view = ( ! empty( $total_view ) ) ? $total_view : 0;

		$total_download = get_post_meta( $post_id, 'total_download', true );
		$total_download = ( ! empty( $total_download ) ) ? $total_download : 0;

		$drive_url = get_post_meta( $post_id, 'drive_url', true );
		$live_url  = get_post_meta( $post_id, 'live_url', true );

		switch ( $column ) {
			case 'dam_record':
				echo esc_html( $record_number );
				break;
			case 'dam_version':
				echo esc_html( $asset_version );
				break;
			case 'dam_license':
				echo wp_kses( $this->dam_asset_copy_to_clipboard_on_click( $license_key ), 'post' );
				break;
			case 'dam_status':
				echo wp_kses( $this->dam_asset_get_status_html( $total_view, $total_download ), 'post' );
				break;
			case 'dam_actions':
				echo wp_kses( $this->dam_asset_get_actions_html( $live_url, $drive_url ), 'post' );
				break;
		}

	}

	/**
	 * Get the view and download status HTML with icons
	 *
	 * @param string $total_view Live url of the asset.
	 * @param string $total_download Drive url of the asset.
	 * @return string
	 */
	public function dam_asset_get_status_html( $total_view, $total_download ) {

		$actions_html = '
			<div class="dam-actions">
				<ul class="dam-actions-list">
					<li class="tooltip"><a class="dashicons-before dashicons-welcome-view-site" href="#!"><span class="tooltiptext" id="myTooltip">Total Views: ' . $total_view . '</span></a></li>
					<li class="tooltip"><a class="dashicons-before dashicons-download" href="#!"><span class="tooltiptext" id="myTooltip">Total Downloads: ' . $total_download . '</span></a></li>
				</ul>
			</div>
		';

		return $actions_html;
	}

	/**
	 * Return the path of the single or archive template.
	 *
	 * @param string $template_path Path to the tmeplate.
	 * @return string
	 */
	public function dam_asset_post_type_templates( $template_path ) {
		if ( 'dam-asset' === get_post_type() ) {
			if ( is_single() ) {
				$template_path = DAM_PUBLIC_PATH . 'partials/digital-asset-manager-single-asset.php';
			} elseif ( is_archive() ) {
				$template_path = DAM_PUBLIC_PATH . 'partials/digital-asset-manager-archive-asset.php';
			}
		}
		return $template_path;
	}

	/**
	 * Convert the license keys to clickable copy to clipboard button
	 *
	 * @param string $license_key Store list of license keys.
	 * @return string Click and copy able license keys.
	 */
	public function dam_asset_copy_to_clipboard_on_click( $license_key ) {

		if ( empty( $license_key ) ) {
			return '-';
		} else {
				$keys           = explode( ',', $license_key );
				$clickable_html = '
				<div class="dam-actions">
					<ul class="dam-actions-list">';
			foreach ( $keys as $key => $value ) {
				$clickable_html .= '<li class="tooltip"><a class="dam-action dam-license-key dashicons-before dashicons-admin-network" href="#!" title="' . trim( $value ) . '" ><span class="tooltiptext" id="myTooltip">Copy to clipboard</span></a></li>';
			}
			$clickable_html .= '
			</ul>
			</div>
			';

			return $clickable_html;
		}

	}

	/**
	 * Convert the tokens to clickable copy to clipboard button
	 *
	 * @param array  $term_links Array of added terms links.
	 * @param string $taxonomy Slug of the custom taxonomy.
	 * @param array  $terms List of added terms objects.
	 * @return array
	 */
	public function dam_manage_assest_token_column( $term_links, $taxonomy, $terms ) {

		if ( 'dam_token' === $taxonomy ) {
				$term_links = array();
			foreach ( $terms as $term ) {
				$term_links[] = '
				<li class="tooltip dam-token-li"><a class="dam-action dam-license-key dashicons-before dashicons-media-code" href="#!" title="' . trim( $term->slug ) . '" ><span class="tooltiptext" id="myTooltip">Copy to clipboard</span></a></li>';
			}
		}

		return $term_links;

	}

	/**
	 * Get the action HTML with icons
	 *
	 * @param string $live_url Live url of the asset.
	 * @param string $drive_url Drive url of the asset.
	 * @return string
	 */
	public function dam_asset_get_actions_html( $live_url, $drive_url ) {

		$actions_html = '
			<div class="dam-actions">
				<ul class="dam-actions-list">';
		if ( ! empty( $live_url ) ) {
			$actions_html .= '<li class="tooltip"><a class="dam-action dam-view dashicons-before dashicons-welcome-view-site" href="' . $live_url . '" target="_blank"><span class="tooltiptext" id="myTooltip">Live URL</span></a></li>';
		}
		if ( ! empty( $drive_url ) ) {
			$actions_html .= '<li class="tooltip"><a class="dam-action dam-download dashicons-before dashicons-download" href="' . $drive_url . '" target="_blank"><span class="tooltiptext" id="myTooltip">Download</span></a></li>';
		}
		$actions_html .= '
				</ul>
			</div>
		';

		return $actions_html;
	}
}
