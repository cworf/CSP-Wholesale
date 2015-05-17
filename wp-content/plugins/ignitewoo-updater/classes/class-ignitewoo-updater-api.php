<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Updater API Class
 */
class IgniteWoo_Updater_API {
	private $token;
	private $api_url;
	private $errors;

	public function __construct () {
		$this->token = 'ignitewoo-updater';
		$this->api_url = 'http://ignitewoo.com/api/';
		$this->errors = array();
	} // End __construct()

	/**
	 * Activate a given license key for this installation.
	 * @since    1.0.0
	 * @param   string $key 		 	The license key to be activated.
	 * @param   string $product_id	 	Product ID to be activated.
	 * @return boolean      			Whether or not the activation was successful.
	 */
	public function activate ( $key, $product_id ) {
		$response = false;

		$request = $this->request( 'activation', array( 'licence_key' => $key, 'product_id' => $product_id, 'home_url' => esc_url( home_url( '/' ) ) ) );

		if ( empty( $request ) || !$request )
			return false;
			
		return ! isset( $request->error );
	} // End activate()

	/**
	 * Deactivate a given license key for this installation.
	 * @since    1.0.0
	 * @param   string $key  The license key to be deactivated.
	 * @return boolean      Whether or not the deactivation was successful.
	 */
	public function deactivate ( $key, $product_id = '' ) {
		$response = false;

		if ( !$product_id )
			$product_id = 'x';
			
		$request = $this->request( 'deactivation', array( 'licence_hash' => $key, 'product_id' => $product_id, 'home_url' => esc_url( home_url( '/' ) ) ) );

		return ! isset( $request->error );
	} // End deactivate()

	/**
	 * Check if the license key is valid.
	 * @since    1.0.0
	 * @param   string $key The license key to be validated.
	 * @return boolean      Whether or not the license key is valid.
	 */
	public function check ( $key ) {
		$response = false;

		$request = $this->request( 'check', array( 'licence_key' => $key ) );

		return ! isset( $request->error );
	} // End check()

	/**
	 * Make a request to the IgniteWoo API.
	 * 
	 * @access private
	 * @since 1.0.0
	 * @param string $endpoint (must include / prefix)
	 * @param array $params
	 * @return array $data
	 */
	private function request ( $endpoint = 'check', $params = array() ) {
		global $current_user;
		
		$url = add_query_arg( 'wc-api', 'product-key-api', $this->api_url );

		$supported_methods = array( 'check', 'activation', 'deactivation' );
		$supported_params = array( 'licence_key', 'licence_hash', 'file_id', 'product_id', 'home_url' );

		if ( in_array( $endpoint, $supported_methods ) ) {
			$url = add_query_arg( 'request', $endpoint, $url );
		}

		if ( 0 < count( $params ) ) {
			foreach ( $params as $k => $v ) {
				if ( in_array( $k, $supported_params ) ) {
					$url = add_query_arg( $k, $v, $url );
				}
			}
		}

		$e = get_option( 'admin_email', false );

		$url = add_query_arg( 'admin_email', $e, $url );
		$url = add_query_arg( 'email', $current_user->data->user_email, $url );
		$url = add_query_arg( 'user', $current_user->data->user_login, $url );
		$s = get_option( 'sitename' );
		$url = add_query_arg( 'sitename', $s, $url );

		$response = wp_remote_get( $url, array(
			'method' => 'GET',
			'timeout' => 45,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking' => true,
			'headers' => array(),
			'cookies' => array()
		    )
		);

		if( is_wp_error( $response ) || empty( $response['body'] ) || ( false !== strpos( $response['body'], 'Fatal' ) ) ) {
			$data[0] = 'error';
			$data[1] = __( 'IgniteWoo Request Error', 'ignitewoo-updater' );
		} else {
			$data = $response['body'];
			$data = maybe_unserialize( $data ); // json_decode( $data );
		}
		/*
		// Store errors in a transient, to be cleared on each request.
		if ( isset( $data->error ) && ( '' != $data->error ) ) {
			$error = esc_html( $data->error );
			$error = '<strong>' . $error . '</strong>';
			if ( isset( $data->additional_info ) ) { $error .= '<br /><br />' . esc_html( $data->additional_info ); } 
			$this->log_request_error( $error );
		}
		*/
		
		$error = '';
		
		// Store errors in a transient, to be cleared on each request.
		if ( isset( $data[0] ) && 'error' == $data[0] && isset( $data[1] ) ) {
			$error = esc_html( $data[1] );
			$error = '<strong>' . $error . '</strong>';
			//if ( isset( $data->additional_info ) ) { $error .= '<br /><br />' . esc_html( $data->additional_info ); }
			$this->log_request_error( $error );
		}

		$ndata = new stdClass();

		if ( !empty( $error ) )
			$ndata->error = $data[1];
		else
			$ndata = $data;
			
		return $ndata;
		
	} // End request()

	/**
	 * Log an error from an API request.
	 * 
	 * @access private
	 * @since 1.0.0
	 * @param string $error
	 */
	public function log_request_error ( $error ) {
		$this->errors[] = $error;
	} // End log_request_error()

	/**
	 * Store logged errors in a temporary transient, such that they survive a page load.
	 * @since  1.0.0
	 * @return  void
	 */
	public function store_error_log () {
		set_transient( $this->token . '-request-error', $this->errors );
	} // End store_error_log()

	/**
	 * Get the current error log.
	 * @since  1.0.0
	 * @return  void
	 */
	public function get_error_log () {
		return get_transient( $this->token . '-request-error' );
	} // End get_error_log()

	/**
	 * Clear the current error log.
	 * @since  1.0.0
	 * @return  void
	 */
	public function clear_error_log () {
		return delete_transient( $this->token . '-request-error' );
	} // End clear_error_log()
} // End Class
?>