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


if ( ! defined('ABSPATH') ) {
    include_once (find_wp_load_path()  . '/wp-load.php');
}

include_once (ABSPATH . 'wp-includes/wp-db.php');
include_once (ABSPATH . 'wp-includes/functions.php');
include_once (WP_PLUGIN_DIR . '/wp-e-commerce/wpsc-core/wpsc-functions.php');
require_once (WP_PLUGIN_DIR . '/wp-e-commerce/wpsc-core/wpsc-constants.php');
include_once (WP_PLUGIN_DIR . '/wp-e-commerce/wpsc-includes/purchaselogs.class.php');
require_once (WP_PLUGIN_DIR . '/wp-e-commerce/wpsc-admin/includes/product-functions.php');		// For creating product variations

global $wp_version;

if (version_compare ( $wp_version, '4.0', '>=' )) {
    global $locale;
    load_textdomain( 'smart-manager', WP_PLUGIN_DIR . '/' . dirname( dirname(plugin_basename( __FILE__ ))) . '/languages/smart-manager-' . $locale . '.mo' );
} else {
    load_textdomain( 'smart-manager', WP_PLUGIN_DIR . '/' . dirname(dirname(plugin_basename( __FILE__ ))) . '/languages/smart-manager-' . WPLANG . '.mo' );
}

