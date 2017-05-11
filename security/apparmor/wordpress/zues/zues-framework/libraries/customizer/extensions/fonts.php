<?php
/**
 * Theme Customizer Fonts
 *
 * @package 	Customizer_Library
 * @author		The Theme Foundry
 */

if ( ! function_exists( 'customizer_library_get_font_choices' ) ) :
	/**
	 * Packages the font choices into value/label pairs for use with the customizer.
	 *
	 * @since  1.0.0.
	 *
	 * @return array    The fonts in value/label pairs.
	 */
	function customizer_library_get_all_fonts() {
		$heading1       = array( 1 => array( 'label' => sprintf( '--- %s ---', __( 'Standard Fonts', 'zues' ) ) ) );
		$standard_fonts = customizer_library_get_standard_fonts();
		$heading2       = array( 2 => array( 'label' => sprintf( '--- %s ---', __( 'Google Fonts', 'zues' ) ) ) );
		$google_fonts   = customizer_library_get_google_fonts();

		/**
	 * Allow for developers to modify the full list of fonts.
	 *
	 * @since 1.3.0.
	 *
	 * @param array    $fonts    The list of all fonts.
	 */
		return apply_filters( 'customizer_library_all_fonts', array_merge( $heading1, $standard_fonts, $heading2, $google_fonts ) );
	}
endif;

if ( ! function_exists( 'customizer_library_get_font_choices' ) ) :
	/**
	 * Packages the font choices into value/label pairs for use with the customizer.
	 *
	 * @since  1.0.0.
	 *
	 * @return array    The fonts in value/label pairs.
	 */
	function customizer_library_get_font_choices() {
		$fonts   = customizer_library_get_all_fonts();
		$choices = array();

		// Repackage the fonts into value/label pairs.
		foreach ( $fonts as $key => $font ) {
			$choices[ $key ] = $font['label'];
		}

		return $choices;
	}
endif;

if ( ! function_exists( 'customizer_library_get_google_font_uri' ) ) :
	/**
	 * Build the HTTP request URL for Google Fonts.
	 *
	 * @since  1.0.0.
	 *
	 * @param  array $fonts The fonts array.
	 * @return string    The URL for including Google Fonts.
	 */
	function customizer_library_get_google_font_uri( $fonts ) {

		// De-dupe the fonts.
		$fonts         = array_unique( $fonts );
		$allowed_fonts = customizer_library_get_google_fonts();
		$family        = array();

		// Validate each font and convert to URL format.
		foreach ( $fonts as $font ) {
			$font = trim( $font );

			// Verify that the font exists.
			if ( array_key_exists( $font, $allowed_fonts ) ) {
				// Build the family name and variant string (e.g., "Open+Sans:regular,italic,700").
				$family[] = urlencode( $font . ':' . join( ',', customizer_library_choose_google_font_variants( $font, $allowed_fonts[ $font ]['variants'] ) ) );
			}
		}

		// Convert from array to string.
		if ( empty( $family ) ) {
			return '';
		} else {
			$request = '//fonts.googleapis.com/css?family=' . implode( '|', $family );
		}

		// Load the font subset.
		$subset = get_theme_mod( 'font-subset', customizer_library_get_default( 'font-subset' ) );

		if ( 'all' === $subset ) {
			$subsets_available = customizer_library_get_google_font_subsets();

			// Remove the all se.
			unset( $subsets_available['all'] );

			// Build the array.
			$subsets = array_keys( $subsets_available );
		} else {
			$subsets = array(
			'latin',
			$subset,
			);
		}

		// Append the subset string.
		if ( ! empty( $subsets ) ) {
			$request .= urlencode( '&subset=' . join( ',', $subsets ) );
		}

		return esc_url( $request );
	}
endif;

if ( ! function_exists( 'customizer_library_get_google_font_subsets' ) ) :
	/**
	 * Retrieve the list of available Google font subsets.
	 *
	 * @since  1.0.0.
	 *
	 * @return array    The available subsets.
	 */
	function customizer_library_get_google_font_subsets() {
		return array(
		'all'          => __( 'All', 'zues' ),
		'cyrillic'     => __( 'Cyrillic', 'zues' ),
		'cyrillic-ext' => __( 'Cyrillic Extended', 'zues' ),
		'devanagari'   => __( 'Devanagari', 'zues' ),
		'greek'        => __( 'Greek', 'zues' ),
		'greek-ext'    => __( 'Greek Extended', 'zues' ),
		'khmer'        => __( 'Khmer', 'zues' ),
		'latin'        => __( 'Latin', 'zues' ),
		'latin-ext'    => __( 'Latin Extended', 'zues' ),
		'vietnamese'   => __( 'Vietnamese', 'zues' ),
		);
	}
endif;

if ( ! function_exists( 'customizer_library_choose_google_font_variants' ) ) :
	/**
	 * Given a font, chose the variants to load for the theme.
	 *
	 * Attempts to load regular, italic, and 700. If regular is not found, the first variant in the family is chosen. italic
	 * and 700 are only loaded if found. No fallbacks are loaded for those fonts.
	 *
	 * @since  1.0.0.
	 *
	 * @param  string $font        The font to load variants for.
	 * @param  array  $variants    The variants for the font.
	 * @return array                  The chosen variants.
	 */
	function customizer_library_choose_google_font_variants( $font, $variants = array() ) {
		$chosen_variants = array();
		if ( empty( $variants ) ) {
			$fonts = customizer_library_get_google_fonts();

			if ( array_key_exists( $font, $fonts ) ) {
				$variants = $fonts[ $font ]['variants'];
			}
		}

		// If a "regular" variant is not found, get the first variant.
		if ( ! in_array( 'regular', $variants ) ) {
			$chosen_variants[] = $variants[0];
		} else {
			$chosen_variants[] = 'regular';
		}

		// Only add "italic" if it exists.
		if ( in_array( 'italic', $variants ) ) {
			$chosen_variants[] = 'italic';
		}

		// Only add "700" if it exists.
		if ( in_array( '700', $variants ) ) {
			$chosen_variants[] = '700';
		}

		return apply_filters( 'customizer_library_font_variants', array_unique( $chosen_variants ), $font, $variants );
	}
endif;

if ( ! function_exists( 'customizer_library_get_standard_fonts' ) ) :
	/**
	 * Return an array of standard websafe fonts.
	 *
	 * @since  1.0.0.
	 *
	 * @return array    Standard websafe fonts.
	 */
	function customizer_library_get_standard_fonts() {
		return array(
		'serif' => array(
			'label' => _x( 'Serif', 'font style', 'zues' ),
			'stack' => 'Georgia,Times,"Times New Roman",serif',
		),
		'sans-serif' => array(
			'label' => _x( 'Sans Serif', 'font style', 'zues' ),
			'stack' => '"Helvetica Neue",Helvetica,Arial,sans-serif',
		),
		'monospace' => array(
			'label' => _x( 'Monospaced', 'font style', 'zues' ),
			'stack' => 'Monaco,"Lucida Sans Typewriter","Lucida Typewriter","Courier New",Courier,monospace',
		),
		);
	}
endif;

if ( ! function_exists( 'customizer_library_get_font_stack' ) ) :
	/**
	 * Validate the font choice and get a font stack for it.
	 *
	 * @since  1.0.0.
	 *
	 * @param  string $font    The 1st font in the stack.
	 * @return string             The full font stack.
	 */
	function customizer_library_get_font_stack( $font ) {

		$all_fonts = customizer_library_get_all_fonts();

		// Sanitize font choice.
		$font = customizer_library_sanitize_font_choice( $font );

		$sans = '"Helvetica Neue",sans-serif';
		$serif = 'Georgia, serif';

		// Use stack if one is identified.
		if ( isset( $all_fonts[ $font ]['stack'] ) && ! empty( $all_fonts[ $font ]['stack'] ) ) {
			$stack = $all_fonts[ $font ]['stack'];
		} else {
			$stack = '"' . $font . '",' . $sans;
		}

		return $stack;
	}
endif;

if ( ! function_exists( 'customizer_library_sanitize_font_choice' ) ) :
	/**
	 * Sanitize a font choice.
	 *
	 * @since  1.0.0.
	 *
	 * @param  string $value    The font choice.
	 * @return string              The sanitized font choice.
	 */
	function customizer_library_sanitize_font_choice( $value ) {
		if ( is_int( $value ) ) {
			// The array key is an integer, so the chosen option is a heading, not a real choice.
			return '';
		} else if ( array_key_exists( $value, customizer_library_get_font_choices() ) ) {
			return $value;
		} else {
			return '';
		}
	}
endif;

