<?php
/**
 * Edit user administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once( dirname( __FILE__ ) . '/admin.php' );

wp_reset_vars( array( 'action', 'user_id', 'wp_http_referer' ) );

$user_id = (int) $user_id;
$current_user = wp_get_current_user();
if ( ! defined( 'IS_PROFILE_PAGE' ) )
	define( 'IS_PROFILE_PAGE', ( $user_id == $current_user->ID ) );

if ( ! $user_id && IS_PROFILE_PAGE )
	$user_id = $current_user->ID;
elseif ( ! $user_id && ! IS_PROFILE_PAGE )
	wp_die(__( 'Invalid user ID.' ) );
elseif ( ! get_userdata( $user_id ) )
	wp_die( __('Invalid user ID.') );

wp_enqueue_script('user-profile');

$title = IS_PROFILE_PAGE ? __('Profile') : __('Edit User');
if ( current_user_can('edit_users') && !IS_PROFILE_PAGE )
	$submenu_file = 'users.php';
else
	$submenu_file = 'profile.php';

if ( current_user_can('edit_users') && !is_user_admin() )
	$parent_file = 'users.php';
else
	$parent_file = 'profile.php';

$profile_help = '<p>' . __('Your profile contains information about you (your &#8220;account&#8221;) as well as some personal options related to using WordPress.') . '</p>' .
	'<p>' . __('You can change your password, turn on keyboard shortcuts, change the color scheme of your WordPress administration screens, and turn off the WYSIWYG (Visual) editor, among other things. You can hide the Toolbar (formerly called the Admin Bar) from the front end of your site, however it cannot be disabled on the admin screens.') . '</p>' .
	'<p>' . __('Your username cannot be changed, but you can use other fields to enter your real name or a nickname, and change which name to display on your posts.') . '</p>' .
	'<p>' . __( 'You can log out of other devices, such as your phone or a public computer, by clicking the Log Out Everywhere Else button.' ) . '</p>' .
	'<p>' . __('Required fields are indicated; the rest are optional. Profile information will only be displayed if your theme is set up to do so.') . '</p>' .
	'<p>' . __('Remember to click the Update Profile button when you are finished.') . '</p>';

get_current_screen()->add_help_tab( array(
	'id'      => 'overview',
	'title'   => __('Overview'),
	'content' => $profile_help,
) );

get_current_screen()->set_help_sidebar(
    '<p><strong>' . __('For more information:') . '</strong></p>' .
    '<p>' . __('<a href="https://codex.wordpress.org/Users_Your_Profile_Screen" target="_blank">Documentation on User Profiles</a>') . '</p>' .
    '<p>' . __('<a href="https://wordpress.org/support/" target="_blank">Support Forums</a>') . '</p>'
);

$wp_http_referer = remove_query_arg(array('update', 'delete_count'), $wp_http_referer );

$user_can_edit = current_user_can( 'edit_posts' ) || current_user_can( 'edit_pages' );

/**
 * Filter whether to allow administrators on Multisite to edit every user.
 *
 * Enabling the user editing form via this filter also hinges on the user holding
 * the 'manage_network_users' cap, and the logged-in user not matching the user
 * profile open for editing.
 *
 * The filter was introduced to replace the EDIT_ANY_USER constant.
 *
 * @since 3.0.0
 *
 * @param bool $allow Whether to allow editing of any user. Default true.
 */
if ( is_multisite()
	&& ! current_user_can( 'manage_network_users' )
	&& $user_id != $current_user->ID
	&& ! apply_filters( 'enable_edit_any_user_configuration', true )
) {
	wp_die( __( 'You do not have permission to edit this user.' ) );
}

