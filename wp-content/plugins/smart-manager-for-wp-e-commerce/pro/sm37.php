<?php

global $wp_version;

if (version_compare ( $wp_version, '4.0', '>=' )) {
    global $locale;
    load_textdomain( 'smart-manager', WP_PLUGIN_DIR . '/' . dirname( dirname(plugin_basename( __FILE__ ))) . '/languages/smart-manager-' . $locale . '.mo' );
} else {
    load_textdomain( 'smart-manager', WP_PLUGIN_DIR . '/' . dirname(dirname(plugin_basename( __FILE__ ))) . '/languages/smart-manager-' . WPLANG . '.mo' );
}

// Update product
function update($obj) {
	global $result, $wpdb;
	$query = "UPDATE " . WPSC_TABLE_PRODUCT_LIST . " SET 
										  name ='" . $wpdb->_real_escape($obj->name) . "',
                                         price = " . $wpdb->_real_escape($obj->price) . ",
                                 special_price = " . $wpdb->_real_escape($obj->price) . " - (" . $wpdb->_real_escape($obj->sale_price) . "),
                                      quantity = " . $wpdb->_real_escape($obj->quantity) . ",
                                       publish = if('" . $wpdb->_real_escape($obj->publish) . "' = 'publish','1','0'),
                                        weight = " . $wpdb->_real_escape($obj->weight) . ",
                                   description = '" . $wpdb->_real_escape($obj->description) . "',
                        additional_description = '" . $wpdb->_real_escape($obj->additional_description) . "',
                                   no_shipping = " . $wpdb->_real_escape($obj->no_shipping) . ",
                                           pnp = " . $wpdb->_real_escape($obj->pnp) . ",
                             international_pnp = " . $wpdb->_real_escape($obj->international_pnp) . ",
                                   weight_unit = '" . $wpdb->_real_escape($obj->weight_unit) . "',
                              quantity_limited = if(" . $wpdb->_real_escape($obj->quantity) . " < 0,'0','1')
                                      WHERE id = " . $wpdb->_real_escape($obj->id);		
	$update_productListTbl = $wpdb->query ( $query );
	
	$dimensions['height']      = $wpdb->_real_escape($obj->height);
	$dimensions['height_unit'] = $wpdb->_real_escape($obj->height_unit);
	$dimensions['width']       = $wpdb->_real_escape($obj->width);
	$dimensions['width_unit']  = $wpdb->_real_escape($obj->width_unit);
	$dimensions['length']      = $wpdb->_real_escape($obj->length);
	$dimensions['length_unit'] = $wpdb->_real_escape($obj->length_unit);
	
	$productmeta_values['dimensions'] = serialize($dimensions);
	$productmeta_values['sku'] = $wpdb->_real_escape($obj->sku);
	
	if($productmeta_values != null) {
		foreach((array)$productmeta_values as $key => $value) {
			$query = "UPDATE ".WPSC_TABLE_PRODUCTMETA." SET meta_value = '$value' WHERE meta_key = '$key' AND product_id = " . $wpdb->_real_escape($obj->id);
			$update_productMetaTbl = $wpdb->query ( $query );
		}
	}
	
	if ($update_productMetaTbl || $update_productListTbl)
		$result ['result'] = true;
	return $result;
}

function get_log_id($email_id) {	
	$query = "SELECT log_id FROM " . WPSC_TABLE_SUBMITED_FORM_DATA . " WHERE unique_name = 'billingemail' AND value = '" . $wpdb->_real_escape($email_id) . "'";
	$result = $wpdb->get_results ( $query );
	$records = array ();
	foreach ( $result as $obj ) {
		$data = (array) $obj;
		$records [] = $data ['log_id'];
	}
	
	if (is_array ( $records ))
		return $log_id = implode ( ',', $records );
}

