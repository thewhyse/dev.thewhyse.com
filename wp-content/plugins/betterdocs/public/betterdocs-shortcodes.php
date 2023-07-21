<?php

/**
 * BetterDocs all shortcodes
 *
 * @link       https://wpdeveloper.com
 * @since      1.0.0
 *
 * @package    BetterDocs
 * @subpackage BetterDocs/public
 */

use WPML\FP\Str;

/**
 * Docs Reactions Shortcode
 * *
 * @since      1.0.2
 *
 */
add_shortcode('betterdocs_article_reactions', 'betterdocs_article_reaction');
function betterdocs_article_reaction($atts, $content = null)
{
	$get_args = shortcode_atts(
		array(
			'text' => ''
		),
		$atts
	);
	do_action( 'betterdocs_before_shortcode_load' );
	if ($get_args['text']) {
		$reactions_text = $get_args['text'];
	} else {
		$reactions_text = get_theme_mod('betterdocs_post_reactions_text', esc_html__('What are your Feelings', 'betterdocs'));
	}
	?>
	<div class="betterdocs-article-reactions">
		<div class="betterdocs-article-reactions-heading">
			<?php
			if ($reactions_text) {
				echo '<h5>' . esc_html($reactions_text) . '</h5>';
			}
			?>
		</div>
		<ul class="betterdocs-article-reaction-links">
			<li>
				<a class="betterdocs-feelings" data-feelings="happy" href="#">
					<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 20 20" style="enable-background:new 0 0 20 20;" xml:space="preserve">
						<path class="st0" d="M10,0.1c-5.4,0-9.9,4.4-9.9,9.8c0,5.4,4.4,9.9,9.8,9.9c5.4,0,9.9-4.4,9.9-9.8C19.9,4.5,15.4,0.1,10,0.1z
					M13.3,6.4c0.8,0,1.5,0.7,1.5,1.5c0,0.8-0.7,1.5-1.5,1.5c-0.8,0-1.5-0.7-1.5-1.5C11.8,7.1,12.5,6.4,13.3,6.4z M6.7,6.4
					c0.8,0,1.5,0.7,1.5,1.5c0,0.8-0.7,1.5-1.5,1.5c-0.8,0-1.5-0.7-1.5-1.5C5.2,7.1,5.9,6.4,6.7,6.4z M10,16.1c-2.6,0-4.9-1.6-5.8-4
					l1.2-0.4c0.7,1.9,2.5,3.2,4.6,3.2s3.9-1.3,4.6-3.2l1.2,0.4C14.9,14.5,12.6,16.1,10,16.1z" />
						<path class="st1" d="M-6.6-119.7c-7.1,0-12.9,5.8-12.9,12.9s5.8,12.9,12.9,12.9s12.9-5.8,12.9-12.9S0.6-119.7-6.6-119.7z
					M-2.3-111.4c1.1,0,2,0.9,2,2c0,1.1-0.9,2-2,2c-1.1,0-2-0.9-2-2C-4.3-110.5-3.4-111.4-2.3-111.4z M-10.9-111.4c1.1,0,2,0.9,2,2
					c0,1.1-0.9,2-2,2c-1.1,0-2-0.9-2-2C-12.9-110.5-12-111.4-10.9-111.4z M-6.6-98.7c-3.4,0-6.4-2.1-7.6-5.3l1.6-0.6
					c0.9,2.5,3.3,4.2,6,4.2s5.1-1.7,6-4.2L1-104C-0.1-100.8-3.2-98.7-6.6-98.7z" />
					</svg>
				</a>
			</li>
			<li>
				<a class="betterdocs-feelings" data-feelings="normal" href="#">
					<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 20 20" style="enable-background:new 0 0 20 20;" xml:space="preserve">
						<path class="st0" d="M10,0.2c-5.4,0-9.8,4.4-9.8,9.8s4.4,9.8,9.8,9.8s9.8-4.4,9.8-9.8S15.4,0.2,10,0.2z M6.7,6.5
				c0.8,0,1.5,0.7,1.5,1.5c0,0.8-0.7,1.5-1.5,1.5C5.9,9.5,5.2,8.9,5.2,8C5.2,7.2,5.9,6.5,6.7,6.5z M14.2,14.3H5.9
				c-0.3,0-0.6-0.3-0.6-0.6c0-0.3,0.3-0.6,0.6-0.6h8.3c0.3,0,0.6,0.3,0.6,0.6C14.8,14,14.5,14.3,14.2,14.3z M13.3,9.5
				c-0.8,0-1.5-0.7-1.5-1.5c0-0.8,0.7-1.5,1.5-1.5c0.8,0,1.5,0.7,1.5,1.5C14.8,8.9,14.1,9.5,13.3,9.5z" />
					</svg>
				</a>
			</li>
			<li>
				<a class="betterdocs-feelings" data-feelings="sad" href="#">
					<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 20 20" style="enable-background:new 0 0 20 20;" xml:space="preserve">
						<circle class="st0" cx="27.5" cy="0.6" r="1.9" />
						<circle class="st0" cx="36" cy="0.6" r="1.9" />
						<path class="st1" d="M10,0.3c-5.4,0-9.8,4.4-9.8,9.8s4.4,9.8,9.8,9.8s9.8-4.4,9.8-9.8S15.4,0.3,10,0.3z M13.3,6.6
					c0.8,0,1.5,0.7,1.5,1.5c0,0.8-0.7,1.5-1.5,1.5c-0.8,0-1.5-0.7-1.5-1.5C11.8,7.3,12.4,6.6,13.3,6.6z M6.7,6.6c0.8,0,1.5,0.7,1.5,1.5
					c0,0.8-0.7,1.5-1.5,1.5C5.9,9.6,5.2,9,5.2,8.1C5.2,7.3,5.9,6.6,6.7,6.6z M14.1,15L14.1,15c-0.2,0-0.4-0.1-0.5-0.2
					c-0.9-1-2.2-1.7-3.7-1.7s-2.8,0.6-3.7,1.7C6.2,14.9,6,15,5.9,15h0c-0.6,0-0.8-0.6-0.5-1.1c1.1-1.3,2.8-2.1,4.6-2.1
					c1.8,0,3.5,0.8,4.6,2.1C15,14.3,14.7,15,14.1,15z" />
					</svg>
				</a>
			</li>
		</ul>
	</div> <!-- Social Share end-->
<?php
}

/**
 * Get terms post count including child terms
 */
function betterdocs_get_postcount($term_count, $term_id, $nested_subcategory=false)
{
    if ($nested_subcategory==false) {
        return $term_count;
    }

	$taxonomy = 'doc_category';

	$args = array(
		'child_of' => $term_id
	);

	$tax_terms = get_terms($taxonomy, $args);

	if ($tax_terms) {
		foreach ($tax_terms as $tax_term) {
			$term_count += $tax_term->count;
		}
	}

	return $term_count;
}

