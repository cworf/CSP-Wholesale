<?php
/**
 * Submenu page for in admin area
 * Import & Export Page
 *
 * @package   Go - Responsive Pricing & Compare Tables
 * @author    Granth <granthweb@gmail.com>
 * @link      http://granthweb.com
 * @copyright 2014 Granth
 */
 
$screen = get_current_screen();

/* Get table data db data */
$pricing_tables = get_option( GW_GO_PREFIX . 'tables', array() );

/* Handle post */
if ( !empty( $_POST ) && check_admin_referer( 'go-pricing' . basename( __FILE__ ), 'go-pricing' . '-nonce' ) ) {

	$reponse = array();
	$referrer=$_POST['_wp_http_referer'];

	/* Default Page POST */
	if ( isset( $_POST['action-type'] ) ) {
		
		/* Export action - validate & redirect */
		if ( $_POST['action-type'] == 'export' ) {
					
			if ( isset( $_POST['export'] ) ) {
				
				/* Set temporary POST data */
				set_transient( md5( $screen->id . '-data' ), $_POST, 30 );
				
				/* Redirect */
				wp_redirect( admin_url( 'admin.php?page=' . $_GET['page'] . '&action=export' ) );
				exit;
				
			} else {
				
				/* Set the reponse message */
				$response['result'] = 'error';
				$response['message'][] = __( 'There is nothing to export!', GW_GO_TEXTDOMAN );
				set_transient( md5( $screen->id . '-response' ), $response, 30 );
				
				/* Redirect */
				$referrer = preg_match( '/&updated=true$/', $referrer ) ? $referrer : $referrer . '&updated=true';
				wp_redirect( $referrer );
				exit;
			}
			
		/* Import action - validate & redirect */
		} elseif ( $_POST['action-type'] == 'import' ) {
				
			if ( isset( $_POST['raw-import'] ) && $_POST['raw-import'] != '' ) {
				$import_data = !empty( $_POST['raw-import'] ) ?  @unserialize( base64_decode( $_POST['raw-import'] ) ) : '';

				/* Validate import data */
				if ( !is_array( $import_data ) ) {
					
					/* Set the reponse message */
					$response['result'] = 'error';
					$response['message'][] = __( 'Invalid import data!', GW_GO_TEXTDOMAN );
					set_transient( md5( $screen->id . '-response' ), $response, 30 );

					/* Redirect */
					$referrer = preg_match( '/&updated=true$/', $referrer ) ? $referrer : $referrer. '&updated=true';
					wp_redirect( $referrer );
					exit;
					
				} else {

					/* Set temporary POST data */
					set_transient( md5( $screen->id . '-data' ), $import_data, 60 );

					/* Redirect */
					wp_redirect( admin_url( 'admin.php?page=' . $_GET['page'] . '&action=import' ) );
					exit;					
				}
			
			} else {

				/* Set the reponse message */
				$response['result'] = 'error';
				$response['message'][] = __( 'There is nothing to import!', GW_GO_TEXTDOMAN );
				set_transient( md5( $screen->id . '-response' ), $response, 30 );
				
				/* Redirect */
				$referrer = preg_match( '/&updated=true$/', $referrer ) ? $referrer : $referrer. '&updated=true';
				wp_redirect( $referrer );
				exit;
			}					
		}
	
	/* Import Page POST */
	} elseif( isset( $_POST['import'] ) ) {


		/* clean post fields */
		foreach( $_POST['import'] as $key=>$value ) {
			if ( is_array( $_POST['import'][$key] ) ) {
				foreach( $_POST['import'][$key] as $skey=>$svalue ) {
					if ( strlen( $_POST['import'][$key][$skey] ) ) { 
						$_POST['import'][$key][$skey] = stripslashes( $_POST['import'][$key][$skey] );
						}
					$_POST['import'][$key][$skey] = trim( $_POST['import'][$key][$skey] );
				}
			} else {
				if ( strlen( $_POST['import'][$key] ) ) { $_POST['import'][$key] = stripslashes( $_POST['import'][$key] ); }
				$_POST['import'][$key] = strip_tags( trim( $_POST['import'][$key] ) );
			}
		}
	
		/* Get temporary POST data */
		$temp_post_data = get_transient( md5( $screen->id . '-data' ) );
		
		/* If temporary POST data missing */
		if ( !$temp_post_data ) { 
			wp_redirect( admin_url( 'admin.php?page=' . $_GET['page'] ) );
			exit;
		} else {
			delete_transient( md5( $screen->id . '-data' ) );	
		}

		/* Import pricing tables */
		if ( isset( $_POST['import'] ) && !empty( $_POST['import'] ) ) {
			$id_list = array();

			/* If 'all' option has been selected */
			if ( isset( $_POST['import']['all'] ) ) {
				$all_tables  = explode (',', $_POST['import']['all']);
				foreach( $all_tables as $table ) { $_POST['import'][$table]=''; }
				unset( $_POST['import']['all'] );			
			}
			
			$imported_tables_cnt=0;
			$replaced_tables_cnt=0;
			foreach( $_POST['import'] as $import_pricing_table_key => $import_pricing_table ) {
				$imported_tables_cnt++;
				echo $_POST['import'][$import_pricing_table_key];
				$_POST['import'][$import_pricing_table_key]=$temp_post_data[$import_pricing_table_key];				
				$id_list[$import_pricing_table_key] = $_POST['import'][$import_pricing_table_key]['table-id'];
				if ( isset( $pricing_tables ) && !empty( $pricing_tables ) ) {
					foreach( $pricing_tables as $pricing_table_key => $pricing_table ) {
						if ( $pricing_table_key ==  $import_pricing_table_key ) {
							if ( isset( $_POST['replace'] ) ) {
								unset( $pricing_tables[$import_pricing_table_key] );
							} else {
								$uniqid=uniqid();
								$_POST['import'][$uniqid] = $_POST['import'][$import_pricing_table_key];
								$_POST['import'][$uniqid]['uniqid'] = $uniqid;
								$_POST['import'][$uniqid]['table-id'] = $pricing_tables[$import_pricing_table_key]['table-id'] . '_copy_' . $uniqid;
								$_POST['import'][$uniqid]['table-name'] = $pricing_tables[$import_pricing_table_key]['table-name'] . ' copy ' . $uniqid;
								$id_list[$uniqid]=$_POST['import'][$uniqid]['table-id'];								
								unset( $id_list[$import_pricing_table_key] );
								unset( $_POST['import'][$import_pricing_table_key] );
							}
						}
					}
					foreach( $pricing_tables as $pricing_table_key => $pricing_table ) {
						$key = array_search( $pricing_table['table-id'], $id_list );
						if ( $key && isset( $_POST['import'][$key]['table-id'] ) ) {
							$replaced_tables_cnt++; 
							$_POST['import'][$key]['table-id'] = $_POST['import'][$key]['table-id'] . '_copy_' . $key;
						}						
					}					
				}
			}
			if ( isset( $pricing_tables ) && empty( $pricing_tables ) ) { $pricing_tables = array(); } 
			$pricing_tables = array_merge( $pricing_tables, $_POST['import'] );
			
			/* Save to db */
			update_option( GW_GO_PREFIX . 'tables', $pricing_tables );
			
			/* Set the reponse message */
			$response['result'] = 'success';
			$response['message'][] = sprintf( __( '%1$d pricing table(s) has been imported.', GW_GO_TEXTDOMAN ), $imported_tables_cnt );
		}
		
		/* Redirect */
		set_transient( md5( $screen->id . '-response' ), $response, 30 );
		wp_redirect( admin_url( 'admin.php?page=' . $_GET['page'] . '&updated=true' ) );
		exit;
		
	} else {	
	
		/* User didn't select anything */
		
		/* Set the reponse message */
		$response['result'] = 'error';
		$response['message'][] = __( 'There is nothing to import!', GW_GO_TEXTDOMAN );
		set_transient( md5( $screen->id . '-response' ), $response, 30 );
		
		/* Redirect */
		$referrer = preg_match( '/&updated=true$/', $referrer) ? $referrer : $referrer. '&updated=true';
		wp_redirect( $referrer );
		exit;					
	}
	
}

