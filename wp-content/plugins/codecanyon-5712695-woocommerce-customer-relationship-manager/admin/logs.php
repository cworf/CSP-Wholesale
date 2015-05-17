<?php
/**
 * Logic related to displaying CRM page.
 *
 * @author   Actuality Extensions
 * @package  WooCommerce_Customer_Relationship_Manager
 * @since    1.0
 */

if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require_once( 'classes/wc_crm_logs.php' );

function wc_customer_relationship_manager_logs_add_options() {
	global $wc_crm_logs;

	$option = 'per_page';
	$args = array(
		'label' => __( 'Log Records', 'wc_customer_relationship_manager' ),
		'default' => 20,
		'option' => 'logs_per_page',
	);
	add_screen_option( $option, $args );
	$wc_crm_logs = new WC_Crm_Logs();
}

add_filter('set-screen-option', 'wc_customer_relationship_manager_set_logs_options', 10, 3);
function wc_customer_relationship_manager_set_logs_options($status, $option, $value) {
    if ( 'logs_per_page' == $option ) return $value;
    return $status;
}
/**
 * Renders CRM page.
 */
function wc_customer_relationship_manager_render_logs_page() {
global $wc_crm_logs;


	if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'view' && isset( $_REQUEST['log'] ) && !empty( $_REQUEST['log'] )) {
		echo '<div class="wrap" id="wc-crm-page"><div class="icon32"><img src="' . plugins_url( 'assets/img/customers-icons.png', dirname( __FILE__ ) ) . '" width="29" height="29" /></div>';
		$logs = $_REQUEST['log'];
		$wc_crm_logs->view_data($logs);

	}else{
		if ( isset( $_REQUEST['action'] ) && isset( $_REQUEST['log'] ) && !empty( $_REQUEST['log'] )) {
			$logs_array = array();
			$logs_string = '';
			if( is_array($_REQUEST['log']) ){
	      $logs_array = $_REQUEST['log'];
	      $logs_string = implode(',', $_REQUEST['log']);
	    }else{
	      $logs_array = explode(',', $_REQUEST['log']);
	      $logs_string = $_REQUEST['log'];
	    }
			switch( $_REQUEST['action'] ) {
	      case 'delete':
						$wc_crm_logs->delete_data($logs_array);
						$count_logs = count($logs_array);
						if($count_logs == 1)
							echo '<div id="message" class="updated"><p>'. $count_logs. __( ' post permanently deleted.', 'wc_customer_relationship_manager' ) . '</p></div>';
						else
							echo '<div id="message" class="updated"><p>'. $count_logs. __( ' posts permanently deleted.', 'wc_customer_relationship_manager' ) . '</p></div>';
	        	break;
	      case 'untrash':
						$wc_crm_logs->untrash_data($logs_array);
						$count_logs = count($logs_array);
						if($count_logs == 1)
							echo '<div id="message" class="updated"><p>'. $count_logs. __( ' post restored from the Trash.', 'wc_customer_relationship_manager' ) . '</p></div>';
						else
							echo '<div id="message" class="updated"><p>'. $count_logs. __( ' posts restored from the Trash.', 'wc_customer_relationship_manager' ) . '</p></div>';
	        	break;
      	case 'trash':
						$wc_crm_logs->move_to_trash($logs_array);
						$count_logs = count($logs_array);
						if($count_logs == 1)
							echo '<div id="message" class="updated"><p>'. $count_logs. __( ' post moved to the Trash.', 'wc_customer_relationship_manager' ) . ' <a href="?page=wc_crm_logs&action=untrash&log='.$logs_string.( (isset($_GET['iframe'] ) ? '&iframe=true' : '') ).( (isset($_GET['order_id'] ) ? '&order_id='.$_GET['order_id'] : '') ).'">'.__( 'Undo', 'wc_customer_relationship_manager' ).'</a></p></div>';
						else
							echo '<div id="message" class="updated"><p>'. $count_logs. __( ' posts moved to the Trash.', 'wc_customer_relationship_manager' ) . ' <a href="?page=wc_crm_logs&action=untrash&log='.$logs_string.( (isset($_GET['iframe'] ) ? '&iframe=true' : '') ).( (isset($_GET['order_id'] ) ? '&order_id='.$_GET['order_id'] : '') ).'">'.__( 'Undo', 'wc_customer_relationship_manager' ).'</a></p></div>';
	        	break;
	    }
		}
		$for ='';
		if( ! empty( $_GET['order_id'] ) ){
        $order = new WC_Order($_GET['order_id'] );
        $for = ' Search results for '.$order->billing_first_name.' '.$order->billing_last_name;
      }elseif( ! empty( $_GET['user_id'] ) ){
        $user_info = get_user_meta($_GET['user_id']);
        $for = ' Search results for customer '.$user_info['billing_first_name'][0].' '.$user_info['billing_last_name'][0];
      }
		echo '<div class="wrap" id="wc-crm-page"><div class="icon32"><img src="' . plugins_url( 'assets/img/customers-icons.png', dirname( __FILE__ ) ) . '" width="29" height="29" /></div><h2>' . __( 'Activity', 'wc_customer_relationship_manager' ).'<span class="subtitle">'.$for.'</span></h2>' ;

	?>
	<?php    
		$c_all_st = $wc_crm_logs->wc_crm_count_logs_status('all'); 
		$c_trash_st = $wc_crm_logs->wc_crm_count_logs_status('trash'); 
		?>
		<ul class="subsubsub">
				<li class="all"><a <?php echo (!isset( $_GET['log_status'] ) || $_GET['log_status'] != 'trash') ? 'class="current"' : '' ?> href="admin.php?page=wc_crm_logs">All <span class="count">(<?php echo $c_all_st; ?>)</span></a> |</li>
			<?php if ($c_trash_st > 0 ){?>
				<li class="trash"><a <?php echo (isset( $_GET['log_status'] ) && $_GET['log_status'] == 'trash') ? 'class="current"' : '' ?> href="admin.php?page=wc_crm_logs&log_status=trash">Trash <span class="count">(<?php echo $c_trash_st; ?>)</span></a></li>
			<?php }?>
		</ul>

		<form method="post">
			<input type="hidden" name="page" value="wc-customer-relationship-manager">
				<?php
				$wc_crm_logs->prepare_items();
				$wc_crm_logs->display();
			?>
		</form>
	<?php
	}

echo '</div>';
}