<?php

// General Functions for Plugin

if (!function_exists('is_post')) {

    function is_post() {
        if (strtolower($_SERVER['REQUEST_METHOD']) == 'post')
            return true;
        else
            return false;
    }

}

if (!function_exists('is_in_post')) {

    function is_in_post($key='', $val='') {
        if ($key == '') {
            return false;
        } else {
            if (isset($_POST[$key])) {
                if ($val == '')
                    return true;
                else if ($_POST[$key] == $val)
                    return true;
                else
                    return false;
            }
            else
                return false;
        }
    }

}

if (!function_exists('is_get')) {

    function is_get() {
        if (strtolower($_SERVER['REQUEST_METHOD']) == 'get')
            return true;
        else
            return false;
    }

}


if (!function_exists('is_in_get')) {

    function is_in_get($key='', $val='') {
        if ($key == '') {
            return false;
        } else {
            if (isset($_GET[$key])) {
                if ($val == '')
                    return true;
                else if ($_GET[$key] == $val)
                    return true;
                else
                    return false;
            }
            else
                return false;
        }
    }

}

if (!function_exists('not_null')) {

    function not_null($value) {
        if (is_array($value)) {
            if (sizeof($value) > 0)
                return true;
            else
                return false;
        }
        else {
            if ((is_string($value) || is_int($value)) && ($value != '') && ($value != 'NULL') && (strlen(trim($value)) > 0))
                return true;
            else
                return false;
        }
    }

}



if (!function_exists('get_value')) {

    function get_value($key='') {
        if ($key != '') {
            if (isset($_GET[$key]) && not_null($_GET[$key])) {
                if (!is_array($_GET[$key]))
                    return trim($_GET[$key]);
                else
                    return $_GET[$key];
            }

            else
                return '';
        }
        else
            return '';
    }

}


if (!function_exists('post_value')) {

    function post_value($key='') {
        if ($key != '') {
            if (isset($_POST[$key]) && not_null($_POST[$key])) {
                if (!is_array($_POST[$key]))
                    return trim($_POST[$key]);
                else
                    return $_POST[$key];
            }
            else
                return '';
        }
        else
            return '';
    }

}


if (!function_exists('is_opera')) {

    function is_opera() {
        $user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);
        return preg_match('/opera/i', $user_agent);
    }

}

if (!function_exists('is_safari')) {

    function is_safari() {
        $user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);
        return (preg_match('/safari/i', $user_agent) && !preg_match('/chrome/i', $user_agent));
    }

}

// Check with the magic quotes functionality Start
function stripslashess(&$item) {
    $item = stripslashes($item);
}

if (get_magic_quotes_gpc ()) {
    array_walk_recursive($_GET, 'stripslashess');
    array_walk_recursive($_POST, 'stripslashess');
    array_walk_recursive($_SERVER, 'stripslashess');
}


if (!function_exists('remove_script_tags')) {

    function remove_script_tags($text) {
        $text = str_ireplace("<script>", "", $text);
        $text = str_ireplace("</script>", "", $text);

        return $text;
    }

}

if (!function_exists('upme_date_format_to_standerd')) {

    function upme_date_format_to_standerd($date, $format) {

        switch ($format) {
            case 'mm/dd/yy':
                $new_format = 'm/d/Y';
                break;

            case 'yy/mm/dd':
                $new_format = 'Y/m/d';
                break;

            case 'dd/mm/yy':
                $new_format = 'd/m/Y';
                break;

            case 'yy-mm-dd':
                $new_format = 'Y-m-d';
                break;

            case 'dd-mm-yy':
                $new_format = 'd-m-Y';
                break;

            case 'mm-dd-yy':
                $new_format = 'm-d-Y';
                break;

            case 'MM d, yy':
                $new_format = 'F d, Y';
                break;

            case 'd M, y':
                $new_format = 'd M, y';
                break;

            case 'd MM, y':
                $new_format = 'd F, y';
                break;

            case 'DD, d MM, yy':
                $new_format = 'l, d F, Y';
                break;

            default:
                $new_format = 'm/d/Y';
                break;
        }

        if(function_exists('date_create_from_format')){
            $date = date_create_from_format($new_format, $date);
            $date = date_format($date, 'm/d/Y');
        }
        
        return $date;
    }

}