// Update customers details
function update_customers($post) {
	global $wpdb;
	$_POST = $post;
	$query = "SELECT isocode,country FROM `" . WPSC_TABLE_CURRENCY_LIST . "` ORDER BY `country` ASC";
	$result = $wpdb->get_results ( $query );
	foreach ( $result as $obj ) {
		$data = (array) $obj;
		$isocode_country [$data ['isocode']] = $data ['country'];
	}
	
	$query = "SELECT id, name FROM " . WPSC_TABLE_REGION_TAX;
	$result = $wpdb->get_results ( $query );
	foreach ( $result as $obj ) {
		$data = (array) $obj;
		$regions_id [$data ['id']] = $data ['name'];
	}
	
	$query  = "SELECT id,unique_name FROM " . WPSC_TABLE_CHECKOUT_FORMS . " WHERE unique_name in ('billingfirstname', 'billinglastname', 'billingaddress', 'billingcity', 'billingstate', 'billingcountry', 'billingpostcode', 'billingphone', 'billingemail')";
	$result = $wpdb->get_results( $query, 'ARRAY_A' );
	if ( count($result) >= 1 ){
		foreach ($result as $key => $arr_value)
		$id_uniquename [$arr_value ['unique_name']] = $arr_value ['id'];
	}		

	$affected_rows = 0;
	$edited_objects = json_decode ( stripslashes ( $_POST ['edited'] ) );	
	foreach ($edited_objects as $obj){
		$old_email_id = $wpdb->_real_escape($obj->Old_Email_Id);

		foreach ((array)$id_uniquename as $uniquename => $form_id ) {
			$update_value = $wpdb->_real_escape($obj->$uniquename);

			if ($uniquename == 'billingcountry' || $uniquename == 'billingstate'){
				$b_country  = array();
				
				if (array_search ($wpdb->_real_escape($obj->billingcountry), $isocode_country))
				$b_country [] = array_search ($wpdb->_real_escape($obj->billingcountry), $isocode_country);

				if(array_search ( $wpdb->_real_escape($obj->billingstate), $regions_id ))
				$b_country [] = (string)array_search ( $wpdb->_real_escape($obj->billingstate), $regions_id );

				$update_value = serialize ( $b_country );
			}
			
			//$key contains unique name
			$query = "UPDATE " . WPSC_TABLE_SUBMITED_FORM_DATA . " s1,
						     	  " . WPSC_TABLE_SUBMITED_FORM_DATA . " s2
						     	  
				         		 SET s1.value   = '$update_value'
				         	WHERE s1.form_id =  $form_id
				         	  AND s1.log_id  = '" . $wpdb->_real_escape($obj->id) ."'
				         	  AND s2.value   = '" . $wpdb->_real_escape($old_email_id) . "'";
			$update_result = $wpdb->query($query);

			if ($update_result)
			$affected_rows ++;
		}
	}
	
	$result = array ();
	if ($affected_rows >= 1) {
		$result ['result'] = true;
		$result ['updateCnt'] = count ( $edited_objects );
	} else {
		$result ['result'] = true;
		$result ['updateCnt'] = 0;
	}
	$result ['updated'] = 1;
	return $result;
}


