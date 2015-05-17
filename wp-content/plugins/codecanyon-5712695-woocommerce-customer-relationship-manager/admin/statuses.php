<?php
/**
 * Logic related to displaying CRM page.
 *
 * @author   Actuality Extensions
 * @package  WooCommerce_Customer_Relationship_Manager
 * @since    1.0
 */

if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

add_action( 'admin_post_wc_crm_add_customer_status', 'wc_crm_add_customer_status' );
add_action( 'admin_notices', 'wc_crm_statuses_admin_notices' );
add_action( 'init', 'wc_customer_relationship_manager_customer_status_bulk_handler' );
function wc_customer_relationship_manager_customer_status_bulk_handler(){
  if(!isset($_GET['page']) || $_GET['page'] != 'wc_crm_statuses')
    return;

  if(!isset($_REQUEST['action']) && !isset($_REQUEST['action2']))
    return;
  $action = '';
  if ( isset( $_REQUEST['action'] ) && -1 != $_REQUEST['action'] )
    $action = $_REQUEST['action'];

  elseif ( isset( $_REQUEST['action2'] ) && -1 != $_REQUEST['action2'] )
    $action = $_REQUEST['action2'];

  if( !empty($action) ){
    switch ($action) {
      case 'delete':
        wc_crm_add_customer_status_delete();
        break;
    }
  }
}
function wc_crm_add_customer_status_delete(){
  global $wpdb;
  if(isset($_REQUEST['id']) && !empty($_REQUEST['id'])){
    $id = $_REQUEST['id'];
    if(is_array($_REQUEST['id'])){
      $filter = " WHERE status_id IN(".implode(',', $id).") ";
      foreach ($id as $st_id) {
        $_status = wc_crm_get_status($st_id);
        $status  = $_status['status_slug'];
        if($status){
          $user_sql = "SELECT user_id FROM {$wpdb->prefix}wc_crm_customer_list WHERE status = '$status' ";
          $user_ids = array();
          $users = $wpdb->get_results($user_sql);
          if(!empty($users)){
            foreach ($users as $value) {
              $user_ids[] = $value->user_id;
            }
            wc_crm_change_customer_status('Customer', $user_ids);
          }
        }
      }
    }else{
      $filter = " WHERE status_id = $id";

        $_status = wc_crm_get_status($id);
        $status  = $_status['status_slug'];
        if($status){
          $user_sql = "SELECT user_id FROM {$wpdb->prefix}wc_crm_customer_list WHERE status = '$status' ";
          $user_ids = array();
          $users = $wpdb->get_results($user_sql);
          if(!empty($users)){
            foreach ($users as $value) {
              $user_ids[] = $value->user_id;
            }
            wc_crm_change_customer_status('Customer', $user_ids);
          }
        }
    }
    
    $table = $wpdb->prefix . "wc_crm_statuses";
    $sql = "DELETE FROM $table $filter";


    $wpdb->query($sql);
    return wp_redirect( add_query_arg( array( "page" => "wc_crm_statuses", 'msg'=>3), 'admin.php' ) );
  }
}
function wc_crm_add_customer_status(){
  global $wpdb;
  $table = $wpdb->prefix . "wc_crm_statuses";
  extract($_POST);

  if(!isset($status_slug) || empty($status_slug) ){
    $status_slug = sanitize_title($status_name);
  }else{
    $status_slug = sanitize_title($status_slug);
  }
  $filter = '';
  if(isset($status_id) && !empty($status_id)){
    $filter = " AND status_id != {$status_id}";
  }
  $check_sql = "SELECT status_slug FROM {$table} WHERE status_slug = '%s' {$filter} LIMIT 1";

  $slug_check = $wpdb->get_var( $wpdb->prepare( $check_sql, $status_slug ) );


  if ( $slug_check ) {
    $suffix = 2;
    do {
      $alt_slug = _truncate_post_slug( $status_slug, 200 - ( strlen( $suffix ) + 1 ) ) . "-$suffix";
      $slug_check = $wpdb->get_var( $wpdb->prepare( $check_sql, $alt_slug ) );
      $suffix++;
    } while ( $slug_check );
    $status_slug = $alt_slug;
  }
  $data = array(
    'status_name'   => $status_name,
    'status_slug'   => $status_slug,
    'status_icon'   => $status_icon,
    'status_colour' => $status_colour,
  );
  if(isset($status_id) && !empty($status_id)){
    if( $wpdb->update( $table, $data, array('status_id'=>$status_id) ) ){          
        return wp_redirect( add_query_arg( array( "page" => "wc_crm_statuses", 'msg'=>2), 'admin.php' ) );
    }
  }else{
    if( $wpdb->insert($table, $data) ){
        return wp_redirect( add_query_arg( array( "page" => "wc_crm_statuses", 'msg'=>1), 'admin.php' ) );
    }
  }
  return wp_redirect( add_query_arg( array( "page" => "wc_crm_statuses"), 'admin.php' ) );
}
function wc_crm_statuses_admin_notices(){
  if(isset($_GET['page']) && $_GET['page'] == 'wc_crm_statuses' && isset($_GET['msg']) && !empty($_GET['msg'])){
    switch ($_GET['msg']) {
      case 1:
        ?>
        <div class="updated">
            <p><?php _e( 'Status added', 'wc_customer_relationship_manager' ); ?></p>
        </div>
        <?php
        break;
      case 2:
        ?>
        <div class="updated">
            <p><?php _e( 'Status updated', 'wc_customer_relationship_manager' ); ?></p>
        </div>
        <?php
        break;
      case 3:
        ?>
        <div class="updated">
            <p><?php _e( 'Status deleted', 'wc_customer_relationship_manager' ); ?></p>
        </div>
        <?php
        break;
    }
  }
    
}

