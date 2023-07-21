<?php
/* WooCommerce skin-specific functions
------------------------------------------------------------------------------- */


// Theme init priorities:
// 4 - add/remove Theme Options elements
if ( ! function_exists( 'alliance_woocommerce_skin_theme_setup4' ) ) {
	add_action( 'after_setup_theme', 'alliance_woocommerce_skin_theme_setup4', 4 );
	function alliance_woocommerce_skin_theme_setup4() {
		if ( alliance_exists_woocommerce() ) {
		
			alliance_storage_set_array2( 'options', 'single_product_gallery_thumbs', 'type', 'hidden' );
			alliance_storage_set_array2( 'options', 'single_product_gallery_thumbs', 'std', 'left' );
			
			alliance_storage_set_array_before(
				'options', 'single_product_gallery_thumbs', array(				
					'page_content_single_shop'	=> array(
						'title'    => esc_html__( 'Page content', 'alliance' ),
						'desc'     => wp_kses_data( __( 'Choose between the style of your page content. The classic view provides you with the primary content block and a sidebar, while the masonry view positions each block on a separate background.', 'alliance' ) ),	
						'std'      => 'classic',
						'options'  => array(
							'classic'  => array(
										'title' => esc_html__( 'Classic', 'alliance' ),
										'icon'  => 'images/theme-options/page-content/classic.png',
									),
							'blocks'  => array(
										'title' => esc_html__( 'Blocks', 'alliance' ),
										'icon'  => 'images/theme-options/page-content/blocks.png',
									),
						),
						'type'     => 'choice',
					)					
				)
			);
		}
	}
}

// Theme init priorities:
// 3 - add/remove Theme Options elements
if ( ! function_exists( 'alliance_woocommerce_skin_theme_setup9' ) ) {
	add_action( 'after_setup_theme', 'alliance_woocommerce_skin_theme_setup9', 9 );
	function alliance_woocommerce_skin_theme_setup9() {
		if ( alliance_exists_woocommerce() ) {
			remove_action( 'single_product_archive_thumbnail_size', 'alliance_woocommerce_single_product_archive_thumbnail_size' );

			$pagination = alliance_get_theme_option('shop_pagination');
			if ( 'pages' == $pagination || 'numbers' == $pagination ) {
				remove_action( 'woocommerce_after_shop_loop', 'woocommerce_pagination' );
				add_action( 'woocommerce_before_shop_loop', 'woocommerce_pagination', 31 );
			}
		}
	}
}

// Return page content type
if ( ! function_exists( 'alliance_woocommerce_page_content_type' ) ) {
	add_filter( 'alliance_skin_filter_page_content_type', 'alliance_woocommerce_page_content_type' );
	function alliance_woocommerce_page_content_type( $page_content ) {	
		if ( alliance_exists_woocommerce() && is_product() ) {	
			$page_content = alliance_get_theme_option( 'page_content_single_shop' );
		}
		return $page_content;
	}
}

//  Remove related post from bottom of page
if ( ! function_exists( 'alliance_woocommerce_skin_theme_setup_wp' ) ) {
	add_action( 'wp', 'alliance_woocommerce_skin_theme_setup_wp' );
	function alliance_woocommerce_skin_theme_setup_wp() {
		if ( alliance_exists_woocommerce() && alliance_is_woocommerce_page() ) {
			remove_action( 'alliance_action_related_posts', 'alliance_show_related_posts_callback' );
		}
	}
}

// Add skin-specific layouts of products for the shop page and shortcodes
if ( ! function_exists( 'alliance_woocommerce_skin_add_product_layouts' ) ) {
	add_filter( 'alliance_filter_woocommerce_products_layouts', 'alliance_woocommerce_skin_add_product_layouts' );
	function alliance_woocommerce_skin_add_product_layouts( $list ) {
		return array_merge( $list, array(
				'light' => array(
					// Template title
					'title' => __( 'Light', 'alliance' ),
					// Array or space-separated string with additional classes for a products wrap (ul.products)
					'products_classes' => 'products_wrap_class1 products_wrap_class2',
					// Array or space-separated string with additional classes for each product (li.product)
					'product_classes' => 'product_item_class1 product_item_class2',
					// A theme or skin relative path to the template file. If empty - a default template from WooCommerce is used
					'template' => '',	// apply_filters( 'alliance_filter_get_theme_file_dir', '', 'plugins/woocommerce/templates/content-product-light.php' ),
				)
		) );
	}
}

