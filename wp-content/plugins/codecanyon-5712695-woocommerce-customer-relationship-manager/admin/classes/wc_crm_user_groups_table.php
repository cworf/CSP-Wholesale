<?php
/**
 * WoocommercePointOfSale Registers Table Class
 *
 * @author    Actuality Extensions
 * @package   WoocommercePointOfSale/Classes/Registers
 * @category	Class
 * @since     0.1
 */


if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
class WC_Pos_Groups_Table extends WP_List_Table {
  private static $data;

  function __construct(){

      parent::__construct( array(
          'singular'  => __( 'groups_table', 'wc_customer_relationship_manager' ),     //singular name of the listed records
          'plural'    => __( 'groups_tables', 'wc_customer_relationship_manager' ),   //plural name of the listed records
          'ajax'      => false        //does this table support ajax?
      ) );

  }

  public function get_data($ids = ''){
        global $wpdb;
        $filter = '';
        if( !empty($ids) ){
          if(is_array($ids)){
            $ids = implode(',', array_map('intval', $ids));
            $filter .= "WHERE ID IN  == ($ids)";
          }else{
            $filter .= "WHERE ID = $ids";
          }
        }
        if( isset($_GET['s']) && !empty($_GET['s']) && $_GET['page'] == 'wc_user_grps' ){
          $s = $_GET['s'];
          $filter = "WHERE lower( concat(group_name) ) LIKE lower('%$s%')";
        }
        $table_name = $wpdb->prefix . "wc_crm_groups";
        $db_data = $wpdb->get_results("SELECT * FROM $table_name $filter");
        $data = array();

        foreach ($db_data as $value) {
          #$value->detail = (array)json_decode($value->detail);
          #$value->settings  = (array)json_decode($value->settings);
          $data[] = get_object_vars($value);
        }
        return $data;
  }
  function no_items() {
    _e( 'Groups not found. Try to adjust the filter.', 'wc_customer_relationship_manager' );
  }
  function column_default( $item, $column_name ) {
    switch ( $column_name ) {
      case 'group_name':
      case 'group_slug':
      case 'group_type':
      case 'group_terms':
        return $item[$column_name];
      default:
        return print_r( $item, true ); //Show the whole array for troubleshooting purposes
    }
  }
  function get_sortable_columns() {
    $sortable_columns = array(
      'group_name' => array('group_name', false),
      'group_slug' => array('group_slug', false),
      'group_type' => array('group_type', false),
    );
    return $sortable_columns;
  }
  function get_columns() {
    $columns = array(
      'cb' => '<input type="checkbox" />',
      'group_name' => __( 'Name', 'wc_customer_relationship_manager' ),
      'group_slug' => __( 'Slug', 'wc_customer_relationship_manager' ),
      'group_type' => __( 'Type', 'wc_customer_relationship_manager' ),
      'group_terms' => __( 'Terms', 'wc_customer_relationship_manager' ),
      'group_action' => '',
    );
    return $columns;
  }
  function usort_reorder( $a, $b ) {
    // If no sort, default to last purchase
    $orderby = ( !empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'group_name';
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

  function get_bulk_actions() {
    $actions = apply_filters( 'wc_customer_relationship_manager_groups_bulk_actions', array(
      'delete' => __( 'Delete', 'wc_customer_relationship_manager' ),
    ) );
    return $actions;
  }

  function column_cb( $item ) {
    return sprintf(
      '<input type="checkbox" name="id[]" value="%s" />', $item['ID']
    );
  }
  function column_group_action( $item ) {
    return sprintf(
      '<a href="admin.php?page=wc-customer-relationship-manager&group=%s" class="button tips view" data-tip="' . esc_attr__( 'View Customers in Group', 'wc_customer_relationship_manager' ) . '">'.esc_attr__( 'View Customers in Group', 'wc_customer_relationship_manager' ).'</a>', $item['ID']
    );
  }
  function column_group_terms( $item ) {
    if($item['group_type'] == 'dynamic'){
      $output = '';
      if(!empty($item['group_last_order_from'])){
        switch ($item['group_last_order']) {
          case 'between':
            $output .= 'Date ' . $item['group_last_order'] . ' ' . $item['group_last_order_from'] . ' to ' . $item['group_last_order_to'] . '<br />';
            break;
          default:
            $output .= 'Date ' . $item['group_last_order'] . ' ' . $item['group_last_order_from'] . '<br />';
            break;
        }
      }
      if(!empty($item['group_user_role'])) $output .= 'User role is ' . $item['group_user_role'] . '<br />';
      if(!empty($item['group_customer_status'])){
        $group_customer_status = unserialize($item['group_customer_status']);
        if(!empty($group_customer_status)){
          if(count($group_customer_status) > 1 || !empty($group_customer_status[0]) )
            $output .= sprintf( __('Customer status is %s'), implode(', ', $group_customer_status) ) . '<br />';
        }
      }
      if(!empty($item['group_product_categories'])){
        $group_product_categories = unserialize($item['group_product_categories']);
        if(!empty($group_product_categories)){
          if(count($group_product_categories) > 1 || !empty($group_product_categories[0]) ){
            $cat_names = array();
            foreach ($group_product_categories as $cat) {
              $term = get_term_by('id', $cat, 'product_cat');
              $cat_names[] = $term->name;
            }
            $output .= sprintf( __('Product category is %s'), implode(', ', $cat_names) ) . '<br />';
          }
        }
      }
      if(!empty($item['group_order_status'])){
        $group_order_status = unserialize($item['group_order_status']);
        if(!empty($group_order_status)){
          if(count($group_order_status) > 1 || !empty($group_order_status[0]) ){
            $wc_statuses = wc_get_order_statuses();
            $staus_names = array();
            foreach ($group_order_status as $status) {
              $staus_names[] = $wc_statuses[$status];
            }
            $output .= sprintf( __('Order status is %s'), implode(', ', $staus_names) ) . '<br />';
          }
        }
      }
      if(!empty($item['group_total_spent'])) $output .= 'Total spent is ' . convert_group_total_spent_mark($item['group_total_spent_mark']) . ' ' . woocommerce_price($item['group_total_spent']) . '<br />';
      return $output;
    }
  }

  function column_group_name( $item ) {

    $actions = array(
      'edit'      => sprintf('<a href="?page=%s&action=%s&id=%s">Edit</a>', 'wc_user_grps','edit', $item['ID']),
      'delete'      => sprintf('<a href="?page=%s&action=%s&id=%s">Delete</a>', 'wc_user_grps','delete', $item['ID']),
    );

    $name = sprintf(
        '<strong><a style="display: block;" href="admin.php?page=%s&group=%s">%s</a></strong>', 'wc-customer-relationship-manager',  $item['ID'], $item['group_name']
    );

    return sprintf('%1$s %2$s', $name, $this->row_actions($actions) );
  }

  function prepare_items() {
    $columns  = $this->get_columns();
    $hidden   = array();
    self::$data = $this->get_data();
    $sortable = $this->get_sortable_columns();
    $this->_column_headers = array( $columns, $hidden, $sortable );
    usort( self::$data, array( &$this, 'usort_reorder' ) );

    $user = get_current_user_id();
    $screen = get_current_screen();
    $option = $screen->get_option('per_page', 'option');
    $per_page = get_user_meta($user, $option, true);
    if ( empty ( $per_page) || $per_page < 1 ) {
        $per_page = $screen->get_option( 'per_page', 'default' );
    }

    $current_page = $this->get_pagenum();

    $total_items = count( self::$data );
    if( $_GET['page'] == 'wc_user_grps' ){
      // only ncessary because we have sample data
      $this->found_data = array_slice( self::$data,( ( $current_page-1 )* $per_page ), $per_page );

      $this->set_pagination_args( array(
        'total_items'   => $total_items,                  //WE have to calculate the total number of items
        'per_page' => $per_page                     //WE have to determine how many items to show on a page
      ) );
      $this->items = $this->found_data;
    }else{
      $this->items = self::$data;
    }
  }

}