<?php
/**
 * Defines constants and global variables that can be overridden, generally in wp-config.php.
 *
 * @package WordPress
 * @subpackage Multisite
 * @since 3.0.0
 */

/**
 * Defines Multisite upload constants.
 *
 * Exists for backward compatibility with legacy file-serving through
 * wp-includes/ms-files.php (wp-content/blogs.php in MU).
 *
 * @since 3.0.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 */
function ms_upload_constants() {
	global $wpdb;

	// This filter is attached in ms-default-filters.php but that file is not included during SHORTINIT.
	add_filter( 'default_site_option_ms_files_rewriting', '__return_true' );

	if ( ! get_site_option( 'ms_files_rewriting' ) )
		return;

	// Base uploads dir relative to ABSPATH
	if ( !defined( 'UPLOADBLOGSDIR' ) )
		define( 'UPLOADBLOGSDIR', 'wp-content/blogs.dir' );

	// Note, the main site in a post-MU network uses wp-content/uploads.
	// This is handled in wp_upload_dir() by ignoring UPLOADS for this case.
	if ( ! defined( 'UPLOADS' ) ) {
		define( 'UPLOADS', UPLOADBLOGSDIR . "/{$wpdb->blogid}/files/" );

		// Uploads dir relative to ABSPATH
		if ( 'wp-content/blogs.dir' == UPLOADBLOGSDIR && ! defined( 'BLOGUPLOADDIR' ) )
			define( 'BLOGUPLOADDIR', WP_CONTENT_DIR . "/blogs.dir/{$wpdb->blogid}/files/" );
	}
}

/**
 * Defines Multisite cookie constants.
 *
 * @since 3.0.0
 */
function ms_cookie_constants(  ) {
	$current_site = get_current_site();

	/**
	 * @since 1.2.0
	 */
	if ( !defined( 'COOKIEPATH' ) )
		define( 'COOKIEPATH', $current_site->path );

	/**
	 * @since 1.5.0
	 */
	if ( !defined( 'SITECOOKIEPATH' ) )
		define( 'SITECOOKIEPATH', $current_site->path );

	/**
	 * @since 2.6.0
	 */
	if ( !defined( 'ADMIN_COOKIE_PATH' ) ) {
		if ( ! is_subdomain_install() || trim( parse_url( get_option( 'siteurl' ), PHP_URL_PATH ), '/' ) ) {
			define( 'ADMIN_COOKIE_PATH', SITECOOKIEPATH );
		} else {
			define( 'ADMIN_COOKIE_PATH', SITECOOKIEPATH . 'wp-admin' );
		}
	}

	/**
	 * @since 2.0.0
	 */
	if ( !defined('COOKIE_DOMAIN') && is_subdomain_install() ) {
		if ( !empty( $current_site->cookie_domain ) )
			define('COOKIE_DOMAIN', '.' . $current_site->cookie_domain);
		else
			define('COOKIE_DOMAIN', '.' . $current_site->domain);
	}
}

/**
 * Defines Multisite file constants.
 *
 * Exists for backward compatibility with legacy file-serving through
 * wp-includes/ms-files.php (wp-content/blogs.php in MU).
 *
 * @since 3.0.0
 */
function ms_file_constants() {
	/**
	 * Optional support for X-Sendfile header
	 * @since 3.0.0
	 */
	if ( !defined( 'WPMU_SENDFILE' ) )
		define( 'WPMU_SENDFILE', false );

	/**
	 * Optional support for X-Accel-Redirect header
	 * @since 3.0.0
	 */
	if ( !defined( 'WPMU_ACCEL_REDIRECT' ) )
		define( 'WPMU_ACCEL_REDIRECT', false );
}

/**
 * Defines Multisite subdomain constants and handles warnings and notices.
 *
 * VHOST is deprecated in favor of SUBDOMAIN_INSTALL, which is a bool.
 *
 * On first call, the constants are checked and defined. On second call,
 * we will have translations loaded and can trigger warnings easily.
 *
 * @since 3.0.0
 *
 * @staticvar bool $subdomain_error
 * @staticvar bool $subdomain_error_warn
 */
function ms_subdomain_constants() {
	static $subdomain_error = null;
	static $subdomain_error_warn = null;

	if ( false === $subdomain_error ) {
		return;
	}

	if ( $subdomain_error ) {
		$vhost_deprecated = __( 'The constant <code>VHOST</code> <strong>is deprecated</strong>. Use the boolean constant <code>SUBDOMAIN_INSTALL</code> in wp-config.php to enable a subdomain configuration. Use is_subdomain_install() to check whether a subdomain configuration is enabled.' );
		if ( $subdomain_error_warn ) {
			trigger_error( __( '<strong>Conflicting values for the constants VHOST and SUBDOMAIN_INSTALL.</strong> The value of SUBDOMAIN_INSTALL will be assumed to be your subdomain configuration setting.' ) . ' ' . $vhost_deprecated, E_USER_WARNING );
		} else {
	 		_deprecated_argument( 'define()', '3.0', $vhost_deprecated );
		}
		return;
	}

	if ( defined( 'SUBDOMAIN_INSTALL' ) && defined( 'VHOST' ) ) {
		$subdomain_error = true;
		if ( SUBDOMAIN_INSTALL !== ( 'yes' == VHOST ) ) {
			$subdomain_error_warn = true;
		}
	} elseif ( defined( 'SUBDOMAIN_INSTALL' ) ) {
		$subdomain_error = false;
		define( 'VHOST', SUBDOMAIN_INSTALL ? 'yes' : 'no' );
	} elseif ( defined( 'VHOST' ) ) {
		$subdomain_error = true;
		define( 'SUBDOMAIN_INSTALL', 'yes' == VHOST );
	} else {
		$subdomain_error = false;
		define( 'SUBDOMAIN_INSTALL', false );
		define( 'VHOST', 'no' );
	}
}
