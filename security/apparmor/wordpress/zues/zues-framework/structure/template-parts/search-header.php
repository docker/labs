<?php
/**
 * Header for search results pages.
 *
 * @package zues
 */

if ( ! is_search() ) {
	return;
} ?>

<header <?php zues_attr( 'archive-header' ) ?>>
	<h1 <?php zues_attr( 'archive-title' ) ?>>
		<?php printf( esc_html__( 'Search Results for: %s', 'zues' ), '<span>' . get_search_query() . '</span>' ); ?>
	</h1>
</header><!-- .archive-header -->
