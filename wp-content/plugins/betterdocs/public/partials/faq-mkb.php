<?php
$output_pro = function_exists('betterdocs_generate_output_pro') ? betterdocs_generate_output_pro() : '';
$query      = new WP_Query( array( 'post_type' => 'betterdocs_faq',  'post_status' => 'publish' ) );
$faq_terms  = BetterDocs_Helper::faq_category_terms('');
$enable_faq_schema = BetterDocs_DB::get_settings('enable_faq_schema') == 1 ? 'true' : '';

if( $output_pro['betterdocs_faq_switch_mkb'] == true && $query->have_posts() && ! is_wp_error( $faq_terms ) ) {

    $terms  = get_theme_mod( 'betterdocs_select_specific_faq_mkb' );
    $layout = get_theme_mod( 'betterdocs_select_faq_template_mkb', 'layout-1' );
    $args = [
        'groups="'.$terms.'"',
        'class="faq-mkb"',
        'faq_heading="'.$output_pro['betterdocs_faq_title_text_mkb'].'"',
        'faq_schema="'.$enable_faq_schema.'"',
    ];

    if( $layout === 'layout-1' ) {
        echo do_shortcode('[betterdocs_faq_list_modern '. implode(' ', $args) .']');
    } else if( $layout === 'layout-2' ) {
        echo do_shortcode('[betterdocs_faq_list_classic '. implode(' ', $args) .']');
    }
}
