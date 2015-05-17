<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Single Updater Class
 */
class IgniteWoo_Updater_Update_Checker {
	private $file;
	private $api_url;
	private $product_id;
	private $file_id;
	private $license_hash;

	/**
	 * Constructor.
	 * 
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function __construct ( $file, $product_id, $file_id, $license_hash ) {
		$this->api_url = 'http://ignitewoo.com/api/?api=installer-api&';
		$this->file = $file;
		$this->product_id = $product_id;
		$this->file_id = $file_id;
		$this->license_hash = $license_hash;

		// Check For Updates
		add_filter( 'pre_set_site_transient_update_plugins', array( &$this, 'update_check' ) );

		// Check For Plugin Information
		add_filter( 'plugins_api', array( &$this, 'plugin_information' ), 10, 3 );
	} // End __construct()

	/**
	 * Check for updates against the remote server.
	 * 
	 * @access public
	 * @since  1.0.0
	 * @param  object $transient
	 * @return object $transient
	 */
	public function update_check ( $transient ) {
		global $ignitewoo_updater;
		// Check if the transient contains the 'checked' information
		// If no, just return its value without hacking it
		if ( empty( $transient->checked ) )
			return $transient;

		// The transient contains the 'checked' information
		// Now append to it information form your own API

		$args = array(
			'request' => 'update_check',
			'plugin_name' => $this->file,
			'version' => isset( $transient->checked[$this->file] ) ? $transient->checked[$this->file] : '',
			'product_id' => $this->product_id,
			'file_id' => $this->file_id,
			'licence_hash' => $this->license_hash,
			'home_url' => trailingslashit( esc_url( home_url( '/' ) ) )
		);

		// Send request checking for an update
		$response = $this->request( $args );

		// If response is false, don't alter the transient
		if ( false !== $response ) {

			// Store the upgrade notice, if it exists so it can be displayed to the admin on the plugins page
			if ( !empty( $response->upgrade_notice ) ) {
			
				$notice_files = get_transient( 'ignitewoo_upgrade_notice_files' );

				$notice_files = maybe_unserialize( $notice_files );
				
				$notice_files[] = $this->file;
				
				$notice_files = array_unique( $notice_files );

				set_transient( 'ignitewoo_upgrade_notice_files', $notice_files, ( 60 * 60 * 12 ) );

			}
			
			if ( isset( $response->errors ) && isset ( $response->errors->ignitewoo_updater_api_license_deactivated ) ) {

				$this->error_msg = $response->msg;

				add_action('admin_notices', array( &$this, 'error_notice_for_deactivated_plugin') );

				$ignitewoo_updater->admin->deactivate_product( $this->file, $this->license_hash );
				
				delete_option( 'plugin_err_' . plugin_basename( $this->file ) );

				update_option( 'plugin_err_' . plugin_basename( $this->file ), $response->msg );

				// Remove transient key value for the file
				$notice_files = get_transient( 'ignitewoo_upgrade_notice_files' );
				
				$notice_files = maybe_unserialize( $notice_files );

				foreach( $notice_files as $k => $f ) { 
					if ( $f == $this->file )
						unset( $notice_files[ $k ] );
				}
				
				$notices_files = array_unique( $notice_files );
				
				set_transient( 'ignitewoo_upgrade_notice_files', $notice_files, ( 60 * 60 * 12 ) );
				
			} else {

				if ( !empty( $response->msg ) ) {
				
					$this_plugin_base = plugin_basename( $this->file );
					
					update_option( 'plugin_err_' . plugin_basename( $this->file ), $response->msg );
					
				} else {
				
					delete_option( 'plugin_err_' . plugin_basename( $this->file ) );
					
				}

				$transient->response[$this->file] = $response;
			
			}

		} else {

			delete_option( 'plugin_err_' . plugin_basename( $this->file ) );
		
		}
		
		return $transient;
	} // End update_check()
	
	/**
	 * Display an error notice 
	 * @param  strin $message The message 
	 * @return void
	 */
	public function error_notice_for_deactivated_plugin( $message ){
		
		$plugins = get_plugins();

		$plugin_name = isset( $plugins[$this->file] ) ? $plugins[$this->file]['Name'] : $this->file;

		echo '<div id="message" class="error"><p>';
		
		echo sprintf( 'The license for the plugin %s has been deactivated. You can inspect the license on your <a href="https://ignitewoo.com/my-account/" target="_blank">My Account page at IgniteWoo.com</a> or set a new license key in your Dashboard at <a href="%s">Dashboard -> IgniteWoo Licenses</a>.', $plugin_name, admin_url('index.php?page=ignitewoo-licenses' ) ) ;
		
		_e( 'The IgniteWoo API responded: ' );

		echo $this->error_msg . ' ( file: ' . $this->file . ')';
		
		echo '</p>';

		echo '</div>';
		
	}
	/**
	 * Check for the plugin's data against the remote server.
	 * 
	 * @access public
	 * @since  1.0.0
	 * @return object $response
	 */
	public function plugin_information ( $false, $action, $args ) {	
		$transient = get_site_transient( 'update_plugins' );

		// Check if this plugins API is about this plugin
		//if( $args->slug != dirname( $this->file ) ) {
		if ( $args->slug != $this->file ) {
			return $false;
		}

		// POST data to send to your API
		$args = array(
			'request' => 'get_plugin_information',
			'plugin_name' => $this->file, 
			'version' => $transient->checked[$this->file], 
			'product_id' => $this->product_id,
			'file_id' => $this->file_id, 
			'licence_hash' => $this->license_hash,
			'home_url' => esc_url( home_url( '/' ) )
		);

		// Send request for detailed information
		$response = $this->request( $args );

		$response->sections = (array)$response->sections;
		$response->compatibility = isset( $response->compatibility ) ? (array)$response->compatibility : array();
		$response->tags = isset( $response->tags ) ? (array)$response->tags : array();
		$response->contributors = isset( $this->contributors ) ? (array)$response->contributors : array();

		if ( count( $response->compatibility ) > 0 ) {
			foreach ( $response->compatibility as $k => $v ) {
				$response->compatibility[$k] = (array)$v;
			}
		}

		return $response;
	} // End plugin_information()

	/**
	 * Generic request helper.
	 * 
	 * @access private
	 * @since  1.0.0
	 * @param  array $args
	 * @return object $response or boolean false
	 */
	private function request ( $args ) {

		$args['wc-api'] = 'product-key-api';
		// Send request
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
		if( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) != 200 ) {
			// Request failed
			return false;
		}
		// Read server response, which should be an object
		if ( $request != '' ) {
			$response = maybe_unserialize( wp_remote_retrieve_body( $request ) ); // json_decode( wp_remote_retrieve_body( $request ) );
		} else {
			$response = false;
		}


		if ( is_object( $response ) && !empty( $response->sitewide_notice ) ) {
			update_option( 'ignitewoo_sitewide_notice', array( 'id' => $response->sitewide_notice_id, 'msg' => $response->sitewide_notice ) );
		}
		
		if ( is_object( $response ) ) {
			return $response;
		//if( is_object( $response ) && isset( $response->payload ) ) {
		//	return $response->payload;
		} else {
			// Unexpected response
			return false;
		}
	} // End prepare_request()
} // End Class
?>