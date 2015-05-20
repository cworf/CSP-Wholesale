<?php
/*
Plugin Name: WooCommerce Xero Integration
Plugin URI: http://woothemes.com/woocommerce
Description: Integrates <a href="http://www.woothemes.com/woocommerce" target="_blank" >WooCommerce</a> with the <a href="http://www.xero.com" target="_blank">Xero</a> accounting software.
Author: WooThemes
Author URI: http://www.woothemes.com
Version: 1.5.0
Text Domain: wc-xero
Domain Path: /languages/
*/
/*  Copyright 2014  WooThemes

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/

/**
 * Required functions
 */
if ( ! function_exists( 'woothemes_queue_update' ) )
	require_once( 'woo-includes/woo-functions.php' );

/**
 * Plugin updates
 */
woothemes_queue_update( plugin_basename( __FILE__ ), 'f0dd29d338d3c67cf6cee88eddf6869b', '18733' );

if ( is_woocommerce_active() ) {

	if ( class_exists( 'WC_Xero' ) ) return;

	/**
	 * Localisation
	 */
	load_plugin_textdomain( 'wc-xero', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

	add_action('plugins_loaded', 'woocommerce_xero_init', 0);

	function woocommerce_xero_init() {
		$GLOBALS['woocommerce_xero'] = new WC_Xero;
	}


	/**
	 * Plugin page links
	 */
	function wc_xero_plugin_links( $links ) {
	
		$plugin_links = array(
			'<a href="' . admin_url( 'admin.php?page=woocommerce_xero' ) . '">' . __( 'Settings', 'wc-xero' ) . '</a>',
			'<a href="http://www.woothemes.com/support/">' . __( 'Support', 'wc-xero' ) . '</a>',
			'<a href="http://docs.woothemes.com/document/xero/">' . __( 'Documentation', 'wc-xero' ) . '</a>',
		);
	
		return array_merge( $plugin_links, $links );
	}
	
	add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'wc_xero_plugin_links' );
	


	/**
	 * Xero Class
	 **/
	class WC_Xero {

		const VERSION = "1.5.0";
		var $log = '';
		var $id = 'wc_xero';
		private $logger;


	   /**
	    * __consturct()
	    *
	    * @access public
	    * @return void
	    */
		public function __construct() {

			// Get setting values
			$this->consumer_key			= get_option('wc_xero_consumer_key');
			$this->consumer_secret		= get_option('wc_xero_consumer_secret');
			$this->private_key			= get_option('wc_xero_private_key');
			$this->public_key			= get_option('wc_xero_public_key');
			$this->sales_account		= get_option('wc_xero_sales_account');
			$this->discount_account		= get_option('wc_xero_discount_account');
			$this->shipping_account		= get_option('wc_xero_shipping_account');
			$this->payment_account		= get_option('wc_xero_payment_account');
			$this->orders_with_zero		= get_option('wc_xero_export_zero_amount');
			$this->send_invoices	    = get_option('wc_xero_send_invoices');
			$this->send_payments		= get_option('wc_xero_send_payments');
			$this->send_inventory		= get_option('wc_xero_send_inventory');
			$this->debug				= get_option('wc_xero_debug');

			// Xero API configuration
			$this->xro_app_type			= 'Private';
			$this->oath_callback		= 'oob';
			$this->api_endpoint			= 'https://api.xero.com/api.xro/2.0/';
			$this->signatures 			= array( 'consumer_key'     => get_option('wc_xero_consumer_key'),
						              	      	 'shared_secret'    => get_option('wc_xero_consumer_secret'),
						                	     'rsa_private_key'	=> get_option('wc_xero_private_key'),
						                     	 'rsa_public_key'	=> get_option('wc_xero_public_key'),
						                     	 'oauth_secret'     => get_option('wc_xero_consumer_secret'),
						                     	 'oauth_token'		=> get_option('wc_xero_consumer_key'));
			// Plugin compatibility
			require_once( 'classes/class-growdev-wc-plugin-compatibility.php' );
			

			// Actions
			add_action('admin_init',array( $this, 'settings_init') );
			add_action('admin_menu',array( $this, 'menu') );

			// Add options to Order Actions meta box
			add_action('woocommerce_order_actions', array( $this, 'xero_order_actions' ));


			// Order Actions callbacks
			add_action('woocommerce_order_action_xero_manual_invoice', array( $this, 'xero_manual_invoice' ));
			add_action('woocommerce_order_action_xero_manual_payment', array( $this, 'xero_manual_payment'));

			if ( $this->send_invoices == 'on' ) {
				add_action('woocommerce_order_status_completed',array(&$this,'xero_new_invoice'));
			}

			// different app method defaults
			$this->xro_defaults = array( 'xero_url'     => 'https://api.xero.com/api.xro/2.0',
			                     'site'    => 'https://api.xero.com',
			                     'authorize_url'    => 'https://api.xero.com/oauth/Authorize',
			                     'signature_method'    => 'HMAC-SHA1');

			$this->xro_private_defaults = array( 'xero_url'     => 'https://api.xero.com/api.xro/2.0',
			                     'site'    => 'https://api.xero.com',
			                     'authorize_url'    => 'https://api.xero.com/oauth/Authorize',
			                     'signature_method'    => 'RSA-SHA1');

       }

	   /**
		* menu()
		*
		* @access public
		* @return void
		*/
		function menu() {
			$show_in_menu = current_user_can('manage_woocommerce') ? 'woocommerce' : false;
			add_submenu_page($show_in_menu, __('Xero', 'wc-xero'),  __('Xero', 'wc-xero') , 'manage_woocommerce', 'woocommerce_xero', array(&$this,'options_page'));
		}

	   /**
		* options_page()
		*
		* @access public
		* @return void
		*/
		function options_page() { ?>
			<div class="wrap woocommerce">
				<form method="post" id="mainform" action="options.php">
					<div class="icon32 icon32-woocommerce-settings" id="icon-woocommerce"><br /></div>
					<h2><?php _e('Xero for WooCommerce','wc_xero'); ?></h2>

					<?php
						if (isset($_GET['settings-updated']) && ($_GET['settings-updated'] == 'true')){
							 echo '<div id="message" class="updated fade"><p><strong>' . __( 'Your settings have been saved.', 'wc-xero' ) . '</strong></p></div>';

						} else if (isset($_GET['settings-updated']) && ($_GET['settings-updated'] == 'false')){
							 echo '<div id="message" class="error fade"><p><strong>' . __( 'There was an error saving your settings.', 'wc-xero' ) . '</strong></p></div>';
						}
					?>

					<?php settings_fields('woocommerce_xero'); ?>
					<?php do_settings_sections('woocommerce_xero'); ?>
					<p class="submit"><input type="submit" class="button-primary" value="Save" /></p>
				</form>
			</div>
		<?php }

	   /**
		* settings_init()
		*
		* @access public
		* @return void
		*/
		function settings_init() {
			global $woocommerce;
			wp_enqueue_style('woocommerce_admin_styles', $woocommerce->plugin_url().'/assets/css/admin.css');

			$settings = array(
				array(
					'name'		=> 'wc_xero_settings',
					'title' 	=> __('Xero Settings','wc-xero'),
					'page'		=> 'woocommerce_xero',
					'settings'	=> array(
						array(
							'name'		=> 'wc_xero_consumer_key',
							'title'		=> __('Consumer Key','wc-xero'),
						),
						array(
							'name'		=> 'wc_xero_consumer_secret',
							'title'		=> __('Consumer Secret','wc-xero'),
						),
						array(
							'name'		=> 'wc_xero_private_key',
							'title'		=> __('Private Key','wc-xero'),
						),
						array(
							'name'		=> 'wc_xero_public_key',
							'title'		=> __('Public Key','wc-xero'),
						),
						array(
							'name'		=> 'wc_xero_sales_account',
							'title'		=> __('Sales Account','wc-xero'),
						),
						array(
							'name'		=> 'wc_xero_discount_account',
							'title'		=> __('Discount Account','wc-xero'),
						),
						array(
							'name'		=> 'wc_xero_shipping_account',
							'title'		=> __('Shipping Account','wc-xero'),
						),
						array(
							'name'		=> 'wc_xero_payment_account',
							'title'		=> __('Payment Account','wc-xero'),
						),
						array(
							'name'		=> 'wc_xero_send_invoices',
							'title'		=> __('Auto Send Invoices','wc-xero'),
						),
						array(
							'name'		=> 'wc_xero_send_payments',
							'title'		=> __('Auto Send Payments','wc-xero'),
						),
						array(
							'name'		=> 'wc_xero_send_inventory',
							'title'		=> __('Send Inventory Items','wc-xero'),
						),
						array(
							'name'		=> 'wc_xero_export_zero_amount',
							'title'		=> __('Orders with zero total','wc-xero'),
						),
						array(
							'name'		=> 'wc_xero_debug',
							'title'		=> __('Debug','wc-xero'),
						),
					),
				),
			);


			foreach($settings as $sections=>$section) {
				add_settings_section($section['name'],$section['title'],array(&$this,$section['name']),$section['page']);
				foreach($section['settings'] as $setting=>$option) {
					add_settings_field($option['name'],$option['title'],array(&$this,$option['name']),$section['page'],$section['name']);
					register_setting($section['page'],$option['name']);
					$this->$option['name'] = get_option($option['name']);
				}
			}

		}


		/**
		 * START: Admin Settings callbacks
		 */
		function wc_xero_settings(){ echo '<p>'. __( 'Settings for your Xero account including security keys and default account numbers.<br/> <strong>All</strong> text fields are required for the integration to work properly.', 'wc-xero'). '</p>'; }
		function wc_xero_consumer_key(){ echo '<input type="text"  style="width: 300px;" name="wc_xero_consumer_key" id="wc_xero_consumer_key" value="'.get_option('wc_xero_consumer_key').'" />';
											echo '<p class="description">OAuth Credential retrieved from <a href="http://api.xero.com" target="_blank">Xero Developer Centre</a>.</p>'; }
		function wc_xero_consumer_secret(){ echo '<input type="text" style="width: 300px;" name="wc_xero_consumer_secret" id="wc_xero_consumer_secret" value="'.get_option('wc_xero_consumer_secret').'" />';
											echo '<p class="description">OAuth Credential retrieved from <a href="http://api.xero.com" target="_blank">Xero Developer Centre</a>.</p>'; }
		function wc_xero_private_key(){  
			echo '<input type="text" style="width: 300px;" name="wc_xero_private_key" id="wc_xero_private_key" value="'.get_option('wc_xero_private_key').'" />';
			echo '<p class="description">Path to the private key file created to authenticate this site with Xero.</p>'; 
			
			if ( is_file( get_option('wc_xero_private_key') ) ) {
				echo '<p><span style="padding: .5em; background-color: #4AB915; color: #fff; font-weight: bold;">' . __('Key file found.', 'wc-xero') . '</span></p>';			
			} else {
				echo '<p><span style="padding: .5em; background-color: #bc0b0b; color: #fff; font-weight: bold;">' . __('Key file not found.','wc-xero') . '</span></p>';
				$working_dir = str_replace( 'wp-admin', '', getcwd()) ; 
				echo  '<p>' . __('  This setting should include the absolute path to the file which might include working directory: ','wc-xero') . '<span class="code" style="background: #efefef;">'. $working_dir . '</span></p>';
			}
											
		}
		function wc_xero_public_key(){  
			echo '<input type="text" style="width: 300px; " name="wc_xero_public_key" id="wc_xero_public_key" value="' . get_option('wc_xero_public_key').'" />';
			echo '<p class="description">Path to the public certificate file created to authenticate this site with Xero.</p>'; 
			
			if ( is_file( get_option('wc_xero_public_key') ) ) {
				echo '<p><span style="padding: .5em; background-color: #4AB915; color: #fff; font-weight: bold;">' . __('Key file found.', 'wc-xero') . '</span></p>';			
			} else {
				echo '<p><span style="padding: .5em; background-color: #bc0b0b; color: #fff; font-weight: bold;">' . __('Key file not found.','wc-xero') . '</span></p>';
				$working_dir = str_replace( 'wp-admin', '', getcwd()) ; 
				echo  '<p>' . __('  This setting should include the absolute path to the file which might include working directory: ','wc-xero') . '<span class="code" style="background: #efefef;">'. $working_dir . '</span></p>';
			}
		}
		function wc_xero_sales_account(){ echo '<input type="text" name="wc_xero_sales_account" id="wc_xero_sales_account" value="'
                                            .get_option('wc_xero_sales_account').'" />';
											echo '<p class="description">Code for Xero account to track sales.</p>'; }
		function wc_xero_discount_account(){ echo '<input type="text" name="wc_xero_discount_account" id="wc_xero_discount_account" value="'
                                            .get_option('wc_xero_discount_account').'" />';
											echo '<p class="description">Code for Xero account to track customer discounts.</p>'; }
		function wc_xero_shipping_account(){ echo '<input type="text" name="wc_xero_shipping_account" id="wc_xero_shipping_account" value="'
                                            .get_option('wc_xero_shipping_account').'" />';
											echo '<p class="description">Code for Xero account to track shipping charges.</p>'; }
		function wc_xero_payment_account(){ echo '<input type="text" name="wc_xero_payment_account" id="wc_xero_payment_account" value="'
                                            .get_option('wc_xero_payment_account').'" />';
											echo '<p class="description">Code for Xero account to track payments received.</p>'; }
 		function wc_xero_send_invoices(){ $checked = (get_option('wc_xero_send_invoices')=='on') ? 'checked="checked"' : '';
				 							   echo '<input type="checkbox" name="wc_xero_send_invoices" id="wc_xero_send_invoices"  '.$checked.' /> '
                                                  .__('Send Invoices to Xero automatically when order status is changed to completed.','wc-xero');}
 		function wc_xero_send_payments(){ $checked = (get_option('wc_xero_send_payments')=='on') ? 'checked="checked"' : '';
				 							   echo '<input type="checkbox" name="wc_xero_send_payments" id="wc_xero_send_payments"  '.$checked.' /> '
                                                   .__('Send Payments to Xero automatically when order is set to completed.','wc-xero');
				 							   echo '<p class="description">This may need to be turned off if you sync via a separate integration such as PayPal.</p>'; }
		function wc_xero_send_inventory(){ $checked = (get_option('wc_xero_send_inventory')=='on') ? 'checked="checked"' : '';
				 							   echo '<input type="checkbox" name="wc_xero_send_inventory" id="wc_xero_send_inventory"  '.$checked.' /> '
                                                   .__('Send Item Code field with invoices','wc-xero');
				 							   echo '<p class="description">If this is enabled then each product must have a SKU defined and be setup as an <a href="https://help.xero.com/us/#Settings_PriceList" target="_blank">inventory item</a> in Xero.</p>'; }
 		function wc_xero_export_zero_amount(){ $checked = (get_option('wc_xero_export_zero_amount')=='on') ? 'checked="checked"' : '';
				 							   echo '<input type="checkbox" name="wc_xero_export_zero_amount" id="wc_xero_export_zero_amount"  '.$checked.' /> '.__('Export orders with zero total','wc-xero'); }
		function wc_xero_debug(){ $checked = (get_option('wc_xero_debug')=='on') ? 'checked="checked"' : '';
				 				  echo '<input type="checkbox" name="wc_xero_debug" id="wc_xero_debug"  '.$checked.' /> ' . __('Enable logging.  Log file is located at: /wp-content/plugins/woocommerce/logs/xero.txt','wc-xero'); }
		/**
		 * END:  Admin Settings callbacks
		 */


	   /**
		* Xero Invoice
		*
		* Create a new invoice for the current purchase.  The customer is created, or if it already
		* is in the system their information is updated.
		*
		* @access public
		* @return void
		*/
		function xero_new_invoice($order_id) {
			include_once 'includes/XeroOAuth.php';

			$order = new WC_Order( $order_id );
			$xro_settings = $this->xro_private_defaults;
			if ( ( $order->order_total == 0 ) && ( get_option('wc_xero_export_zero_amount') != 'on' )){
				$this->add_log('Not exporting zero total order; order_id=' . $order_id );
				return;
			}

			// Sanity check for settings
			if ( ( 0 == strlen($this->consumer_key) ) ||
				( 0 == strlen($this->consumer_secret) ) ||
				( 0 == strlen($this->private_key) ) ||
				( 0 == strlen($this->public_key) ) ||
				( 0 == strlen($this->sales_account) ) ||
				( 0 == strlen($this->discount_account) ) ||
				( 0 == strlen($this->shipping_account) ) ||
				( 0 == strlen($this->payment_account) )
			   ){
				$this->add_log( 'XERO Error: Extension is active, but one or more settings are blank.' );
				$order->add_order_note( 'XERO Error: Extension is active, but one or more settings are blank.' );
				return;
			}

			// Check if key files exist
			if ( !file_exists($this->private_key) ){

				$this->add_log( 'XERO Error: Private key option is set, but file does not exist.' );
				$order->add_order_note( 'XERO Error: Private key option is set, but file does not exist.' );
				return;
			}
			if ( !file_exists($this->public_key) ){
				$this->add_log( 'XERO Error: Public key option is set, but file does not exist.' );
				$order->add_order_note( 'XERO Error: Public key option is set, but file does not exist.' );
				return;
			}

			$this->add_log('START XERO NEW INVOICE. order_id=' . $order_id );

			// authenticate
			$oauthObject = new OAuthSimple();
		    $oauthObject->reset();

			try{
			    $result = $oauthObject->sign(array(
							        'path'      => $xro_settings['xero_url'].'/Invoices/',
							        'action'	=> 'PUT',
							        'parameters'=> array(
									'oauth_signature_method' => $xro_settings['signature_method']),
							        'signatures'=> $this->signatures));
			} catch ( Exception $e ){
				$this->add_log('Caught Exception: ' . $e->getMessage() );
				$order->add_order_note( 'XERO Error: ' . $e->getMessage() );
				return;
			}

			$this->add_log('Signed Url:  '. $result['signed_url']);

			// construct XML
			$xml = '';
		    $xml .= '<Invoices>';
		    $xml .= '<Invoice>';
		    $xml .= '<Type>ACCREC</Type>';
		    $xml .= '<Contact>';

		    // Company name will be used as main name if it exists
		    if ( strlen($order->billing_company) > 0 ){
			    $invoice_name = $order->billing_company;
		    } else {
			    $invoice_name = $order->billing_first_name . ' ' . $order->billing_last_name;
		    }

		    $xml .= '<Name>' . htmlspecialchars( $invoice_name ) . '</Name>';
		    $xml .= '<FirstName>' . htmlspecialchars( $order->billing_first_name ) . '</FirstName>';
		    $xml .= '<LastName>' . htmlspecialchars( $order->billing_last_name ) . '</LastName>';
		    $xml .= '<EmailAddress>' . htmlspecialchars( $order->billing_email ) . '</EmailAddress>';
		    $xml .= '<Addresses>';
		    $xml .= '<Address>';
		    $xml .= '<AddressType>POBOX</AddressType>';
		    $xml .= '<AddressLine1>' . htmlspecialchars( $order->billing_address_1 ) . '</AddressLine1>';
		    if (strlen($order->billing_address_2) > 0 ){
		    	$xml .= '<AddressLine2>' . htmlspecialchars( $order->billing_address_2 ) . '</AddressLine2>';
		    }
		    $xml .= '<City>' . htmlspecialchars( $order->billing_city ) . '</City>';
		    $xml .= '<Region>' . htmlspecialchars( $order->billing_state ) . '</Region>';
		    $xml .= '<PostalCode>' . htmlspecialchars( $order->billing_postcode ) . '</PostalCode>';
		    $xml .= '<Country>' . htmlspecialchars( $order->billing_country ) . '</Country>';
            $xml .= '</Address>';
            $xml .= '</Addresses>';
		    $xml .= '<Phones>';
		    $xml .= '<Phone>';
		    $xml .= '<PhoneType>DEFAULT</PhoneType>';
		    $xml .= '<PhoneNumber>' . htmlspecialchars( $order->billing_phone ) . '</PhoneNumber>';
		    $xml .= '</Phone>';
		    $xml .= '</Phones>';
		    $xml .= '</Contact>';

			$xml .= '<Date>' . substr($order->order_date,0, strpos($order->order_date, ' ') ) . '</Date>';
			$xml .= '<DueDate>' .substr($order->order_date,0, strpos($order->order_date, ' ') ) . '</DueDate>';
		    
			$xml .= '<InvoiceNumber>' . ltrim( $order->get_order_number(), _x( '#', 'hash before order number', 'woocommerce' ) ) . '</InvoiceNumber>';
			
		    // Is tax included in the prices;  'Exclusive', 'Inclusive', 'NoTax'
		    if ( get_option( 'woocommerce_prices_include_tax' ) == 'yes' ){
				$xml .= '<LineAmountTypes>Inclusive</LineAmountTypes>';
		    } else {
				$xml .= '<LineAmountTypes>Exclusive</LineAmountTypes>';
		    }

			$xml .= '<LineItems>';
			// Get all Items
			$items = $order->get_items();

			// Add Each item as a line item
			foreach( $items as $key => $value ) {

				$product = $order->get_product_from_item( $value );
				$replace_pattern = array('&#8220;', '&#8221;');
                $item_description = str_replace($replace_pattern, '""', $value['name']);


				$xml .= '<LineItem>';
				$xml .= '<Description>' . htmlspecialchars( $item_description ) .'</Description>';
				$xml .= '<AccountCode>' . $this->sales_account . '</AccountCode>';
				$xml .= '<Quantity>' . $value['qty'] . '</Quantity>';
				if ( ( 'on' == $this->send_inventory ) && ( '' != $product->sku ) ) {
					$xml .= '<ItemCode>' . $product->sku . '</ItemCode>';
				}
				
			    if ( get_option( 'woocommerce_prices_include_tax' ) == 'yes' ) {
			    	// don't send taxes
			    	$lineamount =  $value['line_total'] + $value['line_tax'];
					$xml .= '<LineAmount>' . rtrim( rtrim( number_format( $lineamount , 2, '.', '' ), '0' ), '.' ) . '</LineAmount>';
					if ( 0 < $value['line_tax'] ) {
						$xml .= '<TaxAmount>' . rtrim( rtrim( number_format( $value['line_tax'], 2, '.', '' ), '0' ), '.' ) . '</TaxAmount>';
					}
			    } else {
			    	// Send Price and tax separate
			    	$unit_amount =  $value['line_total'] / $value['qty'];
					$xml .= '<UnitAmount>' . rtrim( rtrim( number_format( $unit_amount , 2, '.', '' ), '0' ), '.' ) . '</UnitAmount>';
					if ( 0 < $value['line_tax'] ) {
						$xml .= '<TaxAmount>' . rtrim( rtrim( number_format( $value['line_tax'], 2, '.', '' ), '0' ), '.' ) . '</TaxAmount>';
					}
			    }
				$xml .= '</LineItem>';
			}

			// Add Shipping cost as a line item
			if ( $order->order_shipping > 0) {
				$xml .= '<LineItem>';
				$xml .= '<Description>Shipping Charge</Description>';
				$xml .= '<Quantity>1</Quantity>';
				if ( strlen($this->shipping_account) != '' ){
					$xml .= '<AccountCode>' . $this->shipping_account . '</AccountCode>';
				}

				if ( get_option( 'woocommerce_prices_include_tax' ) == 'yes' ) {
			    	$lineamount = $order->order_shipping + $order->order_shipping_tax;
					$xml .= '<UnitAmount>' . $lineamount . '</UnitAmount>';
				} else {
					$xml .= '<UnitAmount>' . $order->order_shipping . '</UnitAmount>';
					if ( $order->order_shipping_tax > 0 ) {
						$xml .= '<TaxAmount>' . $order->order_shipping_tax . '</TaxAmount>';
					}
				}
				$xml .= '</LineItem>';
			}

			
			// Add Order discount as Line Item
			if ( $order->order_discount > 0 ) {
				$xml .= '<LineItem>';
				$xml .= '<Description>Order Discount</Description>';
				$xml .= '<Quantity>1</Quantity>';
				if ( strlen($this->discount_account) != '' ){
					$xml .= '<AccountCode>' . $this->discount_account . '</AccountCode>';
				}
				$xml .= '<UnitAmount>-' . $order->order_discount . '</UnitAmount>';
				$xml .= '</LineItem>';
			}

			$xml .= '</LineItems>';

			$xml .= '<CurrencyCode>' . get_option( 'woocommerce_currency' ) . '</CurrencyCode>';
			$xml .= '<Status>AUTHORISED</Status>';

			// Add Order Tax
			if ( get_option( 'woocommerce_prices_include_tax' ) != 'yes' ) {
				if ( ( $order->order_tax > 0 ) || ( $order->order_shipping_tax > 0 ) ) {
					$total_tax = $order->order_tax + $order->order_shipping_tax;
					$xml .= '<TotalTax>' . $total_tax . '</TotalTax>';
				}
			}
			
		    // Add Order Total
		    if ( get_option( 'woocommerce_prices_include_tax' ) == 'yes' ){
		    	// reduce total by tax amount
		    	$total_tax = $order->order_total - $order->order_tax;
				$xml .= '<Total>' . $order->order_total . '</Total>';
			} else {
				$xml .= '<Total>' . $order->order_total . '</Total>';
			}
			
		    $xml .= '</Invoice>';
		    $xml .= '</Invoices>';

			$this->add_log('SENDING XML:');
			$this->add_log($xml);
			
			# Set some standard curl options....
			$options[CURLOPT_VERBOSE] = 1;
	    	$options[CURLOPT_RETURNTRANSFER] = 1;
	    	$options[CURLOPT_SSL_VERIFYHOST] = 0;
	    	$options[CURLOPT_SSL_VERIFYPEER] = 0;
	    	$useragent = (isset($useragent)) ? (empty($useragent) ? 'XeroOAuth-PHP' : $useragent) : 'XeroOAuth-PHP';
	    	$options[CURLOPT_USERAGENT] = $useragent;

			// execute 'PUT'
			$fh  = fopen('php://memory', 'w+');
			fwrite($fh, $xml);
			rewind($fh);
			$ch = curl_init();
			curl_setopt_array($ch, $options);
			curl_setopt($ch, CURLOPT_PUT, true);
			curl_setopt($ch, CURLOPT_INFILE, $fh);
			curl_setopt($ch, CURLOPT_INFILESIZE, strlen($xml));
		    		curl_setopt($ch, CURLOPT_URL, $result['signed_url']);
			$r = curl_exec($ch);
			curl_close($ch);


			parse_str($r, $returned_items);
			$oauth_problem = isset($returned_items['oauth_problem']) ? $returned_items['oauth_problem'] : null ;
			$oauth_problem_advice =  isset($returned_items['oauth_problem_advice']) ? $returned_items['oauth_problem_advice'] : '';

			if($oauth_problem){
				// report error
				$this->add_log('Invoice not created. OAuth Error: ' . $oauth_problem . ' | '. $oauth_problem_advice );
				$order->add_order_note( __('XERO: Invoice not created. OAuth Error: ', 'wc-xero') . ' ' . $oauth_problem . ' | ' . $oauth_problem_advice );
				return;
			}

			$response = new SimpleXMLElement( $r );

			if ( $response->Status == 'OK' )  {
				// Invoice added
				// Record Invoice ID for use with adding payment

				add_post_meta( $order_id, '_xero_invoice_id', (string) $response->Invoices->Invoice[0]->InvoiceID );
				add_post_meta( $order_id, '_xero_currencyrate', (string) $response->Invoices->Invoice[0]->CurrencyRate );

				$this->add_log('XERO RESPONSE:' . "\n" . $r );
				$order->add_order_note( __('Xero Invoice created.  ', 'wc-xero') .
						' Invoice ID: ' . (string) $response->Invoices->Invoice[0]->InvoiceID );
				$this->add_log('END XERO NEW INVOICE' );

                // Send payment if auto-send payments setting is on
                if ( $this->send_payments == 'on' ) {
                    $this->xero_new_payment( $order_id );
                }

            } else {
				// An error occured
				$this->add_log('XERO RESPONSE:' . "\n" . $r );

				$error_message = $response->Elements->DataContractBase->ValidationErrors->ValidationError->Message ? $response->Elements->DataContractBase->ValidationErrors->ValidationError->Message : __('None', 'wc-xero'); 
				
				$order->add_order_note( __('ERROR creating Xero invoice: ', 'wc-xero') .
				__(' ErrorNumber: ', 'wc-xero') . $response->ErrorNumber .
				__(' ErrorType: ', 'wc-xero') . $response->Type .
				__(' Message: ', 'wc-xero') . $response->Message .
				__(' Detail: ', 'wc-xero') . $error_message );
				$this->add_log('END XERO NEW INVOICE' );
			}

		}


	   /**
		* Xero New Payment
		*
		* This is executed when a new payment
		*
		* @access public
		*/
		function xero_new_payment($order_id) {
			global $woocommerce;

			$order = new WC_Order( $order_id );

			$invoice_id = get_post_meta( $order_id, '_xero_invoice_id', true );
			$currency_rate = get_post_meta( $order_id, '_xero_currencyrate', true );
			
			// check for valid invoice ID
			if ( strlen( $invoice_id ) == 0 ) {
				$order->add_order_note( __('XERO Payment Error adding payment for order. A valid invoice number was not recorded.', 'wc-xero'));
				$this->add_log( __('Error adding payment for order ID:','wc-xero') . $order_id . __('.  A valid invoice number was not recorded.','wc-xero'));
				return;
			}

			// check for valid Currency Rate
			if ( strlen( $currency_rate ) == 0 ) {
				$order->add_order_note( __('XERO Payment Error adding payment for order. A valid currency rate was not recorded.', 'wc-xero'));
				$this->add_log( __('Error adding payment for order ID:','wc-xero') . $order_id . __('.  A valid currency rate was not recorded.','wc-xero'));
				return;
			}

			// Need a Payment Account number
			if ( strlen( $this->payment_account ) == '' ) {
				$order->add_order_note( __('XERO Payment Error adding payment for order ID. Payment Account number is blank.', 'wc-xero'));
				$this->add_log( __('Payment Error adding payment for order ID:','wc-xero') . $order_id . __('.  Payment Account number is blank.','wc-xero'));
				return;
			}

			// Sanity check for settings
			if ( ( 0 == strlen($this->consumer_key) ) ||
				( 0 == strlen($this->consumer_secret) ) ||
				( 0 == strlen($this->private_key) ) ||
				( 0 == strlen($this->public_key) ) ||
				( 0 == strlen($this->sales_account) ) ||
				( 0 == strlen($this->discount_account) ) ||
				( 0 == strlen($this->shipping_account) ) ||
				( 0 == strlen($this->payment_account) )
			   ){
				$this->add_log( 'XERO Payment Error: Extension is active, but one or more settings are blank.' );
				$order->add_order_note( 'XERO Payment Error: Extension is active, but one or more settings are blank.' );
				return;
			}

			// Check if key files exist
			if ( !file_exists($this->private_key) ){
				$this->add_log( 'XERO Error: Payment Private key option is set, but file does not exist. Please check your configuration settings.' );
				$order->add_order_note( 'XERO Payment Error: Private key option is set, but file does not exist. Please check your configuration settings.' );
				return;
			}
			if ( !file_exists($this->public_key) ){
				$this->add_log( 'XERO Payment Error: Public key option is set, but file does not exist. Please check your configuration settings.' );
				$order->add_order_note( 'XERO Payment Error: Public key option is set, but file does not exist. Please check your configuration settings.' );
				return;
			}

			include_once 'includes/XeroOAuth.php';
			$xro_settings = $this->xro_private_defaults;
			$this->add_log('START XERO NEW PAYMENT. order_id=' . $order_id );

			// authenticate
			$oauthObject = new OAuthSimple();
		    $oauthObject->reset();
			try{
			    $result = $oauthObject->sign(array(
						        'path'      => $xro_settings['xero_url'].'/Payments/',
						        'action'	=> 'PUT',
						        'parameters'=> array(
								'oauth_signature_method' => $xro_settings['signature_method']),
						        'signatures'=> $this->signatures));
			} catch ( Exception $e ){
				$this->add_log('Xero Payment Caught Exception: ' . $e->getMessage() );
				$order->add_order_note( 'XERO Payment Error: ' . $e->getMessage() );
				return;
			}


			// construct XML
			$xml = '';
			$xml .= '<Payments><Payment>';
			$xml .= '<Invoice><InvoiceID>' . $invoice_id . '</InvoiceID></Invoice>';
			$xml .= '<Account><Code>' . $this->payment_account. '</Code></Account>';
			$xml .= '<Date>' . date('Y-m-d') . '</Date>';
			// Partial payments not supported.
			$xml .= '<CurrencyRate>' . $currency_rate . '</CurrencyRate>';
			$xml .= '<Amount>' . $order->order_total . '</Amount>';
			$xml .= '</Payment></Payments>';


			$this->add_log('SENDING XML:');
			$this->add_log($xml);

			$options[CURLOPT_VERBOSE] = 1;
	    	$options[CURLOPT_RETURNTRANSFER] = 1;
	    	$options[CURLOPT_SSL_VERIFYHOST] = 0;
	    	$options[CURLOPT_SSL_VERIFYPEER] = 0;
	    	$useragent = (isset($useragent)) ? (empty($useragent) ? 'XeroOAuth-PHP' : $useragent) : 'XeroOAuth-PHP';
	    	$options[CURLOPT_USERAGENT] = $useragent;

			// execute 'PUT'
			$fh  = fopen('php://memory', 'w+');
			fwrite($fh, $xml);
			rewind($fh);
			$ch = curl_init();
			curl_setopt_array($ch, $options);
			curl_setopt($ch, CURLOPT_PUT, true);
			curl_setopt($ch, CURLOPT_INFILE, $fh);
			curl_setopt($ch, CURLOPT_INFILESIZE, strlen($xml));
		    curl_setopt($ch, CURLOPT_URL, $result['signed_url']);
			$r = curl_exec($ch);
			curl_close($ch);

			
			

			parse_str($r, $returned_items);

			if( isset( $returned_items['oauth_problem'] ) ){
				// report error
				$this->add_log('Payment not created. OAuth Error: ' . $returned_items['oauth_problem'] );
				$order->add_order_note( __('XERO: Payment not created. OAuth Error: ', 'wc-xero') . ' ' . $returned_items['oauth_problem']  );
				return;
			}

			$response = new SimpleXMLElement( $r );

			if ( $response->Status == 'OK' )  {
				// Payment added
				// Record Paymend ID

				add_post_meta( $order_id, '_xero_payment_id', (string) $response->Payments->Payment[0]->PaymentID );

				$this->add_log('XERO RESPONSE:' . "\n" . $r );
				$order->add_order_note( __('Xero Payment created.  ', 'wc-xero') .
						' Payment ID: ' . (string) $response->Payments->Payment[0]->PaymentID );
				$this->add_log('END XERO NEW PAYMENT' );
			} else {
				// An error occured
				$this->add_log('ERROR XERO RESPONSE:' . "\n" . $r );
				$error_num = (string) $response->ErrorNumber;
				$error_msg = (string) $response->Elements->DataContractBase->ValidationErrors->ValidationError->Message;
				$order->add_order_note( __('ERROR creating Xero payment. ErrorNumber:' . $error_num . '| Error Message:' . $error_msg , 'wc-xero'));
				$this->add_log('END XERO NEW PAYMENT' );
			}


		}

		/**
		 * Display the Xero actions in the Order Actions meta box drop down.
		 *
		 * Displays buttons for manually sending invoice or Payment to Xero
		 *
		 * @access public
		 * @param $actions
		 * @internal param mixed $post
		 * @return array
		 */
		function xero_order_actions( $actions ) {

			if ( is_array( $actions ) ) {
				$actions['xero_manual_invoice'] = __('Send Invoice to Xero', 'wc-xero');
				$actions['xero_manual_payment'] = __('Send Payment to Xero', 'wc-xero');
			}

			return $actions; 
		}

		/**
		 * Handle the order actions callback for creating a manual invoice
		 * For WooCommerce version > 2.0
		 *
		 * @access public
		 * @param mixed $order
		 * @return void
		 */
		function xero_manual_invoice( $order ){
			$this->xero_new_invoice( $order->id );
		}
		
		/**
		 * Handle the order actions callback for creating a manual payment
		 * For WooCommerce version > 2.0
		 *
		 * @access public
		 * @param mixed $order
		 * @return void
		 */
		function xero_manual_payment( $order ){
			$this->xero_new_payment( $order->id );
		}

		/**
		 * Handle the order actions buttons callback
		 * For WooCommerce version 1.x
		 * 
		 * @access public
		 * @param mixed $post_id, $post
		 * @return void
		 */
		function xero_process_order_meta( $post_id, $post) {
			global $woocommerce_errors;
			$order = new WC_Order( $post_id );

			if ( isset( $_POST['xero_invoice'] ) && $_POST['xero_invoice'] ) {
				$woocommerce_errors[] = "Invoice sent to Xero. Check Order Notes for response.";
				$this->xero_new_invoice( $order->id );
			} elseif ( isset( $_POST['xero_payment'] ) && $_POST['xero_payment'] ) {
				$woocommerce_errors[] = "Payment sent to Xero. Check Order Notes for response.";
				$this->xero_new_payment( $order->id );
			}
		}
	

		/**
		 * add_log()
		 * Use WooCommerce logger if debug is enabled.
		 */
		function add_log( $message ) {

			if ($this->debug!='on') return; // Debug must be enabled

			if ( ! is_object( $this->logger ) ) {
				$this->logger = new WC_Logger();
		    }

			$this->logger->add( 'xero', $message );

		}


	} /* class WC_Xero */

} /* is_woocommerce_active() */