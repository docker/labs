<?php
/**
 * The template for displaying the archive header
 *
 * This can be overwitten by adding a file named archive-header.php to /template-parts
 *
 * @package zues
 */

if ( ! is_archive() ) {
	return;
}

?>

<header <?php zues_attr( 'archive-header' ) ?>>
	<h1 <?php zues_attr( 'archive-title' ) ?>>
		<?php the_archive_title(); ?>
	</h1>

	<div <?php zues_attr( 'archive-description' ) ?>>
		<?php echo get_the_archive_description(); ?>
	</div><!-- .archive-description -->
</header><!-- .archive-header -->
