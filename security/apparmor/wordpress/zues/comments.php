<?php
/**
 * The template for displaying comments.
 *
 * The area of the page that contains both current comments
 * and the comment form.
 *
 * @package zues
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() ) {
	return;
}
?>

<section id="comments" class="comments-area">

    <?php if ( have_comments() ) : ?>
		<h3 class="comments-title">
    <?php
				printf(
					// WPCS: XSS OK.
					esc_html( _nx( 'One comment on &ldquo;%2$s&rdquo;', '%1$s comments on &ldquo;%2$s&rdquo;', get_comments_number(), 'comments title', 'zues' ) ),
					number_format_i18n( get_comments_number() ),
					'<span>' . get_the_title() . '</span>'
				);
	?>
		</h3>

		<ol class="comment-list">
   			<?php wp_list_comments( 'callback=zues_comment' ); ?>
		</ol><!-- .comment-list -->

		<?php zues_comments_nav(); ?>

    <?php
	// If comments are closed and there are comments, let's leave a little note, shall we?
	if ( ! comments_open() && '0' !== get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) : ?>
	    	<p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'zues' ); ?></p>
	    <?php endif; ?>

    <?php endif; ?>

    <?php comment_form(); ?>

</section><!-- #comments -->
