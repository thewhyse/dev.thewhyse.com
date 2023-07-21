<?php

$output = betterdocs_generate_output();
$terms_orderby = BetterDocs_DB::get_settings('terms_orderby');
$terms_order   = BetterDocs_DB::get_settings('terms_order');
if (BetterDocs_DB::get_settings('alphabetically_order_term') == 1) {
    $terms_orderby = 'name';
}
$multiple_kb = intval(BetterDocs_DB::get_settings('multiple_kb'));

echo '<aside id="betterdocs-sidebar-left" class="betterdocs-full-sidebar-left sidebar-layout-4 betterdocs-background">';
echo '<div data-simplebar="init" class="betterdocs-sidebar-content betterdocs-category-sidebar betterdocs-height">';
if ( $multiple_kb == 1 ) {
    $shortcode = do_shortcode( '[betterdocs_category_grid terms_order="'.$terms_order.'" terms_orderby="'.esc_html($terms_orderby).'" title_tag="'.BetterDocs_Helper::html_tag($output['betterdocs_sidebar_title_tag']).'" icon=0 post_counter=0 sidebar_list="true" posts_per_grid="-1" multiple_knowledge_base=true]' );
} else {
    $shortcode = do_shortcode( '[betterdocs_category_grid terms_order="'.$terms_order.'" terms_orderby="'.esc_html($terms_orderby).'" title_tag="'.BetterDocs_Helper::html_tag($output['betterdocs_sidebar_title_tag']).'" icon=0 post_counter=0 sidebar_list="true" posts_per_grid="-1"]' );
}
echo $shortcode;
echo '</div>';
echo '</aside>';