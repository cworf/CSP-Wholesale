<?php
function set_all_privilege($post) {
        global $wpdb;
        $_POST = $post;

        //Company logo update code
        $company_logo = $post['smart_manager_company_logo'];
        update_option('smart_manager_company_logo', $company_logo);

        $query = "INSERT INTO {$wpdb->prefix}options ( option_name, option_value, autoload) VALUES ";
        $all_roles = get_editable_roles();
        if( isset( $all_roles['administrator'] ) ){
            unset( $all_roles['administrator']);
        }
        $role = array_keys($all_roles);
        for ( $i = 0; $i < count($role); $i++) {
                if ( $role[$i] != 'administrator' )	{
                        if (isset($_POST['sm_'.$role[$i].'_dashboard'])) {
                                $query .= "( 'sm_$role[$i]_dashboard', '".serialize($_POST['sm_'.$role[$i].'_dashboard'])."', 'no'), ";
                        } else {
                                $query .= "( 'sm_$role[$i]_dashboard', '', 'no'), ";
                        }
                }
        }
        $query = substr( $query, 0, -2);
        $query .= " ON DUPLICATE KEY UPDATE option_value = VALUES ( option_value );";
        $wpdb->query ($query);
        echo "<div id='message' class='updated fade'><p>";
        printf ( __ ( 'Smart Manager Settings <b>Updated</b>','smart-manager' ));
        echo "</p></div>";
        get_all_privilege();
}

function get_all_privilege() {
        global $wpdb;
        $query = "SELECT option_name, option_value FROM {$wpdb->prefix}options WHERE option_name LIKE 'sm_%_dashboard';";
        $results = $wpdb->get_results ($query);
        $rows_roles = $wpdb->num_rows;

        $roles = array();
        foreach ($results as $obj) {
                $roles_detail = unserialize($obj->option_value);
                $roles[$obj->option_name] = $roles_detail;
        }

        $all_roles = get_editable_roles();

        if( isset( $all_roles['administrator'] ) ){
            unset( $all_roles['administrator']);
        }

        foreach ( $all_roles as $role => $details) {
                $name = translate_user_role($details['name'] );
                $checked = array();

                if($rows_roles > 0) {
                    for ( $i = 0; $i < count($roles['sm_'.$role.'_dashboard']); $i++ ) {
                            $checked[$roles['sm_'.$role.'_dashboard'][$i]] = 'checked';
                    }    
                }
                
                $products_checked = (isset($checked['Products']) && $checked['Products'] === 'checked') ? 'checked' : 'unchecked';
                $customers_orders_checked = (isset($checked['Customers_Orders']) && $checked['Customers_Orders'] === 'checked') ? 'checked' : 'unchecked';
                if ( $name != 'Administrator' ) {
                        echo "
                                <tr>
                                        <th scope='row'>$name</th>
                                        <td align='center'><input name='sm_".$role."_dashboard[]' type='checkbox' id='Products' value='Products' ".$products_checked."></td>
                                        <td align='center'><input name='sm_".$role."_dashboard[]' type='checkbox' id='Customers_Orders' value='Customers_Orders' ".$customers_orders_checked."></td>
                                </tr>
                        ";
                }
        }
}		


if (is_plugin_active ( 'wp-e-commerce/wp-shopping-cart.php' )) {
    $plug_page = 'wpsc';
    $type = 'wpsc-product';
}
elseif (is_plugin_active ( 'woocommerce/woocommerce.php' )) {
    $plug_page = 'woo';
    $type = 'product';
}


?>

<!-- Code to handle the media library for company logo -->
<script type="text/javascript">

jQuery(document).ready(function($){

  var file_frame;
  
  $('#upload_image_button').live('click', function( event ){

    event.preventDefault();

    // If the media frame already exists, reopen it.
    if ( file_frame ) {
      file_frame.open();
      return;
    }

    // Create the media frame.
    file_frame = wp.media.frames.file_frame = wp.media({
      title: jQuery( this ).data( 'uploader_title' ),
      button: {
        text: jQuery( this ).data( 'uploader_button_text' ),
      },
      multiple: false  // Set to true to allow multiple files to be selected
    });

    // When an image is selected, run a callback.
    file_frame.on( 'select', function() {
      // We set multiple to false so only get one image from the uploader
      attachment = file_frame.state().get('selection').first().toJSON();

      // Send the value of attachment.url back to PIP settings form
      jQuery('#smart_manager_company_logo').val(attachment.url);
    });

    // Finally, open the modal
    file_frame.open();
  });
});

  function myMediaPopupHandler()
    {
        window.send_to_editor = function(html) {
            imgurl = jQuery('img',html).attr('src');
            jQuery('#smart_manager_company_logo').val(imgurl);
            tb_remove();
        }

        formfield = jQuery('#smart_manager_company_logo').attr('name');
        tb_show('', '<?php echo admin_url(); ?>media-upload.php?type=image&tab=upload&TB_iframe=true');
        return false;
    }
</script>

<div class="wrap">
<div id="icon-smart-manager" class="icon32"><br/></div>
<h2>Smart Manager Settings</h2>
	
<form action="" method="post">

<h3 style="font-size:120%;"><?php _e('Privileges for different System Roles','smart-manager');?></h3><br/>

<table class="form-table">
	<tbody>
		<tr>
			<th><b>Roles</b></th>
			<td align="center"><b><?php _e('Can Manage Products?','smart-manager');?></b></td>
			<td align="center"><b><?php _e('Can Manage Customers & Orders?','smart-manager')?></b></td>
		</tr>
		<?php
			if (isset($_POST['submit']) && $_POST['submit'] === 'Apply') {
				set_all_privilege($_POST);
			} else {
				get_all_privilege();
			}
		?>
		
	</tbody>
</table>

<!-- Code for Display part for entering the Company Logo -->
<br/><br/>
<h3 style="font-size:120%;"><?php _e('Print Order Settings','smart-manager');?></h3>

<table cellspacing="15" cellpadding="5">
    <tr>
        <th>
      <label for="smart_manager_company_logo"><b><?php _e( 'Company logo:', 'smart-manager' ); ?></b></label>
        </th>
    <td>
       
        <input id="smart_manager_company_logo" type="text" style="margin:1px; padding:3px;" size="36" name="smart_manager_company_logo" value="<?php echo get_option( 'smart_manager_company_logo' ); ?>" />
        <input id="upload_image_button" type="button" style="margin:1px; padding:3px; cursor: pointer;" value="<?php _e( 'Upload Image', 'smart-manager' ); ?>" />
                      
    </td></tr></table>

<p class="submit"><input type="submit" name="submit" id="submit" class="button-primary" value="Apply">
<a href="edit.php?post_type=<?php echo $type ?>&amp;page=smart-manager-<?php echo $plug_page ?>"> <?php _e('Back to Smart Manager') ?> </a>
</p>
</form>
</div>