<?php

if (!function_exists('find_wp_load_path')) {
    function find_wp_load_path() { // function to find the wordpress root directory path
        $dir = dirname(__FILE__);
        do {
            if( file_exists($dir."/wp-load.php") ) {
                return $dir;
            }
        } while( $dir = realpath("$dir/..") );
        return null;
    }    
}


// WOO 2.1 compatibility
if ((!empty($_POST['SM_IS_WOO21']) && $_POST['SM_IS_WOO21'] == "true") || (!empty($_POST['SM_IS_WOO22']) && $_POST['SM_IS_WOO22'] == "true") ) {
    include_once (WP_PLUGIN_DIR . '/woocommerce/includes/class-wc-product-variable.php'); // for handling variable parent price
    include_once (WP_PLUGIN_DIR . '/woocommerce/includes/abstracts/abstract-wc-product.php'); // for updating stock status
}

if ( ! defined('ABSPATH') ) {
    include_once (find_wp_load_path()  . '/wp-load.php');
}


global $wp_version;

if (version_compare ( $wp_version, '4.0', '>=' )) {
    global $locale;
    load_textdomain( 'smart-manager', WP_PLUGIN_DIR . '/' . dirname( dirname(plugin_basename( __FILE__ ))) . '/languages/smart-manager-' . $locale . '.mo' );
} else {
    load_textdomain( 'smart-manager', WP_PLUGIN_DIR . '/' . dirname(dirname(plugin_basename( __FILE__ ))) . '/languages/smart-manager-' . WPLANG . '.mo' );
}

include_once (WP_PLUGIN_DIR . '/woocommerce/woocommerce.php');


//Function to update the count of the attributes
function update_attribute_counts($attribute_name) {
    global $wpdb;

    $taxonomy = "pa_" . $attribute_name;

    $query_taxonomy_id = "SELECT term_taxonomy_id
                                FROM {$wpdb->prefix}term_taxonomy
                                WHERE taxonomy LIKE '$taxonomy'";
    $result_taxonomy_id = $wpdb->get_col($query_taxonomy_id);


    $query_count = "SELECT term_taxonomy_id, count(*) as count FROM {$wpdb->prefix}term_relationships
                    WHERE term_taxonomy_id IN (". implode(',',$result_taxonomy_id) .")
                    AND object_id IN (SELECT ID FROM {$wpdb->prefix}posts
                                        WHERE post_status IN ('publish', 'draft')
                                            AND post_type IN ('product','product_variation'))
                   GROUP BY term_taxonomy_id";
    $result_count = $wpdb->get_results($query_count, 'ARRAY_A');

    for ($i=0;$i<sizeof($result_count);$i++) {
        $taxonomy_count [$result_count[$i]['term_taxonomy_id']] = $result_count[$i]['count'];
    }

    for ($i=0;$i<sizeof($result_taxonomy_id);$i++) {
        if(isset($taxonomy_count[$result_taxonomy_id[$i]])) {
            $count = $taxonomy_count[$result_taxonomy_id[$i]];
        }
        else {
            $count = 0;
        }

        $taxonomy_id = $result_taxonomy_id[$i];
        $query = "UPDATE {$wpdb->prefix}term_taxonomy SET count = $count
                    WHERE term_taxonomy_id = $taxonomy_id";
        $result = $wpdb->query($query);

    }
}

// Function to handle the encoding into UTF - 8 format
if ( !function_exists( 'encoding_utf_8' ) ) {
    function encoding_utf_8($post) {
	$_POST = $post;     // Fix: PHP 5.4
        //For encoding the string in UTF-8 Format
//        $charset = "EUC-JP, ASCII, UTF-8, ISO-8859-1, JIS, SJIS";
        $charset = ( get_bloginfo('charset') === 'UTF-8' ) ? null : get_bloginfo('charset');
        
        if (!(is_null($charset))) {
            $_POST['edited'] = mb_convert_encoding(stripslashes($_POST['edited']),"UTF-8",$charset);
            $_POST['values'] = mb_convert_encoding(stripslashes($_POST['values']),"UTF-8",$charset);
            $_POST['selected'] = mb_convert_encoding(stripslashes($_POST['selected']),"UTF-8",$charset);
            $_POST['updateDetails'] = mb_convert_encoding(stripslashes($_POST['updateDetails']),"UTF-8",$charset);
        }
        else {
            $_POST['edited'] = stripslashes($_POST['edited']);
            $_POST['values'] = stripslashes($_POST['values']);
            $_POST['selected'] = stripslashes($_POST['selected']);
            $_POST['updateDetails'] = stripslashes($_POST['updateDetails']);
        }
        
    }
}

//Print Invoice Function
    function smart_manager_print_logo() {
      if (get_option('smart_manager_company_logo') != '') {
        return '<img src="' . get_option('smart_manager_company_logo') . '"/>';
      }
    }

//Print Box
function sm_woo_get_packing_slip( $purchase_ids, $purchase_id_arr ) {
       
    if ( !empty( $purchase_ids ) && !empty( $purchase_id_arr ) ) {
    
        ?>
        <style type="text/css">
            body {
                font-family:"Helvetica Neue", Helvetica, Arial, Verdana, sans-serif;
            }

            h1 span {
                font-size:0.75em;
            }

            h2 {
                color: #333;
            }
            .no-page-break {
                page-break-after: avoid;
            }

            #wrapper {
                margin:0 auto;
                width:95%;
                page-break-after: always;
            }

            #wrapper_last {
                margin:0 auto;
                width:95%;
                page-break-after: avoid;
            }

            .address{
                width:98%;
                border-top:1px;
                border-right:1px;
                margin:1em auto;
                border-collapse:collapse;
            }
            
            .address_border{
                border-bottom:1px;
                border-left:1px ;
                padding:.2em 1em;
                text-align:left;
            }
           
            table {
                width:98%;
                border-top:1px solid #e5eff8;
                border-right:1px solid #e5eff8;
                margin:1em auto;
                border-collapse:collapse;
                font-size:10pt;
            }
            td {
                border-bottom:1px solid #e5eff8;
                border-left:1px solid #e5eff8;
                padding:.3em 1em;
                text-align:center;
            }

            tr.odd td,
            tr.odd .column1 {
                background:#f4f9fe url(background.gif) no-repeat;
            }
            .column1 {
                background:#f4f9fe;
            }

            thead th {
                background:#f4f9fe;
                text-align:center;
                font:bold 1.2em/2em "Century Gothic","Trebuchet MS",Arial,Helvetica,sans-serif;
            }
            .datagrid {

                position: relative;
                top:-30pt;
            }
            .producthead{ 
                text-align: left;
            }
            .pricehead{
                text-align: right;
            }
            .sm_address_div{
                position: relative;
                left:28pt;
            }
            .sm_email_span{
                position: relative;
                left:10pt;
            }

        </style>
        <?php 
        $counter = 0;
        foreach ($purchase_id_arr as $purchase_id_value){
            $order = new WC_Order($purchase_id_value);
            $date_format = get_option('date_format');


            if (is_plugin_active ( 'woocommerce-sequential-order-numbers-pro/woocommerce-sequential-order-numbers.php' )) {
                $purchase_display_id = (isset($order->order_custom_fields['_order_number_formatted'][0])) ? $order->order_custom_fields['_order_number_formatted'][0] : $purchase_id_value;
            } else {
                $purchase_display_id = $purchase_id_value;
            }

            

            $counter++;
            if ( count( $purchase_id_arr ) == $counter ) {
                echo '<div id="wrapper_last">';
            } else {
                echo '<div id="wrapper">';
            }
            echo smart_manager_print_logo();
            echo '<div style="margin-top:-0.8em;">';
            if (get_option('smart_manager_company_logo') == '') {
                echo '<h4 style="font:bold 1.2em/2em "Century Gothic","Trebuchet MS",Arial,Helvetica,sans-serif;
                        position:relative; 12pt;">&nbsp; '.get_bloginfo( 'name' ).'</h4>';
            }
            echo '</br> <table class="address" style="position:relative; top:-22pt; left:-35pt;">';
            echo '<tr><td class="address_border" colspan="2" valign="top" width="50%"><span style="position:relative; left:27pt; top:10pt;">
                    <b>Order # '.$purchase_display_id.' - '.date($date_format, strtotime($order->order_date)).'</b></span><br/></td></tr>';
            echo '<tr><td class="address_border" width="35%" align="center"><br/><div class="sm_address_div">';
            
            $formatted_billing_address = $order->get_formatted_billing_address();
            if( $formatted_billing_address != '' ) {
                echo '<b>'.__('Billing Address', 'smart-manager').'</b><p>';
                echo $formatted_billing_address; 
                echo '</p></td>';
            }
            
            $formatted_shipping_address = $order->get_formatted_shipping_address();
            if( $formatted_shipping_address != '' ) {
                echo '<td class="address_border" width="30%"><br/><div style="position:relative; top:3pt;"><b>'.__('Shipping Address', 'smart-manager').'</b><p>';
                echo $formatted_shipping_address;
                echo '</p></div></td>';
            }
                        
            echo '</tr>';
            echo '<tr><td colspan="2" class="address_border"><span class="sm_email_span"><table class="address"><tr><td colspan="2" class="address_border" >
                    <b>'.__('Email id', 'smart-manager').':</b> '.$order->billing_email.'</td></tr>
                    <tr><td class="address_border"><b>'.__('Tel', 'smart-manager').' :</b> '.$order->billing_phone.'</td></tr></table> </span></td></tr>';
            echo '</table>';
            echo '<div class="datagrid"><table><tr class="column1">
                    <td class="producthead">'.__('Product', 'smart-manager').'</td><td>'.__('SKU', 'smart-manager').'</td>
                    <td>'.__('Quantity', 'smart-manager').'</td><td class="pricehead">'.__('Price', 'smart-manager').'</td></tr>';
                    
            $total_order = 0;

            foreach($order->get_items() as $item) {
                $_product = $order->get_product_from_item( $item );
                $sku = $variation = '';
                $sku = $_product->get_sku();
                $formatted_variation = woocommerce_get_formatted_variation( $_product->variation_data, true );
                $variation = ( !empty( $formatted_variation ) ) ? ' (' . $formatted_variation . ')' : '';
                $item_total = $_product->get_price() * $item['item_meta']['_qty'][0];
                $total_order += $item_total;
                echo '<tr><td class="producthead">';
                echo $item['name'] . $variation;
                echo '</td><td>'.$sku.'</td><td>';
                echo $item['item_meta']['_qty'][0];
                echo '</td><td class="pricehead">';
                echo woocommerce_price($item_total);
                echo '</td></tr>';    
            }

            echo '<tr><td colspan="2" rowspan="5" class="address_border" valign="top"><br/>
                    <i>'.(($order->customer_note != '')? __('Order Notes', 'smart-manager').' : ' .$order->customer_note:'').'</i></td><td style="text-align:right;" class="address_border" valign="top">
                    <b>Subtotal </b></td><td class="pricehead">'.$order->get_subtotal_to_display().'</td></tr>';
            echo '<tr><td style="text-align:right;" class="address_border"><b>'.__('Shipping', 'smart-manager').' </b></td><td class="pricehead">'.$order->get_shipping_to_display().'</td></tr>';
            
            if ($order->cart_discount > 0) {
                echo '<tr><td style="text-align:right;" class="address_border">'.__('Cart Discount', 'smart-manager').'</td><td style="text-align:right;">';
                echo woocommerce_price($order->cart_discount); 
                echo '</td></tr>';
            } 
            
            if ($order->order_discount > 0) {
                echo '<tr><td style="text-align:right;" class="address_border"><b>'.__('Order Discount', 'smart-manager').' </b></td>';
                echo '<td class="pricehead">'.woocommerce_price($order->order_discount).'</td></tr>';
            }

            echo '<tr><td style="text-align:right;" class="address_border"><b>'.__('Tax', 'smart-manager').' </b></td><td class="pricehead">'.woocommerce_price($order->get_total_tax()).'</td></tr>';
            echo '<tr><td class="column1" style="text-align:right;"><b>'.__('Total', 'smart-manager').' </b></td><td class="column1" style="text-align:right;">'.woocommerce_price($order->order_total).' -via '.$order->payment_method_title.'</td></tr>';
            echo '</table></div></div></div>';
        }
    }
    exit;
}