/**
 * Get the category grid with docs list.
 * *
 * @since      1.0.0
 * *
 * @param int $atts Get attributes for the categories.
 * @param int $content Get content to category.
 */
add_shortcode('betterdocs_category_grid', 'betterdocs_category_grid');
function betterdocs_category_grid($atts, $content = null)
{
    do_action( 'betterdocs_before_shortcode_load' );
	ob_start();
	global $wp_query;

	$column_val          = '';
	$masonry_layout      = BetterDocs_DB::get_settings('masonry_layout');
	$alphabetic_order    = BetterDocs_DB::get_settings('alphabetically_order_post');
	$column_number       = BetterDocs_DB::get_settings('column_number');
	$posts_number        = BetterDocs_DB::get_settings('posts_number');
	$exploremore_btn     = BetterDocs_DB::get_settings('exploremore_btn');
	$exploremore_btn_txt = BetterDocs_DB::get_settings('exploremore_btn_txt');
	$get_args = shortcode_atts(
		array(
			'sidebar_list'             => false,
			'post_type'                => 'docs',
			'category'                 => 'doc_category',
			'orderby'                  => BetterDocs_DB::get_settings('alphabetically_order_post'),
            'order'                    => BetterDocs_DB::get_settings('docs_order'),
			'post_counter'             => BetterDocs_DB::get_settings('post_count') != 'off' ? 'true' : 'false',
			'icon'                     => true,
			'masonry'                  => '',
			'column'                   => '',
			'posts_per_grid'           => '',
			'nested_subcategory'       => BetterDocs_DB::get_settings('nested_subcategory') != 'off' ? 'true' : 'false',
			'terms'                    => '',
            'terms_orderby'            => '',
            'terms_order'              => '',
            'kb_slug'                  => '',
			'multiple_knowledge_base'  => false,
			'disable_customizer_style' => false,
            'title_tag'                => 'h2'
		),
		$atts
	);

	$post_counter       	  = ( $get_args['post_counter'] == 'true' ) ? true : false;
	$post_icon_check          = ( $get_args['icon'] == 'true' ) ? true : false;
	$masonry_check            = ( $get_args['masonry'] == 'true' ) ? true : false;
    $nested_subcategory       =  $get_args['nested_subcategory'] === 'true' ? true : false;
    $masonry = ($masonry_layout == 1 && $masonry_check   == '') || ($masonry_check == true && $masonry_check != "false");
	$taxonomy_objects = BetterDocs_Helper::taxonomy_object($get_args['multiple_knowledge_base'], $get_args['terms'], $get_args['terms_order'], $get_args['terms_orderby'], $get_args['kb_slug'], $nested_subcategory);
    if ($taxonomy_objects && !is_wp_error($taxonomy_objects)) {
		$class = ['betterdocs-categories-wrap category-grid white-bg'];
		if (!is_singular('docs') && !is_tax('doc_category') && !is_tax('doc_tag')) {
			if ($get_args['sidebar_list'] == true) {
				$class[] = 'layout-flex';
			} elseif ($masonry == true) {
                wp_enqueue_script('masonry');
				$class[] = 'layout-masonry';
			} else {
				$class[] = 'layout-flex';
			}

			if ($get_args['sidebar_list'] == true) {
				$column_val = 1;
			} elseif (isset($get_args['column']) && $get_args['column'] == true && is_numeric($get_args['column'])) {
				$column_val = $get_args['column'];
			} else {
				$column_val = $column_number;
			}
			$class[] = 'docs-col-' . $column_val;
			if ($get_args['disable_customizer_style'] == false) {
				$class[] = 'single-kb';
			}
		}

		echo '<div class="'. implode(' ', $class) .'" data-column="'. esc_html($column_val) .'">';
			$term_list = wp_get_post_terms(get_the_ID(), 'doc_category', array("fields" => "all"));
			// get single page category id
			if (is_single() && !empty($term_list)) {
				$category_id = array_column($term_list, 'term_id');
				$cat_index   = isset( $category_id[0] ) ? $category_id[0] : '';
				$ancestors   = get_ancestors($cat_index, 'doc_category');
				$page_cat    = get_the_ID();
			} else {
				$category_id = array();
				$page_cat    = '';
				$ancestors   = array();
			}
			/**
			 * Get Queried Object - For KB
			 */

			// display category grid by order
			foreach ($taxonomy_objects as $term) {
			    //for single doc category page ID
			    $current_cat_id = get_queried_object();
			    //for single post Category ID
                $current_post_cat_id = get_the_terms( get_the_ID(), 'doc_category');
			    $term_id = $term->term_id;
                $term_slug = $term->slug;
                $count = $term->count;
                $get_term_count = betterdocs_get_postcount($count, $term_id, $nested_subcategory);
                $term_count = apply_filters('betterdocs_postcount', $get_term_count, $get_args['multiple_knowledge_base'], $term_id, $term_slug, $count, $nested_subcategory);
                $current_term_id = isset( $current_post_cat_id[0]->term_id ) ? $current_post_cat_id[0]->term_id : '';
				if ($term_count > 0) {
					if (is_single() && ( $current_term_id == $term_id || ( $nested_subcategory == 1 && in_array($term_id, $ancestors )  ) ) ) {
						// set active category class in single page
						$wrap_class = 'docs-single-cat-wrap current-category';
						$title_class = 'docs-cat-title-wrap active-title';
					} elseif( BetterDocs_Helper::get_tax() == 'doc_category' && $current_cat_id->term_id == $term_id ) {
						$wrap_class = 'docs-single-cat-wrap current-category';
						$title_class = 'docs-cat-title-wrap active-title';
					} else {
						// by default docs page
						$wrap_class = 'docs-single-cat-wrap';
						$title_class = 'docs-cat-title-wrap';
                    }

					$cat_icon_id = get_term_meta($term_id, 'doc_category_image-id', true);

					if ($cat_icon_id) {
						$cat_icon_url = wp_get_attachment_image_url($cat_icon_id, 'thumbnail');
						$cat_icon = '<img class="docs-cat-icon" src="' . $cat_icon_url . '" alt="'.$term->name.'">';
					} else {
						$cat_icon = '<img class="docs-cat-icon" src="' . BETTERDOCS_ADMIN_URL . 'assets/img/betterdocs-cat-icon.svg" alt="'.$term->name.'">';
					}

					if ($post_icon_check == false) {
						$cat_icon = '';
					}

					echo '<div id="cat-id-'. esc_attr($term_id) .'" class="'. esc_attr($wrap_class) .'">
						<div class="'. esc_attr($title_class) .'">
							<div class="docs-cat-title-inner">';
								$term_permalink = BetterDocs_Helper::term_permalink('doc_category', $term->slug);

								if ($get_args['sidebar_list'] == true) {
									echo '<div class="docs-cat-title">' . $cat_icon . '<'. BetterDocs_Helper::validate_html_tag($get_args['title_tag']) .' class="docs-cat-heading">' . $term->name . '</'. BetterDocs_Helper::validate_html_tag($get_args['title_tag']) .'></div>';
								} else {
									echo '<div class="docs-cat-title">' . $cat_icon . '<a href="' . esc_url($term_permalink) . '"><'. BetterDocs_Helper::validate_html_tag($get_args['title_tag']) .' class="docs-cat-heading">' . $term->name . '</'. BetterDocs_Helper::validate_html_tag($get_args['title_tag']) .'></a></div>';
								}

								if ( $post_counter === true ) {
									echo '<div class="docs-item-count"><span>' . $term_count . '</span></div>';
								}

								echo '<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="angle-down" class="cat-list-arrow-down svg-inline--fa fa-angle-down fa-w-10" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512">
									<path fill="currentColor" d="M143 352.3L7 216.3c-9.4-9.4-9.4-24.6 0-33.9l22.6-22.6c9.4-9.4 24.6-9.4 33.9 0l96.4 96.4 96.4-96.4c9.4-9.4 24.6-9.4 33.9 0l22.6 22.6c9.4 9.4 9.4 24.6 0 33.9l-136 136c-9.2 9.4-24.4 9.4-33.8 0z"></path>
								</svg>
							</div>
						</div>
						<div class="docs-item-container">';
							if (isset($get_args['posts_per_grid']) && $get_args['posts_per_grid'] == true && is_numeric($get_args['posts_per_grid'])) {
								$posts_per_grid = $get_args['posts_per_grid'];
							} else {
								$posts_per_grid = $posts_number;
							}

							$list_args = BetterDocs_Helper::list_query_arg('docs', $get_args['multiple_knowledge_base'], $term_slug, $posts_per_grid, $get_args['orderby'], $get_args['order'], $get_args['kb_slug']);
							$args = apply_filters('betterdocs_articles_args', $list_args, $term->term_id);
							$post_query = new WP_Query($args);
							if ($post_query->have_posts()) :
								echo '<ul>';
								while ($post_query->have_posts()) : $post_query->the_post();
									$attr = ['href="' . get_the_permalink() . '"'];
									if ($page_cat === get_the_ID() && BetterDocs_Helper::get_tax() != 'doc_category') {
										$attr[] = 'class="active"';
									}
									echo '<li>' . BetterDocs_Helper::list_svg() . '<a ' . implode(' ', $attr) . '>' . wp_kses(get_the_title(), BETTERDOCS_KSES_ALLOWED_HTML) . '</a></li>';
								endwhile;
								echo '</ul>';
							endif;
							wp_reset_query();

							// Sub category query
							if ($nested_subcategory == true) {
								nested_category_list(
									$term_id,
									$get_args['multiple_knowledge_base'],
									$category_id,
									'docs',
                                    $get_args['orderby'],
                                    $get_args['order'],
                                    $get_args['terms_orderby'],
                                    $get_args['terms_order'],
									$page_cat,
                                    $get_args['kb_slug']
								);
							}

							// Read More Button
							if ($get_args['posts_per_grid'] == '-1' || $posts_number == '-1') {
								echo '';
							} else if ($exploremore_btn == 1 && !is_singular('docs') && BetterDocs_Helper::get_tax() != 'doc_category' && !is_tax('doc_tag')) {
								echo '<a class="docs-cat-link-btn" href="' . $term_permalink . '">' . esc_html(stripslashes($exploremore_btn_txt)) . '</a>';
							}
						echo '</div>
					</div>';
                }
			}
		echo '</div>';
	    }

        if ($masonry == true ) {
            $output = betterdocs_generate_output();
            echo '<script>
            jQuery(document).ready(function() {
                let masonryGrid = jQuery(".betterdocs-categories-wrap.layout-masonry");
                let columnPerGrid = jQuery(".betterdocs-categories-wrap.layout-masonry").attr("data-column");
                let masonryItem = jQuery(".betterdocs-categories-wrap.layout-masonry .docs-single-cat-wrap");
                let doc_page_column_space = '.$output['betterdocs_doc_page_column_space'].';
                let total_margin = (columnPerGrid - 1) * doc_page_column_space;
                if (masonryGrid.length) {
                    masonryItem.css("width", "calc((100% - "+total_margin+"px) / "+parseInt(columnPerGrid)+")");
                    masonryGrid.masonry({
                        itemSelector: ".docs-single-cat-wrap",
                        percentPosition: true,
                        gutter: doc_page_column_space
                    });
                }
            });
            </script>';
        }
		return ob_get_clean();
	}

	function nested_category_list($term_id, $multiple_kb, $category_id, $post_type, $docs_orderby, $docs_order, $terms_orderby, $terms_order, $page_cat, $kb_slug='', $nested_posts_num = -1) {
		$sub_categories = BetterDocs_Helper::child_taxonomy_terms($term_id, $multiple_kb, $terms_orderby, $terms_order, $kb_slug);
		if ($sub_categories) {
			foreach ($sub_categories as $sub_category) {
				if (is_single() && in_array($sub_category->term_id, $category_id)) {
					$subcat_class = 'docs-sub-cat current-sub-cat';
				} else {
					$subcat_class = 'docs-sub-cat';
				}

				echo '<span class="docs-sub-cat-title">
					' . BetterDocs_Helper::arrow_right_svg() . '
					' . BetterDocs_Helper::arrow_down_svg() . '
					<a href="#">' . $sub_category->name . '</a></span>';

				echo '<ul class="' . esc_attr($subcat_class) . '">';
				$sub_args = BetterDocs_Helper::list_query_arg($post_type, $multiple_kb, $sub_category->slug, $nested_posts_num, $docs_orderby, $docs_order, $kb_slug);
				$sub_args = apply_filters('betterdocs_articles_args', $sub_args, $sub_category->term_id);
				$sub_post_query = new WP_Query($sub_args);

				if ($sub_post_query->have_posts()) :
					while ($sub_post_query->have_posts()) : $sub_post_query->the_post();
						$sub_attr = ['href="' . get_the_permalink() . '"'];
						if ($page_cat === get_the_ID() && BetterDocs_Helper::get_tax() != 'doc_category') {
							$sub_attr[] = 'class="active"';
						}
						echo '<li class="sub-list">' . BetterDocs_Helper::list_svg() . '<a ' . implode(' ', $sub_attr) . '>' . wp_kses(get_the_title(), BETTERDOCS_KSES_ALLOWED_HTML) . '</a></li>';
					endwhile;
				endif;
				wp_reset_query();
				nested_category_list( $sub_category->term_id, $multiple_kb, $category_id, $post_type, $docs_orderby, $docs_order, $terms_orderby, $terms_order, $page_cat, $kb_slug, $nested_posts_num );
				echo '</ul>';
			}
		}
	}


	/**
	 * Get the category grid with docs list.
	 * *
	 * @since      1.0.0
	 * *
	 * @param int $atts Get attributes for the categories.
	 * @param int $content Get content to category.
	 */
	add_shortcode('betterdocs_category_list', 'betterdocs_category_list');
	function betterdocs_category_list($atts, $content = null)
	{
        do_action( 'betterdocs_before_shortcode_load' );
		ob_start();
		$alphabetic_order = BetterDocs_DB::get_settings('alphabetically_order_post');
		$nested_subcategory = BetterDocs_DB::get_settings('nested_subcategory');
		$get_args = shortcode_atts(
			array(
				'post_type' => 'docs',
				'category' => 'doc_category',
                'orderby' => BetterDocs_DB::get_settings('alphabetically_order_post'),
                'order' => BetterDocs_DB::get_settings('docs_order'),
				'masonry' => '',
				'column' => '',
				'posts_per_page' => '',
				'nested_subcategory' => '',
				'terms' => '',
                'terms_orderby' => '',
                'terms_order' => '',
                'kb_slug' => '',
				'multiple_knowledge_base' => false,
                'title_tag' => 'h2'
			),
			$atts
		);

        $nested_subcategory = ($nested_subcategory == 1 && $get_args['nested_subcategory'] == '') || ($get_args['nested_subcategory'] == true && $get_args['nested_subcategory'] != "false");
		$taxonomy_objects = BetterDocs_Helper::taxonomy_object($get_args['multiple_knowledge_base'], $get_args['terms'], $get_args['terms_order'], $get_args['terms_orderby'], $get_args['kb_slug'], $nested_subcategory);

		if ($taxonomy_objects && !is_wp_error($taxonomy_objects)) : ?>
		<div class="betterdocs-categories-wrap category-list">
			<?php
			$term_list = wp_get_post_terms(get_the_ID(), 'doc_category', array("fields" => "all"));

			// get single page category id
			if (is_single() && $term_list) {
				$category_id = array_column($term_list, 'term_id');
				$page_cat = get_the_ID();
			} else {
				$category_id = array();
				$page_cat = '';
			}

			/**
			 * For Multiple KB
			 */
			$q_object = get_queried_object();
			$kb_slug = '';

			if ($q_object instanceof WP_Term) {
				$kb_slug = $q_object->slug;
			}

			// display category grid by order
			foreach ($taxonomy_objects as $term) {
				$term_id = $term->term_id;
				$term_slug = $term->slug;
                $count = $term->count;
                $get_term_count = betterdocs_get_postcount($count, $term_id, $nested_subcategory);
                $term_count = apply_filters('betterdocs_postcount', $get_term_count, $get_args['multiple_knowledge_base'], $term_id, $term_slug, $count, $nested_subcategory);
                if ($term_count > 0) {
                    // set active category class in single page
                    if (is_single() && in_array($term_id, $category_id)) {
                        $wrap_class = 'docs-single-cat-wrap-2 current-category';
                        $title_class = 'active-title';
                    } else {
                        $wrap_class = 'docs-single-cat-wrap-2';
                        $title_class = '';
                    }

                    $term_permalink = BetterDocs_Helper::term_permalink('doc_category', $term_slug);
                    ?>
                    <div id="cat-id-<?php echo $term_id; ?>" class="cat tet <?php echo esc_attr($wrap_class) ?>">
                        <div class="<?php echo esc_attr($title_class) ?>">
                            <div class="docs-cat-title-inner">
                                <?php
                                echo '<div class="docs-cat-title"><a href="' . esc_url($term_permalink) . '"><'. BetterDocs_Helper::validate_html_tag($get_args['title_tag']) .' class="docs-cat-heading">' . $term->name . '</'. BetterDocs_Helper::validate_html_tag($get_args['title_tag']) .'></a></div>';
                                ?>
                            </div>
                        </div>
                        <div class="docs-item-container">
                            <?php
                            $list_args = BetterDocs_Helper::list_query_arg('docs', $get_args['multiple_knowledge_base'], $term_slug, -1, $get_args['orderby'], $get_args['order'], $kb_slug);
                            $args = apply_filters('betterdocs_articles_args', $list_args, $term->term_id);
                            $post_query = new WP_Query($args);
                            if ($post_query->have_posts()) :

                                echo '<ul>';
                                while ($post_query->have_posts()) : $post_query->the_post();
                                    $attr = ['href="' . get_the_permalink() . '"'];
                                    if ($page_cat === get_the_ID()) {
                                        $attr[] = 'class="active"';
                                    }
                                    echo '<li><a ' . implode(' ', $attr) . '>' . wp_kses(get_the_title(), BETTERDOCS_KSES_ALLOWED_HTML) . '</a></li>';
                                endwhile;

                                echo '</ul>';

                            endif;
                            wp_reset_query();

                            // Sub category query
                            if (($nested_subcategory == 1 || $get_args['nested_subcategory'] == true) && $get_args['nested_subcategory'] != "false") {
                                nested_category_list(
                                    $term_id,
                                    $get_args['multiple_knowledge_base'],
                                    $category_id,
                                    'docs',
                                    $get_args['orderby'],
                                    $get_args['order'],
                                    $get_args['terms_orderby'],
                                    $get_args['terms_order'],
                                    $page_cat,
                                    ''
                                );
                            }
                            ?>
                        </div>
                    </div>
                <?php }
			} ?>
		</div>
	<?php
		endif;
		return ob_get_clean();
	}

	/**
	 * Get the category grid with docs list.
	 * *
	 * @since      1.0.0
	 * *
	 * @param int $atts Get attributes for the categories.
	 * @param int $content Get content to category.
	 */
	add_shortcode('betterdocs_category_box', 'betterdocs_category_box');
	function betterdocs_category_box($atts, $content = null)
	{
        do_action( 'betterdocs_before_shortcode_load' );
		ob_start();
		$column_number = BetterDocs_DB::get_settings('column_number');
		$post_count = BetterDocs_DB::get_settings('post_count');
		$count_text_singular = BetterDocs_DB::get_settings('count_text_singular');
		$count_text = BetterDocs_DB::get_settings('count_text');
        $nested_subcategory = BetterDocs_DB::get_settings('nested_subcategory');
		$get_args = shortcode_atts(
			array(
				'post_type' => 'docs',
				'category' => 'doc_category',
                'orderby' => BetterDocs_DB::get_settings('alphabetically_order_post'),
				'column' => '',
				'nested_subcategory' => '',
				'terms' => '',
                'terms_orderby' => BetterDocs_DB::get_settings('alphabetically_order_term'),
				'terms_order' => '',
				'icon' => true,
                'kb_slug' => '',
                'title_tag' => 'h2',
				'multiple_knowledge_base' => false,
				'disable_customizer_style' => false,
				'border_bottom' => false
			),
			$atts
		);
		$post_icon_check    = ( $get_args['icon'] == 'true' ) ? true : false;
        $nested_subcategory = ($nested_subcategory == 1 && $get_args['nested_subcategory'] == '') || ($get_args['nested_subcategory'] == true && $get_args['nested_subcategory'] != "false");
        $taxonomy_objects = BetterDocs_Helper::taxonomy_object($get_args['multiple_knowledge_base'], $get_args['terms'], $get_args['terms_order'], $get_args['terms_orderby'], $get_args['kb_slug'], $nested_subcategory);

		if ($taxonomy_objects && !is_wp_error($taxonomy_objects)) :
			$class = ['betterdocs-categories-wrap betterdocs-category-box layout-2 ash-bg'];
			$class[] = 'layout-flex';

			if (isset($get_args['column']) && $get_args['column'] == true && is_numeric($get_args['column'])) {
				$class[] = 'docs-col-' . $get_args['column'];
			} else {
				$class[] = 'docs-col-' . $column_number;
			}

            if (isset($get_args['border_bottom']) && $get_args['border_bottom'] == true) {
                $class[] = 'border-bottom';
            }

			if ($get_args['disable_customizer_style'] == false) {
				$class[] = 'single-kb';
			}

			echo '<div class="'.implode(' ', $class).'">';
				// display category grid by order
				foreach ($taxonomy_objects as $term) {
					$term_id = $term->term_id;
                    $term_slug = $term->slug;
                    $count = $term->count;
                    $get_term_count = betterdocs_get_postcount($count, $term_id, $nested_subcategory);
                    $term_count = apply_filters('betterdocs_postcount', $get_term_count, $get_args['multiple_knowledge_base'], $term_id, $term_slug, $count, $nested_subcategory);

                    if ($term_count > 0) {
					// set active category class in single page
					$wrap_class = 'docs-single-cat-wrap';
					$term_permalink = BetterDocs_Helper::term_permalink('doc_category', $term->slug);
					echo '<a href="'.esc_url($term_permalink).'" class="'.esc_attr($wrap_class).'" id="cat-id-'.$term_id.'">';
					if($post_icon_check) {
						$cat_icon_id = get_term_meta($term_id, 'doc_category_image-id', true);
						if ($cat_icon_id) {
							echo wp_get_attachment_image($cat_icon_id, 'thumbnail');
						} else {
							echo '<img class="docs-cat-icon" src="' . BETTERDOCS_ADMIN_URL . 'assets/img/betterdocs-cat-icon.svg" alt="">';
						}
					}
						echo '<'. BetterDocs_Helper::validate_html_tag($get_args['title_tag']) .' class="docs-cat-title">' . $term->name . '</'. BetterDocs_Helper::validate_html_tag($get_args['title_tag']) .'>';
						$cat_desc = get_theme_mod('betterdocs_doc_page_cat_desc');
						if ($cat_desc == true) {
							echo '<p class="cat-description">' . $term->description . '</p>';
						}
						if ($post_count == 1) {
							if ($term->count == 1) {
								echo wp_sprintf('<span class="docs-count">%s %s</span>', $term_count, ($count_text_singular) ? $count_text_singular : __('article', 'betterdocs'));
							} else {
								echo wp_sprintf('<span class="docs-count">%s %s</span>', $term_count, ($count_text) ? $count_text : __('articles', 'betterdocs'));
							}
						}
					echo '</a>';
					}
				}
            echo '</div>';
		endif;
		return ob_get_clean();
	}

    /**
     * Advance search search form with live dropdown result
     * *
     * @since 1.0.0
     *
     */
    add_shortcode('betterdocs_search_form', 'betterdocs_search_form');
    function betterdocs_search_form($atts, $content = null)
    {
        $get_args = shortcode_atts(
            apply_filters('betterdocs_search_form_atts', array(
                'placeholder' => false,
                'heading' => false,
                'subheading' => false,
				'heading_tag' => 'h2',
				'subheading_tag' => 'h3'
            )),
            $atts
        );

        do_action( 'betterdocs_before_shortcode_load' );

        ob_start();
        echo '<div class="betterdocs-live-search">';
            if ( $get_args['heading'] == true || $get_args['subheading'] == true ) {
                echo '<div class="betterdocs-search-heading">';
                    if ( $get_args['heading'] == true ) {
                        echo '<'.$get_args['heading_tag'].' class="heading"> ' . esc_html($get_args['heading']) . ' </'.$get_args['heading_tag'].'>';
                    }

                    if ( $get_args['subheading'] == true ) {
                        echo '<'.$get_args['subheading_tag'].' class="subheading"> ' . esc_html($get_args['subheading']) . ' </'.$get_args['subheading_tag'].'>';
                    }
                echo '</div>';
            }

            do_action('betterdocs_before_live_search_form', $get_args);

            echo '<form class="betterdocs-searchform betterdocs-advance-searchform">
                <div class="betterdocs-searchform-input-wrap">
                    <svg class="docs-search-icon" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="38px" viewBox="0 0 50 50" version="1.1">
                        <g id="surface1">
                            <path style=" " d="M 21 3 C 11.601563 3 4 10.601563 4 20 C 4 29.398438 11.601563 37 21 37 C 24.355469 37 27.460938 36.015625 30.09375 34.34375 L 42.375 46.625 L 46.625 42.375 L 34.5 30.28125 C 36.679688 27.421875 38 23.878906 38 20 C 38 10.601563 30.398438 3 21 3 Z M 21 7 C 28.199219 7 34 12.800781 34 20 C 34 27.199219 28.199219 33 21 33 C 13.800781 33 8 27.199219 8 20 C 8 12.800781 13.800781 7 21 7 Z "></path>
                        </g>
                    </svg>
                    <input type="text" class="betterdocs-search-field" name="s" placeholder="'. $get_args['placeholder'] .'" autocomplete="off" value="'.get_search_query().'">
                    <svg class="docs-search-loader" width="38" height="38" viewBox="0 0 38 38" xmlns="http://www.w3.org/2000/svg" stroke="#444b54">
                        <g fill="none" fill-rule="evenodd">
                            <g transform="translate(1 1)" stroke-width="2">
                                <circle stroke-opacity=".5" cx="18" cy="18" r="18" />
                                <path d="M36 18c0-9.94-8.06-18-18-18">
                                    <animateTransform attributeName="transform" type="rotate" from="0 18 18" to="360 18 18" dur="1s" repeatCount="indefinite" />
                                </path>
                            </g>
                        </g>
                    </svg>
                    <svg class="docs-search-close" xmlns="http://www.w3.org/2000/svg" width="38px" viewBox="0 0 128 128">
                        <path fill="#fff" d="M64 14A50 50 0 1 0 64 114A50 50 0 1 0 64 14Z" transform="rotate(-45.001 64 64.001)"></path>
                        <path class="close-border" d="M64,117c-14.2,0-27.5-5.5-37.5-15.5c-20.7-20.7-20.7-54.3,0-75C36.5,16.5,49.8,11,64,11c14.2,0,27.5,5.5,37.5,15.5c10,10,15.5,23.3,15.5,37.5s-5.5,27.5-15.5,37.5C91.5,111.5,78.2,117,64,117z M64,17c-12.6,0-24.4,4.9-33.2,13.8c-18.3,18.3-18.3,48.1,0,66.5C39.6,106.1,51.4,111,64,111c12.6,0,24.4-4.9,33.2-13.8S111,76.6,111,64s-4.9-24.4-13.8-33.2S76.6,17,64,17z"></path>
                        <path class="close-line" d="M53.4,77.6c-0.8,0-1.5-0.3-2.1-0.9c-1.2-1.2-1.2-3.1,0-4.2l21.2-21.2c1.2-1.2,3.1-1.2,4.2,0c1.2,1.2,1.2,3.1,0,4.2L55.5,76.7C54.9,77.3,54.2,77.6,53.4,77.6z"></path>
                        <path class="close-line" d="M74.6,77.6c-0.8,0-1.5-0.3-2.1-0.9L51.3,55.5c-1.2-1.2-1.2-3.1,0-4.2c1.2-1.2,3.1-1.2,4.2,0l21.2,21.2c1.2,1.2,1.2,3.1,0,4.2C76.1,77.3,75.4,77.6,74.6,77.6z"></path>
                    </svg>
                </div>';

                do_action('betterdocs_live_search_form_footer', $get_args);

                echo '<input type="hidden" value="Search" class="betterdocs-search-submit">';
                echo '</form>';

                do_action('betterdocs_after_live_search_form', $get_args);

        echo '</div>';
        return ob_get_clean();
    }

