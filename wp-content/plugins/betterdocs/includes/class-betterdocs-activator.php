<?php

/**
 * Fired during plugin activation
 *
 * @link       https://wpdeveloper.com
 * @since      1.0.0
 *
 * @package    BetterDocs
 * @subpackage BetterDocs/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    BetterDocs
 * @subpackage BetterDocs/includes
 * @author     WPDeveloper <support@wpdeveloper.com>
 */
class BetterDocs_Activator {

	/**
	 * Detect plugin activation
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		if( current_user_can( 'delete_users' ) ) {
			set_transient( '_betterdocs_meta_activation_notice', true, 30 );
		}
		BetterDocs_Settings::save_default_settings();
	}

    public static function search_migration() {
        global $wpdb;
        $search_data = get_option( 'betterdocs_search_data' );
        if (!empty($search_data)) {
            $search_data_arr = unserialize($search_data);
            foreach ($search_data_arr as $key=>$value) {
                $args = array(
                    'post_type'      => 'docs',
                    'post_status'      => 'publish',
                    'posts_per_page'      => -1,
                    'suppress_filters' => true,
                    's' => $key
                );

                $loop = new WP_Query($args);
                if ($loop->have_posts()) {
                    $count = $value;
                    $not_found_count = 0;
                } else {
                    $count = 0;
                    $not_found_count = $value;
                }
                $insert = $wpdb->query(
                    $wpdb->prepare(
                        "INSERT INTO {$wpdb->prefix}betterdocs_search_keyword 
                        ( keyword )
                        VALUES ( %s )",
                        array(
                            $key
                        )
                    )
                );

                if ($insert) {
                    $wpdb->query(
                        $wpdb->prepare(
                            "INSERT INTO {$wpdb->prefix}betterdocs_search_log
                            (keyword_id, count, not_found_count, created_at)
                            VALUES (%d, %d, %d, %s)",
                            array(
                                $wpdb->insert_id,
                                $count,
                                $not_found_count,
                                date('Y-m-d')
                            )
                        )
                    );
                }
            }
            update_option( "betterdocs_search_data_migration", '1.0' );
        }
    }
}
