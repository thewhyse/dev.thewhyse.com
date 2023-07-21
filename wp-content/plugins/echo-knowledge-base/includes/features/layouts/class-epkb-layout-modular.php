<?php

/**
 *  Outputs the Modular Layout for knowledge base main page. TODO: update
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 */
class EPKB_Layout_Modular extends EPKB_Layout {

	const MAX_ROWS = 5;

	function __construct() {
		add_action( 'epkb_ml_row_search_module', array( $this, 'search_module' ) );
		add_action( 'epkb_ml_row_categories_articles_module', array( $this, 'categories_articles_module' ) );
		add_action( 'epkb_ml_row_articles_list_module', array( $this, 'articles_list_module' ) );
		add_action( 'epkb_ml_row_faqs_module', array( $this, 'faqs_module' ) );
	}

	/**
	 * Generate content of the KB main page
	 */
	public function generate_kb_main_page() { ?>

		<div id="epkb-modular-layout-container" role="main" aria-labelledby="Knowledge Base" class="epkb-css-full-reset <?php echo method_exists( 'EPKB_Utilities', 'get_active_theme_classes' ) ? EPKB_Utilities::get_active_theme_classes( 'mp' ) : ''; ?>">
			<?php
			$this->display_modular_layout_container(); ?>
		</div>   <?php
	}

	/**
	 * Display Modular Layout container
	 */
	private function display_modular_layout_container() {

		// show message that articles are coming soon if the current KB does not have any Category
		if ( ! $this->has_kb_categories ) {
			$this->show_categories_missing_message();
			return;
		}

		for ( $row_number = 1; $row_number <= self::MAX_ROWS; $row_number++ ) {

			if ( empty( $this->kb_config[ 'ml_row_' . $row_number . '_module' ] ) || $this->kb_config[ 'ml_row_' . $row_number . '_module' ] == 'none' ) {
				continue;
			}   ?>

            <div id="epkb-ml__row-<?php echo esc_attr( $row_number ); ?>" class="epkb-ml__row">
                <?php do_action( 'epkb_ml_row_' . ( $this->kb_config[ 'ml_row_' . $row_number . '_module' ] ) . '_module', array( 'config' => $this->kb_config ) ); ?>
            </div>  <?php
        }
	}

	/**
	 * MODULE: Search
	 *
	 * @param $args
	 */
	public static function search_module( $args ) {

		// Advanced Search uses its own search box
		if ( EPKB_Utilities::is_advanced_search_enabled( $args['config'] ) ) {
			do_action( 'eckb_advanced_search_box', $args['config'] );
			return;
		}

		$layout = $args['config']['ml_search_layout'];
		$search_handler = new EPKB_ML_Search( $args['config'] ); ?>

        <div id="epkb-ml__module-search" class="epkb-ml__module">   <?php

			switch( $layout ) {

				case 'modern':
				default:
					$search_handler->display_modern_layout();
					break;

				case 'classic':
					$search_handler->display_classic_layout();
					break;
			}   ?>

		</div>  <?php
	}

	/**
	 * MODULE: Categories and Articles
	 */
	public function categories_articles_module() {

        $layout = $this->kb_config['ml_categories_articles_layout'];

        $module_settings = array(
            'design' => 'epkb-ml-'.$layout.'-layout--design-1',
            //'design' => 'epkb-ml-'.$layout.'-layout--design-' . $this->kb_config['ml_categories_articles_layout_' . $layout . '_design'],
            'height' => $this->kb_config['ml_categories_articles_height_mode'],
        );
		$categories_icons = $this->get_category_icons();
		$categories_articles_handler = new EPKB_ML_Categories_Articles( $this->kb_config, $this->category_seq_data, $this->articles_seq_data );   ?>

		<div id="epkb-ml__module-categories-articles" class="epkb-ml__module">  <?php

			switch( $layout ) {

			    case 'classic':
				default:
				    $categories_articles_handler->display_classic_layout( $module_settings, $categories_icons );
			        break;

			    case 'product':
				    $categories_articles_handler->display_product_layout( $module_settings, $categories_icons );
			        break;
			}   ?>

		</div>    <?php
	}

	/**
	 * MODULE: New and Recent Articles List
	 */
	public function articles_list_module() {  ?>
		<div id="epkb-ml__module-articles-list" class="epkb-ml__module">   <?php
			$articles_list_handler = new EPKB_ML_New_Recent_Articles_List( $this->kb_config );
			$articles_list_handler->display_articles_list( $this->kb_config );    ?>
		</div>  <?php
	}

	/**
	 * MODULE: FAQs
	 */
	public function faqs_module() {    ?>
		<div id="epkb-ml__module-faqs" class="epkb-ml__module">   <?php
			$faqs_handler = new EPKB_ML_FAQs( $this->kb_config );
			$faqs_handler->display_faqs();  ?>
		</div>  <?php
	}

	/**
	 * Returns inline styles for Modular Layout
	 *
	 * @param $kb_config
	 * @param $is_article
	 * @return string
	 */
	public static function get_inline_styles( $kb_config, $is_article=false ) {

		$output = '
		/* CSS for Modular Layout
		-----------------------------------------------------------------------*/';

		for ( $row_number = 1; $row_number <= self::MAX_ROWS; $row_number++ ) {

			if ( empty( $kb_config[ 'ml_row_' . $row_number . '_module' ] ) || $kb_config[ 'ml_row_' . $row_number . '_module' ] == 'none' ) {
				continue;
			}

			$output .= '
			#epkb-ml__row-' . $row_number . ' {
				max-width: ' . $kb_config['ml_row_' . $row_number .'_desktop_width'] . $kb_config['ml_row_' . $row_number .'_desktop_width_units'] . ';
			}';

			switch( $kb_config['ml_row_' . $row_number .'_module'] ) {

				// CSS for Module: Search
				case 'search':
					$output .= EPKB_ML_Search::get_inline_styles( $kb_config, $is_article );
					break;

				// CSS for Module: Categories & Articles
				case 'categories_articles':
					$output .= EPKB_ML_Categories_Articles::get_inline_styles( $kb_config );
					break;

				// CSS for Module: New and Recent Articles List
				case 'articles_list':
					$output .= EPKB_ML_New_Recent_Articles_List::get_inline_styles( $kb_config );
					break;

				// CSS for Module: FAQs
				case 'faqs':
					// TODO
					break;

				default:
					break;
			}
		}

		// render custom CSS at the end to give it higher priority
		$output .= '
		/* Custom CSS for Modular Layout
		-----------------------------------------------------------------------*/
		' . EPKB_Utilities::get_wp_option( 'epkb_ml_custom_css_' . $kb_config['id'], '' ) . ' 
		';

		return $output;
	}
}