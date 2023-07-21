<?php
/**
 * Shortcode: Image Generator
 *
 * @package ThemeREX Addons
 * @since v2.20.2
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

use TrxAddons\AiHelper\OpenAi;
use TrxAddons\AiHelper\StabbleDiffusion;
use TrxAddons\AiHelper\Lists;
use TrxAddons\AiHelper\Utils;


// Load required styles and scripts for the frontend
if ( ! function_exists( 'trx_addons_sc_igenerator_load_scripts_front' ) ) {
	add_action( "wp_enqueue_scripts", 'trx_addons_sc_igenerator_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY );
	add_action( 'trx_addons_action_pagebuilder_preview_scripts', 'trx_addons_sc_igenerator_load_scripts_front', 10, 1 );
	function trx_addons_sc_igenerator_load_scripts_front( $force = false ) {
		trx_addons_enqueue_optimized( 'sc_igenerator', $force, array(
			'css'  => array(
				'trx_addons-sc_igenerator' => array( 'src' => TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/igenerator/igenerator.css' ),
			),
			'js' => array(
				'trx_addons-sc_igenerator' => array( 'src' => TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/igenerator/igenerator.js', 'deps' => 'jquery' ),
			),
			'check' => array(
				array( 'type' => 'sc',  'sc' => 'trx_sc_igenerator' ),
				array( 'type' => 'gb',  'sc' => 'wp:trx-addons/igenerator' ),
				array( 'type' => 'elm', 'sc' => '"widgetType":"trx_sc_igenerator"' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[trx_sc_igenerator' ),
			)
		) );
	}
}

// Enqueue responsive styles for frontend
if ( ! function_exists( 'trx_addons_sc_igenerator_load_scripts_front_responsive' ) ) {
	add_action( 'wp_enqueue_scripts', 'trx_addons_sc_igenerator_load_scripts_front_responsive', TRX_ADDONS_ENQUEUE_RESPONSIVE_PRIORITY );
	add_action( 'trx_addons_action_load_scripts_front_sc_igenerator', 'trx_addons_sc_igenerator_load_scripts_front_responsive', 10, 1 );
	function trx_addons_sc_igenerator_load_scripts_front_responsive( $force = false  ) {
		trx_addons_enqueue_optimized_responsive( 'sc_igenerator', $force, array(
			'css'  => array(
				'trx_addons-sc_igenerator-responsive' => array(
					'src' => TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/igenerator/igenerator.responsive.css',
					'media' => 'sm'
				),
			),
		) );
	}
}


// Add audio effects to the list with JS vars
// if ( !function_exists( 'trx_addons_sc_igenerator_localize_script' ) ) {
// 	add_action("trx_addons_filter_localize_script", 'trx_addons_sc_igenerator_localize_script');
// 	function trx_addons_sc_igenerator_localize_script($vars) {
// 		$vars['sc_igenerator_models'] = Lists::get_list_ai_image_models();
// 		return $vars;
// 	}
// }

// Merge shortcode's specific styles to the single stylesheet
if ( ! function_exists( 'trx_addons_sc_igenerator_merge_styles' ) ) {
	add_filter( "trx_addons_filter_merge_styles", 'trx_addons_sc_igenerator_merge_styles' );
	function trx_addons_sc_igenerator_merge_styles( $list ) {
		$list[ TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/igenerator/igenerator.css' ] = false;
		return $list;
	}
}

// Merge shortcode's specific styles to the single stylesheet (responsive)
if ( ! function_exists( 'trx_addons_sc_igenerator_merge_styles_responsive' ) ) {
	add_filter("trx_addons_filter_merge_styles_responsive", 'trx_addons_sc_igenerator_merge_styles_responsive' );
	function trx_addons_sc_igenerator_merge_styles_responsive( $list ) {
		$list[ TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/igenerator/igenerator.responsive.css' ] = false;
		return $list;
	}
}

// Merge shortcode's specific scripts into single file
if ( ! function_exists( 'trx_addons_sc_igenerator_merge_scripts' ) ) {
	add_action("trx_addons_filter_merge_scripts", 'trx_addons_sc_igenerator_merge_scripts');
	function trx_addons_sc_igenerator_merge_scripts($list) {
		$list[ TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/igenerator/igenerator.js' ] = false;
		return $list;
	}
}

// Load styles and scripts if present in the cache of the menu
if ( ! function_exists( 'trx_addons_sc_igenerator_check_in_html_output' ) ) {
	add_filter( 'trx_addons_filter_get_menu_cache_html', 'trx_addons_sc_igenerator_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_show_layout_from_cache', 'trx_addons_sc_igenerator_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_check_page_content', 'trx_addons_sc_igenerator_check_in_html_output', 10, 1 );
	function trx_addons_sc_igenerator_check_in_html_output( $content = '' ) {
		$args = array(
			'check' => array(
				'class=[\'"][^\'"]*sc_igenerator'
			)
		);
		if ( trx_addons_check_in_html_output( 'sc_igenerator', $content, $args ) ) {
			trx_addons_sc_igenerator_load_scripts_front( true );
		}
		return $content;
	}
}


// trx_sc_igenerator
//-------------------------------------------------------------
/*
[trx_sc_igenerator id="unique_id" number="2" prompt="prompt text for ai"]
*/
if ( ! function_exists( 'trx_addons_sc_igenerator' ) ) {
	function trx_addons_sc_igenerator( $atts, $content = null ) {	
		$atts = trx_addons_sc_prepare_atts( 'trx_sc_igenerator', $atts, trx_addons_sc_common_atts( 'id,title', array(
			// Individual params
			"type" => "default",
			"tags" => "",
			"tags_label" => "",
			"prompt" => "",
			"prompt_width" => "100",
			"button_text" => "",
			"number" => "3",
			"columns" => "",
			"columns_tablet" => "",
			"columns_mobile" => "",
			"size" => Utils::get_default_image_size( 'sc_igenerator' ),
			"model" => "",
			"show_settings" => 0,
			"align" => "",
			"demo_images" => "",
			"demo_images_url" => "",
			'demo_thumb_size' => apply_filters( 'trx_addons_filter_thumb_size',
													trx_addons_get_thumb_size( 'avatar' ),
													'trx_addons_sc_igenerator',
													$atts
												),
		) ) );
		// Load shortcode-specific scripts and styles
		trx_addons_sc_igenerator_load_scripts_front( true );

		// Load template
		$output = '';
		$atts['number'] = max( 1, min( 10, (int)$atts['number'] ) );
		if ( empty( $atts['columns'] ) ) $atts['columns'] = $atts['number'];
		$atts['columns'] = max( 1, min( $atts['number'], (int)$atts['columns'] ) );
		if ( ! empty( $atts['columns_tablet'] ) ) $atts['columns_tablet'] = max( 1, min( $atts['number'], (int)$atts['columns_tablet'] ) );
		if ( ! empty( $atts['columns_mobile'] ) ) $atts['columns_mobile'] = max( 1, min( $atts['number'], (int)$atts['columns_mobile'] ) );
		$atts['size'] = Utils::check_image_size( $atts['size'], 'sc_igenerator' );
		if ( ! is_array( $atts['demo_images'] ) ) {
			$demo_images = explode( '|', $atts['demo_images'] );
			$atts['demo_images'] = array();
			foreach ( $demo_images as $img ) {
				$atts['demo_images'][] = array( 'url' => $img );
			}
		}

		ob_start();
		trx_addons_get_template_part( array(
										TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/igenerator/tpl.' . trx_addons_esc( $atts['type'] ) . '.php',
										TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/igenerator/tpl.default.php'
										),
										'trx_addons_args_sc_igenerator',
										$atts
									);
		$output = ob_get_contents();
		ob_end_clean();
		return apply_filters( 'trx_addons_sc_output', $output, 'trx_sc_igenerator', $atts, $content );
	}
}

