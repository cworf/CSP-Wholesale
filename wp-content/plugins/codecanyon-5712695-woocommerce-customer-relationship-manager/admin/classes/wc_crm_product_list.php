<?php
/**
 * Table with list of customers.
 *
 * @author   Actuality Extensions
 * @package  WooCommerce_Customer_Relationship_Manager
 * @since    1.0
 */

if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !class_exists( 'WP_List_Table' ) ) {
  require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class WC_Crm_Product_List extends WP_List_Table {

  var $data = array();

  function __construct() {
    global $status, $page;

    parent::__construct( array(
      'singular' => __( 'product', 'wc_customer_relationship_manager' ), //singular name of the listed records
      'plural' => __( 'products', 'wc_customer_relationship_manager' ), //plural name of the listed records
      'ajax' => false //does this table support ajax?

    ) );
    ?>
    <script>
    jQuery('document').ready(function($){
      $('.crm_actions .select_order').click(function(){
            var id = $(this).attr('href');
            var parentBody = window.parent.document.body;
            $("#number_order_product", parentBody).val(id);
            $(".fancybox-overlay", parentBody).trigger('click');
            return false;
        });
    });
    </script>
    <style>
    #adminmenuwrap,
    #screen-meta,
    #screen-meta-links,
    #adminmenuback,
    #wpfooter,
    #wpadminbar{
      display: none !important;
    }
    html{
      padding-top: 0 !important;
    }
    #wpcontent{
      margin: 0 !important;
    }
    #wc-crm-page{
      margin: 15px !important;
    }
    </style>
    <?php

  }
