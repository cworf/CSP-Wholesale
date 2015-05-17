<?php

/*
 *  Added from version 1.8
 *
 */
add_action('admin_init', 'upme_upgrade_routine');

function upme_upgrade_routine() {

 
    $stored_version = get_option('upme_version');
    $current_version = upme_get_plugin_version();

    if (!$stored_version && $current_version) {
        upme_initialize_regular_tasks();
        update_option('upme_version', $current_version);
    }

    if (version_compare($current_version, $stored_version) == 0) {
        return;
    }


    // @TO DO Change Version No. on each upgrade
    if (version_compare('1.9.3', $stored_version) >= 0) {

        upme_upgrade_1_8();
    }

    if (version_compare('2.0.0', $stored_version) >= 0) {
        upme_upgrade_2_0();
    }

    if (version_compare('2.0.1', $stored_version) >= 0) {
        upme_upgrade_2_0_1();
    }
	
    if (version_compare('2.0.2', $stored_version) >= 0) {
        upme_upgrade_2_0_2();
    }

    if (version_compare('2.0.3', $stored_version) >= 0) {
        upme_upgrade_2_0_3();
    }

    if (version_compare('2.0.4', $stored_version) >= 0) {
        upme_upgrade_2_0_4();
    }
    
    if (version_compare('2.0.5', $stored_version) >= 0) {
        upme_upgrade_2_0_5();
    }

    if (version_compare('2.0.6', $stored_version) >= 0) {
        upme_upgrade_2_0_6();
    }
    
    if (version_compare('2.0.7', $stored_version) >= 0) {
        upme_upgrade_2_0_7();
    }

    if (version_compare('2.0.8', $stored_version) >= 0) {

        upme_upgrade_2_0_8();
    }

    if (version_compare('2.0.10', $stored_version) >= 0) {

        upme_upgrade_2_0_10();
    }

    if (version_compare('2.0.12', $stored_version) >= 0) {

        upme_upgrade_2_0_12();
    }

    if (version_compare('2.0.14', $stored_version) >= 0) {

        upme_upgrade_2_0_14();
    }

    if (version_compare('2.0.16', $stored_version) >= 0) {

        upme_upgrade_2_0_16();
    }

    if (version_compare('2.0.17', $stored_version) >= 0) {

        upme_upgrade_2_0_17();
    }

    if (version_compare('2.0.20', $stored_version) >= 0) {
        upme_upgrade_2_0_20();
    }

    if (version_compare('2.0.21', $stored_version) >= 0) {
        upme_upgrade_2_0_21();
    }
    
    if (version_compare('2.0.22', $stored_version) >= 0) {
        upme_upgrade_2_0_22();
    }

    if (version_compare('2.0.23', $stored_version) >= 0) {
        upme_upgrade_2_0_23();
    }

    update_option('upme_version', $current_version);
}

function upme_upgrade_1_8() {

    if (empty($GLOBALS['wp_rewrite'])) {
        $GLOBALS['wp_rewrite'] = new WP_Rewrite();
    }

    // Getting current UPME Options
    $current_option = get_option('upme_options');



    if (!isset($current_option['profile_page_id']) || $current_option['profile_page_id'] == 0) {
        // Get default page created by UPME of earlier version
        $id = get_option('upme_profile_page');

        if (isset($id) && $id > 0) {
            // Page is still exists
            $current_option['profile_page_id'] = $id;
        } else {
            // Inserting Profile page
            $profile_data = array(
                'post_title' => __('View Profile', 'upme'),
                'post_type' => 'page',
                'post_name' => 'profile',
                'post_content' => '[upme]',
                'post_status' => 'publish',
                'comment_status' => 'closed',
                'ping_status' => 'closed',
                'post_author' => 1
            );
            $profile_page = wp_insert_post($profile_data, FALSE);

            if (isset($profile_page))
                $current_option['profile_page_id'] = $reg_page;
        }
    }

    if (!isset($current_option['registration_page_id']) || $current_option['registration_page_id'] == 0) {
        // Inserting Registration page
        $reg_data = array(
            'post_title' => __('Register', 'upme'),
            'post_type' => 'page',
            'post_name' => 'register',
            'post_content' => '[upme_registration]',
            'post_status' => 'publish',
            'comment_status' => 'closed',
            'ping_status' => 'closed',
            'post_author' => 1
        );

        $reg_page = wp_insert_post($reg_data, FALSE);

        if (isset($reg_page))
            $current_option['registration_page_id'] = $reg_page;
    }
    

    if (!isset($current_option['login_page_id']) || $current_option['login_page_id'] == 0) {
        // Inserting Login Page
        $login_data = array(
            'post_title' => __('Login', 'upme'),
            'post_type' => 'page',
            'post_name' => 'login',
            'post_content' => '[upme_login]',
            'post_status' => 'publish',
            'comment_status' => 'closed',
            'ping_status' => 'closed',
            'post_author' => 1
        );

        $login_page = wp_insert_post($login_data, FALSE);

        if (isset($login_page))
            $current_option['login_page_id'] = $login_page;
    }

    // Adding registration closed message
    if (!isset($current_option['html_registration_disabled']) || (isset($current_option['html_registration_disabled']) && $current_option['html_registration_disabled'] == ''))
        $current_option['html_registration_disabled'] = __('User registration is currently not allowed.', 'upme');

    if (!isset($current_option['captcha_label']) || (isset($current_option['captcha_label']) && $current_option['captcha_label'] == ''))
        $current_option['captcha_label'] = __('Human Check', 'upme');

    // Adding date format to upgrade routine
    if (!isset($current_option['date_format']) || (isset($current_option['date_format']) && $current_option['date_format'] == ''))
        $current_option['date_format'] = 'mm/dd/yy';

    // Updating UPME Option
    update_option('upme_options', $current_option);
}

