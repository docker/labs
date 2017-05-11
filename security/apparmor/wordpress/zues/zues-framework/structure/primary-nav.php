<?php
/**
 * Outputs the primary navigation.
 *
 * @package zues
 */

if ( ! function_exists( 'zues_nav_primary' ) ) {
	/**
	 * Outputs primary navigation.
	 */
	function zues_nav_primary() {

		$priority = array(
			'template-parts/primary-nav.php',
			'zues-framework/structure/template-parts/primary-nav.php',
		);

		locate_template( $priority, true );

	}
}

/**
 * Function for grabbing a WP nav menu theme location name.
 *
 * @since  2.0.0
 * @access public
 * @param  string $location
 * @return string
 */
function zues_get_menu_location_name( $location ) {
	$locations = get_registered_nav_menus();
	return isset( $locations[ $location ] ) ? $locations[ $location ] : '';
}
