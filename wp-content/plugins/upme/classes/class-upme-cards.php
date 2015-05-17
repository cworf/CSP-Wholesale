<?php

class UPME_Cards {

    public $upme_options;
    public $upme_card_attributes;
    public $searched_users;

    function __construct() {
        $this->upme_options = get_option('upme_options');
    }
    
    public function upme_scripts_styles_profile_cards(){
        global $upme;
        
        /* Google fonts */
        if ('0' == $upme_settings['disable_opensans_google_font']) {
            wp_register_style('upme_google_fonts', '//fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,400,700&subset=latin,latin-ext');
            wp_enqueue_style('upme_google_fonts');
        }

        /* Font Awesome */
        wp_register_style('upme_font_awesome', upme_url . 'css/font-awesome.min.css');
        wp_enqueue_style('upme_font_awesome');

        /* Main css file */
        wp_register_style('upme_css', upme_url . 'css/upme.css');
        wp_enqueue_style('upme_css');

        /* Add style */
        if ($upme->get_option('style')) {
            wp_register_style('upme_style', upme_url . 'styles/' . $upme->get_option('style') . '.css');
            wp_enqueue_style('upme_style');
        }

        /* Responsive */
        wp_register_style('upme_responsive', upme_url . 'css/upme-responsive.css');
        wp_enqueue_style('upme_responsive');

        do_action('upme_add_style_scripts_frontend');
    }
    
    public function upme_author_profile($args){
        global $upme,$upme_template_loader,$upme_template_args;
        
        $upme_template_args =  array();
        
        /* Arguments */
        $defaults = array(
            'id'                => null,
            'template'          => null,
            'pic_style'         => 'rounded',
            'background_color'  => '#FFF',
            'font_color'        => '#000'
        );
        
        $args = wp_parse_args($args, $defaults);
        
        $this->upme_card_attributes = $args;

        extract($args, EXTR_SKIP);
        
        $this->upme_scripts_styles_profile_cards();
        

        // Show custom field as profile title
        $profile_title_field = $this->upme_options['profile_title_field'];
        // Get value of profile title field or default display name if empty
        
        
        $upme_template_args['id']                   = $id;
        
        $upme_template_args['pic_style']            = 'upme-profile-pic-'. $pic_style;   
        $upme_template_args['background_color']     = $background_color;  
        $upme_template_args['font_color']           = $font_color;  
        $upme_template_args['template']             = $template;
        
        
        $upme_template_args['profile_title_display'] = $upme->upme_profile_title_value($profile_title_field, $id);
        $upme_template_args['profile_url']          = $upme->profile_link($id);
        $upme_template_args['user_pic']             = get_user_meta($id,'user_pic',true);
        $upme_template_args['profile_pic_display']  = '<a href="' . $upme_template_args['profile_url'] . '">' . $upme->pic($id, 50) . '</a>';
        $upme_template_args['description']          = get_user_meta($id,'description',true);
        $upme_template_args['social_buttons']       = $upme->show_user_social_profiles($id, array('tag'=>'ul', 'sub_tag'=>'li'));
        
        ob_start();
        
        switch($template){
            case 'author_design_one':                
                $upme_template_loader->get_template_part('author-card','one');
                $display = ob_get_clean();
                break;
            
            case 'author_design_two':            
                $upme_template_loader->get_template_part('author-card','two');
                $display = ob_get_clean();
                break;
            
            case 'author_design_three':            
                $upme_template_loader->get_template_part('author-card','three');
                $display = ob_get_clean();
                break;
            
            case 'author_design_four':            
                $upme_template_loader->get_template_part('author-card','four');
                $display = ob_get_clean();
                break;
        }
      
        return $display;
    }
    
