<?php
/**
 * WooCommerce Jetpack Related Products
 *
 * The WooCommerce Jetpack Related Products class.
 *
 * @class    WCJ_Related_Products
 * @version  1.0.0
 * @category Class
 * @author   Algoritmika Ltd.
 */
 
if ( ! defined( 'ABSPATH' ) ) exit;
 
if ( ! class_exists( 'WCJ_Related_Products' ) ) :
 
class WCJ_Related_Products {
    
    /**
     * Constructor.
     */
    public function __construct() {
 
        // Main hooks
		if ( 'yes' === get_option( 'wcj_related_products_enabled' ) ) {
		
			add_filter( 'woocommerce_related_products_args', 		array( $this, 'related_products_limit' ), 100 );
			
			add_filter( 'woocommerce_output_related_products_args', array( $this, 'related_products_limit_args' ), 100 );
			
			if ( 'no' === get_option( 'wcj_product_info_related_products_relate_by_category' ) ) {
				apply_filters( 'woocommerce_product_related_posts_relate_by_category', false );
			}				
			
			if ( 'no' === get_option( 'wcj_product_info_related_products_relate_by_tag' ) ) {
				apply_filters( 'woocommerce_product_related_posts_relate_by_tag', false );
			}
		}		
	    
        // Settings hooks
        add_filter( 'wcj_settings_sections',         array( $this, 'settings_section' ) );
        add_filter( 'wcj_settings_related_products', array( $this, 'get_settings' ), 100 );
        add_filter( 'wcj_features_status',           array( $this, 'add_enabled_option' ), 100 );
    }
    
    /**
     * add_enabled_option.
     */
    public function add_enabled_option( $settings ) {    
        $all_settings = $this->get_settings();
        $settings[] = $all_settings[1];        
        return $settings;
    }
	
	// RELATED PRODUCTS //
	
	/**
	 * Change number of related products on product page.
	 */ 
	function related_products_limit_args( $args ) {			
		$args['posts_per_page'] = get_option( 'wcj_product_info_related_products_num' );
		$args['orderby'] = get_option( 'wcj_product_info_related_products_orderby' );
		$args['columns'] = get_option( 'wcj_product_info_related_products_columns' );				
		return $args;
	}	
	
	/**
	 * Change number of related products on product page.
	 */ 
	function related_products_limit( $args ) {			
		$args['posts_per_page'] = get_option( 'wcj_product_info_related_products_num' );
		$args['orderby'] = get_option( 'wcj_product_info_related_products_orderby' );
		if ( get_option( 'wcj_product_info_related_products_orderby' ) != 'rand' ) $args['order'] = get_option( 'wcj_product_info_related_products_order' );				
		return $args;
	}	
    
    /**
     * get_settings.
     */    
    function get_settings() {
 
        $settings = array(
 
			array( 'title' 	=> __( 'Related Products Options', 'woocommerce-jetpack' ), 'type' => 'title', 'desc' => '', 'id' => 'wcj_product_info_related_products_options' ),
		
			array(
                'title'    => __( 'Related Products', 'woocommerce-jetpack' ),
				'desc'     => '<strong>' . __( 'Enable Module', 'woocommerce-jetpack' ) . '</strong>',
				'desc_tip' => __( 'Change displayed related products number, columns, order, relate by tag and/or category.', 'woocommerce-jetpack' ) . '</strong>',
				'id' 	   => 'wcj_related_products_enabled',//'wcj_product_info_related_products_enable'
				'default'  => 'no',
				'type' 	   => 'checkbox',
			),
			
			array(
				'title'    => __( 'Related Products Number', 'woocommerce-jetpack' ),
				'id'       => 'wcj_product_info_related_products_num',
				'default'  => 3,
				'type'     => 'number',
			),
			
			array(
				'title'    => __( 'Related Products Columns', 'woocommerce-jetpack' ),
				'id'       => 'wcj_product_info_related_products_columns',
				'default'  => 3,
				'type'     => 'number',
			),			
			
			array(
				'title'    => __( 'Order by', 'woocommerce-jetpack' ),
				'id'       => 'wcj_product_info_related_products_orderby',
				'default'  => 'rand',
				'type'     => 'select',
				'options'  => array(
						'rand'  => __( 'Random', 'woocommerce-jetpack' ),
						'date'	=> __( 'Date', 'woocommerce-jetpack' ),
						'title' => __( 'Title', 'woocommerce-jetpack' ),
					),
			),			
			
			array(
				'title'    => __( 'Order', 'woocommerce-jetpack' ),
				'desc_tip' => __( 'Ignored if order by "Random" is selected above.', 'woocommerce-jetpack' ),
				'id'       => 'wcj_product_info_related_products_order',
				'default'  => 'desc',
				'type'     => 'select',
				'options'  => array(
						'asc'   => __( 'Ascending', 'woocommerce-jetpack' ),
						'desc'	=> __( 'Descending', 'woocommerce-jetpack' ),
					),
			),					
			
			array(
				'title' 	=> __( 'Relate by Category', 'woocommerce-jetpack' ),
				'id' 		=> 'wcj_product_info_related_products_relate_by_category',
				'default'	=> 'yes',
				'type' 		=> 'checkbox',
			),		

			array(
				'title' 	=> __( 'Relate by Tag', 'woocommerce-jetpack' ),
				'id' 		=> 'wcj_product_info_related_products_relate_by_tag',
				'default'	=> 'yes',
				'type' 		=> 'checkbox',
			),			
		
			array( 'type' 	=> 'sectionend', 'id' => 'wcj_product_info_related_products_options' ),					
        );
        
        return $settings;
    }
 
    /**
     * settings_section.
     */
    function settings_section( $sections ) {    
        $sections['related_products'] = __( 'Related Products', 'woocommerce-jetpack' );        
        return $sections;
    }    
}
 
endif;
 
return new WCJ_Related_Products();