// Function to handle the encoding into UTF - 8 format
if ( !function_exists( 'encoding_utf_8' ) ) {
    function encoding_utf_8($post) {
	$_POST = $post;     // Fix: PHP 5.4
        //For encoding the string in UTF-8 Format        
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



function print_packing_slip_data($input_data,$form_data,$purch_data,$rekeyed_input,$purchase_id_value){
	global $wpdb;
	
	if($input_data != null) {
		foreach($form_data as $form_field) {
			switch($form_field['type']) {
				case 'country':

					$delivery_region_count = $wpdb->get_var("SELECT COUNT(`regions`.`id`) FROM `".WPSC_TABLE_REGION_TAX."` AS `regions` INNER JOIN `".WPSC_TABLE_CURRENCY_LIST."` AS `country` ON `country`.`id` = `regions`.`country_id` WHERE `country`.`isocode` IN('".$wpdb->_real_escape( $purch_data[$purchase_id_value]['billing_country'])."')");

					if(is_numeric($purch_data[$purchase_id_value]['billing_region']) && ($delivery_region_count > 0))
					echo "	<tr><td>".__('State', 'wpsc').":</td><td> ".wpsc_get_region($purch_data[$purchase_id_value]['billing_region'])."</td></tr>\n\r";

					echo "	<tr><td>".wp_kses($form_field['name'], array() ).":</td><td>".htmlentities(stripslashes($rekeyed_input[$purchase_id_value][$form_field['id']]['value']), ENT_QUOTES, 'UTF-8')."</td></tr>\n\r";
					break;

				case 'delivery_country':

					if(is_numeric($purch_data[$purchase_id_value]['shipping_region']) && ($delivery_region_count > 0))
					echo "	<tr><td>".__('State', 'wpsc').":</td><td> ".wpsc_get_region($purch_data[$purchase_id_value]['shipping_region'])."</td></tr>\n\r";

					echo "	<tr><td>".wp_kses($form_field['name'], array() ).":</td><td>".htmlentities(stripslashes($rekeyed_input[$purchase_id_value][$form_field['id']]['value']), ENT_QUOTES, 'UTF-8')."</td></tr>\n\r";
					break;

				case 'heading':

					if($form_field['name'] == "Hidden Fields")
					continue;
					else
					echo "<tr class='heading'><td colspan='2'><strong><u>".wp_kses($form_field['name'], array())."</u>:</strong></td></tr>\n\r";
					break;

				default:
					if( $form_field['name'] == "Cupcakes") {
						parse_str($rekeyed_input[$purchase_id_value][$form_field['id']]['value'], $cupcakes );

						foreach( $cupcakes as $product_id => $quantity ) {
							$product = get_post($product_id);
							$string .= "(".$quantity.") ".$product->post_title.", ";
						}

						$string = rtrim($string, ", ");
						echo "	<tr><td>".wp_kses($form_field['name'], array() ).":</td><td>".htmlentities(stripslashes($string), ENT_QUOTES, 'UTF-8')."</td></tr>\n\r";
					} else {
						if ($form_field['name']=="State" && !empty($purch_data[$purchase_id_value]['billing_region']) || $form_field['name']=="State" && !empty($purch_data[$purchase_id_value]['billing_region']))
						echo "";
						else
						echo "	<tr><td>".wp_kses($form_field['name'], array() ).":</td><td>".
						( isset( $rekeyed_input[$purchase_id_value][$form_field['id']] ) ? htmlentities(stripslashes($rekeyed_input[$purchase_id_value][$form_field['id']]['value']), ENT_QUOTES, 'UTF-8') : '' ).
						"</td></tr>\n\r";

					}
					break;
			}

		}
	} else {
		echo "	<tr><td>".__('Name', 'wpsc').":</td><td> ".$purch_data[$purchase_id_value]['firstname']." ".$purch_data[$purchase_id_value]['lastname']."</td></tr>\n\r";
		echo "	<tr><td>".__('Address', 'wpsc').":</td><td> ".$purch_data[$purchase_id_value]['address']."</td></tr>\n\r";
		echo "	<tr><td>".__('Phone', 'wpsc').":</td><td> ".$purch_data[$purchase_id_value]['phone']."</td></tr>\n\r";
		echo "	<tr><td>".__('Email', 'wpsc').":</td><td> ".$purch_data[$purchase_id_value]['email']."</td></tr>\n\r";
	}

	if ( 2 == get_option( 'payment_method' ) ) {
		$gateway_name = '';
		$nzshpcrt_gateways = nzshpcrt_get_gateways();

		foreach( $nzshpcrt_gateways as $gateway ) {
			if ( $purch_data[$purchase_id_value]['gateway'] != 'testmode' ) {
				if ( $gateway['internalname'] == $purch_data[$purchase_id_value]['gateway'] ) {
					$gateway_name = $gateway['name'];
				}
			} else {
				$gateway_name = __('Manual Payment', 'wpsc');
			}
		}
	}
}

//Print Invoice Function
    function smart_manager_print_logo() {
      if (get_option('smart_manager_company_logo') != '') {
        return '<img src="' . get_option('smart_manager_company_logo') . '" />';
      }
    }


function get_packing_slip( $purchase_ids, $purchase_id_arr ) {
        global $purchlogitem;
        
	if (!empty($purchase_ids) && !empty($purchase_id_arr)){
		
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

                            #wrapper {
                                    margin:0 auto;
                                    width:95%;
                                    page-break-after: always;
                            }

                            #header {
                            }

                            #customer {
                                    overflow:hidden;
                            }

                            #customer .shipping, #customer .billing {
                                    float: left;
                                    width: 50%;
                            }

                            table {
                                    border:1px solid #000;
                                    border-collapse:collapse;
                                    margin-top:1em;
                                    width:100%;
                            }

                            th {
                                    background-color:#efefef;
                                    text-align:center;
                            }

                            th, td {
                                    padding:5px;
                            }

                            td {
                                    text-align:center;
                            }

                            #cart-items td.amount {
                                    text-align:right;
                            }

                            td, tbody th {
                                    border-top:1px solid #ccc;
                            }
                            th.column-total {
                                    width:90px;
                            }
                            th.column-shipping {
                                    width:120px;
                            }
                            th.column-price {
                                    width:100px;
                            }
                    </style>
                <?php
                if ( !class_exists( 'wpsc_purchaselogs_items' ) ) {
                    require_once WP_PLUGIN_DIR . '/wp-e-commerce/wpsc-includes/purchaselogs.class.php';
                }
		foreach ($purchase_id_arr as $purchase_id_value){
                    
                    $purchlogitem = new wpsc_purchaselogs_items( (int)$purchase_id_value );
                    
                    ?>
                        <div id="wrapper">
                              <?php echo smart_manager_print_logo();?>
                            <div id="header" style="margin-top:-0.8em;">
                                    <h1>
                                        
                                            <?php echo get_bloginfo('name'); ?><br />
                                            <span><?php printf( esc_html__( 'Packing Slip for Order #%s', 'wpsc' ), $purchase_id_value ); ?></span>
                                    </h1>
                            </div>
                            <div id="customer">
                                    <div class="shipping">
                                            <h2><?php echo esc_html_x( 'Ship To:', 'packing slip', 'wpsc' ); ?></h2>
                                            <strong><?php echo wpsc_display_purchlog_shipping_name(); ?></strong><br />
                                            <?php echo wpsc_display_purchlog_shipping_address(); ?><br />
                                            <?php echo wpsc_display_purchlog_shipping_city(); ?><br />
                                            <?php echo wpsc_display_purchlog_shipping_state_and_postcode(); ?><br />
                                            <?php echo wpsc_display_purchlog_shipping_country(); ?><br />
                                    </div>
                                    <div class="billing">
                                            <h2><?php echo esc_html_x( 'Bill To:', 'packing slip', 'wpsc' ); ?></h2>
                                            <strong><?php echo wpsc_display_purchlog_buyers_name(); ?></strong><br />
                                            <?php echo wpsc_display_purchlog_buyers_address(); ?><br />
                                            <?php echo wpsc_display_purchlog_buyers_city(); ?><br />
                                            <?php echo wpsc_display_purchlog_buyers_state_and_postcode(); ?><br />
                                            <?php echo wpsc_display_purchlog_buyers_country(); ?><br />
                                    </div>
                            </div>
                            <table id="order">
                                    <thead>
                                            <tr>
                                                    <th><?php echo esc_html_x( 'Order Date', 'packing slip', 'wpsc' ); ?></th>
                                                    <th><?php echo esc_html_x( 'Order ID', 'packing slip', 'wpsc' ); ?></th>
                                                    <th><?php echo esc_html_x( 'Shipping Method', 'packing slip', 'wpsc' ); ?></th>
                                                    <th><?php echo esc_html_x( 'Payment Method', 'packing slip', 'wpsc' ); ?></th>
                                            </tr>
                                    </thead>
                                    <tbody>
                                            <tr>
                                                    <td><?php echo wpsc_purchaselog_details_date(); ?></td>
                                                    <td><?php echo wpsc_purchaselog_details_purchnumber(); ?></td>
                                                    <td><?php echo wpsc_display_purchlog_shipping_method(); ?></td>
                                                    <td><?php echo wpsc_display_purchlog_paymentmethod(); ?></td>
                                            </tr>
                                    </tbody>
                            </table>
                            <table id="cart-items" class="widefat" cellspacing="0">
                                    <thead>
                                            <tr>
                                                    <th scope='col' id='title' class='manage-column column-title'  style=""><?php _e( 'Item Name', 'wpsc' ); ?></th>
                                                    <th scope='col' id='sku' class='manage-column column-sku'  style=""><?php _e( 'SKU', 'wpsc' ); ?></th>
                                                    <th scope='col' id='quantity' class='manage-column column-quantity'  style=""><?php _e( 'Quantity', 'wpsc' ); ?></th>
                                                    <th scope='col' id='price' class='manage-column column-price'  style=""><?php _e( 'Price', 'wpsc' ); ?></th>
                                                    <th scope='col' id='shipping' class='manage-column column-shipping'  style=""><?php _e( 'Item Shipping', 'wpsc' ); ?></th>
                                                    <th scope='col' id='tax' class='manage-column column-tax'  style=""><?php _e( 'Item Tax', 'wpsc' ); ?></th>
                                                    <th scope='col' id='total' class='manage-column column-total'  style=""><?php _e( 'Item Total', 'wpsc' ); ?></th>

                                                    <?php $cols = 5;    // this is counted as ( count of th - 2 )  ?>
                                            </tr>
                                    </thead>

                                    <tbody>
                                            <?php

                                                while( wpsc_have_purchaselog_details() ) : wpsc_the_purchaselog_item(); ?>
                                                  <tr>
                                                     <td><?php echo wpsc_purchaselog_details_name(); ?></td> <!-- NAME! -->
                                                     <td><?php echo wpsc_purchaselog_details_SKU(); ?></td> <!-- SKU! -->
                                                     <td><?php echo wpsc_purchaselog_details_quantity(); ?></td> <!-- QUANTITY! -->
                                                     <td>
                                                        <?php
                                                            echo wpsc_currency_display( wpsc_purchaselog_details_price() );
                                                            do_action( 'wpsc_additional_sales_amount_info', wpsc_purchaselog_details_id() );
                                                        ?>
                                                     </td> <!-- PRICE! -->
                                                     <td><?php echo wpsc_currency_display( wpsc_purchaselog_details_shipping() ); ?></td> <!-- SHIPPING! -->
                                                     <?php if( wpec_display_product_tax() ): ?>
                                                        <td><?php echo wpsc_currency_display( wpsc_purchaselog_details_tax() ); ?></td> <!-- TAX! -->
                                                     <?php endif; ?>
                                                     <!-- <td><?php echo wpsc_currency_display( wpsc_purchaselog_details_discount() ); ?></td> --> <!-- DISCOUNT! -->
                                                     <td class="amount"><?php echo wpsc_currency_display( wpsc_purchaselog_details_total() ); ?></td> <!-- TOTAL! -->
                                                  </tr>
                                                  <?php
                                                endwhile;
                                            

                                            ?>

                                            <tr class="wpsc_purchaselog_start_totals">
                                                    <td colspan="<?php echo $cols; ?>">
                                                            <?php if ( wpsc_purchlog_has_discount_data() ): ?>
                                                                    <?php esc_html_e( 'Coupon Code', 'wpsc' ); ?>: <?php echo wpsc_display_purchlog_discount_data(); ?>
                                                            <?php endif; ?>
                                                    </td>
                                                    <th><?php esc_html_e( 'Discount', 'wpsc' ); ?> </th>
                                                    <td class="amount"><?php echo wpsc_display_purchlog_discount(); ?></td>
                                            </tr>

                                            <?php if( ! wpec_display_product_tax() ): ?>
                                                    <tr>
                                                            <td colspan='<?php echo $cols; ?>'></td>
                                                            <th><?php esc_html_e( 'Taxes', 'wpsc' ); ?> </th>
                                                            <td class="amount"><?php echo wpsc_display_purchlog_taxes(); ?></td>
                                                    </tr>
                                            <?php endif; ?>

                                            <tr>
                                                    <td colspan='<?php echo $cols; ?>'></td>
                                                    <th><?php esc_html_e( 'Shipping', 'wpsc' ); ?> </th>
                                                    <td class="amount"><?php echo wpsc_display_purchlog_shipping(); ?></td>
                                            </tr>
                                            <tr>
                                                    <td colspan='<?php echo $cols; ?>'></td>
                                                    <th><?php esc_html_e( 'Total', 'wpsc' ); ?> </th>
                                                    <td class="amount"><?php echo wpsc_display_purchlog_totalprice(); ?></td>
                                            </tr>
                                    </tbody>
                            </table>
                        </div>

                    <?php
                    
		}
                
                exit();
	}
}