/**
 * Get the search result from ajax load.
 * *
 * @since      1.0.0
 *
 */
add_action('wp_ajax_nopriv_betterdocs_get_search_result', 'betterdocs_get_search_result');
add_action('wp_ajax_betterdocs_get_search_result', 'betterdocs_get_search_result');
function betterdocs_get_search_result() {
    global $wpdb;
	$search_input = isset($_POST['search_input']) ? sanitize_text_field($_POST['search_input']) : '';
	$search_cat = isset($_POST['search_cat']) ? wp_strip_all_tags($_POST['search_cat']) : '';
    $search_input = preg_replace('/[^A-Za-z0-9_\- ][]]/', '', strtolower($search_input));
	$args = array(
		'post_type'      => 'docs',
		'post_status'      => 'publish',
		'posts_per_page'      => -1,
		'suppress_filters' => true,
		's' => $search_input,
	);
	$tax_query = '';
    if($search_cat) {
        $tax_query = array(
            array(
                'taxonomy' => 'doc_category',
                'field'     => 'slug',
                'terms'    => $search_cat,
                'operator' => 'AND',
                'include_children' => true
            )
        );
    }

	$args['tax_query'] = array(
        apply_filters('betterdocs_live_search_tax_query', $tax_query, $_POST)
    );

    $args = apply_filters('betterdocs_articles_args', $args);
	$loop = new WP_Query($args);
	$output = '<div class="betterdocs-search-result-wrap"><ul class="docs-search-result">';
	if ($loop->have_posts()) :
        $input_not_found = '';
		while ($loop->have_posts()) : $loop->the_post();
			preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', get_the_content(), $matches);

			if ($matches[1]) {
				$first_img = $matches[1][0];
			} else {
				$first_img = '';
			}

			$terms = get_the_terms(get_the_ID(), 'doc_category');
			$terms_name = array();

			if ( $terms && ! is_wp_error( $terms ) ) {
				foreach ($terms as $term) {
					$terms_name[] = $term->name;
				}
			}

			$all_terms = join(", ", $terms_name);
			$icon = '';
			$search_result_image = BetterDocs_DB::get_settings('search_result_image');
			if ($search_result_image == 1 && has_post_thumbnail()) {
				$icon = get_the_post_thumbnail();
			} elseif ($search_result_image == 1 && !empty($first_img)) {
				$icon = '<img src="' . $first_img . '" alt="">';
			}
			$output .= '<li>' . $icon . '<a href="' . get_permalink() . '"><span class="betterdocs-search-title">' . wp_kses(get_the_title(), BETTERDOCS_KSES_ALLOWED_HTML) . '</span><br><span class="betterdocs-search-category">' . $all_terms . '</span></a></li>';
		endwhile;
	else :
        $input_not_found = $search_input;
		$output .= '<li>' . stripslashes(BetterDocs_DB::get_settings('search_not_found_text')) . '</li>';
	endif;
	$output .= '</ul></div>';
    $response[ 'post_lists' ] = $output;

    if ( $output && strlen( $search_input ) >= 3) {
        BetterDocs_Helper::search_insert($search_input, $input_not_found);
    }
    wp_send_json_success( $response );
	wp_reset_postdata();
	die();
}