if (!function_exists('upme_date_format_to_custom')) {

    function upme_date_format_to_custom($date, $format) {

        switch ($format) {
            case 'mm/dd/yy':
                $date = new DateTime($date);
                $datetime = $date->format("m/d/Y");
                break;

            case 'yy/mm/dd':
                $date = new DateTime($date);
                $datetime = $date->format("Y/m/d");
                break;

            case 'dd/mm/yy':
                $date = new DateTime($date);
                $datetime = $date->format("d/m/Y");
                break;

            case 'yy-mm-dd':
                $date = new DateTime($date);
                $datetime = $date->format("Y-m-d");
                break;

            case 'dd-mm-yy':
                $date = new DateTime($date);
                $datetime = $date->format("d-m-Y");
                break;

            case 'mm-dd-yy':
                $date = new DateTime($date);
                $datetime = $date->format("m-d-Y");
                break;

            case 'MM d, yy':
                $date = new DateTime($date);
                $datetime = $date->format("F j, Y");
                break;

            case 'd M, y':
                $date = new DateTime($date);
                $datetime = $date->format("j M, y");
                break;

            case 'd MM, y':
                $date = new DateTime($date);
                $datetime = $date->format("j F, y");
                break;

            case 'DD, d MM, yy':
                $date = new DateTime($date);
                $datetime = $date->format("l, j F, Y");
                break;

            default:
                $date = new DateTime($date);
                $datetime = $date->format("m/d/Y");
                break;
        }

        return $datetime;
    }

}


if (!function_exists('upme_get_uploads_folder_details')) {

    function upme_get_uploads_folder_details() {

        // Checking for valid uploads folder
        if (!( $upload_dir = wp_upload_dir() ))
            return false;

        return $upload_dir;
    }

}

if (!function_exists('upme_manage_string_for_meta')) {

    function upme_manage_string_for_meta($string='') {
        $badChars = array(' ', ',', '$', '&', '\'', ':', '<', '>', '[', ']', '{', '}', '#', '%', '@', '/', ';', '=', '?', '\\', '^', '|', '~', '(', ')', '"', '.');
        $string = str_replace($badChars, '_', trim($string));
        $string = trim($string, '_');
        $string = str_replace('___', '_', trim($string));
        $string = str_replace('__', '_', trim($string));
        return strtolower($string);
    }

}

if (!function_exists('upme_update_user_cache')) {

    function upme_update_user_cache($user_id) {
        global $wpdb;

        $meta_values_query = "SELECT meta_key, meta_value FROM " . $wpdb->usermeta . " WHERE meta_key!='_upme_search_cache' AND user_id=" . esc_sql($user_id);

        $meta_data = $wpdb->get_results($meta_values_query, 'ARRAY_A');


        $profile_fields = get_option('upme_profile_fields');

        $upme_fields_meta = array();
        foreach ($profile_fields as $key => $value) {
            if ($value['type'] == 'usermeta') {

                $upme_fields_meta[] = $value['meta'];
            }
        }

        $search_cache = array();

        foreach ($meta_data as $k => $v) {
            if ($v['meta_key'] == $wpdb->get_blog_prefix() . "capabilities") {
                $roles = unserialize($v['meta_value']);
                foreach ($roles as $role_key => $role_value) {
                    $search_cache[] = 'role::' . $role_key;
                }
            } 
            else if('user_pass' == $v['meta_key'] || 'user_pass_confirm' == $v['meta_key'] ){
                // Skip these fields for search cache
            }
            else if('upme_user_profile_status' == $v['meta_key'] || 'upme_approval_status' == $v['meta_key'] || 'upme_activation_status' == $v['meta_key']){
                // Add user statuses to cache to prevent showing in search results
                $search_cache[] = $v['meta_key'] . '::' . $v['meta_value'];
            }
            else {

                if (in_array($v['meta_key'], $upme_fields_meta)) {
                    if ($v['meta_value'] == '' || $v['meta_value'] == '0') {
                        $search_cache[] = $v['meta_key'] . '::' . $v['meta_value'];
                    } else {
                        $multi_data = explode(',', $v['meta_value']);
                        foreach ($multi_data as $data_key => $data_value) {
                            $search_cache[] = $v['meta_key'] . '::' . trim($data_value);
                        }
                    }
                }
            }
        }

        $user = get_user_by( 'id', $user_id );
        $search_cache[] = 'username::' . trim($user->data->user_login);
        
        $search_cache_string = '';
        $search_cache_string = implode('||', $search_cache);

        update_user_meta($user_id, '_upme_search_cache', $search_cache_string);
    }

}

