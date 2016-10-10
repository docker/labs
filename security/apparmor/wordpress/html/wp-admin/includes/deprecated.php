<?php
/**
 * Deprecated admin functions from past WordPress versions. You shouldn't use these
 * functions and look for the alternatives instead. The functions will be removed
 * in a later version.
 *
 * @package WordPress
 * @subpackage Deprecated
 */

/*
 * Deprecated functions come here to die.
 */

/**
 * @since 2.1.0
 * @deprecated 2.1.0 Use wp_editor()
 * @see wp_editor()
 */
function tinymce_include() {
	_deprecated_function( __FUNCTION__, '2.1', 'wp_editor()' );

	wp_tiny_mce();
}

/**
 * Unused Admin function.
 *
 * @since 2.0.0
 * @deprecated 2.5.0
 *
 */
function documentation_link() {
	_deprecated_function( __FUNCTION__, '2.5' );
}

/**
 * Calculates the new dimensions for a downsampled image.
 *
 * @since 2.0.0
 * @deprecated 3.0.0 Use wp_constrain_dimensions()
 * @see wp_constrain_dimensions()
 *
 * @param int $width Current width of the image
 * @param int $height Current height of the image
 * @param int $wmax Maximum wanted width
 * @param int $hmax Maximum wanted height
 * @return array Shrunk dimensions (width, height).
 */
function wp_shrink_dimensions( $width, $height, $wmax = 128, $hmax = 96 ) {
	_deprecated_function( __FUNCTION__, '3.0', 'wp_constrain_dimensions()' );
	return wp_constrain_dimensions( $width, $height, $wmax, $hmax );
}

/**
 * Calculated the new dimensions for a downsampled image.
 *
 * @since 2.0.0
 * @deprecated 3.5.0 Use wp_constrain_dimensions()
 * @see wp_constrain_dimensions()
 *
 * @param int $width Current width of the image
 * @param int $height Current height of the image
 * @return array Shrunk dimensions (width, height).
 */
function get_udims( $width, $height ) {
	_deprecated_function( __FUNCTION__, '3.5', 'wp_constrain_dimensions()' );
	return wp_constrain_dimensions( $width, $height, 128, 96 );
}

/**
 * Legacy function used to generate the categories checklist control.
 *
 * @since 0.71
 * @deprecated 2.6.0 Use wp_category_checklist()
 * @see wp_category_checklist()
 *
 * @param int $default       Unused.
 * @param int $parent        Unused.
 * @param array $popular_ids Unused.
 */
function dropdown_categories( $default = 0, $parent = 0, $popular_ids = array() ) {
	_deprecated_function( __FUNCTION__, '2.6', 'wp_category_checklist()' );
	global $post_ID;
	wp_category_checklist( $post_ID );
}

/**
 * Legacy function used to generate a link categories checklist control.
 *
 * @since 2.1.0
 * @deprecated 2.6.0 Use wp_link_category_checklist()
 * @see wp_link_category_checklist()
 *
 * @param int $default Unused.
 */
function dropdown_link_categories( $default = 0 ) {
	_deprecated_function( __FUNCTION__, '2.6', 'wp_link_category_checklist()' );
	global $link_id;
	wp_link_category_checklist( $link_id );
}

/**
 * Get the real filesystem path to a file to edit within the admin.
 *
 * @since 1.5.0
 * @deprecated 2.9.0
 * @uses WP_CONTENT_DIR Full filesystem path to the wp-content directory.
 *
 * @param string $file Filesystem path relative to the wp-content directory.
 * @return string Full filesystem path to edit.
 */
function get_real_file_to_edit( $file ) {
	_deprecated_function( __FUNCTION__, '2.9' );

	return WP_CONTENT_DIR . $file;
}

/**
 * Legacy function used for generating a categories drop-down control.
 *
 * @since 1.2.0
 * @deprecated 3.0.0 Use wp_dropdown_categories()
 * @see wp_dropdown_categories()
 *
 * @param int $currentcat    Optional. ID of the current category. Default 0.
 * @param int $currentparent Optional. Current parent category ID. Default 0.
 * @param int $parent        Optional. Parent ID to retrieve categories for. Default 0.
 * @param int $level         Optional. Number of levels deep to display. Default 0.
 * @param array $categories  Optional. Categories to include in the control. Default 0.
 * @return bool|null False if no categories were found.
 */