function wc_customer_relationship_manager_render_statuses_page(){
  if(isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id']) && !empty($_GET['id'])){
    $data = wc_crm_get_status($_GET['id']);
    ?>
    <div class="wrap">
      <h2><?php _e( 'Edit Status ', 'wc_customer_relationship_manager' ); ?></h2>
      <form action="admin-post.php" method="post"  id="wc_crm_customer_statuses">
        <input type="hidden" value="wc_crm_add_customer_status" name="action">
        <input type="hidden" value="<?php echo $_GET['id']; ?>" name="status_id">  
        <table class="form-table">
          <tbody>
            <tr class="form-field form-required">
              <th scope="row">
                <label for="status_name"><?php _e( 'Name', 'wc_customer_relationship_manager' ); ?></label>
              </th>
              <td>
                <input id="status_name" type="text" aria-required="true" size="40" value="<?php echo $data['status_name']; ?>" name="status_name">
              </td>
            </tr>
            <tr class="form-field">
              <th scope="row">
                <label for="status_slug"><?php _e( 'Slug', 'wc_customer_relationship_manager' ); ?></label>
              </th>
              <td>
                <input id="status_slug" type="text" aria-required="true" size="40" value="<?php echo $data['status_slug']; ?>" name="status_slug">
              </td>
            </tr>
            <tr class="form-field form-required">
              <th scope="row">
                <label for="status_icon"><?php _e( 'Icon', 'wc_customer_relationship_manager' ); ?></label>
              </th>
              <td>
                <input id="status_icon" type="text" aria-required="true" size="40" value="<?php echo $data['status_icon']; ?>" name="status_icon">
              </td>
            </tr>
            <tr class="form-field form-required">
              <th scope="row">
                <label for="status_colour"><?php _e( 'Colour', 'wc_customer_relationship_manager' ); ?></label>
              </th>
              <td>
                <input id="status_colour" type="text" aria-required="true" size="40" value="<?php echo $data['status_colour']; ?>" name="status_colour">
              </td>
            </tr>
          </tbody>
        </table>
        <p class="submit"><input type="submit" value="Save Status" class="button button-primary" id="submit" name="submit"></p>
      </form>
    </div>
    <?php
  }else{
    ?>
    <div class="wrap nosubsub" id="wc-crm-page">
      <h2><?php _e( 'Customer Status ', 'wc_customer_relationship_manager' ); ?></h2>
      <div id="col-container">
        <div id="col-right">
          <div class="col-wrap">
            <form action="" method="post">
              <?php
              require_once( WC_CRM()->plugin_path().'/admin/classes/wc_crm_statuses_table.php' );
              $statuses_table = new WC_CRM_Statuses_Table;
              $statuses_table->prepare_items();
              $statuses_table->display();
              ?>
            </form>
          </div>
        </div><!-- /col-right -->
        <div id="col-left">
          <div class="col-wrap">
            <div class="form-wrap">
              <form action="admin-post.php" method="post"  id="wc_crm_customer_statuses">
                <input type="hidden" value="wc_crm_add_customer_status" name="action">   
                <div class="form-field form-required">
                  <label for="status_name"><?php _e( 'Name', 'wc_customer_relationship_manager' ); ?></label>
                  <input id="status_name" type="text" aria-required="true" size="40" value="" name="status_name">
                </div>
                 <div class="form-field">
                  <label for="status_slug"><?php _e( 'Slug', 'wc_customer_relationship_manager' ); ?></label>
                  <input id="status_slug" type="text" aria-required="true" size="40" value="" name="status_slug">
                </div>
                <div class="form-field form-required">
                  <label for="status_icon"><?php _e( 'Icon', 'wc_customer_relationship_manager' ); ?></label>
                  <input id="status_icon" type="text" aria-required="true" size="40" value="" name="status_icon">
                </div>
                <div class="form-field form-required">
                  <label for="status_colour"><?php _e( 'Colour', 'wc_customer_relationship_manager' ); ?></label>
                  <input id="status_colour" type="text" aria-required="true" size="40" value="" name="status_colour">
                </div>
                <p class="submit"><input type="submit" value="Add New Status" class="button button-primary" id="submit" name="submit"></p>
              </form>
            </div>
          </div>
        </div><!-- /col-left -->
      </div><!-- /col-container -->
    </div>
    <?php
  }
}
