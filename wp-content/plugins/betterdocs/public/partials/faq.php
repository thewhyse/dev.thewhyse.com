<?php
$output    = betterdocs_generate_output();
$query     = new WP_Query( array( 'post_type' => 'betterdocs_faq',  'post_status' => 'publish' ) );
$faq_terms = BetterDocs_Helper::faq_category_terms('');
$enable_faq_schema = BetterDocs_DB::get_settings('enable_faq_schema') == 1 ? 'true' : '';

if( $output['betterdocs_faq_switch'] == true && $query->have_posts() && ! empty( $faq_terms ) ) {
    $terms = get_theme_mod( 'betterdocs_select_specific_faq' );
    $layout = get_theme_mod('betterdocs_select_faq_template', 'layout-1' );
    $args = [
        'groups="'.$terms.'"',
        'class="faq-doc"',
        'faq_heading="'.$output['betterdocs_faq_title_text'].'"',
        'faq_schema="'.$enable_faq_schema.'"',
    ];

    if( $layout === 'layout-1' ) {
        echo do_shortcode('[betterdocs_faq_list_modern '. implode(' ', $args) .']');
    } else if( $layout === 'layout-2' ) {
        echo do_shortcode('[betterdocs_faq_list_classic '. implode(' ', $args) .']');
    }
}
?>