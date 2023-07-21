<?php
$output             = betterdocs_generate_output();
$terms_orderby      = BetterDocs_DB::get_settings('terms_orderby');
$terms_order        = BetterDocs_DB::get_settings('terms_order');
$enable_toc         = BetterDocs_DB::get_settings('enable_toc');
$enable_sticky_toc  = BetterDocs_DB::get_settings('enable_sticky_toc');

if (BetterDocs_DB::get_settings('alphabetically_order_term') == 1) {
    $terms_orderby = 'name';
}

$stick_toc_element = is_single() && $enable_toc == 1 && $enable_sticky_toc == 1 ? '<div class="sticky-toc-container"><a class="close-toc" href="#"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path style="line-height:normal;text-indent:0;text-align:start;text-decoration-line:none;text-decoration-style:solid;text-decoration-color:#000;text-transform:none;block-progression:tb;isolation:auto;mix-blend-mode:normal" d="M 4.9902344 3.9902344 A 1.0001 1.0001 0 0 0 4.2929688 5.7070312 L 10.585938 12 L 4.2929688 18.292969 A 1.0001 1.0001 0 1 0 5.7070312 19.707031 L 12 13.414062 L 18.292969 19.707031 A 1.0001 1.0001 0 1 0 19.707031 18.292969 L 13.414062 12 L 19.707031 5.7070312 A 1.0001 1.0001 0 0 0 18.980469 3.9902344 A 1.0001 1.0001 0 0 0 18.292969 4.2929688 L 12 10.585938 L 5.7070312 4.2929688 A 1.0001 1.0001 0 0 0 4.9902344 3.9902344 z"></path></svg></a></div><!-- #sticky toc -->' : '';

echo '<aside id="betterdocs-sidebar">';
echo '<div class="betterdocs-sidebar-content betterdocs-category-sidebar">';
$shortcode = do_shortcode( '[betterdocs_category_grid terms_order="'.$terms_order.'" terms_orderby="'.esc_html($terms_orderby).'" title_tag="'.BetterDocs_Helper::html_tag($output['betterdocs_sidebar_title_tag']).'" sidebar_list="true" posts_per_grid="-1"]' );
echo apply_filters( 'betterdocs_sidebar_category_shortcode', $shortcode, $terms_orderby, $terms_order);
echo '</div>';
echo $stick_toc_element;
echo '</aside><!-- #sidebar -->';