// Execute confirmed email change. See send_confirmation_on_profile_email().
if ( is_multisite() && IS_PROFILE_PAGE && isset( $_GET[ 'newuseremail' ] ) && $current_user->ID ) {
	$new_email = get_option( $current_user->ID . '_new_email' );
	if ( $new_email[ 'hash' ] == $_GET[ 'newuseremail' ] ) {
		$user = new stdClass;
		$user->ID = $current_user->ID;
		$user->user_email = esc_html( trim( $new_email[ 'newemail' ] ) );
		if ( $wpdb->get_var( $wpdb->prepare( "SELECT user_login FROM {$wpdb->signups} WHERE user_login = %s", $current_user->user_login ) ) )
			$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->signups} SET user_email = %s WHERE user_login = %s", $user->user_email, $current_user->user_login ) );
		wp_update_user( $user );
		delete_option( $current_user->ID . '_new_email' );
		wp_redirect( add_query_arg( array('updated' => 'true'), self_admin_url( 'profile.php' ) ) );
		die();
	}
} elseif ( is_multisite() && IS_PROFILE_PAGE && !empty( $_GET['dismiss'] ) && $current_user->ID . '_new_email' == $_GET['dismiss'] ) {
	delete_option( $current_user->ID . '_new_email' );
	wp_redirect( add_query_arg( array('updated' => 'true'), self_admin_url( 'profile.php' ) ) );
	die();
}

switch ($action) {
case 'update':

check_admin_referer('update-user_' . $user_id);

if ( !current_user_can('edit_user', $user_id) )
	wp_die(__('You do not have permission to edit this user.'));

if ( IS_PROFILE_PAGE ) {
	/**
	 * Fires before the page loads on the 'Your Profile' editing screen.
	 *
	 * The action only fires if the current user is editing their own profile.
	 *
	 * @since 2.0.0
	 *
	 * @param int $user_id The user ID.
	 */
	do_action( 'personal_options_update', $user_id );
} else {
	/**
	 * Fires before the page loads on the 'Edit User' screen.
	 *
	 * @since 2.7.0
	 *
	 * @param int $user_id The user ID.
	 */
	do_action( 'edit_user_profile_update', $user_id );
}

// Update the email address in signups, if present.
if ( is_multisite() ) {
	$user = get_userdata( $user_id );

	if ( $user->user_login && isset( $_POST[ 'email' ] ) && is_email( $_POST[ 'email' ] ) && $wpdb->get_var( $wpdb->prepare( "SELECT user_login FROM {$wpdb->signups} WHERE user_login = %s", $user->user_login ) ) ) {
		$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->signups} SET user_email = %s WHERE user_login = %s", $_POST[ 'email' ], $user_login ) );
	}
}

// Update the user.
$errors = edit_user( $user_id );

// Grant or revoke super admin status if requested.
if ( is_multisite() && is_network_admin() && !IS_PROFILE_PAGE && current_user_can( 'manage_network_options' ) && !isset($super_admins) && empty( $_POST['super_admin'] ) == is_super_admin( $user_id ) ) {
	empty( $_POST['super_admin'] ) ? revoke_super_admin( $user_id ) : grant_super_admin( $user_id );
}

if ( !is_wp_error( $errors ) ) {
	$redirect = add_query_arg( 'updated', true, get_edit_user_link( $user_id ) );
	if ( $wp_http_referer )
		$redirect = add_query_arg('wp_http_referer', urlencode($wp_http_referer), $redirect);
	wp_redirect($redirect);
	exit;
}

default:
$profileuser = get_user_to_edit($user_id);

if ( !current_user_can('edit_user', $user_id) )
	wp_die(__('You do not have permission to edit this user.'));

$sessions = WP_Session_Tokens::get_instance( $profileuser->ID );

include(ABSPATH . 'wp-admin/admin-header.php');
?>

<?php if ( !IS_PROFILE_PAGE && is_super_admin( $profileuser->ID ) && current_user_can( 'manage_network_options' ) ) { ?>
	<div class="updated"><p><strong><?php _e('Important:'); ?></strong> <?php _e('This user has super admin privileges.'); ?></p></div>
<?php } ?>
<?php if ( isset($_GET['updated']) ) : ?>
<div id="message" class="updated notice is-dismissible">
	<?php if ( IS_PROFILE_PAGE ) : ?>
	<p><strong><?php _e('Profile updated.') ?></strong></p>
	<?php else: ?>
	<p><strong><?php _e('User updated.') ?></strong></p>
	<?php endif; ?>
	<?php if ( $wp_http_referer && !IS_PROFILE_PAGE ) : ?>
	<p><a href="<?php echo esc_url( $wp_http_referer ); ?>"><?php _e('&larr; Back to Users'); ?></a></p>
	<?php endif; ?>
