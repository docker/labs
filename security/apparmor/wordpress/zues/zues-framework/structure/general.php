<?php

if ( ! function_exists( 'zues_body_open_html' ) ) {
	/**
	 * Output opening body HTML.
	 */
	function zues_body_open_html() {

		echo '<body '. zues_get_attr( 'body' ) .'>';

	}
}

if ( ! function_exists( 'zues_body_close_html' ) ) {
	/**
	 * Output opening body HTML.
	 */
	function zues_body_close_html() {

		echo '</body>';

	}
}