function perform_action($action_name, $old_value, $params) { // perform update actions & returns the updated value
	switch ($action_name) {
		case "SET_TO" :
			$new_value = $params ['value'];
			break;
		
		case "INCREASE_BY_NUMBER" :
			$new_value = $old_value + $params ['value'];
			break;
		
		case "DECREASE_BY_NUMBER" :
			$new_value = $old_value - $params ['value'];
			break;
		
		case "INCREASE_BY_%" :
			$new_value = $old_value * (1 + ((number_format($params ['value'] / 100,2,'.', ''))));
			break;
		
		case "DECREASE_BY_%" :
			$new_value = $old_value * (1 - ((number_format($params ['value'] / 100,2,'.', ''))));
			break;
		
		case "PREPEND" :
			$new_value = $new_value . "" . $old_value;
			break;
		
		case "APPEND" :
			$new_value = $old_value . "" . $new_value;
			break;
		
		case 'YES' :
			$new_value = '1';
			break;
		
		case 'NO' :
			$new_value = '0';
			break;
	}
	return ( string ) $new_value;
}



function update_record_metadata($update_column, &$wpsc_product_metadata, $params, $action_name) {
	foreach ( $wpsc_product_metadata as $key => $value ) {
		switch ($update_column) {

			case 'weight' :
				if (isset($params['unit']) && !empty($params['unit']))
				$wpsc_product_metadata[$key]['weight_unit'] 	  = $params['unit'];
				$wpsc_product_metadata[$key]['display_weight_as'] = $params['unit'];
				
				eval ( '$wpsc_product_metadata[$key][weight] = perform_action($action_name,$wpsc_product_metadata[$key][weight], $params);' );
				
				// insert the product weight in pound unit since wp-e-commerce does the same.
				$wpsc_product_metadata[$key][weight] = wpsc_convert_weight($wpsc_product_metadata[$key][weight], $wpsc_product_metadata[$key]['weight_unit'], "pound",true);
				break;
			
			case 'height' :
				if (isset($params['unit']) && !empty($params['unit']))
				$wpsc_product_metadata[$key]['dimensions']['height_unit'] = $params['unit'];

				eval ( '$wpsc_product_metadata[$key][dimensions][height] = perform_action($action_name,$wpsc_product_metadata[$key][dimensions][height], $params);' );
				break;

			case 'width' :
				if (isset($params['unit']) && !empty($params['unit']))
				$wpsc_product_metadata[$key]['dimensions']['width_unit'] = $params['unit'];

				eval ( '$wpsc_product_metadata[$key][dimensions][width] = perform_action($action_name,$wpsc_product_metadata[$key][dimensions][width], $params);' );
				break;

			case 'length' :
				if (isset($params['unit']) && !empty($params['unit']))
				$wpsc_product_metadata[$key]['dimensions']['length_unit'] = $params['unit'];

				eval ( '$wpsc_product_metadata[$key][dimensions][length] = perform_action($action_name,$wpsc_product_metadata[$key][dimensions][length], $params);' );
				break;

            //Added for wpec v3.8.8.14 and above
            case 'dimension_unit' :
                if (isset($params['unit']) && !empty($params['unit']))
                $wpsc_product_metadata[$key]['dimension_unit'] = $params['unit'];

                break;

			case 'local' :
				eval ( '$wpsc_product_metadata[$key][shipping][local] = perform_action($action_name,$wpsc_product_metadata[$key][shipping][local], $params);' );
				break;

			case 'international' :
				eval ( '$wpsc_product_metadata[$key][shipping][international] = perform_action($action_name,$wpsc_product_metadata[$key][shipping][international], $params);' );
				break;

			case 'no_shipping' :
				eval ( '$wpsc_product_metadata[$key][no_shipping] = perform_action($action_name,$wpsc_product_metadata[$key][no_shipping], $params);' );
				break;

			case 'quantity_limited' :
				eval ( '$wpsc_product_metadata[$key][quantity_limited] = perform_action($action_name,$wpsc_product_metadata[$key][quantity_limited], $params);' );
				break;

			case 'unpublish_when_none_left' :
				eval ( '$wpsc_product_metadata[$key][unpublish_when_none_left] = perform_action($action_name,$wpsc_product_metadata[$key][unpublish_when_none_left], $params);' );
				break;
		}
	}
}