function wp_dropdown_cats( $currentcat = 0, $currentparent = 0, $parent = 0, $level = 0, $categories = 0 ) {
	_deprecated_function( __FUNCTION__, '3.0', 'wp_dropdown_categories()' );
	if (!$categories )
		$categories = get_categories( array('hide_empty' => 0) );

	if ( $categories ) {
		foreach ( $categories as $category ) {
			if ( $currentcat != $category->term_id && $parent == $category->parent) {
				$pad = str_repeat( '&#8211; ', $level );
				$category->name = esc_html( $category->name );
				echo "\n\t<option value='$category->term_id'";
				if ( $currentparent == $category->term_id )
					echo " selected='selected'";
				echo ">$pad$category->name</option>";
				wp_dropdown_cats( $currentcat, $currentparent, $category->term_id, $level +1, $categories );
			}
		}
	} else {
		return false;
	}
}

/**
 * Register a setting and its sanitization callback
 *
 * @since 2.7.0
 * @deprecated 3.0.0 Use register_setting()
 * @see register_setting()
 *
 * @param string $option_group A settings group name. Should correspond to a whitelisted option key name.
 * 	Default whitelisted option key names include "general," "discussion," and "reading," among others.
 * @param string $option_name The name of an option to sanitize and save.
 * @param callable $sanitize_callback A callback function that sanitizes the option's value.
 */
function add_option_update_handler( $option_group, $option_name, $sanitize_callback = '' ) {
	_deprecated_function( __FUNCTION__, '3.0', 'register_setting()' );
	register_setting( $option_group, $option_name, $sanitize_callback );
}

/**
 * Unregister a setting
 *
 * @since 2.7.0
 * @deprecated 3.0.0 Use unregister_setting()
 * @see unregister_setting()
 *
 * @param string $option_group
 * @param string $option_name
 * @param callable $sanitize_callback
 */
function remove_option_update_handler( $option_group, $option_name, $sanitize_callback = '' ) {
	_deprecated_function( __FUNCTION__, '3.0', 'unregister_setting()' );
	unregister_setting( $option_group, $option_name, $sanitize_callback );
}

/**
 * Determines the language to use for CodePress syntax highlighting.
 *
 * @since 2.8.0
 * @deprecated 3.0.0
 *
 * @param string $filename
**/
function codepress_get_lang( $filename ) {
	_deprecated_function( __FUNCTION__, '3.0' );
}

/**
 * Adds JavaScript required to make CodePress work on the theme/plugin editors.
 *
 * @since 2.8.0
 * @deprecated 3.0.0
**/
function codepress_footer_js() {
	_deprecated_function( __FUNCTION__, '3.0' );
}

/**
 * Determine whether to use CodePress.
 *
 * @since 2.8.0
 * @deprecated 3.0.0
**/
function use_codepress() {
	_deprecated_function( __FUNCTION__, '3.0' );
}

/**
 * Get all user IDs.
 *
 * @deprecated 3.1.0 Use get_users()
 *
 * @return array List of user IDs.
 */
function get_author_user_ids() {
	_deprecated_function( __FUNCTION__, '3.1', 'get_users()' );

	global $wpdb;
	if ( !is_multisite() )
		$level_key = $wpdb->get_blog_prefix() . 'user_level';
	else
		$level_key = $wpdb->get_blog_prefix() . 'capabilities'; // wpmu site admins don't have user_levels

	return $wpdb->get_col( $wpdb->prepare("SELECT user_id FROM $wpdb->usermeta WHERE meta_key = %s AND meta_value != '0'", $level_key) );
}

/**
 * Gets author users who can edit posts.
 *
 * @deprecated 3.1.0 Use get_users()
 *
 * @param int $user_id User ID.
 * @return array|bool List of editable authors. False if no editable users.
 */
function get_editable_authors( $user_id ) {
	_deprecated_function( __FUNCTION__, '3.1', 'get_users()' );

	global $wpdb;

	$editable = get_editable_user_ids( $user_id );

	if ( !$editable ) {
		return false;
	} else {
		$editable = join(',', $editable);
		$authors = $wpdb->get_results( "SELECT * FROM $wpdb->users WHERE ID IN ($editable) ORDER BY display_name" );
	}

	return apply_filters('get_editable_authors', $authors);
}

/**
 * Gets the IDs of any users who can edit posts.
 *
 * @deprecated 3.1.0 Use get_users()
 *
 * @param int $user_id User ID.
 * @param bool $exclude_zeros Optional, default is true. Whether to exclude zeros.
 * @return mixed
 */
