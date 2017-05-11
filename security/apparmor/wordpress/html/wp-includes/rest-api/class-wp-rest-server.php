<?php
/**
 * REST API: WP_REST_Server class
 *
 * @package WordPress
 * @subpackage REST_API
 * @since 4.4.0
 */

/**
 * Core class used to implement the WordPress REST API server.
 *
 * @since 4.4.0
 */
class WP_REST_Server {

	/**
	 * Alias for GET transport method.
	 *
	 * @since 4.4.0
	 * @var string
	 */
	const READABLE = 'GET';

	/**
	 * Alias for POST transport method.
	 *
	 * @since 4.4.0
	 * @var string
	 */
	const CREATABLE = 'POST';

	/**
	 * Alias for POST, PUT, PATCH transport methods together.
	 *
	 * @since 4.4.0
	 * @var string
	 */
	const EDITABLE = 'POST, PUT, PATCH';

	/**
	 * Alias for DELETE transport method.
	 *
	 * @since 4.4.0
	 * @var string
	 */
	const DELETABLE = 'DELETE';

	/**
	 * Alias for GET, POST, PUT, PATCH & DELETE transport methods together.
	 *
	 * @since 4.4.0
	 * @var string
	 */
	const ALLMETHODS = 'GET, POST, PUT, PATCH, DELETE';

	/**
	 * Namespaces registered to the server.
	 *
	 * @since 4.4.0
	 * @access protected
	 * @var array
	 */
	protected $namespaces = array();

	/**
	 * Endpoints registered to the server.
	 *
	 * @since 4.4.0
	 * @access protected
	 * @var array
	 */
	protected $endpoints = array();

	/**
	 * Options defined for the routes.
	 *
	 * @since 4.4.0
	 * @access protected
	 * @var array
	 */
	protected $route_options = array();

	/**
	 * Instantiates the REST server.
	 *
	 * @since 4.4.0
	 * @access public
	 */
	public function __construct() {
		$this->endpoints = array(
			// Meta endpoints.
			'/' => array(
				'callback' => array( $this, 'get_index' ),
				'methods' => 'GET',
				'args' => array(
					'context' => array(
						'default' => 'view',
					),
				),
			),
		);
	}


	/**
	 * Checks the authentication headers if supplied.
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @return WP_Error|null WP_Error indicates unsuccessful login, null indicates successful
	 *                       or no authentication provided
	 */
	public function check_authentication() {
		/**
		 * Pass an authentication error to the API
		 *
		 * This is used to pass a WP_Error from an authentication method back to
		 * the API.
		 *
		 * Authentication methods should check first if they're being used, as
		 * multiple authentication methods can be enabled on a site (cookies,
		 * HTTP basic auth, OAuth). If the authentication method hooked in is
		 * not actually being attempted, null should be returned to indicate
		 * another authentication method should check instead. Similarly,
		 * callbacks should ensure the value is `null` before checking for
		 * errors.
		 *
		 * A WP_Error instance can be returned if an error occurs, and this should
		 * match the format used by API methods internally (that is, the `status`
		 * data should be used). A callback can return `true` to indicate that
		 * the authentication method was used, and it succeeded.
		 *
		 * @since 4.4.0
		 *
		 * @param WP_Error|null|bool WP_Error if authentication error, null if authentication
		 *                              method wasn't used, true if authentication succeeded.
		 */
		return apply_filters( 'rest_authentication_errors', null );
	}

	/**
	 * Converts an error to a response object.
	 *
	 * This iterates over all error codes and messages to change it into a flat
	 * array. This enables simpler client behaviour, as it is represented as a
	 * list in JSON rather than an object/map.
	 *
	 * @since 4.4.0
	 * @access protected
	 *
	 * @param WP_Error $error WP_Error instance.
	 * @return WP_REST_Response List of associative arrays with code and message keys.
	 */
	protected function error_to_response( $error ) {
		$error_data = $error->get_error_data();

		if ( is_array( $error_data ) && isset( $error_data['status'] ) ) {
			$status = $error_data['status'];
		} else {
			$status = 500;
		}

		$errors = array();

		foreach ( (array) $error->errors as $code => $messages ) {
			foreach ( (array) $messages as $message ) {
				$errors[] = array( 'code' => $code, 'message' => $message, 'data' => $error->get_error_data( $code ) );
			}
		}

		$data = $errors[0];
		if ( count( $errors ) > 1 ) {
			// Remove the primary error.
			array_shift( $errors );
			$data['additional_errors'] = $errors;
		}

		$response = new WP_REST_Response( $data, $status );

		return $response;
	}

