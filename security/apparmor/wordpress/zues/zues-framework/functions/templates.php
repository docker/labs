<?php
/**
 * Zues template functions
 *
 * @package zues
 */

/**
 * Return a list of available header templates.
 *
 * @return  array The list of templates.
 * @todo  check against child theme
 */
function zues_get_headers() {

	require_once( ABSPATH . 'wp-admin/includes/file.php' );

	WP_Filesystem();

	global $wp_filesystem;

	$header_templates = array();

	$dir = THEME_DIR . '/template-parts/headers/';

	if ( is_dir( $dir ) ) {

		if ( $dh = opendir( $dir ) ) {

			while ( ($file = readdir( $dh )) !== false ) {
				if ( '.' !== $file && '..' !== $file ) {

					if ( ! preg_match( '|Header Name:(.*)$|mi', $wp_filesystem->get_contents( $dir.$file ), $header ) ) {
						continue;
					}

					$header_templates[ $file ] = _cleanup_header_comment( $header[1] );
				}
			}

			closedir( $dh );
		}

		return $header_templates;
	}

}

	/**
	 * Return a list of available footer templates.
	 *
	 * @return  array The list of templates.
	 */
function zues_get_footers() {

	require_once( ABSPATH . 'wp-admin/includes/file.php' );

	WP_Filesystem();

	global $wp_filesystem;

	$footers_templates = array();

	$dir = THEME_DIR . '/template-parts/footers/';

	if ( is_dir( $dir ) ) {

		if ( $dh = opendir( $dir ) ) {

			while ( ($file = readdir( $dh )) !== false ) {
				if ( '.' !== $file && '..' !== $file ) {

					if ( ! preg_match( '|Footer Name:(.*)$|mi', $wp_filesystem->get_contents( $dir.$file ), $header ) ) {
						continue;
					}

					$footer_templates[ $file ] = _cleanup_header_comment( $header[1] );
				}
			}

			closedir( $dh );
		}

		return $footer_templates;
	}

}