/**
 * feedback form shortcode
 * *
 * @since      1.0.0
 *
 */
add_shortcode('betterdocs_feedback_form', 'betterdocs_feedback_form');
function betterdocs_feedback_form($atts, $content = null)
{
    do_action( 'betterdocs_before_shortcode_load' );
	$get_args = shortcode_atts(
		array(
			'button_text' => esc_html__('Send', 'betterdocs')
		),
		$atts
	);
	ob_start();
	if (is_user_logged_in()) {
		$userdata = get_userdata(get_current_user_id());
		$name = $userdata->first_name . ' ' . $userdata->last_name;
        $email = $userdata->user_email;
	} else {
		$name = '';
        $email = '';
	}

	?>
	<div class="form-wrapper">
		<div class="response"></div>
		<form id="betterdocs-feedback-form" class="betterdocs-feedback-form" action="" method="post">
			<p><label for="message_name" class="form-name">
					<?php esc_html_e('Name:', 'betterdocs') ?> <span>*</span> <br>
					<input type="text" id="message_name" name="message_name" value="<?php echo esc_html($name) ?>">
				</label>
			</p>
			<p><label for="message_email" class="form-email">
					<?php esc_html_e('Email:', 'betterdocs') ?> <span>*</span> <br>
					<input type="text" id="message_email" name="message_email" value="<?php echo esc_html($email) ?>">
				</label>
			</p>
			<p><label for="message_text" class="form-message">
					<?php esc_html_e('Message:', 'betterdocs') ?> <span>*</span> <br>
					<textarea type="text" id="message_text" name="message_text"></textarea>
				</label>
			</p>
			<div class="feedback-from-button">
				<input type="hidden" name="submitted" value="1">
				<input type="submit" name="submit" class="button" id="feedback_form_submit_btn" value="<?php echo esc_attr($get_args['button_text']) ?>" />
			</div>
		</form>
	</div>
<?php
	return ob_get_clean();
}

