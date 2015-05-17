<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Self Updater Class
 */
class IgniteWoo_Updater_Self_Updater {
	public $file;
	private $api_url;

	/**
	 * __construct function.
	 * 
	 * @access public
	 * @return void
	 */
	public function __construct ( $file ) {
		//global $woodojo;

		$this->api_url = 'http://ignitewoo.com/api/';
		$this->file = plugin_basename( $file );

		// Check For Updates
		add_filter( 'pre_set_site_transient_update_plugins', array( &$this, 'update_check' ) );

		// Check For Plugin Information
		add_filter( 'plugins_api', array( &$this, 'plugin_information' ), 10, 3 );
	} // End __construct()

	/**
	 * update_check function.
	 * 
	 * @access public
	 * @param object $transient
	 * @return object $transient
	 */
	public function update_check ( $transient ) {
		// Check if the transient contains the 'checked' information
		// If no, just return its value without hacking it
		if( empty( $transient->checked ) )
		    return $transient;

		// The transient contains the 'checked' information
		// Now append to it information form your own API
		$args = array(
				'request' => 'update_check',
				'plugin_name' => $this->file,
				'version' => $transient->checked[$this->file],
				'product_id' => $this->product_id,
				'file_id' => $this->product_id,
				'licence_hash' => $this->licence_hash,
				'home_url' => trailingslashit( esc_url( home_url( '/' ) ) )
		);

		// Send request checking for an update
		$response = $this->request( $args );

		// If response is false, don't alter the transient
		if( false !== $response ) {
			$transient->response[$this->file] = $response;
		}
		return $transient;
	} // End update_check()
	
	/**
	 * plugin_information function.
	 * 
	 * @access public
	 * @return object $response
	 */
	public function plugin_information ( $false, $action, $args ) {	
		$transient = get_site_transient( 'update_plugins' );

		// Check if this plugins API is about this plugin
		if( $args->slug != $this->file ) {
			return $false;
		}

		// POST data to send to your API
		$args = array(
			'request' => 'get_plugin_information',
			'plugin_name' => $this->file, 
			'version' => $transient->checked[$this->file],
			'product_id' => $this->product_id,
			'file_id' => $this->product_id,
			'licence_hash' => $this->licence_hash,
			'home_url' => trailingslashit( esc_url( home_url( '/' ) ) )
		);

		// Send request for detailed information
		$response = $this->request( $args );

		$response->sections = (array)$response->sections;
		$response->compatibility = (array)$response->compatibility;
		$response->tags = (array)$response->tags;
		$response->contributors = (array)$response->contributors;

		if ( count( $response->compatibility ) > 0 ) {
			foreach ( $response->compatibility as $k => $v ) {
				$response->compatibility[$k] = (array)$v;
			}
		}

		return $response;
	} // End plugin_information()

	/**
	 * request function.
	 * 
	 * @access public
	 * @param array $args
	 * @return object $response or boolean false
	 */
	public function request ( $args ) {
		// Send request

		$args['wc-api'] = 'product-key-api';

		$request = wp_remote_post( $this->api_url, array(
				'method' => 'POST',
				'timeout' => 45,
				'redirection' => 5,
				'httpversion' => '1.0',
				'blocking' => true,
				'headers' => array(),
				'body' => $args,
				'cookies' => array(),
				'sslverify' => false
			) );

		// Make sure the request was successful
		if( is_wp_error( $request ) or wp_remote_retrieve_response_code( $request ) != 200 ) {
			// Request failed
			return false;
		}

		// Read server response, which should be an object
		if ( $request != '' ) {
			$response = maybe_unserialize( wp_remote_retrieve_body( $request ) ); 
		} else {
			$response = false;
		}

		if ( is_object( $response ) ) {
			return $response;
		} else {
			return false;
		}
	} // End prepare_request()
} // End Class
?>