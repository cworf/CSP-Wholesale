<?php
/**
 * WooCommerce Jetpack Invoices Shortcodes
 *
 * The WooCommerce Jetpack Invoices Shortcodes class.
 *
 * @class    WCJ_PDF_Invoices_Shortcodes
 * @version  1.0.0
 * @category Class
 * @author   Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WCJ_Invoices_Shortcodes' ) ) :

class WCJ_Invoices_Shortcodes extends WCJ_Shortcodes {

    /**
     * Constructor.
     */
    public function __construct() {

		$this->the_shortcodes = array(

			'wcj_invoice_number',
			'wcj_proforma_invoice_number',
			'wcj_packing_slip_number',

			'wcj_invoice_date',
			'wcj_proforma_invoice_date',
			'wcj_packing_slip_date',

		);

		$this->the_atts = array(
			'order_id'     => 0,
			'date_format'  => get_option( 'date_format' ),
			'days'         => 0,
			'invoice_type' => 'invoice',
		);

		parent::__construct();
    }

    /**
     * init_atts.
     */
	function init_atts( $atts ) {

		// Atts
		if ( 0 == $atts['order_id'] ) {
			$atts['order_id'] = ( isset( $_GET['order_id'] ) ) ? $_GET['order_id'] : get_the_ID();
			if ( 0 == $atts['order_id'] ) return false;
		}
		if ( 'shop_order' !== get_post_type( $atts['order_id'] ) ) return false;
				
		// Class properties
		/*if ( ! in_array( $atts['invoice_type'], wcj_enabled_invoice_types_ids() ) ) return false;
		$this->the_invoice = wc_get_invoice( $atts['order_id'], $atts['invoice_type'] );		
		if ( ! $this->the_invoice ) return false;*/	

		return $atts;
	}
		
    /**
     * wcj_invoice_date.
     */
    function wcj_invoice_date( $atts ) {
		return wcj_get_invoice_date( $atts['order_id'], $atts['invoice_type'], $atts['days'], $atts['date_format'] );
	}
	function wcj_proforma_invoice_date( $atts ) {
		return wcj_get_invoice_date( $atts['order_id'], 'proforma_invoice', $atts['days'], $atts['date_format'] );
	}
	function wcj_packing_slip_date( $atts ) {
		return wcj_get_invoice_date( $atts['order_id'], 'packing_slip', $atts['days'], $atts['date_format'] );
	}

    /**
     * wcj_invoice_number.
     */
	function wcj_invoice_number( $atts ) {
		return wcj_get_invoice_number( $atts['order_id'], $atts['invoice_type'] );
	}
	function wcj_proforma_invoice_number( $atts ) {
		return wcj_get_invoice_number( $atts['order_id'], 'proforma_invoice' );
	}
	function wcj_packing_slip_number( $atts ) {
		return wcj_get_invoice_number( $atts['order_id'], 'packing_slip' );
	}
}

endif;

return new WCJ_Invoices_Shortcodes();