if (!function_exists('upme_cron_user_cache')) {

    function upme_cron_user_cache() {
        global $wpdb;

        $current_option = get_option('upme_options');

        // Execute Only if set to yes
        if (isset($current_option['use_cron']) && $current_option['use_cron'] == '1') {
            $last_processed_user = get_option('upme_cron_processed_user_id');
            if ($last_processed_user == '') {
                $last_processed_user = 0;
            }

            $limit = 25;

            $user_query = "SELECT ID FROM " . $wpdb->users . " WHERE ID>'" . esc_sql($last_processed_user) . "' ORDER BY ID ASC LIMIT " . $limit;

            $users = $wpdb->get_results($user_query, 'ARRAY_A');

            $count = 0;
            foreach ($users as $key => $value) {
                upme_update_user_cache($value['ID']);

                update_option('upme_cron_processed_user_id', $value['ID']);

                $count++;
            }

            // All users completed, so resetting value to 0
            if ($count < $limit) {
                update_option('upme_cron_processed_user_id', '0');
            }
        }
    }

}

if (!function_exists('upme_activation')) {

    function upme_activation() {
        if (!wp_next_scheduled('upme_process_cache_cron')) {
            wp_schedule_event(time(), 'hourly', 'upme_process_cache_cron');
        }
    }

}

if (!function_exists('upme_deactivation')) {

    function upme_deactivation() {
        wp_clear_scheduled_hook('upme_process_cache_cron');
    }

}

if (!function_exists('upme_video_url_customizer')) {

    function upme_video_url_customizer($url) {
        $url_parts = parse_url($url);
        if ($url_parts) {
            $host = isset($url_parts['host']) ? $url_parts['host'] : '';
            $query = isset($url_parts['query']) ? $url_parts['query'] : '';
            $path = isset($url_parts['path']) ? $url_parts['path'] : '';
            $player_url = '';
            if ('www.youtube.com' == $host) {
                $player_url = upme_youtube_url_customizer($query);
            } else if ('vimeo.com' == $host) {
                $player_url = upme_vimeo_url_customizer($path);
            } else if('youtu.be' == $host){
                $player_url = upme_youtube_short_url_customizer($path);
            }
            return $player_url;
        } else {
            return false;
        }
    }

}

if (!function_exists('upme_vimeo_url_customizer')) {

    function upme_vimeo_url_customizer($path) {
        $player_url = '//player.vimeo.com/video' . $path;
        return $player_url;
    }

}


if (!function_exists('upme_youtube_url_customizer')) {

    function upme_youtube_url_customizer($query) {

        $query_parts = explode('=', $query);
        $video_str = isset($query_parts[1]) ? $query_parts[1] : '';
        $player_url = '//www.youtube.com/embed/' . $video_str;
        return $player_url;
    }
}

if (!function_exists('upme_video_type_css')) {

    function upme_video_type_css($url) {
        $url_parts = parse_url($url);
        $player_details = array();
        $player_details['height'] = '281';
        $player_details['width'] = '500';
        if ($url_parts) {
            $host = isset($url_parts['host']) ? $url_parts['host'] : '';

            if ('www.youtube.com' == $host) {
                $player_details['height'] = '315';
                $player_details['width'] = '560';
            } else if ('vimeo.com' == $host) {
                $player_details['height'] = '281';
                $player_details['width'] = '500';
            }
            return $player_details;
        } else {
            return $player_details;
        }
    }

}