//Function for collecting all term_taxonomy_id in an array
function collect_term_taxonomy_ids( $actions, $new_term_taxonomy_ids = array() ) {
	$new_term_taxonomy_ids['create'] = array();
	$new_term_taxonomy_ids['remove'] = array();
	foreach ( $actions as $action ) {
		if ( $action->action == 'ADD_TO' || $action->action == 'SET_TO' ) {
			$new_term_taxonomy_ids['create'][] = $action->colValue;
		} elseif ( $action->action == 'REMOVE_FROM' ) {
			$new_term_taxonomy_ids['remove'][] = $action->colValue;
		}
	}
	return $new_term_taxonomy_ids;
}

// Function for getting variation values of which new variation will be created
function get_variation_values( $term_hierarchys, $term_taxonomy_ids, $new_term_taxonomy_ids, $edit_var_val = array() ) {
	// For creating hierarchy of terms
	foreach ( $term_hierarchys as $key => $term_hierarchy ) {
		if ( is_array( $term_hierarchy ) ) {
			$child_array = array();
			foreach ( $term_hierarchy as $child_item ) {
				if ( ( ( in_array( $key, $term_taxonomy_ids ) && in_array( $key, $new_term_taxonomy_ids['create'] ) ) || ( in_array( $child_item, $term_taxonomy_ids ) && in_array( $key, $term_taxonomy_ids ) ) || in_array( $child_item, $term_taxonomy_ids ) ) && ! in_array( $key, $new_term_taxonomy_ids['remove'] ) ) {
					$child_array[$child_item] = 1;
				}
			}
			$edit_var_val[$key] = $child_array;
		} else {
			if ( in_array( $term_hierarchy, $term_taxonomy_ids ) ) {
				$edit_var_val[$term_hierarchy] = 1;
			}
		}
		if ( empty( $edit_var_val[$key] ) ) {
			unset( $edit_var_val[$key] );
		}
	}
	return $edit_var_val;
}

// Function to update variations
function sm_wpsc_update_variations( $product_id, $edit_var_val ) {
	$product_type_object = get_post_type_object('wpsc-product');
	
	//Setup postdata
	$post_data = array();
	$post_data['edit_var_val'] = ( count( $edit_var_val ) > 0 ) ? $edit_var_val : '';
	$post_data['description'] = '';
	$post_data['additional_description'] = '';
	$post_data['name'] = get_the_title( $product_id );

	$_REQUEST["product_id"] = $product_id;
	wpsc_edit_product_variations( $product_id, $post_data );
}

// Function to process product's variations ( creation & removal )
function process_product_variation( $actions, $ids ) {
	$new_term_taxonomy_ids = collect_term_taxonomy_ids( $actions );					// for collecting all term_taxonomy_id in an array
	$term_hierarchy = _get_term_hierarchy('wpsc-variation');
	foreach ( $ids as $product_id ) {
		$old_term_taxonomy_ids = wp_get_object_terms( $product_id, 'wpsc-variation', array('fields' => 'ids') );
		$term_taxonomy_ids = array_diff( array_unique( array_merge( $old_term_taxonomy_ids, $new_term_taxonomy_ids['create'] ) ), (array) $new_term_taxonomy_ids['remove'] );
		$edit_var_val = get_variation_values( $term_hierarchy, $term_taxonomy_ids, $new_term_taxonomy_ids );
        sm_wpsc_update_variations( $product_id, $edit_var_val );
	}
}

