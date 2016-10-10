<?php
/**
 * Multisite network settings administration panel.
 *
 * @package WordPress
 * @subpackage Multisite
 * @since 3.0.0
 */

/** Load WordPress Administration Bootstrap */
require_once( dirname( __FILE__ ) . '/admin.php' );

/** WordPress Translation Install API */
require_once( ABSPATH . 'wp-admin/includes/translation-install.php' );

if ( ! is_multisite() )
	wp_die( __( 'Multisite support is not enabled.' ) );

if ( ! current_user_can( 'manage_network_options' ) )
	wp_die( __( 'You do not have permission to access this page.' ), 403 );

$title = __( 'Network Settings' );
$parent_file = 'settings.php';

add_action( 'admin_head', 'network_settings_add_js' );

get_current_screen()->add_help_tab( array(
		'id'      => 'overview',
		'title'   => __('Overview'),
		'content' =>
			'<p>' . __('This screen sets and changes options for the network as a whole. The first site is the main site in the network and network options are pulled from that original site&#8217;s options.') . '</p>' .
			'<p>' . __('Operational settings has fields for the network&#8217;s name and admin email.') . '</p>' .
			'<p>' . __('Registration settings can disable/enable public signups. If you let others sign up for a site, install spam plugins. Spaces, not commas, should separate names banned as sites for this network.') . '</p>' .
			'<p>' . __('New site settings are defaults applied when a new site is created in the network. These include welcome email for when a new site or user account is registered, and what&#8127;s put in the first post, page, comment, comment author, and comment URL.') . '</p>' .
			'<p>' . __('Upload settings control the size of the uploaded files and the amount of available upload space for each site. You can change the default value for specific sites when you edit a particular site. Allowed file types are also listed (space separated only).') . '</p>' .
			'<p>' . __( 'You can set the language, and the translation files will be automatically downloaded and installed (available if your filesystem is writable).' ) . '</p>' .
			'<p>' . __('Menu setting enables/disables the plugin menus from appearing for non super admins, so that only super admins, not site admins, have access to activate plugins.') . '</p>' .
			'<p>' . __('Super admins can no longer be added on the Options screen. You must now go to the list of existing users on Network Admin > Users and click on Username or the Edit action link below that name. This goes to an Edit User page where you can check a box to grant super admin privileges.') . '</p>'
) );

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __('For more information:') . '</strong></p>' .
	'<p>' . __('<a href="https://codex.wordpress.org/Network_Admin_Settings_Screen" target="_blank">Documentation on Network Settings</a>') . '</p>' .
	'<p>' . __('<a href="https://wordpress.org/support/" target="_blank">Support Forums</a>') . '</p>'
);

if ( $_POST ) {
	/** This action is documented in wp-admin/network/edit.php */
	do_action( 'wpmuadminedit' );

	check_admin_referer( 'siteoptions' );

	$checked_options = array( 'menu_items' => array(), 'registrationnotification' => 'no', 'upload_space_check_disabled' => 1, 'add_new_users' => 0 );
	foreach ( $checked_options as $option_name => $option_unchecked_value ) {
		if ( ! isset( $_POST[$option_name] ) )
			$_POST[$option_name] = $option_unchecked_value;
	}

	$options = array(
		'registrationnotification', 'registration', 'add_new_users', 'menu_items',
		'upload_space_check_disabled', 'blog_upload_space', 'upload_filetypes', 'site_name',
		'first_post', 'first_page', 'first_comment', 'first_comment_url', 'first_comment_author',
		'welcome_email', 'welcome_user_email', 'fileupload_maxk', 'global_terms_enabled',
		'illegal_names', 'limited_email_domains', 'banned_email_domains', 'WPLANG', 'admin_email',
	);

	// Handle translation install.
	if ( ! empty( $_POST['WPLANG'] ) && wp_can_install_language_pack() ) {  // @todo: Skip if already installed
		$language = wp_download_language_pack( $_POST['WPLANG'] );
		if ( $language ) {
			$_POST['WPLANG'] = $language;
		}
	}

	foreach ( $options as $option_name ) {
		if ( ! isset($_POST[$option_name]) )
			continue;
		$value = wp_unslash( $_POST[$option_name] );
		update_site_option( $option_name, $value );
	}

	/**
	 * Fires after the network options are updated.
	 *
	 * @since MU
	 */
	do_action( 'update_wpmu_options' );

	wp_redirect( add_query_arg( 'updated', 'true', network_admin_url( 'settings.php' ) ) );
	exit();
}

include( ABSPATH . 'wp-admin/admin-header.php' );

if ( isset( $_GET['updated'] ) ) {
	?><div id="message" class="updated notice is-dismissible"><p><?php _e( 'Options saved.' ) ?></p></div><?php
}
?>

