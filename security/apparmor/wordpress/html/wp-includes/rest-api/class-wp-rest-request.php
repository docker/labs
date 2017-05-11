<?php
/**
 * REST API: WP_REST_Request class
 *
 * @package WordPress
 * @subpackage REST_API
 * @since 4.4.0
 */

/**
 * Core class used to implement a REST request object.
 *
 * Contains data from the request, to be passed to the callback.
 *
 * Note: This implements ArrayAccess, and acts as an array of parameters when
 * used in that manner. It does not use ArrayObject (as we cannot rely on SPL),
 * so be aware it may have non-array behaviour in some cases.
 *
 * @since 4.4.0
 *
 * @see ArrayAccess
 */
class WP_REST_Request implements ArrayAccess {

	/**
	 * HTTP method.
	 *
	 * @since 4.4.0
	 * @access protected
	 * @var string
	 */
	protected $method = '';

	/**
	 * Parameters passed to the request.
	 *
	 * These typically come from the `$_GET`, `$_POST` and `$_FILES`
	 * superglobals when being created from the global scope.
	 *
	 * @since 4.4.0
	 * @access protected
	 * @var array Contains GET, POST and FILES keys mapping to arrays of data.
	 */
	protected $params;

	/**
	 * HTTP headers for the request.
	 *
	 * @since 4.4.0
	 * @access protected
	 * @var array Map of key to value. Key is always lowercase, as per HTTP specification.
	 */
	protected $headers = array();

	/**
	 * Body data.
	 *
	 * @since 4.4.0
	 * @access protected
	 * @var string Binary data from the request.
	 */
	protected $body = null;

	/**
	 * Route matched for the request.
	 *
	 * @since 4.4.0
	 * @access protected
	 * @var string
	 */
	protected $route;

	/**
	 * Attributes (options) for the route that was matched.
	 *
	 * This is the options array used when the route was registered, typically
	 * containing the callback as well as the valid methods for the route.
	 *
	 * @since 4.4.0
	 * @access protected
	 * @var array Attributes for the request.
	 */
	protected $attributes = array();

	/**
	 * Used to determine if the JSON data has been parsed yet.
	 *
	 * Allows lazy-parsing of JSON data where possible.
	 *
	 * @since 4.4.0
	 * @access protected
	 * @var bool
	 */
	protected $parsed_json = false;

	/**
	 * Used to determine if the body data has been parsed yet.
	 *
	 * @since 4.4.0
	 * @access protected
	 * @var bool
	 */
	protected $parsed_body = false;

	/**
	 * Constructor.
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @param string $method     Optional. Request method. Default empty.
	 * @param string $route      Optional. Request route. Default empty.
	 * @param array  $attributes Optional. Request attributes. Default empty array.
	 */
	public function __construct( $method = '', $route = '', $attributes = array() ) {
		$this->params = array(
			'URL'   => array(),
			'GET'   => array(),
			'POST'  => array(),
			'FILES' => array(),

			// See parse_json_params.
			'JSON'  => null,

			'defaults' => array(),
		);

		$this->set_method( $method );
		$this->set_route( $route );
		$this->set_attributes( $attributes );
	}

	/**
	 * Retrieves the HTTP method for the request.
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @return string HTTP method.
	 */
	public function get_method() {
		return $this->method;
	}

	/**
	 * Sets HTTP method for the request.
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @param string $method HTTP method.
	 */
	public function set_method( $method ) {
		$this->method = strtoupper( $method );
	}

	/**
	 * Retrieves all headers from the request.
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @return array Map of key to value. Key is always lowercase, as per HTTP specification.
	 */
	public function get_headers() {
		return $this->headers;
	}

