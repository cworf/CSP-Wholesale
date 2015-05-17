<?php

class UPME_captcha_loader
{
    public $load_captcha = false;
    private $captcha_plugin = '';
    public $default_captcha_plugin = 'recaptcha';
    
    public function __construct()
    {
        // Nothing to do here.    
    }
    
    private function load_captcha_plugin_setting($captcha= '')
    {
        // Getting values from database
        $settings = get_option('upme_options');
        
        // Shortcode optionis not given or given blank
        if($captcha == '')
        {
            if(isset($settings['captcha_plugin']) && $settings['captcha_plugin'] != '' && $settings['captcha_plugin'] != 'none')
            {
                $this->load_captcha = true;
                $this->captcha_plugin = $settings['captcha_plugin']; 
            }
            else
            {
                $this->load_captcha = false;
            }
            
        }
        else if($captcha == 'no' || $captcha == 'false')
        {
            $this->load_captcha = false;
        }
        else 
        {
            if($captcha == 'yes' || $captcha == 'true')
            {
                if(isset($settings['captcha_plugin']) && $settings['captcha_plugin'] != '' && $settings['captcha_plugin'] != 'none')
                {
                    $this->load_captcha = true;
                    $this->captcha_plugin = $settings['captcha_plugin']; 
                }
                else
                {
                    $this->load_captcha = false;
                }
            }
            else
            {
                $this->load_captcha = true;
                $this->captcha_plugin = $captcha;
            }
                
        }
        
    }
    
    public function load_captcha($captcha= '')
    {
        global $upme;
        
        // Load captcha plugin settings based on shortcode and database values.
        $this->load_captcha_plugin_setting($captcha);
        
        if($this->load_captcha == true)
        {

            $method_name = 'load_'.$this->captcha_plugin;
            
            if(method_exists($this, $method_name))
            {
                $captcha_html = '';
                $captcha_html = $this->$method_name();
                
                if($captcha_html == '')
                {
                    return $this->load_no_captcha_html();
                }
                else
                {
                    $form_text = '';
                    $form_text = $upme->get_option('captcha_label');
                    if($form_text == '')
                        $form_text = __('Human Check','upme');
                    
                    
                    $display = '';
                    
                    $display.= '<div class="upme-field upme-edit upme-edit-show">';
                        $display.='<label class="upme-field-type"><i class="upme-icon upme-icon-none"></i><span>'.$form_text.'</span></label>';
                        $display.='<div class="upme-field-value">';
                            $display.=$captcha_html;
                            $display.='<input type="hidden" name="captcha_plugin" value="'.$this->captcha_plugin.'" />';
                        $display.='</div>';
                    $display.='</div>';
                    
                    return $display;
                }
            }
            else
            {
                $captcha_html = '';
                $captcha_html_params = array();
                $captcha_html = apply_filters('upme_captcha_load_'.$this->captcha_plugin,$captcha_html,$captcha_html_params);
                
                if($captcha_html == '')
                {
                    return $this->load_no_captcha_html();
                }
                else
                {
                    $form_text = '';
                    $form_text = $upme->get_option('captcha_label');
                    if($form_text == '')
                        $form_text = __('Human Check','upme');
                    
                    
                    $display = '';
                    
                    $display.= '<div class="upme-field upme-edit upme-edit-show">';
                        $display.='<label class="upme-field-type"><i class="upme-icon upme-icon-none"></i><span>'.$form_text.'</span></label>';
                        $display.='<div class="upme-field-value">';
                            $display.=$captcha_html;
                            $display.='<input type="hidden" name="captcha_plugin" value="'.$this->captcha_plugin.'" />';
                        $display.='</div>';
                    $display.='</div>';
                    
                    return $display;
                }
            }
        }
        else
        {
            return $this->load_no_captcha_html();
        }
    }
    
    public function load_no_captcha_html()
    {
        return '<input type="hidden" name="no_captcha" value="yes" />';
    }
    
    public function validate_captcha($captcha_plugin = '')
    {

        if($captcha_plugin == '')
        {
            // No plugin set, returning true
            return true; 
        }
        else
        {
            $method_name = 'validate_'.$captcha_plugin;
            
            if(method_exists($this, $method_name))
            {
                return $this->$method_name();
            }
            else
            {
                $captcha_check_params = array();
                $status = apply_filters('upme_captcha_check_'.$captcha_plugin,true,$captcha_check_params);
                return $status;
                
                //return true;
            }
            
        }
    }
    
    /*
     *  Function to Load Fun Captcha 
     */
    private function load_funcaptcha()
    {
        if(class_exists('FUNCAPTCHA')) 
        {
            $funcaptcha = funcaptcha_API();
            $options = funcaptcha_get_settings();
            return $funcaptcha->getFunCaptcha($options['public_key']);
        }   
        else
        {
            return '';
        }    
    }
    
    /*
     *  Function to validate Fun Captcha
     */
    private function validate_funcaptcha()
    {
        if(class_exists('FUNCAPTCHA'))
        {
            $funcaptcha = funcaptcha_API();
            $options = funcaptcha_get_settings();
            return $funcaptcha->checkResult($options['private_key']);
        }
        else
        {
            return true;
        } 
    }

    /*
     *  Function to Load Captcha by BestWebSoft
     */
    private function load_captchabestwebsoft()
    {
    
        if ( function_exists( 'cptch_register_form' ) ) 
        {

            ob_start();
            if( function_exists( 'cptch_display_captcha_custom' ) ) { 
                echo "<input type='hidden' name='cntctfrm_contact_action' value='true' />"; 
                echo cptch_display_captcha_custom();
            }; 
            if( function_exists( 'cptchpr_display_captcha_custom' ) ) { 
                echo "<input type='hidden' name='cntctfrm_contact_action' value='true' />"; 
                echo cptchpr_display_captcha_custom(); 
            };

            $display = ob_get_clean();
            return $display;
        }   
        else
        {
            return '';
        }    
    }