// Add shortcode [trx_sc_igenerator]
if ( ! function_exists( 'trx_addons_sc_igenerator_add_shortcode' ) ) {
	add_action( 'init', 'trx_addons_sc_igenerator_add_shortcode', 20 );
	function trx_addons_sc_igenerator_add_shortcode() {
		add_shortcode( "trx_sc_igenerator", "trx_addons_sc_igenerator" );
	}
}


// Add number of generated images to the total number and save to the transient
if ( ! function_exists( 'trx_addons_sc_igenerator_set_total_generated' ) ) {
	function trx_addons_sc_igenerator_set_total_generated( $number ) {
		$data = get_transient( 'trx_addons_sc_igenerator_total' );
		if ( ! is_array( $data ) || $data['date'] != date( 'Y-m-d' ) ) {
			$data = array(
				'per_hour' => array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0 ),
				'per_day' => 0,
				'date' => date( 'Y-m-d' ),
			);
		}
		$hour = (int) date( 'H' );
		$data['per_hour'][ $hour ] += $number;
		$data['per_day'] += $number;
		set_transient( 'trx_addons_sc_igenerator_total', $data, 24 * 60 * 60 );
	}
}

// Get number of generated images from the transient
if ( ! function_exists( 'trx_addons_sc_igenerator_get_total_generated' ) ) {
	function trx_addons_sc_igenerator_get_total_generated( $per = 'hour' ) {
		$data = get_transient( 'trx_addons_sc_igenerator_total' );
		if ( ! is_array( $data ) ) {
			return 0;
		}
		if ( $per == 'hour' ) {
			$hour = (int) date( 'H' );
			return $data['per_hour'][ $hour ];
		} else if ( $per == 'day' ) {
			return $data['per_day'];
		} else if ( $per == 'all' ) {
			return $data;
		} else {
			return 0;
		}
	}
}