/**
 * Submit form via ajax
 * *
 * @since      1.0.0
 *
 */
add_action('wp_ajax_nopriv_betterdocs_feedback_form_submit', 'betterdocs_feedback_form_submit');
add_action('wp_ajax_betterdocs_feedback_form_submit', 'betterdocs_feedback_form_submit');

function betterdocs_feedback_form_submit()
{
	check_ajax_referer( 'betterdocs_submit_data', 'security' );
	$postID = isset($_POST['postID']) ? $_POST['postID'] : '';
	$article = get_the_title($postID);
	$name = isset($_POST['message_name']) ? sanitize_text_field(stripslashes($_POST['message_name'])) : '';
	$email = isset($_POST['message_email']) ? sanitize_email(stripslashes($_POST['message_email'])) : '';
	$message_text = isset($_POST['message_text']) ? sanitize_textarea_field(stripslashes($_POST['message_text'])) : '';
	$message = <<<EOD
	Name : {$name} <br>
	Docs : {$article}
	Email: {$email}
	{$message_text}
EOD;

	//response messages
	$missing_name    = esc_html__('Please enter your name.', 'betterdocs');
	$email_invalid   = esc_html__('Enter a valid email address.', 'betterdocs');
	$missing_message = esc_html__('Please write your message.', 'betterdocs');
	$message_unsent  = esc_html__('Message was not sent. Try Again.', 'betterdocs');
	$message_sent    = esc_html__('Thanks! Your message has been sent.', 'betterdocs');

	//php mailer variables
	$to = BetterDocs_DB::get_settings('email_address');
	if (empty($to)) {
		$to = get_option('admin_email');
	}

	$subject = wp_sprintf( '%s %s', __( 'Feedback message from', 'betterdocs' ), get_bloginfo( 'name' ) );
	$headers = 'From: ' . $email . "\r\n" .
		'Reply-To: ' . $email . "\r\n";
	$response = array();

	//validate presence of name
	if (empty($name)) {
		$response['nameStatus'] = 'error';
		$response['nameMessage'] = $missing_name;
	}

	//validate email
	if (empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$response['emailStatus'] = 'error';
		$response['emailMessage'] = $email_invalid;
	}

	//validate presence of message
	if (empty($message_text)) {
		$response['messageStatus'] = 'error';
		$response['messageMessage'] = $missing_message;
	}

	if (!empty($name) && !empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL) && !empty($message_text)) {
		$sent = wp_mail($to, $subject, strip_tags($message), $headers);
		if ($sent) {
			$response['sentStatus'] = 'success';
			$response['sentMessage'] = $message_sent;
		} else {
			$response['sentStatus'] = 'error';
			$response['sentMessage'] = $message_unsent;
		}
	}
	echo json_encode($response);
	die();
}

