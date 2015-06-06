<?php
/**
 * WooCommerce Jetpack Add to Cart per Category
 *
 * The WooCommerce Jetpack Add to Cart per Category class.
 *
 * @class    WCJ_Add_To_Cart_Per_Category
 * @version  1.0.0
 * @category Class
 * @author   Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WCJ_Add_To_Cart_Per_Category' ) ) :

class WCJ_Add_To_Cart_Per_Category {

    /**
     * Constructor.
     */
    public function __construct() {

        // Main hooks
        if ( 'yes' === get_option( 'wcj_add_to_cart_per_category_enabled' ) ) {
			add_filter( 'woocommerce_product_single_add_to_cart_text', 	array( $this, 'change_add_to_cart_button_text_single' ), 	PHP_INT_MAX );
			add_filter( 'woocommerce_product_add_to_cart_text', 		array( $this, 'change_add_to_cart_button_text_archive' ), 	PHP_INT_MAX );
        }

        // Settings hooks
        add_filter( 'wcj_settings_sections', 					array( $this, 'settings_section' ) );
        add_filter( 'wcj_settings_add_to_cart_per_category', 	array( $this, 'get_settings' ), 100 );
        add_filter( 'wcj_features_status', 						array( $this, 'add_enabled_option' ), 100 );
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
		$product_categories = get_the_terms( get_the_ID(), 'product_cat' );
		if ( empty( $product_categories ) )
			return $add_to_cart_text;
		for ( $i = 1; $i <= apply_filters( 'wcj_get_option_filter', 1, get_option( 'wcj_add_to_cart_per_category_total_groups_number', 1 ) ); $i++ ) {
			if ( 'yes' !== get_option( 'wcj_add_to_cart_per_category_enabled_group_' . $i ) )
				continue;
			$categories = array_filter( explode( ',', get_option( 'wcj_add_to_cart_per_category_group_' . $i ) ) );
			if ( empty(  $categories ) )
				continue;
			foreach ( $product_categories as $product_category_id => $product_category ) {
				foreach ( $categories as $category ) {
					if ( $product_category_id == $category ) {
						return get_option( 'wcj_add_to_cart_per_category_text_' . $single_or_archive . '_group_' . $i, $add_to_cart_text );
					}
				}
			}
		}
        return $add_to_cart_text;
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

            array( 'title' => __( 'Add to Cart per Category Options', 'woocommerce-jetpack' ), 'type' => 'title', 'desc' => '', 'id' => 'wcj_add_to_cart_per_category_options' ),

            array(
                'title'    => __( 'Add to Cart - per Category', 'woocommerce-jetpack' ),
                'desc'     => '<strong>' . __( 'Enable Module', 'woocommerce-jetpack' ) . '</strong>',
				'desc_tip' => __( 'Add to cart button text on per category basis.', 'woocommerce-jetpack' ),
                'id'       => 'wcj_add_to_cart_per_category_enabled',
                'default'  => 'no',
                'type'     => 'checkbox',
            ),

			array(
				'title'    => __( 'Category Groups Number', 'woocommerce-jetpack' ),
				'desc_tip' => __( 'Click "Save changes" after you change this number.', 'woocommerce-jetpack' ),
				'id'       => 'wcj_add_to_cart_per_category_total_groups_number',
				'default'  => 1,
				'type'     => 'number',
				'desc'     => apply_filters( 'get_wc_jetpack_plus_message', '', 'desc' ),
				'custom_attributes'
				           => array_merge(
								is_array( apply_filters( 'get_wc_jetpack_plus_message', '', 'readonly' ) ) ? apply_filters( 'get_wc_jetpack_plus_message', '', 'readonly' ) : array(),
								array(
									'step' 	=> '1',
									'min'	=> '1',
								)
							  ),
				'css'		=> 'width:100px;',
			),
	    );

		for ( $i = 1; $i <= apply_filters( 'wcj_get_option_filter', 1, get_option( 'wcj_add_to_cart_per_category_total_groups_number', 1 ) ); $i++ ) {
            $settings = array_merge( $settings, array(
				array(
					'title'    => __( 'Group', 'woocommerce-jetpack' ) . ' #' . $i,
					'desc'	   => __( 'Enable', 'woocommerce-jetpack' ),
					'id'       => 'wcj_add_to_cart_per_category_enabled_group_' . $i,
					'default'  => 'yes',
					'type'     => 'checkbox',
				),
				array(
					'title'    => '',
					'desc'	   => __( 'Product Category IDs List', 'woocommerce-jetpack' ),
					'desc_tip' => __( 'Comma separated list of product category IDs.', 'woocommerce-jetpack' ),
					'id'       => 'wcj_add_to_cart_per_category_group_' . $i,
					'default'  => '',
					'type'     => 'text',
					'css'      => 'width:30%;min-width:300px;',
				),
				array(
					'title'    => '',
					'desc'	   => __( 'Button text - single product view', 'woocommerce-jetpack' ),
					'id'       => 'wcj_add_to_cart_per_category_text_single_group_' . $i,
					'default'  => '',
					'type'     => 'textarea',
					'css'      => 'width:20%;min-width:200px;',
				),
				array(
					'title'    => '',
					'desc'	   => __( 'Button text - product archive (category) view', 'woocommerce-jetpack' ),
					'id'       => 'wcj_add_to_cart_per_category_text_archive_group_' . $i,
					'default'  => '',
					'type'     => 'textarea',
					'css'      => 'width:20%;min-width:200px;',
				),
			) );
		}

		$settings = array_merge( $settings, array(

			array( 'type'  => 'sectionend', 'id' => 'wcj_add_to_cart_per_category_options' ),

		) );

		// Product Category IDs Info
		$categories = get_categories( array(
			'hide_empty'               => 0,
			'taxonomy'                 => 'product_cat',
		) );
		if ( ! empty( $categories ) ) {
			$categories_info = '<table class="widefat" style="width:30% !important;">';
			$categories_info .= '<tr>';
			$categories_info .= '<th>';
			$categories_info .= __( 'Product Category Name', 'woocommerce-jetpack' );
			$categories_info .= '</th>';
			$categories_info .= '<th>';
			$categories_info .= __( 'Product Category ID', 'woocommerce-jetpack' );
			$categories_info .= '</th>';
			$categories_info .= '</tr>';
			foreach ( $categories as $key => $category ) {
				if ( ! is_object( $category ) )
					continue;
				$categories_info .= '<tr>';
				$categories_info .= '<td>';
				$categories_info .= $category->cat_name;
				$categories_info .= '</td>';
				$categories_info .= '<td>';
				$categories_info .= $category->cat_ID;
				$categories_info .= '</td>';
				$categories_info .= '</tr>';
			}
			$categories_info .= '</table>';

			$settings = array_merge( $settings, array(

				array( 'type'  => 'sectionend', 'id' => 'wcj_add_to_cart_per_category_options' ),

				array( 'title' => __( 'Product Category IDs', 'woocommerce-jetpack' ), 'type' => 'title', 'desc' => $categories_info, 'id' => 'wcj_add_to_cart_per_category_info' ),

				array( 'type'  => 'sectionend', 'id' => 'wcj_add_to_cart_per_category_info' ),

			) );
		}

        return $settings;
    }

    /**
     * settings_section.
     */
    function settings_section( $sections ) {
        $sections['add_to_cart_per_category'] = __( 'Add to Cart - per Category', 'woocommerce-jetpack' );
        return $sections;
    }
}

endif;

return new WCJ_Add_To_Cart_Per_Category();