function get_all_results() {
	global $wpdb;
        
        //Code to get the ids of all the products whose post_status is thrash
        $query_trash = "SELECT ID FROM {$wpdb->prefix}posts 
                        WHERE post_status = 'trash'
                            AND post_type IN ('product')";
        $results_trash = $wpdb->get_col( $query_trash );
        $rows_trash = $wpdb->num_rows;
        
        $query_deleted = "SELECT distinct products.post_parent 
                            FROM {$wpdb->prefix}posts as products 
                            WHERE NOT EXISTS (SELECT * FROM {$wpdb->prefix}posts WHERE ID = products.post_parent) 
                              AND products.post_parent > 0 
                              AND products.post_type = 'product_variation'";
        $results_deleted = $wpdb->get_col( $query_deleted );
        $rows_deleted = $wpdb->num_rows;
        
        for ($i=sizeof($results_trash),$j=0;$j<sizeof($results_deleted);$i++,$j++ ) {
            $results_trash[$i] = $results_deleted[$j];
        }
        
        
        if ($rows_trash > 0 || $rows_deleted > 0) {
            $trash_id = " AND post_parent NOT IN (" .implode(",",$results_trash). ")";
        }
        else {
            $trash_id = "";
        }
        
	$select_results = $wpdb->get_results( "SELECT ID FROM {$wpdb->prefix}posts WHERE post_type IN ('product','product_variation') AND post_status IN ('publish', 'draft') $trash_id" );
	return $select_results;
}

$sql_results = get_all_results();

function get_all_ids( $sql_results ) {
	$all_ids = array();
	if ( is_foreachable( $sql_results ) ) {
		foreach ( $sql_results as $obj ) {
			if( is_foreachable( $obj ) ) {
				foreach ( $obj as $key => $value) {
					$all_ids [] = $value;
				}
			}	
		}
		return  implode ( ',', $all_ids );
	} 
	return '';
}



// Function to update price when there is any change in regular or sale price
function update_price_meta( $ids = '' ) {
    global $wpdb;

    if (!empty($ids)) {
        $where_price = ( !empty( $ids ) ) ? "post_id IN ($ids) AND " : '';
    
        $query = "SELECT post_id,
                    GROUP_CONCAT( meta_key ORDER BY meta_id SEPARATOR '##' ) AS meta_keys, 
                    GROUP_CONCAT( meta_value ORDER BY meta_id SEPARATOR '##' ) AS meta_values 
                FROM {$wpdb->prefix}postmeta 
                WHERE $where_price meta_Key IN ( '_regular_price', '_sale_price', '_sale_price_dates_from', '_sale_price_dates_to' ) 
                GROUP BY post_id";
        $results = $wpdb->get_results ( $query, 'ARRAY_A' );
        
        foreach ( $results as $result ) {
            $meta_keys = explode( '##', $result['meta_keys'] );
            $meta_values = explode( '##', $result['meta_values'] );

            if ( count( $meta_keys ) == count( $meta_values ) ) {
                $keys_values = array_combine( $meta_keys, $meta_values );

                $from_date = (isset($keys_values['_sale_price_dates_from'])) ? $keys_values['_sale_price_dates_from'] : '';
                $to_date = (isset($keys_values['_sale_price_dates_to'])) ? $keys_values['_sale_price_dates_to'] : '';

                $price = get_price( trim($keys_values['_regular_price']), trim($keys_values['_sale_price']), $from_date, $to_date);
                
                $price = trim($price); // For handling when both price and sales price are null

                if(!empty($price)) {
                    $update_query = "UPDATE {$wpdb->prefix}postmeta SET meta_value = $price WHERE meta_key = '_price' AND post_id = " . $result['post_id'];    
                }
                else {
                    $update_query = "UPDATE {$wpdb->prefix}postmeta SET meta_value = '' WHERE meta_key = '_price' AND post_id = " . $result['post_id'];       
                }   

                $wpdb->query( $update_query );
            }
        }    
    }
    
}

// Function for getting all attributes assigned to a product
function process_meta_value( $id ){
	global $wpdb;
	$select_result = $wpdb->get_results("Select pm.meta_value from {$wpdb->prefix}postmeta as pm where pm.meta_key = '_product_attributes' and pm.post_id = $id ");
        $meta_data = unserialize($select_result[0]->meta_value);
        return $meta_data;
}

// Function for getting all terms assigned to the attributes of a product
function get_attribute_terms( $attribute_name, $id = '' ){
	global $wpdb;
	
	if( $id != '' ){
		$query = "SELECT term_taxonomy.term_taxonomy_id from {$wpdb->prefix}terms as term  JOIN {$wpdb->prefix}term_taxonomy as term_taxonomy ON (term.term_id = term_taxonomy.term_id) join {$wpdb->prefix}term_relationships as tr on (tr.term_taxonomy_id = term_taxonomy.term_taxonomy_id)  WHERE term_taxonomy.taxonomy = concat('pa_','$attribute_name') and tr.object_id = $id ";
	} else {
		$query = "SELECT term_taxonomy.term_taxonomy_id from {$wpdb->prefix}terms as term  JOIN {$wpdb->prefix}term_taxonomy as term_taxonomy ON (term.term_id = term_taxonomy.term_id) WHERE term_taxonomy.taxonomy = concat('pa_','$attribute_name') ";
	}
	$result = $wpdb->get_results( $query );
	$term_taxonomy_ids = array();
	for( $i = 0; $i < count( $result ); $i++ ){
		$term_taxonomy_ids[] = $result[$i]->term_taxonomy_id ;
	}
	return $term_taxonomy_ids;
}

// Function for assigning terms to a product
function insert_post_meta( $array_diff_val, $id ) {
	global $wpdb;
        $stored_value = array();
	foreach( $array_diff_val as $k => $v ) {
		$internal_array = array( $id, $v );
		$stored_value[] = $internal_array;
	}
	
	$insert_query = "INSERT into {$wpdb->prefix}term_relationships (object_id,term_taxonomy_id) VALUES ";
	for( $k = 0; $k < count( $stored_value ); $k++ ) {
		$insert_query .=  "(" . $stored_value[$k][0]  . ",";
		$insert_query .= $stored_value[$k][1];
		$insert_query .= ")" . ",";
	}
	$insert_query_str = substr( $insert_query, 0, -1 );
	$insert_result = $wpdb->query( $insert_query_str );
	return $insert_result;
}

// Function returns attributes going to be assigned to a product
function meta_value_array( $attribute_name, $id, $action_name, $custom_terms, $attribute_names, $meta_data ) {
	global $wpdb;
	
	if( $custom_terms != '' ) {
		$custom_value = $custom_terms;
	}
        
	if( $action_name == 'AttributeAdd' ) {
		if( empty( $meta_data ) ) {
			$position = "0";
		} else {
			$position = array();
			foreach($meta_data as $array){
				
				$position_array[] = $array['position'];
			}
			$position = max($position_array);
			$position = ++$position;
		}
	}

	if($id != ''){
		$terms = wp_get_object_terms( $id, 'product_type', array('fields' => 'names') );
	}

	$pa_attribute_name = "pa_" . $attribute_name;
	$meta_value = array();
	if ( in_array( $attribute_name, $attribute_names ) ) {
		$meta_value[$pa_attribute_name] = array(
												"name" 			=> "$pa_attribute_name",
												"value"			=> "",
												"position" 		=> "$position",
												"is_visible" 	=> '1',
                                                // "is_variation"  => ( sanitize_title($terms[0]) == 'variable' ) ? '1' : '0',
												"is_variation" 	=> '0',
												"is_taxonomy"	=> '1'
											);
	} else {
		$meta_value[$attribute_name] = array(
											    "name" 			=> "$attribute_name",
												"value"			=> "$custom_value",
												"position" 		=> "$position",
												"is_visible" 	=> '1',
                                                // "is_variation"  => ( sanitize_title($terms[0]) == 'variable' ) ? '1' : '0',
												"is_variation" 	=> '0',
												"is_taxonomy"	=> '0'
		
											);
	}
	return $meta_value;
}

// Function for updating list of attributes assigned to a product
function sm_post_meta_update($meta_data,$id){
	global $wpdb;
	$query = "SELECT pm.meta_value FROM {$wpdb->prefix}postmeta as pm
			WHERE pm.meta_key LIKE '_product_attributes' and pm.post_id = $id ";
	$result = $wpdb-> get_results($query, 'ARRAY_A');
	$rows_check = $wpdb->num_rows;

	if ($rows_check > 0) {
		$query = "UPDATE {$wpdb->prefix}postmeta as pm SET pm.meta_value = '$meta_data' WHERE pm.meta_key = '_product_attributes' and pm.post_id = $id ";
	}

	else {
		$query = "INSERT INTO {$wpdb->prefix}postmeta (post_id ,meta_key ,meta_value)  VALUES ('$id', '_product_attributes', '$meta_data')";
	}

	$update_result = $wpdb->query($query);
        // IMPORTANT: CHECK WHERE ALL ITS RETRN VALUE IS GETTING USED
//	$update_result = update_post_meta( $id, '_product_attributes', "$meta_data" );
	return $update_result;
}

// Function to assign term/s of an attribute to a product
function process_taxonomy_id( $term_taxonomy_id, $attribute_name, $parent_id ) {
	global $wpdb;
	
	
	$get_all_terms = get_attribute_terms( $attribute_name, '' );
	$get_terms_by_parent_id = get_attribute_terms( $attribute_name, $parent_id );



        if( $term_taxonomy_id == 'all' ) {
		$term_taxonomy_ids = $get_all_terms;
        }
        else if( empty( $get_terms_by_parent_id ) == false) {
            $term_taxonomy_id_new = array( $term_taxonomy_id );
            $term_taxonomy_ids = array_merge( $get_terms_by_parent_id, $term_taxonomy_id_new );
        }
        else {
                $term_taxonomy_ids = array( $term_taxonomy_id );
        }

        $query = "SELECT terms.name, wt.taxonomy FROM {$wpdb->prefix}term_taxonomy AS wt
                        JOIN {$wpdb->prefix}terms AS terms ON (wt.term_id = terms.term_id)
                            WHERE wt.term_taxonomy_id IN (". implode(",",$term_taxonomy_ids) .")";
        $result = $wpdb->get_results ($query , 'ARRAY_A');

        for ($i=0;$i<sizeof($result);$i++) {
            $term_name[$i] = $result [$i]['name'];
        }

	// $inserted = insert_post_meta( $term_taxonomy_ids, $parent_id );

	$inserted = wp_set_object_terms($parent_id, $term_name, $result[0]['taxonomy']);

	return sizeof($inserted);
}

// Function to reflect attributes to all child / product variation, if new attribute is added to parent
function insert_child_metakey( $parent_id, $attribute_name, $meta_data ) {
	global $wpdb;
	
	$meta_key = "pa_" . $attribute_name;
	foreach( $meta_data as $key => $value ){
		$isvariation = $value['is_variation'];
		if( $meta_key == $key ){
			$post_meta_key = "attribute_" . $meta_key;
		} elseif( $attribute_name == $key ) {
			$post_meta_key = "attribute_" . $attribute_name; 
		}
	}
	
	if( $isvariation == 1 ){
		$select_results =  $wpdb->get_col("select p.ID as ID from {$wpdb->prefix}posts as p where p.post_parent = $parent_id ");
		foreach( $select_results as $select_result ){
			update_post_meta( $select_result, $post_meta_key, '' );
		}
	}
}

// Function to add new attribute/s & all its term/s to a product
function add_update_post_meta( $meta_data, $parent_id, $attribute_name, $action, $term_taxonomy_id, $attribute_names ) {
        
        global $wpdb;
    
        $attribute_post_array = meta_value_array( $attribute_name, $parent_id, $action, '', $attribute_names, $meta_data );
	
        
	foreach( $attribute_post_array as $key => $value ) {
		$meta_data[$key] = $value;
	}
	$update = sm_post_meta_update( serialize( $meta_data ), $parent_id );

	if( $update == 1 ) {
                
//                _update_post_term_count($term_taxonomy_id, $attribute_name);
            
		$process_taxonomy_id = process_taxonomy_id( $term_taxonomy_id, $attribute_name, $parent_id );
		if( $process_taxonomy_id >= 1 ) {
			insert_child_metakey( $parent_id, $attribute_name, $meta_data );
		}
	}
}

