<?php

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;
use Elementor\Core\Schemes;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Plugin;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;

class Betterdocs_FAQ_Widget extends Widget_Base{


    public function get_name()
    {
        return 'betterdocs-faq';
    }

    public function get_title()
    {
        return __('BetterDocs FAQ', 'betterdocs');
    }

    public function get_custom_help_url()
    {
        return 'http://betterdocs.co/docs/betterdocs-faq-builder-in-elementor/';
    }

    public function get_icon()
    {
        return 'betterdocs-icon-faq';
    }

    public function get_categories()
    {
        return ['betterdocs-elements'];
    }

    public function get_keywords()
    {
        return ['betterdocs-elements', 'betterdocs', 'docs', 'faq', 'FAQ', 'betterdocs-faq'];
    }

    protected function register_controls()
    {
        $this->start_controls_section(
            'faq_section_controls',
            [
                'label' => __('Layout Options', 'betterdocs')
            ]
        );

        $this->add_control(
            'faq_layout_selection',
            [
                'label'       => __('Select Layout', 'betterdocs'),
                'type'        => Controls_Manager::SELECT2,
                'options'     => [
                    'layout-1' => __('Modern Layout', 'betterdocs'),
                    'layout-2' => __('Classic Layout', 'betterdocs')
                ],
                'default'     => 'layout-1',
                'label_block' => true
            ]
        );

        $this->add_control(
            'faq_layout_section',
            [
                'label' => __('FAQ Section', 'betterdocs'),
                'type' => Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => __('Frequently Asked Questions', 'betterdocs'),
            ]
        );

        $this->add_control(
            'select_specific_faq',
            [
                'label'       => __('Include FAQ Groups', 'betterdocs'),
                'label_block' => true,
                'type'        => Controls_Manager::SELECT2,
                'options'     => BetterDocs_Helper::faq_widget_term_list(),
                'multiple'    => true,
                'default'     => '',
                'select2options' => [
                    'placeholder' => __('Include FAQ Groups', 'betterdocs'),
                    'allowClear' => true,
                ]
            ]
        );

        $this->add_control(
            'exclude_specific_faq',
            [
                'label'       => __('Exclude FAQ Groups', 'betterdocs'),
                'label_block' => true,
                'type'        => Controls_Manager::SELECT2,
                'options'     => BetterDocs_Helper::faq_widget_term_list(),
                'multiple'    => true,
                'default'     => '',
                'select2options' => [
                    'placeholder' => __('Exclude FAQ Groups', 'betterdocs'),
                    'allowClear' => true,
                ]
            ]
        );

        $this->end_controls_section();


        /******* Common Section Style For Both Layouts *******/

        $this->start_controls_section(
            'faq_section_style',
            [
                'label' => __('FAQ Section Title', 'betterdocs'),
                'tab'   => Controls_Manager::TAB_STYLE
            ]
        );

        $this->add_control(
            'faq_layout_section_title_color',
            [
                'label'     => esc_html__('Text Color', 'betterdocs'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .betterdocs-faq-section-title' => 'color:{{VALUE}};',
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'faq_layout_section_title_typography',
                'selector' => '{{WRAPPER}} .betterdocs-faq-section-title'
            ]
        );

        $this->end_controls_section();

        /************************** Layout 1 Controls *************************/

        $this->start_controls_section(
            'faq_box_style_section',
            [
                'label' => __('FAQ List', 'betterdocs'),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'faq_layout_selection' => ['layout-1']
                ]
            ]
        );

        $this->add_responsive_control(
            'faq_box_padding',
            [
                'label'      => __('Padding', 'betterdocs'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .betterdocs-faq-main-wrapper-layout-1 .betterdocs-faq-list > li .betterdocs-faq-group .betterdocs-faq-post' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ]
            ]
        );

        $this->add_responsive_control(
            'faq_box_margin', 
            [
                'label'      => __('Margin', 'betterdocs'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .betterdocs-faq-main-wrapper-layout-1 .betterdocs-faq-list > li .betterdocs-faq-group .betterdocs-faq-post' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'faq_box_typography',
                'selector' => '{{WRAPPER}} .betterdocs-faq-main-wrapper-layout-1 .betterdocs-faq-list > li .betterdocs-faq-group .betterdocs-faq-post .betterdocs-faq-post-name'
            ]
        );

        $this->add_control(
            'faq_box_term_title_color',
            [
                'label'     => esc_html__('Color', 'betterdocs'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .betterdocs-faq-main-wrapper-layout-1 .betterdocs-faq-list > li .betterdocs-faq-group .betterdocs-faq-post .betterdocs-faq-post-name' => 'color:{{VALUE}};',
                ]
            ]
        );

        $this->start_controls_tabs('faq_tabs');

        // Normal State Tab
        $this->start_controls_tab(
            'faq_box_normal',
            ['label' => esc_html__('Normal', 'betterdocs')]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'faq_box_border_normal',
                'label'    => esc_html__('Border', 'betterdocs'),
                'selector' => '{{WRAPPER}} .betterdocs-faq-main-wrapper-layout-1 .betterdocs-faq-list > li .betterdocs-faq-group .betterdocs-faq-post'
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'faq_box_background_normal',
                'label'    => esc_html__('Background', 'betterdocs'),
                'selector' => '{{WRAPPER}} .betterdocs-faq-main-wrapper-layout-1 .betterdocs-faq-list > li .betterdocs-faq-group .betterdocs-faq-post'
            ]
        );

        $this->end_controls_tab();

        // Hover State Tab
        $this->start_controls_tab(
            'faq_box_hover',
            ['label' => esc_html__('Hover', 'betterdocs')]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'faq_box_border_hover',
                'label'    => esc_html__('Border', 'betterdocs'),
                'selector' => '{{WRAPPER}} .betterdocs-faq-main-wrapper-layout-1 .betterdocs-faq-list > li .betterdocs-faq-group .betterdocs-faq-post:hover'
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'faq_box_background_hover',
                'label'    => esc_html__('Background', 'betterdocs'),
                'selector' => '{{WRAPPER}} .betterdocs-faq-main-wrapper-layout-1 .betterdocs-faq-list > li .betterdocs-faq-group .betterdocs-faq-post:hover'
            ]
        );

        $this->end_controls_tab();
    
        $this->end_controls_tabs();

        $this->end_controls_section();


        $this->start_controls_section(
            'faq_box_title_section',
            [
                'label' => __('FAQ List Title', 'betterdocs'),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'faq_layout_selection' => ['layout-1']
                ]
            ]
        );

