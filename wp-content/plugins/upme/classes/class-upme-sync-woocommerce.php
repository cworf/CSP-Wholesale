<?php

class UPME_Sync_Woo {

	public $woocommerce_file_path;
	public $woocommerce_version;
	public $woocommerce_version_status;
	 

	function __construct() {
		global $pagenow;

		if('admin.php' == $pagenow && isset($_GET['page']) && ('upme-sync-tools' == $_GET['page']) 
			&& in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ){

			// Load Woocommerce file based on version number
			$this->woocommerce_version =  get_option('woocommerce_db_version');
			$plugin_dir_path = plugin_dir_path( dirname(dirname(__FILE__)) );
			if(version_compare( $this->woocommerce_version, '2.0.20') == '1'){
				$this->woocommerce_file_path = $plugin_dir_path.'woocommerce/includes/admin/class-wc-admin-profile.php';
				
				$this->woocommerce_version_status = '1';
				$this->woo_admin_profile = include($this->woocommerce_file_path);

			}else{
				$this->woocommerce_file_path = $plugin_dir_path.'woocommerce/admin/woocommerce-admin-users.php';
				$this->woocommerce_version_status = '0';
				require_once $this->woocommerce_file_path;
			}

			if ( isset($_REQUEST['sync']) && !isset($_POST['submit']) && !isset($_POST['upme-add']) && !isset($_POST['reset-options']) && !isset($_POST['reset-options-fields']) ) {
				
				if ($_REQUEST['sync'] == 'woocommerce') {
				
				/* load fields */
				$fields = get_option('upme_profile_fields');

				/*  Add the exisitng entries to prevent duplication of woocommerce fields */
				$field_meta_array = array();
				$separator_array = array();
				foreach ($fields as $field) {
					$field_meta = isset($field['meta']) ? $field['meta'] : '';
					if('' != $field_meta){
						array_push($field_meta_array, $field['meta']);
					}else if('separator' == $field['type']){
						array_push($separator_array, $field['name']);
					}				
				}
				
				/* Add WooCommerce profile fields */
				$woo_meta  = array();
				if(file_exists($this->woocommerce_file_path)){
					if('1' == $this->woocommerce_version_status){

						//$woo_admin_profile = new WC_Admin_Profile();
						$woo_meta = $this->woo_admin_profile->get_customer_meta_fields();
					}else{
						$woo_meta = woocommerce_get_customer_meta_fields();
					}				
				}else{
					echo __('Woocommerce File Doesn\'t Exist','upme');exit;
				}	
				
				$new_index = max(array_keys($fields));

				foreach($woo_meta as $group => $array) {

					
					if(!in_array($array['title'], $separator_array)){
						$fields[$new_index+=10] = array( 
							'type' => 'separator', 
							'name' => $array['title'],
							'private' => 0,
							'deleted' => 0,
							'meta' => $array['title'].'_separator'
						);
					}				
					
					foreach($array['fields'] as $meta => $label) {

						if(!in_array($meta, $field_meta_array)){				
						
						/* switch icon */
						switch ($meta) {
							case 'billing_first_name': $icon = 'user'; break;
							case 'billing_last_name': $icon = 0; break;
							case 'billing_company': $icon = 'building'; break;
							case 'billing_address_1': $icon = 'home'; break;
							case 'billing_address_2': $icon = 0; break;
							case 'billing_city': $icon = 0; break;
							case 'billing_postcode': $icon = 0; break;
							case 'billing_state': $icon = 0; break;
							case 'billing_country': $icon = 'map-marker'; break;
							case 'billing_phone': $icon = 'phone'; break;
							case 'billing_email': $icon = 'envelope'; break;
							case 'shipping_first_name': $icon = 'user'; break;
							case 'shipping_last_name': $icon = 0; break;
							case 'shipping_company': $icon = 'building'; break;
							case 'shipping_address_1': $icon = 'home'; break;
							case 'shipping_address_2': $icon = 0; break;
							case 'shipping_city': $icon = 0; break;
							case 'shipping_postcode': $icon = 0; break;
							case 'shipping_state': $icon = 0; break;
							case 'shipping_country': $icon = 'map-marker'; break;
							default: $icon = 0; break;
						}
						
						switch($meta) {
							
							case 'billing_country':
							$fields[$new_index+=10] = array(
								'icon' => $icon, 
								'field' => 'select', 
								'type' => 'usermeta', 
								'meta' => $meta, 
								'name' => $label['label'],
								'can_hide' => 1,
								'can_edit' => 1,
								'predefined_loop' => 'countries',
								'private' => 0,
								'social' => 0,
								'deleted' => 0
							);
							break;

							case 'shipping_country':
							$fields[$new_index+=10] = array(
								'icon' => $icon, 
								'field' => 'select', 
								'type' => 'usermeta', 
								'meta' => $meta, 
								'name' => $label['label'],
								'can_hide' => 1,
								'can_edit' => 1,
								'predefined_loop' => 'countries',
								'private' => 0,
								'social' => 0,
								'deleted' => 0
							);
							break;
								
							default:
							$fields[$new_index+=10] = array(
								'icon' => $icon, 
								'field' => 'text', 
								'type' => 'usermeta', 
								'meta' => $meta, 
								'name' => $label['label'],
								'can_hide' => 1,
								'can_edit' => 1,
								'private' => 0,
								'social' => 0,
								'deleted' => 0
							);
							break;
						
						}
					}
						
					}
					
				}
				
				update_option('upme_profile_fields', $fields);
				echo '<div class="updated"><p><strong>'.__('WooCommerce customer fields have been added successfully.','upme').'</strong></p></div>';
				
				}
				
				if ($_REQUEST['sync'] == 'woocommerce_clean') {


				/* Add WooCommerce profile fields */
				$woo_meta  = array();
				if(file_exists($this->woocommerce_file_path)){
					if('1' == $this->woocommerce_version_status){
						
						
						$woo_meta = $this->woo_admin_profile->get_customer_meta_fields();
					}else{
						$woo_meta = woocommerce_get_customer_meta_fields();
					}				
				}else{
					echo __('Woocommerce File Doesn\'t Exist','upme');exit;
				}	

							
				$new_index = 0;
				
				foreach($woo_meta as $group => $array) {
					
					$fields[$new_index+=10] = array( 
						'type' => 'separator', 
						'name' => $array['title'],
						'private' => 0,
						'deleted' => 0,
						'meta' => $array['title'].'_separator'
					);
					
					foreach($array['fields'] as $meta => $label) {
						
						/* switch icon */
						switch ($meta) {
							case 'billing_first_name': $icon = 'user'; break;
							case 'billing_last_name': $icon = 0; break;
							case 'billing_company': $icon = 'building'; break;
							case 'billing_address_1': $icon = 'home'; break;
							case 'billing_address_2': $icon = 0; break;
							case 'billing_city': $icon = 0; break;
							case 'billing_postcode': $icon = 0; break;
							case 'billing_state': $icon = 0; break;
							case 'billing_country': $icon = 'map-marker'; break;
							case 'billing_phone': $icon = 'phone'; break;
							case 'billing_email': $icon = 'envelope'; break;
							case 'shipping_first_name': $icon = 'user'; break;
							case 'shipping_last_name': $icon = 0; break;
							case 'shipping_company': $icon = 'building'; break;
							case 'shipping_address_1': $icon = 'home'; break;
							case 'shipping_address_2': $icon = 0; break;
							case 'shipping_city': $icon = 0; break;
							case 'shipping_postcode': $icon = 0; break;
							case 'shipping_state': $icon = 0; break;
							case 'shipping_country': $icon = 'map-marker'; break;
							default: $icon = 0; break;
						}
						
						switch($meta) {
							
							case 'billing_country':
							$fields[$new_index+=10] = array(
								'icon' => $icon, 
								'field' => 'select', 
								'type' => 'usermeta', 
								'meta' => $meta, 
								'name' => $label['label'],
								'can_hide' => 1,
								'can_edit' => 1,
								'predefined_loop' => 'countries',
								'private' => 0,
								'social' => 0,
								'deleted' => 0
							);
							break;
							
							case 'shipping_country':
							$fields[$new_index+=10] = array(
								'icon' => $icon, 
								'field' => 'select', 
								'type' => 'usermeta', 
								'meta' => $meta, 
								'name' => $label['label'],
								'can_hide' => 1,
								'can_edit' => 1,
								'predefined_loop' => 'countries',
								'private' => 0,
								'social' => 0,
								'deleted' => 0
							);
							break;
								
							default:
							$fields[$new_index+=10] = array(
								'icon' => $icon, 
								'field' => 'text', 
								'type' => 'usermeta', 
								'meta' => $meta, 
								'name' => $label['label'],
								'can_hide' => 1,
								'can_edit' => 1,
								'private' => 0,
								'social' => 0,
								'deleted' => 0
							);
							break;
						
						}
						
					}
					
				}
				
				update_option('upme_profile_fields', $fields);
				echo '<div class="updated"><p><strong>'.__('WooCommerce customer fields have been added successfully.','upme').'</strong></p></div>';
				
				}
					
			}
		}
		
	}

}

$upme_sync_woocommerce = new UPME_Sync_Woo();