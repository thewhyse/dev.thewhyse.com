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

?>