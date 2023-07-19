<?php defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WP_Sheet_Editor_ACF' ) ) {

	class WP_Sheet_Editor_ACF {

		private static $instance      = false;
		static $checkbox_keys         = array();
		static $map_keys              = array();
		var $gallery_field_keys       = array();
		var $repeater_keys            = array();
		var $group_keys               = array();
		var $excluded_serialized_keys = array();

		private function __construct() {

		}

		function init() {

			// exit if acf plugin is not active
			if ( ! $this->is_acf_plugin_active() ) {
				return;
			}

			add_action( 'vg_sheet_editor/editor/register_columns', array( $this, 'register_columns' ) );

			// Checkbox
			add_filter( 'vg_sheet_editor/infinite_serialized_column/column_settings', array( $this, 'filter_checkbox_column_settings' ), 20, 3 );
			add_filter( 'vg_sheet_editor/infinite_serialized_column/save_value_in_full_array', array( $this, 'filter_save_checkbox_from_serialized_class' ), 10, 9 );

			// Map
			add_filter( 'vg_sheet_editor/infinite_serialized_column/update_value', array( $this, 'filter_map_data_for_saving' ), 10, 3 );

			// Gallery
			add_filter( 'vg_sheet_editor/provider/post/update_item_meta', array( $this, 'filter_gallery_data_for_saving' ), 10, 3 );
			add_filter( 'vg_sheet_editor/provider/user/update_item_meta', array( $this, 'filter_gallery_data_for_saving' ), 10, 3 );

			// Save ACF field key
			add_action( 'vg_sheet_editor/save_rows/before_saving_cell', array( $this, 'save_acf_field_key' ), 10, 6 );
			add_action( 'vg_sheet_editor/formulas/execute_formula/after_sql_execution', array( $this, 'save_acf_field_key_after_sql_formula' ), 10, 5 );

			// Repeater fields
			add_filter( 'vg_sheet_editor/provider/post/update_item_meta', array( $this, 'sync_repeater_main_field_count' ), 10, 3 );
			add_filter( 'vg_sheet_editor/provider/user/update_item_meta', array( $this, 'sync_repeater_main_field_count' ), 10, 3 );

			// Group fields
			add_filter( 'vg_sheet_editor/provider/post/update_item_meta', array( $this, 'sync_group_field' ), 10, 3 );
			add_filter( 'vg_sheet_editor/provider/user/update_item_meta', array( $this, 'sync_group_field' ), 10, 3 );

			add_filter( 'vg_sheet_editor/serialized_addon/column_settings', array( $this, 'exclude_keys_from_serialized_columns' ), 10, 5 );
			add_filter( 'vg_sheet_editor/options_page/options', array( $this, 'add_settings_page_options' ) );
		}

		/**
		 * Add fields to options page
		 * @param array $sections
		 * @return array
		 */
		function add_settings_page_options( $sections ) {
			$sections['misc']['fields'][] = array(
				'id'    => 'acf_show_checkboxes_multi_dropdown',
				'type'  => 'switch',
				'title' => __( 'ACF: Show checkboxes as multi select dropdowns?', 'vg_sheet_editor' ),
				'desc'  => __( 'By default, we show every checkbox as a separate column, but if you have checkboxes with many options you might want to use one column with a dropdown instead.', 'vg_sheet_editor' ),
			);
			return $sections;
		}

		function sync_group_field( $value, $id, $key ) {
			if ( empty( $this->group_keys ) || strpos( $key, '_' ) === 0 ) {
				return $value;
			}

			$group_key = null;
			foreach ( $this->group_keys as $raw_repeater_key => $subfields ) {
				if ( in_array( $key, $subfields, true ) ) {
					$group_key = $raw_repeater_key;
					break;
				}
			}
			if ( empty( $group_key ) ) {
				return $value;
			}

			remove_filter( 'vg_sheet_editor/provider/post/update_item_meta', array( $this, 'sync_group_field' ), 10 );
			remove_filter( 'vg_sheet_editor/provider/user/update_item_meta', array( $this, 'sync_group_field' ), 10 );

			VGSE()->helpers->get_current_provider()->update_item_meta( $id, $group_key, '' );

			add_filter( 'vg_sheet_editor/provider/post/update_item_meta', array( $this, 'sync_group_field' ), 10, 3 );
			add_filter( 'vg_sheet_editor/provider/user/update_item_meta', array( $this, 'sync_group_field' ), 10, 3 );

			return $value;
		}

		function sync_repeater_main_field_count( $value, $id, $key ) {
			global $wpdb;

			if ( empty( $this->repeater_keys ) || strpos( $key, '_' ) === 0 ) {
				return $value;
			}

			$repeater_key = null;
			$regex        = null;
			foreach ( $this->repeater_keys as $raw_repeater_key => $subfields ) {
				foreach ( $subfields as $repeater_key_regex ) {
					if ( preg_match( $repeater_key_regex, $key ) ) {
						$repeater_key = $raw_repeater_key;
						$regex        = $repeater_key_regex;
						break;
					}
				}
				if ( $repeater_key ) {
					break;
				}
			}
			if ( empty( $repeater_key ) ) {
				return $value;
			}

			$mysql_regex          = str_replace( array( '/', '\d' ), array( '', '[0-9]' ), $regex );
			$meta_table_name      = VGSE()->helpers->get_current_provider()->get_meta_table_name();
			$meta_table_id_column = VGSE()->helpers->get_current_provider()->get_meta_table_post_id_key();
			$sql                  = "SELECT meta_key FROM $meta_table_name WHERE meta_key RLIKE %s AND " . esc_sql( $meta_table_id_column ) . ' = %d ORDER BY meta_key DESC LIMIT 1';
			$highest_key          = $wpdb->get_var( $wpdb->prepare( $sql, '^' . $mysql_regex, $id ) );

			if ( empty( $highest_key ) ) {
				$highest_key = $key;
			}

			$count_regex     = str_replace( '\d+', '(\d+)', $regex );
			$repeater_count  = (int) preg_replace( $count_regex, '$1', $highest_key );
			$key_index_count = (int) preg_replace( $count_regex, '$1', $key );
			if ( $repeater_count < $key_index_count ) {
				$repeater_count = $key_index_count;
			}

			// Subfields index starts from 0, but the parent count starts from 1
			$repeater_count++;

			remove_filter( 'vg_sheet_editor/provider/post/update_item_meta', array( $this, 'sync_repeater_main_field_count' ), 10 );
			remove_filter( 'vg_sheet_editor/provider/user/update_item_meta', array( $this, 'sync_repeater_main_field_count' ), 10 );

			VGSE()->helpers->get_current_provider()->update_item_meta( $id, $repeater_key, $repeater_count );

			add_filter( 'vg_sheet_editor/provider/post/update_item_meta', array( $this, 'sync_repeater_main_field_count' ), 10, 3 );
			add_filter( 'vg_sheet_editor/provider/user/update_item_meta', array( $this, 'sync_repeater_main_field_count' ), 10, 3 );

			return $value;
		}

		function filter_save_checkbox_from_serialized_class( $custom_saved, $final_array, $value, $post_id, $cell_key, $post_type, $column_settings, $spreadsheet_columns, $serialized_field ) {
			if ( empty( $column_settings['is_acf_checkbox'] ) ) {
				return $custom_saved;
			}

			$sample_field_key = $serialized_field->settings['sample_field_key'];

			// Allow to save field with the acf choice key, 1, yes, true, or check
			if ( in_array( $value, array( '1', 'yes', 'true', 'check', $column_settings['formatted']['checkedTemplate'] ), true ) ) {
				$value = $column_settings['formatted']['checkedTemplate'];
			} else {
				$value = '';
			}

			if ( empty( $final_array ) || ! is_array( $final_array ) ) {
				$final_array = array();
			}
			$final_array[] = $value;
			$final_array   = VGSE()->helpers->array_remove_empty( array_unique( VGSE()->helpers->array_flatten( $final_array ) ) );

			if ( empty( $value ) ) {
				$index = array_search( $column_settings['formatted']['checkedTemplate'], $final_array );
				if ( $index !== false ) {
					unset( $final_array[ $index ] );
				}
			}

			return $final_array;
		}

		function save_acf_field_key_after_sql_formula( $column, $formula, $post_type, $spreadsheet_columns, $post_ids ) {
			$column_settings = $spreadsheet_columns[ $column ];
			if ( empty( $column_settings['acf_field'] ) || empty( $column_settings['acf_field']['key'] ) ) {
				return;
			}
			$column_settings['key_for_formulas'] = '_' . $column_settings['key_for_formulas'];
			$formula                             = '=REPLACE(""$current_value$"",""' . $column_settings['acf_field']['key'] . '"")';
			WP_Sheet_Editor_Formulas::get_instance()->execute_formula_as_sql( $post_ids, $formula, $column_settings, $post_type );

			if ( ! empty( $column_settings['acf_field']['parent'] ) ) {
				$column_settings['key_for_formulas'] = '_' . $column_settings['acf_field']['parent']['name'];
				$formula                             = '=REPLACE(""$current_value$"",""' . $column_settings['acf_field']['parent']['key'] . '"")';
				WP_Sheet_Editor_Formulas::get_instance()->execute_formula_as_sql( $post_ids, $formula, $column_settings, $post_type );
			}
		}

		function save_acf_field_key( $item, $post_type, $column_settings, $key, $spreadsheet_columns, $post_id ) {
			if ( empty( $column_settings['acf_field'] ) || empty( $column_settings['acf_field']['key'] ) ) {
				return;
			}

			if ( ! empty( $column_settings['acf_field'] ) ) {
				$real_key = $column_settings['acf_field']['name'];
			} else {
				$real_key = preg_replace( '/_\d+_i_\d+$/', '', $key );
			}
			VGSE()->helpers->get_current_provider()->update_item_meta( $post_id, '_' . $real_key, $column_settings['acf_field']['key'] );

			if ( ! empty( $column_settings['acf_field']['parent'] ) && is_array( $column_settings['acf_field']['parent'] ) ) {
				VGSE()->helpers->get_current_provider()->update_item_meta( $post_id, '_' . $column_settings['acf_field']['parent']['name'], $column_settings['acf_field']['parent']['key'] );
			}
		}

		function exclude_keys_from_serialized_columns( $column_settings, $first_set_keys, $field, $key, $post_type ) {
			if ( ! isset( $this->excluded_serialized_keys[ $post_type ] ) ) {
				return $column_settings;
			}
			foreach ( $this->excluded_serialized_keys[ $post_type ] as $field_key ) {
				if ( ! empty( $column_settings['serialized_field_original_key'] ) && $column_settings['serialized_field_original_key'] === $field_key ) {
					$column_settings = array();
				}
			}

			return $column_settings;
		}

		function filter_map_data_for_saving( $new_value, $id, $real_key ) {
			if ( ! isset( self::$map_keys[ $real_key ] ) ) {
				return $new_value;
			}

			if ( empty( $new_value['address'] ) ) {
				$new_value['lat'] = '';
				$new_value['lng'] = '';
			} else {
				$google_maps_api_key = acf_get_setting( 'google_api_key' );

				if ( empty( $google_maps_api_key ) ) {
					throw new Exception( __( 'You need to configure your Google Maps API key to save the Google Maps columns. This is required by Advanced Custom Fields, <a href="https://www.advancedcustomfields.com/blog/google-maps-api-settings/" target="_blank">you can follow this tutorial</a>', 'vg_sheet_editor' ), E_USER_ERROR );
				}

				$geo_response = wp_remote_get( 'https://maps.googleapis.com/maps/api/geocode/json?key=' . $google_maps_api_key . '&language=en&address=' . urlencode( $new_value['address'] ) . '&sensor=false' );
				$geo_json     = wp_remote_retrieve_body( $geo_response );

				$geo = json_decode( $geo_json, true );
				if ( $geo['status'] === 'OK' ) {
					$new_value['lat'] = $geo['results'][0]['geometry']['location']['lat'];
					$new_value['lng'] = $geo['results'][0]['geometry']['location']['lng'];
				}
			}

			return $new_value;
		}

		function prepare_gallery_value_for_display( $value, $post, $key, $column_settings ) {
			$post_type = VGSE()->helpers->get_provider_from_query_string();
			if ( ! isset( $this->gallery_field_keys[ $post_type ] ) || ! in_array( $key, $this->gallery_field_keys[ $post_type ] ) ) {
				return $value;
			}

			if ( ! empty( $value ) && is_array( $value ) ) {
				$value = implode( ',', $value );
			}

			return $value;
		}

		function prepare_checkbox_value_for_display( $value, $post, $key, $column_settings ) {
			$real_key = preg_replace( '/_\d+$/', '', $key );
			if ( $key === $real_key || ! isset( self::$checkbox_keys[ $real_key ] ) ) {
				return $value;
			}
			$post_id = $post->ID;

			$raw_value = VGSE()->helpers->get_current_provider()->get_item_meta( $post_id, $real_key, true, 'read' );
			if ( empty( $raw_value ) || ! is_array( $raw_value ) ) {
				return $value;
			}
			$index           = (int) preg_replace( '/^.+_(\d+)$/', '$1', $key );
			$accepted_values = array_keys( self::$checkbox_keys[ $real_key ]['choices'] );
			$expected_value  = $accepted_values[ $index ];

			$value = ( in_array( $expected_value, $raw_value ) ) ? $expected_value : '';
			return $value;
		}

		function filter_gallery_data_for_saving( $value, $id, $key ) {
			$post_type = VGSE()->helpers->get_provider_from_query_string();
			if ( ! isset( $this->gallery_field_keys[ $post_type ] ) || ! in_array( $key, $this->gallery_field_keys[ $post_type ] ) ) {
				return $value;
			}

			if ( ! empty( $value ) && is_string( $value ) ) {
				$value = explode( ',', $value );
			}

			return $value;
		}

		function filter_checkbox_column_settings( $column_settings, $serialized_field, $post_type ) {
			// If this serialized field is not an acf checkbox but uses a key known as
			// acf checkbox, return empty to not register the column
			$settings = $serialized_field->settings;
			if ( empty( $settings['is_acf_checkbox'] ) && in_array( $settings['sample_field_key'], array_keys( self::$checkbox_keys ) ) ) {
				return array();
			}

			if ( empty( $settings['is_acf_checkbox'] ) ) {
				return $column_settings;
			}

			$key_parts   = explode( '_', $column_settings['key'] );
			$field_index = (int) end( $key_parts );

			$choices_values                                    = array_keys( $settings['acf_choices'] );
			$column_settings['formatted']['type']              = 'checkbox';
			$column_settings['formatted']['checkedTemplate']   = $choices_values[ $field_index ];
			$column_settings['formatted']['uncheckedTemplate'] = '';
			$column_settings['formatted']['default_value']     = isset( $column_settings['default_value'] ) ? $column_settings['default_value'] : '';
			$column_settings['title']                          = $settings['column_title_prefix'] . ': ' . $settings['acf_choices'][ $choices_values[ $field_index ] ];

			// We ignore the default value set in ACF because it causes issues.
			// If we show the checkbox with the default value (i.e. checked), it will ignore it as checked when saving
			// because it would have the same value as initially loaded
			$column_settings['default_value']   = '';
			$column_settings['is_acf_checkbox'] = true;

			return $column_settings;
		}

		/**
		 * Get fields registered in Advanced Custom Fields for a specific post type
		 * @param str $post_type
		 * @return boolean|array
		 */
		function get_acf_fields_objects_by_post_type( $post_type, $editor ) {
			// get field groups
			if ( $editor->provider->key === 'user' ) {
				$filter = array(
					'user_form' => 'edit',
				);
			} else {
				$filter = array();
			}
			// get field groups
			$acfs   = acf_get_field_groups( $filter );
			$fields = array();

			if ( $acfs ) {
				foreach ( $acfs as $acf ) {
					if ( empty( $acf['location'] ) ) {
						continue;
					}
					if ( empty( $acf['active'] ) ) {
						continue;
					}
					$post_type_fields = false;
					$location         = serialize( $acf['location'] );
					if ( $editor->provider->is_post_type ) {
						if ( $post_type === 'post' && preg_match( '/"(category|post_tag):/', $location ) ) {
							$post_type_fields = true;
						} elseif ( strpos( $location, '"post_type"' ) !== false && strpos( $location, '"' . $post_type . '"' ) !== false ) {
							$post_type_fields = true;
						} else {
							$post_type_fields = array_merge(
								wp_list_filter(
									$acf['location'][0],
									array(
										'param'    => 'post_type',
										'operator' => '==',
										'value'    => $post_type,
									)
								),
								wp_list_filter(
									$acf['location'][0],
									array(
										'param'    => 'post_type',
										'operator' => '==',
										'value'    => 'all',
									)
								)
							);
						}
					} elseif ( $editor->provider->key === 'term' ) {
						$post_type_fields = array_merge(
							wp_list_filter(
								$acf['location'][0],
								array(
									'param'    => 'taxonomy',
									'operator' => '==',
									'value'    => $post_type,
								)
							),
							wp_list_filter(
								$acf['location'][0],
								array(
									'param'    => 'taxonomy',
									'operator' => '==',
									'value'    => 'all',
								)
							)
						);
					} else {
						$post_type_fields = true;
					}

					if ( ! empty( $post_type_fields ) ) {
						$fields[] = acf_get_fields( $acf );
					}
				}
			}

			return apply_filters( 'vg_sheet_editor/acf/fields', $fields, $post_type, $acfs );
		}

		/**
		 * Is acf plugin active
		 * @return boolean
		 */
		function is_acf_plugin_active() {
			return function_exists( 'acf_get_field_groups' ) || class_exists( 'ACF' );
		}

		/**
		 * Creates or returns an instance of this class.
		 */
		static function get_instance() {
			if ( null == self::$instance ) {
				self::$instance = new WP_Sheet_Editor_ACF();
				self::$instance->init();
			}
			return self::$instance;
		}

		function __set( $name, $value ) {
			$this->$name = $value;
		}

		function __get( $name ) {
			return $this->$name;
		}

		/**
		 * Register columns in the spreadsheet
		 * @return null
		 */
		function register_columns( $editor ) {

			if ( $editor->provider->key === 'user' ) {
				$post_types = array(
					'user',
				);
			} else {
				$post_types = $editor->args['enabled_post_types'];
			}

			if ( empty( $post_types ) ) {
				return;
			}

			$columns = array();
			foreach ( $post_types as $post_type ) {
				if ( empty( $post_type ) ) {
					continue;
				}
				$acf_post_type_groups = $this->get_acf_fields_objects_by_post_type( $post_type, $editor );
				if ( empty( $acf_post_type_groups ) ) {
					continue;
				}

				if ( ! isset( $this->gallery_field_keys[ $post_type ] ) ) {
					$this->gallery_field_keys[ $post_type ] = array();
				}
				if ( ! isset( $this->excluded_serialized_keys[ $post_type ] ) ) {
					$this->excluded_serialized_keys[ $post_type ] = array();
				}
				foreach ( $acf_post_type_groups as $acf_group_index => $acf_group ) {
					if ( empty( $acf_group ) ) {
						continue;
					}
					$columns = array_merge( $columns, $this->_acf_fields_to_columns_args( $acf_group, $post_type, $editor ) );
				}
			}

			$this->_register_columns( $columns, $editor );
		}

		function get_taxonomy_cell( $post, $cell_key, $cell_args ) {
			$terms    = VGSE()->helpers->get_current_provider()->get_item_meta( $post->ID, $cell_key, true );
			$taxonomy = $cell_args['acf_field']['taxonomy'];
			$out      = '';
			if ( $terms ) {
				$out = VGSE()->data_helpers->prepare_post_terms_for_display(
					get_terms(
						array(
							'taxonomy'               => $taxonomy,
							'include'                => $terms,
							'update_term_meta_cache' => false,
							'hide_empty'             => false,
						)
					)
				);
			}
			return html_entity_decode( $out );
		}

		function update_taxonomy_cell( $post_id, $cell_key, $data_to_save, $post_type, $cell_args, $spreadsheet_columns ) {
			$data_to_save = trim( $data_to_save );
			$taxonomy     = $cell_args['acf_field']['taxonomy'];
			if ( empty( $data_to_save ) ) {
				$terms_saved = '';
			} else {
				$terms_saved = VGSE()->data_helpers->prepare_post_terms_for_saving( $data_to_save, $taxonomy );
			}
			VGSE()->helpers->get_current_provider()->update_item_meta( $post_id, $cell_key, $terms_saved );

			if ( $cell_args['acf_field']['save_terms'] ) {
				wp_set_object_terms( $post_id, $terms_saved, $taxonomy );
			}
		}

		function _prepare_date_for_display( $value, $post, $cell_key, $cell_args ) {
			if ( ! empty( $value ) ) {
				$timestamp = strtotime( $value );
				$value     = date( $cell_args['acf_field']['display_format'], $timestamp );
			}
			return $value;
		}

		function _prepare_date_for_database( $post_id, $cell_key, $data_to_save, $post_type, $cell_args, $spreadsheet_columns ) {
			if ( ! empty( $data_to_save ) ) {
				$date = DateTime::createFromFormat( $cell_args['acf_field']['display_format'], $data_to_save );
				if ( $date ) {
					$data_to_save = $date->format( 'Ymd' );
				} else {
					$data_to_save = date( 'Ymd', strtotime( $data_to_save ) );
				}
			}
			return $data_to_save;
		}

		function _acf_fields_to_columns_args( $acf_group, $post_type, $editor ) {
			$column_defaults = array(
				'name'               => '',
				'key'                => '',
				'data_source'        => 'meta_data',
				'post_types'         => 'post',
				'read_only'          => 'no',
				'allow_formulas'     => 'yes',
				'allow_hide'         => 'yes',
				'allow_rename'       => 'yes',
				'plain_renderer'     => 'text',
				'formatted_renderer' => 'text',
				'width'              => '150',
				'cell_type'          => '',
			);
			$columns         = array();
			foreach ( $acf_group as $acf_field_index => $acf_field ) {
				// We don't register the text fields and unsupported fields because
				// they will appear automatically. The custom columns module registers
				// all custom fields as plain text. We only register fields with special format here.

				if ( in_array( $acf_field['type'], array( 'image', 'file' ) ) ) {
					$columns[] = wp_parse_args(
						array(
							'name'       => $acf_field['label'],
							'key'        => $acf_field['name'],
							'post_types' => $post_type,
							'cell_type'  => 'boton_gallery',
							'acf_field'  => $acf_field,
						),
						$column_defaults
					);
				} elseif ( $acf_field['type'] === 'date_picker' ) {
					$editor->args['columns']->register_item(
						$acf_field['name'],
						$post_type,
						array(
							'data_type'                  => 'meta_data',
							'column_width'               => 150,
							'title'                      => $acf_field['label'],
							'type'                       => '',
							'supports_formulas'          => true,
							'supports_sql_formulas'      => false,
							'allow_to_hide'              => true,
							'allow_to_rename'            => true,
							'allow_plain_text'           => true,
							'acf_field'                  => $acf_field,
							'formatted'                  => array(
								'type'                 => 'date',
								'customDatabaseFormat' => 'Ymd',
								'dateFormatPhp'        => $acf_field['display_format'],
								'correctFormat'        => true,
								'defaultDate'          => '',
								'datePickerConfig'     => array(
									'firstDay'       => 0,
									'showWeekNumber' => true,
									'numberOfMonths' => 1,
									'yearRange'      => array( 1900, (int) date( 'Y' ) + 20 ),
								),
							),
							'prepare_value_for_database' => array( $this, '_prepare_date_for_database' ),
							'prepare_value_for_display'  => array( $this, '_prepare_date_for_display' ),
						)
					);
				} elseif ( in_array( $acf_field['type'], array( 'text', 'textarea', 'number', 'email', 'url', 'password', 'oembed' ) ) ) {
					$value_type = 'text';
					if ( $acf_field['type'] === 'email' ) {
						$value_type = 'email';
					}
					$columns[] = wp_parse_args(
						array(
							'acf_field'          => $acf_field,
							'name'               => $acf_field['label'],
							'key'                => $acf_field['name'],
							'post_types'         => $post_type,
							'plain_renderer'     => 'text',
							'formatted_renderer' => 'text',
							'value_type'         => $value_type,
						),
						$column_defaults
					);
				} elseif ( in_array( $acf_field['type'], array( 'relationship' ) ) ) {
					$this->excluded_serialized_keys[ $post_type ][] = $acf_field['name'];
					$editor->args['columns']->register_item(
						$acf_field['name'],
						$post_type,
						array(
							'data_type'                 => 'meta_data',
							'column_width'              => 200,
							'title'                     => $acf_field['label'],
							'type'                      => '',
							'supports_formulas'         => true,
							'supports_sql_formulas'     => false,
							'allow_to_hide'             => true,
							'allow_to_rename'           => true,
							'allow_plain_text'          => true,
							'prepare_value_for_display' => array( $this, 'prepare_relationship_for_display' ),
							'save_value_callback'       => array( $this, 'update_relationship_for_cell' ),
							'acf_field'                 => $acf_field,
							'list_separation_character' => ',',
						)
					);
				} elseif ( in_array( $acf_field['type'], array( 'wysiwyg' ) ) ) {
					$columns[] = wp_parse_args(
						array(
							'acf_field'  => $acf_field,
							'name'       => $acf_field['label'],
							'key'        => $acf_field['name'],
							'post_types' => $post_type,
							'cell_type'  => 'boton_tiny',
						),
						$column_defaults
					);
				} elseif ( in_array( $acf_field['type'], array( 'radio' ) ) || ( $acf_field['type'] === 'select' && ! $acf_field['multiple'] ) ) {
					$columns[] = wp_parse_args(
						array(
							'acf_field'          => $acf_field,
							'name'               => $acf_field['label'],
							'key'                => $acf_field['name'],
							'post_types'         => $post_type,
							'plain_renderer'     => 'text',
							'formatted_renderer' => 'select',
							'selectOptions'      => $acf_field['choices'],
							'default_value'      => $acf_field['default_value'],
						),
						$column_defaults
					);
				} elseif ( $acf_field['type'] === 'taxonomy' ) {
					$this->excluded_serialized_keys[ $post_type ][] = $acf_field['name'];
					$editor->args['columns']->register_item(
						$acf_field['name'],
						$post_type,
						array(
							'data_type'                 => 'meta_data',
							'column_width'              => 200,
							'title'                     => $acf_field['label'],
							'type'                      => '',
							'supports_formulas'         => true,
							'supports_sql_formulas'     => false,
							'allow_to_hide'             => true,
							'allow_to_rename'           => true,
							'allow_plain_text'          => true,
							'get_value_callback'        => array( $this, 'get_taxonomy_cell' ),
							'save_value_callback'       => array( $this, 'update_taxonomy_cell' ),
							'acf_field'                 => $acf_field,
							'list_separation_character' => ',',
						)
					);
				} elseif ( $acf_field['type'] === 'select' && $acf_field['multiple'] ) {
					$this->excluded_serialized_keys[ $post_type ][] = $acf_field['name'];
					$editor->args['columns']->register_item(
						$acf_field['name'],
						$post_type,
						array(
							'data_type'                 => 'meta_data',
							'column_width'              => 200,
							'title'                     => $acf_field['label'],
							'type'                      => '',
							'supports_formulas'         => true,
							'supports_sql_formulas'     => false,
							'allow_to_hide'             => true,
							'allow_to_rename'           => true,
							'allow_plain_text'          => true,
							'prepare_value_for_display' => array( $this, 'prepare_multi_select_for_display' ),
							'save_value_callback'       => array( $this, 'update_multi_select_for_cell' ),
							'acf_field'                 => $acf_field,
							'list_separation_character' => ',',
						)
					);
				} elseif ( in_array( $acf_field['type'], array( 'user' ) ) ) {
					$editor->args['columns']->register_item(
						$acf_field['name'],
						$post_type,
						array(
							'data_type'                  => 'meta_data',
							'column_width'               => 200,
							'title'                      => $acf_field['label'],
							'type'                       => '',
							'supports_formulas'          => true,
							'supports_sql_formulas'      => false,
							'allow_to_hide'              => true,
							'allow_to_rename'            => true,
							'allow_plain_text'           => true,
							'formatted'                  => array(
								'type'   => 'autocomplete',
								'source' => 'searchUsers',
							),
							'prepare_value_for_display'  => array( $this, '_prepare_user_for_display' ),
							'prepare_value_for_database' => array( $this, '_prepare_user_for_database' ),
							'acf_field'                  => $acf_field,
						)
					);
				} elseif ( in_array( $acf_field['type'], array( 'page_link' ) ) ) {
					$acf_field_post_type = null;
					if ( ! empty( $acf_field['post_type'] ) ) {
						$acf_field_post_type = is_array( $acf_field['post_type'] ) ? current( $acf_field['post_type'] ) : $acf_field['post_type'];
					}
					$editor->args['columns']->register_item(
						$acf_field['name'],
						$post_type,
						array(
							'data_type'                  => 'meta_data',
							'column_width'               => 200,
							'title'                      => $acf_field['label'],
							'type'                       => '',
							'supports_formulas'          => true,
							'supports_sql_formulas'      => false,
							'allow_to_hide'              => true,
							'allow_to_rename'            => true,
							'allow_plain_text'           => true,
							'formatted'                  => array(
								'type'           => 'autocomplete',
								'source'         => 'searchPostByKeyword',
								'searchPostType' => $acf_field_post_type,
								'comment'        => array( 'value' => __( 'Enter a title', 'vg_sheet_editor' ) ),
							),
							'prepare_value_for_display'  => array( $this, '_prepare_posts_for_display' ),
							'prepare_value_for_database' => array( $this, '_prepare_posts_for_database' ),
							'acf_field'                  => $acf_field,
						)
					);
				} elseif ( in_array( $acf_field['type'], array( 'true_false' ) ) ) {
					$columns[] = wp_parse_args(
						array(
							'acf_field'          => $acf_field,
							'name'               => $acf_field['label'],
							'key'                => $acf_field['name'],
							'post_types'         => $post_type,
							'plain_renderer'     => 'text',
							'formatted_renderer' => 'checkbox',
							'checkedTemplate'    => 1,
							'uncheckedTemplate'  => 0,
							'default_value'      => 0,
						),
						$column_defaults
					);
				} elseif ( in_array( $acf_field['type'], array( 'gallery' ) ) ) {
					$this->gallery_field_keys[ $post_type ][]       = $acf_field['name'];
					$this->excluded_serialized_keys[ $post_type ][] = $acf_field['name'];

					$columns[] = wp_parse_args(
						array(
							'name'                      => $acf_field['label'],
							'key'                       => $acf_field['name'],
							'post_types'                => $post_type,
							'cell_type'                 => 'boton_gallery_multiple',
							'acf_field'                 => $acf_field,
							'prepare_value_for_display' => array( $this, 'prepare_gallery_value_for_display' ),
						),
						$column_defaults
					);
				} elseif ( in_array( $acf_field['type'], array( 'checkbox' ) ) && empty( VGSE()->options['acf_show_checkboxes_multi_dropdown'] ) ) {
					$sample_field = array();
					$choice_index = 0;
					foreach ( $acf_field['choices'] as $choice_key => $choice_label ) {
						$sample_field[] = ( is_array( $acf_field['default_value'] ) && isset( $acf_field['default_value'][ $choice_index ] ) ) ? $acf_field['default_value'][ $choice_index ] : '';
						$choice_index++;
					}

					new WP_Sheet_Editor_Infinite_Serialized_Field(
						array(
							'sample_field_key'    => $acf_field['name'],
							'sample_field'        => $sample_field,
							'column_width'        => 150,
							'column_title_prefix' => $acf_field['label'], // to remove the field key from the column title
							'level'               => 1,
							'allowed_post_types'  => array( $post_type ),
							'is_single_level'     => true,
							'allow_in_wc_product_variations' => false,
							'is_acf_checkbox'     => true,
							'acf_choices'         => $acf_field['choices'],
							'column_settings'     => array(
								'acf_field' => $acf_field,
								'prepare_value_for_display' => array( $this, 'prepare_checkbox_value_for_display' ),
							),
						)
					);
					self::$checkbox_keys[ $acf_field['name'] ]      = $acf_field;
					$this->excluded_serialized_keys[ $post_type ][] = $acf_field['name'];
				} elseif ( $acf_field['type'] === 'checkbox' && ! empty( VGSE()->options['acf_show_checkboxes_multi_dropdown'] ) ) {

					$select_options = array();
					foreach ( $acf_field['choices'] as $choice_key => $choice_label ) {
						$select_options[] = array(
							'id'    => $choice_key,
							'label' => $choice_label,
						);
					}
					$editor->args['columns']->register_item(
						$acf_field['name'],
						$post_type,
						array(
							'data_type'                 => 'meta_data',
							'column_width'              => 200,
							'title'                     => $acf_field['label'],
							'type'                      => '',
							'supports_formulas'         => true,
							'supports_sql_formulas'     => false,
							'allow_to_hide'             => true,
							'allow_to_rename'           => true,
							'allow_plain_text'          => true,
							'formatted'                 => array(
								'editor'        => 'chosen',
								'width'         => 150,
								'source'        => $select_options,
								'chosenOptions' => array(
									'multiple'        => true,
									'search_contains' => true,
									//                      'skip_no_results' => true,
																						'data' => $select_options,
								),
							),
							'prepare_value_for_display' => array( $this, 'prepare_multi_select_for_display' ),
							'save_value_callback'       => array( $this, 'update_multi_select_for_cell' ),
							'acf_field'                 => $acf_field,
						)
					);
				} elseif ( in_array( $acf_field['type'], array( 'google_map' ) ) ) {
					new WP_Sheet_Editor_Infinite_Serialized_Field(
						array(
							'sample_field_key'    => $acf_field['name'],
							'sample_field'        => array(
								'address' => '',
								'lat'     => '',
								'lng'     => '',
							),
							'column_width'        => 150,
							'column_title_prefix' => $acf_field['label'], // to remove the field key from the column title
							'level'               => 1,
							'allowed_post_types'  => array( $post_type ),
							'is_single_level'     => true,
							'allow_in_wc_product_variations' => false,
							'is_acf_map'          => true,
							'column_settings'     => array(
								'acf_field' => $acf_field,
							),
						)
					);
					self::$map_keys[ $acf_field['name'] ] = $acf_field;
				} elseif ( in_array( $acf_field['type'], array( 'repeater' ) ) && class_exists( 'acf_pro' ) ) {
					$this->repeater_keys[ $acf_field['name'] ] = array();

					// The parent repeater is not editable, it's used internally to keep count of internal rows
					$editor->args['columns']->remove_item( $acf_field['name'], $post_type );

					$repeater_count_values = $this->_get_repeater_count_values( $acf_field['name'], $post_type, $editor );

					$highest_count = ( empty( $repeater_count_values ) || empty( $repeater_count_values[0] ) ) ? 3 : (int) $repeater_count_values[0];

					// Save the subfield keys for processing the values during saving/reading
					foreach ( $acf_field['sub_fields'] as $subfield ) {
						$this->repeater_keys[ $acf_field['name'] ][] = '/' . $acf_field['name'] . '_\d+_' . $subfield['name'] . '$/';
					}

					// Register columns for each subfield
					for ( $i = 0; $i < $highest_count; $i++ ) {
						$repeater_field_group = array();
						foreach ( $acf_field['sub_fields'] as $subfield ) {
							$subfield['parent']     = array(
								'name'  => $acf_field['name'],
								'label' => $acf_field['label'],
								'key'   => $acf_field['key'],
							);
							$subfield['name']       = $acf_field['name'] . '_' . $i . '_' . $subfield['name'];
							$subfield['label']      = implode( ' : ', array( $acf_field['label'], $i + 1, $subfield['label'] ) );
							$repeater_field_group[] = $subfield;
						}
						$repeater_columns = $this->_acf_fields_to_columns_args( $repeater_field_group, $post_type, $editor );
						$columns          = array_merge( $columns, $repeater_columns );
					}
				} elseif ( in_array( $acf_field['type'], array( 'group' ) ) ) {
					$this->group_keys[ $acf_field['name'] ] = array();

					// The parent repeater is not editable, it's used internally to keep count of internal rows
					$editor->args['columns']->remove_item( $acf_field['name'], $post_type );

					// Save the subfield keys for processing the values during saving/reading
					foreach ( $acf_field['sub_fields'] as $subfield ) {
						$this->group_keys[ $acf_field['name'] ][] = $acf_field['name'] . '_' . $subfield['name'];
					}

					// Register columns for each subfield
					$field_group = array();
					foreach ( $acf_field['sub_fields'] as $subfield ) {
						$subfield['parent'] = array(
							'name'  => $acf_field['name'],
							'label' => $acf_field['label'],
							'key'   => $acf_field['key'],
						);
						$subfield['name']   = $acf_field['name'] . '_' . $subfield['name'];
						$subfield['label']  = implode( ' : ', array( $acf_field['label'], $subfield['label'] ) );
						$field_group[]      = $subfield;
					}
					$group_columns = $this->_acf_fields_to_columns_args( $field_group, $post_type, $editor );
					$columns       = array_merge( $columns, $group_columns );
				}
			}
			return $columns;
		}

		function _prepare_user_for_database( $post_id, $cell_key, $data_to_save, $post_type, $column_settings, $spreadsheet_columns ) {
			if ( empty( $data_to_save ) ) {
				return $data_to_save;
			}
			$out = '';
			if ( empty( $column_settings['acf_field']['multiple'] ) ) {
				$user = get_user_by( 'login', $data_to_save );
				$out  = $user ? $user->ID : '';
			} else {
				$user_logins = array_filter( explode( ',', $data_to_save ) );
				$out         = array();
				foreach ( $user_logins as $user_login ) {
					$user = get_user_by( 'login', $user_login );
					if ( $user ) {
						$out[] = $user->ID;
					}
				}
			}
			return $out;
		}

		function _prepare_user_for_display( $value, $post, $column_key, $column_settings ) {
			if ( empty( $value ) ) {
				return '';
			}

			$out = '';
			if ( empty( $column_settings['acf_field']['multiple'] ) ) {
				$user = get_user_by( 'ID', $value );
				$out  = $user ? $user->user_login : '';
			} elseif ( is_array( $value ) ) {
				$user_logins = array();
				foreach ( $value as $user_id ) {
					$user = get_user_by( 'ID', $user_id );
					if ( $user ) {
						$user_logins[] = $user->user_login;
					}
				}
				$out = implode( ', ', $user_logins );
			}
			return $out;
		}

		function _prepare_posts_for_database( $post_id, $cell_key, $data_to_save, $post_type, $cell_args, $spreadsheet_columns ) {
			$out = '';
			if ( ! empty( $data_to_save ) && ! is_numeric( $data_to_save ) ) {
				$out = VGSE()->data_helpers->get_post_id_from_title( $data_to_save, $cell_args['formatted']['searchPostType'] );
			}
			return $out;
		}

		function _prepare_posts_for_display( $value, $post, $column_key, $column_settings ) {
			$out = '';
			if ( ! empty( $value ) && is_numeric( $value ) ) {
				$out = html_entity_decode( get_the_title( (int) $value ) );
			}
			return $out;
		}

		function prepare_relationship_for_display( $value, $post, $column_key, $column_settings ) {
			global $wpdb;
			$titles = '';
			if ( is_array( $value ) && ! empty( $value ) ) {
				$ids_in_query_placeholders = implode( ', ', array_fill( 0, count( $value ), '%d' ) );
				$raw_titles                = array_unique( $wpdb->get_col( $wpdb->prepare( "SELECT post_title FROM $wpdb->posts WHERE ID IN ($ids_in_query_placeholders)  ORDER BY FIELD(ID, $ids_in_query_placeholders) ", array_merge( $value, $value ) ) ) );
				$titles                    = implode( ', ', $raw_titles );
			}
			return $titles;
		}

		function update_relationship_for_cell( $post_id, $cell_key, $data_to_save, $post_type, $cell_args, $spreadsheet_columns ) {
			global $wpdb;
			$titles = array_map( 'trim', explode( ',', $data_to_save ) );
			$ids    = '';
			if ( ! empty( $titles ) ) {
				$titles_in_query_placeholders = implode( ', ', array_fill( 0, count( $titles ), '%s' ) );
				$sql                          = "SELECT ID FROM $wpdb->posts WHERE post_title IN ($titles_in_query_placeholders) ";
				if ( ! empty( $cell_args['acf_field']['post_type'] ) ) {
					$post_types_in_query_placeholders = implode( ', ', array_fill( 0, count( $cell_args['acf_field']['post_type'] ), '%s' ) );
					$sql                             .= " AND post_type IN ($post_types_in_query_placeholders) ";
					$merged_variables                 = array_merge( $titles, $cell_args['acf_field']['post_type'], $titles );
				} else {
					$merged_variables = array_merge( $titles, $titles );
				}
				$sql         .= " ORDER BY FIELD(post_title, $titles_in_query_placeholders) ";
				$prepared_sql = $wpdb->prepare( $sql, $merged_variables );
				$ids          = array_unique( $wpdb->get_col( $prepared_sql ) );
			}
			VGSE()->helpers->get_current_provider()->update_item_meta( $post_id, $cell_key, $ids );
		}

		function prepare_multi_select_for_display( $value, $post, $column_key, $column_settings ) {
			$titles = '';
			if ( is_array( $value ) && ! empty( $value ) ) {
				$raw_titles = array();
				foreach ( $value as $key ) {
					if ( ! empty( $column_settings['acf_field']['choices'][ $key ] ) ) {
						$raw_titles[] = $column_settings['acf_field']['choices'][ $key ];
					}
				}

				$titles = implode( ', ', $raw_titles );
			}
			return $titles;
		}

		function update_multi_select_for_cell( $post_id, $cell_key, $data_to_save, $post_type, $column_settings, $spreadsheet_columns ) {
			$titles = array_map( 'trim', explode( ',', $data_to_save ) );
			$ids    = '';
			if ( ! empty( $titles ) ) {
				$ids = array();
				foreach ( $titles as $title ) {
					$key = array_search( $title, $column_settings['acf_field']['choices'] );
					if ( isset( $column_settings['acf_field']['choices'][ $title ] ) ) {
						$ids[] = $title;
					} elseif ( $key !== false ) {
						$ids[] = $key;
					} else {
						continue;
					}
				}
				$ids = array_unique( $ids );
			}
			if ( empty( $ids ) ) {
				$ids = '';
			}
			VGSE()->helpers->get_current_provider()->update_item_meta( $post_id, $cell_key, $ids );
		}

		function _get_repeater_count_values( $key, $post_type, $editor ) {
			$cache_key             = 'vgse_acf_repeater_values' . $key . $post_type;
			$repeater_count_values = get_transient( $cache_key );
			if ( method_exists( VGSE()->helpers, 'can_rescan_db_fields' ) && VGSE()->helpers->can_rescan_db_fields( $post_type ) ) {
				$repeater_count_values = false;
			}

			if ( ! $repeater_count_values ) {
				$repeater_count_values = array_filter( array_map( 'maybe_unserialize', $editor->provider->get_meta_field_unique_values( $key, $post_type ) ) );
				set_transient( $cache_key, $repeater_count_values, DAY_IN_SECONDS );
			}
			return $repeater_count_values;
		}

		/**
		 * Helper: Convert the advanced custom fields, fields objects to the structure
		 * required by the WP Sheet Editor Columns API.
		 * @param array $columns
		 * @return null
		 */
		function _register_columns( $columns, $editor ) {

			if ( empty( $columns ) ) {
				return;
			}

			foreach ( $columns as $column_index => $column_settings ) {

				if ( ! is_array( $column_settings['post_types'] ) ) {
					$column_settings['post_types'] = array( $column_settings['post_types'] );
				}
				foreach ( $column_settings['post_types'] as $post_type ) {
					if ( ! empty( $column_settings['cell_type'] ) ) {
						$column_settings['read_only']          = true;
						$column_settings['plain_renderer']     = 'html';
						$column_settings['formatted_renderer'] = 'html';
					}

					if ( ( $column_settings['cell_type'] === 'boton_gallery' || $column_settings['cell_type'] === 'boton_gallery_multiple' ) && $column_settings['width'] < 280 ) {
						$column_settings['width'] = 300;
					}
					if ( $column_settings['data_source'] === 'post_terms' ) {
						if ( ! in_array( $column_settings['formatted_renderer'], array( 'text', 'taxonomy_dropdown' ) ) ) {
							$column_settings['formatted_renderer'] = 'text';
						} elseif ( ! in_array( $column_settings['plain_renderer'], array( 'text', 'taxonomy_dropdown' ) ) ) {
							$column_settings['plain_renderer'] = 'text';
						}
					}

					$column_args = array(
						'acf_field'         => isset( $column_settings['acf_field'] ) ? $column_settings['acf_field'] : array(),
						'data_type'         => $column_settings['data_source'], //String (post_data,post_meta|meta_data)
						'unformatted'       => array(
							'data'     => $column_settings['key'],
							'readOnly' => ( $column_settings['read_only'] === 'yes' ) ? true : false,
						), //Array (Valores admitidos por el plugin de handsontable)
						'column_width'      => $column_settings['width'], //int (Ancho de la columna)
						'title'             => $column_settings['name'], //String (Titulo de la columna)
						'type'              => $column_settings['cell_type'], // String (Es para saber si será un boton que abre popup, si no dejar vacio) boton_tiny|boton_gallery|boton_gallery_multiple|(vacio)
						'supports_formulas' => ( $column_settings['allow_formulas'] === 'yes' ) ? true : false,
						'allow_to_hide'     => ( $column_settings['allow_hide'] === 'yes' ) ? true : false,
						'allow_to_save'     => ( $column_settings['read_only'] === 'yes' && ! in_array( $column_settings['cell_type'], array( 'boton_gallery', 'boton_gallery_multiple' ) ) ) ? false : true,
						'allow_to_rename'   => ( $column_settings['allow_rename'] === 'yes' ) ? true : false,
						'formatted'         => array(
							'data'     => $column_settings['key'],
							'readOnly' => ( $column_settings['read_only'] === 'yes' ) ? true : false,
						),
					);

					if ( in_array( $column_settings['plain_renderer'], array( 'html', 'text' ) ) ) {
						$column_args['unformatted']['renderer'] = $column_settings['plain_renderer'];
					}
					if ( in_array( $column_settings['formatted_renderer'], array( 'html', 'text' ) ) ) {
						$column_args['formatted']['renderer'] = $column_settings['formatted_renderer'];
					}

					if ( $column_settings['plain_renderer'] === 'checkbox' ) {
						$column_args['unformatted']['type']              = 'checkbox';
						$column_args['unformatted']['checkedTemplate']   = $column_settings['checkedTemplate'];
						$column_args['unformatted']['uncheckedTemplate'] = $column_settings['uncheckedTemplate'];
						$column_args['default_value']                    = $column_settings['default_value'];
					}
					if ( $column_settings['formatted_renderer'] === 'checkbox' ) {
						$column_args['formatted']['type']              = 'checkbox';
						$column_args['formatted']['checkedTemplate']   = $column_settings['checkedTemplate'];
						$column_args['formatted']['uncheckedTemplate'] = $column_settings['uncheckedTemplate'];
						$column_args['default_value']                  = $column_settings['default_value'];
					}
					if ( $column_settings['plain_renderer'] === 'select' ) {
						$column_args['unformatted']['editor']        = 'select';
						$column_args['unformatted']['selectOptions'] = $column_settings['selectOptions'];
						$column_args['default_value']                = $column_settings['default_value'];
					}
					if ( $column_settings['formatted_renderer'] === 'select' ) {
						$column_args['formatted']['editor']        = 'select';
						$column_args['formatted']['selectOptions'] = $column_settings['selectOptions'];
						$column_args['default_value']              = $column_settings['default_value'];
					}
					if ( $column_settings['plain_renderer'] === 'date' ) {
						$column_args['unformatted'] = array_merge(
							$column_args['unformatted'],
							array(
								'type'             => 'date',
								'correctFormat'    => true,
								'defaultDate'      => date( 'm-d-Y' ),
								'datePickerConfig' => array(
									'firstDay'       => 0,
									'showWeekNumber' => true,
									'numberOfMonths' => 1,
								),
							)
						);
						unset( $column_args['unformatted']['renderer'] );
					}
					if ( $column_settings['formatted_renderer'] === 'date' ) {
						$column_args['formatted'] = array_merge(
							$column_args['formatted'],
							array(
								'type'             => 'date',
								'correctFormat'    => true,
								'defaultDate'      => date( 'm-d-Y' ),
								'datePickerConfig' => array(
									'firstDay'       => 0,
									'showWeekNumber' => true,
									'numberOfMonths' => 1,
								),
							)
						);
						unset( $column_args['formatted']['renderer'] );
					}
					if ( $column_settings['data_source'] === 'post_terms' ) {
						if ( $column_settings['plain_renderer'] === 'taxonomy_dropdown' ) {
							$column_args['unformatted'] = array_merge(
								$column_args['unformatted'],
								array(
									'type'   => 'autocomplete',
									'source' => 'loadTaxonomyTerms',
								)
							);
						} elseif ( $column_settings['formatted_renderer'] === 'taxonomy_dropdown' ) {
							$column_args['formatted'] = array_merge(
								$column_args['formatted'],
								array(
									'type'   => 'autocomplete',
									'source' => 'loadTaxonomyTerms',
								)
							);
						}
					}

					if ( $column_settings['cell_type'] === 'metabox' ) {
						$column_args = array_merge( $column_args, $column_settings );
					}

					$editor->args['columns']->register_item( $column_settings['key'], $post_type, $column_args );
				}
			}
		}

	}

}

if ( ! function_exists( 'WP_Sheet_Editor_ACF_Obj' ) ) {

	function WP_Sheet_Editor_ACF_Obj() {
		return WP_Sheet_Editor_ACF::get_instance();
	}
}


add_action( 'vg_sheet_editor/initialized', 'WP_Sheet_Editor_ACF_Obj' );
