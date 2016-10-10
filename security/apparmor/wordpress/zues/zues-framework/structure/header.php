<?php
/**
 * Functions used to build .site-header
 *
 * @package zues
 */

if ( ! function_exists( 'zues_head' ) ) {
	/**
	 * Out put the website head.
	 */
	function zues_head() {

		$priority = array(
			'template-parts/head.php',
			'zues-framework/structure/template-parts/head.php',
		);

		locate_template( $priority, true );

	}
}

if ( ! function_exists( 'zues_load_header_template' ) ) {
	/**
	 * Output the site title.
	 */
	function zues_load_header_template() {

		$priority = array(
			'template-parts/header.php',
			'zues-framework/structure/template-parts/header.php',
		);

		locate_template( $priority, true );

	}
}

if ( ! function_exists( 'zues_text_header' ) ) {
	/**
	 * Output the site title.
	 */
	function zues_text_header() {

		zues_site_title();
		echo '<p '. zues_get_attr( 'site-description' ) . '>' . get_bloginfo( 'description' ) . '</p>';
	}
}

if ( ! function_exists( 'zues_image_header' ) ) {
	/**
	 * Output the header image.
	 */
	function zues_image_header() {

		echo '<a href="'. esc_url( home_url( '/' ) ) .'" rel="home">';
			echo '<img src="'.get_header_image().'" width="'.esc_attr( get_custom_header()->width ) .'" height="'.  esc_attr( get_custom_header()->height ) .'" alt="">';
			zues_site_title();
		echo '</a>';
	}
}

if ( ! function_exists( 'zues_site_title' ) ) {
	/**
	 * Check whether a h1 or h2 site title should be shown for SEO purposes.
	 */
	function zues_site_title() {

		$home_url = esc_url( home_url( '/' ) );
		$blog_name = get_bloginfo( 'name' );

		$link = sprintf( '<a href="%1$s">%2$s</a>', $home_url, $blog_name );

		if ( is_home() ) {
			echo '<h1 '. zues_get_attr( 'site-title' ) . '>'. $link . '</h1>';
		} else {
			echo '<h2 '. zues_get_attr( 'site-title' ) . '>'. $link . '</h2>';
		}

	}
}

if ( ! function_exists( 'zues_custom_header_setup' ) ) {
	/**
	 * Set up the WordPress core custom header feature.
	 *
	 * @uses zues_header_style()
	 * @uses zues_admin_header_style()
	 * @uses zues_admin_header_image()
	 */
	function zues_custom_header_setup() {
		add_theme_support( 'custom-header', apply_filters( 'zues_custom_header_args', array(
			'default-image'          => '',
			'default-text-color'     => '000000',
			'width'                  => 1000,
			'height'                 => 250,
			'flex-height'            => true,
			'wp-head-callback'       => 'zues_header_style',
			'admin-head-callback'    => 'zues_admin_header_style',
			'admin-preview-callback' => 'zues_admin_header_image',
		) ) );
	}
}

add_action( 'after_setup_theme', 'zues_custom_header_setup' );

if ( ! function_exists( 'zues_header_style' ) ) {
	/**
	 * Styles the header image and text displayed on the blog
	 *
	 * @see zues_custom_header_setup().
	 */
	function zues_header_style() {
		$header_text_color = get_header_textcolor();

		// If no custom options for text are set, let's bail
		// get_header_textcolor() options: HEADER_TEXTCOLOR is default, hide text (returns 'blank') or any hex value.
		if ( HEADER_TEXTCOLOR === $header_text_color ) {
			return;
		}

		// If we get this far, we have custom styles. Let's do this.
		?>
		<style type="text/css">
		<?php
		// Has the text been hidden?
		if ( 'blank' === $header_text_color ) :
	?>
		.site-title,
		.site-description {
			position: absolute;
			clip: rect(1px, 1px, 1px, 1px);
		}
	<?php
		// If the user has set a custom color for the text use that.
		else :
	?>
		.site-title a,
		.site-description {
			color: #<?php echo esc_attr( $header_text_color ); ?>;
		}
	<?php endif; ?>
	</style>
	<?php
	}
}

if ( ! function_exists( 'zues_admin_header_style' ) ) {
	/**
	 * Styles the header image displayed on the Appearance > Header admin panel.
	 *
	 * @see zues_custom_header_setup().
	 */
	function zues_admin_header_style() {
	?>
	<style type="text/css">
		.appearance_page_custom-header #headimg {
			border: none;
		}
		#headimg h1,
		#desc {
		}
		#headimg h1 {
		}
		#headimg h1 a {
		}
		#desc {
		}
		#headimg img {
		}
	</style>
	<?php
	}
}

if ( ! function_exists( 'zues_admin_header_image' ) ) {
	/**
	 * Custom header image markup displayed on the Appearance > Header admin panel.
	 *
	 * @see zues_custom_header_setup().
	 */
	function zues_admin_header_image() {
	?>
	<div id="headimg">
		<h1 class="displaying-header-text">
			<a id="name" style="<?php echo esc_attr( 'color: #' . get_header_textcolor() ); ?>" onclick="return false;" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a>
		</h1>
		<div class="displaying-header-text" id="desc" style="<?php echo esc_attr( 'color: #' . get_header_textcolor() ); ?>"><?php bloginfo( 'description' ); ?></div>
		<?php if ( get_header_image() ) : ?>
		<img src="<?php header_image(); ?>" alt="">
		<?php endif; ?>
	</div><!-- #heading -->
<?php
	}
}