if ( ! function_exists( 'customizer_library_get_google_fonts' ) ) :
	/**
	 * Return an array of all available Google Fonts.
	 *
	 * @since  1.0.0.
	 *
	 * @return array    All Google Fonts.
	 */
	function customizer_library_get_google_fonts() {
		return apply_filters( 'customizer_library_get_google_fonts', array(
			'ABeeZee' => array(
			'label'    => 'ABeeZee',
			'variants' => array(
				'regular',
				'italic',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Abel' => array(
			'label'    => 'Abel',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Abril Fatface' => array(
			'label'    => 'Abril Fatface',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Aclonica' => array(
			'label'    => 'Aclonica',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Acme' => array(
			'label'    => 'Acme',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Actor' => array(
			'label'    => 'Actor',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Adamina' => array(
			'label'    => 'Adamina',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Advent Pro' => array(
			'label'    => 'Advent Pro',
			'variants' => array(
				'100',
				'200',
				'300',
				'regular',
				'500',
				'600',
				'700',
			),
			'subsets' => array(
				'latin',
				'greek',
				'latin-ext',
			),
			),
			'Aguafina Script' => array(
			'label'    => 'Aguafina Script',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Akronim' => array(
			'label'    => 'Akronim',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Aladin' => array(
			'label'    => 'Aladin',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Aldrich' => array(
			'label'    => 'Aldrich',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Alef' => array(
			'label'    => 'Alef',
			'variants' => array(
				'regular',
				'700',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Alegreya' => array(
			'label'    => 'Alegreya',
			'variants' => array(
				'regular',
				'italic',
				'700',
				'700italic',
				'900',
				'900italic',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Alegreya SC' => array(
			'label'    => 'Alegreya SC',
			'variants' => array(
				'regular',
				'italic',
				'700',
				'700italic',
				'900',
				'900italic',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Alegreya Sans' => array(
			'label'    => 'Alegreya Sans',
			'variants' => array(
				'100',
				'100italic',
				'300',
				'300italic',
				'regular',
				'italic',
				'500',
				'500italic',
				'700',
				'700italic',
				'800',
				'800italic',
				'900',
				'900italic',
			),
			'subsets' => array(
				'latin',
				'vietnamese',
				'latin-ext',
			),
			),
			'Alegreya Sans SC' => array(
			'label'    => 'Alegreya Sans SC',
			'variants' => array(
				'100',
				'100italic',
				'300',
				'300italic',
				'regular',
				'italic',
				'500',
				'500italic',
				'700',
				'700italic',
				'800',
				'800italic',
				'900',
				'900italic',
			),
			'subsets' => array(
				'latin',
				'vietnamese',
				'latin-ext',
			),
			),
			'Alex Brush' => array(
			'label'    => 'Alex Brush',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Alfa Slab One' => array(
			'label'    => 'Alfa Slab One',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Alice' => array(
			'label'    => 'Alice',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Alike' => array(
			'label'    => 'Alike',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Alike Angular' => array(
			'label'    => 'Alike Angular',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Allan' => array(
			'label'    => 'Allan',
			'variants' => array(
				'regular',
				'700',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Allerta' => array(
			'label'    => 'Allerta',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Allerta Stencil' => array(
			'label'    => 'Allerta Stencil',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Allura' => array(
			'label'    => 'Allura',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Almendra' => array(
			'label'    => 'Almendra',
			'variants' => array(
				'regular',
				'italic',
				'700',
				'700italic',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Almendra Display' => array(
			'label'    => 'Almendra Display',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Almendra SC' => array(
			'label'    => 'Almendra SC',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Amarante' => array(
			'label'    => 'Amarante',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Amaranth' => array(
			'label'    => 'Amaranth',
			'variants' => array(
				'regular',
				'italic',
				'700',
				'700italic',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Amatic SC' => array(
			'label'    => 'Amatic SC',
			'variants' => array(
				'regular',
				'700',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Amethysta' => array(
			'label'    => 'Amethysta',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Anaheim' => array(
			'label'    => 'Anaheim',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Andada' => array(
			'label'    => 'Andada',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Andika' => array(
			'label'    => 'Andika',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'cyrillic',
				'latin-ext',
				'cyrillic-ext',
			),
			),
			'Angkor' => array(
			'label'    => 'Angkor',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'khmer',
			),
			),
			'Annie Use Your Telescope' => array(
			'label'    => 'Annie Use Your Telescope',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Anonymous Pro' => array(
			'label'    => 'Anonymous Pro',
			'variants' => array(
				'regular',
				'italic',
				'700',
				'700italic',
			),
			'subsets' => array(
				'latin',
				'greek-ext',
				'cyrillic',
				'greek',
				'latin-ext',
				'cyrillic-ext',
			),
			),
			'Antic' => array(
			'label'    => 'Antic',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Antic Didone' => array(
			'label'    => 'Antic Didone',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Antic Slab' => array(
			'label'    => 'Antic Slab',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Anton' => array(
			'label'    => 'Anton',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Arapey' => array(
			'label'    => 'Arapey',
			'variants' => array(
				'regular',
				'italic',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Arbutus' => array(
			'label'    => 'Arbutus',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Arbutus Slab' => array(
			'label'    => 'Arbutus Slab',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Architects Daughter' => array(
			'label'    => 'Architects Daughter',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Archivo Black' => array(
			'label'    => 'Archivo Black',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Archivo Narrow' => array(
			'label'    => 'Archivo Narrow',
			'variants' => array(
				'regular',
				'italic',
				'700',
				'700italic',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Arimo' => array(
			'label'    => 'Arimo',
			'variants' => array(
				'regular',
				'italic',
				'700',
				'700italic',
			),
			'subsets' => array(
				'latin',
				'greek-ext',
				'cyrillic',
				'greek',
				'vietnamese',
				'latin-ext',
				'cyrillic-ext',
			),
			),
			'Arizonia' => array(
			'label'    => 'Arizonia',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Armata' => array(
			'label'    => 'Armata',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Artifika' => array(
			'label'    => 'Artifika',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Arvo' => array(
			'label'    => 'Arvo',
			'variants' => array(
				'regular',
				'italic',
				'700',
				'700italic',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Asap' => array(
			'label'    => 'Asap',
			'variants' => array(
				'regular',
				'italic',
				'700',
				'700italic',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Asset' => array(
			'label'    => 'Asset',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Astloch' => array(
			'label'    => 'Astloch',
			'variants' => array(
				'regular',
				'700',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Asul' => array(
			'label'    => 'Asul',
			'variants' => array(
				'regular',
				'700',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Atomic Age' => array(
			'label'    => 'Atomic Age',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Aubrey' => array(
			'label'    => 'Aubrey',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Audiowide' => array(
			'label'    => 'Audiowide',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Autour One' => array(
			'label'    => 'Autour One',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Average' => array(
			'label'    => 'Average',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Average Sans' => array(
			'label'    => 'Average Sans',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Averia Gruesa Libre' => array(
			'label'    => 'Averia Gruesa Libre',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Averia Libre' => array(
			'label'    => 'Averia Libre',
			'variants' => array(
				'300',
				'300italic',
				'regular',
				'italic',
				'700',
				'700italic',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Averia Sans Libre' => array(
			'label'    => 'Averia Sans Libre',
			'variants' => array(
				'300',
				'300italic',
				'regular',
				'italic',
				'700',
				'700italic',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Averia Serif Libre' => array(
			'label'    => 'Averia Serif Libre',
			'variants' => array(
				'300',
				'300italic',
				'regular',
				'italic',
				'700',
				'700italic',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Bad Script' => array(
			'label'    => 'Bad Script',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'cyrillic',
			),
			),
			'Balthazar' => array(
			'label'    => 'Balthazar',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Bangers' => array(
			'label'    => 'Bangers',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Basic' => array(
			'label'    => 'Basic',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Battambang' => array(
			'label'    => 'Battambang',
			'variants' => array(
				'regular',
				'700',
			),
			'subsets' => array(
				'khmer',
			),
			),
			'Baumans' => array(
			'label'    => 'Baumans',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Bayon' => array(
			'label'    => 'Bayon',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'khmer',
			),
			),
			'Belgrano' => array(
			'label'    => 'Belgrano',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Belleza' => array(
			'label'    => 'Belleza',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'BenchNine' => array(
			'label'    => 'BenchNine',
			'variants' => array(
				'300',
				'regular',
				'700',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Bentham' => array(
			'label'    => 'Bentham',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Berkshire Swash' => array(
			'label'    => 'Berkshire Swash',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Bevan' => array(
			'label'    => 'Bevan',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Bigelow Rules' => array(
			'label'    => 'Bigelow Rules',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Bigshot One' => array(
			'label'    => 'Bigshot One',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Bilbo' => array(
			'label'    => 'Bilbo',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Bilbo Swash Caps' => array(
			'label'    => 'Bilbo Swash Caps',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Bitter' => array(
			'label'    => 'Bitter',
			'variants' => array(
				'regular',
				'italic',
				'700',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Black Ops One' => array(
			'label'    => 'Black Ops One',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Bokor' => array(
			'label'    => 'Bokor',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'khmer',
			),
			),
			'Bonbon' => array(
			'label'    => 'Bonbon',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Boogaloo' => array(
			'label'    => 'Boogaloo',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Bowlby One' => array(
			'label'    => 'Bowlby One',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Bowlby One SC' => array(
			'label'    => 'Bowlby One SC',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Brawler' => array(
			'label'    => 'Brawler',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Bree Serif' => array(
			'label'    => 'Bree Serif',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Bubblegum Sans' => array(
			'label'    => 'Bubblegum Sans',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Bubbler One' => array(
			'label'    => 'Bubbler One',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Buda' => array(
			'label'    => 'Buda',
			'variants' => array(
				'300',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Buenard' => array(
			'label'    => 'Buenard',
			'variants' => array(
				'regular',
				'700',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Butcherman' => array(
			'label'    => 'Butcherman',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Butterfly Kids' => array(
			'label'    => 'Butterfly Kids',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Cabin' => array(
			'label'    => 'Cabin',
			'variants' => array(
				'regular',
				'italic',
				'500',
				'500italic',
				'600',
				'600italic',
				'700',
				'700italic',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Cabin Condensed' => array(
			'label'    => 'Cabin Condensed',
			'variants' => array(
				'regular',
				'500',
				'600',
				'700',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Cabin Sketch' => array(
			'label'    => 'Cabin Sketch',
			'variants' => array(
				'regular',
				'700',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Caesar Dressing' => array(
			'label'    => 'Caesar Dressing',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Cagliostro' => array(
			'label'    => 'Cagliostro',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Calligraffitti' => array(
			'label'    => 'Calligraffitti',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Cambo' => array(
			'label'    => 'Cambo',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Candal' => array(
			'label'    => 'Candal',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Cantarell' => array(
			'label'    => 'Cantarell',
			'variants' => array(
				'regular',
				'italic',
				'700',
				'700italic',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Cantata One' => array(
			'label'    => 'Cantata One',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Cantora One' => array(
			'label'    => 'Cantora One',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Capriola' => array(
			'label'    => 'Capriola',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Cardo' => array(
			'label'    => 'Cardo',
			'variants' => array(
				'regular',
				'italic',
				'700',
			),
			'subsets' => array(
				'latin',
				'greek-ext',
				'greek',
				'latin-ext',
			),
			),
			'Carme' => array(
			'label'    => 'Carme',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Carrois Gothic' => array(
			'label'    => 'Carrois Gothic',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Carrois Gothic SC' => array(
			'label'    => 'Carrois Gothic SC',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Carter One' => array(
			'label'    => 'Carter One',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Caudex' => array(
			'label'    => 'Caudex',
			'variants' => array(
				'regular',
				'italic',
				'700',
				'700italic',
			),
			'subsets' => array(
				'latin',
				'greek-ext',
				'greek',
				'latin-ext',
			),
			),
			'Cedarville Cursive' => array(
			'label'    => 'Cedarville Cursive',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Ceviche One' => array(
			'label'    => 'Ceviche One',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Changa One' => array(
			'label'    => 'Changa One',
			'variants' => array(
				'regular',
				'italic',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Chango' => array(
			'label'    => 'Chango',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Chau Philomene One' => array(
			'label'    => 'Chau Philomene One',
			'variants' => array(
				'regular',
				'italic',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Chela One' => array(
			'label'    => 'Chela One',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Chelsea Market' => array(
			'label'    => 'Chelsea Market',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Chenla' => array(
			'label'    => 'Chenla',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'khmer',
			),
			),
			'Cherry Cream Soda' => array(
			'label'    => 'Cherry Cream Soda',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Cherry Swash' => array(
			'label'    => 'Cherry Swash',
			'variants' => array(
				'regular',
				'700',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Chewy' => array(
			'label'    => 'Chewy',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Chicle' => array(
			'label'    => 'Chicle',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Chivo' => array(
			'label'    => 'Chivo',
			'variants' => array(
				'regular',
				'italic',
				'900',
				'900italic',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Cinzel' => array(
			'label'    => 'Cinzel',
			'variants' => array(
				'regular',
				'700',
				'900',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Cinzel Decorative' => array(
			'label'    => 'Cinzel Decorative',
			'variants' => array(
				'regular',
				'700',
				'900',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Clicker Script' => array(
			'label'    => 'Clicker Script',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Coda' => array(
			'label'    => 'Coda',
			'variants' => array(
				'regular',
				'800',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Coda Caption' => array(
			'label'    => 'Coda Caption',
			'variants' => array(
				'800',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Codystar' => array(
			'label'    => 'Codystar',
			'variants' => array(
				'300',
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Combo' => array(
			'label'    => 'Combo',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Comfortaa' => array(
			'label'    => 'Comfortaa',
			'variants' => array(
				'300',
				'regular',
				'700',
			),
			'subsets' => array(
				'latin',
				'cyrillic',
				'greek',
				'latin-ext',
				'cyrillic-ext',
			),
			),
			'Coming Soon' => array(
			'label'    => 'Coming Soon',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Concert One' => array(
			'label'    => 'Concert One',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Condiment' => array(
			'label'    => 'Condiment',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Content' => array(
			'label'    => 'Content',
			'variants' => array(
				'regular',
				'700',
			),
			'subsets' => array(
				'khmer',
			),
			),
			'Contrail One' => array(
			'label'    => 'Contrail One',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Convergence' => array(
			'label'    => 'Convergence',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Cookie' => array(
			'label'    => 'Cookie',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Copse' => array(
			'label'    => 'Copse',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Corben' => array(
			'label'    => 'Corben',
			'variants' => array(
				'regular',
				'700',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Courgette' => array(
			'label'    => 'Courgette',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Cousine' => array(
			'label'    => 'Cousine',
			'variants' => array(
				'regular',
				'italic',
				'700',
				'700italic',
			),
			'subsets' => array(
				'latin',
				'greek-ext',
				'cyrillic',
				'greek',
				'vietnamese',
				'latin-ext',
				'cyrillic-ext',
			),
			),
			'Coustard' => array(
			'label'    => 'Coustard',
			'variants' => array(
				'regular',
				'900',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Covered By Your Grace' => array(
			'label'    => 'Covered By Your Grace',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Crafty Girls' => array(
			'label'    => 'Crafty Girls',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Creepster' => array(
			'label'    => 'Creepster',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Crete Round' => array(
			'label'    => 'Crete Round',
			'variants' => array(
				'regular',
				'italic',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Crimson Text' => array(
			'label'    => 'Crimson Text',
			'variants' => array(
				'regular',
				'italic',
				'600',
				'600italic',
				'700',
				'700italic',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Croissant One' => array(
			'label'    => 'Croissant One',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Crushed' => array(
			'label'    => 'Crushed',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Cuprum' => array(
			'label'    => 'Cuprum',
			'variants' => array(
				'regular',
				'italic',
				'700',
				'700italic',
			),
			'subsets' => array(
				'latin',
				'cyrillic',
				'latin-ext',
			),
			),
			'Cutive' => array(
			'label'    => 'Cutive',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Cutive Mono' => array(
			'label'    => 'Cutive Mono',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Damion' => array(
			'label'    => 'Damion',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Dancing Script' => array(
			'label'    => 'Dancing Script',
			'variants' => array(
				'regular',
				'700',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Dangrek' => array(
			'label'    => 'Dangrek',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'khmer',
			),
			),
			'Dawning of a New Day' => array(
			'label'    => 'Dawning of a New Day',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Days One' => array(
			'label'    => 'Days One',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Delius' => array(
			'label'    => 'Delius',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Delius Swash Caps' => array(
			'label'    => 'Delius Swash Caps',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Delius Unicase' => array(
			'label'    => 'Delius Unicase',
			'variants' => array(
				'regular',
				'700',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Della Respira' => array(
			'label'    => 'Della Respira',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Denk One' => array(
			'label'    => 'Denk One',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Devonshire' => array(
			'label'    => 'Devonshire',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Didact Gothic' => array(
			'label'    => 'Didact Gothic',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'greek-ext',
				'cyrillic',
				'greek',
				'latin-ext',
				'cyrillic-ext',
			),
			),
			'Diplomata' => array(
			'label'    => 'Diplomata',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Diplomata SC' => array(
			'label'    => 'Diplomata SC',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Domine' => array(
			'label'    => 'Domine',
			'variants' => array(
				'regular',
				'700',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Donegal One' => array(
			'label'    => 'Donegal One',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Doppio One' => array(
			'label'    => 'Doppio One',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Dorsa' => array(
			'label'    => 'Dorsa',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Dosis' => array(
			'label'    => 'Dosis',
			'variants' => array(
				'200',
				'300',
				'regular',
				'500',
				'600',
				'700',
				'800',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Dr Sugiyama' => array(
			'label'    => 'Dr Sugiyama',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Droid Sans' => array(
			'label'    => 'Droid Sans',
			'variants' => array(
				'regular',
				'700',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Droid Sans Mono' => array(
			'label'    => 'Droid Sans Mono',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Droid Serif' => array(
			'label'    => 'Droid Serif',
			'variants' => array(
				'regular',
				'italic',
				'700',
				'700italic',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Duru Sans' => array(
			'label'    => 'Duru Sans',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Dynalight' => array(
			'label'    => 'Dynalight',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'EB Garamond' => array(
			'label'    => 'EB Garamond',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'cyrillic',
				'vietnamese',
				'latin-ext',
				'cyrillic-ext',
			),
			),
			'Eagle Lake' => array(
			'label'    => 'Eagle Lake',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Eater' => array(
			'label'    => 'Eater',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Economica' => array(
			'label'    => 'Economica',
			'variants' => array(
				'regular',
				'italic',
				'700',
				'700italic',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Electrolize' => array(
			'label'    => 'Electrolize',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Elsie' => array(
			'label'    => 'Elsie',
			'variants' => array(
				'regular',
				'900',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Elsie Swash Caps' => array(
			'label'    => 'Elsie Swash Caps',
			'variants' => array(
				'regular',
				'900',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Emblema One' => array(
			'label'    => 'Emblema One',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Emilys Candy' => array(
			'label'    => 'Emilys Candy',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Engagement' => array(
			'label'    => 'Engagement',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Englebert' => array(
			'label'    => 'Englebert',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Enriqueta' => array(
			'label'    => 'Enriqueta',
			'variants' => array(
				'regular',
				'700',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Erica One' => array(
			'label'    => 'Erica One',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Esteban' => array(
			'label'    => 'Esteban',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Euphoria Script' => array(
			'label'    => 'Euphoria Script',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Ewert' => array(
			'label'    => 'Ewert',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Exo' => array(
			'label'    => 'Exo',
			'variants' => array(
				'100',
				'100italic',
				'200',
				'200italic',
				'300',
				'300italic',
				'regular',
				'italic',
				'500',
				'500italic',
				'600',
				'600italic',
				'700',
				'700italic',
				'800',
				'800italic',
				'900',
				'900italic',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Exo 2' => array(
			'label'    => 'Exo 2',
			'variants' => array(
				'100',
				'100italic',
				'200',
				'200italic',
				'300',
				'300italic',
				'regular',
				'italic',
				'500',
				'500italic',
				'600',
				'600italic',
				'700',
				'700italic',
				'800',
				'800italic',
				'900',
				'900italic',
			),
			'subsets' => array(
				'latin',
				'cyrillic',
				'latin-ext',
			),
			),
			'Expletus Sans' => array(
			'label'    => 'Expletus Sans',
			'variants' => array(
				'regular',
				'italic',
				'500',
				'500italic',
				'600',
				'600italic',
				'700',
				'700italic',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Fanwood Text' => array(
			'label'    => 'Fanwood Text',
			'variants' => array(
				'regular',
				'italic',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Fascinate' => array(
			'label'    => 'Fascinate',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Fascinate Inline' => array(
			'label'    => 'Fascinate Inline',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Faster One' => array(
			'label'    => 'Faster One',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Fasthand' => array(
			'label'    => 'Fasthand',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'khmer',
			),
			),
			'Fauna One' => array(
			'label'    => 'Fauna One',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Federant' => array(
			'label'    => 'Federant',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Federo' => array(
			'label'    => 'Federo',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Felipa' => array(
			'label'    => 'Felipa',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Fenix' => array(
			'label'    => 'Fenix',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Finger Paint' => array(
			'label'    => 'Finger Paint',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Fira Sans' => array(
			'label'    => 'Fira Sans',
			'variants' => array(
				'300',
				'300italic',
				'400',
				'400italic',
				'500',
				'500italic',
				'700',
				'700italic',
			),
			'subsets' => array(
				'latin',
				'greek',
				'cyrillic',
				'latin-ext',
				'cyrillic-ext',
			),
			),
			'Fira Mono' => array(
			'label'    => 'Fira Mono',
			'variants' => array(
				'400',
				'700',
			),
			'subsets' => array(
				'latin',
				'greek',
				'cyrillic',
				'latin-ext',
				'cyrillic-ext',
			),
			),
			'Fjalla One' => array(
			'label'    => 'Fjalla One',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Fjord One' => array(
			'label'    => 'Fjord One',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Flamenco' => array(
			'label'    => 'Flamenco',
			'variants' => array(
				'300',
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Flavors' => array(
			'label'    => 'Flavors',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Fondamento' => array(
			'label'    => 'Fondamento',
			'variants' => array(
				'regular',
				'italic',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Fontdiner Swanky' => array(
			'label'    => 'Fontdiner Swanky',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Forum' => array(
			'label'    => 'Forum',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'cyrillic',
				'latin-ext',
				'cyrillic-ext',
			),
			),
			'Francois One' => array(
			'label'    => 'Francois One',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Freckle Face' => array(
			'label'    => 'Freckle Face',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Fredericka the Great' => array(
			'label'    => 'Fredericka the Great',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Fredoka One' => array(
			'label'    => 'Fredoka One',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Freehand' => array(
			'label'    => 'Freehand',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'khmer',
			),
			),
			'Fresca' => array(
			'label'    => 'Fresca',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Frijole' => array(
			'label'    => 'Frijole',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Fruktur' => array(
			'label'    => 'Fruktur',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Fugaz One' => array(
			'label'    => 'Fugaz One',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'GFS Didot' => array(
			'label'    => 'GFS Didot',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'greek',
			),
			),
			'GFS Neohellenic' => array(
			'label'    => 'GFS Neohellenic',
			'variants' => array(
				'regular',
				'italic',
				'700',
				'700italic',
			),
			'subsets' => array(
				'greek',
			),
			),
			'Gabriela' => array(
			'label'    => 'Gabriela',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Gafata' => array(
			'label'    => 'Gafata',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Galdeano' => array(
			'label'    => 'Galdeano',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Galindo' => array(
			'label'    => 'Galindo',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Gentium Basic' => array(
			'label'    => 'Gentium Basic',
			'variants' => array(
				'regular',
				'italic',
				'700',
				'700italic',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Gentium Book Basic' => array(
			'label'    => 'Gentium Book Basic',
			'variants' => array(
				'regular',
				'italic',
				'700',
				'700italic',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Geo' => array(
			'label'    => 'Geo',
			'variants' => array(
				'regular',
				'italic',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Geostar' => array(
			'label'    => 'Geostar',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Geostar Fill' => array(
			'label'    => 'Geostar Fill',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Germania One' => array(
			'label'    => 'Germania One',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Gilda Display' => array(
			'label'    => 'Gilda Display',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Give You Glory' => array(
			'label'    => 'Give You Glory',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Glass Antiqua' => array(
			'label'    => 'Glass Antiqua',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Glegoo' => array(
			'label'    => 'Glegoo',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Gloria Hallelujah' => array(
			'label'    => 'Gloria Hallelujah',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Goblin One' => array(
			'label'    => 'Goblin One',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Gochi Hand' => array(
			'label'    => 'Gochi Hand',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Gorditas' => array(
			'label'    => 'Gorditas',
			'variants' => array(
				'regular',
				'700',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Goudy Bookletter 1911' => array(
			'label'    => 'Goudy Bookletter 1911',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Graduate' => array(
			'label'    => 'Graduate',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Grand Hotel' => array(
			'label'    => 'Grand Hotel',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Gravitas One' => array(
			'label'    => 'Gravitas One',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Great Vibes' => array(
			'label'    => 'Great Vibes',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Griffy' => array(
			'label'    => 'Griffy',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Gruppo' => array(
			'label'    => 'Gruppo',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Gudea' => array(
			'label'    => 'Gudea',
			'variants' => array(
				'regular',
				'italic',
				'700',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Habibi' => array(
			'label'    => 'Habibi',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Hammersmith One' => array(
			'label'    => 'Hammersmith One',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Hanalei' => array(
			'label'    => 'Hanalei',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Hanalei Fill' => array(
			'label'    => 'Hanalei Fill',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Handlee' => array(
			'label'    => 'Handlee',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Hanuman' => array(
			'label'    => 'Hanuman',
			'variants' => array(
				'regular',
				'700',
			),
			'subsets' => array(
				'khmer',
			),
			),
			'Happy Monkey' => array(
			'label'    => 'Happy Monkey',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Headland One' => array(
			'label'    => 'Headland One',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Henny Penny' => array(
			'label'    => 'Henny Penny',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Herr Von Muellerhoff' => array(
			'label'    => 'Herr Von Muellerhoff',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Holtwood One SC' => array(
			'label'    => 'Holtwood One SC',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Homemade Apple' => array(
			'label'    => 'Homemade Apple',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Homenaje' => array(
			'label'    => 'Homenaje',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'IM Fell DW Pica' => array(
			'label'    => 'IM Fell DW Pica',
			'variants' => array(
				'regular',
				'italic',
			),
			'subsets' => array(
				'latin',
			),
			),
			'IM Fell DW Pica SC' => array(
			'label'    => 'IM Fell DW Pica SC',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'IM Fell Double Pica' => array(
			'label'    => 'IM Fell Double Pica',
			'variants' => array(
				'regular',
				'italic',
			),
			'subsets' => array(
				'latin',
			),
			),
			'IM Fell Double Pica SC' => array(
			'label'    => 'IM Fell Double Pica SC',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'IM Fell English' => array(
			'label'    => 'IM Fell English',
			'variants' => array(
				'regular',
				'italic',
			),
			'subsets' => array(
				'latin',
			),
			),
			'IM Fell English SC' => array(
			'label'    => 'IM Fell English SC',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'IM Fell French Canon' => array(
			'label'    => 'IM Fell French Canon',
			'variants' => array(
				'regular',
				'italic',
			),
			'subsets' => array(
				'latin',
			),
			),
			'IM Fell French Canon SC' => array(
			'label'    => 'IM Fell French Canon SC',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'IM Fell Great Primer' => array(
			'label'    => 'IM Fell Great Primer',
			'variants' => array(
				'regular',
				'italic',
			),
			'subsets' => array(
				'latin',
			),
			),
			'IM Fell Great Primer SC' => array(
			'label'    => 'IM Fell Great Primer SC',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Iceberg' => array(
			'label'    => 'Iceberg',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Iceland' => array(
			'label'    => 'Iceland',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Imprima' => array(
			'label'    => 'Imprima',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Inconsolata' => array(
			'label'    => 'Inconsolata',
			'variants' => array(
				'regular',
				'700',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Inder' => array(
			'label'    => 'Inder',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Indie Flower' => array(
			'label'    => 'Indie Flower',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Inika' => array(
			'label'    => 'Inika',
			'variants' => array(
				'regular',
				'700',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Irish Grover' => array(
			'label'    => 'Irish Grover',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Istok Web' => array(
			'label'    => 'Istok Web',
			'variants' => array(
				'regular',
				'italic',
				'700',
				'700italic',
			),
			'subsets' => array(
				'latin',
				'cyrillic',
				'latin-ext',
				'cyrillic-ext',
			),
			),
			'Italiana' => array(
			'label'    => 'Italiana',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Italianno' => array(
			'label'    => 'Italianno',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Jacques Francois' => array(
			'label'    => 'Jacques Francois',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Jacques Francois Shadow' => array(
			'label'    => 'Jacques Francois Shadow',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Jim Nightshade' => array(
			'label'    => 'Jim Nightshade',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Jockey One' => array(
			'label'    => 'Jockey One',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Jolly Lodger' => array(
			'label'    => 'Jolly Lodger',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Josefin Sans' => array(
			'label'    => 'Josefin Sans',
			'variants' => array(
				'100',
				'100italic',
				'300',
				'300italic',
				'regular',
				'italic',
				'600',
				'600italic',
				'700',
				'700italic',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Josefin Slab' => array(
			'label'    => 'Josefin Slab',
			'variants' => array(
				'100',
				'100italic',
				'300',
				'300italic',
				'regular',
				'italic',
				'600',
				'600italic',
				'700',
				'700italic',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Joti One' => array(
			'label'    => 'Joti One',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Judson' => array(
			'label'    => 'Judson',
			'variants' => array(
				'regular',
				'italic',
				'700',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Julee' => array(
			'label'    => 'Julee',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Julius Sans One' => array(
			'label'    => 'Julius Sans One',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Junge' => array(
			'label'    => 'Junge',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Jura' => array(
			'label'    => 'Jura',
			'variants' => array(
				'300',
				'regular',
				'500',
				'600',
			),
			'subsets' => array(
				'latin',
				'greek-ext',
				'cyrillic',
				'greek',
				'latin-ext',
				'cyrillic-ext',
			),
			),
			'Just Another Hand' => array(
			'label'    => 'Just Another Hand',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Just Me Again Down Here' => array(
			'label'    => 'Just Me Again Down Here',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Kameron' => array(
			'label'    => 'Kameron',
			'variants' => array(
				'regular',
				'700',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Kantumruy' => array(
			'label'    => 'Kantumruy',
			'variants' => array(
				'300',
				'regular',
				'700',
			),
			'subsets' => array(
				'khmer',
			),
			),
			'Karla' => array(
			'label'    => 'Karla',
			'variants' => array(
				'regular',
				'italic',
				'700',
				'700italic',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Kaushan Script' => array(
			'label'    => 'Kaushan Script',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Kavoon' => array(
			'label'    => 'Kavoon',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Kdam Thmor' => array(
			'label'    => 'Kdam Thmor',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'khmer',
			),
			),
			'Keania One' => array(
			'label'    => 'Keania One',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Kelly Slab' => array(
			'label'    => 'Kelly Slab',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'cyrillic',
				'latin-ext',
			),
			),
			'Kenia' => array(
			'label'    => 'Kenia',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Khmer' => array(
			'label'    => 'Khmer',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'khmer',
			),
			),
			'Kite One' => array(
			'label'    => 'Kite One',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Knewave' => array(
			'label'    => 'Knewave',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Kotta One' => array(
			'label'    => 'Kotta One',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Koulen' => array(
			'label'    => 'Koulen',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'khmer',
			),
			),
			'Kranky' => array(
			'label'    => 'Kranky',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Kreon' => array(
			'label'    => 'Kreon',
			'variants' => array(
				'300',
				'regular',
				'700',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Kristi' => array(
			'label'    => 'Kristi',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Krona One' => array(
			'label'    => 'Krona One',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'La Belle Aurore' => array(
			'label'    => 'La Belle Aurore',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Lancelot' => array(
			'label'    => 'Lancelot',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Lato' => array(
			'label'    => 'Lato',
			'variants' => array(
				'100',
				'100italic',
				'300',
				'300italic',
				'regular',
				'italic',
				'700',
				'700italic',
				'900',
				'900italic',
			),
			'subsets' => array(
				'latin',
			),
			),
			'League Script' => array(
			'label'    => 'League Script',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Leckerli One' => array(
			'label'    => 'Leckerli One',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Ledger' => array(
			'label'    => 'Ledger',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'cyrillic',
				'latin-ext',
			),
			),
			'Lekton' => array(
			'label'    => 'Lekton',
			'variants' => array(
				'regular',
				'italic',
				'700',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Lemon' => array(
			'label'    => 'Lemon',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Libre Baskerville' => array(
			'label'    => 'Libre Baskerville',
			'variants' => array(
				'regular',
				'italic',
				'700',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Life Savers' => array(
			'label'    => 'Life Savers',
			'variants' => array(
				'regular',
				'700',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Lilita One' => array(
			'label'    => 'Lilita One',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Lily Script One' => array(
			'label'    => 'Lily Script One',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Limelight' => array(
			'label'    => 'Limelight',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Linden Hill' => array(
			'label'    => 'Linden Hill',
			'variants' => array(
				'regular',
				'italic',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Lobster' => array(
			'label'    => 'Lobster',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'cyrillic',
				'latin-ext',
				'cyrillic-ext',
			),
			),
			'Lobster Two' => array(
			'label'    => 'Lobster Two',
			'variants' => array(
				'regular',
				'italic',
				'700',
				'700italic',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Londrina Outline' => array(
			'label'    => 'Londrina Outline',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Londrina Shadow' => array(
			'label'    => 'Londrina Shadow',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Londrina Sketch' => array(
			'label'    => 'Londrina Sketch',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Londrina Solid' => array(
			'label'    => 'Londrina Solid',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Lora' => array(
			'label'    => 'Lora',
			'variants' => array(
				'regular',
				'italic',
				'700',
				'700italic',
			),
			'subsets' => array(
				'latin',
				'cyrillic',
				'latin-ext',
			),
			),
			'Love Ya Like A Sister' => array(
			'label'    => 'Love Ya Like A Sister',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Loved by the King' => array(
			'label'    => 'Loved by the King',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Lovers Quarrel' => array(
			'label'    => 'Lovers Quarrel',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Luckiest Guy' => array(
			'label'    => 'Luckiest Guy',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Lusitana' => array(
			'label'    => 'Lusitana',
			'variants' => array(
				'regular',
				'700',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Lustria' => array(
			'label'    => 'Lustria',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Macondo' => array(
			'label'    => 'Macondo',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Macondo Swash Caps' => array(
			'label'    => 'Macondo Swash Caps',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Magra' => array(
			'label'    => 'Magra',
			'variants' => array(
				'regular',
				'700',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Maiden Orange' => array(
			'label'    => 'Maiden Orange',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Mako' => array(
			'label'    => 'Mako',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Marcellus' => array(
			'label'    => 'Marcellus',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Marcellus SC' => array(
			'label'    => 'Marcellus SC',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Marck Script' => array(
			'label'    => 'Marck Script',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'cyrillic',
				'latin-ext',
			),
			),
			'Margarine' => array(
			'label'    => 'Margarine',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Marko One' => array(
			'label'    => 'Marko One',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Marmelad' => array(
			'label'    => 'Marmelad',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'cyrillic',
				'latin-ext',
			),
			),
			'Marvel' => array(
			'label'    => 'Marvel',
			'variants' => array(
				'regular',
				'italic',
				'700',
				'700italic',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Mate' => array(
			'label'    => 'Mate',
			'variants' => array(
				'regular',
				'italic',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Mate SC' => array(
			'label'    => 'Mate SC',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Maven Pro' => array(
			'label'    => 'Maven Pro',
			'variants' => array(
				'regular',
				'500',
				'700',
				'900',
			),
			'subsets' => array(
				'latin',
			),
			),
			'McLaren' => array(
			'label'    => 'McLaren',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Meddon' => array(
			'label'    => 'Meddon',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'MedievalSharp' => array(
			'label'    => 'MedievalSharp',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Medula One' => array(
			'label'    => 'Medula One',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Megrim' => array(
			'label'    => 'Megrim',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Meie Script' => array(
			'label'    => 'Meie Script',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Merienda' => array(
			'label'    => 'Merienda',
			'variants' => array(
				'regular',
				'700',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Merienda One' => array(
			'label'    => 'Merienda One',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Merriweather' => array(
			'label'    => 'Merriweather',
			'variants' => array(
				'300',
				'300italic',
				'regular',
				'italic',
				'700',
				'700italic',
				'900',
				'900italic',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Merriweather Sans' => array(
			'label'    => 'Merriweather Sans',
			'variants' => array(
				'300',
				'300italic',
				'regular',
				'italic',
				'700',
				'700italic',
				'800',
				'800italic',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Metal' => array(
			'label'    => 'Metal',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'khmer',
			),
			),
			'Metal Mania' => array(
			'label'    => 'Metal Mania',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Metamorphous' => array(
			'label'    => 'Metamorphous',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Metrophobic' => array(
			'label'    => 'Metrophobic',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Michroma' => array(
			'label'    => 'Michroma',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Milonga' => array(
			'label'    => 'Milonga',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Miltonian' => array(
			'label'    => 'Miltonian',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Miltonian Tattoo' => array(
			'label'    => 'Miltonian Tattoo',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Miniver' => array(
			'label'    => 'Miniver',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Miss Fajardose' => array(
			'label'    => 'Miss Fajardose',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Modern Antiqua' => array(
			'label'    => 'Modern Antiqua',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Molengo' => array(
			'label'    => 'Molengo',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Molle' => array(
			'label'    => 'Molle',
			'variants' => array(
				'italic',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Monda' => array(
			'label'    => 'Monda',
			'variants' => array(
				'regular',
				'700',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Monofett' => array(
			'label'    => 'Monofett',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Monoton' => array(
			'label'    => 'Monoton',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Monsieur La Doulaise' => array(
			'label'    => 'Monsieur La Doulaise',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Montaga' => array(
			'label'    => 'Montaga',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Montez' => array(
			'label'    => 'Montez',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Montserrat' => array(
			'label'    => 'Montserrat',
			'variants' => array(
				'regular',
				'700',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Montserrat Alternates' => array(
			'label'    => 'Montserrat Alternates',
			'variants' => array(
				'regular',
				'700',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Montserrat Subrayada' => array(
			'label'    => 'Montserrat Subrayada',
			'variants' => array(
				'regular',
				'700',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Moul' => array(
			'label'    => 'Moul',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'khmer',
			),
			),
			'Moulpali' => array(
			'label'    => 'Moulpali',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'khmer',
			),
			),
			'Mountains of Christmas' => array(
			'label'    => 'Mountains of Christmas',
			'variants' => array(
				'regular',
				'700',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Mouse Memoirs' => array(
			'label'    => 'Mouse Memoirs',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Mr Bedfort' => array(
			'label'    => 'Mr Bedfort',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Mr Dafoe' => array(
			'label'    => 'Mr Dafoe',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Mr De Haviland' => array(
			'label'    => 'Mr De Haviland',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Mrs Saint Delafield' => array(
			'label'    => 'Mrs Saint Delafield',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Mrs Sheppards' => array(
			'label'    => 'Mrs Sheppards',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Muli' => array(
			'label'    => 'Muli',
			'variants' => array(
				'300',
				'300italic',
				'regular',
				'italic',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Mystery Quest' => array(
			'label'    => 'Mystery Quest',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Neucha' => array(
			'label'    => 'Neucha',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'cyrillic',
			),
			),
			'Neuton' => array(
			'label'    => 'Neuton',
			'variants' => array(
				'200',
				'300',
				'regular',
				'italic',
				'700',
				'800',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'New Rocker' => array(
			'label'    => 'New Rocker',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'News Cycle' => array(
			'label'    => 'News Cycle',
			'variants' => array(
				'regular',
				'700',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Niconne' => array(
			'label'    => 'Niconne',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Nixie One' => array(
			'label'    => 'Nixie One',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Nobile' => array(
			'label'    => 'Nobile',
			'variants' => array(
				'regular',
				'italic',
				'700',
				'700italic',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Nokora' => array(
			'label'    => 'Nokora',
			'variants' => array(
				'regular',
				'700',
			),
			'subsets' => array(
				'khmer',
			),
			),
			'Norican' => array(
			'label'    => 'Norican',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Nosifer' => array(
			'label'    => 'Nosifer',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Nothing You Could Do' => array(
			'label'    => 'Nothing You Could Do',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Noticia Text' => array(
			'label'    => 'Noticia Text',
			'variants' => array(
				'regular',
				'italic',
				'700',
				'700italic',
			),
			'subsets' => array(
				'latin',
				'vietnamese',
				'latin-ext',
			),
			),
			'Noto Sans' => array(
			'label'    => 'Noto Sans',
			'variants' => array(
				'regular',
				'italic',
				'700',
				'700italic',
			),
			'subsets' => array(
				'latin',
				'greek-ext',
				'cyrillic',
				'greek',
				'vietnamese',
				'latin-ext',
				'devanagari',
				'cyrillic-ext',
			),
			),
			'Noto Serif' => array(
			'label'    => 'Noto Serif',
			'variants' => array(
				'regular',
				'italic',
				'700',
				'700italic',
			),
			'subsets' => array(
				'latin',
				'greek-ext',
				'cyrillic',
				'greek',
				'vietnamese',
				'latin-ext',
				'cyrillic-ext',
			),
			),
			'Nova Cut' => array(
			'label'    => 'Nova Cut',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Nova Flat' => array(
			'label'    => 'Nova Flat',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Nova Mono' => array(
			'label'    => 'Nova Mono',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'greek',
			),
			),
			'Nova Oval' => array(
			'label'    => 'Nova Oval',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Nova Round' => array(
			'label'    => 'Nova Round',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Nova Script' => array(
			'label'    => 'Nova Script',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Nova Slim' => array(
			'label'    => 'Nova Slim',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Nova Square' => array(
			'label'    => 'Nova Square',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Numans' => array(
			'label'    => 'Numans',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Nunito' => array(
			'label'    => 'Nunito',
			'variants' => array(
				'300',
				'regular',
				'700',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Odor Mean Chey' => array(
			'label'    => 'Odor Mean Chey',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'khmer',
			),
			),
			'Offside' => array(
			'label'    => 'Offside',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Old Standard TT' => array(
			'label'    => 'Old Standard TT',
			'variants' => array(
				'regular',
				'italic',
				'700',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Oldenburg' => array(
			'label'    => 'Oldenburg',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Oleo Script' => array(
			'label'    => 'Oleo Script',
			'variants' => array(
				'regular',
				'700',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Oleo Script Swash Caps' => array(
			'label'    => 'Oleo Script Swash Caps',
			'variants' => array(
				'regular',
				'700',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Open Sans' => array(
			'label'    => 'Open Sans',
			'variants' => array(
				'300',
				'300italic',
				'regular',
				'italic',
				'600',
				'600italic',
				'700',
				'700italic',
				'800',
				'800italic',
			),
			'subsets' => array(
				'latin',
				'greek-ext',
				'cyrillic',
				'greek',
				'vietnamese',
				'latin-ext',
				'devanagari',
				'cyrillic-ext',
			),
			),
			'Open Sans Condensed' => array(
			'label'    => 'Open Sans Condensed',
			'variants' => array(
				'300',
				'300italic',
				'700',
			),
			'subsets' => array(
				'latin',
				'greek-ext',
				'cyrillic',
				'greek',
				'vietnamese',
				'latin-ext',
				'cyrillic-ext',
			),
			),
			'Oranienbaum' => array(
			'label'    => 'Oranienbaum',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'cyrillic',
				'latin-ext',
				'cyrillic-ext',
			),
			),
			'Orbitron' => array(
			'label'    => 'Orbitron',
			'variants' => array(
				'regular',
				'500',
				'700',
				'900',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Oregano' => array(
			'label'    => 'Oregano',
			'variants' => array(
				'regular',
				'italic',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Orienta' => array(
			'label'    => 'Orienta',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Original Surfer' => array(
			'label'    => 'Original Surfer',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Oswald' => array(
			'label'    => 'Oswald',
			'variants' => array(
				'300',
				'regular',
				'700',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Over the Rainbow' => array(
			'label'    => 'Over the Rainbow',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Overlock' => array(
			'label'    => 'Overlock',
			'variants' => array(
				'regular',
				'italic',
				'700',
				'700italic',
				'900',
				'900italic',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Overlock SC' => array(
			'label'    => 'Overlock SC',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Ovo' => array(
			'label'    => 'Ovo',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Oxygen' => array(
			'label'    => 'Oxygen',
			'variants' => array(
				'300',
				'regular',
				'700',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Oxygen Mono' => array(
			'label'    => 'Oxygen Mono',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'PT Mono' => array(
			'label'    => 'PT Mono',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'cyrillic',
				'latin-ext',
				'cyrillic-ext',
			),
			),
			'PT Sans' => array(
			'label'    => 'PT Sans',
			'variants' => array(
				'regular',
				'italic',
				'700',
				'700italic',
			),
			'subsets' => array(
				'latin',
				'cyrillic',
				'latin-ext',
				'cyrillic-ext',
			),
			),
			'PT Sans Caption' => array(
			'label'    => 'PT Sans Caption',
			'variants' => array(
				'regular',
				'700',
			),
			'subsets' => array(
				'latin',
				'cyrillic',
				'latin-ext',
				'cyrillic-ext',
			),
			),
			'PT Sans Narrow' => array(
			'label'    => 'PT Sans Narrow',
			'variants' => array(
				'regular',
				'700',
			),
			'subsets' => array(
				'latin',
				'cyrillic',
				'latin-ext',
				'cyrillic-ext',
			),
			),
			'PT Serif' => array(
			'label'    => 'PT Serif',
			'variants' => array(
				'regular',
				'italic',
				'700',
				'700italic',
			),
			'subsets' => array(
				'latin',
				'cyrillic',
				'latin-ext',
				'cyrillic-ext',
			),
			),
			'PT Serif Caption' => array(
			'label'    => 'PT Serif Caption',
			'variants' => array(
				'regular',
				'italic',
			),
			'subsets' => array(
				'latin',
				'cyrillic',
				'latin-ext',
				'cyrillic-ext',
			),
			),
			'Pacifico' => array(
			'label'    => 'Pacifico',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Paprika' => array(
			'label'    => 'Paprika',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Parisienne' => array(
			'label'    => 'Parisienne',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Passero One' => array(
			'label'    => 'Passero One',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Passion One' => array(
			'label'    => 'Passion One',
			'variants' => array(
				'regular',
				'700',
				'900',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Pathway Gothic One' => array(
			'label'    => 'Pathway Gothic One',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Patrick Hand' => array(
			'label'    => 'Patrick Hand',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'vietnamese',
				'latin-ext',
			),
			),
			'Patrick Hand SC' => array(
			'label'    => 'Patrick Hand SC',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'vietnamese',
				'latin-ext',
			),
			),
			'Patua One' => array(
			'label'    => 'Patua One',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Paytone One' => array(
			'label'    => 'Paytone One',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Peralta' => array(
			'label'    => 'Peralta',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Permanent Marker' => array(
			'label'    => 'Permanent Marker',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Petit Formal Script' => array(
			'label'    => 'Petit Formal Script',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Petrona' => array(
			'label'    => 'Petrona',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Philosopher' => array(
			'label'    => 'Philosopher',
			'variants' => array(
				'regular',
				'italic',
				'700',
				'700italic',
			),
			'subsets' => array(
				'latin',
				'cyrillic',
			),
			),
			'Piedra' => array(
			'label'    => 'Piedra',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Pinyon Script' => array(
			'label'    => 'Pinyon Script',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Pirata One' => array(
			'label'    => 'Pirata One',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Plaster' => array(
			'label'    => 'Plaster',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Play' => array(
			'label'    => 'Play',
			'variants' => array(
				'regular',
				'700',
			),
			'subsets' => array(
				'latin',
				'greek-ext',
				'cyrillic',
				'greek',
				'latin-ext',
				'cyrillic-ext',
			),
			),
			'Playball' => array(
			'label'    => 'Playball',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Playfair Display' => array(
			'label'    => 'Playfair Display',
			'variants' => array(
				'regular',
				'italic',
				'700',
				'700italic',
				'900',
				'900italic',
			),
			'subsets' => array(
				'latin',
				'cyrillic',
				'latin-ext',
			),
			),
			'Playfair Display SC' => array(
			'label'    => 'Playfair Display SC',
			'variants' => array(
				'regular',
				'italic',
				'700',
				'700italic',
				'900',
				'900italic',
			),
			'subsets' => array(
				'latin',
				'cyrillic',
				'latin-ext',
			),
			),
			'Podkova' => array(
			'label'    => 'Podkova',
			'variants' => array(
				'regular',
				'700',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Poiret One' => array(
			'label'    => 'Poiret One',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'cyrillic',
				'latin-ext',
			),
			),
			'Poller One' => array(
			'label'    => 'Poller One',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Poly' => array(
			'label'    => 'Poly',
			'variants' => array(
				'regular',
				'italic',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Pompiere' => array(
			'label'    => 'Pompiere',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Pontano Sans' => array(
			'label'    => 'Pontano Sans',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Port Lligat Sans' => array(
			'label'    => 'Port Lligat Sans',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Port Lligat Slab' => array(
			'label'    => 'Port Lligat Slab',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Prata' => array(
			'label'    => 'Prata',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Preahvihear' => array(
			'label'    => 'Preahvihear',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'khmer',
			),
			),
			'Press Start 2P' => array(
			'label'    => 'Press Start 2P',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'cyrillic',
				'greek',
				'latin-ext',
			),
			),
			'Princess Sofia' => array(
			'label'    => 'Princess Sofia',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Prociono' => array(
			'label'    => 'Prociono',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Prosto One' => array(
			'label'    => 'Prosto One',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'cyrillic',
				'latin-ext',
			),
			),
			'Puritan' => array(
			'label'    => 'Puritan',
			'variants' => array(
				'regular',
				'italic',
				'700',
				'700italic',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Purple Purse' => array(
			'label'    => 'Purple Purse',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Quando' => array(
			'label'    => 'Quando',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Quantico' => array(
			'label'    => 'Quantico',
			'variants' => array(
				'regular',
				'italic',
				'700',
				'700italic',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Quattrocento' => array(
			'label'    => 'Quattrocento',
			'variants' => array(
				'regular',
				'700',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Quattrocento Sans' => array(
			'label'    => 'Quattrocento Sans',
			'variants' => array(
				'regular',
				'italic',
				'700',
				'700italic',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Questrial' => array(
			'label'    => 'Questrial',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Quicksand' => array(
			'label'    => 'Quicksand',
			'variants' => array(
				'300',
				'regular',
				'700',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Quintessential' => array(
			'label'    => 'Quintessential',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Qwigley' => array(
			'label'    => 'Qwigley',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Racing Sans One' => array(
			'label'    => 'Racing Sans One',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Radley' => array(
			'label'    => 'Radley',
			'variants' => array(
				'regular',
				'italic',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Raleway' => array(
			'label'    => 'Raleway',
			'variants' => array(
				'100',
				'200',
				'300',
				'regular',
				'500',
				'600',
				'700',
				'800',
				'900',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Raleway Dots' => array(
			'label'    => 'Raleway Dots',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Rambla' => array(
			'label'    => 'Rambla',
			'variants' => array(
				'regular',
				'italic',
				'700',
				'700italic',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Rammetto One' => array(
			'label'    => 'Rammetto One',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Ranchers' => array(
			'label'    => 'Ranchers',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Rancho' => array(
			'label'    => 'Rancho',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Rationale' => array(
			'label'    => 'Rationale',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Redressed' => array(
			'label'    => 'Redressed',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Reenie Beanie' => array(
			'label'    => 'Reenie Beanie',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Revalia' => array(
			'label'    => 'Revalia',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Ribeye' => array(
			'label'    => 'Ribeye',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Ribeye Marrow' => array(
			'label'    => 'Ribeye Marrow',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Righteous' => array(
			'label'    => 'Righteous',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Risque' => array(
			'label'    => 'Risque',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Roboto' => array(
			'label'    => 'Roboto',
			'variants' => array(
				'100',
				'100italic',
				'300',
				'300italic',
				'regular',
				'italic',
				'500',
				'500italic',
				'700',
				'700italic',
				'900',
				'900italic',
			),
			'subsets' => array(
				'latin',
				'greek-ext',
				'cyrillic',
				'greek',
				'vietnamese',
				'latin-ext',
				'cyrillic-ext',
			),
			),
			'Roboto Condensed' => array(
			'label'    => 'Roboto Condensed',
			'variants' => array(
				'300',
				'300italic',
				'regular',
				'italic',
				'700',
				'700italic',
			),
			'subsets' => array(
				'latin',
				'greek-ext',
				'cyrillic',
				'greek',
				'vietnamese',
				'latin-ext',
				'cyrillic-ext',
			),
			),
			'Roboto Slab' => array(
			'label'    => 'Roboto Slab',
			'variants' => array(
				'100',
				'300',
				'regular',
				'700',
			),
			'subsets' => array(
				'latin',
				'greek-ext',
				'cyrillic',
				'greek',
				'vietnamese',
				'latin-ext',
				'cyrillic-ext',
			),
			),
			'Rochester' => array(
			'label'    => 'Rochester',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Rock Salt' => array(
			'label'    => 'Rock Salt',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Rokkitt' => array(
			'label'    => 'Rokkitt',
			'variants' => array(
				'regular',
				'700',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Romanesco' => array(
			'label'    => 'Romanesco',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Ropa Sans' => array(
			'label'    => 'Ropa Sans',
			'variants' => array(
				'regular',
				'italic',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Rosario' => array(
			'label'    => 'Rosario',
			'variants' => array(
				'regular',
				'italic',
				'700',
				'700italic',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Rosarivo' => array(
			'label'    => 'Rosarivo',
			'variants' => array(
				'regular',
				'italic',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Rouge Script' => array(
			'label'    => 'Rouge Script',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Ruda' => array(
			'label'    => 'Ruda',
			'variants' => array(
				'regular',
				'700',
				'900',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Rubik One' => array(
			'label'    => 'Rubik One',
			'variants' => array(
				'400',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Rubik Mono One' => array(
			'label'    => 'Rubik Mono One',
			'variants' => array(
				'400',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Rufina' => array(
			'label'    => 'Rufina',
			'variants' => array(
				'regular',
				'700',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Ruge Boogie' => array(
			'label'    => 'Ruge Boogie',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Ruluko' => array(
			'label'    => 'Ruluko',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Rum Raisin' => array(
			'label'    => 'Rum Raisin',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Ruslan Display' => array(
			'label'    => 'Ruslan Display',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'cyrillic',
				'latin-ext',
				'cyrillic-ext',
			),
			),
			'Russo One' => array(
			'label'    => 'Russo One',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'cyrillic',
				'latin-ext',
			),
			),
			'Ruthie' => array(
			'label'    => 'Ruthie',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Rye' => array(
			'label'    => 'Rye',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Sacramento' => array(
			'label'    => 'Sacramento',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Sail' => array(
			'label'    => 'Sail',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Salsa' => array(
			'label'    => 'Salsa',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Sanchez' => array(
			'label'    => 'Sanchez',
			'variants' => array(
				'regular',
				'italic',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Sancreek' => array(
			'label'    => 'Sancreek',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Sansita One' => array(
			'label'    => 'Sansita One',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Sarina' => array(
			'label'    => 'Sarina',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Satisfy' => array(
			'label'    => 'Satisfy',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Scada' => array(
			'label'    => 'Scada',
			'variants' => array(
				'regular',
				'italic',
				'700',
				'700italic',
			),
			'subsets' => array(
				'latin',
				'cyrillic',
				'latin-ext',
			),
			),
			'Schoolbell' => array(
			'label'    => 'Schoolbell',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Seaweed Script' => array(
			'label'    => 'Seaweed Script',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Sevillana' => array(
			'label'    => 'Sevillana',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Seymour One' => array(
			'label'    => 'Seymour One',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'cyrillic',
				'latin-ext',
			),
			),
			'Shadows Into Light' => array(
			'label'    => 'Shadows Into Light',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Shadows Into Light Two' => array(
			'label'    => 'Shadows Into Light Two',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Shanti' => array(
			'label'    => 'Shanti',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Share' => array(
			'label'    => 'Share',
			'variants' => array(
				'regular',
				'italic',
				'700',
				'700italic',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Share Tech' => array(
			'label'    => 'Share Tech',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Share Tech Mono' => array(
			'label'    => 'Share Tech Mono',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Shojumaru' => array(
			'label'    => 'Shojumaru',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Short Stack' => array(
			'label'    => 'Short Stack',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Siemreap' => array(
			'label'    => 'Siemreap',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'khmer',
			),
			),
			'Sigmar One' => array(
			'label'    => 'Sigmar One',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Signika' => array(
			'label'    => 'Signika',
			'variants' => array(
				'300',
				'regular',
				'600',
				'700',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Signika Negative' => array(
			'label'    => 'Signika Negative',
			'variants' => array(
				'300',
				'regular',
				'600',
				'700',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Simonetta' => array(
			'label'    => 'Simonetta',
			'variants' => array(
				'regular',
				'italic',
				'900',
				'900italic',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Sintony' => array(
			'label'    => 'Sintony',
			'variants' => array(
				'regular',
				'700',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Sirin Stencil' => array(
			'label'    => 'Sirin Stencil',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Six Caps' => array(
			'label'    => 'Six Caps',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Skranji' => array(
			'label'    => 'Skranji',
			'variants' => array(
				'regular',
				'700',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Slackey' => array(
			'label'    => 'Slackey',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Smokum' => array(
			'label'    => 'Smokum',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Smythe' => array(
			'label'    => 'Smythe',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Sniglet' => array(
			'label'    => 'Sniglet',
			'variants' => array(
				'regular',
				'800',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Snippet' => array(
			'label'    => 'Snippet',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Snowburst One' => array(
			'label'    => 'Snowburst One',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Sofadi One' => array(
			'label'    => 'Sofadi One',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Sofia' => array(
			'label'    => 'Sofia',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Sonsie One' => array(
			'label'    => 'Sonsie One',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Sorts Mill Goudy' => array(
			'label'    => 'Sorts Mill Goudy',
			'variants' => array(
				'regular',
				'italic',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Source Code Pro' => array(
			'label'    => 'Source Code Pro',
			'variants' => array(
				'200',
				'300',
				'regular',
				'500',
				'600',
				'700',
				'900',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Source Sans Pro' => array(
			'label'    => 'Source Sans Pro',
			'variants' => array(
				'200',
				'200italic',
				'300',
				'300italic',
				'regular',
				'italic',
				'600',
				'600italic',
				'700',
				'700italic',
				'900',
				'900italic',
			),
			'subsets' => array(
				'latin',
				'vietnamese',
				'latin-ext',
			),
			),
			'Source Serif Pro' => array(
			'label'    => 'Source Serif Pro',
			'variants' => array(
				'400',
				'600',
				'700',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Special Elite' => array(
			'label'    => 'Special Elite',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Spicy Rice' => array(
			'label'    => 'Spicy Rice',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Spinnaker' => array(
			'label'    => 'Spinnaker',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Spirax' => array(
			'label'    => 'Spirax',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Squada One' => array(
			'label'    => 'Squada One',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Stalemate' => array(
			'label'    => 'Stalemate',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Stalinist One' => array(
			'label'    => 'Stalinist One',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'cyrillic',
				'latin-ext',
			),
			),
			'Stardos Stencil' => array(
			'label'    => 'Stardos Stencil',
			'variants' => array(
				'regular',
				'700',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Stint Ultra Condensed' => array(
			'label'    => 'Stint Ultra Condensed',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Stint Ultra Expanded' => array(
			'label'    => 'Stint Ultra Expanded',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Stoke' => array(
			'label'    => 'Stoke',
			'variants' => array(
				'300',
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Strait' => array(
			'label'    => 'Strait',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Sue Ellen Francisco' => array(
			'label'    => 'Sue Ellen Francisco',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Sunshiney' => array(
			'label'    => 'Sunshiney',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Supermercado One' => array(
			'label'    => 'Supermercado One',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Suwannaphum' => array(
			'label'    => 'Suwannaphum',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'khmer',
			),
			),
			'Swanky and Moo Moo' => array(
			'label'    => 'Swanky and Moo Moo',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Syncopate' => array(
			'label'    => 'Syncopate',
			'variants' => array(
				'regular',
				'700',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Tangerine' => array(
			'label'    => 'Tangerine',
			'variants' => array(
				'regular',
				'700',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Taprom' => array(
			'label'    => 'Taprom',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'khmer',
			),
			),
			'Tauri' => array(
			'label'    => 'Tauri',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Telex' => array(
			'label'    => 'Telex',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Tenor Sans' => array(
			'label'    => 'Tenor Sans',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'cyrillic',
				'latin-ext',
				'cyrillic-ext',
			),
			),
			'Text Me One' => array(
			'label'    => 'Text Me One',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'The Girl Next Door' => array(
			'label'    => 'The Girl Next Door',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Tienne' => array(
			'label'    => 'Tienne',
			'variants' => array(
				'regular',
				'700',
				'900',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Tinos' => array(
			'label'    => 'Tinos',
			'variants' => array(
				'regular',
				'italic',
				'700',
				'700italic',
			),
			'subsets' => array(
				'latin',
				'greek-ext',
				'cyrillic',
				'greek',
				'vietnamese',
				'latin-ext',
				'cyrillic-ext',
			),
			),
			'Titan One' => array(
			'label'    => 'Titan One',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Titillium Web' => array(
			'label'    => 'Titillium Web',
			'variants' => array(
				'200',
				'200italic',
				'300',
				'300italic',
				'regular',
				'italic',
				'600',
				'600italic',
				'700',
				'700italic',
				'900',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Trade Winds' => array(
			'label'    => 'Trade Winds',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Trocchi' => array(
			'label'    => 'Trocchi',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Trochut' => array(
			'label'    => 'Trochut',
			'variants' => array(
				'regular',
				'italic',
				'700',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Trykker' => array(
			'label'    => 'Trykker',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Tulpen One' => array(
			'label'    => 'Tulpen One',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Ubuntu' => array(
			'label'    => 'Ubuntu',
			'variants' => array(
				'300',
				'300italic',
				'regular',
				'italic',
				'500',
				'500italic',
				'700',
				'700italic',
			),
			'subsets' => array(
				'latin',
				'greek-ext',
				'cyrillic',
				'greek',
				'latin-ext',
				'cyrillic-ext',
			),
			),
			'Ubuntu Condensed' => array(
			'label'    => 'Ubuntu Condensed',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'greek-ext',
				'cyrillic',
				'greek',
				'latin-ext',
				'cyrillic-ext',
			),
			),
			'Ubuntu Mono' => array(
			'label'    => 'Ubuntu Mono',
			'variants' => array(
				'regular',
				'italic',
				'700',
				'700italic',
			),
			'subsets' => array(
				'latin',
				'greek-ext',
				'cyrillic',
				'greek',
				'latin-ext',
				'cyrillic-ext',
			),
			),
			'Ultra' => array(
			'label'    => 'Ultra',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Uncial Antiqua' => array(
			'label'    => 'Uncial Antiqua',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Underdog' => array(
			'label'    => 'Underdog',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'cyrillic',
				'latin-ext',
			),
			),
			'Unica One' => array(
			'label'    => 'Unica One',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'UnifrakturCook' => array(
			'label'    => 'UnifrakturCook',
			'variants' => array(
				'700',
			),
			'subsets' => array(
				'latin',
			),
			),
			'UnifrakturMaguntia' => array(
			'label'    => 'UnifrakturMaguntia',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Unkempt' => array(
			'label'    => 'Unkempt',
			'variants' => array(
				'regular',
				'700',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Unlock' => array(
			'label'    => 'Unlock',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Unna' => array(
			'label'    => 'Unna',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'VT323' => array(
			'label'    => 'VT323',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Vampiro One' => array(
			'label'    => 'Vampiro One',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Varela' => array(
			'label'    => 'Varela',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Varela Round' => array(
			'label'    => 'Varela Round',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Vast Shadow' => array(
			'label'    => 'Vast Shadow',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Vibur' => array(
			'label'    => 'Vibur',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Vidaloka' => array(
			'label'    => 'Vidaloka',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Viga' => array(
			'label'    => 'Viga',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Voces' => array(
			'label'    => 'Voces',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Volkhov' => array(
			'label'    => 'Volkhov',
			'variants' => array(
				'regular',
				'italic',
				'700',
				'700italic',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Vollkorn' => array(
			'label'    => 'Vollkorn',
			'variants' => array(
				'regular',
				'italic',
				'700',
				'700italic',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Voltaire' => array(
			'label'    => 'Voltaire',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Waiting for the Sunrise' => array(
			'label'    => 'Waiting for the Sunrise',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Wallpoet' => array(
			'label'    => 'Wallpoet',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Walter Turncoat' => array(
			'label'    => 'Walter Turncoat',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Warnes' => array(
			'label'    => 'Warnes',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Wellfleet' => array(
			'label'    => 'Wellfleet',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Wendy One' => array(
			'label'    => 'Wendy One',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Wire One' => array(
			'label'    => 'Wire One',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Yanone Kaffeesatz' => array(
			'label'    => 'Yanone Kaffeesatz',
			'variants' => array(
				'200',
				'300',
				'regular',
				'700',
			),
			'subsets' => array(
				'latin',
				'latin-ext',
			),
			),
			'Yellowtail' => array(
			'label'    => 'Yellowtail',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Yeseva One' => array(
			'label'    => 'Yeseva One',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
				'cyrillic',
				'latin-ext',
			),
			),
			'Yesteryear' => array(
			'label'    => 'Yesteryear',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
			'Zeyada' => array(
			'label'    => 'Zeyada',
			'variants' => array(
				'regular',
			),
			'subsets' => array(
				'latin',
			),
			),
		) );
	}
endif;
