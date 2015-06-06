<?php
/**
 * WooCommerce Jetpack Product Input Fields
 *
 * The WooCommerce Jetpack Product Input Fields class.
 *
 * @class    WCJ_Product_Input_Fields
 * @version  1.0.0
 * @category Class
 * @author   Algoritmika Ltd.
 */
 
if ( ! defined( 'ABSPATH' ) ) exit;
 
if ( ! class_exists( 'WCJ_Product_Input_Fields' ) ) :
 
class WCJ_Product_Input_Fields {
    
    /**
     * Constructor.
     */
    public function __construct() {
	
		include_once( 'input-fields/class-wcj-product-input-fields-abstract.php' );
 
        // Main hooks
        if ( 'yes' === get_option( 'wcj_product_input_fields_enabled' ) ) {
			
			include_once( 'input-fields/class-wcj-product-input-fields-global.php' );
			include_once( 'input-fields/class-wcj-product-input-fields-per-product.php' );
			
			if ( 'yes' === get_option( 'wcj_product_input_fields_global_enabled' ) || 'yes' === get_option( 'wcj_product_input_fields_local_enabled' ) ) {	
				add_action( 'wp_enqueue_scripts' , array( $this, 'enqueue_scripts' ) );	
				add_action( 'init', array( $this, 'register_scripts' ) );							
			}				
        }        
    
        // Settings hooks
        add_filter( 'wcj_settings_sections',      array( $this, 'settings_section' ) );
        add_filter( 'wcj_settings_product_input_fields', array( $this, 'get_settings' ), 100 );
        add_filter( 'wcj_features_status',        array( $this, 'add_enabled_option' ), 100 );
    }
		
    /**
     * register_script.
     */	
    public function register_scripts() {
        //wp_register_script( 'wcj-product-input-fields', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'js/product-input-fields.js', array( 'jquery' ), false, true );
        wp_register_script( 'wcj-product-input-fields', WCJ()->plugin_url() . '/includes/js/product-input-fields.js', array( 'jquery' ), false, true );
    }

    /**
     * enqueue_checkout_script.
     */	
    public function enqueue_scripts() {
        if( ! is_product() ) return;		
		wp_enqueue_script( 'wcj-product-input-fields' );
    }				
	