	/**
	 * Canonicalizes the header name.
	 *
	 * Ensures that header names are always treated the same regardless of
	 * source. Header names are always case insensitive.
	 *
	 * Note that we treat `-` (dashes) and `_` (underscores) as the same
	 * character, as per header parsing rules in both Apache and nginx.
	 *
	 * @link http://stackoverflow.com/q/18185366
	 * @link http://wiki.nginx.org/Pitfalls#Missing_.28disappearing.29_HTTP_headers
	 * @link http://nginx.org/en/docs/http/ngx_http_core_module.html#underscores_in_headers
	 *
	 * @since 4.4.0
	 * @access public
	 * @static
	 *
	 * @param string $key Header name.
	 * @return string Canonicalized name.
	 */
	public static function canonicalize_header_name( $key ) {
		$key = strtolower( $key );
		$key = str_replace( '-', '_', $key );

		return $key;
	}

	/**
	 * Retrieves the given header from the request.
	 *
	 * If the header has multiple values, they will be concatenated with a comma
	 * as per the HTTP specification. Be aware that some non-compliant headers
	 * (notably cookie headers) cannot be joined this way.
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @param string $key Header name, will be canonicalized to lowercase.
	 * @return string|null String value if set, null otherwise.
	 */
	public function get_header( $key ) {
		$key = $this->canonicalize_header_name( $key );

		if ( ! isset( $this->headers[ $key ] ) ) {
			return null;
		}

		return implode( ',', $this->headers[ $key ] );
	}

	/**
	 * Retrieves header values from the request.
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @param string $key Header name, will be canonicalized to lowercase.
	 * @return array|null List of string values if set, null otherwise.
	 */
	public function get_header_as_array( $key ) {
		$key = $this->canonicalize_header_name( $key );

		if ( ! isset( $this->headers[ $key ] ) ) {
			return null;
		}

		return $this->headers[ $key ];
	}

	/**
	 * Sets the header on request.
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @param string $key   Header name.
	 * @param string $value Header value, or list of values.
	 */
	public function set_header( $key, $value ) {
		$key = $this->canonicalize_header_name( $key );
		$value = (array) $value;

		$this->headers[ $key ] = $value;
	}

	/**
	 * Appends a header value for the given header.
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @param string $key   Header name.
	 * @param string $value Header value, or list of values.
	 */
	public function add_header( $key, $value ) {
		$key = $this->canonicalize_header_name( $key );
		$value = (array) $value;

		if ( ! isset( $this->headers[ $key ] ) ) {
			$this->headers[ $key ] = array();
		}

		$this->headers[ $key ] = array_merge( $this->headers[ $key ], $value );
	}

	/**
	 * Removes all values for a header.
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @param string $key Header name.
	 */
	public function remove_header( $key ) {
		unset( $this->headers[ $key ] );
	}

	/**
	 * Sets headers on the request.
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @param array $headers  Map of header name to value.
	 * @param bool  $override If true, replace the request's headers. Otherwise, merge with existing.
	 */
	public function set_headers( $headers, $override = true ) {
		if ( true === $override ) {
			$this->headers = array();
		}

		foreach ( $headers as $key => $value ) {
			$this->set_header( $key, $value );
		}
	}

	/**
	 * Retrieves the content-type of the request.
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @return array Map containing 'value' and 'parameters' keys.
	 */
	public function get_content_type() {
		$value = $this->get_header( 'content-type' );
		if ( empty( $value ) ) {
			return null;
		}

		$parameters = '';
		if ( strpos( $value, ';' ) ) {
			list( $value, $parameters ) = explode( ';', $value, 2 );
		}

		$value = strtolower( $value );
		if ( strpos( $value, '/' ) === false ) {
			return null;
		}

		// Parse type and subtype out.
		list( $type, $subtype ) = explode( '/', $value, 2 );

		$data = compact( 'value', 'type', 'subtype', 'parameters' );
		$data = array_map( 'trim', $data );

		return $data;
	}

