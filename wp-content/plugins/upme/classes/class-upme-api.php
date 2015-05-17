<?php
/*
 *  API for providing UPME date for other plugins and themes
 *
*/

/* TODO - Get the link to the user profile by username */
/* TODO - Get the link to the user profile by ID */

class UPME_API{

	public $upme_settings;

	public function __construct(){
		$this->upme_settings = get_option('upme_options');;
	}

	/* Get the user ID of logged in user */
	public function current_user_id(){

		$current_user_id = 0;
		if(is_user_logged_in()){
			$current_user_id = get_current_user_id();		
		}	

		return $current_user_id;
	}

	/* Check whether user is logged in to the site */
	public function is_user_loggedin(){
		if(is_user_logged_in()){
			return true;
		}
		return false;
	}

	/* Get the user ID of currently viewed profile */
	public function user_profile_id(){
		global $upme;
		if(isset($upme->current_view_profile_id) && '' != $upme->current_view_profile_id){
			return $upme->current_view_profile_id;
		}
		return false;
	}

	/* Display welcome message for the users using the username */
	public function display_welcome_message(){
		$message = __('Welcome Guest','upme');
		$message = apply_filters('upme_user_welcome_message',$message,'guest');

		if(is_user_logged_in()){
			$current_user = wp_get_current_user();
			$message = __('Welcome '.$current_user->user_login,'upme');
			$message = apply_filters('upme_user_welcome_message',$message,'member');		
		}	

		return $message;
	}

	/*  Get custom field value for loggedin user */
	public function get_loggedin_profile_field_value($meta_key){

		$field_value = false;

		if(is_user_logged_in()){
			$current_user = wp_get_current_user();
			$field_value = get_user_meta( $current_user->ID , $meta_key, TRUE);		
			
		}

		return $field_value;
	}

	/*  Get custom field value for user of currently viewed profile */
	public function get_viewed_profile_field_value($meta_key){
		global $upme;

		$field_value = false;

		if(isset($upme->current_view_profile_id) && '' != $upme->current_view_profile_id){
			$field_value = get_user_meta( $upme->current_view_profile_id , $meta_key, TRUE);		
		}

		return $field_value;
	}

	/*  Get custom field value for loggedin user using shortcode */
	public function get_loggedin_profile_field_shortcode_value($args,$content = ''){

		$defaults = array(
            'key' => null,
        );

        $args = wp_parse_args($args, $defaults);
        extract($args, EXTR_SKIP);

		return $this->get_loggedin_profile_field_value($key);
	}

	/*  Get custom field value for user of currently viewed profile using shortcode */
	public function get_viewed_profile_field_shortcode_value($args,$content = ''){
		$defaults = array(
            'key' => null,
        );

        $args = wp_parse_args($args, $defaults);
        extract($args, EXTR_SKIP);

		return $this->get_viewed_profile_field_value($key);
	}

	/* Get the link to the global profile page defined in UPME settings */
	public function profile_page_link(){
		$link = $this->upme_settings['profile_page_id'];
		if($link == 0){
			return false;
		}
		return get_permalink($link);
	}

	/* Get the link to the global member list page defined in UPME settings */
	public function member_list_page_link(){
		$link = $this->upme_settings['member_list_page_id'];
		if($link == 0){
			return false;
		}
		return get_permalink($link);
	}

	/* Get the link to the global registration page defined in UPME settings */
	public function registration_page_link(){
		$link = $this->upme_settings['registration_page_id'];
		if($link == 0){
			return false;
		}
		return get_permalink($link);
	}

	/* Get the link to the global login page defined in UPME settings */
	public function login_page_link(){
		$link = $this->upme_settings['login_page_id'];
		if($link == 0){
			return false;
		}
		return get_permalink($link);
	}

	/* Get the link to the global reset password page defined in UPME settings */
	public function reset_password_page_link(){
		$link = $this->upme_settings['reset_password_page_id'];
		if($link == 0){
			return false;
		}
		return get_permalink($link);
	}

	/* Get links to system pages using a shortcode. This function provides the option
	 * to get text link or the actual HTML link
	 */
	public function upme_api_page_link($args,$content = ''){

		$defaults = array(
            'page' => null,
            'link' => 'yes',
        );

        $args = wp_parse_args($args, $defaults);

        $this->upme_args = $args;

        extract($args, EXTR_SKIP);

        $display = '';

        $content_str = array(
        					'login' 			=> __('Login Page Link','upme'),
        					'registration' 		=> __('Registration Page Link','upme'),
        					'reset_password' 	=> __('Reset Password Page Link','upme'),
        					'profile' 			=> __('Profile Page Link','upme'),
        					'member_list' 		=> __('Member List Page Link','upme'),
        				);

        $content = ('' == $content) ? $content_str[$page] : $content;
        $func = $page.'_page_link';
        if('yes' == $link){
        	
			$display = '<a href="'.$this->$func().'">'.$content.'</a>';
		}else{
			$display = $this->$func();
		}

        return $display;
	}

}

$upme_api = new UPME_API();


/*
 *  =================================================
 *	Shortcodes of getting the output of API functions
 *  =================================================
 */

add_shortcode('upme_api_user_profile_id', array($upme_api,'user_profile_id'));
add_shortcode('upme_api_current_user_id', array($upme_api,'current_user_id'));
add_shortcode('upme_api_display_welcome_message', array($upme_api,'display_welcome_message'));
add_shortcode('upme_api_get_loggedin_profile_field_value', array($upme_api,'get_loggedin_profile_field_shortcode_value'));
add_shortcode('upme_api_get_viewed_profile_field_value', array($upme_api,'get_viewed_profile_field_shortcode_value'));
add_shortcode('upme_api_page_link', array($upme_api,'upme_api_page_link'));


