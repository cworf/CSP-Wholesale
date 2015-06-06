<?php
/**
 * WooCommerce Jetpack Product Tabs
 *
 * The WooCommerce Jetpack Product Tabs class.
 *
 * @class		WCJ_Product_Tabs
 * @version		1.2.0
 * @category	Class
 * @author		Algoritmika Ltd.
 */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
 
if ( ! class_exists( 'WCJ_Product_Tabs' ) ) :
 
class WCJ_Product_Tabs {
    
    /**
     * Constructor.
     */
    public function __construct() {
 
        // Main hooks
        if ( get_option( 'wcj_product_tabs_enabled' ) == 'yes' ) {
		
			add_filter( 'woocommerce_product_tabs', array( $this, 'customize_product_tabs' ), 98 );	
			
			if ( 'yes' === get_option( 'wcj_custom_product_tabs_local_enabled' ) ) {
				add_action( 'add_meta_boxes', array( $this, 'add_custom_tabs_meta_box' ) );
				add_action( 'save_post_product', array( $this, 'save_custom_tabs_meta_box' ), 100, 2 );
			}
        }        
    
        // Settings hooks
        add_filter( 'wcj_settings_sections', array( $this, 'settings_section' ) );
        add_filter( 'wcj_settings_product_tabs', array( $this, 'get_settings' ), 100 );
        add_filter( 'wcj_features_status', array( $this, 'add_enabled_option' ), 100 );
    }
	