/**
 * Social Share Shortcode
 * *
 * @since      1.0.0
 *
 */
add_shortcode('betterdocs_social_share', 'betterdocs_social_share');
function betterdocs_social_share($atts, $content = null)
{
    do_action( 'betterdocs_before_shortcode_load' );
	$get_args = shortcode_atts(
		array(
			'title' => esc_html__('Share This Article :', 'betterdocs'),
			'facebook_sharing' => '1',
			'twitter_sharing' => '1',
			'linkedin_sharing' => '1',
			'pinterest_sharing' => '1'
		),
		$atts
	);
	$thumbnail = '';
	if (function_exists('has_post_thumbnail')) {
		if (has_post_thumbnail()) {
			$thumbnail = wp_get_attachment_url(get_post_thumbnail_id());
		}
	}

	?>
	<div class="betterdocs-social-share">
		<div class="betterdocs-social-share-heading">
			<?php
				if ($get_args['title']) :
					echo '<h5>' . esc_html($get_args['title']) . '</h5>';
				endif;
			?>
		</div>
		<ul class="betterdocs-social-share-links">
			<?php if ($get_args['facebook_sharing'] == true) : ?>
				<li><a href="https://www.facebook.com/sharer/sharer.php?u=<?php the_permalink(); ?>" target="_blank"><img src="<?php echo BETTERDOCS_URL ?>public/img/facebook.svg" alt="Facebook"></a></li>
			<?php endif; ?>

			<?php if ($get_args['twitter_sharing'] == true) : ?>
				<li><a href="https://twitter.com/intent/tweet?url=<?php the_permalink(); ?>" target="_blank"><img src="<?php echo BETTERDOCS_URL ?>public/img/twitter.svg" alt="Twitter"></a></li>
			<?php endif; ?>

			<?php if ($get_args['linkedin_sharing'] == true) : ?>
				<li><a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php the_permalink(); ?>&title=&summary=&source=" target="_blank"><img src="<?php echo BETTERDOCS_URL ?>public/img/linkedin.svg" alt="LinkedIn"></a></li>
			<?php endif; ?>

			<?php if ($get_args['pinterest_sharing'] == true) : ?>
				<li><a href="https://pinterest.com/pin/create/button/?url=<?php the_permalink(); ?>&media=<?php echo $thumbnail; ?>&description=" target="_blank"><img src="<?php echo BETTERDOCS_URL ?>public/img/pinterest.svg" alt="Pinterest"></a></li>
			<?php endif; ?>
		</ul>
	</div> <!-- Social Share end-->
<?php }