        $this->add_control(
            'faq_box_title_color',
            [
                'label'     => esc_html__('Title Color', 'betterdocs'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .betterdocs-faq-main-wrapper-layout-1 .betterdocs-faq-title h2' => 'color:{{VALUE}};',
                ]
            ]
        );

        $this->add_control(
            'faq_box_title_color_hover',
            [
                'label'     => esc_html__('Title Hover Color', 'betterdocs'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .betterdocs-faq-main-wrapper-layout-1 .betterdocs-faq-title h2:hover' => 'color:{{VALUE}};',
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'faq_box_title_typography',
                'selector' => '{{WRAPPER}} .betterdocs-faq-main-wrapper-layout-1 .betterdocs-faq-title h2'
            ]
        );
        
        $this->end_controls_section();
    
        $this->start_controls_section(
            'faq_box_content_section',
            [
                'label' => __('FAQ Content', 'betterdocs'),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'faq_layout_selection' => ['layout-1']
                ]
            ]
        );

        $this->add_responsive_control(
            'faq_box_content_section_padding',
            [
                'label'      => __('Padding', 'betterdocs'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .betterdocs-faq-main-wrapper-layout-1 .betterdocs-faq-group .betterdocs-faq-main-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ]
            ]
        );

        $this->add_responsive_control(
            'faq_box_content_section_margin', 
            [
                'label'      => __('Margin', 'betterdocs'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .betterdocs-faq-main-wrapper-layout-1 .betterdocs-faq-group .betterdocs-faq-main-content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'faq_box_content_section_background',
                'label'    => esc_html__('Background', 'betterdocs'),
                'selector' => '{{WRAPPER}} .betterdocs-faq-main-wrapper-layout-1 .betterdocs-faq-group .betterdocs-faq-main-content'
            ]
        );


        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'faq_box_content_section_typography',
                'selector' => '{{WRAPPER}} .betterdocs-faq-main-wrapper-layout-1 .betterdocs-faq-group .betterdocs-faq-main-content'
            ]
        );


        $this->add_control(
            'faq_box_content_section_color',
            [
                'label'     => esc_html__('Color', 'betterdocs'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .betterdocs-faq-main-wrapper-layout-1 .betterdocs-faq-group .betterdocs-faq-main-content' => 'color:{{VALUE}};',
                ]
            ]
        );


        $this->end_controls_section();

        $this->start_controls_section(
            'faq_box_content_icon',
            [
                'label' => __('FAQ List Icon', 'betterdocs'),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'faq_layout_selection' => ['layout-1']
                ]
            ]
        );

        $this->add_responsive_control(
            'faq_box_content_icon_height',
            [
                'label'      => esc_html__('Icon Height', 'betterdocs'),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'em'],
                'range'      => [
                    'px' => [
                        'max' => 500,
                    ],
                ],
                'selectors'  => [
                    '{{WRAPPER}} .betterdocs-faq-main-wrapper-layout-1 .betterdocs-faq-list > li .betterdocs-faq-group .betterdocs-faq-post .betterdocs-faq-iconminus' => 'height:{{SIZE}}{{UNIT}}; max-height: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .betterdocs-faq-main-wrapper-layout-1 .betterdocs-faq-list > li .betterdocs-faq-group .betterdocs-faq-post .betterdocs-faq-iconplus' => 'height:{{SIZE}}{{UNIT}}; max-height: {{SIZE}}{{UNIT}};'
                ]
            ]
        );

        $this->add_responsive_control(
            'faq_box_content_icon_width',
            [
                'label'      => esc_html__('Icon Width', 'betterdocs'),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'em'],
                'range'      => [
                    'px' => [
                        'max' => 500,
                    ],
                ],
                'selectors'  => [
                    '{{WRAPPER}} .betterdocs-faq-main-wrapper-layout-1 .betterdocs-faq-list > li .betterdocs-faq-group .betterdocs-faq-post .betterdocs-faq-iconminus' => 'width:{{SIZE}}{{UNIT}}; max-width: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .betterdocs-faq-main-wrapper-layout-1 .betterdocs-faq-list > li .betterdocs-faq-group .betterdocs-faq-post .betterdocs-faq-iconplus' => 'width:{{SIZE}}{{UNIT}}; max-width: {{SIZE}}{{UNIT}};'
                ]
            ]
        );

        $this->add_control(
            'faq_box_content_icon_color',
            [
                'label'     => esc_html__('Icon Color', 'betterdocs'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .betterdocs-faq-main-wrapper-layout-1 .betterdocs-faq-list > li .betterdocs-faq-group .betterdocs-faq-post .betterdocs-faq-iconminus g' => 'fill:{{VALUE}} ! important;',
                    '{{WRAPPER}} .betterdocs-faq-main-wrapper-layout-1 .betterdocs-faq-list > li .betterdocs-faq-group .betterdocs-faq-post .betterdocs-faq-iconplus g' => 'fill:{{VALUE}} ! important;',
                ]   
            ]
        );

        $this->end_controls_section();

        /************************** Layout 2 Controls *************************/

        $this->start_controls_section(
            'faq_box_style_section_layout_2',
            [
                'label' => __('FAQ List', 'betterdocs'),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'faq_layout_selection' => ['layout-2']
                ]
            ]
        );

        $this->add_responsive_control(
            'faq_box_padding_layout_2',
            [
                'label'      => __('Padding', 'betterdocs'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .betterdocs-faq-main-wrapper-layout-2 .betterdocs-faq-list-layout-2 > li .betterdocs-faq-group-layout-2' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ]
            ]
        );

        $this->add_responsive_control(
            'faq_box_margin_layout_2', 
            [
                'label'      => __('Margin', 'betterdocs'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .betterdocs-faq-main-wrapper-layout-2 .betterdocs-faq-list-layout-2 > li .betterdocs-faq-group-layout-2' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'faq_box_typography_layout_2',
                'selector' => '{{WRAPPER}} .betterdocs-faq-main-wrapper-layout-2 .betterdocs-faq-list-layout-2 > li .betterdocs-faq-group-layout-2 .betterdocs-faq-post-layout-2 .betterdocs-faq-post-name-layout-2'
            ]
        );


        $this->add_control(
            'faq_box_term_title_color_layout_2',
            [
                'label'     => esc_html__('Color', 'betterdocs'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .betterdocs-faq-main-wrapper-layout-2 .betterdocs-faq-list > li .betterdocs-faq-group .betterdocs-faq-post-layout-2 .betterdocs-faq-post-name' => 'color:{{VALUE}};',
                ]
            ]
        );


        $this->start_controls_tabs('faq_tabs_layout_2');

        // Normal State Tab
        $this->start_controls_tab(
            'faq_box_normal_layout_2',
            ['label' => esc_html__('Normal', 'betterdocs')]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'faq_box_border_normal_layout_2',
                'label'    => esc_html__('Border', 'betterdocs'),
                'selector' => '{{WRAPPER}} .betterdocs-faq-main-wrapper-layout-2 .betterdocs-faq-list-layout-2 > li .betterdocs-faq-group-layout-2'
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'faq_box_background_normal_layout_2',
                'label'    => esc_html__('Background', 'betterdocs'),
                'selector' => '{{WRAPPER}} .betterdocs-faq-main-wrapper-layout-2 .betterdocs-faq-list-layout-2 > li .betterdocs-faq-group-layout-2'
            ]
        );

        $this->end_controls_tab();

        // Hover State Tab
        $this->start_controls_tab(
            'faq_box_hover_layout_2',
            ['label' => esc_html__('Hover', 'betterdocs')]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'faq_box_border_hover_layout_2',
                'label'    => esc_html__('Border', 'betterdocs'),
                'selector' => '{{WRAPPER}} .betterdocs-faq-main-wrapper-layout-2 .betterdocs-faq-list-layout-2 > li .betterdocs-faq-group-layout-2:hover'
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'faq_box_background_hover_layout_2',
                'label'    => esc_html__('Background', 'betterdocs'),
                'selector' => '{{WRAPPER}} .betterdocs-faq-main-wrapper-layout-2 .betterdocs-faq-list-layout-2 > li .betterdocs-faq-group-layout-2:hover'
            ]
        );

        $this->end_controls_tab();
    
        $this->end_controls_tabs();

        $this->end_controls_section();


        $this->start_controls_section(
            'faq_box_title_section_layout_2',
            [
                'label' => __('FAQ List Title', 'betterdocs'),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'faq_layout_selection' => ['layout-2']
                ]
            ]
        );

        $this->add_control(
            'faq_box_title_color_layout_2',
            [
                'label'     => esc_html__('Box Title Color', 'betterdocs'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .betterdocs-faq-title-layout-2 h2' => 'color:{{VALUE}};',
                ]
            ]
        );

        $this->add_control(
            'faq_box_title_color_hover_layout_2',
            [
                'label'     => esc_html__('Box Title Hover Color', 'betterdocs'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .betterdocs-faq-title-layout-2 h2:hover' => 'color:{{VALUE}};',
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'faq_box_title_typography_layout_2',
                'selector' => '{{WRAPPER}} .betterdocs-faq-title-layout-2 h2'
            ]
        );
        
        $this->end_controls_section();

       
        $this->start_controls_section(
            'faq_box_content_section_layout_2',
            [
                'label' => __('FAQ Content', 'betterdocs'),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'faq_layout_selection' => ['layout-2']
                ]
            ]
        );

        $this->add_responsive_control(
            'faq_box_content_section_padding_layout_2',
            [
                'label'      => __('Padding', 'betterdocs'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .betterdocs-faq-main-wrapper-layout-2 .betterdocs-faq-list-layout-2 > li .betterdocs-faq-group-layout-2 .betterdocs-faq-main-content-layout-2' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ]
            ]
        );

        $this->add_responsive_control(
            'faq_box_content_section_margin_layout_2', 
            [
                'label'      => __('Margin', 'betterdocs'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .betterdocs-faq-main-wrapper-layout-2 .betterdocs-faq-list-layout-2 > li .betterdocs-faq-group-layout-2 .betterdocs-faq-main-content-layout-2' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'faq_box_content_section_background_layout_2',
                'label'    => esc_html__('Background', 'betterdocs'),
                'selector' => '{{WRAPPER}} .betterdocs-faq-main-wrapper-layout-2 .betterdocs-faq-list-layout-2 > li .betterdocs-faq-group-layout-2 .betterdocs-faq-main-content-layout-2'
            ]
        );


        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'faq_box_content_section_typography_layout_2',
                'selector' => '{{WRAPPER}} .betterdocs-faq-main-wrapper-layout-2 .betterdocs-faq-list-layout-2 > li .betterdocs-faq-group-layout-2 .betterdocs-faq-main-content-layout-2'
            ]
        );


        $this->add_control(
            'faq_box_content_section_color_layout_2',
            [
                'label'     => esc_html__('Color', 'betterdocs'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .betterdocs-faq-main-wrapper-layout-2 .betterdocs-faq-list-layout-2 > li .betterdocs-faq-group-layout-2 .betterdocs-faq-main-content-layout-2' => 'color:{{VALUE}};',
                ]
            ]
        );


        $this->end_controls_section();


        $this->start_controls_section(
            'faq_box_content_icon_layout_2',
            [
                'label' => __('FAQ Icon', 'betterdocs'),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'faq_layout_selection' => ['layout-2']
                ]
            ]
        );

        $this->add_responsive_control(
            'faq_box_content_icon_height_layout_2',
            [
                'label'      => esc_html__('Icon Height', 'betterdocs'),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'em'],
                'range'      => [
                    'px' => [
                        'max' => 500,
                    ],
                ],
                'selectors'  => [
                    '{{WRAPPER}} .betterdocs-faq-main-wrapper-layout-2 .betterdocs-faq-list-layout-2 > li .betterdocs-faq-group-layout-2 .betterdocs-faq-post-layout-2 .betterdocs-faq-post-layout-2-icon-group .betterdocs-faq-iconplus-layout-2' => 'height:{{SIZE}}{{UNIT}}; max-height: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .betterdocs-faq-main-wrapper-layout-2 .betterdocs-faq-list-layout-2 > li .betterdocs-faq-group-layout-2 .betterdocs-faq-post-layout-2 .betterdocs-faq-post-layout-2-icon-group .betterdocs-faq-iconminus-layout-2' => 'height:{{SIZE}}{{UNIT}}; max-height: {{SIZE}}{{UNIT}};'
                ]
            ]
        );

        $this->add_responsive_control(
            'faq_box_content_icon_width_layout_2',
            [
                'label'      => esc_html__('Icon Width', 'betterdocs'),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'em'],
                'range'      => [
                    'px' => [
                        'max' => 500,
                    ],
                ],
                'selectors'  => [
                    '{{WRAPPER}} .betterdocs-faq-main-wrapper-layout-2 .betterdocs-faq-list-layout-2 > li .betterdocs-faq-group-layout-2 .betterdocs-faq-post-layout-2 .betterdocs-faq-post-layout-2-icon-group .betterdocs-faq-iconplus-layout-2' => 'width:{{SIZE}}{{UNIT}}; max-width: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .betterdocs-faq-main-wrapper-layout-2 .betterdocs-faq-list-layout-2 > li .betterdocs-faq-group-layout-2 .betterdocs-faq-post-layout-2 .betterdocs-faq-post-layout-2-icon-group .betterdocs-faq-iconminus-layout-2' => 'width:{{SIZE}}{{UNIT}}; max-width: {{SIZE}}{{UNIT}};'
                ]
            ]
        );

        $this->add_control(
            'faq_box_content_icon_color_layout_2',
            [
                'label'     => esc_html__('Icon Color', 'betterdocs'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .betterdocs-faq-main-wrapper-layout-2 .betterdocs-faq-list-layout-2 > li .betterdocs-faq-group-layout-2 .betterdocs-faq-post-layout-2 .betterdocs-faq-post-layout-2-icon-group .betterdocs-faq-iconplus-layout-2 path' => 'fill:{{VALUE}} ! important;',
                    '{{WRAPPER}} .betterdocs-faq-main-wrapper-layout-2 .betterdocs-faq-list-layout-2 > li .betterdocs-faq-group-layout-2 .betterdocs-faq-post-layout-2 .betterdocs-faq-post-layout-2-icon-group .betterdocs-faq-iconminus-layout-2 path' => 'fill:{{VALUE}} ! important;',
                ]   
            ]
        );


        $this->end_controls_section();
    }

    protected function render()
    {
        $control_values = $this->get_settings_for_display();

        $shortcode      = '';
        
        $specific_faqs  = ! empty( $control_values['select_specific_faq'] ) ? implode( ',' ,$control_values['select_specific_faq'] ) : '';

        $faqs_exclude   = ! empty( $control_values['exclude_specific_faq'] ) ? implode( ',', $control_values['exclude_specific_faq'] ) : '';


        $enable_faq_schema   = BetterDocs_DB::get_settings('enable_faq_schema') == 1 ? 'true' : '';

        $args = [
            'groups="'.$specific_faqs.'"',
            'group_exclude="'.$faqs_exclude.'"',
            'faq_heading="'.$control_values['faq_layout_section'].'"',
            'faq_schema="'.$enable_faq_schema.'"',
        ];

        if( $control_values['faq_layout_selection'] === 'layout-1' ) {
            $shortcode .= do_shortcode('[betterdocs_faq_list_modern '. implode(' ', $args) .']');
        }

        if( $control_values['faq_layout_selection'] === 'layout-2' ) {
            $shortcode .= do_shortcode('[betterdocs_faq_list_classic '. implode(' ', $args) .']');
        }

        if (\Elementor\Plugin::instance()->editor->is_edit_mode()) {
            $this->render_editor_script();
        }

        echo $shortcode;
    }

    protected function render_editor_script()
    { 
        ?>
            <script>
                jQuery(document).ready(function($) {
                    $('.betterdocs-faq-post').on('click', function(e) {
                        var current_node = $(this);
                        var active_list  = $('.betterdocs-faq-group.active');
                        
                        if( ! current_node.parent().hasClass('active') ) {
                            current_node.parent().addClass('active');
                            current_node.children('svg').toggle();
                            current_node.next().slideDown();
                        }

                        for( let node of active_list ) {
                            if( $(node).hasClass('active') ) {
                                $(node).removeClass('active');
                                $(node).children('.betterdocs-faq-post').children('svg').toggle();
                                $(node).children('.betterdocs-faq-main-content').slideUp();
                            }
                        }
                    });

                    $('.betterdocs-faq-post-layout-2').on('click', function(e) {
                        var current_node = $(this);

                        if( ! current_node.parent().hasClass('active') ) {
                            current_node.parent().addClass('active');
                            current_node.children('.betterdocs-faq-post-layout-2-icon-group').children('svg').toggle();
                            current_node.next().slideDown();
                        } else {
                            current_node.parent().removeClass('active');
                            current_node.children('.betterdocs-faq-post-layout-2-icon-group').children('svg').toggle();
                            current_node.next().slideUp();
                        }

                    });
                });
            </script>
        <?php
    }

}