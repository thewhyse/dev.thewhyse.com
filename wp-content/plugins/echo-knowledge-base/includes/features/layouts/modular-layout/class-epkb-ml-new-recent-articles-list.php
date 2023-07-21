<?php

/**
 *  Outputs the New and Recent Articles List module for Modular Layout.
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 */
class EPKB_ML_New_Recent_Articles_List {

	private $kb_config;

	function __construct( $kb_config ) {
		$this->kb_config = $kb_config;
	}

	/**
	 * Display Articles ( Recent & Newest )
	 *
	 */
	public function display_articles_list( $kb_config ) {

		$newest_articles = $this->execute_search( 'date' );
		$recent_articles = $this->execute_search( 'modified' );

		//var_dump($kb_config);
		$category_articles_layout = esc_attr( $kb_config['ml_categories_articles_layout'] );

		$designClass = '';
		$layoutClass = 'default';
        if ( $kb_config['ml_articles_list_match_style_toggle'] == 'on' ) {
	        switch ( $category_articles_layout ) {
		        case "classic":
			        $layoutClass = 'classic';
			        $designClass = '-design-1';
			        //$designClass = '-design-' . $kb_config['ml_categories_articles_layout_product_design'];
			        break;
		        case "product":
			        $layoutClass = 'product';
			        $designClass = '-design-1';
			        //$designClass = '-design-' . $kb_config['ml_categories_articles_layout_classic_design'];
			        break;
		        default:
			        break;
	        }
        } ?>

        <div id="epkb-ml-article-list-<?php echo esc_attr( $layoutClass ) . esc_attr( $designClass ); ?>" class="epkb-ml-article-list-container">
            <!-- Newest Articles -->
            <section id="epkb-ml-newest-articles" class="epkb-ml-article-section">
                <div class="epkb-ml-article-section__head"><?php esc_html_e( 'Newest articles', 'echo-knowledge-base' ); ?></div>
                <div class="epkb-ml-article-section__body">
                    <ul class="epkb-ml-articles-list">    <?php
				        foreach ( $newest_articles as $article ) {  ?>
                            <li><?php $this->single_article_link( $article ); ?></li><?php
				        }   ?>
                    </ul>
                </div>
            </section>

            <!-- Recent Articles -->
            <section id="epkb-ml-recent-articles" class="epkb-ml-article-section">
                <div class="epkb-ml-article-section__head"><?php esc_html_e( 'Recently updated', 'echo-knowledge-base' ); ?></div>
                <div class="epkb-ml-article-section__body">
                    <ul class="epkb-ml-articles-list">    <?php
				        foreach ( $recent_articles as $article ) {  ?>
                            <li><?php $this->single_article_link( $article ); ?></li><?php
				        }   ?>
                    </ul>
                </div>
            </section>
        </div>        <?php
	}

	/**
	 * Display a link to a KB article.
	 *
	 * @param $article
	 */
	private function single_article_link( $article ) {

		$article_url = get_permalink( $article->ID );
		if ( empty( $article_url ) ) {
			return;
		}

		// TODO Move to Style setting tag, <style id="epkb-module-layout-settings">
		$article_color = EPKB_Utilities::get_inline_style( 'color:: ' . 'article_font_color', $this->kb_config );
		$icon_color    = EPKB_Utilities::get_inline_style( 'color:: ' . 'article_icon_color', $this->kb_config );

		// handle any add-on content TODO: apply
		if ( has_filter( 'eckb_single_article_filter' ) ) {
			$result = apply_filters( 'eckb_single_article_filter', $article->ID, array( $this->kb_config['id'], $article->post_title, $article_color, $icon_color ) );
			if ( ! empty($result) && $result === true ) {
				return;
			}
		} ?>

		<a href="<?php echo esc_url( $article_url ); ?>" class="epkb-ml-article-container" data-kb-article-id="<?php echo esc_attr( $article->ID ); ?>">
			<span class="epkb-article-inner">
				<span class="epkb-article__icon ep_font_icon_document" aria-hidden="true"></span>
				<span class="epkb-article__text"><?php echo esc_html( $article->post_title ); ?></span>
			</span>
		</a>    <?php
	}

	/**
	 * Call WP query to get matching terms (any term OR match)
	 *
	 * @return array
	 */
	private function execute_search( $order_by ) {

		$post_status_search = EPKB_Utilities::is_amag_on() ? array( 'publish', 'private' ) : array( 'publish' );

		$result = array();
		$search_params = array(
			'post_type'             => EPKB_KB_Handler::get_post_type( $this->kb_config['id'] ),
			'post_status'           => $post_status_search,
			'ignore_sticky_posts'   => true,    // sticky posts will not show at the top
			'posts_per_page'        => EPKB_Utilities::is_amag_on() ? -1 : $this->kb_config['ml_articles_list_nof_articles_displayed'],  // limit search results
			'no_found_rows'         => true,    // query only posts_per_page rather than finding total nof posts for pagination etc.
			'orderby'               => $order_by,
			'order'                 => 'DESC'
		);

		$found_posts = new WP_Query( $search_params );
		if ( ! empty( $found_posts->posts ) ) {
			$result = $found_posts->posts;
			wp_reset_postdata();
		}

		// limit the number of articles by config settings
		if ( EPKB_Utilities::is_amag_on() && count( $result ) > $this->kb_config['ml_articles_list_nof_articles_displayed'] ) {
			$result = array_splice( $result, 0, $this->kb_config['ml_articles_list_nof_articles_displayed'] );
		}

		return $result;
	}

	/**
	 * Returns inline styles for New and Recent Articles List Module
	 *
	 * @param $kb_config
	 * @return string
	 */
	public static function get_inline_styles( $kb_config ) {

		$output = '
		/* CSS for Articles List Module
		-----------------------------------------------------------------------*/
		';
		// If Match Style "on" pull the settings from the Categories & Articles module.
		if ( $kb_config['ml_articles_list_match_style_toggle'] == 'on' ) {
			$output .= '
			#epkb-ml-article-list-product-design-1 .epkb-ml-article-section {
                border-color: ' . $kb_config['ml_categories_articles_border_color'] . ';
            }
            #epkb-ml-article-list-product-design-1 .epkb-article-inner {
                color: ' . $kb_config['ml_categories_articles_article_color'] . ';
            }
            #epkb-ml-article-list-classic-design-1 .epkb-ml-article-section__head {
                color: ' . $kb_config['ml_categories_articles_icon_color'] . ';
            }
            #epkb-ml-article-list-classic-design-1 .epkb-article-inner {
                color: ' . $kb_config['ml_categories_articles_article_color'] . ';
            }
			';
		}
		return $output;
	}
}