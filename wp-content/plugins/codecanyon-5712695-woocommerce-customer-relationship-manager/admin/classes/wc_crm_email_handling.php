<?php
/**
 * Class for E-mail handling.
 *
 * @author   Actuality Extensions
 * @package  WooCommerce_Customer_Relationship_Manager
 * @since    1.0
 */

if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WC_Crm_Email_Handling {

	/**
	 * Displays form with e-mail editor.
	 */
	public static function display_form() {

		$recipients = array();
		$phones = array();
		$orders = array();

		if ( isset( $_REQUEST['user_id'] ) && !is_array( $_REQUEST['user_id'] ) ) { // if we have only one element, make it as array
			$user_id = $_REQUEST['user_id'];
			array_push( $recipients, get_the_author_meta( 'user_email', $user_id ) );
			array_push( $phones, get_the_author_meta( 'billing_phone', $user_id ) );
		} else if ( isset( $_REQUEST['user_id'] ) && is_array( $_REQUEST['user_id'] ) ) {
			$user_ids = $_REQUEST['user_id'];
			foreach ($user_ids as $id) {
				array_push( $recipients, get_the_author_meta( 'user_email', $id ) );
				array_push( $phones, get_the_author_meta( 'billing_phone', $id ) );
			}
		}
		if ( isset( $_REQUEST['order_id'] ) && !is_array( $_REQUEST['order_id'] ) ) { // if we have only one element, make it as array
			array_push( $orders, $_REQUEST['order_id'] );
		} else if ( isset( $_REQUEST['order_id'] ) ) {
			$orders = $_REQUEST['order_id'];
		}
		if( !empty($orders) ){
			foreach ( $orders as $item ) {
				$o = new WC_Order( $item );
				array_push( $recipients, $o->billing_email );
				array_push( $phones, $o->billing_phone );
			}
		}
		?>
		<h2 class="email-heading">Send Email</h2>
		<label for="form_email"><?php _e( 'From Email Address', 'wc_customer_relationship_manager' ); ?></label></br><input type="text"
		                                                                                                 name="from_email"
		                                                                                                 value=""
		                                                                                                 id="from_email"
		                                                                                                 placeholder="<?php
			global $woocommerce;
			$mailer = $woocommerce->mailer();
			echo sprintf($mailer->get_from_address() );
			?>"
		                                                                                                 autocomplete="off"/>
&nbsp;
			<?php
			global $woocommerce;
			$mailer = $woocommerce->mailer();
			echo sprintf( __( 'Default "From" Name is <strong>%s</strong>', 'wc_customer_relationship_manager' ), $mailer->get_from_name() );
			?>
			<?php
			global $woocommerce;
			$mailer = $woocommerce->mailer();
			echo sprintf( __( 'and the default "From" Email Address is <strong>%s</strong>', 'wc_customer_relationship_manager' ), $mailer->get_from_address() );
			?>
			&nbsp;
			<?php _e( sprintf( '<i>You can change the default email name & address by clicking <a href="%s" target="_blank">here</a>.</i>', '?page=wc-settings&tab=email' ), 'wc_customer_relationship_manager' ); ?></i>
			</br>
		<label for="recipients"><?php _e( 'Recipients', 'wc_customer_relationship_manager' ); ?></label><input
			type="text"
			name="recipients"
			value="<?php echo implode( ',', $recipients ); ?>"
			id="recipients"
			autocomplete="off"/>
		<br/>
		<input type="hidden" name="phones" value="<?php echo implode( ',', $phones ); ?>">
		<label for="subject"><?php _e( 'Subject', 'wc_customer_relationship_manager' ); ?></label><input type="text"
		                                                                                                 name="subject"
		                                                                                                 value=""
		                                                                                                 id="subject"
		                                                                                                 autocomplete="on"/>
		<br/>
		<?php wp_editor( '', 'emaileditor' ); ?>
		<div id="emaileditor">
		</div>
		<input type="hidden" name="update" value="sent_email">
		<button name="send" type="submit" class="button button-primary button-large" id="send" accesskey="p" value="Send"
		       style="margin-top: 10px;">Send Email<i class="ico_send"></i></button>

       <style>
			input[type="submit"]:after {
				content:"\e606";
				font-family: 'IcoMoon' !important;
			}
       </style>
	<?php
	}

	/**
	 * Processes the form data.
	 */
	public static function process_form() {
		global $woocommerce, $wpdb;


		$recipients = explode( ',', $_POST['recipients'] );

		$text = wpautop($_POST['emaileditor']);
		$subject = $_POST['subject'];
		if( !empty( $_POST['from_email'] ) && filter_var($_POST['from_email'], FILTER_VALIDATE_EMAIL) ) {
			add_filter( 'wp_mail_from', array( "\WC_Crm_Email_Handling", 'change_from_email' ), 9999 );
		}
		$mailer = $woocommerce->mailer();
		ob_start();
		wc_crm_custom_woocommerce_get_template( 'emails/customer-send-email.php', array(
			'email_heading' => $subject,
			'email_message' => $text
		) );
		$message = ob_get_clean();
		$order_ID = '';
		if(isset($_GET['order_id']) && $_GET['order_id'] != ''){
			$order_ID = $_GET['order_id'];
		}
		foreach ( $recipients as $r ) {
			//if ( $mailer->send( $r, $subject, $message ) ){ //it doesn't return a success or failure
			$mailer->send( $r, stripslashes($subject), stripslashes($message) );

		}
		//save log
			$emails_ = $_POST['recipients'];
			$phones_ = $_POST['phones'];
			$type = "email";
			$table_name = $wpdb->prefix . "wc_crm_log";
			$created = current_time('mysql');
			$created_gmt = get_gmt_from_date( $created );

			$insert = $wpdb->prepare( "(%s, %s, %s, %s, %s, %s, %s, %s)", $created, $created_gmt, $subject, $text, $emails_, $phones_ , $type, get_current_user_id() );

			if ( !empty($insert) )
				$wpdb->query("INSERT INTO $table_name (created, created_gmt, subject, message, user_email, phone, activity_type, user_id) VALUES " . $insert);

#			$rows_affected = $wpdb->insert( $table_name, array( 'created' => current_time('mysql'), 'subject' => $subject, 'message'=>addslashes($text),  'user_email' => $emails_, 'phone' => $phones_,  'activity_type' => $type,'user_id'=>get_current_user_id() ) );
	}

	public static function change_from_email( $email ) {
		return $_POST['from_email'];
	}


}
