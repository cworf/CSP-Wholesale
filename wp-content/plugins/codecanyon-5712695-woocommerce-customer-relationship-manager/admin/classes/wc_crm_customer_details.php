<?php
/**
 * Class for E-mail handling.
 *
 * @author   Actuality Extensions
 * @package  WooCommerce_Customer_Relationship_Manager
 * @since    1.0
 */

if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WC_Crm_Customer_Details {

	public $billing_fields;
	public $shipping_fields;

	public $user_data;

	public $order;
	public $messages;
	public $error;

	public $user_id;
	public $order_id;


	public function __construct($user_id = 0, $order_id = 0) {
	    $this->user_id = $user_id;
    	$this->order_id = $order_id;
    	if( $this->order_id ){
						$this->order  = new WC_Order( $this->order_id );
			}
	}
	/**
	 * Displays content.
	 */
	function display() {
		$post_type = 'crm_customers';
		do_action( 'add_meta_boxes', $post_type, $this );
		do_action( 'add_meta_boxes_' . $post_type, $this );

		do_action( 'do_meta_boxes', $post_type, 'normal', $this );
		/** This action is documented in wp-admin/edit-form-advanced.php */
		do_action( 'do_meta_boxes', $post_type, 'advanced', $this );
		/** This action is documented in wp-admin/edit-form-advanced.php */
		do_action( 'do_meta_boxes', $post_type, 'side', $this );
		?>
		<div class="wrap">
		<?php if( $this->user_id ){
			?>
			<h2><?php _e('Edit Customer ', 'wc_customer_relationship_manager'); ?><a class="add-new-h2" href="admin.php?page=wc_new_customer"><?php _e('Add New Customer', 'wc_customer_relationship_manager'); ?></a></h2>
		<?php }elseif( $this->order_id ){ 			?>
			<h2><?php _e('View Customer ', 'wc_customer_relationship_manager'); ?><a class="add-new-h2" href="admin.php?page=wc_new_customer"><?php _e('Add New Customer', 'wc_customer_relationship_manager'); ?></a></h2>
		<?php }else{ ?>
			<h2><?php _e('Add New Customer', 'wc_customer_relationship_manager'); ?></h2>
		<?php } 
				if ( isset($_SESSION['customer_save_errors']) && is_wp_error( $_SESSION['customer_save_errors'] ) ) : ?>
						<div class="error below-h2" style="display: block;">
							<ul>
							<?php
								$errors = $_SESSION['customer_save_errors'];
								foreach ( $errors->get_error_messages() as $err )
									echo "<li>$err</li>\n";
							?>
							</ul>
						</div>
					<?php
					unset($_SESSION['customer_save_errors']);
					endif;

		 if( isset($_GET['message']) && !empty($_GET['message']) ){
				if($_GET['message'] == 2){
					echo '<div class="updated below-h2" id="message"  style="display: block;">';
						echo '<p>Customer updated.</p>';
					echo '</div>';
				}
			}

			if( !empty($this->error) ){
				echo '<div class="error below-h2" style="display: block;">';
				foreach ($this->error as $value) {
					echo '<p>'.$value.'</p>';
				}
				echo '</div>';
			}
			
			?>
			<form id="wc_crm_edit_customer_form" method="post" autocomplete="off">
			<?php if( $this->user_id ){ ?>
			<input type="hidden" id="customer_user" name="customer_user" value="<?php echo $this->user_id; ?>">
			<?php }else if( $this->order_id ){ ?>
			<input type="hidden" id="order_id" name="order_id" value="<?php echo $this->order_id; ?>">
			<?php } ?>
			<div id="poststuff">
				<div id="post-body" class="metabox-holder columns-2">
					<div id="postbox-container-1" class="postbox-container">
						<div class="meta-box-sortables">
							<div class="postbox " id="woocommerce-order-actions" style="display: block;">
								<div title="Click to toggle" class="handlediv"><br></div><h3 class="hndle"><span>Customer Actions</span></h3>
								<div class="inside">
										<ul class="order_actions submitbox">
											<li id="actions" class="wide">
												<select name="wc_crm_customer_action" id="wc_crm_customer_action">
													<option value="">Actions</option>
													<option value="wc_crm_customer_action_new_order">New order</option>
													<option value="wc_crm_customer_action_send_email">Send email</option>
													<option value="wc_crm_customer_action_phone_call">Add a new call</option>
												</select>
												<button title="Apply" class="button wc-reload wc_crm_new_action"><span>Apply</span></button>
												<a href="" class="wc_crm_new_action_href" target="_blank" style="display: none;">_</a>
											</li>

											<li class="wide">
												<?php if( !$this->order_id ){  ?>
													<input type="submit" value="Save Customer" name="save" style="float: right;" class="button save_customer button-primary wc_crm_new_action">
												<?php } ?>
											</li>
										</ul>
										</div>
								</div>
								<?php if( $this->user_id ) { ?>
									<div class="postbox " id="woocommerce-customer-notes">
										<div title="Click to toggle" class="handlediv"><br></div><h3 class="hndle"><span>Customer Notes</span></h3>
										<div class="inside" style="margin:0px; padding:0px;">
										<ul class="order_notes">
											<?php  $notes = $this->get_customer_notes(); ?>
													<?php if ( $notes ) {
																	foreach( $notes as $note ) {
																		?>
																		<li style="padding: 0 10px;"rel="<?php echo absint( $note->comment_ID ) ; ?>">
																			<div class="note_content">
																				<?php echo wpautop( wptexturize( wp_kses_post( $note->comment_content ) ) ); ?>
																			</div>
																			<p class="meta">
																				<abbr class="exact-date" title="<?php echo $note->comment_date_gmt; ?> GMT"><?php printf( __( 'added %s ago', 'wc_customer_relationship_manager' ), human_time_diff( strtotime( $note->comment_date_gmt ), current_time( 'timestamp', 1 ) ) ); ?></abbr>
																				<?php if ( $note->comment_author !== __( 'WooCommerce', 'wc_customer_relationship_manager' ) ) printf( ' ' . __( 'by %s', 'wc_customer_relationship_manager' ), $note->comment_author ); ?>
																				<a href="#" class="delete_customer_note"><?php _e( 'Delete note', 'wc_customer_relationship_manager' ); ?></a>
																			</p>
																		</li>
																		<?php
																	}
																} else {
																	echo '<li>' . __( 'There are no notes for this customer yet.', 'wc_customer_relationship_manager' ) . '</li>';
																} ?>
														</ul>
														<div class="add_note">
															<h4>Add note</h4>
													<p>
														<textarea rows="5" cols="20" class="input-text" id="add_order_note" name="order_note" type="text"></textarea>
													</p>
													<p>
														<a class="add_note_customer button" href="#">Add</a>
													</p>
												</div>
												</div>
										</div>
								<?php } ?>
						</div>
						<?php do_meta_boxes($post_type, 'side', $this); ?>
					</div>
					<div id="postbox-container-2" class="postbox-container">
						<div class="meta-box-sortables">
							<div id="woocommerce-customer-detail" class="postbox ">
								<div class="inside" style="margin:0px; padding:0px;">
									<?php $this->get_customer_detail(); ?>
								</div>
							</div>
							<?php if( $this->user_id || $this->order_id ){ ?>
								<div id="woocommerce-customer-orders" class="postbox ">
									<div title="Click to toggle" class="handlediv"><br></div>
									<h3 class="hndle"><span>Customer Orders</span></h3>
									<div class="inside" style="margin:0px; padding:0px;">
										<?php $this->get_customer_orders(); ?>
									</div>
								</div>
								<div id="woocommerce-customer-activity" class="postbox ">
									<div title="Click to toggle" class="handlediv"><br></div>
									<h3 class="hndle"><span>Activity</span></h3>
									<div class="inside" style="margin:0px; padding:0px;">
										<?php $this->get_customer_activity(); ?>
									</div>
								</div>
							<?php } ?>
						</div>
						<?php						
						do_meta_boxes($post_type, 'normal', $this);
						do_meta_boxes($post_type, 'advanced', $this);
						?>
					</div>
				</div>
			</div>
		</form>
		</div>

		<?php
	}

	function init_address_fields($b_country = '', $s_country = '', $show_country = true) {

		$this->billing_fields = apply_filters( 'woocommerce_admin_billing_fields', array(
			'first_name' => array(
				'label'    => __( 'First Name', 'woocommerce' ),
				'required' => true,
				'class'    => array( 'form-row-first' ),
				'show'	=> false
				),
			'last_name' => array(
				'label'    => __( 'Last Name', 'woocommerce' ),
				'required' => true,
				'class'    => array( 'form-row-last' ),
				'clear'    => true,
				'show'	=> false
				),
			'company' => array(
				'label' => __( 'Company Name', 'woocommerce' ),
				'class' => array( 'form-row-wide' ),
				'show'	=> false
				),
			'address_1' => array(
				'label'       => __( 'Address', 'woocommerce' ),
				'placeholder' => _x( 'Street address', 'placeholder', 'woocommerce' ),
				'required'    => true,
				'class'       => array( 'form-row-wide', 'address-field' ),
				'show'	=> false
				),
			'address_2' => array(
				'label'       => __( 'Address 2', 'woocommerce' ),
				'placeholder' => _x( 'Apartment, suite, unit etc. (optional)', 'placeholder', 'woocommerce' ),
				'class'       => array( 'form-row-wide', 'address-field' ),
				'required'    => false,
				'show'	=> false
				),
			'country' => array(
				'type'     => 'country',
				'label'    => __( 'Country', 'woocommerce' ),
				'required' => true,
				'class'    => array( 'form-row-wide', 'address-field', 'chosen_select' ),
				'show'	=> false,
				'type'	=> 'select',
				'options' => array( '' => __( 'Select a country&hellip;', 'wc_customer_relationship_manager' ) ) + WC()->countries->get_shipping_countries()
				),
			'city' => array(
				'label'       => __( 'Town / City', 'woocommerce' ),
				'placeholder' => __( 'Town / City', 'woocommerce' ),
				'required'    => true,
				'class'       => array( 'form-row-right', 'address-field' ),
				'show'	=> false
				),
			'state' => array(
				'type'        => 'state',
				'label'       => __( 'State / County', 'woocommerce' ),
				'placeholder' => __( 'State / County', 'woocommerce' ),
				'required'    => true,
				'class'       => array( 'form-row-left', 'address-field' ),
				'validate'    => array( 'state' ),
				'show'	=> false
				),
			'postcode' => array(
				'label'       => __( 'Postcode / Zip', 'woocommerce' ),
				'placeholder' => __( 'Postcode / Zip', 'woocommerce' ),
				'required'    => true,
				'class'       => array( 'form-row-right', 'address-field' ),
				'clear'       => true,
				'validate'    => array( 'postcode' ),
				'show'	=> false
				),
			'email' => array(
				'label' 		=> __( 'Email Address', 'woocommerce' ),
				'required' 		=> true,
				'class' 		=> array( 'form-row-first' ),
				'validate'		=> array( 'email' ),
				),
			'phone' => array(
				'label' 		=> __( 'Phone', 'woocommerce' ),
				'required' 		=> true,
				'class' 		=> array( 'form-row-last' ),
				'clear'			=> true,
				'validate'		=> array( 'phone' ),
				),
		) );

		


		$this->shipping_fields = apply_filters( 'woocommerce_admin_shipping_fields', array(
			'first_name' => array(
				'label'    => __( 'First Name', 'woocommerce' ),
				'required' => true,
				'class'    => array( 'form-row-first' ),
				'show'	=> false
				),
			'last_name' => array(
				'label'    => __( 'Last Name', 'woocommerce' ),
				'required' => true,
				'class'    => array( 'form-row-last' ),
				'clear'    => true,
				'show'	=> false
				),
			'company' => array(
				'label' => __( 'Company Name', 'woocommerce' ),
				'class' => array( 'form-row-wide' ),
				'show'	=> false
				),
			'address_1' => array(
				'label'       => __( 'Address', 'woocommerce' ),
				'placeholder' => _x( 'Street address', 'placeholder', 'woocommerce' ),
				'required'    => true,
				'class'       => array( 'form-row-wide', 'address-field' ),
				'show'	=> false
				),
			'address_2' => array(
				'label'       => __( 'Address 2', 'woocommerce' ),
				'placeholder' => _x( 'Apartment, suite, unit etc. (optional)', 'placeholder', 'woocommerce' ),
				'class'       => array( 'form-row-wide', 'address-field' ),
				'required'    => false,
				'show'	=> false
				),
			'country' => array(
				'label'    => __( 'Country', 'woocommerce' ),
				'required' => true,
				'class'    => array( 'form-row-wide', 'address-field', 'chosen_select' ),
				'show'	=> false,
				'type'	=> 'select',
				'options' => array( '' => __( 'Select a country&hellip;', 'wc_customer_relationship_manager' ) ) + WC()->countries->get_shipping_countries()
				),
			'city' => array(
				'label'       => __( 'Town / City', 'woocommerce' ),
				'placeholder' => __( 'Town / City', 'woocommerce' ),
				'required'    => true,
				'class'       => array( 'form-row-right', 'address-field' ),
				'show'	=> false
				),
			'state' => array(
				'label'       => __( 'State / County', 'woocommerce' ),
				'placeholder' => __( 'State / County', 'woocommerce' ),
				'required'    => true,
				'class'       => array( 'form-row-left', 'address-field' ),
				'validate'    => array( 'state' ),
				'show'	=> false
				),
			'postcode' => array(
				'label'       => __( 'Postcode / Zip', 'woocommerce' ),
				'placeholder' => __( 'Postcode / Zip', 'woocommerce' ),
				'required'    => true,
				'class'       => array( 'form-row-right', 'address-field' ),
				'clear'       => true,
				'validate'    => array( 'postcode' ),
				'show'	=> false
				),
		) );
		if(!empty($b_country) || !empty($s_country)){
			$countries = new WC_Countries();
			$locale		 = $countries->get_country_locale();
			$state_arr = WC()->countries->get_allowed_country_states();
		}
		if(!empty($b_country )){

			if ( isset( $locale[ $b_country ] ) ) {

				$this->billing_fields = wc_array_overlay( $this->billing_fields, $locale[ $b_country ] );

				// If default country has postcode_before_city switch the fields round.
				// This is only done at this point, not if country changes on checkout.
				if ( isset( $locale[ $b_country ]['postcode_before_city'] ) ) {
					if ( isset( $this->billing_fields['postcode'] ) ) {
						$this->billing_fields['postcode']['class'] = array( 'form-row-wide', 'address-field' );

						$switch_fields = array();

						foreach ( $this->billing_fields as $key => $value ) {
							if ( $key == 'city' ) {
								// Place postcode before city
								$switch_fields['postcode'] = '';
							}
							$switch_fields[$key] = $value;
						}

						$this->billing_fields = $switch_fields;
					}
				}

				if(isset($state_arr[$b_country]) && !empty($state_arr[$b_country])){
					$this->billing_fields['state']['type'] = 'select';
					$this->billing_fields['state']['class'] = array( 'form-row-left', 'address-field', 'chosen_select' );
					$this->billing_fields['state']['options'] = $state_arr[$b_country];					
				}
				
			}
		}
		if(!empty($s_country )){
			if ( isset( $locale[ $s_country ] ) ) {

				$this->shipping_fields = wc_array_overlay( $this->shipping_fields, $locale[ $s_country ] );

				// If default country has postcode_before_city switch the fields round.
				// This is only done at this point, not if country changes on checkout.
				if ( isset( $locale[ $s_country ]['postcode_before_city'] ) ) {
					if ( isset( $this->shipping_fields['postcode'] ) ) {
						$this->shipping_fields['postcode']['class'] = array( 'form-row-wide', 'address-field' );

						$switch_fields = array();

						foreach ( $this->shipping_fields as $key => $value ) {
							if ( $key == 'city' ) {
								// Place postcode before city
								$switch_fields['postcode'] = '';
							}
							$switch_fields[$key] = $value;
						}

						$this->shipping_fields = $switch_fields;
					}
				}
				if(isset($state_arr[$s_country]) && !empty($state_arr[$s_country])){
					$this->shipping_fields['state']['type'] = 'select';
					$this->shipping_fields['state']['class'] = array( 'form-row-left', 'address-field', 'chosen_select' );
					$this->shipping_fields['state']['options'] = $state_arr[$b_country];					
				}
			}
		}
	}
/**
	 * Get customer detail
	 */
	function get_customer_detail() {
		global $thepostid, $post, $woocommerce;
			$b_country = '';
			$s_country = '';

			if($this->user_id){
				$b_country = get_the_author_meta( 'billing_country', $this->user_id );
				$s_country = get_the_author_meta( 'shipping_country', $this->user_id );
			}else if($this->order_id){
				$b_country = $this->order->billing_country;
				$s_country = $this->order->shipping_country;
			}

			$this->init_address_fields($b_country, $s_country);

			?>
			<div class="panel-wrap woocommerce" id="customer_data">
				<div id="order_data" class="panel">

					<h2><?php _e( 'Customer Details', 'wc_customer_relationship_manager' ); ?></h2>
					<?php if( $this->user_id ){ ?>
						<p class="order_number">
							<?php echo __( 'Customer number', 'wc_customer_relationship_manager' ) . ' #' . $this->user_id . '. '; ?>
						</p>
					<?php } else if( $this->order_id ){ ?>
						<p class="order_number">
							<?php echo $this->order->billing_first_name. ' ' . $this->order->billing_last_name; ?>
						</p>
					<?php } ?>

					<div class="order_data_column_container">
						<?php if( !$this->order_id ){ ?>
						<div class="order_data_column">
							<h4><?php _e( 'General Details', 'wc_customer_relationship_manager' ); ?></h4>

							<p class="form-field form-field-wide">
								<label for="first_name">First Name:</label>
								<input type="text" id="first_name" name="first_name"  value="<?php echo get_the_author_meta( 'first_name', $this->user_id ); ?>" >
							</p>
							<p class="form-field form-field-wide">
								<label for="last_name">Last Name:</label>
								<input type="text" id="last_name" name="last_name"  value="<?php echo get_the_author_meta( 'last_name', $this->user_id ); ?>" >
							</p>
							<p class="form-field form-field-wide">
								<label for="user_email">Email Address:</label>
								<input type="text" id="user_email" name="user_email"  value="<?php echo get_the_author_meta( 'user_email', $this->user_id ); ?>" >
							</p>


							<p class="form-field form-field-wide"><label for="customer_status"><?php _e( 'Customer status:', 'wc_customer_relationship_manager' ) ?></label>
							<select id="customer_status" name="customer_status" class="chosen_select">
								<?php
										$selected = get_the_author_meta( 'customer_status', $this->user_id );
									$statuses = wc_crm_get_statuses();
									foreach ( $statuses as $status ) {
										echo '<option value="' . esc_attr( $status->status_slug ) . '" ' . selected( $status->status_slug, $selected, false ) . '>' . esc_html__( $status->status_name, 'wc_customer_relationship_manager' ) . '</option>';
									}
								?>
							</select>
							</p>


							<p class="form-field form-field-wide"><label for="date_of_birth">Date of Birth:</label>
								<input type="text" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])"  value="<?php echo get_the_author_meta( 'date_of_birth', $this->user_id ); ?>"  maxlength="10" id="date_of_birth" name="date_of_birth">
							</p>

							<p class="form-field form-field-wide">
								<label for="customer_site">Website:</label>
								<input type="text" id="customer_site" name="customer_site"  value="<?php echo get_the_author_meta( 'url', $this->user_id ); ?>" >
							</p>

							<p class="form-field form-field-wide">
								<label for="customer_twitter">Twitter:</label>
								<input type="text" id="customer_twitter" name="customer_twitter"  value="@<?php echo get_the_author_meta( 'twitter', $this->user_id ); ?>" >
							</p>

							<p class="form-field form-field-wide">
								<label for="customer_skype">Skype:</label>
								<input type="text" id="customer_skype" name="customer_skype" value="<?php echo get_the_author_meta( 'skype', $this->user_id ); ?>" >
							</p>
							<p class="form-field form-field-wide">
								<label for="customer_categories">Categories:</label>
								<select id="customer_categories" name="customer_categories[]" multiple="multiple" class="chosen_select" data-placeholder="<?php _e( 'Search for a category&hellip;', 'woocommerce' ); ?>" >
									<?php $cat     = get_the_author_meta( 'customer_categories', $this->user_id ); 
												$all_cat = get_terms( array('product_cat'),  array( 'orderby' => 'name', 'order' => 'ASC')  );
												if(!empty($all_cat)){
													foreach ($all_cat as $key => $value) {
														echo '<option value="'.$value->term_id.'" '.( in_array($value->term_id, $cat) ? 'selected="selected"' : '' ).'>'.$value->name.'</option>';
													}
												}
									?>
								</select>
							</p>
							<?php if( class_exists( 'WC_Brands_Admin' ) ) { ?>
							<p class="form-field form-field-wide">
								<label for="customer_brands">Brands:</label>
								<select id="customer_brands" name="customer_brands[]" multiple="multiple" class="chosen_select" data-placeholder="<?php _e( 'Search for a brand&hellip;', 'woocommerce' ); ?>" >
									<?php $brand     = get_the_author_meta( 'customer_brands', $this->user_id ); 
												$all_brands = get_terms( array('product_brand'),  array( 'orderby' => 'name', 'order' => 'ASC')  );
												if(!empty($all_brands)){
													foreach ($all_brands as $key => $value) {
														echo '<option value="'.$value->term_id.'" '.( in_array($value->term_id, $brand) ? 'selected="selected"' : '' ).'>'.$value->name.'</option>';
													}
												}
									?>
								</select>
							</p>
							<?php } ?>
						</div>
						<?php } ?>
						<div class="order_data_column" id="order_data_column_billing">
							<h4><?php _e( 'Billing Details', 'wc_customer_relationship_manager' ); ?><a class="edit_address" href="#" <?php echo ( ( empty($this->user_id) ) ? 'style="display: none;"' : ''); ?> ><img src="<?php echo WC()->plugin_url(); ?>/assets/images/icons/edit.png" alt="Edit" width="14" /></a></h4>
							<?php
								if( $this->user_id || $this->order_id ){
									// Display values
									echo '<div class="address">';

										if ( $this->get_formatted_billing_address()  )
											echo '<p><strong>' . __( 'Address', 'wc_customer_relationship_manager' ) . ':</strong>' . wp_kses( $this->get_formatted_billing_address(), array( 'br' => array() ) ) . '</p>';
										else
											echo '<p class="none_set"><strong>' . __( 'Address', 'wc_customer_relationship_manager' ) . ':</strong> ' . __( 'No billing address set.', 'wc_customer_relationship_manager' ) . '</p>';

										foreach ( $this->billing_fields as $key => $field ) {
											if ( isset( $field['show'] ) && $field['show'] === false )
												continue;

											$field_name = 'billing_' . $key;


											if($this->user_id){
												$field_value = get_the_author_meta( $field_name , $this->user_id );
											}
											elseif($this->order_id){
												$name_var = 'billing_'.$key;
												$field_value = $this->order->$field_name;
											}

											if ( !empty($field_value) )
												echo '<p><strong>' . esc_html( $field['label'] ) . ':</strong> ' . make_clickable( esc_html( $field_value ) ) . '</p>';
										}
										if ( WC()->payment_gateways() )
											$payment_gateways = WC()->payment_gateways->payment_gateways();

										$payment_method = ! empty( $this->order->payment_method ) ? $this->order->payment_method : '';

										if ( $payment_method )
											echo '<p><strong>' . __( 'Preferred Payment Method', 'wc_customer_relationship_manager' ) . ':</strong> ' . ( isset( $payment_gateways[ $payment_method ] ) ? esc_html( $payment_gateways[ $payment_method ]->get_title() ) : esc_html( $payment_method ) ) . '</p>';

									echo '</div>';
								}
								if( !$this->order_id ){
								// Display form
								echo '<div class="edit_address" ' . ( ( !$this->user_id ) ? 'style="display: block;"' : '') . '>';

								foreach ( $this->billing_fields as $key => $field ) {
									if ( ! isset( $field['type'] ) )
										$field['type'] = 'text';
										if(isset($data['_billing_' . $key]) && $data['_billing_' . $key]){
											$value = $data['_billing_' . $key];
										}elseif($this->user_id){
											$value = get_the_author_meta( 'billing_'.$key, $this->user_id );
										}elseif($this->order_id){
											$var_name = 'billing_'.$key;
											$value = $this->order->$var_name;
										}
										else{
											$value = '';
										}
									$post = new WC_Order( $this->order_id );
									if(!empty($field['class']) && is_array($field['class']))
										$class_ = implode(' ', $field['class']);
									
									switch ( $field['type'] ) {
										case "select" :
											woocommerce_wp_select( array( 'id' => '_billing_' . $key, 'class' => $class_, 'label' => $field['label'], 'options' => $field['options'], 'value' => $value ) );
										break;
										default :
											woocommerce_wp_text_input( array( 'id' => '_billing_' . $key, 'class' => $class_, 'label' => $field['label'], 'value' => $value ) );
										break;
									}
								}

								?>
								<p class="form-field form-field-wide">
									<label><?php _e( 'Payment Method:', 'wc_customer_relationship_manager' ); ?></label>
									<select name="_payment_method" id="_payment_method" class="first">
										<option value=""><?php _e( 'N/A', 'wc_customer_relationship_manager' ); ?></option>
										<?php
											$found_method 	= false;

											foreach ( $payment_gateways as $gateway ) {
												if ( $gateway->enabled == "yes" ) {
													echo '<option value="' . esc_attr( $gateway->id ) . '" ' . selected( $payment_method, $gateway->id, false ) . '>' . esc_html( $gateway->get_title() ) . '</option>';
													if ( $payment_method == $gateway->id )
														$found_method = true;
												}
											}

											if ( ! $found_method && ! empty( $payment_method ) ) {
												echo '<option value="' . esc_attr( $payment_method ) . '" selected="selected">' . __( 'Other', 'wc_customer_relationship_manager' ) . '</option>';
											} else {
												echo '<option value="other">' . __( 'Other', 'wc_customer_relationship_manager' ) . '</option>';
											}
										?>
									</select>
								</p>
								<?php

								echo '</div>';
								}
							?>
						</div>
						<div class="order_data_column" id="order_data_column_shipping">

							<h4><?php _e( 'Shipping Details', 'wc_customer_relationship_manager' ); ?> <a class="edit_address" href="#" <?php echo ( ( empty($this->user_id) ) ? 'style="display: none;"' : ''); ?> ><img src="<?php echo WC()->plugin_url(); ?>/assets/images/icons/edit.png" alt="Edit" width="14" /></a></h4>
							<?php
								if( $this->user_id || $this->order_id ){
										// Display values
										echo '<div class="address">';

											if ( $this->get_formatted_shipping_address() ){
												echo '<p><strong>' . __( 'Address', 'wc_customer_relationship_manager' ). ':</strong>'. wp_kses( $this->get_formatted_shipping_address(), array( 'br' => array() ) ) . '</p>';
											}
											else{
												echo '<p class="none_set"><strong>' . __( 'Address', 'wc_customer_relationship_manager' ) . ':</strong> ' . __( 'No shipping address set.', 'wc_customer_relationship_manager' ) . '</p>';
											}

											if ( $this->shipping_fields ) foreach ( $this->shipping_fields as $key => $field ) {
												if ( isset( $field['show'] ) && $field['show'] === false ){
													continue;
												}

												$field_name = 'shipping_' . $key;


												if($this->user_id){
													$field_value = get_the_author_meta( $field_name , $this->user_id );
												}
												elseif($this->order_id){
													$name_var = 'billing_'.$key;
													$field_value = $this->order->$field_name;
												}

												if ( ! empty( $order->$field_name ) ){
													echo '<p><strong>' . esc_html( $field['label'] ) . ':</strong> ' . make_clickable( esc_html( $field_value ) ) . '</p>';
												}
											}

										echo '</div>';
								}
								if( !$this->order_id ){
									// Display form
									echo '<div class="edit_address" ' . ( ( empty($this->user_id) ) ? 'style="display: block;"' : '') . '><p><button class="button" id="copy-billing-same-as-shipping" type="button">'. __( 'Copy from billing', 'wc_customer_relationship_manager' ) . '</button></p>';

									if ( $this->shipping_fields ) foreach ( $this->shipping_fields as $key => $field ) {
										if ( ! isset( $field['type'] ) )
											$field['type'] = 'text';
											if(isset($data['copy']) && $data['copy']){
												if(isset($data['_billing_' . $key]) && $data['_billing_' . $key]){
													$value = $data['_billing_' . $key];
												}elseif($this->user_id){
													$value = get_the_author_meta( 'billing_'.$key, $this->user_id );
												}elseif($this->order_id){
													$var_name = 'billing_'.$key;
													$value = $this->order->$var_name;
												}
												else{
													$value = '';
												}
											}elseif(isset($data['_shipping_' . $key]) && $data['_shipping_' . $key]){
												$value = $data['_shipping_' . $key];
											}elseif($this->user_id){
												$value = get_the_author_meta( 'shipping_'.$key, $this->user_id );
											}elseif($this->order_id){
												$var_name = 'shipping_'.$key;
												$value = $this->order->$var_name;
											}
											else{
												$value = '';
											}
										switch ( $field['type'] ) {
											case "select" :
												woocommerce_wp_select( array( 'id' => '_shipping_' . $key, 'class' => implode(' ', $field['class']), 'label' => $field['label'], 'options' => $field['options'], 'value' => $value ) );
											break;
											default :
												woocommerce_wp_text_input( array( 'id' => '_shipping_' . $key, 'class' => implode(' ', $field['class']), 'label' => $field['label'], 'value' => $value ) );
											break;
										}
									}

									echo '</div>';
								}
							?>
						</div>
					</div>
					<?php 
					if( $this->user_id || $this->order_id ){
						$address_l = str_replace('<br/>', ', ',$this->get_formatted_billing_address( true )) ; ?>
						<div id="customer_address_map_canvas"></div>
						<script>
						var wc_pos_customer_formatted_billing_address = '<?php echo $address_l; ?>';						
						</script>
					<?php } ?>
					<div class="clear"></div>
				</div>
			</div>
			<script>
				jQuery('.form-row-left').parent().css('float', 'left');
				jQuery('.form-row-right').parent().css('float', 'right');
			</script>
			<?php
	}
	function	get_formatted_billing_address($map = false) {
		$address = array();
		foreach ( $this->billing_fields as $key => $field ) {
			if($this->user_id){
				$address[$key] = get_the_author_meta( 'billing_'.$key, $this->user_id );
			}
			elseif($this->order_id){
				$name_var = 'billing_'.$key;
				$address[$key] = $this->order->$name_var;
			}
			if($map && ($key == 'first_name' || $key == 'last_name' || $key == 'company' || $key == 'phone' || $key == 'email' ))
				$address[$key] = '';
		}

		$formatted_address = WC()->countries->get_formatted_address( $address );
		return $formatted_address;
	}
	function	get_formatted_shipping_address() {
		$address = array();
		foreach ( $this->shipping_fields as $key => $field ) {
			if($this->user_id){
				$address[$key] = get_the_author_meta( 'shipping_'.$key, $this->user_id );
			}
			elseif($this->order_id){
				$name_var = 'shipping_'.$key;
				$address[$key] = $this->order->$name_var;
			}
		}

		$formatted_address = WC()->countries->get_formatted_address( $address );
		return $formatted_address;
	}
	function get_customer_orders(){
			require_once( 'wc_crm_order_list.php');
			$wc_crm_order_list = new WC_Crm_Order_List();
			$wc_crm_order_list->prepare_items();
			$wc_crm_order_list->display();
	}
	function get_customer_activity(){
		require_once( 'wc_crm_logs.php' );
		$logs = new WC_Crm_Logs();
		$logs->prepare_items();
		$logs->display();
	}

	function save( $user_id, $new=false) {
		
		if ( !empty($user_id ) && !is_wp_error($user_id) ) {		
			$ID = (int) $user_id;
			$old_user_data = WP_User::get_data_by( 'id', $ID );
			$user_data_up = array(
				'ID' => $user_id,
				'user_url' => $_POST['customer_site']
				);

			$errors = new WP_Error();
			if ( empty( $_POST['user_email'] ) ) {
				$errors->add( 'empty_email', __( '<strong>ERROR</strong>: Please enter an e-mail address.' ), array( 'form-field' => 'user_email' ) );
			} elseif ( !is_email( $_POST['user_email'] ) ) {
				$errors->add( 'invalid_email', __( '<strong>ERROR</strong>: The email address isn&#8217;t correct.' ), array( 'form-field' => 'user_email' ) );
			} elseif ( ( $owner_id = email_exists($_POST['user_email']) ) && $owner_id != $user_id ) {
				$errors->add( 'email_exists', __('<strong>ERROR</strong>: This email is already registered, please choose another one.'), array( 'form-field' => 'user_email' ) );
			}
			$err = $errors->get_error_codes();

			if(!$err){
				$user_data_up['user_email'] = $_POST['user_email'];
				$_SESSION['customer_save_errors'] = '';
			}else{
				$_SESSION['customer_save_errors'] = $errors;
			}

			wp_update_user($user_data_up);

			update_user_meta( $user_id, 'customer_status', $_POST['customer_status'] );
			update_user_meta( $user_id, 'date_of_birth', $_POST['date_of_birth'] );
			update_user_meta( $user_id, 'twitter', str_replace('@', '', $_POST['customer_twitter']) );
			update_user_meta( $user_id, 'skype', $_POST['customer_skype'] );
			update_user_meta( $user_id, 'customer_brands', isset($_POST['customer_brands']) ? $_POST['customer_brands'] : array() );
			update_user_meta( $user_id, 'customer_categories', isset($_POST['customer_categories']) ? $_POST['customer_categories'] : array() );

			update_user_meta( $user_id, 'first_name', $_POST['first_name'] );
			update_user_meta( $user_id, 'last_name', $_POST['last_name'] );

			update_user_meta( $user_id, 'billing_first_name', $_POST['_billing_first_name'] );
			update_user_meta( $user_id, 'billing_last_name', $_POST['_billing_last_name'] );
			update_user_meta( $user_id, 'billing_company', $_POST['_billing_company'] );
			update_user_meta( $user_id, 'billing_address_1', $_POST['_billing_address_1'] );
			update_user_meta( $user_id, 'billing_address_2', $_POST['_billing_address_2'] );
			update_user_meta( $user_id, 'billing_city', $_POST['_billing_city'] );
			update_user_meta( $user_id, 'billing_postcode', $_POST['_billing_postcode'] );
			update_user_meta( $user_id, 'billing_country', $_POST['_billing_country'] );
			update_user_meta( $user_id, 'billing_state', $_POST['_billing_state'] );
			update_user_meta( $user_id, 'billing_email', $_POST['_billing_email'] );
			update_user_meta( $user_id, 'billing_phone', $_POST['_billing_phone'] );
			update_user_meta( $user_id, 'payment_method', $_POST['_payment_method'] );
			update_user_meta( $user_id, 'shipping_first_name', $_POST['_shipping_first_name'] );
			update_user_meta( $user_id, 'shipping_last_name', $_POST['_shipping_last_name'] );
			update_user_meta( $user_id, 'shipping_company', $_POST['_shipping_company'] );
			update_user_meta( $user_id, 'shipping_address_1', $_POST['_shipping_address_1'] );
			update_user_meta( $user_id, 'shipping_address_2', $_POST['_shipping_address_2'] );
			update_user_meta( $user_id, 'shipping_city', $_POST['_shipping_city'] );
			update_user_meta( $user_id, 'shipping_postcode', $_POST['_shipping_postcode'] );
			update_user_meta( $user_id, 'shipping_country', $_POST['_shipping_country'] );
			update_user_meta( $user_id, 'shipping_state', $_POST['_shipping_state'] );

			// update the post (may even be a revision / autosave preview)
			do_action('acf/save_post', 'user_'.$user_id);

			do_action( 'profile_update', $user_id, $old_user_data );
			if($errors->get_error_codes() ){
				var_dump($errors); die;

				return wp_redirect( add_query_arg( array( "page" => "wc_new_customer", "user_id" => $user_id,  "message" => 7 ), 'admin.php' ) );
			}elseif($new)
				return wp_redirect( add_query_arg( array( "page" => "wc-customer-relationship-manager", "message" => 1), 'admin.php' ) );
			else
				return wp_redirect( add_query_arg( array( "page" => "wc_new_customer", "user_id" => $user_id,  "message" => 2 ), 'admin.php' ) );
		}else if( is_wp_error($user_id) && $user_id->get_error_codes()){
				$_SESSION['customer_save_errors'] = $user_id;
				return wp_redirect( add_query_arg( array( "page" => "wc_new_customer", "message" => 7 ), 'admin.php' ) );
		}
	}
	function create_user() {
			global $wpdb;
			extract($_POST);
			$user_email = trim($user_email);
			if( empty($user_email)){
				$this->error[] = __('<p><strong>ERROR</strong>: The email address isn’t correct.</p>');
			}else	if ( !email_exists( $user_email ) ) {
				//$random_password = wp_generate_password( $length=12, $include_standard_special_chars=false );
				$nickname = str_replace(' ', '', ucfirst(strtolower($_POST['first_name'])) ) . str_replace(' ', '', ucfirst(strtolower($_POST['last_name'])) );
				$tmp_registration_generate_password = get_option( 'woocommerce_registration_generate_password' );
				update_option( 'woocommerce_registration_generate_password', 'yes' );
				$username_opt = get_option('woocommerce_crm_username_add_customer');
				switch ($username_opt) {
					case 2:
						$username = str_replace(' ', '', strtolower($_POST['first_name']) ) . '-' . str_replace(' ', '', strtolower($_POST['last_name']) );
						break;
					case 3:
              $username = $user_email;
              break;
					default:
						$username = strtolower($nickname);
						break;
				}
				$username = _truncate_post_slug( $username, 60 );
				$check_sql = "SELECT user_login FROM {$wpdb->users} WHERE user_login = '%s' LIMIT 1";
        
        $user_name_check = $wpdb->get_var( $wpdb->prepare( $check_sql, $username ) );


        if ( $user_name_check ) {
          $suffix = 1;
          do {
            $alt_user_name = _truncate_post_slug( $username, 60 - ( strlen( $suffix ) + 1 ) ) . "-$suffix";
            $user_name_check = $wpdb->get_var( $wpdb->prepare( $check_sql, $alt_user_name ) );
            $suffix++;
          } while ( $user_name_check );
          $username = $alt_user_name;
        }

				$user_id = wc_create_new_customer( $user_email, $username );
        
				update_option( 'woocommerce_registration_generate_password', $tmp_registration_generate_password );

      	$this->save($user_id, true);
      	if(!is_wp_error($user_id)){	      	
					update_user_meta( $user_id, 'nickname', $nickname );
					wp_update_user( array( 'ID' => $user_id, 'role' => 'customer' ) );
				}
			} else {
				$errors = new WP_Error();
				$errors->add( 'invalid_email', __( '<strong>ERROR</strong>: User already exists.' ), array( 'form-field' => 'user_email' ) );
				$_SESSION['customer_save_errors'] = $errors;
			}
	}

	function add_order_note( $note, $customer_id = 0) {

		if ( is_user_logged_in() && current_user_can( 'manage_woocommerce' ) ) {
			$user                 = get_user_by( 'id', get_current_user_id() );
			$comment_author       = $user->display_name;
			$comment_author_email = $user->user_email;
		} else {
			$comment_author       = __( 'WC_CRM', 'wc_customer_relationship_manager' );
			$comment_author_email = strtolower( __( 'WC_CRM', 'wc_customer_relationship_manager' ) ) . '@';
			$comment_author_email .= isset( $_SERVER['HTTP_HOST'] ) ? str_replace( 'www.', '', $_SERVER['HTTP_HOST'] ) : 'noreply.com';
			$comment_author_email = sanitize_email( $comment_author_email );
		}

		$comment_post_ID 		= 0;
		$comment_author_url 	= '';
		$comment_content 		= $note;
		$comment_agent			= 'WC_CRM';
		$comment_type			= 'customer_note';
		$comment_parent			= 0;
		$comment_approved 		= 1;
		$commentdata 			= apply_filters( 'cw_crm_new_customer_note_data', compact( 'comment_post_ID', 'comment_author', 'comment_author_email', 'comment_author_url', 'comment_content', 'comment_agent', 'comment_type', 'comment_parent', 'comment_approved' ));

		$comment_id = wp_insert_comment( $commentdata );

		add_comment_meta( $comment_id, 'customer_id', $customer_id );

		return $comment_id;
	}

	function get_last_customer_note(){
		global $woocommerce, $post;
		$notes = 'No Customer Notes';
		$notes_array = $this->get_customer_notes();
		$count_notes = count($notes_array);
		#print_R($notes_array);
		#die;
		if( $count_notes == 0 ) return $notes;
		$count_notes--;
		if($count_notes == 0){
			$notes = esc_attr($notes_array[0]->comment_content);
		}
		elseif($count_notes == 1){
			$notes = esc_attr($notes_array[0]->comment_content . '<small style="display:block">plus ' . $count_notes . ' other note</small>');
		}
		else{
			$notes = esc_attr($notes_array[0]->comment_content . '<small style="display:block">plus ' . $count_notes . ' other notes</small>');
		}
		return $notes;
	}
	/**
	 * List customer notes (public)
	 *
	 * @access public
	 * @return array
	 */
	function get_customer_notes() {
		global $woocommerce, $post;
		$notes = array();

		$args = array(
			#'post_id' 	=> 0,
			'approve' 	=> 'approve',
			'type' 		=> 'customer_note',
			'meta_key' => 'customer_id',
			'meta_value' => $this->user_id,
		);
		remove_filter( 'comments_clauses', array( 'WC_Comments', 'exclude_order_comments' ), 10, 1 );
		if(   defined( 'WC_VERSION') && floatval(WC_VERSION) >= 2.2 ){
			remove_filter( 'comments_clauses', array( 'WC_Comments', 'exclude_webhook_comments' ), 10, 1 );
		}

		$comments = get_comments($args);

		add_filter( 'comments_clauses', array( 'WC_Comments', 'exclude_order_comments' ), 10, 1 );
		if(   defined( 'WC_VERSION') && floatval(WC_VERSION) >= 2.2 ){
			add_filter( 'comments_clauses', array( 'WC_Comments', 'exclude_webhook_comments' ), 10, 1 );
		}

		#print_r($comments);

		foreach ( $comments as $comment ) {
				$comment->comment_content = make_clickable( $comment->comment_content );
				$notes[] = $comment;
		}

		return (array) $notes;

	}
	/**
	 * List customer notes (public)
	 *
	 * @access public
	 * @return array
	 */
	function display_notes() {
		?>
		<style>
	    #adminmenuwrap,
	    #screen-meta,
	    #screen-meta-links,
	    #adminmenuback,
	    #wpfooter,
	    #wpadminbar{
	      display: none !important;
	    }
	    #wpbody-content{
	    	padding: 0;
	    }
	    html{
	      padding-top: 0 !important;
	    }
	    #wpcontent{
	      margin: 0 !important;
	    }
	    #wc-crm-page{
	      margin: 15px !important;
	    }
    </style>
    <input type="hidden" id="customer_user" name="customer_user" value="<?php echo $this->user_id; ?>">
		<div id="side-sortables" class="meta-box-sortables">
				<div class="postbox " id="woocommerce-customer-notes">
						<div class="inside">
						<ul class="order_notes">
							<?php  $notes = $this->get_customer_notes(); ?>
									<?php if ( $notes ) {
													foreach( $notes as $note ) {
														?>
														<li rel="<?php echo absint( $note->comment_ID ) ; ?>">
															<div class="note_content">
																<?php echo wpautop( wptexturize( wp_kses_post( $note->comment_content ) ) ); ?>
															</div>
															<p class="meta">
																<abbr class="exact-date" title="<?php echo $note->comment_date_gmt; ?> GMT"><?php printf( __( 'added %s ago', 'wc_customer_relationship_manager' ), human_time_diff( strtotime( $note->comment_date_gmt ), current_time( 'timestamp', 1 ) ) ); ?></abbr>
																<?php if ( $note->comment_author !== __( 'WooCommerce', 'wc_customer_relationship_manager' ) ) printf( ' ' . __( 'by %s', 'wc_customer_relationship_manager' ), $note->comment_author ); ?>
																<a href="#" class="delete_customer_note"><?php _e( 'Delete note', 'wc_customer_relationship_manager' ); ?></a>
															</p>
														</li>
														<?php
													}
												} else {
													echo '<li>' . __( 'There are no notes for this customer yet.', 'wc_customer_relationship_manager' ) . '</li>';
												} ?>
										</ul>
										<div class="add_note">
											<h4>Add note</h4>
									<p>
										<textarea rows="5" cols="20" class="input-text" id="add_order_note" name="order_note" type="text"></textarea>
									</p>
									<p>
										<a class="add_note_customer button" href="#">Add</a>
									</p>
								</div>
								</div>
						</div>
		</div>
		<?php
	}
}