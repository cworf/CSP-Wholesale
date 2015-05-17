<?php
/**
 * Table with list of user groups.
 *
 * @author   Actuality Extensions
 * @package  WooCommerce_Customer_Relationship_Manager
 * @since    1.0
 */

if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WC_Crm_Customer_Groups' ) ) :

/**
 * WC_Crm_Customer_Groups Class
 */
class WC_Crm_Customer_Groups {

	/**
	 * Handles output of the Customer Groups page in admin.
	 *
	 * Shows the created groups and lets you add new ones or edit existing ones.
 	 * The added groups are stored in the database and can be used for layered navigation.
	 */
	public function output() {

		global $wpdb, $woocommerce, $action_completed, $action_updated, $action_deleted;
		// Action to perform: add, edit, delete or none
		$action = '';
		if ( ! empty( $_POST['wc_crm_add_new_group'] ) ) {
			$action = 'add';
		} elseif ( ! empty( $_POST['wc_crm_save_group'] ) && ! empty( $_GET['id'] ) ) {
			$action = 'edit';
		} elseif ( !empty( $_GET['action'] ) && $_GET['action'] == 'delete' ) {
			$action = 'delete';
		}
		elseif ( ( !empty( $_POST['action'] ) && $_POST['action'] == 'delete' ) || ( !empty( $_POST['action2'] ) && $_POST['action2'] == 'delete' ) ) {
			$action = 'delete_groups';
		}

		// Add or edit an group
		if ( 'add' === $action || 'edit' === $action ) {

			// Security check
			if ( 'add' === $action ) {
				check_admin_referer( 'wc-crm-add-new-group' );
			}
			if ( 'edit' === $action ) {
				$group_id = absint( $_GET['id'] );
			}

			// Grab the submitted data
			$group_name               = ( isset( $_POST['group_name'] ) )   ? (string) stripslashes( $_POST['group_name'] ) : '';
			$group_slug               = ( isset( $_POST['group_slug'] ) )    ? wc_sanitize_taxonomy_name( stripslashes( (string) $_POST['group_slug'] ) ) : '';
			$group_type               = ( isset( $_POST['group_type'] ) )    ? (string) stripslashes( $_POST['group_type'] ) : '';
			$group_total_spent_mark   = ( isset( $_POST['group_total_spent_mark'] ) )    ? (string) stripslashes( $_POST['group_total_spent_mark'] ) : '';
			$group_total_spent        = ( isset( $_POST['group_total_spent'] ) )    ? (string) stripslashes( $_POST['group_total_spent'] ) : '';
			$group_user_role          = ( isset( $_POST['group_user_role'] ) )    ? (string) stripslashes( $_POST['group_user_role'] ) : '';
			$group_customer_status    = ( isset( $_POST['group_customer_status'] ) )    ? $_POST['group_customer_status'] : array();
			$group_product_categories = ( isset( $_POST['group_product_categories'] ) ) ? $_POST['group_product_categories'] : array();
			$group_order_status       = ( isset( $_POST['group_order_status'] ) ) ? $_POST['group_order_status'] : array();
			$group_last_order         = ( isset( $_POST['group_last_order'] ) )    ? (string) stripslashes( $_POST['group_last_order'] ) : '';
			$group_last_order_from    = ( isset( $_POST['group_last_order_from'] ) )    ? (string) stripslashes( $_POST['group_last_order_from'] ) : '';
			$group_last_order_to      = ( isset( $_POST['group_last_order_to'] ) ) ? (string) stripslashes( $_POST['group_last_order_to'] ) : '';


			// Auto-generate the label or slug if only one of both was provided
			if ( ! $group_name && $group_slug ) {
				$group_name = ucfirst( $group_slug );
			}
			if ( ! $group_slug && $group_name) {
				$group_slug = wc_sanitize_taxonomy_name( stripslashes( $group_name ) );
			}

			// Forbidden group names
			// http://codex.wordpress.org/Function_Reference/register_taxonomy#Reserved_Terms
			$reserved_terms = array(
				'attachment', 'attachment_id', 'author', 'author_name', 'calendar', 'cat', 'category', 'category__and',
				'category__in', 'category__not_in', 'category_name', 'comments_per_page', 'comments_popup', 'cpage', 'day',
				'debug', 'error', 'exact', 'feed', 'hour', 'link_category', 'm', 'minute', 'monthnum', 'more', 'name',
				'nav_menu', 'nopaging', 'offset', 'order', 'orderby', 'p', 'page', 'page_id', 'paged', 'pagename', 'pb', 'perm',
				'post', 'post__in', 'post__not_in', 'post_format', 'post_mime_type', 'post_status', 'post_tag', 'post_type',
				'posts', 'posts_per_archive_page', 'posts_per_page', 'preview', 'robots', 's', 'search', 'second', 'sentence',
				'showposts', 'static', 'subpost', 'subpost_id', 'tag', 'tag__and', 'tag__in', 'tag__not_in', 'tag_id',
				'tag_slug__and', 'tag_slug__in', 'taxonomy', 'tb', 'term', 'type', 'w', 'withcomments', 'withoutcomments', 'year',
			);

			// Error checking
			if('add' === $action){
				if ( ! $group_name || ! $group_slug || ! $group_type ) {
					$error = __( 'Please, provide a group name, slug and type.', 'wc_customer_relationship_manager' );
				} elseif ( strlen( $group_name ) >= 28 ) {
					$error = sprintf( __( 'Slug “%s” is too long (28 characters max). Shorten it, please.', 'woocommerce' ), sanitize_title( $group_name ) );
				} elseif ( in_array( $group_name, $reserved_terms ) ) {
					$error = sprintf( __( 'Slug “%s” is not allowed because it is a reserved term. Change it, please.', 'woocommerce' ), sanitize_title( $group_name ) );
				} elseif ( in_array( $group_name, $reserved_terms ) ) {
					$error = sprintf( __( 'Slug “%s” is not allowed because it is a reserved term. Change it, please.', 'woocommerce' ), sanitize_title( $group_name ) );
				} else {
					$group_exists = wc_crm_group_exists( $group_slug );

					if ( 'add' === $action && $group_exists ) {
						$error = sprintf( __( 'Slug “%s” is already in use. Change it, please.', 'woocommerce' ), sanitize_title( $group_name ) );
					}
				}
			}
			/*if ( $group_type == 'dynamic' ) {
				if( ! $group_total_spent ){
					$error = __( 'Please, provide a Total Spent.', 'wc_customer_relationship_manager' );
				}else if( $group_last_order == 'between' && (!$group_last_order_from || !$group_last_order_to) ){
					$error = __( 'Please, provide a Date.', 'wc_customer_relationship_manager' );
				}else if( $group_last_order != 'between' && !$group_last_order_from ){
					$error = __( 'Please, provide a Date.', 'wc_customer_relationship_manager' );
				}
			}*/

			// Show the error message if any
			if ( ! empty( $error ) ) {
				echo '<div id="woocommerce_errors" class="error fade"><p>' . $error . '</p></div>';
			} else {

				// Add new group
				$group = array(
						'group_type'               => $group_type,
						'group_total_spent_mark'   => $group_total_spent_mark,
						'group_total_spent'        => $group_total_spent,
						'group_user_role'          => $group_user_role,
						'group_customer_status'    => serialize($group_customer_status),
						'group_product_categories' => serialize($group_product_categories),
						'group_order_status'       => serialize($group_order_status),
						'group_last_order'         => $group_last_order,
						'group_last_order_from'    => $group_last_order_from,
						'group_last_order_to'      => $group_last_order_to
					);
				if ( 'add' === $action ) {

					$group['group_slug'] = $group_slug;
					$group['group_name'] = $group_name;

					$wpdb->insert( $wpdb->prefix . 'wc_crm_groups', $group );

					do_action( 'wc_crm_group_added', $wpdb->insert_id, $group );

					$action_completed = true;
				}

				// Edit existing group
				if ( 'edit' === $action ) {

					$wpdb->update( $wpdb->prefix . 'wc_crm_groups', $group, array( 'ID' => $group_id ) );
					

					do_action( 'wc_crm_group_updated', $group_id, $group);

					$action_completed = true;
					$action_updated = true;
				}

				flush_rewrite_rules();
			}
		}

		// Delete an group
		if ( 'delete' === $action ) {
			// Security check
			$group_id = absint( $_GET['id'] );

			$wpdb->query( "DELETE FROM {$wpdb->prefix}wc_crm_groups WHERE ID = $group_id" );

			do_action( 'wc_crm_group_deleted', $group_id);

				$action_completed = true;
				$action_deleted = '<div id="message" class="updated below-h2"><p>Group deleted</p></div>';
		}

		// Delete an groups

		if ( 'delete_groups' === $action ) {
			// Security check
			$ids = $_POST['id'];
			$count_groups = count($ids);
			$ids = implode(',', $ids);
			$wpdb->query( "DELETE FROM {$wpdb->prefix}wc_crm_groups WHERE ID IN ($ids)" );

			do_action( 'wc_crm_group_deleted', $group_id);

				$action_completed = true;
				$action_deleted = '<div id="message" class="updated below-h2"><p>'. $count_groups .' Groups deleted</p></div>';
		}
		// Show admin interface
		if ( !empty( $_GET['action'] ) && $_GET['action'] == 'edit'){
			$this->edit_group();
		}
		else{
			$this->add_group();
		}
	}

