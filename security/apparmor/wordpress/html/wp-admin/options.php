<?php
/**
 * Options Management Administration Screen.
 *
 * If accessed directly in a browser this page shows a list of all saved options
 * along with editable fields for their values. Serialized data is not supported
 * and there is no way to remove options via this page. It is not linked to from
 * anywhere else in the admin.
 *
 * This file is also the target of the forms in core and custom options pages
 * that use the Settings API. In this case it saves the new option values
 * and returns the user to their page of origin.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once( dirname( __FILE__ ) . '/admin.php' );

$title = __('Settings');
$this_file = 'options.php';
$parent_file = 'options-general.php';

wp_reset_vars(array('action', 'option_page'));

$capability = 'manage_options';

// This is for back compat and will eventually be removed.
if ( empty($option_page) ) {
	$option_page = 'options';
} else {

	/**
	 * Filter the capability required when using the Settings API.
	 *
	 * By default, the options groups for all registered settings require the manage_options capability.
	 * This filter is required to change the capability required for a certain options page.
	 *
	 * @since 3.2.0
	 *
	 * @param string $capability The capability used for the page, which is manage_options by default.
	 */
	$capability = apply_filters( "option_page_capability_{$option_page}", $capability );
}

if ( ! current_user_can( $capability ) ) {
	wp_die(
		'<h1>' . __( 'Cheatin&#8217; uh?' ) . '</h1>' .
		'<p>' . __( 'You are not allowed to manage these items.' ) . '</p>',
		403
	);
}

// Handle admin email change requests
if ( is_multisite() ) {
	if ( ! empty($_GET[ 'adminhash' ] ) ) {
		$new_admin_details = get_option( 'adminhash' );
		$redirect = 'options-general.php?updated=false';
		if ( is_array( $new_admin_details ) && $new_admin_details[ 'hash' ] == $_GET[ 'adminhash' ] && !empty($new_admin_details[ 'newemail' ]) ) {
			update_option( 'admin_email', $new_admin_details[ 'newemail' ] );
			delete_option( 'adminhash' );
			delete_option( 'new_admin_email' );
			$redirect = 'options-general.php?updated=true';
		}
		wp_redirect( admin_url( $redirect ) );
		exit;
	} elseif ( ! empty( $_GET['dismiss'] ) && 'new_admin_email' == $_GET['dismiss'] ) {
		delete_option( 'adminhash' );
		delete_option( 'new_admin_email' );
		wp_redirect( admin_url( 'options-general.php?updated=true' ) );
		exit;
	}
}

if ( is_multisite() && ! is_super_admin() && 'update' != $action ) {
	wp_die(
		'<h1>' . __( 'Cheatin&#8217; uh?' ) . '</h1>' .
		'<p>' . __( 'You are not allowed to delete these items.' ) . '</p>',
		403
	);
}

$whitelist_options = array(
	'general' => array( 'blogname', 'blogdescription', 'gmt_offset', 'date_format', 'time_format', 'start_of_week', 'timezone_string', 'WPLANG' ),
	'discussion' => array( 'default_pingback_flag', 'default_ping_status', 'default_comment_status', 'comments_notify', 'moderation_notify', 'comment_moderation', 'require_name_email', 'comment_whitelist', 'comment_max_links', 'moderation_keys', 'blacklist_keys', 'show_avatars', 'avatar_rating', 'avatar_default', 'close_comments_for_old_posts', 'close_comments_days_old', 'thread_comments', 'thread_comments_depth', 'page_comments', 'comments_per_page', 'default_comments_page', 'comment_order', 'comment_registration' ),
	'media' => array( 'thumbnail_size_w', 'thumbnail_size_h', 'thumbnail_crop', 'medium_size_w', 'medium_size_h', 'medium_large_size_w', 'medium_large_size_h', 'large_size_w', 'large_size_h', 'image_default_size', 'image_default_align', 'image_default_link_type' ),
	'reading' => array( 'posts_per_page', 'posts_per_rss', 'rss_use_excerpt', 'show_on_front', 'page_on_front', 'page_for_posts', 'blog_public' ),
	'writing' => array( 'default_category', 'default_email_category', 'default_link_category', 'default_post_format' )
);
$whitelist_options['misc'] = $whitelist_options['options'] = $whitelist_options['privacy'] = array();

