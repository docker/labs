<?php
/**
 * Helper functions for working with the WordPress sidebar system.  Currently, the framework creates a
 * simple function for registering HTML5-ready sidebars instead of the default WordPress unordered lists.
 *
 * @package    HybridCore
 * @subpackage Functions
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2008 - 2014, Justin Tadlock
 * @link       http://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

if ( ! function_exists( 'zues_register_widget_area' ) ) {
	/**
	 * Wrapper function for WordPress' register_sidebar() function.  This function exists so that theme authors
	 * can more quickly register sidebars with an HTML5 structure instead of having to write the same code
	 * over and over.  Theme authors are also expected to pass in the ID, name, and description of the sidebar.
	 * This function can handle the rest at that point.
	 *
	 * @since  2.0.0
	 * @access public
	 * @param  array $args Arguements used to build widget.
	 * @return string  Sidebar ID.
	 */
	function zues_register_widget_area( $args ) {

		/* Set up some default sidebar arguments. */
		$defaults = array(
		'id'            => '',
		'name'          => '',
		'description'   => '',
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section><!-- .widget -->',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
		);

		/* Parse the arguments. */
		$args = wp_parse_args( $args, $defaults );

		/* Remove action. */
		remove_action( 'widgets_init', '__return_false', 95 );

		/* Register the sidebar. */
		return register_sidebar( $args );
	}
}

/* Compatibility for when a theme doesn't register any sidebars. */
add_action( 'widgets_init', '__return_false', 95 );
