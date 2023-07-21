<?php

/**
 * Template archive docs
 *
 * @link       https://wpdeveloper.com
 * @since      1.0.0
 *
 * @package    BetterDocs
 * @subpackage BetterDocs/public
 */

get_header();

$docs_orderby  = BetterDocs_DB::get_settings('alphabetically_order_post');
$docs_order    = BetterDocs_DB::get_settings('docs_order');
$terms_orderby = BetterDocs_DB::get_settings('terms_orderby');
$terms_order   = BetterDocs_DB::get_settings('terms_order');
$layout        = get_theme_mod( 'betterdocs_archive_layout_select', 'layout-1' ); // This controller is used to change the sidebar only Sidebar Layout (1, 2, 3, 4, 5)
$layout_class  = $layout == 'layout-2' ? ' doc-category-layout-2' : ''; // This class is only applied to sidebar layout 2

$nested_subcategory = BetterDocs_DB::get_settings('archive_nested_subcategory');

$current_category = get_queried_object() != null ? get_queried_object() : '';

    echo '<div class="betterdocs-category-wraper betterdocs-single-wraper">';
    $live_search = BetterDocs_DB::get_settings('live_search');
    if ( $live_search == 1 ) {
        echo BetterDocs_Public::search();
    }

	echo '<div class="betterdocs-content-area'.$layout_class.'">';
		$enable_archive_sidebar = BetterDocs_DB::get_settings('enable_archive_sidebar');
		if ($enable_archive_sidebar == 1) {
			include BetterDocs_Public::archive_sidebar_loader($layout);
		}

		echo '<div id="main" class="docs-listing-main">
			<div class="docs-category-listing">';
				$enable_breadcrumb = BetterDocs_DB::get_settings('enable_breadcrumb');

				if ($enable_breadcrumb == 1) {
					betterdocs_breadcrumbs();
				}
                $output = betterdocs_generate_output();
				$term_description = isset( $term->description ) ? $term->description : '';
				echo '<div class="docs-cat-title">';
					echo wp_sprintf('<'.BetterDocs_Helper::html_tag($output['betterdocs_archive_title_tag']).' class="docs-cat-heading">%s </'.BetterDocs_Helper::html_tag($output['betterdocs_sidebar_title_tag']).'>', $current_category->name);
					echo wp_sprintf('<p>%s</p>', esc_attr( $term_description ) );
				echo '</div>
				<div class="docs-list">';
					$multikb 	= apply_filters('betterdocs_cat_template_multikb', false);
					$args 	 	= BetterDocs_Helper::list_query_arg('docs', $multikb, $current_category->slug, -1, $docs_orderby, $docs_order);
					$args 	 	= apply_filters('betterdocs_articles_args', $args, $current_category->term_id);
					$post_query = new WP_Query($args);

					if ($post_query->have_posts()) :
						echo '<ul>';
						while ($post_query->have_posts()) : $post_query->the_post();
							echo '<li>' . BetterDocs_Helper::list_svg() . '<a href="' . get_the_permalink() . '">' . wp_kses(get_the_title(), BETTERDOCS_KSES_ALLOWED_HTML) . '</a></li>';
						endwhile;
						echo '</ul>';
					endif;
					wp_reset_query();

					// Sub category query
					if ($nested_subcategory == 1) {
						nested_category_list(
							$current_category->term_id,
							$multikb,
							'',
							'docs',
                            $docs_orderby,
                            $docs_order,
                            $terms_orderby,
                            $terms_order,
                            '',
                            ''
						);
					}
				echo '</div>
			</div>
		</div>
	</div>
</div>';

get_footer();