</div>
<?php endif; ?>
<?php if ( isset( $errors ) && is_wp_error( $errors ) ) : ?>
<div class="error"><p><?php echo implode( "</p>\n<p>", $errors->get_error_messages() ); ?></p></div>
<?php endif; ?>

<div class="wrap" id="profile-page">
<h1>
<?php
echo esc_html( $title );
if ( ! IS_PROFILE_PAGE ) {
	if ( current_user_can( 'create_users' ) ) { ?>
		<a href="user-new.php" class="page-title-action"><?php echo esc_html_x( 'Add New', 'user' ); ?></a>
	<?php } elseif ( is_multisite() && current_user_can( 'promote_users' ) ) { ?>
		<a href="user-new.php" class="page-title-action"><?php echo esc_html_x( 'Add Existing', 'user' ); ?></a>
	<?php }
} ?>
</h1>
<form id="your-profile" action="<?php echo esc_url( self_admin_url( IS_PROFILE_PAGE ? 'profile.php' : 'user-edit.php' ) ); ?>" method="post" novalidate="novalidate"<?php
	/**
	 * Fires inside the your-profile form tag on the user editing screen.
	 *
	 * @since 3.0.0
	 */
	do_action( 'user_edit_form_tag' );
?>>
<?php wp_nonce_field('update-user_' . $user_id) ?>
<?php if ( $wp_http_referer ) : ?>
	<input type="hidden" name="wp_http_referer" value="<?php echo esc_url($wp_http_referer); ?>" />
<?php endif; ?>
<p>
<input type="hidden" name="from" value="profile" />
<input type="hidden" name="checkuser_id" value="<?php echo get_current_user_id(); ?>" />
</p>

<h2><?php _e( 'Personal Options' ); ?></h2>

<table class="form-table">
<?php if ( ! ( IS_PROFILE_PAGE && ! $user_can_edit ) ) : ?>
	<tr class="user-rich-editing-wrap">
		<th scope="row"><?php _e( 'Visual Editor' ); ?></th>
		<td><label for="rich_editing"><input name="rich_editing" type="checkbox" id="rich_editing" value="false" <?php if ( ! empty( $profileuser->rich_editing ) ) checked( 'false', $profileuser->rich_editing ); ?> /> <?php _e( 'Disable the visual editor when writing' ); ?></label></td>
	</tr>
<?php endif; ?>
<?php if ( count($_wp_admin_css_colors) > 1 && has_action('admin_color_scheme_picker') ) : ?>
<tr class="user-admin-color-wrap">
<th scope="row"><?php _e('Admin Color Scheme')?></th>
<td><?php
	/**
	 * Fires in the 'Admin Color Scheme' section of the user editing screen.
	 *
	 * The section is only enabled if a callback is hooked to the action,
	 * and if there is more than one defined color scheme for the admin.
	 *
	 * @since 3.0.0
	 * @since 3.8.1 Added `$user_id` parameter.
	 *
	 * @param int $user_id The user ID.
	 */
	do_action( 'admin_color_scheme_picker', $user_id );
