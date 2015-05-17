<?php

class UPME_Save {

    public $allowed_extensions;
    public $usermeta;
	public $allowed_exts;
    function __construct() {

        $this->upme_fileds_array = get_option('upme_profile_fields');
        $this->upme_fileds_meta_value_array = array();
        $this->upme_fileds_meta_type_array = array();
        foreach ($this->upme_fileds_array as $key => $value) {
            $this->upme_fileds_meta_value_array[$value['meta']] = $value['name'];
            $this->upme_fileds_meta_type_array[$value['meta']] = isset($value['field']) ? $value['field'] : '';
        }

        add_action('init', array($this, 'handle_init'));

        $this->errors = null;

    }

    /* Prepare user meta */

    function prepare($array) {
        foreach ($array as $k => $v) {
            $k = str_replace('-' . $this->userid, '', $k);
            if ($k == 'upme-submit')
                continue;
            $this->usermeta[$k] = $v;
        }
        return $this->usermeta;
    }

    /* Process uploads */

    function process_upload($array) {

        /* File upload conditions */
        $this->allowed_extensions = array("image/gif", "image/jpeg", "image/png");
        
		$this->allowed_exts = array('gif','png','jpeg','jpg');

        $this->allowed_non_image_extensions = apply_filters('upme_non_image_extensions',array());
        $this->allowed_non_image_exts = apply_filters('upme_non_image_exts',array());

        $settings = get_option('upme_options');


        // Set default to 500KB
        $this->max_size = 512000;
        
        $this->image_height = 0;
        $this->image_width  = 0;

        // Setting Max File Size set from admin
        if (isset($settings['avatar_max_size']) && $settings['avatar_max_size'] > 0)
            $this->max_size = $settings['avatar_max_size'] * 1024 * 1024;

        if (isset($_FILES)) {
            foreach ($_FILES as $key => $array) {
                
                extract($array);
                if ($name) {

                    $clean_file = true;

                    if(in_array($type, $this->allowed_extensions)){
                        // Security Check Start
                        // Checking for Image size. If this is a valid image (not tempered) then this function will return width and height and other values in return.
                        $image_data = @getimagesize($tmp_name);

                        
                        if (!isset($image_data[0]) || !isset($image_data[1])){
                            $clean_file = false;
                            
                        }else{
                            $this->image_height = $image_data[1];
                            $this->image_width  = $image_data[0];
                        }
                            
                        // Security Check End
                    }                   

                    $clean_key = str_replace('-' . $this->userid, '', $key);

                    /* UPME action for adding restrictions before uploading files */
                    $before_upload_profile_files_params = array();
                    do_action('upme_before_upload_profile_files', $this->userid, $clean_key, $before_upload_profile_files_params);
                    /* END action */   
                    
                    $field_label = $this->upme_fileds_meta_value_array[$clean_key];

                    if (!in_array($type, $this->allowed_extensions) && !in_array($type, $this->allowed_non_image_extensions)) {
                        $this->errors[$clean_key] = sprintf(__('The file you have selected for %s has a file extension that is not allowed. Please choose a different file.','upme'), $field_label).'<br/>';
                    } elseif ($size > $this->max_size) {
                        $this->errors[$clean_key] = sprintf(__('The file you have selected for %s exceeds the maximum allowed file size.', 'upme'), $field_label).'<br/>';
                    } elseif ($clean_file == false) {
                        $this->errors[$clean_key] = sprintf(__('The file you selected for %s appears to be corrupt or not a real image file.', 'upme'), $field_label).'<br/>';
                    } elseif (!preg_match("/.(".implode("|",$this->allowed_exts).")$/i",$name) && !preg_match("/.(".implode("|",$this->allowed_non_image_exts).")$/i",$name)) {
						$this->errors[$clean_key] = sprintf(__('The file you have selected for %s has a file extension that is not allowed. Please choose a different file.', 'upme'), $field_label).'<br/>';
					} 

                    else {
                        
                        $upload_file_custom_validation_params = array('id'=>$this->userid, 'key'=>$key, 'height'=>$this->image_height, 'width'=> $this->image_width, 'field_label'=>$field_label );
                        $custom_errors = apply_filters('upme_upload_file_custom_validation',array('status'=>false, 'msg'=>'') ,$upload_file_custom_validation_params);

                        if(!$custom_errors['status']){
                            /* Upload image */
                            // Checking for valid uploads folder
                            if ($upload_dir = upme_get_uploads_folder_details()) {
                                $target_path = $upload_dir['basedir'] . "/upme/";

                                // Checking for upload directory, if not exists then new created.
                                if (!is_dir($target_path))
                                    mkdir($target_path, 0777);

                                $base_name = sanitize_file_name(basename($name));

                                $target_path = $target_path . time() . '_' . $base_name;

                                $nice_url = $upload_dir['baseurl'] . "/upme/";
                                $nice_url = $nice_url . time() . '_' . $base_name;
                                move_uploaded_file($tmp_name, $target_path);

                                /* Clean the previous file allocated for the current upload field */
                                $current_field_url = get_user_meta($this->userid, $clean_key, true);
                                if('' != $current_field_url){
                                    upme_delete_uploads_folder_files($current_field_url);                                
                                }                            

                                /* Now we have the nice url */
                                /* Store in usermeta */
                                update_user_meta($this->userid, $clean_key, $nice_url);
                            }
                        }else{
                            $this->errors[$clean_key] = $custom_errors['msg'];
                        }
                    }

                    /* UPME action for removing restrictions after uploading files */
                    $after_upload_profile_files_params = array();
                    do_action('upme_after_upload_profile_files', $this->userid, $clean_key, $after_upload_profile_files_params);
                    /* END action */
                }
            }
        }
    }

