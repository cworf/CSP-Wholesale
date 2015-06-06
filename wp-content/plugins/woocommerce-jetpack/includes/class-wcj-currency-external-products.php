<?php
/**
 * WooCommerce Jetpack Currency for External Products
 *
 * The WooCommerce Jetpack Currency for External Products class.
 *
 * @class       WCJ_Currency_External_Products
 * @version		1.0.0
 * @category	Class
 * @author 		Algoritmika Ltd. 
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
 
if ( ! class_exists( 'WCJ_Currency_External_Products' ) ) :
 
class WCJ_Currency_External_Products {
    
    /**
     * Constructor.
     */
    public function __construct() {
	
		$currencies = include( 'currencies/wcj-currencies.php' );
		foreach( $currencies as $data ) {
			$this->currency_symbols[ $data['code'] ]           = $data['symbol'];
			$this->currency_names_and_symbols[ $data['code'] ] = $data['name'] . ' (' . $data['symbol'] . ')';		
		}			
 
        // Main hooks
        if ( 'yes' === get_option( 'wcj_currency_external_products_enabled' ) ) {
			if ( '' != get_option( 'wcj_currency_external_products_symbol' ) )
				add_filter( 'woocommerce_currency_symbol', array( $this, 'change_currency_symbol' ), 100, 2 );
        }        
    
        // Settings hooks
        add_filter( 'wcj_settings_sections', 						array( $this, 'settings_section' ) );
        add_filter( 'wcj_settings_currency_external_products', 		array( $this, 'get_settings' ), 100 );
        add_filter( 'wcj_features_status', 							array( $this, 'add_enabled_option' ), 100 );
    }	
	
    /**
     * change_currency_symbol.
     */
    public function change_currency_symbol( $currency_symbol, $currency ) {
		global $product;
		if ( $product && 'external' === $product->product_type )
			return $this->currency_symbols[ get_option( 'wcj_currency_external_products_symbol' ) ];
		return $currency_symbol;
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
     * get_settings.
     */    
    function get_settings() {
	
        $settings = array(
 
            array( 'title' => __( 'Currency for External Products Options', 'woocommerce-jetpack' ), 'type' => 'title', 'desc' => '', 'id' => 'wcj_currency_external_products_options' ),
            
            array(
                'title'    => __( 'Currency for External Products', 'woocommerce-jetpack' ),
				'desc'     => '<strong>' . __( 'Enable Module', 'woocommerce-jetpack' ) . '</strong>',
                'desc_tip' => __( 'Set different currency for external products.', 'woocommerce-jetpack' ),
                'id'       => 'wcj_currency_external_products_enabled',
                'default'  => 'no',
                'type'     => 'checkbox',
            ),
			
            array(
                'title'    => __( 'Currency Symbol', 'woocommerce-jetpack' ),
                'desc'     => __( 'Set currency symbol for all external products.', 'woocommerce-jetpack' ),
                'desc_tip' => __( 'Set currency symbol for all external products.', 'woocommerce-jetpack' ),
                'id'       => 'wcj_currency_external_products_symbol',
                'default'  => 'EUR',
                'type'     => 'select',
				'options'  => $this->currency_names_and_symbols,
            ),			
        
            array( 'type'  => 'sectionend', 'id' => 'wcj_currency_external_products_options' ),
        );
        
        return $settings;
    }
 
    /**
     * settings_section.
     */
    function settings_section( $sections ) {    
        $sections['currency_external_products'] = __( 'Currency for External Products', 'woocommerce-jetpack' );        
        return $sections;
    }    
}
 
endif;
 
return new WCJ_Currency_External_Products();