function get_editable_user_ids( $user_id, $exclude_zeros = true, $post_type = 'post' ) {
	_deprecated_function( __FUNCTION__, '3.1', 'get_users()' );

	global $wpdb;

	if ( ! $user = get_userdata( $user_id ) )
		return array();
	$post_type_obj = get_post_type_object($post_type);

	if ( ! $user->has_cap($post_type_obj->cap->edit_others_posts) ) {
		if ( $user->has_cap($post_type_obj->cap->edit_posts) || ! $exclude_zeros )
			return array($user->ID);
		else
			return array();
	}

	if ( !is_multisite() )
		$level_key = $wpdb->get_blog_prefix() . 'user_level';
	else
		$level_key = $wpdb->get_blog_prefix() . 'capabilities'; // wpmu site admins don't have user_levels

	$query = $wpdb->prepare("SELECT user_id FROM $wpdb->usermeta WHERE meta_key = %s", $level_key);
	if ( $exclude_zeros )
		$query .= " AND meta_value != '0'";

	return $wpdb->get_col( $query );
}

/**
 * Gets all users who are not authors.
 *
 * @deprecated 3.1.0 Use get_users()
 */
function get_nonauthor_user_ids() {
	_deprecated_function( __FUNCTION__, '3.1', 'get_users()' );

	global $wpdb;

	if ( !is_multisite() )
		$level_key = $wpdb->get_blog_prefix() . 'user_level';
	else
		$level_key = $wpdb->get_blog_prefix() . 'capabilities'; // wpmu site admins don't have user_levels

	return $wpdb->get_col( $wpdb->prepare("SELECT user_id FROM $wpdb->usermeta WHERE meta_key = %s AND meta_value = '0'", $level_key) );
}

if ( ! class_exists( 'WP_User_Search', false ) ) :
/**
 * WordPress User Search class.
 *
 * @since 2.1.0
 * @deprecated 3.1.0 Use WP_User_Query
 */
class WP_User_Search {

	/**
	 * {@internal Missing Description}}
	 *
	 * @since 2.1.0
	 * @access private
	 * @var mixed
	 */
	var $results;

	/**
	 * {@internal Missing Description}}
	 *
	 * @since 2.1.0
	 * @access private
	 * @var string
	 */
	var $search_term;

	/**
	 * Page number.
	 *
	 * @since 2.1.0
	 * @access private
	 * @var int
	 */
	var $page;

	/**
	 * Role name that users have.
	 *
	 * @since 2.5.0
	 * @access private
	 * @var string
	 */
	var $role;

	/**
	 * Raw page number.
	 *
	 * @since 2.1.0
	 * @access private
	 * @var int|bool
	 */
	var $raw_page;

	/**
	 * Amount of users to display per page.
	 *
	 * @since 2.1.0
	 * @access public
	 * @var int
	 */
	var $users_per_page = 50;

	/**
	 * {@internal Missing Description}}
	 *
	 * @since 2.1.0
	 * @access private
	 * @var int
	 */
	var $first_user;

	/**
	 * {@internal Missing Description}}
	 *
	 * @since 2.1.0
	 * @access private
	 * @var int
	 */
	var $last_user;

	/**
	 * {@internal Missing Description}}
	 *
	 * @since 2.1.0
	 * @access private
	 * @var string
	 */
	var $query_limit;

	/**
	 * {@internal Missing Description}}
	 *
	 * @since 3.0.0
	 * @access private
	 * @var string
	 */
	var $query_orderby;

	/**
	 * {@internal Missing Description}}
	 *
	 * @since 3.0.0
	 * @access private
	 * @var string
	 */
	var $query_from;

	/**
	 * {@internal Missing Description}}
	 *
	 * @since 3.0.0
	 * @access private
	 * @var string
	 */
	var $query_where;

	/**
	 * {@internal Missing Description}}
	 *
	 * @since 2.1.0
	 * @access private
	 * @var int
	 */
	var $total_users_for_query = 0;

	/**
	 * {@internal Missing Description}}
	 *
	 * @since 2.1.0
	 * @access private
	 * @var bool
	 */
	var $too_many_total_users = false;

	/**
	 * {@internal Missing Description}}
	 *
	 * @since 2.1.0
	 * @access private
	 * @var WP_Error
	 */
	var $search_errors;

	/**
	 * {@internal Missing Description}}
	 *
	 * @since 2.7.0
	 * @access private
	 * @var string
	 */
	var $paging_text;

	/**
	 * PHP5 Constructor - Sets up the object properties.
	 *
	 * @since 2.1.0
	 *
	 * @param string $search_term Search terms string.
	 * @param int $page Optional. Page ID.
	 * @param string $role Role name.
	 * @return WP_User_Search
	 */
	function __construct( $search_term = '', $page = '', $role = '' ) {
		_deprecated_function( __FUNCTION__, '3.1', 'WP_User_Query' );

		$this->search_term = wp_unslash( $search_term );
		$this->raw_page = ( '' == $page ) ? false : (int) $page;
		$this->page = (int) ( '' == $page ) ? 1 : $page;
		$this->role = $role;

		$this->prepare_query();
		$this->query();
		$this->do_paging();
	}