// Open tag products header
if ( ! function_exists( 'alliance_woocommerce_posts_header_open' ) ) {
	add_action( 'alliance_action_before_list_products_header', 'alliance_woocommerce_posts_header_open', 10 );
	function alliance_woocommerce_posts_header_open() {
		if ( is_archive() ) {
			echo '<div class="list_products_top">';
		}
	}
}

// Open tag products sorting
if ( ! function_exists( 'alliance_woocommerce_posts_sorting_open' ) ) {
	add_action( 'woocommerce_before_shop_loop', 'alliance_woocommerce_posts_sorting_open', 10 );
	function alliance_woocommerce_posts_sorting_open() {
		if ( is_archive() ) {
			echo '<div class="list_products_sorting">';
		}
	}
}

// Close tag products sorting
if ( ! function_exists( 'alliance_woocommerce_posts_sorting_close' ) ) {
	add_action( 'woocommerce_before_shop_loop', 'alliance_woocommerce_posts_sorting_close', 40 );
	function alliance_woocommerce_posts_sorting_close() {
		if ( is_archive() ) {
			echo '</div>';
		}
	}
}

// Close tag products header
if ( ! function_exists( 'alliance_woocommerce_posts_header_close' ) ) {
	add_action( 'woocommerce_before_shop_loop', 'alliance_woocommerce_posts_header_close', 41 );
	add_action( 'woocommerce_no_products_found', 'alliance_woocommerce_posts_header_close', 10 );
	function alliance_woocommerce_posts_header_close() {
		if ( is_archive() ) {
			echo '</div>';
		}
	}
}

// Products hovers
if ( ! function_exists( 'alliance_woocommerce_shop_hover' ) ) {
	add_filter( 'alliance_filter_shop_hover', 'alliance_woocommerce_shop_hover', 10, 1 );
	function alliance_woocommerce_shop_hover( $list ) {
		unset($list['shop']);
		return $list;
	}
}

// Remove extensions options
if ( ! function_exists( 'alliance_woocommerce_extensions_allow_components' ) ) {
	add_filter( 'alliance_filter_woocommerce_extensions_allow_components', 'alliance_woocommerce_extensions_allow_components', 10, 2 );
	function alliance_woocommerce_extensions_allow_components( $allow, $type ) {
		if ( in_array($type, array('product_style', 'add_brand', 'gallery_style', 'details_position', 'additional_info', 'custom_tabs', 'add_to_cart_sticky', 'details_style', 'text_after_price', 'text_after_add_to_cart', 'add_attributes_to_product_list', 'product_summary_sticky') ) ) {
			return false;
		}
		return $allow;
	}
}

// Post class
if ( ! function_exists( 'alliance_woocommerce_post_class' ) ) {
	add_filter( 'woocommerce_post_class', 'alliance_woocommerce_post_class', 10, 2 );
	function alliance_woocommerce_post_class( $classes, $product ) {
		global $product;

		$classes[] = empty($product->get_price_html()) ? 'without_price' : '';
		
		$attachment_ids = $product->get_gallery_image_ids();
		$classes[] = is_array( $attachment_ids ) && count( $attachment_ids ) > 0 ? 'with-images' : 'without-images';
		return $classes;
	}
}

// Search form
if ( ! function_exists( 'alliance_woocommerce_skin_get_product_search_form' ) ) {
	add_filter( 'get_product_search_form', 'alliance_woocommerce_skin_get_product_search_form', 11 );
	function alliance_woocommerce_skin_get_product_search_form( $form ) {
		return '
		<form role="search" method="get" class="search_form" action="' . esc_url( home_url( '/' ) ) . '">
			<input type="text" class="search_field" placeholder="' . esc_attr__( 'Search for products &hellip;', 'alliance' ) . '" data-mobile-placeholder="' .  esc_attr__('Product search', 'alliance') . '"  value="' . get_search_query() . '" name="s" /><button class="search_button" type="submit">' . esc_html__( 'Search', 'alliance' ) . '</button>
			<input type="hidden" name="post_type" value="product" />
		</form>
		';
	}
}

// Price
if ( ! function_exists( 'alliance_woocommerce_skin_get_price_html' ) ) {
	add_filter( 'woocommerce_get_price_html', 'alliance_woocommerce_skin_get_price_html', 10, 2 );
	function alliance_woocommerce_skin_get_price_html( $price, $product ) {
		return $price;
	}
}


// Remove Account details tab
if ( ! function_exists( 'alliance_woocommerce_account_menu_items' ) ) {
	add_filter ( 'woocommerce_account_menu_items', 'alliance_woocommerce_account_menu_items' );
	function alliance_woocommerce_account_menu_items( $menu_links ){
		unset( $menu_links['edit-account'] ); 
		return $menu_links;
	}
}