// Batch Update for 3.8
function batchUpdateWpsc($post) {
    
	global $post_status_update, $table_prefix, $wpdb;
    $_POST = $post;     // Fix: PHP 5.4

	if (! empty ( $wpdb->prefix ))
		$wp_table_prefix = $wpdb->prefix;

	$ids = json_decode ( stripslashes ( $_POST ['ids'] ) );
    $fupdatecount_value = json_decode( stripslashes ( $_POST ['fupdatecnt'] ) ); // code to handle the message for different number of max. records
	
        if ($_POST ['activeModule'] == 'Products') {
	
		$active_module = 'Products';
		$actions       = json_decode ( $_POST ['updateDetails'] );
		$sel_records   = json_decode ( $_POST ['selected'] );
		$radioData     = $wpdb->_real_escape ( $_POST['radio'] );
		$flag	       = $wpdb->_real_escape ( $_POST['flag'] );

		$result = data_for_insert_update ( $_POST ); 			//save new products and update modified products before doing batch update.
		
		// create an array of ids (newly added products & modified products)
		$count = 0;
		for($i = 0; $i < count ( $ids ); $i ++) {
			if (strstr ( $ids [$i], 'ext-record' ) != '') {
				$ids_temp [$i] = $result ['productId'] [$count];
				$count ++;
			}
		}
		
		if(isset($sel_records) && $sel_records != null) {//collectin the variation product's id
			foreach ($sel_records as $record){
				if($record->post_parent != 0 )
					$children_ids[] = $record->id;
			    else
					$parent_ids[] = $record->id;
			}
		}

        $variation_action = array();
        foreach ( $actions as $action ) {
            if ( substr( $action->colFilter, 0, 9 ) == 'Variation' ) {
                $variation_action[] = $action;
            }
        }

        if ( !empty( $parent_ids ) && !empty( $variation_action ) ) {
            process_product_variation( $actions, $parent_ids );			// Function call to process product variations
        }

	} else {
		if ($_POST ['activeModule'] == 'Customers') {
			$active_module = 'Customers';
			$result = update_customers ( $_POST );
		}else {
			$active_module = 'Orders';
			$result = data_for_update_orders ( $_POST );
		}
		$actions = json_decode ( $_POST ['values'] );
	}
	
	//$idLength = count ( $ids );
    $idLength  = $fupdatecount_value;
    $selected_ids = $wpdb->_real_escape ( implode ( ',', $ids ) );
	$length = count ( $actions );
	
        $query = "SELECT meta_id,meta_value FROM `{$wp_table_prefix}postmeta`
                   WHERE `meta_key` = '_wpsc_product_metadata'
                   AND `post_id` in (" . $wpdb->_real_escape ( implode ( ',', $ids ) ) . ")";

	$records  = $wpdb->get_results ( $query );
	$num_rows = $wpdb->num_rows;
	if ($num_rows > 0) {
		foreach ( $records as &$record ) {
			$wpsc_product_metadata [$record->meta_id] = unserialize ( $record->meta_value );
		}
	}	

        //Function to handle the batch update for Registered Customers
        function update_customer_user($action_name,$old_value,$new_value) {
            switch ($action_name) {
			case 'SET_TO' :
                            $update_value = $new_value;
                            break;
			
			case 'PREPEND' :
                            $update_value = $new_value . $old_value;
                            break;
                        
                        case 'APPEND' :
                            $update_value = $old_value . $new_value;
                            break;
            }
            
            return $update_value;
        }

	// Building queries
	for($i = 0; $i < $length; $i ++) {


		if($active_module == 'Products'){
                        if ( substr( $actions [$i]->colFilter, 0, 9 ) == 'Variation' ) continue;			// To skip updation of Variation
			$action_name 	  = $wpdb->_real_escape ( $actions [$i]->action );
			$column_name 	  = "{$wpdb->_real_escape ( $actions [$i]->colName )}";
			$update_column    = ($actions [$i]->updateColName != '') ? "{$wpdb->_real_escape ( $actions [$i]->updateColName )}" : "{$wpdb->_real_escape ( $actions [$i]->colName )}";			
			$table_name 	  = "`{$wpdb->_real_escape ( $actions [$i]->tableName )}`";			
			$col_filter       = "{$wpdb->_real_escape ( $actions [$i]->colFilter )}";			
			$drop_down3_value = "{$wpdb->_real_escape ( $actions [$i]->unit )}"; //@todo for state code for customers		
			$col_id            = $wpdb->_real_escape ( $actions [$i]->colId );	
			$is_category = (strstr($col_id,'group') != '') ? true : false;
			
                        if ( $update_column == 'thumbnail' ) {
                            for ( $j=0;$j<sizeof($ids);$j++ ) {
                                update_post_meta($ids[$j], '_thumbnail_id', $actions [$i]->colValue );
                            }
                        }
                        
			$row_filter = '';
			$filter_col = '';
			if ($col_filter != '') {
				$col_filter_arr = explode ( ':', $col_filter );
				$filter_col = " {$wpdb->_real_escape ( $col_filter_arr[0] )} ";
				$row_filter = $wpdb->_real_escape ( $col_filter_arr [1] );
			}
			$text_cmp_value = $wpdb->_real_escape ( $actions [$i]->colValue );
		}else{

			$actions [$i] [0] = explode ( ',', $actions [$i] [0] );
			$actions_index = 0;
			foreach ( $actions [$i] [0] as $action ) { // trimming the field names & table names
				$actions [$i] [0] [$action_index] = trim ( $actions [$i] [0] [$action_index] );
				$action_index ++;
			}

			// getting values from POST
			$is_category      = $wpdb->_real_escape ( ( integer ) $actions [$i] [0] [0] );
			$action_name      = $wpdb->_real_escape (trim( $actions [$i] [1]) );
			$update_column    = $wpdb->_real_escape (trim( $actions [$i] [0] [0] ));
			$table_name       = $wpdb->_real_escape (trim( $actions [$i] [0] [1] ));
			$meta_value       = $wpdb->_real_escape (trim( $actions [$i] [0] [2] ));	// form_id
			$drop_down3_value = $wpdb->_real_escape (trim( $actions [$i] [3] ));	    // countryID
			$drop_down4_value = $wpdb->_real_escape (trim( $actions [$i] [4] )); 		// region_id
			$country_reg      = array(); 				//reinitializaton
			$text_cmp_value = (trim($actions [$i] [2]) == '') ? $drop_down3_value : $wpdb->_real_escape(trim($actions [$i] [2]));
			
			if ($table_name == WPSC_TABLE_SUBMITED_FORM_DATA) {
                                
                                if($active_module == 'Orders'){
				$log_ids = $wpdb->_real_escape ( implode ( ',', $ids ) );
			}
                                else {
                                    $selected_objects = json_decode ( $_POST ['selected'] );

                                    $k=0;$l=0;
                                    foreach ($selected_objects as $obj) {
                                        if ($obj->id > 0) {
                                            $user_ids[$l] = $obj->id;
                                            $l++;
		}
                                        else {
                                            $log_id[$k] = $obj->last_order_id;
                                            $k++;
                                        }

                                    }

                                    if (!(is_null($log_id))) {
                                        $log_ids = implode (",", $log_id);
                                    }

                                    if (!(is_null($user_ids))) {
                                        $log_users = implode (",", $user_ids);

                                        $query = "SELECT users.ID,users.user_email, GROUP_CONCAT(usermeta.meta_value 
                                                            ORDER BY usermeta.umeta_id SEPARATOR '###' ) AS name
                                               FROM $wpdb->users AS users
                                                   JOIN $wpdb->usermeta  AS usermeta ON usermeta.user_id = users.id
                                               WHERE usermeta.meta_key IN ('first_name','last_name','wpshpcrt_usr_profile')
                                                    AND users.ID IN ($log_users)
                                               GROUP BY users.id DESC";
                                        $reg_user = $wpdb->get_results ($query ,'ARRAY_A');

                                        for ($k=0;$k<sizeof($reg_user);$k++) {
                                            $user_details = explode("###",$reg_user[$k]['name']);
                                            if ($meta_value == 2) {
                                                $old_value = $user_details[0];
                                                $update = $text_cmp_value;
                                                $updated_value = update_customer_user($action_name,$old_value,$update);
                                                $query_user = "UPDATE $wpdb->usermeta SET meta_value='" .$updated_value. "'
                                                                    WHERE meta_key='first_name' AND user_id =". $reg_user[$k]['ID'];
                                                $result_user = $wpdb->query($query_user);
                                            } 
                                            else if ($meta_value == 3) {
                                                $old_value = $user_details[1];
                                                $update = $text_cmp_value;
                                                $updated_value = update_customer_user($action_name,$old_value,$update);
                                                $query_user = "UPDATE $wpdb->usermeta SET meta_value='" .$updated_value. "'
                                                                    WHERE meta_key='last_name'AND user_id =". $reg_user[$k]['ID'];
                                                $result_user = $wpdb->query($query_user);
                                            }
                                            else if ($meta_value == 9) {
                                                $old_value = $reg_user[$k]['user_email'];
                                                $update = $text_cmp_value;
                                                $updated_value = update_customer_user($action_name,$old_value,$update);                                            $query_user = "UPDATE $wpdb->users SET user_email='" .$updated_value. "'
                                                                    WHERE id =". $reg_user[$k]['ID'];
                                                $result_user = $wpdb->query($query_user);
                                            }
                                            else {
                                                $old_value = unserialize($user_details[2]);
                                                $update = $text_cmp_value;

                                                // Code for handling the batch update for Country of the Customer
                                                if ($meta_value == 7) {
                                                    $old_country = $old_value[$meta_value][0];
                                                    $updated_value = update_customer_user($action_name,$old_country,$update);
                                                    $old_value[$meta_value][0] = $updated_value;
                                                    $old_value[6] = $drop_down4_value;
                                                }
                                                else {
                                                    $updated_value = update_customer_user($action_name,$old_value[$meta_value],$update);
                                                    $old_value[$meta_value] = $updated_value;
                                                }
                                                $updated_final = serialize($old_value);
                                                $query_user = "UPDATE $wpdb->usermeta SET meta_value='" .$updated_final. "'
                                                                    WHERE meta_key='wpshpcrt_usr_profile'AND user_id =". $reg_user[$k]['ID'];
                                                $result_user = $wpdb->query($query_user);
                                                }
                                        }
                                    }
                                }
                            }
		}

                $flag_query = 0;
		switch ($action_name) {
			case 'SET_TO' :
				if ($row_filter != '_wpsc_product_metadata') { //version 3.8
					$update_value [] = $update_column . ' = \'' . $text_cmp_value . '\''; //is array for weight
				}
				if($is_category) {
                                        $query = "DELETE FROM " . $table_name . " WHERE `object_id` in (".$wpdb->_real_escape(implode(',',$ids)).")";
                                        $sql_result = $wpdb->query ( $query );

                                        $sub_query = array ();
                                        for($j = 0; $j < count ( $ids ); $j ++) {
                                                $sub_query [] = "(" . $wpdb->_real_escape( $ids [$j] ) . "," . $text_cmp_value . ")";
                                        }
                                        $sub_query = implode ( ',', $sub_query );
                                        $query = "INSERT INTO " . $table_name . " (object_id,`$update_column`) VALUES " . $sub_query;
                                        $sql_result = $wpdb->query ( $query );
                                }
				break;
			
			case 'PREPEND' :
				if ($row_filter != '_wpsc_product_metadata') { //version 3.8
					$update_value = $update_column . ' = concat(\'' . $text_cmp_value . '\',' . $update_column . ')';
				}
				break;
			
			case 'APPEND' :
				if ($row_filter != '_wpsc_product_metadata') { //version 3.8
					$update_value = $update_column . ' = concat(' . $update_column . ',\'' . $text_cmp_value . '\')';
				}
				break;
			
			case 'INCREASE_BY_NUMBER' :
				if ($row_filter != '_wpsc_product_metadata') { //version 3.8
					$update_value [] = $update_column . ' = ' . $update_column . '+' . $text_cmp_value;
				}
				break;
			
			case 'DECREASE_BY_NUMBER' :
				if ($row_filter != '_wpsc_product_metadata') { //version 3.8
					$update_value [] = $update_column . ' = ' . $update_column . '-' . $text_cmp_value;
				}
				break;
			
			case 'INCREASE_BY_%' :
				if ($row_filter != '_wpsc_product_metadata') { //version 3.8
					$update_value [] = $update_column . ' = ' . $update_column . '+' . ($update_column . '*' . (number_format($text_cmp_value / 100,2,'.', '')));
				}
				break;
			
			case 'DECREASE_BY_%' :
				if ($row_filter != '_wpsc_product_metadata') { //version 3.8
					$update_value [] = $update_column . ' = ' . $update_column . '-' . ($update_column . '*' . (number_format($text_cmp_value / 100,2,'.', '')));
				}
				break;
			
			case 'YES' :
				if ($column_name == 'post_status'){
					$update_value = $update_column . ' = \'publish\'';
				}elseif($column_name == '_wpsc_stock'){
					$update_value = $update_column . ' = 0';				
				}else{
					$update_value = $update_column . ' = 1';
				}
				break;

			case 'NO' :
				if ($column_name == 'post_status'){
					$update_value = $update_column . ' = \'draft\'';
				}elseif($column_name == '_wpsc_stock'){
					$update_value = $update_column . ' = ""';				
				}else{
					$update_value = $update_column . ' = 0';
				}
				break;
			
			case 'ADD_TO' :
			$sub_query = array ();
			
			for($j = 0; $j < count ( $ids ); $j ++) {
					$sub_query [] = "( " . $wpdb->_real_escape($ids[$j]) . "," . $text_cmp_value . ")";
					}
					$sub_query = implode ( ',', $sub_query );
					$query = "INSERT INTO " . $table_name . " (object_id,`$update_column`) VALUES " . $sub_query;;
					$sql_result = $wpdb->query ( $query );
			break;
		
		    case 'REMOVE_FROM' :
                        $query = "DELETE FROM " . $table_name . " WHERE object_id in (".$wpdb->_real_escape(implode(',',$ids)).")
                                AND `$update_column` = " . $text_cmp_value;

			$sql_result = $wpdb->query ( $query );
			break;
                        
                    case 'SET_TO_SALES_PRICE' :

                        for ($j=0;$j<sizeof($ids);$j++) {
//                               $query = "SELECT meta_value FROM `{$wpdb->prefix}postmeta`
//                                        WHERE meta_key = '_wpsc_special_price' AND post_id = ". $ids[$j];
//                                $result = $wpdb->get_col($query);
//                                $query = "UPDATE `{$wpdb->prefix}postmeta` SET meta_value =" . $result[0] . " WHERE meta_key = '_wpsc_price' AND post_id = ". $ids[$j];
//                                $result1 = $wpdb->query ($query);
                            $sale_price = get_post_meta( $ids[$j], '_wpsc_special_price', true );
                            update_post_meta( $ids[$j], '_wpsc_price', $sale_price );
                        }
                    $flag_query = 1;

                    break; 
                        
                    case 'SET_TO_REGULAR_PRICE' :

                        for ($j=0;$j<sizeof($ids);$j++) {
//                               $query = "SELECT meta_value FROM `{$wpdb->prefix}postmeta`
//                                        WHERE meta_key = '_wpsc_price' AND post_id = ". $ids[$j];
//                                $result = $wpdb->get_col($query);
//                                $query = "UPDATE `{$wpdb->prefix}postmeta` SET meta_value =" . $result[0] . " WHERE meta_key = '_wpsc_special_price' AND post_id = ". $ids[$j];
//                                $result1 = $wpdb->query ($query);
                            $regular_price = get_post_meta( $ids[$j], '_wpsc_price', true );
                            update_post_meta( $ids[$j], '_wpsc_special_price', $regular_price );
                        }
                        $flag_query = 1;

                    break;
		}

                if (is_array ( $update_value ))
			$update_value = implode ( ',', $update_value );

		$query = "UPDATE  $table_name SET $update_value ";		
		
		if (isset($row_filter) && !empty($row_filter)){
			if ($row_filter == '_wpsc_product_metadata') { //version 3.8
				$query = '';
                                $params ['value'] = $text_cmp_value; // getting the text value

				$params['unit'] = '';
				if (!empty($drop_down3_value))
				$params['unit']  = $drop_down3_value;  // getting the weight unit,				
				
				update_record_metadata($update_column,$wpsc_product_metadata,$params,$action_name);
				
				foreach($wpsc_product_metadata as $key=>$value)
				$sz_postId_data[$key] = serialize($value);

			} else {
				$post_col = ($table_name == "{$wp_table_prefix}_posts") ? 'id' : 'post_id';
                                $query .= " WHERE $post_col in (".$wpdb->_real_escape(implode(',',$ids)).")";
                                if ($col_filter != '')
                                $query .= " AND $filter_col = '$row_filter'";
                        }
		}else{

			if ($update_column == 'value' || $table_name == WPSC_TABLE_SUBMITED_FORM_DATA) {//BOF non-filter columns
				$query .= ' WHERE form_id = ' . $meta_value . ' AND log_id in (' . $log_ids . ');';
				
				$get_form_id_query = "SELECT id,unique_name FROM ". WPSC_TABLE_CHECKOUT_FORMS ." 
				WHERE unique_name in ('billingstate','billingcountry','shippingstate','shippingcountry')";
				
				$form_ids = $wpdb->get_results ( $get_form_id_query, 'ARRAY_A' );

				foreach ($form_ids as $form_id){
						$ctry_reg_ids[$form_id['unique_name']]    = $form_id['id'];
				}
				
				if (empty($drop_down4_value)){ //*Note: when non-usa & non-canada country has been selected
					$drop_down4_value = '';
				}

				if($active_module == 'Customers'){
					if($meta_value == $ctry_reg_ids['billingcountry']){

						$sql = "UPDATE ".WPSC_TABLE_PURCHASE_LOGS." SET billing_country  = '$drop_down3_value' WHERE id in ($log_ids)";
                        $sql_result = $wpdb->query ( $sql );

                        $update_region_name_query  = "UPDATE $table_name SET value = '$drop_down4_value'
							 WHERE form_id = {$ctry_reg_ids['billingstate']} 
							 AND log_id in ($log_ids)";
							$update_region_name_result = $wpdb->query ( $update_region_name_query );
							
						$sql = "UPDATE ".WPSC_TABLE_PURCHASE_LOGS." SET billing_region = '$drop_down4_value'
							        WHERE id in ($log_ids);";
					}
				}elseif ($active_module == 'Orders'){
						
					if($meta_value == $ctry_reg_ids['shippingcountry']){
                        $sql = "UPDATE ".WPSC_TABLE_PURCHASE_LOGS." SET shipping_country  = '$drop_down3_value' WHERE id in ($log_ids);";
                        $sql_result = $wpdb->query ( $sql );
						
						//@todo need to be checked that if the country is usa save the region to purchlogs else to submitted form data
							$update_region_name_query  = "UPDATE $table_name SET value = '$drop_down4_value'
							 WHERE form_id = {$ctry_reg_ids['shippingstate']} 
							 AND log_id in ($log_ids)";
							$update_region_name_result = $wpdb->query ( $update_region_name_query );
							
							$sql = "UPDATE ".WPSC_TABLE_PURCHASE_LOGS." SET shipping_region = '$drop_down4_value'
							        WHERE id in ($log_ids);";
					}
				}
				$sql_result     = $wpdb->query ( $sql );
				$drop_down4_value = '';
			} //EOF non-filter columns
			else {
                            $query .= " WHERE `id` in (".$wpdb->_real_escape(implode(",",$ids)).")";
			}
                        
                        if (is_null($log_id) && $active_module == 'Customers') {
                            $query = "";
		}
		}


                if($flag_query == 0 && (!empty($query))) {
                    $result = $wpdb->query ( $query );
                }
		
		$update_value = '';
		
		if ($column_name == 'post_status'){
			$post_status_col    = $column_name;
			$post_status_update = true;
		}
		
		if(isset($post_status_update) && $post_status_update && $i == $length-1 && !empty($children_ids)){
			$query = "UPDATE  $table_name SET $post_status_col = 'inherit' where id in (".implode(',',$children_ids).")";
			$result  = $wpdb->query($query);
		}
			

	if ($active_module == 'Products' && !empty($row_filter)) {
		foreach ( ( array ) $sz_postId_data as $meta_id => $meta_value ) {
			//batch update sub part query
			$sub_part_values [] .= "('$meta_id','$meta_value')";
		}

		if (is_array($sub_part_values)){
			$query = "insert into `{$wp_table_prefix}postmeta` (`meta_id`,`meta_value`) values ".implode(',',$sub_part_values).
			         "on duplicate key update meta_value = VALUES(meta_value)";
			$records  = $wpdb->query($query);
		}
	}
    
	
	}

        if( $radioData == 2 && $flag == 1 ){
		$updated_rows_cnt = 'All';
	} else {
		$updated_rows_cnt = $idLength;		
	}
	return $updated_rows_cnt;
}