	/**
	 * PHP4 Constructor - Sets up the object properties.
	 *
	 * @since 2.1.0
	 *
	 * @param string $search_term Search terms string.
	 * @param int $page Optional. Page ID.
	 * @param string $role Role name.
	 * @return WP_User_Search
	 */
	public function WP_User_Search( $search_term = '', $page = '', $role = '' ) {
		self::__construct( $search_term, $page, $role );
	}

	/**
	 * Prepares the user search query (legacy).
	 *
	 * @since 2.1.0
	 * @access public
	 */
	public function prepare_query() {
		global $wpdb;
		$this->first_user = ($this->page - 1) * $this->users_per_page;

		$this->query_limit = $wpdb->prepare(" LIMIT %d, %d", $this->first_user, $this->users_per_page);
		$this->query_orderby = ' ORDER BY user_login';

		$search_sql = '';
		if ( $this->search_term ) {
			$searches = array();
			$search_sql = 'AND (';
			foreach ( array('user_login', 'user_nicename', 'user_email', 'user_url', 'display_name') as $col )
				$searches[] = $wpdb->prepare( $col . ' LIKE %s', '%' . like_escape($this->search_term) . '%' );
			$search_sql .= implode(' OR ', $searches);
			$search_sql .= ')';
		}

		$this->query_from = " FROM $wpdb->users";
		$this->query_where = " WHERE 1=1 $search_sql";

		if ( $this->role ) {
			$this->query_from .= " INNER JOIN $wpdb->usermeta ON $wpdb->users.ID = $wpdb->usermeta.user_id";
			$this->query_where .= $wpdb->prepare(" AND $wpdb->usermeta.meta_key = '{$wpdb->prefix}capabilities' AND $wpdb->usermeta.meta_value LIKE %s", '%' . $this->role . '%');
		} elseif ( is_multisite() ) {
			$level_key = $wpdb->prefix . 'capabilities'; // wpmu site admins don't have user_levels
			$this->query_from .= ", $wpdb->usermeta";
			$this->query_where .= " AND $wpdb->users.ID = $wpdb->usermeta.user_id AND meta_key = '{$level_key}'";
		}

		do_action_ref_array( 'pre_user_search', array( &$this ) );
	}

	/**
	 * Executes the user search query.
	 *
	 * @since 2.1.0
	 * @access public
	 */
	public function query() {
		global $wpdb;

		$this->results = $wpdb->get_col("SELECT DISTINCT($wpdb->users.ID)" . $this->query_from . $this->query_where . $this->query_orderby . $this->query_limit);

		if ( $this->results )
			$this->total_users_for_query = $wpdb->get_var("SELECT COUNT(DISTINCT($wpdb->users.ID))" . $this->query_from . $this->query_where); // no limit
		else
			$this->search_errors = new WP_Error('no_matching_users_found', __('No users found.'));
	}

	/**
	 * Prepares variables for use in templates.
	 *
	 * @since 2.1.0
	 * @access public
	 */
	function prepare_vars_for_template_usage() {}

	/**
	 * Handles paging for the user search query.
	 *
	 * @since 2.1.0
	 * @access public
	 */
	public function do_paging() {
		if ( $this->total_users_for_query > $this->users_per_page ) { // have to page the results
			$args = array();
			if ( ! empty($this->search_term) )
				$args['usersearch'] = urlencode($this->search_term);
			if ( ! empty($this->role) )
				$args['role'] = urlencode($this->role);

			$this->paging_text = paginate_links( array(
				'total' => ceil($this->total_users_for_query / $this->users_per_page),
				'current' => $this->page,
				'base' => 'users.php?%_%',
				'format' => 'userspage=%#%',
				'add_args' => $args
			) );
			if ( $this->paging_text ) {
				$this->paging_text = sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s' ) . '</span>%s',
					number_format_i18n( ( $this->page - 1 ) * $this->users_per_page + 1 ),
					number_format_i18n( min( $this->page * $this->users_per_page, $this->total_users_for_query ) ),
					number_format_i18n( $this->total_users_for_query ),
					$this->paging_text
				);
			}
		}
	}

	/**
	 * Retrieves the user search query results.
	 *
	 * @since 2.1.0
	 * @access public
	 *
	 * @return array
	 */
	public function get_results() {
		return (array) $this->results;
	}

	/**
	 * Displaying paging text.
	 *
	 * @see do_paging() Builds paging text.
	 *
	 * @since 2.1.0
	 * @access public
	 */
	function page_links() {
		echo $this->paging_text;
	}

	/**
	 * Whether paging is enabled.
	 *
	 * @see do_paging() Builds paging text.
	 *
	 * @since 2.1.0
	 * @access public
	 *
	 * @return bool
	 */
	function results_are_paged() {
		if ( $this->paging_text )
			return true;
		return false;
	}

	/**
	 * Whether there are search terms.
	 *
	 * @since 2.1.0
	 * @access public
	 *
	 * @return bool
	 */
	function is_search() {
		if ( $this->search_term )
			return true;
		return false;
	}
}
endif;

