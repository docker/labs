<?php
/**
 * kses 0.2.2 - HTML/XHTML filter that only allows some elements and attributes
 * Copyright (C) 2002, 2003, 2005  Ulf Harnhammar
 *
 * This program is free software and open source software; you can redistribute
 * it and/or modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of the License,
 * or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin St, Fifth Floor, Boston, MA 02110-1301, USA
 * http://www.gnu.org/licenses/gpl.html
 *
 * [kses strips evil scripts!]
 *
 * Added wp_ prefix to avoid conflicts with existing kses users
 *
 * @version 0.2.2
 * @copyright (C) 2002, 2003, 2005
 * @author Ulf Harnhammar <http://advogato.org/person/metaur/>
 *
 * @package External
 * @subpackage KSES
 *
 */

/**
 * You can override this in a plugin.
 *
 * The wp_kses_allowed_html filter is more powerful and supplies context.
 * CUSTOM_TAGS is not recommended and should be considered deprecated.
 *
 * @see wp_kses_allowed_html()
 *
 * @since 1.2.0
 */
if ( ! defined( 'CUSTOM_TAGS' ) )
	define( 'CUSTOM_TAGS', false );

// Ensure that these variables are added to the global namespace
// (e.g. if using namespaces / autoload in the current PHP environment).
global $allowedposttags, $allowedtags, $allowedentitynames;