function data_for_update_orders($post) {
	global $purchlogs, $wpsc_purchase_log_statuses;
	$_POST = $post;     // Fix: PHP 5.4
        $query = "SELECT id,country_id, name, code FROM ".WPSC_TABLE_REGION_TAX;
	$res = $wpdb->get_results($query);

	if (count($res) >= 1){
		foreach ($res as $obj) {
			$data = (array) $obj;
			$regions[$data['id']] = $data['name'];
		}
	}

	$query = "SELECT isocode,country FROM `".WPSC_TABLE_CURRENCY_LIST."` ORDER BY `country` ASC";
	$res = $wpdb->get_results($query);

	if (count($res) >= 1){
		foreach ($res as $obj) {
			$data = (array) $obj;
			$countries[$data['isocode']] = $data['country'];
		}
	}

	//getting the id,uniquename
	$query  = "SELECT id,name,unique_name
		 		FROM " . WPSC_TABLE_CHECKOUT_FORMS . " 
				WHERE unique_name IN ('shippingfirstname', 'shippinglastname', 'shippingaddress', 'shippingcity', 'shippingstate','shippingcountry', 'shippingpostcode')";
	$res    = $wpdb->get_results($query);
	foreach($res as $obj) {
		$data = (array) $obj;
		$id_uniquename[$data['unique_name']] = $data['id'];
	}

	$edited_object = json_decode ( stripslashes ( $_POST ['edited'] ) );
	$_POST = array ();
	$ordersCnt = 1;
	foreach ( $edited_object as $obj ) {
		$query = "UPDATE `" . WPSC_TABLE_PURCHASE_LOGS . "`
				                    SET processed ='{$wpdb->_real_escape($obj->order_status)}',notes='{$wpdb->_real_escape($obj->notes)}',track_id ='{$wpdb->_real_escape($obj->track_id)}'
				                    WHERE id='{$wpdb->_real_escape($obj->id)}'";
		$update_result = $wpdb->query ( $query );

		foreach ($id_uniquename as $uniquename => $form_id) {
			$update_value = $wpdb->_real_escape($obj->$uniquename);

			if ($uniquename == 'shippingcountry' || $uniquename == 'shippingstate'){
				$b_country  = array();

				if (array_search ($wpdb->_real_escape($obj->shippingcountry), $countries))
				$b_country [] = array_search ($wpdb->_real_escape($obj->shippingcountry), $countries);

				if(array_search ( $wpdb->_real_escape($obj->shippingstate), $regions ))
				$b_country [] = (string)array_search ( $wpdb->_real_escape($obj->shippingstate), $regions );

				$update_value = serialize ( $b_country );
			}

			$query  = "UPDATE `" . WPSC_TABLE_SUBMITED_FORM_DATA . "`
				              SET value = '".$update_value."'
				              WHERE form_id = $form_id
				              AND  log_id   = '".$wpdb->_real_escape($obj->id)."'";
			$update_result = $wpdb->query($query);
		}
		$result ['updateCnt'] = $ordersCnt ++;
	}
	$result ['result'] = true;
	$result ['updated'] = 1;
	return $result;
}

// Data required for insert and update product detials.
function data_for_insert_update($post) {
	global $result, $wpdb;
	$_POST = $post;     // Fix: PHP 5.4
        $selected_object = json_decode ( stripslashes ( $_POST ['selected'] ) );
	$edited_object = json_decode ( stripslashes ( $_POST ['edited'] ) );
	$objectArray = array ();
	
	if (is_array ( $edited_object )) {
		foreach ( $edited_object as $obj ) {
			array_push ( $objectArray, $obj );
		}
	}
	
	if (is_array ( $selected_object )) {
		foreach ( $selected_object as $obj ) {
			if (! in_array ( $obj, $objectArray ))
				array_push ( $objectArray, $obj );
		}
	}
	
	$insertCnt = 1;
	$updateCnt = 1;
	$result ['updated'] = 0;
	$result ['inserted'] = 0;
	$result ['productId'] = array ();
	$result ['8B_Email'] = array ();
	$result ['log_id'] = array ();
	
	if (is_array ( $objectArray )) {
		foreach ( $objectArray as $obj ) {
			foreach ( $obj as $obj_key => $obj_value )
				$obj->$obj_key = $wpdb->_real_escape ( $obj->$obj_key ); //escaping the input values			

			if ($obj->id == '') {
				$result ['inserted'] = 1;
				if ($obj->name == 'Enter Product Name')
					$obj->name = 'New Product Added';
				$new_product_details ['product_id'] = 0;
				$new_product_details ['title'] = $obj->name;
				
				$new_product_details ['productmeta_values'] ['sku'] = $obj->sku;
				$new_product_details ['productmeta_values'] ['table_rate_price'] ['quantity']    = array (0 => '' );
				$new_product_details ['productmeta_values'] ['table_rate_price'] ['table_price'] = array (0 => '' );
				$new_product_details ['productmeta_values'] ['custom_tax'] = '';
				$new_product_details ['productmeta_values'] ['dimensions'] ['height']      = $obj->height;
				$new_product_details ['productmeta_values'] ['dimensions'] ['height_unit'] = $obj->height_unit;
				$new_product_details ['productmeta_values'] ['dimensions'] ['width']       = $obj->width;
				$new_product_details ['productmeta_values'] ['dimensions'] ['width_unit']  = $obj->width_unit;
				$new_product_details ['productmeta_values'] ['dimensions'] ['length']      = $obj->length;
				$new_product_details ['productmeta_values'] ['dimensions'] ['length_unit'] = $obj->length_unit;
				$new_product_details ['productmeta_values'] ['merchant_notes'] = '';
				$new_product_details ['productmeta_values'] ['engraved'] = 0;
				$new_product_details ['productmeta_values'] ['can_have_uploaded_image'] = 0;
				$new_product_details ['productmeta_values'] ['external_link'] = '';
				
				$new_product_details ['price'] 					= $obj->price;
				$new_product_details ['special_price']  		= $obj->sale_price;
				$new_product_details ['quantity'] 				= $obj->quantity;				
				$new_product_details ['category']       		= array (0 => $obj->category );				
				$new_product_details ['weight']         		= $obj->weight;
				$new_product_details ['weight_unit']    		= $obj->weight_unit;
				$new_product_details ['publish']        		= $obj->publish;
				$new_product_details ['no_shipping'] 			= $obj->disregard_shipping;
				$new_product_details ['content'] 				= $obj->description;
				$new_product_details ['pnp']    				= $obj->pnp;
				$new_product_details ['additional_description'] = $obj->additional_description;
				$new_product_details ['international_pnp']      = $obj->international_pnp;
				
				$post_data = wpsc_sanitise_product_forms ( $new_product_details );
				if (isset ( $post_data ['title'] ) && $post_data ['title'] != '' && isset ( $post_data ['category'] )) {
					$product_id = wpsc_insert_product ( $post_data, true );
				}
				if ($product_id)
					$result ['result'] = true;
				array_push ( $result ['productId'], $product_id );
				
				if ($result ['result'])
					$result ['insertCnt'] = $insertCnt ++;
			} else {
				if (in_array ( $obj, $edited_object )) {
					$result ['updated'] = 1;
					$result = update ( $obj );
					
					if ($result ['result'])
						$result ['updateCnt'] = $updateCnt ++;
				}
			}
			//to save customer details.			
			//since $obj is an object array we cannot access email key by doing $obj->8B_Email			
			$email = ( array ) $obj;
			if ($email ['Old_Email_Id']) {
				$log_id = get_log_id ( $email ['Old_Email_Id'] );
				array_push ( $result ['log_id'], $log_id );
			}
		}
	}
	return $result;
}