/**
 * Retrieve editable posts from other users.
 *
 * @deprecated 3.1.0 Use get_posts()
 * @see get_posts()
 *
 * @param int $user_id User ID to not retrieve posts from.
 * @param string $type Optional, defaults to 'any'. Post type to retrieve, can be 'draft' or 'pending'.
 * @return array List of posts from others.
 */
function get_others_unpublished_posts($user_id, $type='any') {
	_deprecated_function( __FUNCTION__, '3.1' );

	global $wpdb;

	$editable = get_editable_user_ids( $user_id );

	if ( in_array($type, array('draft', 'pending')) )
		$type_sql = " post_status = '$type' ";
	else
		$type_sql = " ( post_status = 'draft' OR post_status = 'pending' ) ";

	$dir = ( 'pending' == $type ) ? 'ASC' : 'DESC';

	if ( !$editable ) {
		$other_unpubs = '';
	} else {
		$editable = join(',', $editable);
		$other_unpubs = $wpdb->get_results( $wpdb->prepare("SELECT ID, post_title, post_author FROM $wpdb->posts WHERE post_type = 'post' AND $type_sql AND post_author IN ($editable) AND post_author != %d ORDER BY post_modified $dir", $user_id) );
	}

	return apply_filters('get_others_drafts', $other_unpubs);
}

/**
 * Retrieve drafts from other users.
 *
 * @deprecated 3.1.0 Use get_posts()
 * @see get_posts()
 *
 * @param int $user_id User ID.
 * @return array List of drafts from other users.
 */
function get_others_drafts($user_id) {
	_deprecated_function( __FUNCTION__, '3.1' );

	return get_others_unpublished_posts($user_id, 'draft');
}

/**
 * Retrieve pending review posts from other users.
 *
 * @deprecated 3.1.0 Use get_posts()
 * @see get_posts()
 *
 * @param int $user_id User ID.
 * @return array List of posts with pending review post type from other users.
 */
function get_others_pending($user_id) {
	_deprecated_function( __FUNCTION__, '3.1' );

	return get_others_unpublished_posts($user_id, 'pending');
}

/**
 * Output the QuickPress dashboard widget.
 *
 * @since 3.0.0
 * @deprecated 3.2.0 Use wp_dashboard_quick_press()
 * @see wp_dashboard_quick_press()
 */
function wp_dashboard_quick_press_output() {
	_deprecated_function( __FUNCTION__, '3.2', 'wp_dashboard_quick_press()' );
	wp_dashboard_quick_press();
}

/**
 * Outputs the TinyMCE editor.
 *
 * @since 2.7.0
 * @deprecated 3.3.0 Use wp_editor()
 * @see wp_editor()
 *
 * @staticvar int $num
 */
function wp_tiny_mce( $teeny = false, $settings = false ) {
	_deprecated_function( __FUNCTION__, '3.3', 'wp_editor()' );

	static $num = 1;

	if ( ! class_exists( '_WP_Editors', false ) )
		require_once( ABSPATH . WPINC . '/class-wp-editor.php' );

	$editor_id = 'content' . $num++;

	$set = array(
		'teeny' => $teeny,
		'tinymce' => $settings ? $settings : true,
		'quicktags' => false
	);

	$set = _WP_Editors::parse_settings($editor_id, $set);
	_WP_Editors::editor_settings($editor_id, $set);
}

/**
 * Preloads TinyMCE dialogs.
 *
 * @deprecated 3.3.0 Use wp_editor()
 * @see wp_editor()
 */
function wp_preload_dialogs() {
	_deprecated_function( __FUNCTION__, '3.3', 'wp_editor()' );
}

/**
 * Prints TinyMCE editor JS.
 *
 * @deprecated 3.3.0 Use wp_editor()
 * @see wp_editor()
 */
function wp_print_editor_js() {
	_deprecated_function( __FUNCTION__, '3.3', 'wp_editor()' );
}

/**
 * Handles quicktags.
 *
 * @deprecated 3.3.0 Use wp_editor()
 * @see wp_editor()
 */
function wp_quicktags() {
	_deprecated_function( __FUNCTION__, '3.3', 'wp_editor()' );
}

/**
 * Returns the screen layout options.
 *
 * @since 2.8.0
 * @deprecated 3.3.0 WP_Screen::render_screen_layout()
 * @see WP_Screen::render_screen_layout()
 */
