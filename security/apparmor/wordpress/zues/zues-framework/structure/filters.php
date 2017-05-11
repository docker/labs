<?php
/**
 * Filters used to modify theme output.
 *
 * @package zues
 */

if ( ! function_exists( 'zues_excerpt_more' ) ) {
	/**
	 * Filter default more text.
	 *
	 * @param $string $more Default 'read more link' html.
	 */
	function zues_excerpt_more( $more ) {
		global $post;
		return '<p><a class="moretag" href="'. get_permalink( $post->ID ) . '">'.__( 'Continue Reading', 'zues' ).'&hellip;</a></p>';
	}
}
add_filter( 'excerpt_more', 'zues_excerpt_more' );


/**
 * Remove sidebar from full width page template.
 */
function remove_sidebar_from_full_width_template() {

	// Remove sidebar from just this page-template.
	if ( is_page_template( 'page-templates/full-width.php' ) ) {

		remove_action( 'zues_content_sidebar_wrapper', 'zues_sidebar_primary', 20 );

	}

}
add_action( 'zues_content_sidebar_wrapper','remove_sidebar_from_full_width_template' );
