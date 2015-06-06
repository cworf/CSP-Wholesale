<?php
/**
 * WooCommerce Jetpack Payment Gateways
 *
 * The WooCommerce Jetpack Payment Gateways class.
 *
 * @class       WCJ_Payment_Gateways
 * @version		1.1.1
 * @category	Class
 * @author 		Algoritmika Ltd. 
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
 
if ( ! class_exists( 'WCJ_Payment_Gateways' ) ) :
 
class WCJ_Payment_Gateways {

	public $woocommerce_icon_filters = array(
		'woocommerce_cod_icon' 				=> 'COD',
		'woocommerce_cheque_icon' 			=> 'Cheque',
		'woocommerce_bacs_icon' 			=> 'BACS',
		'woocommerce_mijireh_checkout_icon' => 'Mijireh Checkout', //depreciated?
		'woocommerce_paypal_icon' 			=> 'PayPal',
		//'woocommerce_wcj_custom_icon' 		=> 'WooJetpack Custom',
	);
    
    /**
     * Constructor.
     */
    public function __construct() {       
        if ( get_option( 'wcj_payment_gateways_enabled' ) == 'yes' ) {
			// Include custom payment gateway 
			include_once( 'gateways/class-wc-gateway-wcj-custom.php' );  
			
			// Main hooks
			// Icons for default WooCommerce methods hooks
			/*$this->woocommerce_icon_filters = array (
				'woocommerce_cod_icon' 				=> __( 'COD', 'woocommerce-jetpack' ),
				'woocommerce_cheque_icon' 			=> __( 'Cheque', 'woocommerce-jetpack' ),
				'woocommerce_bacs_icon' 			=> __( 'BACS', 'woocommerce-jetpack' ),
				'woocommerce_mijireh_checkout_icon' => __( 'Mijireh Checkout', 'woocommerce-jetpack' ),
				'woocommerce_paypal_icon' 			=> __( 'PayPal', 'woocommerce-jetpack' ),
				//'woocommerce_wcj_custom_icon' 		=> __( 'WooJetpack Custom', 'woocommerce-jetpack' ),
			);*/
			foreach ( $this->woocommerce_icon_filters as $filter_name => $filter_title )
				add_filter( $filter_name, array( $this, 'set_icon' ) );
				
			// Settings
			add_filter( 'woocommerce_payment_gateways_settings', array( $this, 'add_woocommerce_icons_options' ), 100 );
			
			// PDF Invoices
			if ( 'yes' === get_option( 'wcj_pdf_invoices_enabled' ) && 'yes' === get_option( 'wcj_pdf_invoices_attach_to_email_enabled' ) )
				add_filter( 'woocommerce_payment_gateways_settings', array( $this, 'add_attach_invoice_settings' ), 100 );

			// Payment Gateways Fees
			if ( 'yes' === get_option( 'wcj_payment_gateways_fees_enabled' ) ) {
				// Main Hooks
				add_action( 'woocommerce_cart_calculate_fees', array( $this, 'gateways_fees' ) );
				add_action( 'wp_enqueue_scripts' , array( $this, 'enqueue_checkout_script' ) );				
				// Settings Hooks
				add_filter( 'woocommerce_payment_gateways_settings', array( $this, 'add_fees_settings' ), 100 );
				// Scripts				
				add_action( 'init', array( $this, 'register_script' ) );
			}			
        }        
    
        // Settings hooks
        add_filter( 'wcj_settings_sections', array( $this, 'settings_section' ) );
        add_filter( 'wcj_settings_payment_gateways', array( $this, 'get_settings' ), 100 );
        add_filter( 'wcj_features_status', array( $this, 'add_enabled_option' ), 100 );
	}
	
    /**
     * register_script.
     */	
    public function register_script() {
        wp_register_script( 'wcj-payment-gateways-checkout', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'js/checkout.js', array( 'jquery' ), false, true );
    }	
		
    /**
     * enqueue_checkout_script.
     */	
    public function enqueue_checkout_script() {
        if( ! is_checkout() )
			return;		
		wp_enqueue_script( 'wcj-payment-gateways-checkout' );
    }	
	
    /**
     * gateways_fees.
     */		
	function gateways_fees() {
	
		global $woocommerce;

		//if ( is_admin() && ! defined( 'DOING_AJAX' ) )
		//	return;
			
		$current_gateway = $woocommerce->session->chosen_payment_method;
		if ( '' != $current_gateway ) {
			$fee_text  = get_option( 'wcj_gateways_fees_text_' . $current_gateway );			
			$min_cart_amount = get_option( 'wcj_gateways_fees_min_cart_amount_' . $current_gateway );			
			$max_cart_amount = get_option( 'wcj_gateways_fees_max_cart_amount_' . $current_gateway );			
			// Fees are applied BEFORE taxes
			$total_in_cart = $woocommerce->cart->cart_contents_total + $woocommerce->cart->shipping_total;			
			if ( '' != $fee_text && $total_in_cart >= $min_cart_amount  && ( 0 == $max_cart_amount || $total_in_cart <= $max_cart_amount ) ) {				
				$fee_value = get_option( 'wcj_gateways_fees_value_' . $current_gateway );
				$fee_type  = apply_filters( 'wcj_get_option_filter', 'fixed', get_option( 'wcj_gateways_fees_type_' . $current_gateway ) );
				$final_fee_to_add = 0;			
				switch ( $fee_type ) {
					case 'fixed': 	$final_fee_to_add = $fee_value; break;
					case 'percent': $final_fee_to_add = ( $fee_value / 100 ) * $total_in_cart; break;
				}			
				if ( '' != $fee_text && 0 != $final_fee_to_add )
					$woocommerce->cart->add_fee( $fee_text, $final_fee_to_add, true );//, 'standard' );	 
			}
		}
	}	
	
    /**
     * add_woocommerce_icons_options.
     */	
	function add_woocommerce_icons_options( $settings ) {

		$settings[] = array( 'title' => __( 'WooCommerce Jetpack: Default WooCommerce Payment Gateways Options', 'woocommerce-jetpack' ), 'type' => 'title', 'desc' => __( 'If you want to show an image next to the gateway\'s name on the frontend, enter a URL to an image.', 'woocommerce-jetpack' ), 'id' => 'wcj_payment_gateways_icons_options' );
        
		foreach ( $this->woocommerce_icon_filters as $filter_name => $filter_title ) {		
			// Prepare current value
			$desc = '';
			$icon_url = apply_filters( $filter_name, '' );
			if ( '' != $icon_url )
				$desc = '<img src="' . $icon_url . '" alt="' . $filter_title . '" title="' . $filter_title . '" />';
				//$desc = __( 'Current Icon: ', 'woocommerce-jetpack' ) . '<img src="' . $icon_url . '" alt="' . $filter_title . '" title="' . $filter_title . '" />';
				
			$settings[] = array(
					'title'    	=> $filter_title,
					//'title'   => sprintf( __( 'Icon for %s payment gateway', 'woocommerce-jetpack' ), $filter_title ),
					'desc'    	=> $desc,
					//'desc_tip'	=> $filter_name,
					'id'       	=> 'wcj_payment_gateways_icons_' . $filter_name,
					'default'  	=> '',
					'type'		=> 'text',
					'css'    	=> 'min-width:300px;width:50%;',
				);
		}
        
        $settings[] = array( 'type'  => 'sectionend', 'id' => 'wcj_payment_gateways_icons_options' );		
	  
		return $settings;
	}
	
    /**
     * add_fees_settings.
     */	
	function add_fees_settings( $settings ) {
		// Gateway's Extra Fees
        $settings[] = array( 
			'title' => __( 'Payment Gateways Fees Options', 'woocommerce-jetpack' ), 
			'type' => 'title', 
			'desc' => __( 'This section lets you set extra fees for payment gateways.', 'woocommerce-jetpack' ) . ' ' .
					  __( 'Fees are applied BEFORE taxes.', 'woocommerce-jetpack' ),
			'id' => 'wcj_payment_gateways_fees_options' );

		//$available_gateways = WC()->payment_gateways->payment_gateways();		
		global $woocommerce;
		$available_gateways = $woocommerce->payment_gateways->payment_gateways();		
		foreach ( $available_gateways as $key => $gateway ) {
			/*echo '<h5>' . $gateway->title . '</h5>';
			if ( $gateway->is_available() )
				echo '<strong style="color: green;">' . __( 'Available', 'woocommerce-jetpack' ) . '</strong>';
			else
				echo '<strong style="color: red;">' . __( 'Not available', 'woocommerce-jetpack' ) . '</strong>';*/		
		
			$settings = array_merge( $settings, array(
			
				array(
					'title'    	=> $gateway->title,
					'desc'    	=> __( 'Fee title to show to customer.', 'woocommerce-jetpack' ),
					'desc_tip'	=> __( 'Leave blank to disable.', 'woocommerce-jetpack' ),
					'id'       	=> 'wcj_gateways_fees_text_' . $key,
					'default'  	=> '',
					'type'		=> 'text',
				),
				
				array(
					'title'    	=> '',
					'desc'    	=> __( 'Fee type.', 'woocommerce-jetpack' ),
					'desc_tip'	=> __( 'Percent or fixed value.', 'woocommerce-jetpack' ) . ' ' . apply_filters( 'get_wc_jetpack_plus_message', '', 'desc_no_link' ),
					'custom_attributes'
								=> apply_filters( 'get_wc_jetpack_plus_message', '', 'disabled' ),							
					'id'       	=> 'wcj_gateways_fees_type_' . $key,
					'default'  	=> 'fixed',
					'type'		=> 'select',
					'options'     => array(
						'fixed' 	=> __( 'Fixed', 'woocommerce-jetpack' ),
						'percent'   => __( 'Percent', 'woocommerce-jetpack' ),						
					),
				),
				
				array(
					'title'    	=> '',
					'desc'    	=> __( 'Fee value.', 'woocommerce-jetpack' ),
					'desc_tip'	=> __( 'The value.', 'woocommerce-jetpack' ),
					'id'       	=> 'wcj_gateways_fees_value_' . $key,
					'default'  	=> 0,
					'type'		=> 'number',
					'custom_attributes' => array(
						'step' 	=> '0.01',
						'min'	=> '0',
					),
				),	
				
				array(
					'title'    	=> '',
					'desc'    	=> __( 'Minimum cart amount for adding the fee.', 'woocommerce-jetpack' ),
					'desc_tip'	=> __( 'Set 0 to disable.', 'woocommerce-jetpack' ),
					'id'       	=> 'wcj_gateways_fees_min_cart_amount_' . $key,
					'default'  	=> 0,
					'type'		=> 'number',
					'custom_attributes' => array(
						'step' 	=> '0.01',
						'min'	=> '0',
					),
				),		

				array(
					'title'    	=> '',
					'desc'    	=> __( 'Maximum cart amount for adding the fee.', 'woocommerce-jetpack' ),
					'desc_tip'	=> __( 'Set 0 to disable.', 'woocommerce-jetpack' ),
					'id'       	=> 'wcj_gateways_fees_max_cart_amount_' . $key,
					'default'  	=> 0,
					'type'		=> 'number',
					'custom_attributes' => array(
						'step' 	=> '0.01',
						'min'	=> '0',
					),
				),					

				/*array(
					'title'    	=> '',
					'desc'    	=> __( 'Taxes.', 'woocommerce-jetpack' ),
					//'desc_tip'	=> __( 'Percent or fixed value.', 'woocommerce-jetpack' ),
					'id'       	=> 'wcj_gateways_fees_tax_' . $key,
					'default'  	=> ,
					'type'		=> 'select',
					'options'   => array(
						
					),
				),*/
				
			) );
        }
		
        $settings[] = array( 'type'  => 'sectionend', 'id' => 'wcj_payment_gateways_fees_options' );
		
		return $settings;
	}
	
    /**
     * add_attach_invoice_settings.
     */		
	function add_attach_invoice_settings( $settings ) {
        $settings[] = array( 'title' => __( 'Payment Gateways Attach PDF Invoice Options', 'woocommerce-jetpack' ), 'type' => 'title', 'desc' => __( 'This section lets you choose when to attach PDF invoice to customers emails.', 'woocommerce-jetpack' ), 'id' => 'wcj_gateways_attach_invoice_options' );
		global $woocommerce;
		$available_gateways = $woocommerce->payment_gateways->payment_gateways();		
		foreach ( $available_gateways as $key => $gateway ) {
		
			$settings = array_merge( $settings, array(			
				
				array(
					'title'		=> $gateway->title,
					//'desc'		=> __( 'Attach PDF invoice to customers emails.', 'woocommerce-jetpack' ),
					'desc'		=> __( 'Attach PDF invoice.', 'woocommerce-jetpack' ),
					'id'       	=> 'wcj_gateways_attach_invoice_' . $key,
					'default'  	=> 'yes',
					'type'		=> 'checkbox',
				),					

				
			) );
        }
		
        $settings[] = array( 'type'  => 'sectionend', 'id' => 'wcj_gateways_attach_invoice_options' );
		
		return $settings;
	}	
    
    /**
     * add_enabled_option.
     */
    public function add_enabled_option( $settings ) {
    
        $all_settings = $this->get_settings();
        $settings[] = $all_settings[1];
        
        return $settings;
    }
			
	/**
	 * set_icon
	 */
	public function set_icon( $value ) {
		$icon_url = get_option( 'wcj_payment_gateways_icons_' . current_filter(), '' );
		if ( $icon_url === '' )
			return $value;
		return $icon_url;
	}		
    
    /**
     * get_settings.
     */    
    function get_settings() {
 
        $settings = array(
 
            array( 'title' => __( 'Payment Gateways Options', 'woocommerce-jetpack' ), 'type' => 'title', 'desc' => __( '', 'woocommerce-jetpack' ), 'id' => 'wcj_payment_gateways_options' ),
            
            array(
                'title'    => __( 'Payment Gateways', 'woocommerce-jetpack' ),
                'desc'     => '<strong>' . __( 'Enable Module', 'woocommerce-jetpack' ) . '</strong>',
                'desc_tip' => __( 'Add custom payment gateway, change icons (images) for all default WooCommerce payment gateways.', 'woocommerce-jetpack' ),
                'id'       => 'wcj_payment_gateways_enabled',
                'default'  => 'yes',
                'type'     => 'checkbox',
            ),
        
            array( 'type'  => 'sectionend', 'id' => 'wcj_payment_gateways_options' ),
			
            array( 'title' => __( 'Custom Payment Gateways Options', 'woocommerce-jetpack' ), 'type' => 'title', 'desc' => __( '', 'woocommerce-jetpack' ), 'id' => 'wcj_custom_payment_gateways_options' ),
            
            array(
                'title'    => __( 'Number of Gateways', 'woocommerce-jetpack' ),
                'desc' 	   => apply_filters( 'get_wc_jetpack_plus_message', '', 'desc' ),
                'desc_tip' => __( 'Number of custom payments gateways to be added. All settings for each new gateway are in WooCommerce > Settings > Checkout.', 'woocommerce-jetpack' ),
                'id'       => 'wcj_custom_payment_gateways_number',
                'default'  => 1,
                'type'     => 'number',
				'custom_attributes' => array(
					'min'  => 1,
					'step' => 1,
					'max'  => apply_filters( 'wcj_get_option_filter', 1, 10 ),
				)				
            ),
        
            array( 'type'  => 'sectionend', 'id' => 'wcj_custom_payment_gateways_options' ),			
		);
			
        $settings[] = array( 'title' => __( 'Default WooCommerce Payment Gateways Options', 'woocommerce-jetpack' ), 'type' => 'title', 'desc' => __( 'If you want to show an image next to the gateway\'s name on the frontend, enter a URL to an image.', 'woocommerce-jetpack' ), 'id' => 'wcj_payment_gateways_icons_options' );
        
		foreach ( $this->woocommerce_icon_filters as $filter_name => $filter_title ) {		
			// Prepare current value
			$desc = '';
			$icon_url = apply_filters( $filter_name, '' );
			if ( '' != $icon_url )
				$desc = '<img src="' . $icon_url . '" alt="' . $filter_title . '" title="' . $filter_title . '" />';
				//$desc = __( 'Current Icon: ', 'woocommerce-jetpack' ) . '<img src="' . $icon_url . '" alt="' . $filter_title . '" title="' . $filter_title . '" />';
				
			$settings[] = array(
					'title'    	=> $filter_title,
					//'title'   => sprintf( __( 'Icon for %s payment gateway', 'woocommerce-jetpack' ), $filter_title ),
					'desc'    	=> $desc,
					//'desc_tip'	=> $filter_name,
					'id'       	=> 'wcj_payment_gateways_icons_' . $filter_name,
					'default'  	=> '',
					'type'		=> 'text',
					'css'    	=> 'min-width:300px;width:50%;',
				);
		}
        
        $settings[] = array( 'type'  => 'sectionend', 'id' => 'wcj_payment_gateways_icons_options' );		
		
		//$settings = $this->add_fees_settings( $settings );
		$settings = array_merge( $settings, array( 
		
			array( 'title' => __( 'Payment Gateways Fees Options', 'woocommerce-jetpack' ), 'type' => 'title', 'desc' => __( 'This section lets you enable extra fees for payment gateways. When enabled all options are added to WooCommerce > Settings > Checkout', 'woocommerce-jetpack' ), 'id' => 'wcj_payment_gateways_fees_options' ),
			
			array(
				'title'    => __( 'Payment Gateways Fees', 'woocommerce-jetpack' ),
				'desc'     => __( 'Enable the Payment Gateways Fees', 'woocommerce-jetpack' ),
				'id'       => 'wcj_payment_gateways_fees_enabled',
				'default'  => 'yes',
				'type'     => 'checkbox',
			),
		
			array( 'type'  => 'sectionend', 'id' => 'wcj_payment_gateways_fees_options' ),
		) );		
		
		return $settings;
    }
 
    /**
     * settings_section.
     */
    function settings_section( $sections ) {    
        $sections['payment_gateways'] = __( 'Payment Gateways', 'woocommerce-jetpack' );        
        return $sections;
    }    
}
 
endif;
 
return new WCJ_Payment_Gateways();