	/**
	 * Retrieves the parameter priority order.
	 *
	 * Used when checking parameters in get_param().
	 *
	 * @since 4.4.0
	 * @access protected
	 *
	 * @return array List of types to check, in order of priority.
	 */
	protected function get_parameter_order() {
		$order = array();
		$order[] = 'JSON';

		$this->parse_json_params();

		// Ensure we parse the body data.
		$body = $this->get_body();
		if ( $this->method !== 'POST' && ! empty( $body ) ) {
			$this->parse_body_params();
		}

		$accepts_body_data = array( 'POST', 'PUT', 'PATCH' );
		if ( in_array( $this->method, $accepts_body_data ) ) {
			$order[] = 'POST';
		}

		$order[] = 'GET';
		$order[] = 'URL';
		$order[] = 'defaults';

		/**
		 * Filter the parameter order.
		 *
		 * The order affects which parameters are checked when using get_param() and family.
		 * This acts similarly to PHP's `request_order` setting.
		 *
		 * @since 4.4.0
		 *
		 * @param array           $order {
		 *    An array of types to check, in order of priority.
		 *
		 *    @param string $type The type to check.
		 * }
		 * @param WP_REST_Request $this The request object.
		 */
		return apply_filters( 'rest_request_parameter_order', $order, $this );
	}

	/**
	 * Retrieves a parameter from the request.
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @param string $key Parameter name.
	 * @return mixed|null Value if set, null otherwise.
	 */
	public function get_param( $key ) {
		$order = $this->get_parameter_order();

		foreach ( $order as $type ) {
			// Determine if we have the parameter for this type.
			if ( isset( $this->params[ $type ][ $key ] ) ) {
				return $this->params[ $type ][ $key ];
			}
		}

		return null;
	}

	/**
	 * Sets a parameter on the request.
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @param string $key   Parameter name.
	 * @param mixed  $value Parameter value.
	 */
	public function set_param( $key, $value ) {
		switch ( $this->method ) {
			case 'POST':
				$this->params['POST'][ $key ] = $value;
				break;

			default:
				$this->params['GET'][ $key ] = $value;
				break;
		}
	}

	/**
	 * Retrieves merged parameters from the request.
	 *
	 * The equivalent of get_param(), but returns all parameters for the request.
	 * Handles merging all the available values into a single array.
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @return array Map of key to value.
	 */
	public function get_params() {
		$order = $this->get_parameter_order();
		$order = array_reverse( $order, true );

		$params = array();
		foreach ( $order as $type ) {
			$params = array_merge( $params, (array) $this->params[ $type ] );
		}

		return $params;
	}

	/**
	 * Retrieves parameters from the route itself.
	 *
	 * These are parsed from the URL using the regex.
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @return array Parameter map of key to value.
	 */
	public function get_url_params() {
		return $this->params['URL'];
	}

	/**
	 * Sets parameters from the route.
	 *
	 * Typically, this is set after parsing the URL.
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @param array $params Parameter map of key to value.
	 */
	public function set_url_params( $params ) {
		$this->params['URL'] = $params;
	}

	/**
	 * Retrieves parameters from the query string.
	 *
	 * These are the parameters you'd typically find in `$_GET`.
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @return array Parameter map of key to value
	 */
	public function get_query_params() {
		return $this->params['GET'];
	}

	/**
	 * Sets parameters from the query string.
	 *
	 * Typically, this is set from `$_GET`.
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @param array $params Parameter map of key to value.
	 */
	public function set_query_params( $params ) {
		$this->params['GET'] = $params;
	}

	/**
	 * Retrieves parameters from the body.
	 *
	 * These are the parameters you'd typically find in `$_POST`.
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @return array Parameter map of key to value.
	 */
	public function get_body_params() {
		return $this->params['POST'];
	}

	/**
	 * Sets parameters from the body.
	 *
	 * Typically, this is set from `$_POST`.
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @param array $params Parameter map of key to value.
	 */
	public function set_body_params( $params ) {
		$this->params['POST'] = $params;
	}

	/**
	 * Retrieves multipart file parameters from the body.
	 *
	 * These are the parameters you'd typically find in `$_FILES`.
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @return array Parameter map of key to value
	 */
	public function get_file_params() {
		return $this->params['FILES'];
	}

	/**
	 * Sets multipart file parameters from the body.
	 *
	 * Typically, this is set from `$_FILES`.
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @param array $params Parameter map of key to value.
	 */
	public function set_file_params( $params ) {
		$this->params['FILES'] = $params;
	}