function screen_layout( $screen ) {
	_deprecated_function( __FUNCTION__, '3.3', '$current_screen->render_screen_layout()' );

	$current_screen = get_current_screen();

	if ( ! $current_screen )
		return '';

	ob_start();
	$current_screen->render_screen_layout();
	return ob_get_clean();
}

/**
 * Returns the screen's per-page options.
 *
 * @since 2.8.0
 * @deprecated 3.3.0 Use WP_Screen::render_per_page_options()
 * @see WP_Screen::render_per_page_options()
 */
function screen_options( $screen ) {
	_deprecated_function( __FUNCTION__, '3.3', '$current_screen->render_per_page_options()' );

	$current_screen = get_current_screen();

	if ( ! $current_screen )
		return '';

	ob_start();
	$current_screen->render_per_page_options();
	return ob_get_clean();
}

/**
 * Renders the screen's help.
 *
 * @since 2.7.0
 * @deprecated 3.3.0 Use WP_Screen::render_screen_meta()
 * @see WP_Screen::render_screen_meta()
 */
function screen_meta( $screen ) {
	$current_screen = get_current_screen();
	$current_screen->render_screen_meta();
}

/**
 * Favorite actions were deprecated in version 3.2. Use the admin bar instead.
 *
 * @since 2.7.0
 * @deprecated 3.2.0 Use WP_Admin_Bar
 * @see WP_Admin_Bar
 */
function favorite_actions() {
	_deprecated_function( __FUNCTION__, '3.2', 'WP_Admin_Bar' );
}

/**
 * Handles uploading an image.
 *
 * @deprecated 3.3.0 Use wp_media_upload_handler()
 * @see wp_media_upload_handler()
 *
 * @return null|string
 */
function media_upload_image() {
	_deprecated_function( __FUNCTION__, '3.3', 'wp_media_upload_handler()' );
	return wp_media_upload_handler();
}

/**
 * Handles uploading an audio file.
 *
 * @deprecated 3.3.0 Use wp_media_upload_handler()
 * @see wp_media_upload_handler()
 *
 * @return null|string
 */
function media_upload_audio() {
	_deprecated_function( __FUNCTION__, '3.3', 'wp_media_upload_handler()' );
	return wp_media_upload_handler();
}

/**
 * Handles uploading a video file.
 *
 * @deprecated 3.3.0 Use wp_media_upload_handler()
 * @see wp_media_upload_handler()
 *
 * @return null|string
 */
function media_upload_video() {
	_deprecated_function( __FUNCTION__, '3.3', 'wp_media_upload_handler()' );
	return wp_media_upload_handler();
}

/**
 * Handles uploading a generic file.
 *
 * @deprecated 3.3.0 Use wp_media_upload_handler()
 * @see wp_media_upload_handler()
 *
 * @return null|string
 */
function media_upload_file() {
	_deprecated_function( __FUNCTION__, '3.3', 'wp_media_upload_handler()' );
	return wp_media_upload_handler();
}

/**
 * Handles retrieving the insert-from-URL form for an image.
 *
 * @deprecated 3.3.0 Use wp_media_insert_url_form()
 * @see wp_media_insert_url_form()
 *
 * @return string
 */
function type_url_form_image() {
	_deprecated_function( __FUNCTION__, '3.3', "wp_media_insert_url_form('image')" );
	return wp_media_insert_url_form( 'image' );
}

/**
 * Handles retrieving the insert-from-URL form for an audio file.
 *
 * @deprecated 3.3.0 Use wp_media_insert_url_form()
 * @see wp_media_insert_url_form()
 *
 * @return string
 */
function type_url_form_audio() {
	_deprecated_function( __FUNCTION__, '3.3', "wp_media_insert_url_form('audio')" );
	return wp_media_insert_url_form( 'audio' );
}

/**
 * Handles retrieving the insert-from-URL form for a video file.
 *
 * @deprecated 3.3.0 Use wp_media_insert_url_form()
 * @see wp_media_insert_url_form()
 *
 * @return string
 */
function type_url_form_video() {
	_deprecated_function( __FUNCTION__, '3.3', "wp_media_insert_url_form('video')" );
	return wp_media_insert_url_form( 'video' );
}

/**
 * Handles retrieving the insert-from-URL form for a generic file.
 *
 * @deprecated 3.3.0 Use wp_media_insert_url_form()
 * @see wp_media_insert_url_form()
 *
 * @return string
 */
function type_url_form_file() {
	_deprecated_function( __FUNCTION__, '3.3', "wp_media_insert_url_form('file')" );
	return wp_media_insert_url_form( 'file' );
}

