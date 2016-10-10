<?php
/**
 * Customize for textarea, extend the WP customizer
 *
 * @package Customizer_Library
 * @author  Devin Price, The Theme Foundry
 */

if ( ! class_exists( 'WP_Customize_Control' ) ) {
	return null;
}

/**
 * Customize for textarea, extend the WP customizer
 */
class Customizer_Library_Textarea extends WP_Customize_Control {

	/**
	 * Render the control's content.
	 *
	 * Allows the content to be overriden without having to rewrite the wrapper.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function render_content() {

		?>
     <label>
      <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
      <textarea class="large-text" cols="20" rows="5" <?php $this->link(); ?>>
				<?php echo esc_textarea( $this->value() ); ?>
      </textarea>
     </label>
        <?php
	}
}
