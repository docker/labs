<?php
/**
 * Zues Framework - A WordPress theme development framework.
 *
 * @package   zues
 * @version   1.0.0
 * @author    Danny Cooper <email@dannycooper.com
 * @copyright Copyright (c) 2008 - 2015, Danny Cooper
 * @link      https://olympusthemes.com/zues
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

 /**
  * Fires before the zues framework
  */
do_action('zues_before');

/**
 * The main class that loads all zues core framework files.
 */
class Zues_Framework {
	/**
	 * Get everything started.
	 */
	function __construct() {

		$this->constants();
		$this->functions();
		$this->structure();
		$this->admin();

		add_action( 'wp_enqueue_scripts', array( $this, 'styles' ) );

	}

	/**
	 * Define the framework constants
	 */
	function constants() {

		/* Sets the framework version number. */
		define( 'ZUES_FRAMEWORK_VERSION', '1.0.0' );

		/* Sets the path to the parent theme directory. */
		define( 'ZUES_THEME_DIR', get_template_directory() );

		/* Sets the path to the parent theme directory URI. */
		define( 'ZUES_THEME_URI', get_template_directory_uri() );

		/* Sets the path to the child theme directory. */
		define( 'ZUES_CHILD_THEME_DIR', get_stylesheet_directory() );

		/* Sets the path to the child theme directory URI. */
		define( 'ZUES_CHILD_THEME_URI', get_stylesheet_directory_uri() );

		/* Sets the path to the child theme directory. */
		define( 'ZUES_FRAMEWORK_DIR', ZUES_THEME_DIR . '/zues-framework' );

		/* Sets the path to the child theme directory URI. */
		define( 'ZUES_FRAMEWORK_URI', ZUES_THEME_URI . '/zues-framework' );

	}

	/**
	 * Load the core functions/classes required by the rest of the framework.
	 */
	function functions() {

		include_once ZUES_FRAMEWORK_DIR . '/functions/template-tags.php';
		include_once ZUES_FRAMEWORK_DIR . '/functions/widget-areas.php';
		include_once ZUES_FRAMEWORK_DIR . '/functions/generate-css.php';
		include_once ZUES_FRAMEWORK_DIR . '/functions/attr.php';

		include_once ZUES_FRAMEWORK_DIR . '/functions/templates.php';
		include_once ZUES_FRAMEWORK_DIR . '/functions/helpers.php';

		include_once ZUES_FRAMEWORK_DIR . '/libraries/customizer/customizer-library.php';

	}

	/**
	 * Load the functions relating to the theme structure.
	 */
	function structure() {

		include_once ZUES_FRAMEWORK_DIR . '/structure/wrapper.php';
		include_once ZUES_FRAMEWORK_DIR . '/structure/general.php';
		include_once ZUES_FRAMEWORK_DIR . '/structure/header.php';
		include_once ZUES_FRAMEWORK_DIR . '/structure/primary-nav.php';
		include_once ZUES_FRAMEWORK_DIR . '/structure/post.php';
		include_once ZUES_FRAMEWORK_DIR . '/structure/page.php';
		include_once ZUES_FRAMEWORK_DIR . '/structure/comments.php';
		include_once ZUES_FRAMEWORK_DIR . '/structure/sidebar.php';
		include_once ZUES_FRAMEWORK_DIR . '/structure/footer.php';

		include_once ZUES_FRAMEWORK_DIR . '/structure/hooks.php';
		include_once ZUES_FRAMEWORK_DIR . '/structure/filters.php';

		/**
		 * Automatically load all widgets in specified directory
		 *
		 * @see zues-framework/functions/helpers.php:77
		 */
		zues_autoloader( '/template-parts/widgets/' );

	}

	/**
	 * Register and enqueue core stylesheets.
	 */
	function styles() {

		wp_enqueue_style( 'olympus-reset', ZUES_FRAMEWORK_URI . '/assets/css/normalize.css' );
		wp_enqueue_style( 'olympus-base', ZUES_FRAMEWORK_URI . '/assets/css/base.css' );

		wp_enqueue_script( 'superfish', ZUES_FRAMEWORK_URI . '/assets/js/superfish.js', array( 'jquery' ), '', true );
		wp_enqueue_script( 'tinynav', ZUES_FRAMEWORK_URI . '/assets/js/tinynav.js', array( 'jquery' ), '', true );

		// Register Font Awesome incase we want to use it later
		wp_register_style( 'font-awesome', ZUES_FRAMEWORK_URI . '/assets/css/font-awesome.css' );

	}

	/**
	 * Load the functions/classes to be used within wp-admin.
	 */
	function admin() {

		if ( defined('USE_ZUES_ADMIN_NOTICES') ) {

			// Class for generating admin notices
			include_once ZUES_FRAMEWORK_DIR . '/classes/class-admin-notices.php';

		}

		if ( defined('USE_ZUES_CUSTOMIZER') ) {

			// Class for required/recommend plugin notification and installation.
			include_once ZUES_FRAMEWORK_DIR . '/libraries/customizer/customizer-library.php';

		}

		if ( defined('USE_TGMPA') ) {

			// Class for required/recommend plugin notification and installation.
			include_once ZUES_FRAMEWORK_DIR . '/libraries/TGMPA/class-tgm-plugin-activation.php';

		}

	}
}

$zues_framework = new Zues_Framework();

/*
 * Fires after the zues framework
 */
do_action('zues_end');