?></td>
</tr>
<?php
endif; // $_wp_admin_css_colors
if ( !( IS_PROFILE_PAGE && !$user_can_edit ) ) : ?>
<tr class="user-comment-shortcuts-wrap">
<th scope="row"><?php _e( 'Keyboard Shortcuts' ); ?></th>
<td><label for="comment_shortcuts"><input type="checkbox" name="comment_shortcuts" id="comment_shortcuts" value="true" <?php if ( ! empty( $profileuser->comment_shortcuts ) ) checked( 'true', $profileuser->comment_shortcuts ); ?> /> <?php _e('Enable keyboard shortcuts for comment moderation.'); ?></label> <?php _e('<a href="https://codex.wordpress.org/Keyboard_Shortcuts" target="_blank">More information</a>'); ?></td>
</tr>
<?php endif; ?>
<tr class="show-admin-bar user-admin-bar-front-wrap">
<th scope="row"><?php _e( 'Toolbar' ); ?></th>
<td><fieldset><legend class="screen-reader-text"><span><?php _e('Toolbar') ?></span></legend>
<label for="admin_bar_front">
<input name="admin_bar_front" type="checkbox" id="admin_bar_front" value="1"<?php checked( _get_admin_bar_pref( 'front', $profileuser->ID ) ); ?> />
<?php _e( 'Show Toolbar when viewing site' ); ?></label><br />
</fieldset>
</td>
</tr>
<?php
/**
 * Fires at the end of the 'Personal Options' settings table on the user editing screen.
 *
 * @since 2.7.0
 *
 * @param WP_User $profileuser The current WP_User object.
 */
do_action( 'personal_options', $profileuser );
?>

</table>
<?php
	if ( IS_PROFILE_PAGE ) {
		/**
		 * Fires after the 'Personal Options' settings table on the 'Your Profile' editing screen.
		 *
		 * The action only fires if the current user is editing their own profile.
		 *
		 * @since 2.0.0
		 *
		 * @param WP_User $profileuser The current WP_User object.
		 */
		do_action( 'profile_personal_options', $profileuser );
	}
?>

<h2><?php _e( 'Name' ); ?></h2>

<table class="form-table">
	<tr class="user-user-login-wrap">
		<th><label for="user_login"><?php _e('Username'); ?></label></th>
		<td><input type="text" name="user_login" id="user_login" value="<?php echo esc_attr($profileuser->user_login); ?>" disabled="disabled" class="regular-text" /> <span class="description"><?php _e('Usernames cannot be changed.'); ?></span></td>
	</tr>

<?php if ( !IS_PROFILE_PAGE && !is_network_admin() ) : ?>
<tr class="user-role-wrap"><th><label for="role"><?php _e('Role') ?></label></th>
<td><select name="role" id="role">
<?php
// Compare user role against currently editable roles
$user_roles = array_intersect( array_values( $profileuser->roles ), array_keys( get_editable_roles() ) );
$user_role  = reset( $user_roles );

// print the full list of roles with the primary one selected.
wp_dropdown_roles($user_role);

// print the 'no role' option. Make it selected if the user has no role yet.
if ( $user_role )
	echo '<option value="">' . __('&mdash; No role for this site &mdash;') . '</option>';
else
	echo '<option value="" selected="selected">' . __('&mdash; No role for this site &mdash;') . '</option>';
?>
</select></td></tr>
<?php endif; //!IS_PROFILE_PAGE

if ( is_multisite() && is_network_admin() && ! IS_PROFILE_PAGE && current_user_can( 'manage_network_options' ) && !isset($super_admins) ) { ?>
<tr class="user-super-admin-wrap"><th><?php _e('Super Admin'); ?></th>
<td>
<?php if ( $profileuser->user_email != get_site_option( 'admin_email' ) || ! is_super_admin( $profileuser->ID ) ) : ?>
<p><label><input type="checkbox" id="super_admin" name="super_admin"<?php checked( is_super_admin( $profileuser->ID ) ); ?> /> <?php _e( 'Grant this user super admin privileges for the Network.' ); ?></label></p>
<?php else : ?>
<p><?php _e( 'Super admin privileges cannot be removed because this user has the network admin email.' ); ?></p>
<?php endif; ?>
</td></tr>
<?php } ?>

<tr class="user-first-name-wrap">
	<th><label for="first_name"><?php _e('First Name') ?></label></th>
	<td><input type="text" name="first_name" id="first_name" value="<?php echo esc_attr($profileuser->first_name) ?>" class="regular-text" /></td>
</tr>

<tr class="user-last-name-wrap">
	<th><label for="last_name"><?php _e('Last Name') ?></label></th>
	<td><input type="text" name="last_name" id="last_name" value="<?php echo esc_attr($profileuser->last_name) ?>" class="regular-text" /></td>