    public function upme_team_profile($args,$content){
        global $upme, $upme_template_loader, $upme_template_args;
        
        $defaults = array(
            'id' => null,
            'view' => null,
            'group' => null,
            'width' => 1,
            'users_per_page' => 100,
            'orderby' => 'registered',
            'order' => 'desc',
            'orderby_custom' => false,
            'role' => null,
            'group_meta' => null,
            'group_meta_value' => null,
            'display' => '',
            'limit_results' => false,
            'hide_admins' => false,
            
            'id'                => null,
            'template'          => null,
            'pic_style'         => 'rounded',
            'background_color'  => '#FFF',
            'font_color'        => '#000',
            'team_name'         => '',
        );
        
        $args = wp_parse_args($args, $defaults);
        extract($args, EXTR_SKIP);

        $upme_template_args =  array();        
        $this->upme_scripts_styles_profile_cards();
        
        $users = $this->load_searched_member_list($args);

        // Show custom field as profile title
        $profile_title_field = $this->upme_options['profile_title_field'];
        // Get value of profile title field or default display name if empty
        
        $user_details = array();
        foreach($users as $user){
            $id = $user;
            $single_user = array();
            $single_user['id'] = $user;
            $single_user['profile_title_display'] = $upme->upme_profile_title_value($profile_title_field, $id);
            $single_user['profile_url'] = $upme->profile_link($id);
            $single_user['user_pic'] = get_user_meta($id,'user_pic',true);
            $single_user['profile_pic_display'] = '<a href="' . $single_user['profile_url'] . '">' . $upme->pic($id, 50) . '</a>';
            $single_user['description'] = get_user_meta($id,'description',true);;
            $single_user['social_buttons'] = $upme->show_user_social_profiles($id, array('tag'=>'ul', 'sub_tag'=>'li'));
            
            array_push($user_details,$single_user);
        }
        
        
        $upme_template_args['id']                   = $id;        
        $upme_template_args['pic_style']            = 'upme-profile-pic-'. $pic_style;   
        $upme_template_args['background_color']     = $background_color;  
        $upme_template_args['font_color']           = $font_color;  
        $upme_template_args['template']             = $template;        
        $upme_template_args['team_name']            = $team_name;
        $upme_template_args['team_description']     = trim($content);
        $upme_template_args['users']                = $user_details;
        //echo "<pre>";print_r($upme_template_args);exit;
        ob_start();
        
        switch($template){
            case 'team_design_one':                
                $upme_template_loader->get_template_part('team-card','one');
                $display = ob_get_clean();
                break;
            
            case 'team_design_two':            
                $upme_template_loader->get_template_part('team-card','two');
                $display = ob_get_clean();
                break;
            
            case 'team_design_three':            
                $upme_template_loader->get_template_part('team-card','three');
                $display = ob_get_clean();
                break;
            
            case 'team_design_four':            
                $upme_template_loader->get_template_part('team-card','four');
                $display = ob_get_clean();
                break;
            
            case 'team_design_five':            
                $upme_template_loader->get_template_part('team-card','five');
                $display = ob_get_clean();
                break;
            
            case 'team_design_six':            
                $upme_template_loader->get_template_part('team-card','six');
                $display = ob_get_clean();
                break;
            
            case 'team_design_sevan':            
                $upme_template_loader->get_template_part('team-card','sevan');
                $display = ob_get_clean();
                break;
        }
      
        return $display;
    }
    
