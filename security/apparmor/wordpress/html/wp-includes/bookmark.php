<?php
/**
 * Link/Bookmark API
 *
 * @package WordPress
 * @subpackage Bookmark
 */

/**
 * Retrieve Bookmark data
 *
 * @since 2.1.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param int|stdClass $bookmark
 * @param string $output Optional. Either OBJECT, ARRAY_N, or ARRAY_A constant
 * @param string $filter Optional, default is 'raw'.
 * @return array|object|null Type returned depends on $output value.
 */
function get_bookmark($bookmark, $output = OBJECT, $filter = 'raw') {
	global $wpdb;

	if ( empty($bookmark) ) {
		if ( isset($GLOBALS['link']) )
			$_bookmark = & $GLOBALS['link'];
		else
			$_bookmark = null;
	} elseif ( is_object($bookmark) ) {
		wp_cache_add($bookmark->link_id, $bookmark, 'bookmark');
		$_bookmark = $bookmark;
	} else {
		if ( isset($GLOBALS['link']) && ($GLOBALS['link']->link_id == $bookmark) ) {
			$_bookmark = & $GLOBALS['link'];
		} elseif ( ! $_bookmark = wp_cache_get($bookmark, 'bookmark') ) {
			$_bookmark = $wpdb->get_row($wpdb->prepare("SELECT * FROM $wpdb->links WHERE link_id = %d LIMIT 1", $bookmark));
			if ( $_bookmark ) {
				$_bookmark->link_category = array_unique( wp_get_object_terms( $_bookmark->link_id, 'link_category', array( 'fields' => 'ids' ) ) );
				wp_cache_add( $_bookmark->link_id, $_bookmark, 'bookmark' );
			}
		}
	}

	if ( ! $_bookmark )
		return $_bookmark;

	$_bookmark = sanitize_bookmark($_bookmark, $filter);

	if ( $output == OBJECT ) {
		return $_bookmark;
	} elseif ( $output == ARRAY_A ) {
		return get_object_vars($_bookmark);
	} elseif ( $output == ARRAY_N ) {
		return array_values(get_object_vars($_bookmark));
	} else {
		return $_bookmark;
	}
}

/**
 * Retrieve single bookmark data item or field.
 *
 * @since 2.3.0
 *
 * @param string $field The name of the data field to return
 * @param int $bookmark The bookmark ID to get field
 * @param string $context Optional. The context of how the field will be used.
 * @return string|WP_Error
 */
function get_bookmark_field( $field, $bookmark, $context = 'display' ) {
	$bookmark = (int) $bookmark;
	$bookmark = get_bookmark( $bookmark );

	if ( is_wp_error($bookmark) )
		return $bookmark;

	if ( !is_object($bookmark) )
		return '';

	if ( !isset($bookmark->$field) )
		return '';

	return sanitize_bookmark_field($field, $bookmark->$field, $bookmark->link_id, $context);
}

/**
 * Retrieves the list of bookmarks
 *
 * Attempts to retrieve from the cache first based on MD5 hash of arguments. If
 * that fails, then the query will be built from the arguments and executed. The
 * results will be stored to the cache.
 *
 * @since 2.1.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param string|array $args {
 *     Optional. String or array of arguments to retrieve bookmarks.
 *
 *     @type string   $orderby        How to order the links by. Accepts post fields. Default 'name'.
 *     @type string   $order          Whether to order bookmarks in ascending or descending order.
 *                                    Accepts 'ASC' (ascending) or 'DESC' (descending). Default 'ASC'.
 *     @type int      $limit          Amount of bookmarks to display. Accepts 1+ or -1 for all.
 *                                    Default -1.
 *     @type string   $category       Comma-separated list of category ids to include links from.
 *                                    Default empty.
 *     @type string   $category_name  Category to retrieve links for by name. Default empty.
 *     @type int|bool $hide_invisible Whether to show or hide links marked as 'invisible'. Accepts
 *                                    1|true or 0|false. Default 1|true.
 *     @type int|bool $show_updated   Whether to display the time the bookmark was last updated.
 *                                    Accepts 1|true or 0|false. Default 0|false.
 *     @type string   $include        Comma-separated list of bookmark IDs to include. Default empty.
 *     @type string   $exclude        Comma-separated list of bookmark IDs to exclude. Default empty.
 * }
 * @return array List of bookmark row objects.
 */