// Version 2.0.0 upgrade routine
function upme_upgrade_2_0() {
    // Uploader folder upgrade routine 
    $upme_current_upload_path = upme_path . "uploads";
    if (is_dir($upme_current_upload_path)) {

        global $wpdb;

        $available_files = 0;
        $delete_files = 0;

        // Checking for valid uploads folder
        if (!( $upload_dir = wp_upload_dir() ))
            return false;

        $upload_base_dir = $upload_dir['basedir'];
        $upme_upgraded_upload_path = $upload_base_dir . "/upme/";
        $upme_upgraded_upload_url = $upload_dir['baseurl'] . "/upme/";

        $upme_current_upload_path = upme_path . "uploads/";
        $upme_current_upload_url = upme_url . "uploads/";

        // Check the existence of upme folder within uploads folder of the site
        if (wp_mkdir_p($upme_upgraded_upload_path)) {


            $source = $upme_current_upload_path;
            $destination = $upme_upgraded_upload_path;

            // Get array of  uploaded files
            $files = scandir($upme_current_upload_path);
            $files_delete = array();

            // Copy all the files into destination folder
            $available_files = array();
            foreach ($files as $file) {
                if (in_array($file, array(".", "..")))
                    continue;

                array_push($available_files, $source . $file);
                if (copy($source . $file, $destination . $file)) {
                    array_push($files_delete, $source . $file);
                }
            }

            $delete_files = count($files_delete);
            $available_files = count($available_files);
            // Delete all successfully-copied files
            foreach ($files_delete as $file) {
                unlink($file);
            }


            // Check whther all the files are moved to deffault uploads folder
            if (($available_files == $delete_files) && (0 != $available_files)) {

                // Filter the file fields used as profile fields
                $fields = get_option('upme_profile_fields');
                foreach ($fields as $field) {

                    if (isset($field['field']) && isset($field['meta']) && 'fileupload' == $field['field']) {

                        // Update the link location of images to new upload path
                        $sql = 'update ' . $wpdb->usermeta . ' set meta_value= REPLACE(meta_value, %s , %s) where meta_key=%s';

                        $result = $wpdb->query(
                                        $wpdb->prepare($sql, $upme_current_upload_url, $upme_upgraded_upload_url, $field['meta'])
                        );
                    }
                }
                // Remove upload directory once all the files have been transfered
                rmdir($upme_current_upload_path);
            }
        } else {
            echo '<p class="error">' . __('Upload folder creation failed.', 'upme') . '</p>';
        }
    }
}

function upme_upgrade_2_0_1() {
    // Option Update for Separator for Profile Viewing
    $current_option = get_option('upme_options');
    $current_option['show_separator_on_profile'] = '0';
    $current_option['show_empty_field_on_profile'] = '0';

    //Adding UPME Setting for Cron Usage
    $current_option['use_cron'] = '1';

    // Adding Cron Scheduled Task Start
    if (!wp_next_scheduled('upme_process_cache_cron')) {
        wp_schedule_event(time(), 'hourly', 'upme_process_cache_cron');
    }
    // Adding Cron Scheduled Task Ends
    // Updating UPME Option
    update_option('upme_options', $current_option);

    // Updating Meta Value for Separator
    $profile_fields = get_option('upme_profile_fields');

    foreach ($profile_fields as $key => $value) {
        if ($value['type'] == 'seperator') {
            $profile_fields[$key]['type'] = 'separator';
        }

        if ($profile_fields[$key]['type'] == 'separator') {
            $profile_fields[$key]['meta'] = upme_manage_string_for_meta($value['name']) . '_separator';
            $current_user = wp_get_current_user();

            // Updating User Meta for Admin User
            add_user_meta($current_user->ID, $profile_fields[$key]['meta'], '', false);
        }
    }

    update_option('upme_profile_fields', $profile_fields);

    /* Upgrade Routine to Create Cache for Meta Values for All Users */
    $users = get_users(array('fields' => 'ID'));

    foreach ($users as $key => $value) {
        upme_update_user_cache($value);
    }
}