</tr>

<tr class="user-nickname-wrap">
	<th><label for="nickname"><?php _e('Nickname'); ?> <span class="description"><?php _e('(required)'); ?></span></label></th>
	<td><input type="text" name="nickname" id="nickname" value="<?php echo esc_attr($profileuser->nickname) ?>" class="regular-text" /></td>
</tr>

<tr class="user-display-name-wrap">
	<th><label for="display_name"><?php _e('Display name publicly as') ?></label></th>
	<td>
		<select name="display_name" id="display_name">
		<?php
			$public_display = array();
			$public_display['display_nickname']  = $profileuser->nickname;
			$public_display['display_username']  = $profileuser->user_login;

			if ( !empty($profileuser->first_name) )
				$public_display['display_firstname'] = $profileuser->first_name;

			if ( !empty($profileuser->last_name) )
				$public_display['display_lastname'] = $profileuser->last_name;

			if ( !empty($profileuser->first_name) && !empty($profileuser->last_name) ) {
				$public_display['display_firstlast'] = $profileuser->first_name . ' ' . $profileuser->last_name;
				$public_display['display_lastfirst'] = $profileuser->last_name . ' ' . $profileuser->first_name;
			}

			if ( !in_array( $profileuser->display_name, $public_display ) ) // Only add this if it isn't duplicated elsewhere
				$public_display = array( 'display_displayname' => $profileuser->display_name ) + $public_display;

			$public_display = array_map( 'trim', $public_display );
			$public_display = array_unique( $public_display );

			foreach ( $public_display as $id => $item ) {
		?>
			<option <?php selected( $profileuser->display_name, $item ); ?>><?php echo $item; ?></option>
		<?php
			}
		?>
		</select>
	</td>
</tr>
</table>

<h2><?php _e( 'Contact Info' ); ?></h2>

<table class="form-table">
<tr class="user-email-wrap">
	<th><label for="email"><?php _e('Email'); ?> <span class="description"><?php _e('(required)'); ?></span></label></th>
	<td><input type="email" name="email" id="email" value="<?php echo esc_attr( $profileuser->user_email ) ?>" class="regular-text ltr" />
	<?php
	$new_email = get_option( $current_user->ID . '_new_email' );
	if ( $new_email && $new_email['newemail'] != $current_user->user_email && $profileuser->ID == $current_user->ID ) : ?>
	<div class="updated inline">
	<p><?php
		printf(
			__( 'There is a pending change of your email to %1$s. <a href="%2$s">Cancel</a>' ),
			'<code>' . $new_email['newemail'] . '</code>',
			esc_url( self_admin_url( 'profile.php?dismiss=' . $current_user->ID . '_new_email' ) )
	); ?></p>
	</div>
	<?php endif; ?>
	</td>
</tr>

<tr class="user-url-wrap">
	<th><label for="url"><?php _e('Website') ?></label></th>
	<td><input type="url" name="url" id="url" value="<?php echo esc_attr( $profileuser->user_url ) ?>" class="regular-text code" /></td>
</tr>

<?php
	foreach ( wp_get_user_contact_methods( $profileuser ) as $name => $desc ) {
?>
<tr class="user-<?php echo $name; ?>-wrap">
	<th><label for="<?php echo $name; ?>">
		<?php
		/**
		 * Filter a user contactmethod label.
		 *
		 * The dynamic portion of the filter hook, `$name`, refers to
		 * each of the keys in the contactmethods array.
		 *
		 * @since 2.9.0
		 *
		 * @param string $desc The translatable label for the contactmethod.
		 */
		echo apply_filters( "user_{$name}_label", $desc );
		?>
	</label></th>
	<td><input type="text" name="<?php echo $name; ?>" id="<?php echo $name; ?>" value="<?php echo esc_attr($profileuser->$name) ?>" class="regular-text" /></td>
</tr>
<?php
	}
?>
</table>

<h2><?php IS_PROFILE_PAGE ? _e( 'About Yourself' ) : _e( 'About the user' ); ?></h2>