	/**
	 * Customize the product tabs.
	 */
	function customize_product_tabs( $tabs ) {
	 
		// Unset
		if ( get_option( 'wcj_product_info_product_tabs_description_disable' ) === 'yes' ) 
			unset( $tabs['description'] );
		if ( get_option( 'wcj_product_info_product_tabs_reviews_disable' ) === 'yes' ) 
			unset( $tabs['reviews'] );
		if ( get_option( 'wcj_product_info_product_tabs_additional_information_disable' ) === 'yes' ) 
			unset( $tabs['additional_information'] );
		
		// Priority and Title
		if ( isset( $tabs['description'] ) ) { 
			$tabs['description']['priority'] = apply_filters( 'wcj_get_option_filter', 10, get_option( 'wcj_product_info_product_tabs_description_priority' ) );
			if ( get_option( 'wcj_product_info_product_tabs_description_title' ) !== '' ) 
				$tabs['description']['title'] = get_option( 'wcj_product_info_product_tabs_description_title' );
		}
		if ( isset( $tabs['reviews'] ) ) { 
			$tabs['reviews']['priority'] = apply_filters( 'wcj_get_option_filter', 20, get_option( 'wcj_product_info_product_tabs_reviews_priority' ) );
			if ( get_option( 'wcj_product_info_product_tabs_reviews_title' ) !== '' )
				$tabs['reviews']['title'] = get_option( 'wcj_product_info_product_tabs_reviews_title' );
		}
		if ( isset( $tabs['additional_information'] ) ) {
			$tabs['additional_information']['priority'] = apply_filters( 'wcj_get_option_filter', 30, get_option( 'wcj_product_info_product_tabs_additional_information_priority' ) );
			if ( get_option( 'wcj_product_info_product_tabs_additional_information_title' ) !== '' )
				$tabs['additional_information']['title'] = get_option( 'wcj_product_info_product_tabs_additional_information_title' );
		}
		
		// Add New
		// Add New - Global
		global $product;
		for ( $i = 1; $i <= apply_filters( 'wcj_get_option_filter', 1, get_option( 'wcj_custom_product_tabs_global_total_number', 1 ) ); $i++ ) {
			$key = 'global_' . $i;
			if ( '' != get_option( 'wcj_custom_product_tabs_title_' . $key, '' ) && '' != get_option( 'wcj_custom_product_tabs_content_' . $key, '' ) ) {
			
				// Exclude by product id
				$list_to_exclude = get_option( 'wcj_custom_product_tabs_title_global_hide_in_product_ids_' . $i );				
				if ( '' != $list_to_exclude ) {
					$array_to_exclude = explode( ',', $list_to_exclude );
					if ( $product && $array_to_exclude && in_array( $product->id, $array_to_exclude ) )
						continue;
				}
				
				// Exclude by product category
				$list_to_exclude = get_option( 'wcj_custom_product_tabs_title_global_hide_in_cats_ids_' . $i );
				if ( '' != $list_to_exclude ) {
					$array_to_exclude = explode( ',', $list_to_exclude );
					
					$do_exclude = false;
					$product_categories_objects = get_the_terms( $product->id, 'product_cat' );
					if ( $product_categories_objects && ! empty( $product_categories_objects ) ) {
						foreach ( $product_categories_objects as $product_categories_object )	{				
							if ( $product && $array_to_exclude && in_array( $product_categories_object->term_id, $array_to_exclude ) ) {
								$do_exclude = true;
								break;
							}
						}
					}
					if ( $do_exclude )
						continue;
				}

				// Include by product id
				$list_to_include = get_option( 'wcj_custom_product_tabs_title_global_show_in_product_ids_' . $i );				
				if ( '' != $list_to_include ) {
					$array_to_include = explode( ',', $list_to_include );
					// If NOT in array then hide this tab for this product
					if ( $product && $array_to_include && ! in_array( $product->id, $array_to_include ) )
						continue;						
				}

				// Include by product category
				$list_to_include = get_option( 'wcj_custom_product_tabs_title_global_show_in_cats_ids_' . $i );				
				if ( '' != $list_to_include ) {
					$array_to_include = explode( ',', $list_to_include );
					
					$do_include = false;
					$product_categories_objects = get_the_terms( $product->id, 'product_cat' );
					if ( $product_categories_objects && ! empty( $product_categories_objects ) ) {
						foreach ( $product_categories_objects as $product_categories_object )	{				
							if ( $product && $array_to_include && in_array( $product_categories_object->term_id, $array_to_include ) ) {
								$do_include = true;
								break;
							}
						}
					}
					if ( ! $do_include )
						continue;
				}				
			
				// Adding the tab
				$tabs[ $key ] = array(
					'title' 	=> get_option( 'wcj_custom_product_tabs_title_' . $key ),
					'priority' 	=> get_option( 'wcj_custom_product_tabs_priority_' . $key, 40 ),
					'callback' 	=> array( $this, 'create_new_custom_product_tab_global' ),
				);
			}
		}
		// Add New - Local		
		$current_post_id = get_the_ID();
		$option_name = 'wcj_custom_product_tabs_local_total_number';
		if ( ! ( $total_custom_tabs = get_post_meta( $current_post_id, '_' . $option_name, true ) ) ) 
			$total_custom_tabs = apply_filters( 'wcj_get_option_filter', 1, get_option( 'wcj_custom_product_tabs_local_total_number_default', 1 ) );
		if ( 'yes' !== get_option( 'wcj_custom_product_tabs_local_enabled' ) )
			$total_custom_tabs = 0;			
			
		for ( $i = 1; $i <= $total_custom_tabs; $i++ ) {
			$key = 'local_' . $i;
			
			$tab_priority = get_post_meta( $current_post_id, '_' . 'wcj_custom_product_tabs_priority_' . $key, true );
			if ( ! $tab_priority )
				$tab_priority = (50 + $i - 1);
			
			if ( '' != get_post_meta( $current_post_id, '_' . 'wcj_custom_product_tabs_title_' . $key, true ) && '' != get_post_meta( $current_post_id, '_' . 'wcj_custom_product_tabs_content_' . $key, true ) )
				$tabs[ $key ] = array(
					'title' 	=> get_post_meta( $current_post_id, '_' . 'wcj_custom_product_tabs_title_' . $key, true ),
					'priority' 	=> $tab_priority,
					'callback' 	=> array( $this, 'create_new_custom_product_tab_local' ),
				);
		}		
		
		return $tabs;
	}
	
	function create_new_custom_product_tab_local( $key, $tab ) {
		$current_post_id = get_the_ID();
		echo apply_filters( 'the_content', get_post_meta( $current_post_id, '_' . 'wcj_custom_product_tabs_content_' . $key, true ) );
	}	
	
