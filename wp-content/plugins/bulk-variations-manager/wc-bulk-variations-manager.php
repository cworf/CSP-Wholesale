<?php
/*
Plugin Name: WooCommerce Bulk Variations Manager
Plugin URI: http://www.storeapps.org/product/bulk-variations-manager/
Description: Create WooCommerce Product Variations in bulk
Version: 1.9
Author: Ratnakar
License: GPLv2 or later
Copyright (c) 2013, 2014, 2015 Store Apps All rights reserved.
*/

if ( !defined( 'ABSPATH' ) ) exit;

$active_plugins = (array) get_option( 'active_plugins', array() );

if ( is_multisite() ) {
    $active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
}

if ( ! ( in_array( 'woocommerce/woocommerce.php', $active_plugins ) || array_key_exists( 'woocommerce/woocommerce.php', $active_plugins ) ) ) return;

require_once 'classes/class-wc-compatibility.php';
require_once 'classes/class-wc-compatibility-2-2.php';
require_once 'classes/class-wc-compatibility-2-3.php';

if ( !class_exists( 'SA_Bulk_Variations' ) ) {

	class SA_Bulk_Variations {

		function __construct() {

            if ( in_array( 'smart-manager-for-wp-e-commerce/smart-manager.php', get_option( 'active_plugins' ) ) ) {
                add_action( 'admin_menu', array( &$this, 'bvm_add_menu_access' ), 9 );
            } else {
                add_action( 'admin_menu', array( &$this, 'woocommerce_variation_menu' ) );
            }

            add_action( 'init', array( &$this, 'localize_bulk_variation' ) );
            add_action( 'init', array( &$this, 'bvm_include_classes' ) );

            add_action( 'wp_ajax_json_search_products_with_status', array( $this, 'json_search_products_with_status' ) );

            add_action( 'admin_footer', array( $this, 'bvm_support_ticket_content' ) );
            add_action( 'in_admin_footer', array( $this, 'add_social_links' ) );
            
		}

        function bvm_add_menu_access() {
            global $wpdb, $current_user;
            $current_user = wp_get_current_user();
            if ( !isset( $current_user->roles[0] ) ) {
                $roles = array_values( $current_user->roles );
            } else {
                $roles = $current_user->roles;
            }
            $results = get_option( 'sm_' . $roles [0] . '_dashboard' );
            if ( ( is_array( $results ) && in_array( 'Products', $results, true ) ) || $current_user->roles [0] == 'administrator') {
                add_action( 'admin_menu', array( &$this, 'woocommerce_variation_menu' ) );
            }
        }

		function localize_bulk_variation() {
			load_plugin_textdomain( 'sa_bulk_variation', false, dirname( plugin_basename( __FILE__ ) ).'/languages/' );
		}

        function bvm_include_classes() {
            global $bvm_operation_new, $bvm_operation_old;

        	include_once 'classes/class-bvm-operation-old.php';
        	include_once 'classes/class-bvm-operation-new.php';

        }

        function json_search_products_with_status( $x = '' ) {

            check_ajax_referer( 'ajax-search-products-with-status', 'security' );

            $post_types = array( 'product' );
            $post_status = ( !empty( $_GET['status'] ) ) ? unserialize( stripslashes( $_GET['status'] ) ) : 'any';

            $term = (string) stripslashes( $_GET['term'] );

            if (empty($term)) die();

            if ( is_numeric( $term ) ) {

                $args = array(
                    'post_type'         => $post_types,
                    'post_status'       => $post_status,
                    'posts_per_page'    => -1,
                    'post__in'          => array(0, $term),
                    'fields'            => 'ids'
                );

                $args2 = array(
                    'post_type'         => $post_types,
                    'post_status'       => $post_status,
                    'posts_per_page'    => -1,
                    'post_parent'       => $term,
                    'fields'            => 'ids'
                );

                $args3 = array(
                    'post_type'         => $post_types,
                    'post_status'       => $post_status,
                    'posts_per_page'    => -1,
                    'meta_query'        => array(
                        array(
                        'key'   => '_sku',
                        'value' => $term,
                        'compare' => 'LIKE'
                        )
                    ),
                    'fields'            => 'ids'
                );

                $posts = array_unique(array_merge( get_posts( $args ), get_posts( $args2 ), get_posts( $args3 ) ));

            } else {

                $args = array(
                    'post_type'         => $post_types,
                    'post_status'       => $post_status,
                    'posts_per_page'    => -1,
                    's'                 => $term,
                    'fields'            => 'ids'
                );

                $args2 = array(
                    'post_type'         => $post_types,
                    'post_status'       => $post_status,
                    'posts_per_page'    => -1,
                    'meta_query'        => array(
                        array(
                        'key'   => '_sku',
                        'value' => $term,
                        'compare' => 'LIKE'
                        )
                    ),
                    'fields'            => 'ids'
                );

                $posts = array_unique(array_merge( get_posts( $args ), get_posts( $args2 ) ));

            }

            $found_products = array();

            if ( $posts ) foreach ( $posts as $post ) {

                $product = get_product( $post );

                $found_products[ $post ] = $product->get_formatted_name();

            }

            echo json_encode( $found_products );

            die();
        }

        function woocommerce_variation_menu() {
            global $wpdb, $current_user, $bvm_operation_new, $bvm_operation_old;
            
            if ( isset( $_GET['bvm_version'] ) && $_GET['bvm_version'] == 'old' ) {
                $bvm_operation = $bvm_operation_old;
            } else {
                $bvm_operation = $bvm_operation_new;
            }

            if( !wp_script_is('thickbox') ) {
                wp_enqueue_script('thickbox');
            }
            
            if( !wp_style_is('thickbox') ){
                 wp_enqueue_style('thickbox');
            }

            if ( (!current_user_can( 'edit_pages' )) && (is_plugin_active( 'woocommerce/woocommerce.php' )) ) {
                add_menu_page( __( 'WooCommerce Bulk Variations Manager', 'sa_bulk_variation' ), __( 'Bulk Variations Manager Demo for WooCommerce', 'sa_bulk_variation' ), 'read', 'woocommerce_variations', array( $bvm_operation, 'woocommerce_variations_page' ) );
            } else {
                add_submenu_page( 'edit.php?post_type=product', __( 'WooCommerce Bulk Variations Manager', 'sa_bulk_variation' ), __( 'Bulk Variations', 'sa_bulk_variation' ), 'edit_pages', 'woocommerce_variations', array( $bvm_operation, 'woocommerce_variations_page' ) );
            }
            
		}

        function bvm_support_ticket_content() {
            global $pagenow, $typenow;
            
            if ( $pagenow != 'edit.php' ) return;
            
            if ( $typenow != 'product') return;

            if ( ! empty( $_REQUEST['page'] ) && $_REQUEST['page'] != 'woocommerce_variations' ) return;

            if ( ! method_exists( 'Store_Apps_Upgrade', 'support_ticket_content' ) ) return;

            $prefix = 'sa_bulk_variations';
            $sku = 'bvm';
            $plugin_data = get_plugin_data( __FILE__ );
            $license_key = get_site_option( $prefix.'_license_key' );
            $text_domain = 'sa_bulk_variation';

            Store_Apps_Upgrade::support_ticket_content( $prefix, $sku, $plugin_data, $license_key, $text_domain );
        }

        function add_social_links() {

            if ( ! method_exists( 'Store_Apps_Upgrade', 'add_social_links' ) ) return;

            if ( ( ! empty( $_REQUEST['post_type'] ) && $_REQUEST['post_type'] == 'product' ) && ( ! empty( $_REQUEST['page'] ) && $_REQUEST['page'] == 'woocommerce_variations' ) ) {
                echo '<div class="bvm_social_links" style="padding-bottom: 1em;">' . Store_Apps_Upgrade::add_social_links( 'bvm' ) . '</div>';
            }

        }

		static function array_cartesian( $input ) {
            $result = array();

            @set_time_limit(0);     // prevent timeout

            foreach ( $input as $key => $values ) {
                // If a sub-array is empty, it doesn't affect the cartesian product
                if ( empty( $values ) ) {
                    continue;
                }

                // Special case: seeding the product array with the values from the first sub-array
                if ( empty( $result ) ) {
                    foreach ( $values as $value ) {
                        $result[] = array( $key => $value );
                    }
                }
                else {
                    // Second and subsequent input sub-arrays work like this:
                    //   1. In each existing array inside $product, add an item with
                    //      key == $key and value == first item in input sub-array
                    //   2. Then, for each remaining item in current input sub-array,
                    //      add a copy of each existing array inside $product with
                    //      key == $key and value == first item in current input sub-array

                    // Store all items to be added to $product here; adding them on the spot
                    // inside the foreach will result in an infinite loop
                    $append = array();
                    foreach( $result as &$product ) {
                        // Do step 1 above. array_shift is not the most efficient, but it
                        // allows us to iterate over the rest of the items with a simple
                        // foreach, making the code short and familiar.
                        $product[ $key ] = array_shift( $values );

                        // $product is by reference (that's why the key we added above
                        // will appear in the end result), so make a copy of it here
                        $copy = $product;

                        // Do step 2 above.
                        foreach( $values as $item ) {
                            $copy[ $key ] = $item;
                            $append[] = $copy;
                        }

                        // Undo the side effecst of array_shift
                        array_unshift( $values, $product[ $key ] );
                    }

                    // Out of the foreach, we can add to $results now
                    $result = array_merge( $result, $append );
                }
            }

            return $result;
        }

        static function get_price( $regular_price, $sale_price, $sale_price_dates_from, $sale_price_dates_to ) {
			// Get price if on sale
			if ($sale_price && $sale_price_dates_to == '' && $sale_price_dates_from == '') {
				$price = $sale_price;
			} else { 
				$price = $regular_price;
			}	

			if ($sale_price_dates_from && strtotime($sale_price_dates_from) < strtotime('NOW')) {
				$price = $sale_price;
			}
			
			if ($sale_price_dates_to && strtotime($sale_price_dates_to) < strtotime('NOW')) {
				$price = $regular_price;
			}
			
			return $price;
		}

	}

}

function initialize_bulk_variations_manager() {
    $GLOBAL['sa_bulk_variations'] = new SA_Bulk_Variations();

    if ( !class_exists( 'Store_Apps_Upgrade' ) ) {
        require_once 'sa-includes/class-storeapps-upgrade.php';
    }

    $sku = 'bvm';
    $prefix = 'sa_bulk_variations';
    $plugin_name = 'WooCommerce Bulk Variations Manager';
    $text_domain = 'sa_bulk_variation';
    $documentation_link = 'http://www.storeapps.org/support/documentation/bulk-variations-manager/';
    $GLOBALS['bulk_variations_manager_upgrade'] = new Store_Apps_Upgrade( __FILE__, $sku, $prefix, $plugin_name, $text_domain, $documentation_link );
}
add_action( 'plugins_loaded', 'initialize_bulk_variations_manager' );