	/**
	 * Retrieves the default parameters.
	 *
	 * These are the parameters set in the route registration.
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @return array Parameter map of key to value
	 */
	public function get_default_params() {
		return $this->params['defaults'];
	}

	/**
	 * Sets default parameters.
	 *
	 * These are the parameters set in the route registration.
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @param array $params Parameter map of key to value.
	 */
	public function set_default_params( $params ) {
		$this->params['defaults'] = $params;
	}

	/**
	 * Retrieves the request body content.
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @return string Binary data from the request body.
	 */
	public function get_body() {
		return $this->body;
	}

	/**
	 * Sets body content.
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @param string $data Binary data from the request body.
	 */
	public function set_body( $data ) {
		$this->body = $data;

		// Enable lazy parsing.
		$this->parsed_json = false;
		$this->parsed_body = false;
		$this->params['JSON'] = null;
	}

	/**
	 * Retrieves the parameters from a JSON-formatted body.
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @return array Parameter map of key to value.
	 */
	public function get_json_params() {
		// Ensure the parameters have been parsed out.
		$this->parse_json_params();

		return $this->params['JSON'];
	}

	/**
	 * Parses the JSON parameters.
	 *
	 * Avoids parsing the JSON data until we need to access it.
	 *
	 * @since 4.4.0
	 * @access protected
	 */
	protected function parse_json_params() {
		if ( $this->parsed_json ) {
			return;
		}

		$this->parsed_json = true;

		// Check that we actually got JSON.
		$content_type = $this->get_content_type();

		if ( empty( $content_type ) || 'application/json' !== $content_type['value'] ) {
			return;
		}

		$params = json_decode( $this->get_body(), true );

		/*
		 * Check for a parsing error.
		 *
		 * Note that due to WP's JSON compatibility functions, json_last_error
		 * might not be defined: https://core.trac.wordpress.org/ticket/27799
		 */
		if ( null === $params && ( ! function_exists( 'json_last_error' ) || JSON_ERROR_NONE !== json_last_error() ) ) {
			return;
		}

		$this->params['JSON'] = $params;
	}

	/**
	 * Parses the request body parameters.
	 *
	 * Parses out URL-encoded bodies for request methods that aren't supported
	 * natively by PHP. In PHP 5.x, only POST has these parsed automatically.
	 *
	 * @since 4.4.0
	 * @access protected
	 */
	protected function parse_body_params() {
		if ( $this->parsed_body ) {
			return;
		}

		$this->parsed_body = true;

		/*
		 * Check that we got URL-encoded. Treat a missing content-type as
		 * URL-encoded for maximum compatibility.
		 */
		$content_type = $this->get_content_type();

		if ( ! empty( $content_type ) && 'application/x-www-form-urlencoded' !== $content_type['value'] ) {
			return;
		}

		parse_str( $this->get_body(), $params );

		/*
		 * Amazingly, parse_str follows magic quote rules. Sigh.
		 *
		 * NOTE: Do not refactor to use `wp_unslash`.
		 */
		if ( get_magic_quotes_gpc() ) {
			$params = stripslashes_deep( $params );
		}

		/*
		 * Add to the POST parameters stored internally. If a user has already
		 * set these manually (via `set_body_params`), don't override them.
		 */
		$this->params['POST'] = array_merge( $params, $this->params['POST'] );
	}

	/**
	 * Retrieves the route that matched the request.
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @return string Route matching regex.
	 */
	public function get_route() {
		return $this->route;
	}

	/**
	 * Sets the route that matched the request.
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @param string $route Route matching regex.
	 */
	public function set_route( $route ) {
		$this->route = $route;
	}

	/**
	 * Retrieves the attributes for the request.
	 *
	 * These are the options for the route that was matched.
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @return array Attributes for the request.
	 */
	public function get_attributes() {
		return $this->attributes;
	}

	/**
	 * Sets the attributes for the request.
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @param array $attributes Attributes for the request.
	 */
	public function set_attributes( $attributes ) {
		$this->attributes = $attributes;
	}

