<?php
/**
 * WooCommerce Jetpack Order Items Shortcodes
 *
 * The WooCommerce Jetpack Order Items Shortcodes class.
 *
 * @class    WCJ_Order_Items_Shortcodes
 * @version  1.0.0
 * @category Class
 * @author   Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WCJ_Order_Items_Shortcodes' ) ) :

class WCJ_Order_Items_Shortcodes extends WCJ_Shortcodes {

    /**
     * Constructor.
     */
    public function __construct() {

		$this->the_shortcodes = array(
			'wcj_order_items_table',
		);

		parent::__construct();
    }

    /**
     * add_extra_atts.
     */
	function add_extra_atts( $atts ) {
		$modified_atts = array_merge( array(
			'order_id'           => ( isset( $_GET['order_id'] ) ) ? $_GET['order_id'] : get_the_ID(),
			'hide_currency'      => 'no',
			'table_class'        => '',
			'shipping_as_item'   => '',//__( 'Shipping', 'woocommerce-jetpack' ),
			'discount_as_item'   => '',//__( 'Discount', 'woocommerce-jetpack' ),
			'columns'            => '',
			'columns_titles'     => '',
			'columns_styles'     => '',
			'tax_percent_format' => '%.2f %%',
			'item_image_width'   => 0,
			'item_image_height'  => 0,
		), $atts );
		return $modified_atts;
	}

    /**
     * init_atts.
     */
	function init_atts( $atts ) {
		$this->the_order = ( 'shop_order' === get_post_type( $atts['order_id'] ) ) ? wc_get_order( $atts['order_id'] ) : null;
		if ( ! $this->the_order ) return false;
		return $atts;
	}
	
    /**
     * wcj_price_shortcode.
     */
	private function wcj_price_shortcode( $raw_price, $atts ) {
		return wcj_price( $raw_price, $this->the_order->get_order_currency(), $atts['hide_currency'] );
	}	

    /**
     * add_item.
     */
    private function add_item( $items, $new_item_args = array() ) {
		if ( empty ( $new_item_args ) ) return $items;
		extract( $new_item_args );
		// Create item
		$items[] = array(
			'is_custom'			=> true,
			'name'				=> $name,
			'type' 				=> 'line_item',
			'qty' 				=> $qty,
			'line_subtotal' 	=> $line_subtotal,
			'line_total' 		=> $line_total,
			'line_tax' 			=> $line_tax,
			'line_subtotal_tax' => $line_subtotal_tax,
			'item_meta'			=> array(
				'_qty' 					=> array( $qty ),
				'_line_subtotal' 		=> array( $line_subtotal ),
				'_line_total' 			=> array( $line_total ),
				'_line_tax' 			=> array( $line_tax ),
				'_line_subtotal_tax' 	=> array( $line_subtotal_tax ),
			),
		);
		return $items;
	}

    /**
     * wcj_order_items_table.
     */
    function wcj_order_items_table( $atts, $content = '' ) {

		$html = '';
		$the_order = $this->the_order;

		// Get columns
		$columns = explode( '|', $atts['columns'] );
		if ( empty( $columns ) ) return '';
		$columns_total_number = count( $columns );
		// Check all possible args
		$columns_titles = ( '' == $atts['columns_titles'] ) ? array() : explode( '|', $atts['columns_titles'] );
		$columns_styles = ( '' == $atts['columns_styles'] ) ? array() : explode( '|', $atts['columns_styles'] );
		//if ( ! ( $columns_total_number === count( $columns_titles ) === count( $columns_styles ) ) ) return '';
		
		// The Items
		$the_items = $the_order->get_items();

		// Shipping as item
		if ( '' != $atts['shipping_as_item'] && $the_order->get_total_shipping() > 0 ) {
			$name           = str_replace( '%shipping_method_name%', $the_order->get_shipping_method(), $atts['shipping_as_item'] );
			$total_tax_excl = $the_order->get_total_shipping();
			$tax            = $the_order->get_shipping_tax();
			
			$the_items = $this->add_item( $the_items, array( 'name' => $name, 'qty' => 1, 'line_subtotal' => $total_tax_excl, 'line_total' => $total_tax_excl, 'line_tax' => $tax, 'line_subtotal_tax' => $tax, ) );
		}

		// Discount as item
		if ( '' != $atts['discount_as_item'] && $the_order->get_total_discount( true ) > 0 ) {
			$name           = $atts['discount_as_item'];
			$total_tax_excl = $the_order->get_total_discount( true );
			$tax            = $the_order->get_total_discount( false ) - $total_tax_excl;
			
			$the_items = $this->add_item( $the_items, array( 'name' => $name, 'qty' => 1, 'line_subtotal' => $total_tax_excl, 'line_total' => $total_tax_excl, 'line_tax' => $tax, 'line_subtotal_tax' => $tax, ) );
		}

		// Starting data[] by adding columns titles
		$data = array();
		foreach( $columns_titles as $column_title ) {
			$data[0][] = $column_title;
		}
		// Items to data[]
		$item_counter = 0;
		foreach ( $the_items as $item ) {
			$item['is_custom'] = ( isset( $item['is_custom'] ) ) ? true : false;
			$the_product = ( true === $item['is_custom'] ) ? null : $the_order->get_product_from_item( $item );
			$item_counter++;			
			// Columns
			foreach( $columns as $column ) {				
				switch ( $column ) {
					case 'item_number':
						$data[ $item_counter ][] = $item_counter;
						break;
					case 'item_name':						
						//$data[ $item_counter ][] = ( true === $item['is_custom'] ) ? $item['name'] : $the_product->get_title();
						if ( true === $item['is_custom'] ) {
							$data[ $item_counter ][] = $item['name'];
						} else {
							$the_item_title = $the_product->get_title();
							// Variation (if needed)
							if ( $the_product->is_type( 'variation' ) )
								$the_item_title .= '<div style="font-size:smaller;">' . wc_get_formatted_variation( $the_product->variation_data, true ) . '</div>';
							$data[ $item_counter ][] = $the_item_title;
						}
						break;
					case 'item_thumbnail':
						//$data[ $item_counter ][] = $the_product->get_image();
						$image_id = ( true === $item['is_custom'] ) ? 0 : $the_product->get_image_id();
						$image_src = ( 0 != $image_id ) ? wp_get_attachment_image_src( $image_id ) : wc_placeholder_img_src();
						if ( is_array( $image_src ) ) $image_src = $image_src[0];
						$maybe_width  = ( 0 != $atts['item_image_width'] )  ? ' width="'  . $atts['item_image_width']  . '"' : '';
						$maybe_height = ( 0 != $atts['item_image_height'] ) ? ' height="' . $atts['item_image_height'] . '"' : '';
						$data[ $item_counter ][] = '<img src="' . $image_src . '"' . $maybe_width . $maybe_height . '>';
						break;
					case 'item_sku':
						$data[ $item_counter ][] = ( true === $item['is_custom'] ) ? '' : $the_product->get_sku();
						break;
					case 'item_quantity':
						$data[ $item_counter ][] = $item['qty'];
						break;
					case 'item_total_tax_excl':
						$data[ $item_counter ][] = $this->wcj_price_shortcode( $the_order->get_item_total( $item, false, true ), $atts );
						break;
					case 'item_total_tax_incl':
						$data[ $item_counter ][] = $this->wcj_price_shortcode( $the_order->get_item_total( $item, true, true ), $atts );
						break;
					case 'item_subtotal_tax_excl':
						$data[ $item_counter ][] = $this->wcj_price_shortcode( $the_order->get_item_subtotal( $item, false, true ), $atts );
						break;
					case 'item_subtotal_tax_incl':
						$data[ $item_counter ][] = $this->wcj_price_shortcode( $the_order->get_item_subtotal( $item, true, true ), $atts );
						break;
					case 'item_tax':
						$data[ $item_counter ][] = $this->wcj_price_shortcode( $the_order->get_item_tax( $item, true ), $atts );
						break;
					case 'line_total_tax_excl':
						$line_total_tax_excl = $the_order->get_line_total( $item, false, true );
						$line_total_tax_excl = apply_filters( 'wcj_line_total_tax_excl', $line_total_tax_excl, $the_order );
						$data[ $item_counter ][] = $this->wcj_price_shortcode( $line_total_tax_excl, $atts );
						break;
					case 'line_total_tax_incl':
						$data[ $item_counter ][] = $this->wcj_price_shortcode( $the_order->get_line_total( $item, true, true ), $atts );
						break;
					case 'line_subtotal_tax_excl':
						$data[ $item_counter ][] = $this->wcj_price_shortcode( $the_order->get_line_subtotal( $item, false, true ), $atts );
						break;
					case 'line_subtotal_tax_incl':
						$data[ $item_counter ][] = $this->wcj_price_shortcode( $the_order->get_line_subtotal( $item, true, true ), $atts );
						break;
					case 'line_tax':
						$line_tax = $the_order->get_line_tax( $item );
						$line_tax = apply_filters( 'wcj_line_tax', $line_tax, $the_order );
						$data[ $item_counter ][] = $this->wcj_price_shortcode( $line_tax, $atts );					
						break;
					case 'item_tax_percent':
						$item_total = $the_order->get_item_total( $item, false, true );
						$item_tax_percent = ( 0 != $item_total ) ? $the_order->get_item_tax( $item, false ) / $item_total * 100 : 0;
						$data[ $item_counter ][] = sprintf( $atts['tax_percent_format'], $item_tax_percent );
						break;
					case 'line_tax_percent':
						$line_total = $the_order->get_line_total( $item, false, true );
						$line_tax_percent = ( 0 != $line_total ) ? $the_order->get_line_tax( $item ) / $line_total * 100 : 0;
						$line_tax_percent = apply_filters( 'wcj_line_tax_percent', $line_tax_percent, $the_order );
						$data[ $item_counter ][] = sprintf( $atts['tax_percent_format'], $line_tax_percent );
						break;
					default:
						$data[ $item_counter ][] = '';
				}
			}
		}

		$html = wcj_get_table_html( $data, array(
			'table_class'        => $atts['table_class'],
			'table_heading_type' => 'horizontal',
			'columns_classes'    => array(),
			'columns_styles'     => $columns_styles,
		) );

		return $html;
	}
}

endif;

return new WCJ_Order_Items_Shortcodes();