add_shortcode('betterdocs_post_content', 'betterdocs_post_content');
function betterdocs_post_content($atts, $content = null)
{
    do_action( 'betterdocs_before_shortcode_load' );
	$get_args = shortcode_atts(
		array(
			'post_id' => get_the_ID(),
			'htags' => 'h1,h2,h3,h4,h5,h6',
			'enable_toc' => '',
			'toc_hierarchy' => '',
			'list_number' => '',
			'display_toc_on_top' => '',
			'collapsible_toc_mobile' => ''
		),
		$atts
	);

	$get_post = get_post($get_args['post_id']);
	$post_content = $get_post->post_content;
	$the_content = BetterDocs_Public::betterdocs_the_content(
		$post_content,
		$get_args['htags'],
		$get_args['enable_toc']
	);

	return $the_content;
}

add_shortcode('betterdocs_faq_list_modern', 'betterdocs_faq_list_modern');

function betterdocs_faq_list_modern( $atts, $content = null ) {
	do_action( 'betterdocs_before_shortcode_load' );

	ob_start();

	$get_args = shortcode_atts(
		array(
			'groups' 		=> '',
			'class' 		=> '',
			'group_exclude' => '',
			'faq_heading'	=> __('Frequently Asked Questions', 'betterdocs'),
			'faq_schema'	=> false
		),
		$atts
	);

	$class 		  = ! empty( $get_args['class'] ) ? ' '.$get_args['class'] : '';

	$faq_terms    = BetterDocs_Helper::faq_category_terms( $get_args['groups'], $get_args['group_exclude'] );

	$faq_markup   = ! empty( $faq_terms ) ? '<h2 class="betterdocs-faq-section-title'.$class.'">'.$get_args['faq_heading'].'</h2>' : '';

	$faq_markup  .= '<div class="betterdocs-faq-main-wrapper betterdocs-faq-main-wrapper-layout-1'.$class.'">';

	if( ! is_wp_error( $faq_terms ) ) {

		if ( $get_args['faq_schema'] == 'true' ) {
			$json = [
				'@context' => 'https://schema.org',
				'@type' => 'FAQPage',
				'mainEntity' => [],
			];
		}

		foreach( $faq_terms as $term ) {
			$term_count = isset( $term->count ) ? $term->count : '';
			$term_title = isset( $term->name ) ? $term->name : '';
			$term_id  = isset( $term->term_id ) ? $term->term_id : '';
			$faq_markup .= '<div class="betterdocs-faq-title"><h2>'.$term_title.'</h2></div>';
			if( $term_count > 0 ) {
				$faq_args = $faq_terms = BetterDocs_Helper::faq_args( $term_id );
				$faq_query = new WP_Query( $faq_args );
				if ( $faq_query->have_posts() ) {
					$faq_markup .= '<ul class="betterdocs-faq-list">';
					while ( $faq_query->have_posts() ) : $faq_query->the_post();
						$faq_markup .= '<li>';
						$faq_markup .= '<div class="betterdocs-faq-group">';
						$faq_markup .= '<div class="betterdocs-faq-post">';
						$faq_markup .= '<span class="betterdocs-faq-post-name">'.  get_the_title() .'</span>';
						$faq_markup .= '<svg class="betterdocs-faq-iconminus" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2"><g fill="none" stroke="#528ffe" stroke-linecap="round" stroke-miterlimit="10" stroke-linejoin="round"><path d="M17 12H7"></path><circle cx="12" cy="12" r="11"></circle></g></svg>';
						$faq_markup .= '<svg class="betterdocs-faq-iconplus" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><g stroke-width="2" fill="none" stroke="#528ffe" stroke-linecap="square" stroke-miterlimit="10"><path d="M12 7v10M17 12H7"></path><circle cx="12" cy="12" r="11"></circle></g></svg>';
						$faq_markup .= '</div>';
						$faq_markup .= '<div class="betterdocs-faq-main-content">';
						$faq_markup .= get_the_content();
						$faq_markup .= '</div>';
						$faq_markup .= '</li>';
						if ( $get_args['faq_schema'] == 'true' ) {
							$json['mainEntity'][] = [
								'@type' => 'Question',
								'name' => get_the_title(),
								'acceptedAnswer' => [
									'@type' => 'Answer',
									'text' => get_the_content(),
								],
							];
						}
					endwhile;
					$faq_markup .= '</ul>';

					wp_reset_postdata();
				} else {
					echo '<p>' . esc_html__( 'Sorry, no FAQ matched your criteria.', 'betterdocs') . '</p>';
				}
			}
		}

		if ( $get_args['faq_schema'] == 'true' ) {
			echo '<script type="application/ld+json">'. wp_json_encode( $json ) .'</script>';
		}
	}
	$faq_markup .= '</div>';

	echo $faq_markup;

	return ob_get_clean();
}


