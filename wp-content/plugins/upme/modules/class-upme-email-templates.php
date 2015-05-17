<?php


class UPME_Email_Templates{

    public $user_id;
    public $current_option;

    function __construct(){   

        $this->templates = get_option('upme_email_templates');

        add_action( 'wp_ajax_upme_get_email_template',  array($this,'upme_get_email_template_info'));
        add_action( 'wp_ajax_upme_save_email_template',  array($this,'upme_save_email_template_info')); 
        add_action( 'wp_ajax_upme_reset_email_template',  array($this,'upme_reset_email_template_info'));         
    }

    public function upme_reset_email_template_info(){
        $template_name = isset($_POST['template_name']) ? $_POST['template_name'] : '';
       
        if($this->templates){

            $template = $this->upme_get_template($template_name,'1');

            $this->templates[$template_name] = $template;
            update_option('upme_email_templates',$this->templates);

            $response['status'] = 'success';
            $response['html']   = __('Email Template reset successfully.','upme');
            $response['temp_status']   = $template['status'];
            $response['temp_content']   = $template['template'];
            $response['temp_subject']   = $template['subject'];

        }else{
            $message = __('Template not found.','upme');
            $response['status'] = 'fail';
            $response['html']   = $message;
        }

        echo json_encode($response);exit;
    }

    public function upme_save_email_template_info(){
        $template_name = isset($_POST['template_name']) ? $_POST['template_name'] : '';
        $template_content = isset($_POST['template_content']) ? trim($_POST['template_content']) : '';
        $template_status = isset($_POST['template_status']) ? trim($_POST['template_status']) : '';
        $template_subject = isset($_POST['template_subject']) ? trim($_POST['template_subject']) : '';
        
        if($this->templates){
            $template = $this->templates[$template_name];
            $template['status'] = $template_status;
            $template['template'] = $template_content;
            $template['subject'] = $template_subject;
            $this->templates[$template_name] = $template;
            update_option('upme_email_templates',$this->templates);

            $response['status'] = 'success';
            $response['html']   = __('Email Template saved successfully.','upme');
        }else{
            $message = __('Template not found.','upme');
            $response['status'] = 'fail';
            $response['html']   = $message;
        }

        echo json_encode($response);exit;
    }

    public function upme_get_email_template_info(){
        $template_name = isset($_POST['template_name']) ? $_POST['template_name'] : '';

        if($this->templates){
            $template = $this->templates[$template_name];
            $response['status'] = 'success';
            $response['temp_status']   = $template['status'];
            $response['temp_content']   = $template['template'];
            $response['temp_subject']   = $template['subject'];
        }else{
            $message = __('Template not found.','upme');
            $response['status'] = 'fail';
            $response['html']   = $message;
        }

        echo json_encode($response);exit;
    }

    public function upme_get_saved_template($template_name){
        $template_info = (array) $this->templates[$template_name];
        return $template_info;
    }

    public function upme_reset_all_templates(){
        $templates = $this->upme_default_email_templates();
        update_option('upme_email_templates',$templates);
    }

    // public function upme_save_template(){
    //     $template_name = isset($_POST['template_name']) ? $_POST['template_name'] : '';
    //     $template_content = isset($_POST['template_content']) ? trim($_POST['template_content']) : '';
    //     $template_status = isset($_POST['template_status']) ? $_POST['template_status'] : '1';
    // }

    public function upme_default_email_templates(){

        $email_templates = array();

        $templates = array('reg_default_user', 'reg_default_admin', 'reg_email_confirm_user',
                            'reg_email_confirm_admin', 'nofify_profile_update', 'approval_notify_user',
                            'forgot_password','two_factor_email_verify');

        foreach ($templates as $key => $template) {
            $email_templates[$template] = $this->upme_get_template($template,'1');
        }

        return $email_templates;
    }

    public function upme_get_template($name,$status){

        ob_start();
        require_once upme_path. 'modules/email_templates/upme_' . $name . '_view.php';
        $template = ob_get_clean();

        $subject = '';
        switch($name){
            case 'reg_default_user':
                $subject = sprintf( __('[%s] Your username and password','upme'), get_option('blogname') );
                break;
            case 'reg_default_admin':
                $subject = sprintf(__('[%s] New User Registration','upme'), get_option('blogname') );
                break;
            case 'reg_email_confirm_user':
                $subject = sprintf(__('[%s] Action Required: Email Verification','upme'), get_option('blogname'));
                break;
            case 'reg_email_confirm_admin':
                $subject = sprintf(__('[%s] New User Registration','upme'), get_option('blogname'));
                break;
            case 'nofify_profile_update':
                $subject = __('Profile Information Update','upme');
                break;
            case 'approval_notify_user':
                $subject = sprintf(__('[%s] User Account Approved','upme'), get_option('blogname'));
                break;
            case 'forgot_password':
                $subject = sprintf( __('[%s] Password Reset','upme'), get_option('blogname') );
                break;
            case 'two_factor_email_verify':
                $subject = sprintf( __('[%s] Email Verification and Login','upme'), get_option('blogname') );
                break;
        }

        return array('status' => $status, 'template' => $template, 'subject' => $subject);

    }

    public function upme_send_emails($template_name,$email,$subject,$message,$params, $user_id){
        global $upme_email_templates;
        $template_info = $this->upme_get_saved_template($template_name);


        if($template_info['status'] == '1'){

            if(trim($template_info['template']) != ''){

                $allowed_tags = array();
                switch ($template_name) {
                    case 'nofify_profile_update':

                        $allowed_tags = array('full_name','changed_fields','blog_name');
                        break;
                    case 'reg_default_user':
                        $template_info_optional = $this->upme_get_saved_template('reg_default_admin');
                        if(trim($template_info_optional['template']) == ''){
                            return false;
                        }else{
                            // Generate the necessary info for user from user ID
                            $allowed_tags = array('username','password','login_link','blog_name');
                            $user = new WP_User( $user_id );
                            $email = $user->user_email;
                        }
                        break;
                    case 'reg_default_admin':
                        $template_info_optional = $this->upme_get_saved_template('reg_default_user');
                        if(trim($template_info_optional['template']) == ''){
                            return false;
                        }else{
                            // Generate the necessary info for user from admin email
                            $allowed_tags = array('email','username','blog_name');
                            $user = get_user_by( 'email' , get_option('admin_email') );
                            $email = $user->data->user_email;
                        }
                        break; 

                    case 'reg_email_confirm_user':
                        $allowed_tags = array('activation_link','username','password','blog_name');
                        break;
                    case 'reg_email_confirm_admin':
                        $allowed_tags = array('email','username','blog_name');
                        break;
                    case 'approval_notify_user':
                        $allowed_tags = array('username','login_link','email','blog_name');
                        break;   
                    case 'forgot_password':
                        $allowed_tags = array('network_home_url','username','reset_page_url','blog_name');
                        break;
                    case 'two_factor_email_verify':
                        $allowed_tags = array('email','username','email_two_factor_login_link','blog_name');
                        break; 
                }

                $subject = $template_info['subject'];  
                $message = $template_info['template'];                
                $message = upme_match_template_tags($template_name, $message, $allowed_tags,$params, $user_id);

            }

            return wp_mail(
                $email,                
                $subject,
                $message
            );


        }else{
            return true;
        }   
    }

    

}

$upme_email_templates = new UPME_Email_Templates();