<?php
/**
 * WooCommerce Jetpack Product Input Fields per Product
 *
 * The WooCommerce Jetpack Product Input Fields per Product class.
 *
 * @class       WCJ_Product_Input_Fields_Per_Product
 * @version		1.0.0
 * @category	Class
 * @author 		Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit;
 
if ( ! class_exists( 'WCJ_Product_Input_Fields_Per_Product' ) ) :
 
class WCJ_Product_Input_Fields_Per_Product extends WCJ_Product_Input_Fields_Abstract {
    
    /**
     * Constructor.
     */
    public function __construct() {
 
		$this->scope = 'local';
 
        // Main hooks
        if ( 'yes' === get_option( 'wcj_product_input_fields_local_enabled' ) ) {			
					
			// Product Edit
			add_action( 'add_meta_boxes', 							array( $this, 'add_local_product_input_fields_meta_box_to_product_edit' ) );
			add_action( 'save_post_product', 						array( $this, 'save_local_product_input_fields_on_product_edit' ), 999, 2 );				

			// Show fields at frontend
			add_action( 'woocommerce_before_add_to_cart_button', 	array( $this, 'add_product_input_fields_to_frontend' ), 100 );			

			// Process from $_POST to cart item data
			add_filter( 'woocommerce_add_to_cart_validation', 		array( $this, 'validate_product_input_fields_on_add_to_cart' ), 100, 2 );			
			add_filter( 'woocommerce_add_cart_item_data', 			array( $this, 'add_product_input_fields_to_cart_item_data' ), 100, 3 );
			// from session
			add_filter( 'woocommerce_get_cart_item_from_session', 	array( $this, 'get_cart_item_product_input_fields_from_session' ), 100, 3 );
			
			// Show details at cart, order details, emails
			add_filter( 'woocommerce_cart_item_name', 				array( $this, 'add_product_input_fields_to_cart_item_name' ), 100, 3 );			
			add_filter( 'woocommerce_order_item_name', 				array( $this, 'add_product_input_fields_to_order_item_name' ), 100, 2 );						

			// Add item meta from cart to order
			add_action( 'woocommerce_add_order_item_meta', 			array( $this, 'add_product_input_fields_to_order_item_meta' ), 100, 3 );

			// Make nicer name for product input fields in order at backend (shop manager)
			add_action( 'woocommerce_before_order_itemmeta', 		array( $this, 'start_making_nicer_name_for_product_input_fields' ), 100, 3 );        
			add_action( 'woocommerce_after_order_itemmeta', 		array( $this, 'finish_making_nicer_name_for_product_input_fields' ), 100, 3 );        
			

        }        
    
        // Settings hooks
        //add_filter( 'wcj_settings_sections', 						array( $this, 'settings_section' ) );
        //add_filter( 'wcj_settings_product_input_fields_local',		array( $this, 'get_settings' ), 100 );
        //add_filter( 'wcj_features_status', 							array( $this, 'add_enabled_option' ), 100 );
    }
	
	/**
	 * get_value.
	 */	
	public function get_value( $option_name, $product_id, $default ) {
		return get_post_meta( $product_id, '_' . $option_name, true );
	}	
		
	/**
	 * Save product input fields on Product Edit.
	 */	
	public function save_local_product_input_fields_on_product_edit( $post_id, $post ) {
		// Check that we are saving with input fields displayed.
		if ( ! isset( $_POST['woojetpack_product_input_fields_save_post'] ) )
			return;
		// Save total product input fields number
		$option_name = 'wcj_product_input_fields_local_total_number';
		$total_input_fields = isset( $_POST[ $option_name ] ) ? $_POST[ $option_name ] : apply_filters( 'wcj_get_option_filter', 1, get_option( 'wcj_product_input_fields_local_total_number_default', 1 ) );		
		update_post_meta( $post_id, '_' . $option_name, $_POST[ $option_name ] );
		// Save enabled, required, title etc.
		$options = $this->get_options();
		for ( $i = 1; $i <= $total_input_fields; $i++ ) {
			foreach ( $options as $option ) {
				if ( isset( $_POST[ $option['id'] . $i ] ) )
					update_post_meta( $post_id, '_' . $option['id'] . $i, $_POST[ $option['id'] . $i ] );
				//else if ( 'wcj_product_input_fields_title_local_' != $option['id'] && 'wcj_product_input_fields_placeholder_local_' != $option['id'] )
				elseif ( 'checkbox' === $option['type'] )
					update_post_meta( $post_id, '_' . $option['id'] . $i, 'off' );
			}
		}			
	}		
	
	/**
	 * add_local_product_input_fields_meta_box_to_product_edit.
	 */	
	public function add_local_product_input_fields_meta_box_to_product_edit() {	
		add_meta_box( 'wc-jetpack-product-input-fields', __( 'WooCommerce Jetpack: Product Input Fields', 'woocommerce-jetpack' ), array( $this, 'create_local_product_input_fields_meta_box' ), 'product', 'normal', 'high' );
	}	
	
	/**
	 * create_local_product_input_fields_meta_box.
	 */	
	public function create_local_product_input_fields_meta_box() {
	
		$meta_box_id = 'product_input_fields';
		$meta_box_desc =  __( 'Product Input Fields', 'woocommerce-jetpack' );
		
		$options = $this->get_options();
		
		// Get total number
		$current_post_id = get_the_ID();		
		$option_name = 'wcj_' . $meta_box_id . '_local_total_number';
		// If none total number set - check for the default
		if ( ! ( $total_number = apply_filters( 'wcj_get_option_filter', 1, get_post_meta( $current_post_id, '_' . $option_name, true ) ) ) )
			$total_number = apply_filters( 'wcj_get_option_filter', 1, get_option( 'wcj_' . $meta_box_id . '_local_total_number_default', 1 ) );	

		// Start html
		$html = '';
		
		// Total number
		$is_disabled = apply_filters( 'get_wc_jetpack_plus_message', '', 'readonly_string' );
		$is_disabled_message = apply_filters( 'get_wc_jetpack_plus_message', '', 'desc' );		
		$html .= '<table>';		
		$html .= '<tr>';
		$html .= '<th>';
		$html .= __( 'Total number of ', 'woocommerce-jetpack' ) . $meta_box_desc;
		$html .= '</th>';
		$html .= '<td>';
		$html .= '<input type="number" id="' . $option_name . '" name="' . $option_name . '" value="' . $total_number . '" ' . $is_disabled . '>';
		$html .= '</td>';
		$html .= '<td>';
		$html .= __( 'Click "Update" product after you change this number.', 'woocommerce-jetpack' ) . '<br>' . $is_disabled_message;		
		$html .= '</td>';		
		$html .= '</td>';
		$html .= '</tr>';
		$html .= '</table>';
		
		// The options		
		$html .= '<h4>' . $meta_box_desc . '</h4>';
		$html .= '<table style="width:100%;">';				
		for ( $i = 1; $i <= $total_number; $i++ ) {
			$html .= '<tr>';
			$html .= '<td>' . '<em>' . ' #' . $i . '</em>' . '</td>';
			foreach ( $options as $option ) {					
				$option_id = $option['id'] . $i;
				$option_value = get_post_meta( $current_post_id, '_' . $option_id, true );		

				$html .= '<th>' . $option['title'] . '</th>';
				if ( 'textarea' === $option['type'] )
					$html .= '<td style="width:20%;">';
				else	
					$html .= '<td>';
					
				if ( 'checkbox' === $option['type'] )
					$is_checked = checked( $option_value, 'on', false );
				
				switch ( $option['type'] ) {
					case 'number':
					case 'text': 
						$html .= '<input type="' . $option['type'] . '" id="' . $option_id . '" name="' . $option_id . '" value="' . $option_value . '">';
						break;
					case 'textarea':
						$html .= '<textarea style="width:100%;" id="' . $option_id . '" name="' . $option_id . '">' . $option_value . '</textarea>';
						break;
					case 'checkbox':										
						$html .= '<input class="checkbox" type="checkbox" name="' . $option_id . '" id="' . $option_id . '" ' . $is_checked . ' />';
						break;
				}
				$html .= '</td>';							
			}
			$html .= '</tr>';
		}	
		$html .= '</table>';
		$html .= '<input type="hidden" name="woojetpack_' . $meta_box_id . '_save_post" value="woojetpack_' . $meta_box_id . '_save_post">';
		
		// Output
		echo $html;		
	}
  
}
 
endif;

return new WCJ_Product_Input_Fields_Per_Product();
