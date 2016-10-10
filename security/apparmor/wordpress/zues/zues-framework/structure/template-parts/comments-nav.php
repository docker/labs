<?php
/**
 * Template used for the comment navigation.
 *
 * @package zues
 */
?>

<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // Are there comments to navigate through? ?>

	<nav id="comment-nav-above" class="navigation comment-navigation">

		<h2 class="screen-reader-text"><?php esc_html_e( 'Comment navigation', 'zues' ); ?></h2>

		<div class="nav-links nav-links-comments clear">

			<div class="nav-previous">
				<?php previous_comments_link( esc_html__( '&larr; Older Comments', 'zues' ) ); ?>
			</div><!-- .nav-previous -->

			<div class="nav-next">
				<?php next_comments_link( esc_html__( 'Newer Comments &rarr;', 'zues' ) ); ?>
			</div><!-- .nav-next -->

		</div><!-- .nav-links -->

	</nav><!-- #comment-nav-above -->

<?php endif; // Check for comment navigation. ?>