if (!function_exists('upme_new_user_notification')) {

    function upme_new_user_notification($user_id, $plaintext_pass = '', $activation_status = '', $activation_code = '') {
        global $upme_email_templates;

        $user = new WP_User($user_id);

        $user_login = stripslashes($user->user_login);
        $user_email = stripslashes($user->user_email);

        $message  = sprintf(__('New user registration on %s:','upme'), get_option('blogname')) . "\r\n\r\n";
        $message .= sprintf(__('Username: %s','upme'), $user_login) . "\r\n\r\n";
        $message .= sprintf(__('E-mail: %s','upme'), $user_email) . "\r\n";

        /* UPME Filter for customizing new user activation email content for admins */
        $message  = apply_filters('upme_new_user_act_admin_content',$message,$user_login,$user_email);
        // End Filter

        $subject  = sprintf(__('[%s] New User Registration','upme'), get_option('blogname'));
        /* UPME Filter for customizing new user activation email subject for admins  */
        $subject  = apply_filters('upme_new_user_act_admin_subject',$subject);
        // End Filter
                        
        $send_params = array('username' => $user_login, 'email' => $user_email);
        $admin_email = get_option('admin_email');
        $admin_user = get_user_by( 'email', $admin_email);
        $email_status = $upme_email_templates->upme_send_emails('reg_email_confirm_admin', get_option('admin_email') ,$subject,$message,$send_params,$user_id);
    
        // @wp_mail(
        //                 get_option('admin_email'),
        //                 $subject,
        //                 $message
        // );

        if (empty($plaintext_pass))
            return;

        if (!empty($activation_status) && 'INACTIVE' == $activation_status) {

            $current_option = get_option('upme_options');
            $link = get_permalink($current_option['profile_page_id']);

            $query_str = "upme_action=upme_activate&upme_id=" . $user_id . "&upme_activation_code=" . $activation_code;
            $activation_link = upme_add_query_string($link, $query_str);


            $message = sprintf(__("Someone (hopefully you) has used this email to register at %s :",'upme'), get_option('blogname')) . "\r\n\r\n";
            $message .= __('Please click the link below to verify your ownership of this email:','upme') . "\r\n\r\n";
            $message .= __('You will not be able to log in to use your account until you do so:','upme') . "\r\n\r\n";
            $message .= sprintf(__('%s'), $activation_link, $activation_link) . "\r\n\r\n";
            $message .= __('Thanks','upme') . "\r\n";
            $message .= sprintf(__('%s'), get_option('blogname'),'upme') . "\r\n";

            /* UPME Filter for customizing new user activation email content for users  */
            $message  = apply_filters('upme_new_user_act_content',$message,$user_login,$user_email,$activation_link);
            // End Filter

            $subject = sprintf(__('[%s] Action Required: Email Verification','upme'), get_option('blogname'));                    
            /* UPME Filter for customizing new user activation email content for users  */
            $subject  = apply_filters('upme_new_user_act_subject',$subject);
            // End Filter

            $send_params = array('username' => $user_login, 'password' => $plaintext_pass, 'activation_link' => $activation_link);
            $email_status = $upme_email_templates->upme_send_emails('reg_email_confirm_user', $user_email ,$subject,$message,$send_params,$user_id);
    
            // wp_mail(
            //         $user_email,
            //         $subject,
            //         $message
            // );
        } 
    }

}

if (!function_exists('upme_add_query_string')) {

    function upme_add_query_string($link, $query_str) {

        $build_url = $link;

        $query_comp = explode('&', $query_str);

        foreach ($query_comp as $param) {
            $params = explode('=', $param);
            $key = isset($params[0]) ? $params[0] : '';
            $value = isset($params[1]) ? $params[1] : '';
            $build_url = add_query_arg($key, $value, $build_url);
        }

        return $build_url;
    }

}


if (!function_exists('upme_date_picker_setting')) {

    function upme_date_picker_setting() {
        // Set date format from admin settings
        $upme_settings = get_option('upme_options');
        $upme_date_format = (string) isset($upme_settings['date_format']) ? $upme_settings['date_format'] : 'mm/dd/yy';

        $date_picker_array = array(
            'closeText' => __('Done','upme'),
            'prevText' => __('Prev','upme'),
            'nextText' => __('Next','upme'),
            'currentText' => __('Today','upme'),
            'monthNames' => array(
                'Jan' => __('January','upme'),
                'Feb' => __('February','upme'),
                'Mar' => __('March','upme'),
                'Apr' => __('April','upme'),
                'May' => __('May','upme'),
                'Jun' => __('June','upme'),
                'Jul' => __('July','upme'),
                'Aug' => __('August','upme'),
                'Sep' => __('September','upme'),
                'Oct' => __('October','upme'),
                'Nov' => __('November','upme'),
                'Dec' => __('December','upme')
            ),
            'monthNamesShort' => array(
                'Jan' => __('Jan','upme'),
                'Feb' => __('Feb','upme'),
                'Mar' => __('Mar','upme'),
                'Apr' => __('Apr','upme'),
                'May' => __('May','upme'),
                'Jun' => __('Jun','upme'),
                'Jul' => __('Jul','upme'),
                'Aug' => __('Aug','upme'),
                'Sep' => __('Sep','upme'),
                'Oct' => __('Oct','upme'),
                'Nov' => __('Nov','upme'),
                'Dec' => __('Dec','upme')
            ),
            'dayNames' => array(
                'Sun' => __('Sunday','upme'),
                'Mon' => __('Monday','upme'),
                'Tue' => __('Tuesday','upme'),
                'Wed' => __('Wednesday','upme'),
                'Thu' => __('Thursday','upme'),
                'Fri' => __('Friday','upme'),
                'Sat' => __('Saturday','upme')
            ),
            'dayNamesShort' => array(
                'Sun' => __('Sun','upme'),
                'Mon' => __('Mon','upme'),
                'Tue' => __('Tue','upme'),
                'Wed' => __('Wed','upme'),
                'Thu' => __('Thu','upme'),
                'Fri' => __('Fri','upme'),
                'Sat' => __('Sat','upme')
            ),
            'dayNamesMin' => array(
                'Sun' => __('Su','upme'),
                'Mon' => __('Mo','upme'),
                'Tue' => __('Tu','upme'),
                'Wed' => __('We','upme'),
                'Thu' => __('Th','upme'),
                'Fri' => __('Fr','upme'),
                'Sat' => __('Sa','upme')
            ),
            'weekHeader' => __('Wk','upme'),
            'dateFormat' => $upme_date_format,
            'yearRange'  => '1920:2020'
        );

        /* UPME Filter for customizing date picker settings */
        $date_picker_array = apply_filters('upme_datepicker_settings', $date_picker_array);
        // End Filter

        return $date_picker_array;
    }

}