if ( ! CUSTOM_TAGS ) {
	/**
	 * Kses global for default allowable HTML tags.
	 *
	 * Can be override by using CUSTOM_TAGS constant.
	 *
	 * @global array $allowedposttags
	 * @since 2.0.0
	 */
	$allowedposttags = array(
		'address' => array(),
		'a' => array(
			'href' => true,
			'rel' => true,
			'rev' => true,
			'name' => true,
			'target' => true,
		),
		'abbr' => array(),
		'acronym' => array(),
		'area' => array(
			'alt' => true,
			'coords' => true,
			'href' => true,
			'nohref' => true,
			'shape' => true,
			'target' => true,
		),
		'article' => array(
			'align' => true,
			'dir' => true,
			'lang' => true,
			'xml:lang' => true,
		),
		'aside' => array(
			'align' => true,
			'dir' => true,
			'lang' => true,
			'xml:lang' => true,
		),
		'audio' => array(
			'autoplay' => true,
			'controls' => true,
			'loop' => true,
			'muted' => true,
			'preload' => true,
			'src' => true,
		),
		'b' => array(),
		'bdo' => array(
			'dir' => true,
		),
		'big' => array(),
		'blockquote' => array(
			'cite' => true,
			'lang' => true,
			'xml:lang' => true,
		),
		'br' => array(),
		'button' => array(
			'disabled' => true,
			'name' => true,
			'type' => true,
			'value' => true,
		),
		'caption' => array(
			'align' => true,
		),
		'cite' => array(
			'dir' => true,
			'lang' => true,
		),
		'code' => array(),
		'col' => array(
			'align' => true,
			'char' => true,
			'charoff' => true,
			'span' => true,
			'dir' => true,
			'valign' => true,
			'width' => true,
		),
		'colgroup' => array(
			'align' => true,
			'char' => true,
			'charoff' => true,
			'span' => true,
			'valign' => true,
			'width' => true,
		),
		'del' => array(
			'datetime' => true,
		),
		'dd' => array(),
		'dfn' => array(),
		'details' => array(
			'align' => true,
			'dir' => true,
			'lang' => true,
			'open' => true,
			'xml:lang' => true,
		),
		'div' => array(
			'align' => true,
			'dir' => true,
			'lang' => true,
			'xml:lang' => true,
		),
		'dl' => array(),
		'dt' => array(),
		'em' => array(),
		'fieldset' => array(),
		'figure' => array(
			'align' => true,
			'dir' => true,
			'lang' => true,
			'xml:lang' => true,
		),
		'figcaption' => array(
			'align' => true,
			'dir' => true,
			'lang' => true,
			'xml:lang' => true,
		),
		'font' => array(
			'color' => true,
			'face' => true,
			'size' => true,
		),
		'footer' => array(
			'align' => true,
			'dir' => true,
			'lang' => true,
			'xml:lang' => true,
		),
		'form' => array(
			'action' => true,
			'accept' => true,
			'accept-charset' => true,
			'enctype' => true,
			'method' => true,
			'name' => true,
			'target' => true,
		),
		'h1' => array(
			'align' => true,
		),
		'h2' => array(
			'align' => true,
		),
		'h3' => array(
			'align' => true,
		),
		'h4' => array(
			'align' => true,
		),
		'h5' => array(
			'align' => true,
		),
		'h6' => array(
			'align' => true,
		),
		'header' => array(
			'align' => true,
			'dir' => true,
			'lang' => true,
			'xml:lang' => true,
		),
		'hgroup' => array(
			'align' => true,
			'dir' => true,
			'lang' => true,
			'xml:lang' => true,
		),
		'hr' => array(
			'align' => true,
			'noshade' => true,
			'size' => true,
			'width' => true,
		),
		'i' => array(),
		'img' => array(
			'alt' => true,
			'align' => true,
			'border' => true,
			'height' => true,
			'hspace' => true,
			'longdesc' => true,
			'vspace' => true,
			'src' => true,
			'usemap' => true,
			'width' => true,
		),
		'ins' => array(
			'datetime' => true,
			'cite' => true,
		),
		'kbd' => array(),
		'label' => array(
			'for' => true,
		),
		'legend' => array(
			'align' => true,
		),
		'li' => array(
			'align' => true,
			'value' => true,
		),
		'map' => array(
			'name' => true,
		),
		'mark' => array(),
		'menu' => array(
			'type' => true,
		),
		'nav' => array(
			'align' => true,
			'dir' => true,
			'lang' => true,
			'xml:lang' => true,
		),
		'p' => array(
			'align' => true,
			'dir' => true,
			'lang' => true,
			'xml:lang' => true,
		),
		'pre' => array(
			'width' => true,
		),
		'q' => array(
			'cite' => true,
		),
		's' => array(),
		'samp' => array(),
		'span' => array(
			'dir' => true,
			'align' => true,
			'lang' => true,
			'xml:lang' => true,
		),
		'section' => array(
			'align' => true,
			'dir' => true,
			'lang' => true,
			'xml:lang' => true,
		),
		'small' => array(),
		'strike' => array(),
		'strong' => array(),
		'sub' => array(),
		'summary' => array(
			'align' => true,
			'dir' => true,
			'lang' => true,
			'xml:lang' => true,
		),
		'sup' => array(),
		'table' => array(
			'align' => true,
			'bgcolor' => true,
			'border' => true,
			'cellpadding' => true,
			'cellspacing' => true,
			'dir' => true,
			'rules' => true,
			'summary' => true,
			'width' => true,
		),
		'tbody' => array(
			'align' => true,
			'char' => true,
			'charoff' => true,
			'valign' => true,
		),
		'td' => array(
			'abbr' => true,
			'align' => true,
			'axis' => true,
			'bgcolor' => true,
			'char' => true,
			'charoff' => true,
			'colspan' => true,
			'dir' => true,
			'headers' => true,
			'height' => true,
			'nowrap' => true,
			'rowspan' => true,
			'scope' => true,
			'valign' => true,
			'width' => true,
		),
		'textarea' => array(
			'cols' => true,
			'rows' => true,
			'disabled' => true,
			'name' => true,
			'readonly' => true,
		),
		'tfoot' => array(
			'align' => true,
			'char' => true,
			'charoff' => true,
			'valign' => true,
		),
		'th' => array(
			'abbr' => true,
			'align' => true,
			'axis' => true,
			'bgcolor' => true,
			'char' => true,
			'charoff' => true,
			'colspan' => true,
			'headers' => true,
			'height' => true,
			'nowrap' => true,
			'rowspan' => true,
			'scope' => true,
			'valign' => true,
			'width' => true,
		),
		'thead' => array(
			'align' => true,
			'char' => true,
			'charoff' => true,
			'valign' => true,
		),
		'title' => array(),
		'tr' => array(
			'align' => true,
			'bgcolor' => true,
			'char' => true,
			'charoff' => true,
			'valign' => true,
		),
		'track' => array(
			'default' => true,
			'kind' => true,
			'label' => true,
			'src' => true,
			'srclang' => true,
		),
		'tt' => array(),
		'u' => array(),
		'ul' => array(
			'type' => true,
		),
		'ol' => array(
			'start' => true,
			'type' => true,
		),
		'var' => array(),
		'video' => array(
			'autoplay' => true,
			'controls' => true,
			'height' => true,
			'loop' => true,
			'muted' => true,
			'poster' => true,
			'preload' => true,
			'src' => true,
			'width' => true,
		),
	);

	/**
	 * Kses allowed HTML elements.
	 *
	 * @global array $allowedtags
	 * @since 1.0.0
	 */
	$allowedtags = array(
		'a' => array(
			'href' => true,
			'title' => true,
		),
		'abbr' => array(
			'title' => true,
		),
		'acronym' => array(
			'title' => true,
		),
		'b' => array(),
		'blockquote' => array(
			'cite' => true,
		),
		'cite' => array(),
		'code' => array(),
		'del' => array(
			'datetime' => true,
		),
		'em' => array(),
		'i' => array(),
		'q' => array(
			'cite' => true,
		),
		's' => array(),
		'strike' => array(),
		'strong' => array(),
	);

	$allowedentitynames = array(
		'nbsp',    'iexcl',  'cent',    'pound',  'curren', 'yen',
		'brvbar',  'sect',   'uml',     'copy',   'ordf',   'laquo',
		'not',     'shy',    'reg',     'macr',   'deg',    'plusmn',
		'acute',   'micro',  'para',    'middot', 'cedil',  'ordm',
		'raquo',   'iquest', 'Agrave',  'Aacute', 'Acirc',  'Atilde',
		'Auml',    'Aring',  'AElig',   'Ccedil', 'Egrave', 'Eacute',
		'Ecirc',   'Euml',   'Igrave',  'Iacute', 'Icirc',  'Iuml',
		'ETH',     'Ntilde', 'Ograve',  'Oacute', 'Ocirc',  'Otilde',
		'Ouml',    'times',  'Oslash',  'Ugrave', 'Uacute', 'Ucirc',
		'Uuml',    'Yacute', 'THORN',   'szlig',  'agrave', 'aacute',
		'acirc',   'atilde', 'auml',    'aring',  'aelig',  'ccedil',
		'egrave',  'eacute', 'ecirc',   'euml',   'igrave', 'iacute',
		'icirc',   'iuml',   'eth',     'ntilde', 'ograve', 'oacute',
		'ocirc',   'otilde', 'ouml',    'divide', 'oslash', 'ugrave',
		'uacute',  'ucirc',  'uuml',    'yacute', 'thorn',  'yuml',
		'quot',    'amp',    'lt',      'gt',     'apos',   'OElig',
		'oelig',   'Scaron', 'scaron',  'Yuml',   'circ',   'tilde',
		'ensp',    'emsp',   'thinsp',  'zwnj',   'zwj',    'lrm',
		'rlm',     'ndash',  'mdash',   'lsquo',  'rsquo',  'sbquo',
		'ldquo',   'rdquo',  'bdquo',   'dagger', 'Dagger', 'permil',
		'lsaquo',  'rsaquo', 'euro',    'fnof',   'Alpha',  'Beta',
		'Gamma',   'Delta',  'Epsilon', 'Zeta',   'Eta',    'Theta',
		'Iota',    'Kappa',  'Lambda',  'Mu',     'Nu',     'Xi',
		'Omicron', 'Pi',     'Rho',     'Sigma',  'Tau',    'Upsilon',
		'Phi',     'Chi',    'Psi',     'Omega',  'alpha',  'beta',
		'gamma',   'delta',  'epsilon', 'zeta',   'eta',    'theta',
		'iota',    'kappa',  'lambda',  'mu',     'nu',     'xi',
		'omicron', 'pi',     'rho',     'sigmaf', 'sigma',  'tau',
		'upsilon', 'phi',    'chi',     'psi',    'omega',  'thetasym',
		'upsih',   'piv',    'bull',    'hellip', 'prime',  'Prime',
		'oline',   'frasl',  'weierp',  'image',  'real',   'trade',
		'alefsym', 'larr',   'uarr',    'rarr',   'darr',   'harr',
		'crarr',   'lArr',   'uArr',    'rArr',   'dArr',   'hArr',
		'forall',  'part',   'exist',   'empty',  'nabla',  'isin',
		'notin',   'ni',     'prod',    'sum',    'minus',  'lowast',
		'radic',   'prop',   'infin',   'ang',    'and',    'or',
		'cap',     'cup',    'int',     'sim',    'cong',   'asymp',
		'ne',      'equiv',  'le',      'ge',     'sub',    'sup',
		'nsub',    'sube',   'supe',    'oplus',  'otimes', 'perp',
		'sdot',    'lceil',  'rceil',   'lfloor', 'rfloor', 'lang',
		'rang',    'loz',    'spades',  'clubs',  'hearts', 'diams',
		'sup1',    'sup2',   'sup3',    'frac14', 'frac12', 'frac34',
		'there4',
	);

	$allowedposttags = array_map( '_wp_add_global_attributes', $allowedposttags );
} else {
	$allowedtags = wp_kses_array_lc( $allowedtags );
	$allowedposttags = wp_kses_array_lc( $allowedposttags );
}

