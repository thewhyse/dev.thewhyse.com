<?php

/**
 * Lists settings, default values and display of Modular layout.
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 */
class EPKB_KB_Config_Layout_Modular {

    const LAYOUT_NAME = 'Modular';
	const CATEGORY_LEVELS = 6;

	/**
	 * Defines KB configuration for this theme.
	 * ALL FIELDS ARE MANDATORY by default ( otherwise use 'mandatory' => 'false' )
	 *
	 * @return array with both basic and theme-specific configuration
	 */
	public static function get_fields_specification() {

        $config_specification = array(

	        // Row 1
	        'ml_row_1_module'                               => array(
		        'label'       => __( 'Navigation Mode', 'echo-knowledge-base' ),
		        'name'        => 'ml_row_1_module',
		        'type'        => EPKB_Input_Filter::SELECTION,
		        'options'     => array(
			        'none'                  => '-----',
			        'search'                => __( 'Search',   'echo-knowledge-base' ),
			        'categories_articles'   => __( 'Categories & Articles',   'echo-knowledge-base' ),
			        'articles_list'         => __( 'New and Recent Articles List',   'echo-knowledge-base' ),
			        'faqs'                  => __( 'FAQs',   'echo-knowledge-base' ),
		        ),
		        'default'     => 'search'
	        ),
	        'ml_row_1_desktop_width' => array(
		        'label'       => __( 'Row Container Width', 'echo-knowledge-base' ),
		        'name'        => 'ml_row_1_desktop_width',
		        'max'         => 3000,
		        'min'         => 10,
		        'type'        => EPKB_Input_Filter::NUMBER,
		        'style'       => 'small',
		        'default'     => 100
	        ),
	        'ml_row_1_desktop_width_units' => array(
		        'label'       => __( 'Row Container Width - Units', 'echo-knowledge-base' ),
		        'name'        => 'ml_row_1_desktop_width_units',
		        'type'        => EPKB_Input_Filter::SELECTION,
		        'style'       => 'small',
		        'options'     => array(
			        'px'            => _x( 'px', 'echo-knowledge-base' ),
			        '%'             => _x( '%',  'echo-knowledge-base' )
		        ),
		        'default'     => '%'
	        ),

	        // Row 2
	        'ml_row_2_module'                               => array(
		        'label'       => __( 'Navigation Mode', 'echo-knowledge-base' ),
		        'name'        => 'ml_row_2_module',
		        'type'        => EPKB_Input_Filter::SELECTION,
		        'options'     => array(
			        'none'                  => '-----',
			        'search'                => __( 'Search',   'echo-knowledge-base' ),
			        'categories_articles'   => __( 'Categories & Articles',   'echo-knowledge-base' ),
			        'articles_list'         => __( 'New and Recent Articles List',   'echo-knowledge-base' ),
			        'faqs'                  => __( 'FAQs',   'echo-knowledge-base' ),
		        ),
		        'default'     => 'categories_articles'
	        ),
	        'ml_row_2_desktop_width' => array(
		        'label'       => __( 'Row Container Width', 'echo-knowledge-base' ),
		        'name'        => 'ml_row_2_desktop_width',
		        'max'         => 3000,
		        'min'         => 10,
		        'type'        => EPKB_Input_Filter::NUMBER,
		        'style'       => 'small',
		        'default'     => 1080
	        ),
	        'ml_row_2_desktop_width_units' => array(
		        'label'       => __( 'Row Container Width - Units', 'echo-knowledge-base' ),
		        'name'        => 'ml_row_2_desktop_width_units',
		        'type'        => EPKB_Input_Filter::SELECTION,
		        'style'       => 'small',
		        'options'     => array(
			        'px'            => _x( 'px', 'echo-knowledge-base' ),
			        '%'             => _x( '%',  'echo-knowledge-base' )
		        ),
		        'default'     => 'px'
	        ),

	        // Row 3
	        'ml_row_3_module'                               => array(
		        'label'       => __( 'Navigation Mode', 'echo-knowledge-base' ),
		        'name'        => 'ml_row_3_module',
		        'type'        => EPKB_Input_Filter::SELECTION,
		        'options'     => array(
			        'none'                  => '-----',
			        'search'                => __( 'Search',   'echo-knowledge-base' ),
			        'categories_articles'   => __( 'Categories & Articles',   'echo-knowledge-base' ),
			        'articles_list'         => __( 'New and Recent Articles List',   'echo-knowledge-base' ),
			        'faqs'                  => __( 'FAQs',   'echo-knowledge-base' ),
		        ),
		        'default'     => 'articles_list'
	        ),
	        'ml_row_3_desktop_width' => array(
		        'label'       => __( 'Row Container Width', 'echo-knowledge-base' ),
		        'name'        => 'ml_row_3_desktop_width',
		        'max'         => 3000,
		        'min'         => 10,
		        'type'        => EPKB_Input_Filter::NUMBER,
		        'style'       => 'small',
		        'default'     => 1080
	        ),
	        'ml_row_3_desktop_width_units' => array(
		        'label'       => __( 'Row Container Width - Units', 'echo-knowledge-base' ),
		        'name'        => 'ml_row_3_desktop_width_units',
		        'type'        => EPKB_Input_Filter::SELECTION,
		        'style'       => 'small',
		        'options'     => array(
			        'px'            => _x( 'px', 'echo-knowledge-base' ),
			        '%'             => _x( '%',  'echo-knowledge-base' )
		        ),
		        'default'     => 'px'
	        ),

	        // Row 4
	        'ml_row_4_module'                               => array(
		        'label'       => __( 'Navigation Mode', 'echo-knowledge-base' ),
		        'name'        => 'ml_row_4_module',
		        'type'        => EPKB_Input_Filter::SELECTION,
		        'options'     => array(
			        'none'                  => '-----',
			        'search'                => __( 'Search',   'echo-knowledge-base' ),
			        'categories_articles'   => __( 'Categories & Articles',   'echo-knowledge-base' ),
			        'articles_list'         => __( 'New and Recent Articles List',   'echo-knowledge-base' ),
			        'faqs'                  => __( 'FAQs',   'echo-knowledge-base' ),
		        ),
		        'default'     => 'none'
	        ),
	        'ml_row_4_desktop_width' => array(
		        'label'       => __( 'Row Container Width', 'echo-knowledge-base' ),
		        'name'        => 'ml_row_4_desktop_width',
		        'max'         => 3000,
		        'min'         => 10,
		        'type'        => EPKB_Input_Filter::NUMBER,
		        'style'       => 'small',
		        'default'     => 100
	        ),
	        'ml_row_4_desktop_width_units' => array(
		        'label'       => __( 'Row Container Width - Units', 'echo-knowledge-base' ),
		        'name'        => 'ml_row_4_desktop_width_units',
		        'type'        => EPKB_Input_Filter::SELECTION,
		        'style'       => 'small',
		        'options'     => array(
			        'px'            => _x( 'px', 'echo-knowledge-base' ),
			        '%'             => _x( '%',  'echo-knowledge-base' )
		        ),
		        'default'     => '%'
	        ),

	        // Row 5
	        'ml_row_5_module'                               => array(
		        'label'       => __( 'Navigation Mode', 'echo-knowledge-base' ),
		        'name'        => 'ml_row_5_module',
		        'type'        => EPKB_Input_Filter::SELECTION,
		        'options'     => array(
			        'none'                  => '-----',
			        'search'                => __( 'Search',   'echo-knowledge-base' ),
			        'categories_articles'   => __( 'Categories & Articles',   'echo-knowledge-base' ),
			        'articles_list'         => __( 'New and Recent Articles List',   'echo-knowledge-base' ),
			        'faqs'                  => __( 'FAQs',   'echo-knowledge-base' ),
		        ),
		        'default'     => 'none'
	        ),
	        'ml_row_5_desktop_width' => array(
		        'label'       => __( 'Row Container Width', 'echo-knowledge-base' ),
		        'name'        => 'ml_row_5_desktop_width',
		        'max'         => 3000,
		        'min'         => 10,
		        'type'        => EPKB_Input_Filter::NUMBER,
		        'style'       => 'small',
		        'default'     => 100
	        ),
	        'ml_row_5_desktop_width_units' => array(
		        'label'       => __( 'Row Container Width - Units', 'echo-knowledge-base' ),
		        'name'        => 'ml_row_5_desktop_width_units',
		        'type'        => EPKB_Input_Filter::SELECTION,
		        'style'       => 'small',
		        'options'     => array(
			        'px'            => _x( 'px', 'echo-knowledge-base' ),
			        '%'             => _x( '%',  'echo-knowledge-base' )
		        ),
		        'default'     => '%'
	        ),

	        // Module: Categories & Articles
	        'ml_categories_articles_layout'                 => array(
		        'label'       => __( 'Layout', 'echo-knowledge-base' ),
		        'name'        => 'ml_categories_articles_layout',
		        'type'        => EPKB_Input_Filter::SELECTION,
		        'options'     => array(
			        'classic'   => __( 'Classic Layout',   'echo-knowledge-base' ),
			        'product'   => __( 'Product Layout',   'echo-knowledge-base' ),
		        ),
		        'default'     => 'classic'
	        ),
	        /*	'ml_categories_articles_layout_classic_design'  => array(
					'label'       => __( 'Design', 'echo-knowledge-base' ),
					'name'        => 'ml_categories_articles_layout_classic_design',
					'type'        => EPKB_Input_Filter::SELECTION,
					'options'     => array(
						'1' => '1',
						'2' => '2',
					),
					'default'     => '1'
				),*/
	        /*'ml_categories_articles_layout_product_design'  => array(
				'label'       => __( 'Design', 'echo-knowledge-base' ),
				'name'        => 'ml_categories_articles_layout_product_design',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
				),
				'default'     => '3'
			),*/
	        'ml_categories_articles_height_mode'            => array(
		        'label'       => __( 'Height Mode', 'echo-knowledge-base' ),
		        'name'        => 'ml_categories_articles_height_mode',
		        'type'        => EPKB_Input_Filter::SELECTION,
		        'options'     => array(
			        'variable'  => __( 'Variable',   'echo-knowledge-base' ),
			        'fixed'     => __( 'Fixed',   'echo-knowledge-base' ),
		        ),
		        'default'     => 'variable'
	        ),
	        'ml_categories_articles_fixed_height'           => array(
		        'label'       => __( 'Height ( px )', 'echo-knowledge-base' ),
		        'name'        => 'ml_categories_articles_fixed_height',
		        'max'         => '2000',
		        'min'         => '1',
		        'type'        => EPKB_Input_Filter::NUMBER,
		        'style'       => 'small',
		        'default'     => 514
	        ),
	        'ml_categories_articles_nof_articles_displayed' => array(
		        'label'       => __( 'Number of Articles Listed', 'echo-knowledge-base' ),
		        'name'        => 'ml_categories_articles_nof_articles_displayed',
		        'max'         => '200',
		        'min'         => '1',
		        'type'        => EPKB_Input_Filter::NUMBER,
		        'style'       => 'small',
		        'default'     => 8
	        ),
	        'ml_categories_articles_icon_background_color_toggle'      => array(
		        'label'       => __( 'Show Icon Background Color', 'echo-knowledge-base' ),
		        'name'        => 'ml_categories_articles_icon_background_color_toggle',
		        'type'        => EPKB_Input_Filter::CHECKBOX,
		        'default'     => 'on'
	        ),

	        'ml_categories_articles_icon_background_color'      => array(
		        'label'       => __( 'Icon Background Color', 'echo-knowledge-base' ),
		        'name'        => 'ml_categories_articles_icon_background_color',
		        'size'        => '10',
		        'max'         => '7',
		        'min'         => '7',
		        'type'        => EPKB_Input_Filter::COLOR_HEX,
		        'default'     => '#d9f8ff'
	        ),
	        'ml_categories_articles_icon_color'                 => array(
		        'label'       => __( 'Icon Color', 'echo-knowledge-base' ),
		        'name'        => 'ml_categories_articles_icon_color',
		        'size'        => '10',
		        'max'         => '7',
		        'min'         => '7',
		        'type'        => EPKB_Input_Filter::COLOR_HEX,
		        'default'     => '#7accef'
	        ),

	        'ml_categories_articles_border_color'               => array(
		        'label'       => __( 'Border Color', 'echo-knowledge-base' ),
		        'name'        => 'ml_categories_articles_border_color',
		        'size'        => '10',
		        'max'         => '7',
		        'min'         => '7',
		        'type'        => EPKB_Input_Filter::COLOR_HEX,
		        'default'     => '#8224e3'
	        ),
	        'ml_categories_articles_article_color'              => array(
		        'label'       => __( 'Article Color', 'echo-knowledge-base' ),
		        'name'        => 'ml_categories_articles_article_color',
		        'size'        => '10',
		        'max'         => '7',
		        'min'         => '7',
		        'type'        => EPKB_Input_Filter::COLOR_HEX,
		        'default'     => '#8224e3'
	        ),
	        'ml_categories_articles_article_bg_color'           => array(
		        'label'       => __( 'Article Background Color', 'echo-knowledge-base' ),
		        'name'        => 'ml_categories_articles_article_bg_color',
		        'size'        => '10',
		        'max'         => '7',
		        'min'         => '7',
		        'type'        => EPKB_Input_Filter::COLOR_HEX,
		        'default'     => '#faf4ff'
	        ),
	        'ml_categories_articles_article_show_more_color'    => array(
		        'label'       => __( 'Show More Color', 'echo-knowledge-base' ),
		        'name'        => 'ml_categories_articles_article_show_more_color',
		        'size'        => '10',
		        'max'         => '7',
		        'min'         => '7',
		        'type'        => EPKB_Input_Filter::COLOR_HEX,
		        'default'     => '#000000'
	        ),
	        'ml_categories_articles_cat_desc_color'             => array(
		        'label'       => __( 'Category Desc Color', 'echo-knowledge-base' ),
		        'name'        => 'ml_categories_articles_cat_desc_color',
		        'size'        => '10',
		        'max'         => '7',
		        'min'         => '7',
		        'type'        => EPKB_Input_Filter::COLOR_HEX,
		        'default'     => '#38076a'
	        ),
	        'ml_categories_articles_back_button_bg_color'        => array(
		        'label'       => __( 'Back Button Color', 'echo-knowledge-base' ),
		        'name'        => 'ml_categories_articles_back_button_bg_color',
		        'size'        => '10',
		        'max'         => '7',
		        'min'         => '7',
		        'type'        => EPKB_Input_Filter::COLOR_HEX,
		        'default'     => '#38076a'
	        ),

	        'ml_categories_articles_icon_size'              => array(
		        'label'       => __( 'Top Icon Size ( px )', 'echo-knowledge-base' ),
		        'name'        => 'ml_categories_articles_icon_size',
		        'max'         => '250',
		        'min'         => '0',
		        'type'        => EPKB_Input_Filter::NUMBER,
		        'style'       => 'small',
		        'default'     => '60'
	        ),
	        'ml_categories_articles_title_html_tag'         => array(
		        'label'       => __( 'Title HTML Tag', 'echo-knowledge-base' ),
		        'name'        => 'ml_categories_articles_title_html_tag',
		        'type'        => EPKB_Input_Filter::SELECTION,
		        'default'     => 'h2',
		        'style'       => 'small',
		        'options'     => array(
			        'div' => 'div',
			        'h1' => 'h1',
			        'h2' => 'h2',
			        'h3' => 'h3',
			        'h4' => 'h4',
			        'h5' => 'h5',
			        'h6' => 'h6',
			        'span' => 'span',
			        'p' => 'p',
		        ),
	        ),

	        // Module: Search
	        'ml_search_layout'                              => array(
		        'label'       => __( 'Layout', 'echo-knowledge-base' ),
		        'name'        => 'ml_search_layout',
		        'type'        => EPKB_Input_Filter::SELECTION,
		        'options'     => array(
			        'classic'   => __( 'Classic Layout',   'echo-knowledge-base' ),
			        'modern'    => __( 'Modern Layout',   'echo-knowledge-base' ),
		        ),
		        'default'     => 'classic'
	        ),

	        // Module: New and Recent Articles List
	        'ml_articles_list_nof_articles_displayed'       => array(
		        'label'       => __( 'Number of Articles Listed', 'echo-knowledge-base' ),
		        'name'        => 'ml_articles_list_nof_articles_displayed',
		        'max'         => '200',
		        'min'         => '1',
		        'type'        => EPKB_Input_Filter::NUMBER,
		        'style'       => 'small',
		        'default'     => 5
	        ),
	        'ml_articles_list_match_style_toggle'      => array(
		        'label'       => __( 'Match Style from Cat/Art Module', 'echo-knowledge-base' ),
		        'name'        => 'ml_articles_list_match_style_toggle',
		        'type'        => EPKB_Input_Filter::CHECKBOX,
		        'default'     => 'on'
	        ),

	        // Module: FAQs
	        'ml_faqs_preset'                                => array(
		        'label'       => __( 'Preset', 'echo-knowledge-base' ),
		        'name'        => 'ml_faqs_preset',
		        'type'        => EPKB_Input_Filter::SELECTION,
		        'options'     => array(
			        'basic'         => __( 'Basic', 'echo-knowledge-base' ),
			        'boxed'         => __( 'Boxed', 'echo-knowledge-base' ),
			        'grey-box'      => __( 'Grey Box', 'echo-knowledge-base' ),
			        'grey-box-dark' => __( 'Grey Box Dark', 'echo-knowledge-base' )
		        ),
		        'default'     => 'basic'
	        ),
	        'ml_faqs_content_mode'                          => array(
		        'label'       => __( 'Content Mode', 'echo-knowledge-base' ),
		        'name'        => 'ml_faqs_content_mode',
		        'type'        => EPKB_Input_Filter::SELECTION,
		        'options'     => array(
			        'content'    => __( 'Content', 'echo-knowledge-base' ),
			        'excerpt'    => __( 'Excerpt', 'echo-knowledge-base' )
		        ),
		        'default'     => 'content'
	        ),
	        'ml_faqs_custom_css_class'                      => array(
		        'label'       => __( 'Custom CSS class', 'echo-knowledge-base' ),
		        'name'        => 'ml_faqs_custom_css_class',
		        'size'        => '200',
		        'max'         => '200',
		        'min'         => '0',
		        'mandatory'   => false,
		        'type'        => EPKB_Input_Filter::TEXT,
		        'default'     => ''
	        ),
	        'ml_faqs_kb_id'       => array(
		        'label'       => __( 'Knowledge Base', 'echo-knowledge-base' ),
		        'name'        => 'ml_faqs_kb_id',
		        'max'         => '200',
		        'min'         => '1',
		        'type'        => EPKB_Input_Filter::NUMBER,
		        'style'       => 'small',
		        'default'     => EPKB_KB_Config_DB::DEFAULT_KB_ID
	        ),
	        'ml_faqs_category_ids'                          => array(
		        'label'       => __( 'Categories', 'echo-knowledge-base' ),
		        'name'        => 'ml_faqs_category_ids',
		        'size'        => '2000',
		        'max'         => '2000',
		        'min'         => '0',
		        'mandatory'   => false,
		        'type'        => EPKB_Input_Filter::TEXT,
		        'default'     => ''
	        ),
        );

		return $config_specification;
	}
}