function upme_upgrade_2_0_2() {
    // Adding UPME Setting for Hide Admin Bar on Frontend
    $current_option = get_option('upme_options');
    $current_option['hide_frontend_admin_bar'] = 'enabled';
    $current_option['profile_url_type'] = 1;
    update_option('upme_options', $current_option);

    flush_rewrite_rules();    
}

function upme_upgrade_2_0_3() {
    $current_option = get_option('upme_options');
    $current_option['profile_url_type'] = 1;
    update_option('upme_options', $current_option);

    flush_rewrite_rules();    
}

function upme_upgrade_2_0_4() {
    flush_rewrite_rules();    
    
}

function upme_upgrade_2_0_5() {

    $current_option = get_option('upme_options');
    $current_option['require_search_input'] = '0';
    $current_option['users_are_called'] = 'Users';
    $current_option['combined_search_text'] = 'Combined Search';
    $current_option['search_button_text'] = 'Filter';
    $current_option['profile_title_field'] = 'display_name';

    update_option('upme_options', $current_option);
}

function upme_initialize_regular_tasks(){
   flush_rewrite_rules(); 
}

function upme_upgrade_2_0_6() {
    global $wpdb;

    $user_query = new WP_User_Query( array( 
        'meta_key' => 'upme_activation_status', 
        'meta_compare' => 'NOT EXISTS',
        ) );

    $users = $user_query->get_results();

    foreach ($users as $key => $user) {
        $activation_code = wp_generate_password(12, false);
        update_user_meta($user->data->ID, 'upme_activation_status', "ACTIVE");
        update_user_meta($user->data->ID, 'upme_activation_code',$activation_code);
    }

    $current_option = get_option('upme_options');
    $current_option['select_user_role_in_registration'] = '0';
    $current_option['choose_roles_for_registration'] = get_option('default_role');
    $current_option['label_for_registration_user_role'] = __('Select Role', 'upme');
    update_option('upme_options', $current_option);

}

function upme_upgrade_2_0_7() {
    $current_option = get_option('upme_options');
    $current_option['reset_button_text'] = __('Reset', 'upme');
    update_option('upme_options', $current_option);    
}

function upme_upgrade_2_0_8() {

    $current_option = get_option('upme_options');

    // Insert default password reset page
    if (!isset($current_option['reset_password_page_id']) || $current_option['reset_password_page_id'] == 0) {
  
        $reset_password_data = array(
            'post_title' => __('Reset Password', 'upme'),
            'post_type' => 'page',
            'post_name' => 'reset_password',
            'post_content' => '[upme_reset_password]',
            'post_status' => 'publish',
            'comment_status' => 'closed',
            'ping_status' => 'closed',
            'post_author' => 1
        );

        $reset_password_page = wp_insert_post($reset_password_data, FALSE);

        if (isset($reset_password_page))
            $current_option['reset_password_page_id'] = $reset_password_page;


    }

    $current_option['set_email_confirmation'] = '0';

    update_option('upme_options', $current_option);

}

function upme_upgrade_2_0_10() {
    global $wpdb;

    $current_option = get_option('upme_options');
    $current_option['lightbox_avatar_cropping'] = '1';
    $current_option['show_recent_user_posts'] = '0';
    $current_option['maximum_allowed_posts'] = '3';
    $current_option['show_feature_image_posts'] = '0';
    $current_option['website_link_on_profile'] = '0';

    $current_option['default_predefined_country'] = 'US';

    $current_option['enforce_password_strength'] = '0';


    // Insert default member list page
    if (!isset($current_option['member_list_page_id']) || $current_option['member_list_page_id'] == 0) {
  
        $member_list_data = array(
            'post_title' => __('Member List', 'upme'),
            'post_type' => 'page',
            'post_name' => 'member_list',
            'post_content' => '[upme_search] [upme group=all view=compact users_per_page=10]',
            'post_status' => 'publish',
            'comment_status' => 'closed',
            'ping_status' => 'closed',
            'post_author' => 1
        );

        $member_list_page = wp_insert_post($member_list_data, FALSE);

        if (isset($member_list_page))
            $current_option['member_list_page_id'] = $member_list_page;


    }

    // Remove existing password values on user meta table
    $qry = "DELETE FROM $wpdb->usermeta where meta_key='user_pass' or  meta_key='user_pass_confirm' ";
    $result = $wpdb->get_results( $qry );

    update_option('upme_options', $current_option);

}

