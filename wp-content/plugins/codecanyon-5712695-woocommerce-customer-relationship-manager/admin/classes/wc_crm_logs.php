<?php
/**
 * CRM Logs
 *
 * @author   Actuality Extensions
 * @package  WooCommerce_Customer_Relationship_Manager
 * @since    1.0
 */

if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

require_once( plugin_dir_path( __FILE__ ) . '../../functions.php' );

add_action( 'admin_head', 'admin_header_crm_logs' );
  function admin_header_crm_logs() {
    $page = ( isset($_GET['page'] ) ) ? esc_attr( $_GET['page'] ) : false;
    if( 'wc_crm_logs' != $page )
    return;
  }

class WC_Crm_Logs extends WP_List_Table {
    private static $data;

    function __construct(){
    global $status, $page;

        parent::__construct( array(
            'singular'  => __( 'log', 'wc_customer_relationship_manager' ),     //singular name of the listed records
            'plural'    => __( 'logs', 'wc_customer_relationship_manager' ),   //plural name of the listed records
            'ajax'      => false        //does this table support ajax?

    ) );

    }

  function no_items() {
    _e( 'No found.' );
  }

  function get_data(){
        global $wpdb, $logs_data, $months;
        $user_email ='';
        if( !empty( $_GET['order_id'] ) ){
          $order = new WC_Order($_GET['order_id'] );
          $user_email = $order->billing_email;
        }else if(!empty( $_GET['user_id'] )){
          $user_email = get_the_author_meta( 'email', $_GET['user_id'] );
          if(empty($user_email))
          $user_email = get_the_author_meta( 'billing_email', $_GET['user_id'] );
        }else if(!empty( $_GET['userid'] )){
          $user_email = get_the_author_meta( 'email', $_GET['userid'] );
          if(empty($user_email))
          $user_email = get_the_author_meta( 'billing_email', $_GET['userid'] );
        }

        //$filter = ( ! empty( $_GET['order_id'] ) ) ? 'WHERE user_email = \''.$user_email.'\'' : '';
        $filter  = '';
        $filter  .= ( ! empty( $user_email ) ) ? 'WHERE (locate(\''.$user_email.'\',user_email)>0)' : '';

        if( !empty( $_REQUEST['activity_types'] ) ){
          if($filter == '') $filter .= 'WHERE ';
          else  $filter .= ' AND ';
            $filter .= 'activity_type = "'.$_REQUEST['activity_types'].'"';
        }
        if( isset( $_GET['log_status'] ) && $_GET['log_status'] == 'trash' ){
          if($filter == '') $filter .= 'WHERE ';
          else  $filter .= ' AND ';
            $filter .= 'log_status = \'trash\' ';
        }else{
          if($filter == '') $filter .= 'WHERE ';
          else  $filter .= ' AND ';
            $filter .= 'log_status <> \'trash\' ';
        }
        if( !empty( $_REQUEST['log_users'] ) ){
          if($filter == '') $filter .= 'WHERE ';
          else  $filter .= ' AND ';
            $filter .= 'user_id = '.$_REQUEST['log_users'];
        }
        $filter_m = '';
        if( !empty( $_REQUEST['created_date'] ) ){
          $month = substr($_REQUEST['created_date'], -2);
          if( $month{0} == 0 ) $month = substr($month, -1);
          $year = substr($_REQUEST['created_date'], 0, 4);
          if($filter == '') $filter_m .= 'WHERE ';
          else  $filter_m .= ' AND ';
            $filter_m .= 'YEAR( created ) = ' . $year . ' AND MONTH( created ) = ' . $month;
        }
        $orderby = ( ! empty( $_GET['orderby'] ) ) ? 'ORDER BY '.$_GET['orderby'] : 'ORDER BY created';
        $order = ( ! empty($_GET['order'] ) ) ? $_GET['order'] : 'ASC';

        $table_name = $wpdb->prefix . "wc_crm_log";
        $db_data = $wpdb->get_results("SELECT * FROM $table_name $filter $filter_m $orderby $order");
        $data = array();

        foreach ($db_data as $value) {
            $data[] = get_object_vars($value);
        }

        $logs_data = $data;

        $months = $wpdb->get_results("
          SELECT DISTINCT YEAR( created ) AS year, MONTH( created ) AS month
          FROM $table_name
          $filter
          ORDER BY created DESC
        " );
        return $data;
    }
  function delete_data($ids){
    global $wpdb;
    $table_name = $wpdb->prefix . "wc_crm_log";
    if( is_array($ids) ){
      foreach ($ids as $id) {
        $wpdb->query("DELETE FROM $table_name WHERE ID = $id");
      }
    }else{
     $n_ids = explode(',', $ids);
     foreach ($n_ids as $id) {
        $wpdb->query("DELETE FROM $table_name WHERE ID = $id");
      }
    }
  }
  function move_to_trash($ids){
    global $wpdb;
    $table_name = $wpdb->prefix . "wc_crm_log";
    if( is_array($ids) ){
      foreach ($ids as $id) {
        $wpdb->query("UPDATE $table_name SET log_status = 'trash' WHERE ID = $id");
      }
    }else{
      $n_ids = explode(',', $ids);
      foreach ($n_ids as $id) {
        $wpdb->query("UPDATE $table_name SET log_status = 'trash' WHERE ID = $id");
      }
    }
  }
  function untrash_data($ids){
    global $wpdb;
    $table_name = $wpdb->prefix . "wc_crm_log";
    if( is_array($ids) ){
      foreach ($ids as $id) {
        $wpdb->query("UPDATE $table_name SET log_status = 'publish' WHERE ID = $id");
      }
    }else{
      $n_ids = explode(',', $ids);
      foreach ($n_ids as $id) {
        $wpdb->query("UPDATE $table_name SET log_status = 'publish' WHERE ID = $id");
      }
    }
  }
  function column_default( $item, $column_name ) {
    switch( $column_name ) {
        case 'subject':
        case 'activity_type':
        case 'created':
        case 'crm_actions':
            return $item[ $column_name ];
        case 'user_id':
            $user_info  = get_userdata( $item[ $column_name ] );
            return '<a href="'.get_edit_user_link($item[ $column_name ]).'">'.$user_info->display_name.'</a>';
        default:
            return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
    }
  }

function get_sortable_columns() {
  $sortable_columns = array(
    'subject'  => array('subject',false),
    'activity_type'   => array('activity_type',false),
    'created'   => array('created',false),
    'user_id'   => array('user_id',false)
  );
  return $sortable_columns;
}

function get_columns(){
  if( $_GET['page'] != 'wc_new_customer' ){
    $columns['cb'] = '<input type="checkbox" />';
  }
  $columns['subject'] = __( 'Subject', 'wc_customer_relationship_manager' );
  $columns['activity_type'] = __( 'Type', 'wc_customer_relationship_manager' );
  $columns['created'] = __( 'Date', 'wc_customer_relationship_manager' );
  $columns['user_id'] = __( 'Author', 'wc_customer_relationship_manager' );

    if( !isset($_GET['log_status']) || $_GET['log_status'] != 'trash'){
      $columns['crm_actions'] = __( 'Actions', 'wc_customer_relationship_manager' );
    }
   return $columns;
}

function usort_reorder( $a, $b ) {
  // If no sort, default to title
  $orderby = ( ! empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'created';
  // If no order, default to desc
  $order = ( ! empty($_GET['order'] ) ) ? $_GET['order'] : 'ASC';
  // Determine sort order
  $result = strcmp( $a[$orderby], $b[$orderby] );
  // Send final sort direction to usort
  return ( $order === 'desc' ) ? $result : -$result;
}


function column_crm_actions( $item ) {
    global $woocommerce;

    $actions = array(
      'view' => array(
        'classes' => 'view',
        'url' => sprintf('?page=%s&action=%s&log=%s'.( (isset($_GET['order_id'] ) ? '&order_id='.$_GET['order_id'] : '') ),'wc_crm_logs','view',$item['ID']),
        'name' => __( 'View', 'wc_customer_relationship_manager' )
      ),
      'delete_log' => array(
        'classes' => 'delete_log',
        'url' => sprintf('?page=%s&action=%s&log=%s'.( (isset($_GET['order_id'] ) ? '&order_id='.$_GET['order_id'] : '') ),'wc_crm_logs','trash',$item['ID']),
        'name' => __( 'Trash', 'wc_customer_relationship_manager' ),
      )
    );

    echo '<p>';
    foreach ( $actions as $action ) {
      printf( '<a class="button tips %s" href="%s" data-tip="%s">%s</a>', esc_attr($action['classes']), esc_url( $action['url'] ), esc_attr( $action['name'] ), esc_attr( $action['name'] ) );
    }
    echo '</p>';

  }

function get_bulk_actions() {
  $actions = array();
  if( $_GET['page'] != 'wc_new_customer' ){
    if( isset($_GET['log_status'])  && $_GET['log_status'] == 'trash'){
      $actions = array(
        'untrash'    => 'Restore',
        'delete'    => 'Delete Permanently'
      );
    }else{
      $actions = array(
        'trash'    => 'Move to Trash'
      );
    }
  }
  return $actions;
}

function column_cb($item) {
    return sprintf(
        '<input type="checkbox" name="log[]" value="%s" />', $item['ID']
    );
}
function column_subject($item) {
  $actions = array();
  $subject = '';
  if( isset($_GET['log_status'])  &&  $_GET['log_status'] == 'trash'){
    $actions = array(
      'untrash'      => sprintf('<a href="?page=%s&log_status=trash&action=%s&log=%s">Restore</a>',$_GET['page'],'untrash',$item['ID']),
      'delete'    => sprintf('<a href="?page=%s&log_status=trash&action=%s&log=%s">Delete Permanently</a>',$_GET['page'],'delete',$item['ID']),
    );
    $subject =  '<strong>' . $item['subject'] . '</strong>';
  }else{
    $actions = array(
      'id'      => sprintf('ID: %s ', $item['ID']),
      'view'      => sprintf('<a href="?page=%s&action=%s&log=%s'.( (isset($_GET['order_id'] ) ? '&order_id='.$_GET['order_id'] : '') ).'">View</a>','wc_crm_logs','view',$item['ID']),
      'trash'    => sprintf('<a href="?page=%s&action=%s&log=%s'.( (isset($_GET['order_id'] ) ? '&order_id='.$_GET['order_id'] : '') ).'">Trash</a>','wc_crm_logs','trash',$item['ID']),
    );
    $subject =  sprintf(
        '<strong><a href="?page=%s&action=%s&log=%s'.( (isset($_GET['order_id'] ) ? '&order_id='.$_GET['order_id'] : '') ).'">%s</a></strong>','wc_crm_logs', 'view', $item['ID'], $item['subject']
    );
  }


  return sprintf('%1$s %2$s', $subject, $this->row_actions($actions) );

}
function column_activity_type($item) {
    if($item['activity_type'] == 'email'){
      return '<i class="email tips" data-tip="' . esc_attr__( 'Email', 'woocommerce-crm' ) . '"></i>';
    }else if($item['activity_type'] == 'phone call'){
      return '<i class="phone tips" data-tip="' . esc_attr__( 'Phone', 'woocommerce-crm' ) . '"></i>';
    }else{
      return '-';
    }
}
function column_created($item) {
    $t_time = date("Y/m/d g:i:s A", strtotime($item['created'] ) );

    if ( '0000-00-00 00:00:00' == $item['created_gmt'] ) {
      $item['created_gmt'] =  get_gmt_from_date( $item['created'] );
    }
    $gmt_time = strtotime( $item['created_gmt'] . ' UTC' );
    $time_diff = current_time( 'timestamp', 1 ) - $gmt_time;

    if ( $time_diff > 0 && $time_diff < 24 * 60 * 60 )
      $h_time = sprintf( __( '%s ago', 'woocommerce' ), human_time_diff( $gmt_time, current_time( 'timestamp', 1 ) ) );
    else
      $h_time = date("Y/m/d", strtotime($item['created'] ) );
    return '<abbr title="' . esc_attr( $t_time ) . '">' . esc_html( $h_time) . '</abbr>';
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
  if( $_GET['page'] != 'wc_new_customer' ){
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
function view_data($ids){
   #echo '<pre>';
  ?>
  <style>
  .panel-wrap{
    background: #fff;
    border: 1px solid #e5e5e5;
	-webkit-box-shadow: 0 1px 1px rgba(0,0,0,.04);
	box-shadow: 0 1px 1px rgba(0,0,0,.04);
	margin-bottom: 20px;
	padding: 0;
	line-height: 1;
  }
  #log_data{
    padding: 20px;
  }
  #log_data h2{
  	font-weight: 100;
  }
  #log_data h2 span{
    font-size: 18px;
    padding: 5px;
    display: block;
  }
  .activity_date{
    float: right;
    font-size: 14px;
    color: #555555;
  }
  .ico_phone, .ico_mail {
	  float: right;
	  font-size: 10em;
	  color: #d3d3d3;
  }
  </style>
  <?php

   global $wpdb;
   $table_name = $wpdb->prefix . "wc_crm_log";
    $db_data = $wpdb->get_results("SELECT * FROM $table_name WHERE ID = $ids");
    foreach ($db_data as $key => $value) {
      ?>
      <?php if($value->activity_type == 'phone call'):?>
        <?php $date = date("d F Y", strtotime($value->created));  ?>
        <?php $time = date("H:i:s", strtotime($value->created));  ?>
        <h2><?php echo __('View Phone Call', 'wc_customer_relationship_manager');?></h2>
        <div class="panel-wrap woocommerce">
          <div id="log_data" class="panel">
            <i class="ico_phone"></i>
            <h2>Activity Details</h2>
            <table class="view-activity">
                <tr>
                  <th width="200"><strong>Activity ID</strong></th>
                  <td><?php echo $value->ID; ?></td>
                </tr>
                <tr>
                  <th width="200"><strong>Subject</strong></th>
                  <td><?php echo stripslashes($value->subject); ?></td>
                </tr>
                <tr>
                  <th width="200"><strong>Type</strong></th>
                  <td><?php echo $value->call_type; ?></td>
                </tr>
                <tr>
                  <th width="200"><strong>Purpose</strong></th>
                  <td><?php echo $value->call_purpose; ?></td>
                </tr>
                <tr>
                  <th><strong>Customer Name</strong></th>
                  <td>
                      <?php
                        $user = get_user_by( 'email', $value->user_email );
                        if(!empty($user)){
                          echo '<a href="'.get_edit_user_link($user->ID).'">'. $user->first_name . ' ' . $user->last_name.'</a>';
                        }else{
                          global $wpdb;
                          $table_name  = $wpdb->prefix . "wc_crm_customer_list";
                          $order_id    = $wpdb->get_var("SELECT order_id FROM $table_name WHERE email = '$email' LIMIT 1");
                          if($order_id){
                            $first_name = get_post_meta($order_id, '_billing_first_name', true);
                            $last_name  = get_post_meta($order_id, '_billing_last_name', true);
                            echo $first_name. '' .$last_name;                            
                          }
                        }
                      ?>
                   </td>
                </tr>
                <tr>
                    <th width="100"><strong>Related To</strong></th>
                    <td>
                      <?php echo ucwords($value->related_to); ?>
                      <a href="post.php?post=<?php echo substr($value->number_order_product, 1); ?>&action=edit"> <?php echo $value->number_order_product; ?></a>
                    </td>
                </tr>
                <tr>
                    <th width="100"><strong>Call Date</strong></th>
                    <td><?php echo $date; ?></td>
                </tr>
                <tr>
                    <th width="100"><strong>Call Time</strong></th>
                    <td><?php echo $time; ?></td>
                </tr>
                <tr>
                    <th width="100"><strong>Call Duration</strong></th>
                    <td><?php echo $this->convertToHoursMins($value->call_duration); ?></td>
                </tr>
                <tr>
                <th><strong>Call Result</strong></th>
                <td><?php echo stripslashes($value->message); ?></td>
              </tr>
            </table>

          </div>
        </div>
      <?php endif; ?>
      <?php if($value->activity_type == 'email'):?>
        <?php $date = date("d F Y", strtotime($value->created));  ?>
        <?php $time = date("H:i:s", strtotime($value->created));  ?>
        <?php $user_info  = get_userdata( $value->user_id );
              $user_link = $user_info->user_email.' <a href="'.get_edit_user_link($value->user_id).'" target="_blank">'.$user_info->first_name.' '.$user_info->last_name.'</a>';
        ?>
        <h2><?php echo __('View Email', 'wc_customer_relationship_manager'); ?></h2>
        <div class="panel-wrap woocommerce">
          <div id="log_data" class="panel">
            <i class="ico_mail"></i>
            <h2>Activity Details</h2>
            <table class="view-activity">
          	  <tr>
                <th width="200"><strong>Activity ID</strong></th>
                <td><?php echo $value->ID; ?></td>
              </tr>
              <tr>
                <th width="200"><strong>Email Sender</strong></th>
                <td>
                <?php 
                  $userdata = get_userdata( $value->user_id ); 
                  echo '<a href="mailto:'.$userdata->user_email.'">'.$userdata->user_email.'</a> (<a href="user-edit.php?user_id='.$value->user_id.'">'.$userdata->user_firstname.' '.$userdata->user_lastname.'</a>)';
                ?>
                </td>
              </tr>
              <tr>
                <th width="200"><strong>Email Recipients</strong></th>
                <td>
                  <?php
                    $emails = explode(',', $value->user_email);
                    $emails_str = '';
                    foreach ($emails as $email_c) {
                      if(!empty($emails_str)) $emails_str .= ', ';
                      $emails_str .= "'".$email_c."'";
                    }
                    global $wpdb;
                    $sql = "SELECT * FROM {$wpdb->prefix}wc_crm_customer_list                    
                      WHERE email IN ($emails_str)
                      ORDER BY email ASC
                    " ;
                    $results = $wpdb->get_results( $sql);
                    $d = 0;
                    foreach ($results as $customer) {
                      if($d) echo ', ';
                      if($customer->user_id && !empty($customer->user_id) && $customer->user_id != NULL){
                        $data = get_user_meta($customer->user_id);
                        $first_name = $data['first_name'][0];
                        $last_name  = $data['last_name'][0];

                        echo '<a href="admin.php?page=wc-customer-relationship-manager&action=email&user_id='.$customer->user_id.'">'.$customer->email.'</a> (<a href="admin.php?page=wc_new_customer&user_id='.$customer->user_id.'">'.$first_name.' '.$last_name.'</a>)';
                      }
                      elseif( !empty($customer->order_id) && $customer->order_id != NULL){
                        $first_name = get_post_meta($customer->order_id, '_billing_first_name', true);
                        $last_name  = get_post_meta($customer->order_id, '_billing_last_name', true);
                        echo '<a href="admin.php?page=wc-customer-relationship-manager&action=email&order_id='.$customer->order_id.'">'.$customer->email.'</a> (<a href="admin.php?page=wc_new_customer&order_id='.$customer->order_id.'">'.$first_name.' '.$last_name.'</a>)';
                      }
                      $d++;
                    }
                   ?>
                </td>
              </tr>
			        <tr>
                <th width="200"><strong>Subject</strong></th>
                <td><?php echo stripslashes($value->subject); ?></td>
              </tr>
              <tr>
                <th width="200"><strong>Email Date & Time</strong></th>
                <td><?php echo $date; ?> at <?php echo $time; ?></td>
              </tr>
              <tr>
                <th width="200"><strong>Message</strong></th>
                <td><?php echo stripslashes($value->message); ?></td>
              </tr>
            </table>
          </div>
        </div>
      <?php endif; ?>
      <p><a href="?page=<?php echo $_GET['page']?><?php echo( (isset($_GET['order_id'] ) ? '&order_id='.$_GET['order_id'] : '') ); ?><?php echo( (isset($_GET['iframe'] ) ? '&iframe=true' : '') ); ?>" class="button button-primary button-large"><?php echo __( 'Back', 'wc_customer_relationship_manager' ) ?></a></p>
      <?php
    }
  }

 function extra_tablenav( $which ) {
  if( $_GET['page'] != 'wc_new_customer' ){
    if ( $which == 'top' ) {
     do_action( 'wc_crm_restrict_list_logs' );
    }
  }
 }
function wc_crm_count_logs_status($status){
    global $wpdb;
    $table_name = $wpdb->prefix . "wc_crm_log";
    switch ( $status ) {
      case "all":
        $myrows = $wpdb->get_results( "SELECT id FROM $table_name WHERE log_status <> 'trash' " );
        return count($myrows);
        break;
      case "trash":
        $myrows = $wpdb->get_results( "SELECT id FROM $table_name WHERE log_status = 'trash' " );
        return count($myrows);
        break;
      default: return 0 ;
    }
}

  public function convertToHoursMins($time) {
    $new_time = '';
    $time_arr = explode(':', $time);
    $h = intval($time_arr[0]);
    $m = intval($time_arr[1]);
    $s = intval($time_arr[2]);
    $new_time .= ($h > 0) ? $h.'h ' : '';
    $new_time .= ($m > 0) ? $m.'m ' : '';
    $new_time .= ($s > 0) ? $s.'s' : '';
    return $new_time;
  }

} //class