if(!function_exists('upme_default_socail_links')) {
    function upme_default_socail_links() {
        add_filter('upme_social_url_user_email', 'upme_format_email_link');
        add_filter('upme_social_url_twitter', 'upme_format_twitter_link');
        add_filter('upme_social_url_facebook', 'upme_format_facebook_link');
        add_filter('upme_social_url_googleplus', 'upme_format_google_link');
    }
}

// Hooking default social url
upme_default_socail_links();


if(!function_exists('upme_format_email_link')) {
    function upme_format_email_link($content){
        return 'mailto:'.$content;
    }
}

if(!function_exists('upme_format_twitter_link')) {
    function upme_format_twitter_link($content){
        return 'http://twitter.com/'.$content;
    }
}

if(!function_exists('upme_format_facebook_link')) {
    function upme_format_facebook_link($content){
        return 'http://www.facebook.com/'.$content;
    }
}

if(!function_exists('upme_format_google_link')) {
    function upme_format_google_link($content){
        return 'https://plus.google.com/'.$content;
    }
}

if (!function_exists('upme_sound_cloud_player')) {

    function upme_sound_cloud_player($url){
        $width = '100%';

        $soundcloud_player_url = 'https://w.soundcloud.com/player/?url='.$url;

        $soundcloud_params = array('color' => 'ff5500',
                                    'auto_play' =>  'false',
                                    'hide_related' => 'false',
                                    'show_artwork' => 'false'
                                );

        $height = (preg_match('/^(.+?)\/(sets|groups|playlists)\/(.+?)$/', $soundcloud_player_url) ) ? '400px' : '150px';

        $soundcloud_player_url = add_query_arg( $soundcloud_params, $soundcloud_player_url);

        return sprintf('<iframe width="%s" height="%s" scrolling="no" frameborder="no" src="%s"></iframe>', $width, $height, $soundcloud_player_url);
    }

}

if (!function_exists('upme_youtube_short_url_customizer')) {

    function upme_youtube_short_url_customizer($path) {
        $player_url = '//www.youtube.com/embed' . $path;
        return $player_url;
    }
}


if (!function_exists('upme_admin_approval_notification')) {

    function upme_admin_approval_notification($user_id,$link) {
        global $upme_email_templates;

        $user = new WP_User($user_id);

        $user_login = stripslashes($user->user_login);
        $user_email = stripslashes($user->user_email);

        $message  = __('Your account has been approved successfully. ','upme') . "\r\n\r\n";
        
        $message .= sprintf(__('Username: %s','upme'), $user_login) . "\r\n\r\n";
        $message .= sprintf(__('E-mail: %s','upme'), $user_email) . "\r\n";

        $message .= __('You can now log in to use your account using the following link.','upme') . "\r\n\r\n";
        $message .= sprintf('%s', $link) . "\r\n\r\n";
        $message .= __('Thanks','upme') . "\r\n";

        /* UPME Filter for customizing user approval email content  */
        $message  = apply_filters('upme_new_user_admin_approval_content',$message,$user_login,$user_email);
        // End Filter

        $subject  = sprintf(__('[%s] User Account Approved','upme'), get_option('blogname'));
        /* UPME Filter for customizing user approval email subject  */
        $subject  = apply_filters('upme_new_user_admin_approval_subject',$subject);
        // End Filter  

        $send_params = array('email' => $user_email, 'username' => $user_login, 'login_link' => $link);
        $email_status = $upme_email_templates->upme_send_emails('approval_notify_user', $user_email ,$subject,$message,$send_params,$user_id);
 
        // @wp_mail(
        //     $user_email,
        //     $subject,
        //     $message
        // );

        
    }

}


