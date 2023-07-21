<?php
/**
 * BuddyPress plugin widgets
 *
 * @addon bp-widgets
 * @version 1.3
 *
 * @package ThemeREX Addons
 * @since v2.8.0
 */

// Load widget BP Messages & Notifications
if (!function_exists('trx_addons_widget_bp_messages_notifications_load')) {
	add_action( 'widgets_init', 'trx_addons_widget_bp_messages_notifications_load' );
	function trx_addons_widget_bp_messages_notifications_load() {
		if ( function_exists('trx_addons_exists_bbpress') && trx_addons_exists_bbpress() ) {
			register_widget('trx_addons_widget_bp_messages_notifications');
		}
	}
}

// Widget Class BP Messages & Notifications
class trx_addons_widget_bp_messages_notifications extends TRX_Addons_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'widget_bp_messages_notifications', 'description' => esc_html__('BuddyPress Messages & Notifications', 'trx_addons'));
		parent::__construct( 'trx_addons_widget_bp_messages_notifications', esc_html__('ThemeREX BuddyPress Messages & Notifications', 'trx_addons'), $widget_ops );
	}

	// Show widget
	function widget($args, $instance) {
		extract($args);

		$title = apply_filters( 'widget_title', isset($instance['title']) ? $instance['title'] : '', $instance, $this->id_base );
		

		// Unread message count.
		$messages_count = 0;
		$messages_link = function_exists( 'bp_get_activity_directory_permalink' ) ? bp_get_activity_directory_permalink() : '';
        $messages_count = function_exists( 'messages_get_unread_count' ) ? messages_get_unread_count( bp_loggedin_user_id()) : '';
		if ( is_user_logged_in() ) {
                $bp_get_messages_slug = function_exists( 'bp_get_messages_slug' ) ? bp_get_messages_slug() : '';
			    $messages_link = trailingslashit( bp_loggedin_user_domain() . $bp_get_messages_slug );
		}

		// Unread notifications count.
		$notifications_count = 0;
		$notifications_link = function_exists( 'bp_get_activity_directory_permalink' ) ? bp_get_activity_directory_permalink() : '';
		if ( is_user_logged_in() ) {
			$notifications_count = bp_notifications_get_unread_notification_count( bp_displayed_user_id() );
			$notifications_link = trailingslashit( bp_loggedin_user_domain() . bp_get_notifications_slug() );	
		}

		$output = 	'<div class="bp_messages_notifications">
						<div class="messages">
							<a href="' . esc_url( $messages_link ) . '" class="icon-commit"></a>
							' . ( $messages_count > 0 ?  '<span class="count">' . bp_core_number_format( $messages_count ) . '</span>' : '' ) . '
						</div>
						<div class="notifications">
							<a href="' . esc_url( $notifications_link ) . '" class="icon-notification"></a>
							' . ( $notifications_count > 0 ?  '<span class="count">' . bp_core_number_format( $notifications_count ) . '</span>' : '' ) . '
						</div>
					</div>';

		if (!empty($output)) {
	
			// Before widget (defined by themes)
			trx_addons_show_layout($before_widget);
			
			// Display the widget title if one was input (before and after defined by themes)
			if ($title) trx_addons_show_layout($before_title . $title . $after_title);
	
			// Display widget body
			trx_addons_show_layout( $output );
			
			// After widget (defined by themes)
			trx_addons_show_layout($after_widget);
		}
	}

	// Update the widget settings.
	function update($new_instance, $instance) {
		$instance = array_merge($instance, $new_instance);
		$instance['title'] = strip_tags($new_instance['title']);
		return $instance;
	}

	// Displays the widget settings controls on the widget panel.
	function form($instance) {

		// Set up some default widget settings
		$instance = wp_parse_args( (array) $instance, array(
			'title' => '',
			)
		);
		
		do_action('trx_addons_action_before_widget_fields', $instance, 'trx_addons_widget_bp_messages_notifications', $this);

		$this->show_field(array('name' => 'title',
								'title' => __('Title:', 'trx_addons'),
								'value' => $instance['title'],
								'type' => 'text'));
		
		do_action('trx_addons_action_after_widget_title', $instance, 'trx_addons_widget_bp_messages_notifications', $this);
		
		do_action('trx_addons_action_after_widget_fields', $instance, 'trx_addons_widget_bp_messages_notifications', $this);
	}
}