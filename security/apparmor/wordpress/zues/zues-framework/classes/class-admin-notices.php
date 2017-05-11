<?php
/**
 * Class for displaying notices in wp-admin
 *
 * @package zues
 */

/**
 * Admin notice class
 */
class Zues_Admin_Notice
{
	/**
	 * Notice ID
	 *
	 * @var int
	 */
	public $notice_id = '';

	/**
	 * Notice CSS class
	 *
	 * @var string
	 */
	public $class = '';

	/**
	 * The message to be shown
	 *
	 * @var string
	 */
	public $message = '';

	/**
	 * Construct the class
	 *
	 * @param int    $notice_id Notice ID.
	 * @param string $message   Message to be shown.
	 * @param string $class     Notice class.
	 */
	function __construct( $notice_id, $message, $class = 'updated' ) {
		$this->notice_id = $notice_id;
		$this->class = $class;
		$this->message = $message;
		add_action( 'admin_notices', array( $this, 'output' ) );
		add_action( 'admin_init', array( $this, 'ignore' ) );
		add_action( 'admin_head', array( $this, 'css' ) );
	}

	/**
	 * If user clicks to ignore the notice, add that to their user meta.
	 */
	function ignore() {

		global $current_user;
		$user_id = $current_user->ID;

		if ( isset( $_GET[ $this->notice_id ] ) && 'hide' === $_GET[ $this->notice_id ] ) {
			add_user_meta( $user_id, $this->notice_id, 'true', true );
		}

	}

	/**
	 * Output the admin notice.
	 */
	function output() {

		global $current_user;
		$user_id = $current_user->ID;
		if ( ! get_user_meta( $user_id, $this->notice_id ) ) {
			echo '<div id="olympus-message" class="' . esc_attr( $this->class ) .'"><p>' . esc_html( $this->message );
			echo '<span class="olympus-dismiss"><a href="?' . esc_attr( $this->notice_id ) . '=hide">Hide Notice</a></p></span></div>';
		}

	}
}