// Function to add attribute/s & its term/s to a product, if not present
function process_add_attribute( $parent_id, $attribute_name, $term_taxonomy_id, $action, $attribute_names ) {
	global $wpdb;
	$available_attributes = array();
	
	$meta_data = process_meta_value( $parent_id );
	
	if ( is_foreachable( $meta_data ) ) {
		foreach( $meta_data as $key => $value ) {
			$available_attributes[] = substr( strstr( $key, '_' ), 1 );
		}
	}
        
        if ( !(empty( $available_attributes )) && in_array( $attribute_name, $available_attributes ) ) {
                process_taxonomy_id( $term_taxonomy_id, $attribute_name, $parent_id );
	} else {
                add_update_post_meta( $meta_data, $parent_id, $attribute_name, $action, $term_taxonomy_id, $attribute_names );
	}

    //Code to update the count of the attributes
    update_attribute_counts($attribute_name);
}

// Function to add Custom attribute/s & its term/s to a product 
function process_custom_add_attribute( $parent_id, $custom_attribute_name, $custom_terms, $action, $attribute_names ) {
	global $wpdb;
	
	$meta_data = process_meta_value( $parent_id );
	$attribute_post_array = meta_value_array( $custom_attribute_name, $parent_id, $action, $custom_terms, $attribute_names, $meta_data );
	foreach( $attribute_post_array as $key => $value ) {
		$meta_data[$key] = $value;
	}
	
	$update = sm_post_meta_update( serialize( $meta_data ), $parent_id );
	
	if( $update == 1 ) {
		$terms = wp_get_object_terms( $parent_id, 'product_type', array('fields' => 'names') );
		if( sanitize_title( $terms[0] ) == 'variable' ){
			insert_child_metakey( $parent_id, $custom_attribute_name, $meta_data );
		}
	}
}

// Function returns those attribute names which are already assigned to a product
function get_attribute_names( $attribute_name, $parent_id, $meta_data ) {
	$attribute_names = array();
	foreach( $meta_data as $key => $value ) {
		$strposition = strpos( $key, '_' );
		$strposition++;
		$name = substr( $key, $strposition );
		$attribute_names[] = ( $name != '' ) ? $name : $key;
	}
	return $attribute_names;
}

// Function to remove attribute & also update its association with product variations ( if present )
function remove_attribute( $parent_id, $attribute_name, $child_ids, $meta_data ) {
	global $wpdb;

	$key = "pa_" . $attribute_name;
	$meta_key = "attribute_" . $key;
        unset( $meta_data[$key] );
	$attribute_names = array();
	foreach( $meta_data as $attribute ) {
		$attributes_position[] = $attribute['position'];
		$attribute_names[] = $attribute['name'];
	}

        if (is_null($attributes_position) == false) {
            $pos = min( $attributes_position );
        }

	if( $pos != 0 ) {
            $pos = "0";
	}

	for( $i = 0; $i < count( $attributes_position ); $i++ ) {
		$attr_name = $attribute_names[$i];
		$meta_data[$attr_name]['position'] = "$pos";
		$pos++;
	}
	$update = sm_post_meta_update( serialize( $meta_data ), $parent_id );
	if( $update == 1 ) {
		$terms = wp_get_object_terms( $parent_id, 'product_type', array('fields' => 'names') );
		if( sanitize_title( $terms[0] ) == 'variable' ){
			$delete_query = "DELETE FROM {$wpdb->prefix}postmeta where meta_key = '$meta_key'  and post_id in ( $child_ids )";
			$wpdb->query( $delete_query );
		} 
	}
}

// Function to delete term/s.
function delete_terms( $attribute_name, $term_taxonomy_id, $parent_id, $meta_data ) {
	global $wpdb;
	
	$select_results = $wpdb->get_results( "select p.ID as ID from {$wpdb->prefix}posts as p where p.post_parent = $parent_id " );
    $rows_child_ids = $wpdb->num_rows;
    
	$ids = array();
	
    if ($rows_child_ids > 0) {
        foreach( $select_results as $select_result ) {
            $ids[] = $select_result->ID;
        }
        $child_ids = implode(",", $ids);    
    }
    
	$id = ( $term_taxonomy_id == 'all' ) ? implode( "," , get_attribute_terms( $attribute_name, $parent_id ) ) : $term_taxonomy_id;
	$delete_query = "DELETE FROM {$wpdb->prefix}term_relationships where object_id = $parent_id and term_taxonomy_id in ( $id )" ;
	

    $delete_result = $wpdb->query( $delete_query );
        
	if( $delete_result >= 1 ) {
		if( $term_taxonomy_id != 'all' ) {
			$taxonomy = "pa_" . $attribute_name;
			$meta_key = "attribute_" . $taxonomy;			
			$select = "select t.slug from {$wpdb->prefix}terms as t join {$wpdb->prefix}term_taxonomy as tt on (t.term_id = tt.term_id) where tt.term_taxonomy_id = $term_taxonomy_id and tt.taxonomy = '$taxonomy' ";
			$select_output = $wpdb->get_row($select);
			$slug = $select_output->slug;
	
            if ($rows_child_ids > 0) {
                $update_query = "UPDATE {$wpdb->prefix}postmeta as pm SET pm.meta_value = '' where pm.meta_key = '$meta_key' and pm.meta_value = '$slug' and pm.post_id in ( $child_ids )";
                $update_result = $wpdb->query( $update_query );
            }
			
			$count_result =  $wpdb->get_row("select count(*) as count from {$wpdb->prefix}term_relationships as tr join {$wpdb->prefix}term_taxonomy as tt on (tr.term_taxonomy_id = tt.term_taxonomy_id) where tt.taxonomy = '$taxonomy' and tr.object_id = $parent_id ");
			$count = $count_result->count;
		}
		
		// $count < 1 means not a single term exists for that attribute so it should be removed
		if ( $count < 1 || $term_taxonomy_id == 'all' ) {
			remove_attribute( $parent_id, $attribute_name, $child_ids, $meta_data );
		}
	}
}

// Function to handle removal of attribute/s OR term/s
function process_remove_attribute( $parent_id, $attribute_name, $term_taxonomy_id ) {
	global $wpdb;
	
	$meta_data = process_meta_value( $parent_id );
	$attribute_names = get_attribute_names( $attribute_name, $parent_id, $meta_data );

	if( in_array( $attribute_name, $attribute_names ) ) {
		$delete_term = delete_terms( $attribute_name, $term_taxonomy_id, $parent_id, $meta_data );
	}

    //Code to update the count of the attributes
    update_attribute_counts($attribute_name);
}

// Function to handle changes in attribute of product variation
function process_change_attribute( $child_id, $attribute_name, $term_taxonomy_id, $term_name = array() ) {
	global $wpdb;
	
	$post_parent = $wpdb->get_var( "SELECT post_parent FROM $wpdb->posts WHERE ID = $child_id " );
	$meta_data = process_meta_value( $post_parent );
	$attribute_names = get_attribute_names( $attribute_name, $post_parent, $meta_data );
	if( in_array( $attribute_name, $attribute_names ) ) {
		$taxonomy = "pa_" . $attribute_name;
		$meta_key = "attribute_" . $taxonomy;

		if( $term_taxonomy_id == 'all' ) {
			$update = "UPDATE {$wpdb->prefix}postmeta as pm SET pm.meta_value = '' where pm.meta_key = '$meta_key' and pm.post_id = $child_id ";
		} else {
			$taxonomy_terms_by_id = get_attribute_terms( $attribute_name, $post_parent );
			$term_taxonomy_id = implode( ",", $taxonomy_terms_by_id );
			$select_all = "select t.slug from {$wpdb->prefix}terms as t join {$wpdb->prefix}term_taxonomy as tt on (t.term_id = tt.term_id)
							 where tt.taxonomy = '$taxonomy'
							 and t.term_id in ( select term_id from {$wpdb->prefix}term_taxonomy
							  where term_taxonomy_id in ( $term_taxonomy_id ) )";
			$select_results = $wpdb->get_results( $select_all );
			$terms_by_id = array();
			foreach( $select_results as $select_result ) {
				$terms_by_id[] = $select_result->slug ;
			}
			if( in_array( $term_name[0], $terms_by_id ) ) {
				$update = "UPDATE {$wpdb->prefix}postmeta as pm SET pm.meta_value = '$term_name[0]' where pm.meta_key = '$meta_key' and pm.post_id = $child_id ";
			}
		}
		if ( $update != '' ) {
			$result = $wpdb->query( $update );
		}
	}
}

//Function to handle the insertion of the meta_key that is not present
function sm_insert_metakey($ids,$column_name) {
      global $wpdb;
      $values = array();

      $query_insert_ids = "SELECT DISTINCT p1.post_id FROM {$wpdb->prefix}postmeta AS p1
                            WHERE NOT EXISTS (SELECT post_id FROM {$wpdb->prefix}postmeta AS p2 
                                      WHERE p2.meta_key LIKE '$column_name' 
                                          AND p2.post_id = p1.post_id)
                                AND p1.post_id IN ($ids)";
      $result_insert_ids = $wpdb->get_col ( $query_insert_ids );
      $rows_insert   = $wpdb->num_rows;


      if ($rows_insert > 0) {
            $insert_query = "INSERT INTO {$wpdb->prefix}postmeta (`post_id`, `meta_key`) VALUES ";
            foreach ($result_insert_ids as $result_insert_id) {
                $values[] = "( $result_insert_id, '$column_name')";
            }
            $insert_query .= implode( ',', $values );
            $wpdb->query( $insert_query );
      }
}