    /* Handle/return any errors */

    function handle() {
        if (is_array($this->usermeta)) {
            foreach ($this->usermeta as $key => $value) {

                /* Validate email */
                if ($key == 'user_email') {
                    if (!is_email($value)) {
                        $this->errors[$key] = __('E-mail address was not updated. Please enter a valid e-mail.', 'upme');
                    }
                }

                /* Validate password */
                if ($key == 'user_pass') {
                    if (esc_attr($value) != '') {
                        if ($this->usermeta['user_pass'] != $this->usermeta['user_pass_confirm']) {
                            $this->errors[$key] = __('Your passwords do not match.', 'upme');
                        }
                    }
                }


                /* UPME filter for adding restrictions before custom field type saving */
                $frontend_custom_field_type_restrictions_params = array('meta' => $key, 'value' => $value);
                $this->errors = apply_filters('upme_frontend_custom_field_type_restrictions', $this->errors, $frontend_custom_field_type_restrictions_params);
                /* END filter */ 
            }
        }
    }

    /* Update user meta */

    function update() {
        require_once(ABSPATH . 'wp-includes/pluggable.php');

        /* Update profile when there is no error */
        if (!isset($this->errors)) {

            // Get list of dattime fields
            $date_time_fields = array();

            foreach ($this->upme_fileds_array as $key => $field) {
                extract($field);

                if (isset($this->upme_fileds_array[$key]['field']) && $this->upme_fileds_array[$key]['field'] == 'checkbox') {
                    update_user_meta($this->userid, $meta, null);
                }

                // Filter date/time custom fields
                if (isset($this->upme_fileds_array[$key]['field']) && $this->upme_fileds_array[$key]['field'] == 'datetime') {
                    array_push($date_time_fields, $this->upme_fileds_array[$key]['meta']);
                }
            }


            if (is_array($this->usermeta)) {

                $changed_fields = array();
                $limited_fields = array();
                $param_field_updates = array('user_id' => $this->userid);
                $limited_fields = apply_filters('upme_trigger_field_update',$limited_fields,$param_field_updates);

                foreach ($this->usermeta as $key => $value) {

                    /* Update profile when there is no error */
                    if (!isset($this->errors[$key])) {

                        // save checkboxes
                        if (is_array($value)) { // checkboxes
                            $value = implode(', ', $value);
                        }

                        //
                        // Set date format from admin settings
                        $upme_settings = get_option('upme_options');
                        $upme_date_format = (string) isset($upme_settings['date_format']) ? $upme_settings['date_format'] : 'mm/dd/yy';

                        if (in_array($key, $date_time_fields)) {
                            if (!empty($value)) {
                                $formatted_date = upme_date_format_to_standerd($value, $upme_date_format);
                                $value = $formatted_date;
                            }
                        }

                        /* UPME Actions for checking extra fields or hidden data in profile edit form */                    
                        if(in_array($key, $limited_fields)){
                            
                            $prev_value = get_user_meta($this->userid,$key,true);
                            if($prev_value != esc_attr($value) ){
                                array_push($changed_fields, array('meta'=>$key, 'prev_value'=> $prev_value, 'new_value'=>$value));
                            }
                        }
                        // End Filter

                        // Prevent passwords from saving in user meta table
                        if('user_pass' != $key && 'user_pass_confirm' != $key){
                            update_user_meta($this->userid, $key, esc_attr($value));
                        }
                        

                        /* update core fields - email, url, pass */
                        if ((in_array($key, array('user_email', 'user_url', 'display_name')) ) || ($key == 'user_pass' && esc_attr($value) != '')) {

                            $result = wp_update_user(array('ID' => $this->userid, $key => esc_attr($value)));

                            /* UPME Action for after changing password */
                            if(!is_wp_error($result) && 'user_pass' == $key){
                                do_action('upme_after_password_change', $this->userid);
                            }
                            // End Filter


                        }
                    }
                }

                if(is_array($changed_fields) && count($changed_fields) != 0){

                    $this->notify_field_update = true;

                    /* UPME Actions for executing custom functions on profile data change */
                    $profile_field_update_triggered_params = array('changed_fields' => $changed_fields, 'user_id' => $this->userid );
                    do_action('upme_profile_field_update_triggered',$profile_field_update_triggered_params);                   
                    // End action

                    if($this->notify_field_update){
                        $full_name = get_user_meta($this->userid, 'first_name', true). ' ' . get_user_meta($this->userid, 'last_name', true);
                        $subject = __('Profile Information Update','upme');
                        $message = sprintf(__('%s has updated profile information.','upme'), $full_name) . "\r\n\r\n";
                        $message .= sprintf(__('Please find the updated information below.','upme'), $full_name) . "\r\n\r\n";

                        foreach ($changed_fields as $key => $value) {
                            $message .= __('Field Key','upme'). "   :" . $value['meta']. "\r\n";
                            $message .= __('Previous Value','upme'). "   :" . $value['prev_value']. "\r\n";
                            $message .= __('Updated Value','upme'). "   :" . $value['new_value']. "\r\n\r\n";
                        }

                        $message .= __('Thanks','upme') . "\r\n";
                        $message .= sprintf(__('%s'), get_option('blogname'),'upme') . "\r\n";

                        global $upme_email_templates;
                        $send_params = array('full_name' => $full_name, 'changed_fields' => $changed_fields);
                        $upme_email_templates->upme_send_emails('nofify_profile_update', get_option('admin_email'),$subject,$message,$send_params,$this->userid);
                        
                        // wp_mail(
                        //     get_option('admin_email'),
                        //     $subject,
                        //     $message
                        // );
                        
                    }
                    
                }
                // Implementing the email sending capabilities
            }

        }
    }