/**
 * Filters content and keeps only allowable HTML elements.
 *
 * This function makes sure that only the allowed HTML element names, attribute
 * names and attribute values plus only sane HTML entities will occur in
 * $string. You have to remove any slashes from PHP's magic quotes before you
 * call this function.
 *
 * The default allowed protocols are 'http', 'https', 'ftp', 'mailto', 'news',
 * 'irc', 'gopher', 'nntp', 'feed', 'telnet, 'mms', 'rtsp' and 'svn'. This
 * covers all common link protocols, except for 'javascript' which should not
 * be allowed for untrusted users.
 *
 * @since 1.0.0
 *
 * @param string $string            Content to filter through kses
 * @param array  $allowed_html      List of allowed HTML elements
 * @param array  $allowed_protocols Optional. Allowed protocol in links.
 * @return string Filtered content with only allowed HTML elements
 */
function wp_kses( $string, $allowed_html, $allowed_protocols = array() ) {
	if ( empty( $allowed_protocols ) )
		$allowed_protocols = wp_allowed_protocols();
	$string = wp_kses_no_null( $string, array( 'slash_zero' => 'keep' ) );
	$string = wp_kses_js_entities($string);
	$string = wp_kses_normalize_entities($string);
	$string = wp_kses_hook($string, $allowed_html, $allowed_protocols); // WP changed the order of these funcs and added args to wp_kses_hook
	return wp_kses_split($string, $allowed_html, $allowed_protocols);
}

/**
 * Filters one attribute only and ensures its value is allowed.
 *
 * This function has the advantage of being more secure than esc_attr() and can
 * escape data in some situations where wp_kses() must strip the whole attribute.
 *
 * @since 4.2.3
 *
 * @param string $string The 'whole' attribute, including name and value.
 * @param string $element The element name to which the attribute belongs.
 * @return string Filtered attribute.
 */
function wp_kses_one_attr( $string, $element ) {
	$uris = array('xmlns', 'profile', 'href', 'src', 'cite', 'classid', 'codebase', 'data', 'usemap', 'longdesc', 'action');
	$allowed_html = wp_kses_allowed_html( 'post' );
	$allowed_protocols = wp_allowed_protocols();
	$string = wp_kses_no_null( $string, array( 'slash_zero' => 'keep' ) );
	$string = wp_kses_js_entities( $string );
	
	// Preserve leading and trailing whitespace.
	$matches = array();
	preg_match('/^\s*/', $string, $matches);
	$lead = $matches[0];
	preg_match('/\s*$/', $string, $matches);
	$trail = $matches[0];
	if ( empty( $trail ) ) {
		$string = substr( $string, strlen( $lead ) );
	} else {
		$string = substr( $string, strlen( $lead ), -strlen( $trail ) );
	}
	
	// Parse attribute name and value from input.
	$split = preg_split( '/\s*=\s*/', $string, 2 );
	$name = $split[0];
	if ( count( $split ) == 2 ) {
		$value = $split[1];

		// Remove quotes surrounding $value.
		// Also guarantee correct quoting in $string for this one attribute.
		if ( '' == $value ) {
			$quote = '';
		} else {
			$quote = $value[0];
		}
		if ( '"' == $quote || "'" == $quote ) {
			if ( substr( $value, -1 ) != $quote ) {
				return '';
			}
			$value = substr( $value, 1, -1 );
		} else {
			$quote = '"';
		}

		// Sanitize quotes, angle braces, and entities.
		$value = esc_attr( $value );

		// Sanitize URI values.
		if ( in_array( strtolower( $name ), $uris ) ) {
			$value = wp_kses_bad_protocol( $value, $allowed_protocols );
		}

		$string = "$name=$quote$value$quote";
		$vless = 'n';
	} else {
		$value = '';
		$vless = 'y';
	}
	
	// Sanitize attribute by name.
	wp_kses_attr_check( $name, $value, $string, $vless, $element, $allowed_html );

	// Restore whitespace.
	return $lead . $string . $trail;
}

/**
 * Return a list of allowed tags and attributes for a given context.
 *
 * @since 3.5.0
 *
 * @global array $allowedposttags
 * @global array $allowedtags
 * @global array $allowedentitynames
 *
 * @param string $context The context for which to retrieve tags.
 *                        Allowed values are post, strip, data,entities, or
 *                        the name of a field filter such as pre_user_description.
 * @return array List of allowed tags and their allowed attributes.
 */
function wp_kses_allowed_html( $context = '' ) {
	global $allowedposttags, $allowedtags, $allowedentitynames;

	if ( is_array( $context ) ) {
		/**
		 * Filter HTML elements allowed for a given context.
		 *
		 * @since 3.5.0
		 *
		 * @param string $tags    Allowed tags, attributes, and/or entities.
		 * @param string $context Context to judge allowed tags by. Allowed values are 'post',
		 *                        'data', 'strip', 'entities', 'explicit', or the name of a filter.
		 */
		return apply_filters( 'wp_kses_allowed_html', $context, 'explicit' );
	}

	switch ( $context ) {
		case 'post':
			/** This filter is documented in wp-includes/kses.php */
			return apply_filters( 'wp_kses_allowed_html', $allowedposttags, $context );

		case 'user_description':
		case 'pre_user_description':
			$tags = $allowedtags;
			$tags['a']['rel'] = true;
			/** This filter is documented in wp-includes/kses.php */
			return apply_filters( 'wp_kses_allowed_html', $tags, $context );

		case 'strip':
			/** This filter is documented in wp-includes/kses.php */
			return apply_filters( 'wp_kses_allowed_html', array(), $context );

		case 'entities':
			/** This filter is documented in wp-includes/kses.php */
			return apply_filters( 'wp_kses_allowed_html', $allowedentitynames, $context);

		case 'data':
		default:
			/** This filter is documented in wp-includes/kses.php */
			return apply_filters( 'wp_kses_allowed_html', $allowedtags, $context );
	}
}

/**
 * You add any kses hooks here.
 *
 * There is currently only one kses WordPress hook and it is called here. All
 * parameters are passed to the hooks and expected to receive a string.
 *
 * @since 1.0.0
 *
 * @param string $string            Content to filter through kses
 * @param array  $allowed_html      List of allowed HTML elements
 * @param array  $allowed_protocols Allowed protocol in links
 * @return string Filtered content through 'pre_kses' hook
 */
function wp_kses_hook( $string, $allowed_html, $allowed_protocols ) {
	/**
	 * Filter content to be run through kses.
	 *
	 * @since 2.3.0
	 *
	 * @param string $string            Content to run through kses.
	 * @param array  $allowed_html      Allowed HTML elements.
	 * @param array  $allowed_protocols Allowed protocol in links.
	 */
	return apply_filters( 'pre_kses', $string, $allowed_html, $allowed_protocols );
}

/**
 * This function returns kses' version number.
 *
 * @since 1.0.0
 *
 * @return string KSES Version Number
 */
function wp_kses_version() {
	return '0.2.2';
}

