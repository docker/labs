<?php
/**
 * Functions used to build the page-*.php templates.
 *
 * @package zues
 */

if ( ! function_exists( 'zues_archive_header' ) ) {
	/**
	 * Output the header for archive pages.
	 */
	function zues_archive_header() {

		$priority = array(
			'template-parts/archive-header.php',
			'zues-framework/structure/template-parts/archive-header.php',
		);

		locate_template( $priority, true );

	}
}

if ( ! function_exists( 'zues_search_header' ) ) {
	/**
	 * Output the header for search pages.
	 */
	function zues_search_header() {

		$priority = array(
			'template-parts/search-header.php',
			'zues-framework/structure/template-parts/search-header.php',
		);

		locate_template( $priority, true );
	}
}
