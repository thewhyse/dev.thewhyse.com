<?php
/**
 * The template to display the side menu
 *
 * @package ALLIANCE
 * @since ALLIANCE 1.0
 */
if ( false ) { // In this theme we don't need side-menu template. Instead of the side-menu template we use the mobile-menu template ?>
<div class="menu_side_wrap
<?php
echo ' menu_side_' . esc_attr( alliance_get_theme_option( 'menu_side_icons' ) > 0 ? 'icons' : 'dots' );
$alliance_menu_scheme = alliance_get_theme_option( 'menu_scheme' );
$alliance_header_scheme = alliance_get_theme_option( 'header_scheme' );
if ( ! empty( $alliance_menu_scheme ) && ! alliance_is_inherit( $alliance_menu_scheme  ) ) {
	echo ' scheme_' . esc_attr( $alliance_menu_scheme );
} elseif ( ! empty( $alliance_header_scheme ) && ! alliance_is_inherit( $alliance_header_scheme ) ) {
	echo ' scheme_' . esc_attr( $alliance_header_scheme );
}
?>
				">
	<span class="menu_side_button icon-menu-2"></span>

	<div class="menu_side_inner">
		<?php
		// Logo
		set_query_var( 'alliance_logo_args', array( 'type' => 'side' ) );
		get_template_part( apply_filters( 'alliance_filter_get_template_part', 'templates/header-logo' ) );
		set_query_var( 'alliance_logo_args', array() );
		// Main menu button
		?>
		<div class="toc_menu_item"
			<?php
			if ( alliance_mouse_helper_enabled() ) {
				echo ' data-mouse-helper="hover" data-mouse-helper-axis="y" data-mouse-helper-text="' . esc_attr__( 'Open main menu', 'alliance' ) . '"';
			}
			?>
		>
			<a href="#" class="toc_menu_description menu_mobile_description"><span class="toc_menu_description_title"><?php esc_html_e( 'Main menu', 'alliance' ); ?></span></a>
			<a class="menu_mobile_button toc_menu_icon icon-menu-2" href="#"></a>
		</div>		
	</div>

</div>
<?php }