/**
 * Searches for HTML tags, no matter how malformed.
 *
 * It also matches stray ">" characters.
 *
 * @since 1.0.0
 *
 * @global array $pass_allowed_html
 * @global array $pass_allowed_protocols
 *
 * @param string $string            Content to filter
 * @param array  $allowed_html      Allowed HTML elements
 * @param array  $allowed_protocols Allowed protocols to keep
 * @return string Content with fixed HTML tags
 */
function wp_kses_split( $string, $allowed_html, $allowed_protocols ) {
	global $pass_allowed_html, $pass_allowed_protocols;
	$pass_allowed_html = $allowed_html;
	$pass_allowed_protocols = $allowed_protocols;
	return preg_replace_callback( '%(<!--.*?(-->|$))|(<[^>]*(>|$)|>)%', '_wp_kses_split_callback', $string );
}

/**
 * Callback for wp_kses_split.
 *
 * @since 3.1.0
 * @access private
 *
 * @global array $pass_allowed_html
 * @global array $pass_allowed_protocols
 *
 * @return string
 */
function _wp_kses_split_callback( $match ) {
	global $pass_allowed_html, $pass_allowed_protocols;
	return wp_kses_split2( $match[0], $pass_allowed_html, $pass_allowed_protocols );
}

/**
 * Callback for wp_kses_split for fixing malformed HTML tags.
 *
 * This function does a lot of work. It rejects some very malformed things like
 * <:::>. It returns an empty string, if the element isn't allowed (look ma, no
 * strip_tags()!). Otherwise it splits the tag into an element and an attribute
 * list.
 *
 * After the tag is split into an element and an attribute list, it is run
 * through another filter which will remove illegal attributes and once that is
 * completed, will be returned.
 *
 * @access private
 * @since 1.0.0
 *
 * @param string $string            Content to filter
 * @param array  $allowed_html      Allowed HTML elements
 * @param array  $allowed_protocols Allowed protocols to keep
 * @return string Fixed HTML element
 */
function wp_kses_split2($string, $allowed_html, $allowed_protocols) {
	$string = wp_kses_stripslashes($string);

	if (substr($string, 0, 1) != '<')
		return '&gt;';
	// It matched a ">" character

	if ( '<!--' == substr( $string, 0, 4 ) ) {
		$string = str_replace( array('<!--', '-->'), '', $string );
		while ( $string != ($newstring = wp_kses($string, $allowed_html, $allowed_protocols)) )
			$string = $newstring;
		if ( $string == '' )
			return '';
		// prevent multiple dashes in comments
		$string = preg_replace('/--+/', '-', $string);
		// prevent three dashes closing a comment
		$string = preg_replace('/-$/', '', $string);
		return "<!--{$string}-->";
	}
	// Allow HTML comments

	if (!preg_match('%^<\s*(/\s*)?([a-zA-Z0-9]+)([^>]*)>?$%', $string, $matches))
		return '';
	// It's seriously malformed

	$slash = trim($matches[1]);
	$elem = $matches[2];
	$attrlist = $matches[3];

	if ( ! is_array( $allowed_html ) )
		$allowed_html = wp_kses_allowed_html( $allowed_html );

	if ( ! isset($allowed_html[strtolower($elem)]) )
		return '';
	// They are using a not allowed HTML element

	if ($slash != '')
		return "</$elem>";
	// No attributes are allowed for closing elements

	return wp_kses_attr( $elem, $attrlist, $allowed_html, $allowed_protocols );
}

/**
 * Removes all attributes, if none are allowed for this element.
 *
 * If some are allowed it calls wp_kses_hair() to split them further, and then
 * it builds up new HTML code from the data that kses_hair() returns. It also
 * removes "<" and ">" characters, if there are any left. One more thing it does
 * is to check if the tag has a closing XHTML slash, and if it does, it puts one
 * in the returned code as well.
 *
 * @since 1.0.0
 *
 * @param string $element           HTML element/tag
 * @param string $attr              HTML attributes from HTML element to closing HTML element tag
 * @param array  $allowed_html      Allowed HTML elements
 * @param array  $allowed_protocols Allowed protocols to keep
 * @return string Sanitized HTML element
 */
function wp_kses_attr($element, $attr, $allowed_html, $allowed_protocols) {
	if ( ! is_array( $allowed_html ) )
		$allowed_html = wp_kses_allowed_html( $allowed_html );

	// Is there a closing XHTML slash at the end of the attributes?
	$xhtml_slash = '';
	if (preg_match('%\s*/\s*$%', $attr))
		$xhtml_slash = ' /';

	// Are any attributes allowed at all for this element?
	if ( ! isset($allowed_html[strtolower($element)]) || count($allowed_html[strtolower($element)]) == 0 )
		return "<$element$xhtml_slash>";

	// Split it
	$attrarr = wp_kses_hair($attr, $allowed_protocols);

	// Go through $attrarr, and save the allowed attributes for this element
	// in $attr2
	$attr2 = '';
	foreach ( $attrarr as $arreach ) {
		if ( wp_kses_attr_check( $arreach['name'], $arreach['value'], $arreach['whole'], $arreach['vless'], $element, $allowed_html ) ) {
			$attr2 .= ' '.$arreach['whole'];
		}
	}

	// Remove any "<" or ">" characters
	$attr2 = preg_replace('/[<>]/', '', $attr2);

	return "<$element$attr2$xhtml_slash>";
}

/**
 * Determine whether an attribute is allowed.
 *
 * @since 4.2.3
 *
 * @param string $name The attribute name. Returns empty string when not allowed.
 * @param string $value The attribute value. Returns a filtered value.
 * @param string $whole The name=value input. Returns filtered input.
 * @param string $vless 'y' when attribute like "enabled", otherwise 'n'.
 * @param string $element The name of the element to which this attribute belongs.
 * @param array $allowed_html The full list of allowed elements and attributes.
 * @return bool Is the attribute allowed?
 */
function wp_kses_attr_check( &$name, &$value, &$whole, $vless, $element, $allowed_html ) {
	$allowed_attr = $allowed_html[strtolower( $element )];

	$name_low = strtolower( $name );
	if ( ! isset( $allowed_attr[$name_low] ) || '' == $allowed_attr[$name_low] ) {
		$name = $value = $whole = '';
		return false;
	}

	if ( 'style' == $name_low ) {
		$new_value = safecss_filter_attr( $value );

		if ( empty( $new_value ) ) {
			$name = $value = $whole = '';
			return false;
		}

		$whole = str_replace( $value, $new_value, $whole );
		$value = $new_value;
	}

	if ( is_array( $allowed_attr[$name_low] ) ) {
		// there are some checks
		foreach ( $allowed_attr[$name_low] as $currkey => $currval ) {
			if ( ! wp_kses_check_attr_val( $value, $vless, $currkey, $currval ) ) {
				$name = $value = $whole = '';
				return false;
			}
		}
	}

	return true;
}

