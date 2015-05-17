<?php

class UPME_Reset_Password {

	function __construct() {
    	add_action( 'init', array($this, 'handle_init' ) );


	}
	
	/*Prepare user meta*/
	function prepare ($array ) {
		foreach($array as $k => $v) {
			if ($k == 'upme-reset-password') continue;
			$this->usermeta[$k] = $v;
		}
		return $this->usermeta;
	}
	
	/*Handle/return any errors*/
	function handle() {

		// Get username from reset password link to get user
        $login = isset($_GET['login']) ? $_GET['login'] : '';
        $user_data = get_user_by('login', $login);
        $user_id = '';
        if (($user_data instanceof WP_User)) {
            $user_id = $user_data->ID;
        }

        $upme_settings = get_option('upme_options');
	    
		require_once(ABSPATH . 'wp-includes/pluggable.php');
		foreach($this->usermeta as $key => $value) {
		
			if ($key == 'upme_new_password') {
				if (esc_attr($value) == '') {
					$this->errors[] = __('The new password field is empty.','upme');
				}
			}
			
			if ($key == 'upme_confirm_new_password') {
				if (esc_attr($value) == '') {
					$this->errors[] = __('The confirm new password field is empty.','upme');
				}
			}
		
		}


		if(!empty($this->usermeta['upme_new_password']) && !empty($this->usermeta['upme_confirm_new_password']) 
			&& $this->usermeta['upme_new_password'] != $this->usermeta['upme_confirm_new_password']){
			$this->errors[] = __('The passwords do not match.','upme');
		}

		
	
			/* attempt to signon */
			if (!is_array($this->errors)) {

				$reset_pass_key = get_user_meta($user_id, 'upme_reset_pass_key' , true);

				$key = isset($_GET['key']) ? $_GET['key'] : ''; 
				if('expired' == $reset_pass_key || ($key != '' && $reset_pass_key != $key)){
					$reset_password_page_id = (int) isset($upme_settings['reset_password_page_id']) ? $upme_settings['reset_password_page_id'] : 0;
        			if ($reset_password_page_id) {
                		$url = get_permalink($reset_password_page_id);
                		$url = upme_add_query_string($url,'upme_reset_status=expired');
            		}
            		
					wp_redirect( $url );exit;
				}
			
				wp_update_user(array('ID' => $user_id, 'user_pass' => esc_attr($this->usermeta['upme_new_password'])));
				 
				// Expire the reset password key after usage
				update_user_meta($user_id, 'upme_reset_pass_key','expired');

				
                $login_redirect_page_id = (int) isset($upme_settings['login_page_id']) ? $upme_settings['login_page_id'] : 0;
                     
      			if ($login_redirect_page_id) {
                	$url = get_permalink($login_redirect_page_id);
                	
                	wp_redirect( $url );exit;
            	}
				
			}
			
	}
	
	/*Get errors display*/
	function get_errors() {
		global $upme;
		$display = null;

		// Get global login redirect settings
        $upme_settings = get_option('upme_options');
        $login_redirect_page_id = (int) isset($upme_settings['login_redirect_page_id']) ? $upme_settings['login_redirect_page_id'] : 0;
        $reset_password_page_id = (int) isset($upme_settings['reset_password_page_id']) ? $upme_settings['reset_password_page_id'] : 0;
        


        $action = isset($_GET['action']) ? $_GET['action'] : '';
        $key = isset($_GET['key']) ? $_GET['key'] : ''; 
        $login = isset($_GET['login']) ? $_GET['login'] : '';
        $upme_reset_status = isset($_GET['upme_reset_status']) ? $_GET['upme_reset_status'] : '';
        $info_message = '';

        $user_data = get_user_by('login', $login);
		$user_id = '';
        if (($user_data instanceof WP_User)) {
            $user_id = $user_data->ID;
        }


        if('upme_reset_pass' == $action && '' != $key){
            $reset_pass_key = get_user_meta($user_id, 'upme_reset_pass_key' , true);
            
            
            if('expired' == $reset_pass_key){
            	$this->errors[] = __('This password key has expired or has already been used. Please initiate a new password reset.','upme');
            
            	if ($reset_password_page_id) {
                	$url = get_permalink($reset_password_page_id);
                	$url = upme_add_query_string($url,'upme_reset_status=expired');
            	}
				wp_redirect( $url );

            }else if($reset_pass_key != $key){
                $this->errors[] = __('Invalid Reset Password Key','upme');
            }
            else{
                $this->success[] = __('Please enter the new password.','upme');
            }
        }elseif('expired' == $upme_reset_status){

        	$this->errors[] = __('This password key has expired or has already been used. Please initiate a new password reset.','upme');
            
        }

		
		if (isset($this->errors) && is_array($this->errors))  
		{
		    $display .= '<div class="upme-errors">';
		
			foreach($this->errors as $newError) {
				
				$display .= '<span class="upme-error upme-error-block"><i class="upme-icon upme-icon-remove"></i>'.$newError.'</span>';
			
			}
			$display .= '</div>';
		} else if (isset($this->success) && is_array($this->success)) {
		    $display .= '<div class="upme-success">';
		
			foreach($this->success as $newMsg) {
				
				$display .= '<span class="upme-success upme-success-block"><i class="upme-icon upme-icon-ok"></i>'.$newMsg.'</span>';
			
			}
			$display .= '</div>';
		}


		else {

                        
      		if ($login_redirect_page_id) {
                $url = get_permalink($login_redirect_page_id);
            } else {
				$url = $_SERVER['REQUEST_URI'];
			}
			wp_redirect( $url );
		}
		return $display;
	}

    /* Initializing login class on init action  */
	function handle_init(){
		/*Form is fired*/
		if ( isset( $_POST['upme-reset-password'] ) ) {

			/* Prepare array of fields */
			$this->prepare( $_POST );

			// Setting default to false;
			$this->errors = false;

			/* Validate, get errors, etc before we login a user */
			$this->handle();

		}
	}

}

$upme_reset_password = new UPME_Reset_Password();