function batchUpdateWoo($post) {
	global $post_status_update, $table_prefix, $wpdb, $sql_results,$woocommerce;
        $_POST = $post;     // Fix: PHP 5.4
	if (! empty ( $wpdb->prefix ))
		$wp_table_prefix = $wpdb->prefix;

    $edited_ids = (isset($_POST['edited'])) ? json_decode ($_POST['edited']) : array();
    if (!empty($edited_ids)) {
		$_POST ['active_module'] = $_POST ['activeModule'];
		$result = woo_insert_update_data($_POST);
	}

	$ids = json_decode ( stripslashes ( $_POST ['ids'] ) );

    $woo_prod_obj = '';
    // if ($_POST['SM_IS_WOO21'] == "true") {
    //     $woo_prod_obj = new WC_Product_Variable();
    // }
	
    $radioData = ''; // For WP_Debug
    $flag = ''; // For WP_Debug
        
		if ($_POST ['activeModule'] == 'Products') {
			
			$active_module = 'Products';
            $actions       = json_decode ( $_POST ['updateDetails'] );
            $sel_records   = json_decode ( $_POST ['selected'] );
			$radioData     = $wpdb->_real_escape ( $_POST['radio'] );
			$flag	       = $wpdb->_real_escape ( $_POST['flag'] );
			
			// create an array of ids (newly added products & modified products)
			$count = isset($result ['updateCnt']) ? $result ['updateCnt'] : 0;			//to skip updated & unselected records from batch update
			for($i = 0; $i < count ( $ids ); $i ++) {
				if (strstr ( $ids [$i], 'ext-record' ) != '') {
					$ids_temp [$i] = $result ['productId'] [$count];
					$count ++;
				}
			}
			if(isset($sel_records) && $sel_records != null) {//collectin the variation product's id
				foreach ($sel_records as $record){
					if (isset($record->id) && !empty($record->id) && $record->id != '') {
						if($record->post_parent != 0 ){
							$children_ids[] = $record->id;
                                                }
                                                else{
							$parent_ids[] = $record->id;
                                                }
					} else {
						$parent_ids = $result ['productId'];
					}
				}
			}
		} else {
			if ($_POST ['activeModule'] == 'Customers') {
				$active_module = 'Customers';
			}else {
				$active_module = 'Orders';
			}
                        $actions = json_decode ( $_POST ['values'] );
		}

		//$idLength = count ( $ids );
        $idLength  = json_decode( stripslashes ( $_POST ['fupdatecnt'] ) ); // code to handle the message for different number of max. records
		$length = count ( $actions );
		$all_ids = get_all_ids($sql_results);
		
                $_POST ['actions_count'] = $length;
                
		// For distributing ids based on product type, will help in reducing number of queries
		if($active_module == 'Products'){
			$variation_parent_id = array();
			$selected_id = array();
			$selected_id_variation = array();
			$all_id = array();
			$all_id_variation = array();

				foreach ( $sel_records as $sel_record ) {	
					$terms = wp_get_object_terms( $sel_record->id, 'product_type', array('fields' => 'names') );			// woocommerce gets product_type using this method
					$post_parent = $wpdb->get_var("SELECT post_parent FROM $wpdb->posts WHERE ID = $sel_record->id;");
                    $terms_type = (!empty($terms[0])) ? $terms[0] : '';
					if ( sanitize_title($terms_type) == 'variable' && $post_parent == 0 ) {
						$variation_parent_id[] = $sel_record->id;
					} else {
						if ( $post_parent > 0 && sanitize_title($terms_type) == 'simple' ) {
                                                    $selected_id[] = $sel_record->id;
                                                } elseif ( $post_parent > 0 ) {
                                                    $selected_id_variation[] = $sel_record->id;
                                                } else {
                                                    $selected_id[] = $sel_record->id;
                                                }
					}
				}	
			
			$all_ids_grouped = array();						// Array used for passing ids to function for processing variations
			$all_ids_grouped['selected_id'] = $selected_id;
			$all_ids_grouped['variation_parent_id'] = $variation_parent_id;
			$all_ids_grouped['selected_id_variation'] = $selected_id_variation;
			$all_ids_grouped['all_id'] = $all_id;
			$all_ids_grouped['all_id_variation'] = $all_id_variation;
			
			$parent_ids = array_merge( $all_ids_grouped['selected_id'], $all_ids_grouped['variation_parent_id'] );
			
			$results = $wpdb->get_results( "SELECT attribute_name FROM {$wpdb->prefix}woocommerce_attribute_taxonomies", 'ARRAY_A' );
			$attribute_names = array();
			$count = 0;
			foreach( $results as $result ) {
				$attribute_names[] = $result['attribute_name'];
				$count++;
			}
			
			// For handling modification in attributes of parent product
			foreach( $parent_ids as $parent_id ) {
				for( $i = 0; $i < count( $actions ); $i++ ) {

                    $actions_colfilter = (property_exists($actions[$i], 'colFilter') === true) ? $actions[$i]->colFilter : ''; // For WP_Debug

					if( $actions_colfilter == 'AttributeAdd' ) {
						$action = $actions_colfilter;
						$attribute_name = $actions[$i]->action;
						$attribute_type = $wpdb->get_var("SELECT attribute_type FROM {$wpdb->prefix}woocommerce_attribute_taxonomies WHERE attribute_name LIKE '$attribute_name'");
                        if( $attribute_name == 'custom' ) {
							$custom_attribute_name = $actions[$i]->colValue;
							$custom_attribute_terms = $actions[$i]->unit;
							process_custom_add_attribute( $parent_id, $custom_attribute_name, $custom_attribute_terms, $action, $attribute_names );
						} else {
							if ( $attribute_type == 'text' ) {
                                $terms = explode( '|', $actions[$i]->unit );
                                if ( is_array( $terms ) && count( $terms ) > 0 ) {
                                    foreach ( $terms as $term ) {
                                        $term_details = term_exists( $term, 'pa_'.$attribute_name );
                                        if ( !is_array( $term_details ) && $term_details == 0 ) {
                                            $term_details = wp_insert_term( $term, 'pa_'.$attribute_name );
                                        }
                                        $term_taxonomy_id = $term_details['term_taxonomy_id'];
                                        process_add_attribute( $parent_id, $attribute_name, $term_taxonomy_id, $action, $attribute_names );
                                    }
                                }
                            } else {
                                $term_taxonomy_id = $actions[$i]->colValue;
                                process_add_attribute( $parent_id, $attribute_name, $term_taxonomy_id, $action, $attribute_names );
                            }
						}
					} elseif ( $actions_colfilter == 'AttributeRemove' ) {
						$attribute_name = $actions[$i]->action;
						$term_taxonomy_id =  $actions[$i]->colValue;
						process_remove_attribute( $parent_id, $attribute_name, $term_taxonomy_id );
					}
				}
			}
			
			// For handling changes in attributes of product variation
			foreach( $all_ids_grouped['selected_id_variation'] as $c_id ) {
				for( $i = 0; $i < count( $actions ); $i++ ) {

                    $actions_colfilter = (property_exists($actions[$i], 'colFilter') === true) ? $actions[$i]->colFilter : ''; // For WP_Debug

					if( $actions_colfilter == 'AttributeChange' ) {
						$attribute_name = $actions[$i]->action;
						$term_taxonomy_id = $actions[$i]->colValue;
						$taxonomy = "pa_" . $attribute_name;
						$select = "select t.slug from {$wpdb->prefix}terms as t join {$wpdb->prefix}term_taxonomy as tt on (t.term_id = tt.term_id) where tt.taxonomy = '$taxonomy' and t.term_id in ( " ;
						$select .= "select term_id from {$wpdb->prefix}term_taxonomy where term_taxonomy_id = '$term_taxonomy_id' ) ";
						$results = $wpdb->get_results($select , 'ARRAY_A');
						$term_name = array();
						foreach( $results as $result ){
							$term_name[] = $result['slug'] ;
						}
						process_change_attribute( $c_id, $attribute_name, $term_taxonomy_id, $term_name );
					}
				}
			}
			
			// generating string of comma separated ids
			$variation_parent_id 	= $wpdb->_real_escape ( implode ( ',', $variation_parent_id ) );
                        $selected_id 			= $wpdb->_real_escape ( implode ( ',', $selected_id ) );
                        $selected_id_variation 	= $wpdb->_real_escape ( implode ( ',', $selected_id_variation ) );
                        $all_id 				= $wpdb->_real_escape ( implode ( ',', $all_id ) );
                        //This is commented as the implode function dosent work properly with very long list of ids.
            
                        $product_type_grouped = "";
                        $product_type = "";
                        $no_product_type = "";
                        $price_variation = "";

                        //$all_id_variation 		= $wpdb->_real_escape ( implode ( ',', $all_id_variation ) );

                        //Query for handling the grouped products updation
            
                        $query_grouped = "SELECT id FROM `{$wpdb->prefix}posts`
                                    WHERE post_parent IN (SELECT posts.id as id
                                            FROM `{$wpdb->prefix}posts` AS posts
                                                JOIN {$wpdb->prefix}term_relationships AS term_relationships 
                                                                ON term_relationships.object_id = posts.id
                                                JOIN {$wpdb->prefix}term_taxonomy AS term_taxonomy 
                                                                ON term_taxonomy.term_taxonomy_id = term_relationships.term_taxonomy_id 
                                                JOIN {$wpdb->prefix}terms AS terms 
                                                                ON term_taxonomy.term_id = terms.term_id 
                                            WHERE posts.post_parent = 0 
                                                AND posts.post_type IN ('product')
                                                AND posts.post_status IN ('publish', 'draft')
                                                AND terms.slug IN ('grouped'))
                                        AND id IN (". implode(",",$ids) .")
                                    GROUP BY id
                                    ORDER BY id desc ";
                        $product_type_grouped = implode (",",$wpdb->get_col( $query_grouped )); 

                        //Query to get the post_id type of all the simple products
                        $query = "SELECT posts.id as id
                                            FROM `{$wpdb->prefix}posts` AS posts
                                                JOIN {$wpdb->prefix}term_relationships AS term_relationships 
                                                                ON term_relationships.object_id = posts.id
                                                JOIN {$wpdb->prefix}term_taxonomy AS term_taxonomy 
                                                                ON term_taxonomy.term_taxonomy_id = term_relationships.term_taxonomy_id 
                                                JOIN {$wpdb->prefix}terms AS terms 
                                                                ON term_taxonomy.term_id = terms.term_id 
                                            WHERE posts.post_parent = 0 
                                                AND posts.post_type IN ('product')
                                                AND posts.post_status IN ('publish', 'draft')
                                                AND terms.slug NOT IN ('variable','grouped','external')
                                                AND posts.id IN (". implode(",",$ids) .")
                                            GROUP BY posts.id
                                            ORDER BY posts.id desc ";
                        $product_type_result = $wpdb->get_col( $query );                                             

                        //Query to handle the products which have no type
                        
                        $query = "SELECT object_id FROM {$wpdb->prefix}term_relationships
                                    WHERE object_id IN (". implode(",",$ids) .")";
                        $result = $wpdb->get_col( $query );           
                        
                        $no_product_type = array_values(array_diff($ids, $result));


                        for($i=0, $j=sizeof($product_type_result);$i<sizeof($no_product_type);$i++,$j++) {

                            //Code to exclude variations from the product type update                            
                            $post_parent = $wpdb->get_var("SELECT post_parent FROM {$wpdb->prefix}posts WHERE ID = $no_product_type[$i]");
                            if($post_parent == 0) {

                                $product_type_result [$j] = $no_product_type[$i];
                                //Code for adding the product type as simple for products having no type
                                $terms = wp_set_object_terms($no_product_type[$i], 'simple', 'product_type');       
                            }
                        }

                        $product_type = implode (",",array_filter($product_type_result)); 

                        //Query to get the post_id of all the variations for price updation
                        $query_variation = "SELECT posts.id as id
                                            FROM `{$wpdb->prefix}posts` AS posts
                                            WHERE posts.post_parent > 0 
                                                AND posts.post_type IN ('product_variation')
                                                AND posts.post_status IN ('publish', 'draft')
                                                AND posts.id IN (". implode(",",$ids) .")
                                            GROUP BY posts.id
                                            ORDER BY posts.id desc ";
                        $price_variation = implode (",",$wpdb->get_col( $query_variation )); 


        }
                //Code to get all the term_names along with the term_taxonomy_id in an array
                $query_terms = "SELECT terms.name,term_taxonomy.term_taxonomy_id 
                                FROM {$wpdb->prefix}term_taxonomy AS term_taxonomy
                                    JOIN {$wpdb->prefix}terms AS terms ON terms.term_id = term_taxonomy.term_id
                                WHERE taxonomy LIKE 'shop_order_status'";
              
                $terms = $wpdb->get_results ( $query_terms,'ARRAY_A');
                
                
                for ($i=0;$i<sizeof($terms);$i++) {
                    $terms_name[$terms[$i]['name']] = $terms[$i]['term_taxonomy_id'];
                }


                
		// Building queries
		for($i = 0; $i < $length; $i++) {
			$selected_ids = "";

			if($active_module == 'Products'){

                $actions_colfilter = (property_exists($actions[$i], 'colFilter') === true) ? $actions [$i]->colFilter : ''; // For WP_Debug
                $actions_updatecolname = (property_exists($actions[$i], 'updateColName') === true) ? $actions [$i]->updateColName : ''; // For WP_Debug
                $actions_colname = (property_exists($actions[$i], 'colName') === true) ? $actions [$i]->colName : ''; // For WP_Debug

				if(substr($actions_colfilter,0,8 ) == 'Attribute') {continue;}
				$table_name 	  = "{$wpdb->_real_escape ( $actions [$i]->tableName )}";
				$col_id           = $wpdb->_real_escape ( $actions [$i]->colId );
				$is_category 	  = (strstr($col_id,'group') != '') ? true : false;	
				$column_name 	  = "{$wpdb->_real_escape ( $actions_colname )}";
				$action_name 	  = $wpdb->_real_escape ( $actions [$i]->action );
                $column_filter    = $wpdb->_real_escape ( $actions_colfilter );
                $column_type      = (!empty($actions [$i]->colType)) ? $wpdb->_real_escape ( $actions [$i]->colType ) : '';

                if ( $column_name == 'thumbnail' ) {
                    for ( $j=0;$j<sizeof($ids);$j++ ) {
                        update_post_meta($ids[$j], '_thumbnail_id', $actions [$i]->colValue );
                    }
                }

                // populating all ids based on column to be updated
				if ( $column_name == "_regular_price" || $column_name == "_sale_price" ) {
					$all_ids = $all_id;
				} elseif ( $column_name == "post_title" || $column_name == "post_status" || $column_name == "post_content" || $column_name == "post_excerpt" || $is_category == true || $column_name == "_tax_status" ) {
					$all_ids = $variation_parent_id;
                    $all_ids .= ( isset($all_id) && $all_id != '' ) ? ',' . $all_id : '';
				} else {
					$all_ids = $variation_parent_id;
                    // $all_ids .= ( isset($all_id_variation) && $all_id_variation != '' ) ? ',' . $all_id_variation : '';
                    $all_ids .= ( !empty($all_id_variation) ) ? ',' . implode(",", $all_id_variation) : ''; // For WP_Debug
                    $all_ids .= ( isset($all_id) && $all_id != '' ) ? ',' . $all_id : '';
				}
                $all_ids = trim( $all_ids, ',' );
				$update_column    = ($actions_updatecolname != '') ? "{$wpdb->_real_escape ( $actions_updatecolname )}" : "{$wpdb->_real_escape ( $actions_colname )}";			
				$col_filter       = "{$wpdb->_real_escape ( $actions_colfilter )}";			
				$drop_down3_value = "{$wpdb->_real_escape ( $actions [$i]->unit )}"; //@todo for state code for customers		
				
				$row_filter = '';
				$filter_col = '';
				if ($col_filter != '') {
					$col_filter_arr = explode ( ':', $col_filter );
					$filter_col = "$col_filter_arr[0]";
					$row_filter = $col_filter_arr [1];
				}
				$text_cmp_value = (!empty($actions [$i]->colValue)) ? $wpdb->_real_escape ( $actions [$i]->colValue ) : '';

                if ($column_type == 'custom_column' && $col_id == 'other_meta') {

                    if (empty($ids))
                        continue;

                    $meta_key = (!empty($actions [$i]->colValue)) ? $wpdb->_real_escape ( $actions [$i]->colValue ) : '';
                    $meta_value = (!empty($actions [$i]->unit)) ? $wpdb->_real_escape ( $actions [$i]->unit ) : '';

                    $query_other_meta_existing = "SELECT DISTINCT post_id FROM {$wpdb->prefix}postmeta
                                                    WHERE meta_key LIKE '".$meta_key."' 
                                                        AND post_id IN (".implode ( ',', $ids ).")";
                    $results_other_meta_existing = $wpdb->get_col($query_other_meta_existing);
                    $rows_other_meta_existing = $wpdb->num_rows;

                    if ($rows_other_meta_existing > 0) {
                        $query_other_meta_update = "UPDATE {$wpdb->prefix}postmeta
                                                            SET meta_value = '".$meta_value."'
                                                        WHERE meta_key LIKE '".$meta_key."' 
                                                        AND post_id IN (".implode ( ',', $results_other_meta_existing ).")";
                        $results_other_meta_update = $wpdb->query($query_other_meta_update);

                        $ids = array_diff($ids,$results_other_meta_existing); // Removing already updated ids

                        if (empty($ids))
                            continue;
                    }

                    $insert_values = array();
                    foreach ($ids as $id) {
                        $insert_values [] = "(".$id.",'".$meta_key."','".$meta_value."')";
                    }

                    $query_other_meta   = "REPLACE INTO {$wpdb->prefix}postmeta (post_id, meta_key, meta_value)
                                                VALUES ".implode ( ',', $insert_values );
                    $results_other_meta = $wpdb->query($query_other_meta);

                    continue;
                }

                if ($column_type == 'custom_column_serialized') {

                    if (is_serialized($text_cmp_value) == true) {
                        $query_serialized_data = "SELECT meta_value, post_id 
                                              FROM {$wpdb->prefix}postmeta
                                              WHERE post_id IN (".implode(',',$ids).")
                                                AND meta_key LIKE '".$actions_colname."'
                                              GROUP BY post_id";
                        $results_serialized_data = $wpdb->get_results($query_serialized_data, 'ARRAY_A');
                        $rows_serialized_data = $wpdb->num_rows;

                        if ($rows_serialized_data > 0) {

                            $final_meta_value_array = array();

                            foreach ($results_serialized_data as $result_serialized_data) {
                                $old_value = unserialize($result_serialized_data['meta_value']);
                                $new_value = unserialize(stripslashes($text_cmp_value));
                                // $final_array = $new_value + $old_value;

                                $old_value_key = (!empty($old_value)) ? array_keys($old_value) : '';
                                $final_array = ($old_value_key[0] > 1) ? $new_value + $old_value : array_merge($old_value,$new_value);

                                $final_meta_value_array [] = "WHEN {$wpdb->prefix}postmeta.post_id = ".$result_serialized_data['post_id']." THEN '". serialize($final_array) ."'";
                            }

                            // $final_meta_value_array = serialize($final_meta_value_array);
                        
                            $query_serialized_update   = "UPDATE {$wpdb->prefix}postmeta
                                                        SET meta_value = CASE ".implode(" ",$final_meta_value_array)." END 
                                                        WHERE {$wpdb->prefix}postmeta.meta_key LIKE '".$actions_colname."'
                                                            AND {$wpdb->prefix}postmeta.post_id IN (".implode ( ',', $ids ).")";
                            $results_serialized_update = $wpdb->query($query_serialized_update);

                        } else {

                            foreach ($ids as $id) {
                                $final_meta_value_array [] = "(".$id.", '". $actions_colname ."', '". serialize($text_cmp_value) ."')";
                            }

                            $query_serialized_insert   = "INSERT INTO {$wpdb->prefix}postmeta (post_id,meta_key,meta_value)
                                                        VALUES ".implode(", ",$final_meta_value_array);
                            $results_serialized_insert = $wpdb->query($query_serialized_update);
                        }   

                    }

                    continue;
                } 
                $selected_ids = implode ( ',', $ids ); //Added by Tarun to cater the function to update only for ids passed
			} else {
				$actions [$i] [0] = explode ( ',', $actions [$i] [0] );

				$action_index = 0;
				foreach ( $actions [$i] [0] as $action ) { // trimming the field names & table names
					$actions [$i] [0] [$action_index] = trim ( $actions [$i] [0] [$action_index] );
					$action_index ++;
				}

				// getting values from POST
				$action_name      = $wpdb->_real_escape ( $actions [$i] [1] );
				$column_name      = $wpdb->_real_escape ( $actions [$i] [0] [0] );
				$table_name       = $wpdb->_real_escape ( $actions [$i] [0] [1] );
				$drop_down3_value = isset($actions [$i] [3]) ? $wpdb->_real_escape ( $actions [$i] [3] ) : '';
				$selected_ids	  = $wpdb->_real_escape ( implode ( ',', $ids ) );


                                if ($active_module == 'Customers') {
                                    
                                    //Query for getting the email id and the customer_user for the selected ids
                                    $query_email = "SELECT DISTINCT(GROUP_CONCAT( meta_value
                                     ORDER BY meta_id SEPARATOR '###' ) )AS meta_value,
                                     GROUP_CONCAT(distinct meta_key
                                     ORDER BY meta_id SEPARATOR '###' ) AS meta_key
                                     FROM {$wpdb->prefix}postmeta 
                                     WHERE meta_key in ('_billing_email','_customer_user') 
                                        AND post_id IN ($selected_ids)
                                     GROUP BY post_id";

                                    $result_email = $wpdb->get_results ( $query_email, 'ARRAY_A' );                     

                                    $email="";
                                    $users="";

                                    $index=0; $index1=0;
                                    for ( $j=0;$j<sizeof($result_email);$j++ ) {
                                        $meta_key = explode ("###",$result_email[$j]['meta_key']);
                                        $meta_value = explode ("###",$result_email[$j]['meta_value']);

                                        $postmeta[$j] = array_combine ($meta_key,$meta_value);
                                   
                                        if ($postmeta[$j]['_customer_user'] == 0) {
                                            $email[$index] = $postmeta [$j]['_billing_email'];
                                            $index++;
                                        }
                                        elseif ($postmeta[$j]['_customer_user'] > 0) {
                                            $users[$index1] = $postmeta [$j]['_customer_user'];
                                            $index1++;
                                        }
                                        unset($meta_key);
                                        unset($meta_value);
                                    }
                                    
                                    //For Guest Customers
                                    if($email!=""){
                                        $email = "'" . implode ("','",$email) . "'";
                                        
                                        //Query for getting all the post_ids w.r.to the email id of the edited customer record
                                        $query_ids="SELECT DISTINCT(post_id) FROM {$wpdb->prefix}postmeta WHERE meta_key='_billing_email' AND meta_value IN ($email)";        
                                        $id=implode(", ",$wpdb->get_col($query_ids));
                                        $selected_ids=$id;
                                    }
                                    
                                    //For Registered Customers
                                    if($users!=""){
                                        $users  = implode (",",$users);
                                    }
                                    
                                }

                				$text_cmp_value	  = '';
                				
                				if ( $column_name == '_billing_country' || $column_name == '_shipping_country' ) {
                					$region = ( !empty($actions [$i] [4]) ) ? $wpdb->_real_escape ( $actions [$i] [4] ) : $wpdb->_real_escape ( $actions [$i] [2] ); // For WP_Debug
                					$text_cmp_value = $drop_down3_value;
                					$state_column = ($column_name == '_billing_country') ? '_billing_state' : '_shipping_state';
                					$region_query = "UPDATE " . $wpdb->_real_escape($table_name) . " SET meta_value = '".$wpdb->_real_escape($region)."' WHERE post_id IN ( " . $wpdb->_real_escape($selected_ids) . " ) AND meta_key = '$state_column'";			
                					$result = $wpdb->query ( $region_query );
                					// if ( $result < 1 ) {
                					// 	$updated_rows_cnt = _e('Batch Updation of Region not successful','smart-manager');
                					// }
                				} 
                                else if ($table_name == "{$wpdb->prefix}term_relationships" && ( $actions [$i] [4] != '' )) {
                                     $term_id = $terms_name [$actions [$i] [4]];
                                     $query = "UPDATE `{$wpdb->prefix}term_relationships` SET term_taxonomy_id = $term_id 
                                                WHERE object_id IN ( " . $wpdb->_real_escape($selected_ids) . " )";
                                     $result = $wpdb->query ( $query );
                                     continue  ;
                                } else if (!empty($_POST['SM_IS_WOO22']) && $_POST['SM_IS_WOO22'] == "true" && $table_name == "{$wpdb->prefix}posts") {
                                    $order_status = 'wc-' . $actions [$i] [3];
                                     $query = "UPDATE `{$wpdb->prefix}posts` SET post_status = '$order_status'
                                                WHERE id IN ( " . $wpdb->_real_escape($selected_ids) . " )";
                                     $result = $wpdb->query ( $query );
                                     continue  ;
                                }
                                else {
                					$text_cmp_value = ($actions [$i] [2] == '') ? $drop_down3_value : $wpdb->_real_escape($actions [$i] [2]);					
                				}
			}
		
			if ($table_name == "{$wpdb->prefix}postmeta" || $table_name == "{$wpdb->prefix}usermeta") {
				$update_column = 'meta_value';
				$reference_column = 'meta_key';
			}
			
            $flag_query = 0; //Flag for handling the 'Set To Sales Price' and 'Set To Regular Price' batch update actions
                        
			switch ($action_name) {
				case 'SET_TO' :
                	if ($table_name =="{$wpdb->prefix}posts" || $table_name =="{$wpdb->prefix}postmeta" || $table_name =="{$wpdb->prefix}usermeta") { //version 3.8
                            //condition for handling the decimal places
                            if ((!empty($text_cmp_value)) && ($column_name == '_regular_price' || $column_name == '_sale_price')) {
                                $update_value = $update_column . ' = ROUND(' . $text_cmp_value. ','.get_option( 'woocommerce_price_num_decimals' ).')' ;
                            }
                            else {
                                if (!empty($text_cmp_value)) {
                                    $update_value = $update_column . ' = \'' . $text_cmp_value . '\''; //is array for weight
                                }
                                else {
                                    $update_value = $update_column . ' = ""';
                                }
                                
                            }

					} else if($is_category) {
                                            	$delete_query = "DELETE FROM " . $table_name . " WHERE `object_id` in (";
						$insert_query = "INSERT INTO " . $table_name . " (object_id,`" . $update_column . "`) VALUES ";
                                                $delete_query .= $selected_ids;
                                                $sub_query = array ();
                                                $category_selected_ids = explode ( ',', $selected_ids );
                                                foreach ( $category_selected_ids as $category_selected_id ) {
                                                        $sub_query [] = "(" . $category_selected_id . "," . $text_cmp_value . ")";
                                                }
                                                $insert_query .= implode ( ',', $sub_query );
                                        	$delete_query .= ") AND `term_taxonomy_id` IN ( SELECT term_taxonomy_id FROM {$wpdb->prefix}term_taxonomy WHERE taxonomy = 'product_cat' )";
						$delete_sql_result = $wpdb->query ( $delete_query );
						$insert_sql_result = $wpdb->query ( $insert_query );
					}
					break;

				case 'PREPEND' :
					if ($table_name =="{$wpdb->prefix}posts" || $table_name =="{$wpdb->prefix}postmeta" || $table_name =="{$wpdb->prefix}usermeta") { //version 3.8
						$update_value = $update_column . ' = concat(\'' . $text_cmp_value . '\',' . $update_column . ')';
					}
					break;
				
				case 'APPEND' :
					if ($table_name =="{$wpdb->prefix}posts" || $table_name =="{$wpdb->prefix}postmeta" || $table_name =="{$wpdb->prefix}usermeta") { //version 3.8
						$update_value = $update_column . ' = concat(' . $update_column . ',\'' . $text_cmp_value . '\')';
					}
					break;
				
				case 'INCREASE_BY_NUMBER' :

                    

					if ($table_name =="{$wpdb->prefix}postmeta") { 
                        //condition for handling the decimal places
                        if ((!empty($text_cmp_value)) && ($column_name == '_regular_price' || $column_name == '_sale_price')) {
                            $update_value = $update_column . ' = ROUND(' . $update_column . '+' . $text_cmp_value .','.get_option( 'woocommerce_price_num_decimals' ).')' ;
                        }
                        else {
                            if (!empty($text_cmp_value)) {
                                $update_value = $update_column . ' = ' . $update_column . '+' . $text_cmp_value;
                            }
                            else {
                                $text_cmp_value = "0";
                                $update_value = $update_column . ' = ' . $update_column . '+' . $text_cmp_value;
                            }
                            
                        }
					}
					break;
				
				case 'DECREASE_BY_NUMBER' :
					if ($table_name =="{$wpdb->prefix}postmeta") { 
                        //condition for handling the decimal places
                        if ((!empty($text_cmp_value)) && ($column_name == '_regular_price' || $column_name == '_sale_price')) {
                            $update_value = $update_column . ' = ROUND(' . $update_column . '-' . $text_cmp_value .','.get_option( 'woocommerce_price_num_decimals' ).')' ;
                        }
                        else {
                            if (!empty($text_cmp_value)) {
                                $update_value = $update_column . ' = ' . $update_column . '-' . $text_cmp_value;
                            }
                            else {
                                $text_cmp_value = "0";
                                $update_value = $update_column . ' = ' . $update_column . '-' . $text_cmp_value;
                            }
                            
                        }
					}
					break;
				
				case 'INCREASE_BY_%' :
                    if ($table_name =="{$wpdb->prefix}postmeta") { 
                        //condition for handling the decimal places
                        if ((!empty($text_cmp_value)) && ($column_name == '_regular_price' || $column_name == '_sale_price')) {
                            // $update_value = $update_column . ' = ROUND(' . $update_column . '+' . ($update_column . '*' . (number_format($text_cmp_value / 100,get_option( 'woocommerce_price_num_decimals' ),'.', ''))) .','.get_option( 'woocommerce_price_num_decimals' ).')';
                            $update_value = $update_column . ' = ROUND(' . $update_column . '+' . ($update_column . '*' . (number_format($text_cmp_value / 100,2,'.', ''))) .','.get_option( 'woocommerce_price_num_decimals' ).')';
                        }
                        else {
                            if (!empty($text_cmp_value)) {
                                $update_value = $update_column . ' =' . $update_column . '+' . ($update_column . '*' . (number_format($text_cmp_value / 100,2,'.', '')));
                            }
                            else {
                                $update_value = $update_column . ' =' . $update_column ;
                            }
                            
                        }
                                            
                    }
                    break;
                
                case 'DECREASE_BY_%' :
                    if ($table_name =="{$wpdb->prefix}postmeta") { 
                        //condition for handling the decimal places
                        if ((!empty($text_cmp_value)) && ($column_name == '_regular_price' || $column_name == '_sale_price')) {
                            // $update_value = $update_column . ' = ROUND(' . $update_column . '-' . ($update_column . '*' . (number_format($text_cmp_value / 100,get_option( 'woocommerce_price_num_decimals' ),'.', ''))) .','.get_option( 'woocommerce_price_num_decimals' ).')';
                            $update_value = $update_column . ' = ROUND(' . $update_column . '-' . ($update_column . '*' . (number_format($text_cmp_value / 100,2,'.', ''))) .','.get_option( 'woocommerce_price_num_decimals' ).')';

                        }
                        else {
                            if (!empty($text_cmp_value)) {
                                $update_value = $update_column . ' =' . $update_column . '-' . ($update_column . '*' . (number_format($text_cmp_value / 100,2,'.', '')));
                            }
                            else {
                                $update_value = $update_column . ' =' . $update_column ;
                            }
                            
                        }
                        
                    }
                    break;
				
				case 'YES' :
					if ($column_name == 'post_status'){
						$update_value = $update_column . ' = \'publish\'';
					} elseif($column_name == '_stock'){
						$update_value = $update_column . ' = 0';
					} elseif($column_type == 'custom_column'){
                        $update_value = $update_column . ' = \'yes\'';
                    } else{
						$update_value = $update_column . ' = 1';
					}
					break;
	
				case 'NO' :
					if ($column_name == 'post_status'){
						$update_value = $update_column . ' = \'draft\'';
					} elseif($column_name == '_stock'){
						$update_value = $update_column . ' = ""';				
					} elseif($column_type == 'custom_column'){
                        $update_value = $update_column . ' = \'no\'';
                    } else{
						$update_value = $update_column . ' = 0';
					}
					break;
				
				case 'ADD_TO' :
					$sub_query = array ();
					if ( ! $is_category ) {					// Need to be reworked
							for($j = 0; $j < count ( $ids ); $j ++) {
								$sub_query [] = "( " . $wpdb->_real_escape($ids[$j]) . "," . $text_cmp_value . ")";
							}
							$sub_query = implode ( ',', $sub_query );
							$query = "INSERT INTO " . $table_name . " (object_id,`" . $update_column . "`) VALUES " . $sub_query;
							$sql_result = $wpdb->query ( $query );
					} else {
                                            $delete_query = "DELETE FROM " . $table_name . " WHERE `object_id` in (";
                                            $insert_query = "INSERT INTO " . $table_name . " (`object_id`,`" . $update_column . "`) VALUES ";

                                            $delete_query .= $selected_ids;
                                            $sub_query = array ();
                                            $category_selected_ids = explode ( ',', $selected_ids );
                                            foreach ( $category_selected_ids as $category_selected_id ) {
                                                $sub_query [] = "(" . $category_selected_id . "," . $text_cmp_value . ")";
                                            }
                                            $insert_query .= implode ( ',', $sub_query );
                                            $delete_query .= ") AND `term_taxonomy_id`=" . $text_cmp_value;
                                            $delete_sql_result = $wpdb->query ( $delete_query );
                                            $insert_sql_result = $wpdb->query ( $insert_query );

                                        }
					break;
			
			    case 'REMOVE_FROM' :
						if ( ! $is_category ) {					// Need to be reworked
				    		$query = "DELETE FROM " . $table_name . " WHERE object_id in (".$wpdb->_real_escape(implode(',',$ids)).")
						              AND `" . $update_column . "` = " . $text_cmp_value;					
							$sql_result = $wpdb->query ( $query );
						}else{
                                                    $delete_query = "DELETE FROM " . $table_name . " WHERE `object_id` in (";
                                                    $delete_query .= $selected_ids;
                                                    $delete_query .= ") AND `term_taxonomy_id` = $text_cmp_value";
                                                    $delete_sql_result = $wpdb->query ( $delete_query );
                                                }
                                                break;

                case 'CATALOG & SEARCH' :
                    if ($table_name =="{$wpdb->prefix}postmeta") {
                        $update_value = $update_column . ' = \'visible\'';
                    }
                    break;

                case 'CATALOG' :
                    if ($table_name =="{$wpdb->prefix}postmeta") {
                        $update_value = $update_column . ' = \'catalog\'';
                    }
                    break;

                case 'SEARCH' :
                    if ($table_name =="{$wpdb->prefix}postmeta") {
                        $update_value = $update_column . ' = \'search\'';
                    }
                    break;

                case 'HIDDEN' :
                    if ($table_name =="{$wpdb->prefix}postmeta") {
                        $update_value = $update_column . ' = \'hidden\'';
                    }
                    break;
                    
                    
                case 'SET_TO_SALES_PRICE' :
                    
                        for ($j=0;$j<sizeof($ids);$j++) {
                                $type = wp_get_object_terms( $ids[$j], 'product_type', array('fields' => 'slugs') );
                                $query = "SELECT post_parent FROM `{$wpdb->prefix}posts` WHERE ID =". $ids[$j];
                                $result_parent = $wpdb->get_col($query);

                                if(!(empty($result_parent))){
                                    $product_type_parent = wp_get_object_terms($result_parent, 'product_type', array('fields' => 'slugs'));
                                }

                                // if (($type[0] == 'simple' && $result_parent[0] == 0) || ($product_type_parent[0] == "grouped") || ($_POST['SM_IS_WOO16'] == "false") ) {
                                if ( ( ( (!empty($type[0])) && $type[0] == 'simple') && ( (!empty($result_parent)) && $result_parent[0] == 0)) || ( (!empty($product_type_parent[0])) && $product_type_parent[0] == "grouped") || ( ((!empty($result_parent[0])) && $result_parent[0] > 0) && ( (!empty($_POST['SM_IS_WOO16'])) && $_POST['SM_IS_WOO16'] == "false")) ) {
                                    $flag_success = 1;
                                    $query = "SELECT meta_value FROM `{$wpdb->prefix}postmeta`
                                            WHERE meta_key = '_sale_price' AND post_id = ". $ids[$j];
                                    $result = $wpdb->get_col($query);

                                    $result[0] = trim($result[0]); // For handling when both price and sales price are null

                                    if(!empty($result[0])) {
                                        $query = "UPDATE `{$wpdb->prefix}postmeta` SET meta_value = ROUND(" . $result[0] . ',' . get_option( 'woocommerce_price_num_decimals' ) . ')' . " WHERE meta_key = '_regular_price' AND post_id = ". $ids[$j];
                                    }
                                    else {
                                        $query = "UPDATE `{$wpdb->prefix}postmeta` SET meta_value = '' WHERE meta_key = '_regular_price' AND post_id = ". $ids[$j];    
                                    }



                                    $result1 = $wpdb->query ($query);    
                                }
                                else if ( ( (!empty($result_parent[0])) && $result_parent[0] > 0) && ( (!empty($_POST['SM_IS_WOO16'])) && $_POST['SM_IS_WOO16'] == "true") ) {
                                    $flag_success = 1;
                                    $query = "SELECT meta_value FROM `{$wpdb->prefix}postmeta`
                                            WHERE meta_key = '_sale_price' AND post_id = ". $ids[$j];
                                    $result = $wpdb->get_col($query);

                                    $result[0] = trim($result[0]); // For handling when both price and sales price are null

                                    if(!empty($result[0])) {
                                        $query = "UPDATE `{$wpdb->prefix}postmeta` SET meta_value = ROUND(" . $result[0] . ',' . get_option( 'woocommerce_price_num_decimals' ) . ')' . " WHERE meta_key = '_price' AND post_id = ". $ids[$j];    
                                    }
                                    else {
                                        $query = "UPDATE `{$wpdb->prefix}postmeta` SET meta_value = '' WHERE meta_key = '_price' AND post_id = ". $ids[$j];
                                    }
                                    
                                    $result1 = $wpdb->query ($query);
                                }
                            }
                            $flag_query = 1;
                       
                    break;    
                        
                    
                    
                case 'SET_TO_REGULAR_PRICE' :

                        for ($j=0;$j<sizeof($ids);$j++) {
                            $type = wp_get_object_terms( $ids[$j], 'product_type', array('fields' => 'slugs') );
                            $query = "SELECT post_parent FROM `{$wpdb->prefix}posts` WHERE ID =". $ids[$j];
                            $result_parent = $wpdb->get_col($query);
                            
                            if(!(empty($result_parent))){
                                $product_type_parent = wp_get_object_terms($result_parent, 'product_type', array('fields' => 'slugs'));
                            }

                            // if ($type[0] == 'simple' && $result_parent[0] == 0 || ($product_type_parent[0] == "grouped")  || ($_POST['SM_IS_WOO16'] == "false") ) {
                            if ( ( ((!empty($type[0])) && $type[0] == 'simple') && ( (!empty($result_parent)) && $result_parent[0] == 0)) || ( (!empty($product_type_parent[0])) && $product_type_parent[0] == "grouped" ) || ( ( (!empty($result_parent[0])) && $result_parent[0] > 0) && ( (!empty($_POST['SM_IS_WOO16'])) && $_POST['SM_IS_WOO16'] == "false")) ) {
                                $query = "SELECT meta_value FROM `{$wpdb->prefix}postmeta`
                                            WHERE meta_key = '_regular_price' AND post_id = ". $ids[$j];
                                $result = $wpdb->get_col($query);

                                $result[0] = trim($result[0]); // For handling when both price and sales price are null

                                if(!empty($result[0])) {
                                    $query = "UPDATE `{$wpdb->prefix}postmeta` SET meta_value = ROUND(" . $result[0] . ',' . get_option( 'woocommerce_price_num_decimals' ) . ')' . " WHERE meta_key = '_sale_price' AND post_id = ". $ids[$j];
                                }
                                else {
                                    $query = "UPDATE `{$wpdb->prefix}postmeta` SET meta_value = '' WHERE meta_key = '_sale_price' AND post_id = ". $ids[$j];
                                }

                                $result1 = $wpdb->query ($query);
                            }
                            
                            else if ( ( (!empty($result_parent[0])) && $result_parent[0] > 0)  && ( (!empty($_POST['SM_IS_WOO16'])) && $_POST['SM_IS_WOO16'] == "true") ) {
                                $query = "SELECT meta_value FROM `{$wpdb->prefix}postmeta`
                                            WHERE meta_key = '_price' AND post_id = ". $ids[$j];
                                $result = $wpdb->get_col($query);
                                
                                $result[0] = trim($result[0]); // For handling when both price and sales price are null
                                
                                if(!empty($result[0])) {
                                    $query = "UPDATE `{$wpdb->prefix}postmeta` SET meta_value = ROUND(" . $result[0] . ',' . get_option( 'woocommerce_price_num_decimals' ) . ')' . " WHERE meta_key = '_sale_price' AND post_id = ". $ids[$j];
                                }
                                else {
                                    $query = "UPDATE `{$wpdb->prefix}postmeta` SET meta_value = '' WHERE meta_key = '_sale_price' AND post_id = ". $ids[$j];
                                }

                                $result1 = $wpdb->query ($query);
                            }
                        }
                        $flag_query = 1;
                    break;
			}

			if ($table_name != "{$wpdb->prefix}term_relationships") {
                if($flag_query == 0) {
                    if (is_array ( $update_value ))
                    $update_value = implode ( ',', $update_value );
                    $update_price_meta = false;
                                
                    $query        = "UPDATE " . $table_name . " SET " . $update_value;

                    if ($table_name =="{$wpdb->prefix}posts") {
                        if ($active_module == 'Products' && $update_column == 'post_status') {
                            $query .= " WHERE `post_type` IN ('product') AND `ID` in (" . $selected_ids . ")";
                        } else {
                            $query .= ' WHERE `ID` in (' . $selected_ids . ')';
                        }
                    } else if ($table_name =="{$wpdb->prefix}postmeta") {

                        if($column_name == '_regular_price') {
                            //Query for updating the price for the simple products

                            if (!empty($product_type))
                            {

                                sm_insert_metakey($product_type,'_regular_price');// Code for inserting the meta_key if not present
                                
                                $query_simple = $query;
                                $query_simple = $query_simple . " WHERE meta_key LIKE '_regular_price' AND post_id IN ($product_type)";
                                $result = $wpdb->query ( $query_simple );
                                
                                if (empty($price_variation)) {
                                    $query = "";
                                }
                        
                            }
                            
//                                                    $query = "INSERT INTO ". $table_name . "VALUES"
                            
                            //Query for updating the price for the grouped products
                            
                            if (!empty($product_type_grouped))
                            {

                                sm_insert_metakey($product_type_grouped,'_regular_price');// Code for inserting the meta_key if not present
                                
                                $query_grouped = $query;
                                $query_grouped = $query_grouped . " WHERE meta_key LIKE '_regular_price' AND post_id IN ($product_type_grouped)";
                                $result_grouped = $wpdb->query ( $query_grouped );
                                
                                if (empty($price_variation)) {
                                    $query = "";
                                }
                                
                            }
                               


//                                                    $query .= " WHERE meta_key LIKE '_price' AND post_id IN (" . $selected_ids . ")";
                            
                            //Woo 2.0 Compatibility check
                            if ($_POST['SM_IS_WOO16'] == "true") {
                                if (!empty($price_variation))
                                {
                                    sm_insert_metakey($price_variation,'_price');// Code for inserting the meta_key if not present
                                    $query .= " WHERE meta_key LIKE '_price' AND post_id IN ($price_variation)";

                                }
                                else {
                                    $query = "";    
                                }
                                
                            }
                            else {

                                if (!empty($price_variation))
                                {
                                    sm_insert_metakey($price_variation,'_regular_price');// Code for inserting the meta_key if not present
                                    $query .= " WHERE meta_key LIKE '_regular_price' AND post_id IN ($price_variation)";
                                }
                                else {
                                    $query = "";
                                }
                                
                            }

                        }
                        else {

                            if (!empty($selected_ids))
                            {
                                sm_insert_metakey($selected_ids,$column_name);// Code for inserting the meta_key if not present
                                $query .= " WHERE `post_id` in (" . $selected_ids . ") AND meta_key = '" . $column_name . "'";
                            }
                            
                        }
                    
                    }


                    if ((!empty($query)) && $flag_query == 0) {
                        $result = $wpdb->query ( $query );
                    }
                }
				

                    //Updating the stock status on updation of inventory
                    if ( $column_name == '_stock' && $selected_ids != "" ) {

                        if ($_POST['SM_IS_WOO21'] == "true" || $_POST['SM_IS_WOO22'] == "true") {
                            $product_ids = explode(",",$selected_ids);

                            foreach ( $product_ids as $product_id ) {
                                $woo_prod_obj_stock_status = new WC_Product($product_id);
                                $woo_prod_obj_stock_status->set_stock($woo_prod_obj_stock_status->get_stock_quantity());
                            }
                        }
                    }
                    
                    
                    // Code for Updating the '_price' for All Products
                    if ($column_name == '_regular_price' || $column_name == '_sale_price') {
                        $simple_ids = explode (",",$product_type);
                        $grouped_ids = explode (",",$product_type_grouped);
                        $variation_ids = explode (",",$price_variation);

                        if (!empty($product_type) && !empty($product_type_grouped)) {
                            $final_ids = array_merge($simple_ids, $grouped_ids);
                        }
                        else
                        {
                            if (!empty($product_type)) {
                                $final_ids = $simple_ids;
                            }
                            else {
                                $final_ids = $grouped_ids;
                            }
                        }
                        
                        if ((!empty($price_variation)) && ($_POST['SM_IS_WOO16'] == "false") ) {
                            if (!empty($final_ids)) {
                                $final_ids = array_merge($final_ids, $variation_ids);
                            }
                            else {
                                $final_ids = $variation_ids;
                            }
                    
                        }

                        update_price_meta(implode(",",array_filter($final_ids)));
                        
                        // For Updating Variable Parent Price
                        if (!empty($price_variation))
                        {
                            if ($_POST['SM_IS_WOO21'] == "true" || $_POST['SM_IS_WOO22'] == "true") {

                                $query = "SELECT distinct post_parent as id from {$wpdb->prefix}posts WHERE post_type='product_variation' AND id IN ($price_variation)";
                                $parent_ids = $wpdb->get_col ( $query );

                                if (!empty($parent_ids)) {
                                    foreach ($parent_ids as $parent_id) {
                                        WC_Product_Variable::sync($parent_id); // as the WC_Product_Variable is static
                                        // $woo_prod_obj->sync($parent_id);  
                                    }
                                }
                                
                            } else {
                                variable_price_sync($variation_ids);
                            }
                        }
                        

                        
                    }
                    
                //Condition for handling the Batch Update for the registered Customers
                if($active_module == 'Customers' && $users!="")
                {
                    $table_name = "{$wpdb->prefix}usermeta";
                    $column_name = substr($column_name,1);
                    $user_ids = $users;
                    
                    if ( $column_name == 'billing_email') {
                        
                        $update_column = 'user_email';
                        
                        switch ($action_name) {
				case 'SET_TO' :
                                    $user_value = $update_column . ' = \'' . $text_cmp_value . '\'';
                                    break;
				
				case 'PREPEND' :
                                    $user_value = $update_column . ' = concat(\'' . $text_cmp_value . '\',' . $update_column . ')';
                                    break;
				
				case 'APPEND' :
                                    $user_value = $update_column . ' = concat(' . $update_column . ',\'' . $text_cmp_value . '\')';
                                    break;
                        }
                        
                        $query_users  = "UPDATE `{$wpdb->prefix}users` SET " . $user_value .
                                        "WHERE `id` in (" . $user_ids . ")";
                        
                        
                        $result_users = $wpdb->query ( $query_users );
                    }
                    
                    if ( $column_name == 'billing_country') {
                            $region = ( !empty($actions [$i] [4]) ) ? $wpdb->_real_escape ( $actions [$i] [4] ) : $wpdb->_real_escape ( $actions [$i] [2] );
                            $region_query = "UPDATE " . $wpdb->_real_escape($table_name) . " SET meta_value = '".$wpdb->_real_escape($region)."' WHERE user_id IN ( " . $wpdb->_real_escape($user_ids) . " ) AND meta_key = 'billing_state'";			
                            $result = $wpdb->query ( $region_query );
                            if ( $result < 1 ) {
                                    $updated_rows_cnt = _e('Batch Updation of Region not successful','smart-manager');
                            }
                    }
                    
                    $query        = "UPDATE " . $table_name . " SET " . $update_value .
                                     "WHERE `user_id` in (" . $user_ids . ") AND meta_key = '" . $column_name . "'";
                    $result = $wpdb->query ( $query );

                }
                
			} elseif ($table_name == "{$wpdb->prefix}term_relationships") {
				if ( $active_module != 'Products' ) {
					$term_taxonomy_id = get_term_taxonomy_id($text_cmp_value);
					$query = "UPDATE " . $table_name . " SET term_taxonomy_id = " . $wpdb->_real_escape($term_taxonomy_id) . " WHERE object_id IN (" . $selected_ids . ") ";
					$result = $wpdb->query ( $query );
					$order_ids = explode ( ',', $selected_ids );
					if ( $text_cmp_value == 'processing' || $text_cmp_value == 'completed' ) {
						foreach ( $order_ids as $order_id ) {
							$order = new WC_Order( $wpdb->_real_escape($order_id) );
							$order->update_status( $text_cmp_value );
						}
					}
				}
			}
			$update_value = '';
		}

   

		// Handled with a different ajax request
	if( $radioData == 2 && $flag == 1 ){
		$updated_rows_cnt = 'All';
	} else {
		$updated_rows_cnt = $idLength;		
	}
        
    //Clearing the transients to handle the proper functioning of the widgets
    if ($_POST['SM_IS_WOO21'] == "true" || $_POST['SM_IS_WOO22'] == "true") {
        wc_delete_product_transients();
    } else {
        $woocommerce->clear_product_transients();    
    }
        
	return $updated_rows_cnt;
}

