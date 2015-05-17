<?php

class UPME_Private_Content {

	private $status;
	private $current_user_id;

	public function __construct(){
		$this->status  = true;
		$this->current_user_id = 0;

		add_action('init',array($this,'get_current_user'));        

	}

	public function get_current_user(){
		if (is_user_logged_in ()) {
 			$this->current_user_id = get_current_user_id();
		}
	}

	/**
	 * Apply all the validation filters on private conetnt and returns
	 * whether content is vieweble and respective messages
	 * 
	 * @param 	array $atts 	Shortcode attributes for private content
	 * @param 	string $content The content placed within private shortcode
	 * @return 	array 			Information on whether content is viewable and respective messages
	 */
	public function validate_private_content($atts, $content){

		$private_content_result = array('status'=>true, 'type'=>'');

		$defaults = array(
            'message' => 'on'
        );
        $args = wp_parse_args($atts, $defaults);
        extract($args, EXTR_SKIP);

        // Provide permission for admin to view any content
        if(current_user_can('manage_options')){
        	return $private_content_result;
        }

        // Execute default filter for validating members or guests
        $this->status = $this->guest_filter();
        if(!$this->status){
        	$private_content_result['status'] = false;
        	$private_content_result['type'] = 'guest';
        	return $private_content_result;
        }

        // Apply the optional dynamic filters on shortcode
        foreach ($args as $sh_attr => $sh_value) {
        	switch ($sh_attr) {
	        	case 'allowed_roles':
	        		$this->status = $this->allowed_roles_filter($sh_value);
	        		$private_content_result['type'] = $sh_attr;
	        		break;

	        	case 'blocked_roles':
	        		$this->status = $this->blocked_roles_filter($sh_value);
	        		$private_content_result['type'] = $sh_attr;
	        		break;

	        	case 'allowed_users':
	        		$this->status = $this->allowed_users_filter($sh_value);
	        		$private_content_result['type'] = $sh_attr;
	        		break;

	        	case 'blocked_users':
	        		$this->status = $this->blocked_users_filter($sh_value);
	        		$private_content_result['type'] = $sh_attr;
	        		break;

	        	case 'allowed_meta_key':

	        		$this->status = $this->allowed_meta_key_filter($sh_value, $args['allowed_meta_value']);
	        		$private_content_result['type'] = $sh_attr;
	        		break;

	        	case 'blocked_meta_key':
	        		$this->status = $this->blocked_meta_key_filter($sh_value, $args['blocked_meta_value']);
	        		$private_content_result['type'] = $sh_attr;
	        		break;

	        	case 'custom_restriction_key':
	        		/* UPME Filters for applying custom rules on private content verification */
	        		$custom_restriction_key = isset($args['custom_restriction_key']) ? $args['custom_restriction_key'] : '';
	        		$custom_restriction_value = isset($args['custom_restriction_value']) ? $args['custom_restriction_value'] : '';
	                $validate_private_content_params = array('user_id' => $this->current_user_id, 'value'=> $custom_restriction_value, 'key' => $custom_restriction_key );
	                
	                $this->status = apply_filters( 'upme_validate_private_content', $this->status , $validate_private_content_params);
	                // End Filters
	        		$private_content_result['type'] = $custom_restriction_key;
	        		break;
        	
	        }

	        // Set permission denied status and message
	        if(!$this->status){
	        	$private_content_result['status'] = false;        		
	        }
	        
        }
        
        return $private_content_result;
	}

	/**
	 * Check whether user is a member or guest to block the content
	 *
	 * @return boolean Whether user is a guest or a member
	 */
	public function guest_filter(){
		if (!is_user_logged_in())
			return false;
		return true;
	}

	/**
	 * Validate whether logged in user has one of allowed user roles
	 * 
	 * @param 	string $roles 	List of allowed user roles separated by commas
	 * @return 	boolean 		Whether logged in user is able to view the content
	 */
	public function allowed_roles_filter($roles){
		global $upme_roles;

		$user_roles = $upme_roles->upme_get_user_roles_by_id($this->current_user_id);
		$roles = explode(',', $roles);

		// TODO - Implement action or fiilter to override exisitng validation and use new validations

		foreach ($roles as $role) {
			if(in_array($role, $user_roles)){
				return true;
			}
		}
		
		return false;
	}