	function create_new_custom_product_tab_global( $key, $tab ) {
		echo apply_filters( 'the_content', get_option( 'wcj_custom_product_tabs_content_' . $key ) );
	}
	
	/**
	 * save_custom_tabs_meta_box.
	 */	
	public function save_custom_tabs_meta_box( $post_id, $post ) {
		// Check that we are saving with custom tab metabox displayed.
		if ( ! isset( $_POST['woojetpack_custom_tabs_save_post'] ) )
			return;
		// Save: total custom tabs number
		$option_name = 'wcj_custom_product_tabs_local_total_number';
		$total_custom_tabs = isset( $_POST[ $option_name ] ) ? $_POST[ $option_name ] : apply_filters( 'wcj_get_option_filter', 1, get_option( 'wcj_custom_product_tabs_local_total_number_default', 1 ) );		
		update_post_meta( $post_id, '_' . $option_name, $_POST[ $option_name ] );
		// Save: title, priority, content
		$option_names = array(
			'wcj_custom_product_tabs_title_local_',
			'wcj_custom_product_tabs_priority_local_',
			'wcj_custom_product_tabs_content_local_',
		);		
		for ( $i = 1; $i <= $total_custom_tabs; $i++ ) {
			foreach ( $option_names as $option_name )
				update_post_meta( $post_id, '_' . $option_name . $i, $_POST[ $option_name . $i ] );
		}
	}		
	
	/**
	 * add_custom_tabs_meta_box.
	 */	
	public function add_custom_tabs_meta_box() {	
		add_meta_box( 'wc-jetpack-custom-tabs', 'WooCommerce Jetpack: Custom Tabs', array( $this, 'create_custom_tabs_meta_box' ), 'product', 'normal', 'high' );
	}
	
