<?php
namespace TrxAddons\AiHelper\MediaLibrary;

use TrxAddons\AiHelper\OpenAi;
use TrxAddons\AiHelper\StabbleDiffusion;
use TrxAddons\AiHelper\Lists;
use TrxAddons\AiHelper\Utils;

if ( ! class_exists( 'Helper' ) ) {

	/**
	 * Main class for AI Helper MediaSelector support
	 */
	class Helper {

		/**
		 * Constructor
		 */
		function __construct() {
			add_action( 'trx_addons_action_load_scripts_admin', array( $this, 'enqueue_scripts_admin' ) );
			add_filter( 'trx_addons_filter_localize_script_admin', array( $this, 'localize_script_admin' ) );

			// AJAX callback for the 'Generate images' button
			add_action( 'wp_ajax_trx_addons_ai_helper_generate_images', array( $this, 'generate_images' ) );

			// AJAX callback for the 'Make variations' button
			add_action( 'wp_ajax_trx_addons_ai_helper_make_variations', array( $this, 'make_variations' ) );

			// AJAX callback for the 'Add to Uploads' button
			add_action( 'wp_ajax_trx_addons_ai_helper_add_to_uploads', array( $this, 'add_to_uploads' ) );

			// AJAX callback for the 'Fetch images'
			add_action( 'wp_ajax_trx_addons_ai_helper_fetch_images', array( $this, 'fetch_images' ) );
		}

		/**
		 * Check if AI Helper is allowed for MediaSelector
		 */
		public static function is_allowed() {
			return OpenAi::instance()->get_api_key() != '' || StabbleDiffusion::instance()->get_api_key() != '';
		}

		/**
		 * Enqueue scripts and styles for the admin mode
		 * 
		 * @hooked 'admin_enqueue_scripts'
		 */
		function enqueue_scripts_admin() {
			if ( self::is_allowed() ) {
				wp_enqueue_style( 'trx_addons-ai-helper-media-selector', trx_addons_get_file_url( TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/support/MediaLibrary/assets/css/index.css' ), array(), null );
				wp_enqueue_script( 'trx_addons-ai-helper-media-selector', trx_addons_get_file_url( TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/support/MediaLibrary/assets/js/index.js' ), array( 'jquery' ), null, true );
			}
		}

		/**
		 * Localize script to show messages in the admin mode
		 * 
		 * @hooked 'trx_addons_filter_localize_script_admin'
		 * 
		 * @param array $vars  Array of variables to be passed to the script
		 * 
		 * @return array  Modified array of variables
		 */
		function localize_script_admin( $vars ) {
			if ( self::is_allowed() ) {
				$vars['msg_ai_helper_error'] = esc_html__( "AI Helper unrecognized response", 'trx_addons' );
				$vars['msg_ai_helper_prompt_error'] = esc_html__( "Prompt is empty!", 'trx_addons' );
				$vars['ai_helper_generate_image_models'] = Lists::get_list_ai_image_models();
				$vars['ai_helper_generate_image_templates'] = Lists::get_list_ai_image_templates();
				$vars['ai_helper_generate_image_sizes'] = Lists::get_list_ai_image_sizes();
				$vars['ai_helper_generate_image_numbers'] = trx_addons_get_list_range( 1, 10 );
			}
			return $vars;
		}

		/**
		 * Send a query to API to generate images from the prompt
		 * 
		 * @hooked 'wp_ajax_trx_addons_ai_helper_generate_images'
		 * 
		 * @param WP_REST_Request  $request  Full details about the request.
		 */
		function generate_images( $request = false ) {

			trx_addons_verify_nonce();

			$answer = array(
				'error' => '',
				'data' => array(
					'images' => array()
				)
			);
			if ( current_user_can( 'edit_posts' ) ) {
				if ( $request ) {
					$params = $request->get_params();
					$model  = ! empty( $params['model'] ) ? $params['model'] : Utils::get_default_image_model();
					$size   = ! empty( $params['size'] ) ? (int)$params['size'] : Utils::get_default_image_size();
					$number = ! empty( $params['number'] ) ? (int)$params['number'] : 1;
					$prompt = ! empty( $params['prompt'] ) ? $params['prompt'] : '';
				} else {
					$model  = trx_addons_get_value_gp( 'model', Utils::get_default_image_model() );
					$size   = trx_addons_get_value_gp( 'size', Utils::get_default_image_size() );
					$number = (int)trx_addons_get_value_gp( 'number', 1 );
					$prompt = trx_addons_get_value_gp( 'prompt' );
				}
				$number = max( 1, min( 10, $number ) );
				if ( strpos( $model, 'stabble-diffusion' ) !== false ) {
					$number = max( 1, min( 4, $number ) );
				}
				if ( ! empty( $prompt ) ) {
					$api = strpos( $model, 'stabble-diffusion' ) !== false
							? StabbleDiffusion::instance()
							: OpenAi::instance();
					$args = array(
						'prompt' => apply_filters( 'trx_addons_filter_ai_helper_prompt', $prompt, $params, 'generate_images' ),
						'size' => Utils::check_image_size( $size ),
						'n' => $number,
					);
					if ( strpos( $model, 'stabble-diffusion' ) !== false ) {
						$args['model'] = $model;
					}
					$response = $api->generate_images( $args );
					$answer = Utils::parse_response( $response, $model, $answer );
				} else {
					$answer['error'] = __( 'Error! Empty prompt.', 'trx_addons' );
				}
			}

			if ( $request ) {
				// Return response to the REST API
				return rest_ensure_response( $answer );
			} else {
				// Return response to the AJAX handler
				trx_addons_ajax_response( $answer );
			}
		}

		/**
		 * Send a query to API to make variations of the image
		 * 
		 * @hooked 'wp_ajax_trx_addons_ai_helper_make_variations'
		 * 
		 * @param WP_REST_Request  $request  Full details about the request.
		 */
		function make_variations( $request = false ) {

			trx_addons_verify_nonce();

			$answer = array(
				'error' => '',
				'data' => array(
					'images' => array()
				)
			);
			if ( current_user_can( 'edit_posts' ) ) {
				if ( $request ) {
					$params = $request->get_params();
					$prompt = ! empty( $params['prompt'] ) ? $params['prompt'] : '';
					$model  = ! empty( $params['model'] ) ? $params['model'] : Utils::get_default_image_model();
					$size   = ! empty( $params['size'] ) ? (int)$params['size'] : Utils::get_default_image_size();
					$number = ! empty( $params['number'] ) ? (int)$params['number'] : 1;
					$image  = ! empty( $params['image'] ) ? $params['image'] : '';
				} else {
					$prompt = trx_addons_get_value_gp( 'prompt', '' );
					$model  = trx_addons_get_value_gp( 'model', Utils::get_default_image_model() );
					$size   = trx_addons_get_value_gp( 'size', Utils::get_default_image_size() );
					$number = (int)trx_addons_get_value_gp( 'number', 1 );
					$image  = trx_addons_get_value_gp( 'image' );
				}
				$number = max( 1, min( 10, $number ) );
				if ( strpos( $model, 'stabble-diffusion' ) !== false ) {
					$number = max( 1, min( 4, $number ) );
				}
				if ( ! empty( $image ) ) {
					$api = strpos( $model, 'stabble-diffusion' ) !== false
							? StabbleDiffusion::instance()
							: OpenAi::instance();
					$args = array(
						'image' => $image,
						'size'  => Utils::check_image_size( $size ),
						'n'     => $number,
					);
					if ( strpos( $model, 'stabble-diffusion' ) !== false ) {
						$args['model']  = $model;
						$args['prompt'] = $prompt;
					}
					$response = $api->make_variations( $args );
					$answer = Utils::parse_response( $response, $model, $answer );
				} else {
					$answer['error'] = __( 'Error! Image is not specified.', 'trx_addons' );
				}
			}

			if ( $request ) {
				// Return response to the REST API
				return rest_ensure_response( $answer );
			} else {
				// Return response to the AJAX handler
				trx_addons_ajax_response( $answer );
			}
		}

		/**
		 * Add an image to the media library
		 * 
		 * @hooked 'wp_ajax_trx_addons_ai_helper_add_to_uploads'
		 * 
		 * @param WP_REST_Request  $request  Full details about the request.
		 */
		function add_to_uploads( $request = false ) {

			trx_addons_verify_nonce();

			$answer = array(
				'error' => '',
				'data' => ''
			);
			if ( current_user_can( 'edit_posts' ) ) {
				if ( $request ) {
					$params = $request->get_params();
					$image = ! empty( $params['image'] ) ? $params['image'] : '';
					$filename = ! empty( $params['filename'] ) ? $params['filename'] : '';
					$caption = ! empty( $params['caption'] ) ? $params['caption'] : '';
				} else {
					$image = trx_addons_get_value_gp( 'image' );
					$filename = trx_addons_get_value_gp( 'filename' );
					$caption = trx_addons_get_value_gp( 'caption' );
				}
				if ( ! empty( $image ) ) {
					// Get image data
					$image_content = trx_addons_fgc( $image );
					// Add image to the media library
					if ( empty( $filename ) ) {
						$image = explode( '?', $image );
						$filename = trx_addons_esc( basename( $image[0] ) );
					} else {
						$filename = explode( '.', trim( $filename ) );
						$filename = trx_addons_esc( str_replace( ' ', '-', $filename[0] ) . '.png' );
					}
					$upload = wp_upload_bits( $filename, null, $image_content );
					if ( empty( $upload['error'] ) ) {
						// Add an attachment to the DB
						$attachment = array(
							'guid'           => $upload['url'],
							'post_mime_type' => 'image/png',
							'post_title'     => preg_replace( '/\.[^.]+$/', '', $filename ),
							'post_excerpt'   => strip_tags( trim( $caption ) ),
							'post_content'   => '',
							'post_status'    => 'inherit'
						);
						$attach_id = wp_insert_attachment( $attachment, $upload['file'] );
						// Generate metadata and thumbnails
						$attach_data = wp_generate_attachment_metadata( $attach_id, $upload['file'] );
						wp_update_attachment_metadata( $attach_id, $attach_data );
						// Return attachment ID
						$answer['data'] = $attach_id;
					} else {
						$answer['error'] = $upload['error'];
					}
				} else {
					$answer['error'] = __( 'Error! Image URL is empty.', 'trx_addons' );
				}
			}
			if ( $request ) {
				// Return response to the REST API
				return rest_ensure_response( $answer );
			} else {
				// Return response to the AJAX handler
				trx_addons_ajax_response( $answer );
			}
		}

		/**
		 * Fetch images from the Stabble Diffusion API
		 * 
		 * @hooked 'wp_ajax_trx_addons_ai_helper_fetch_images'
		 * 
		 * @param WP_REST_Request  $request  Full details about the request.
		 */
		function fetch_images( $request = false ) {

			trx_addons_verify_nonce();

			$answer = array(
				'error' => '',
				'data' => array(
					'images' => array()
				)
			);
			if ( current_user_can( 'edit_posts' ) ) {
				if ( $request ) {
					$params = $request->get_params();
					$model  = ! empty( $params['fetch_model'] ) ? $params['fetch_model'] : Utils::get_default_image_model();
					$id     = ! empty( $params['fetch_id'] ) ? $params['fetch_id'] : '';
				} else {
					$model  = trx_addons_get_value_gp( 'fetch_model', Utils::get_default_image_model() );
					$id     = trx_addons_get_value_gp( 'fetch_id', '' );
				}
				if ( ! empty( $id ) ) {
					$api = StabbleDiffusion::instance();
					$response = $api->fetch_images( array(
						'fetch_id' => $id,
						'model'    => $model,
					) );
					$answer = Utils::parse_response( $response, $model, $answer );
				} else {
					$answer['error'] = __( 'Error! Need the queue ID for fetch images from server.', 'trx_addons' );
				}
			}

			if ( $request ) {
				// Return response to the REST API
				return rest_ensure_response( $answer );
			} else {
				// Return response to the AJAX handler
				trx_addons_ajax_response( $answer );
			}
		}
	}
}