if (!function_exists('upme_form_validate_setting')) {

    function upme_form_validate_setting() {
        
        $upme_settings = get_option('upme_options');

        $validate_strings = array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'ErrMsg' => array(
                'similartousername' => __('Your password is too similar to your username.', 'upme'),
                'mismatch' => __('Both passwords do not match.', 'upme'),
                'tooshort' => __('Your password is too short.', 'upme'),
                'veryweak' => __('Your password strength is too weak.', 'upme'),
                'weak' => __('Your password strength is weak.', 'upme'),
                'usernamerequired' => __('Please provide username.', 'upme'),
                'emailrequired' => __('Please provide email address.', 'upme'),
                'validemailrequired' => __('Please provide valid email address.', 'upme'),
                'usernameexists' => __('That username is already taken, please try a different one.', 'upme'),
                'emailexists' => __('The email you entered is already registered. Please try a new email or log in to your existing account.', 'upme')
            ),
            'MeterMsg' => array(
                'similartousername' => __('Your password is too similar to your username.', 'upme'),
                'mismatch' => __('Both passwords do not match.', 'upme'),
                'tooshort' => __('Your password is too short.', 'upme'),
                'veryweak' => __('Very weak', 'upme'),
                'weak' => __('Weak', 'upme'),
                'medium' => __('Medium', 'upme'),
                'good' => __('Good', 'upme'),
                'strong' => __('Strong', 'upme')
            ),
            'Err' => __('ERROR', 'upme'),
            'PasswordStrength' => $upme_settings['enforce_password_strength'],
            'MinPassStrength' => __('Minimum password strength level should be', 'upme'),
            'FieldRequiredText' => __(' is required.','upme'),
            'NewPasswordMsg' => __(' New password is required.','upme'),
            'ConfirmPassMsg' => __(' Confirm new password is required.','upme'),
        );



        /* UPME Filter for customizing form validate settings */
        $validate_strings = apply_filters('upme_form_validate_settings', $validate_strings);
        // End Filter

        return $validate_strings;
    }

}

