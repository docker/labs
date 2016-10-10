/**
 * Javascript code should be placed here, then enqueued using wp_enqueue_script();
 *
 * @package zues
 */
//
// jQuery(function(){
// 	jQuery( '.primary-navigation .menu' ).slicknav({
// 		prependTo:'.site-navigation-outer'
// 	});
// });

jQuery(document).ready(function() {

	jQuery('.menu-primary .menu').superfish({
		delay:       200,                            // one second delay on mouseout
		cssArrows:  false                            // disable generation of arrow mark-up
	});

	jQuery('.menu-primary ul.menu, .menu-primary .menu ul').tinyNav({
		active: 'current-menu-item',
		header: '- Navigation -', // String: Specify text for "header" and show header instead of the active item
	});

});
