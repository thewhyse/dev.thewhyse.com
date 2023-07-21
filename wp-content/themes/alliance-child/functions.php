<?php
/**
 * Child-Theme functions and definitions
 */

function alliance_child_scripts() {
    wp_enqueue_style( 'alliance-style', get_template_directory_uri(). '/style.css' );
}
add_action( 'wp_enqueue_scripts', 'alliance_child_scripts' );
?>