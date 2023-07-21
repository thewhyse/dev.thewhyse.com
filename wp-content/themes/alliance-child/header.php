<?php
/**
 * The Header: Logo and main menu
 *
 * @package ALLIANCE
 * @since ALLIANCE 1.0
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js<?php
	// Class scheme_xxx need in the <html> as context for the <body>!
	echo ' scheme_' . esc_attr( alliance_get_theme_option( 'color_scheme' ) );
?>">

<head>
<link rel="apple-touch-icon-precomposed" sizes="57x57" href="/icons/apple-touch-icon-57x57.png" />
<link rel="apple-touch-icon-precomposed" sizes="114x114" href="/icons/apple-touch-icon-114x114.png" />
<link rel="apple-touch-icon-precomposed" sizes="72x72" href="/icons/apple-touch-icon-72x72.png" />
<link rel="apple-touch-icon-precomposed" sizes="144x144" href="/icons/apple-touch-icon-144x144.png" />
<link rel="apple-touch-icon-precomposed" sizes="60x60" href="/icons/apple-touch-icon-60x60.png" />
<link rel="apple-touch-icon-precomposed" sizes="120x120" href="/icons/apple-touch-icon-120x120.png" />
<link rel="apple-touch-icon-precomposed" sizes="76x76" href="/icons/apple-touch-icon-76x76.png" />
<link rel="apple-touch-icon-precomposed" sizes="152x152" href="/icons/apple-touch-icon-152x152.png" />
<link rel="icon" type="image/png" href="/icons/favicon-196x196.png" sizes="196x196" />
<link rel="icon" type="image/png" href="/icons/favicon-96x96.png" sizes="96x96" />
<link rel="icon" type="image/png" href="/icons/favicon-32x32.png" sizes="32x32" />
<link rel="icon" type="image/png" href="/icons/favicon-16x16.png" sizes="16x16" />
<link rel="icon" type="image/png" href="/icons/favicon-128.png" sizes="128x128" />
<meta name="application-name" content="&nbsp;"/>
<meta name="msapplication-TileColor" content="#FFFFFF" />
<meta name="msapplication-TileImage" content="/icons/mstile-144x144.png" />
<meta name="msapplication-square70x70logo" content="/icons/mstile-70x70.png" />
<meta name="msapplication-square150x150logo" content="/icons/mstile-150x150.png" />
<meta name="msapplication-wide310x150logo" content="/icons/mstile-310x150.png" />
<meta name="msapplication-square310x310logo" content="/icons/mstile-310x310.png" />
<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

	<?php
	if ( function_exists( 'wp_body_open' ) ) {
		wp_body_open();
	} else {
		do_action( 'wp_body_open' );
	}
	do_action( 'alliance_action_before_body' );
	?>

	<div class="<?php echo esc_attr( apply_filters( 'alliance_filter_body_wrap_class', 'body_wrap' ) ); ?>" <?php do_action('alliance_action_body_wrap_attributes'); ?>>

		<?php do_action( 'alliance_action_before_page_wrap' ); ?>

		<div class="<?php echo esc_attr( apply_filters( 'alliance_filter_page_wrap_class', 'page_wrap' ) ); ?>" <?php do_action('alliance_action_page_wrap_attributes'); ?>>

			<?php do_action( 'alliance_action_page_wrap_start' ); ?>

			<?php
			$alliance_full_post_loading = ( alliance_is_singular( 'post' ) || alliance_is_singular( 'attachment' ) ) && alliance_get_value_gp( 'action' ) == 'full_post_loading';
			$alliance_prev_post_loading = ( alliance_is_singular( 'post' ) || alliance_is_singular( 'attachment' ) ) && alliance_get_value_gp( 'action' ) == 'prev_post_loading';

			// Don't display the header elements while actions 'full_post_loading' and 'prev_post_loading'
			if ( ! $alliance_full_post_loading && ! $alliance_prev_post_loading ) {

				// Short links to fast access to the content, sidebar and footer from the keyboard
				?>
				<a class="alliance_skip_link skip_to_content_link" href="#content_skip_link_anchor" tabindex="<?php echo esc_attr( apply_filters( 'alliance_filter_skip_links_tabindex', 1 ) ); ?>"><?php esc_html_e( "Skip to content", 'alliance' ); ?></a>
				<?php if ( alliance_sidebar_present() ) { ?>
				<a class="alliance_skip_link skip_to_sidebar_link" href="#sidebar_skip_link_anchor" tabindex="<?php echo esc_attr( apply_filters( 'alliance_filter_skip_links_tabindex', 1 ) ); ?>"><?php esc_html_e( "Skip to sidebar", 'alliance' ); ?></a>
				<?php } ?>
				<a class="alliance_skip_link skip_to_footer_link" href="#footer_skip_link_anchor" tabindex="<?php echo esc_attr( apply_filters( 'alliance_filter_skip_links_tabindex', 1 ) ); ?>"><?php esc_html_e( "Skip to footer", 'alliance' ); ?></a>

				<?php
				do_action( 'alliance_action_before_header' );

				// Header
				$alliance_header_type = alliance_get_theme_option( 'header_type' );
				if ( 'custom' == $alliance_header_type && ! alliance_is_layouts_available() ) {
					$alliance_header_type = 'default';
				}
				get_template_part( apply_filters( 'alliance_filter_get_template_part', "templates/header-" . sanitize_file_name( $alliance_header_type ) ) );

				// Side menu
				if ( in_array( alliance_get_theme_option( 'menu_side' ), array( 'left', 'right' ) ) ) {
					get_template_part( apply_filters( 'alliance_filter_get_template_part', 'templates/header-navi-side' ) );
				}

				// Mobile menu
				get_template_part( apply_filters( 'alliance_filter_get_template_part', 'templates/header-navi-mobile' ) );

				do_action( 'alliance_action_after_header' );

			}
			?>

			<?php do_action( 'alliance_action_before_page_content_wrap' ); ?>

			<div class="page_content_wrap<?php
				if ( alliance_is_off( alliance_get_theme_option( 'remove_margins' ) ) ) {
					if ( empty( $alliance_header_type ) ) {
						$alliance_header_type = alliance_get_theme_option( 'header_type' );
					}
					if ( 'custom' == $alliance_header_type && alliance_is_layouts_available() ) {
						$alliance_header_id = alliance_get_custom_header_id();
						if ( $alliance_header_id > 0 ) {
							$alliance_header_meta = alliance_get_custom_layout_meta( $alliance_header_id );
							if ( ! empty( $alliance_header_meta['margin'] ) ) {
								?> page_content_wrap_custom_header_margin<?php
							}
						}
					}
					$alliance_footer_type = alliance_get_theme_option( 'footer_type' );
					if ( 'custom' == $alliance_footer_type && alliance_is_layouts_available() ) {
						$alliance_footer_id = alliance_get_custom_footer_id();
						if ( $alliance_footer_id ) {
							$alliance_footer_meta = alliance_get_custom_layout_meta( $alliance_footer_id );
							if ( ! empty( $alliance_footer_meta['margin'] ) ) {
								?> page_content_wrap_custom_footer_margin<?php
							}
						}
					}
				}
				do_action( 'alliance_action_page_content_wrap_class', $alliance_prev_post_loading );
				?>"<?php
				if ( apply_filters( 'alliance_filter_is_prev_post_loading', $alliance_prev_post_loading ) ) {
					?> data-single-style="<?php echo esc_attr( alliance_get_theme_option( 'single_style' ) ); ?>"<?php
				}
				do_action( 'alliance_action_page_content_wrap_data', $alliance_prev_post_loading );
			?>>
				<?php
				do_action( 'alliance_action_page_content_wrap', $alliance_full_post_loading || $alliance_prev_post_loading );

				// Single posts banner
				if ( apply_filters( 'alliance_filter_single_post_header', alliance_is_singular( 'post' ) || alliance_is_singular( 'attachment' ) ) ) {
					if ( $alliance_prev_post_loading ) {
						if ( alliance_get_theme_option( 'posts_navigation_scroll_which_block' ) != 'article' ) {
							do_action( 'alliance_action_between_posts' );
						}
					}
					// Single post thumbnail and title
					$alliance_path = apply_filters( 'alliance_filter_get_template_part', 'templates/single-styles/' . alliance_get_theme_option( 'single_style' ) );
					if ( alliance_get_file_dir( $alliance_path . '.php' ) != '' ) {
						get_template_part( $alliance_path );
					}
				}

				// Widgets area above page
				$alliance_body_style   = alliance_get_theme_option( 'body_style' );
				$alliance_widgets_name = alliance_get_theme_option( 'widgets_above_page' );
				$alliance_show_widgets = ! alliance_is_off( $alliance_widgets_name ) && is_active_sidebar( $alliance_widgets_name );
				if ( $alliance_show_widgets ) {
					if ( 'fullscreen' != $alliance_body_style ) {
						?>
						<div class="content_wrap">
							<?php
					}
					alliance_create_widgets_area( 'widgets_above_page' );
					if ( 'fullscreen' != $alliance_body_style ) {
						?>
						</div>
						<?php
					}
				}

				// Content area
				do_action( 'alliance_action_before_content_wrap' );
				?>
				<div class="content_wrap<?php echo 'fullscreen' == $alliance_body_style ? '_fullscreen' : ''; ?>">

					<?php do_action( 'alliance_action_content_wrap_start' ); ?>

					<div class="content">
						<?php
						do_action( 'alliance_action_page_content_start' );

						// Skip link anchor to fast access to the content from keyboard
						?>
						<a id="content_skip_link_anchor" class="alliance_skip_link_anchor" href="#"></a>
						<?php
						// Single posts banner between prev/next posts
						if ( ( alliance_is_singular( 'post' ) || alliance_is_singular( 'attachment' ) )
							&& $alliance_prev_post_loading 
							&& alliance_get_theme_option( 'posts_navigation_scroll_which_block' ) == 'article'
						) {
							do_action( 'alliance_action_between_posts' );
						}

						// Widgets area above content
						alliance_create_widgets_area( 'widgets_above_content' );

						do_action( 'alliance_action_page_content_start_text' );
