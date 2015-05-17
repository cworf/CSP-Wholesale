<?php
/**
 * Logic related to displaying CRM page.
 *
 * @author   Actuality Extensions
 * @package  WooCommerce_Customer_Relationship_Manager
 * @since    1.0
 */

if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

add_action( 'init', 'wc_customer_relationship_manager_import_handler' );
function wc_customer_relationship_manager_import_handler(){
  if(isset($_FILES['wcrm_import_customers'])){
    if ($_FILES['wcrm_import_customers']['size'] > 0) { 
        global $wpdb;

        //get the csv file 
        $file = $_FILES['wcrm_import_customers']['tmp_name']; 
        $skip_first   = true;
        $row          = 0;
        $not_import   = array();
        $username_opt = get_option('woocommerce_crm_username_add_customer');
        if (($handle = fopen($file, "r")) !== FALSE) {
          $tmp_registration_generate_password = get_option( 'woocommerce_registration_generate_password' );
          update_option( 'woocommerce_registration_generate_password', 'yes' );
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
              if($skip_first && $row == 0){
                $skip_first = false; continue;
              }

                $num = count($data);
                
                $user_email = trim($data[2]);
                if( empty($user_email) || email_exists( $user_email ) ){
                  $not_import[] = $data;
                  continue;
                }

                $nickname        = str_replace(' ', '', ucfirst(strtolower($data[0])) ) . str_replace(' ', '', ucfirst(strtolower($data[1])) );
                $username        = $data[3];
                if(empty($username)){
                  switch ($username_opt) {
                      case 2:
                          $username = str_replace(' ', '', strtolower($data[0]) ) . '-' . str_replace(' ', '', strtolower($data[1]) );
                          break;
                      case 3:
                          $username = $user_email;
                          break;            
                      default:
                          $username = strtolower($nickname);
                          break;
                  }
                }
                if(empty($username))
                  $username = $user_email;

                $username = _truncate_post_slug( $username, 60 );
                $check_sql = "SELECT user_login FROM {$wpdb->users} WHERE user_login = '%s' LIMIT 1";
                
                $user_name_check = $wpdb->get_var( $wpdb->prepare( $check_sql, $username ) );


                if ( $user_name_check ) {
                  $suffix = 1;
                  do {
                    $alt_user_name   = _truncate_post_slug( $username, 60 - ( strlen( $suffix ) + 1 ) ) . "-$suffix";
                    $user_name_check = $wpdb->get_var( $wpdb->prepare( $check_sql, $alt_user_name ) );
                    $suffix++;
                  } while ( $user_name_check );
                  $username = $alt_user_name;
                }
                //$random_password = wp_generate_password( 12, false );
                $user_id = wc_create_new_customer( $user_email, $username );
                
                

                if ( !empty($user_id ) && !is_wp_error( $user_id ) ) {
                  $row++;
                  if(isset($_POST['customer_status']))
                    update_user_meta( $user_id, 'customer_status', $_POST['customer_status'] );                 

                  update_user_meta( $user_id, 'nickname', $nickname );
                  if(isset($data[0]))
                    update_user_meta( $user_id, 'first_name', $data[0] );

                  if(isset($data[1]))
                    update_user_meta( $user_id, 'last_name', $data[1] );

                  if(isset($data[4]))
                    update_user_meta($user_id, 'billing_phone', $data[4]);

                  if(isset($data[5]))
                    update_user_meta( $user_id, 'date_of_birth', $data[5] );

                  if(isset($data[6]))
                    wp_update_user(array( 'ID' => $user_id, 'user_url' => $data[6] ) );

                  if(isset($_POST['customer_role']))
                    wp_update_user(array( 'ID' => $user_id, 'role' => $_POST['customer_role'] ) );

                  if(isset($data[7]))
                    update_user_meta( $user_id, 'twitter', str_replace('@', '', $data[7]) );

                  if(isset($data[8]))
                    update_user_meta( $user_id, 'skype', $data[8] );

                  if(isset($data[9]))
                    update_user_meta( $user_id, 'customer_categories', !empty($data[9]) ? array_filter(explode(',', $data[9])) : array() );

                  if(isset($data[10]))
                    update_user_meta( $user_id, 'customer_brands', !empty($data[10]) ? array_filter(explode(',', $data[10])) : array() );

                  do_action( 'profile_update', $user_id, array() );

                }
            }
            update_option( 'woocommerce_registration_generate_password', $tmp_registration_generate_password );
            fclose($handle);
        }

    }
    if($row == 0)
      $row = '';
    return wp_redirect( add_query_arg( array( "page" => "wc-customer-relationship-manager", "message" => 2, 'added_rows' => $row), 'admin.php' ) );
    die;
  }
}

function wc_customer_relationship_manager_render_import_page(){
  ?>
  <div class="wrap" id="wc-crm-page">
    <h2><?php _e( 'Import ', 'wc_customer_relationship_manager' ); ?></h2>
    <form action="" enctype="multipart/form-data" method="post"  id="wcrm_import_customers">
      <div class="wcrm_import_customers_wrap">
        <table class="form-table">
          <tbody>
            <tr class="form-field">
              <th valign="top" scope="row">
                <label><?php _e( 'Role ', 'wc_customer_relationship_manager' ); ?></label>
              </th>
              <td>
                <select name="customer_role">
                  <?php 
                  $selected ='customer';
                  foreach (get_editable_roles() as $role_name => $role_info):
                      echo '<option value="' . esc_attr( $role_name ) . '" ' . selected( $role_name, $selected, false ) . '>' . $role_info['name'] . '</option>';
                  endforeach; ?>
                </select>
              </td>
            </tr>
            <tr class="form-field">
              <th valign="top" scope="row">
                <label><?php _e( 'Status ', 'wc_customer_relationship_manager' ); ?></label>
              </th>
              <td>
                <select name="customer_status">
                  <?php
                  $selected ='Lead';
                  $statuses = wc_crm_get_statuses_slug();
                  foreach ( $statuses as $key => $status ) {
                    echo '<option value="' . esc_attr( $key ) . '" ' . selected( $key, $selected, false ) . '>' . esc_html__( $status, 'wc_customer_relationship_manager' ) . '</option>';
                  }
                  ?>
                </select>
              </td>
            </tr>
            <tr class="form-field">
              <th valign="top" scope="row">
                <label><?php _e( 'CSV ', 'wc_customer_relationship_manager' ); ?></label>
              </th>
              <td>
                <input type="file" name='wcrm_import_customers'>
              </td>
            </tr>
          </tbody>        
        </table>  
        <p class="submit">
          <input type="submit" value="<?php _e( 'Import ', 'wc_customer_relationship_manager' ); ?>" class="button-primary">
        </p>    
      </div>
      <div class="spiner_wrap"><span class="spinner"></span></div>
    </form>
  </div>
  <?php
}
