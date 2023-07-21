<?php
namespace TrxAddons\AiHelper;

if ( ! class_exists( 'Utils' ) ) {

	/**
	 * Utilities for text and image generation
	 */
	class Utils {

		/**
		 * Constructor
		 */
		function __construct() {
		}

		/**
		 * Parse the response and return an array with answer
		 * 
		 * @param string $response  Response from the API
		 * @param string $model     Image model
		 * 
		 * @return array  Array with answer
		 */
		static function parse_response( $response, $model, $answer ) {
			if ( strpos( $model, 'stabble-diffusion' ) !== false ) {
				if ( ! empty( $response['status'] ) ) {
					if ( $response['status'] == 'success' ) {
						if ( ! empty( $response['output'] ) && is_array( $response['output'] ) && ! empty( $response['output'][0] ) ) {
							foreach( $response['output'] as $image_url ) {
								$answer['data']['images'][] = array(
									'url' => $image_url,
								);
							}
						} else {
							$answer['error'] = __( 'Error! Unexpected answer from the Stabble Diffusion API.', 'trx_addons' );
						}
					} else if ( $response['status'] == 'processing' ) {
						if ( ! empty( $response['fetch_result'] ) ) {
							$answer['data'] = array_merge( $answer['data'], apply_filters( 'trx_addons_filter_ai_helper_fetch', array(
								'fetch_model' => $model,
								'fetch_id'  => $response['id'],
								'fetch_img' => trx_addons_get_file_url( TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/images/fetch.png' ),
								'fetch_msg' => __( 'wait for render...', 'trx_addons' ),
							) ) );
						}
					} else if ( $response['status'] == 'error' ) {
						if ( ! empty( $response['message'] ) ) {
								$answer['error'] = is_array( $response['message'] ) ? join( ', ', $response['message'] ) : $response['message'];
						} else if ( ! empty( $response['messege'] ) ) {
								$answer['error'] = is_array( $response['messege'] ) ? join( ', ', $response['messege'] ) : $response['messege'];
						} else {
							$answer['error'] = __( 'Error! Unexpected response from the Stabble Diffusion API.', 'trx_addons' );
						}
					} else {
						$answer['error'] = __( 'Error! Unexpected response from the Stabble Diffusion API.', 'trx_addons' );
					}
				} else {
					$answer['error'] = __( 'Error! Unknown response from the Stabble Diffusion API.', 'trx_addons' );
				}
			} else {
				if ( ! empty( $response['data'] ) && ! empty( $response['data'][0]['url'] ) ) {
					$answer['data']['images'] = $response['data'];
				} else {
					if ( ! empty( $response['error']['message'] ) ) {
						$answer['error'] = $response['error']['message'];
					} else {
						$answer['error'] = __( 'Error! Unknown response from the OpenAI API.', 'trx_addons' );
					}
				}
			}
			return $answer;
		}

		/**
		 * Return default image model for the 'Generate images' button and the 'Make variations' button
		 * 
		 * @return string  Image model
		 */
		static function get_default_image_model() {
			return 'openai/default';
		}

		/**
		 * Return default image size for the 'Generate images' button and the 'Make variations' button
		 * 
		 * @return string  Image size in format 'WxH'
		 */
		static function get_default_image_size( $mode = 'media' ) {
			return $mode == 'media' ? '1024x1024' : '256x256';
		}

		/**
		 * Check if the image is in the list of allowed sizes
		 * 
		 * @return string  Image size in format 'WxH'
		 */
		static function check_image_size( $size, $mode = 'media' ) {
			$allowed_sizes = Lists::get_list_ai_image_sizes();
			return ! empty( $allowed_sizes[ $size ] ) ? $size : self::get_default_image_size( $mode );
		}
	}
}