function get_all_results() {
    global $wpdb;
    $select_results = $wpdb->get_col( "SELECT ID FROM {$wpdb->prefix}posts WHERE post_type IN ('wpsc-product') AND post_status IN ('inherit','publish', 'draft')" );
    return $select_results;
}

if (isset ( $_POST ['cmd'] ) && $_POST ['cmd'] == 'batchUpdate' && isset($_POST['wpscRunning']) && $_POST['wpscRunning'] == 1) {
	   global $wpdb;

        $post_status_update = false;
	
        encoding_utf_8($_POST); // For converting the $_POST in correct encoding format
        
        if (!($_POST['radio'] == 2 && $_POST['flag'] == 1)) {
            $batch_update_ids = json_decode ( stripslashes ( $_POST ['ids'] ) );
        }

        if(isset ( $_POST ['part'] ) && $_POST ['part'] == 'initial') {

            if ( $_POST['radio'] == 2 && $_POST['flag'] == 1 ) {        
                //Code for getting the ids of all search result
                if (!empty($_POST['products_search_flag']) && $_POST['products_search_flag'] == "true" ) {
                    $batch_update_ids = $wpdb->get_col( "SELECT product_id FROM {$wpdb->prefix}sm_advanced_search_temp" );
                } else {
                    $batch_update_ids = get_all_results();
                }
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
                    $nxtreq = $_POST['part'];

                    for ($j=$_POST['updatecnt'],$k=0;$j<$_POST['fupdatecnt'];$j++,$k++) {
                        $ids_final [$k] = $batch_update_ids [$j];
                    }
                    $ids = '[\"'.implode ('\",\"',$ids_final).'\"]';
                    $_POST['ids'] = $ids;

                    $updated_rows_cnt = batchUpdateWpsc( $_POST ); 
                    
                    $per = intval(($nxtreq/$count)*100); // Calculating the percentage for the display purpose
                    $perval = $per/100;

                    if ($per == 100) {
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
                    $encoded ['nxtreq'] = $nxtreq;
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

// Converting Order Status code to String for displaying it in CSV file
if (! function_exists( 'get_order_status_string' )) {
	function get_order_status_string ( $status_code ) {
		switch ( intval ( $status_code ) ) {
			
			case 1:
				return 'Incomplete Sale';
				break;
				
			case 2:
				return 'Order Received';
				break;
				
			case 3:
				return 'Accepted Payment';
				break;
				
			case 4:
				return 'Job Dispatched';
				break;
				
			case 5:
				return 'Closed Order';
				break;
				
			default:
				return 'Payment Declined';
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

        $data = fgetcsv($fp, 0, $delimiter, $enclosure); //  $escape only got added in 5.3.0

        fclose($fp);
        return $data;
    }
} 

//Function to export CSV file
if (! function_exists( 'export_csv_wpsc_38' )) {
	function export_csv_wpsc_38 ( $active_module, $columns_header, $data ) {
		foreach ( $columns_header as $key => $value ) {
			$getfield .= $value . ',';
		}
		
		$fields = substr_replace($getfield, '', -1);
		$each_field = array_keys( $columns_header );
		
		$csv_file_name = sanitize_title(get_bloginfo( 'name' )) . '_' . $active_module . '_' . gmdate('d-M-Y_H:i:s') . ".csv";
		
		foreach( (array) $data as $obj ){
			$row = (array) $obj;
			for($i = 0; $i < count ( $columns_header ); $i++){
				if($i == 0) $fields .= "\n";
				( $each_field[$i] == 'order_status' ) ? $row[$each_field[$i]] = get_order_status_string ( $row[$each_field[$i]] ) : '';
				$str = str_replace(array("\n", "\n\r", "\r\n", "\r"), "\t", $row[$each_field[$i]]); 
				$array = str_getcsv ( $str , ",", "\"" , "\\"); 
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