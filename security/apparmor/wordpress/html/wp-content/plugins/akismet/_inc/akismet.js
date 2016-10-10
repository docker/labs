jQuery( function ( $ ) {
	var mshotRemovalTimer = null;
	var mshotSecondTryTimer = null
	var mshotThirdTryTimer = null
	
	$( 'a.activate-option' ).click( function(){
		var link = $( this );
		if ( link.hasClass( 'clicked' ) ) {
			link.removeClass( 'clicked' );
		}
		else {
			link.addClass( 'clicked' );
		}
		$( '.toggle-have-key' ).slideToggle( 'slow', function() {});
		return false;
	});
	$('.akismet-status').each(function () {
		var thisId = $(this).attr('commentid');
		$(this).prependTo('#comment-' + thisId + ' .column-comment');
	});
	$('.akismet-user-comment-count').each(function () {
		var thisId = $(this).attr('commentid');
		$(this).insertAfter('#comment-' + thisId + ' .author strong:first').show();
	});
	$('#the-comment-list')
		.find('tr.comment, tr[id ^= "comment-"]')
		.find('.column-author a[href^="http"]:first') // Ignore mailto: links, which would be the comment author's email.
		.each(function () {
		var linkHref = $(this).attr( 'href' );
		
		// Ignore any links to the current domain, which are diagnostic tools, like the IP address link
		// or any other links another plugin might add.
		var currentHostParts = document.location.href.split( '/' );
		var currentHost = currentHostParts[0] + '//' + currentHostParts[2] + '/';
		
		if ( linkHref.indexOf( currentHost ) != 0 ) {
			var thisCommentId = $(this).parents('tr:first').attr('id').split("-");

			$(this)
				.attr("id", "author_comment_url_"+ thisCommentId[1])
				.after(
					$( '<a href="#" class="remove_url">x</a>' )
						.attr( 'commentid', thisCommentId[1] )
						.attr( 'title', WPAkismet.strings['Remove this URL'] )
				);
		}
	});
	$('.remove_url').live('click', function () {
		var thisId = $(this).attr('commentid');
		var data = {
			action: 'comment_author_deurl',
			_wpnonce: WPAkismet.comment_author_url_nonce,
			id: thisId
		};
		$.ajax({
			url: ajaxurl,
			type: 'POST',
			data: data,
			beforeSend: function () {
				// Removes "x" link
				$("a[commentid='"+ thisId +"']").hide();
				// Show temp status
				$("#author_comment_url_"+ thisId).html( $( '<span/>' ).text( WPAkismet.strings['Removing...'] ) );
			},
			success: function (response) {
				if (response) {
					// Show status/undo link
					$("#author_comment_url_"+ thisId)
						.attr('cid', thisId)
						.addClass('akismet_undo_link_removal')
						.html(
							$( '<span/>' ).text( WPAkismet.strings['URL removed'] )
						)
						.append( ' ' )
						.append(
							$( '<span/>' )
								.text( WPAkismet.strings['(undo)'] )
								.addClass( 'akismet-span-link' )
						);
				}
			}
		});

		return false;
	});
	$('.akismet_undo_link_removal').live('click', function () {
		var thisId = $(this).attr('cid');
		var thisUrl = $(this).attr('href');
		var data = {
			action: 'comment_author_reurl',
			_wpnonce: WPAkismet.comment_author_url_nonce,
			id: thisId,
			url: thisUrl
		};
		$.ajax({
			url: ajaxurl,
			type: 'POST',
			data: data,
			beforeSend: function () {
				// Show temp status
				$("#author_comment_url_"+ thisId).html( $( '<span/>' ).text( WPAkismet.strings['Re-adding...'] ) );
			},
			success: function (response) {
				if (response) {
					// Add "x" link
					$("a[commentid='"+ thisId +"']").show();
					// Show link. Core strips leading http://, so let's do that too.
					$("#author_comment_url_"+ thisId).removeClass('akismet_undo_link_removal').text( thisUrl.replace( /^http:\/\/(www\.)?/ig, '' ) );
				}
			}
		});

		return false;
	});

	// Show a preview image of the hovered URL. Applies to author URLs and URLs inside the comments.
	$( 'a[id^="author_comment_url"], tr.pingback td.column-author a:first-of-type, table.comments td.comment p a' ).mouseover( function () {
		clearTimeout( mshotRemovalTimer );

		if ( $( '.akismet-mshot' ).length > 0 ) {
			if ( $( '.akismet-mshot:first' ).data( 'link' ) == this ) {
				// The preview is already showing for this link.
				return;
			}
			else {
				// A new link is being hovered, so remove the old preview.
				$( '.akismet-mshot' ).remove();
			}
		}

		clearTimeout( mshotSecondTryTimer );
		clearTimeout( mshotThirdTryTimer );

		var thisHref = $.URLEncode( $( this ).attr( 'href' ) );

		var mShot = $( '<div class="akismet-mshot mshot-container"><div class="mshot-arrow"></div><img src="//s0.wordpress.com/mshots/v1/' + thisHref + '?w=450" width="450" height="338" class="mshot-image" /></div>' );
		mShot.data( 'link', this );

		var offset = $( this ).offset();

		mShot.offset( {
			left : Math.min( $( window ).width() - 475, offset.left + $( this ).width() + 10 ), // Keep it on the screen if the link is near the edge of the window.
			top: offset.top + ( $( this ).height() / 2 ) - 101 // 101 = top offset of the arrow plus the top border thickness
		} );

		mshotSecondTryTimer = setTimeout( function () {
			mShot.find( '.mshot-image' ).attr( 'src', '//s0.wordpress.com/mshots/v1/'+thisHref+'?w=450&r=2' );
		}, 6000 );

		mshotThirdTryTimer = setTimeout( function () {
			mShot.find( '.mshot-image' ).attr( 'src', '//s0.wordpress.com/mshots/v1/'+thisHref+'?w=450&r=3' );
		}, 12000 );

		$( 'body' ).append( mShot );
	} ).mouseout( function () {
		mshotRemovalTimer = setTimeout( function () {
			clearTimeout( mshotSecondTryTimer );
			clearTimeout( mshotThirdTryTimer );

			$( '.akismet-mshot' ).remove();
		}, 200 );
	} );

	$('.checkforspam:not(.button-disabled)').click( function(e) {
		$('.checkforspam:not(.button-disabled)').addClass('button-disabled');
		$('.checkforspam-spinner').addClass( 'spinner' );
		akismet_check_for_spam(0, 100);
		e.preventDefault();
	});

	function akismet_check_for_spam(offset, limit) {
		$.post(
			ajaxurl,
			{
				'action': 'akismet_recheck_queue',
				'offset': offset,
				'limit': limit
			},
			function(result) {
				if (result.processed < limit) {
					window.location.reload();
				}
				else {
					akismet_check_for_spam(offset + limit, limit);
				}
			}
		);
	}
});
// URL encode plugin
jQuery.extend({URLEncode:function(c){var o='';var x=0;c=c.toString();var r=/(^[a-zA-Z0-9_.]*)/;
  while(x<c.length){var m=r.exec(c.substr(x));
    if(m!=null && m.length>1 && m[1]!=''){o+=m[1];x+=m[1].length;
    }else{if(c[x]==' ')o+='+';else{var d=c.charCodeAt(x);var h=d.toString(16);
    o+='%'+(h.length<2?'0':'')+h.toUpperCase();}x++;}}return o;}
});
