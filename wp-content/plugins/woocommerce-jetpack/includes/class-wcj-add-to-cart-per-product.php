<?php
/**
 * WooCommerce Jetpack Add to Cart per Product
 *
 * The WooCommerce Jetpack Add to Cart per Product class.
 *
 * @class    WCJ_Add_To_Cart_Per_Product
 * @version  1.0.0
 * @category Class
 * @author   Algoritmika Ltd.
 */
 
if ( ! defined( 'ABSPATH' ) ) exit;
 
if ( ! class_exists( 'WCJ_Add_To_Cart_Per_Product' ) ) :
 
class WCJ_Add_To_Cart_Per_Product {
    
    /**
     * Constructor.
     */
    public function __construct() {
 
        // Main hooks
        if ( 'yes' === get_option( 'wcj_add_to_cart_per_product_enabled' ) ) {
		
			add_filter( 'woocommerce_product_single_add_to_cart_text', 	array( $this, 'change_add_to_cart_button_text_single' ), 	PHP_INT_MAX );
			add_filter( 'woocommerce_product_add_to_cart_text', 		array( $this, 'change_add_to_cart_button_text_archive' ), 	PHP_INT_MAX );		
			
			//add_filter( 'add_to_cart_redirect', 						array( $this, 'redirect_to_url' ), 							100 );
			
			add_action( 'add_meta_boxes', 								array( $this, 'add_custom_add_to_cart_meta_box' ) );
			add_action( 'save_post_product', 							array( $this, 'save_custom_add_to_cart_meta_box' ), 		100, 			2 );			
        }        
    
        // Settings hooks
        add_filter( 'wcj_settings_sections', 							array( $this, 'settings_section' ) );
        add_filter( 'wcj_settings_add_to_cart_per_product', 			array( $this, 'get_settings' ), 							100 );
        add_filter( 'wcj_features_status', 								array( $this, 'add_enabled_option' ), 						100 );
    }
	
    /**
     * redirect_to_url.
     *
    public function redirect_to_url( $url ) {
	
		global $product;		
		if ( ! $product )
			return $url;
		$local_custom_add_to_cart_option_id = 'wcj_custom_add_to_cart_local_' . 'url';
		$local_custom_add_to_cart_option_value = get_post_meta( $product->id, '_' . $local_custom_add_to_cart_option_id, true );
		if ( '' != $local_custom_add_to_cart_option_value )
			return $local_custom_add_to_cart_option_value;			
		return $url;	
	}	
	
    /**
     * change_add_to_cart_button_text_single.
     */
    public function change_add_to_cart_button_text_single( $add_to_cart_text ) {
		return $this->change_add_to_cart_button_text( $add_to_cart_text, 'single' );
	}

    /**
     * change_add_to_cart_button_text_archive.
     */
    public function change_add_to_cart_button_text_archive( $add_to_cart_text ) {
		return $this->change_add_to_cart_button_text( $add_to_cart_text, 'archive' );
	}	
	
    /**
     * change_add_to_cart_button_text.
     */
    public function change_add_to_cart_button_text( $add_to_cart_text, $single_or_archive ) {	
		global $product;		
		if ( ! $product )
			return $add_to_cart_text;
		$local_custom_add_to_cart_option_id = 'wcj_custom_add_to_cart_local_' . $single_or_archive;
		$local_custom_add_to_cart_option_value = get_post_meta( $product->id, '_' . $local_custom_add_to_cart_option_id, true );
		if ( '' != $local_custom_add_to_cart_option_value )
			return $local_custom_add_to_cart_option_value;			
		return $add_to_cart_text;
	}		
	
