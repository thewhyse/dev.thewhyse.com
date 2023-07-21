<?php
/**
 * The template to display the main menu
 *
 * @package ALLIANCE
 * @since ALLIANCE 1.0
 */
?>
<div class="top_panel_navi sc_layouts_row sc_layouts_row_type_narrow
	<?php
	if ( alliance_is_on( alliance_get_theme_option( 'header_mobile_enabled' ) ) ) {
		?>
		sc_layouts_hide_on_mobile
		<?php
	}
	?>
">
	<div class="content_wrap">
		<div class="columns_wrap columns_fluid"><?php
			// Side menu not exists
			if ( 'hide' == alliance_get_theme_option( 'menu_side' ) ) { ?>
				<div class="sc_layouts_column sc_layouts_column_align_left sc_layouts_column_icons_position_left sc_layouts_column_fluid column-1_3">
					<div class="sc_layouts_item">
						<?php
						// Logo
						get_template_part( apply_filters( 'alliance_filter_get_template_part', 'templates/header-logo' ) );
						?>
					</div>
				</div><div class="sc_layouts_column sc_layouts_column_align_right sc_layouts_column_icons_position_left sc_layouts_column_fluid column-2_3">
					<div class="sc_layouts_item">
						<?php
						// Main menu
						$alliance_menu_main = alliance_get_nav_menu( 'menu_main' );
						// Show any menu if no menu selected in the location 'menu_main'
						if ( alliance_get_theme_setting( 'autoselect_menu' ) && empty( $alliance_menu_main ) ) {
							$alliance_menu_main = alliance_get_nav_menu();
						}
						alliance_show_layout(
							$alliance_menu_main,
							'<nav class="menu_main_nav_area sc_layouts_menu sc_layouts_menu_default sc_layouts_hide_on_mobile"'
								. ' itemscope="itemscope" itemtype="' . esc_attr( alliance_get_protocol( true ) ) . '//schema.org/SiteNavigationElement"'
								. '>',
							'</nav>'
						);
						// Mobile menu button
						?>
						<div class="sc_layouts_iconed_text sc_layouts_menu_mobile_button">
							<a class="sc_layouts_item_link sc_layouts_iconed_text_link" href="#">
								<span class="sc_layouts_item_icon sc_layouts_iconed_text_icon trx_addons_icon-menu"></span>
							</a>
						</div>
					</div>
					<?php
					if ( false && alliance_exists_trx_addons() ) {
						?>
						<div class="sc_layouts_item">
							<?php
							// Display search field
							do_action(
								'alliance_action_search',
								array(
									'style' => 'fullscreen',
									'class' => 'header_search',
									'ajax'  => false
								)
							);
							?>
						</div>
						<?php
					}
					?>
				</div><?php
			} else { 
				?><div class="sc_layouts_column sc_layouts_column_align_left sc_layouts_column_icons_position_left sc_layouts_column_fluid column-1_1">					
					<div class="sc_layouts_item">
						<?php
						// Display search field
						do_action(
							'alliance_action_search',
							array(
								'style' => 'normal',
								'class' => 'header_search',
								'ajax'  => false
							)
						);
						?>
					</div>
				</div><?php
			} ?>
		</div>
	</div>
</div>
