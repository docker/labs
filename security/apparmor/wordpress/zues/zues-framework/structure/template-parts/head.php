<?php
/**
 * Standard WordPress <head>.
 *
 * @package zues
 */
 ?>

<!DOCTYPE html>
<html <?php echo get_language_attributes(); ?>>
<head>
<meta charset="<?php echo get_bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php echo get_bloginfo( 'pingback_url' ) ?>">

<?php

/**
 * Fires before wp_head
 */
 do_action( 'zues_head' );

 wp_head();

 ?>

</head>
