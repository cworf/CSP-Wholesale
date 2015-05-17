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

//require_once( 'wc_crm_customer_details.php' );

//require_once( plugin_dir_path( __FILE__ ) . '../../functions.php' );

class WC_Crm_Customers_Table extends WP_List_Table {
	private static $data;

	
	public $pending_count = array();

	function __construct() {
		parent::__construct( array(
			'singular' => __( 'customer', 'wc_customer_relationship_manager' ), //singular name of the listed records
			'plural' => __( 'customers', 'wc_customer_relationship_manager' ), //plural name of the listed records
			'ajax' => false //does this table support ajax?
			//'screen'   => isset( $args['screen'] ) ? $args['screen'] : null,
		) );
		add_action( 'admin_head', array(&$this, 'admin_header') );

		$this->mailchimp = array();
		if ( woocommerce_crm_mailchimp_enabled() ) {
			$this->mailchimp = woocommerce_crm_get_members();
		}
	}
	function admin_header() {
		$page = ( isset( $_GET['page'] ) ) ? esc_attr( $_GET['page'] ) : false;
		if ( WC_CRM()->id != $page )
			return;
		echo '<style type="text/css">';
		if ( woocommerce_crm_mailchimp_enabled() ) {
			echo '.wp-list-table .column-id {}';
			echo '.wp-list-table .column-customer_status { width: 45px;}';
			echo '.wp-list-table .column-customer_name { width: 15%;}';
			echo '.wp-list-table .column-email { width: 15%;}';
			echo '.wp-list-table .column-phone { width: 85px;}';
			echo '.wp-list-table .column-user { width: 10%;}';
			echo '.wp-list-table .column-last_purchase { width: 110px;}';
			echo '.wp-list-table .column-num_orders { width: 48px;}';
			echo '.wp-list-table .column-order_value { width: 10%;}';
			echo '.wp-list-table .column-enrolled { width: 47px;}';
			echo '.wp-list-table .column-customer_notes { width: 48px;}';
			echo '.wp-list-table .column-crm_actions { width: 120px;}';
		} else {
			echo '.wp-list-table .column-id {}';
			echo '.wp-list-table .column-customer_status { width: 45px;}';
			echo '.wp-list-table .column-customer_name { width: 15%;}';
			echo '.wp-list-table .column-email { width: 15%;}';
			echo '.wp-list-table .column-phone { width: 85px;}';
			echo '.wp-list-table .column-user { width: 10%;}';
			echo '.wp-list-table .column-last_purchase { width: 110px;}';
			echo '.wp-list-table .column-num_orders { width: 48px;}';
			echo '.wp-list-table .column-order_value { width: 10%;}';
			echo '.wp-list-table .column-customer_notes { width: 48px;}';
			echo '.wp-list-table .column-crm_actions { width: 120px;}';
		}
		echo '</style>';
	}

	
  function no_items() {
    _e( 'Registers not found. Try to adjust the filter.', 'wc_point_of_sale' );
  }
  function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'customer_status':
			case 'customer_name':
			case 'email':
			case 'phone':
			case 'user':
			case 'last_purchase':
			case 'num_orders':
			case 'order_value':
			case 'customer_notes':
			case 'enrolled':
			case 'crm_actions':
				return $item[$column_name];
			default:
				return print_r( $item, true ); //Show the whole array for troubleshooting purposes
		}
	}
  function get_sortable_columns() {
		$sortable_columns = array(
			'last_purchase' => array('last_purchase', true)
		);
		if ( woocommerce_crm_mailchimp_enabled() ) {
			$sortable_columns['enrolled'] = array('enrolled', true);
		};
		return $sortable_columns;
	}

  function get_columns() {
		$columns = array(
			'cb' => '<input type="checkbox" />',
			'customer_status' => '<span class="status_head tips" data-tip="' . esc_attr__( 'Customer Status', 'wc_customer_relationship_manager' ) . '">' . esc_attr__( 'Customer Status', 'wc_customer_relationship_manager' ) . '</span>',
			'customer_name' => __( 'Customer Name', 'wc_customer_relationship_manager' ),
			'email' => __( 'Email', 'wc_customer_relationship_manager' ),
			'phone' => __( 'Phone', 'wc_customer_relationship_manager' ),
			'user' => __( 'Username', 'wc_customer_relationship_manager' ),
			'last_purchase' => __( 'Last Order', 'wc_customer_relationship_manager' ),
			'num_orders' => '<span class="ico_orders tips" data-tip="' . esc_attr__( 'Number of Orders', 'wc_customer_relationship_manager' ) . '">' . esc_attr__( 'Number of Orders', 'wc_customer_relationship_manager' ) . '</span>',
			'customer_notes' => '<span class="ico_notes tips" data-tip="' . esc_attr__( 'Customer Notes', 'wc_customer_relationship_manager' ) . '">' . esc_attr__( 'Customer Notes', 'wc_customer_relationship_manager' ) . '</span>',
			'order_value' => '<span class="ico_value tips" data-tip="' . esc_attr__( 'Total Value', 'wc_customer_relationship_manager' ) . '">' . esc_attr__( 'Total Value', 'wc_customer_relationship_manager' ) . '</span>',
		);
		if ( woocommerce_crm_mailchimp_enabled() ) {
			$columns['enrolled'] = '<span class="ico_news tips" data-tip="' . esc_attr__( 'Newsletter Subscription', 'wc_customer_relationship_manager' ) . '">'.esc_attr__( 'Newsletter Subscription', 'wc_customer_relationship_manager' ).'</span>';
		};
		$columns['crm_actions'] = __( 'Actions', 'wc_customer_relationship_manager' );
		$columns = apply_filters( 'wc_pos_customer_custom_column', $columns );
		return $columns;
	}
  function usort_reorder( $a, $b ) {
		// If no sort, default to last purchase
		$orderby = ( !empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'last_purchase';
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
		$actions = array(
			'email' => __( 'Send Email', 'wc_customer_relationship_manager' ),
			'export_csv' => __( 'Export Contacts', 'wc_customer_relationship_manager' ),
		);
		$statuses = wc_crm_get_statuses();
		foreach ($statuses as $status) {
			$actions[$status->status_slug] = sprintf( __( 'Mark as %s', 'wc_customer_relationship_manager' ), $status->status_name );
		}
		$groups = wc_get_static_groups();
		
		foreach ($groups as $group) {
			$actions['crm_add_to_group_'.$group->ID] = sprintf( __( 'Add to %s', 'wc_customer_relationship_manager' ), $group->group_name );
		}
		return $actions;
	}

  function column_cb( $item ) {
    if($item['user_id'] && !empty($item['user_id']) && $data = get_userdata($item['user_id']) ){
      return '<label class="screen-reader-text" for="cb-select-' . $item['user_id'] . '">' . sprintf( __( 'Select %s' ), $data->user_nicename ) . '</label>'
            . "<input type='checkbox' name='user_id[]' id='user_" . $item['user_id'] . "' value='" . $item['user_id'] . "' />";
    }else if($item['order_id'] && !empty($item['order_id'])){
      return "<input type='checkbox' name='order_id[]' id='user_order_id".$item['order_id']."' value='".$item['order_id']."' />"; 
    }
  }
  function column_customer_status( $item ) {
    if($item['user_id'] && !empty($item['user_id']) && $data = get_userdata($item['user_id'])){

	  	if($item['status'] && !empty($item['status']) ){
	  			$default_statuses = WC_CRM()->statuses;
	  			$_status = $item['status'];

	    		if(array_key_exists($_status, $default_statuses) ){
						$customer_status = '<div style="position: relative;"><span class="'.$_status.' tips" data-tip="' . esc_attr( $_status ) . '"></span></div>';						
					}else{
						$custom_status = wc_crm_get_status_by_slug($_status);
						if($custom_status){
							$s = wc_crm_get_status_icon_code($custom_status['status_icon']);    
	    				$customer_status =  sprintf('<i data-icomoon="%s" data-fip-value="%s" style="color: %s;" class="tips" data-tip="' . esc_attr( $custom_status['status_name'] ) . '"></i>', $s, $custom_status['status_icon'],  $custom_status['status_colour']);							
						}else{
							$customer_status = '<div style="position: relative;">'.$_status.'</div>';							
						}
					}
					return $customer_status;
	  	}
	    else{
	    	return '<div style="position: relative;"><span class="Customer tips" data-tip="Customer"></span></div>';
	    }
  	}else{
  		return '';
  	}
  }
  function column_customer_name( $item ) {
  	if($item['user_id'] && !empty($item['user_id'])  && get_userdata($item['user_id'])){
	  	$data = get_user_meta($item['user_id']);
	  	if($data)
	    	return "<strong><a href='admin.php?page=wc_new_customer&user_id=" . $item['user_id'] . "'>" . $data['first_name'][0] . ' ' . $data['last_name'][0] . "<a></strong>";
	    else
	    	return '';
  	}else if($item['order_id'] && !empty($item['order_id'])){
  		$first_name = get_post_meta($item['order_id'], '_billing_first_name', true);
  		$last_name  = get_post_meta($item['order_id'], '_billing_last_name', true);
  		return "<strong><a href='admin.php?page=wc_new_customer&order_id=" . $item['order_id'] . "'>" . $first_name . ' ' . $last_name . "<a></strong>";
  	}else{
  		return '';
  	}
  }
  function column_email( $item ) {
    if($item['user_id'] && !empty($item['user_id']) && get_userdata($item['user_id'])){
    	$identifier = get_option('woocommerce_crm_unique_identifier');    	
	  	$data = get_user_meta($item['user_id']);
	  	if($identifier == 'username_email' || ( $data && !isset($data['billing_email']) )  ){
	  		$data  = get_userdata($item['user_id']);
	  		$email = $data->user_email;
	  	}else if($data){
	    	$email = $data['billing_email'][0];
	  	}
	    else{
	    	return '';
	    }

  	}else if($item['order_id'] && !empty($item['order_id'])){
  		$email = get_post_meta($item['order_id'], '_billing_email', true);
  	}
		return "<a href='mailto:$email' title='" . esc_attr( sprintf( __( 'E-mail: %s' ), $email ) ) . "'>$email</a>";

  }
  function column_phone( $item ) {
    if($item['user_id'] && !empty($item['user_id']) && get_userdata($item['user_id'])){
	  	$data = get_user_meta($item['user_id']);
	  	if($data && isset($data['billing_phone']))
	    	return $data['billing_phone'][0];
	    else
	    	return '';
  	}else if($item['order_id'] && !empty($item['order_id'])){
  		return get_post_meta($item['order_id'], '_billing_phone', true);
  	}else{
  		return '';
  	}
  }
  function column_user( $item ) {
  	$nicename = '';
  	if($item['user_id'] && !empty($item['user_id']) && $data = get_userdata($item['user_id'])){
	  	if($data)
	    	$nicename =  '<a href="admin.php?page=wc_new_customer&user_id=' . $item['user_id'] . '">' . $data->user_nicename . '</a>';
  	}else{
      $nicename = __( 'Guest', 'wc_customer_relationship_manager' );
    }
  	return $nicename;
  }
  function column_last_purchase( $item ) {
    if($item['order_id'] && !empty($item['order_id']) ){
			return woocommerce_crm_get_pretty_time( $item['order_id'] );
		}else{
			return '';
		}
  }
  function column_num_orders( $item ) {
    if($item['user_id'] && !empty($item['user_id']) && get_userdata($item['user_id'])){
	  	return wc_crm_get_num_orders($item['user_id']);
  	}else if($item['order_id'] && !empty($item['order_id'])){
  		$email = get_post_meta($item['order_id'], '_billing_email', true);
  		return wc_crm_get_num_orders($email, '_billing_email', true);
  	}else{
  		return 0;
  	}
  }
  function column_order_value( $item ) {
  	$total_spent = 0;
  	if($item['user_id'] && !empty($item['user_id']) && get_userdata($item['user_id'])){
	  	$total_spent =  wc_crm_get_order_value($item['user_id']);
  	}else if($item['order_id'] && !empty($item['order_id'])){
  		$email = get_post_meta($item['order_id'], '_billing_email', true);
  		$total_spent = wc_crm_get_order_value($email, '_billing_email', true);
  	}
    return wc_price( $total_spent );
  }
  function column_customer_notes( $item ) {
  	if($item['user_id'] && !empty($item['user_id']) && get_userdata($item['user_id'])){
	    $wc_crm_customer_details = new WC_Crm_Customer_Details($item['user_id']);
			$notes = $wc_crm_customer_details->get_last_customer_note();
			if($notes == 'No Customer Notes')
				$customer_notes = '<span class="note-off">-</span>';
			else
			  $customer_notes = '<a href="admin.php?page=wc_new_customer&screen=customer_notes&user_id='.$item['user_id'].'" class="fancybox note-on tips" data-tip="'.$notes.'"></a>';

			return $customer_notes;

		}else if($item['order_id'] && !empty($item['order_id'])){
  		return '<span class="note-off">-</span>';
  	}
  }
  function column_enrolled( $item ) {
		$email = '';
  	if($item['user_id'] && !empty($item['user_id']) && get_userdata($item['user_id'])){
  		$data = get_user_meta($item['user_id']);
	  		
  		if(!$data || !isset($data['billing_email']) ){
	  		$data  = get_userdata($item['user_id']);
	  		$email = $data->user_email;
	  	}else{
	    	$email = $data['billing_email'];
	  	}			
  	}else if($item['order_id'] && !empty($item['order_id'])){
  		$email = get_post_meta($item['order_id'], '_billing_email', true);
  	}

  	if ( woocommerce_crm_mailchimp_enabled() ) {
			return in_array( $email, $this->mailchimp ) ? "<span class='enrolled-yes'></span>" : "<span class='enrolled-no'></span>";
		}

  }
  function column_crm_actions( $item ) {
  	$actions = array();
    if($item['user_id'] && !empty($item['user_id']) && get_userdata($item['user_id'])){

    	$email = get_user_meta($item['user_id'], 'billing_email', true);

    	$phone = get_user_meta($item['user_id'], 'billing_phone', true);

    	if ( $item['order_id'] && !empty($item['order_id']) ){
					$actions['orders'] = array(
						'classes' => 'view',
						'url' => sprintf( 'edit.php?s=%s&post_status=%s&post_type=%s&shop_order_status&_customer_user&paged=1&mode=list&search_by_email_only', urlencode( $email ), 'all', 'shop_order' ),
						'action' => 'view',
						'name' => __( 'View Orders', 'wc_customer_relationship_manager' ),
						'target' => ''
					);					
				}
				$actions['email'] = array(
					'classes' => 'email',
					'url' => sprintf( '?page=%s&action=%s&user_id=%s', $_REQUEST['page'], 'email', $item['user_id'] ),
					'name' => __( 'Send Email', 'wc_customer_relationship_manager' ),
					'target' => ''
				);
				if ($phone){
					$actions['phone'] = array(
						'classes' => 'phone',
						'url' => sprintf( '?page=%s&action=%s&user_id=%s', $_REQUEST['page'], 'phone_call', $item['user_id'] ),
						'name' => __( 'Call Customer', 'wc_customer_relationship_manager' ),
						'target' => ''
					);
				}
				$actions['activity'] = array(
					'classes' => 'activity',
					'url' => sprintf( '?page=%s&user_id=%s', 'wc_crm_logs', $item['user_id']  ),
					'name' => __( 'Contact Activity', 'wc_customer_relationship_manager' ),
					'target' => ''
				);
    }else if($item['order_id'] && !empty($item['order_id']) && $item['order_id'] !=  null){
    	$email = get_post_meta($item['order_id'], '_billing_email', true);
    	$phone = get_post_meta($item['order_id'], '_billing_phone', true);
  		$actions['orders'] = array(
					'classes' => 'view',
					'url' => sprintf( 'edit.php?s=%s&post_status=%s&post_type=%s&shop_order_status&_customer_user&paged=1&mode=list&search_by_email_only', urlencode( $email ), 'all', 'shop_order' ),
					'action' => 'view',
					'name' => __( 'View Orders', 'wc_customer_relationship_manager' ),
					'target' => ''
				);
				$actions['email'] = array(
					'classes' => 'email',
					'url' => sprintf( '?page=%s&action=%s&order_id=%s', $_REQUEST['page'], 'email', $item['order_id'] ),
					'name' => __( 'Send Email', 'wc_customer_relationship_manager' ),
					'target' => ''
				);
				if ($phone){
					$actions['phone'] = array(
						'classes' => 'phone',
						'url' => sprintf( '?page=%s&action=%s&order_id=%s', $_REQUEST['page'], 'phone_call', $item['order_id'] ),
						'name' => __( 'Call Customer', 'wc_customer_relationship_manager' ),
						'target' => ''
					);
				}
				$actions['activity'] = array(
					'classes' => 'activity',
					'url' => sprintf( '?page=%s&order_id=%s', 'wc_crm_logs', $item['order_id']  ),
					'name' => __( 'Contact Activity', 'wc_customer_relationship_manager' ),
					'target' => ''
				);
  	}

		$crm_actions = '';
  	if(!empty($actions)){
			foreach ( $actions as $action ) {
				$crm_actions .= '<a class="button tips '.esc_attr($action['classes']).'" href="'.esc_url( $action['url'] ).'" data-tip="'.esc_attr( $action['name'] ).'" '.esc_attr( $action['target'] ).' >'.esc_attr( $action['name'] ).'</a>';
			}
		}
		return $crm_actions;
  }
  

  function prepare_items() {
    $columns  = $this->get_columns();
    $hidden   = array();

    

    $sortable = $this->get_sortable_columns();
    $this->_column_headers = array( $columns, $hidden, $sortable );
    

    $user = get_current_user_id();
    $screen = get_current_screen();
    $option = $screen->get_option('per_page', 'option');
    $per_page = get_user_meta($user, $option, true);
    if ( empty ( $per_page) || $per_page < 1 ) {
        $per_page = $screen->get_option( 'per_page', 'default' );
    }

    $current_page = $this->get_pagenum();
    $o = WC_CRM()->orders();
    self::$data = $o->get_orders($current_page, $per_page);
    usort( self::$data, array( &$this, 'usort_reorder' ) );

    $total_items = $o->orders_ount;
      $this->found_data = array_slice( self::$data,( ( $current_page-1 )* $per_page ), $per_page );

      $this->set_pagination_args( array(
        'total_items'   => $total_items,                  //WE have to calculate the total number of items
        'per_page' => $per_page                     //WE have to determine how many items to show on a page
      ) );
      $this->items = $this->found_data;
    
  }


	function extra_tablenav( $which ) {
		if ( $which == 'top' ) {
			do_action( 'wc_crm_restrict_list_customers' );
		}
	}


}