/**
 * Add contextual help text for a page.
 *
 * Creates an 'Overview' help tab.
 *
 * @since 2.7.0
 * @deprecated 3.3.0 Use WP_Screen::add_help_tab()
 * @see WP_Screen::add_help_tab()
 *
 * @param string    $screen The handle for the screen to add help to. This is usually the hook name returned by the add_*_page() functions.
 * @param string    $help   The content of an 'Overview' help tab.
 */
function add_contextual_help( $screen, $help ) {
	_deprecated_function( __FUNCTION__, '3.3', 'get_current_screen()->add_help_tab()' );

	if ( is_string( $screen ) )
		$screen = convert_to_screen( $screen );

	WP_Screen::add_old_compat_help( $screen, $help );
}

/**
 * Get the allowed themes for the current blog.
 *
 * @since 3.0.0
 * @deprecated 3.4.0 Use wp_get_themes()
 * @see wp_get_themes()
 *
 * @return array $themes Array of allowed themes.
 */
function get_allowed_themes() {
	_deprecated_function( __FUNCTION__, '3.4', "wp_get_themes( array( 'allowed' => true ) )" );

	$themes = wp_get_themes( array( 'allowed' => true ) );

	$wp_themes = array();
	foreach ( $themes as $theme ) {
		$wp_themes[ $theme->get('Name') ] = $theme;
	}

	return $wp_themes;
}

/**
 * Retrieves a list of broken themes.
 *
 * @since 1.5.0
 * @deprecated 3.4.0 Use wp_get_themes()
 * @see wp_get_themes()
 *
 * @return array
 */
function get_broken_themes() {
	_deprecated_function( __FUNCTION__, '3.4', "wp_get_themes( array( 'errors' => true )" );

	$themes = wp_get_themes( array( 'errors' => true ) );
	$broken = array();
	foreach ( $themes as $theme ) {
		$name = $theme->get('Name');
		$broken[ $name ] = array(
			'Name' => $name,
			'Title' => $name,
			'Description' => $theme->errors()->get_error_message(),
		);
	}
	return $broken;
}

/**
 * Retrieves information on the current active theme.
 *
 * @since 2.0.0
 * @deprecated 3.4.0 Use wp_get_theme()
 * @see wp_get_theme()
 *
 * @return WP_Theme
 */
function current_theme_info() {
	_deprecated_function( __FUNCTION__, '3.4', 'wp_get_theme()' );

	return wp_get_theme();
}

/**
 * This was once used to display an 'Insert into Post' button.
 *
 * Now it is deprecated and stubbed.
 *
 * @deprecated 3.5.0
 */
function _insert_into_post_button( $type ) {
	_deprecated_function( __FUNCTION__, '3.5' );
}

/**
 * This was once used to display a media button.
 *
 * Now it is deprecated and stubbed.
 *
 * @deprecated 3.5.0
 */
function _media_button($title, $icon, $type, $id) {
	_deprecated_function( __FUNCTION__, '3.5' );
}

/**
 * Gets an existing post and format it for editing.
 *
 * @since 2.0.0
 * @deprecated 3.5.0 Use get_post()
 * @see get_post()
 *
 * @param int $id
 * @return object
 */
function get_post_to_edit( $id ) {
	_deprecated_function( __FUNCTION__, '3.5', 'get_post()' );

	return get_post( $id, OBJECT, 'edit' );
}

/**
 * Gets the default page information to use.
 *
 * @since 2.5.0
 * @deprecated 3.5.0 Use get_default_post_to_edit()
 * @see get_default_post_to_edit()
 *
 * @return WP_Post Post object containing all the default post data as attributes
 */
function get_default_page_to_edit() {
	_deprecated_function( __FUNCTION__, '3.5', "get_default_post_to_edit( 'page' )" );

	$page = get_default_post_to_edit();
	$page->post_type = 'page';
	return $page;
}

/**
 * This was once used to create a thumbnail from an Image given a maximum side size.
 *
 * @since 1.2.0
 * @deprecated 3.5.0 Use image_resize()
 * @see image_resize()
 *
 * @param mixed $file Filename of the original image, Or attachment id.
 * @param int $max_side Maximum length of a single side for the thumbnail.
 * @param mixed $deprecated Never used.
 * @return string Thumbnail path on success, Error string on failure.
 */
function wp_create_thumbnail( $file, $max_side, $deprecated = '' ) {
	_deprecated_function( __FUNCTION__, '3.5', 'image_resize()' );
	return apply_filters( 'wp_create_thumbnail', image_resize( $file, $max_side, $max_side ) );
}

/**
 * This was once used to display a metabox for the nav menu theme locations.
 *
 * Deprecated in favor of a 'Manage Locations' tab added to nav menus management screen.
 *
 * @since 3.0.0
 * @deprecated 3.6.0
 */
function wp_nav_menu_locations_meta_box() {
	_deprecated_function( __FUNCTION__, '3.6' );
}

