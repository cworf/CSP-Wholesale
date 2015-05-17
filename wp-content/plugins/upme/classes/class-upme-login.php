<?php

class UPME_Login {

	public $upme_settings;

	function __construct() {
    	add_action( 'init', array($this, 'handle_init' ) );
    	$this->upme_settings = get_option('upme_options');

    	add_action('upme_before_login_restrictions',array($this, 'upme_before_login_restrictions'), 10 ,2);
        add_action('upme_validate_login', array($this,'upme_two_factor_email_verify'));
        add_action( 'init', array($this, 'upme_two_factor_email_login' ) );
	}
	
	/*Prepare user meta*/
	function prepare ($array ) {
		foreach($array as $k => $v) {
			if ($k == 'upme-login') continue;
			$this->usermeta[$k] = $v;
		}
		return $this->usermeta;
	}
	
	/*Handle/return any errors*/
	function handle() {
	    global $upme_captcha_loader;

		// Verify whether login form name is modified
		if(isset($_POST['upme-hidden-login-form-name'])){

			$upme_secret_key = get_option('upme_secret_key');
            $login_form_name = $_POST['upme-hidden-login-form-name'];
            $login_form_name_hash = $_POST['upme-hidden-login-form-name-hash'];

            if($login_form_name_hash != hash('sha256', $login_form_name.$upme_secret_key) ){
            	// Invailid form name was defined by manually editing
            	$this->errors[] = __('Invalid login form.','upme');
            	return;
            }
            $this->login_form_name = $login_form_name;
		}
	    
		require_once(ABSPATH . 'wp-includes/pluggable.php');
		foreach($this->usermeta as $key => $value) {
		
			if ($key == 'user_login') {
				if (sanitize_user($value) == '') {
					$this->errors[] = __('The username field is empty.','upme');
				}
			}
			
			if ($key == 'user_pass') {
				if (esc_attr($value) == '') {
					$this->errors[] = __('The password field is empty.','upme');
				}
			}
		
		}

		/* UPME action for adding restrictions before login */
        $before_login_validation_params = array();
        do_action('upme_before_login_restrictions', $this->usermeta , $before_login_validation_params);
        /* END action */ 

		// Check approval status and activation status before login
		if(isset($_POST['user_login']) && '' != $_POST['user_login']){
			
			// Check whether email or username is used for login
			$user_email_check = email_exists($_POST['user_login']);
			if($user_email_check){
				$user_data = new stdClass;
				$user_data->ID = $user_email_check;
			}else{
				$user_data = get_user_by( 'login', $_POST['user_login'] );
				if(!$user_data){
					$user_data = new stdClass;
					$user_data->ID = '';
				}
			}			

			if('INACTIVE' == get_user_meta($user_data->ID, 'upme_approval_status' , true)){
				$this->errors[] = $this->upme_settings['html_profile_approval_pending_msg'];
			
			}else if('INACTIVE' == get_user_meta($user_data->ID, 'upme_activation_status' , true)){
				$this->errors[] = __('Please confirm your email to activate your account.','upme');
			}
		}
		
		
		// Check captcha first
		if(!is_in_post('no_captcha','yes'))
		{
		    if(!$upme_captcha_loader->validate_captcha(post_value('captcha_plugin')))
		    {
		        $this->errors[] = __('Please complete Captcha Test first.','upme');
		    }
		}
	
		/* attempt to signon */
		if (!is_array($this->errors)) {
			$creds = array();
			
			// Adding support for login by email
			if(is_email($_POST['user_login']))
			{
			    $user = get_user_by( 'email', $_POST['user_login'] );
			    if($user){
			    	if(isset($user->data->user_login))
				        $creds['user_login'] = $user->data->user_login;
				    else
				        $creds['user_login'] = '';
			    }else{
			    	$creds['user_login'] = sanitize_user($_POST['user_login'],TRUE);
			    }
			    
			}
			// User is trying to login using username
			else{
			    $creds['user_login'] = sanitize_user($_POST['user_login'],TRUE);

			}
			
			$creds['user_password'] = $_POST['login_user_pass'];
			$creds['remember'] = $_POST['rememberme'];
			
			$secure_cookie = false;
			if(is_ssl()){
				$secure_cookie = true;
			}

			/* UPME Action validating before login */
			do_action('upme_validate_login',$creds);
			// End Action

			if(!$this->errors){
                
                
				$user = wp_signon( $creds, $secure_cookie );
				
				if ( is_wp_error($user) ) {
					if ($user->get_error_code() == 'invalid_username') {
						$this->errors[] = __('Invalid Username or Email','upme');
					}
					if ($user->get_error_code() == 'incorrect_password') {
						$this->errors[] = __('Incorrect Username or Password','upme');
					}
					
					if ($user->get_error_code() == 'empty_password') {
					    $this->errors[] = __('Please enter a password.','upme');
					}

					/* UPME action for adding actions after unsuccessfull login */
			        $login_failed_params = array();
			        do_action('upme_login_failed', $this->usermeta, $user, $login_failed_params);
			        /* END action */				
					
				}else{
					do_action('wp_login');

					/* UPME action for adding actions after successfull login */
			        $login_sucess_params = array();
			        do_action('upme_login_sucess', $this->usermeta, $user, $login_sucess_params);
			        /* END action */
				}
			}
		}

		
			
	}
	
