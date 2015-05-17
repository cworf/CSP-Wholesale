<?php

class UPME_Profile_Fields {

    private $user_roles;
    private $upme_profile_statuses;

    function __construct() {
        $this->upme_profile_statuses = array(
                                            'INACTIVE' => __('Inactive','upme'),
                                            'ACTIVE'   => __('Active','upme')
                                        );

        /* UPME Filter for adding custom profile status */
        $upme_custom_profile_statuses = apply_filters('upme_custom_profile_statuses',array());
        $this->upme_profile_statuses = array_merge($this->upme_profile_statuses,$upme_custom_profile_statuses);
        // End Filter
    }

    /* Returns the available mandatory fields for backend profile */
    public function upme_backend_mandatory_fields($upme_settings,$user) {

        $display = '';

        if($upme_settings['profile_view_status'] || current_user_can('manage_options')){
      
            $display .= '<tr>';
            $profile_status_label = __('Profile Status','upme');
            $display .= '<th scope="row"><label for="' . $profile_status_label . '">' . $profile_status_label . '</label></th>';

            $current_profile_status = esc_attr(get_the_author_meta('upme_user_profile_status', $user->ID));

            $display .= '<td><select class="input" name="upme[upme_user_profile_status]" id="upme_user_profile_status">';
                        foreach ($this->upme_profile_statuses as $status=>$display_status) {
                            $status = trim($status);

                            $display .= '<option value="' . $status . '" ' . selected($current_profile_status, $status, 0) . '>' . $display_status . '</option>';
                        }
            $display .= '</select></td></tr>';
        }
        
        if($upme_settings['email_two_factor_verification_status'] || current_user_can('manage_options') ){
            
            $display .= '<tr>';
            $label = __('Email Authentication','upme');
            $display .= '<th scope="row"><label for="' . $label . '">' . $label . '</label></th>';

            $current_profile_status = esc_attr(get_the_author_meta('upme_email_two_factor_status', $user->ID));

            $display .= '<td><select class="input" name="upme[upme_email_two_factor_status]" id="upme_email_two_factor_status">';
            $display .= '<option value="0" ' . selected($current_profile_status, '0', 0) . '>' . __('Disable','upme') . '</option>';
            $display .= '<option value="1" ' . selected($current_profile_status, '1', 0) . '>' . __('Enable','upme') . '</option>';
               
            $display .= '</select></td></tr>';
            
        }

        return $display;
    }

    public function upme_frontend_mandatory_fields($upme_settings,$user_id,$profile_user_id){

        $display = '';

        if($upme_settings['profile_view_status']){

            $current_profile_status = esc_attr(get_the_author_meta('upme_user_profile_status', $profile_user_id));

            $display .= '<div class="upme-field upme-edit">';
            $display .= '<label class="upme-field-type" for="upme_user_profile_status-' . $profile_user_id . '">';

            $name     = __('Profile Status','upme');
            
            $display .= '<i class="upme-icon upme-icon-unlock-alt"></i>';
            $display .= '<span>' . apply_filters('upme_edit_profile_label_upme_user_profile_status', $name) . '</span></label>';
    
            $display .= '<div class="upme-field-value">';
            $display .= '<select class="upme-input " name="upme_user_profile_status-' . $profile_user_id . '" id="upme_user_profile_status-' . $profile_user_id . '" >';
                            foreach ($this->upme_profile_statuses as $status=>$display_status) {
                                $status = trim($status);

                                $display .= '<option value="' . $status . '" ' . selected($current_profile_status, $status, 0) . '>' . $display_status . '</option>';
                            }
            $display .= '</select>';
            $display .= '<div class="upme-clear"></div>';
            $display .= '</div></div>';
        }
        
        if($upme_settings['email_two_factor_verification_status']){
            
            $current_profile_status = esc_attr(get_the_author_meta('upme_email_two_factor_status', $profile_user_id));

            $display .= '<div class="upme-field upme-edit">';
            $display .= '<label class="upme-field-type" for="upme_email_two_factor_status-' . $profile_user_id . '">';

            $name     = __('Email Authentication','upme');
            
            $display .= '<i class="upme-icon upme-icon-unlock-alt"></i>';
            $display .= '<span>' . apply_filters('upme_edit_profile_label_email_two_factor_status', $name) . '</span></label>';
    
            $display .= '<div class="upme-field-value">';
            $display .= '<select class="upme-input " name="upme_email_two_factor_status-' . $profile_user_id . '" id="upme_email_two_factor_status-' . $profile_user_id . '" >';
            $display .= '<option value="0" ' . selected($current_profile_status, '0', 0) . '>' . __('Disable','upme') . '</option>';
            $display .= '<option value="1" ' . selected($current_profile_status, '1', 0) . '>' . __('Enable','upme') . '</option>';                            
                            
            $display .= '</select>';
            $display .= '<div class="upme-clear"></div>';
            $display .= '</div></div>';
            
        }

        return $display;

    }
}

$upme_profile_fields = new UPME_Profile_Fields();