	/**
	 * Sanitizes (where possible) the params on the request.
	 *
	 * This is primarily based off the sanitize_callback param on each registered
	 * argument.
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @return true|null True if there are no parameters to sanitize, null otherwise.
	 */
	public function sanitize_params() {

		$attributes = $this->get_attributes();

		// No arguments set, skip sanitizing.
		if ( empty( $attributes['args'] ) ) {
			return true;
		}

		$order = $this->get_parameter_order();

		foreach ( $order as $type ) {
			if ( empty( $this->params[ $type ] ) ) {
				continue;
			}
			foreach ( $this->params[ $type ] as $key => $value ) {
				// Check if this param has a sanitize_callback added.
				if ( isset( $attributes['args'][ $key ] ) && ! empty( $attributes['args'][ $key ]['sanitize_callback'] ) ) {
					$this->params[ $type ][ $key ] = call_user_func( $attributes['args'][ $key ]['sanitize_callback'], $value, $this, $key );
				}
			}
		}
		return null;
	}

	/**
	 * Checks whether this request is valid according to its attributes.
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @return bool|WP_Error True if there are no parameters to validate or if all pass validation,
	 *                       WP_Error if required parameters are missing.
	 */
	public function has_valid_params() {

		$attributes = $this->get_attributes();
		$required = array();

		// No arguments set, skip validation.
		if ( empty( $attributes['args'] ) ) {
			return true;
		}

		foreach ( $attributes['args'] as $key => $arg ) {

			$param = $this->get_param( $key );
			if ( isset( $arg['required'] ) && true === $arg['required'] && null === $param ) {
				$required[] = $key;
			}
		}

		if ( ! empty( $required ) ) {
			return new WP_Error( 'rest_missing_callback_param', sprintf( __( 'Missing parameter(s): %s' ), implode( ', ', $required ) ), array( 'status' => 400, 'params' => $required ) );
		}

		/*
		 * Check the validation callbacks for each registered arg.
		 *
		 * This is done after required checking as required checking is cheaper.
		 */
		$invalid_params = array();

		foreach ( $attributes['args'] as $key => $arg ) {

			$param = $this->get_param( $key );

			if ( null !== $param && ! empty( $arg['validate_callback'] ) ) {
				$valid_check = call_user_func( $arg['validate_callback'], $param, $this, $key );

				if ( false === $valid_check ) {
					$invalid_params[ $key ] = __( 'Invalid parameter.' );
				}

				if ( is_wp_error( $valid_check ) ) {
					$invalid_params[] = sprintf( '%s (%s)', $key, $valid_check->get_error_message() );
				}
			}
		}

		if ( $invalid_params ) {
			return new WP_Error( 'rest_invalid_param', sprintf( __( 'Invalid parameter(s): %s' ), implode( ', ', $invalid_params ) ), array( 'status' => 400, 'params' => $invalid_params ) );
		}

		return true;

	}

	/**
	 * Checks if a parameter is set.
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @param string $offset Parameter name.
	 * @return bool Whether the parameter is set.
	 */
	public function offsetExists( $offset ) {
		$order = $this->get_parameter_order();

		foreach ( $order as $type ) {
			if ( isset( $this->params[ $type ][ $offset ] ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Retrieves a parameter from the request.
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @param string $offset Parameter name.
	 * @return mixed|null Value if set, null otherwise.
	 */
	public function offsetGet( $offset ) {
		return $this->get_param( $offset );
	}

	/**
	 * Sets a parameter on the request.
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @param string $offset Parameter name.
	 * @param mixed  $value  Parameter value.
	 */
	public function offsetSet( $offset, $value ) {
		$this->set_param( $offset, $value );
	}

	/**
	 * Removes a parameter from the request.
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @param string $offset Parameter name.
	 */
	public function offsetUnset( $offset ) {
		$order = $this->get_parameter_order();

		// Remove the offset from every group.
		foreach ( $order as $type ) {
			unset( $this->params[ $type ][ $offset ] );
		}
	}
}