	/*Get errors display*/
	function get_errors() {
		global $upme;
		$display = null;
		
		if (isset($this->errors) && is_array($this->errors))  
		{
		    $display .= '<div class="upme-errors">';
		
			foreach($this->errors as $newError) {
				
				$display .= '<span class="upme-error upme-error-block"><i class="upme-icon upme-icon-remove"></i>'.$newError.'</span>';
			
			}
			$display .= '</div>';
		} else {

            // Get global login redirect settings
                        
            $login_redirect_page_id = (int) isset($this->upme_settings['login_redirect_page_id']) ? $this->upme_settings['login_redirect_page_id'] : 0;
                        
	      	if (isset($_GET['redirect_to']) && !empty($_GET['redirect_to'])) {
				$url = $_GET['redirect_to'];
			} elseif (isset($_POST['redirect_to']) && !empty($_POST['redirect_to']) ) {
				$url = $_POST['redirect_to'];
			} elseif ($login_redirect_page_id) {
                $url = get_permalink($login_redirect_page_id);
            } else {
				$url = $_SERVER['REQUEST_URI'];
			}

			/* UPME filter for customizing login redirection */
	        $login_redrect_uri_params = array();
	        $url = apply_filters('upme_login_redrect_uri', $url , $this->usermeta, $login_redrect_uri_params);
	        /* END filter */

			wp_redirect( $url );
		}
		return $display;
	}

    /* Initializing login class on init action  */
	function handle_init(){
		/*Form is fired*/
		if ( isset( $_POST['upme-login'] ) ) {

			/* Prepare array of fields */
			$this->prepare( $_POST );

			// Setting default to false;
			$this->errors = false;

			/* Validate, get errors, etc before we login a user */
			$this->handle();

		}
	}

	function upme_before_login_restrictions($usermeta, $params){
		global $upme_login;

	    $username = $usermeta['user_login'];
	    $email    = '';
	    if( is_email($username) ){
	    	$email    = $username;
	    }

//	    $this->upme_login_username_restrictions($username);
//	    $this->upme_login_email_restrictions($email);
	}

	function upme_login_username_restrictions($username){
		global $upme_login;

		$blocked_usernames = array();
		/* UPME filter for defining blocked emails for login */
        $login_blocked_username_params = array();
        $blocked_usernames = apply_filters('upme_login_blocked_usernames',array(),$login_blocked_username_params);
        /* End filter */ 

		if(in_array($username, $blocked_usernames)){
			$upme_login->errors[] = __('Username you have used is not allowed.','upme');
		}
	}

	function upme_login_email_restrictions($email){
		global $upme_login;
		
		/* UPME filter for defining blocked emails for login */
        $login_blocked_email_params = array();
        $blocked_emails = apply_filters('upme_login_blocked_emails',array(),$login_blocked_email_params);
        /* End filter */ 


		if(in_array($email, $blocked_emails)){
			$upme_login->errors[] = __('Email you have used is not allowed.','upme');
		}

		/* UPME filter for defining blocked emails for login */
        $login_blocked_email_domain_params = array();
        $blocked_email_domains = apply_filters('upme_login_blocked_email_domains',array(),$login_blocked_email_domain_params);
        /* End filter */ 


        if(is_email($email)){
        	$email_domain = explode('@', $email);
        	$email_domain = array_pop($email_domain);

			if(in_array($email_domain, $blocked_email_domains)){
				$upme_login->errors[] = __('Email domain you have used is not allowed.','upme');
			}
        }
		
	}
    
    /* Verify login details and send the verification link to email - 2 Factor authentication with email */
    function upme_two_factor_email_verify($creds){
        global $upme_email_templates;

        if(!$this->errors){
            $user_login = $creds['user_login'];

            if($this->upme_settings['email_two_factor_verification_status']){
                $user = get_user_by( 'login', $user_login );
                if ( $user && wp_check_password( $creds['user_password'], $user->data->user_pass, $user->ID) ){
                
                    $user_id = $user->ID;

                    if(get_user_meta($user_id,'upme_email_two_factor_status',true)){
                        
                        $link = get_permalink($this->upme_settings['login_page_id']);
                        $verification_code = wp_generate_password();
                        $link = add_query_arg(array('upme_email_two_factor_verify' => $verification_code), $link); 
                        $link = add_query_arg(array('upme_email_two_factor_login' => rawurlencode($user->data->user_login)), $link); 
                        update_user_meta($user_id,'upme_email_two_factor_code',$verification_code);
                        
                        $send_params = array('username' => $user->data->user_login , 'email' => $user->data->user_email, 'email_two_factor_login_link' => $link);
                        $email_status = $upme_email_templates->upme_send_emails('two_factor_email_verify', $send_params['email'] , '' , '' ,$send_params,$user_id);
                        
                        $this->errors[] = __('Verfication link sent to your email. Please click the link in the email to login','upme');
                    }
                }else{
                    $upme_login->errors[] = __('Incorrect username or password.','upme');
                }
            }      
        }
    }
    
    /* Verify code from email and automatic login for 2 factor email authentication */
    function upme_two_factor_email_login(){
        
        $verify_code = isset($_GET['upme_email_two_factor_verify']) ? $_GET['upme_email_two_factor_verify'] : '';
        $user_login  = isset($_GET['upme_email_two_factor_login']) ? $_GET['upme_email_two_factor_login'] : '';
        
        if('' != $verify_code){
            $user = get_user_by( 'login', $user_login );
            if($user){
                $user_id = $user->ID;
                if($verify_code == get_user_meta($user_id,'upme_email_two_factor_code',true)){
                    delete_user_meta($user_id,'upme_email_two_factor_code');
                    // Set automatic login based on the setting value in admin
                    wp_set_auth_cookie($user_id, false, is_ssl());
                    $link = get_permalink($this->upme_settings['login_page_id']);
                    wp_redirect($link);exit;
                }else{
                    $this->errors[] = __('Invalid verification code in link.','upme');
                }
            }else{
                $this->errors[] = __('Invalid verification link.','upme');
            }
        }
        
        
    }


}

$upme_login = new UPME_Login();