	/**
	 * Edit group admin panel
	 *
	 * Shows the interface for changing an groups type between select and text
	 */
	public function edit_group() {
		global $wpdb, $action_completed, $action_updated;

		$id = absint( $_GET['id'] );

		$group_to_edit = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "wc_crm_groups WHERE ID = '$id'");

		$group_name 	          = $group_to_edit->group_name;

		#$group_name   = ( isset( $_POST['group_name'] ) )   ? (string) stripslashes( $_POST['group_name'] ) : '';
		#$group_slug    = ( isset( $_POST['group_slug'] ) )    ? wc_sanitize_taxonomy_name( stripslashes( (string) $_POST['group_slug'] ) ) : '';
		$group_type               = ( isset( $_POST['group_type'] ) )    ? (string) stripslashes( $_POST['group_type'] ) : $group_to_edit->group_type;
		$group_total_spent_mark   = ( isset( $_POST['group_total_spent_mark'] ) )    ? (string) stripslashes( $_POST['group_total_spent_mark'] ) : $group_to_edit->group_total_spent_mark;
		$group_total_spent        = ( isset( $_POST['group_total_spent'] ) )    ? (string) stripslashes( $_POST['group_total_spent'] ) : $group_to_edit->group_total_spent;
		$group_user_role          = ( isset( $_POST['group_user_role'] ) )    ? (string) stripslashes( $_POST['group_user_role'] ) : $group_to_edit->group_user_role;