	/**
	 * create_custom_tabs_meta_box.
	 */	
	public function create_custom_tabs_meta_box() {
		$current_post_id = get_the_ID();
		$option_name = 'wcj_custom_product_tabs_local_total_number';
		if ( ! ( $total_custom_tabs = get_post_meta( $current_post_id, '_' . $option_name, true ) ) ) 
			$total_custom_tabs = apply_filters( 'wcj_get_option_filter', 1, get_option( 'wcj_custom_product_tabs_local_total_number_default', 1 ) );	
		$html = '';

		$is_disabled = apply_filters( 'get_wc_jetpack_plus_message', '', 'readonly_string' );
		$is_disabled_message = apply_filters( 'get_wc_jetpack_plus_message', '', 'desc' );
				
		$html .= '<table>';		
		$html .= '<tr>';
		$html .= '<th>';
		$html .= __( 'Total number of custom tabs', 'woocommerce-jetpack' );
		$html .= '</th>';
		$html .= '<td>';
		$html .= '<input type="number" id="' . $option_name . '" name="' . $option_name . '" value="' . $total_custom_tabs . '" ' . $is_disabled . '>';
		$html .= '</td>';
		$html .= '<td>';
		$html .= __( 'Click "Update" product after you change this number.', 'woocommerce-jetpack' ) . '<br>' . $is_disabled_message;		
		$html .= '</td>';		
		$html .= '</td>';
		$html .= '</tr>';
		$html .= '</table>';
		
		$options = array(
			array(				
				'id'				=> 'wcj_custom_product_tabs_title_local_',
				'title'				=> __( 'Title', 'woocommerce-jetpack' ),
				'type'				=> 'text',
			),
			array(				
				'id'				=> 'wcj_custom_product_tabs_priority_local_',
				'title'				=> __( 'Priority', 'woocommerce-jetpack' ),
				'type'				=> 'number',
			),
			array(				
				'id'				=> 'wcj_custom_product_tabs_content_local_',
				'title'				=> __( 'Content', 'woocommerce-jetpack' ),
				'type'				=> 'textarea',
			),			
		);		
		$html .= '<h4>' . __( 'Customize the tab(s)', 'woocommerce-jetpack' ) . '</h4>';
		$html .= '<table style="width:100%;">';				
		for ( $i = 1; $i <= $total_custom_tabs; $i++ ) {
			$html .= '<tr>';
			$html .= '<td style="font-style:italic;">' . __( 'Custom Product Tab', 'woocommerce-jetpack' ) . ' #' . $i . '</td>';
			foreach ( $options as $option ) {					
				$option_id = $option['id'] . $i;
				$option_value = get_post_meta( $current_post_id, '_' . $option_id, true );		
				if ( ! $option_value && 'wcj_custom_product_tabs_priority_local_' == $option['id'] )
					$option_value = 50;
				$html .= '<th>' . $option['title'] . '</th>';
				if ( 'textarea' === $option['type'] )
					$html .= '<td style="width:30%;">';
				else	
					$html .= '<td>';
				switch ( $option['type'] ) {
					case 'number':
					case 'text': 
						$html .= '<input type="' . $option['type'] . '" id="' . $option_id . '" name="' . $option_id . '" value="' . $option_value . '">';
						break;
					case 'textarea':
						$html .= '<textarea style="width:100%;" id="' . $option_id . '" name="' . $option_id . '">' . $option_value . '</textarea>';
						break;
				}
				$html .= '</td>';							
			}
			$html .= '</tr>';
		}	
		$html .= '</table>';
		$html .= '<input type="hidden" name="woojetpack_custom_tabs_save_post" value="woojetpack_custom_tabs_save_post">';
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
     * settings_section.
     */
    function settings_section( $sections ) {    
        $sections['product_tabs'] = __( 'Product Tabs', 'woocommerce-jetpack' );        
        return $sections;
    }	
    
    /**
     * get_settings.
     */    
    function get_settings() {
 
        $settings = array(
 
            array( 'title' => __( 'Product Tabs Options', 'woocommerce-jetpack' ), 'type' => 'title', 'desc' => '', 'id' => 'wcj_product_tabs_options' ),
            
            array(
                'title'    => __( 'Product Tabs', 'woocommerce-jetpack' ),
                'desc'     => __( 'Enable the Product Tabs feature', 'woocommerce-jetpack' ),
                'desc_tip' => __( 'Add custom product tabs - globally or per product. Customize or completely remove WooCommerce default product tabs.', 'woocommerce-jetpack' ),
                'id'       => 'wcj_product_tabs_enabled',
                'default'  => 'yes',
                'type'     => 'checkbox',
            ),
        
            array( 'type'  => 'sectionend', 'id' => 'wcj_product_tabs_options' ),
			
			array( 'title' 	=> __( 'Custom Product Tabs Options', 'woocommerce-jetpack' ), 'type' => 'title', 'desc' => 'This section lets you add custom single product tabs.', 'id' => 'wcj_custom_product_tabs_options' ),
			
			array(
				'title' 	=> __( 'Custom Product Tabs Number', 'woocommerce-jetpack' ),
				'desc_tip' 	=> __( 'Click "Save changes" after you change this number.', 'woocommerce-jetpack' ),
				'id' 		=> 'wcj_custom_product_tabs_global_total_number',
				'default'	=> 1,
				'type' 		=> 'number',
				'desc' 	   => apply_filters( 'get_wc_jetpack_plus_message', '', 'desc' ),
				'custom_attributes'	
						   => apply_filters( 'get_wc_jetpack_plus_message', '', 'readonly' ),					
			),				
        );
		
		for ( $i = 1; $i <= apply_filters( 'wcj_get_option_filter', 1, get_option( 'wcj_custom_product_tabs_global_total_number', 1 ) ); $i++ ) {		
			$settings = array_merge( $settings, 
				array(			
					array(
						'title' 	=> __( 'Custom Product Tab', 'woocommerce-jetpack' ) . ' #' . $i,
						'desc'		=> __( 'Title', 'woocommerce-jetpack' ),
						'id' 		=> 'wcj_custom_product_tabs_title_global_' . $i,
						'default'	=> '',
						'type' 		=> 'text',
						'css'	    => 'width:30%;min-width:300px;',
					),				
					array(
						'title' 	=> '',
						'desc'		=> __( 'Priority (i.e. Order)', 'woocommerce-jetpack' ),
						'id' 		=> 'wcj_custom_product_tabs_priority_global_' . $i,
						'default'	=> (40 + $i - 1),
						'type' 		=> 'number',
					),
					array(
						'title' 	=> '',
						'desc'		=> __( 'Content', 'woocommerce-jetpack' ),
						'desc_tip'	=> __( 'You can use shortcodes here...', 'woocommerce-jetpack' ),
						'id' 		=> 'wcj_custom_product_tabs_content_global_' . $i,
						'default'	=> '',
						'type' 		=> 'textarea',
						'css'	    => 'width:50%;min-width:300px;height:200px;',	
					),
					array(
						'title' 	=> '',
						'desc' 		=> __( 'Comma separated PRODUCT IDs to HIDE this tab', 'woocommerce-jetpack' ),
						'desc_tip'	=> __( 'To hide this tab from some products, enter product IDs here.', 'woocommerce-jetpack' ),
						'id' 		=> 'wcj_custom_product_tabs_title_global_hide_in_product_ids_' . $i,
						'default'	=> '',
						'type' 		=> 'text',
						'css'	    => 'width:30%;min-width:300px;',
					),
					array(
						'title' 	=> '',
						'desc' 		=> __( 'Comma separated CATEGORY IDs to HIDE this tab', 'woocommerce-jetpack' ),
						'desc_tip'	=> __( 'To hide this tab from some categories, enter category IDs here.', 'woocommerce-jetpack' ),
						'id' 		=> 'wcj_custom_product_tabs_title_global_hide_in_cats_ids_' . $i,
						'default'	=> '',
						'type' 		=> 'text',
						'css'	    => 'width:30%;min-width:300px;',
					),
					array(
						'title' 	=> '',
						'desc' 		=> __( 'Comma separated PRODUCT IDs to SHOW this tab', 'woocommerce-jetpack' ),
						'desc_tip'	=> __( 'To show this tab only for some products, enter product IDs here.', 'woocommerce-jetpack' ),
						'id' 		=> 'wcj_custom_product_tabs_title_global_show_in_product_ids_' . $i,
						'default'	=> '',
						'type' 		=> 'text',
						'css'	    => 'width:30%;min-width:300px;',
					),
					array(
						'title' 	=> '',
						'desc' 		=> __( 'Comma separated CATEGORY IDs to SHOW this tab', 'woocommerce-jetpack' ),
						'desc_tip'	=> __( 'To show this tab only for some categories, enter category IDs here.', 'woocommerce-jetpack' ),
						'id' 		=> 'wcj_custom_product_tabs_title_global_show_in_cats_ids_' . $i,
						'default'	=> '',
						'type' 		=> 'text',
						'css'	    => 'width:30%;min-width:300px;',
					),						
				) 
			);
		}

		$settings = array_merge( $settings, array(
		
			array( 'type' 	=> 'sectionend', 'id' => 'wcj_custom_product_tabs_options' ),				
			
			array( 'title' 	=> __( 'Local Custom Product Tabs', 'woocommerce-jetpack' ), 'type' => 'title', 'desc' => 'This section lets you set defaults for local custom tabs.', 'id' => 'wcj_custom_product_tabs_options_local' ),
			
			array(
				'title' 	=> __( 'Enable Custom Product Tabs', 'woocommerce-jetpack' ),
				//'desc' 		=> __( 'Remove tab from product page', 'woocommerce-jetpack' ),
				'id' 		=> 'wcj_custom_product_tabs_local_enabled',
				'default'	=> 'yes',
				'type' 		=> 'checkbox',
			),			
			
			array(
				'title' 	=> __( 'Default Local Custom Product Tabs Number', 'woocommerce-jetpack' ),
				'id' 		=> 'wcj_custom_product_tabs_local_total_number_default',
				'default'	=> 1,
				'type' 		=> 'number',
				'desc' 	   => apply_filters( 'get_wc_jetpack_plus_message', '', 'desc' ),
				'custom_attributes'	
						   => apply_filters( 'get_wc_jetpack_plus_message', '', 'readonly' ),					
			),	

			array( 'type' 	=> 'sectionend', 'id' => 'wcj_custom_product_tabs_options_local' ),
			
			/**/			
			array( 'title' 	=> __( 'WooCommerce Standard Product Tabs Options', 'woocommerce-jetpack' ), 'type' => 'title', 'desc' => 'This section lets you customize single product tabs.', 'id' => 'wcj_product_info_product_tabs_options' ),
		
			array(
				'title' 	=> __( 'Description Tab', 'woocommerce-jetpack' ),
				'desc' 		=> __( 'Remove tab from product page', 'woocommerce-jetpack' ),
				'id' 		=> 'wcj_product_info_product_tabs_description_disable',
				'default'	=> 'no',
				'type' 		=> 'checkbox',
			),
			
			array(
				'title'    => __( 'Priority (i.e. Order)', 'woocommerce-jetpack' ),
				'id'       => 'wcj_product_info_product_tabs_description_priority',
				'default'  => 10,
				'type'     => 'number',
				'desc' 	   => apply_filters( 'get_wc_jetpack_plus_message', '', 'desc' ),
				'custom_attributes'	
						   => apply_filters( 'get_wc_jetpack_plus_message', '', 'readonly' ),					
			),
			
			array(
				'title'    => __( 'Title', 'woocommerce-jetpack' ),
				'desc_tip' 	   => __( 'Leave blank for WooCommerce defaults', 'woocommerce-jetpack' ),
				'id'       => 'wcj_product_info_product_tabs_description_title',
				'default'  => '',
				'type'     => 'text',
			),	

			array(
				'title' 	=> __( 'Additional Information Tab', 'woocommerce-jetpack' ),
				'desc' 		=> __( 'Remove tab from product page', 'woocommerce-jetpack' ),
				'id' 		=> 'wcj_product_info_product_tabs_additional_information_disable',
				'default'	=> 'no',
				'type' 		=> 'checkbox',
			),
			
			array(
				'title'    => __( 'Priority (i.e. Order)', 'woocommerce-jetpack' ),
				'id'       => 'wcj_product_info_product_tabs_additional_information_priority',
				'default'  => 20,
				'type'     => 'number',
				'desc' 	   => apply_filters( 'get_wc_jetpack_plus_message', '', 'desc' ),
				'custom_attributes'	
						   => apply_filters( 'get_wc_jetpack_plus_message', '', 'readonly' ),				
			),

			array(
				'title'    => __( 'Title', 'woocommerce-jetpack' ),
				'desc_tip' => __( 'Leave blank for WooCommerce defaults', 'woocommerce-jetpack' ),
				'id'       => 'wcj_product_info_product_tabs_additional_information_title',
				'default'  => '',
				'type'     => 'text',
			),			
		
			array(
				'title' 	=> __( 'Reviews Tab', 'woocommerce-jetpack' ),
				'desc' 		=> __( 'Remove tab from product page', 'woocommerce-jetpack' ),
				'id' 		=> 'wcj_product_info_product_tabs_reviews_disable',
				'default'	=> 'no',
				'type' 		=> 'checkbox',
			),

			array(
				'title'    => __( 'Priority (i.e. Order)', 'woocommerce-jetpack' ),
				'id'       => 'wcj_product_info_product_tabs_reviews_priority',
				'default'  => 30,
				'type'     => 'number',
				'desc' 	   => apply_filters( 'get_wc_jetpack_plus_message', '', 'desc' ),
				'custom_attributes'	
						   => apply_filters( 'get_wc_jetpack_plus_message', '', 'readonly' ),				
			),
			
			array(
				'title'    => __( 'Title', 'woocommerce-jetpack' ),
				'desc_tip' 	   => __( 'Leave blank for WooCommerce defaults', 'woocommerce-jetpack' ),
				'id'       => 'wcj_product_info_product_tabs_reviews_title',
				'default'  => '',
				'type'     => 'text',
			),
			
			array( 'type' 	=> 'sectionend', 'id' => 'wcj_product_info_product_tabs_options' ),

		) );	
        
        return $settings;
    }
}
 
endif;
 
return new WCJ_Product_Tabs();
