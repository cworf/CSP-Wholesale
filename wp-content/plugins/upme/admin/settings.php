<div class="wrap">
    <h2><?php _e('UPME - Settings','upme')?></h2>
    <div class="updated" id="upme-settings-saved" style="display:none;">
        <p><?php _e('Settings Saved','upme');?></p>
    </div>
    
    <div class="updated" id="upme-settings-reset" style="display:none;">
        <p><?php _e('Settings Reset Completed.','upme');?></p>
    </div>
    
    <div id="upme-tab-group" class="upme-tab-group vertical_tabs">
        <ul id="upme-tabs" class="upme-tabs">
            <li class="upme-tab active" id="upme-general-settings-tab"><?php _e('General Settings','upme');?></li>
            <li class="upme-tab" id="upme-profile-settings-tab"><?php _e('User Profile Settings','upme')?></li>
            <li class="upme-tab" id="upme-system-pages-tab"><?php _e('UPME System Pages','upme')?></li>
            <li class="upme-tab" id="upme-redirect-setting-tab"><?php _e('Redirect Settings','upme')?></li>
            <li class="upme-tab" id="upme-registration-option-tab"><?php _e('Registration Options','upme')?></li>
            <li class="upme-tab" id="upme-search-settings-tab"><?php _e('Search Settings','upme')?></li>
            <li class="upme-tab" id="upme-privacy-option-tab"><?php _e('Privacy Options','upme')?></li>
            <li class="upme-tab" id="upme-misc-messages-tab" ><?php _e('Messages','upme')?></li>
            <li class="upme-tab" id="upme-scripts-styles-tab" ><?php _e('Scripts and Styles','upme')?></li>
        </ul>
        <div id="upme-tab-container" class="upme-tab-container" style="min-height: 325px;">
            <div class="upme-tab-content-holder">
                <div class="upme-tab-content" id="upme-general-settings-content">
                    <h3><?php _e('General Settings','upme');?></h3>
                    <form id="upme-general-settings-form">
                        <table class="form-table">
                            <tbody>
                                <tr valign="top">
                                    <th scope="row"><label for="style"><?php _e('Style', 'upme'); ?></label></th>
                                    <td>
                                        <?php 
                                            $custom_styles = glob(upme_path.'styles/*.css');
                                            $styles[] =  __('None - I will use custom CSS','upme');
                                            
                                            if(is_array($custom_styles))
                                            {
                                                foreach($custom_styles as $key=>$value)
                                                {
                                                    $name = str_replace('.css','',str_replace(upme_path.'styles/','',$value));
                                                    
                                                    $styles[$name] = $name;
                                                }
                                            }
                                            
                                            echo UPME_Html::drop_down(array('name'=>'style','id'=>'style'), $styles, $this->options['style']);
                                            
                                        ?><i class="upme-icon upme-icon-question-circle upme-tooltip2 option-help" original-title="<?php _e('Select Theme Style or disable CSS output to use your own custom CSS.', 'upme') ?>"></i>
                                    </td>
                                </tr>
                                
                                <tr valign="top">
                                    <th scope="row"><label for="date_format"><?php _e('Date Format', 'upme'); ?></label></th>
                                    <td>
                                    <?php 
                                        
                                        $property = array('name'=>'date_format','id' => 'date_format');

                                        $version_message = '';
                                        if(!function_exists('date_create_from_format') && '-1' == upme_php_version_status){
                                            $data = array(
                                                    'mm/dd/yy' => date('m/d/Y')                                                    
                                            );

                                            $version_message = __('Your server is running PHP '.phpversion().', which does nto support custom date formats. Update to PHP 5.3+ for custom date formats','upme');
                                        }else{
                                            $data = array(
                                                    'mm/dd/yy' => date('m/d/Y'),
                                                    'yy/mm/dd' => date('Y/m/d'),
                                                    'dd/mm/yy' => date('d/m/Y'),
                                                    'yy-mm-dd' => date('Y-m-d'),
                                                    'dd-mm-yy' => date('d-m-Y'),
                                                    'mm-dd-yy' => date('m-d-Y'),
                                                    'MM d, yy' => date('F j, Y'),
                                                    'd M, y' => date('j M, y'),
                                                    'd MM, y' => date('j F, y'),
                                                    'DD, d MM, yy' => date('l, j F, Y')
                                            ); 
                                        }
                                        
                                        echo UPME_Html::drop_down($property, $data, $this->options['date_format']);
                                    
                                    ?><i class="upme-icon upme-icon-question-circle upme-tooltip2 option-help" original-title="<?php _e('Select the date format to be used for date picker.', 'upme') ?>"></i>
                                    <p class="description"><?php echo $version_message; ?></p>
                                    </td>
                                </tr>
                                
                                <?php 
                                    
                                    
                                    $this->add_plugin_setting(
                                        'select',
                                        'hide_frontend_admin_bar',
                                        __('Admin Bar', 'upme'),
                                        array(
                                            'enabled' => __('Enabled', 'upme'),
                                            'hide_from_non_admin' => __('Hide from Non-Admin Users', 'upme'),
                                            'hide_from_all' => __('Hide from All Users', 'upme')
                                        ),
                                        __('Optionally hide the WordPress admin bar for logged in users on frontend pages.', 'upme'),
                                        __('Enabled will show the WordPress admin bar to all users. You amy select an option to hide the admin bar on frontend for non-admin users or all users.', 'upme')
                                    );

                                    $this->add_plugin_setting(
                                            'checkbox',
                                            'lightbox_avatar_cropping',
                                            __('Lighbox Avatar Cropping', 'upme'),
                                            '1',
                                            __('If checked, users will be able to crop avatar images in a lightbox.', 'upme'),
                                            __('Unchecking this option will enable the default file upload instead of lightbox cropping.', 'upme')
                                    );

                                    $this->add_plugin_setting(
                                            'checkbox',
                                            'ajax_profile_field_save',
                                            __('Use AJAX on Custom Fields', 'upme'),
                                            '1',
                                            __('If checked, backend custom fields will be updated and sorted using AJAX.', 'upme'),
                                            __('Checking this option will enable AJAX on backend custom fields. Useful for working with large number
                                            of custom fields .', 'upme')
                                    );

                                ?>
                                
                                <tr valign="top">
                                    <th scope="row"><label>&nbsp;</label></th>
                                    <td>
                                        <?php 
                                            echo UPME_Html::button('button', array('name'=>'save-upme-general-settings-tab', 'id'=>'save-upme-general-settings-tab', 'value'=>'Save Changes', 'class'=>'button button-primary upme-save-options'));
                                            echo '&nbsp;&nbsp;';
                                            echo UPME_Html::button('button', array('name'=>'reset-upme-general-settings-tab', 'id'=>'reset-upme-general-settings-tab', 'value'=>__('Reset Options','upme'), 'class'=>'button button-secondary upme-reset-options'));
                                        ?>
                                    </td>
                                </tr>
                                
                            </tbody>
                        </table>
                    </form>
                </div>
                <div class="upme-tab-content" id="upme-profile-settings-content" style="display:none;">
                    <h3><?php _e('User Profile Settings','upme');?></h3>
                    <form id="upme-profile-settings-form">
                        <table class="form-table">
                            <tbody>
                                <?php 
                                    $this->add_plugin_setting(
                                            'select',
                                            'clickable_profile',
                                            __('Display Name / User Link Options', 'upme'),
                                            array(
                                                    1 => __('Link to user profiles', 'upme'),
                                                    2 => __('Link to author archives', 'upme'),
                                                    0 => __('No link, show as static text', 'upme')),
                                            __('Enable/disable linking of Display Names on user profiles', 'upme'),
                                            __('This is where the display name on user profiles will link.', 'upme')
                                    );

                                    $this->add_plugin_setting(
                                            'select',
                                            'profile_url_type',
                                            __('Profile Permalinks', 'upme'),
                                            array(
                                                1 => __('User ID', 'upme'),
                                                2 => __('Username', 'upme')),
                                            __('Select profile link type.<br />
												Username will be written as <code>profile/username/</code><br />
												User ID will be writtne as <code>profile/1/</code>', 'upme'),
                                            __('This is the rewrite rule used to link to user profiles.', 'upme')
                                    );
                                    
                                    $this->add_plugin_setting(
                                            'checkbox',
                                            'link_author_posts_page',
                                            __('Link Post Count to Author Archive', 'upme'),
                                            '1',
                                            __('If checked, post/entries count on user profiles will link to the Author archive page.', 'upme'),
                                            __('Unchecking this option will show post count in text only, without linking to Author archive.', 'upme')
                                    );
                                    
                                    $this->add_plugin_setting(
                                            'input',
                                            'avatar_max_size',
                                            __('Maximum allowed user image size', 'upme'), array(),
                                            sprintf(__('Provide file size in megabytes, decimal values are accepted. Your server configuration supports up to <strong>%s</strong>', 'upme'), ini_get('upload_max_filesize')),
                                            __('Users will receive an error message if they try to upload files larger than the limit set here.', 'upme')
                                    );
                                    
                                    $this->add_plugin_setting(
                                            'checkbox',
                                            'show_separator_on_profile',
                                            __('Show separators on profiles', 'upme'), array(),
                                            __('<p>If checked, separators will be displayed when viewing front-end profiles.<br />
                                                    Otherwise, separators are displayed only on the registration form and when editing profiles.<br />
                                                    If you are using this option, it is recommended to also enable the next option to show empty fields on profiles.</p>', 'upme'),
                                            __('Separators may be added & edited in the UPME Custom Fields section. When using this option, it is recommended to also check the option below to show empty fields on profiles.', 'upme')
                                    );
                                    
                                    $this->add_plugin_setting(
                                            'checkbox',
                                            'show_empty_field_on_profile',
                                            __('Show empty fields on profiles', 'upme'), array(),
                                            __('<p>If checked, empty fields will be displayed when viewing front-end profiles.<br />
                                                    Otherwise, only fields populated with data are when viewing front-end profiles.</p>', 'upme'),
                                            __('Empty fields are fields where a user has not filled in any data.', 'upme')
                                    );

                                    $this->add_plugin_setting(
                                            'select',
                                            'profile_title_field',
                                            __('Field for Profile Title', 'upme'),
                                            $this->upme_profile_title_fields(),                                            
                                            __('Select the field to be displayed as profile title. Default and recommeneded choice is <code>Display Name</code>.', 'upme'),
                                            __('This field will be used as the display name on user profiles.', 'upme')
                                    );

                                    $this->add_plugin_setting(
                                            'checkbox',
                                            'show_recent_user_posts',
                                            __('Show recent user posts on profiles', 'upme'), array(),
                                            __('If checked, recent posts of the user will be displayed under profile information.', 'upme'),
                                            __('User need to be the author of these posts.', 'upme')
                                    );

                                    $allowed_post_counts = array_combine( range(1,10), range(1,10));
                                    
                                    $this->add_plugin_setting(
                                            'select',
                                            'maximum_allowed_posts',
                                            __('Maximum number of posts', 'upme'),
                                            $allowed_post_counts,
                                            __('Select maximum post count allowed for user profiles.', 'upme'),
                                            __('Given number of user posts are displayed under the profile.', 'upme')
                                    );
                                    

                                    $this->add_plugin_setting(
                                            'checkbox',
                                            'show_feature_image_posts',
                                            __('Show featured images on posts', 'upme'), array(),
                                            __('If checked, featured image thumbnails will be displayed with the users recent posts.', 'upme'),
                                            __('If feature image doesnt exist, a default image will be used.', 'upme')
                                    );

                                    $this->add_plugin_setting(
                                            'select',
                                            'website_link_on_profile',
                                            __('Website links on user profiles', 'upme'),
                                            array(
                                                    0 => __('No link, plain text', 'upme'),
                                                    1 => __('Live link', 'upme')),
                                            __('Enable/disable linking of Website on user profiles', 'upme'),
                                            __('This is where the website on user profiles will link.', 'upme')
                                    );

                                    $this->add_plugin_setting(
                                            'input',
                                            'profile_modal_window_shortcode',
                                            __('Shortcode for Profile Popup', 'upme'), array(),
                                            sprintf(__('Provide [upme] shortcode with neccessary attribute values for profile modal window', 'upme'), ini_get('upload_max_filesize')),
                                            __('When modal window is enabled on profile links, this shortcode will be used', 'upme')
                                    );

                                    $this->add_plugin_setting(
                                            'checkbox',
                                            'profile_view_status',
                                            __('Enable Profile Status', 'upme'), array(),
                                            __('If checked, users will be able to change the profile status to active/inactive.', 'upme'),
                                            __('Admin can check this feature to enable the private status feature for users.', 'upme')
                                    );

                                    $this->add_plugin_setting(
                                            'checkbox',
                                            'display_profile_status',
                                            __('Display Profile Status', 'upme'), array(),
                                            __('If checked, users will be able to see the status of the profile along with other profile fields.', 'upme'),
                                            __('Admin can check this feature to display the profile status inside profiles.', 'upme')
                                    );

                                    $this->add_plugin_setting(
                                            'checkbox',
                                            'link_post_author_to_upme',
                                            __('Link Post Author Link to UPME Profile', 'upme'),
                                            array(),
                                            __('If checked, author link in posts will link to the UPME profile page.', 'upme'),
                                            __('Unchecking this option will link post author to Author archive.', 'upme')
                                    );

                                    $this->add_plugin_setting(
                                            'checkbox',
                                            'display_profile_after_post',
                                            __('Display UPME Profile for Post Author after Post Content', 'upme'),
                                            array(),
                                            __('If checked, UPME profile of author is displayed after the post content.', 'upme'),
                                            __('Unchecking this option will not display UPME author profile.', 'upme')
                                    );

                                    $this->add_plugin_setting(
                                            'select',
                                            'author_post_profile_template',
                                            __('Template for Post Author Profiles', 'upme'),
                                            array(
                                                    '0' => __('Default Design', 'upme'),
                                                    'author_design_one' => __('Author Profile Design 1', 'upme'),
                                                    'author_design_two' => __('Author Profile Design 2', 'upme'),
                                                    'author_design_three' => __('Author Profile Design 3', 'upme'),
                                                    'author_design_four' => __('Author Profile Design 4', 'upme'),
                                                ),
                                            __('Select the template for displaying author profile.', 'upme'),
                                            __('Select the template for displaying author profile.', 'upme')
                                    );

                                    $this->add_plugin_setting(
                                            'checkbox',
                                            'email_two_factor_verification_status',
                                            __('Enable Two-Factor authentication with Email', 'upme'), array(),
                                            __('If checked, users will be able to enable Two-Factor authentication with email.', 'upme'),
                                            __('Admin can check this feature to enable Two-Factor authentication with email for users.', 'upme')
                                    );
                                ?>
                                
                                <tr valign="top">
                                    <th scope="row"><label>&nbsp;</label></th>
                                    <td>
                                        <?php 
                                            echo UPME_Html::button('button', array('name'=>'save-upme-profile-settings-tab', 'id'=>'save-upme-profile-settings-tab', 'value'=>'Save Changes', 'class'=>'button button-primary upme-save-options'));
                                            echo '&nbsp;&nbsp;';
                                            echo UPME_Html::button('button', array('name'=>'reset-upme-profile-settings-tab', 'id'=>'reset-upme-profile-settings-tab', 'value'=>__('Reset Options','upme'), 'class'=>'button button-secondary upme-reset-options'));
                                            
                                        ?>
                                    </td>
                                </tr>
                                
                            </tbody>
                        </table>
                    </form>
                </div>
                <div class="upme-tab-content" id="upme-system-pages-content" style="display:none;">
                    <h3><?php _e('UPME System Pages','upme');?></h3>
                    <p><?php _e('These pages are automatically created when UPME is activated. You can leave them as they are or change to custom pages here.', 'upme'); ?></p>
                    <form id="upme-system-pages-form">
                        <table class="form-table">
                            <tbody>
                            <?php 
                                $this->add_plugin_setting(
                                        'select',
                                        'profile_page_id',
                                        __('UPME Profile Page', 'upme'),
                                        $this->get_all_pages(),
                                        __('If you wish to change default UPME Profile page, you may set it here. Make sure you have the <code>[upme]</code> shortcode on this page.', 'upme'),
                                        __('This page is where users will view their own profiles, or view other user profiles from the member directory if allowed.', 'upme')
                                );
                                
                                $this->add_plugin_setting(
                                        'select',
                                        'login_page_id',
                                        __('UPME Login Page', 'upme'),
                                        $this->get_all_pages(),
                                        __('If you wish to change default UPME login page, you may set it here. Make sure you have the <code>[upme_login]</code> shortcode on this page.', 'upme'),
                                        __('The default front-end login page.', 'upme')
                                );
                                
                                
                                $this->add_plugin_setting(
                                        'select',
                                        'registration_page_id',
                                        __('UPME Registration Page', 'upme'),
                                        $this->get_all_pages(),
                                        __('If you wish to change default UPME Registration page, you may set it here. Make sure you have the <code>[upme_registration]</code> shortcode on this page.', 'upme'),
                                        __('The default front-end Registration page where new users will sign up.', 'upme')
                                );

                                $this->add_plugin_setting(
                                        'select',
                                        'reset_password_page_id',
                                        __('UPME Reset Password Page', 'upme'),
                                        $this->get_all_pages(),
                                        __('If you wish to change default UPME Reset Password page, you may set it here. Make sure you have the <code>[upme_reset_password]</code> shortcode on this page.', 'upme'),
                                        __('The default front-end Reset Password page where new users will sign up.', 'upme')
                                );

                                $this->add_plugin_setting(
                                        'select',
                                        'member_list_page_id',
                                        __('UPME Member List Page', 'upme'),
                                        $this->get_all_pages(),
                                        __('If you wish to change default UPME Member List page, you may set it here. Make sure you have member list shortcode there, for example: <code>[upme_search] [upme group=all view=compact users_per_page=10]</code> is the default.', 'upme'),
                                        __('The default front-end Member List page.', 'upme')
                                );
                            ?>
                            <tr valign="top">
                                <th scope="row"><label>&nbsp;</label></th>
                                <td>
                                    <?php 
                                        echo UPME_Html::button('button', array('name'=>'save-upme-system-pages-tab', 'id'=>'save-upme-system-pages-tab', 'value'=>'Save Changes', 'class'=>'button button-primary upme-save-options'));
                                        echo '&nbsp;&nbsp;';
                                        echo UPME_Html::button('button', array('name'=>'reset-upme-system-pages-tab', 'id'=>'reset-upme-system-pages-tab', 'value'=>__('Reset Options','upme'), 'class'=>'button button-secondary upme-reset-options'));
                                    ?>
                                </td>
                            </tr>
                            
                            </tbody>
                        </table>
                    </form>
                </div>
                <div class="upme-tab-content" id="upme-redirect-setting-content" style="display:none;">
                    <h3><?php _e('Redirect Settings','upme');?></h3>
                    <form id="upme-redirect-setting-form">
                        <table class="form-table">
                            <?php 
                                $this->add_plugin_setting(
                                        'checkbox',
                                        'redirect_backend_profile',
                                        __('Redirect Backend Profiles', 'upme'),
                                        '1',
                                        __('If checked, non-admin users who try to access backend WP profiles will be redirected to UPME Profile Page specified above.', 'upme'),
                                        __('Checking this option will send all users to the front-end UPME Profile Page if they try to access the default backend profile page in wp-admin. The page can be selected in the UPME System Pages settings.', 'upme')
                                );
                                
                                $this->add_plugin_setting(
                                        'checkbox',
                                        'redirect_backend_login',
                                        __('Redirect Backend Login', 'upme'),
                                        '1',
                                        __('If checked, non-admin users who try to access backend login form will be redirected to the front end UPME Login Page specified above.', 'upme'),
                                        __('Checking this option will send all users to the front-end UPME Login Page if they try to access the default backend login form. The page can be selected in the UPME System Pages settings.', 'upme')
                                );
                                
                                $this->add_plugin_setting(
                                        'checkbox',
                                        'redirect_backend_registration',
                                        __('Redirect Backend Registrations', 'upme'),
                                        '1',
                                        __('If checked, non-admin users who try to access backend registration form will be redirected to the front end UPME Registration Page specified above.', 'upme'),
                                        __('Checking this option will send all users to the front-end UPME Registration Page if they try to access the default backend registraiton form. The page can be selected in the UPME System Pages settings.', 'upme')
                                );
                                
                                
                                $login_page_options = $this->get_all_pages();
                                $login_page_options['0'] = __('Default', 'upme');
                                
                                $this->add_plugin_setting(
                                        'select',
                                        'login_redirect_page_id',
                                        __('Redirect After Login', 'upme'),
                                        $login_page_options,
                                        __('Users will be redirected to the page set here after successfully logging in. <br /> You may over-ride this setting for a specific login form by using the shortcode <code>[upme_login redirect_to="url_here"]</code>', 'upme'),
                                        __('Setting this option to Default will automatically use any redirect specified in the URL, and will not prevent redirect_to set in shortcode option from working. If no redirect is found in the URL and redirect_to option is not set in shortcode option, the login page will simply be reloaded to welcome the logged in user.', 'upme')
                                );
                                
                                $register_page_options = $this->get_all_pages();
                                $register_page_options['0'] = __('Default', 'upme');
                                
                                $this->add_plugin_setting(
                                        'select',
                                        'register_redirect_page_id',
                                        __('Redirect After Registration', 'upme'),
                                        $register_page_options,
                                        __('New users will be redirected to the page set here after successfully registering using the UPME registration form. <br /> You may over-ride this setting for a specific registration form by using the shortcode <code>[upme_registration redirect_to="url_here"]</code>', 'upme'),
                                        __('Setting this option to Default will show the Register Success message instead of redirecting to a custom page.', 'upme')
                                );
                            ?>
                            
                            <tr valign="top">
                                <th scope="row"><label>&nbsp;</label></th>
                                <td>
                                    <?php 
                                        echo UPME_Html::button('button', array('name'=>'save-upme-redirect-setting-tab', 'id'=>'save-upme-redirect-setting-tab', 'value'=>'Save Changes', 'class'=>'button button-primary upme-save-options'));
                                        echo '&nbsp;&nbsp;';
                                        echo UPME_Html::button('button', array('name'=>'reset-upme-redirect-setting-tab', 'id'=>'reset-upme-redirect-setting-tab', 'value'=>__('Reset Options','upme'), 'class'=>'button button-secondary upme-reset-options'));
                                    ?>
                                </td>
                            </tr>
                            
                        </table>
                    </form>
                </div>
                <div class="upme-tab-content" id="upme-registration-option-content" style="display:none;">
                    <h3><?php _e('Registration Options','upme');?></h3>
                    <form id="upme-registration-option-form">
                        <table class="form-table">
                            <?php 
                                $this->add_plugin_setting(
                                        'select',
                                        'set_password',
                                        __('User Selected Passwords', 'upme'),
                                        array(
                                                1 => __('Enabled, allow users to set password', 'upme'),
                                                0 => __('Disabled, email a random password to users', 'upme')),
                                        __('Enable or disable setting a user selected password at registration', 'upme'),
                                        __('If enabled, users can choose their own password at registration. If disabled, WordPress will email users a random password when they register.', 'upme')
                                );
                                
                                // Automatic Login Selection Start
                                $this->add_plugin_setting(
                                        'select',
                                        'automatic_login',
                                        __('Automatic Login After Registration', 'upme'),
                                        array(
                                                '1' => __('Enabled, log users in automatically after registration', 'upme'),
                                                '0' => __('Disabled, users must login normally after registration', 'upme')
                                        ),
                                        __('Enable or disable automatic login after registration.', 'upme'),
                                        __('If enabled, users will be logged automatically after registration and redirected to the page defined in Redirect After Registration setting. If disabled, users must login normally after registration.', 'upme')
                                );


                                $this->add_plugin_setting(
                                        'select',
                                        'profile_approval_status',
                                        __('User Profile Approvals', 'upme'),
                                        array(
                                                0 => __('Disabled, new users are not required to get the approval.', 'upme'),
                                                1 => __('Enabled, new users must get the approval', 'upme')),
                                        __('Enable or disable setting a user account approval at registration', 'upme'),
                                        __('If enabled, users must be approved by admin before login. If disabled, new users are not required to get the account approved before login.', 'upme')
                                );

                                $this->add_plugin_setting(
                                        'select',
                                        'set_email_confirmation',
                                        __('Email Confirmation', 'upme'),
                                        array(
                                                0 => __('Disabled, new users are not required to confirm email', 'upme'),
                                                1 => __('Enabled, new users must confirm email to activate', 'upme')),
                                        __('Enable or disable setting a user email confirmation at registration', 'upme'),
                                        __('If enabled, users must confirm email address to activate. If disabled, new users are not required to confirm email address.', 'upme')
                                );

                                $this->add_plugin_setting(
                                        'select',
                                        'accepting_terms_and_conditions',
                                        __('Terms and Conditions', 'upme'),
                                        array(
                                                0 => __('Disabled, terms and conditions are not shown in registration form', 'upme'),
                                                1 => __('Enabled, terms and conditions are shown in registration form', 'upme')),
                                        __('Enable or disable agreeing to terms and conditions at registration', 'upme'),
                                        __('If enabled, users must agree to terms and conditions for registration.', 'upme')
                                );


                                // Automatic Login Selection End
                                // Captcha Plugin Selection Start
                                $captcha_plugins = array(
                                                            'none' => __('None', 'upme'),
                                                            'funcaptcha' => __('FunCaptcha', 'upme'),
                                                            'recaptcha' => __('reCaptcha', 'upme'),
                                                            'captchabestwebsoft' => __('Captcha', 'upme'),
                                                    );
                                $captcha_plugins_params = array();
                                $captcha_plugins = apply_filters('upme_captcha_plugins_list',$captcha_plugins,$captcha_plugins_params);

                                $this->add_plugin_setting(
                                        'select',
                                        'captcha_plugin',
                                        __('Captcha Plugin', 'upme'),
                                        $captcha_plugins,
                                        __('Select which captcha plugin you want to use on the registration form. Funcaptcha requires the Funcaptcha plugin, however reCaptcha is built into UPME and requires no additional plugin to be installed. <br /> You can enable or disable captcha with shortcode options: <code>[upme_registration captcha=yes]</code> or <code>[upme_registration captcha=no]</code>.', 'upme'),
                                        __('If you are using a captcha that requires a plugin, you must install and activate the selected captcha plugin. Some captcha plugins require you to register a free account with them, including FunCaptcha', 'upme')
                                );
        
                                // Captcha Plugin Selection End
                                $this->add_plugin_setting(
                                        'input',
                                        'captcha_label',
                                        __('CAPTCHA Field Label', 'upme'), array(),
                                        __('Enter text which you want to show in form in front of CAPTCHA.', 'upme'),
                                        __('Enter text which you want to show in form in front of CAPTCHA.', 'upme')
                                );
                                
                                $this->add_plugin_setting(
                                        'input',
                                        'recaptcha_public_key',
                                        __('reCaptcha Public Key', 'upme'), array(),
                                        __('Enter your reCaptcha Public Key. You can sign up for a free reCaptcha account <a href="http://www.google.com/recaptcha" title="Get a reCaptcha Key" target="_blank">here</a>.', 'upme'),
                                        __('Your reCaptcha kays are required to use reCaptcha. You can register your site for a free key on the Google reCaptcha page.', 'upme')
                                );
                                
                                $this->add_plugin_setting(
                                        'input',
                                        'recaptcha_private_key',
                                        __('reCaptcha Private Key', 'upme'), array(),
                                        __('Enter your reCaptcha Private Key.', 'upme'),
                                        __('Your reCaptcha kays are required to use reCaptcha. You can register your site for a free key on the Google reCaptcha page.', 'upme')
                                );
                                
                                $this->add_plugin_setting(
                                        'textarea',
                                        'msg_register_success',
                                        __('Register success message', 'upme'),
                                        null,
                                        __('Show a text message when users complete the registration process.', 'upme'),
                                        __('This message will be shown to users after registration is complted.', 'upme')
                                );
                                
                                $this->add_plugin_setting(
                                        'textarea',
                                        'html_register_success_after',
                                        __('Text/HTML below the Register Success message.', 'upme'),
                                        null,
                                        __('Show a text/HTML content under success message when users complete the registration process.', 'upme'),
                                        __('This message will be shown to users under the success messsage after registration is completed.', 'upme')
                                );

                                $this->add_plugin_setting(
                                        'checkbox',
                                        'select_user_role_in_registration',
                                        __('Select User Role at Registration', 'upme'),
                                        '1',
                                        __('If checked, users will be able to select their user role at registration. If you do not understand what this means, leave this option unchecked.', 'upme'),
                                        __('Checking this option will enable users to select a user role at registration, based on available user roles defined in the choose roles setting.', 'upme')
                                );

                                $this->add_plugin_setting(
                                        'input',
                                        'label_for_registration_user_role',
                                        __('Select Role Label', 'upme'), array(),
                                        __('Enter text which you want to show as the label for User Role selection.', 'upme'),
                                        __('Enter text which you want to show as the label for User Role selection.', 'upme')
                                );

                                global $upme_roles;
                                $default_role = get_option("default_role");                        

                                $this->add_plugin_setting(
                                        'checkbox_list',
                                        'choose_roles_for_registration',
                                        __('Choose User Roles for Registration', 'upme'),
                                        $upme_roles->upme_available_user_roles_registration(),
                                        __('Selected user roles will be available for users to choose at registration. The default role for new users will be always available, you can change the default role in WordPress general settings.', 'upme'),
                                        __('User roles selected in this section will appear on the registration form. Be aware that some user roles will give posting and editing access to your site, so please be careful when using this option.', 'upme'),
                                        '',
                                        array('disabled' => array($default_role))
                                );

                                global $predefined;

                                $this->add_plugin_setting(
                                        'select',
                                        'default_predefined_country',
                                        __('Default Country', 'upme'),
                                        $predefined->get_array('countries'),
                                        __('List the countries to be used as default country.', 'upme'),
                                        __('Selected country will appear as the default value for country fields.', 'upme')
                                );

                                $this->add_plugin_setting(
                                        'select',
                                        'enforce_password_strength',
                                        __('Password Strength Level', 'upme'),
                                        array(
                                                '0' => __('Disabled', 'upme'),
                                                '1' => __('Weak', 'upme'),
                                                '2' => __('Medium', 'upme'),
                                                '3' => __('Strong', 'upme')
                                        ),
                                        __('Select the level of strength for user password.', 'upme'),
                                        __('User password should match the expected criteria required by strength level', 'upme')
                                );                               

                                
                            ?>
                            
                            <tr valign="top">
                                <th scope="row"><label>&nbsp;</label></th>
                                <td>
                                    <?php 
                                        echo UPME_Html::button('button', array('name'=>'save-upme-registration-option-tab', 'id'=>'save-upme-registration-option-tab', 'value'=>'Save Changes', 'class'=>'button button-primary upme-save-options'));
                                        echo '&nbsp;&nbsp;';
                                        echo UPME_Html::button('button', array('name'=>'reset-upme-registration-option-tab', 'id'=>'reset-upme-registration-option-tab', 'value'=>__('Reset Options','upme'), 'class'=>'button button-secondary upme-reset-options'));
                                    ?>
                                </td>
                            </tr>
                            
                        </table>
                    </form>
                </div>
                <div class="upme-tab-content" id="upme-search-settings-content" style="display:none;">
                    <h3><?php _e('Search Settings','upme');?></h3>
                    <form id="upme-search-settings-form">
                        <table class="form-table">
                            <?php 
                                $this->add_plugin_setting(
                                        'checkbox',
                                        'use_cron',
                                        __('Use WP Cron', 'upme'),
                                        '1',
                                        __('If checked, UPME will use WP Cron Feature to update User Search Cache.<br />
                                                When usign this option, make sure <code>DISABLE_WP_CRON</code> is not set to <code>TRUE</code> in <code>wp-config.php</code>', 'upme'),
                                        __('Using WP Cron will update your search cache automatically at regular intervals.', 'upme')
                                );
                                
                                $this->add_plugin_setting(
                                        'checkbox',
                                        'require_search_input',
                                        __('Search Input Mandatory', 'upme'),
                                        '1',
                                        __('If checked, UPME will not show search results when <code>hide_until_search=true</code> is used and no search input is given.', 'upme'),
                                        __('Checking this option is useful if you do not want to show all users when submitting the search with no search criteria.', 'upme')
                                );
                                
                                $this->add_plugin_setting(
                                        'input',
                                        'users_are_called',
                                        __('Users are Called', 'upme'), array(),
                                        __('What users are called in your system. Like Members, Doctors, etc.', 'upme'),
                                        __('This will update the text for the search form and search results. The default is Users.', 'upme')
                                );
                                
                                $this->add_plugin_setting(
                                        'input',
                                        'combined_search_text',
                                        __('Combined Search Text', 'upme'), array(),
                                        __('Name of Text search field which is used to perform combined search.', 'upme'),
                                        __('You may choose custom text for the main combined text search field. The default is Combined Search.', 'upme')
                                );
                                
                                $this->add_plugin_setting(
                                        'input',
                                        'search_button_text',
                                        __('Search Button Text', 'upme'), array(),
                                        __('Text to display on search button.', 'upme'),
                                        __('This is the text of the button to submit the member search form. The default is Filter.', 'upme')
                                );
                                
                                $this->add_plugin_setting(
                                        'input',
                                        'reset_button_text',
                                        __('Reset Button Text', 'upme'), array(),
                                        __('Text to display on reset button.', 'upme'),
                                        __('This is the text of the button to reset the member search form. The default is Reset.', 'upme')
                                );
                                
                                
                            ?>
                            
                            
                            <tr valign="top">
                                <th scope="row"><label>&nbsp;</label></th>
                                <td>
                                    <?php 
                                        echo UPME_Html::button('button', array('name'=>'save-upme-search-settings-tab', 'id'=>'save-upme-search-settings-tab', 'value'=>'Save Changes', 'class'=>'button button-primary upme-save-options'));
                                        echo '&nbsp;&nbsp;';
                                        echo UPME_Html::button('button', array('name'=>'reset-upme-search-settings-tab', 'id'=>'reset-upme-search-settings-tab', 'value'=>__('Reset Options','upme'), 'class'=>'button button-secondary upme-reset-options'));
                                    ?>
                                </td>
                            </tr>
                        </table>
                    </form>
                </div>
                <div class="upme-tab-content" id="upme-privacy-option-content" style="display:none;">
                    <h3><?php _e('Privacy Options','upme');?></h3>
                    <form id="upme-privacy-option-form">
                        <table class="form-table">
                            <?php 
							    $this->add_plugin_setting(
                                        'select',
                                        'guests_can_view',
                                        __('Guest viewing of profiles', 'upme'),
                                        array(
                                                1 => __('Enabled, make profiles publicly visible', 'upme'),
                                                0 => __('Disabled, users must login to view profiles', 'upme')),
                                        __('Enable or disable guest and non-logged in user viewing of profiles.', 'upme'),
                                        __('If enabled, profiles will be publicly visible to non-logged in users. If disabled, guests must log in to view profiles.', 'upme')
                                );
								
                                $this->add_plugin_setting(
                                        'select',
                                        'users_can_view',
                                        __('Logged-in user viewing of other profiles', 'upme'),
                                        array(
                                                1 => __('Enabled, logged-in users may view other user profiles', 'upme'),
                                                0 => __('Disabled, users may only view their own profile', 'upme'),
                                                2 => __('Restricted by User Role', 'upme')),
                                        __('Enable or disable logged-in user viewing of other user profiles. Admin users can always view all profiles.', 'upme'),
                                        __('If enabled, logged-in users are allowed to view other user profiles. If disabled, logged-in users may only view theor own profile.', 'upme')
                                );

                                global $upme_roles;                        

                                $this->add_plugin_setting(
                                        'checkbox_list',
                                        'choose_roles_for_view_profile',
                                        __('Select User Roles', 'upme'),
                                        $upme_roles->upme_available_user_roles_view_profile(),
                                        __('Selected user roles can view other user profiles. Roles not selected here will only be able to view their own profile.', 'upme'),
                                        __('User roles selected in this section will have permission to view other profiles.', 'upme'),
                                        '',
                                        array('disabled' => array('administrator'))
                                );

                            ?>
                            
                            <tr valign="top">
                                <th scope="row"><label>&nbsp;</label></th>
                                <td>
                                    <?php 
                                        echo UPME_Html::button('button', array('name'=>'save-upme-privacy-option-tab', 'id'=>'save-upme-privacy-option-tab', 'value'=>'Save Changes', 'class'=>'button button-primary upme-save-options'));
                                        echo '&nbsp;&nbsp;';
                                        echo UPME_Html::button('button', array('name'=>'reset-upme-privacy-option-tab', 'id'=>'reset-upme-privacy-option-tab', 'value'=>__('Reset Options','upme'), 'class'=>'button button-secondary upme-reset-options'));
                                    ?>
                                </td>
                            </tr>
                            
                            
                        </table>
                    </form>
                </div>
                <div class="upme-tab-content" id="upme-misc-messages-content" style="display:none;">
                    <h3><?php _e('Messages for Insuficient Permissions','upme');?></h3>
                    <form id="upme-misc-messages-form">
                        <table class="form-table">
                            <?php 
                                $this->add_plugin_setting(
                                        'textarea',
                                        'html_login_to_view',
                                        __('Guests cannot view profiles', 'upme'),
                                        null,
                                        __('Show a text/HTML message when guests try to view profiles if they are not allowed, asking them to login or register to view the profile.', 'upme'),
                                        __('This message will eb shown to guests who try to view profiles if it is not allowed in the above settings.', 'upme')
                                );

                                $display_login_msg = __('Display login form below this message','upme');
                                $this->add_plugin_setting(
                                        'checkbox',
                                        'html_login_to_view_form',
                                        '',
                                        '1',
                                        '',
                                        '',
                                        '',
                                        array('checkbox_type'=>'inline', 'message' => $display_login_msg)
                                );
                                
                                $this->add_plugin_setting(
                                        'textarea',
                                        'html_user_login_message',
                                        __('User must log-in to view/edit his profile', 'upme'),
                                        null,
                                        __('Show a text/HTML message asking the user to login to view or edit his own profile. Leave blank to show nothing.', 'upme'),
                                        __('This message is shown to users who try to view/edit their own profile but are not logged in.', 'upme')
                                );

                                $this->add_plugin_setting(
                                        'checkbox',
                                        'html_user_login_message_form',
                                        '',
                                        '1',
                                        '',
                                        '',
                                        '',
                                        array('checkbox_type'=>'inline', 'message' => $display_login_msg)
                                );
                                
                                $this->add_plugin_setting(
                                        'textarea',
                                        'html_private_content',
                                        __('User must log-in to view private content', 'upme'),
                                        null,
                                        __('Show a text/HTML message to guests and non-logged in users who try to view private member-only content. Leave blank to show nothing.', 'upme'),
                                        __('This message is shown to guests and non-logged in users who try to view private member-only content.', 'upme')
                                );
                                

                                $this->add_plugin_setting(
                                        'checkbox',
                                        'html_private_content_form',
                                        '',
                                        '1',
                                        '',
                                        '',
                                        '',
                                        array('checkbox_type'=>'inline', 'message' => $display_login_msg)
                                );

                                $this->add_plugin_setting(
                                        'textarea',
                                        'html_members_private_content',
                                        __('User must have permission to view private content', 'upme'),
                                        null,
                                        __('Show a text/HTML message to logged in users who try to view private content restricted for current user. Leave blank to show nothing.', 'upme'),
                                        __('This message is shown to logged in users who try to view private content restricted for current user.', 'upme')
                                );
								
                                $this->add_plugin_setting(
                                        'textarea',
                                        'html_other_profiles_restricted',
                                        __('User Role may not view other profiles', 'upme'),
                                        null,
                                        __('Show a text/HTML message in place of profiles when a User Role is not allowed to view other profiles.', 'upme'),
                                        __('This message is shown to users who try to view the UPME Profile List while user role is blocked for viewing other profiles.', 'upme')
                                );
                                
                                $this->add_plugin_setting(
                                        'textarea',
                                        'html_registration_disabled',
                                        __('Registration Closed Message', 'upme'),
                                        null,
                                        __('Show a text/HTML message in place of the registration form when registration is closed. Registeration can be opened or closed from the WordPress general settings using the checkbox <code>Anyone can register</code>.', 'upme'),
                                        __('This message is shown to users who try to view the UPME registration form while you have registrations disabled.', 'upme')
                                );

                                $this->add_plugin_setting(
                                        'textarea',
                                        'html_profile_status_msg',
                                        __('Profile Status Message', 'upme'),
                                        null,
                                        __('Show a text/HTML message in place of the profile when profile is set to INACTIVE by user.', 'upme'),
                                        __('This message is shown to users who try to view profiles set to INACTIVE by users.', 'upme')
                                );

                                $this->add_plugin_setting(
                                        'textarea',
                                        'html_profile_approval_pending_msg',
                                        __('Profile Pending Approval Message', 'upme'),
                                        null,
                                        __('Show a text/HTML message on top of login form when user profile is pending approval of admin.', 'upme'),
                                        __('This message is shown to users who try to login when user profile is pending approval of admin.', 'upme')
                                );

                                $this->add_plugin_setting(
                                        'textarea',
                                        'html_terms_and_conditions',
                                        __('Terms and Conditions Message', 'upme'),
                                        null,
                                        __('Show a text/HTML message as the Terms and Conditions for user registration.', 'upme'),
                                        __('This message is shown to users in registration forms as terms and conditions for registration.', 'upme')
                                );

                            ?>
                            <tr valign="top">
                                <th scope="row"><label>&nbsp;</label></th>
                                <td>
                                    <?php 
                                        echo UPME_Html::button('button', array('name'=>'save-upme-misc-messages-tab', 'id'=>'save-upme-misc-messages-tab', 'value'=>'Save Changes', 'class'=>'button button-primary upme-save-options'));
                                        echo '&nbsp;&nbsp;';
                                        echo UPME_Html::button('button', array('name'=>'reset-upme-misc-messages-tab', 'id'=>'reset-upme-misc-messages-tab', 'value'=>__('Reset Options','upme'), 'class'=>'button button-secondary upme-reset-options'));
                                        
                                    ?>
                                </td>
                            </tr>
                        </table>
                    </form>
                </div>
                <div class="upme-tab-content" id="upme-scripts-styles-content" style="display:none;">
                    <h3><?php _e('Loading Scripts and Styles','upme');?></h3>
                    <form id="upme-scripts-styles-form">
                        <table class="form-table">
                            <?php 
                                $this->add_plugin_setting(
                                        'checkbox',
                                        'disable_fancybox_script_styles',
                                        __('Disable Fancybox Scripts and Styles', 'upme'),
                                        '0',
                                        __('If checked, UPME will disable the loading of script and style files for Fancybox library.', 'upme'),
                                        __('Use it when you have newer Fancybox version in your theme or other plugins.', 'upme')
                                );

                                $this->add_plugin_setting(
                                        'checkbox',
                                        'disable_tipsy_script_styles',
                                        __('Disable Tipsy Scripts and Styles', 'upme'),
                                        '0',
                                        __('If checked, UPME will disable the loading of script and style files for Tipsy library.', 'upme'),
                                        __('Use it when you have newer Tipsy version in your theme or other plugins.', 'upme')
                                ); 

                                $this->add_plugin_setting(
                                        'checkbox',
                                        'disable_fitvids_script_styles',
                                        __('Disable FitVids Scripts and Styles', 'upme'),
                                        '0',
                                        __('If checked, UPME will disable the loading of script and style files for FitVids library.', 'upme'),
                                        __('Use it when you have newer FitVids version in your theme or other plugins.', 'upme')
                                ); 

                                $this->add_plugin_setting(
                                        'checkbox',
                                        'disable_opensans_google_font',
                                        __('Disable Google Font Files for Open Sans', 'upme'),
                                        '0',
                                        __('If checked, UPME will disable the loading of Open Sans from google fonts.', 'upme'),
                                        __('Use it when you want to avoid requests to google fonts.', 'upme')
                                );


                                
                            ?>
                            <tr valign="top">
                                <th scope="row"><label>&nbsp;</label></th>
                                <td>
                                    <?php 
                                        echo UPME_Html::button('button', array('name'=>'save-upme-scripts-styles-tab', 'id'=>'save-upme-scripts-styles-tab', 'value'=>'Save Changes', 'class'=>'button button-primary upme-save-options'));
                                        echo '&nbsp;&nbsp;';
                                        echo UPME_Html::button('button', array('name'=>'reset-upme-scripts-styles-tab', 'id'=>'reset-upme-scripts-styles-tab', 'value'=>__('Reset Options','upme'), 'class'=>'button button-secondary upme-reset-options'));
                                        
                                    ?>
                                </td>
                            </tr>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
</div>