		$group_customer_status    = ( isset( $_POST['group_customer_status'] ) )    ? $_POST['group_customer_status'] : unserialize($group_to_edit->group_customer_status);
		$group_product_categories = ( isset( $_POST['group_product_categories'] ) ) ? $_POST['group_product_categories'] : unserialize($group_to_edit->group_product_categories);
		$group_order_status       = ( isset( $_POST['group_order_status'] ) ) ? $_POST['group_order_status'] : unserialize($group_to_edit->group_order_status);

		$group_last_order         = ( isset( $_POST['group_last_order'] ) )    ? (string) stripslashes( $_POST['group_last_order'] ) : $group_to_edit->group_last_order;
		$group_last_order_from    = ( isset( $_POST['group_last_order_from'] ) )    ? (string) stripslashes( $_POST['group_last_order_from'] ) : $group_to_edit->group_last_order_from;
		$group_last_order_to      = ( isset( $_POST['group_last_order_to'] ) ) ? (string) stripslashes( $_POST['group_last_order_to'] ) : $group_to_edit->group_last_order_to;

		if(!is_array($group_customer_status))
			$group_customer_status = array();

		if(!is_array($group_product_categories))
			$group_product_categories = array();

		if(!is_array($group_order_status))
			$group_order_status = array();

		?>
		<div class="wrap woocommerce">
			<div class="icon32 icon32-groups" id="icon-woocommerce"><br/></div>
		    <h2><?php _e( 'Edit group', 'wc_customer_relationship_manager' ) ?></h2>
		    <?php if ($action_updated){ ?>
		    <div id="message" class="updated below-h2"><p>Group Updated</p></div>
		    <?php } ?>
				<p><?php _e( 'Groups are used to organise your customers.', 'wc_customer_relationship_manager' ) ?></p>
			<form action="" method="post">
				<input type="hidden" name="wc_crm_edit_group" value="<?php echo $id; ?>">
				<table class="form-table">
					<tbody>
								<tr class="form-field">
									<td>
										<label for="f_group_name"><?php _e( 'Name', 'woocommerce' ); ?></label>
									</td>
									<td>
										<?php echo  $group_name; ?>
									</td>
								</tr>

