<?php
/**
 * Child-Theme functions and definitions
 */

function alliance_child_scripts() {
    wp_enqueue_style( 'alliance-style', get_template_directory_uri(). '/style.css' );
}
add_action( 'wp_enqueue_scripts', 'alliance_child_scripts' );

/**
 * Add your custom stylesheet
 * Be sure to change 'my' to your own prefix to prevent conflicts
 */
function whyse_projecthuddle_styles() {
	wp_enqueue_style( 'whyse_ph_style', get_stylesheet_directory_uri() . '/ph-stylesheet.css', 'project-huddle', '1.0' );
}
add_action( 'wp_enqueue_scripts', 'whyse_projecthuddle_styles' );

/**
 * Add your stylesheet handle to allowed styles
 */
function whyse_allowed_ph_styles( $allowed ) {
	$allowed[] = 'whyse_ph_style';
	return $allowed;
}
add_filter( 'ph_allowed_styles', 'whyse_allowed_ph_styles' );

// Project Huddle - filter plugin settings
add_filter( 'ph_settings_extensions', 'whyse_github_extension_plugin_settings' );


// Project Huddle - add GitHub Repo settings
function whyse_github_extension_plugin_settings($settings) {
	// add fields

	// divider with title
	$settings['fields'][] = array(
		'id'          => 'highlight_divider',
		'label'       => __( 'GitHub Repository', 'dev-whyse' ),
		'description' => '',
		'type'        => 'divider',
	);

	// see all field types in includes/admin/settings/settings-fields.php

    // text box example
	$settings['fields'][] = array(
		'id'          => 'whyse_github_extension_text_box',
		'label'       => __( 'Repository URL', 'dev-whyse' ),
		'description' => __( 'To associate a GitHub repostory with this project, paste or enter it here.', 'dev-whyse' ),
		'type'        => 'text',
		'default'     => '',
		'placeholder' => __( 'Project GitHub URL here...', 'dev-whyse' ),
	);
	return $settings;
}


/**
 * Get our options
 */
$text_box = get_option('ph_whyse_github_extension_text_box');

?>