<?php
/**
 * Abstract WooCommerce Jetpack Product Input Fields
 *
 * The WooCommerce Jetpack Product Input Fields abstract class.
 *
 * @class       WCJ_Product_Input_Fields_Abstract
 * @version		1.0.0
 * @category	Class
 * @author 		Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit;
 
if ( ! class_exists( 'WCJ_Product_Input_Fields_Abstract' ) ) :
 
class WCJ_Product_Input_Fields_Abstract {

	/** @var string scope. */
	public $scope = '';
    
    /**
     * Constructor.
     */
    public function __construct() {	
    }
	
    /**
     * get_options.
     */	
	public function get_options() {
		$options = array(
			array(				
				'id'				=> 'wcj_product_input_fields_enabled_' . $this->scope . '_',
				'title'				=> __( 'Enabled', 'woocommerce-jetpack' ),
				'type'				=> 'checkbox',
				'default'			=> 'no',
			),
			array(				
				'id'				=> 'wcj_product_input_fields_required_' . $this->scope . '_',
				'title'				=> __( 'Required', 'woocommerce-jetpack' ),
				'type'				=> 'checkbox',
				'default'			=> 'no',
			),
			array(				
				'id'				=> 'wcj_product_input_fields_title_' . $this->scope . '_',
				'title'				=> __( 'Title', 'woocommerce-jetpack' ),
				'type'				=> 'textarea',
				'default'			=> '',
			),
			array(				
				'id'				=> 'wcj_product_input_fields_placeholder_' . $this->scope . '_',
				'title'				=> __( 'Placeholder', 'woocommerce-jetpack' ),
				'type'				=> 'textarea',
				'default'			=> '',
			),	
			array(				
				'id'				=> 'wcj_product_input_fields_required_message_' . $this->scope . '_',
				'title'				=> __( 'Message on required', 'woocommerce-jetpack' ),
				'type'				=> 'textarea',
				'default'			=> '',
			),	
		);
		return $options;
	}
	
	/**
	 * make_nicer_name.
	 */	
	public function make_nicer_name( $buffer ) {		
		return str_replace( 
			'_wcj_product_input_fields_' . $this->scope . '_', 
			__( 'Product Input Field', 'woocommerce-jetpack' ) . ' (' . $this->scope . ') #', 
			$buffer
		);
	}	
	
	/**
	 * start_making_nicer_name_for_product_input_fields.
	 */	
	public function start_making_nicer_name_for_product_input_fields( $item_id, $item, $_product ) {
		ob_start( array( $this, 'make_nicer_name' ) );
	}
	
	/**
	 * finish_making_nicer_name_for_product_input_fields.
	 */	
	public function finish_making_nicer_name_for_product_input_fields( $item_id, $item, $_product ) {
		ob_end_flush();
	}	
	
	/**
	 * get_value.
	 */	
	public function get_value( $option_name, $product_id, $default ) {
		return false;
	}	
		
	/**
	 * validate_product_input_fields_on_add_to_cart.
	 */
	public function validate_product_input_fields_on_add_to_cart( $passed, $product_id ) {
	
		//$message = date( 'l jS \of F Y h:i:s A' ) . ' ' . print_r( $_POST, true );
		//update_option( 'wcj_log', $message );	
	
		$total_number = apply_filters( 'wcj_get_option_filter', 1, $this->get_value( 'wcj_' . 'product_input_fields' . '_' . $this->scope . '_total_number', $product_id, 1 ) );		
		for ( $i = 1; $i <= $total_number; $i++ ) {			
			$is_required = $this->get_value( 'wcj_product_input_fields_required_' . $this->scope . '_' . $i, $product_id, 'no' );
			if ( ( 'on' === $is_required  || 'yes' === $is_required ) && isset( $_POST[ 'wcj_product_input_fields_' . $this->scope . '_' . $i ] ) && '' == $_POST[ 'wcj_product_input_fields_' . $this->scope . '_' . $i ] ) {
				$passed = false;
				//__( 'Fill text box before adding to cart.', 'woocommerce-jetpack' )
				wc_add_notice( $this->get_value( 'wcj_product_input_fields_required_message_' . $this->scope . '_' . $i, $product_id, '' ), 'error' );				
			}
		}
		return $passed;
	}		
	
	/**
	 * add_product_input_fields_to_frontend.
	 */
	public function add_product_input_fields_to_frontend() {
		global $product;
		//if ( ! $product )
			//	return;				
		$total_number = apply_filters( 'wcj_get_option_filter', 1, $this->get_value( 'wcj_' . 'product_input_fields' . '_' . $this->scope . '_total_number', $product->id, 1 ) );		

		for ( $i = 1; $i <= $total_number; $i++ ) {	
		
			$type =			'text';		
			//$type = 		$this->get_value( 'wcj_product_input_fields_type_' . $this->scope . '_' . $i, $product->id, 'text' );			
			
			$is_enabled = 	$this->get_value( 'wcj_product_input_fields_enabled_' . $this->scope . '_' . $i, $product->id, 'no' );
			$is_required = 	$this->get_value( 'wcj_product_input_fields_required_' . $this->scope . '_' . $i, $product->id, 'no' );
			$title = 		$this->get_value( 'wcj_product_input_fields_title_' . $this->scope . '_' . $i, $product->id, '' );
			$placeholder = 	$this->get_value( 'wcj_product_input_fields_placeholder_' . $this->scope . '_' . $i, $product->id, '' );
										
			if ( 'on' === $is_enabled || 'yes' === $is_enabled ) {
				switch ( $type ) {
					case 'number':
					case 'text':
					case 'checkbox':
						echo '<p>' . $title . '<input type="' . $type . '" name="wcj_product_input_fields_' . $this->scope . '_' . $i . '" placeholder="' . $placeholder . '" value>' . '</p>';
						break;
				}				
			}
		}
	}	
	
	/**
	 * add_product_input_fields_to_cart_item_data.
	 * from $_POST to $cart_item_data
	 */
	public function add_product_input_fields_to_cart_item_data( $cart_item_data, $product_id, $variation_id ) {
		$total_number = apply_filters( 'wcj_get_option_filter', 1, $this->get_value( 'wcj_' . 'product_input_fields' . '_' . $this->scope . '_total_number', $product_id, 1 ) );		
		for ( $i = 1; $i <= $total_number; $i++ ) {	
			if ( isset( $_POST[ 'wcj_product_input_fields_' . $this->scope . '_' . $i ] ) )
				$cart_item_data[ 'wcj_product_input_fields_' . $this->scope . '_' . $i ] = $_POST[ 'wcj_product_input_fields_' . $this->scope . '_' . $i ];
		}
		return $cart_item_data;
	}

	/**
	 * get_cart_item_product_input_fields_from_session.
	 */
	public function get_cart_item_product_input_fields_from_session( $item, $values, $key ) {
		$total_number = apply_filters( 'wcj_get_option_filter', 1, $this->get_value( 'wcj_' . 'product_input_fields' . '_' . $this->scope . '_total_number', $item['product_id'], 1 ) );		
		for ( $i = 1; $i <= $total_number; $i++ ) {		
			if ( array_key_exists( 'wcj_product_input_fields_' . $this->scope . '_' . $i, $values ) )
				$item[ 'wcj_product_input_fields_' . $this->scope . '_' . $i ] = $values[ 'wcj_product_input_fields_' . $this->scope . '_' . $i ];
		}
		return $item;
	}
	
	/**
	 * Adds product input values to order details (and emails).
	 */
	public function add_product_input_fields_to_order_item_name( $name, $item ) {
		
		$total_number = apply_filters( 'wcj_get_option_filter', 1, $this->get_value( 'wcj_' . 'product_input_fields' . '_' . $this->scope . '_total_number', $item['product_id'], 1 ) );		
		if ( $total_number < 1 )
			return $name;
			
		$name .= '<dl style="font-size:smaller;">';
		for ( $i = 1; $i <= $total_number; $i++ ) {			
			if ( array_key_exists( 'wcj_product_input_fields_' . $this->scope . '_' . $i , $item ) ) {
				//$name .= '<p style="font-size:smaller;">' . $item[ 'wcj_product_input_fields_' . $this->scope . '_' . $i ] . '</p>';
				

				$title = $this->get_value( 'wcj_product_input_fields_title_' . $this->scope . '_' . $i, $item['product_id'], '' );
				
				
				$name .= '<dt>'
					  . $title
					  . '</dt>'
					  . '<dd>'
					  . $item[ 'wcj_product_input_fields_' . $this->scope . '_' . $i ]
					  . '</dd>';
			}		
		}
		$name .= '</dl>';
		
		return $name;
	}	
	
	/**
	 * Adds product input values to cart item details.
	 */
	public function add_product_input_fields_to_cart_item_name( $name, $cart_item, $cart_item_key  ) {	
		return $this->add_product_input_fields_to_order_item_name( $name, $cart_item );
	}	
	
	/**
	 * add_product_input_fields_to_order_item_meta.
	 */
	public function add_product_input_fields_to_order_item_meta(  $item_id, $values, $cart_item_key  ) {		
		$total_number = apply_filters( 'wcj_get_option_filter', 1, $this->get_value( 'wcj_' . 'product_input_fields' . '_' . $this->scope . '_total_number', $values['product_id'], 1 ) );		
		for ( $i = 1; $i <= $total_number; $i++ ) {	
			if ( array_key_exists( 'wcj_product_input_fields_' . $this->scope . '_' . $i , $values ) )
				wc_add_order_item_meta( $item_id, '_wcj_product_input_fields_' . $this->scope . '_' . $i, $values[ 'wcj_product_input_fields_' . $this->scope . '_' . $i ] );
		}
	}		
}
 
endif;
