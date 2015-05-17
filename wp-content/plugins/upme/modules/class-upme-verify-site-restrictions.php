<?php


class UPME_Verify_Site_Restrictions{

    public $user_id;
    public $current_option;

    function __construct(){   
        // http://t31os.wordpress.com/2011/02/01/conditional-tags-on-init/     
        add_action('template_redirect', array($this, 'upme_verify_site_lockdown_restrictions'));
        add_filter('the_content',array($this,'upme_restrict_rss_feed' ));

        $this->current_option = get_option('upme_options');
        
        if($this->current_option['site_lockdown_status'] == '0'){
            add_action('template_redirect', array($this, 'upme_verify_site_content_restrictions'));
        }
        
    }

    function upme_verify_site_lockdown_restrictions(){
        global $upme,$pagenow;

        // Return without verifying when restriction rules are disabled
        if($this->current_option['site_lockdown_status'] == '0'){
            return;
        }

        if(is_feed()){
            return;
        }

        if (is_user_logged_in ()) {
            $this->user_id = get_current_user_id();
        }else{
            $this->user_id = 0;
        }

        // Skip restrictions for admin users and return the page
        if(current_user_can('manage_options')){
            return;
        }

        $redirect_page_id = $this->current_option['site_lockdown_redirect_url'];

        // Add globally skipped URL's, pages and posts
        $skipped_urls = array( get_permalink($redirect_page_id) , wp_login_url(), wp_registration_url(), wp_lostpassword_url());
        $skipped_pages = (array) $this->current_option['site_lockdown_allowed_pages'];
        
        foreach ($skipped_pages as $page_id) {
           if($page_id != '0' && $page_id != ''){
                array_push($skipped_urls, get_permalink( $page_id ));
           }
        }

        $skipped_posts = (array) $this->current_option['site_lockdown_allowed_posts'];
        foreach ($skipped_posts as $page_id) {
           if($page_id != '0' && $page_id != ''){
                array_push($skipped_urls, get_permalink( $page_id ));
           }
        }

        $skipped_custom_urls = explode(PHP_EOL, $this->current_option['site_lockdown_allowed_urls']);
        foreach ($skipped_custom_urls as $url) {
            if($url != ''){
                array_push($skipped_urls, $url);
            }
        }

        $current_page_url = upme_current_page_url();

        $parsed_url = parse_url($current_page_url);
        $scheme   = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '';
        $host     = isset($parsed_url['host']) ? $parsed_url['host'] : '';
        $port     = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '';
        $user     = isset($parsed_url['user']) ? $parsed_url['user'] : '';
        $pass     = isset($parsed_url['pass']) ? ':' . $parsed_url['pass']  : '';
        $pass     = ($user || $pass) ? "$pass@" : '';
        $path     = isset($parsed_url['path']) ? $parsed_url['path'] : '';
 
        $current_page_url = $scheme.$user.$pass.$host.$port.$path;

        if(in_array($current_page_url, $skipped_urls)){
            return;
        }else{
            if($this->user_id == 0){
                // Check for URL exceptions in admin area
                
                if('wp-login' == $redirect_page_id){
                    $url = add_query_arg( 'redirect_to', $current_page_url, wp_login_url() );
                    wp_redirect($url);
                    
                }else{
                    $redirect_page_id = $this->current_option['site_lockdown_redirect_url'];
                    $url = add_query_arg( 'redirect_to', $current_page_url, get_permalink($redirect_page_id) );
                    wp_redirect($url);
                }             
                exit;
            }
            
        }        

    }

    function upme_verify_site_content_restrictions(){
        global $upme,$pagenow;


        if (is_user_logged_in ()) {
            $this->user_id = get_current_user_id();
        }else{
            $this->user_id = 0;
        }

        // Skip restrictions for admin users and return the page
        if(current_user_can('manage_options')){
            return;
        }

        // Get Restrictions Rules
        $restriction_rules = (array) get_option('upme_site_restriction_rules'); 
        $redirection_urls = array_map(create_function('$ar', 'return get_permalink($ar["site_content_redirect_url"]);'), $restriction_rules);
    
        // Add globally skipped URL's, pages and posts
        $skipped_urls = array( wp_login_url(), wp_registration_url(), wp_lostpassword_url());
        // All redirection URL's defined in restriction rules are allowed by
        // default to prevent any confusion while redirecting
        $skipped_urls =  array_merge($skipped_urls,$redirection_urls);
       
        
        $current_page_url = upme_current_page_url();

        $parsed_url = parse_url($current_page_url);
        $scheme   = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '';
        $host     = isset($parsed_url['host']) ? $parsed_url['host'] : '';
        $port     = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '';
        $user     = isset($parsed_url['user']) ? $parsed_url['user'] : '';
        $pass     = isset($parsed_url['pass']) ? ':' . $parsed_url['pass']  : '';
        $pass     = ($user || $pass) ? "$pass@" : '';
        $path     = isset($parsed_url['path']) ? $parsed_url['path'] : '';
 
        $current_page_url = $scheme.$user.$pass.$host.$port.$path;

        if(in_array($current_page_url, $skipped_urls)){
            return;
        }else if($pagenow == 'wp-login.php'){
            return;
        }

        if(!is_admin()){                       

            if($restriction_rules == '' || count($restriction_rules) == 0){
                // 
            }else{
                if(is_array($restriction_rules)){                    

                    foreach ($restriction_rules as $key => $rule) {

                        if($rule['site_restriction_rule_status'] == '1'){
                            $display_status = true;
     
                            // FALSE == $display_status means content is blocked
                            $display_status = $this->upme_process_restriction_rules($rule);

                            $url_comp = parse_url($rule['site_content_redirect_url']);
                            if(!$display_status){

                                if($url_comp['path'] != $_SERVER["REQUEST_URI"]){
                                    $url = add_query_arg( 'redirect_to', $current_page_url, get_permalink($rule['site_content_redirect_url']) );
                                    wp_redirect($url);                                    
                                    exit;
                                }else{
                                    //echo $url_comp['path'].$_SERVER["REQUEST_URI"];exit;
                                    return;
                                }                              
                            } 
                        }
                          
                    }       
                }else{
                    //echo "No Restriction Rules";
                }
                
            }

        }

    }

