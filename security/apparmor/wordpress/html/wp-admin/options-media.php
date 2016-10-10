<?php
/**
 * Media settings administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once( dirname( __FILE__ ) . '/admin.php' );

if ( ! current_user_can( 'manage_options' ) )
	wp_die( __( 'You do not have sufficient permissions to manage options for this site.' ) );

$title = __('Media Settings');
$parent_file = 'options-general.php';

$media_options_help = '<p>' . __('You can set maximum sizes for images inserted into your written content; you can also insert an image as Full Size.') . '</p>';

if ( ! is_multisite() && ( get_option('upload_url_path') || ( get_option('upload_path') != 'wp-content/uploads' && get_option('upload_path') ) ) ) {
	$media_options_help .= '<p>' . __('Uploading Files allows you to choose the folder and path for storing your uploaded files.') . '</p>';
}

$media_options_help .= '<p>' . __('You must click the Save Changes button at the bottom of the screen for new settings to take effect.') . '</p>';

get_current_screen()->add_help_tab( array(
	'id'      => 'overview',
	'title'   => __('Overview'),
	'content' => $media_options_help,
) );

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __('For more information:') . '</strong></p>' .
	'<p>' . __('<a href="https://codex.wordpress.org/Settings_Media_Screen" target="_blank">Documentation on Media Settings</a>') . '</p>' .
	'<p>' . __('<a href="https://wordpress.org/support/" target="_blank">Support Forums</a>') . '</p>'
);

include( ABSPATH . 'wp-admin/admin-header.php' );

?>

<div class="wrap">
<h1><?php echo esc_html( $title ); ?></h1>

<form action="options.php" method="post">
<?php settings_fields('media'); ?>

<h2 class="title"><?php _e('Image sizes') ?></h2>
<p><?php _e( 'The sizes listed below determine the maximum dimensions in pixels to use when adding an image to the Media Library.' ); ?></p>

<table class="form-table">
<tr>
<th scope="row"><?php _e('Thumbnail size') ?></th>
<td>
<label for="thumbnail_size_w"><?php _e('Width'); ?></label>
<input name="thumbnail_size_w" type="number" step="1" min="0" id="thumbnail_size_w" value="<?php form_option('thumbnail_size_w'); ?>" class="small-text" />
<label for="thumbnail_size_h"><?php _e('Height'); ?></label>
<input name="thumbnail_size_h" type="number" step="1" min="0" id="thumbnail_size_h" value="<?php form_option('thumbnail_size_h'); ?>" class="small-text" /><br />
<input name="thumbnail_crop" type="checkbox" id="thumbnail_crop" value="1" <?php checked('1', get_option('thumbnail_crop')); ?>/>
<label for="thumbnail_crop"><?php _e('Crop thumbnail to exact dimensions (normally thumbnails are proportional)'); ?></label>
</td>
</tr>

<tr>
<th scope="row"><?php _e('Medium size') ?></th>
<td><fieldset><legend class="screen-reader-text"><span><?php _e('Medium size'); ?></span></legend>
<label for="medium_size_w"><?php _e('Max Width'); ?></label>
<input name="medium_size_w" type="number" step="1" min="0" id="medium_size_w" value="<?php form_option('medium_size_w'); ?>" class="small-text" />
<label for="medium_size_h"><?php _e('Max Height'); ?></label>
<input name="medium_size_h" type="number" step="1" min="0" id="medium_size_h" value="<?php form_option('medium_size_h'); ?>" class="small-text" />
</fieldset></td>
</tr>

<tr>
<th scope="row"><?php _e('Large size') ?></th>
<td><fieldset><legend class="screen-reader-text"><span><?php _e('Large size'); ?></span></legend>
<label for="large_size_w"><?php _e('Max Width'); ?></label>
<input name="large_size_w" type="number" step="1" min="0" id="large_size_w" value="<?php form_option('large_size_w'); ?>" class="small-text" />
<label for="large_size_h"><?php _e('Max Height'); ?></label>
<input name="large_size_h" type="number" step="1" min="0" id="large_size_h" value="<?php form_option('large_size_h'); ?>" class="small-text" />
</fieldset></td>
</tr>

<?php do_settings_fields('media', 'default'); ?>
</table>

<?php
/**
 * @global array $wp_settings
 */
if ( isset( $GLOBALS['wp_settings']['media']['embeds'] ) ) : ?>
<h2 class="title"><?php _e('Embeds') ?></h2>
<table class="form-table">
<?php do_settings_fields( 'media', 'embeds' ); ?>
</table>
<?php endif; ?>

<?php if ( !is_multisite() ) : ?>
<h2 class="title"><?php _e('Uploading Files'); ?></h2>
<table class="form-table">
<?php
// If upload_url_path is not the default (empty), and upload_path is not the default ('wp-content/uploads' or empty)
if ( get_option('upload_url_path') || ( get_option('upload_path') != 'wp-content/uploads' && get_option('upload_path') ) ) :
?>
<tr>
<th scope="row"><label for="upload_path"><?php _e('Store uploads in this folder'); ?></label></th>
<td><input name="upload_path" type="text" id="upload_path" value="<?php echo esc_attr(get_option('upload_path')); ?>" class="regular-text code" />
<p class="description"><?php
	/* translators: %s: wp-content/uploads */
	printf( __( 'Default is %s' ), '<code>wp-content/uploads</code>' );
?></p>
</td>
</tr>

<tr>
<th scope="row"><label for="upload_url_path"><?php _e('Full URL path to files'); ?></label></th>
<td><input name="upload_url_path" type="text" id="upload_url_path" value="<?php echo esc_attr( get_option('upload_url_path')); ?>" class="regular-text code" />
<p class="description"><?php _e('Configuring this is optional. By default, it should be blank.'); ?></p>
</td>
</tr>
<?php endif; ?>
<tr>
<th scope="row" colspan="2" class="th-full">
<label for="uploads_use_yearmonth_folders">
<input name="uploads_use_yearmonth_folders" type="checkbox" id="uploads_use_yearmonth_folders" value="1"<?php checked('1', get_option('uploads_use_yearmonth_folders')); ?> />
<?php _e('Organize my uploads into month- and year-based folders'); ?>
</label>
</th>
</tr>

<?php do_settings_fields('media', 'uploads'); ?>
</table>
<?php endif; ?>

<?php do_settings_sections('media'); ?>

<?php submit_button(); ?>

</form>

</div>

<?php include( ABSPATH . 'wp-admin/admin-footer.php' ); ?>