// Log a visitor ip address to the json file
if ( ! function_exists( 'trx_addons_sc_igenerator_log_to_json' ) ) {
	function trx_addons_sc_igenerator_log_to_json( $number ) {
		$ip = ! empty( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : 'Unknown';
		$date = date( 'Y-m-d' );
		$time = date( 'H:i:s' );
		$hour = date( 'H' );
		$json = trx_addons_fgc( TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/igenerator/igenerator.log' );
		if ( empty( $json ) ) $json = '[]';
		$ips = json_decode( $json, true );
		if ( ! is_array( $ips ) ) {
			$ips = array();
		}
		if ( empty( $ips[ $date ] ) ) {
			$ips[ $date ] = array( 'total' => 0, 'ip' => array(), 'hour' => array() );
		}
		// Log total
		$ips[ $date ]['total'] += $number;
		// Log by IP
		if ( empty( $ips[ $date ]['ip'][ $ip ] ) ) {
			$ips[ $date ]['ip'][ $ip ] = array();
		}
		if ( empty( $ips[ $date ]['ip'][ $ip ][ $time ] ) ) {
			$ips[ $date ]['ip'][ $ip ][ $time ] = 0;
		}
		$ips[ $date ]['ip'][ $ip ][ $time ] += $number;
		// Log by hour
		if ( empty( $ips[ $date ]['hour'][ $hour ] ) ) {
			$ips[ $date ]['hour'][ $hour ] = array();
		}
		if ( empty( $ips[ $date ]['hour'][ $hour ][ $time ] ) ) {
			$ips[ $date ]['hour'][ $hour ][ $time ] = 0;
		}
		$ips[ $date ]['hour'][ $hour ][ $time ] += $number;
		trx_addons_fpc( TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/igenerator/igenerator.log', json_encode( $ips, JSON_PRETTY_PRINT ) );
	}
}

// Callback function to generate images from the shortcode AJAX request
if ( ! function_exists( 'trx_addons_sc_igenerator_generate_images' ) ) {
	add_action( 'wp_ajax_nopriv_trx_addons_ai_helper_igenerator', 'trx_addons_sc_igenerator_generate_images' );
	add_action( 'wp_ajax_trx_addons_ai_helper_igenerator', 'trx_addons_sc_igenerator_generate_images' );
	function trx_addons_sc_igenerator_generate_images() {

		trx_addons_verify_nonce();

		$settings = trx_addons_decode_settings( trx_addons_get_value_gp( 'settings' ) );
		$prompt = trx_addons_get_value_gp( 'prompt' );
		$model = trx_addons_get_value_gp( 'model' );
		if ( empty( $model ) ) {
			$model = ! empty( $settings[ 'model' ] ) ? $settings[ 'model' ] : Utils::get_default_image_model();
		}
		$size = ! empty( $settings[ 'size' ] ) ? Utils::check_image_size( $settings[ 'size' ], 'sc_igenerator' ) : Utils::get_default_image_size('sc_igenerator');
		$number = ! empty( $settings[ 'number' ] ) ? max( 1, min( 10, $settings['number'] ) ) : 3;
		$count = (int)trx_addons_get_value_gp( 'count' );

		$limits = (int)trx_addons_get_option( 'ai_helper_sc_igenerator_limits' ) > 0;
		$limit_per_hour = (int)trx_addons_get_option( 'ai_helper_sc_igenerator_limit_per_hour' );
		$limit_per_visitor = (int)trx_addons_get_option( 'ai_helper_sc_igenerator_limit_per_visitor' );
	
		$answer = array(
			'error' => '',
			'data' => array(
				'images' => array(),
				'number' => $number,
				'columns' => ! empty( $settings[ 'columns' ] ) ? max( 1, min( 12, $settings['columns'] ) ) : 3,
				'columns_tablet' => ! empty( $settings[ 'columns_tablet' ] ) ? max( 1, min( 12, $settings['columns_tablet'] ) ) : '',
				'columns_mobile' => ! empty( $settings[ 'columns_mobile' ] ) ? max( 1, min( 12, $settings['columns_mobile'] ) ) : '',
				'message' => ''
			)
		);

		if ( ! empty( $prompt ) ) {

			$generated_per_hour = trx_addons_sc_igenerator_get_total_generated();
			$lph  = $limits && $limit_per_hour < $generated_per_hour + $number;
			$lpv  = $limits && $limit_per_visitor < $count;
			$demo = $count == 0 || $lph || $lpv;

			$api = strpos( $model, 'stabble-diffusion' ) !== false
					? StabbleDiffusion::instance()
					: OpenAi::instance();

			if ( $api->get_api_key() != '' && ! $demo ) {

				// Log a visitor ip address to the json file
				//trx_addons_sc_igenerator_log_to_json( $number );

				$args = array(
					'prompt' => apply_filters( 'trx_addons_filter_ai_helper_prompt', $prompt, compact( 'size', 'number' ),'sc_igenerator' ),
					'size' => $size,
					'n' => $number,
				);
				if ( strpos( $model, 'stabble-diffusion' ) !== false ) {
					$args['model'] = $model;
				}
				$response = $api->generate_images( $args );
				$answer = Utils::parse_response( $response, $model, $answer );
				if ( ! empty( $answer['data']['fetch_id'] ) ) {
					$answer['data']['fetch_number'] = $number;
				}
				trx_addons_sc_igenerator_set_total_generated( $number );
			} else {
				if ( ! empty( $settings['demo_images'] ) && ! empty( $settings['demo_images'][0]['url'] ) ) {
					$images = array();
					foreach ( $settings['demo_images'] as $img ) {
						$images[] = trx_addons_add_thumb_size( $img['url'], $settings['demo_thumb_size'] );
					}
				} else {
					$images = trx_addons_get_list_files( TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/igenerator/images/' . trx_addons_esc( $size ) );
				}
				if ( $api->get_api_key() != '' && $demo ) {
					$answer['data']['message'] = '<p data-lp="' . ( $lph ? 'lph' . $generated_per_hour : ( $lpv ? 'lpv' : '' ) ) . '">' . __( 'Limits are reached!', 'trx_addons' ) . '</p>'
												. '<p>' . __( 'The limit of the number of images that can be generated within 1 hour has been reached.', 'trx_addons' ) . '</p>'
												. ( is_array( $images ) && count( $images ) > 0 ? __( 'Therefore, instead of generated images, you see demo samples.', 'trx_addons' ) : '' )
												. '<p>' . __( ' Please try again later.', 'trx_addons' ) . '</p>';
				}
				if ( is_array( $images ) && count( $images ) > 0 ) {
					shuffle( $images );
					for ( $i = 0; $i < min( $number, count( $images ) ); $i++ ) {
						$answer['data']['images'][] = array(
							'url' => $images[ $i ]
						);
					}
				} else if ( $api->get_api_key() == '' )  {
					$answer['error'] = __( 'Error! API key is not specified.', 'trx_addons' );
				}
			}
		} else {
			$answer['error'] = __( 'Error! The prompt is empty.', 'trx_addons' );
		}

		// Return response to the AJAX handler
		trx_addons_ajax_response( apply_filters( 'trx_addons_filter_sc_igenerator_answer', $answer ) );
	}
}


// Add shortcodes
//----------------------------------------------------------------------------

// Add shortcodes to Elementor
if ( trx_addons_exists_elementor() && function_exists('trx_addons_elm_init') ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/igenerator/igenerator-sc-elementor.php';
}

// Add shortcodes to Gutenberg
if ( trx_addons_exists_gutenberg() && function_exists( 'trx_addons_gutenberg_get_param_id' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/igenerator/igenerator-sc-gutenberg.php';
}