if (!function_exists('upme_tinymce_language_setting')) {

    function upme_tinymce_language_setting() {

                                 
        $lang_strings = array(
            'InsertUPMEShortcode'           => __('Insert UPME Shortcode','upme'),
            'LoginRegistrationForms'        => __('Login / Registration Forms','upme'),
            'FrontRegistrationForm'         => __('Front-end Registration Form','upme'),
            'RegFormCustomRedirect'         => __('Registration Form with Custom Redirect','upme'),
            'RegFormCaptcha'                => __('Registration Form with Captcha','upme'),
            'RegFormNoCaptcha'              => __('Registration Form without Captcha','upme'),
            'FrontLoginForm'                => __('Front-end Login Form','upme'),
            'SidebarLoginWidget'            => __('Sidebar Login Widget (use in text widget)','upme'),
            'LoginFormCustomRedirect'       => __('Login Form with Custom Redirect','upme'),
            'LogoutButton'                  => __('Logout Button','upme'),
            'LogoutButtonCustomRedirect'    => __('Logout Button with Custom Redirect','upme'),
            'SingleProfile'                 => __('Single Profile','upme'),
            'LoggedUserProfile'             => __('Logged in User Profile','upme'),
            'LoggedUserProfileUserID'       => __('Logged in User Profile showing User ID','upme'),

            'LoggedUserProfileHideStats'    => __('Logged in User Profile without Stats','upme'),
            'LoggedUserProfileUserRole'     => __('Logged in User Profile showing User Role','upme'),
            'LoggedUserProfileStatus'       => __('Logged in User Profile showing Profile Status','upme'),
            'LoggedUserProfileLogoutRedirect' => __('Logged in User Profile with Logout Redirect','upme'),

            'PostAuthorProfile'             => __('Post Author Profile','upme'),
            'SpecificUserProfile'           => __('Specific User Profile','upme'),
            'MultipleProfilesMemberList'    => __('Multiple Profiles / Member List','upme'),
            'GroupSpecificUsers'            => __('Group of Specific Users','upme'),
            'AllUsers'                      => __('All Users','upme'),
            'AllUsersCompactView'           => __('All Users in Compact View','upme'),
            'AllUsersCompactViewHalfWidth'  => __('All Users in Compact View, Half Width','upme'),
            'AllUsersModalWindow'           => __('All Users in Modal Windows','upme'),
            'AllUsersNewWindow'           => __('All Users in New Windows','upme'),
            'UsersBasedUserRole'            => __('Users Based on User Role','upme'),
            'AdministratorUsersOnly'        => __('Administrator Users Only','upme'),
            'AllUsersOrderedDisplayName'    => __('All Users Ordered by Display Name','upme'),
            'AllUsersOrderedPostCount'      => __('All Users Ordered by Post Count','upme'),
            'AllUsersOrderedRegistrationDate' => __('All Users Ordered by Registration Date','upme'),
            'AllUsersOrderedCustomField'    => __('All Users Ordered by Custom Field','upme'),
            'AllUsersUserID'                => __('All Users showing User ID','upme'),  
            'GroupUsersCustomField'         => __('Group Users by Custom Field Values','upme'),          
            'HideUsersUntilSearch'          => __('Hide All Users until Search','upme'),
            'SearchProfile'                 => __('Search Profiles','upme'),
            'SearchCustomFieldFilters'      => __('Search with Custom Field Filters','upme'),
            'PrivateContentLoginRequired'   => __('Private Content (Login Required)','upme'),
            'ShortcodeOptionExamples'       => __('Shortcode Option Examples','upme'),
            'HideUserStatistics'            => __('Hide User Statistics','upme'),
            'HideUserSocialBar'             => __('Hide User Social Bar','upme'),
            'HalfWidthProfileView'          => __('1/2 Width Profile View','upme'),
            'CompactViewNoExtraFields'      => __('Compact View (No extra fields)','upme'),
            'CustomizedProfileFields'       => __('Customized Profile Fields','upme'),
            'ShowUserIDProfiles'            => __('Show User ID on Profiles','upme'),
            'LimitResultsMemberList'        => __('Limit Results on Member List','upme'),
            'ShowResultCountMemberList'     => __('Show Result Count on Member List','upme'),

        );

        return $lang_strings;
    }

}



if (!function_exists('upme_current_page_url')) {

    function upme_current_page_url() {
      $url  = @( $_SERVER["HTTPS"] != 'on' ) ? 'http://'.$_SERVER["SERVER_NAME"] :  'https://'.$_SERVER["SERVER_NAME"];
      $url .= $_SERVER["REQUEST_URI"];
      return $url;
    }

}

if (!function_exists('upme_delete_uploads_folder_files')) {

    function upme_delete_uploads_folder_files($image_url) {

        if ($upload_dir = upme_get_uploads_folder_details()) {

            $image_folder_link = $upload_dir['baseurl'] . "/upme/";
            $image_name = str_replace($image_folder_link, '', $image_url);

            $upme_upload_path = $upload_dir['basedir'] . "/upme/";
            if(unlink($upme_upload_path . $image_name)){
                return true;
            }
        }

        return false;
    }

}


if (!function_exists('upme_stripslashes_deep')) {

    function upme_stripslashes_deep($value){
        // $value = is_array($value) ?
        //             array_map('stripslashes_deep', $value) :
        //             stripslashes($value);

        return $value;
    }

}