								<tr class="form-field">
									<td>
										<label for="f_group_type"><?php _e( 'Type', 'wc_customer_relationship_manager' ); ?></label>
									</td>
									<td>
										<select name="group_type" id="f_group_type">
											<option value="dynamic" <?php selected( $group_type, 'dynamic' ); ?> ><?php _e( 'Dynamic', 'wc_customer_relationship_manager' ) ?></option>
											<option value="static" <?php selected( $group_type, 'static' ); ?> ><?php _e( 'Static', 'wc_customer_relationship_manager' ) ?></option>
											<?php do_action('wc_crm_customer_group_types'); ?>
										</select>
										<p class="description"><?php _e( 'Determines how you select group for customers.', 'wc_customer_relationship_manager' ); ?></p>
									</td>
								</tr>
								<tr class="form-field dynamic_group_type">
									<td>
										<label for="group_total_spent"><?php _e( 'Total Spent', 'wc_customer_relationship_manager' ); ?></label>
									</td>
									<td>
										<select name="group_total_spent_mark" id="group_total_spent_mark">
											<option value="equal" <?php selected( $group_total_spent_mark, 'equal' ); ?>><?php _e( '=', 'wc_customer_relationship_manager' ) ?></option>
											<option value="greater" <?php selected( $group_total_spent_mark, 'greater' ); ?>><?php _e( '&gt;', 'wc_customer_relationship_manager' ) ?></option>
											<option value="less" <?php selected( $group_total_spent_mark, 'less' ); ?>><?php _e( '&lt;', 'wc_customer_relationship_manager' ) ?></option>
											<option value="greater_or_equal" <?php selected( $group_total_spent_mark, 'greater_or_equal' ); ?>><?php _e( '&ge;', 'wc_customer_relationship_manager' ) ?></option>
											<option value="less_or_equal" <?php selected( $group_total_spent_mark, 'less_or_equal' ); ?>><?php _e( '&le;', 'wc_customer_relationship_manager' ) ?></option>
										</select>
										<input type="number" step="any" id="group_total_spent" name="group_total_spent" style="width: 40%;" value="<?php echo  $group_total_spent; ?>">
									</td>
								</tr>
								<tr class="form-field dynamic_group_type">
									<td>
										<label for="group_user_role"><?php _e( 'User Role', 'wc_customer_relationship_manager' ); ?></label>
									</td>
									<td>
										<select name="group_user_role" id="group_user_role">
											<option value="any">
						            	<?php _e( 'Any', 'wc_customer_relationship_manager' ); ?>
					            </option>
											<option value="guest">
						            	<?php _e( 'Guest', 'wc_customer_relationship_manager' ); ?>
					            </option>
											<?php
						          global $wp_roles;
						          foreach ( $wp_roles->role_names as $role => $name ) : ?>
						            <option value="<?php echo strtolower($name); ?>" <?php selected( $group_user_role, strtolower($name) ); ?>>
						            	<?php _e( $name, 'wc_customer_relationship_manager' ); ?>
						            </option>
						          <?php
						          endforeach;
						          ?>
										</select>
									</td>
								</tr>
								<tr class="form-field dynamic_group_type">
									<td>
										<label for="group_customer_status"><?php _e( 'Customer Status', 'wc_customer_relationship_manager' ); ?></label>
									</td>
									<td>
										<select name="group_customer_status[]" id="group_customer_status" multiple="multiple" data-placeholder="<?php _e( 'Choose a Customer Status...', 'wc_customer_relationship_manager' ); ?>">
											<?php
						          global $statuses;
						          foreach ( $statuses as $status => $name ) : ?>
						            <option value="<?php echo strtolower($name); ?>" <?php echo in_array(strtolower($name), $group_customer_status) ? 'selected="selected"' : ''; ?> >
						            	<?php _e( $name, 'wc_customer_relationship_manager' ); ?>
						            </option>
						          <?php
						          endforeach;
						          ?>
										</select>
									</td>
								</tr>
								<tr class="form-field dynamic_group_type">
									<td>
										<label for="group_product_categories"><?php _e( 'Product Category', 'wc_customer_relationship_manager' ); ?></label>
									</td>
									<td>
										<select name="group_product_categories[]" id="group_product_categories" multiple="multiple" data-placeholder="<?php _e( 'Choose a Product Category...', 'wc_customer_relationship_manager' ); ?>">
										<?php
					          $all_cat = get_terms( array('product_cat'),  array( 'orderby' => 'name', 'order' => 'ASC', 'hide_empty' => false, )  );
					          if(!empty($all_cat)){
					            foreach ($all_cat as $cat) {?>
					            	<option value="<?php echo $cat->term_id; ?>" <?php echo in_array($cat->term_id, $group_product_categories) ? 'selected="selected"' : ''; ?>>
					            		<?php echo $cat->name; ?>
					            	</option>
					          	<?php
					            }
					          }
					          ?>
									</select>
									</td>
								</tr>
								<tr class="form-field dynamic_group_type">
									<td>
										<label for="group_order_status"><?php _e( 'Order Status', 'wc_customer_relationship_manager' ); ?></label>
									</td>
									<td>
									<select name="group_order_status[]" id="group_order_status" multiple="multiple" data-placeholder="<?php _e( 'Choose a Product Category...', 'wc_customer_relationship_manager' ); ?>">
										<?php
					          $wc_statuses = wc_get_order_statuses();
					          if(!empty($wc_statuses)){
					            foreach ($wc_statuses as $key => $status_name) {?>
					            	<option value="<?php echo $key; ?>" <?php echo in_array($key, $group_order_status) ? 'selected="selected"' : ''; ?>>
					            		<?php echo $status_name; ?>
					            	</option>
					          	<?php
					            }
					          }
					          ?>
									</select>
									</td>
								</tr>
								<tr class="form-field dynamic_group_type">
									<td>
										<label for="group_last_order"><?php _e( 'Last Order', 'wc_customer_relationship_manager' ); ?></label>
									</td>
									<td>
										<div class="wrap_date">
											<select name="group_last_order" id="group_last_order">
												<option value="between" <?php selected( $group_last_order, 'between' ); ?>><?php _e( 'Between', 'wc_customer_relationship_manager' ); ?></option>
												<option value="before"  <?php selected( $group_last_order, 'before' ); ?>><?php _e( 'Before', 'wc_customer_relationship_manager' ); ?></option>
												<option value="after"   <?php selected( $group_last_order, 'after' ); ?>><?php _e( 'After', 'wc_customer_relationship_manager' ); ?></option>
											</select>
										</div>
										<div class="wrap_date">
											<input type="text" id="group_last_order_from" name="group_last_order_from" value="<?php echo $group_last_order_from; ?>">
											<i class="ico_calendar"></i>
										</div>
										<div class="wrap_date group_last_order_between" style="height: 30px; line-height: 30px; padding: 0 10px;">
											to
										</div>
										<div class="wrap_date group_last_order_between">
											<input type="text" id="group_last_order_to" name="group_last_order_to" value="<?php echo $group_last_order_to; ?>">
											<i class="ico_calendar"></i>
										</div>
										<div class="clear"></div>
									</td>
								</tr>
					</tbody>
				</table>
				<p class="submit"><input type="submit" name="wc_crm_save_group" id="submit" class="button-primary" value="<?php _e( 'Update', 'woocommerce' ); ?>"></p>