// Batch Update.
function batchUpdate($post) {
	global $wpdb;
	$_POST = $post;     // Fix: PHP 5.4
        $ids = json_decode ( stripslashes ( $_POST ['ids'] ) );
	$actions = json_decode ( stripslashes ( $_POST ['values'] ) );

	if($_POST['activeModule'] == 'Products')
		$result = data_for_insert_update ( $_POST ); //save new products and update modified products before doing batch update.
	if ($_POST['activeModule'] == 'Customers') {
            //for customer batch Update.
            if ($result ['log_id'] && is_array ( $result ['log_id'] )){
                    $log_ids = $wpdb->_real_escape ( implode ( ',', $result ['log_id'] ) );
            }else {
                    $log_ids = $wpdb->_real_escape ( $result ['log_id'] );
            }
        }
	// create an array of ids (newly added products & modified products)
	$count = 0;
	for($i = 0; $i < count ( $ids ); $i ++) {
		if (strstr ( $ids [$i], 'ext-record' ) != '') {
			$ids [$i] = $result ['productId'] [$count];
			$count ++;
		}
	}
	$idLength = count ( $ids );
	$selected_ids = $wpdb->_real_escape ( implode ( ',', $ids ) );
	$length = count ( $actions );
	
	// Building queries
	for($i = 0; $i < $length; $i ++) {
		$actions [$i] [0] = explode ( ',', $actions [$i] [0] );
		$actions_index = 0;
		foreach ( $actions [$i] [0] as $action ) { // trimming the field names & table names
			$actions [$i] [0] [$action_index] = trim ( $actions [$i] [0] [$action_index] );
			$action_index ++;
		}
		
		$updateDetails = json_decode ( stripslashes ( $_POST['updateDetails'] ) );		
		if($_POST['activeModule'] == 'Products'){
			// getting values from POST	
			$action_name = $wpdb->_real_escape ( $updateDetails[$i]->action );
			$table_name  = $wpdb->_real_escape ( $updateDetails[$i]->tableName );
			$unit 	     = $wpdb->_real_escape ( $updateDetails[$i]->unit );

			if ($table_name == WPSC_TABLE_PRODUCTMETA){
				$column_name = $wpdb->_real_escape ( $updateDetails[$i]->updateColName );
				$meta_value  = $wpdb->_real_escape ( $updateDetails[$i]->colName );
			}elseif ($table_name == WPSC_TABLE_ITEM_CATEGORY_ASSOC){
				$is_category = $wpdb->_real_escape ( $updateDetails[$i]->colId );
			}else {
				if ($wpdb->_real_escape ( $updateDetails[$i]->updateColName ) == 'special_price'){
					$column_name = $wpdb->_real_escape ( $updateDetails[$i]->updateColName );
				}else{
					$column_name = $wpdb->_real_escape ( $updateDetails[$i]->colName );
				}
			}

			$drop_down3_value = $unit;			
			$text_cmp_value = $wpdb->_real_escape ( $updateDetails[$i]->colValue );			
		}else{
			// getting values from POST	
			$is_category = '';
			$action_name = $wpdb->_real_escape ( $actions [$i] [1] );
			$column_name = $wpdb->_real_escape ( $actions [$i] [0] [0] );
			$table_name  = $wpdb->_real_escape ( $actions [$i] [0] [1] );
			$meta_value  = $wpdb->_real_escape ( $actions [$i] [0] [2] );
			$drop_down3_value = $wpdb->_real_escape ( $actions [$i] [3] );
			$drop_down4_value = $wpdb->_real_escape ( $actions [$i] [4] ); //get the dropown value
			$country_reg = array(); //reinitializaton

			if($table_name == WPSC_TABLE_SUBMITED_FORM_DATA){
				$log_ids = $wpdb->_real_escape ( implode(',',$ids) );
			}

			if ($column_name == 'processed'){
				$text_cmp_value = $wpdb->_real_escape ( $actions [$i] [3] );
			}else if ($column_name == 'value' || $table_name == WPSC_TABLE_SUBMITED_FORM_DATA){
				if ($meta_value == 6 || $meta_value == 15){
					$country_reg [] = (string)$drop_down3_value;
					($drop_down4_value !== '') ? $country_reg [] = (string)$drop_down4_value : ''; //get the dropown value
					$text_cmp_value = serialize ( $country_reg );
				}else{
					$text_cmp_value =  $wpdb->_real_escape($actions [$i] [2]);
				}
			}else{
				$text_cmp_value = $wpdb->_real_escape ( $actions [$i] [2] );
			}
		}		
		
		switch ($action_name) {
			case 'SET_TO' :
				if ($is_category) {
					$query = "DELETE FROM " . WPSC_TABLE_ITEM_CATEGORY_ASSOC . " WHERE product_id in (" . $selected_ids . ")";
					$result = $wpdb->query ( $query );
					
					$sub_query = array ();
					for($j = 0; $j < count ( $ids ); $j ++)
						array_push ( $sub_query, "( ''," . $ids [$j] . "," . $text_cmp_value . ")" );
					
					$sub_query = implode ( ',', $sub_query );
					$query = "INSERT INTO " . WPSC_TABLE_ITEM_CATEGORY_ASSOC . " VALUES " . $sub_query;
				}

				elseif ($column_name == 'price' && $table_name == WPSC_TABLE_PRODUCT_LIST) {
					$update_value [] = 'special_price = ' . $text_cmp_value . '- ( price - special_price )';
					$update_value [] = $column_name . ' = \'' . $text_cmp_value . '\'';
				}

				elseif ($column_name == 'special_price') {
					$update_value = 'special_price = price - ' . $text_cmp_value;
					$table_name = WPSC_TABLE_PRODUCT_LIST;
				} else{
					$update_value [] = $column_name . ' = \'' . $text_cmp_value . '\''; //is array for weight					
				}
				break;
			
			case 'PREPEND' :
				$update_value = $column_name . ' = concat(\'' . $text_cmp_value . '\',' . $column_name . ')';
				break;
			
			case 'APPEND' :
				$update_value = $column_name . ' = concat(' . $column_name . ',\'' . $text_cmp_value . '\')';
				break;
			
			case 'INCREASE_BY_NUMBER' :
				
				if ($column_name == 'special_price') {
					$update_value = 'special_price = special_price - ' . $text_cmp_value;
					$table_name = WPSC_TABLE_PRODUCT_LIST;
				} elseif ($column_name == 'price' && $table_name == WPSC_TABLE_PRODUCT_LIST) {
					$update_value [] = $column_name . ' = ' . $column_name . '+' . $text_cmp_value;
					$update_value [] = 'special_price = special_price + ' . $text_cmp_value;					
				} 

				else
					$update_value [] = $column_name . ' = ' . $column_name . '+' . $text_cmp_value;
				break;
			
			case 'DECREASE_BY_NUMBER' :
				
				if ($column_name == 'special_price') {
					$update_value = 'special_price = special_price + ' . $text_cmp_value;
					$table_name = WPSC_TABLE_PRODUCT_LIST;
				} 

				elseif ($column_name == 'price' && $table_name == WPSC_TABLE_PRODUCT_LIST) {
					$update_value [] = $column_name . ' = ' . $column_name . '-' . $text_cmp_value;
					$update_value [] = 'special_price = special_price + ' . $text_cmp_value;
				} 

				else
					$update_value [] = $column_name . ' = ' . $column_name . '-' . $text_cmp_value;
				break;
			
			case 'INCREASE_BY_%' :
				
				if ($column_name == 'special_price') {
					$update_value = 'special_price = price - ((price-special_price)+((price-special_price) * ' . ($text_cmp_value / 100) . '))';
					$table_name = WPSC_TABLE_PRODUCT_LIST;
				} 

				elseif ($column_name == 'price' && $table_name == WPSC_TABLE_PRODUCT_LIST) {
					$update_value [] = 'special_price = (' . $column_name . '+' . ($column_name . '*' . $text_cmp_value / 100) . ') - (price-special_price)';
					$update_value [] = $column_name . ' = ' . $column_name . '+' . ($column_name . '*' . $text_cmp_value / 100);
				} 

				else
					$update_value [] = $column_name . ' = ' . $column_name . '+' . ($column_name . '*' . $text_cmp_value / 100);
				break;
			
			case 'DECREASE_BY_%' :
				
				if ($column_name == 'special_price') {
					$update_value = 'special_price = price - ((price-special_price)-((price-special_price) * ' . ($text_cmp_value / 100) . '))';
					$table_name = WPSC_TABLE_PRODUCT_LIST;
				} 

				elseif ($column_name == 'price' && $table_name == WPSC_TABLE_PRODUCT_LIST) {
					$update_value [] = 'special_price = (' . $column_name . '-' . ($column_name . '*' . $text_cmp_value / 100) . ') - (price-special_price)';
					$update_value [] = $column_name . ' = ' . $column_name . '-' . ($column_name . '*' . $text_cmp_value / 100);
				} 

				else
					$update_value [] = $column_name . ' = ' . $column_name . '-' . ($column_name . '*' . $text_cmp_value / 100);
				break;
			
			case 'YES' :
				
				$update_value = $column_name . ' = 1';
				break;
			
			case 'NO' :
				
				$update_value = $column_name . ' = 0';
				break;
			
			case 'ADD_TO' :
				$sub_query = array ();
				for($j = 0; $j < count ( $ids ); $j ++) {
					$sub_query [] = "( ''," . $wpdb->_real_escape ( $ids [$j] ) . "," . $text_cmp_value . ")";
				}
				$sub_query = implode ( ',', $sub_query );
				$query = "INSERT INTO " . WPSC_TABLE_ITEM_CATEGORY_ASSOC . " VALUES " . $sub_query;
				break;
			
			case 'REMOVE_FROM' :
				$query = "DELETE FROM " . WPSC_TABLE_ITEM_CATEGORY_ASSOC . " WHERE product_id in ($selected_ids)
			              AND category_id = " . $text_cmp_value . "";
				break;
		}
		//update query
			if (!$is_category){
			//EOF Overwriting the update values
			if ($action_name == ('SET_TO' || 'INCREASE_BY_NUMBER' || 'DECREASE_BY_NUMBER' || ' INCREASE_BY_%' || ' DECREASE_BY_%')) {
				if ($column_name == 'weight') {
					if ($drop_down3_value != '') {
						$update_value [] = "weight_unit = '" . $drop_down3_value . "'";
					}
				}
				if (is_array ( $update_value ))
				$update_value = implode ( ',', $update_value );
			} //EOF Overwriting the update values
			
			$query = 'UPDATE ' . $table_name . ' SET ' . $update_value;
			$update_value = '';			
			
			if ($column_name == 'meta_value') {
				if ($meta_value == 'unpublish_oos')
					$query .= ' WHERE product_id in (' . $selected_ids . ') AND meta_key =\'unpublish_oos\'';
				elseif ($meta_value == 'sku')
					$query .= ' WHERE product_id in (' . $selected_ids . ') AND meta_key = \'sku\'';
				elseif ($meta_value == 'height' || $meta_value == 'Width' || $meta_value == 'Length'){
					$query .= ' WHERE product_id in (' . $selected_ids . ') AND meta_key = \'dimensions\'';
				}					
			} elseif ( ($column_name == 'price' || $column_name == 'weight') && $table_name == WPSC_TABLE_VARIATION_PROPERTIES) {
				$query .= ' WHERE product_id in (' . $selected_ids . ')';
			} elseif ($column_name == 'value' || $table_name == '{$wpdb->prefix}wpsc_submited_form_data') {
				$query .= ' WHERE form_id = ' . $meta_value . ' AND log_id in (' . $log_ids . ')';
			} else
				$query .= ' WHERE `id` in (' . $selected_ids . ')';
		}
		$result = $wpdb->query ( $query );
		if ($result)
			$updated_rows_cnt = $idLength; // to give count even if zero updated rows.		
	}	
	return $updated_rows_cnt;
}