function admin_header__() {
   die;

  }


  function no_items() {
    _e( 'No products data found.', 'wc_customer_relationship_manager' );
  }

  function column_default( $item, $column_name ) {
    switch ( $column_name ) {
      case 'thumb':
      case 'name':
      case 'sku':
      case 'is_in_stock':
      case 'price':
      case 'product_cat':
      case 'product_type':
      case 'date':
      case 'crm_actions':
        return $item[$column_name];
      default:
        return print_r( $item, true ); //Show the whole array for troubleshooting purposes
    }
  }

  function get_sortable_columns() {
    $sortable_columns = array(
      'name' => array('name', false),
      'sku' => array('sku', false),
      'date' => array('date', false),
    );
    return $sortable_columns;
  }

  function get_columns() {
    $columns = array(
      'thumb' => '<span class="wc-image tips">'.__( 'Image', 'wc_customer_relationship_manager' ).'</span>',
      'name' => __( 'Name', 'wc_customer_relationship_manager' ),
      'sku' => __( 'SKU', 'wc_customer_relationship_manager' ),
      'is_in_stock' => __( 'Stock', 'wc_customer_relationship_manager' ),
      'price' => __( 'Price', 'wc_customer_relationship_manager' ),
      'product_cat' => __( 'Categories', 'wc_customer_relationship_manager' ),
      'product_type' =>  '<span class="wc-type tips">'.__( 'Type', 'wc_customer_relationship_manager' ).'</span>',
      'date' => __( 'Date', 'wc_customer_relationship_manager' ),
      'crm_actions' => __( 'Actions', 'wc_customer_relationship_manager' ),
    );
    return $columns;
  }

  function usort_reorder( $a, $b ) {
    // If no sort, default to last purchase
    $orderby = ( !empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'date';
    // If no order, default to desc
    $order = ( !empty( $_GET['order'] ) ) ? $_GET['order'] : 'desc';
    // Determine sort order
    if ( $orderby == 'order_value' ) {
      $result = $a[$orderby] - $b[$orderby];
    } else {
      $result = strcmp( $a[$orderby], $b[$orderby] );
    }
    // Send final sort direction to usort
    return ( $order === 'asc' ) ? $result : -$result;
  }

  function column_crm_actions( $item ) {
    global $woocommerce;

    $actions = array(
      'select_order' => array(
        'classes' => 'select_order',
        'url' => '#'.$item['ID'],
        'name' => __( 'Select', 'wc_customer_relationship_manager' ),
      )
    );

    echo '<p>';
    foreach ( $actions as $action ) {
      printf( '<a class="button tips %s" href="%s" data-tip="%s">%s</a>', esc_attr($action['classes']), esc_url( $action['url'] ), esc_attr( $action['name'] ), esc_attr( $action['name'] ) );
    }
    echo '</p>';

  }
  function prepare_items() {

    $this->data = $this->get_wooc_product_data();;


    $columns = $this->get_columns();
    $hidden = array();
    $sortable = $this->get_sortable_columns();
    $this->_column_headers = array($columns, $hidden, $sortable);
    usort( $this->data, array(&$this, 'usort_reorder') );

    $user = get_current_user_id();
    $screen = get_current_screen();
    $option = $screen->get_option('per_page', 'option');

    $per_page = get_user_meta($user, $option, true);

    if ( empty ( $per_page) || $per_page < 1 ) {
        $per_page = $screen->get_option( 'per_page', 'default' );
    }
    //$per_page = 20;
    $current_page = $this->get_pagenum();
    $total_items = count( $this->data );


    $this->found_data = array_slice( $this->data, ( ( $current_page - 1 ) * $per_page ), $per_page );


    $this->set_pagination_args( array(
      'total_items' => $total_items, //WE have to calculate the total number of items
      'per_page' => $per_page //WE have to determine how many items to show on a page
    ) );
    $this->items = $this->found_data;
  }
  function get_wooc_product_data(){
    global $post, $woocommerce, $the_order;
    
    $orders_data = array();
    $order_products = array();
    if(   defined( 'WC_VERSION') && floatval(WC_VERSION) >= 2.2 ){
      include_once( dirname(WC_PLUGIN_FILE).'/includes/admin/class-wc-admin-post-types.php' );
      $CPT_Product = new WC_Admin_Post_Types();

       $args = array(
        'numberposts' => -1,
        'post_type' => 'product',
        'post_status' => 'publish'
      );
      $products = get_posts( $args );

      foreach ( $products as $post ) {
         #$post = get_post($prod_id);

          $o['ID'] = $post->ID;
          ob_start();
            $CPT_Product->render_product_columns( 'thumb' );
          $o['thumb']  = ob_get_contents();
          ob_end_clean();
          ob_start();
            $edit_link = get_edit_post_link( $post->ID );
            $title = _draft_or_post_title();
            echo '<strong><a class="row-title" href="'.$edit_link.'">' . $title.'</a>';
          $o['name']  = ob_get_contents();
          ob_end_clean();
          ob_start();
            $CPT_Product->render_product_columns( 'sku' );
          $o['sku']  = ob_get_contents();
          ob_end_clean();
          ob_start();
            $CPT_Product->render_product_columns( 'is_in_stock' );
          $o['is_in_stock']  = ob_get_contents();
          ob_end_clean();
          ob_start();
            $CPT_Product->render_product_columns( 'price' );
          $o['price']  = ob_get_contents();
          ob_end_clean();
          ob_start();
            $CPT_Product->render_product_columns( 'product_cat' );
          $o['product_cat']  = ob_get_contents();
          ob_end_clean();
          ob_start();
            $CPT_Product->render_product_columns( 'product_type' );
          $o['product_type']  = ob_get_contents();
          ob_end_clean();
          ob_start();
            if ( '0000-00-00 00:00:00' == $post->post_date ) {
              $t_time = $h_time = __( 'Unpublished', 'woocommerce' );
            } else {
              $t_time = get_the_time( __( 'Y/m/d g:i:s A', 'woocommerce' ), $post );

              $gmt_time = strtotime( $post->post_date_gmt . ' UTC' );
              $time_diff = current_time('timestamp', 1) - $gmt_time;

              if ( $time_diff > 0 && $time_diff < 24*60*60 )
                $h_time = sprintf( __( '%s ago', 'woocommerce' ), human_time_diff( $gmt_time, current_time('timestamp', 1) ) );
              else
                $h_time = get_the_time( __( 'Y/m/d', 'woocommerce' ), $post );
            }

            echo '<abbr title="' . esc_attr( $t_time ) . '">' . esc_html( apply_filters( 'post_date_column_time', $h_time, $post ) ) . '</abbr>';
          $o['date']  = ob_get_contents();
          ob_end_clean();
        $orders_data[] = $o;

      }
    }else{
      $args = array(
        'numberposts' => -1,
        'post_type' => 'product',
        'post_status' => 'publish'
      );
       $args = $this->product_filters_query($args);
      $orders = get_posts( $args );

      $CPT_Product = new WC_Admin_CPT_Product();
      $user_email = '';
      if(isset($_GET['order_id']) ){
        $user_order_id = $_GET['order_id'];
        $u = new WC_Order( $user_order_id );
        $user_email = $u->billing_email;

        /**/
        $args_ = array(
          'numberposts' => -1,
          'post_type' => 'shop_order',
          'post_status' => 'publish'
        );
        $orders_ = get_posts( $args_ );
        foreach ( $orders_ as $order_ ) {
          $id = $order_->ID;
          if(!empty($user_email)){
            $t = new WC_Order( $id );
            if($user_email != $t->billing_email) continue;
          }
          $order_d = new WC_Order( $order_->ID );
            $items = $order_d->get_items();
            foreach ( $items as $item ) {
              $prod_id = $item['item_meta']['_product_id'][0];

              if ( !in_array( $prod_id, $order_products ) ) {
                $order_products[] = $prod_id;
              }
            }
        }
        /**/

     }
      foreach ( $orders as $order ) {
         $post = $order;

         if ( !in_array( $post->ID, $order_products ) ) {
            continue;
          }

          $o['ID'] = $post->ID;
          ob_start();
            $CPT_Product->custom_columns( 'thumb' );
          $o['thumb']  = ob_get_contents();
          ob_end_clean();
          ob_start();
            $edit_link = get_edit_post_link( $post->ID );
            $title = _draft_or_post_title();
            echo '<strong><a class="row-title" href="'.$edit_link.'">' . $title.'</a>';
          $o['name']  = ob_get_contents();
          ob_end_clean();
          ob_start();
            $CPT_Product->custom_columns( 'sku' );
          $o['sku']  = ob_get_contents();
          ob_end_clean();
          ob_start();
            $CPT_Product->custom_columns( 'is_in_stock' );
          $o['is_in_stock']  = ob_get_contents();
          ob_end_clean();
          ob_start();
            $CPT_Product->custom_columns( 'price' );
          $o['price']  = ob_get_contents();
          ob_end_clean();
          ob_start();
            $CPT_Product->custom_columns( 'product_cat' );
          $o['product_cat']  = ob_get_contents();
          ob_end_clean();
          ob_start();
            $CPT_Product->custom_columns( 'product_type' );
          $o['product_type']  = ob_get_contents();
          ob_end_clean();
          ob_start();
            if ( '0000-00-00 00:00:00' == $post->post_date ) {
              $t_time = $h_time = __( 'Unpublished', 'woocommerce' );
            } else {
              $t_time = get_the_time( __( 'Y/m/d g:i:s A', 'woocommerce' ), $post );

              $gmt_time = strtotime( $post->post_date_gmt . ' UTC' );
              $time_diff = current_time('timestamp', 1) - $gmt_time;

              if ( $time_diff > 0 && $time_diff < 24*60*60 )
                $h_time = sprintf( __( '%s ago', 'woocommerce' ), human_time_diff( $gmt_time, current_time('timestamp', 1) ) );
              else
                $h_time = get_the_time( __( 'Y/m/d', 'woocommerce' ), $post );
            }

            echo '<abbr title="' . esc_attr( $t_time ) . '">' . esc_html( apply_filters( 'post_date_column_time', $h_time, $post ) ) . '</abbr>';
          $o['date']  = ob_get_contents();
          ob_end_clean();
        $orders_data[] = $o;

      }
    }

  return $orders_data;
  }
  
   function product_filters_query($args) {

      if ( isset( $_REQUEST['product_cat'] ) && !empty($_REQUEST['product_cat'])) {
        $args['tax_query'][] = array(
          'taxonomy' => 'product_cat',
          'terms' => $_REQUEST['product_cat'],
          'field' => 'slug',
        );
      }
      if ( isset( $_REQUEST['product_type'] ) && !empty($_REQUEST['product_type'])) {
        if ( $_REQUEST['product_type'] == 'downloadable' ) {
              $args['meta_key']    = '_downloadable';
              $args['meta_value']  = 'yes';
          } elseif ( $_REQUEST['product_type'] == 'virtual' ) {
              $args['meta_key']    = '_virtual';
              $args['meta_value']  = 'yes';
            }
      }
      return $args;
    }

}