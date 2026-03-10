<?php
/**
 * ThessNest Child Theme Functions
 */

add_action( 'wp_enqueue_scripts', 'thessnest_child_enqueue_styles' );
function thessnest_child_enqueue_styles() {
    // Enqueue parent style
    wp_enqueue_style( 'thessnest-parent-style', get_template_directory_uri() . '/style.css' );
    
    // Enqueue child style
    wp_enqueue_style( 'thessnest-child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array( 'thessnest-parent-style' ),
        wp_get_theme()->get('Version')
    );
}

// Add your custom functions here
