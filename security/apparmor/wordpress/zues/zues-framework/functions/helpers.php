<?php
/**
 * Zues helper functions
 *
 * @package zues
 */

/**
 * Has the theme just been activated
 *
 * @return bool
 */
function zues_is_theme_activated() {
	global $pagenow;

	if ( is_admin() && isset( $_GET['activated'] ) && $pagenow === 'themes.php' ) {
		return true;
	}

	return false;
}

/**
 * Automatically loads all .php files in a directory
 * @param string $dir
 * @todo check against child theme
 */
function zues_autoloader( $dir ) {

	$full_dir = ZUES_THEME_DIR . $dir;
	foreach ( glob( $full_dir.'*.php' ) as $filename ) {
		include $filename; }

}

/**
 * Check if sidebar is active, if it is then display it.
 * @param string $id
 */
function zues_widget_area( $id ) {

	if ( is_active_sidebar( $id ) ) {
		echo '<div class="widget-area '. $id .'">';
			     dynamic_sidebar( $id );
		echo '</div>';
	}

}
