<?php
/**
 * General global functions.
 *
 * @author   Actuality Extensions
 * @package  WooCommerce_Customer_Relationship_Manager
 * @since    1.0
 */


// Include MailChimp API class
if ( !class_exists( 'MCAPI_Wc_Crm' ) ) {
	require_once( 'admin/classes/api/MCAPI.class.php' );
}
/**
 * Gets the data about logs.
 */
function woocommerce_crm_get_logs_data() {
	global $logs_data, $activity_types, $created_dates, $log_users;

		$activity_types = array();
		$created_dates = array();
		$log_users = array();
		foreach ($logs_data as $key => $value) {
			############# TYPES #########################
			if ( !in_array( $value['activity_type'], array_keys( $activity_types ) ) ) {
				$activity_types[$value['activity_type']] = 1;
			} else {
				$activity_types[$value['activity_type']]++;
			}
			############ END OF TYPES ####################

			############# TYPES #########################
			if ( !in_array( $value['created'], array_keys( $created_dates ) ) ) {
				$created_dates[$value['created']] = 1;
			} else {
				$created_dates[$value['created']]++;
			}
			############ END OF TYPES ####################

			############# USERS #########################
			if ( !in_array( $value['user_id'], array_keys( $log_users ) ) ) {
				$log_users[$value['user_id']] = 1;
			} else {
				$log_users[$value['user_id']]++;
			}
			############ END OF USERS ####################
		}


}


/**
 * Obtains list of MailChimp registered users
 *
 * @return array
 */
function woocommerce_crm_get_members() {
	if ( !$retval = get_transient( 'woocommerce_crm_mailchimp_members' ) ) {
		$mc_api = new MCAPI_Wc_Crm( get_option( 'woocommerce_crm_mailchimp_api_key' ) ); // this assumes Subscribe to newsletter extension is enabled
		$retval = $mc_api->listMembers( get_option( 'woocommerce_crm_mailchimp_list', false ) ); // this assumes Subscribe to newsletter extension is enabled
		set_transient( 'woocommerce_crm_mailchimp_members', $retval, 60 * 60 * 1 );
	}

	$members = array();
	if(!empty($retval['data'])){
		foreach ( $retval['data'] as $item ) {
			array_push( $members, $item['email'] );
		}
	}
	return $members;
}

/**
 * Determine if MailChimp integration is enabled and set up.
 *
 * @return bool
 */
function woocommerce_crm_mailchimp_enabled() {
	return ( get_option( 'woocommerce_crm_mailchimp', 'no' ) == 'yes' && strlen( get_option( 'woocommerce_crm_mailchimp_api_key' ) ) > 0 && strlen( get_option( 'woocommerce_crm_mailchimp_list' ) ) > 0 ) ? true : false;
}

/**
 * Obtain better date/time formatting. Snippet borrowed from WooCommerce plugin.
 *
 * @param $post_id
 * @return string
 */
function woocommerce_crm_get_pretty_time( $post_id, $plain = false ) {
	$post = get_post( $post_id );
	if(!$post) return  '';
	if ( '0000-00-00 00:00:00' == $post->date ) {
		$t_time = $h_time = __( 'Unpublished', 'woocommerce' );
	} else {
		$t_time = get_the_time( __( 'Y/m/d g:i:s A', 'woocommerce' ), $post );

		$gmt_time = strtotime( $post->post_date_gmt . ' UTC' );
		$time_diff = current_time( 'timestamp', 1 ) - $gmt_time;

		if ( $time_diff > 0 && $time_diff < 24 * 60 * 60 )
			$h_time = sprintf( __( '%s ago', 'woocommerce' ), human_time_diff( $gmt_time, current_time( 'timestamp', 1 ) ) );
		else
			$h_time = get_the_time( __( 'Y/m/d', 'woocommerce' ), $post );
	}
	if ( $plain ) {
		return esc_attr( $t_time );
	} else {
		return '<abbr title="' . esc_attr( $t_time ) . '">' . esc_html( apply_filters( 'post_date_column_time', $h_time, $post ) ) . '</abbr>';
	}
}

/**
 * Obtains MailChimp lists for given API key.
 *
 * @param $api_key
 * @return array|bool
 */
