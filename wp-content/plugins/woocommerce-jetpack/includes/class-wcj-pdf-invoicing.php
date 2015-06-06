<?php
/**
 * WooCommerce Jetpack PDF Invoicing
 *
 * The WooCommerce Jetpack PDF Invoicing class.
 *
 * @class		WCJ_PDF_Invoicing
 * @version		2.1.2
 * @category	Class
 * @author 		Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WCJ_PDF_Invoicing' ) ) :

class WCJ_PDF_Invoicing {

    /**
     * Constructor.
     */
    public function __construct() {

        if ( get_option( 'wcj_pdf_invoicing_enabled' ) == 'yes' ) {
		
			include_once( 'pdf-invoices/class-wcj-pdf-invoicing-renumerate-tool.php' );		

			add_action( 'init', array( $this, 'catch_args' ) );
			add_action( 'init', array( $this, 'generate_pdf_on_init' ) );

			define ( 'K_PATH_IMAGES', $_SERVER['DOCUMENT_ROOT'] );

			$invoice_types = wcj_get_enabled_invoice_types();
			foreach ( $invoice_types as $invoice_type ) {
				$the_hook = get_option( 'wcj_invoicing_' . $invoice_type['id'] . '_create_on', 'woocommerce_new_order' );				
				if ( 'disabled' != $the_hook ) {				
					add_action( $the_hook, array( $this, 'create_' . $invoice_type['id'] ) );
				}
			}			
	    }

        // Settings hooks
        add_filter( 'wcj_settings_sections',      array( $this, 'settings_section' )        );
        add_filter( 'wcj_settings_pdf_invoicing', array( $this, 'get_settings' ),       100 );
        add_filter( 'wcj_features_status',        array( $this, 'add_enabled_option' ), 100 );
    }
	
    /**
     * create_invoice.
     */	
	function create_invoice( $order_id ) {
		return $this->create_document( $order_id, 'invoice' );
	}	
	
    /**
     * create_proforma_invoice.
     */	
	function create_proforma_invoice( $order_id ) {
		return $this->create_document( $order_id, 'proforma_invoice' );
	}	
	
    /**
     * create_packing_slip.
     */	
	function create_packing_slip( $order_id ) {
		return $this->create_document( $order_id, 'packing_slip' );
	}
	
    /**
     * create_document.
     */
	function create_document( $order_id, $invoice_type ) {
		
		if ( false == wcj_is_invoice_created( $order_id, $invoice_type ) ) {
			wcj_create_invoice( $order_id, $invoice_type );
		}
		/*
		if ( '' == get_post_meta( $order_id, '_wcj_invoicing_' . $invoice_type . '_number', true ) ) {
			if ( 'yes' === get_option( 'wcj_invoicing_' . $invoice_type . '_sequential_enabled' ) ) {
				$the_invoice_number = get_option( 'wcj_invoicing_' . $invoice_type . '_numbering_counter', 1 );
				update_option( 'wcj_invoicing_' . $invoice_type . '_numbering_counter', ( $the_invoice_number + 1 ) );
			} else {
				$the_invoice_number = $order_id;
			}			
			update_post_meta( $order_id, '_wcj_invoicing_' . $invoice_type . '_number', $the_invoice_number );
			update_post_meta( $order_id, '_wcj_invoicing_' . $invoice_type . '_date', time() );
		}
		*/
	}

    /**
     * catch_args.
     */
    function catch_args() {
		$this->order_id        = ( isset( $_GET['order_id'] ) )                                             ? $_GET['order_id'] : 0;
		$this->invoice_type_id = ( isset( $_GET['invoice_type_id'] ) )                                      ? $_GET['invoice_type_id'] : '';
		$this->save_as_pdf     = ( isset( $_GET['save_pdf_invoice'] ) && '1' == $_GET['save_pdf_invoice'] ) ? true : false;
		$this->get_invoice     = ( isset( $_GET['get_invoice'] ) && '1' == $_GET['get_invoice'] )           ? true : false;
	}

    /**
     * generate_pdf_on_init.
     */	
	function generate_pdf_on_init() {
	
		// Check if all is OK
		if ( ( true !== $this->get_invoice ) ||
		     ( 0 == $this->order_id ) ||
		     ( ! is_user_logged_in() ) ||
		     ( ! current_user_can( 'administrator' ) && get_current_user_id() != intval( get_post_meta( $this->order_id, '_customer_user', true ) ) ) )
			return;
	
		$the_invoice = wcj_get_pdf_invoice( $this->order_id, $this->invoice_type_id );
		//$invoice = new WCJ_PDF_Invoice();
		$dest = ( true === $this->save_as_pdf ) ? 'D' : 'I';
		//$invoice->get_pdf( $this->order_id, $this->invoice_type_id, '', $dest );//, $this->invoice_type_id );
		$the_invoice->get_pdf( $dest );
		//echo $invoice_html;
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

            array( 'title' => __( 'PDF Invoicing General Options', 'woocommerce-jetpack' ), 'type' => 'title', 'desc' => __( '', 'woocommerce-jetpack' ), 'id' => 'wcj_pdf_invoicing_options' ),

            array(
                'title'    => __( 'PDF Invoicing', 'woocommerce-jetpack' ),
                'desc'     => '<strong>' . __( 'Enable Module', 'woocommerce-jetpack' ) . '</strong>',
                //'desc_tip' => __( 'Add PDF invoices for the store owners and for the customers.', 'woocommerce-jetpack' ),
                'desc_tip' => __( 'Invoices, Proforma Invoices, Packing Slips.', 'woocommerce-jetpack' ),
                'id'       => 'wcj_pdf_invoicing_enabled',
                'default'  => 'no',
                'type'     => 'checkbox',
            ),
		);

		// Hooks Array
		$create_on_array = array();
		$create_on_array['disabled'] = __( 'Disabled', 'woocommerce-jetpack' );		
		$create_on_array['woocommerce_new_order'] = __( 'Create on New Order', 'woocommerce-jetpack' );
		$order_statuses = wcj_get_order_statuses( true );
		foreach ( $order_statuses as $status => $desc ) {
			$create_on_array[ 'woocommerce_order_status_' . $status ] = __( 'Create on Order Status', 'woocommerce-jetpack' ) . ' ' . $desc;
		}		
		
		// Settings
		$invoice_types = wcj_get_invoice_types();
		foreach ( $invoice_types as $k => $invoice_type ) {
			$settings[] = array(
                'title'    => $invoice_type['title'],
                'id'       => 'wcj_invoicing_' . $invoice_type['id'] . '_create_on',
                'default'  => 'disabled',
                'type'     => 'select',
				'class'    => 'chosen_select',
                'options'  => $create_on_array,
				'desc' 	   => ( 0 === $k ) ? '' : apply_filters( 'get_wc_jetpack_plus_message', '', 'desc' ),
				'custom_attributes'	
						   => ( 0 === $k ) ? '' : apply_filters( 'get_wc_jetpack_plus_message', '', 'disabled' ),				
            );
		}

		$settings[] = array( 'type'  => 'sectionend', 'id' => 'wcj_pdf_invoicing_options' );

		return $settings;
    }

    /**
     * settings_section.
     */
    function settings_section( $sections ) {
        $sections['pdf_invoicing'] = __( 'General', 'woocommerce-jetpack' );
        return $sections;
    }
}

endif;

return new WCJ_PDF_Invoicing();