    /**
     * get_options.
     */	
	public function get_options() {
	
		$product_input_fields_abstract = new WCJ_Product_Input_Fields_Abstract();
	
		$product_input_fields_abstract->scope = 'global';
		
		return $product_input_fields_abstract->get_options();
		
		/*$options = array(
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
		return $options;*/
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
 
            array( 'title' => __( 'Product Input Fields Options', 'woocommerce-jetpack' ), 'type' => 'title', 'desc' => __( 'Product Input Fields.', 'woocommerce-jetpack' ), 'id' => 'wcj_product_input_fields_options' ),
            
            array(
                'title'    => __( 'Product Input Fields', 'woocommerce-jetpack' ),
                'desc'     => '<strong>' . __( 'Enable Module', 'woocommerce-jetpack' ) . '</strong>',
                'desc_tip' => __( 'Product Input Fields.', 'woocommerce-jetpack' ),
                'id'       => 'wcj_product_input_fields_enabled',
                'default'  => 'no',
                'type'     => 'checkbox',
            ),
        
            array( 'type'  => 'sectionend', 'id' => 'wcj_product_input_fields_options' ),
			
            array( 
				'title'    => __( 'Product Input Fields per Product Options', 'woocommerce-jetpack' ), 
				'type'     => 'title', 
				'desc'     => __( 'Add custom input fields to product\'s single page for customer to fill before adding product to cart.', 'woocommerce-jetpack' )
				              . ' ' 
							  . __( 'When enabled this module will add "Product Input Fields" tab to product\'s "Edit" page.', 'woocommerce-jetpack' ),
				'id'       => 'wcj_product_input_fields_local_options', 
			),			
            
            array(
                'title'    => __( 'Product Input Fields - per Product', 'woocommerce-jetpack' ),
                'desc'     => '<strong>' . __( 'Enable', 'woocommerce-jetpack' ) . '</strong>',
                'desc_tip' => __( 'Add custom input field on per product basis.', 'woocommerce-jetpack' ),
                'id'       => 'wcj_product_input_fields_local_enabled',
                'default'  => 'no',
                'type'     => 'checkbox',
            ),
			
            array(
                'title'    => __( 'Default Number of Product Input Fields per Product', 'woocommerce-jetpack' ),
                'id'       => 'wcj_product_input_fields_local_total_number_default',
				'desc_tip' => __( 'You will be able to change this number later as well as define the fields, for each product individually, in product\'s "Edit".', 'woocommerce-jetpack' ),				
                'default'  => 1,
                'type'     => 'number',
				'desc' 	   => apply_filters( 'get_wc_jetpack_plus_message', '', 'desc' ),
				'custom_attributes'	
						   => apply_filters( 'get_wc_jetpack_plus_message', '', 'readonly' ),					
            ),		
        
            array( 
				'type'     => 'sectionend', 
				'id'       => 'wcj_product_input_fields_local_options',
			),			
			
            array( 
				'title'    => __( 'Product Input Fields Global Options', 'woocommerce-jetpack' ), 
				'type'     => 'title', 
				'desc'     => __( 'Add custom input fields to product\'s single page for customer to fill before adding product to cart.', 'woocommerce-jetpack' ), 
				'id'       => 'wcj_product_input_fields_global_options', 
			),
            
            array(
                'title'    => __( 'Product Input Fields - All Products', 'woocommerce-jetpack' ),
                'desc'     => '<strong>' . __( 'Enable', 'woocommerce-jetpack' ) . '</strong>',
                'desc_tip' => __( 'Add custom input fields to all products.', 'woocommerce-jetpack' ),
                'id'       => 'wcj_product_input_fields_global_enabled',
                'default'  => 'no',
                'type'     => 'checkbox',
            ),
        
			array(
				'title' 	=> __( 'Product Input Fields Number', 'woocommerce-jetpack' ),
				'desc_tip' 	=> __( 'Click "Save changes" after you change this number.', 'woocommerce-jetpack' ),
				'id' 		=> 'wcj_product_input_fields_global_total_number',
				'default'	=> 1,
				'type' 		=> 'number',
				'desc' 	   => apply_filters( 'get_wc_jetpack_plus_message', '', 'desc' ),
				'custom_attributes'	
						   => apply_filters( 'get_wc_jetpack_plus_message', '', 'readonly' ),					
			),				
        );
		
		$options = $this->get_options();
		for ( $i = 1; $i <= apply_filters( 'wcj_get_option_filter', 1, get_option( 'wcj_product_input_fields_global_total_number', 1 ) ); $i++ ) {		
			foreach( $options as $option ) {
				$settings[] = 
					array(
						'title' 	=> ( 'wcj_product_input_fields_enabled_global_' === $option['id'] ) ? __( 'Product Input Field', 'woocommerce-jetpack' ) . ' #' . $i : '',
						'desc'		=> $option['title'],
						'id' 		=> $option['id'] . $i,
						'default'	=> $option['default'],
						'type' 		=> $option['type'],
						'css'	    => 'width:30%;min-width:300px;',
					);		
			}
		}

		$settings[] = 
			array( 
				'type'     => 'sectionend', 
				'id'       => 'wcj_product_input_fields_global_options',
			);
        
        return $settings;
    }
 
    /**
     * settings_section.
     */
    function settings_section( $sections ) {    
        $sections['product_input_fields'] = __( 'Product Input Fields', 'woocommerce-jetpack' );        
        return $sections;
    }  
}
 
endif;
 
return new WCJ_Product_Input_Fields();
