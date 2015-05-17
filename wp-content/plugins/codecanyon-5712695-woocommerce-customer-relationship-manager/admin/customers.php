<?php
/**
 * Logic related to displaying CRM page.
 *
 * @author   Actuality Extensions
 * @package  WooCommerce_Customer_Relationship_Manager
 * @since    1.0
 */

if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


add_action('init', 'wc_customer_relationship_manager_customers_actions');

add_action( 'init', 'wc_customer_relationship_manager_bulk_handler' );
function wc_customer_relationship_manager_bulk_handler(){
	if(!isset($_REQUEST['action']) && !isset($_REQUEST['action2']))
    return;

		$action = '';
		$statuses = wc_crm_get_statuses_slug();

		if ( isset( $_REQUEST['action'] ) && -1 != $_REQUEST['action'] )
			$action = $_REQUEST['action'];

		elseif ( isset( $_REQUEST['action2'] ) && -1 != $_REQUEST['action2'] )
			$action = $_REQUEST['action2'];

		
		if( !empty($action) ){

			if(array_key_exists($action, $statuses) ){

				if( !isset($_POST['user_id']) || empty($_POST['user_id']) ) return;
				$user_ids = $_POST['user_id'];
				wc_crm_change_customer_status($action, $user_ids);

			}else if( strstr( $action, 'crm_add_to_group_') ) {

				if( (!isset($_POST['user_id']) || empty($_POST['user_id']) ) && ( !isset($_POST['order_id']) || empty($_POST['order_id']) ) ) return;
					$filer = '';
					if(!empty($_POST['user_id'])){
						$filer .= "user_id IN(" . implode(',', $_POST['user_id']) . ")";
					}
					if(!empty($_POST['order_id'])){
						if(!empty($filer))
							$filer .= ' OR ';
						$filer .= "order_id IN(" . implode(',', $_POST['order_id']) . ")";
					}

	       $group_id = substr( $action, strlen('crm_add_to_group_') );

	       global $wpdb;
	       $cutomers = $wpdb->get_results("SELECT email FROM {$wpdb->prefix}wc_crm_customer_list WHERE $filer");
	       if(!empty($cutomers)){
	       		$user_emails = array();
	       		foreach ($cutomers as $cutomer) {
	       			$user_emails[] = $cutomer->email;
	       		}
	       		wc_crm_add_to_group($group_id, $user_emails);
	       		$_POST['report_action'] = "added_to_group";
	       }
	       
	    } else{
	        return;
	    }
  	}
}

function wc_customer_relationship_manager_customers_actions()
{
	if ( isset( $_POST['send'] ) && isset( $_POST['recipients'] ) && isset( $_POST['emaileditor'] ) && isset( $_POST['subject'] ) ) {
		WC_Crm_Email_Handling::process_form();
	}else	if ( isset( $_POST['save_phone_call'] ) ) {
		WC_Crm_Phone_Call::process_form();
	}
}

function wc_customer_relationship_manager_add_options() {
	$option = 'per_page';
	$args = array(
		'label' => __( 'Customers', 'wc_customer_relationship_manager' ),
		'default' => 20,
		'option' => 'customers_per_page'
	);
	add_screen_option( $option, $args );
	WC_CRM()->customers_table();
}

add_filter('set-screen-option', 'wc_customer_relationship_manager_set_options', 10, 3);
function wc_customer_relationship_manager_set_options($status, $option, $value) {
    if ( 'customers_per_page' == $option ) return $value;
    return $status;
}

/**
 * Gets template for emailing customers.
 *
 * @param $template_name
 * @param array $args
 */
function wc_crm_custom_woocommerce_get_template( $template_name, $args = array() ) {

	if ( $args && is_array( $args ) )
		extract( $args );

	$located = dirname( __FILE__ ) . '/templates/' . $template_name;

	do_action( 'woocommerce_before_template_part', $template_name, '', $located, $args );

	include( $located );

	do_action( 'woocommerce_after_template_part', $template_name, '', $located, $args );

}

/**
 * Renders CRM page.
 */