/**
 * Builds an attribute list from string containing attributes.
 *
 * This function does a lot of work. It parses an attribute list into an array
 * with attribute data, and tries to do the right thing even if it gets weird
 * input. It will add quotes around attribute values that don't have any quotes
 * or apostrophes around them, to make it easier to produce HTML code that will
 * conform to W3C's HTML specification. It will also remove bad URL protocols
 * from attribute values. It also reduces duplicate attributes by using the
 * attribute defined first (foo='bar' foo='baz' will result in foo='bar').
 *
 * @since 1.0.0
 *
 * @param string $attr              Attribute list from HTML element to closing HTML element tag
 * @param array  $allowed_protocols Allowed protocols to keep
 * @return array List of attributes after parsing
 */
function wp_kses_hair($attr, $allowed_protocols) {
	$attrarr = array();
	$mode = 0;
	$attrname = '';
	$uris = array('xmlns', 'profile', 'href', 'src', 'cite', 'classid', 'codebase', 'data', 'usemap', 'longdesc', 'action');

	// Loop through the whole attribute list

	while (strlen($attr) != 0) {
		$working = 0; // Was the last operation successful?

		switch ($mode) {
			case 0 : // attribute name, href for instance

				if ( preg_match('/^([-a-zA-Z:]+)/', $attr, $match ) ) {
					$attrname = $match[1];
					$working = $mode = 1;
					$attr = preg_replace( '/^[-a-zA-Z:]+/', '', $attr );
				}

				break;

			case 1 : // equals sign or valueless ("selected")

				if (preg_match('/^\s*=\s*/', $attr)) // equals sign
					{
					$working = 1;
					$mode = 2;
					$attr = preg_replace('/^\s*=\s*/', '', $attr);
					break;
				}

				if (preg_match('/^\s+/', $attr)) // valueless
					{
					$working = 1;
					$mode = 0;
					if(false === array_key_exists($attrname, $attrarr)) {
						$attrarr[$attrname] = array ('name' => $attrname, 'value' => '', 'whole' => $attrname, 'vless' => 'y');
					}
					$attr = preg_replace('/^\s+/', '', $attr);
				}

				break;

			case 2 : // attribute value, a URL after href= for instance

				if (preg_match('%^"([^"]*)"(\s+|/?$)%', $attr, $match))
					// "value"
					{
					$thisval = $match[1];
					if ( in_array(strtolower($attrname), $uris) )
						$thisval = wp_kses_bad_protocol($thisval, $allowed_protocols);

					if(false === array_key_exists($attrname, $attrarr)) {
						$attrarr[$attrname] = array ('name' => $attrname, 'value' => $thisval, 'whole' => "$attrname=\"$thisval\"", 'vless' => 'n');
					}
					$working = 1;
					$mode = 0;
					$attr = preg_replace('/^"[^"]*"(\s+|$)/', '', $attr);
					break;
				}

				if (preg_match("%^'([^']*)'(\s+|/?$)%", $attr, $match))
					// 'value'
					{
					$thisval = $match[1];
					if ( in_array(strtolower($attrname), $uris) )
						$thisval = wp_kses_bad_protocol($thisval, $allowed_protocols);

					if(false === array_key_exists($attrname, $attrarr)) {
						$attrarr[$attrname] = array ('name' => $attrname, 'value' => $thisval, 'whole' => "$attrname='$thisval'", 'vless' => 'n');
					}
					$working = 1;
					$mode = 0;
					$attr = preg_replace("/^'[^']*'(\s+|$)/", '', $attr);
					break;
				}

				if (preg_match("%^([^\s\"']+)(\s+|/?$)%", $attr, $match))
					// value
					{
					$thisval = $match[1];
					if ( in_array(strtolower($attrname), $uris) )
						$thisval = wp_kses_bad_protocol($thisval, $allowed_protocols);

					if(false === array_key_exists($attrname, $attrarr)) {
						$attrarr[$attrname] = array ('name' => $attrname, 'value' => $thisval, 'whole' => "$attrname=\"$thisval\"", 'vless' => 'n');
					}
					// We add quotes to conform to W3C's HTML spec.
					$working = 1;
					$mode = 0;
					$attr = preg_replace("%^[^\s\"']+(\s+|$)%", '', $attr);
				}

				break;
		} // switch

		if ($working == 0) // not well formed, remove and try again
		{
			$attr = wp_kses_html_error($attr);
			$mode = 0;
		}
	} // while

	if ($mode == 1 && false === array_key_exists($attrname, $attrarr))
		// special case, for when the attribute list ends with a valueless
		// attribute like "selected"
		$attrarr[$attrname] = array ('name' => $attrname, 'value' => '', 'whole' => $attrname, 'vless' => 'y');

	return $attrarr;
}

/**
 * Finds all attributes of an HTML element.
 *
 * Does not modify input.  May return "evil" output.
 *
 * Based on wp_kses_split2() and wp_kses_attr()
 *
 * @since 4.2.3
 *
 * @param string $element HTML element/tag
 * @return array|bool List of attributes found in $element. Returns false on failure.
 */
function wp_kses_attr_parse( $element ) {
	$valid = preg_match('%^(<\s*)(/\s*)?([a-zA-Z0-9]+\s*)([^>]*)(>?)$%', $element, $matches);
	if ( 1 !== $valid ) {
		return false;
	}

	$begin =  $matches[1];
	$slash =  $matches[2];
	$elname = $matches[3];
	$attr =   $matches[4];
	$end =    $matches[5];

	if ( '' !== $slash ) {
		// Closing elements do not get parsed.
		return false;
	}

	// Is there a closing XHTML slash at the end of the attributes?
	if ( 1 === preg_match( '%\s*/\s*$%', $attr, $matches ) ) {
		$xhtml_slash = $matches[0];
		$attr = substr( $attr, 0, -strlen( $xhtml_slash ) );
	} else {
		$xhtml_slash = '';
	}
	
	// Split it
	$attrarr = wp_kses_hair_parse( $attr );
	if ( false === $attrarr ) {
		return false;
	}

	// Make sure all input is returned by adding front and back matter.
	array_unshift( $attrarr, $begin . $slash . $elname );
	array_push( $attrarr, $xhtml_slash . $end );
	
	return $attrarr;
}

/**
 * Builds an attribute list from string containing attributes.
 *
 * Does not modify input.  May return "evil" output.
 * In case of unexpected input, returns false instead of stripping things.
 *
 * Based on wp_kses_hair() but does not return a multi-dimensional array.
 *
 * @since 4.2.3
 *
 * @param string $attr Attribute list from HTML element to closing HTML element tag
 * @return array|bool List of attributes found in $attr. Returns false on failure.
 */