function get_bookmarks( $args = '' ) {
	global $wpdb;

	$defaults = array(
		'orderby' => 'name', 'order' => 'ASC',
		'limit' => -1, 'category' => '',
		'category_name' => '', 'hide_invisible' => 1,
		'show_updated' => 0, 'include' => '',
		'exclude' => '', 'search' => ''
	);

	$r = wp_parse_args( $args, $defaults );

	$key = md5( serialize( $r ) );
	if ( $cache = wp_cache_get( 'get_bookmarks', 'bookmark' ) ) {
		if ( is_array( $cache ) && isset( $cache[ $key ] ) ) {
			$bookmarks = $cache[ $key ];
			/**
			 * Filter the returned list of bookmarks.
			 *
			 * The first time the hook is evaluated in this file, it returns the cached
			 * bookmarks list. The second evaluation returns a cached bookmarks list if the
			 * link category is passed but does not exist. The third evaluation returns
			 * the full cached results.
			 *
			 * @since 2.1.0
			 *
			 * @see get_bookmarks()
			 *
			 * @param array $bookmarks List of the cached bookmarks.
			 * @param array $r         An array of bookmark query arguments.
			 */
			return apply_filters( 'get_bookmarks', $bookmarks, $r );
		}
	}

	if ( ! is_array( $cache ) ) {
		$cache = array();
	}

	$inclusions = '';
	if ( ! empty( $r['include'] ) ) {
		$r['exclude'] = '';  //ignore exclude, category, and category_name params if using include
		$r['category'] = '';
		$r['category_name'] = '';
		$inclinks = preg_split( '/[\s,]+/', $r['include'] );
		if ( count( $inclinks ) ) {
			foreach ( $inclinks as $inclink ) {
				if ( empty( $inclusions ) ) {
					$inclusions = ' AND ( link_id = ' . intval( $inclink ) . ' ';
				} else {
					$inclusions .= ' OR link_id = ' . intval( $inclink ) . ' ';
				}
			}
		}
	}
	if (! empty( $inclusions ) ) {
		$inclusions .= ')';
	}

	$exclusions = '';
	if ( ! empty( $r['exclude'] ) ) {
		$exlinks = preg_split( '/[\s,]+/', $r['exclude'] );
		if ( count( $exlinks ) ) {
			foreach ( $exlinks as $exlink ) {
				if ( empty( $exclusions ) ) {
					$exclusions = ' AND ( link_id <> ' . intval( $exlink ) . ' ';
				} else {
					$exclusions .= ' AND link_id <> ' . intval( $exlink ) . ' ';
				}
			}
		}
	}
	if ( ! empty( $exclusions ) ) {
		$exclusions .= ')';
	}

	if ( ! empty( $r['category_name'] ) ) {
		if ( $r['category'] = get_term_by('name', $r['category_name'], 'link_category') ) {
			$r['category'] = $r['category']->term_id;
		} else {
			$cache[ $key ] = array();
			wp_cache_set( 'get_bookmarks', $cache, 'bookmark' );
			/** This filter is documented in wp-includes/bookmark.php */
			return apply_filters( 'get_bookmarks', array(), $r );
		}
	}

	$search = '';
	if ( ! empty( $r['search'] ) ) {
		$like = '%' . $wpdb->esc_like( $r['search'] ) . '%';
		$search = $wpdb->prepare(" AND ( (link_url LIKE %s) OR (link_name LIKE %s) OR (link_description LIKE %s) ) ", $like, $like, $like );
	}

	$category_query = '';
	$join = '';
	if ( ! empty( $r['category'] ) ) {
		$incategories = preg_split( '/[\s,]+/', $r['category'] );
		if ( count($incategories) ) {
			foreach ( $incategories as $incat ) {
				if ( empty( $category_query ) ) {
					$category_query = ' AND ( tt.term_id = ' . intval( $incat ) . ' ';
				} else {
					$category_query .= ' OR tt.term_id = ' . intval( $incat ) . ' ';
				}
			}
		}
	}
	if ( ! empty( $category_query ) ) {
		$category_query .= ") AND taxonomy = 'link_category'";
		$join = " INNER JOIN $wpdb->term_relationships AS tr ON ($wpdb->links.link_id = tr.object_id) INNER JOIN $wpdb->term_taxonomy as tt ON tt.term_taxonomy_id = tr.term_taxonomy_id";
	}

	if ( $r['show_updated'] ) {
		$recently_updated_test = ", IF (DATE_ADD(link_updated, INTERVAL 120 MINUTE) >= NOW(), 1,0) as recently_updated ";
	} else {
		$recently_updated_test = '';
	}

	$get_updated = ( $r['show_updated'] ) ? ', UNIX_TIMESTAMP(link_updated) AS link_updated_f ' : '';

	$orderby = strtolower( $r['orderby'] );
	$length = '';
	switch ( $orderby ) {
		case 'length':
			$length = ", CHAR_LENGTH(link_name) AS length";
			break;
		case 'rand':
			$orderby = 'rand()';
			break;
		case 'link_id':
			$orderby = "$wpdb->links.link_id";
			break;
		default:
			$orderparams = array();
			$keys = array( 'link_id', 'link_name', 'link_url', 'link_visible', 'link_rating', 'link_owner', 'link_updated', 'link_notes', 'link_description' );
			foreach ( explode( ',', $orderby ) as $ordparam ) {
				$ordparam = trim( $ordparam );

				if ( in_array( 'link_' . $ordparam, $keys ) ) {
					$orderparams[] = 'link_' . $ordparam;
				} elseif ( in_array( $ordparam, $keys ) ) {
					$orderparams[] = $ordparam;
				}
			}
			$orderby = implode( ',', $orderparams );
	}

	if ( empty( $orderby ) ) {
		$orderby = 'link_name';
	}

	$order = strtoupper( $r['order'] );
	if ( '' !== $order && ! in_array( $order, array( 'ASC', 'DESC' ) ) ) {
		$order = 'ASC';
	}

	$visible = '';
	if ( $r['hide_invisible'] ) {
		$visible = "AND link_visible = 'Y'";
	}

	$query = "SELECT * $length $recently_updated_test $get_updated FROM $wpdb->links $join WHERE 1=1 $visible $category_query";
	$query .= " $exclusions $inclusions $search";
	$query .= " ORDER BY $orderby $order";
	if ( $r['limit'] != -1 ) {
		$query .= ' LIMIT ' . $r['limit'];
	}

	$results = $wpdb->get_results( $query );

	$cache[ $key ] = $results;
	wp_cache_set( 'get_bookmarks', $cache, 'bookmark' );

	/** This filter is documented in wp-includes/bookmark.php */
	return apply_filters( 'get_bookmarks', $results, $r );
}