if (isset ( $_POST ['cmd'] ) && $_POST ['cmd'] == 'batchUpdatesync' && isset($_POST['wooRunning']) && $_POST['wooRunning'] == 1) {
    $post_status_update = false;
    
    encoding_utf_8($_POST); // For converting the $_POST in correct encoding format
    
    
    $col_nm=explode('"',$_POST['values']);
    if($col_nm[1]=='price' || $col_nm[1]=='salePrice'){
        
        if( $_POST['radio'] == 2 && $_POST['flag'] == 1 ){
            $updated_rows_cnt=variable_price_sync(0);
        }
        else{
            $ids = json_decode ( stripslashes ( $_POST ['ids'] ) );
            $updated_rows_cnt=variable_price_sync( $ids );
        }
    }
    $encoded ['msg'] = __( "Success" );
    // ob_clean();

    while(ob_get_contents()) {
        ob_clean();
    }

    echo json_encode ( $encoded );

    exit;

}

if (isset ( $_POST ['cmd'] ) && $_POST ['cmd'] == 'batchUpdatesync1' && isset($_POST['wooRunning']) && $_POST['wooRunning'] == 1) {
    encoding_utf_8($_POST); // For converting the $_POST in correct encoding format
    $encoded ['msg'] = __( "Success" );
    // ob_clean();

    while(ob_get_contents()) {
        ob_clean();
    }

    echo json_encode ( $encoded );

    exit;
}