function wp_kses_hair_parse( $attr ) {
	if ( '' === $attr ) {
		return array();
	}

	$regex =
	  '(?:'
	.     '[-a-zA-Z:]+'   // Attribute name.
	. '|'
	.     '\[\[?[^\[\]]+\]\]?' // Shortcode in the name position implies unfiltered_html.
	. ')'
	. '(?:'               // Attribute value.
	.     '\s*=\s*'       // All values begin with '='
	.     '(?:'
	.         '"[^"]*"'   // Double-quoted
	.     '|'
	.         "'[^']*'"   // Single-quoted
	.     '|'
	.         '[^\s"\']+' // Non-quoted
	.         '(?:\s|$)'  // Must have a space
	.     ')'
	. '|'
	.     '(?:\s|$)'      // If attribute has no value, space is required.
	. ')'
	. '\s*';              // Trailing space is optional except as mentioned above.

	// Although it is possible to reduce this procedure to a single regexp,
	// we must run that regexp twice to get exactly the expected result.

	$validation = "%^($regex)+$%";
	$extraction = "%$regex%";

	if ( 1 === preg_match( $validation, $attr ) ) {
		preg_match_all( $extraction, $attr, $attrarr );
		return $attrarr[0];
	} else {
		return false;
	}
}

/**
 * Performs different checks for attribute values.
 *
 * The currently implemented checks are "maxlen", "minlen", "maxval", "minval"
 * and "valueless".
 *
 * @since 1.0.0
 *
 * @param string $value      Attribute value
 * @param string $vless      Whether the value is valueless. Use 'y' or 'n'
 * @param string $checkname  What $checkvalue is checking for.
 * @param mixed  $checkvalue What constraint the value should pass
 * @return bool Whether check passes
 */
function wp_kses_check_attr_val($value, $vless, $checkname, $checkvalue) {
	$ok = true;

	switch (strtolower($checkname)) {
		case 'maxlen' :
			// The maxlen check makes sure that the attribute value has a length not
			// greater than the given value. This can be used to avoid Buffer Overflows
			// in WWW clients and various Internet servers.

			if (strlen($value) > $checkvalue)
				$ok = false;
			break;

		case 'minlen' :
			// The minlen check makes sure that the attribute value has a length not
			// smaller than the given value.

			if (strlen($value) < $checkvalue)
				$ok = false;
			break;

		case 'maxval' :
			// The maxval check does two things: it checks that the attribute value is
			// an integer from 0 and up, without an excessive amount of zeroes or
			// whitespace (to avoid Buffer Overflows). It also checks that the attribute
			// value is not greater than the given value.
			// This check can be used to avoid Denial of Service attacks.

			if (!preg_match('/^\s{0,6}[0-9]{1,6}\s{0,6}$/', $value))
				$ok = false;
			if ($value > $checkvalue)
				$ok = false;
			break;

		case 'minval' :
			// The minval check makes sure that the attribute value is a positive integer,
			// and that it is not smaller than the given value.

			if (!preg_match('/^\s{0,6}[0-9]{1,6}\s{0,6}$/', $value))
				$ok = false;
			if ($value < $checkvalue)
				$ok = false;
			break;

		case 'valueless' :
			// The valueless check makes sure if the attribute has a value
			// (like <a href="blah">) or not (<option selected>). If the given value
			// is a "y" or a "Y", the attribute must not have a value.
			// If the given value is an "n" or an "N", the attribute must have one.

			if (strtolower($checkvalue) != $vless)
				$ok = false;
			break;
	} // switch

	return $ok;
}

/**
 * Sanitize string from bad protocols.
 *
 * This function removes all non-allowed protocols from the beginning of
 * $string. It ignores whitespace and the case of the letters, and it does
 * understand HTML entities. It does its work in a while loop, so it won't be
 * fooled by a string like "javascript:javascript:alert(57)".
 *
 * @since 1.0.0
 *
 * @param string $string            Content to filter bad protocols from
 * @param array  $allowed_protocols Allowed protocols to keep
 * @return string Filtered content
 */
function wp_kses_bad_protocol($string, $allowed_protocols) {
	$string = wp_kses_no_null($string);
	$iterations = 0;

	do {
		$original_string = $string;
		$string = wp_kses_bad_protocol_once($string, $allowed_protocols);
	} while ( $original_string != $string && ++$iterations < 6 );

	if ( $original_string != $string )
		return '';

	return $string;
}

/**
 * Removes any invalid control characters in $string.
 *
 * Also removes any instance of the '\0' string.
 *
 * @since 1.0.0
 *
 * @param string $string
 * @param array $options Set 'slash_zero' => 'keep' when '\0' is allowed. Default is 'remove'.
 * @return string
 */
function wp_kses_no_null( $string, $options = null ) {
	if ( ! isset( $options['slash_zero'] ) ) {
		$options = array( 'slash_zero' => 'remove' );
	}

	$string = preg_replace( '/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', $string );
	if ( 'remove' == $options['slash_zero'] ) {
		$string = preg_replace( '/\\\\+0+/', '', $string );
	}

	return $string;
}

/**
 * Strips slashes from in front of quotes.
 *
 * This function changes the character sequence \" to just ". It leaves all
 * other slashes alone. It's really weird, but the quoting from
 * preg_replace(//e) seems to require this.
 *
 * @since 1.0.0
 *
 * @param string $string String to strip slashes
 * @return string Fixed string with quoted slashes
 */
function wp_kses_stripslashes($string) {
	return preg_replace('%\\\\"%', '"', $string);
}

/**
 * Goes through an array and changes the keys to all lower case.
 *
 * @since 1.0.0
 *
 * @param array $inarray Unfiltered array
 * @return array Fixed array with all lowercase keys
 */
function wp_kses_array_lc($inarray) {
	$outarray = array ();

	foreach ( (array) $inarray as $inkey => $inval) {
		$outkey = strtolower($inkey);
		$outarray[$outkey] = array ();

		foreach ( (array) $inval as $inkey2 => $inval2) {
			$outkey2 = strtolower($inkey2);
			$outarray[$outkey][$outkey2] = $inval2;
		} // foreach $inval
	} // foreach $inarray

	return $outarray;
}

/**
 * Removes the HTML JavaScript entities found in early versions of Netscape 4.
 *
 * @since 1.0.0
 *
 * @param string $string
 * @return string
 */
function wp_kses_js_entities($string) {
	return preg_replace('%&\s*\{[^}]*(\}\s*;?|$)%', '', $string);
}

/**
 * Handles parsing errors in wp_kses_hair().
 *
 * The general plan is to remove everything to and including some whitespace,
 * but it deals with quotes and apostrophes as well.
 *
 * @since 1.0.0
 *
 * @param string $string
 * @return string
 */
function wp_kses_html_error($string) {
	return preg_replace('/^("[^"]*("|$)|\'[^\']*(\'|$)|\S)*\s*/', '', $string);
}

/**
 * Sanitizes content from bad protocols and other characters.
 *
 * This function searches for URL protocols at the beginning of $string, while
 * handling whitespace and HTML entities.
 *
 * @since 1.0.0
 *
 * @param string $string            Content to check for bad protocols
 * @param string $allowed_protocols Allowed protocols
 * @return string Sanitized content
 */