			</form>
		</div>
		<?php
	}
	/**
	 * Add group admin panel
	 *
	 * Shows the interface for adding new groups
	 */
	public function add_group() {
		global $wc_crm_customer_groups_table, $action_completed,  $action_deleted;
		// Grab the submitted data
			$group_name               = '';
			$group_slug               = '';
			$group_type               = '';
			$group_total_spent_mark   = '';
			$group_total_spent        = '';
			$group_user_role          = '';
			$group_customer_status    = array();
			$group_product_categories = array();
			$group_order_status       = array();
			$group_last_order         = '';
			$group_last_order_from    = '';
			$group_last_order_to      = '';
		if(!$action_completed){
			$group_name               = ( isset( $_POST['group_name'] ) ) ? (string) stripslashes( $_POST['group_name'] ) : '';
			$group_slug               = ( isset( $_POST['group_slug'] ) ) ? wc_sanitize_taxonomy_name( stripslashes( (string) $_POST['group_slug'] ) ) : '';
			$group_type               = ( isset( $_POST['group_type'] ) ) ? (string) stripslashes( $_POST['group_type'] ) : '';
			$group_total_spent_mark   = ( isset( $_POST['group_total_spent_mark'] ) ) ? (string) stripslashes( $_POST['group_total_spent_mark'] ) : '';
			$group_total_spent        = ( isset( $_POST['group_total_spent'] ) ) ? (string) stripslashes( $_POST['group_total_spent'] ) : '';
			$group_user_role          = ( isset( $_POST['group_user_role'] ) ) ? (string) stripslashes( $_POST['group_user_role'] ) : '';
			$group_customer_status    = ( isset( $_POST['group_customer_status'] ) ) ? $_POST['group_customer_status'] : array();
			$group_product_categories = ( isset( $_POST['group_product_categories'] ) ) ? $_POST['group_product_categories'] : array();
			$group_order_status       = ( isset( $_POST['group_order_status'] ) ) ? $_POST['group_order_status'] : array();
			$group_last_order         = ( isset( $_POST['group_last_order'] ) ) ? (string) stripslashes( $_POST['group_last_order'] ) : '';
			$group_last_order_from    = ( isset( $_POST['group_last_order_from'] ) ) ? (string) stripslashes( $_POST['group_last_order_from'] ) : '';
			$group_last_order_to      = ( isset( $_POST['group_last_order_to'] ) ) ? (string) stripslashes( $_POST['group_last_order_to'] ) : '';
		}
		?>
		<div class="wrap woocommerce">
			<div class="icon32 icon32-groups" id="icon-woocommerce"><br/></div>
		    <h2><?php _e( 'Customer Groups', 'woocommerce' ) ?></h2>
		    <?php if ($action_deleted){ echo $action_deleted; } ?>
		    <br class="clear" />
		    <div id="col-container">
		    	<div id="col-right">
		    		<div class="col-wrap">
			    		<form action="admin.php?page=wc_user_grps" method="post">
			    			<?php
				    			$wc_crm_customer_groups_table->prepare_items();
				    			$wc_crm_customer_groups_table->display();
				    		?>
			    		</form>
		    		</div>
		    	</div>
		    	<div id="col-left">
		    		<div class="col-wrap">
		    			<div class="form-wrap">
		    				<h3><?php _e( 'Add New group', 'wc_customer_relationship_manager' ) ?></h3>
		    				<p><?php _e( 'Groups are used to organise your customers. Please Note: you are cannot rename a group later.', 'wc_customer_relationship_manager' ) ?></p>
		    				<form action="admin.php?page=wc_user_grps" method="post" style=" padding-bottom: 150px;">
								<div class="form-field">
									<label for="f_group_name"><?php _e( 'Name', 'woocommerce' ); ?></label>
									<input name="group_name" id="f_group_name" type="text" value="<?php echo  $group_name; ?>" />
									<p class="description"><?php _e( 'Name for the group.', 'wc_customer_relationship_manager' ); ?></p>
								</div>

								<div class="form-field">
									<label for="f_group_slug"><?php _e( 'Slug', 'woocommerce' ); ?></label>
									<input name="group_slug" id="f_group_slug" type="text" value="<?php echo  $group_slug; ?>" maxlength="28" />
									<p class="description"><?php _e( 'Unique slug/reference for the group; must be shorter than 28 characters.', 'wc_customer_relationship_manager' ); ?></p>
								</div>

								<div class="form-field">
									<label for="f_group_type"><?php _e( 'Type', 'wc_customer_relationship_manager' ); ?></label>
									<select name="group_type" id="f_group_type">
										<option value="dynamic" <?php selected( $group_type, 'dynamic' ); ?> ><?php _e( 'Dynamic', 'wc_customer_relationship_manager' ) ?></option>
										<option value="static" <?php selected( $group_type, 'static' ); ?> ><?php _e( 'Static', 'wc_customer_relationship_manager' ) ?></option>
										<?php do_action('wc_crm_customer_group_types'); ?>
									</select>
									<p class="description"><?php _e( 'Determines how you select group for customers.', 'wc_customer_relationship_manager' ); ?></p>
								</div>
								<div class="form-field dynamic_group_type">
									<label for="group_total_spent"><?php _e( 'Total Spent', 'wc_customer_relationship_manager' ); ?></label>
									<select name="group_total_spent_mark" id="group_total_spent_mark">
										<option value="equal" <?php selected( $group_total_spent_mark, 'equal' ); ?>><?php _e( '=', 'wc_customer_relationship_manager' ) ?></option>
										<option value="greater" <?php selected( $group_total_spent_mark, 'greater' ); ?>><?php _e( '&gt;', 'wc_customer_relationship_manager' ) ?></option>
										<option value="less" <?php selected( $group_total_spent_mark, 'less' ); ?>><?php _e( '&lt;', 'wc_customer_relationship_manager' ) ?></option>
										<option value="greater_or_equal" <?php selected( $group_total_spent_mark, 'greater_or_equal' ); ?>><?php _e( '&ge;', 'wc_customer_relationship_manager' ) ?></option>
										<option value="less_or_equal" <?php selected( $group_total_spent_mark, 'less_or_equal' ); ?>><?php _e( '&le;', 'wc_customer_relationship_manager' ) ?></option>
									</select>
									<input type="number" step="any" id="group_total_spent" name="group_total_spent" style="width: 40%;" value="<?php echo  $group_total_spent; ?>">
								</div>
								<div class="form-field dynamic_group_type">
									<label for="group_user_role"><?php _e( 'User Role', 'wc_customer_relationship_manager' ); ?></label>
									<select name="group_user_role" id="group_user_role">
										<option value="any">
					            	<?php _e( 'Any', 'wc_customer_relationship_manager' ); ?>
				            </option>
										<option value="guest">
					            	<?php _e( 'Guest', 'wc_customer_relationship_manager' ); ?>
				            </option>
										<?php
					          global $wp_roles;
					          foreach ( $wp_roles->role_names as $role => $name ) : ?>
					            <option value="<?php echo strtolower($name); ?>" <?php selected( $group_user_role, strtolower($name) ); ?>>
					            	<?php _e( $name, 'wc_customer_relationship_manager' ); ?>
					            </option>
					          <?php
					          endforeach;
					          ?>
									</select>
								</div>
								<div class="form-field dynamic_group_type">
									<label for="group_customer_status"><?php _e( 'Customer Status', 'wc_customer_relationship_manager' ); ?></label>
									<select name="group_customer_status[]" id="group_customer_status" multiple="multiple" data-placeholder="<?php _e( 'Choose a Customer Status...', 'wc_customer_relationship_manager' ); ?>">
										<?php
					          global $statuses;
					          foreach ( $statuses as $statuse => $name ) : ?>
					            <option value="<?php echo strtolower($name); ?>" <?php echo in_array(strtolower($name), $group_customer_status) ? 'selected="selected"' : ''; ?>>
					            	<?php _e( $name, 'wc_customer_relationship_manager' ); ?>
					            </option>
					          <?php
					          endforeach;
					          ?>
									</select>
								</div>
								<div class="form-field dynamic_group_type">
									<label for="group_product_categories"><?php _e( 'Product Category', 'wc_customer_relationship_manager' ); ?></label>
									<select name="group_product_categories[]" id="group_product_categories" multiple="multiple" data-placeholder="<?php _e( 'Choose a Product Category...', 'wc_customer_relationship_manager' ); ?>">
										<?php
					          $all_cat = get_terms( array('product_cat'),  array( 'orderby' => 'name', 'order' => 'ASC', 'hide_empty' => false, )  );
					          if(!empty($all_cat)){
					            foreach ($all_cat as $cat) {?>
					            	<option value="<?php echo $cat->term_id; ?>" <?php echo in_array($cat->term_id, $group_product_categories) ? 'selected="selected"' : ''; ?>>
					            		<?php echo $cat->name; ?>
					            	</option>
					          	<?php
					            }
					          }
					          ?>
									</select>
								</div>
								<div class="form-field dynamic_group_type">
									<label for="group_order_status"><?php _e( 'Order Status', 'wc_customer_relationship_manager' ); ?></label>
									<select name="group_order_status[]" id="group_order_status" multiple="multiple" data-placeholder="<?php _e( 'Choose a Product Category...', 'wc_customer_relationship_manager' ); ?>">
										<?php
					          $wc_statuses = wc_get_order_statuses();
					          if(!empty($wc_statuses)){
					            foreach ($wc_statuses as $key => $status_name) {?>
					            	<option value="<?php echo $key; ?>" <?php echo in_array($key, $group_order_status) ? 'selected="selected"' : ''; ?>>
					            		<?php echo $status_name; ?>
					            	</option>
					          	<?php
					            }
					          }
					          ?>
									</select>
								</div>
								<div class="form-field dynamic_group_type">
									<label for="group_last_order"><?php _e( 'Last Order', 'wc_customer_relationship_manager' ); ?></label>
									<div class="wrap_date">
										<select name="group_last_order" id="group_last_order">
											<option value="between" <?php selected( $group_last_order, 'between' ); ?>><?php _e( 'Between', 'wc_customer_relationship_manager' ); ?></option>
											<option value="before"  <?php selected( $group_last_order, 'before' ); ?>><?php _e( 'Before', 'wc_customer_relationship_manager' ); ?></option>
											<option value="after"   <?php selected( $group_last_order, 'after' ); ?>><?php _e( 'After', 'wc_customer_relationship_manager' ); ?></option>
										</select>
									</div>
									<div class="wrap_date">
										<input type="text" id="group_last_order_from" name="group_last_order_from" value="<?php echo $group_last_order_from; ?>">
										<i class="ico_calendar"></i>
									</div>
									<div class="wrap_date group_last_order_between" style="height: 30px; line-height: 30px; padding: 0 10px;">
										to
									</div>
									<div class="wrap_date group_last_order_between">
										<input type="text" id="group_last_order_to" name="group_last_order_to" value="<?php echo $group_last_order_to; ?>">
										<i class="ico_calendar"></i>
									</div>
									<div class="clear"></div>
								</div>

								<p class="submit"><input type="submit" name="wc_crm_add_new_group" id="submit" class="button" value="<?php _e( 'Add group', 'wc_customer_relationship_manager' ); ?>"></p>
								<?php wp_nonce_field( 'wc-crm-add-new-group' ); ?>
		    				</form>
		    			</div>
		    		</div>
		    	</div>
		    </div>
		    <script type="text/javascript">
			/* <![CDATA[ */

				jQuery('a.delete').click(function(){
		    		var answer = confirm ("<?php _e( 'Are you sure you want to delete this group?', 'wc_customer_relationship_manager' ); ?>");
					if (answer) return true;
					return false;
		    	});

			/* ]]> */
			</script>
		</div>
		<?php
	}
}

endif;

 function convert_group_total_spent_mark($value='')
  {
    switch ($value) {
      case 'equal':
        return '=';
        break;
      case 'greater':
        return '>';
        break;
      case 'less':
        return '<';
        break;
      case 'greater_or_equal':
        return '&ge;';
        break;
      case 'less_or_equal':
        return '&le;';
        break;
    }
  }

return new WC_Crm_Customer_Groups();