if (isset ( $_POST ['cmd'] ) && $_POST ['cmd'] == 'batchUpdateprice' && isset($_POST['wooRunning']) && $_POST['wooRunning'] == 1) {
    
    encoding_utf_8($_POST); // For converting the $_POST in correct encoding format
    
    function update_price ($updatecnt,$fupdatecnt,$data,$msg,$per,$perval) {
        global $wpdb;
        $flag_success=0;
        
        for ($i=$updatecnt;$i<$fupdatecnt;$i++) {
            $type = wp_get_object_terms( $data[$i], 'product_type', array('fields' => 'slugs') );
            $query = "SELECT post_parent FROM `{$wpdb->prefix}posts` WHERE ID =". $data[$i];
            $result_parent = $wpdb->get_col($query);

            if ($type[0] == 'simple' && $result_parent[0] == 0) {
                $flag_success = 1;
                $query = "SELECT meta_value FROM `{$wpdb->prefix}postmeta`
                        WHERE meta_key = '_sale_price' AND post_id = ". $data[$i];
                $result = $wpdb->get_col($query);
                $query = "UPDATE `{$wpdb->prefix}postmeta` SET meta_value =" . $result[0] . " WHERE meta_key = '_regular_price' AND post_id = ". $data[$i];
                $result1 = $wpdb->query ($query);
            }
            else if ($result_parent[0] > 0) {
                $flag_success = 1;
                $query = "SELECT meta_value FROM `{$wpdb->prefix}postmeta`
                        WHERE meta_key = '_sale_price' AND post_id = ". $data[$i];
                $result = $wpdb->get_col($query);
                $query = "UPDATE `{$wpdb->prefix}postmeta` SET meta_value =" . $result[0] . " WHERE meta_key = '_price' AND post_id = ". $data[$i];
                $result1 = $wpdb->query ($query);
            }
        }
        
        if ($flag_success == 1) {
            $result = true;
        }
        else{
            $result = false;
        }

        if ($result == true) {
                $encoded ['msg'] = $msg;
                $encoded ['nxtreq'] = $_POST ['part'];
                $encoded ['per'] = $per;
                $encoded ['val'] = $perval;
        }
        elseif ($result == false) {
                $encoded ['msg'] = $activeModule . __('s were not duplicated','smart-manager');
        }
        echo json_encode ( $encoded );

        exit;
    }
    
    if ($_POST['data'] == 'ALL') {
        $msg_no = 'ALL';
        $query = "SELECT id FROM `{$wpdb->prefix}posts` WHERE post_type IN ('product', 'product_variation')";
        $data = $wpdb->get_col($query);
    }
    else {
        $data = json_decode ( stripslashes ( $_POST ['data'] ) );
        $msg_no = sizeof($data);
    }
    
    $count = $_POST['count'];
    
    for ($i=1;$i<=$count;$i++) {
        if (isset ( $_POST ['part'] ) && $_POST ['part'] == $i) {
            
            $per = intval(($_POST ['part']/$count)*100); // Calculating the percentage for the display purpose
            $perval = $per/100;
            
            if ($per == 100) {
                $msg =  "<b>" . $msg_no . "</b> "  . __('Records Updated Successfully', 'smart-manager');
            }
            else{
                $msg = $per . "% Batch Update Completed";
            }
            update_price ($_POST ['updatecnt'], $_POST ['fupdatecnt'],$data,$msg,$per,$perval);
        }
    }
    
    
}