function wp_kses_bad_protocol_once($string, $allowed_protocols, $count = 1 ) {
	$string2 = preg_split( '/:|&#0*58;|&#x0*3a;/i', $string, 2 );
	if ( isset($string2[1]) && ! preg_match('%/\?%', $string2[0]) ) {
		$string = trim( $string2[1] );
		$protocol = wp_kses_bad_protocol_once2( $string2[0], $allowed_protocols );
		if ( 'feed:' == $protocol ) {
			if ( $count > 2 )
				return '';
			$string = wp_kses_bad_protocol_once( $string, $allowed_protocols, ++$count );
			if ( empty( $string ) )
				return $string;
		}
		$string = $protocol . $string;
	}

	return $string;
}

/**
 * Callback for wp_kses_bad_protocol_once() regular expression.
 *
 * This function processes URL protocols, checks to see if they're in the
 * whitelist or not, and returns different data depending on the answer.
 *
 * @access private
 * @since 1.0.0
 *
 * @param string $string            URI scheme to check against the whitelist
 * @param string $allowed_protocols Allowed protocols
 * @return string Sanitized content
 */
function wp_kses_bad_protocol_once2( $string, $allowed_protocols ) {
	$string2 = wp_kses_decode_entities($string);
	$string2 = preg_replace('/\s/', '', $string2);
	$string2 = wp_kses_no_null($string2);
	$string2 = strtolower($string2);

	$allowed = false;
	foreach ( (array) $allowed_protocols as $one_protocol )
		if ( strtolower($one_protocol) == $string2 ) {
			$allowed = true;
			break;
		}

	if ($allowed)
		return "$string2:";
	else
		return '';
}

/**
 * Converts and fixes HTML entities.
 *
 * This function normalizes HTML entities. It will convert `AT&T` to the correct
 * `AT&amp;T`, `&#00058;` to `&#58;`, `&#XYZZY;` to `&amp;#XYZZY;` and so on.
 *
 * @since 1.0.0
 *
 * @param string $string Content to normalize entities
 * @return string Content with normalized entities
 */
function wp_kses_normalize_entities($string) {
	// Disarm all entities by converting & to &amp;
	$string = str_replace('&', '&amp;', $string);

	// Change back the allowed entities in our entity whitelist
	$string = preg_replace_callback('/&amp;([A-Za-z]{2,8}[0-9]{0,2});/', 'wp_kses_named_entities', $string);
	$string = preg_replace_callback('/&amp;#(0*[0-9]{1,7});/', 'wp_kses_normalize_entities2', $string);
	$string = preg_replace_callback('/&amp;#[Xx](0*[0-9A-Fa-f]{1,6});/', 'wp_kses_normalize_entities3', $string);

	return $string;
}

/**
 * Callback for wp_kses_normalize_entities() regular expression.
 *
 * This function only accepts valid named entity references, which are finite,
 * case-sensitive, and highly scrutinized by HTML and XML validators.
 *
 * @since 3.0.0
 *
 * @global array $allowedentitynames
 *
 * @param array $matches preg_replace_callback() matches array
 * @return string Correctly encoded entity
 */
function wp_kses_named_entities($matches) {
	global $allowedentitynames;

	if ( empty($matches[1]) )
		return '';

	$i = $matches[1];
	return ( ! in_array( $i, $allowedentitynames ) ) ? "&amp;$i;" : "&$i;";
}

/**
 * Callback for wp_kses_normalize_entities() regular expression.
 *
 * This function helps {@see wp_kses_normalize_entities()} to only accept 16-bit
 * values and nothing more for `&#number;` entities.
 *
 * @access private
 * @since 1.0.0
 *
 * @param array $matches preg_replace_callback() matches array
 * @return string Correctly encoded entity
 */
function wp_kses_normalize_entities2($matches) {
	if ( empty($matches[1]) )
		return '';

	$i = $matches[1];
	if (valid_unicode($i)) {
		$i = str_pad(ltrim($i,'0'), 3, '0', STR_PAD_LEFT);
		$i = "&#$i;";
	} else {
		$i = "&amp;#$i;";
	}

	return $i;
}

/**
 * Callback for wp_kses_normalize_entities() for regular expression.
 *
 * This function helps wp_kses_normalize_entities() to only accept valid Unicode
 * numeric entities in hex form.
 *
 * @access private
 *
 * @param array $matches preg_replace_callback() matches array
 * @return string Correctly encoded entity
 */
function wp_kses_normalize_entities3($matches) {
	if ( empty($matches[1]) )
		return '';

	$hexchars = $matches[1];
	return ( ! valid_unicode( hexdec( $hexchars ) ) ) ? "&amp;#x$hexchars;" : '&#x'.ltrim($hexchars,'0').';';
}

/**
 * Helper function to determine if a Unicode value is valid.
 *
 * @param int $i Unicode value
 * @return bool True if the value was a valid Unicode number
 */
function valid_unicode($i) {
	return ( $i == 0x9 || $i == 0xa || $i == 0xd ||
			($i >= 0x20 && $i <= 0xd7ff) ||
			($i >= 0xe000 && $i <= 0xfffd) ||
			($i >= 0x10000 && $i <= 0x10ffff) );
}

/**
 * Convert all entities to their character counterparts.
 *
 * This function decodes numeric HTML entities (`&#65;` and `&#x41;`).
 * It doesn't do anything with other entities like &auml;, but we don't
 * need them in the URL protocol whitelisting system anyway.
 *
 * @since 1.0.0
 *
 * @param string $string Content to change entities
 * @return string Content after decoded entities
 */
function wp_kses_decode_entities($string) {
	$string = preg_replace_callback('/&#([0-9]+);/', '_wp_kses_decode_entities_chr', $string);
	$string = preg_replace_callback('/&#[Xx]([0-9A-Fa-f]+);/', '_wp_kses_decode_entities_chr_hexdec', $string);

	return $string;
}

/**
 * Regex callback for wp_kses_decode_entities()
 *
 * @param array $match preg match
 * @return string
 */
function _wp_kses_decode_entities_chr( $match ) {
	return chr( $match[1] );
}

/**
 * Regex callback for wp_kses_decode_entities()
 *
 * @param array $match preg match
 * @return string
 */
function _wp_kses_decode_entities_chr_hexdec( $match ) {
	return chr( hexdec( $match[1] ) );
}

/**
 * Sanitize content with allowed HTML Kses rules.
 *
 * @since 1.0.0
 *
 * @param string $data Content to filter, expected to be escaped with slashes
 * @return string Filtered content
 */
function wp_filter_kses( $data ) {
	return addslashes( wp_kses( stripslashes( $data ), current_filter() ) );
}

/**
 * Sanitize content with allowed HTML Kses rules.
 *
 * @since 2.9.0
 *
 * @param string $data Content to filter, expected to not be escaped
 * @return string Filtered content
 */
function wp_kses_data( $data ) {
	return wp_kses( $data, current_filter() );
}