    function upme_process_restriction_rules($rule){

        switch ($rule['site_content_user_restrictions']) {
            case 'by_all_users':
                return $this->upme_process_allow_part_loggedin_users($rule);
                break;
            
            case 'by_user_roles':
                return $this->upme_process_allow_part_user_roles($rule);
                break;
        }
    }

    function upme_process_allow_part_loggedin_users($rule){


        $content_status = $this->upme_process_content_locks($rule);

        if($content_status){
            return true;       
            
        }else{
            if($this->user_id != 0){
                return true;
            }else{
                return false;
            }
        }
    }

    function upme_process_allow_part_user_roles($rule){
        global $upme_roles;

        // Role status becomes true when role is in the restricted list
        $role_status = false;
      
        $current_user_roles = (array) $upme_roles->upme_get_user_roles_by_id($this->user_id);
        foreach ($current_user_roles as $role) {

            if(in_array($role, $rule['site_content_allowed_roles'])){
                $role_status = true;
            }
        }

        $content_status =  $this->upme_process_content_locks($rule);

        if($content_status){
            return true;       
            
        }else{
            if($this->user_id != 0){
                if($role_status){
                    return true;    
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }

    }

    function upme_process_content_locks($rule){
        switch ($rule['site_content_section_restrictions']) {
            case 'all_pages':
                return $this->upme_process_all_pages_restriction($rule);
                break;
            
            case 'all_posts':
                return $this->upme_process_all_posts_restriction($rule);
                break;

            case 'restrict_selected_pages':

                return $this->upme_process_specific_pages_restriction($rule);
                break;

            case 'restrict_selected_posts':

                return $this->upme_process_specific_posts_restriction($rule);
                break;

        }
    }

    function upme_process_all_pages_restriction($rule){

        if(is_page()){
            return false;
        }else{
            return true;
        }
    }

    function upme_process_all_posts_restriction($rule){

        if(is_single()){         
            return false;
        }else{
            return true;
        }
    }

    function upme_process_specific_pages_restriction($rule){
        global $post;

        $restricted_pages = (array) $rule['site_content_page_restrictions'];

        if(is_page() && in_array($post->ID, $restricted_pages)){
            return false;
        }else{
            return true;
        }
    }

    function upme_process_specific_posts_restriction($rule){
        global $post;

        $restricted_posts = (array) $rule['site_content_post_restrictions'];

        if(is_single() && in_array($post->ID, $restricted_posts)){
            return false;
        }else{
            return true;
        }
    }

    // Enable/Disable RSS feed based on setting setup in Site Lockdown section
    function upme_restrict_rss_feed($content){
        
        $current_page_url = upme_current_page_url();

        $parsed_url = parse_url($current_page_url);
        $scheme   = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '';
        $host     = isset($parsed_url['host']) ? $parsed_url['host'] : '';
        $port     = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '';
        $user     = isset($parsed_url['user']) ? $parsed_url['user'] : '';
        $pass     = isset($parsed_url['pass']) ? ':' . $parsed_url['pass']  : '';
        $pass     = ($user || $pass) ? "$pass@" : '';
        $path     = isset($parsed_url['path']) ? $parsed_url['path'] : '';
 
        $current_page_url = $scheme.$user.$pass.$host.$port.$path;

        if ( is_feed() ) {
            $login_page_url = get_permalink($this->current_option['login_page_id']);

            $rss_status = $this->current_option['site_lockdown_rss_feed'];
            switch ($rss_status) {
                case "0":
                    // Enable RSS Feed
                    break;
                case "1":
                    // Enable RSS Feed only for logged in users
                    if(!is_user_logged_in()){
                        
                        $url = add_query_arg( 'redirect_to', $current_page_url, $login_page_url );
                        wp_redirect($url);
                        exit;
                    }
                    break;
                case "2":
                    // Enable RSS Feed without content for logged in users
                    if(is_user_logged_in()){
                        $content = '';
                    }else{
                        
                        $url = add_query_arg( 'redirect_to', $current_page_url, $login_page_url );
                        wp_redirect($url); 
                        exit;
                    }
                    
                    break;
            }
        }
        
        return $content;
    }

}

$upme_verify_site_restrictions = new UPME_Verify_Site_Restrictions();