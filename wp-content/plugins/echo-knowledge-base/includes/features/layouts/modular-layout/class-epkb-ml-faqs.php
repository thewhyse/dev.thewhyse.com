<?php

/**
 *  Outputs the FAQs module for Modular Layout.
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 */
class EPKB_ML_FAQs {

	private $kb_config;

	function __construct( $kb_config ) {
		$this->kb_config = $kb_config;
	}

	public function display_faqs() {

		$faqs_kb_id = $this->kb_config['ml_faqs_kb_id'];
		$faqs_kb_config = epkb_get_instance()->kb_config_obj->get_kb_config( $faqs_kb_id );

		$category_seq_data = EPKB_Utilities::get_kb_option( $faqs_kb_id, EPKB_Categories_Admin::KB_CATEGORIES_SEQ_META, array(), true );
		$articles_seq_data = EPKB_Utilities::get_kb_option( $faqs_kb_id, EPKB_Articles_Admin::KB_ARTICLES_SEQ_META, array(), true );

		// for WPML filter categories and articles given active language
		if ( EPKB_Utilities::is_wpml_enabled( $faqs_kb_config ) ) {
			$category_seq_data = EPKB_WPML::apply_category_language_filter( $category_seq_data );
			$articles_seq_data = EPKB_WPML::apply_article_language_filter( $articles_seq_data );
		}

		$stored_ids_obj = new EPKB_Categories_Array( $category_seq_data ); // normalizes the array as well
		$allowed_categories_ids = $stored_ids_obj->get_all_keys();

		// No categories found - message only for admins
		if ( empty( $allowed_categories_ids ) && current_user_can( 'manage_options' ) ) {
			esc_html_e( 'FAQs Module: No categories with articles found.', 'echo-knowledge-base' );
			return;
		}

		// remove epkb filter
		remove_filter( 'the_content', array( 'EPKB_Layouts_Setup', 'get_kb_page_output_hook' ), 99999 );

		// for empty categories parameters show all
		$included_categories = empty( $this->kb_config['ml_faqs_category_ids'] )
			? array_keys( $allowed_categories_ids )
			: explode( ',', $this->kb_config['ml_faqs_category_ids'] );    ?>

		<div class="epkb-faqs-container <?php echo esc_html( $this->kb_config['ml_faqs_custom_css_class'] ) . ' epkb-faqs-shortcode-preset-' . esc_html( $this->kb_config['ml_faqs_preset'] ); ?>">  <?php

			foreach( $included_categories as $include_category_id ) {

				if ( empty( $articles_seq_data[$include_category_id] ) ) {
					continue;
				}

				if ( empty( $allowed_categories_ids[$include_category_id] ) ) {
					continue;
				}

				foreach ( $articles_seq_data[$include_category_id] as $article_id => $article_title ) {

					// category title/description
					if ( $article_id == 0 || $article_id == 1 ) {
						continue;
					}

					// exclude linked articles
					$article = get_post( $article_id );

					// disallow article that failed to retrieve
					if ( empty( $article ) || empty( $article->post_status ) ) {
						unset( $articles_seq_data[$include_category_id][$article_id] );
						continue;
					}

					if ( EPKB_Utilities::is_link_editor( $article ) ) {
						unset( $articles_seq_data[$include_category_id][$article_id] );
						continue;
					}

					// exclude not allowed
					if ( ! EPKB_Utilities::is_article_allowed_for_current_user( $article_id ) ) {
						unset( $articles_seq_data[$include_category_id][$article_id] );
					}
				}

				// not empty term but with hidden articles for the user
				if ( empty( $articles_seq_data[$include_category_id] ) ) {
					continue;
				}   ?>

				<div class="epkb-faqs-cat-container" id="epkb-faqs-cat-<?php echo $include_category_id; ?>">
					<div class="epkb-faqs__cat-header">
						<h3><?php echo esc_html( $articles_seq_data[$include_category_id][0] ); ?></h3>
					</div>  <?php

					foreach( $articles_seq_data[$include_category_id] as $article_id => $article_title ) {

						if ( $article_id == 0 || $article_id == 1 ) {
							continue;
						}

						// second call is cached by wp core, will not create db query
						$article = get_post( $article_id );

						// disallow article that failed to retrieve
						if ( empty( $article ) || empty( $article->post_status ) ) {
							continue;
						}

						// ignore password-protected pages
						if ( ! empty( $article->post_password ) ) {
							continue;
						}

						$post_content = '';
						if ( $this->kb_config['ml_faqs_content_mode'] == 'content' ) {
							$post_content = $article->post_content;
						}

						if ( $this->kb_config['ml_faqs_content_mode'] == 'excerpt' ) {
							$post_content = $article->post_excerpt;
						}   ?>

						<div class="epkb-faqs__item-container" id="epkb-faqs-article-<?php echo $article->ID; ?>">
							<div class="epkb-faqs__item__question">
								<div class="epkb-faqs__item__question__icon epkbfa epkbfa-plus-square"></div>
								<div class="epkb-faqs__item__question__icon epkbfa epkbfa-minus-square"></div>
								<div class="epkb-faqs__item__question__text"><?php echo get_the_title( $article ); ?></div>
							</div>
							<div class="epkb-faqs__item__answer">
								<div class="epkbs-faqs__item__answer__text">    <?php
									$content = apply_filters( 'the_content', $post_content );
									$content = str_replace( ']]>', ']]&gt;', $content );
									echo $content;  ?>
								</div>
							</div>
						</div>  <?php
					}   ?>
				</div>  <?php
			} ?>
		</div>  <?php

		// add epkb filter back
		add_filter( 'the_content', array( 'EPKB_Layouts_Setup', 'get_kb_page_output_hook' ), 99999 );
	}

	/**
	 * Returns inline styles for FAQs Module
	 *
	 * @param $kb_config
	 * @return string
	 */
	public static function get_inline_styles( $kb_config ) {

		$output = '
		/* CSS for FAQs Module
		-----------------------------------------------------------------------*/';

		return $output;
	}
}