function woocommerce_crm_get_mailchimp_lists( $api_key ) {
	$mailchimp_lists = array();
	if ( !$mailchimp_lists = get_transient( 'woocommerce_crm_mailchimp_lists' ) ) {

		$mailchimp = new MCAPI_Wc_Crm( $api_key );
		$retval = $mailchimp->lists();

		if ( $mailchimp->errorCode ) {

			echo '<div class="error"><p>' . sprintf( __( 'Unable to load lists() from MailChimp: (%s) %s', 'wc_customer_relationship_manager' ), $mailchimp->errorCode, $mailchimp->errorMessage ) . '</p></div>';

			return false;

		} else {
			foreach ( $retval['data'] as $list )
				$mailchimp_lists[$list['id']] = $list['name'];

			if ( sizeof( $mailchimp_lists ) > 0 )
				set_transient( 'woocommerce_crm_mailchimp_lists', $mailchimp_lists, 60 * 60 * 1 );
		}
	}

	return $api_key ? array_merge( array( '' => __( 'Select a list...', 'wc_customer_relationship_manager' ) ), $mailchimp_lists ) : array( '' => __( 'Enter your key and save to see your lists', 'wc_customer_relationship_manager' ) );

}

function wc_crm_get_statuses($arr = false, $without = false){
	global $wpdb;
	$sql = "SELECT * FROM {$wpdb->prefix}wc_crm_statuses";
	
	if($arr === true ){
		$result = $wpdb->get_results($sql, ARRAY_A );
		if(!$without){
			$statuses = WC_CRM()->statuses;
			foreach ($statuses as $key => $value) {
				$object = array();
				$object['status_name'] = $key;
				$object['status_slug'] = $value;
				$result[] = $object;
			}
		}
	}else{
		$result = $wpdb->get_results($sql);
		if(!$without){
			$statuses = WC_CRM()->statuses;
			foreach ($statuses as $key => $value) {
				$object = new stdClass();
				$object->status_name = $key;
				$object->status_slug = $value;
				$result[] = $object;
			}
		}
	}
	return $result;
}
function wc_crm_get_statuses_slug(){
	$st = wc_crm_get_statuses();
	$new_st = array();
	if(!empty($st)){
		foreach ($st as $s) {
			$new_st[$s->status_slug] = $s->status_name;
		}
	}
	return $new_st;
}
function wc_crm_get_status($id = 0){
	if($id){
		global $wpdb;
		$sql = "SELECT * FROM {$wpdb->prefix}wc_crm_statuses WHERE status_id = {$id} LIMIT 1";
		$result = $wpdb->get_results($sql, ARRAY_A );
		if($result)
			return $result[0];
		else
			return false;
	}else{
		return false;
	}
}
function wc_crm_get_status_by_slug($slug = ''){
	if($slug){
		global $wpdb;
		$sql = "SELECT * FROM {$wpdb->prefix}wc_crm_statuses WHERE status_slug = '{$slug}' LIMIT 1";
		$result = $wpdb->get_results($sql, ARRAY_A );
		return $result[0];
	}else{
		return false;
	}
}

function wc_crm_get_status_icon_code($number){
	$d = intval($number, 10);
  $s = '';
  if($d){
    $s = base_convert($d, 10, 16);
    $s = '&#x' . $s . ';';
  }
  return $s;
}

function wc_crm_get_num_orders($val='', $field = '_customer_user', $string = false){
	
	$num_orders_status = get_option('woocommerce_crm_number_of_orders');
  if(!$num_orders_status || empty($num_orders_status)){
    $num_orders_status[] = 'wc-completed';
  }
  $num_orders_statuses = "'" . implode("','", $num_orders_status) . "'";
  $wc_get_order_types  = "'" . implode( "','", wc_get_order_types( 'order-count' ) ) . "'";

  global $wpdb;

  if( $field != '' && $val != ''){
  	if($string)
  		$val = "'".$val."'";

  	$count = $wpdb->get_var( "SELECT COUNT(*)
						FROM $wpdb->posts as posts

						LEFT JOIN {$wpdb->postmeta} AS meta ON posts.ID = meta.post_id

						WHERE   meta.meta_key       = '{$field}'
						AND     posts.post_type     IN ({$wc_get_order_types})
						AND     posts.post_status   IN ({$num_orders_statuses})
						AND     meta_value          = {$val}
					" );
  	return $count;
	}
}
function wc_crm_get_order_value($val='', $field = '_customer_user', $string = false){
	
	$num_orders_status = get_option('woocommerce_crm_number_of_orders');
  if(!$num_orders_status || empty($num_orders_status)){
    $num_orders_status[] = 'wc-completed';
  }
  $num_orders_statuses = "'" . implode("','", $num_orders_status) . "'";
  $wc_get_order_types  = "'" . implode( "','", wc_get_order_types( 'order-count' ) ) . "'";

  global $wpdb;

  if( $field != '' && $val != ''){
  	if($string)
  		$val = "'".$val."'";
  	
  	$count = $wpdb->get_var( "SELECT SUM(meta.meta_value)

  					FROM {$wpdb->postmeta} as post

						LEFT JOIN {$wpdb->postmeta} AS meta ON (post.post_id = meta.post_id AND meta.meta_key = '_order_total')

						WHERE   post.meta_key            = '{$field}'
						AND     post.meta_value          = {$val} 
						" );
  	return $count;
	}
}