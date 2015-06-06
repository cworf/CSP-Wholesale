<?php
/**
 * WooCommerce Jetpack Checkout
 *
 * The WooCommerce Jetpack Checkout class.
 *
 * @class		WCJ_Checkout
 * @version		1.1.0
 * @category	Class
 * @author 		Algoritmika Ltd. 
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
 
if ( ! class_exists( 'WCJ_Checkout' ) ) :
 
class WCJ_Checkout {

	/**
	 * @var array $sub_items
	 */
	public $sub_items = array(
		'enabled'		=> 'checkbox',		
		'required'		=> 'checkbox',
		'label' 		=> 'text', 
		'placeholder'	=> 'text', 		
	);
	
	/**
	 * @var array $items
	 */
	public $items = array(				
		'billing_country' 		=> array( 'enabled' => 'yes', 'label' => '', 'placeholder' => '', 'required' => 'yes' ),
		'billing_first_name' 	=> array( 'enabled' => 'yes', 'label' => '', 'placeholder' => '', 'required' => 'yes' ),
		'billing_last_name' 	=> array( 'enabled' => 'yes', 'label' => '', 'placeholder' => '', 'required' => 'yes' ),
		'billing_company' 		=> array( 'enabled' => 'yes', 'label' => '', 'placeholder' => '', 'required' => 'no' ),
		'billing_address_1' 	=> array( 'enabled' => 'yes', 'label' => '', 'placeholder' => '', 'required' => 'yes' ),
		'billing_address_2' 	=> array( 'enabled' => 'yes', 'label' => '', 'placeholder' => '', 'required' => 'no' ),
		'billing_city' 			=> array( 'enabled' => 'yes', 'label' => '', 'placeholder' => '', 'required' => 'yes' ),
		'billing_state' 		=> array( 'enabled' => 'yes', 'label' => '', 'placeholder' => '', 'required' => 'yes' ),
		'billing_postcode' 		=> array( 'enabled' => 'yes', 'label' => '', 'placeholder' => '', 'required' => 'yes' ),
		'billing_email' 		=> array( 'enabled' => 'yes', 'label' => '', 'placeholder' => '', 'required' => 'yes' ),
		'billing_phone' 		=> array( 'enabled' => 'yes', 'label' => '', 'placeholder' => '', 'required' => 'yes' ),
		'shipping_country' 		=> array( 'enabled' => 'yes', 'label' => '', 'placeholder' => '', 'required' => 'yes' ),
		'shipping_first_name' 	=> array( 'enabled' => 'yes', 'label' => '', 'placeholder' => '', 'required' => 'yes' ),
		'shipping_last_name' 	=> array( 'enabled' => 'yes', 'label' => '', 'placeholder' => '', 'required' => 'yes' ),
		'shipping_company' 		=> array( 'enabled' => 'yes', 'label' => '', 'placeholder' => '', 'required' => 'no' ),
		'shipping_address_1' 	=> array( 'enabled' => 'yes', 'label' => '', 'placeholder' => '', 'required' => 'yes' ),
		'shipping_address_2' 	=> array( 'enabled' => 'yes', 'label' => '', 'placeholder' => '', 'required' => 'no' ),
		'shipping_city' 		=> array( 'enabled' => 'yes', 'label' => '', 'placeholder' => '', 'required' => 'yes' ),
		'shipping_state' 		=> array( 'enabled' => 'yes', 'label' => '', 'placeholder' => '', 'required' => 'yes' ),
		'shipping_postcode' 	=> array( 'enabled' => 'yes', 'label' => '', 'placeholder' => '', 'required' => 'yes' ),
		'account_password' 		=> array( 'enabled' => 'yes', 'label' => '', 'placeholder' => '', 'required' => 'yes' ),
		'order_comments' 		=> array( 'enabled' => 'yes', 'label' => '', 'placeholder' => '', 'required' => 'no' ),				
	);
    
    /**
     * Constructor.
     */
    public function __construct() {
 
        // Main hooks
        if ( 'yes' === get_option( 'wcj_checkout_enabled' ) ) {
			add_filter( 'woocommerce_checkout_fields' , array( $this, 'custom_override_checkout_fields' ) );
			add_filter( 'woocommerce_order_button_text', array( $this, 'set_order_button_text' ) );
        }        
    
        // Settings hooks
        add_filter( 'wcj_settings_sections', array( $this, 'settings_section' ) );
        add_filter( 'wcj_settings_checkout', array( $this, 'get_settings' ), 100 );
        add_filter( 'wcj_features_status', array( $this, 'add_enabled_option' ), 100 );	
    }
	
    /**
     * set_order_button_text.
     */
    public function set_order_button_text( $current_text ) {
		$new_text = get_option( 'wcj_checkout_place_order_button_text' );
		if ( $new_text != '' )
			return $new_text;
		return $current_text;
    }	
    
    /**
     * add_enabled_option.
     */
    public function add_enabled_option( $settings ) {
    
        $all_settings = $this->get_settings();
        $settings[] = $all_settings[1];
        
        return $settings;
    }
	
	function custom_override_checkout_fields( $checkout_fields ) {

		//if ( is_super_admin() ) { echo '<pre>'; print_r( $checkout_fields ); echo '</pre>'; }
		
		foreach ( $this->items as $item_key => $default_values ) {
			
			foreach ( $this->sub_items as $sub_item_key => $sub_item_type ) {
			
				$item_id = 'wcj_checkout_fields_' . $item_key . '_' . $sub_item_key;
				$the_option = get_option( $item_id );
				
				$field_parts = explode( "_", $item_key, 2 );
				
				if ( $sub_item_key == 'enabled' ) {
				
					if ( $the_option == 'no' )
						unset( $checkout_fields[$field_parts[0]][$item_key] ); // e.g. unset( $checkout_fields['billing']['billing_country'] );
				}
				else if ( isset( $checkout_fields[$field_parts[0]][$item_key] ) ) {

					if ( $the_option != '' ) {
					
						if ( $sub_item_key == 'required' ) {
							
							if ( $the_option == 'yes' ) $the_option = true;
							else $the_option = false;
						}
						
						$checkout_fields[$field_parts[0]][$item_key][$sub_item_key] = $the_option;
					}
				}						
			}
		}		
		
		return $checkout_fields;
	}	
    
    /**
     * get_settings.
     */    
    function get_settings() {
	
		//global $woocommerce;
 
        $settings = array(
 
            array( 'title' => __( 'Checkout Options', 'woocommerce-jetpack' ), 'type' => 'title', 'desc' => __( '', 'woocommerce-jetpack' ), 'id' => 'wcj_checkout_options' ),
            
            array(
                'title'    => __( 'Checkout', 'woocommerce-jetpack' ),
                'desc'     => __( 'Enable the Checkout feature', 'woocommerce-jetpack' ),
                'desc_tip' => __( 'Customize checkout fields. Disable/enable fields, set required, change labels and/or placeholders.', 'woocommerce-jetpack' ),
                'id'       => 'wcj_checkout_enabled',
                'default'  => 'no',
                'type'     => 'checkbox',
            ),
        
            array( 'type'  => 'sectionend', 'id' => 'wcj_checkout_options' ),
        );
		
		// Place order (Order now) Button
		$settings[] = array( 'title' => __( 'Place order (Order now) Button', 'woocommerce-jetpack' ), 'type' => 'title', 'desc' => __( '', 'woocommerce-jetpack' ), 'id' => 'wcj_checkout_place_order_button_options' );
		
		$settings[] = array(
			'title'    => __( 'Text', 'woocommerce-jetpack' ),
			'desc'     => __( 'leave blank for WooCommerce default', 'woocommerce-jetpack' ),
			'desc_tip' => __( 'Button on the checkout page.', 'woocommerce-jetpack' ),
			'id'       => 'wcj_checkout_place_order_button_text',
			'default'  => '',
			'type'     => 'text',
		);
		
		$settings[] = array( 'type'  => 'sectionend', 'id' => 'wcj_checkout_place_order_button_options' );		
		
		// Checkout fields
		$settings[] = array( 'title' => __( 'Checkout Fields Options', 'woocommerce-jetpack' ), 'type' => 'title', 'desc' => __( 'This section lets you customize the checkout fields: change label, placeholder, set required, or remove any field.', 'woocommerce-jetpack' ), 'id' => 'wcj_checkout_fields_options' );
		
		/*$items = array(
			'enabled'		=> 'checkbox',		
			////'type', 
			'label' 		=> 'text', 
			'placeholder'	=> 'text', 
			'required'		=> 'checkbox',
			//'clear',			
		);

		$fields = array(				
			'billing_country',// => array( 'yes', '', '', 'yes' ),
			'billing_first_name',
			'billing_last_name',
			'billing_company',
			'billing_address_1',
			'billing_address_2',
			'billing_city',
			'billing_state',
			'billing_postcode',
			'billing_email',
			'billing_phone',
			'shipping_country',
			'shipping_first_name',
			'shipping_last_name',
			'shipping_company',
			'shipping_address_1',
			'shipping_address_2',
			'shipping_city',
			'shipping_state',
			'shipping_postcode',
			'account_password',
			'order_comments',				
		);*/

		//global $woocommerce;
		//$checkout_fields = WC()->WC_Checkout->$checkout_fields;//apply_filters( 'woocommerce_checkout_fields' , array() );		
		/*if ( is_super_admin() ) {
			
			global $woocommerce;
			echo '<pre>[';
			print_r( WC_Checkout::instance() );//->checkout()->checkout_fields;
			echo ']</pre>';
		}*/
		
		//global $woocommerce;
		//echo '<pre>'; print_r( $woocommerce->checkout()->checkout_fields ); echo '</pre>';
		
		foreach ( $this->items as $field => $default_values) {
			
			foreach ( $this->sub_items as $item_key => $item_type ) {
			
				$item_id = 'wcj_checkout_fields_' . $field . '_' . $item_key;
				
				//$field_parts = explode( "_", $field, 2 );
				
				$default_value = $default_values[$item_key];//'';
				//echo '<pre>' . $item_key . ':' . $item_type . ':' . $default_value . '</pre>';
				//if ( $item_type == 'checkbox' ) $default_value = 'yes';
				
				$item_title = $field;// . ' ' . $item_key;
				$item_title = str_replace( "_", " ", $item_title );
				$item_title = ucwords( $item_title );
				
				$item_desc_tip = '';
				if ( 'text' == $item_type ) $item_desc_tip = __( 'Leave blank for WooCommerce defaults.', 'woocommerce-jetpack' );
				
				$settings_to_add = array(
					//'title'    => $item_title,
					//'desc'     => $item_id,//__( 'Enable the Checkout feature', 'woocommerce-jetpack' ),
					'desc'	   => $item_key,
					'desc_tip' => $item_desc_tip,// . __( 'Default: ', 'woocommerce-jetpack' ) . $default_value,
					'id'       => $item_id,
					'default'  => $default_value,
					'type'     => $item_type,
					'css'	   => 'min-width:300px;width:50%;',
				);
				
				if ( 'enabled' == $item_key ) { 
				
					$settings_to_add['title'] = $item_title;
					$settings_to_add['checkboxgroup'] = 'start';
				}					
				else if ( 'required' == $item_key ) $settings_to_add['checkboxgroup'] = 'end';
				
				//echo '<pre>';
				//print_r( $settings_to_add );
				//echo '</pre>';
				
				$settings[] = $settings_to_add;
			}
		}
		
		$settings[] = array( 'type'  => 'sectionend', 'id' => 'wcj_checkout_fields_options' );
        
        return $settings;
    }
 
    /**
     * settings_section.
     */
    function settings_section( $sections ) {    
        $sections['checkout'] = __( 'Checkout', 'woocommerce-jetpack' );        
        return $sections;
    }    
}
 
endif;
 
return new WCJ_Checkout();
