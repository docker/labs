<?php
/**
 * Outputs the primary navigation.
 *
 * @package zues
 */


	echo '<nav '. zues_get_attr( 'menu', 'primary' ) .'">';

		/**
		* Fires before the primary navigation
		*/
		do_action( 'zues_primary_nav_before' );

		echo '<div class="wrap">';
			wp_nav_menu(
			array(
				'theme_location' => 'primary',
				'container' => false,
				)
		);
		echo '</div>';


		/**
		 * Fires after the primary navigation
		 */
		 do_action( 'zues_primary_nav_after' );

	echo '</nav><!-- .menu-primary -->';
