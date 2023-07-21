<?php
namespace TrxAddons\AiHelper;

if ( ! class_exists( 'Lists' ) ) {

	/**
	 * Return arrays with the lists used in the addon
	 */
	class Lists {

		/**
		 * Constructor
		 */
		function __construct() {
		}

		/* OPEN AI API
		--------------------------------------------------------------------------------------- */

		/**
		 * Return a list of models for Open AI with max tokens for each model
		 * 
		 * @access public
		 * @static
		 * 
		 * @return array  	  The list of models for Open AI
		 */
		static function get_ai_models() {
			return apply_filters( 'trx_addons_filter_ai_helper_ai_models', array(
				'gpt-3.5-turbo' => array( 
					'title' => esc_html__( 'GPT 3.5 turbo', 'trx_addons' ),
					'max_tokens' => 4000,
				),
				'gpt-4' => array(
					'title' => esc_html__( 'GPT 4', 'trx_addons' ),
					'max_tokens' => 32000,
				)
			) );
		}

		/**
		 * Return a list of models for Open AI
		 * 
		 * @access public
		 * @static
		 * 
		 * @return array  	  The list of models for Open AI
		 */
		static function get_list_ai_models() {
			return apply_filters( 'trx_addons_filter_ai_helper_list_ai_models', trx_addons_array_from_list( self::get_ai_models() ) );
		}

		/**
		 * Return a list of AI Commands
		 * 
		 * @access public
		 * @static
		 * 
		 * @return array  	  The list of AI Commands
		 */
		static function get_list_ai_commands() {
			return apply_filters( 'trx_addons_filter_ai_helper_list_ai_commands', array(

				'/-content' => array(
					'title' => esc_html__( '- Content -', 'trx_addons' )
				),
				'write_blog' => array(
					'title' => esc_html__( 'Blog post', 'trx_addons' ),
					'prompt' => esc_html__( 'Write a blog post about', 'trx_addons' )
				),
				'write_social' => array(
					'title' => esc_html__( 'Social media post', 'trx_addons' ),
					'prompt' => esc_html__( 'Write a social media post about', 'trx_addons' ),
				),
				'write_outline' => array(
					'title' => esc_html__( 'Outline', 'trx_addons' ),
					'prompt' => esc_html__( 'Write an outline about', 'trx_addons' ),
				),
				'write_press' => array(
					'title' => esc_html__( 'Press Release', 'trx_addons' ),
					'prompt' => esc_html__( 'Write a press release about', 'trx_addons' ),
				),
				'write_creative' => array(
					'title' => esc_html__( 'Creative story', 'trx_addons' ),
					'prompt' => esc_html__( 'Write a creative story about', 'trx_addons' ),
				),
				'write_essay' => array(
					'title' => esc_html__( 'Essay', 'trx_addons' ),
					'prompt' => esc_html__( 'Write an essay about', 'trx_addons' ),
				),
				'write_poem' => array(
					'title' => esc_html__( 'Poem', 'trx_addons' ),
					'prompt' => esc_html__( 'Write a poem about', 'trx_addons' ),
				),
				'write_todo' => array(
					'title' => esc_html__( 'To-Do list', 'trx_addons' ),
					'prompt' => esc_html__( 'Write a todo list about', 'trx_addons' ),
				),
				'write_agenda' => array(
					'title' => esc_html__( 'Meeting agenda', 'trx_addons' ),
					'prompt' => esc_html__( 'Write a meeting agenda about', 'trx_addons' ),
				),
				'write_pros' => array(
					'title' => esc_html__( 'Pros and Cons list', 'trx_addons' ),
					'prompt' => esc_html__( 'Write a pros and cons list about', 'trx_addons' ),
				),
				'write_job' => array(
					'title' => esc_html__( 'Job description', 'trx_addons' ),
					'prompt' => esc_html__( 'Write a job description about', 'trx_addons' ),
				),
				'write_sales' => array(
					'title' => esc_html__( 'Sales email', 'trx_addons' ),
					'prompt' => esc_html__( 'Write a sales email about', 'trx_addons' ),
				),
				'write_recruiting' => array(
					'title' => esc_html__( 'Recruiting email', 'trx_addons' ),
					'prompt' => esc_html__( 'Write a recruiting email about', 'trx_addons' ),
				),
				'write_brainstorm' => array(
					'title' => esc_html__( 'Brainstorm ideas', 'trx_addons' ),
					'prompt' => esc_html__( 'Brainstorm ideas on', 'trx_addons' ),
				),

				'/-process' => array(
					'title' => esc_html__( '- Text processing -', 'trx_addons' ),
				),
				'process_title' => array(
					'title' => esc_html__( 'Generate a post title', 'trx_addons' ),
					'prompt' => esc_html__( 'Write a post title about', 'trx_addons' ),
					'variation_name' => esc_html__( 'post title', 'trx_addons' ),
					'variations' => 5,
				),
				'process_excerpt' => array(
					'title' => esc_html__( 'Generate a post excerpt', 'trx_addons' ),
					'prompt' => esc_html__( 'Write a post excerpt about', 'trx_addons' ),
					'variation_name' => esc_html__( 'post excerpt', 'trx_addons' ),
					'variations' => 3,
				),
				'process_heading' => array(
					'title' => esc_html__( 'Generate a text heading', 'trx_addons' ),
					'prompt' => esc_html__( 'Write a text heading', 'trx_addons' ),
					'variation_name' => esc_html__( 'text heading', 'trx_addons' ),
					'variations' => 5,
				),
				'process_continue' => array(
					'title' => esc_html__( 'Continue writing', 'trx_addons' ),
					'prompt' => esc_html__( 'Write a continuation of the text', 'trx_addons' ),
				),
				'process_longer' => array(
					'title' => esc_html__( 'Make longer', 'trx_addons' ),
					'prompt' => esc_html__( 'Make text longer', 'trx_addons' ),
				),
				'process_shorter' => array(
					'title' => esc_html__( 'Make shorter', 'trx_addons' ),
					'prompt' => esc_html__( 'Make text shorter', 'trx_addons' ),
				),
				'process_summarize' => array(
					'title' => esc_html__( 'Summarize', 'trx_addons' ),
					'prompt' => esc_html__( 'Summarize text', 'trx_addons' ),
					'variation_name' => esc_html__( 'text summary', 'trx_addons' ),
					'variations' => 3,
				),
				'process_explain' => array(
					'title' => esc_html__( 'Explain', 'trx_addons' ),
					'prompt' => esc_html__( 'Explain text', 'trx_addons' ),
				),
				'process_spell' => array(
					'title' => esc_html__( 'Spell check', 'trx_addons' ),
					'prompt' => esc_html__( 'Fix spelling and grammar', 'trx_addons' ),
				),
				'process_tone' => array(
					'title' => esc_html__( 'Change tone', 'trx_addons' ),
					'prompt' => esc_html__( 'Change a tone of the text to %tone%', 'trx_addons' ),
				),
				'process_translate' => array(
					'title' => esc_html__( 'Translate', 'trx_addons' ),
					'prompt' => esc_html__( 'Translate a text to %language%', 'trx_addons' ),
				),
			) );
		}

		/**
		 * Return a list of parts of text used as a source (base) for AI
		 * 
		 * @access public
		 * @static
		 * 
		 * @return array  	  The list of parts of text
		 */
		static function get_list_ai_bases() {
			return apply_filters( 'trx_addons_filter_ai_helper_list_ai_base', array(
				'prompt' => esc_html__( 'Prompt', 'trx_addons' ),
				'title' => esc_html__( 'Post title', 'trx_addons' ),
				'excerpt' => esc_html__( 'Post excerpt', 'trx_addons' ),
				'content' => esc_html__( 'Post content', 'trx_addons' ),
				'selected' => esc_html__( 'Selected text', 'trx_addons' ),
			) );
		}

		/**
		 * Return a list of text tones for AI
		 * 
		 * @access public
		 * @static
		 * 
		 * @return array  	  The list of text tones
		 */
		static function get_list_ai_text_tones() {
			return apply_filters( 'trx_addons_filter_ai_helper_list_ai_text_tones', array(
				'normal' => esc_html__( 'Normal', 'trx_addons' ),
				'professional' => esc_html__( 'Professional', 'trx_addons' ),
				'casual' => esc_html__( 'Casual', 'trx_addons' ),
				'confident' => esc_html__( 'Confident', 'trx_addons' ),
				'friendly' => esc_html__( 'Friendly', 'trx_addons' ),
				'straightforward' => esc_html__( 'Straightforward', 'trx_addons' ),
			) );
		}

		/**
		 * Return a list of text languages for AI translations
		 * 
		 * @access public
		 * @static
		 * 
		 * @return array  	  The list of languages
		 */
		static function get_list_ai_text_languages() {
			return apply_filters( 'trx_addons_filter_ai_helper_list_ai_translations', array(
				'English' => esc_html__( 'English', 'trx_addons' ),
				'French' => esc_html__( 'French', 'trx_addons' ),
				'German' => esc_html__( 'German', 'trx_addons' ),
				'Spanish' => esc_html__( 'Spanish', 'trx_addons' ),
				'Portuguese' => esc_html__( 'Portuguese', 'trx_addons' ),
				'Italian' => esc_html__( 'Italian', 'trx_addons' ),
				'Dutch' => esc_html__( 'Dutch', 'trx_addons' ),
				'Ukrainian' => esc_html__( 'Ukrainian', 'trx_addons' ),
				'Chinese' => esc_html__( 'Chinese', 'trx_addons' ),
				'Japanese' => esc_html__( 'Japanese', 'trx_addons' ),
				'Korean' => esc_html__( 'Korean', 'trx_addons' ),
			) );
		}

		/**
		 * Return a list of image prompt templates for AI
		 * 
		 * @access public
		 * @static
		 * 
		 * @return array  	  The list of image prompt templates
		 */
		static function get_list_ai_image_templates() {
			return apply_filters( 'trx_addons_filter_ai_helper_list_ai_image_templates', array(
				'default'   => array( 'title' => esc_html__( 'Default', 'trx_addons' ), 'prompt' => '' ),
				'steampunk' => array( 'title' => esc_html__( 'Steampunk Architecture', 'trx_addons' ), 'prompt' => esc_html__( 'steampunk architecture, exterior view, award-winning architectural photography from magazine, trees, theater', 'trx_addons' ) ),
				'ghilbi' => array( 'title' => esc_html__( 'Ghilbi Inspired' ), 'prompt' => esc_html__( 'japan, tokyo, trees, izakaya, anime oil painting, high resolution, ghibli inspired, 4k', 'trx_addons' ) ),
				'modern' => array( 'title' => esc_html__( 'Modern Illustration' ), 'prompt' => esc_html__( 'illustration of a ..., modern design, for the web, cute, happy, 4k, high resolution, trending in artstation', 'trx_addons' ) ),
			) );
		}

		/**
		 * Return a list of image sizes for AI
		 * 
		 * @access public
		 * @static
		 * 
		 * @return array  	  The list of image sizes
		 */
		static function get_list_ai_image_sizes() {
			return apply_filters( 'trx_addons_filter_ai_helper_list_ai_image_sizes', array(
				'256x256'   => esc_html__( ' 256 x  256', 'trx_addons' ),
				'512x512'   => esc_html__( ' 512 x  512', 'trx_addons' ),
				'1024x1024' => esc_html__( '1024 x 1024', 'trx_addons' ),
			) );
		}

		/**
		 * Return a list of image models for AI
		 * 
		 * @access public
		 * @static
		 * 
		 * @return array  	  The list of image models
		 */
		static function get_list_ai_image_models() {
			$models = array(
				'openai/default' => esc_html__( 'Open AI', 'trx_addons' ),
			);
			$sd_models = self::get_sd_models();
			if ( ! isset( $sd_models['default'] ) ) {
				$sd_models['default'] = esc_html__( 'Stabble Diffusion default', 'trx_addons' );
			}
			foreach ( $sd_models as $k => $v ) {
				$models[ 'stabble-diffusion/' . $k ] = $v['title'];
			}
			return apply_filters( 'trx_addons_filter_ai_helper_list_ai_image_models', $models );
		}



		/* STABBLE DIFFUSION (SD) API
		--------------------------------------------------------------------------------------- */

		/**
		 * Return a default list of models for SD
		 * 
		 * @access public
		 * @static
		 * 
		 * @return array  	  The list of models for SD
		 */
		static function get_default_sd_models() {
			return apply_filters( 'trx_addons_filter_ai_helper_default_sd_models', array(
				'default' => array( 
					'title' => esc_html__( 'Stabble Diffusion default', 'trx_addons' ),
				),
				'midjourney' => array(
					'title' => esc_html__( 'MidJourney', 'trx_addons' ),
				),
				'realistic-vision-v13' => array(
					'title' => esc_html__( 'Realistic Vision V1.3', 'trx_addons' ),
				),
				'dream-shaper-8797' => array(
					'title' => esc_html__( 'Dream Shaper', 'trx_addons' ),
				),
				'protogen-3.4' => array(
					'title' => esc_html__( 'Protogen x3.4', 'trx_addons' ),
				),
				'f222-diffusion' => array(
					'title' => esc_html__( 'F222', 'trx_addons' ),
				),
				'portraitplus-diffusion' => array(
					'title' => esc_html__( 'Portrait+', 'trx_addons' ),
				),
				'perfect-deli-appfact' => array(
					'title' => esc_html__( 'perfect deli appfactory', 'trx_addons' ),
				),
				'deliberateappfactory' => array(
					'title' => esc_html__( 'Deliberate_appfactory', 'trx_addons' ),
				),
				'anything-v5' => array(
					'title' => esc_html__( 'Anything V5', 'trx_addons' ),
				),
				'anything-v4' => array(
					'title' => esc_html__( 'Anything V4', 'trx_addons' ),
				),
				'anything-v3' => array(
					'title' => esc_html__( 'Anything V3', 'trx_addons' ),
				),
				'gta5-artwork-diffusi' => array(
					'title' => esc_html__( 'GTA5 Artwork Diffusion', 'trx_addons' ),
				),
				'wifu-diffusion' => array(
					'title' => esc_html__( 'Wifu Diffusion', 'trx_addons' ),
				),
			) );
		}

		/**
		 * Return a list of models for SD with max tokens for each model
		 * 
		 * @access public
		 * @static
		 * 
		 * @return array  	  The list of models for SD
		 */
		static function get_sd_models() {
			$models = trx_addons_get_option( 'ai_helper_models_stabble_diffusion' );
			if ( ! is_array( $models ) || empty( $models ) ) {
				$models = self::get_default_sd_models();
			} else {
				$new_models = array();
				foreach ( $models as $k => $v ) {
					if ( ! empty( $v['id'] ) ) {
						$new_models[ $v['id'] ] = $v;
						unset( $new_models[ $v['id'] ]['id'] );
					}
				}
				$models = $new_models;
			}
			return $models;
		}

		/**
		 * Return a list of models for SD
		 * 
		 * @access public
		 * @static
		 * 
		 * @return array  	  The list of models for SD
		 */
		static function get_list_sd_models() {
			return apply_filters( 'trx_addons_filter_ai_helper_list_sd_models', trx_addons_array_from_list( self::get_sd_models() ) );
		}
	}
}
