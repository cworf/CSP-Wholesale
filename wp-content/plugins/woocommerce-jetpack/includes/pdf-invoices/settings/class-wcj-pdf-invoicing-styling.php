<?php
/**
 * WooCommerce Jetpack PDF Invoicing Styling
 *
 * The WooCommerce Jetpack PDF Invoicing Styling class.
 *
 * @class    WCJ_PDF_Invoicing_Styling
 * @version  1.0.0
 * @category Class
 * @author   Algoritmika Ltd.
 */
 
if ( ! defined( 'ABSPATH' ) ) exit;
 
if ( ! class_exists( 'WCJ_PDF_Invoicing_Styling' ) ) :
 
class WCJ_PDF_Invoicing_Styling {
    
    /**
     * Constructor.
     */
    public function __construct() {
        // Settings hooks
        add_filter( 'wcj_settings_sections',              array( $this, 'settings_section' ) );
        add_filter( 'wcj_settings_pdf_invoicing_styling', array( $this, 'get_settings' ), 100 );
    }
	    
    /**
     * get_settings.
     */    
    function get_settings() {
	
		$settings = array();				
		
		$invoice_types = wcj_get_invoice_types();
		foreach ( $invoice_types as $invoice_type ) {	
		
			$settings[] = array( 'title' => strtoupper( $invoice_type['desc'] ), 'type' => 'title', 'desc' => '', 'id' => 'wcj_invoicing_' . $invoice_type['id'] . '_styling_options' );			

			//$default_template = include( 'defaults/' . $invoice_type['id'] . '/wcj-content-template.php' );
			ob_start();
			include( 'defaults/wcj-' . $invoice_type['id'] . '.css' );
			$default_template = ob_get_clean();
			
			$settings[] = array(
                'title'    => __( 'CSS', 'woocommerce-jetpack' ),
                'id'       => 'wcj_invoicing_' . $invoice_type['id'] . '_css',
                'default'  => $default_template,
                'type'     => 'textarea',
				'css'	   => 'width:66%;min-width:300px;height:200px;',
            );
			
			$settings[] = array(
                'title'    => __( 'Font Family', 'woocommerce-jetpack' ),
                //'desc'     => __( 'Default: dejavusans', 'woocommerce-jetpack' ),
				'desc'	   => apply_filters( 'get_wc_jetpack_plus_message', '', 'desc' ),
                'id'       => 'wcj_invoicing_' . $invoice_type['id'] . '_general_font_family',
                'default'  => 'dejavusans',
                'type'     => 'select',
				'options'  => array(
								'dejavusans' => 'DejaVu Sans',
								'courier'    => 'Courier',
								'helvetica'  => 'Helvetica',	
								'times'      => 'Times',
				),
				'custom_attributes'	=> apply_filters( 'get_wc_jetpack_plus_message', '', 'disabled' ),
            );
			
			$settings[] = array(
                'title'    => __( 'Font Size', 'woocommerce-jetpack' ),
                //'desc'     => __( 'Default: 8', 'woocommerce-jetpack' ),
                'id'       => 'wcj_invoicing_' . $invoice_type['id'] . '_general_font_size',
                'default'  => 8,
                'type'     => 'number',
				//'css'      => 'width:50px;',
            );
			
            $settings[] = array(
                'title'    => __( 'Make Font Shadowed', 'woocommerce-jetpack' ),
                //'desc'     => __( 'Default: Yes', 'woocommerce-jetpack' ),
                'id'       => 'wcj_invoicing_' . $invoice_type['id'] . '_general_font_shadowed',
                'default'  => 'no',
                'type'     => 'checkbox',
            );		
			
			$settings[] = array( 'type'  => 'sectionend', 'id' => 'wcj_invoicing_' . $invoice_type['id'] . '_styling_options' );			
		}	
        
        return $settings;
    }
 
    /**
     * settings_section.
     */
    function settings_section( $sections ) {    
        $sections['pdf_invoicing_styling'] = __( 'Styling', 'woocommerce-jetpack' );        
        return $sections;
    }  
}
 
endif;
 
return new WCJ_PDF_Invoicing_Styling();