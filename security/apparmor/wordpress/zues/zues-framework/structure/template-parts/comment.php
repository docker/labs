<?php
/**
 * Template used for the site comments.
 *
 * @package zues
 */
?>

<?php $GLOBALS['comment'] = $comment; ?>

<li <?php zues_attr( 'comment' ); ?>>

    <article>
        <header class="comment-meta">

            <div class="comment-author-avatar">
                <?php echo get_avatar( $comment->comment_author_email, 100 ); ?>
            </div><!-- .comment-author-avatar -->

            <?php printf( __( '<cite %s>%s</cite>', 'zues' ), zues_get_attr( 'comment-author' ), get_comment_author_link() ); ?>

            <?php
			// Check if author of comment is also author of post.
			zues_bypostauthor( $comment ); ?>

            <time <?php zues_attr( 'comment-published' ); ?>>
                <?php printf( esc_html__( '%s ago', 'zues' ), human_time_diff( get_comment_time( 'U' ), current_time( 'timestamp' ) ) ); ?>
            </time>

            <?php edit_comment_link( '<i class="icon-edit"></i>', '' ); ?>

        </header><!-- .comment-meta -->

        <div <?php zues_attr( 'comment-content' ); ?>>
            <?php comment_text(); ?>
        </div><!-- .comment-content -->

        <?php comment_reply_link(); ?>

        <?php if ( $comment->comment_approved === '0' ) : ?>
            <em class="comment-awaiting-moderation"><?php esc_html_e( 'Your comment is awaiting moderation.', 'zues' ); ?></em>
        <?php endif; ?>
    </article>