<div class="wrap">
	<h1><?php echo esc_html( $title ); ?></h1>
	<form method="post" action="settings.php" novalidate="novalidate">
		<?php wp_nonce_field( 'siteoptions' ); ?>
		<h2><?php _e( 'Operational Settings' ); ?></h2>
		<table class="form-table">
			<tr>
				<th scope="row"><label for="site_name"><?php _e( 'Network Title' ) ?></label></th>
				<td>
					<input name="site_name" type="text" id="site_name" class="regular-text" value="<?php echo esc_attr( $current_site->site_name ) ?>" />
				</td>
			</tr>

			<tr>
				<th scope="row"><label for="admin_email"><?php _e( 'Network Admin Email' ) ?></label></th>
				<td>
					<input name="admin_email" type="email" id="admin_email" aria-describedby="admin-email-desc" class="regular-text" value="<?php echo esc_attr( get_site_option( 'admin_email' ) ) ?>" />
					<p class="description" id="admin-email-desc">
						<?php _e( 'This email address will receive notifications. Registration and support emails will also come from this address.' ); ?>
					</p>
				</td>
			</tr>
		</table>
		<h2><?php _e( 'Registration Settings' ); ?></h2>
		<table class="form-table">
			<tr>
				<th scope="row"><?php _e( 'Allow new registrations' ) ?></th>
				<?php
				if ( !get_site_option( 'registration' ) )
					update_site_option( 'registration', 'none' );
				$reg = get_site_option( 'registration' );
				?>
				<td>
					<fieldset>
					<legend class="screen-reader-text"><?php _e( 'New registrations settings' ) ?></legend>
					<label><input name="registration" type="radio" id="registration1" value="none"<?php checked( $reg, 'none') ?> /> <?php _e( 'Registration is disabled.' ); ?></label><br />
					<label><input name="registration" type="radio" id="registration2" value="user"<?php checked( $reg, 'user') ?> /> <?php _e( 'User accounts may be registered.' ); ?></label><br />
					<label><input name="registration" type="radio" id="registration3" value="blog"<?php checked( $reg, 'blog') ?> /> <?php _e( 'Logged in users may register new sites.' ); ?></label><br />
					<label><input name="registration" type="radio" id="registration4" value="all"<?php checked( $reg, 'all') ?> /> <?php _e( 'Both sites and user accounts can be registered.' ); ?></label>
					<?php if ( is_subdomain_install() ) {
						echo '<p class="description">';
						/* translators: 1: NOBLOGREDIRECT 2: wp-config.php */
						printf( __( 'If registration is disabled, please set %1$s in %2$s to a URL you will redirect visitors to if they visit a non-existent site.' ),
							'<code>NOBLOGREDIRECT</code>',
							'<code>wp-config.php</code>'
						);
						echo '</p>';
					} ?>
					</fieldset>
				</td>
			</tr>

			<tr>
				<th scope="row"><?php _e( 'Registration notification' ) ?></th>
				<?php
				if ( !get_site_option( 'registrationnotification' ) )
					update_site_option( 'registrationnotification', 'yes' );
				?>
				<td>
					<label><input name="registrationnotification" type="checkbox" id="registrationnotification" value="yes"<?php checked( get_site_option( 'registrationnotification' ), 'yes' ) ?> /> <?php _e( 'Send the network admin an email notification every time someone registers a site or user account.' ) ?></label>
				</td>
			</tr>

			<tr id="addnewusers">
				<th scope="row"><?php _e( 'Add New Users' ) ?></th>
				<td>
					<label><input name="add_new_users" type="checkbox" id="add_new_users" value="1"<?php checked( get_site_option( 'add_new_users' ) ) ?> /> <?php _e( 'Allow site administrators to add new users to their site via the "Users &rarr; Add New" page.' ); ?></label>
				</td>
			</tr>

			<tr>
				<th scope="row"><label for="illegal_names"><?php _e( 'Banned Names' ) ?></label></th>
				<td>
					<input name="illegal_names" type="text" id="illegal_names" aria-describedby="illegal-names-desc" class="large-text" value="<?php echo esc_attr( implode( " ", (array) get_site_option( 'illegal_names' ) ) ); ?>" size="45" />
					<p class="description" id="illegal-names-desc">
						<?php _e( 'Users are not allowed to register these sites. Separate names by spaces.' ) ?>
					</p>
				</td>
			</tr>

			<tr>
				<th scope="row"><label for="limited_email_domains"><?php _e( 'Limited Email Registrations' ) ?></label></th>
				<td>
					<?php $limited_email_domains = get_site_option( 'limited_email_domains' );
					$limited_email_domains = str_replace( ' ', "\n", $limited_email_domains ); ?>
					<textarea name="limited_email_domains" id="limited_email_domains" aria-describedby="limited-email-domains-desc" cols="45" rows="5">
