<?php
/**
 * The template for displaying archive pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package zues
 */

 remove_action( 'zues_loop', 'zues_content', 20 );
 add_action( 'zues_loop', 'zues_content_excerpt', 20 );

 zues();
