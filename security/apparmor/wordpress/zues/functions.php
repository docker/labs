<?php
/**
 * Zues functions and definitions
 *
 * @package zues
 */

/**
 * Load zues framework.
 */
require_once( get_template_directory() . '/zues-framework/init.php' );

if ( ! function_exists( 'zues_setup' ) ) {
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function zues_setup() {
		/*
         * Make theme available for translation.
         * Translations can be filed in the /languages/ directory.
         * If you're building a theme based on Core, use a find and replace
         * to change 'zues' to the name of your theme in all the template files
        */
		load_theme_textdomain( 'zues', get_template_directory() . '/languages' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
         * Let WordPress manage the document title.
         * By adding theme support, we declare that this theme does not use a
         * hard-coded <title> tag in the document head, and expect WordPress to
         * provide it for us.
        */
		add_theme_support( 'title-tag' );
		add_theme_support( 'custom-header' );

		/*
         * Enable support for Post Thumbnails on posts and pages.
         *
         * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
        */
		add_theme_support( 'post-thumbnails' );

		add_image_size( 'zues-blog-post', 700, 9999 );

		// This theme uses wp_nav_menu() in one location.
		register_nav_menus(
			array(
			'primary' => esc_html__( 'Primary Menu', 'zues' ),
			)
		);

		/*
         * Switch default core markup for search form, comment form, and comments
         * to output valid HTML5.
        */
		add_theme_support(
			'html5', array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			)
		);

		// Set up the WordPress core custom background feature.
		add_theme_support(
			'custom-background', apply_filters(
				'zues_custom_background_args', array(
				'default-color' => 'E9E9E9',
				'default-image' => '',
				)
			)
		);

	}
}
add_action( 'after_setup_theme', 'zues_setup' );

if ( ! function_exists( 'zues_content_width' ) ) {
	/**
	 * Set the content width in pixels, based on the theme's design and stylesheet.
	 *
	 * Priority 0 to make it available to lower priority callbacks.
	 *
	 * @global int $content_width
	 */
	function zues_content_width() {
		$GLOBALS['content_width'] = apply_filters( 'zues_content_width', 700 );
	}
	add_action( 'after_setup_theme', 'zues_content_width', 0 );
}

/**
 * Register the widget areas this theme supports
 */
function zues_register_sidebars() {

	zues_register_widget_area(
		array(
		'id'          => 'sidebar-primary',
		'name'        => __( 'Primary Sidebar', 'zues' ),
		'description' => __( 'Widgets added here are shown in the sidebar next to your content.', 'zues' ),
		)
	);

	zues_register_widget_area(
		array(
		'id'          => 'footer-1',
		'name'        => __( 'Footer One', 'zues' ),
		'description' => __( 'The footer is divided into four widget areas, each spanning 25% of the layout\'s width.', 'zues' ),
		)
	);

	zues_register_widget_area(
		array(
		'id'          => 'footer-2',
		'name'        => __( 'Footer Two', 'zues' ),
		'description' => __( 'The footer is divided into four widget areas, each spanning 25% of the layout\'s width.', 'zues' ),
		)
	);

	zues_register_widget_area(
		array(
		'id'          => 'footer-3',
		'name'        => __( 'Footer Three', 'zues' ),
		'description' => __( 'The footer is divided into four widget areas, each spanning 25% of the layout\'s width.', 'zues' ),
		)
	);

	zues_register_widget_area(
		array(
		'id'          => 'footer-4',
		'name'        => __( 'Footer Four', 'zues' ),
		'description' => __( 'The footer is divided into four widget areas, each spanning 25% of the layout\'s width.', 'zues' ),
		)
	);

}

add_action( 'widgets_init', 'zues_register_sidebars', 5 );

/**
 * Enqueue scripts and styles.
 */
function zues_scripts() {
	wp_enqueue_style( 'ot-zues-style', get_stylesheet_uri() );

	wp_enqueue_script( 'zues-scripts', ZUES_THEME_URI . '/assets/js/scripts.js', array(), '', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'zues_scripts' );