    /*
     *  Function to validate Captcha by BestWebSoft
     */
    private function validate_captchabestwebsoft()
    {
        if ( ( function_exists( 'cptch_check_custom_form' ) && cptch_check_custom_form() !== true ) 
                || ( function_exists( 'cptchpr_check_custom_form' ) && cptchpr_check_custom_form() !== true ) ){ 
            return false;
        }else{
            return true;
        }

    }


    /*
     *  Function to Load SI Captcha
     */
    private function load_si_captcha()
    {
    
        if ( class_exists( 'ReallySimpleCaptcha' ) ) 
        {

            $captcha_instance = new ReallySimpleCaptcha();
            $captcha_instance->bg = array( 0, 0, 0 );
            $word = $captcha_instance->generate_random_word();
            $prefix = mt_rand();
            return $captcha_instance->generate_image( $prefix, $word );
        }   
        else
        {
            return '';
        }    
    }

    /*
     *  Function to validate SI Captcha
     */
    private function validate_si_captcha()
    {
        if ( class_exists( 'ReallySimpleCaptcha' ) ) 
        {

        }else{
            return true;
        }

    }
    

   /*
    *  Function to Load ReCaptcha
    */
    
    private function load_recaptcha_class()
    {
        
        require_once upme_path . 'classes/class-upme-recaptchalib.php';
    } 
    
    private function load_recaptcha()
    {
        global $upme;
        $this->load_recaptcha_class();
        
        // Getting the Public Key to load reCaptcha
        $public_key = '';
        $public_key = $upme->get_option('recaptcha_public_key');
        
        if($public_key != '')
        {
            $captcha_code = '';
            
            // Loading the theme configured in admin.
            //$recaptcha_theme = $upme->get_option('recaptcha_theme');
            $recaptcha_theme = 'upme';
            
            if($recaptcha_theme == 'upme')
            {
                $theme_code = "<script type=\"text/javascript\"> var RecaptchaOptions = {    theme : 'custom',lang: 'en',    custom_theme_widget: 'recaptcha_widget' };</script>";
                $captcha_code = $this->load_custom_recaptcha($public_key);
            }
            else
            {
                $theme_code = "<script type=\"text/javascript\">var RecaptchaOptions = {theme : '".$recaptcha_theme."', lang:'en'};</script>";
                if(is_ssl()){
                    $captcha_code = recaptcha_get_html($public_key, null, true);
                }else{
                    $captcha_code = recaptcha_get_html($public_key, null);
                }
                
            }
            
            return $theme_code.$captcha_code;
        }
        else
        {
            // No public key is not set in admin. So loading no captcha HTML. 
            return $this->load_no_captcha_html();
        }
        
    }
    
   /*
    *  Function to Validate ReCaptcha
    */
    
    private function validate_recaptcha()
    {
        global $upme;
        $this->load_recaptcha_class();
        
        // Getting the Private Key to validate reCaptcha
        $private_key = '';
        $private_key = $upme->get_option('recaptcha_private_key');
        
        
        if($private_key != '')
        {
            if (is_in_post('recaptcha_response_field'))
            {
                $resp = recaptcha_check_answer ($private_key,
                        $_SERVER["REMOTE_ADDR"],
                        post_value("recaptcha_challenge_field"),
                        post_value("recaptcha_response_field"));
            
                // Captcha is Valid
                if ($resp->is_valid)
                {
                    return true;
                }
                else
                {
                    return false;
                }
            }
            else
            {
                return true;
            }    
        }
        else
        {
            // Private key is not set in admin
            return true;
        }
    }
    
    private function load_custom_recaptcha($public_key='')
    {

        $reCaptcha_server = RECAPTCHA_API_SERVER;
        if(is_ssl()){
            $reCaptcha_server = RECAPTCHA_API_SECURE_SERVER;
        }

        return '<div id="recaptcha_widget">
                        <div id="recaptcha_image_holder">
                            <div id="recaptcha_image" class="upme-captcha-img"></div>
                            <div class="recaptcha_text_box">
                                <input type="text" id="recaptcha_response_field" name="recaptcha_response_field" class="text" placeholder="Enter Verification Words" />
                            </div>
                        </div>
                        <div id="recaptcha_control_holder">
                            <a href="javascript:Recaptcha.switch_type(\'image\');" title="Load Image"><i class="upme-icon upme-icon-camera"></i></a>
                            <a href="javascript:Recaptcha.switch_type(\'audio\');" title="Load Audio"><i class="upme-icon upme-icon-volume-up"></i></a>
                            <a href="javascript:void(0);" id="recaptcha_reload_btn" onclick="Recaptcha.reload();" title="Refresh Image"><i class="upme-icon upme-icon-refresh"></i></a>
                        </div> 
                </div>

                 <script type="text/javascript" src="'.$reCaptcha_server.'/challenge?k='.$public_key.'"></script>
                 <noscript>
                   <iframe src="'.$reCaptcha_server.'/noscript?k='.$public_key.'" height="300" width="500" frameborder="0"></iframe>
                   <textarea name="recaptcha_challenge_field" rows="3" cols="40"></textarea>
                   <input type="hidden" name="recaptcha_response_field" value="manual_challenge">
                 </noscript>';
    }
    
    
}

$upme_captcha_loader = new UPME_captcha_loader();