function upme_upgrade_2_0_12() {
    global $wpdb;

    $current_option = get_option('upme_options');
    $current_option['choose_roles_for_view_profile'] = 'administrator';
    $current_option['profile_modal_window_shortcode'] = '[upme]';

    //Adding UPME Setting for disabling fancybox scripts
    $current_option['disable_fancybox_script_styles'] = '0';

    $current_option['html_login_to_view_form'] = '1';
    $current_option['html_user_login_message_form'] = '1';
    $current_option['html_private_content_form'] = '1';
    $current_option['html_other_profiles_restricted'] = __('Viewing of other profiles restricted for your user role.','upme');

    update_option('upme_options', $current_option);
}

function upme_upgrade_2_0_14() {
    global $wpdb;

    $current_option = get_option('upme_options');
    $current_option['profile_view_status'] = '0';
    $current_option['display_profile_status'] = '0';

    // Add default user profile status as active to user meta table
    $users = get_users(array('fields' => 'ID'));

    foreach ($users as $key => $user_id) {
        // Setting for displaying or hiding individual profiles
        update_user_meta( $user_id, 'upme_user_profile_status', 'ACTIVE' );
        // Setting for approving users by adminstrative user
        update_user_meta( $user_id, 'upme_approval_status', 'ACTIVE' );

        upme_update_user_cache($user_id);

    }

    $current_option['html_profile_status_msg'] = __('This is a private profile. You are not allowed to view this profile.', 'upme');
    $current_option['html_profile_approval_pending_msg'] = __('This profile is pending approval. You will get a notification once the profile is approved.', 'upme');
    $current_option['profile_approval_status'] = '0';
    $current_option['ajax_profile_field_save'] = '0';

    $current_option['html_terms_and_conditions'] = __('I agree to Terms and Conditions.', 'upme');
    $current_option['accepting_terms_and_conditions'] = '0';
            
    update_option('upme_options', $current_option);

    // Remove existing password values on user meta table
    $qry = "DELETE FROM $wpdb->usermeta where meta_key='user_pass' or  meta_key='user_pass_confirm' ";
    $result = $wpdb->get_results( $qry );


}

function upme_upgrade_2_0_16() {
    global $wpdb;

    $user_query = new WP_User_Query( array( 
        'meta_key' => 'upme_activation_status', 
        'meta_compare' => 'NOT EXISTS',
        ) );

    $users = $user_query->get_results();

    foreach ($users as $key => $user) {
        $activation_code = wp_generate_password(12, false);
        update_user_meta($user->data->ID, 'upme_activation_status', "ACTIVE");
        update_user_meta($user->data->ID, 'upme_activation_code',$activation_code);
    }
}
// TODO Update Activation Status of Users who have empty activation values

function upme_upgrade_2_0_17() {
    $current_option = get_option('upme_options');

    $current_option['site_lockdown_status']  = '0';
    $current_option['site_lockdown_allowed_pages']      = '';
    $current_option['site_lockdown_allowed_posts']      = '';
    $current_option['site_lockdown_allowed_urls']       = '';
    $current_option['site_lockdown_redirect_url']       = $current_option['login_page_id']; 
    $current_option['site_lockdown_rss_feed']           = '0';   
    $current_option['html_members_private_content']     = __('This content is restricted for your user account.', 'upme');
    
    update_option('upme_options', $current_option);
}


function upme_upgrade_2_0_20() {
    global $upme_email_templates;
    $upme_email_templates->upme_reset_all_templates();
}

function upme_upgrade_2_0_21() {
    $current_option = get_option('upme_options');

    $current_option['link_post_author_to_upme']  = '0';
    $current_option['display_profile_after_post']  = '0';
    update_option('upme_options', $current_option);
}

function upme_upgrade_2_0_22() {
    $current_option = get_option('upme_options');

    $current_option['author_post_profile_template']  = '0';
    update_option('upme_options', $current_option);
}

function upme_upgrade_2_0_23(){
    global $wpdb,$upme_email_templates;

    $current_option = get_option('upme_options');
    $current_option['disable_fitvids_script_styles'] = '0';
    $current_option['disable_tipsy_script_styles'] = '0';
    $current_option['disable_opensans_google_font'] = '0';
    $current_option['email_two_factor_verification_status'] = '0';
    update_option('upme_options', $current_option);
    
    $email_templates = get_option('upme_email_templates');
    $email_templates['two_factor_email_verify'] = $upme_email_templates->upme_get_template('two_factor_email_verify','1');
    update_option('upme_email_templates',$email_templates);

}