	/**
	 * Validate whether logged in user has one of blocked user roles
	 * 
	 * @param 	string $roles 	List of blocked user roles separated by commas
	 * @return 	boolean 		Whether logged in user is able to view the content
	 */
	public function blocked_roles_filter($roles){
		global $upme_roles;

		$user_roles = $upme_roles->upme_get_user_roles_by_id($this->current_user_id);
		$roles = explode(',', $roles);

		foreach ($roles as $role) {
			if(in_array($role, $user_roles)){
				return false;
			}
		}
		
		return true;
	}

	/**
	 * Validate whether logged in user is part of the allowed users list
	 * 
	 * @param 	string $users 	List of allowed user ID's separated by commas
	 * @return 	boolean 		Whether logged in user is able to view the content
	 */
	public function allowed_users_filter($users){

		$users = explode(',', $users);
		if(in_array($this->current_user_id, $users)){
			return true;		
		}
		
		return false;

	}

	/**
	 * Validate whether logged in user is part of the blocked users list
	 * 
	 * @param 	string $users 	List of blocked user ID's separated by commas
	 * @return 	boolean 		Whether logged in user is able to view the content
	 */
	public function blocked_users_filter($users){

		$users = explode(',', $users);
		if(in_array($this->current_user_id, $users)){
			return false;		
		}
		
		return true;

	}

	/**
	 * Validate whether logged in user has specified meta key and value
	 * 
	 * @param 	string $meta_key 	Meta key of usermeta table
	 * @param 	string $meta_value 	Meta value of usermeta table
	 * @return 	boolean 			Whether logged in user is able to view the content
	 */
	public function allowed_meta_key_filter($meta_key,$meta_value){

		$value = get_user_meta($this->current_user_id,trim($meta_key),true);

		if(trim($value) == trim($meta_value)){
			return true;		
		}
		
		return false;
	}

	/**
	 * Validate whether logged in user doesnt have the specified meta key and value
	 * 
	 * @param 	string $meta_key 	Meta key of usermeta table
	 * @param 	string $meta_value 	Meta value of usermeta table
	 * @return 	boolean 			Whether logged in user is able to view the content
	 */
	public function blocked_meta_key_filter($meta_key,$meta_value){

		$value = get_user_meta($this->current_user_id,$meta_key,true);
		if($value == $meta_value){
			return false;		
		}
		
		return true;
	}

	/**
	 * Prepare the message to be displayed for the restricted users based
	 * on the imposed restrictions.
	 * 
	 * @param 	array  $private_content_result 	Result array generated by restrictions
	 * @return 	string Content to be displayed for restricted users
	 */
	public function get_restriction_message($args,$content,$private_content_result){
		global $upme;

		$display = null;

        /* Arguments */
        $defaults = array(
            'message' => 'on'
        );
        $args = wp_parse_args($args, $defaults);
        extract($args, EXTR_SKIP);

        /* Require login */
        if (!$private_content_result['status']) {

            if ($message !== 'off') {

            	switch ($private_content_result['type']) {
            		case 'guest':
            			/* filter wildcards */
		                $html = $upme->get_option('html_private_content');
		                $html = str_replace("{upme_current_uri}", $upme->current_page, $html);
		                $display .= wpautop($html);

		                if($upme->get_option('html_private_content_form')){
		                  $display .= do_shortcode('[upme_login]');  
		                } 
            			break;
            		
            		case 'allowed_roles':
            		case 'blocked_roles':
            		case 'allowed_users':
            		case 'blocked_users':
            		case 'allowed_meta_key':
            		case 'blocked_meta_key':
		        		$html = $upme->get_option('html_members_private_content');
		                $html = str_replace("{upme_current_uri}", $upme->current_page, $html);
		                $display .= wpautop($html);

		                $params = array('restriction_type' => $private_content_result['type']);
		                $display = apply_filters('upme_private_content_restriction_info',$display,$params);
		        		break;
		        	default:
		        		$html = $upme->get_option('html_members_private_content');
		                $html = str_replace("{upme_current_uri}", $upme->current_page, $html);
		                $display .= wpautop($html);

		                $params = array('restriction_type' => $private_content_result['type']);
		                $display = apply_filters('upme_private_content_restriction_info',$display,$params);
		        		break;
		        	
            	}                

                               
            }
        } else { /* Show hidden content */
            /* Adding do_shortcode again to allow shortcode inside shortcode, now private content can have shortcode too */
            $display .= do_shortcode($content);
        }

        return $display;
	}

}

$upme_private_content =  new UPME_Private_Content();


?>