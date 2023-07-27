<?php
/**
 * Child-Theme functions and definitions
 */

function alliance_child_scripts() {
    wp_enqueue_style( 'alliance-style', get_template_directory_uri(). '/style.css' );
}
add_action( 'wp_enqueue_scripts', 'alliance_child_scripts' );

/*
 * Quickly add Project Huddle inline css styles to only website projects
 */
function ph_add_inline_styles() {
    global $post;

    if ( 'ph-website' !== get_post_type($post) ) {
        return;
    }
    ?>
    .ph-panel-header {
        padding: 2rem;
    }
    .ph-panel-logo img.ph-object-contain.ph-object-left {
        max-height: 55px !important;
    }
    .ph-logo {
        max-height: 36px !important;
    }
    .ph-panel-header,
    .ph-toolbar__inner {
        background-color: #000000 !important;
    }
<?php }

// add inline css styles to all Project Huddle projects
add_action( 'ph_style_options_output', 'ph_add_inline_styles' );

/**
 * Add custom stylesheet to Project Huddle
 */
function whyse_projecthuddle_styles() {
	// bail if not a projecthuddle post type
	if ( ! ( is_singular('ph-project') || is_singular('ph-website') ) ) {
		return;
	}
	wp_enqueue_style( 'whyse_ph_style', get_stylesheet_directory_uri() . '/ph-stylesheet.css', '', '1.0' );
}
add_action( 'wp_enqueue_scripts', 'whyse_projecthuddle_styles' );

/**
 * IMPORTANT: Add PH stylesheet handle to allowed styles.
 * If we don't do this it will get removed on ProjectHuddle pages.
 */
function whyse_allowed_ph_styles( $allowed ) {
	$allowed[] = 'whyse_ph_style';
	return $allowed;
}
// add to PH mockup styls
add_filter( 'ph_allowed_styles', 'whyse_allowed_ph_styles' );

// add to PH website styles
add_filter( 'ph_allowed_website_styles', 'whyse_allowed_ph_styles' );

// Project Huddle - filter plugin settings
add_filter( 'ph_settings_extensions', 'whyse_github_extension_plugin_settings' );

// Project Huddle - add GitHub Repo settings
function whyse_github_extension_plugin_settings($settings) {
	// add fields

	// divider with title
	$settings['fields'][] = array(
		'id'          => 'highlight_divider',
		'label'       => __( 'GitHub Repository', 'project-huddle' ),
		'description' => '',
		'type'        => 'divider',
	);

	// see all field types in includes/admin/settings/settings-fields.php

    // text box example
	$settings['fields'][] = array(
		'id'          => 'whyse_github_extension_text_box',
		'label'       => __( 'Repository URL', 'project-huddle' ),
		'description' => __( 'To associate a GitHub repostory with this project, paste or enter it here.', 'project-huddle' ),
		'type'        => 'text',
		'default'     => '',
		'placeholder' => __( 'Project GitHub URL here...', 'project-huddle' ),
	);
	return $settings;
}


/**
 * Get our options
 */
$text_box = get_option('ph_whyse_github_extension_text_box');

?>