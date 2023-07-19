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

<table class="form-table" role="presentation">
	<tbody>
		<tr class="record-number-wrap">
			<th><label for="record_number">Record Number</label></th>
			<td><input type="text" name="record_number" id="record_number" value="<?php echo esc_attr( $record_number ); ?>" class="large-text"> <span class="description">A unique value to identify the file in your drive/dropbox etc.</span></td>
		</tr>
		<tr class="asset-version-wrap">
			<th><label for="asset_version">Version Number</label></th>
			<td><input type="text" name="asset_version" id="asset_version" value="<?php echo esc_attr( $asset_version ); ?>" class="large-text"> <span class="description">Version number of the file.</span></td>
		</tr>
		<tr class="drive-url-wrap">
			<th><label for="drive_url">Drive URL</label></th>
			<td><input type="text" name="drive_url" id="drive_url" value="<?php echo esc_attr( $drive_url ); ?>" class="large-text"> <span class="description">Url where you have uploaded the file. It will help you to download the file within your dashboard.</span></td>
		</tr>
		<tr class="live-url-wrap">
			<th><label for="live_url">Live URL <span class="description"></span></label></th>
			<td><input type="text" name="live_url" id="live_url" value="<?php echo esc_attr( $live_url ); ?>" class="large-text"> <span class="description">URL where you can buy the license or see the demo.</span></td>
		</tr>
		<tr class="download-url-wrap">
			<th><label for="download_url">Download URL <span class="description"></span></label></th>
			<td><input type="text" name="download_url" id="download_url" value="<?php echo esc_attr( $download_url ); ?>" class="large-text"> <span class="description">URL to download this asset zip file on 1 click.</span></td>
		</tr>
		<tr class="license-key-wrap">
			<th><label for="license_key">License Key <span class="description"></span></label></th>
			<td><input type="text" name="license_key" id="license_key" value="<?php echo esc_attr( $license_key ); ?>" class="large-text"> <span class="description">Any key to activate this asset like purchase key, token etc.</span></td>
		</tr>
		<tr class="total-view-wrap">
			<th><label for="total_view">Total View <span class="description"></span></label></th>
			<td><input type="text" name="total_view" id="total_view" value="<?php echo esc_attr( $total_view ); ?>" class="large-text"> <span class="description">Set total view of this asset. Only work when Asset post type is set to public.</span></td>
		</tr>
		<tr class="total-download-wrap">
			<th><label for="total_download">Total Download<span class="description"></span></label></th>
			<td><input type="text" name="total_download" id="total_download" value="<?php echo esc_attr( $total_download ); ?>" class="large-text"> <span class="description">How many time this asset is downloaded. Only work when Asset post type is set to public.</span></td>
		</tr>
		<tr class="account-credentials-wrap">
			<th><label for="account_credentials">Account Credentials <span class="description"></span></label></th>
			<td>
			<?php
			wp_editor(
				$account_credentials,
				'account_credentials',
				array(
					'wpautop'        => true,
					'media_buttons'  => false,
					'textarea_name'  => 'account_credentials',
					'textarea_rows'  => 10,
					'teeny'          => false,
					'quicktags'      => true,
					'default_editor' => 'tinymce',
				)
			);
			?>
			<span class="description">Any account details where you can download the latest version of the files.</span></td>
		</tr>
		<tr class="private-note-wrap">
			<th><label for="private_note">Private Note <span class="description"></span></label></th>
			<td>
			<?php
			wp_editor(
				$private_note,
				'private_note',
				array(
					'wpautop'        => true,
					'media_buttons'  => false,
					'textarea_name'  => 'private_note',
					'textarea_rows'  => 10,
					'teeny'          => true,
					'quicktags'      => true,
					'default_editor' => 'tinymce',
					'tinymce'        => true,
				)
			);
			?>
			<span class="description"></span></td>
		</tr>
	</tbody>
</table>
