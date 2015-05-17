<?php
    global $upme_admin;
    // $site_content_restriction_status_val = $upme_admin->get_value('site_lockdown_status');
    // $site_content_allowed_pages_val = $upme_admin->get_value('site_content_allowed_pages');
    // $site_content_allowed_pages_val = ('0' == $site_content_allowed_pages_val) ? '' : $site_content_allowed_pages_val;
    // $site_content_allowed_posts_val = $upme_admin->get_value('site_content_allowed_posts');
    // $site_content_allowed_posts_val = ('0' == $site_content_allowed_posts_val) ? '' : $site_content_allowed_posts_val;

    // $site_content_allowed_urls_val = $upme_admin->get_value('site_content_allowed_urls');


    $this->add_plugin_module_setting(
            'checkbox',
            'site_lockdown_status',
            'site_lockdown_status',
            __('Lock Entire Site', 'upme'),
            '1',
            __('If checked, access to your entire site will be restricted for guests and non-logged in users.
                You can create exceptions below by allowing posts, pages or URL\'s.', 'upme'),
            __('Checking this option will restrict access to your entire site for guests and non-logged in users .', 'upme')
    );

    $this->add_plugin_module_setting(
            'select',
            'site_lockdown_allowed_pages[]',
            'site_lockdown_allowed_pages',
            __('Allowed Pages', 'upme'),
            $site_content_allowed_pages = $upme_admin->get_all_pages(),
            __('These pages will be acessible to any user without any restrictions.', 'upme'),
            __('Define public pages .', 'upme'),
            array('class'=> 'chosen-admin_setting','multiple'=>'','init_value' => '')
    );

    $this->add_plugin_module_setting(
            'select',
            'site_lockdown_allowed_posts[]',
            'site_lockdown_allowed_posts',
            __('Allowed Posts', 'upme'),
            $site_content_allowed_posts = $upme_admin->get_all_posts(),
            __('These posts will be acessible to any user without any restrictions.', 'upme'),
            __('Define public posts .', 'upme'),
            array('class'=> 'chosen-admin_setting','multiple'=>'','init_value' => '')
    );

    $this->add_plugin_module_setting(
            'textarea',
            'site_lockdown_allowed_urls',
            'site_lockdown_allowed_urls',
            __('Allowed URL\'s', 'upme'),
            '',
            __('These URL\'s will be acessible to any user without any restrictions.', 'upme'),
            __('Define public URL\'s' , 'upme')
    );


    $site_lockdown_redirect_pages = $upme_admin->get_all_pages();
    if(!$this->options['redirect_backend_login']){
        $site_lockdown_redirect_pages['wp-login'] = 'wp-login.php';
    }
    
    $this->add_plugin_module_setting(
            'select',
            'site_lockdown_redirect_url',
            'site_lockdown_redirect_url',
            __('Redirect Page', 'upme'),
            $site_lockdown_redirect_pages,
            __('Guests will be redirected here when they try to access your locked site.The default is UPME Login page which may be
                set in UPME Settings -> System Pages. If you choose another page, be sure it includes <code>[upme_login]</code> shortcode.', 'upme'),
            __('Define redirection URL for violating site lockdown restrictions .', 'upme'),
            array('class'=> 'chosen-admin_setting')
    );

    $this->add_plugin_module_setting(
            'select',
            'site_lockdown_rss_feed',
            'site_lockdown_rss_feed',
            __('RSS Feed', 'upme'),
            array('0'=> __('RSS Enabled','upme'),'1' => __('RSS Disabled','upme'),'2' => __('Limited to Headlines/ Titles','upme')),
            __('RSS feed viewing does not require the user to login.Your RSS feed is publicly acessible if enabled.You may disable or
                limit the RSS feed above to prevent access to locked content through your RSS feed.', 'upme'),
            __('RSS feed viewing does not require the user to login.Your RSS feed is publicly acessible if enabled.You may disable or
                limit the RSS feed above to prevent access to locked content through your RSS feed.', 'upme'),
            array('class'=> 'chosen-admin_setting')
    );
?>