$mail_options = array('mailserver_url', 'mailserver_port', 'mailserver_login', 'mailserver_pass');

if ( ! in_array( get_option( 'blog_charset' ), array( 'utf8', 'utf-8', 'UTF8', 'UTF-8' ) ) )
	$whitelist_options['reading'][] = 'blog_charset';

if ( get_site_option( 'initial_db_version' ) < 32453 ) {
	$whitelist_options['writing'][] = 'use_smilies';
	$whitelist_options['writing'][] = 'use_balanceTags';
}

if ( !is_multisite() ) {
	if ( !defined( 'WP_SITEURL' ) )
		$whitelist_options['general'][] = 'siteurl';
	if ( !defined( 'WP_HOME' ) )
		$whitelist_options['general'][] = 'home';

	$whitelist_options['general'][] = 'admin_email';
	$whitelist_options['general'][] = 'users_can_register';
	$whitelist_options['general'][] = 'default_role';

	$whitelist_options['writing'] = array_merge($whitelist_options['writing'], $mail_options);
	$whitelist_options['writing'][] = 'ping_sites';

	$whitelist_options['media'][] = 'uploads_use_yearmonth_folders';

	// If upload_url_path and upload_path are both default values, they're locked.
	if ( get_option( 'upload_url_path' ) || ( get_option('upload_path') != 'wp-content/uploads' && get_option('upload_path') ) ) {
		$whitelist_options['media'][] = 'upload_path';
		$whitelist_options['media'][] = 'upload_url_path';
	}
} else {
	$whitelist_options['general'][] = 'new_admin_email';

	/**
	 * Filter whether the post-by-email functionality is enabled.
	 *
	 * @since 3.0.0
	 *
	 * @param bool $enabled Whether post-by-email configuration is enabled. Default true.
	 */
	if ( apply_filters( 'enable_post_by_email_configuration', true ) )
		$whitelist_options['writing'] = array_merge($whitelist_options['writing'], $mail_options);
}

/**
 * Filter the options white list.
 *
 * @since 2.7.0
 *
 * @param array White list options.
 */
$whitelist_options = apply_filters( 'whitelist_options', $whitelist_options );

/*
 * If $_GET['action'] == 'update' we are saving settings sent from a settings page
 */
