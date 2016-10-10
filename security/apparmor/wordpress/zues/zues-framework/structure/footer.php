<?php
/**
 * Filters used to modify theme output.
 *
 * @package zues
 */

if ( ! function_exists( 'zues_load_footer_template' ) ) {

	/**
	 * Output the footer widget areas
	 */
	function zues_load_footer_template() {

		$priority = array(
			'template-parts/footer.php',
			'zues-framework/structure/template-parts/footer.php',
		);

		locate_template( $priority, true );
	}
}

if ( ! function_exists( 'zues_sub_footer' ) ) {
	/**
	 * Output the subfooter
	 */
	function zues_sub_footer() {
		echo '<div class="sub-footer">';
			echo '<div class="wrap">';
				echo '<div class="sub-footer-inner">';
					/**
					 * Sub Footer Hook
					 */
					do_action( 'zues_sub_footer' );
				echo '</div><!-- .sub-footer-inner -->';
			echo '</div><!-- .wrap -->';
		echo '</div><!-- .sub-footer -->';
	}
}

if ( ! function_exists( 'zues_footer_attribution' ) ) {
	/**
	 * Output the footer attribution text, this can be overwritten using a filter (zues_footer_attribution).
	 */
	function zues_footer_attribution() {

		$footer_attribution = __( 'Powered by the <a href="http://olympusthemes.com">Zues Theme</a>.', 'zues' );

		/*
		 * Returns footer attribution html
		 *
		 * Usage:
		 * add_filter( 'zues_footer_attribution', 'my_callback' );
		 * function my_callback(){
		 *     return '<a href="http://mywebsite.com">My Link</a>';
		 * }
		 */
		$filtered_footer_attribution = apply_filters( 'zues_footer_attribution', $footer_attribution );

		echo '<span class="footer-attribution">'.wp_kses_post( $filtered_footer_attribution ).'</span>';

	}
}

if ( ! function_exists( 'zues_footer_copyright' ) ) {
	/**
	 * Output the footer copyright text, this can be overwritten using a filter (zues_footer_copyright).
	 */
	function zues_footer_copyright() {

		$text = __( 'Copyright &copy; %1$s <a href="%2$s">%3$s</a> &middot; All Rights Reserved.', 'zues' );

		$date = date( 'Y' );
		$url = esc_url( home_url() );
		$name = get_bloginfo( 'name' );

		$footer_copyright = sprintf( $text, $date, $url, $name );

		/*
		 * Returns a footer copyright html
		 *
		 * Usage:
		 * add_filter( 'zues_footer_copyright', 'my_callback' );
		 * function my_callback(){
		 *     return 'Copyright &copy; My Website';
		 * }
		 */
		$filtered_footer_copyright = apply_filters( 'zues_footer_copyright', $footer_copyright );

		echo '<span class="footer-copyright">'.wp_kses_post( $filtered_footer_copyright ).'</span>';

	}
}

if ( ! function_exists( 'zues_wpfooter' ) ) {
	/**
	 * Output the wp_footer function, required for plugins.
	 */
	function zues_wpfooter() {

		wp_footer();

	}
}
