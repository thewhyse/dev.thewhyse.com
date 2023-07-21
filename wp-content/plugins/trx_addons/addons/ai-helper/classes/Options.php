<?php
namespace TrxAddons\AiHelper;

if ( ! class_exists( 'Options' ) ) {

	/**
	 * Add options to the ThemeREX Addons Options
	 */
	class Options {

		/**
		 * Constructor
		 */
		function __construct() {
			add_filter( 'trx_addons_filter_options', array( $this, 'add_options' ) );
			add_filter( 'trx_addons_filter_before_show_options', array( $this, 'fix_options' ) );
			add_filter( 'trx_addons_filter_export_options', array( $this, 'remove_token_from_export' ) );
		}

		/**
		 * Add options to the ThemeREX Addons Options
		 * 
		 * @hooked trx_addons_filter_options
		 *
		 * @param array $options  Array of options
		 * 
		 * @return array  	  Modified array of options
		 */
		function add_options( $options ) {
			$is_options_page = trx_addons_get_value_gp( 'page' ) == 'trx_addons_options';
			$log_open_ai = $is_options_page ? Logger::instance()->get_log_report( 'open-ai') : '';
			$log_sd = $is_options_page ? Logger::instance()->get_log_report( 'stabble-diffusion') : '';
			trx_addons_array_insert_before( $options, 'sc_section', apply_filters( 'trx_addons_filter_options_ai_helper', array(
				'ai_helper_section' => array(
					"title" => esc_html__('AI Helper', 'trx_addons'),
					'icon' => 'trx_addons_icon-android',
					"type" => "section"
				),

				// Open AI API settings
				'ai_helper_info_openai' => array(
					"title" => esc_html__('Open AI', 'trx_addons'),
					"desc" => wp_kses_data( __("Settings of the AI Helper for Open AI API", 'trx_addons') )
							. ( ! empty( $log_open_ai ) ? wp_kses( $log_open_ai, 'trx_addons_kses_content' ) : '' ),
					"type" => "info"
				),
				'ai_helper_token_openai' => array(
					"title" => esc_html__('Open AI token', 'trx_addons'),
					"desc" => wp_kses( sprintf(
													__('Specify a token to use the OpenAI API. You can generate a token in your personal account using the link %s', 'trx_addons'),
													apply_filters( 'trx_addons_filter_openai_api_key_url',
																	'<a href="https://beta.openai.com/account/api-keys" target="_blank">https://beta.openai.com/account/api-keys</a>'
																)
												),
										'trx_addons_kses_content'
									),
					"std" => "",
					"type" => "text"
				),
				'ai_helper_model_openai' => array(
					"title" => esc_html__('Open AI model', 'trx_addons'),
					"desc" => wp_kses_data( __('Select a model to use with OpenAI API', 'trx_addons') ),
					"std" => "gpt-3.5-turbo",
					"options" => apply_filters( 'trx_addons_filter_ai_helper_list_models', Lists::get_list_ai_models(), 'openai' ),
					"type" => "select",
					"dependency" => array(
						"ai_helper_token_openai" => array('not_empty')
					),
				),
				'ai_helper_temperature_openai' => array(
					"title" => esc_html__('Temperature', 'trx_addons'),
					"desc" => wp_kses_data( __('Select a temperature to use with OpenAI API queries in the editor.', 'trx_addons') )
							. '<br />'
							. wp_kses_data( __('What sampling temperature to use, between 0 and 2. Higher values like 0.8 will make the output more random, while lower values like 0.2 will make it more focused and deterministic.', 'trx_addons') ),
					"std" => 1.0,
					"min" => 0,
					"max" => 2.0,
					"step" => 0.1,
					"type" => "slider",
					"dependency" => array(
						"ai_helper_token_openai" => array('not_empty')
					),
				),

				// Stabble Diffusion API settings
				'ai_helper_info_stabble_diffusion' => array(
					"title" => esc_html__('Stabble Diffusion', 'trx_addons'),
					"desc" => wp_kses_data( __("Settings of the AI Helper for Stabble Diffusion API", 'trx_addons') )
							. ( ! empty( $log_sd ) ? wp_kses( $log_sd, 'trx_addons_kses_content' ) : '' ),
					"type" => "info"
				),
				'ai_helper_token_stabble_diffusion' => array(
					"title" => esc_html__('Stabble Diffusion token', 'trx_addons'),
					"desc" => wp_kses( sprintf(
													__('Specify a token to use the Stabble Diffusion API. You can generate a token in your personal account using the link %s', 'trx_addons'),
													apply_filters( 'trx_addons_filter_stabble_diffusion_api_key_url',
																	'<a href="https://stablediffusionapi.com/settings/api" target="_blank">https://stablediffusionapi.com/settings/api</a>'
																)
												),
										'trx_addons_kses_content'
									),
					"std" => "",
					"type" => "text"
				),
				'ai_helper_guidance_scale_stabble_diffusion' => array(
					"title" => esc_html__('Guidance scale', 'trx_addons'),
					"desc" => wp_kses_data( __('Scale for classifier-free guidance.', 'trx_addons') ),
					"std" => 7.5,
					"min" => 1,
					"max" => 20,
					"step" => 0.1,
					"type" => "slider",
					"dependency" => array(
						"ai_helper_token_stabble_diffusion" => array('not_empty')
					),
				),
				'ai_helper_interference_steps_stabble_diffusion' => array(
					"title" => esc_html__('Interference steps', 'trx_addons'),
					"desc" => wp_kses_data( __('Number of denoising steps. Available values: 21, 31, 41, 51.', 'trx_addons') ),
					"std" => 21,
					"min" => 21,
					"max" => 51,
					"step" => 10,
					"type" => "slider",
					"dependency" => array(
						"ai_helper_token_stabble_diffusion" => array('not_empty')
					),
				),
				'ai_helper_models_stabble_diffusion' => array(
					"title" => esc_html__("List of available models", 'trx_addons'),
					"desc" => wp_kses(
								sprintf(
									__("Specify id and name (title) for the each new model. A complete list of available models can be found at %s", 'trx_addons'),
									'<a href="https://stablediffusionapi.com/models" target="_blank">https://stablediffusionapi.com/models</a>'
								),
								'trx_addons_kses_content'
							),
					"dependency" => array(
						"ai_helper_token_stabble_diffusion" => array('not_empty')
					),
					"clone" => true,
					"std" => trx_addons_list_from_array( Lists::get_default_sd_models() ),
					"type" => "group",
					"fields" => array(
						"id" => array(
							"title" => esc_html__("Model ID", 'trx_addons'),
							"class" => "trx_addons_column-1_2",
							"std" => "",
							"type" => "text"
						),
						"title" => array(
							"title" => esc_html__("Title", 'trx_addons'),
							"class" => "trx_addons_column-1_2",
							"std" => "",
							"type" => "text"
						),
					)
				),

				// Image Generator
				'ai_helper_sc_igenerator_info' => array(
					"title" => esc_html__('Shortcode IGenerator', 'trx_addons'),
					"type" => "info"
				),
				'ai_helper_sc_igenerator_limits' => array(
					"title" => esc_html__('Use limits', 'trx_addons'),
					"desc" => wp_kses_data( __('Use limits (per hour and per visitor) when generating images.', 'trx_addons') ),
					"std" => "1",
					"type" => "switch"
				),
				'ai_helper_sc_igenerator_limit_per_hour' => array(
					"title" => esc_html__('Images per 1 hour', 'trx_addons'),
					"desc" => wp_kses_data( __('How many images can all visitors generate in 1 hour?', 'trx_addons') ),
					"std" => 12,
					"min" => 0,
					"max" => 1000,
					"type" => "slider",
					"dependency" => array(
						"ai_helper_sc_igenerator_limits" => array(1)
					),
				),
				'ai_helper_sc_igenerator_limit_per_visitor' => array(
					"title" => esc_html__('Requests from 1 visitor', 'trx_addons'),
					"desc" => wp_kses_data( __('How many requests can send a single visitor in 1 hour?', 'trx_addons') ),
					"std" => 2,
					"min" => 0,
					"max" => 100,
					"type" => "slider",
					"dependency" => array(
						"ai_helper_sc_igenerator_limits" => array(1)
					),
				),

				// Text Generator
				'ai_helper_sc_tgenerator_info' => array(
					"title" => esc_html__('Shortcode TGenerator', 'trx_addons'),
					"type" => "info"
				),
				'ai_helper_sc_tgenerator_temperature' => array(
					"title" => esc_html__('Temperature', 'trx_addons'),
					"desc" => wp_kses_data( __('What sampling temperature to use, between 0 and 2. Higher values like 0.8 will make the output more random, while lower values like 0.2 will make it more focused and deterministic.', 'trx_addons') ),
					"std" => 1,
					"min" => 0,
					"max" => 2,
					"step" => 0.1,
					"type" => "slider"
				),
				'ai_helper_sc_tgenerator_limits' => array(
					"title" => esc_html__('Use limits', 'trx_addons'),
					"desc" => wp_kses_data( __('Use limits (per request, per hour and per visitor) when generating text.', 'trx_addons') ),
					"std" => "1",
					"type" => "switch"
				),
				'ai_helper_sc_tgenerator_limit_per_request' => array(
					"title" => esc_html__('Max. tokens per 1 request', 'trx_addons'),
					"desc" => wp_kses_data( __('How many tokens can be used per one request to the API?', 'trx_addons') ),
					"std" => 1000,
					"min" => 0,
					"max" => 32000,
					"step" => 100,
					"type" => "slider",
					"dependency" => array(
						"ai_helper_sc_tgenerator_limits" => array(1)
					),
				),
				'ai_helper_sc_tgenerator_limit_per_hour' => array(
					"title" => esc_html__('Requests per 1 hour', 'trx_addons'),
					"desc" => wp_kses_data( __('How many requests can be processed for all visitors in 1 hour?', 'trx_addons') ),
					"std" => 8,
					"min" => 0,
					"max" => 1000,
					"type" => "slider",
					"dependency" => array(
						"ai_helper_sc_tgenerator_limits" => array(1)
					),
				),
				'ai_helper_sc_tgenerator_limit_per_visitor' => array(
					"title" => esc_html__('Requests from 1 visitor', 'trx_addons'),
					"desc" => wp_kses_data( __('How many requests can send a single visitor in 1 hour?', 'trx_addons') ),
					"std" => 2,
					"min" => 0,
					"max" => 100,
					"type" => "slider",
					"dependency" => array(
						"ai_helper_sc_tgenerator_limits" => array(1)
					),
				),

				// Chat
				'ai_helper_sc_chat_info' => array(
					"title" => esc_html__('Shortcode AI Chat', 'trx_addons'),
					"type" => "info"
				),
				'ai_helper_sc_chat_temperature' => array(
					"title" => esc_html__('Temperature', 'trx_addons'),
					"desc" => wp_kses_data( __('What sampling temperature to use, between 0 and 2. Higher values like 0.8 will make the output more random, while lower values like 0.2 will make it more focused and deterministic.', 'trx_addons') ),
					"std" => 1,
					"min" => 0,
					"max" => 2,
					"step" => 0.1,
					"type" => "slider"
				),
				'ai_helper_sc_chat_limits' => array(
					"title" => esc_html__('Use limits', 'trx_addons'),
					"desc" => wp_kses_data( __('Use limits (per request, per hour and per visitor) when chatting.', 'trx_addons') ),
					"std" => "1",
					"type" => "switch"
				),
				'ai_helper_sc_chat_limit_per_request' => array(
					"title" => esc_html__('Max. tokens per 1 request', 'trx_addons'),
					"desc" => wp_kses_data( __('How many tokens can be used per one request to the chat?', 'trx_addons') ),
					"std" => 1000,
					"min" => 0,
					"max" => 32000,
					"step" => 100,
					"type" => "slider",
					"dependency" => array(
						"ai_helper_sc_chat_limits" => array(1)
					),
				),
				'ai_helper_sc_chat_limit_per_hour' => array(
					"title" => esc_html__('Requests per 1 hour', 'trx_addons'),
					"desc" => wp_kses_data( __('How many requests can be processed for all visitors in 1 hour?', 'trx_addons') ),
					"std" => 80,
					"min" => 0,
					"max" => 1000,
					"type" => "slider",
					"dependency" => array(
						"ai_helper_sc_chat_limits" => array(1)
					),
				),
				'ai_helper_sc_chat_limit_per_visitor' => array(
					"title" => esc_html__('Requests from 1 visitor', 'trx_addons'),
					"desc" => wp_kses_data( __('How many requests can send a single visitor in 1 hour?', 'trx_addons') ),
					"std" => 10,
					"min" => 0,
					"max" => 100,
					"type" => "slider",
					"dependency" => array(
						"ai_helper_sc_chat_limits" => array(1)
					),
				),
			) ) );

			return $options;
		}

		/**
		 * Fix option params in the ThemeREX Addons Options
		 * 
		 * @hooked trx_addons_filter_before_show_options
		 *
		 * @param array $options  Array of options
		 * 
		 * @return array  	  Modified array of options
		 */
		function fix_options( $options ) {
			$max_tokens = OpenAI::get_max_tokens();
			if ( ! empty( $options['ai_helper_sc_tgenerator_limit_per_request']['std'] ) && $options['ai_helper_sc_tgenerator_limit_per_request']['std'] > $max_tokens ) {
				$options['ai_helper_sc_tgenerator_limit_per_request']['std'] = $max_tokens;
			}
			if ( ! empty( $options['ai_helper_sc_tgenerator_limit_per_request']['val'] ) && $options['ai_helper_sc_tgenerator_limit_per_request']['val'] > $max_tokens ) {
				$options['ai_helper_sc_tgenerator_limit_per_request']['val'] = $max_tokens;
			}
			if ( ! empty( $options['ai_helper_sc_tgenerator_limit_per_request']['max'] ) ) {
				$options['ai_helper_sc_tgenerator_limit_per_request']['max'] = $max_tokens;
			}
			if ( ! empty( $options['ai_helper_sc_chat_limit_per_request']['std'] ) && $options['ai_helper_sc_chat_limit_per_request']['std'] > $max_tokens ) {
				$options['ai_helper_sc_chat_limit_per_request']['std'] = $max_tokens;
			}
			if ( ! empty( $options['ai_helper_sc_chat_limit_per_request']['val'] ) && $options['ai_helper_sc_chat_limit_per_request']['val'] > $max_tokens ) {
				$options['ai_helper_sc_chat_limit_per_request']['val'] = $max_tokens;
			}
			if ( ! empty( $options['ai_helper_sc_chat_limit_per_request']['max'] ) ) {
				$options['ai_helper_sc_chat_limit_per_request']['max'] = $max_tokens;
			}
			return $options;
		}

		/**
		 * Clear some addon specific options before export
		 * 
		 * @hooked trx_addons_filter_export_options
		 * 
		 * @param array $options  Array of options
		 * 
		 * @return array  	  Modified array of options
		 */
		 function remove_token_from_export( $options ) {
			if ( isset( $options['trx_addons_ai_helper_log'] ) ) {
				unset( $options['trx_addons_ai_helper_log'] );
			}
			if ( ! empty( $options['trx_addons_options']['ai_helper_token_openai'] ) ) {
				$options['trx_addons_options']['ai_helper_token_openai'] = '';
			}
			if ( ! empty( $options['trx_addons_options']['ai_helper_token_stabble_diffusion'] ) ) {
				$options['trx_addons_options']['ai_helper_token_stabble_diffusion'] = '';
			}
			return $options;
		}
	}
}