<?php echo esc_textarea( $limited_email_domains == '' ? '' : implode( "\n", (array) $limited_email_domains ) ); ?></textarea>
					<p class="description" id="limited-email-domains-desc">
						<?php _e( 'If you want to limit site registrations to certain domains. One domain per line.' ) ?>
					</p>
				</td>
			</tr>

			<tr>
				<th scope="row"><label for="banned_email_domains"><?php _e('Banned Email Domains') ?></label></th>
				<td>
					<textarea name="banned_email_domains" id="banned_email_domains" aria-describedby="banned-email-domains-desc" cols="45" rows="5">
<?php echo esc_textarea( get_site_option( 'banned_email_domains' ) == '' ? '' : implode( "\n", (array) get_site_option( 'banned_email_domains' ) ) ); ?></textarea>
					<p class="description" id="banned-email-domains-desc">
						<?php _e( 'If you want to ban domains from site registrations. One domain per line.' ) ?>
					</p>
				</td>
			</tr>

		</table>
		<h2><?php _e( 'New Site Settings' ); ?></h2>
		<table class="form-table">

			<tr>
				<th scope="row"><label for="welcome_email"><?php _e( 'Welcome Email' ) ?></label></th>
				<td>
					<textarea name="welcome_email" id="welcome_email" aria-describedby="welcome-email-desc" rows="5" cols="45" class="large-text">
<?php echo esc_textarea( get_site_option( 'welcome_email' ) ) ?></textarea>
					<p class="description" id="welcome-email-desc">
						<?php _e( 'The welcome email sent to new site owners.' ) ?>
					</p>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="welcome_user_email"><?php _e( 'Welcome User Email' ) ?></label></th>
				<td>
					<textarea name="welcome_user_email" id="welcome_user_email" aria-describedby="welcome-user-email-desc" rows="5" cols="45" class="large-text">
<?php echo esc_textarea( get_site_option( 'welcome_user_email' ) ) ?></textarea>
					<p class="description" id="welcome-user-email-desc">
						<?php _e( 'The welcome email sent to new users.' ) ?>
					</p>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="first_post"><?php _e( 'First Post' ) ?></label></th>
				<td>
					<textarea name="first_post" id="first_post" aria-describedby="first-post-desc" rows="5" cols="45" class="large-text">
<?php echo esc_textarea( get_site_option( 'first_post' ) ) ?></textarea>
					<p class="description" id="first-post-desc">
						<?php _e( 'The first post on a new site.' ) ?>
					</p>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="first_page"><?php _e( 'First Page' ) ?></label></th>
				<td>
					<textarea name="first_page" id="first_page" aria-describedby="first-page-desc" rows="5" cols="45" class="large-text">
<?php echo esc_textarea( get_site_option( 'first_page' ) ) ?></textarea>
					<p class="description" id="first-page-desc">
						<?php _e( 'The first page on a new site.' ) ?>
					</p>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="first_comment"><?php _e( 'First Comment' ) ?></label></th>
				<td>
					<textarea name="first_comment" id="first_comment" aria-describedby="first-comment-desc" rows="5" cols="45" class="large-text">
