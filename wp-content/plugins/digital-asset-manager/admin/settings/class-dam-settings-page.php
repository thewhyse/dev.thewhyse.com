<?php
/**
 * The file that defines options on settings page.
 *
 * A class definition that register the setting sections and fields in the admin area.
 *
 * @link       https://2bytecode.com/
 * @since      1.0.0
 *
 * @package    Digital_Asset_Manager
 * @subpackage Digital_Asset_Manager/admin
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, dashboard-specific hooks, and
 * public-facing site hooks.
 *
 * @since   1.0.0
 * @package    Digital_Asset_Manager
 * @subpackage Digital_Asset_Manager/includes
 * @author     2ByteCode <support@2bytecode.com>
 */
class DAM_Settings_Page {

	/**
	 * Define the core functionality of the plugin.
	 *
	 * @since       1.0.0
	 */
	public function __construct() {
		$this->options = get_option( 'dam_settings_options', dam_settings_default_options() );
	}

	/**
	 * Create an option page for DAM Settings.
	 *
	 * @return void
	 */
	public function dam_settings_options_page() {
		add_submenu_page(
			'edit.php?post_type=dam-asset',
			esc_html__( 'Assets Management Settings', 'digital-asset-manager' ),
			esc_html__( 'Settings', 'digital-asset-manager' ),
			'manage_options',
			'dam-settings',
			array( $this, 'dam_settings_page_html' ),
			99
		);
	}

	/**
	 * Display HTML content for the settings page.
	 *
	 * @return void
	 */
	public function dam_settings_page_html() {

		// check if user is allowed access.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Includes settings page html.
		require_once DAM_ADMIN_PATH . 'partials/digital-asset-manager-settings-page.php';

	}

	/**
	 * Register settings section and fields.
	 *
	 * @return void
	 */
	public function dam_register_settings() {

		register_setting(
			'dam_settings_options',
			'dam_settings_options',
			array( $this, 'dam_validate_options_callback' )
		);

		// Asset Post Type Settings.
		add_settings_section(
			'dam_asset_post_type_section',
			esc_html__( 'Asset Post Type', 'digital-asset-manager' ),
			array( $this, 'dam_asset_post_type_section_description' ),
			'dam-settings'
		);

		add_settings_field(
			'asset_post_type_capability',
			esc_html__( 'Capability', 'digital-asset-manager' ),
			array( $this, 'myplugin_callback_field_radio' ),
			'dam-settings',
			'dam_asset_post_type_section',
			array(
				'id'   => 'asset_post_type_capability',
				'desc' => esc_html__( 'Set the capability for asset post type.', 'digital-asset-manager' ),
			)
		);

		add_settings_field(
			'asset_post_type_visibility',
			esc_html__( 'Visibility', 'digital-asset-manager' ),
			array( $this, 'myplugin_callback_field_radio' ),
			'dam-settings',
			'dam_asset_post_type_section',
			array(
				'id'   => 'asset_post_type_visibility',
				'desc' => esc_html__( 'Set the visibility for asset post type.', 'digital-asset-manager' ),
			)
		);

		add_settings_field(
			'asset_post_type_fields',
			esc_html__( 'Fields To Display', 'digital-asset-manager' ),
			array( $this, 'myplugin_callback_field_checkbox' ),
			'dam-settings',
			'dam_asset_post_type_section',
			array(
				'id'   => 'asset_post_type_fields',
				'desc' => esc_html__( 'Check which fields to display in front-end.', 'digital-asset-manager' ),
			)
		);

		// Asset Type taxonomy  Settings.
		add_settings_section(
			'dam_asset_type_taxonomy_section',
			esc_html__( 'Asset Type Taxonomy', 'digital-asset-manager' ),
			array( $this, 'dam_asset_type_taxonomy_section_description' ),
			'dam-settings'
		);

		add_settings_field(
			'asset_type_taxonomy_visibility',
			esc_html__( 'Visibility', 'digital-asset-manager' ),
			array( $this, 'myplugin_callback_field_radio' ),
			'dam-settings',
			'dam_asset_type_taxonomy_section',
			array(
				'id'   => 'asset_type_taxonomy_visibility',
				'desc' => esc_html__( 'Set the visibility for asset type taxonomy.', 'digital-asset-manager' ),
			)
		);

	}

	/**
	 * Asset Post Type Section Description.
	 *
	 * @return void
	 */
	public function dam_asset_post_type_section_description() {
		echo '<p>' . esc_html__( 'Configure settings for asset post type.', 'digital-asset-manager' ) . '</p>';
	}