<table class="form-table">
<tr class="user-description-wrap">
	<th><label for="description"><?php _e('Biographical Info'); ?></label></th>
	<td><textarea name="description" id="description" rows="5" cols="30"><?php echo $profileuser->description; // textarea_escaped ?></textarea>
	<p class="description"><?php _e('Share a little biographical information to fill out your profile. This may be shown publicly.'); ?></p></td>
</tr>

<?php if ( get_option( 'show_avatars' ) ) : ?>
<tr class="user-profile-picture">
	<th><?php _e( 'Profile Picture' ); ?></th>
	<td>
		<?php echo get_avatar( $user_id ); ?>
		<p class="description"><?php
			if ( IS_PROFILE_PAGE ) {
				/* translators: %s: Gravatar URL */
				$description = sprintf( __( 'You can change your profile picture on <a href="%s">Gravatar</a>.' ),
					__( 'https://en.gravatar.com/' )
				);
			} else {
				$description = '';
			}

			/**
			 * Filter the user profile picture description displayed under the Gravatar.
			 *
			 * @since 4.4.0
			 *
			 * @param string $description The description that will be printed.
			 */
			echo apply_filters( 'user_profile_picture_description', $description );
		?></p>
	</td>
</tr>
<?php endif; ?>

<?php
/**
 * Filter the display of the password fields.
 *
 * @since 1.5.1
 * @since 2.8.0 Added the `$profileuser` parameter.
 * @since 4.4.0 Now evaluated only in user-edit.php.
 *
 * @param bool    $show        Whether to show the password fields. Default true.
 * @param WP_User $profileuser User object for the current user to edit.
 */
if ( $show_password_fields = apply_filters( 'show_password_fields', true, $profileuser ) ) :
?>
</table>

<h2><?php _e( 'Account Management' ); ?></h2>
<table class="form-table">
<tr id="password" class="user-pass1-wrap">
	<th><label for="pass1"><?php _e( 'New Password' ); ?></label></th>
	<td>
		<input class="hidden" value=" " /><!-- #24364 workaround -->
		<button type="button" class="button button-secondary wp-generate-pw hide-if-no-js"><?php _e( 'Generate Password' ); ?></button>
		<div class="wp-pwd hide-if-js">
			<span class="password-input-wrapper">
				<input type="password" name="pass1" id="pass1" class="regular-text" value="" autocomplete="off" data-pw="<?php echo esc_attr( wp_generate_password( 24 ) ); ?>" aria-describedby="pass-strength-result" />
			</span>
			<button type="button" class="button button-secondary wp-hide-pw hide-if-no-js" data-toggle="0" aria-label="<?php esc_attr_e( 'Hide password' ); ?>">
				<span class="dashicons dashicons-hidden"></span>
				<span class="text"><?php _e( 'Hide' ); ?></span>
			</button>
			<button type="button" class="button button-secondary wp-cancel-pw hide-if-no-js" data-toggle="0" aria-label="<?php esc_attr_e( 'Cancel password change' ); ?>">
				<span class="text"><?php _e( 'Cancel' ); ?></span>
			</button>
			<div style="display:none" id="pass-strength-result" aria-live="polite"></div>
		</div>
	</td>
</tr>
<tr class="user-pass2-wrap hide-if-js">
	<th scope="row"><label for="pass2"><?php _e( 'Repeat New Password' ); ?></label></th>
	<td>
	<input name="pass2" type="password" id="pass2" class="regular-text" value="" autocomplete="off" />
	<p class="description"><?php _e( 'Type your new password again.' ); ?></p>
	</td>
</tr>
<tr class="pw-weak">
	<th><?php _e( 'Confirm Password' ); ?></th>
	<td>
		<label>
			<input type="checkbox" name="pw_weak" class="pw-checkbox" />
			<?php _e( 'Confirm use of weak password' ); ?>
		</label>
	</td>
</tr>
<?php endif; ?>

