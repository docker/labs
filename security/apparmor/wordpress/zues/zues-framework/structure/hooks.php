<?php
/**
 * Build the layout using the hooks.
 *
 * @package zues
 */

// Body hooks.
add_action( 'zues_body_open', 'zues_body_open_html', 10 );
add_action( 'zues_body_close', 'zues_body_close_html', 10 );

// Head hooks.
add_action( 'zues_header', 'zues_load_header_template', 10 );
add_action( 'zues_header_after', 'zues_nav_primary', 10 );

// Content hooks.
add_action( 'zues_content_sidebar_wrapper', 'zues_content_area', 10 );
add_action( 'zues_content_sidebar_wrapper', 'zues_sidebar_primary', 20 );

add_action( 'zues_content', 'zues_loop', 10 );
add_action( 'zues_content', 'zues_content_paging_nav', 20 );

// Entry Header hooks.
add_action( 'zues_entry_header', 'zues_entry_title', 10 );
add_action( 'zues_entry_header', 'zues_entry_meta', 15 );


// Loop Hooks.
add_action( 'zues_loop', 'zues_featured_image', 5 );
add_action( 'zues_loop', 'zues_entry_header', 10 );
add_action( 'zues_loop', 'zues_content', 20 );
add_action( 'zues_loop', 'zues_entry_footer', 30 );
add_action( 'zues_loop_after', 'zues_display_comments', 10 );
 add_action( 'zues_loop_after', 'zues_content_nav', 30 );

// Archive Page Hooks.
add_action( 'zues_loop_before', 'zues_archive_header', 20 );

// Search Page Hooks.
add_action( 'zues_loop_before', 'zues_search_header', 20 );

// Sidebar Hooks.
add_action( 'zues_sidebar_primary', 'zues_build_sidebar', 10 );

// Footer Hooks.
add_action( 'zues_footer', 'zues_load_footer_template', 10 );
add_action( 'zues_footer_after', 'zues_sub_footer', 10 );
add_action( 'zues_sub_footer', 'zues_footer_attribution', 15 );
add_action( 'zues_sub_footer', 'zues_footer_copyright', 20 );
add_action( 'zues_body_close_before', 'zues_wpfooter', 100 );