function wc_customer_relationship_manager_render_list_page() {
	echo '<div class="wrap" id="wc-crm-page">';
	wc_crm_page_title($_REQUEST);
	wc_crm_page_messages($_REQUEST);
	if(isset($_GET['message']) && $_GET['message'] == 1){
		echo '<div id="message" class="updated fade"><p>Customer added.</p></div>';
	}
	if(isset($_GET['message']) && $_GET['message'] == 2 && isset($_GET['added_rows']) && !empty($_GET['added_rows'])){
		echo '<div id="message" class="updated fade"><p>'.$_GET['added_rows'].' customers have been imported successfully.</p></div>';
	}
	$group = '';
	if(  isset($_REQUEST['group']) && !empty( $_REQUEST['group'] ) && $_REQUEST['group'] > 0 )
		$group = '&group='.$_REQUEST['group'];

	$statuses = wc_crm_get_statuses();

	?>

	
	<form method="post" id="wc_crm_customers_form" action="admin.php?page=wc-customer-relationship-manager<?php echo $group; ?>">
		<input type="hidden" name="page" value="wc-customer-relationship-manager">
		<?php	

		if ( isset( $_GET['order_list'] ) ) {
			require_once( 'classes/wc_crm_order_list.php');
			$wc_crm_order_list = new WC_Crm_Order_List();
			$wc_crm_order_list->prepare_items();
			$wc_crm_order_list->display();
		}elseif ( isset( $_GET['product_list'] ) ) {
			require_once( 'classes/wc_crm_product_list.php');
			$wc_crm_product_list = new WC_Crm_Product_List();
			$wc_crm_product_list->prepare_items();
			$wc_crm_product_list->display();
		}
		else if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'email' && !isset( $_REQUEST['send'] )) {
				WC_Crm_Email_Handling::display_form();
		}
		else if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'phone_call' && !isset( $_POST['save_phone_call'] ) ) {
			WC_Crm_Phone_Call::display_form();
		} else {
				?>
				<p class="search-box">
				<?php
					$ss ='';
					if ( !empty( $_POST['s'] ) ){
						echo '<a href="?page=wc-customer-relationship-manager" style="float: left; padding-right: 15px ; ">Reset</a>';
						$ss =$_POST['s'];
					}
				?>
					<label for="post-search-input" class="screen-reader-text"><?php _e('Search', 'wc_customer_relationship_manager'); ?></label>
					<input type="search" value="<?php echo $ss; ?>" name="s" id="post-search-input">
					<input type="submit" value="<?php _e('Search Customers', 'wc_customer_relationship_manager');?>" class="button" id="search-submit" name="">
				</p>
				<?php
				$customers_table = WC_CRM()->customers_table();
				$customers_table->prepare_items();
				$customers_table->display();
		}
		?>
	</form></div>
<?php
}

function wc_crm_page_title($request){
	if( ( isset($_REQUEST['group']) && !empty( $_REQUEST['group'] ) ) ){
		$group_name = '';
			global $wpdb;
			$table_name = $wpdb->prefix . "wc_crm_groups";
			$id = $_REQUEST['group'];
			$db_data = $wpdb->get_results("SELECT * FROM $table_name WHERE ID = $id");
			$group_name = ' <i>"'.$db_data[0]->group_name.'"</i>';
		echo '<h2>' . __( 'Customers group ', 'wc_customer_relationship_manager' ) . $group_name .' <a href="'.admin_url().'admin.php?page=wc_new_customer" class="add-new-h2">Add Customer</a></h2>';
	}elseif ( isset( $request['order_list'] ) ) {
		echo '<h2>' . __( 'Orders', 'wc_customer_relationship_manager' ) . '</h2>';
	}elseif ( isset( $request['product_list'] ) ) {
		echo '<h2>' . __( 'Products ', 'wc_customer_relationship_manager' ) . '</h2>';
	}
	else if ( isset( $request['action'] ) && $request['action'] == 'email'  && !isset( $request['send'] ) ) {}
	else if ( isset( $request['action'] ) && $request['action'] == 'phone_call' && !isset( $_POST['save_phone_call'] )) {}
	else {
			echo '<h2>' . __( 'Customers', 'wc_customer_relationship_manager' ) . ' <a href="'.admin_url().'admin.php?page=wc_new_customer" class="add-new-h2">Add Customer</a></h2>';
	}
}
function wc_crm_page_messages($request){
	$messages = array();
	if ( isset($request['update']) ) {

			switch ( $request['update'] ) {
				case "sent_email":
					$messages[] =  __( 'Your email has been successfully sent.', 'wc_customer_relationship_manager' );
					break;
				case "save_phone_call":
					$messages[] =  __( 'Phone Call has been saved.', 'wc_customer_relationship_manager' );
					break;
			}
	}
	if ( isset($request['action']) ) {
			switch ( $request['action'] ) {
				case "Favourite":
				case "Lead":
				case "Follow-Up":
				case "Prospect":
				case "Favourite":
				case "Blocked":
				case "Blocked":
				case "Flagged":
					$messages[] =  __( 'Customer status updated.', 'wc_customer_relationship_manager' );
					break;
			}
	}
	if ( ! empty( $messages ) ) {
		foreach ( $messages as $msg )
			echo '<div id="message" class="updated fade"><p>' . $msg . '</p></div>';
	}
}

