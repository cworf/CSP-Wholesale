<!-- <tr valign="top">
    <th scope="row"><label for="Restriction Type"><?php _e('Restriction Type', 'upme'); ?></label></th>
    <td>
        <?php 
            $site_restrictions = array(
                                        'lock_full'   => __('Lock Entire Site', 'upme'),
                                        'lock_part'   => __('Lock Part of Site', 'upme')
                                    );
            
            echo UPME_Html::drop_down(array('name'=>'site_content_restrictions','id'=>'site_content_restrictions'), $site_restrictions, 'lock_none');
            
        ?><i class="upme-icon upme-icon-question-circle upme-tooltip2 option-help" original-title="<?php _e('Select whether you want to restrict entire site or part of the site for users based on following conditions.', 'upme') ?>"></i>
    </td>
</tr> -->

<tr valign="top">
    <th scope="row"><label for="Restricted Section"><?php _e('Restricted Section', 'upme'); ?></label></th>
    <td>
        <?php 
            $site_restriction_section = array(                                
                                        'all_pages'                 => __('Restrict All Pages', 'upme'),
                                        'all_posts'                 => __('Restrict All Posts', 'upme'),
                                        'restrict_selected_pages'    => __('Restrict Selected Pages', 'upme'),
                                        'restrict_selected_posts'    => __('Restrict Selected Posts', 'upme'),
                                        
                                    );
            
            echo UPME_Html::drop_down(array('name'=>'site_content_section_restrictions','id'=>'site_content_section_restrictions','class'=> 'chosen-admin_setting'), $site_restriction_section, 'all_pages');
            
        ?><i class="upme-icon upme-icon-question-circle upme-tooltip2 option-help" original-title="<?php _e('Select type of content to be restricted.', 'upme') ?>"></i>
    </td>
</tr>

<tr valign="top">
    <th scope="row"><label for="Restricted Pages"><?php _e('Restricted Pages', 'upme'); ?></label></th>
    <td>
        <?php 
            $site_content_allowed_pages = $upme_admin->get_all_pages();
            echo UPME_Html::drop_down(array('name'=>'site_content_page_restrictions[]','id'=>'site_content_page_restrictions','class'=> 'chosen-admin_setting','multiple'=>''), $site_content_allowed_pages, '');
            
        ?><i class="upme-icon upme-icon-question-circle upme-tooltip2 option-help" original-title="<?php _e('Select pages to be restricted.', 'upme') ?>"></i>
    </td>
</tr>

<tr valign="top">
    <th scope="row"><label for="Restricted Posts"><?php _e('Restricted Posts', 'upme'); ?></label></th>
    <td>
        <?php 
            $site_content_allowed_posts = $upme_admin->get_all_posts();
            echo UPME_Html::drop_down(array('name'=>'site_content_post_restrictions[]','id'=>'site_content_post_restrictions','class'=> 'chosen-admin_setting','multiple'=>''), $site_content_allowed_posts, '');
            
        ?><i class="upme-icon upme-icon-question-circle upme-tooltip2 option-help" original-title="<?php _e('Select posts to be restricted.', 'upme') ?>"></i>
    </td>
</tr>

<tr valign="top">
    <th scope="row"><label for="Allowed Users"><?php _e('Allowed Users', 'upme'); ?></label></th>
    <td>
        <?php 
            $site_restriction_by = array(
                                        'by_all_users'    => __('All Logged in Users', 'upme'),
                                        'by_user_roles'     => __('Specific User Roles', 'upme')
                                    );
            
            echo UPME_Html::drop_down(array('name'=>'site_content_user_restrictions','id'=>'site_content_user_restrictions','class'=> 'chosen-admin_setting'), $site_restriction_by, 'by_guest_users');
            
        ?><i class="upme-icon upme-icon-question-circle upme-tooltip2 option-help" original-title="<?php _e('Select what type of users have access to restricted content.', 'upme') ?>"></i>
    </td>
</tr>

<tr valign="top">
    <th scope="row"><label for="Allowed User Roles"><?php _e('Allowed User Roles', 'upme'); ?></label></th>
    <td class='site_content_allowed_roles'>
        <?php 
            global $upme_roles;
            $site_allowed_roles = $upme_roles->upme_available_user_roles_restriction_rules();
            
            $checked_value = '';
            foreach ($site_allowed_roles as $role_key => $role) {
                echo UPME_Html::check_box(array('name' => 'site_content_allowed_roles[]', 'id' => 'site_content_allowed_roles', 'value' => $role_key),$checked_value).$role.'<br/>';            
            }
            
            
        ?><i class="upme-icon upme-icon-question-circle upme-tooltip2 option-help" original-title="<?php _e('Select which user roles will be allowed to access this content.', 'upme') ?>"></i>
    </td>
</tr>

<tr valign="top">
    <th scope="row"><label for="Redirect URL"><?php _e('Redirect URL', 'upme'); ?></label></th>
    <td>
        <?php 
            $site_redirect_allowed_pages = $upme_admin->get_all_pages();
            echo UPME_Html::drop_down(array('name'=>'site_content_redirect_url','id'=>'site_content_redirect_url','class'=> 'chosen-admin_setting'), $site_redirect_allowed_pages, '0');
        
                       
        ?><i class="upme-icon upme-icon-question-circle upme-tooltip2 option-help" original-title="<?php _e('Specify the redirection URL for users with unauthorized access based on this rule.', 'upme') ?>"></i>
    </td>
</tr>

