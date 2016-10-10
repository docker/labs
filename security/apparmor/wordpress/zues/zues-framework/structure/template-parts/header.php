<?php
/**
 * Template used for the site heaader.
 *
 * @package zues
 */

echo '<div ' . zues_get_attr( 'branding' ) . '>';

	if ( get_header_image() ) {
		zues_image_header();
	} else {
		zues_text_header();
	}

echo '</div><!-- .site-branding -->';