	/**
	 * Retrieves an appropriate error representation in JSON.
	 *
	 * Note: This should only be used in WP_REST_Server::serve_request(), as it
	 * cannot handle WP_Error internally. All callbacks and other internal methods
	 * should instead return a WP_Error with the data set to an array that includes
	 * a 'status' key, with the value being the HTTP status to send.
	 *
	 * @since 4.4.0
	 * @access protected
	 *
	 * @param string $code    WP_Error-style code.
	 * @param string $message Human-readable message.
	 * @param int    $status  Optional. HTTP status code to send. Default null.
	 * @return string JSON representation of the error
	 */
	protected function json_error( $code, $message, $status = null ) {
		if ( $status ) {
			$this->set_status( $status );
		}

		$error = compact( 'code', 'message' );

		return wp_json_encode( $error );
	}

	/**
	 * Handles serving an API request.
	 *
	 * Matches the current server URI to a route and runs the first matching
	 * callback then outputs a JSON representation of the returned value.
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @see WP_REST_Server::dispatch()
	 *
	 * @param string $path Optional. The request route. If not set, `$_SERVER['PATH_INFO']` will be used.
	 *                     Default null.
	 * @return false|null Null if not served and a HEAD request, false otherwise.
	 */
	public function serve_request( $path = null ) {
		$content_type = isset( $_GET['_jsonp'] ) ? 'application/javascript' : 'application/json';
		$this->send_header( 'Content-Type', $content_type . '; charset=' . get_option( 'blog_charset' ) );

		/*
		 * Mitigate possible JSONP Flash attacks.
		 *
		 * http://miki.it/blog/2014/7/8/abusing-jsonp-with-rosetta-flash/
		 */
		$this->send_header( 'X-Content-Type-Options', 'nosniff' );
		$this->send_header( 'Access-Control-Expose-Headers', 'X-WP-Total, X-WP-TotalPages' );
		$this->send_header( 'Access-Control-Allow-Headers', 'Authorization' );

		/**
		 * Send nocache headers on authenticated requests.
		 *
		 * @since 4.4.0
		 *
		 * @param bool $rest_send_nocache_headers Whether to send no-cache headers.
		 */
		$send_no_cache_headers = apply_filters( 'rest_send_nocache_headers', is_user_logged_in() );
		if ( $send_no_cache_headers ) {
			foreach ( wp_get_nocache_headers() as $header => $header_value ) {
				$this->send_header( $header, $header_value );
			}
		}

		/**
		 * Filter whether the REST API is enabled.
		 *
		 * @since 4.4.0
		 *
		 * @param bool $rest_enabled Whether the REST API is enabled. Default true.
		 */
		$enabled = apply_filters( 'rest_enabled', true );

		/**
		 * Filter whether jsonp is enabled.
		 *
		 * @since 4.4.0
		 *
		 * @param bool $jsonp_enabled Whether jsonp is enabled. Default true.
		 */
		$jsonp_enabled = apply_filters( 'rest_jsonp_enabled', true );

		$jsonp_callback = null;

		if ( ! $enabled ) {
			echo $this->json_error( 'rest_disabled', __( 'The REST API is disabled on this site.' ), 404 );
			return false;
		}
		if ( isset( $_GET['_jsonp'] ) ) {
			if ( ! $jsonp_enabled ) {
				echo $this->json_error( 'rest_callback_disabled', __( 'JSONP support is disabled on this site.' ), 400 );
				return false;
			}

			// Check for invalid characters (only alphanumeric allowed).
			if ( is_string( $_GET['_jsonp'] ) ) {
				$jsonp_callback = preg_replace( '/[^\w\.]/', '', wp_unslash( $_GET['_jsonp'] ), -1, $illegal_char_count );
				if ( 0 !== $illegal_char_count ) {
					$jsonp_callback = null;
				}
			}
			if ( null === $jsonp_callback ) {
				echo $this->json_error( 'rest_callback_invalid', __( 'The JSONP callback function is invalid.' ), 400 );
				return false;
			}
		}

		if ( empty( $path ) ) {
			if ( isset( $_SERVER['PATH_INFO'] ) ) {
				$path = $_SERVER['PATH_INFO'];
			} else {
				$path = '/';
			}
		}

		$request = new WP_REST_Request( $_SERVER['REQUEST_METHOD'], $path );

		$request->set_query_params( $_GET );
		$request->set_body_params( $_POST );
		$request->set_file_params( $_FILES );
		$request->set_headers( $this->get_headers( $_SERVER ) );
		$request->set_body( $this->get_raw_data() );

		/*
		 * HTTP method override for clients that can't use PUT/PATCH/DELETE. First, we check
		 * $_GET['_method']. If that is not set, we check for the HTTP_X_HTTP_METHOD_OVERRIDE
		 * header.
		 */
		if ( isset( $_GET['_method'] ) ) {
			$request->set_method( $_GET['_method'] );
		} elseif ( isset( $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] ) ) {
			$request->set_method( $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] );
		}

		$result = $this->check_authentication();

		if ( ! is_wp_error( $result ) ) {
			$result = $this->dispatch( $request );
		}

		// Normalize to either WP_Error or WP_REST_Response...
		$result = rest_ensure_response( $result );

		// ...then convert WP_Error across.
		if ( is_wp_error( $result ) ) {
			$result = $this->error_to_response( $result );
		}

		/**
		 * Filter the API response.
		 *
		 * Allows modification of the response before returning.
		 *
		 * @since 4.4.0
		 *
		 * @param WP_HTTP_Response $result  Result to send to the client. Usually a WP_REST_Response.
		 * @param WP_REST_Server   $this    Server instance.
		 * @param WP_REST_Request  $request Request used to generate the response.
		 */
		$result = apply_filters( 'rest_post_dispatch', rest_ensure_response( $result ), $this, $request );

		// Wrap the response in an envelope if asked for.
		if ( isset( $_GET['_envelope'] ) ) {
			$result = $this->envelope_response( $result, isset( $_GET['_embed'] ) );
		}

		// Send extra data from response objects.
		$headers = $result->get_headers();
		$this->send_headers( $headers );

		$code = $result->get_status();
		$this->set_status( $code );

		/**
		 * Filter whether the request has already been served.
		 *
		 * Allow sending the request manually - by returning true, the API result
		 * will not be sent to the client.
		 *
		 * @since 4.4.0
		 *
		 * @param bool             $served  Whether the request has already been served.
		 *                                           Default false.
		 * @param WP_HTTP_Response $result  Result to send to the client. Usually a WP_REST_Response.
		 * @param WP_REST_Request  $request Request used to generate the response.
		 * @param WP_REST_Server   $this    Server instance.
		 */
		$served = apply_filters( 'rest_pre_serve_request', false, $result, $request, $this );

		if ( ! $served ) {
			if ( 'HEAD' === $request->get_method() ) {
				return null;
			}

			// Embed links inside the request.
			$result = $this->response_to_data( $result, isset( $_GET['_embed'] ) );

			$result = wp_json_encode( $result );

			$json_error_message = $this->get_json_last_error();
			if ( $json_error_message ) {
				$json_error_obj = new WP_Error( 'rest_encode_error', $json_error_message, array( 'status' => 500 ) );
				$result = $this->error_to_response( $json_error_obj );
				$result = wp_json_encode( $result->data[0] );
			}

			if ( $jsonp_callback ) {
				// Prepend '/**/' to mitigate possible JSONP Flash attacks
				// http://miki.it/blog/2014/7/8/abusing-jsonp-with-rosetta-flash/
				echo '/**/' . $jsonp_callback . '(' . $result . ')';
			} else {
				echo $result;
			}
		}
		return null;
	}

	/**
	 * Converts a response to data to send.
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @param WP_REST_Response $response Response object.
	 * @param bool             $embed    Whether links should be embedded.
	 * @return array {
	 *     Data with sub-requests embedded.
	 *
	 *     @type array [$_links]    Links.
	 *     @type array [$_embedded] Embeddeds.
	 * }
	 */
	public function response_to_data( $response, $embed ) {
		$data  = $response->get_data();
		$links = $this->get_response_links( $response );

		if ( ! empty( $links ) ) {
			// Convert links to part of the data.
			$data['_links'] = $links;
		}
		if ( $embed ) {
			// Determine if this is a numeric array.
			if ( wp_is_numeric_array( $data ) ) {
				$data = array_map( array( $this, 'embed_links' ), $data );
			} else {
				$data = $this->embed_links( $data );
			}
		}

		return $data;
	}

	/**
	 * Retrieves links from a response.
	 *
	 * Extracts the links from a response into a structured hash, suitable for
	 * direct output.
	 *
	 * @since 4.4.0
	 * @access public
	 * @static
	 *
	 * @param WP_REST_Response $response Response to extract links from.
	 * @return array Map of link relation to list of link hashes.
	 */
	public static function get_response_links( $response ) {
		$links = $response->get_links();

		if ( empty( $links ) ) {
			return array();
		}

		// Convert links to part of the data.
		$data = array();
		foreach ( $links as $rel => $items ) {
			$data[ $rel ] = array();

			foreach ( $items as $item ) {
				$attributes = $item['attributes'];
				$attributes['href'] = $item['href'];
				$data[ $rel ][] = $attributes;
			}
		}

		return $data;
	}

	/**
	 * Embeds the links from the data into the request.
	 *
	 * @since 4.4.0
	 * @access protected
	 *
	 * @param array $data Data from the request.
	 * @return array {
	 *     Data with sub-requests embedded.
	 *
	 *     @type array [$_links]    Links.
	 *     @type array [$_embedded] Embeddeds.
	 * }
	 */
	protected function embed_links( $data ) {
		if ( empty( $data['_links'] ) ) {
			return $data;
		}

		$embedded = array();
		$api_root = rest_url();

		foreach ( $data['_links'] as $rel => $links ) {
			// Ignore links to self, for obvious reasons.
			if ( 'self' === $rel ) {
				continue;
			}

			$embeds = array();

			foreach ( $links as $item ) {
				// Determine if the link is embeddable.
				if ( empty( $item['embeddable'] ) || strpos( $item['href'], $api_root ) !== 0 ) {
					// Ensure we keep the same order.
					$embeds[] = array();
					continue;
				}

				// Run through our internal routing and serve.
				$route = substr( $item['href'], strlen( untrailingslashit( $api_root ) ) );
				$query_params = array();

				// Parse out URL query parameters.
				$parsed = parse_url( $route );
				if ( empty( $parsed['path'] ) ) {
					$embeds[] = array();
					continue;
				}

				if ( ! empty( $parsed['query'] ) ) {
					parse_str( $parsed['query'], $query_params );

					// Ensure magic quotes are stripped.
					if ( get_magic_quotes_gpc() ) {
						$query_params = stripslashes_deep( $query_params );
					}
				}

				// Embedded resources get passed context=embed.
				if ( empty( $query_params['context'] ) ) {
					$query_params['context'] = 'embed';
				}

				$request = new WP_REST_Request( 'GET', $parsed['path'] );

				$request->set_query_params( $query_params );
				$response = $this->dispatch( $request );

				$embeds[] = $this->response_to_data( $response, false );
			}

			// Determine if any real links were found.
			$has_links = count( array_filter( $embeds ) );
			if ( $has_links ) {
				$embedded[ $rel ] = $embeds;
			}
		}

		if ( ! empty( $embedded ) ) {
			$data['_embedded'] = $embedded;
		}

		return $data;
	}

	/**
	 * Wraps the response in an envelope.
	 *
	 * The enveloping technique is used to work around browser/client
	 * compatibility issues. Essentially, it converts the full HTTP response to
	 * data instead.
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @param WP_REST_Response $response Response object.
	 * @param bool             $embed    Whether links should be embedded.
	 * @return WP_REST_Response New response with wrapped data
	 */
	public function envelope_response( $response, $embed ) {
		$envelope = array(
			'body'    => $this->response_to_data( $response, $embed ),
			'status'  => $response->get_status(),
			'headers' => $response->get_headers(),
		);

		/**
		 * Filter the enveloped form of a response.
		 *
		 * @since 4.4.0
		 *
		 * @param array            $envelope Envelope data.
		 * @param WP_REST_Response $response Original response data.
		 */
		$envelope = apply_filters( 'rest_envelope_response', $envelope, $response );

		// Ensure it's still a response and return.
		return rest_ensure_response( $envelope );
	}

	/**
	 * Registers a route to the server.
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @param string $namespace  Namespace.
	 * @param string $route      The REST route.
	 * @param array  $route_args Route arguments.
	 * @param bool   $override   Optional. Whether the route should be overriden if it already exists.
	 *                           Default false.
	 */
	public function register_route( $namespace, $route, $route_args, $override = false ) {
		if ( ! isset( $this->namespaces[ $namespace ] ) ) {
			$this->namespaces[ $namespace ] = array();

			$this->register_route( $namespace, '/' . $namespace, array(
				array(
					'methods' => self::READABLE,
					'callback' => array( $this, 'get_namespace_index' ),
					'args' => array(
						'namespace' => array(
							'default' => $namespace,
						),
						'context' => array(
							'default' => 'view',
						),
					),
				),
			) );
		}

		// Associative to avoid double-registration.
		$this->namespaces[ $namespace ][ $route ] = true;
		$route_args['namespace'] = $namespace;

		if ( $override || empty( $this->endpoints[ $route ] ) ) {
			$this->endpoints[ $route ] = $route_args;
		} else {
			$this->endpoints[ $route ] = array_merge( $this->endpoints[ $route ], $route_args );
		}
	}

	/**
	 * Retrieves the route map.
	 *
	 * The route map is an associative array with path regexes as the keys. The
	 * value is an indexed array with the callback function/method as the first
	 * item, and a bitmask of HTTP methods as the second item (see the class
	 * constants).
	 *
	 * Each route can be mapped to more than one callback by using an array of
	 * the indexed arrays. This allows mapping e.g. GET requests to one callback
	 * and POST requests to another.
	 *
	 * Note that the path regexes (array keys) must have @ escaped, as this is
	 * used as the delimiter with preg_match()
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @return array `'/path/regex' => array( $callback, $bitmask )` or
	 *               `'/path/regex' => array( array( $callback, $bitmask ), ...)`.
	 */
	public function get_routes() {

		/**
		 * Filter the array of available endpoints.
		 *
		 * @since 4.4.0
		 *
		 * @param array $endpoints The available endpoints. An array of matching regex patterns, each mapped
		 *                         to an array of callbacks for the endpoint. These take the format
		 *                         `'/path/regex' => array( $callback, $bitmask )` or
		 *                         `'/path/regex' => array( array( $callback, $bitmask ).
		 */
		$endpoints = apply_filters( 'rest_endpoints', $this->endpoints );

		// Normalise the endpoints.
		$defaults = array(
			'methods'       => '',
			'accept_json'   => false,
			'accept_raw'    => false,
			'show_in_index' => true,
			'args'          => array(),
		);

		foreach ( $endpoints as $route => &$handlers ) {

			if ( isset( $handlers['callback'] ) ) {
				// Single endpoint, add one deeper.
				$handlers = array( $handlers );
			}

			if ( ! isset( $this->route_options[ $route ] ) ) {
				$this->route_options[ $route ] = array();
			}

			foreach ( $handlers as $key => &$handler ) {

				if ( ! is_numeric( $key ) ) {
					// Route option, move it to the options.
					$this->route_options[ $route ][ $key ] = $handler;
					unset( $handlers[ $key ] );
					continue;
				}

				$handler = wp_parse_args( $handler, $defaults );

				// Allow comma-separated HTTP methods.
				if ( is_string( $handler['methods'] ) ) {
					$methods = explode( ',', $handler['methods'] );
				} else if ( is_array( $handler['methods'] ) ) {
					$methods = $handler['methods'];
				} else {
					$methods = array();
				}

				$handler['methods'] = array();

				foreach ( $methods as $method ) {
					$method = strtoupper( trim( $method ) );
					$handler['methods'][ $method ] = true;
				}
			}
		}
		return $endpoints;
	}

	/**
	 * Retrieves namespaces registered on the server.
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @return array List of registered namespaces.
	 */
	public function get_namespaces() {
		return array_keys( $this->namespaces );
	}

	/**
	 * Retrieves specified options for a route.
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @param string $route Route pattern to fetch options for.
	 * @return array|null Data as an associative array if found, or null if not found.
	 */
	public function get_route_options( $route ) {
		if ( ! isset( $this->route_options[ $route ] ) ) {
			return null;
		}

		return $this->route_options[ $route ];
	}

	/**
	 * Matches the request to a callback and call it.
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @param WP_REST_Request $request Request to attempt dispatching.
	 * @return WP_REST_Response Response returned by the callback.
	 */
	public function dispatch( $request ) {
		/**
		 * Filter the pre-calculated result of a REST dispatch request.
		 *
		 * Allow hijacking the request before dispatching by returning a non-empty. The returned value
		 * will be used to serve the request instead.
		 *
		 * @since 4.4.0
		 *
		 * @param mixed           $result  Response to replace the requested version with. Can be anything
		 *                                 a normal endpoint can return, or null to not hijack the request.
		 * @param WP_REST_Server  $this    Server instance.
		 * @param WP_REST_Request $request Request used to generate the response.
		 */
		$result = apply_filters( 'rest_pre_dispatch', null, $this, $request );

		if ( ! empty( $result ) ) {
			return $result;
		}

		$method = $request->get_method();
		$path   = $request->get_route();

		foreach ( $this->get_routes() as $route => $handlers ) {
			$match = preg_match( '@^' . $route . '$@i', $path, $args );

			if ( ! $match ) {
				continue;
			}

			foreach ( $handlers as $handler ) {
				$callback  = $handler['callback'];
				$response = null;

				$checked_method = 'HEAD' === $method ? 'GET' : $method;
				if ( empty( $handler['methods'][ $checked_method ] ) ) {
					continue;
				}

				if ( ! is_callable( $callback ) ) {
					$response = new WP_Error( 'rest_invalid_handler', __( 'The handler for the route is invalid' ), array( 'status' => 500 ) );
				}

				if ( ! is_wp_error( $response ) ) {
					// Remove the redundant preg_match argument.
					unset( $args[0] );

					$request->set_url_params( $args );
					$request->set_attributes( $handler );

					$request->sanitize_params();

					$defaults = array();

					foreach ( $handler['args'] as $arg => $options ) {
						if ( isset( $options['default'] ) ) {
							$defaults[ $arg ] = $options['default'];
						}
					}

					$request->set_default_params( $defaults );

					$check_required = $request->has_valid_params();
					if ( is_wp_error( $check_required ) ) {
						$response = $check_required;
					}
				}

				if ( ! is_wp_error( $response ) ) {
					// Check permission specified on the route.
					if ( ! empty( $handler['permission_callback'] ) ) {
						$permission = call_user_func( $handler['permission_callback'], $request );

						if ( is_wp_error( $permission ) ) {
							$response = $permission;
						} else if ( false === $permission || null === $permission ) {
							$response = new WP_Error( 'rest_forbidden', __( "You don't have permission to do this." ), array( 'status' => 403 ) );
						}
					}
				}

				if ( ! is_wp_error( $response ) ) {
					/**
					 * Filter the REST dispatch request result.
					 *
					 * Allow plugins to override dispatching the request.
					 *
					 * @since 4.4.0
					 *
					 * @param bool            $dispatch_result Dispatch result, will be used if not empty.
					 * @param WP_REST_Request $request         Request used to generate the response.
					 */
					$dispatch_result = apply_filters( 'rest_dispatch_request', null, $request );

					// Allow plugins to halt the request via this filter.
					if ( null !== $dispatch_result ) {
						$response = $dispatch_result;
					} else {
						$response = call_user_func( $callback, $request );
					}
				}

				if ( is_wp_error( $response ) ) {
					$response = $this->error_to_response( $response );
				} else {
					$response = rest_ensure_response( $response );
				}

				$response->set_matched_route( $route );
				$response->set_matched_handler( $handler );

				return $response;
			}
		}

		return $this->error_to_response( new WP_Error( 'rest_no_route', __( 'No route was found matching the URL and request method' ), array( 'status' => 404 ) ) );
	}

	/**
	 * Returns if an error occurred during most recent JSON encode/decode.
	 *
	 * Strings to be translated will be in format like
	 * "Encoding error: Maximum stack depth exceeded".
	 *
	 * @since 4.4.0
	 * @access protected
	 *
	 * @return bool|string Boolean false or string error message.
	 */
	protected function get_json_last_error() {
		// See https://core.trac.wordpress.org/ticket/27799.
		if ( ! function_exists( 'json_last_error' ) ) {
			return false;
		}

		$last_error_code = json_last_error();

		if ( ( defined( 'JSON_ERROR_NONE' ) && JSON_ERROR_NONE === $last_error_code ) || empty( $last_error_code ) ) {
			return false;
		}

		return json_last_error_msg();
	}

	/**
	 * Retrieves the site index.
	 *
	 * This endpoint describes the capabilities of the site.
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @param array $request {
	 *     Request.
	 *
	 *     @type string $context Context.
	 * }
	 * @return array Index entity
	 */
	public function get_index( $request ) {
		// General site data.
		$available = array(
			'name'           => get_option( 'blogname' ),
			'description'    => get_option( 'blogdescription' ),
			'url'            => get_option( 'siteurl' ),
			'namespaces'     => array_keys( $this->namespaces ),
			'authentication' => array(),
			'routes'         => $this->get_data_for_routes( $this->get_routes(), $request['context'] ),
		);

		$response = new WP_REST_Response( $available );

		$response->add_link( 'help', 'http://v2.wp-api.org/' );

		/**
		 * Filter the API root index data.
		 *
		 * This contains the data describing the API. This includes information
		 * about supported authentication schemes, supported namespaces, routes
		 * available on the API, and a small amount of data about the site.
		 *
		 * @since 4.4.0
		 *
		 * @param WP_REST_Response $response Response data.
		 */
		return apply_filters( 'rest_index', $response );
	}

	/**
	 * Retrieves the index for a namespace.
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @param WP_REST_Request $request REST request instance.
	 * @return WP_REST_Response|WP_Error WP_REST_Response instance if the index was found,
	 *                                   WP_Error if the namespace isn't set.
	 */
	public function get_namespace_index( $request ) {
		$namespace = $request['namespace'];

		if ( ! isset( $this->namespaces[ $namespace ] ) ) {
			return new WP_Error( 'rest_invalid_namespace', __( 'The specified namespace could not be found.' ), array( 'status' => 404 ) );
		}

		$routes = $this->namespaces[ $namespace ];
		$endpoints = array_intersect_key( $this->get_routes(), $routes );

		$data = array(
			'namespace' => $namespace,
			'routes' => $this->get_data_for_routes( $endpoints, $request['context'] ),
		);
		$response = rest_ensure_response( $data );

		// Link to the root index.
		$response->add_link( 'up', rest_url( '/' ) );

		/**
		 * Filter the namespace index data.
		 *
		 * This typically is just the route data for the namespace, but you can
		 * add any data you'd like here.
		 *
		 * @since 4.4.0
		 *
		 * @param WP_REST_Response $response Response data.
		 * @param WP_REST_Request  $request  Request data. The namespace is passed as the 'namespace' parameter.
		 */
		return apply_filters( 'rest_namespace_index', $response, $request );
	}

	/**
	 * Retrieves the publicly-visible data for routes.
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @param array  $routes  Routes to get data for.
	 * @param string $context Optional. Context for data. Accepts 'view' or 'help'. Default 'view'.
	 * @return array Route data to expose in indexes.
	 */
	public function get_data_for_routes( $routes, $context = 'view' ) {
		$available = array();

		// Find the available routes.
		foreach ( $routes as $route => $callbacks ) {
			$data = $this->get_data_for_route( $route, $callbacks, $context );
			if ( empty( $data ) ) {
				continue;
			}

			/**
			 * Filter the REST endpoint data.
			 *
			 * @since 4.4.0
			 *
			 * @param WP_REST_Request $request Request data. The namespace is passed as the 'namespace' parameter.
			 */
			$available[ $route ] = apply_filters( 'rest_endpoints_description', $data );
		}

		/**
		 * Filter the publicly-visible data for routes.
		 *
		 * This data is exposed on indexes and can be used by clients or
		 * developers to investigate the site and find out how to use it. It
		 * acts as a form of self-documentation.
		 *
		 * @since 4.4.0
		 *
		 * @param array $available Map of route to route data.
		 * @param array $routes    Internal route data as an associative array.
		 */
		return apply_filters( 'rest_route_data', $available, $routes );
	}

	/**
	 * Retrieves publicly-visible data for the route.
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @param string $route     Route to get data for.
	 * @param array  $callbacks Callbacks to convert to data.
	 * @param string $context   Optional. Context for the data. Accepts 'view' or 'help'. Default 'view'.
	 * @return array|null Data for the route, or null if no publicly-visible data.
	 */
	public function get_data_for_route( $route, $callbacks, $context = 'view' ) {
		$data = array(
			'namespace' => '',
			'methods' => array(),
			'endpoints' => array(),
		);

		if ( isset( $this->route_options[ $route ] ) ) {
			$options = $this->route_options[ $route ];

			if ( isset( $options['namespace'] ) ) {
				$data['namespace'] = $options['namespace'];
			}

			if ( isset( $options['schema'] ) && 'help' === $context ) {
				$data['schema'] = call_user_func( $options['schema'] );
			}
		}

		$route = preg_replace( '#\(\?P<(\w+?)>.*?\)#', '{$1}', $route );

		foreach ( $callbacks as $callback ) {
			// Skip to the next route if any callback is hidden.
			if ( empty( $callback['show_in_index'] ) ) {
				continue;
			}

			$data['methods'] = array_merge( $data['methods'], array_keys( $callback['methods'] ) );
			$endpoint_data = array(
				'methods' => array_keys( $callback['methods'] ),
			);

			if ( isset( $callback['args'] ) ) {
				$endpoint_data['args'] = array();
				foreach ( $callback['args'] as $key => $opts ) {
					$arg_data = array(
						'required' => ! empty( $opts['required'] ),
					);
					if ( isset( $opts['default'] ) ) {
						$arg_data['default'] = $opts['default'];
					}
					if ( isset( $opts['enum'] ) ) {
						$arg_data['enum'] = $opts['enum'];
					}
					if ( isset( $opts['description'] ) ) {
						$arg_data['description'] = $opts['description'];
					}
					$endpoint_data['args'][ $key ] = $arg_data;
				}
			}

			$data['endpoints'][] = $endpoint_data;

			// For non-variable routes, generate links.
			if ( strpos( $route, '{' ) === false ) {
				$data['_links'] = array(
					'self' => rest_url( $route ),
				);
			}
		}

		if ( empty( $data['methods'] ) ) {
			// No methods supported, hide the route.
			return null;
		}

		return $data;
	}

	/**
	 * Sends an HTTP status code.
	 *
	 * @since 4.4.0
	 * @access protected
	 *
	 * @param int $code HTTP status.
	 */
	protected function set_status( $code ) {
		status_header( $code );
	}

	/**
	 * Sends an HTTP header.
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @param string $key Header key.
	 * @param string $value Header value.
	 */
	public function send_header( $key, $value ) {
		/*
		 * Sanitize as per RFC2616 (Section 4.2):
		 *
		 * Any LWS that occurs between field-content MAY be replaced with a
		 * single SP before interpreting the field value or forwarding the
		 * message downstream.
		 */
		$value = preg_replace( '/\s+/', ' ', $value );
		header( sprintf( '%s: %s', $key, $value ) );
	}

	/**
	 * Sends multiple HTTP headers.
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @param array $headers Map of header name to header value.
	 */
	public function send_headers( $headers ) {
		foreach ( $headers as $key => $value ) {
			$this->send_header( $key, $value );
		}
	}

	/**
	 * Retrieves the raw request entity (body).
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @global string $HTTP_RAW_POST_DATA Raw post data.
	 *
	 * @return string Raw request data.
	 */
	public static function get_raw_data() {
		global $HTTP_RAW_POST_DATA;

		/*
		 * A bug in PHP < 5.2.2 makes $HTTP_RAW_POST_DATA not set by default,
		 * but we can do it ourself.
		 */
		if ( ! isset( $HTTP_RAW_POST_DATA ) ) {
			$HTTP_RAW_POST_DATA = file_get_contents( 'php://input' );
		}

		return $HTTP_RAW_POST_DATA;
	}

	/**
	 * Extracts headers from a PHP-style $_SERVER array.
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @param array $server Associative array similar to `$_SERVER`.
	 * @return array Headers extracted from the input.
	 */
	public function get_headers( $server ) {
		$headers = array();

		// CONTENT_* headers are not prefixed with HTTP_.
		$additional = array( 'CONTENT_LENGTH' => true, 'CONTENT_MD5' => true, 'CONTENT_TYPE' => true );

		foreach ( $server as $key => $value ) {
			if ( strpos( $key, 'HTTP_' ) === 0 ) {
				$headers[ substr( $key, 5 ) ] = $value;
			} elseif ( isset( $additional[ $key ] ) ) {
				$headers[ $key ] = $value;
			}
		}

		return $headers;
	}
}