	/**
	 * Asset Type Taxonomy Section Description.
	 *
	 * @return void
	 */
	public function dam_asset_type_taxonomy_section_description() {
		echo '<p>' . esc_html__( 'Configure settings for asset type taxonomy.', 'digital-asset-manager' ) . '</p>';
	}

	/**
	 * Display Input form field.
	 *
	 * @param array $args Arguments to use in displaying the Inout type text field.
	 * @return void
	 */
	public function myplugin_callback_field_text( $args ) {

		$id    = isset( $args['id'] ) ? $args['id'] : '';
		$label = isset( $args['label'] ) ? $args['label'] : '';

		$value = isset( $this->options[ $id ] ) ? sanitize_text_field( $this->options[ $id ] ) : '';

		echo '<input id="dam_settings_options_' . esc_attr( $id ) . '" name="dam_settings_options[' . esc_attr( $id ) . ']" type="text" size="100" value="' . esc_attr( $value ) . '"><br />';
		echo '<label for="dam_settings_options_' . esc_attr( $id ) . '">' . esc_html( $label ) . '</label>';

	}

	/**
	 * Display radio field.
	 *
	 * @param array $args Arguments to use in displaying the radio field.
	 * @return void
	 */
	public function myplugin_callback_field_radio( $args ) {

		$id   = isset( $args['id'] ) ? $args['id'] : '';
		$desc = isset( $args['desc'] ) ? $args['desc'] : '';

		$selected_option = isset( $this->options[ $id ] ) ? sanitize_text_field( $this->options[ $id ] ) : '';

		$radio_options = $this->dam_return_setting_fields_options( $id );

		foreach ( $radio_options as $value => $label ) {
			$checked = checked( $selected_option === $value, true, false );
			echo '<label><input name="dam_settings_options[' . esc_attr( $id ) . ']" type="radio" value="' . esc_attr( $value ) . '"' . esc_attr( $checked ) . '> ';
			echo '<span>' . esc_html( $label ) . '</span></label><br />';
		}

		echo '<p class="description">' . esc_html( $desc ) . '</p>';
	}

	/**
	 * Display CheckBoxes.
	 *
	 * @param array $args Arguments to use in displaying the checkboxes.
	 * @return void
	 */
	public function myplugin_callback_field_checkbox( $args ) {

		$id   = isset( $args['id'] ) ? $args['id'] : '';
		$desc = isset( $args['desc'] ) ? $args['desc'] : '';

		$checked_boxes = isset( $this->options[ $id ] ) ? $this->options[ $id ] : array();

		$checkbox_options = $this->dam_return_setting_fields_options( $id );

		foreach ( $checkbox_options as $value => $label ) {
			$checked = checked( array_key_exists( $value, $checked_boxes ), true, false );
			echo '<label><input name="dam_settings_options[' . esc_attr( $id ) . '][' . esc_attr( $value ) . ']" type="checkbox" value="' . esc_attr( $value ) . '"' . esc_attr( $checked ) . '> ';
			echo '<span>' . esc_html( $label ) . '</span></label><br />';
		}

		echo '<p class="description">' . esc_html( $desc ) . '</p>';
	}

	/**
	 * Validate the input fields before saving into the database in necessary.
	 *
	 * @param array $input Global post array.
	 * @return array
	 */
	public function dam_validate_options_callback( $input ) {
		return $input;
	}

	/**
	 * Default options for settings fields.
	 *
	 * @param string $key Field name whose options need to return.
	 * @return array List of options related to matched key.
	 */
	public function dam_return_setting_fields_options( $key ) {

		$options = array(
			'asset_post_type_capability'     => array(
				'post' => 'Post',
				'page' => 'Page',
			),
			'asset_post_type_visibility'     => array(
				'public'  => 'Public',
				'private' => 'Private',
			),
			'asset_post_type_fields'         => array(
				'title'                 => 'Title',
				'description'           => 'Description',
				'record_number'         => 'Record Number',
				'version_number'        => 'Version Number',
				'drive_url'             => 'Drive URL',
				'live_url'              => 'Live URL',
				'download_url'          => 'Download URL',
				'license_key'           => 'License Key',
				'account_credentials'   => 'Account Credentials',
				'private_note'          => 'Private Note',
				'featured_image'        => 'Featured Image',
				'associated_tokens'     => 'Associated Tokens',
				'associated_asset_type' => 'Associated Asset Type',
			),
			'asset_type_taxonomy_visibility' => array(
				'public'  => 'Public',
				'private' => 'Private',
			),
		);

		return $options[ $key ];
	}

}


