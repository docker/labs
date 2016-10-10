<?php
/**
 * The template for displaying search results pages.
 *
 * @package zues
 */

 remove_action( 'zues_loop', 'zues_content', 20 );
 add_action( 'zues_loop', 'zues_content_excerpt', 20 );

 zues();
