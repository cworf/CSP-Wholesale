<?php
    global $upme_admin;
    if( !get_option('upme_email_templates')){
        global $upme_email_templates;
        $upme_email_templates->upme_reset_all_templates();
    }
?>

<tr valign="top">
    <th scope="row"><label for="Email Template Title"><?php _e('Email Template Title', 'upme'); ?></label></th>
    <td>
        <?php 
            $email_templates = array(                                
                                        '0'                         => __('Select Template', 'upme'),
                                        'reg_default_user'           => __('Default registration for users', 'upme'),
                                        'reg_default_admin'          => __('Default registration for admin', 'upme'),
                                        'reg_email_confirm_user'    => __('Registration with email confirmation for Users', 'upme'),
                                        'reg_email_confirm_admin'   => __('Registration with email confirmation for Admin', 'upme'),
                                        'nofify_profile_update'     => __('Notify Profile Update', 'upme'),
                                        'forgot_password'           => __('Forgot Password', 'upme'),
                                        'approval_notify_user'      => __('User approval notification for users', 'upme'),
                                        // 'reg_approval_user'         => __('Registration with approvals for users', 'upme'),
                                        // 'reg_approval_admin'        => __('Registration with approvals for admin', 'upme'),
                                    );
            
            echo UPME_Html::drop_down(array('name'=>'email_template','id'=>'email_template','class'=> 'chosen-admin_setting', 'style' => 'width:80%'), $email_templates, '0');
            
        ?><i class="upme-icon upme-icon-question-circle upme-tooltip2 option-help" original-title="<?php _e('Select the email template name.', 'upme') ?>"></i>
    </td>
</tr>
<tr valign="top" style='display:none'>
    <th scope="row"><label for="Email Status"><?php _e('Email Status', 'upme'); ?></label></th>
    <td class='site_content_allowed_roles'>
        <?php 
            $email_statuses = array('0' => __('Disabled - Don\'t send Email', 'upme'), '1' => __('Enabled - Send Email', 'upme'));
            echo UPME_Html::drop_down(array('name'=>'email_status','id'=>'email_status','class'=> 'chosen-admin_setting'), $email_statuses, '1');
            
            
            
        ?><i class="upme-icon upme-icon-question-circle upme-tooltip2 option-help" original-title="<?php _e('Enable/Disable sending a specific email.', 'upme') ?>"></i>
    </td>
</tr>
<tr valign="top" style='display:none'>
    <th scope="row"><label for="Email Subject"><?php _e('Email Subject', 'upme'); ?></label></th>
    <td>
        <?php 
           
            echo UPME_Html::text_box(array('name' => 'email_subject', 'id' => 'email_subject', 'class' => 'regular-text', 'value' => ''));
            
        ?><i class="upme-icon upme-icon-question-circle upme-tooltip2 option-help" original-title="<?php _e('Edit the subject of email template.', 'upme') ?>"></i>
    </td>
</tr>
<tr valign="top" style='display:none'>
    <th scope="row"><label for="Email Template Editor"><?php _e('Email Template Editor', 'upme'); ?></label></th>
    <td>
        <?php 
           
            echo UPME_Html::text_area(array('name' => 'email_template_editor', 'id' => 'email_template_editor', 'class' => 'large-text code text-area', 'value' => '', 'cols' => '50', 'style' => 'min-height:300px;width:90% !important;'));
            
        ?><i class="upme-icon upme-icon-question-circle upme-tooltip2 option-help" original-title="<?php _e('Edit the contents of email template.', 'upme') ?>"></i>
    </td>
</tr>