/**
 * This was once used to kick-off the Core Updater.
 *
 * Deprecated in favor of instantating a Core_Upgrader instance directly,
 * and calling the 'upgrade' method.
 *
 * @since 2.7.0
 * @deprecated 3.7.0 Use Core_Upgrader
 * @see Core_Upgrader
 */
function wp_update_core($current, $feedback = '') {
	_deprecated_function( __FUNCTION__, '3.7', 'new Core_Upgrader();' );

	if ( !empty($feedback) )
		add_filter('update_feedback', $feedback);

	include( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );
	$upgrader = new Core_Upgrader();
	return $upgrader->upgrade($current);

}

/**
 * This was once used to kick-off the Plugin Updater.
 *
 * Deprecated in favor of instantating a Plugin_Upgrader instance directly,
 * and calling the 'upgrade' method.
 * Unused since 2.8.0.
 *
 * @since 2.5.0
 * @deprecated 3.7.0 Use Plugin_Upgrader
 * @see Plugin_Upgrader
 */
function wp_update_plugin($plugin, $feedback = '') {
	_deprecated_function( __FUNCTION__, '3.7', 'new Plugin_Upgrader();' );

	if ( !empty($feedback) )
		add_filter('update_feedback', $feedback);

	include( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );
	$upgrader = new Plugin_Upgrader();
	return $upgrader->upgrade($plugin);
}

/**
 * This was once used to kick-off the Theme Updater.
 *
 * Deprecated in favor of instantating a Theme_Upgrader instance directly,
 * and calling the 'upgrade' method.
 * Unused since 2.8.0.
 *
 * @since 2.7.0
 * @deprecated 3.7.0 Use Theme_Upgrader
 * @see Theme_Upgrader
 */
function wp_update_theme($theme, $feedback = '') {
	_deprecated_function( __FUNCTION__, '3.7', 'new Theme_Upgrader();' );

	if ( !empty($feedback) )
		add_filter('update_feedback', $feedback);

	include( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );
	$upgrader = new Theme_Upgrader();
	return $upgrader->upgrade($theme);
}

/**
 * This was once used to display attachment links. Now it is deprecated and stubbed.
 *
 * @since 2.0.0
 * @deprecated 3.7.0
 *
 * @param int|bool $id
 */
function the_attachment_links( $id = false ) {
	_deprecated_function( __FUNCTION__, '3.7' );
}

/**
 * Displays a screen icon.
 *
 * @since 2.7.0
 * @since 3.8.0 Screen icons are no longer used in WordPress. This function no longer produces output.
 * @deprecated 3.8.0 Use get_screen_icon()
 * @see get_screen_icon()
 */
function screen_icon() {
	echo get_screen_icon();
}

/**
 * Retrieves the screen icon (no longer used in 3.8+).
 *
 * @deprecated 3.8.0
 *
 * @return string
 */
function get_screen_icon() {
	return '<!-- Screen icons are no longer used as of WordPress 3.8. -->';
}

/**
 * Deprecated dashboard widget controls.
 *
 * @since 2.5.0
 * @deprecated 3.8.0
 */
function wp_dashboard_incoming_links_output() {}

/**
 * Deprecated dashboard secondary output.
 *
 * @deprecated 3.8.0
 */
function wp_dashboard_secondary_output() {}

/**
 * Deprecated dashboard widget controls.
 *
 * @since 2.7.0
 * @deprecated 3.8.0
 */
function wp_dashboard_incoming_links() {}

/**
 * Deprecated dashboard incoming links control.
 *
 * @deprecated 3.8.0
 */
function wp_dashboard_incoming_links_control() {}

/**
 * Deprecated dashboard plugins control.
 *
 * @deprecated 3.8.0
 */
function wp_dashboard_plugins() {}

/**
 * Deprecated dashboard primary control.
 *
 * @deprecated 3.8.0
 */
function wp_dashboard_primary_control() {}

/**
 * Deprecated dashboard recent comments control.
 *
 * @deprecated 3.8.0
 */
function wp_dashboard_recent_comments_control() {}

/**
 * Deprecated dashboard secondary section.
 *
 * @deprecated 3.8.0
 */
function wp_dashboard_secondary() {}

/**
 * Deprecated dashboard secondary control.
 *
 * @deprecated 3.8.0
 */
function wp_dashboard_secondary_control() {}

/**
 * This was once used to move child posts to a new parent.
 *
 * @since 2.3.0
 * @deprecated 3.9.0
 * @access private
 *
 * @param int $old_ID
 * @param int $new_ID
 */
function _relocate_children( $old_ID, $new_ID ) {
	_deprecated_function( __FUNCTION__, '3.9' );
}