/**
 * Sanitizes all bookmark fields
 *
 * @since 2.3.0
 *
 * @param object|array $bookmark Bookmark row
 * @param string $context Optional, default is 'display'. How to filter the
 *		fields
 * @return object|array Same type as $bookmark but with fields sanitized.
 */
function sanitize_bookmark($bookmark, $context = 'display') {
	$fields = array('link_id', 'link_url', 'link_name', 'link_image', 'link_target', 'link_category',
		'link_description', 'link_visible', 'link_owner', 'link_rating', 'link_updated',
		'link_rel', 'link_notes', 'link_rss', );

	if ( is_object($bookmark) ) {
		$do_object = true;
		$link_id = $bookmark->link_id;
	} else {
		$do_object = false;
		$link_id = $bookmark['link_id'];
	}

	foreach ( $fields as $field ) {
		if ( $do_object ) {
			if ( isset($bookmark->$field) )
				$bookmark->$field = sanitize_bookmark_field($field, $bookmark->$field, $link_id, $context);
		} else {
			if ( isset($bookmark[$field]) )
				$bookmark[$field] = sanitize_bookmark_field($field, $bookmark[$field], $link_id, $context);
		}
	}

	return $bookmark;
}

/**
 * Sanitizes a bookmark field
 *
 * Sanitizes the bookmark fields based on what the field name is. If the field
 * has a strict value set, then it will be tested for that, else a more generic
 * filtering is applied. After the more strict filter is applied, if the
 * $context is 'raw' then the value is immediately return.
 *
 * Hooks exist for the more generic cases. With the 'edit' context, the
 * 'edit_$field' filter will be called and passed the $value and $bookmark_id
 * respectively. With the 'db' context, the 'pre_$field' filter is called and
 * passed the value. The 'display' context is the final context and has the
 * $field has the filter name and is passed the $value, $bookmark_id, and
 * $context respectively.
 *
 * @since 2.3.0
 *
 * @param string $field The bookmark field
 * @param mixed $value The bookmark field value
 * @param int $bookmark_id Bookmark ID
 * @param string $context How to filter the field value. Either 'raw', 'edit',
 *		'attribute', 'js', 'db', or 'display'
 * @return mixed The filtered value
 */
function sanitize_bookmark_field($field, $value, $bookmark_id, $context) {
	switch ( $field ) {
	case 'link_id' : // ints
	case 'link_rating' :
		$value = (int) $value;
		break;
	case 'link_category' : // array( ints )
		$value = array_map('absint', (array) $value);
		// We return here so that the categories aren't filtered.
		// The 'link_category' filter is for the name of a link category, not an array of a link's link categories
		return $value;

	case 'link_visible' : // bool stored as Y|N
		$value = preg_replace('/[^YNyn]/', '', $value);
		break;
	case 'link_target' : // "enum"
		$targets = array('_top', '_blank');
		if ( ! in_array($value, $targets) )
			$value = '';
		break;
	}

	if ( 'raw' == $context )
		return $value;

	if ( 'edit' == $context ) {
		/** This filter is documented in wp-includes/post.php */
		$value = apply_filters( "edit_$field", $value, $bookmark_id );

		if ( 'link_notes' == $field ) {
			$value = esc_html( $value ); // textarea_escaped
		} else {
			$value = esc_attr($value);
		}
	} elseif ( 'db' == $context ) {
		/** This filter is documented in wp-includes/post.php */
		$value = apply_filters( "pre_$field", $value );
	} else {
		/** This filter is documented in wp-includes/post.php */
		$value = apply_filters( $field, $value, $bookmark_id, $context );

		if ( 'attribute' == $context ) {
			$value = esc_attr( $value );
		} elseif ( 'js' == $context ) {
			$value = esc_js( $value );
		}
	}

	return $value;
}

/**
 * Deletes bookmark cache
 *
 * @since 2.7.0
 */
function clean_bookmark_cache( $bookmark_id ) {
	wp_cache_delete( $bookmark_id, 'bookmark' );
	wp_cache_delete( 'get_bookmarks', 'bookmark' );
	clean_object_term_cache( $bookmark_id, 'link');
}