add_shortcode('betterdocs_faq_list_classic', 'betterdocs_faq_list_classic');

function betterdocs_faq_list_classic( $atts, $content = null ) {
	do_action( 'betterdocs_before_shortcode_load' );

	ob_start();

	$get_args = shortcode_atts(
		array(
			'groups'  		=> '',
			'class'  		=> '',
			'group_exclude' => '',
			'faq_heading'   => __('Frequently Asked Questions', 'betterdocs'),
			'faq_schema'	=> false
		),
		$atts
	);

	$class 	     = ! empty( $get_args['class'] ) ? ' '.$get_args['class'] : '';

	$faq_terms   = BetterDocs_Helper::faq_category_terms( $get_args['groups'], $get_args['group_exclude'] );

	$faq_markup  = ! empty( $faq_terms ) ? '<h2 class="betterdocs-faq-section-title'.$class.'">'.$get_args['faq_heading'].'</h2>' : '';

	$faq_markup .= '<div class="betterdocs-faq-main-wrapper betterdocs-faq-main-wrapper-layout-2'.$class.'">';

	if( ! is_wp_error( $faq_terms ) ) {

		if ( $get_args['faq_schema'] == 'true' ) {
			$json = [
				'@context' => 'https://schema.org',
				'@type' => 'FAQPage',
				'mainEntity' => [],
			];
		}

		foreach( $faq_terms as $term ) {
			$term_count = isset( $term->count ) ? $term->count : '';
			$term_title = isset( $term->name ) ? $term->name : '';
			$term_slug  = isset( $term->slug ) ? $term->slug : '';
			$term_id  = isset( $term->term_id ) ? $term->term_id : '';

			$faq_markup .= '<div class="betterdocs-faq-title betterdocs-faq-title-layout-2"><h2>'.$term_title.'</h2></div>';

			if ( $term_count > 0 ) {
				$faq_args = $faq_terms = BetterDocs_Helper::faq_args( $term_id );
				$faq_query = new WP_Query( $faq_args );
				if( $faq_query->have_posts() ) {
					$faq_markup .= '<ul class="betterdocs-faq-list betterdocs-faq-list-layout-2">';
					while ( $faq_query->have_posts() ) : $faq_query->the_post();
						$faq_markup .= '<li>';
						$faq_markup .= '<div class="betterdocs-faq-group betterdocs-faq-group-layout-2">';
						$faq_markup .= '<div class="betterdocs-faq-post-layout-2">';
						$faq_markup .= '<div class="betterdocs-faq-post-layout-2-icon-group">';
						$faq_markup .= '<svg class="betterdocs-faq-iconplus-layout-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="#000000" d="M18 10h-4V6h-4v4H6v4h4v4h4v-4h4"></path></svg>';
						$faq_markup .= '<svg class="betterdocs-faq-iconminus-layout-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="#000000" d="M6 10h12v4H6z"></path></svg>';
						$faq_markup .= '</div>';
						$faq_markup .= '<span class="betterdocs-faq-post-name betterdocs-faq-post-name-layout-2">'. get_the_title() .'</span>';
						$faq_markup .= '</div>';
						$faq_markup .= '<div class="betterdocs-faq-main-content betterdocs-faq-main-content-layout-2">';
						$faq_markup .= get_the_content();
						$faq_markup .= '</div>';
						$faq_markup .= '</li>';
						if ( $get_args['faq_schema'] == 'true' ) {
							$json['mainEntity'][] = [
								'@type' => 'Question',
								'name' => get_the_title(),
								'acceptedAnswer' => [
									'@type' => 'Answer',
									'text' => get_the_content(),
								],
							];
						}
					endwhile;
					$faq_markup .= '</ul>';



					wp_reset_postdata();
				} else {
					echo '<p>' . esc_html__( 'Sorry, no FAQ matched your criteria.', 'betterdocs') . '</p>';
				}
			}
		}

		if ( $get_args['faq_schema'] == 'true' ) {
			echo '<script type="application/ld+json">'. wp_json_encode( $json ) .'</script>';
		}
	}

	$faq_markup  .= '</div>';

	echo $faq_markup;

	return ob_get_clean();
}