<?php
if ( IS_PROFILE_PAGE && count( $sessions->get_all() ) === 1 ) : ?>
	<tr class="user-sessions-wrap hide-if-no-js">
		<th><?php _e( 'Sessions' ); ?></th>
		<td aria-live="assertive">
			<div class="destroy-sessions"><button type="button" disabled class="button button-secondary"><?php _e( 'Log Out Everywhere Else' ); ?></button></div>
			<p class="description">
				<?php _e( 'You are only logged in at this location.' ); ?>
			</p>
		</td>
	</tr>
<?php elseif ( IS_PROFILE_PAGE && count( $sessions->get_all() ) > 1 ) : ?>
	<tr class="user-sessions-wrap hide-if-no-js">
		<th><?php _e( 'Sessions' ); ?></th>
		<td aria-live="assertive">
			<div class="destroy-sessions"><button type="button" class="button button-secondary" id="destroy-sessions"><?php _e( 'Log Out Everywhere Else' ); ?></button></div>
			<p class="description">
				<?php _e( 'Did you lose your phone or leave your account logged in at a public computer? You can log out everywhere else, and stay logged in here.' ); ?>
			</p>
		</td>
	</tr>
<?php elseif ( ! IS_PROFILE_PAGE && $sessions->get_all() ) : ?>
	<tr class="user-sessions-wrap hide-if-no-js">
		<th><?php _e( 'Sessions' ); ?></th>
		<td>
			<p><button type="button" class="button button-secondary" id="destroy-sessions"><?php _e( 'Log Out Everywhere' ); ?></button></p>
			<p class="description">
				<?php
				/* translators: 1: User's display name. */
				printf( __( 'Log %s out of all locations.' ), $profileuser->display_name );
				?>
			</p>
		</td>
	</tr>
<?php endif; ?>

</table>

<?php
	if ( IS_PROFILE_PAGE ) {
		/**
		 * Fires after the 'About Yourself' settings table on the 'Your Profile' editing screen.
		 *
		 * The action only fires if the current user is editing their own profile.
		 *
		 * @since 2.0.0
		 *
		 * @param WP_User $profileuser The current WP_User object.
		 */
		do_action( 'show_user_profile', $profileuser );
	} else {
		/**
		 * Fires after the 'About the User' settings table on the 'Edit User' screen.
		 *
		 * @since 2.0.0
		 *
		 * @param WP_User $profileuser The current WP_User object.
		 */
		do_action( 'edit_user_profile', $profileuser );
	}
?>

<?php
/**
 * Filter whether to display additional capabilities for the user.
 *
 * The 'Additional Capabilities' section will only be enabled if
 * the number of the user's capabilities exceeds their number of
 * roles.
 *
 * @since 2.8.0
 *
 * @param bool    $enable      Whether to display the capabilities. Default true.
 * @param WP_User $profileuser The current WP_User object.
 */
if ( count( $profileuser->caps ) > count( $profileuser->roles )
	&& apply_filters( 'additional_capabilities_display', true, $profileuser )
) : ?>
<h2><?php _e( 'Additional Capabilities' ); ?></h2>
<table class="form-table">
<tr class="user-capabilities-wrap">
	<th scope="row"><?php _e( 'Capabilities' ); ?></th>
	<td>
<?php
	$output = '';
	foreach ( $profileuser->caps as $cap => $value ) {
		if ( ! $wp_roles->is_role( $cap ) ) {
			if ( '' != $output )
				$output .= ', ';
			$output .= $value ? $cap : sprintf( __( 'Denied: %s' ), $cap );
		}
	}
	echo $output;
?>
	</td>
</tr>
</table>
<?php endif; ?>

<input type="hidden" name="action" value="update" />
<input type="hidden" name="user_id" id="user_id" value="<?php echo esc_attr($user_id); ?>" />

<?php submit_button( IS_PROFILE_PAGE ? __('Update Profile') : __('Update User') ); ?>

</form>
</div>
<?php
break;
}
?>
<script type="text/javascript">
	if (window.location.hash == '#password') {
		document.getElementById('pass1').focus();
	}
</script>
<?php
include( ABSPATH . 'wp-admin/admin-footer.php');