	/**
	 * save_custom_add_to_cart_meta_box.
	 */	
	public function save_custom_add_to_cart_meta_box( $post_id, $post ) {
		// Check that we are saving with custom add to cart metabox displayed.
		if ( ! isset( $_POST['woojetpack_custom_add_to_cart_save_post'] ) )
			return;		
		$option_name = 'wcj_custom_add_to_cart_local_' . 'single';		
		update_post_meta( $post_id, '_' . $option_name, $_POST[ $option_name ] );
		$option_name = 'wcj_custom_add_to_cart_local_' . 'archive';		
		update_post_meta( $post_id, '_' . $option_name, $_POST[ $option_name ] );	
		//$option_name = 'wcj_custom_add_to_cart_local_' . 'url';		
		//update_post_meta( $post_id, '_' . $option_name, $_POST[ $option_name ] );			
	}		
	
	/**
	 * add_custom_add_to_cart_meta_box.
	 */	
	public function add_custom_add_to_cart_meta_box() {	
		add_meta_box( 'wc-jetpack-custom-add-to-cart', __( 'WooCommerce Jetpack: Custom Add to Cart', 'woocommerce-jetpack' ), array( $this, 'create_custom_add_to_cart_meta_box' ), 'product', 'normal', 'high' );
	}
	
	/**
	 * create_custom_add_to_cart_meta_box.
	 */	
	public function create_custom_add_to_cart_meta_box() {
		
		$current_post_id = get_the_ID();
		
		$options = array(				
			'single'			=> __( 'Single product view', 'woocommerce-jetpack' ),
			'archive'			=> __( 'Product category (archive) view', 'woocommerce-jetpack' ),
			//'url'				=> __( 'Add to cart button URL (i.e. redirect).', 'woocommerce-jetpack' ),
		);		
		
		$html = '<table style="width:50%;min-width:300px;">';
		foreach ( $options as $option_key => $option_desc ) {
			$option_type = 'textarea';
			if ( 'url' == $option_key )
				$option_type = 'text';
			$html .= '<tr>';
			$html .= '<th>' . $option_desc . '</th>';
			
			$option_id = 'wcj_custom_add_to_cart_local_' . $option_key;
			$option_value = get_post_meta( $current_post_id, '_' . $option_id, true );		

			if ( 'textarea' === $option_type )
				$html .= '<td style="width:80%;">';
			else	
				$html .= '<td>';
			//switch ( $option_type ) {
				//case 'number':
				//case 'text': 
				//	$html .= '<input style="width:100%;" type="' . $option_type . '" id="' . $option_id . '" name="' . $option_id . '" value="' . $option_value . '">';
				//	break;
				//case 'textarea':
					$html .= '<textarea style="width:100%;" id="' . $option_id . '" name="' . $option_id . '">' . $option_value . '</textarea>';
				//	break;
			//}
			$html .= '</td>';		
			$html .= '</tr>';
		}	
		$html .= '</table>';
		$html .= '<input type="hidden" name="woojetpack_custom_add_to_cart_save_post" value="woojetpack_custom_add_to_cart_save_post">';
		echo $html;
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
 
            array( 
				'title' => __( 'Add to Cart per Product Options', 'woocommerce-jetpack' ), 
				'type' => 'title', 
				'desc' => __( 'When module is enabled, add to cart button text for each product can be changed in "Edit Product".', 'woocommerce-jetpack' ), 
				'id' => 'wcj_add_to_cart_per_product_options' ),
            
            array(
                'title'    => __( 'Add to Cart - per Product', 'woocommerce-jetpack' ),
                'desc'     => '<strong>' . __( 'Enable Module', 'woocommerce-jetpack' ) . '</strong>',
				'desc_tip' => __( 'Add to cart button text on per product basis.', 'woocommerce-jetpack' ),
                'id'       => 'wcj_add_to_cart_per_product_enabled',
                'default'  => 'yes',
                'type'     => 'checkbox',
            ),
        
            array( 'type'  => 'sectionend', 'id' => 'wcj_add_to_cart_per_product_options' ),
        );
        
        return $settings;
    }
 
    /**
     * settings_section.
     */
    function settings_section( $sections ) {    
        $sections['add_to_cart_per_product'] = __( 'Add to Cart - per Product', 'woocommerce-jetpack' );        
        return $sections;
    }    
}
 
endif;
 
return new WCJ_Add_To_Cart_Per_Product();
