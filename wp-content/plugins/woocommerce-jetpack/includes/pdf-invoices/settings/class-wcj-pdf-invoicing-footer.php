<?php
/**
 * WooCommerce Jetpack PDF Invoicing Footer
 *
 * The WooCommerce Jetpack PDF Invoicing Footer class.
 *
 * @class    WCJ_PDF_Invoicing_Footer
 * @version  1.0.0
 * @category Class
 * @author   Algoritmika Ltd.
 */
 
if ( ! defined( 'ABSPATH' ) ) exit;
 
if ( ! class_exists( 'WCJ_PDF_Invoicing_Footer' ) ) :
 
class WCJ_PDF_Invoicing_Footer {
    
    /**
     * Constructor.
     */
    public function __construct() {
        // Settings hooks
        add_filter( 'wcj_settings_sections',             array( $this, 'settings_section' ) );
        add_filter( 'wcj_settings_pdf_invoicing_footer', array( $this, 'get_settings' ), 100 );
    }
	    
    /**
     * get_settings.
     */    
    function get_settings() {
	
		$settings = array();		
		$invoice_types = wcj_get_invoice_types();
		foreach ( $invoice_types as $invoice_type ) {	

			$settings[] = array( 'title' => strtoupper( $invoice_type['desc'] ), 'type' => 'title', 'desc' => '', 'id' => 'wcj_invoicing_' . $invoice_type['id'] . '_footer_options' );			
				
			$settings = array_merge( $settings, array(
			
				array(
					'title'    => __( 'Enable Footer', 'woocommerce-jetpack' ),
					'id'       => 'wcj_invoicing_' . $invoice_type['id'] . '_footer_enabled',
					'default'  => 'yes',
					'type'     => 'checkbox',
				),					
				
				array(
					'title'    => __( 'Footer Text Color', 'woocommerce-jetpack' ),
					'id'       => 'wcj_invoicing_' . $invoice_type['id'] . '_footer_text_color',
					'default'  => '#cccccc',
					'type'     => 'color',
					'css'      => 'width:6em;',
				),
				
				array(
					'title'    => __( 'Footer Line Color', 'woocommerce-jetpack' ),
					'id'       => 'wcj_invoicing_' . $invoice_type['id'] . '_footer_line_color',
					'default'  => '#cccccc',
					'type'     => 'color',
					'css'      => 'width:6em;',
				),		
				
				array(
					'title'    => __( 'Footer Margin', 'woocommerce-jetpack' ),
					'id'       => 'wcj_invoicing_' . $invoice_type['id'] . '_margin_footer',
					'default'  => 10,//PDF_MARGIN_FOOTER
					'type'     => 'number',
				),				

			) );
		
			$settings[] = array( 'type'  => 'sectionend', 'id' => 'wcj_invoicing_' . $invoice_type['id'] . '_footer_options' );			
		}	
        
        return $settings;
    }
 
    /**
     * settings_section.
     */
    function settings_section( $sections ) {    
        $sections['pdf_invoicing_footer'] = __( 'Footer', 'woocommerce-jetpack' );        
        return $sections;
    }  
}
 
endif;
 
return new WCJ_PDF_Invoicing_Footer();