if (!function_exists('upme_match_template_tags')) {
    function upme_match_template_tags($template_name, $text, $allowed_tags, $params,$user_id = ''){

        $allowed_profile_tags = array();
        $profile_fields = get_option('upme_profile_fields');
        foreach ($profile_fields as $key => $value) {
            array_push($allowed_profile_tags, $value['meta']);
        }

        $allowed_email_profile_tags_params = array('name' => $template_name, 'user_id' => $user_id);
        $allowed_profile_tags = apply_filters('upme_allowed_email_profile_tags', $allowed_profile_tags, $allowed_email_profile_tags_params);

        preg_match_all("/{%(.*?)%}/",$text, $matches, PREG_PATTERN_ORDER);

        if(is_array($matches) && isset($matches[1])){
            foreach ($matches[1] as $key => $tag) {

                $replacement = '';
                if(in_array($tag, $allowed_tags)){
                    switch($tag){
                        case 'blog_name';
                            $replacement = get_option('blogname');
                            break;
                        case 'reset_page_url';
                            $replacement = $params['reset_page_url'];
                            break;
                        case 'network_home_url';
                            $replacement = network_home_url( '/' );
                            break;
                        case 'login_link';
                            $replacement = $params['login_link'];
                            break;
                        case 'username';
                            $replacement = $params['username'];
                            break;
                        case 'password';
                            $replacement = $params['password'];
                            break;
                        case 'email';
                            $replacement = $params['email'];
                            break;
                        case 'full_name';
                            $replacement = $params['full_name'];
                            break;
                        case 'activation_link';
                            $replacement = $params['activation_link'];
                            break;
                        case 'email_two_factor_login_link';
                            $replacement = $params['email_two_factor_login_link'];
                            break;
                        case 'changed_fields';
                            $replacement = '';
                            foreach ($params['changed_fields'] as $key => $value) {
                                $replacement .= __('Field Key','upme'). "   :" . $value['meta']. "\r\n";
                                $replacement .= __('Previous Value','upme'). "   :" . $value['prev_value']. "\r\n";
                                $replacement .= __('Updated Value','upme'). "   :" . $value['new_value']. "\r\n\r\n";
                            }
                            break;
                    }
                }

                if( in_array($tag, $allowed_profile_tags) && '' != $user_id ){
                    $replacement = get_user_meta($user_id, $tag, true);
                }

                $text = str_replace('{%'.$tag.'%}', " $replacement ", $text);
            }
        }
        return $text;
    }

}

if (!function_exists('upme_get_gravatar_url')) {
    function upme_get_gravatar_url( $email ) {
        $hash = md5( strtolower( trim ( $email ) ) );
        return 'http://gravatar.com/avatar/' . $hash;
    }
}

if (!function_exists('upme_addons_feed')) {
    function upme_addons_feed() {
        global $upme_template_loader,$upme_addon_template_data;

        $upme_addon_template_data['active_plugins'] = get_option('active_plugins');

        $addons = array();             
        $addons['invitation_codes'] = array(
                                    'title'     => 'UPME Invitation Codes',
                                    'image'     => 'http://www.upmeaddons.innovativephp.com/wp-content/uploads/2014/12/invitationcodes.png',
                                    'desc'      => 'UPME Invitation Codes can be used to only allow registration for invited users. Admin can send invitations to email addresses.Registration will be blocked for normal users without valid code. You can create unlimited codes with limited number of users. Invitation codes can be enabled/disabled at any time using settings.',
                                    'name'      => 'upme-invitation-codes/upme-invitation-codes.php',
                                    'type'      => __('Free','upme'),
                                    'download'  => 'http://profileplugin.com/invitation-codes-addon/',
        
                                    );
        
        $addons['custom_field_types'] = array(
                                    'title'     => 'UPME Custom Field Types',
                                    'image'     => 'http://www.upmeaddons.innovativephp.com/wp-content/uploads/2014/11/upmeft.png',
                                    'desc'      => 'UPME Custom Field Types is an addon created to add custom field types for UPME custom fields section. You need User Profiles Made Easy plugin to use this addon. This addon extends the default predefined field types by providing more variations to suit specific requirements of each site. Currently it offers over 10 new field types and over 15 variations.',
                                    'name'      => 'upme-field-types-addon/upme-field-types.php',
                                    'type'      => __('Premium','upme'),
                                    'download'  => 'http://codecanyon.net/item/upme-custom-field-types/9527156?ref=nimeshrmr',
        
                                    );
        
        $addons['social_login'] = array(
                                    'title'     => 'UPME Social',
                                    'image'     => 'https://static-2.gumroad.com/res/gumroad/files/e096c7e44d214d08bcc6ebf098b42730/original/scl1.jpg',
                                    'desc'      => 'UPME Social is an addon created to integrate social networking capabilities into UPME profiles. Initial version contains the registration and login support for most popular social sites such as Facebook, Twitter and LinkedIn. More sites and social networking capabilities such as wall posts, friends, followers will be added in future versions',
                                    'name'      => 'upme_social_addon/upme-social.php',
                                    'type'      => __('Premium','upme'),
                                    'download'  => 'http://www.upmeaddons.innovativephp.com/upme-social/',
        
                                    );
        
        $upme_addon_template_data['addons'] = $addons;
        
        ob_start();
        $upme_template_loader->get_template_part('addons','feed');
        $display = ob_get_clean();
        echo $display;
    }
}



