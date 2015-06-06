<?php
/**
 * WooCommerce Jetpack General
 *
 * The WooCommerce Jetpack General class.
 *
 * @class		WCJ_General
 * @version		1.1.0
 * @category	Class
 * @author 		Algoritmika Ltd. 
 */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
 
if ( ! class_exists( 'WCJ_General' ) ) :
 
class WCJ_General {
    
    /**
     * Constructor.
     */
    public function __construct() { 
        // Main hooks
        if ( 'yes' === get_option( 'wcj_general_enabled' ) ) {
		
			if ( '' != get_option( 'wcj_general_custom_css' ) )
				add_action( 'wp_head', 		array( $this, 'hook_custom_css' ) );				
			if ( '' != get_option( 'wcj_general_custom_admin_css' ) )
				add_action( 'admin_head', 	array( $this, 'hook_custom_admin_css' ) );					
		} 
		
        // Settings hooks
        add_filter( 'wcj_settings_sections', 	array( $this, 'settings_section' ) );
        add_filter( 'wcj_settings_general', 	array( $this, 'get_settings' ), 		100 );
        add_filter( 'wcj_features_status', 		array( $this, 'add_enabled_option' ), 	100 );
    }
	
    /**
     * hook_custom_css.
     */
    public function hook_custom_css() {
    	$output = '<style>' . get_option( 'wcj_general_custom_css' ) . '</style>';
		echo $output;
    }	
	
    /**
     * hook_custom_admin_css.
     */
    public function hook_custom_admin_css() {
    	$output = '<style>' . get_option( 'wcj_general_custom_admin_css' ) . '</style>';
		echo $output;
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
 
            array( 'title' => __( 'General Options', 'woocommerce-jetpack' ), 'type' => 'title', 'desc' => __( '', 'woocommerce-jetpack' ), 'id' => 'wcj_general_options' ),
            
            array(
                'title'    => __( 'General', 'woocommerce-jetpack' ),
                'desc'     => '<strong>' . __( 'Enable Module', 'woocommerce-jetpack' ) . '</strong>',
                'desc_tip' => __( 'Separate custom CSS for front and back end.', 'woocommerce-jetpack' ),
                'id'       => 'wcj_general_enabled',
                'default'  => 'no',
                'type'     => 'checkbox',
            ),
        
            array( 'type'  => 'sectionend', 'id' => 'wcj_general_options' ),
			
            array( 'title' => __( 'Custom CSS Options', 'woocommerce-jetpack' ), 'type' => 'title', 'desc' => __( 'Another custom CSS, if you need one.', 'woocommerce-jetpack' ), 'id' => 'wcj_general_custom_css_options' ),
            
            array(
                'title'    => __( 'Custom CSS - Front end (Customers)', 'woocommerce-jetpack' ),
                'id'       => 'wcj_general_custom_css',
                'default'  => '',
                'type'     => 'textarea',
				'css'	   => 'width:66%;min-width:300px;min-height:300px;',
            ),
			
            array(
                'title'    => __( 'Custom CSS - Back end (Admin)', 'woocommerce-jetpack' ),
                'id'       => 'wcj_general_custom_admin_css',
                'default'  => '',
                'type'     => 'textarea',
				'css'	   => 'width:66%;min-width:300px;min-height:300px;',
            ),			
        
            array( 'type'  => 'sectionend', 'id' => 'wcj_general_custom_css_options' ),
        );
        
        return $settings;
    }
 
    /**
     * settings_section.
     */
    function settings_section( $sections ) {    
        $sections['general'] = __( 'General', 'woocommerce-jetpack' );        
        return $sections;
    } 
}
 
endif;
 
return new WCJ_General();