if (isset ( $_POST ['cmd'] ) && $_POST ['cmd'] == 'batchUpdate' && isset($_POST['wooRunning']) && $_POST['wooRunning'] == 1) {
    global $wpdb;

	$post_status_update = false;
	
        encoding_utf_8($_POST); // For converting the $_POST in correct encoding format
    
  //       if ( $_POST['radio'] == 2 && $_POST['flag'] == 1 ) {		
  //               $all_ids = get_all_ids($sql_results);
		// $ids = explode ( ',', $all_ids );
  //       }
  //       else {
  //           $ids = json_decode ( stripslashes ( $_POST ['ids'] ) );
  //       }
        
        if (!($_POST['radio'] == 2 && $_POST['flag'] == 1)) {
            $batch_update_ids = json_decode ( stripslashes ( $_POST ['ids'] ) );
        }
        
        if(isset ( $_POST ['part'] ) && $_POST ['part'] == 'initial') {

            // code to save the ids into options table if batch update for all products
            if ( $_POST['radio'] == 2 && $_POST['flag'] == 1) {

                //Code for getting the ids of all search result
                if (!empty($_POST['products_search_flag']) && $_POST['products_search_flag'] == "true" ) {
                    $search_results = $wpdb->get_results( "SELECT product_id FROM {$wpdb->prefix}sm_advanced_search_temp" );
                    $all_ids = get_all_ids($search_results);
                } else {
                    $all_ids = get_all_ids($sql_results);
                }

                $batch_update_ids = explode ( ',', $all_ids );
                update_option('sm_batch_update_all_ids',$batch_update_ids);
            }

            $count_batch = 0;
            if (sizeof($batch_update_ids) > 50) {
                for ($i=0;$i<sizeof($batch_update_ids);) {
                    $count_batch ++;
                    $i = $i+50;
                }
            }
            else{
                $count_batch = 1;
            }
            
            $encoded ['count_batch'] = $count_batch;
            $encoded ['total_records'] = sizeof($batch_update_ids);
            echo json_encode ( $encoded );
        }
        else {

            //code to get the all ids from the options table
            if ( $_POST['radio'] == 2 && $_POST['flag'] == 1) {
                $batch_update_ids = get_option('sm_batch_update_all_ids');
            }

            $count = $_POST['count'];
            
            for ($i=1;$i<=$count;$i++) {
                if (isset ( $_POST ['part'] ) && $_POST ['part'] == $i) {
                    for ($j=$_POST['updatecnt'],$k=0;$j<$_POST['fupdatecnt'];$j++,$k++) {
                        $ids_final [$k] = $batch_update_ids [$j];
                    }
                    $ids = '[\"'.implode ('\",\"',$ids_final).'\"]';
                    $_POST['ids'] = $ids;
                    $updated_rows_cnt = batchUpdateWoo( $_POST );
                    
                    $per = intval(($_POST ['part']/$count)*100); // Calculating the percentage for the display purpose
                    $perval = $per/100;

                    if ($per == 100) {

                        //code to delete the option used to store all ids
                        if ( $_POST['radio'] == 2 && $_POST['flag'] == 1) {
                            delete_option('sm_batch_update_all_ids');
                        }

                        if ($updated_rows_cnt == 1) {
                            $msg =  "<b>" . $updated_rows_cnt . "</b> "  . __('Record Updated Successfully', 'smart-manager');
                        }
                        else {
                            $msg =  "<b>" . $updated_rows_cnt . "</b> "  . __('Records Updated Successfully', 'smart-manager');
                        }
                        
                    }
                    else{
                        $msg = $per . "% Batch Update Completed";
                    }
                    $encoded ['nxtreq'] = $_POST['part'];
                    $encoded ['per'] = $per;
                    $encoded ['val'] = $perval;
                    $encoded ['msg'] = $msg;
                    
                	echo json_encode ( $encoded );
                }
            }
        }

        exit;
}


