<?php
/**
 * The template part for displaying a message that posts cannot be found.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package zues
 */

/**
 * Fires before the 'no content' content
 */
do_action( 'zues_no_content_before' ); ?>

<section class="no-results not-found">
	<header class="entry-header">
		<h1 class="entry-title"><?php esc_html_e( 'Nothing Found', 'zues' ); ?></h1>
	</header><!-- .page-header -->


    <?php if ( is_home() && current_user_can( 'publish_posts' ) ) : ?>

		<p><?php printf( wp_kses( __( 'Ready to publish your first post? <a href="%1$s">Get started here</a>.', 'zues' ), array( 'a' => array( 'href' => array() ) ) ), esc_url( admin_url( 'post-new.php' ) ) ); ?></p>

    <?php elseif ( is_search() ) : ?>

		<p><?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'zues' ); ?></p>
		<?php get_search_form(); ?>

    <?php else : ?>

		<p><?php esc_html_e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'zues' ); ?></p>
		<?php get_search_form(); ?>

    <?php endif; ?>

</section><!-- .no-results -->

<?php

/**
 * Fires after the 'no content' content
 */
do_action( 'zues_no_content_after' ); ?>
