<?php
/**
 * Functions for outputting CSS.
 *
 * @package zues
 */

if ( ! function_exists( 'zues_generate_css' ) ) {
	/**
	 * Generate CSS code
	 *
	 * @param  string  $selector  The CSS selector.
	 * @param  string  $style     The property to be styled.
	 * @param  string  $mod_name  The style value.
	 * @param  string  $prefix    Optional prefix before value.
	 * @param  string  $postfix  Optional postfix after value.
	 * @param  boolean $echo     Option to echo or return generated CSS code.
	 * @return string            Returned CSS code.
	 */
	function zues_generate_css( $selector, $style, $mod_name, $prefix = '', $postfix = '', $echo = true ) {

		$return = '';

		$mod = get_theme_mod( $mod_name );

		if ( ! empty( $mod ) ) {
			$return = sprintf(
				'%s { %s:%s; }',
				$selector,
				$style,
				$prefix.$mod.$postfix
			);
			if ( $echo ) {
				echo $return;
			}
		}
		return $return;
	}
}