if (isset ( $_POST ['cmd'] ) && $_POST ['cmd'] == 'batchUpdate') {
	$updated_rows_cnt = batchUpdate ( $_POST );	
	if ($updated_rows_cnt >= 1)
		$encoded ['msg'] = "<b>" . $updated_rows_cnt . "</b> " . __('Records Updated Successfully','smart-manager');
	else
		$encoded ['msg'] = __("No Records Updated",'smart-manager');
        // ob_clean();

	while(ob_get_contents()) {
        ob_clean();
    }

	echo json_encode ( $encoded );

	exit;
}

function customers_query($search_text = '', $region_exists, $country_region) {
	//customer's query for pro
	global $wpdb;	
	$query = "SELECT log_id AS id,user_details,unique_names,Last_Order_Date,Total_Purchased,Last_Order_Amt $country_region
                            FROM   (SELECT ord_emailid.log_id,
                                   user_details,unique_names, 
                                   DATE_FORMAT(FROM_UNIXTIME(date), '%b %e %Y') AS Last_Order_Date,
                                   SUM( totalprice ) Total_Purchased,
                                   totalprice AS Last_Order_Amt $country_region
                                   FROM    (SELECT log_id, value email
                                           FROM " . WPSC_TABLE_SUBMITED_FORM_DATA . " wwsfd1
                                           WHERE form_id =( SELECT id
															  FROM ". WPSC_TABLE_CHECKOUT_FORMS ."
											WHERE unique_name =  'billingemail')) AS ord_emailid
											
                                     LEFT JOIN
									( SELECT log_id, 
									GROUP_CONCAT( wwsfd2.value ORDER BY form_id SEPARATOR  '#' ) user_details, 
									GROUP_CONCAT( wwcf.unique_name ORDER BY wwcf.id SEPARATOR  '#' ) unique_names					
									FROM ". WPSC_TABLE_SUBMITED_FORM_DATA ." as wwsfd2 
					
									LEFT JOIN ". WPSC_TABLE_CHECKOUT_FORMS ." wwcf ON ( wwcf.id = wwsfd2.form_id) 					
									WHERE unique_name
									IN (
										'billingfirstname',  
										'billinglastname',  
										'billingaddress',  
										'billingcity',  
										'billingstate',
										'billingcountry',
										'billingpostcode',
										'billingemail',
										'billingphone'
									) 
									GROUP BY log_id
									) AS ord_all_user_details ON ( ord_emailid.log_id = ord_all_user_details.log_id ) 
									LEFT JOIN
                                           (SELECT id, date, totalprice
                                           FROM " . WPSC_TABLE_PURCHASE_LOGS . " wwpl
                                           ORDER BY date DESC
                                           ) AS purchlog_info
                                           ON ( purchlog_info.id = ord_emailid.log_id )";                                           

	if ($region_exists == true){
		$query .=   "LEFT JOIN
		                            (SELECT  log_id,form_id,country," . WPSC_TABLE_REGION_TAX . ".name as region
		                            FROM
		                            (SELECT log_id,form_id,country,CAST(CAST(SUBSTRING_INDEX(value,'\"',-2) AS signed)AS char) AS region_id
		                            FROM " . WPSC_TABLE_SUBMITED_FORM_DATA . " LEFT JOIN " . WPSC_TABLE_CURRENCY_LIST . " wwcl 
		                            ON (RIGHT(SUBSTRING_INDEX(value,'\"',2),2) = isocode) WHERE form_id = (SELECT id from ". WPSC_TABLE_CHECKOUT_FORMS ." WHERE unique_name = 'billingcountry')
		                            ) AS country_info
		                            LEFT OUTER JOIN " . WPSC_TABLE_REGION_TAX . " ON (country_info.region_id = " . WPSC_TABLE_REGION_TAX . ".id)) AS user_country_regions
		                            ON ( ord_emailid.log_id = user_country_regions.log_id)";
	}

	$query .=   "GROUP BY email ) AS customers_info \n";
		
	if ($search_text) {
		$search_text = $wpdb->_real_escape ( $_POST ['searchText'] );
		$query .= "WHERE user_details like '%$search_text%'
                               OR Last_Order_Date like '%$search_text%'
                               OR Total_Purchased like '$search_text%'
                               OR Last_Order_Amt  like '$search_text%'
                               OR country   LIKE '$search_text%'
                               OR region   LIKE '$search_text%'";
	}
	return $query;
}

// Converting Order Status code to String for displaying it in CSV file
if (! function_exists( 'get_order_status_string' )) {
	function get_order_status_string ( $status_code ) {
		switch ( intval ( $status_code ) ) {
			
			case 1:
				return __('Incomplete Sale','smart-manager');
				break;
				
			case 2:
				return __('Order Received','smart-manager');
				break;
				
			case 3:
				return __('Accepted Payment','smart-manager');
				break;
				
			case 4:
				return __('Job Dispatched','smart-manager');
				break;
				
			case 5:
				return __('Closed Order','smart-manager');
				break;
				
			default:
				return __('Payment Declined','smart-manager');
				break;

		}
	}
}

// For PHP version lesser than 5.3.0
if (!function_exists('str_getcsv')) {
    function str_getcsv($input, $delimiter = ",", $enclosure = '"', $escape = "\\") {
        $fiveMBs = 5 * 1024 * 1024;
        $fp = fopen("php://temp/maxmemory:$fiveMBs", 'r+');
        fputs($fp, $input);
        rewind($fp);

        $data = fgetcsv($fp, 1000, $delimiter, $enclosure); //  $escape only got added in 5.3.0

        fclose($fp);
        return $data;
    }
} 

//Function to export CSV file
if (! function_exists( 'export_csv_wpsc_37' )) {
	function export_csv_wpsc_37 ( $active_module, $columns_header, $data ) {

		foreach ( $columns_header as $key => $value ) {
			$getfield .= $value . ',';
		}

		$fields = substr_replace($getfield, '', -1);
		$each_field = array_keys( $columns_header );
		
		$csv_file_name = $active_module . '_' . gmdate('d-M-Y_H:i:s') . ".csv";
		
		foreach( (array) $data as $row ){
			for($i = 0; $i < count ( $columns_header ); $i++){
				if($i == 0) $fields .= "\n"; 
				( $each_field[$i] == 'order_status' ) ? $row[$each_field[$i]] = get_order_status_string ( $row[$each_field[$i]] ) : '';
				$array = str_replace(array("\n", "\n\r", "\r\n", "\r"), "\t", $row[$each_field[$i]]); 
				$array = str_getcsv ( $array , ",", "\"" , "\\"); 
				$fields .= implode( ' ', $array ) . ',';  
			}			
			$fields = substr_replace($fields, '', -1); 
		}

		header("Content-type: text/x-csv; charset=UTF-8"); 
		header("Content-Transfer-Encoding: binary");
		header("Content-Disposition: attachment; filename=".$csv_file_name); 
		header("Pragma: no-cache");
		header("Expires: 0");

		echo $fields;
		
		exit;
	}
}
?>