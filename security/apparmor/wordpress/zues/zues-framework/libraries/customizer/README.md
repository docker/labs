# Customizer Library

A helpful library for working with the WordPress customizer.

## About

The customizer allows WordPress developers to add options for themes and plugins, but it should be easier to work with.  This library abstracts out some of the complexity.

Instead of adding options to the $wp_customize object directly, developers can just define an array of controls and sections and pass it to the Customizer_Library class.

To see how this works in practice, please see the [Customizer Library Demo](https://github.com/devinsays/customizer-library-demo) theme.

The Customizer Library adds sections, settings and controls to the customizer based on the array that gets passed to it.  There is default sanitization for all options (though you're still welcome to pass a sanitize_callback).  All options are also saved by default as theme_mods (though look for a future update to make this more flexible).

At the moment there is only one custom control (for textarea), but look for additional controls as the library matures.

The Customizer Library includes additional classes and helper functions for creating inline styles and loading Google fonts.  These functions and classes were developed by [The Theme Foundry](https://thethemefoundry.com/) for their theme [Make](https://thethemefoundry.com/wordpress-themes/make/) and I've found them quite useful in my own projects.  However, I'm considering moving them into separate modules in order to make the core library as focused as possible.  Feedback on this is welcome.

## Installation

The [Customizer Library](https://github.com/devinsays/customizer-library) can be included in your own projects git submodule if you'd like to be able to pull down changes.  To include it in your own projects the same way, navigate to the directory and use:

`git submodule add git@github.com:devinsays/customizer-library customizer-library`

## Options

The Customizer Library currently supports these options:

* Checkbox
* Select
* Radio
* Upload
* Image
* Color
* Text
* URL
* Range
* Textarea
* Select (Typography)

### Sections

Sections are convenient ways to group controls in the customizer.

Customizer Sections can be defined like this:

~~~php
// Example Section

$sections[] = array(
	'id' => 'example', // Required
	'title' => __( 'Example Section', 'zues' ), // Required
	'priority' => '30', // Optional
	'description' => 'Example description', // Optional
	'panel' => 'panel_id' // optional, and it requires WP >= 4.0
);
~~~

### Panels

Panels are a convenient way to group your different sections.

Here's an example that adds a panel, a section to the panel, and then a text option to that section:

~~~php
// Panel Example
$panel = 'panel';

$panels[] = array(
	'id' => $panel,
	'title' => __( 'Panel Examples', 'zues' ),
	'priority' => '100'
);

$section = 'panel-section';

$sections[] = array(
	'id' => $section,
	'title' => __( 'Panel Section', 'zues' ),
	'priority' => '10',
	'panel' => $panel
);

$options['example-panel-text'] = array(
	'id' => 'example-panel-text',
	'label'   => __( 'Example Text Input', 'zues' ),
	'section' => $section,
	'type'    => 'text',
);
~~~

The Customizer_Library uses the core function `$wp_customize->add_panel( $id, $args );` to add panels, and all the same $args are available. See [codex](https://developer.wordpress.org/reference/classes/wp_customize_manager/add_panel/).

### Text

~~~php
$options['example-text'] = array(
	'id' => 'example-text',
	'label'   => __( 'Example Text Input', 'zues' ),
	'section' => $section,
	'type'    => 'text',
);
~~~

### URL

~~~php
$options['example-url'] = array(
	'id' => 'example-url',
	'label'   => __( 'Example URL Input', 'zues' ),
	'section' => $section,
	'type'    => 'url',
);
~~~

### Checkbox

~~~php
$options['example-checkbox'] = array(
	'id' => 'example-checkbox',
	'label'   => __( 'Example Checkbox', 'zues' ),
	'section' => $section,
	'type'    => 'checkbox',
	'default' => 0,
);
~~~

### Select

~~~php
$choices = array(
	'choice-1' => 'Choice One',
	'choice-2' => 'Choice Two',
	'choice-3' => 'Choice Three'
);

$options['example-select'] = array(
	'id' => 'example-select',
	'label'   => __( 'Example Select', 'zues' ),
	'section' => $section,
	'type'    => 'select',
	'choices' => $choices,
	'default' => 'choice-1'
);
~~~

### Drop Down Pages

$options['example-dropdown-pages'] = array(
	'id' => 'example-dropdown-pages',
	'label'   => __( 'Example Drop Down Pages', 'zues' ),
	'section' => $section,
	'type'    => 'dropdown-pages',
	'default' => ''
);
~~~

### Radio

~~~php
$choices = array(
	'choice-1' => 'Choice One',
	'choice-2' => 'Choice Two',
	'choice-3' => 'Choice Three'
);

$options['example-radio'] = array(
	'id' => 'example-radio',
	'label'   => __( 'Example Radio', 'zues' ),
	'section' => $section,
	'type'    => 'radio',
	'choices' => $choices,
	'default' => 'choice-1'
);
~~~

### Upload

~~~php
$options['example-upload'] = array(
	'id' => 'example-upload',
	'label'   => __( 'Example Upload', 'zues' ),
	'section' => $section,
	'type'    => 'upload',
	'default' => '',
);
~~~

### Color

~~~php
$options['example-color'] = array(
	'id' => 'example-color',
	'label'   => __( 'Example Color', 'zues' ),
	'section' => $section,
	'type'    => 'color',
	'default' => $color // hex
);
~~~


### Textarea

~~~php
$options['example-textarea'] = array(
	'id' => 'example-textarea',
	'label'   => __( 'Example Textarea', 'zues' ),
	'section' => $section,
	'type'    => 'textarea',
	'default' => __( 'Example textarea text.', 'zues'),
);
~~~

### Select (Typography)

~~~php
$options['example-font'] = array(
	'id' => 'example-font',
	'label'   => __( 'Example Font', 'zues' ),
	'section' => $section,
	'type'    => 'select',
	'choices' => customizer_library_get_font_choices(),
	'default' => 'Monoton'
);
~~~

### Range

~~~php
$options['example-range'] = array(
	'id' => 'example-range',
	'label'   => __( 'Example Range Input', 'zues' ),
	'section' => $section,
	'type'    => 'range',
	'input_attrs' => array(
        'min'   => 0,
        'max'   => 10,
        'step'  => 1,
        'style' => 'color: #0a0',
	)
);
~~~

### Content

~~~php
$options['example-content'] = array(
	'id' => 'example-content',
	'label' => __( 'Example Content', 'zues' ),
	'section' => $section,
	'type' => 'content',
	'content' => '<p>' . __( 'Content to output. Use <a href="#">HTML</a> if you like.', 'zues' ) . '</p>',
	'description' => __( 'Optional: Example Description.', 'zues' )
);
~~~

### Pass $options to Customizer Library

After all the options and sections are defined, load them with the Customizer Library:

~~~php
// Adds the sections to the $options array
$options['sections'] = $sections;

$customizer_library = Customizer_Library::Instance();
$customizer_library->add_options( $options );
~~~

### Demo

A full working example can be found here:
https://github.com/devinsays/customizer-library-demo/blob/master/inc/customizer-options.php

## Styles

The Customizer Library has a helper class to output inline styles.  This code was originally developed by [The Theme Foundry](https://thethemefoundry.com/) for use in [Make](https://thethemefoundry.com/wordpress-themes/make/).  To see how it works, see "inc/styles.php".

CSS selector(s) and value are passed to Customizer_Library_Styles class like this:

~~~php
Customizer_Library_Styles()->add( array(
	'selectors' => array(
		'.primary'
	),
	'declarations' => array(
		'color' => $color
	)
) );
~~~

#### Media Queries

Media queries can also be be used with Customizer_Library_Styles.  Here's an example for outputting logo-image-2x on high resolution devices.

~~~php
$setting = 'logo-image-2x';
$mod = get_theme_mod( $setting, false );

if ( $mod ) {

	Customizer_Library_Styles()->add( array(
		'selectors' => array(
			'.logo'
		),
		'declarations' => array(
			'background-image' => 'url(' . $mod . ')'
		),
		'media' => '(-webkit-min-device-pixel-ratio: 1.3),(-o-min-device-pixel-ratio: 2.6/2),(min--moz-device-pixel-ratio: 1.3),(min-device-pixel-ratio: 1.3),(min-resolution: 1.3dppx)'
	) );

}
~~~



## Fonts

The Customizer Library has a helper functions to output font stacks and load inline fonts.  This code was also developed by [The Theme Foundry](https://thethemefoundry.com/) for use in [Make](https://thethemefoundry.com/wordpress-themes/make/).  You can see an example of font enqueing in "inc/mods.php":

~~~php
function demo_fonts() {

	// Font options
	$fonts = array(
		get_theme_mod( 'primary-font', customizer_library_get_default( 'primary-font' ) ),
		get_theme_mod( 'secondary-font', customizer_library_get_default( 'secondary-font' ) )
	);

	$font_uri = customizer_library_get_google_font_uri( $fonts );

	// Load Google Fonts
	wp_enqueue_style( 'demo_fonts', $font_uri, array(), null, 'screen' );

}
add_action( 'wp_enqueue_scripts', 'demo_fonts' );
~~~

Fonts can be used in inline styles like this:

~~~php
// Primary Font
$setting = 'primary-font';
$mod = get_theme_mod( $setting, customizer_library_get_default( $setting ) );
$stack = customizer_library_get_font_stack( $mod );

if ( $mod != customizer_library_get_default( $setting ) ) {

	Customizer_Library_Styles()->add( array(
		'selectors' => array(
			'.primary'
		),
		'declarations' => array(
			'font-family' => $stack
		)
	) );

}
~~~

## Change Log

Development
===

* Enhancement: Content option (for help text, HTML output, etc.)

1.3.0
===

* Enhancement: Add text input option
* Enhancement: Sort system fonts and webfonts within dropdown
* Enhancement: Add Panels Support, from WP 4.0
* Enhancement: Add support for "url" type
* Enhancement: Add support for "range" type
* Enhancement: Add support for "dropdown-pages" type
* Update: Change how setting parameters are added

1.2.0
===

* Enhancement: Allow setting parameters
* Update: Refactor interface loop

1.1.0
===

* Bugfix: customizer.js enqueue relative to library
* Enhancement: Use new textarea control from core

1.0.0
===

* Public Release
