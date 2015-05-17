<?php
/**
 * Class for E-mail handling.
 *
 * @author   Actuality Extensions
 * @package  WooCommerce_Customer_Relationship_Manager
 * @since    1.0
 */

if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WC_Crm_Phone_Call {

	/**
	 * Displays form with e-mail editor.
	 */
	public static function display_form() {
	?>
	<div class="panel-wrap woocommerce" id="place_new_call">
        <div id="log_data" class="panel">
			<h2>
			<?php
					$phone = '';
					$user_name = '';
					$user_id = '';
					$user_email = '';
					$order_id = '';
				if ( isset( $_REQUEST['order_id'] ) && !empty($_REQUEST['order_id']) ) {
						$o = new WC_Order( $_REQUEST['order_id'] );
						$user_email = $o->billing_email;
						$phone = $o->billing_phone;
						$user_name = trim($o->billing_first_name.' '.$o->billing_last_name);
						$user_id = $o->user_id;
						$order_id = $_REQUEST['order_id'];
				}else if( isset($_GET['user_id']) && !empty($_GET['user_id']) ){
					$user_id = $_GET['user_id'];
					$user_email = get_the_author_meta( 'user_email', $user_id );
					$phone      = get_the_author_meta( 'billing_phone', $user_id );
					$user_name  = get_the_author_meta( 'user_firstname', $user_id ) . " " . get_the_author_meta( 'user_lastname', $user_id ) ;
					if(   defined( 'WC_VERSION') && floatval(WC_VERSION) > 2.1 ){
						$last_order = new WP_Query(array(
																	'numberposts' => 1,
																	'meta_key' => '_customer_user',
		         											'meta_value' => $user_id,
		         											'post_type' => 'shop_order',
		       												'post_status' => array_keys( wc_get_order_statuses() ),
																));
					}else{
						$last_order = new WP_Query(array(
																	'numberposts' => 1,
																	'meta_key' => '_customer_user',
		         											'meta_value' => $user_id,
		         											'post_type' => 'shop_order',
		       												'post_status' => 'publish',
																));	
					}
					if($last_order->found_posts > 0){
						$order_id  = $last_order->posts[0]->ID;						
					}
				}
			?>
			<input type="hidden" id="order_id" value="<?php echo $order_id ?>">
				<span  class="h2_new_call"><?php _e('Place New Call', 'wc_customer_relationship_manager'); ?></span>
				<?php
					if ( !empty( $phone ) ) {
				?>
					<div class="wprap_new_call">
						<a href="tel:<?php echo $phone; ?>" class="button button-primary button-large" ><i class="ico_call"></i><?php _e('Place Call', 'wc_customer_relationship_manager'); ?></a>
						<input type="hidden" name="user_phone" value="<?php echo $phone; ?>" id="user_phone">
					</div>
				<?php
				} else{
				?>
					<div class="wprap_new_call">
						<input type="text" name="user_phone" id="user_phone" placeholder="<?php _e('Enter Phone Number', 'wc_customer_relationship_manager'); ?>">
						<spann class="error_message"><strong>ERROR</strong>: Please enter user phone.</spann>
					</div>
				<?php
				}
				?>
				<div class="clear"></div>
			</h2>
			<div class="error below-h2"></div>
			<p>Place a new call with <?php echo $user_name ?> on <?php echo $phone; ?> using the fields below. </p>
			<table class="wp-list-table form-table fixed phone_call">
				<tr class="form-field form-required">
					<th width="200">
						<label for="subject_of_call"><?php _e('Subject', 'wc_customer_relationship_manager'); ?>
						<span class="description">(required)</span></label>
					</th>
					<td>
						<input type="text" name="subject_of_call" id="subject_of_call">
						<p class="description">Enter the subject or topic of this phone call.</p>
						<spann class="error_message"><strong>ERROR</strong>: Please enter Subject of Call.</spann>
					</td>
				</tr>
				<tr>
					<th>
						<label for="call_type"><?php _e('Type', 'wc_customer_relationship_manager'); ?></label>
					</th>
					<td>
						<select name="call_type" id="call_type">
							<option value="<?php _e('Inbound', 'wc_customer_relationship_manager'); ?>"><?php _e('Inbound', 'wc_customer_relationship_manager'); ?></option>
							<option value="<?php _e('Outbound', 'wc_customer_relationship_manager'); ?>"><?php _e('Outbound', 'wc_customer_relationship_manager'); ?></option>
						</select>
						<p class="description">Select the type of this phone call.</p>
					</td>
				</tr>
				<tr>
					<th>
						<label for="call_purpose"><?php _e('Purpose', 'wc_customer_relationship_manager'); ?></label>
					</th>
					<td>
						<select name="call_purpose" id="call_purpose">
							<option value="<?php _e('None', 'wc_customer_relationship_manager'); ?>"><?php _e('None', 'wc_customer_relationship_manager'); ?></option>
							<option value="<?php _e('Prospecting', 'wc_customer_relationship_manager'); ?>"><?php _e('Prospecting', 'wc_customer_relationship_manager'); ?></option>
							<option value="<?php _e('Administrative', 'wc_customer_relationship_manager'); ?>"><?php _e('Administrative', 'wc_customer_relationship_manager'); ?></option>
							<option value="<?php _e('Negotiation', 'wc_customer_relationship_manager'); ?>"><?php _e('Negotiation', 'wc_customer_relationship_manager'); ?></option>
							<option value="<?php _e('Demo', 'wc_customer_relationship_manager'); ?>"><?php _e('Demo', 'wc_customer_relationship_manager'); ?></option>
							<option value="<?php _e('Project', 'wc_customer_relationship_manager'); ?>"><?php _e('Project', 'wc_customer_relationship_manager'); ?></option>
							<option value="<?php _e('Support', 'wc_customer_relationship_manager'); ?>"><?php _e('Support', 'wc_customer_relationship_manager'); ?></option>
						</select>
						<p class="description">Select the purpose of the phone call.</p>
					</td>
				</tr>
				<?php
				if( !empty($user_name) ){
				?>
				<tr>
					<th>
						<label for="contact_name"><?php _e('Customer Name', 'wc_customer_relationship_manager'); ?></label>
					</th>
					<td>
						<?php
								if( !empty($user_id) ){
									echo "<input type='text' value='".$user_name."' disabled='disabled'><a class='button tips' href='admin.php?page=wc_new_customer&user_id=$user_id' target='_blank' id='view_customer_info' data-tip='View Customer Profile'>";
									echo "<input type='hidden' name='user_email' value='".$user_email ."'/>";
								}else{
									echo "<input type='text' value='".$user_name."' disabled='disabled'>";
									echo "<input type='hidden' name='user_email' value='".$user_email ."'/>";
								}
						?>
					</td>
				</tr>
				<?php } ?>
				<tr>
					<th>
						<label for="related_to"><?php _e('Related To', 'wc_customer_relationship_manager'); ?>
						<span class="description">(required)</span></label>
					</th>
					<td>
						<select name="related_to" id="related_to">
							<option value="<?php _e('order', 'wc_customer_relationship_manager'); ?>"><?php _e('Order', 'wc_customer_relationship_manager'); ?></option>
							<option value="<?php _e('product', 'wc_customer_relationship_manager'); ?>"><?php _e('Product', 'wc_customer_relationship_manager'); ?></option>
						</select>
						<input type="text" name="number_order_product" id="number_order_product">
						<a href="?page=wc-customer-relationship-manager&order_list=order&order_id=<?php echo $order_id; ?>" class="button fancybox  glass tips" id="view_info" data-tip="<?php _e('Find', 'wc_customer_relationship_manager'); ?>"></a>
						<span id="message_view_info"></span>
						<p class="description">Enter the order/product or use the "Find" button to search for it.</p>
						<spann class="error_message"><strong>ERROR</strong>: Please enter Subject of Call.</spann>
					</td>
				</tr>
				<tr>
					<th>
						<label for="call_details"><?php _e('Call Details', 'wc_customer_relationship_manager'); ?></label>
					</th>
					<td>
						<label for="current_call" class="call_details">
							<input type="radio" name="call_details" id="current_call" checked="checked" value="current_call">
							<?php _e('Current Call', 'wc_customer_relationship_manager'); ?>
						</label>
						<label for="completed_call" class="call_details">
							<input type="radio" name="call_details" id="completed_call"  value="completed_call">
							<?php _e('Completed Call', 'wc_customer_relationship_manager'); ?>
						</label>
					</td>
				</tr>
				<tr id="current_call_wrap">
					<th><?php _e('Call Timer', 'wc_customer_relationship_manager'); ?></th>
					<td>
						<span class="display_time">00:00:00:00</span>

						<a href="#" class="button tips" id="start_timer" data-tip="<?php _e('Start', 'wc_customer_relationship_manager'); ?>"><i class="ico_start"></i></a>
						<a href="#" class="button tips" id="stop_timer" data-tip="<?php _e('Stop', 'wc_customer_relationship_manager'); ?>"><i class="ico_stop"></i></a>
						<a href="#" class="button tips" id="pause_timer" data-tip="<?php _e('Pause/Resume', 'wc_customer_relationship_manager'); ?>"><i class="ico_pause"></i></a>
						<a href="#" class="button tips" id="reset_timer" data-tip="<?php _e('Reset', 'wc_customer_relationship_manager'); ?>"><i class="ico_reset"></i></a>
					</td>
				</tr>
				<tr class="completed_call_wrap disabled">
					<th>
						<label for="call_date"><strong><?php _e('Call Date', 'wc_customer_relationship_manager'); ?></strong></label>
					</th>
					<td>
							<div class="wrap_disabled">
								<input type="text" name="call_date" id="call_date" value="<?php echo current_time('Y-m-d'); ?>">
								<i class="ico_calendar"></i>
								<spann class="error_message"><strong>ERROR</strong>: Please enter correct Date.</spann>
								<div class="content_disabled"></div>
							</div>
					</td>
				</tr>
				<tr class="completed_call_wrap disabled">
					<th>
						<label for="call_time_h"><strong><?php _e('Call Time', 'wc_customer_relationship_manager'); ?></strong>
						<span class="description">(required)</span></label>
					</th>
					<td>
							<div class="wrap_disabled">
								<input type="number" style="float:none; margin-right:1px;" name="call_time_h" id="call_time_h" class="call_time"> :
								<input type="number" style="float:none; margin-right:1px;" name="call_time_m" id="call_time_m" class="call_time"> :
								<input type="number" style="float:none; margin-right:1px;" name="call_time_s" id="call_time_s" class="call_time">
								<spann class="error_message"><strong>ERROR</strong>: Please enter correct Call Time. (No value more than 59 in minutes and seconds. And no value more than 23 in hours)</spann>
								<div class="content_disabled"></div>
							</div>
					</td>
				</tr>
				<tr class="completed_call_wrap disabled">
					<th>
						<label for="call_duration_h"><strong><?php _e('Call Duration', 'wc_customer_relationship_manager'); ?></strong>
						<span class="description">(required)</span></label>
					</th>
					<td>
							<div class="wrap_disabled">
								<input type="number" style="float:none; margin-right:1px;" name="call_duration_h" id="call_duration_h" class="call_time"> hour(s)
								<input type="number" style="float:none; margin-right:1px;" name="call_duration_m" id="call_duration_m" class="call_time"> minute(s)
								<input type="number" style="float:none; margin-right:1px;" name="call_duration_s" id="call_duration_s" class="call_time"> second(s)
								<spann class="error_message"><strong>ERROR</strong>: Please enter correct Call Duration. (No value more than 59 in minutes and seconds. And no value more than 23 in hours)</spann>
								<div class="content_disabled"></div>
							</div>
					</td>
				</tr>
				<tr class="completed_call_wrap">
					<th><label for="call_results"><?php _e('Call Results', 'wc_customer_relationship_manager'); ?></label></th>
					<td>
						<textarea name="call_results" id="call_results" rows="5"></textarea>
						<spann class="error_message"><strong>ERROR</strong>: This field Call Results is required.</spann>
					</td>
				</tr>
			</table>
			<input type="hidden" name="update" value="save_phone_call">
			<input type="submit" id="save_call" value="Save Call" class="button button-primary button-large" name="save_phone_call">
			<div class="clear"></div>

		</div>
	</div>

	<?php
	}
	/**
	 * Processes the form data.
	 */
	public static function process_form() {
		global $woocommerce, $wpdb;
		extract($_POST);

		$s = $call_date.' '.$call_time_h.':'.$call_time_m.':'.$call_time_s;
		$unix_timestamp = strtotime($s);
		$mysql_timestamp = date('Y-m-d H:i:s',$unix_timestamp);
		$created_gmt = get_gmt_from_date( $mysql_timestamp );


		$data = array(
		  'subject'               => $subject_of_call,
			'message'               => wpautop($call_results),
			'user_email'            => $user_email,
			'phone'                 => $user_phone,
			'activity_type'         => 'phone call',
			'user_id'               => get_current_user_id(),
			'created'               => $mysql_timestamp,
			'created_gmt'           => $created_gmt,
			'call_type'             => $call_type,
			'call_purpose'          => $call_purpose,
			'related_to'            => $related_to,
			'number_order_product'  => $number_order_product,
			'call_duration'         => $call_duration_h.':'.$call_duration_m.':'.$call_duration_s
		);
		$table_name = $wpdb->prefix . "wc_crm_log";
		$rows_affected = $wpdb->insert( $table_name, $data );
 		 ?>
       <style>
       		#successfully_s{
       			padding: 50px;
       			text-align: center;
       			font-size: 22px;
       		}
     		</style>
   		<?php
	}

}
