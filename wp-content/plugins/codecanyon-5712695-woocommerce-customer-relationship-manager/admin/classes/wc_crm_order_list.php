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

class WC_Crm_Order_List extends WP_List_Table {

  var $data = array();

  function __construct() {
    global $status, $page;

    parent::__construct( array(
      'singular' => __( 'order', 'wc_customer_relationship_manager' ), //singular name of the listed records
      'plural' => __( 'orders', 'wc_customer_relationship_manager' ), //plural name of the listed records
      'ajax' => false //does this table support ajax?

    ) );
    if( $_GET['page'] != 'wc_new_customer' ){
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

  }
  function admin_header__() {
     die;
  }


  function no_items() {
    _e( 'No orders data found.', 'wc_customer_relationship_manager' );
  }

  function column_default( $item, $column_name ) {
    switch ( $column_name ) {
      case 'order_status':
      case 'order_title':
      case 'order_items':
      case 'shipping_address':
      case 'customer_message':
      case 'order_notes':
      case 'order_date':
      case 'order_total':
      case 'crm_actions':
        return $item[$column_name];
      default:
        return print_r( $item, true ); //Show the whole array for troubleshooting purposes
    }
  }

  function get_sortable_columns() {
    $sortable_columns = array(
      'order_title' => array('order_title', false),
      'order_date' => array('order_date', false),
      'order_total' => array('order_total', false),
    );
    return $sortable_columns;
  }

  function get_columns() {
    $columns = array(
      'order_status' => '<span class="status_head tips" data-tip="' . esc_attr__( 'Status', 'woocommerce' ) . '">' . esc_attr__( 'Status', 'woocommerce' ) . '</span>',
      'order_title' => __( 'Order', 'wc_customer_relationship_manager' ),
      'order_items' => __( 'Purchased', 'wc_customer_relationship_manager' ),
      'shipping_address' => __( 'Ship to', 'wc_customer_relationship_manager' ),
      'customer_message' => '<span class="notes_head tips">'.__( 'Customer Message', 'wc_customer_relationship_manager' ).'</span>',
      'order_notes' => '<span class="order-notes_head tips">'.__( 'Order Notes', 'wc_customer_relationship_manager' ).'</span>',
      'order_date' => __( 'Date', 'wc_customer_relationship_manager' ),
      'order_total' => __( 'Total', 'wc_customer_relationship_manager' ),
      'crm_actions' => __( 'Actions', 'wc_customer_relationship_manager' ),
    );
    return $columns;
  }

  function usort_reorder( $a, $b ) {
    // If no sort, default to last purchase
    $orderby = ( !empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'order_date';
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

 /* function column_customer_name( $item ) {
    return sprintf( '<strong>%1$s</strong>', $item['customer_name'] );
  }*/

  function column_crm_actions( $item ) {
    global $woocommerce;

    if( $_GET['page'] == 'wc_new_customer' ){
      ?><p>
          <?php
          $order_status = strip_tags($item['order_status']);

            $actions = array();


            if ( in_array( $order_status, array( 'pending', 'on-hold' ) ) ) {
              $actions['processing'] = array(
                'url'     => wp_nonce_url( admin_url( 'admin-ajax.php?action=woocommerce_mark_order_processing&order_id=' . $item['ID'] ), 'woocommerce-mark-order-processing' ),
                'name'    => __( 'Processing', 'woocommerce' ),
                'action'  => "processing"
              );
            }

            if ( in_array( $order_status, array( 'pending', 'on-hold', 'processing' ) ) ) {
              $actions['complete'] = array(
                'url'     => wp_nonce_url( admin_url( 'admin-ajax.php?action=woocommerce_mark_order_complete&order_id=' . $item['ID'] ), 'woocommerce-mark-order-complete' ),
                'name'    => __( 'Complete', 'woocommerce' ),
                'action'  => "complete"
              );
            }

            $actions['view'] = array(
              'url'     => admin_url( 'post.php?post=' . $item['ID'] . '&action=edit' ),
              'name'    => __( 'View', 'woocommerce' ),
              'action'  => "view"
            );

            $actions = apply_filters( 'wc_crm_admin_order_actions', $actions, $item );

            foreach ( $actions as $action ) {
              printf( '<a class="button tips %s" href="%s" data-tip="%s">%s</a>', esc_attr( $action['action'] ), esc_url( $action['url'] ), esc_attr( $action['name'] ), esc_attr( $action['name'] ) );
            }
          ?>
        </p><?php
    }else{
      $actions = array(
        'orders' => array(
          'classes' => 'view',
          'url' => sprintf( 'post.php?post=%s&action=edit', urlencode( $item['ID'] ) ),
          'action' => 'view',
          'name' => __( 'View Orders', 'wc_customer_relationship_manager' )
        ),
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


  }

  /*function column_order_value( $item ) {
    return woocommerce_price( $item['order_value'] );
  }*/

  function prepare_items() {

    $this->data = $this->get_wooc_orders_data();


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

    if( $_GET['page'] != 'wc_new_customer' ){
      $this->found_data = array_slice( $this->data, ( ( $current_page - 1 ) * $per_page ), $per_page );
      $this->set_pagination_args( array(
        'total_items' => $total_items, //WE have to calculate the total number of items
        'per_page' => $per_page //WE have to determine how many items to show on a page
      ) );
      $this->items = $this->found_data;
    }else{
      $this->items = $this->data;
    }

  }
  function get_wooc_orders_data(){
    global $post, $woocommerce, $the_order;

    if(   defined( 'WC_VERSION') && floatval(WC_VERSION) >= 2.2 ){
      $orders_data = array();
      include_once( dirname(WC_PLUGIN_FILE).'/includes/admin/class-wc-admin-post-types.php' );
      $CPT_Shop_Order = new WC_Admin_Post_Types();

      $user_email = '';
      if(isset($_GET['order_id']) ){
            $user_order_id = $_GET['order_id'];
            $u = new WC_Order( $user_order_id );
            $user_email = $u->billing_email;
       }else if(isset($_GET['user_id']) &&  !empty($_GET['user_id'])){
          $user_email = get_the_author_meta( 'email', $_GET['user_id'] );
          if(empty($user_email))
          $user_email = get_the_author_meta( 'billing_email', $_GET['user_id'] );
       }
       if(empty($user_email)) return $orders_data;
       $args = array(
        'numberposts' => -1,
        'post_type' => 'shop_order',
        'post_status' => array_keys( wc_get_order_statuses() ),
        'meta_query' => array(
                      array(
                          'key' => '_billing_email',
                          'value' => $user_email
                      )
                  ),
      );
      $orders = get_posts( $args );
      
      foreach ( $orders as $order ) {
          $id = $order->ID;
          $post = $order;
          $o['ID'] = $post->ID;
          $post_status = get_post_status( $post->ID );

          $st = array('wc-pending', 'wc-failed', 'wc-on-hold', 'wc-processing', 'wc-completed', 'wc-refunded', 'wc-cancelled');

          if(!in_array($post_status, $st) ){
            ob_start();
            do_action( 'manage_shop_order_posts_custom_column', 'order_status');
            $o['order_status'] = ob_get_contents();
            ob_end_clean();
          }else{
            ob_start();
              $CPT_Shop_Order->render_shop_order_columns( 'order_status' );
              $o['order_status']  = ob_get_contents();
            ob_end_clean();
          }

          ob_start();
            $CPT_Shop_Order->render_shop_order_columns( 'order_title' );
          $o['order_title']  = ob_get_contents();
          ob_end_clean();
          ob_start();
            $CPT_Shop_Order->render_shop_order_columns( 'order_items' );
          $o['order_items']  = ob_get_contents();
          ob_end_clean();
          ob_start();
            $CPT_Shop_Order->render_shop_order_columns( 'shipping_address' );
          $o['shipping_address']  = ob_get_contents();
          ob_end_clean();
          ob_start();
            $CPT_Shop_Order->render_shop_order_columns( 'customer_message' );
          $o['customer_message']  = ob_get_contents();
          ob_end_clean();
          ob_start();
          remove_filter( 'comments_clauses', array( 'WC_Comments', 'exclude_order_comments' ), 10, 1 );
            if(   defined( 'WC_VERSION') && floatval(WC_VERSION) >= 2.2 ){
              remove_filter( 'comments_clauses', array( 'WC_Comments', 'exclude_webhook_comments' ), 10, 1 );
            }
            $CPT_Shop_Order->render_shop_order_columns( 'order_notes' );
            add_filter( 'comments_clauses', array( 'WC_Comments', 'exclude_order_comments' ), 10, 1 );
            if(   defined( 'WC_VERSION') && floatval(WC_VERSION) >= 2.2 ){
              add_filter( 'comments_clauses', array( 'WC_Comments', 'exclude_webhook_comments' ), 10, 1 );
            }
            
          $o['order_notes']  = ob_get_contents();
          ob_end_clean();
          ob_start();
            $CPT_Shop_Order->render_shop_order_columns( 'order_date' );
          $o['order_date']  = ob_get_contents();
          ob_end_clean();
          ob_start();
            $CPT_Shop_Order->render_shop_order_columns( 'order_total' );
          $o['order_total']  = ob_get_contents();
          ob_end_clean();
        $orders_data[] = $o;

      }
    }else{
      $orders_data = array();
      $CPT_Shop_Order = new WC_Admin_CPT_Shop_Order();
      $user_email = '';
      if(isset($_GET['order_id']) ){
            $user_order_id = $_GET['order_id'];
            $u = new WC_Order( $user_order_id );
            $user_email = $u->billing_email;
       }else if(isset($_GET['user_id']) &&  !empty($_GET['user_id'])){
          $user_email = get_the_author_meta( 'email', $_GET['user_id'] );
          if(empty($user_email))
          $user_email = get_the_author_meta( 'billing_email', $_GET['user_id'] );
       }
       if(empty($user_email)) return $orders_data;
       $args = array(
        'numberposts' => -1,
        'post_type' => 'shop_order',
        'post_status' => 'publish',
        'meta_query' => array(
                      array(
                          'key' => '_billing_email',
                          'value' => $user_email
                      )
                  ),
      );
      $orders = get_posts( $args );
      
      foreach ( $orders as $order ) {
        $id = $order->ID;


       $post = $order;

        $o['ID'] = $post->ID;
        ob_start();
          $CPT_Shop_Order->custom_columns( 'order_status' );
          $o['order_status']  = ob_get_contents();
        ob_end_clean();
        $st = array('pending', 'failed', 'on-hold', 'processing', 'completed', 'refunded', 'cancelled');

        if(!in_array(strip_tags($o['order_status']), $st) ){
          ob_start();
          do_action( 'manage_shop_order_posts_custom_column', 'order_status');
          $o['order_status'] = ob_get_contents();
          ob_end_clean();
        }
        ob_start();
          $CPT_Shop_Order->custom_columns( 'order_title' );
        $o['order_title']  = ob_get_contents();
        ob_end_clean();
        ob_start();
          $CPT_Shop_Order->custom_columns( 'order_items' );
        $o['order_items']  = ob_get_contents();
        ob_end_clean();
        ob_start();
          $CPT_Shop_Order->custom_columns( 'shipping_address' );
        $o['shipping_address']  = ob_get_contents();
        ob_end_clean();
        ob_start();
          $CPT_Shop_Order->custom_columns( 'customer_message' );
        $o['customer_message']  = ob_get_contents();
        ob_end_clean();
        ob_start();
          $CPT_Shop_Order->custom_columns( 'order_notes' );
        $o['order_notes']  = ob_get_contents();
        ob_end_clean();
        ob_start();
          $CPT_Shop_Order->custom_columns( 'order_date' );
        $o['order_date']  = ob_get_contents();
        ob_end_clean();
        ob_start();
          $CPT_Shop_Order->custom_columns( 'order_total' );
        $o['order_total']  = ob_get_contents();
        ob_end_clean();
      $orders_data[] = $o;

    }
  }
  return $orders_data;
  }
 

}