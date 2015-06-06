<?php
/**
 * WooCommerce Jetpack Orders Shortcodes
 *
 * The WooCommerce Jetpack Orders Shortcodes class.
 *
 * @class    WCJ_Orders_Shortcodes
 * @version  2.1.3
 * @category Class
 * @author   Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WCJ_Orders_Shortcodes' ) ) :

class WCJ_Orders_Shortcodes extends WCJ_Shortcodes {

    /**
     * Constructor.
     */
    public function __construct() {

		$this->the_shortcodes = array(
			'wcj_order_date',
			'wcj_order_time',
			'wcj_order_number',
			'wcj_order_id',
			'wcj_order_billing_address',
			'wcj_order_shipping_address',
			'wcj_order_subtotal',
			'wcj_order_subtotal_after_discount',
			'wcj_order_total_discount',
			'wcj_order_shipping_tax',
			'wcj_order_total_tax',
			'wcj_order_total_tax_percent',
			'wcj_order_total',
			'wcj_order_currency',
			'wcj_order_total_in_words',
			'wcj_order_total_excl_tax',
			'wcj_order_shipping_price',
			'wcj_order_payment_method',
			'wcj_order_shipping_method',
			'wcj_order_items_total_weight',
			'wcj_order_items_total_quantity',
			'wcj_order_items_total_number',
		);

		parent::__construct();
    }

    /**
     * add_extra_atts.
     */
	function add_extra_atts( $atts ) {		
		$modified_atts = array_merge( array(
			//'order_id'      => ( isset( $_GET['order_id'] ) ) ? $_GET['order_id'] : get_the_ID(),
			'order_id'      => 0,
			'hide_currency' => 'no',
			'excl_tax'      => 'no',
			'date_format'   => get_option( 'date_format' ),			
			'time_format'   => get_option( 'time_format' ),			
			'hide_if_zero'  => 'no',			
		), $atts );
		
		return $modified_atts;
	}	
	
    /**
     * init_atts.
     */	
	function init_atts( $atts ) {	
	
		// Atts
		$atts['excl_tax'] = ( 'yes' === $atts['excl_tax'] ) ? true : false;
		
		if ( 0 == $atts['order_id'] ) $atts['order_id'] = ( isset( $_GET['order_id'] ) ) ? $_GET['order_id'] : get_the_ID();
		if ( 0 == $atts['order_id'] ) $atts['order_id'] = ( isset( $_GET['pdf_invoice'] ) ) ? $_GET['pdf_invoice'] : 0; // PDF Invoices V1 compatibility
		//if ( 0 == $atts['order_id'] ) $atts['order_id'] = get_the_ID();
		if ( 0 == $atts['order_id'] ) return false;
		//if ( 'shop_order' !== get_post_type( $atts['order_id'] ) ) return false;		
	
		// Class properties
		$this->the_order = ( 'shop_order' === get_post_type( $atts['order_id'] ) ) ? wc_get_order( $atts['order_id'] ) : null;
		if ( ! $this->the_order ) return false;
		
		return $atts;
	}
	
    /**
     * wcj_price_shortcode.
     */
	private function wcj_price_shortcode( $raw_price, $atts ) {		
		return ( 'yes' === $atts['hide_if_zero'] && 0 == $raw_price ) ? '' : wcj_price( $raw_price, $this->the_order->get_order_currency(), $atts['hide_currency'] );
	}	

    /**
     * wcj_order_shipping_method.
     */
	function wcj_order_shipping_method( $atts ) {
		return $this->the_order->get_shipping_method();
	}
	
    /**
     * wcj_order_payment_method.
     */
	function wcj_order_payment_method( $atts ) {
		//return $this->the_order->payment_method_title;
		return get_post_meta( $this->the_order->id, '_payment_method_title', true );
	}
	
    /**
     * wcj_order_items_total_weight.
     */
	function wcj_order_items_total_weight( $atts ) {
		$total_weight = 0;
		$the_items = $this->the_order->get_items();
		foreach( $the_items as $the_item ) {
			$the_product = wc_get_product( $the_item['product_id'] );
			$total_weight += $the_item['qty'] * $the_product->get_weight();
		}
		return ( 0 == $total_weight && 'yes' === $atts['hide_if_zero'] ) ? '' : $total_weight;
	}

    /**
     * wcj_order_items_total_quantity.
     */
	function wcj_order_items_total_quantity( $atts ) {
		$total_quantity = 0;
		$the_items = $this->the_order->get_items();
		foreach( $the_items as $the_item ) {
			$total_quantity += $the_item['qty'];
		}
		return ( 0 == $total_quantity && 'yes' === $atts['hide_if_zero'] ) ? '' : $total_quantity;
	}

    /**
     * wcj_order_items_total_number.
     */
	function wcj_order_items_total_number( $atts ) {
		$total_number = count( $this->the_order->get_items() );
		return ( 0 == $total_number && 'yes' === $atts['hide_if_zero'] ) ? '' : $total_number;
	}

    /**
     * wcj_order_billing_address.
     */
	function wcj_order_billing_address( $atts ) {
		return $this->the_order->get_formatted_billing_address();
	}

    /**
     * wcj_order_shipping_address.
     */
	function wcj_order_shipping_address( $atts ) {
		return $this->the_order->get_formatted_shipping_address();
	}

    /**
     * wcj_order_date.
     */
	function wcj_order_date( $atts ) {
		return date_i18n( $atts['date_format'], strtotime( $this->the_order->order_date ) );
	}

    /**
     * wcj_order_time.
     */
	function wcj_order_time( $atts ) {
		return date_i18n( $atts['time_format'], strtotime( $this->the_order->order_date ) );
	}

    /**
     * wcj_order_number.
     */
	function wcj_order_number( $atts ) {
		return $this->the_order->get_order_number();
	}

    /**
     * wcj_order_id.
     */
	function wcj_order_id( $atts ) {
		return $atts['order_id'];
	}
	
    /**
     * wcj_order_shipping_price.
     */
	function wcj_order_shipping_price( $atts ) {
		$the_result = ( true === $atts['excl_tax'] ) ? $this->the_order->get_total_shipping() - $this->the_order->get_shipping_tax() : $this->the_order->get_total_shipping();
		return $this->wcj_price_shortcode( $the_result , $atts );
	}
	
    /**
     * wcj_order_total_discount.
     */
	function wcj_order_total_discount( $atts ) {
		return $this->wcj_price_shortcode( $this->the_order->get_total_discount( $atts['excl_tax'] ) , $atts );		
	}
	
    /**
     * wcj_order_shipping_tax.
     */
	function wcj_order_shipping_tax( $atts ) {
		return $this->wcj_price_shortcode( $this->the_order->get_shipping_tax(), $atts );
	}	
	
    /**
     * wcj_order_total_tax_percent.
     */
	function wcj_order_total_tax_percent( $atts ) {
	
		$order_total_excl_tax = $this->the_order->get_total() - $this->the_order->get_total_tax();
		$order_total_tax = $this->the_order->get_total_tax();
		$order_total_tax_percent = ( 0 == $order_total_excl_tax ) ? 0 : $order_total_tax / $order_total_excl_tax * 100;
		
		$order_total_tax_percent = apply_filters( 'wcj_order_total_tax_percent', $order_total_tax_percent, $this->the_order );
		
		return $order_total_tax_percent;
		//return $this->wcj_price_shortcode( $order_total_tax_percent, $atts );;
	}	
	
    /**
     * wcj_order_total_tax.
     */
	function wcj_order_total_tax( $atts ) {
		$order_total_tax = $this->the_order->get_total_tax();
		$order_total_tax = apply_filters( 'wcj_order_total_tax', $order_total_tax, $this->the_order );
		return $this->wcj_price_shortcode( $order_total_tax, $atts );
		//return wcj_price( $order_total_tax, $this->the_order->get_order_currency(), $atts['hide_currency'] );
	}

    /**
     * wcj_order_subtotal.
     */
	function wcj_order_subtotal( $atts ) {
		$the_subtotal = $this->the_order->get_subtotal();
		if ( isset( $atts['after_discount'] ) && 'yes' === $atts['after_discount'] ) $the_subtotal -= $this->the_order->get_total_discount( true );
		return $this->wcj_price_shortcode( $the_subtotal, $atts );
	}    
	
	/**
     * wcj_order_subtotal_after_discount.
     */
	function wcj_order_subtotal_after_discount( $atts ) {
		$atts['after_discount'] = 'yes';
		wcj_order_subtotal( $atts );
		//$the_subtotal = $this->the_order->get_subtotal() - $this->the_order->get_total_discount( true );
		//return $this->wcj_price_shortcode( $the_subtotal, $atts );			
	}
	
    /**
     * wcj_order_total_excl_tax.
     */
	function wcj_order_total_excl_tax( $atts ) {
		$order_total = $this->the_order->get_total() - $this->the_order->get_total_tax();
		$order_total = apply_filters( 'wcj_order_total_excl_tax', $order_total, $this->the_order );
		return $this->wcj_price_shortcode( $order_total, $atts );
	}	

    /**
     * wcj_order_currency.
     */
	function wcj_order_currency( $atts ) {		
		return $this->the_order->get_order_currency();
	}
	
    /**
     * wcj_order_total.
     */
	function wcj_order_total( $atts ) {
		$order_total = ( true === $atts['excl_tax'] ) ? $this->the_order->get_total() - $this->the_order->get_total_tax() : $this->the_order->get_total();
		return $this->wcj_price_shortcode( $order_total, $atts );
	}
	
    /**
     * wcj_order_total_in_words.
     */
	function wcj_order_total_in_words( $atts ) {		
		$order_total = ( true === $atts['excl_tax'] ) ? $this->the_order->get_total() - $this->the_order->get_total_tax() : $this->the_order->get_total();
		$order_total_dollars = intval( $order_total );
		$order_total_cents = ( $order_total - intval( $order_total ) ) * 100;
		
		$the_number_in_words = '%s %s';
		if ( 0 != $order_total_cents ) $the_number_in_words .= ', %s %s.';
		else $the_number_in_words .= '.';
		$dollars = 'Dollars';//$this->the_order->get_order_currency();
		$cents = 'Cents';
		return sprintf( $the_number_in_words, 
			ucfirst( convert_number_to_words( $order_total_dollars ) ),
			$dollars,
			ucfirst( convert_number_to_words( $order_total_cents ) ),
			$cents );
	}		
}

endif;

return new WCJ_Orders_Shortcodes();