    public function upme_slider_profiles($args){
        global $upme, $upme_template_loader, $upme_template_args;
        
        $defaults = array(
            'id' => null,
            'view' => null,
            'group' => null,
            'width' => 1,
            'users_per_page' => 100,
            'orderby' => 'registered',
            'order' => 'desc',
            'orderby_custom' => false,
            'role' => null,
            'group_meta' => null,
            'group_meta_value' => null,
            'display' => '',
            'limit_results' => false,
            'hide_admins' => false,
            
            'id'                => null,
            'template'          => null,
            'pic_style'         => 'rounded',
            'background_color'  => '#FFF',
            'font_color'        => '#000',
            'slider'            => 'flexSlider'
        );
        
        $args = wp_parse_args($args, $defaults);
        extract($args, EXTR_SKIP);

        $upme_template_args =  array();        
        $this->upme_scripts_styles_profile_cards();
        
        $users = $this->load_searched_member_list($args);

        // Show custom field as profile title
        $profile_title_field = $this->upme_options['profile_title_field'];
        // Get value of profile title field or default display name if empty
        
        $user_details = array();
        foreach($users as $user){
            $id = $user;
            $single_user = array();
            $single_user['id'] = $user;
            $single_user['profile_title_display'] = $upme->upme_profile_title_value($profile_title_field, $id);
            $single_user['profile_url'] = $upme->profile_link($id);
            $single_user['user_pic'] = get_user_meta($id,'user_pic',true);
            if($single_user['user_pic'] == ''){
                $single_user['user_pic'] = upme_get_gravatar_url(get_user_meta($id,'user_email',true));
            }
            
            $single_user['profile_pic_display'] = '<a href="' . $single_user['profile_url'] . '">' . $upme->pic($id, 100) . '</a>';
            $single_user['description'] = get_user_meta($id,'description',true);;
            $single_user['social_buttons'] = $upme->show_user_social_profiles($id, array('tag'=>'ul', 'sub_tag'=>'li'));
            
            array_push($user_details,$single_user);
        }
        
        
        $upme_template_args['id']                   = $id;        
        $upme_template_args['pic_style']            = 'upme-profile-pic-'. $pic_style;   
        $upme_template_args['background_color']     = $background_color;  
        $upme_template_args['font_color']           = $font_color;  
        $upme_template_args['template']             = $template;        
        $upme_template_args['users']                = $user_details;
        
        //echo "<pre>";print_r($upme_template_args);exit;
        ob_start();
        
        $template_parts     = explode('_',$template);
        $template_part_main = $template_parts[0] . '-card';
        $template_part_sub  = isset($template_parts[2]) ? $template_parts[2] : '';
        
        wp_register_script('upme_sliders', upme_url . 'integrated_plugins/sliders/upme-sliders.js', array('jquery'));
        wp_enqueue_script('upme_sliders');
        
        switch($slider){
            case 'flexSlider':
                $upme_template_args['slider'] = 'flexSlider';
            
                wp_register_style('upme_flex_slider_style', upme_url . 'integrated_plugins/sliders/woothemes-flexSlider/flexslider.css');
                wp_enqueue_style('upme_flex_slider_style');
            
                wp_register_script('upme_flex_slider', upme_url . 'integrated_plugins/sliders/woothemes-flexSlider/jquery.flexslider-min.js', array('jquery'));
                wp_enqueue_script('upme_flex_slider');
            
                $upme_template_loader->get_template_part($template_part_main,$template_part_sub);
                break;
            
            default:
                break;
        }
        
        
        $display = ob_get_clean();
        
        return $display;
    }
    
    public function load_searched_member_list($args){
        global $upme;
        
        extract($args, EXTR_SKIP);
        
        $upme->upme_args                        = $args;
        $upme->profile_orderby_custom_status    = $orderby_custom;
        $upme->profile_order_field              = $orderby;
        $upme->profile_order = 'asc';
        if (strtolower($order) == 'asc' || strtolower($order) == 'desc')
            $upme->profile_order = $order;
        
        $upme->profile_role = $role;
        $upme->hide_admin_role = $hide_admins;

        $this->upme_card_attributes = $args;
        
        $search_args = array('per_page' => $users_per_page , 'orderby' => $orderby, 'order' => $order);
        
        unset($upme->searched_users);
        
        if ($users_per_page) {
            $search_args = $upme->setup_page($search_args, $users_per_page);
        }
        
        $users = array();
        /* Ignore id if group is used */
        if($group){
            if ($group != 'all') {
                $users = explode(',', $group);
            }else{
                if (!isset($upme->searched_users)) {
                    $upme->search_result($search_args);
                }

                foreach ($upme->searched_users as $user) {
                    $users[] = $user->ID;
                }
            }
        }
        else if($id){
            $users[] = $id;
        }
        else {
            $current_user = wp_get_current_user();
            if (($current_user instanceof WP_User)) {
                $users[] = $current_user->ID;
            }else{
                $users = array();
            }
            
        }
        
        return $users;
    }
    
    
    
}

$upme_cards = new UPME_Cards();


/* Shortcodes for author card templates */
add_shortcode('upme_author_card', 'upme_author_card');
add_shortcode('upme_team_card', 'upme_team_card');
add_shortcode('upme_slider_card', 'upme_slider_card');

function upme_author_card($atts) {
    global $upme_cards;
    return $upme_cards->upme_author_profile($atts);
}

function upme_team_card($atts,$content) {
    global $upme_cards;
    return $upme_cards->upme_team_profile($atts,$content);
}

function upme_slider_card($atts,$content) {
    global $upme_cards;
    return $upme_cards->upme_slider_profiles($atts);
}