<?php echo esc_textarea( get_site_option( 'first_comment' ) ) ?></textarea>
					<p class="description" id="first-comment-desc">
						<?php _e( 'The first comment on a new site.' ) ?>
					</p>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="first_comment_author"><?php _e( 'First Comment Author' ) ?></label></th>
				<td>
					<input type="text" size="40" name="first_comment_author" id="first_comment_author" aria-describedby="first-comment-author-desc" value="<?php echo get_site_option('first_comment_author') ?>" />
					<p class="description" id="first-comment-author-desc">
						<?php _e( 'The author of the first comment on a new site.' ) ?>
					</p>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="first_comment_url"><?php _e( 'First Comment URL' ) ?></label></th>
				<td>
					<input type="text" size="40" name="first_comment_url" id="first_comment_url" aria-describedby="first-comment-url-desc" value="<?php echo esc_attr( get_site_option( 'first_comment_url' ) ) ?>" />
					<p class="description" id="first-comment-url-desc">
						<?php _e( 'The URL for the first comment on a new site.' ) ?>
					</p>
				</td>
			</tr>
		</table>
		<h2><?php _e( 'Upload Settings' ); ?></h2>
		<table class="form-table">
			<tr>
				<th scope="row"><?php _e( 'Site upload space' ) ?></th>
				<td>
					<label><input type="checkbox" id="upload_space_check_disabled" name="upload_space_check_disabled" value="0"<?php checked( (bool) get_site_option( 'upload_space_check_disabled' ), false ) ?>/> <?php printf( __( 'Limit total size of files uploaded to %s MB' ), '</label><label><input name="blog_upload_space" type="number" min="0" style="width: 100px" id="blog_upload_space" aria-describedby="blog-upload-space-desc" value="' . esc_attr( get_site_option('blog_upload_space', 100) ) . '" />' ); ?></label><br />
					<p class="screen-reader-text" id="blog-upload-space-desc">
						<?php _e( 'Size in megabytes' ) ?>
					</p>
				</td>
			</tr>

			<tr>
				<th scope="row"><label for="upload_filetypes"><?php _e( 'Upload file types' ) ?></label></th>
				<td>
					<input name="upload_filetypes" type="text" id="upload_filetypes" aria-describedby="upload-filetypes-desc" class="large-text" value="<?php echo esc_attr( get_site_option( 'upload_filetypes', 'jpg jpeg png gif' ) ) ?>" size="45" />
					<p class="description" id="upload-filetypes-desc">
						<?php _e( 'Allowed file types. Separate types by spaces.' ) ?>
					</p>
				</td>
			</tr>

			<tr>
				<th scope="row"><label for="fileupload_maxk"><?php _e( 'Max upload file size' ) ?></label></th>
				<td>
					<?php printf( _x( '%s KB', 'File size in kilobytes' ), '<input name="fileupload_maxk" type="number" min="0" style="width: 100px" id="fileupload_maxk" aria-describedby="fileupload-maxk-desc" value="' . esc_attr( get_site_option( 'fileupload_maxk', 300 ) ) . '" />' ); ?>
					<p class="screen-reader-text" id="fileupload-maxk-desc">
						<?php _e( 'Size in kilobytes' ) ?>
					</p>
				</td>
			</tr>
		</table>

		<?php
		$languages = get_available_languages();
		$translations = wp_get_available_translations();
		if ( ! empty( $languages ) || ! empty( $translations ) ) {
			?>
			<h2><?php _e( 'Language Settings' ); ?></h2>
			<table class="form-table">
				<tr>
					<th><label for="WPLANG"><?php _e( 'Default Language' ); ?></label></th>
					<td>
						<?php
						$lang = get_site_option( 'WPLANG' );
						if ( ! in_array( $lang, $languages ) ) {
							$lang = '';
						}

						wp_dropdown_languages( array(
							'name'         => 'WPLANG',
							'id'           => 'WPLANG',
							'selected'     => $lang,
							'languages'    => $languages,
							'translations' => $translations,
							'show_available_translations' => wp_can_install_language_pack(),
						) );
						?>
					</td>
				</tr>
			</table>
			<?php
		}
		?>

		<h2><?php _e( 'Menu Settings' ); ?></h2>
		<table id="menu" class="form-table">
			<tr>
				<th scope="row"><?php _e( 'Enable administration menus' ); ?></th>
				<td>
			<?php
			$menu_perms = get_site_option( 'menu_items' );
			/**
			 * Filter available network-wide administration menu options.
			 *
			 * Options returned to this filter are output as individual checkboxes that, when selected,
			 * enable site administrator access to the specified administration menu in certain contexts.
			 *
			 * Adding options for specific menus here hinges on the appropriate checks and capabilities
			 * being in place in the site dashboard on the other side. For instance, when the single
			 * default option, 'plugins' is enabled, site administrators are granted access to the Plugins
			 * screen in their individual sites' dashboards.
			 *
			 * @since MU
			 *
			 * @param array $admin_menus The menu items available.
			 */
			$menu_items = apply_filters( 'mu_menu_items', array( 'plugins' => __( 'Plugins' ) ) );
			$fieldset_end = '';
			if ( count( (array) $menu_items ) > 1 ) {
				echo '<fieldset><legend class="screen-reader-text">' . __( 'Enable menus' ) . '</legend>';
				$fieldset_end = '</fieldset>';
			}
			foreach ( (array) $menu_items as $key => $val ) {
				echo "<label><input type='checkbox' name='menu_items[" . $key . "]' value='1'" . ( isset( $menu_perms[$key] ) ? checked( $menu_perms[$key], '1', false ) : '' ) . " /> " . esc_html( $val ) . "</label><br/>";
			}
			echo $fieldset_end;
			?>
				</td>
			</tr>
		</table>

		<?php
		/**
		 * Fires at the end of the Network Settings form, before the submit button.
		 *
		 * @since MU
		 */
		do_action( 'wpmu_options' ); ?>
		<?php submit_button(); ?>
	</form>
</div>

<?php include( ABSPATH . 'wp-admin/admin-footer.php' ); ?>