    /* Get errors display */

    function get_errors($id) {
        global $upme;
        $display = null;

        /* UPME action for adding errors before save profile */
        $before_profile_update_error_params = array('post_data' => $_POST, 'files' => $_FILES);
        do_action('upme_before_profile_update_errors', $this->userid, $before_profile_update_error_params);
        /* END action */       

        if (isset($this->errors) && count($this->errors) > 0) {
            $display .= '<div class="upme-errors">';
            foreach ($this->errors as $newError) {
                $display .= '<span class="upme-error"><i class="upme-icon upme-icon-remove"></i>' . $newError . '</span>';
            }
            $display .= '</div>';
        } else {
            /* Success message */
            if ($id == $upme->logged_in_user) {
                $display .= '<div class="upme-success"><span><i class="upme-icon upme-icon-ok"></i>' . __('Your profile was updated.', 'upme') . '</span></div>';
            } else {
                $display .= '<div class="upme-success"><span><i class="upme-icon upme-icon-ok"></i>' . __('Profile was updated.', 'upme') . '</span></div>';
            }
        }
        return $display;
    }

    /* Initializing login class on init action  */

    function handle_init() {
        
        /* Form is fired */
        foreach ($_POST as $k => $v) {
            if (strstr($k, 'upme-submit-')) {

                // User ID
                $this->userid = str_replace('upme-submit-', '', $k);

                /* UPME action before save profile */
                $before_profile_update_params = array('post_data' => $_POST, 'files' => $_FILES);
                do_action('upme_before_profile_update', $this->userid, $before_profile_update_params);
                /* END action */

                // Prepare fields prior to update
                $this->prepare($_POST);

                // upload files
                $this->process_upload($_FILES);

                // Error handler
                $this->handle();

                // Update fields
                $this->update();

                /* action after save profile */
                do_action('upme_profile_update', $this->userid);
            } else if (strstr($k, 'upme-upload-submit-')) {

                // User ID
                $this->userid = str_replace('upme-upload-submit-', '', $k);

                // upload files
                $this->process_upload($_FILES);
            } else if (strstr($k, 'upme-crop-submit-')) {

                // User ID
                $this->userid = str_replace('upme-crop-submit-', '', $k);
            } else if (strstr($k, 'upme-crop-save-')) {

                // User ID
                $this->userid = str_replace('upme-crop-save-', '', $k);
            }
        }
    }

}

$upme_save = new UPME_Save();