/**
 *
 * Content
 *
 */

?>
<div id="go-pricing-admin-wrap" class="wrap">
	<div id="go-pricing-admin-icon" class="icon32"></div>
	<h2><?php _e( 'Go - Responsive Pricing & Compare Tables', GW_GO_TEXTDOMAN ); ?></h2>
	<p></p>	
	<?php

	/* Print message */
	if ( isset( $_GET['updated'] ) && $_GET['updated'] == 'true' && $response = get_transient( md5( $screen->id . '-response' ) ) ) : 
	?>
	<div id="result" class="<?php echo $response['result'] == 'error' ? 'error' : 'updated'; ?>">
	<?php foreach ( $response['message'] as $error_msg ) : ?>
		<p><strong><?php echo $error_msg; ?></strong></p>
	<?php endforeach;  $response = array(); ?>
	</div>
	<?php 	
	delete_transient( md5( $screen->id . '-response' ) );
	endif;
	/* /Print message */

	?>

	<?php
		
	/**
	 *
	 * Default Page content
	 *
	 */

	if ( empty( $_POST ) && !isset( $_GET['action'] )  || ( isset( $_GET['action'] ) && empty ( $_GET['action'] ) ) ) : 
	?>
	<!-- form -->
	<form id="go-pricing-import-form" name="go-pricing-import-form" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>&noheader=true">
		<?php wp_nonce_field( 'go-pricing' . basename( __FILE__ ), 'go-pricing' . '-nonce' ); ?>

		<!-- postbox -->
		<div class="postbox">
			<h3 class="hndle"><?php _e( 'Import & Export Data', GW_GO_TEXTDOMAN ); ?><span class="gwwpa-toggle"></span></h3>
			<div class="inside">
				<table class="form-table">
					<tr>
						<th class="w150"><div><?php _e( 'Select action', GW_GO_TEXTDOMAN ); ?></div></th>
						<td class="w300">
							<select id="go-pricing-select" name="action-type" class="w200" data-parent="import-export">
								<option data-children="import" value="import"><?php _e( 'Import data', GW_GO_TEXTDOMAN ); ?></option>
								<option data-children="export" value="export"><?php _e( 'Export data', GW_GO_TEXTDOMAN ); ?></option>
							</select>
						</td>
						<td><p class="description"><?php _e( 'Import or Export table data.', GW_GO_TEXTDOMAN ); ?></p></td>
					</tr>					

					<!-- import -->
					<tr class="go-pricing-group" data-parent="import-export" data-children="import">
						<th colspan="3"><?php _e( 'To import data open the file that contains demodata and copy its content to the textarea below and click to the "Save" button.', GW_GO_TEXTDOMAN ); ?></th>
					</tr>
					<tr class="go-pricing-group" data-parent="import-export" data-children="import">
						<th colspan="3"><textarea name="raw-import" style="width:100%;" rows="10"><?php echo !empty( $temp_post_data ) ? base64_encode( serialize( $temp_post_data ) ) : ''; ?></textarea></th>
				    </tr>										
					<!-- /import -->
		
					<!-- export -->
					<?php if ( isset( $pricing_tables ) && !empty( $pricing_tables ) ) : ?>
					<tr class="go-pricing-group" data-parent="import-export" data-children="export" style="display:none;">
						<th class="w150"><div><?php _e( 'Tables', GW_GO_TEXTDOMAN ); ?></div></th>
						<td class="w300">
							<ul class="go-pricing-checkbox-list">
								<li><label><input type="checkbox" name="export[]" value="all" class="go-pricing-checkbox-parent"> <?php _e( 'All tables', GW_GO_TEXTDOMAN ); ?> [&nbsp;.&nbsp;]<span></span></label>
									<ul class="go-pricing-checkbox-list">
										<?php foreach( $pricing_tables as $pricing_table_key => $pricing_table ) : ?>
										<li><label><input type="checkbox" name="export[]" value="<?php echo esc_attr( $pricing_table_key ); ?>" /> <?php echo $pricing_table['table-name']; ?></label></li>
										<?php endforeach; ?>
									</ul>
								</li>	
							</ul>
						</td>
						<td style="vertical-align:top;"><p class="description"><?php _e( 'Select the pricing tables you would like to export.', GW_GO_TEXTDOMAN ); ?></p></td>
					</tr>
					<?php endif; ?>
					<!-- /export -->
					
				</table>
			</div> 				
		</div> 
		<!-- /postbox -->	

		<p class="submit">
			<input type="submit" class="button-primary" value="<?php esc_attr_e( 'Save', GW_GO_TEXTDOMAN ); ?>" />
		</p>

	</form>
	<!-- /form -->
	
	<?php endif; ?>
	
	<?php
		
	/**
	 *
	 * Import Page content
	 *
	 */

	if ( empty( $_POST ) && isset( $_GET['action'] ) && ( $_GET['action'] == 'import' ) ) : 
	$temp_post_data = get_transient( md5( $screen->id . '-data' ) );
	if ( !$temp_post_data ) {
		?>
		<div id="result" class="error">
		<p><strong><?php _e( 'There is nothing to import!', GW_GO_TEXTDOMAN ); ?> <a href="<?php echo esc_attr( admin_url( 'admin.php?page=' . $_GET['page'] ) ) ?>"><?php _e( 'Click here', GW_GO_TEXTDOMAN ); ?></a> <?php _e( 'for Import & Export', GW_GO_TEXTDOMAN ); ?></strong></p>
		</div>
		<?php
		exit;	
	} 
	?>
	<!-- form -->
	<form id="go-pricing-import-form" name="go-pricing-import-form" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>&noheader=true">
		<?php wp_nonce_field( 'go-pricing' . basename( __FILE__ ), 'go-pricing' . '-nonce' ); ?>
		
		<!-- postbox -->
		<div class="postbox">
			<h3 class="hndle"><?php _e( 'Import Data', GW_GO_TEXTDOMAN ); ?><span class="gwwpa-toggle"></span></h3>
			<div class="inside">
				<table class="form-table">
					<tr>
						<th colspan="3"><?php _e( 'Select the data to be imported and click to "Save" button.', GW_GO_TEXTDOMAN ); ?></th>
					</tr>
					<?php if ( isset( $temp_post_data ) && !empty( $temp_post_data ) ) : ?>
					<tr>
						<th class="w150"><div><?php _e( 'Pricing tables', GW_GO_TEXTDOMAN ); ?></div></th>
						<td class="w300">
							<ul class="go-pricing-checkbox-list">
								<li><label><input type="checkbox" name="import[all]" value="<?php echo implode( ',', array_keys( $temp_post_data ) ); ?>" class="go-pricing-checkbox-parent"> <?php _e( 'All Pricing Tables', GW_GO_TEXTDOMAN ); ?> [&nbsp;.&nbsp;]<span></span></label>
									<ul class="go-pricing-checkbox-list">
										<?php foreach( $temp_post_data as $pricing_table_key => $pricing_table ) : ?>
										<li><label><input type="checkbox" name="import[<?php echo esc_attr( $pricing_table_key ); ?>]" value="<?php echo esc_attr( $pricing_table_key ); ?>" /> <?php echo $pricing_table['table-name']; ?></label></li>
										<?php endforeach; ?>
									</ul>
								</li>	
							</ul>
						</td>
						<td style="vertical-align:top;"><p class="description"><?php _e( 'Select the pricing tables you would like to export.', GW_GO_TEXTDOMAN ); ?></p></td>
					</tr>
					<tr>
						<th class="w150"><div><?php _e( 'Replace existing items?', GW_GO_TEXTDOMAN ); ?></div></th>
						<th><label><input type="checkbox" name="replace" value="1" > <?php _e( 'Yes', GW_GO_TEXTDOMAN ); ?></label></th>
				    	<td><p class="description"><?php _e( 'Existing items with same ids will be replaced with the new ones if set, else a new copy will be created.', GW_GO_TEXTDOMAN ); ?></p></td>
					</tr>					
					<?php endif; ?>
				</table>
			</div> 				
		</div> 
		<!-- /postbox -->

		<p class="submit">
			<input type="submit" class="button-primary" value="<?php esc_attr_e( 'Save', GW_GO_TEXTDOMAN ); ?>" />
		</p>

	</form>
	<!-- /form -->	
	
	<?php endif; ?>	

	<?php	
	/**
	 *
	 * Export Page content
	 *
	 */

	if ( empty( $_POST ) && isset( $_GET['action'] ) && ( $_GET['action'] == 'export' ) ) : 

	$temp_post_data = get_transient( md5( $screen->id . '-data' ) );
	if ( $temp_post_data ) {
		delete_transient( md5( $screen->id . '-data' ) );
		
		/* Get selected pricing tables */
		if ( isset( $temp_post_data['export'] ) && !empty( $temp_post_data['export'] ) ) {
			if ( in_array( 'all', $temp_post_data['export'] ) ) {
				$export_data = $pricing_tables;
			} else {
				if ( isset( $pricing_tables ) && !empty( $pricing_tables ) ) {
					foreach( $pricing_tables as $pricing_table_key => $pricing_table ) {
						if ( in_array( $pricing_table_key, $temp_post_data['export'] ) ) {
							$export_data[$pricing_table_key] = $pricing_table;
						}
					}
				
				}
			}

		}
	} else {
		?>
		<div id="result" class="error">
		<p><strong><?php _e( 'There is nothing to export!', GW_GO_TEXTDOMAN ); ?> <a href="<?php echo esc_attr( admin_url( 'admin.php?page=' . $_GET['page'] ) ) ?>"><?php _e( 'Click here', GW_GO_TEXTDOMAN ); ?></a> <?php _e( 'for Import & Export', GW_GO_TEXTDOMAN ); ?></strong></p>
		</div>
		<?php
		exit;
	}
	?>
		
	<!-- postbox -->
	<div class="postbox">
		<h3 class="hndle"><?php _e( 'Export Data', GW_GO_TEXTDOMAN ); ?><span class="gwwpa-toggle"></span></h3>
		<div class="inside">
			<table class="form-table">
				<tr>
					<th><?php _e( 'Copy the content of the textarea below and save into file on your hard drive.', GW_GO_TEXTDOMAN ); ?></th>
				</tr>
				<tr>
					<th><textarea id="go-pricing-db-data" name="db-data" style="width:100%;" rows="10"><?php echo !empty( $export_data ) ? base64_encode( serialize( $export_data ) ) : ''; ?></textarea></th>
			   </tr>
			</table>
		</div> 				
	</div> 
	<!-- /postbox -->
	
	<?php endif; ?>	
	
</div>	