/**
 * Sanitize content for allowed HTML tags for post content.
 *
 * Post content refers to the page contents of the 'post' type and not $_POST
 * data from forms.
 *
 * @since 2.0.0
 *
 * @param string $data Post content to filter, expected to be escaped with slashes
 * @return string Filtered post content with allowed HTML tags and attributes intact.
 */
function wp_filter_post_kses( $data ) {
	return addslashes( wp_kses( stripslashes( $data ), 'post' ) );
}

/**
 * Sanitize content for allowed HTML tags for post content.
 *
 * Post content refers to the page contents of the 'post' type and not $_POST
 * data from forms.
 *
 * @since 2.9.0
 *
 * @param string $data Post content to filter
 * @return string Filtered post content with allowed HTML tags and attributes intact.
 */
function wp_kses_post( $data ) {
	return wp_kses( $data, 'post' );
}

/**
 * Navigates through an array, object, or scalar, and sanitizes content for
 * allowed HTML tags for post content.
 *
 * @since 4.4.2
 *
 * @param mixed $value The array or string to filter.
 * @return mixed $value The filtered content.
 */
function wp_kses_post_deep( $data ) {
	return map_deep( $data, 'wp_kses_post' );
}

/**
 * Strips all of the HTML in the content.
 *
 * @since 2.1.0
 *
 * @param string $data Content to strip all HTML from
 * @return string Filtered content without any HTML
 */
function wp_filter_nohtml_kses( $data ) {
	return addslashes( wp_kses( stripslashes( $data ), 'strip' ) );
}

/**
 * Adds all Kses input form content filters.
 *
 * All hooks have default priority. The wp_filter_kses() function is added to
 * the 'pre_comment_content' and 'title_save_pre' hooks.
 *
 * The wp_filter_post_kses() function is added to the 'content_save_pre',
 * 'excerpt_save_pre', and 'content_filtered_save_pre' hooks.
 *
 * @since 2.0.0
 */
function kses_init_filters() {
	// Normal filtering
	add_filter('title_save_pre', 'wp_filter_kses');

	// Comment filtering
	if ( current_user_can( 'unfiltered_html' ) )
		add_filter( 'pre_comment_content', 'wp_filter_post_kses' );
	else
		add_filter( 'pre_comment_content', 'wp_filter_kses' );

	// Post filtering
	add_filter('content_save_pre', 'wp_filter_post_kses');
	add_filter('excerpt_save_pre', 'wp_filter_post_kses');
	add_filter('content_filtered_save_pre', 'wp_filter_post_kses');
}

/**
 * Removes all Kses input form content filters.
 *
 * A quick procedural method to removing all of the filters that kses uses for
 * content in WordPress Loop.
 *
 * Does not remove the kses_init() function from 'init' hook (priority is
 * default). Also does not remove kses_init() function from 'set_current_user'
 * hook (priority is also default).
 *
 * @since 2.0.6
 */
function kses_remove_filters() {
	// Normal filtering
	remove_filter('title_save_pre', 'wp_filter_kses');

	// Comment filtering
	remove_filter( 'pre_comment_content', 'wp_filter_post_kses' );
	remove_filter( 'pre_comment_content', 'wp_filter_kses' );

	// Post filtering
	remove_filter('content_save_pre', 'wp_filter_post_kses');
	remove_filter('excerpt_save_pre', 'wp_filter_post_kses');
	remove_filter('content_filtered_save_pre', 'wp_filter_post_kses');
}

/**
 * Sets up most of the Kses filters for input form content.
 *
 * If you remove the kses_init() function from 'init' hook and
 * 'set_current_user' (priority is default), then none of the Kses filter hooks
 * will be added.
 *
 * First removes all of the Kses filters in case the current user does not need
 * to have Kses filter the content. If the user does not have unfiltered_html
 * capability, then Kses filters are added.
 *
 * @since 2.0.0
 */
function kses_init() {
	kses_remove_filters();

	if ( ! current_user_can( 'unfiltered_html' ) ) {
		kses_init_filters();
	}
}

/**
 * Inline CSS filter
 *
 * @since 2.8.1
 */
function safecss_filter_attr( $css, $deprecated = '' ) {
	if ( !empty( $deprecated ) )
		_deprecated_argument( __FUNCTION__, '2.8.1' ); // Never implemented

	$css = wp_kses_no_null($css);
	$css = str_replace(array("\n","\r","\t"), '', $css);

	if ( preg_match( '%[\\\\(&=}]|/\*%', $css ) ) // remove any inline css containing \ ( & } = or comments
		return '';

	$css_array = explode( ';', trim( $css ) );

	/**
	 * Filter list of allowed CSS attributes.
	 *
	 * @since 2.8.1
	 *
	 * @param array $attr List of allowed CSS attributes.
	 */
	$allowed_attr = apply_filters( 'safe_style_css', array( 'text-align', 'margin', 'color', 'float',
	'border', 'background', 'background-color', 'border-bottom', 'border-bottom-color',
	'border-bottom-style', 'border-bottom-width', 'border-collapse', 'border-color', 'border-left',
	'border-left-color', 'border-left-style', 'border-left-width', 'border-right', 'border-right-color',
	'border-right-style', 'border-right-width', 'border-spacing', 'border-style', 'border-top',
	'border-top-color', 'border-top-style', 'border-top-width', 'border-width', 'caption-side',
	'clear', 'cursor', 'direction', 'font', 'font-family', 'font-size', 'font-style',
	'font-variant', 'font-weight', 'height', 'min-height','max-height' , 'letter-spacing', 'line-height', 'margin-bottom',
	'margin-left', 'margin-right', 'margin-top', 'overflow', 'padding', 'padding-bottom',
	'padding-left', 'padding-right', 'padding-top', 'text-decoration', 'text-indent', 'vertical-align',
	'width', 'min-width', 'max-width' ) );

	if ( empty($allowed_attr) )
		return $css;

	$css = '';
	foreach ( $css_array as $css_item ) {
		if ( $css_item == '' )
			continue;
		$css_item = trim( $css_item );
		$found = false;
		if ( strpos( $css_item, ':' ) === false ) {
			$found = true;
		} else {
			$parts = explode( ':', $css_item );
			if ( in_array( trim( $parts[0] ), $allowed_attr ) )
				$found = true;
		}
		if ( $found ) {
			if( $css != '' )
				$css .= ';';
			$css .= $css_item;
		}
	}

	return $css;
}

/**
 * Helper function to add global attributes to a tag in the allowed html list.
 *
 * @since 3.5.0
 * @access private
 *
 * @param array $value An array of attributes.
 * @return array The array of attributes with global attributes added.
 */
function _wp_add_global_attributes( $value ) {
	$global_attributes = array(
		'class' => true,
		'id' => true,
		'style' => true,
		'title' => true,
		'role' => true,
	);

	if ( true === $value )
		$value = array();

	if ( is_array( $value ) )
		return array_merge( $value, $global_attributes );

	return $value;
}
