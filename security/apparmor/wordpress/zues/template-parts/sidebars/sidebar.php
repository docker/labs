<?php
/**
 * The sidebar containing the main widget area.
 *
 * @package zues
 */

if ( ! is_active_sidebar( 'sidebar-primary' ) ) {
	return;
}

zues_sidebar_primary();
