<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://2bytecode.com/
 * @since      1.0.0
 *
 * @package    Digital_Asset_Manager
 * @subpackage Digital_Asset_Manager/admin/partials
 */

?>

<div class="wrap">
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
	<?php settings_errors(); ?>

	<form method="post" action="options.php">';

	<?php
	// output security fields.
	settings_fields( 'dam_settings_options' );

	// output setting sections.
	do_settings_sections( 'dam-settings' );

	// submit button.
	submit_button();
	?>

	</form>
</div>
