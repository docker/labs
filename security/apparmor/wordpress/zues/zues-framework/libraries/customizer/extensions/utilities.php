<?php
/**
 * Customizer Utility Functions
 *
 * @package 	Customizer_Library
 * @author		Devin Price, The Theme Foundry
 */

/**
 * Helper function to return defaults
 *
 * @since  1.0.0
 *
 * @param string
 * @return mixed $default
 */

function customizer_library_get_default( $setting ) {

	$customizer_library = Customizer_Library::Instance();
	$options = $customizer_library->get_options();

	if ( isset( $options[ $setting ]['default'] ) ) {
		return $options[ $setting ]['default'];
	}

}

/**
 * Helper function to return choices
 *
 * @since  1.0.0
 *
 * @param string
 * @return mixed $default
 */
function customizer_library_get_choices( $setting ) {

	$customizer_library = Customizer_Library::Instance();
	$options = $customizer_library->get_options();

	if ( isset( $options[ $setting ]['choices'] ) ) {
		return $options[ $setting ]['choices'];
	}

}

/**
 * Converts a hex color to RGB.  Returns the RGB values as an array.
 *
 * @since  1.0.0
 *
 * @access public
 * @param  string $hex
 * @return array
 */
function customizer_library_hex_to_rgb( $hex ) {

	// Remove "#" if it was added
	$color = trim( $hex, '#' );

	// Return empty array if invalid value was sent
	if ( ! ( 3 === strlen( $color ) ) && ! ( 6 === strlen( $color ) ) ) {
		return array();
	}

	// If the color is three characters, convert it to six.
	if ( 3 === strlen( $color ) ) {
		$color = $color[0] . $color[0] . $color[1] . $color[1] . $color[2] . $color[2];
	}

	// Get the red, green, and blue values
	$red   = hexdec( $color[0] . $color[1] );
	$green = hexdec( $color[2] . $color[3] );
	$blue  = hexdec( $color[4] . $color[5] );

	// Return the RGB colors as an array
	return array( 'r' => $red, 'g' => $green, 'b' => $blue );
}

/**
 * Helper function to remove custom theme mods
 *
 * @since  1.0.0
 *
 * @param string
 * @return mixed $default
 */
function customizer_library_remove_theme_mods() {

	$customizer_library = Customizer_Library::Instance();
	$options = $customizer_library->get_options();

	if ( $options ) {
		foreach ( $options as $option ) {
			if ( isset( $option['id'] ) ) {
				remove_theme_mod( $option['id'] );
			}
		}
	}
}

/**
 * Helper function to return background-repeat choices
 *
 * @since  1.0.0
 *
 * @param string
 * @return array $choices
 */
function customizer_library_get_repeat_choices() {

	$choices = array(
		'no-repeat' => 'No Repeat',
		'repeat-x' => 'Repeat Horizontally',
		'repeat-y' => 'Repeat Vertically',
		'repeat' => 'Repeat Both',
		);

	return $choices;
}

/**
 * Helper function to return background-repeat choices
 *
 * @since  1.0.0
 *
 * @param string
 * @return array $choices
 */
function customizer_library_replace_space( $font ) {

	$font = str_replace( ' ', '-', $font );
	return $font;
}