if ( 'update' == $action ) {
	if ( 'options' == $option_page && !isset( $_POST['option_page'] ) ) { // This is for back compat and will eventually be removed.
		$unregistered = true;
		check_admin_referer( 'update-options' );
	} else {
		$unregistered = false;
		check_admin_referer( $option_page . '-options' );
	}

	if ( !isset( $whitelist_options[ $option_page ] ) )
		wp_die( __( '<strong>ERROR</strong>: options page not found.' ) );

	if ( 'options' == $option_page ) {
		if ( is_multisite() && ! is_super_admin() )
			wp_die( __( 'You do not have sufficient permissions to modify unregistered settings for this site.' ) );
		$options = explode( ',', wp_unslash( $_POST[ 'page_options' ] ) );
	} else {
		$options = $whitelist_options[ $option_page ];
	}

	if ( 'general' == $option_page ) {
		// Handle custom date/time formats.
		if ( !empty($_POST['date_format']) && isset($_POST['date_format_custom']) && '\c\u\s\t\o\m' == wp_unslash( $_POST['date_format'] ) )
			$_POST['date_format'] = $_POST['date_format_custom'];
		if ( !empty($_POST['time_format']) && isset($_POST['time_format_custom']) && '\c\u\s\t\o\m' == wp_unslash( $_POST['time_format'] ) )
			$_POST['time_format'] = $_POST['time_format_custom'];
		// Map UTC+- timezones to gmt_offsets and set timezone_string to empty.
		if ( !empty($_POST['timezone_string']) && preg_match('/^UTC[+-]/', $_POST['timezone_string']) ) {
			$_POST['gmt_offset'] = $_POST['timezone_string'];
			$_POST['gmt_offset'] = preg_replace('/UTC\+?/', '', $_POST['gmt_offset']);
			$_POST['timezone_string'] = '';
		}

		// Handle translation install.
		if ( ! empty( $_POST['WPLANG'] ) && ( ! is_multisite() || is_super_admin() ) ) { // @todo: Skip if already installed
			require_once( ABSPATH . 'wp-admin/includes/translation-install.php' );

			if ( wp_can_install_language_pack() ) {
				$language = wp_download_language_pack( $_POST['WPLANG'] );
				if ( $language ) {
					$_POST['WPLANG'] = $language;
				}
			}
		}
	}

	if ( $options ) {
		foreach ( $options as $option ) {
			if ( $unregistered ) {
				_deprecated_argument( 'options.php', '2.7',
					sprintf(
						/* translators: %s: the option/setting */
						__( 'The %s setting is unregistered. Unregistered settings are deprecated. See https://codex.wordpress.org/Settings_API' ),
						'<code>' . $option . '</code>'
					)
				);
			}

			$option = trim( $option );
			$value = null;
			if ( isset( $_POST[ $option ] ) ) {
				$value = $_POST[ $option ];
				if ( ! is_array( $value ) )
					$value = trim( $value );
				$value = wp_unslash( $value );
			}
			update_option( $option, $value );
		}

		// Switch translation in case WPLANG was changed.
		$language = get_option( 'WPLANG' );
		if ( $language ) {
			load_default_textdomain( $language );
		} else {
			unload_textdomain( 'default' );
		}
	}

	/**
	 * Handle settings errors and return to options page
	 */
	// If no settings errors were registered add a general 'updated' message.
	if ( !count( get_settings_errors() ) )
		add_settings_error('general', 'settings_updated', __('Settings saved.'), 'updated');
	set_transient('settings_errors', get_settings_errors(), 30);

	/**
	 * Redirect back to the settings page that was submitted
	 */
	$goback = add_query_arg( 'settings-updated', 'true',  wp_get_referer() );
	wp_redirect( $goback );
	exit;
}

include( ABSPATH . 'wp-admin/admin-header.php' ); ?>

<div class="wrap">
  <h1><?php esc_html_e( 'All Settings' ); ?></h1>
  <form name="form" action="options.php" method="post" id="all-options">
  <?php wp_nonce_field('options-options') ?>
  <input type="hidden" name="action" value="update" />
  <input type="hidden" name="option_page" value="options" />
  <table class="form-table">
<?php
$options = $wpdb->get_results( "SELECT * FROM $wpdb->options ORDER BY option_name" );

foreach ( (array) $options as $option ) :
	$disabled = false;
	if ( $option->option_name == '' )
		continue;
	if ( is_serialized( $option->option_value ) ) {
		if ( is_serialized_string( $option->option_value ) ) {
			// This is a serialized string, so we should display it.
			$value = maybe_unserialize( $option->option_value );
			$options_to_update[] = $option->option_name;
			$class = 'all-options';
		} else {
			$value = 'SERIALIZED DATA';
			$disabled = true;
			$class = 'all-options disabled';
		}
	} else {
		$value = $option->option_value;
		$options_to_update[] = $option->option_name;
		$class = 'all-options';
	}
	$name = esc_attr( $option->option_name );
	?>
<tr>
	<th scope="row"><label for="<?php echo $name ?>"><?php echo esc_html( $option->option_name ); ?></label></th>
<td>
<?php if ( strpos( $value, "\n" ) !== false ) : ?>
	<textarea class="<?php echo $class ?>" name="<?php echo $name ?>" id="<?php echo $name ?>" cols="30" rows="5"><?php
		echo esc_textarea( $value );
	?></textarea>
	<?php else: ?>
		<input class="regular-text <?php echo $class ?>" type="text" name="<?php echo $name ?>" id="<?php echo $name ?>" value="<?php echo esc_attr( $value ) ?>"<?php disabled( $disabled, true ) ?> />
	<?php endif ?></td>
</tr>
<?php endforeach; ?>
  </table>

<input type="hidden" name="page_options" value="<?php echo esc_attr( implode( ',', $options_to_update ) ); ?>" />

<?php submit_button( __( 'Save Changes' ), 'primary', 'Update' ); ?>

  </form>
</div>

<?php
include( ABSPATH . 'wp-admin/admin-footer.php' );