if (! function_exists( 'get_dashboard_combo_store' )) {
	function get_dashboard_combo_store() {
		global $wpdb, $current_user;

        if (!function_exists('wp_get_current_user')) {
            require_once (ABSPATH . 'wp-includes/pluggable.php'); // Sometimes conflict with SB-Welcome Email Editor
        }
    
    	$current_user = wp_get_current_user();
                if ( !isset( $current_user->roles[0] ) ) {
                    $roles = array_values( $current_user->roles );
                } else {
                    $roles = $current_user->roles;
                }
		$query = "SELECT option_value FROM {$wpdb->prefix}options WHERE option_name LIKE 'sm_".$roles[0]."_dashboard'";
		$results = $wpdb->get_results ( $query );
		$results = unserialize($results[0]->option_value);
		return $results;
	}
}

// For PHP version lower than 5.3.0
if (!function_exists('str_getcsv')) {
    function str_getcsv($input, $delimiter = ",", $enclosure = '"', $escape = "\\") {
        $fiveMBs = 5 * 1024 * 1024;
        $fp = fopen("php://temp/maxmemory:$fiveMBs", 'r+');
        fputs($fp, $input);
        rewind($fp);

        $data = fgetcsv($fp, 0, $delimiter, $enclosure); //  $escape only got added in 5.3.0

        fclose($fp);
        return $data;
    }
} 


//Function to export CSV file
if (! function_exists( 'export_csv_woo' )) {
	function export_csv_woo ( $active_module, $columns_header, $data ) {
        // global $tax_status, $visibility;

        $tax_status = array(
            'taxable'   => __('Taxable','smart-manager'),
            'shipping'  => __('Shipping only','smart-manager'),
            'none'      => __('None','smart-manager')
                    );

        $visibility = array(
                                'visible'   => __('Catalog & Search', 'smart-manager'),
                                'catalog'   => __('Catalog', 'smart-manager'),
                                'search'    => __('Search', 'smart-manager'),
                                'hidden'    => __('Hidden', 'smart-manager')
                            );

		foreach ( $columns_header as $key => $value ) {
			$getfield .= $value . ',';
		}

		$fields = substr_replace($getfield, '', -1);
		$each_field = array_keys( $columns_header );
		
		$csv_file_name = sanitize_title(get_bloginfo( 'name' )) . '_' . $active_module . '_' . gmdate('d-M-Y_H:i:s') . ".csv";
		
		foreach( (array) $data as $row ){
			for($i = 0; $i < count ( $columns_header ); $i++){
				if($i == 0) $fields .= "\n";
                if ( $each_field[$i] == '_tax_status' ) {
                    $row_each_field = $tax_status[$row[$each_field[$i]]];
                } elseif ( $each_field[$i] == '_visibility' ) {
                    $row_each_field = $visibility[$row[$each_field[$i]]];
                } elseif ( $each_field[$i] == '_shipping_method_title' ) { //Condition for handling shipping method for woo 2.0 and below
                    $row_each_field = (!empty($row[$each_field[$i]])) ? $row[$each_field[$i]] : $row['_shipping_method'];
                } else {
                    $row_each_field = $row[$each_field[$i]];
                }
                $array_temp = str_replace(array("\n", "\n\r", "\r\n", "\r"), "\t", $row_each_field); 
                $array = str_replace("<br>", "\n", $array_temp);
				$array = str_replace('"', '""', $array);
				$array = str_getcsv ( $array , ",", "\"" , "\\");
				$str = ( $array && is_array( $array ) ) ? implode( ', ', $array ) : '';
				$fields .= '"'. $str . '",'; 
			}			
			$fields = substr_replace($fields, '', -1); 
		}
		$upload_dir = wp_upload_dir();
		$file_data = array();
		$file_data['wp_upload_dir'] = $upload_dir['path'] . '/';
		$file_data['file_name'] = $csv_file_name;
		$file_data['file_content'] = $fields;
		return $file_data;
	}
}

?>