function wc_crm_add_to_group($group_id = 0, $customers_emails = array()){
	if(!$group_id || empty($customers_emails) || !is_array($customers_emails)) return false;

	$groups_array = wc_get_static_groups_ids_array();
	foreach ($customers_emails as $customers_email) {
		if(!in_array($group_id, $groups_array)) continue;
		global $wpdb;
		$data = array(
			'group_id'       => $group_id,
			'customer_email' => $customers_email
		);
		$table =  $wpdb->prefix.'wc_crm_groups_relationships';
		$wpdb->show_errors();
  	$wpdb->query("INSERT INTO $table (group_id, customer_email) VALUES ({$group_id}, '{$customers_email}') ON DUPLICATE KEY UPDATE group_id = {$group_id}, customer_email = '{$customers_email}';");
	}

}

function wc_crm_change_customer_status($action = '', $user_ids = array())
	{

		if (empty($action) || empty($user_ids) || !is_array($user_ids)) return;

			foreach ($user_ids as $value) {
				$user_id = $value;				
				$status = '';
				
				$statuses = wc_crm_get_statuses_slug();
				if(array_key_exists($action, $statuses) ){
					update_user_meta( $user_id, 'customer_status', $action );
	        $status = $action;
				}
				global $wpdb;
				 $sql = "UPDATE {$wpdb->prefix}wc_crm_customer_list
				 					SET status = '$status'
              		WHERE user_id = $user_id
              
      	";
      	$wpdb->query($sql);
			}

	}

function get_customer_by_term($term=''){
	global $wpdb;
	$term = strtolower($term);
  $sql = "SELECT *
      FROM (
        SELECT 
          IF(fname.meta_value IS NULL , pfname.meta_value, fname.meta_value) as first_name,
          IF(lname.meta_value IS NULL , plname.meta_value, lname.meta_value) as last_name,
          customer.email as email,
          customer.user_id as user_id
        FROM {$wpdb->prefix}wc_crm_customer_list as customer
          LEFT JOIN {$wpdb->usermeta} fname ON (customer.user_id = fname.user_id AND fname.meta_key = 'first_name')
          LEFT JOIN {$wpdb->usermeta} lname ON (customer.user_id = lname.user_id AND lname.meta_key = 'last_name')

          LEFT JOIN {$wpdb->postmeta} pfname ON (customer.order_id = pfname.post_id AND pfname.meta_key = '_billing_first_name')
          LEFT JOIN {$wpdb->postmeta} plname ON (customer.order_id = plname.post_id AND plname.meta_key = '_billing_last_name')
      ) as customers
      WHERE LOWER(first_name) LIKE LOWER('%$term%') OR LOWER(last_name) LIKE LOWER('%$term%') OR LOWER(email) LIKE LOWER('%$term%') OR user_id LIKE '%$term%' OR concat_ws(' ',first_name,last_name) LIKE '%$term%'
      " ;
      $users = $wpdb->get_results($sql);
  return $users;
}