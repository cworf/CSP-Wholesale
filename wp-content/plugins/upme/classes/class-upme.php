<?php

class UPME {

    public $upme_options;

    function __construct() {

        $this->logged_in_user = 0;
        $this->allow_search = false;
        $this->login_code_count = 0;

        $this->include_for_validation = array('text', 'textarea', 'select', 'radio', 'checkbox', 'password', 'datetime','video','soundcloud');
        
        /* UPME Filter for defining custom field types for validation */
        $include_validation_field_types_params = array();
        $this->include_for_validation = apply_filters('upme_include_validation_field_types',$this->include_for_validation, $include_validation_field_types_params);                   
        // End filter

        $this->yes_true_array = array('yes', 'true');
        $this->method_mapping = array(
            'text' => 'text_box',
            'fileupload' => '',
            'textarea' => 'text_box',
            'select' => 'drop_down',
            'radio' => 'drop_down',
            'checkbox' => 'drop_down',
            'password' => '',
            'datetime' => 'text_box'
        );


        add_action('wp_enqueue_scripts', array(&$this, 'add_style_scripts_frontend'), 9);

        /* Allowed input types */
        $this->allowed_inputs = array(
            'text' => __('Text', 'upme'),
            'fileupload' => __('Image Upload', 'upme'),
            'textarea' => __('Textarea', 'upme'),
            'select' => __('Select Dropdown', 'upme'),
            'radio' => __('Radio', 'upme'),
            'checkbox' => __('Checkbox', 'upme'),
            'password' => __('Password', 'upme'),
            'datetime' => __('Date Picker', 'upme'),
            'video' => __('Video', 'upme'),
            'soundcloud' => __('Soundcloud', 'upme'),
        );

        /* UPME Filter for defining custom field types */
        $allowed_input_field_types_params = array();
        $this->allowed_inputs = apply_filters('upme_allowed_input_field_types',$this->allowed_inputs, $allowed_input_field_types_params);                   
        // End filter

        /* Core registration fields */
        $set_pass = $this->get_option('set_password');
        if ($set_pass) {
            $this->registration_fields = array(
                50 => array(
                    'icon' => 'user',
                    'field' => 'text',
                    'type' => 'usermeta',
                    'meta' => 'user_login',
                    'name' => __('Username', 'upme'),
                    'required' => 1,
                    'help_text' => ''
                ),
                100 => array(
                    'icon' => 'envelope',
                    'field' => 'text',
                    'type' => 'usermeta',
                    'meta' => 'user_email',
                    'name' => __('E-mail', 'upme'),
                    'required' => 1,
                    'can_hide' => 1,
                    'help_text' => ''
                ),
                150 => array(
                    'icon' => 'lock',
                    'field' => 'password',
                    'type' => 'usermeta',
                    'meta' => 'user_pass',
                    'name' => __('Password', 'upme'),
                    'required' => 1,
                    'can_hide' => 0,
                    'help_text' => apply_filters('upme_password_help_text',__('Password must be at least 7 characters long. To make it stronger, use upper and lower case letters, numbers and symbols.', 'upme')),
                ),
                200 => array(
                    'icon' => 0,
                    'field' => 'password',
                    'type' => 'usermeta',
                    'meta' => 'user_pass_confirm',
                    'name' => __('Confirm Password', 'upme'),
                    'required' => 1,
                    'can_hide' => 0,
                    'help_text' => __('Type your password again.', 'upme')
                ),
                250 => array(
                    'icon' => 0,
                    'field' => 'password_indicator',
                    'type' => 'usermeta',
                    'help_text' => ''
                ),
                300 => array(
                    'icon' => 'user-md',
                    'field' => 'select',
                    'type' => 'usermeta',
                    'meta' => 'user_role',
                    'name' => $this->get_option('label_for_registration_user_role'),
                    'required' => 1,
                    'help_text' => '',
                )
            );
        } else {
            $this->registration_fields = array(
                50 => array(
                    'icon' => 'user',
                    'field' => 'text',
                    'type' => 'usermeta',
                    'meta' => 'user_login',
                    'name' => __('Username', 'upme'),
                    'required' => 1,
                    'help_text' => ''
                ),
                100 => array(
                    'icon' => 'envelope',
                    'field' => 'text',
                    'type' => 'usermeta',
                    'meta' => 'user_email',
                    'name' => __('E-mail', 'upme'),
                    'required' => 1,
                    'can_hide' => 1,
                    'help_text' => __('A password will be e-mailed to you.', 'upme')
                ),
                150 => array(
                    'icon' => 'user-md',
                    'field' => 'select',
                    'type' => 'usermeta',
                    'meta' => 'user_role',
                    'name' => $this->get_option('label_for_registration_user_role'),
                    'required' => 1,
                    'help_text' => '',
                )
            );
        }

        /* Core login fields */
        $this->login_fields = array(
            50 => array(
                'icon' => 'user',
                'field' => 'text',
                'type' => 'usermeta',
                'meta' => 'user_login',
                'name' => __('Username or Email', 'upme'),
                'required' => 1,
                'help_text' => ''
            ),
            100 => array(
                'icon' => 'lock',
                'field' => 'password',
                'type' => 'usermeta',
                'meta' => 'login_user_pass',
                'name' => __('Password', 'upme'),
                'required' => 1,
                'help_text' => ''
            )
        );

        /* Setup profile fields */
        $this->fields = array(
            50 => array(
                'position' => '50',
                'type' => 'separator',
                'meta' => 'profile_info_separator',
                'name' => __('Profile Info', 'upme'),
                'private' => 0,
                'deleted' => 0,
                'show_to_user_role' => 0
            ),
            60 => array(
                'position' => '60',
                'icon' => 'camera',
                'field' => 'fileupload',
                'type' => 'usermeta',
                'meta' => 'user_pic',
                'name' => __('Profile Picture', 'upme'),
                'can_hide' => 0,
                'can_edit' => 1,
                'private' => 0,
                'social' => 0,
                'deleted' => 0,
                'show_to_user_role' => 0,
                'edit_by_user_role' => 0,
                'help_text' => ''
            ),
            100 => array(
                'position' => '100',
                'icon' => 'user',
                'field' => 'text',
                'type' => 'usermeta',
                'meta' => 'first_name',
                'name' => __('First Name', 'upme'),
                'can_hide' => 1,
                'can_edit' => 1,
                'private' => 0,
                'social' => 0,
                'deleted' => 0,
                'show_to_user_role' => 0,
                'edit_by_user_role' => 0,
                'help_text' => ''
            ),
            101 => array(
                'position' => '101',
                'icon' => 0,
                'field' => 'text',
                'type' => 'usermeta',
                'meta' => 'last_name',
                'name' => __('Last Name', 'upme'),
                'can_hide' => 1,
                'can_edit' => 1,
                'private' => 0,
                'social' => 0,
                'deleted' => 0,
                'show_to_user_role' => 0,
                'edit_by_user_role' => 0,
                'help_text' => ''
            ),
            150 => array(
                'position' => '150',
                'icon' => 'user',
                'field' => 'text',
                'type' => 'usermeta',
                'meta' => 'display_name',
                'name' => __('Display Name', 'upme'),
                'can_hide' => 0,
                'can_edit' => 1,
                'private' => 0,
                'social' => 0,
                'deleted' => 0,
                'show_to_user_role' => 0,
                'edit_by_user_role' => 0,
                'help_text' => ''
            ),
            200 => array(
                'position' => '200',
                'icon' => 'pencil',
                'field' => 'textarea',
                'type' => 'usermeta',
                'meta' => 'description',
                'name' => __('About / Bio', 'upme'),
                'can_hide' => 1,
                'can_edit' => 1,
                'private' => 0,
                'social' => 0,
                'deleted' => 0,
                'allow_html' => 1,
                'show_to_user_role' => 0,
                'edit_by_user_role' => 0,
                'help_text' => ''
            ),
            210 => array(
                'position' => '210',
                'icon' => 'picture',
                'field' => 'fileupload',
                'type' => 'usermeta',
                'meta' => 'custom_pic',
                'name' => __('Custom Photo 1', 'upme'),
                'can_hide' => 0,
                'can_edit' => 1,
                'private' => 0,
                'social' => 0,
                'deleted' => 0,
                'show_to_user_role' => 0,
                'edit_by_user_role' => 0,
                'help_text' => ''
            ),
            250 => array(
                'position' => '250',
                'type' => 'separator',
                'meta' => 'contact_info_separator',
                'name' => __('Contact Info', 'upme'),
                'private' => 0,
                'deleted' => 0,
                'show_to_user_role' => 0
            ),
            300 => array(
                'position' => '300',
                'icon' => 'envelope',
                'field' => 'text',
                'type' => 'usermeta',
                'meta' => 'user_email',
                'name' => __('Email', 'upme'),
                'can_hide' => 1,
                'can_edit' => 1,
                'private' => 0,
                'social' => 1,
                'tooltip' => __('Send E-mail', 'upme'),
                'deleted' => 0,
                'show_to_user_role' => 0,
                'edit_by_user_role' => 0,
                'help_text' => ''
            ),
            400 => array(
                'position' => '400',
                'icon' => 'link',
                'field' => 'text',
                'type' => 'usermeta',
                'meta' => 'user_url',
                'name' => __('Website', 'upme'),
                'can_hide' => 1,
                'can_edit' => 1,
                'private' => 0,
                'social' => 0,
                'deleted' => 0,
                'show_to_user_role' => 0,
                'edit_by_user_role' => 0,
                'help_text' => ''
            ),
            450 => array(
                'position' => '450',
                'type' => 'separator',
                'meta' => 'social_profiles_separator',
                'name' => __('Social Profiles', 'upme'),
                'private' => 0,
                'deleted' => 0,
                'show_to_user_role' => 0
            ),
            500 => array(
                'position' => '500',
                'icon' => 'facebook',
                'field' => 'text',
                'type' => 'usermeta',
                'meta' => 'facebook',
                'name' => __('Facebook', 'upme'),
                'can_hide' => 1,
                'can_edit' => 1,
                'private' => 0,
                'social' => 1,
                'tooltip' => __('Connect via Facebook', 'upme'),
                'deleted' => 0,
                'show_to_user_role' => 0,
                'edit_by_user_role' => 0,
                'help_text' => ''
            ),
            510 => array(
                'position' => '510',
                'icon' => 'twitter',
                'field' => 'text',
                'type' => 'usermeta',
                'meta' => 'twitter',
                'name' => __('Twitter Username', 'upme'),
                'can_hide' => 1,
                'can_edit' => 1,
                'private' => 0,
                'social' => 1,
                'tooltip' => __('Connect via Twitter', 'upme'),
                'deleted' => 0,
                'show_to_user_role' => 0,
                'edit_by_user_role' => 0,
                'help_text' => ''
            ),
            520 => array(
                'position' => '520',
                'icon' => 'google-plus',
                'field' => 'text',
                'type' => 'usermeta',
                'meta' => 'googleplus',
                'name' => __('Google+', 'upme'),
                'can_hide' => 1,
                'can_edit' => 1,
                'private' => 0,
                'social' => 1,
                'tooltip' => __('Connect via Google+', 'upme'),
                'deleted' => 0,
                'show_to_user_role' => 0,
                'edit_by_user_role' => 0,
                'help_text' => ''
            ),
            550 => array(
                'position' => '550',
                'type' => 'separator',
                'meta' => 'account_info_separator',
                'name' => __('Account Info', 'upme'),
                'private' => 0,
                'deleted' => 0,
                'show_to_user_role' => 0
            ),
            600 => array(
                'position' => '600',
                'icon' => 'lock',
                'field' => 'password',
                'type' => 'usermeta',
                'meta' => 'user_pass',
                'name' => __('New Password', 'upme'),
                'can_hide' => 0,
                'can_edit' => 1,
                'private' => 1,
                'social' => 0,
                'deleted' => 0,
                'help_text' => ''
            ),
            700 => array(
                'position' => '700',
                'icon' => 0,
                'field' => 'password',
                'type' => 'usermeta',
                'meta' => 'user_pass_confirm',
                'name' => 0,
                'can_hide' => 0,
                'can_edit' => 1,
                'private' => 1,
                'social' => 0,
                'deleted' => 0,
                'help_text' => ''
            )
        );

        /* Store default profile fields */
        if (!get_option('upme_profile_fields')) {
            update_option('upme_profile_fields', $this->fields);
        }

        /* Create a generic profile page */
        add_action('wp_loaded', array(&$this, 'create_profile_page'), 9);

        /* Setup redirection */
        add_action('wp_loaded', array(&$this, 'upme_redirect'), 9);

        /* Setup global vars */
        add_action('wp_loaded', array(&$this, 'upme_globals'), 9);

        /* Should we override "default avatar" */
        add_filter('get_avatar', array(&$this, 'upme_replace_avatar'), 10, 5);

        /* Current page of users */
        if (!isset($_REQUEST['userspage']) || $_REQUEST['userspage'] == 0 || $_REQUEST['userspage'] == '') {
            $this->current_users_page = 1;
        } else {
            $this->current_users_page = $_REQUEST['userspage'];
        }

        add_action('init', array($this, 'upme_hide_admin_bar'));

        add_action('init', array($this, 'upme_profile_custom_routes'));

        add_filter('query_vars', array($this, 'upme_profile_custom_routes_query_vars'));


        $custom_file_field_types_params = array();
        $this->custom_file_field_types = apply_filters('upme_custom_file_field_types',array(), $custom_file_field_types_params );

        // Link post authors to UPME profiles
        add_filter( 'author_link', array($this,'upme_author_link'),10,2 );
        // Display UPME compact profile after the post
        add_filter( 'the_content', array($this,'upme_profile_after_post') );
        
        $this->upme_options = get_option('upme_options');
    }

    /* Replace avatar */

    function upme_replace_avatar($avatar, $id_or_email, $size, $default, $alt) {
        // Optimized condition and added strict conditions
        if (is_numeric($id_or_email)) {
            $user_id = $id_or_email;
        } else if (is_object($id_or_email)) {
            $user_id = email_exists($id_or_email->comment_author_email);
        } else {
            $user_id = email_exists($id_or_email);
        }


        // Filter default gravatars to prvent the loading of custom profile image
        $default_gravatars = array("blank", "identicon", "wavatar", "monsterid", "retro");

        if ($user_id > 0 && !in_array($default, $default_gravatars)) {
            if (get_the_author_meta('user_pic', $user_id) != '') {
                $avatar = '<img src="' . get_the_author_meta('user_pic', $user_id) . '" alt="" width="' . $size . '" height="' . $size . '" class="avatar avatar-' . $size . ' photo">';
            }
        }

        /* UPME Filter for customizing avatar image on profiles */
        $avatar = apply_filters('upme_replace_avatar', $avatar, $user_id);
        // End Filter

        return $avatar;
    }

    /* Globals */

    function upme_globals() {
        $this->current_page = $_SERVER['REQUEST_URI'];
    }

    /* Setup redirection */

    function upme_redirect() {
        global $pagenow;

        /* Not admin */
        if (!current_user_can('manage_options')) {

            $option_name = '';
            // Check if current page is profile page
            if ('profile.php' == $pagenow) {
                // If user have selected to redirect backend profile page
                if ($this->get_option('redirect_backend_profile') == '1') {
                    $option_name = 'profile_page_id';
                }
            }


            // Check if current page is login or not
            if ('wp-login.php' == $pagenow && !isset($_REQUEST['action'])) {

                $pos = strpos($_SERVER['REQUEST_URI'], 'interim-login=1');
                $interim_status = isset($_POST['interim-login']) ? $_POST['interim-login'] : '';
                if ('1' == $interim_status || $pos === true) {
                    $option_name = '';
                } else if ($pos === false && $this->get_option('redirect_backend_login') == '1') {
                    $option_name = 'login_page_id';
                }
            }

            if ('wp-login.php' == $pagenow && isset($_REQUEST['action']) && $_REQUEST['action'] == 'register') {

                if ($this->get_option('redirect_backend_registration') == '1') {
                    $option_name = 'registration_page_id';
                }
            }

            if ($option_name != '') {
                if ($this->get_option($option_name) > 0) {
                    // Generating page url based on stored ID
                    $page_url = get_permalink($this->get_option($option_name));

                    // Redirect if page is not blank
                    if ($page_url != '') {
                        if ($option_name == 'login_page_id' && isset($_GET['redirect_to'])) {
                            $url_data = parse_url($page_url);
                            $join_code = '/?';
                            if (isset($url_data['query']) && $url_data['query'] != '') {
                                $join_code = '&';
                            }

                            $page_url = $page_url . $join_code . 'redirect_to=' . $_GET['redirect_to'];
                        }

                        wp_redirect($page_url);
                        exit;
                    }
                }
            }
        }
    }

    /* Create profile page */

    function create_profile_page() {
        if (!get_option('upme_profile_page')) {
            $new = array(
                'post_title' => __('View Profile', 'upme'),
                'post_type' => 'page',
                'post_name' => 'profile',
                'post_content' => '[upme]',
                'post_status' => 'publish',
                'comment_status' => 'closed',
                'ping_status' => 'closed',
                'post_author' => 1
            );
            $new_page = wp_insert_post($new, FALSE);


            if (isset($new_page)) {

                $current_option = get_option('upme_options');
                $page_data = get_post($new_page);

                $current_option['edit_profile_redirect'] = '';
                if (isset($page_data->guid))
                    $current_option['edit_profile_redirect'] = $page_data->guid;

                update_option('upme_options', $current_option);
                update_option('upme_profile_page', $new_page);
            }
        }
    }

    /* Get profile link by ID */

    function profile_link($id) {
        global $wp_query;

        $current_option = get_option('upme_options');

        $username = get_the_author_meta('user_login', $id);

        if (isset($current_option['profile_page_id']))
            $link = get_permalink($current_option['profile_page_id']);
        else
            $link = '';



        if ($link != '') {
            // Get the current permalink structure
            $permalink_structure;
            if (isset($_REQUEST['page_id'])) {
                $permalink_structure = 'DEFAULT';
            } else {
                $permalink_structure = 'CUSTOM';
                // Add forward slash if not available
                $link = rtrim($link, '/') . '/';
            }


            if (isset($current_option['profile_url_type']) && 2 == $current_option['profile_url_type']) {
                if ('DEFAULT' == $permalink_structure) {
                    return add_query_arg(array('username' => $username), $link);
                } else {
                    $username = str_replace('@', '-at-', $username);
                    return $link . $username;
                }
            } else {
                if ('DEFAULT' == $permalink_structure) {
                    return add_query_arg(array('viewuser' => $id), $link);
                } else {
                    return $link . $id;
                }
            }
        } else {
            return '';
        }
    }

    /* get setting */

    function get_option($option) {
        $settings = $this->upme_options;
        if(!is_array($settings)){
            $settings = get_option('upme_options');
        }

        if (isset($settings[$option])) {
            return $settings[$option];
        } else {
            return '';
        }
    }

    /* register styles */

    function add_style_scripts_frontend() {
        
        $upme_settings = get_option('upme_options');

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
        if ($this->get_option('style')) {
            wp_register_style('upme_style', upme_url . 'styles/' . $this->get_option('style') . '.css');
            wp_enqueue_style('upme_style');
        }

        /* Responsive */
        wp_register_style('upme_responsive', upme_url . 'css/upme-responsive.css');
        wp_enqueue_style('upme_responsive');

        if ($upme_settings['disable_fitvids_script_styles']) {
            wp_register_script('upme_fitvids_js', upme_url . 'js/upme-fitvids.js', array('jquery'));
            wp_enqueue_script('upme_fitvids_js');
        }

        // Add lightbox using Thickbox
        wp_enqueue_script("thickbox");
        wp_enqueue_style("thickbox");

        // Set date format from admin settings
        
        $upme_date_format = (string) isset($upme_settings['date_format']) ? $upme_settings['date_format'] : 'mm/dd/yy';

        
        $date_picker_array = upme_date_picker_setting();
        wp_localize_script('upme_date_picker_js', 'UPMEDatePicker', $date_picker_array);
     
        $lang_strings = upme_tinymce_language_setting();
        
        wp_register_script('upme_tmce', upme_url . 'admin/js/tinymce_language_strings.js');
        wp_enqueue_script('upme_tmce');

        wp_localize_script('upme_tmce', 'UPMETmce', $lang_strings);

        do_action('upme_add_style_scripts_frontend');
    }

    /* Display shortcode */

    function display($args=array()) {

        global $post, $wp_query;

        $current_option = get_option('upme_options');
        
        // Loading CSS and Script only when required
        /* Tipsy script */
        if (!wp_script_is('upme_tipsy') && '0' == $current_option['disable_tipsy_script_styles'] ) {
            wp_register_script('upme_tipsy', upme_url . 'js/jquery.tipsy.js', array('jquery'));
            wp_enqueue_script('upme_tipsy');
        }

        /* Tipsy css */
        if (!wp_style_is('upme_tipsy') && '0' == $current_option['disable_tipsy_script_styles'] ) {
            wp_register_style('upme_tipsy', upme_url . 'css/tipsy.css');
            wp_enqueue_style('upme_tipsy');
        }


        /* date_picker */
        if (!wp_style_is('upme_date_picker')) {
            wp_register_style('upme_date_picker', upme_url . 'css/upme-datepicker.css');
            wp_enqueue_style('upme_date_picker');
        }


        if (!wp_script_is('upme_date_picker_js')) {
            wp_register_script('upme_date_picker_js', upme_url . 'js/upme-datepicker.js', array('jquery'));
            wp_enqueue_script('upme_date_picker_js');
            wp_localize_script('upme_date_picker_js', 'UPMEDatePicker', upme_date_picker_setting());
        }

        // Set date picker default settings
        $date_picker_array = upme_date_picker_setting();

        wp_localize_script('upme_date_picker_js', 'UPMEDatePicker', $date_picker_array);


        /* Capture logged in user ID */
        if (is_user_logged_in ()) {
            $current_user = wp_get_current_user();
            if (($current_user instanceof WP_User)) {
                $this->logged_in_user = $current_user->ID;
            }
        }

        /* Arguments */
        $defaults = array(
            'id' => null,
            'group' => null,
            'width' => 1,
            'view' => null,
            'show_stats' => null,
            'show_social_bar' => null,
            'use_in_sidebar' => null,
            'users_per_page' => '50',
            'hide_until_search' => null,
            'orderby' => 'registered',
            'order' => 'desc',
            'orderby_custom' => false,
            'show_id' => false,            
            'role' => null,
            'group_meta' => null,
            'group_meta_value' => null,
            'recent_posts' => null,
            'logout_redirect' => null,
            'new_window' => null,
            'modal' => null,
            'modal_view' => null,
            'show_role' => false,
            'show_profile_status' => false,
            'show_result_count' => false,
            'display' => 'yes',
            'limit_results' => false,
            'hide_admins' => false,
            'name' => wp_generate_password(12,false),
        );
        $args = wp_parse_args($args, $defaults);

        $this->upme_args = $args;

        extract($args, EXTR_SKIP);

        $this->profile_show_status = $show_profile_status;

        $this->profile_show_id = $show_id;

        $this->profile_show_role = $show_role;

        $this->profile_order_field = $orderby;

        $this->profile_orderby_custom_status = $orderby_custom;

        $this->profile_order = 'asc';
        if (strtolower($order) == 'asc' || strtolower($order) == 'desc')
            $this->profile_order = $order;

        $this->profile_role = $role;
        $this->hide_admin_role = $hide_admins;

        /*  Set form name for allowing multiple profile forms through filters */
        $this->profile_form_name = $name;

        $sidebar_class = null;
        if ($use_in_sidebar)
            $sidebar_class = 'upme-sidebar';

        


        $curauth = (get_query_var('author_name')) ? get_user_by('slug', get_query_var('author_name')) : get_userdata(get_query_var('author'));

        /* Using group shuts down id */
        if (!$group) {

            $this->author_filtering_status = false;   

            if ($id == 'author' && isset($post->post_author)) {
                $this->author_filtering_status = true; 
                $id = $post->post_author;
            } else if($id == 'author' && isset($curauth->ID)) {
                $this->author_filtering_status = true; 
                $id = $curauth->ID;
            } elseif ($this->user_exists($id)) {
                $id = $id;
            } elseif (isset($_REQUEST['viewuser']) && $this->user_exists($_REQUEST['viewuser']) &&  !$use_in_sidebar) {
                $id = $_REQUEST['viewuser'];
            } elseif (isset($_REQUEST['username']) && !$use_in_sidebar) {
                // View profiles by username in default permalinks
                
                $userdata = get_user_by('login', $_REQUEST['username']);
                if ($userdata != false) {
                    $id = $userdata->data->ID;
                }

            } elseif (isset($wp_query->query_vars['upme_profile_filter']) && !$use_in_sidebar) {

                // View profiles by username/user id in custom permalinks
                $upme_profile_filter_value = $wp_query->query_vars['upme_profile_filter'];
                $upme_profile_filter_value = str_replace('-at-', '@', urldecode($upme_profile_filter_value));

                if (isset($current_option['profile_url_type']) && 2 == $current_option['profile_url_type']) {

                    $userdata = get_user_by('login', $upme_profile_filter_value);
                    if ($userdata != false) {
                        $id = $userdata->data->ID;
                    }
                } else {
                    $id = $upme_profile_filter_value;
                }

            } else {
                // Current logged ins users profile being viewed

                $id = $this->logged_in_user;
            }

            // Set the user ID of currently viewed profile
            $this->current_view_profile_id = $id;

            // Prevent restriction of logged in users profile
            $restrict_setting_status = false;
            if(is_user_logged_in()){
                $restrict_setting_status = $this->restricted_user_profile($this->logged_in_user,$this->get_option('users_can_view'));
            }

            if($id == $this->logged_in_user){
                $restrict_setting_status = false;
            }
            
        }

        /* Execute the shortcode and hide the profile. This should be used to get the 
         *  user ID of current viewed profile and execute additonal features
         */
        if(!($display == 'yes' || $display == 'true')){
            return;
        }

        /* If no ID is set, normally logged out */
        /* He must login to view his profile. */

        if (!$group) {

            // Check and display messages for INACTIVE profiles
            $current_profile_status = esc_attr(get_the_author_meta('upme_user_profile_status', $id));
            if($current_option['profile_view_status'] && ('INACTIVE' == $current_profile_status) &&
                !($this->can_edit_profile($this->logged_in_user, $id)) ){
                $profile_status_message = $current_option['html_profile_status_msg'];
                return $profile_status_message;
            }

            // Check and display messages for profiles with pending approval
            $current_approval_status = esc_attr(get_the_author_meta('upme_approval_status', $id));
            if($current_option['profile_approval_status'] && ('INACTIVE' == $current_approval_status) &&
                !($this->can_edit_profile($this->logged_in_user, $id)) ){
                $profile_status_message = $current_option['html_profile_approval_pending_msg'];
                return $profile_status_message;
            }

            // Check and display messages for profiles with pending activation
            $current_activation_status = esc_attr(get_the_author_meta('upme_activation_status', $id));
            if($current_option['set_email_confirmation'] && ('INACTIVE' == $current_activation_status) &&
                !($this->can_edit_profile($this->logged_in_user, $id)) ){
                $profile_status_message = __('This account is pending activation.','upme');
                return $profile_status_message;
            }

            if ($id == null && !is_user_logged_in()) {
                return $this->login_to_view_your_profile();
            } elseif ($id && !is_user_logged_in() && !$this->guests_can_view()) {
                return $this->login_to_view_profile();
            } elseif (!is_numeric($id)) {
                return $this->upme_invalid_user_profile();
            } elseif (is_numeric($id) && !(get_user_by('id', $id))) {
                return $this->upme_invalid_user_profile();
            } else {                
                return $this->view_profile($id, $width, $view, $group, $show_stats, $show_social_bar, $use_in_sidebar, $users_per_page, null, $role, $recent_posts,$logout_redirect,$new_window,$modal,$modal_view,$show_result_count,$limit_results);
            }
        }

        /* If group of users is used */
        if ($group) {

            if (!is_user_logged_in() && !$this->guests_can_view()) {
                return $this->login_to_view_profile();
            } else {

                return $this->view_profile($id, $width, $view, $group, $show_stats, $show_social_bar, $use_in_sidebar, $users_per_page, $hide_until_search, $role, $recent_posts,$logout_redirect,$new_window,$modal,$modal_view,$show_result_count,$limit_results);
            }
        }
    }

    function display_mini_profile($args=array()) {
        global $post;
        /* [upme_login] becomes the mini profile */

        /* Capture logged in user ID */

        $current_user = wp_get_current_user();
        if (($current_user instanceof WP_User)) {
            $this->logged_in_user = $current_user->ID;
        }

        /* Arguments */
        $defaults = array(
            'id' => null,
            'group' => null,
            'width' => 1,
            'view' => null,
            'show_stats' => null,
            'show_social_bar' => null,
            'use_in_sidebar' => null,
            'users_per_page' => null,
            'hide_until_search' => null,
            'orderby' => 'registered',
            'order' => 'desc',
            'show_id' => false,
            'role' => null,
            'logout_redirect' => null,
            'new_window' => null,
        );
        $args = wp_parse_args($args, $defaults);
        extract($args, EXTR_SKIP);

        $this->profile_show_id = $show_id;

        $this->profile_order_field = $orderby;

        $this->profile_order = 'asc';
        if (strtolower($order) == 'asc' || strtolower($order) == 'desc')
            $this->profile_order = $order;

        $sidebar_class = null;
        $name_holder_width = '50%';
        if ($use_in_sidebar) {
            $sidebar_class = 'upme-sidebar';
            $name_holder_width = '100%';
        }

        // Show custom field as profile title
        $profile_title_field = $this->upme_options['profile_title_field'];
        // Get value of profile title field or default display name if empty
        $profile_title_display = $this->upme_profile_title_value($profile_title_field, $this->logged_in_user);


        /* Block profile based on custom status and display information to user */
        $validate_profile_visibility_params = array('user_id' => $this->logged_in_user, 'status' => 'true', 'info' => '', 'context' => 'mini_profile');
        $profile_visibility = apply_filters('upme_validate_profile_visibility', $validate_profile_visibility_params);
        if( isset($profile_visibility['status']) && ! $profile_visibility['status'] ){
            $info_display = upme_profile_visibility_info($profile_visibility,$profile_title_display);
            return $info_display;
        }
        /* <-- Block profile --> */

        /* If no ID is set, normally logged out */
        /* User must login to view his profile. */

        $pic_class = 'upme-pic mini_profile';
        if (is_safari ())
            $pic_class = 'upme-pic safari mini_profile';

        $display = '';

        $display .= '<div class="upme-wrap upme-' . $id . ' upme-width-' . $width . ' ' . $sidebar_class . '">
        <div class="upme-inner upme-clearfix">
         
        <div class="upme-head">
         
        <div class="upme-left upme-profile-holder">
        <div class="' . $pic_class . '" style="width:' . $name_holder_width . ';">';

        /* UPME Filter for customizing profile URL */
        $params = array('id' => $this->logged_in_user, 'view' => $view, 'modal' => null, 'group'=>$group , 'use_in_sidebar'=>$use_in_sidebar,'context'=>'mini_profile');
        $profile_url = apply_filters('upme_custom_profile_url',$this->profile_link($this->logged_in_user),$params);
        // End Filter

        // Enable profile loading on new window
        $new_window_display = ('yes' == $new_window || 'true' == $new_window) ? ' target="_blank" ' : '';

        /* UPME Filter for customizing profile picture */
        $params = array('id'=> $this->logged_in_user, 'view' => $view, 'modal' => null, 'use_in_sidebar'=>$use_in_sidebar,'context'=>'mini_profile');
        $profile_pic_display = '<a '.$new_window_display.' href="' . $profile_url . '">' . $this->pic($this->logged_in_user, 50) . '</a>';
        $profile_pic_display = apply_filters('upme_custom_profile_pic',$profile_pic_display,$params);
        $display .= $profile_pic_display;
        // End Filter
        
        $display.='<div class="upme-field-name">';
        if ($this->get_option('clickable_profile')) {
            if ($this->get_option('clickable_profile') == 1) {                

                $display .= '<a href="' . $profile_url  . '" '.$new_window_display.'>';
            } else {
                $display .= '<a href="' . get_author_posts_url($this->logged_in_user) . '" '.$new_window_display.'>';
            }

            $display .= $profile_title_display;
            $display .= '</a>';
        } else {
            $display .= $profile_title_display;
        }
        $display.='</div>';

        $display.='</div>';

        if (is_user_logged_in ()) {

            $display .= '<div class="upme-name upme-button-holder">';
            $link = get_permalink($this->get_option('profile_page_id'));
            $class = "upme-button-alt";
            $link_text = __('View Profile', 'upme');

            //Enable customlogout url
            $logout_url = '';
            if($logout_redirect){
                $logout_url = ' redirect_to='.$logout_redirect;
            }

            $display .= '<div class="upme-field-edit upme-mini-profile-button"><a href="' . $link . '" class="' . $class . '">' . $link_text . '</a>&nbsp;' . do_shortcode('[upme_logout wrap_div="false" user_id="' . $this->logged_in_user . '"  '.$logout_url.']') . '</div>
            </div>';
        }

        $display .= '</div><div class="upme-clear"></div>';

        $display .= '</div>
         
        </div>
        </div>';

        return $display;
    }

    /* Return true if guests can view profiles */

    function guests_can_view() {
        if ($this->get_option('guests_can_view') == 1)
            return true;
        return false;
    }

    /* Login to view profile */

    function login_to_view_profile() {
        $display = null;
        $display .= '<div class="upme-wrap">';
        $display .= wpautop($this->get_option('html_login_to_view'));
        $display .= '<p class="upme-login-spacer">&nbsp;</p>';
        if($this->get_option('html_login_to_view_form')){
            $display .= do_shortcode('[upme_login]');
        }        
        $display .= '</div>';
        return $display;
    }

    /* Login to view your profile */

    function login_to_view_your_profile() {
        $display = null;
        $display .= '<div class="upme-wrap">';
        $display .= wpautop($this->get_option('html_user_login_message'));
        $display .= '<p class="upme-login-spacer">&nbsp;</p>';
        if($this->get_option('html_user_login_message_form')){
            $display .= do_shortcode('[upme_login]');
        }
        $display .= '</div>';
        return $display;
    }

    /* Display pagination class */

    function pagination($users_per_page, $users, $role=null) {
        $display = null;

        /* Prepare loop */
        $args = array('orderby' => 'registered', 'order' => 'DESC');

        if ($users != 'all') {
            $list_of_users = explode(',', $users);
            $args['include'] = $list_of_users;
        }

        $args['per_page'] = $users_per_page;
        if (!isset($this->searched_users)) {
            $this->search_result($args);
        }

        /* Count of users returned */
        $count = $this->total_matching_user;
        if ($count > $users_per_page) { // activate page links
            $display .= '<div class="upme-navi">';

            /* How many links will we display ? */
            $this->num_of_links = ceil($count / $users_per_page);

            // This determined how many links to show
            $this->link_delta = 6;

            // Getting From where to start
            $first_link = $this->current_users_page - floor($this->link_delta / 2);
            if ($first_link <= 0)
                $first_link = 1;

            // Getting From where to end
            $last_link = $this->link_delta + $first_link - 1;

            if ($last_link > $this->num_of_links) {
                $k = $last_link - $this->num_of_links;
                $first_link = $first_link - $k;
                $last_link = $this->num_of_links;
            }

            if ($first_link <= 0)
                $first_link = 1;

            // First & Previous Links Start
            if ($this->current_users_page - 1 <= $this->num_of_links && $this->current_users_page != 1) {
                $previous_link = add_query_arg(array('userspage' => $this->current_users_page - 1));
            } else {
                $previous_link = null;
            }

            $link = add_query_arg(array('userspage' => 1));
            if ($this->current_users_page > 1 && $this->current_users_page <= $this->num_of_links) {
                $display .= '<a href="javascript:void(0);" onclick="javascript:change_page(1)" class="page gradient"><span>' . __('first', 'upme') . '</span></a>';
            }
            if ($previous_link)
                $display .= '<a href="javascript:void(0);" onclick="javascript:change_page(' . ($this->current_users_page - 1) . ')" class="page gradient"><span>' . __('previous', 'upme') . '</span></a>';
            // First & Previous Links Ends


            /* Show links for limited Pages rather than all pages Starts */
            for ($i = $first_link; $i <= $last_link; $i++) {

                if ($this->current_users_page > $this->num_of_links) {
                    $this->current_users_page = 1;
                }

                $link = add_query_arg(array('userspage' => $i));

                if ($i == $this->current_users_page) {
                    $display .= '<span class="page active">' . $i . '</span>';
                } else {
                    $display .= '<a href="javascript:void(0);" onclick="javascript:change_page(' . $i . ')" class="page gradient"><span>' . $i . '</span></a>';
                }
            }
            /* Show links for limited Pages rather than all pages Ends */


            // Last & Next Links Starts
            if ($this->current_users_page + 1 <= $this->num_of_links) {
                $next_link = add_query_arg(array('userspage' => $this->current_users_page + 1));
            } else {
                $next_link = null;
            }

            $link = add_query_arg(array('userspage' => $this->num_of_links));
            if ($this->current_users_page < $this->num_of_links) { // last
                if ($next_link)
                    $display .= '<a href="javascript:void(0);" onclick="javascript:change_page(' . ($this->current_users_page + 1) . ')"  class="page gradient"><span>' . __('next', 'upme') . '</span></a>';
                if ($this->current_users_page < $this->num_of_links) {
                    $display .= '<a href="javascript:void(0);" onclick="javascript:change_page(' . $this->num_of_links . ')" class="page gradient"><span>' . __('last', 'upme') . '</span></a>';
                }
            }
            // Last & Next Links Ends
            // Getting Dropdown to jump to page if Total Pages are greater than Page Link delta
            if ($this->num_of_links > $this->link_delta) {
                $display.='<select class="upme-go-to-page">';
                $display.='<option value="0">Jump</option>';
                for ($i = 1; $i <= $this->num_of_links; $i++) {
                    $link = add_query_arg(array('userspage' => $i));

                    $sel = '';
                    if ($i == $this->current_users_page)
                        $sel = ' selected="selected"';

                    $display.='<option value="' . $i . '" ' . $sel . '>' . $i . '</option>';
                }

                $display.='</select>';
            }

            $display .= '</div>';
        }

        return $display;
    }

    /* Setup which results appear on page */

    function setup_page($args, $users_per_page) {
        if (isset($this->num_of_links) && $this->current_users_page > $this->num_of_links) {
            $current_page = 0;
        } else {
            $current_page = $this->current_users_page - 1;
        }
        $offset = $users_per_page * ($current_page);
        $args['number'] = $users_per_page;
        $args['offset'] = $offset;
        return $args;
    }

    private function build_search_field_array() {


        $custom_fields = get_option('upme_profile_fields');
        $this->search_banned_field_type = array('fileupload', 'password', 'datetime');
        $this->search_banned_field_type = array_merge($this->search_banned_field_type,$this->custom_file_field_types);

        $this->show_combined_search_field = false;
        $this->show_nontext_search_fields = false;

        $this->all_text_search_field = array();
        $this->combined_search_field = array();
        $this->nontext_search_fields = array();
        $this->checkbox_search_fields = array();

        $included_fields = '';
        if ($this->search_args['fields'] != '')
            $included_fields = explode(',', $this->search_args['fields']);

        $excluded_fields = explode(',', $this->search_args['exclude_fields']);

        $search_filters = array();
        $search_filters = explode(',', $this->search_args['filters']);

        foreach ($custom_fields as $key => $value) {
            if (isset($value['type']) && $value['type'] == 'usermeta') {
                if (isset($value['field']) && !in_array($value['field'], $this->search_banned_field_type)) {
                    if (isset($value['meta']) && !in_array($value['meta'], $excluded_fields)) {
                        switch ($value['field']) {
                            case 'text':
                            case 'textarea':
                            case 'datetime':

                                if (is_array($search_filters) && in_array($value['meta'], $search_filters)) {
                                    if ($this->show_nontext_search_fields === false) {
                                        $this->show_nontext_search_fields = true;
                                    }

                                    $this->nontext_search_fields[] = $value;
                                } else {
                                    if ($this->show_combined_search_field === false)
                                        $this->show_combined_search_field = true;

                                    $this->all_text_search_field[] = $value['meta'];

                                    if (is_array($included_fields) && count($included_fields) > 0 && in_array($value['meta'], $included_fields))
                                        $this->combined_search_field[] = $value['meta'];
                                }



                                break;

                            case 'select':
                            case 'radio':

                                $is_in_field = false;
                                $is_in_filter = false;

                                if (is_array($search_filters) && in_array($value['meta'], $search_filters))
                                    $is_in_filter = true;

                                if (is_array($included_fields) && count($included_fields) > 0 && in_array($value['meta'], $included_fields))
                                    $is_in_field = true;

                                if ($is_in_field == true || $is_in_filter == true) {
                                    if ($this->show_nontext_search_fields === false) {
                                        $this->show_nontext_search_fields = true;
                                    }

                                    $this->nontext_search_fields[] = $value;
                                }
                                break;

                            case 'checkbox':

                                $is_in_field = false;
                                $is_in_filter = false;

                                if (is_array($search_filters) && in_array($value['meta'], $search_filters))
                                    $is_in_filter = true;

                                if (is_array($included_fields) && count($included_fields) > 0 && in_array($value['meta'], $included_fields))
                                    $is_in_field = true;

                                if ($is_in_filter == true || $is_in_field == true) {
                                    if ($this->show_nontext_search_fields === false) {
                                        $this->show_nontext_search_fields = true;
                                    }

                                    $this->checkbox_search_fields[] = $value;
                                }
                                break;

                            default:
                                break;
                        }
                    }
                }
            }
        }
    }

    /* Setup search form */

    function search($args=array()) {

        global $predefined;

        $current_option = get_option('upme_options');

        // Determine search form is loaded
        $this->upme_search = true;
        /* Default Arguments */
        $defaults = array(
            'fields' => null,
            'filters' => null,
            'exclude_fields' => null,
            'operator' => 'AND',
            'use_in_sidebar' => null,
            'users_are_called' => $current_option['users_are_called'],
            'combined_search_text' => $current_option['combined_search_text'],
            'button_text' => $current_option['search_button_text'],
            'reset_button_text' => $current_option['reset_button_text'],
            'show_combined_search' => true,
            'name' => wp_generate_password(12,false),
        );

        $this->search_args = wp_parse_args($args, $defaults);        

        $this->search_operator = $this->search_args['operator'];

        $this->search_form_name = $this->search_args['name'];


        if (strtolower($this->search_args['operator']) != 'and' && strtolower($this->search_args['operator']) != 'or') {
            $this->search_args['operator'] = 'AND';
        }

        // Prepare array of all fields to load
        $this->build_search_field_array();

        // Remove combned search option based on shortcode attributes
        if('false' == strtolower($this->search_args['show_combined_search']) || ('no' == strtolower($this->search_args['show_combined_search']))){
            $this->show_combined_search_field = false;
        }        

        $sidebar_class = null;
        if ($this->search_args['use_in_sidebar'])
            $sidebar_class = ' upme-sidebar';

        $display = null;

        $display.='<div class="upme-wrap upme-wrap-form upme-search-wrap' . $sidebar_class . '">';
        $display.='<div class="upme-inner upme-clearfix">';
        $display.='<div class="upme-head">' . sprintf(__('Search %s', 'upme'), $this->search_args['users_are_called']) . '</div>';
        
        /* Set form submission to member list page when used search in sidebar */
        $action = '';
        if ($this->search_args['use_in_sidebar']){
            $member_list_page_id = isset($current_option['member_list_page_id']) ? $current_option['member_list_page_id'] :'';
            if('' != $member_list_page_id){
                $action = get_permalink($member_list_page_id);
            }            
        }             

        $display.='<form action="'.$action.'" method="post" id="upme_search_form" class="upme-search-form upme-clearfix">';

        
        // Check For default fields Start
        if ($this->show_combined_search_field === true) {
            $display.='<p class="upme-p upme-search-p">';
            $display.= UPME_Html::text_box(array(
                        'class' => 'upme-search-input upme-combined-search',
                        'value' => isset($_POST['upme_combined_search']) ? $_POST['upme_combined_search'] : '',
                        'name' => 'upme_combined_search',
                        'placeholder' => $this->search_args['combined_search_text']
                    ));

            if (count($this->combined_search_field) > 0) {
                $display.='<input type="hidden" name="upme_combined_search_fields" value="' . implode(',', $this->combined_search_field) . '" />';
            } else {
                $display.='<input type="hidden" name="upme_combined_search_fields" value="' . implode(',', $this->all_text_search_field) . '" />';
            }


            $display.='</p>';
        }

        // Check For default fields End
        // Custom Search Fields Creation Starts

        if ($this->show_nontext_search_fields === true) {
            $counter = 0;
            $display.='<p class="upme-p upme-search-p">';
            foreach ($this->nontext_search_fields as $key => $value) {
                $method_name = '';
                $method_name = $this->method_mapping[$value['field']];
                if ($method_name != '') {
                    if ($counter > 0 && $counter % 2 == 0) {
                        $display.='</p>';
                        $display.='<p class="upme-p upme-search-p">';
                    }

                    $counter++;

                    $class = 'upme-search-input upme-search-input-left upme-search-meta-' . $value['meta'];
                    if ($counter > 0 && $counter % 2 == 0)
                        $class = 'upme-search-input upme-search-input-right upme-search-meta-' . $value['meta'];


                    if ($method_name == 'drop_down') {
                        $loop = array();

                        if (isset($value['predefined_loop']) && $value['predefined_loop'] != '' && $value['predefined_loop'] != '0') {
                            $defined_loop = $predefined->get_array($value['predefined_loop']);

                            foreach ($defined_loop as $option) {
                                if ($option == '' || $option == null) {
                                    $loop[$option] = $value['name'];
                                } else {
                                    $loop[$option] = $option;
                                }
                            }
                        } else if (isset($value['choices']) && $value['choices'] != '') {
                            $loop_default = explode(PHP_EOL, $value['choices']);
                            $loop[''] = $value['name'];

                            foreach ($loop_default as $option)
                                $loop[$option] = $option;
                        }

                        if (isset($_POST['upme_search'][$value['meta']]))
                            $_POST['upme_search'][$value['meta']] = stripslashes_deep($_POST['upme_search'][$value['meta']]);


                        $default = isset($_POST['upme_search'][$value['meta']]) ? $_POST['upme_search'][$value['meta']] : '0';
                        $name = 'upme_search[' . $value['meta'] . ']';

                        if ($value['field'] == 'checkbox') {
                            $default = isset($_POST['upme_search'][$value['meta']]) ? $_POST['upme_search'][$value['meta']] : array();
                            $name = 'upme_search[' . $value['meta'] . '][]';
                        }

                        if (count($loop) > 0) {
                            $display.= UPME_Html::drop_down(array(
                                        'class' => $class,
                                        'name' => $name,
                                        'placeholder' => $value['name']
                                            ), $loop, $default);
                        }
                    } else if ($method_name == 'text_box') {
                        if (isset($_POST['upme_search'][$value['meta']]))
                            $_POST['upme_search'][$value['meta']] = stripslashes_deep($_POST['upme_search'][$value['meta']]);


                        $default = isset($_POST['upme_search'][$value['meta']]) ? $_POST['upme_search'][$value['meta']] : '';
                        $name = 'upme_search[' . $value['meta'] . ']';

                        $display.= UPME_Html::text_box(array(
                                    'class' => $class,
                                    'name' => $name,
                                    'placeholder' => $value['name'],
                                    'value' => $default
                                ));
                    }
                }
            }
            $display.='</p>';


            if (isset($this->checkbox_search_fields) && count($this->checkbox_search_fields) > 0) {
                foreach ($this->checkbox_search_fields as $key => $value) {
                    $display.='<p class="upme-p upme-search-p upme-multiselect-p">';

                    $method_name = '';
                    $method_name = $this->method_mapping[$value['field']];
                    if ($method_name != '') {
                        $class = 'upme-search-input upme-search-multiselect upme-search-meta-' . $value['meta'];

                        $loop = array();

                        if (isset($value['predefined_loop']) && $value['predefined_loop'] != '' && $value['predefined_loop'] != '0') {
                            $defined_loop = $predefined->get_array($value['predefined_loop']);

                            foreach ($defined_loop as $option)
                                $loop[$option] = $option;
                        } else if (isset($value['choices']) && $value['choices'] != '') {
                            $loop_default = explode(PHP_EOL, $value['choices']);
                            $loop[''] = $value['name'];

                            foreach ($loop_default as $option)
                                $loop[$option] = $option;
                        }

                        if (isset($_POST['upme_search'][$value['meta']]))
                            $_POST['upme_search'][$value['meta']] = stripslashes_deep($_POST['upme_search'][$value['meta']]);

                        $default = isset($_POST['upme_search'][$value['meta']]) ? $_POST['upme_search'][$value['meta']] : '0';
                        $name = 'upme_search[' . $value['meta'] . ']';
                        if ($value['field'] == 'checkbox') {
                            $default = isset($_POST['upme_search'][$value['meta']]) ? $_POST['upme_search'][$value['meta']] : array();
                            $name = 'upme_search[' . $value['meta'] . '][]';
                        }

                        if (count($loop) > 0) {
                            $display.= UPME_Html::drop_down(array(
                                        'class' => $class,
                                        'name' => $name,
                                        'placeholder' => $value['name']
                                            ), $loop, $default);
                        }
                    }

                    $display.='</p>';
                }
            }
        }

        $search_custom_inputs_params = array();
        $display.= apply_filters('upme_search_custom_inputs','',$search_custom_inputs_params);


        $display.='<input type="hidden" name="userspage" id="userspage" value="" />';

        $display.='<input type="hidden" name="upme-search-fired" id="upme-search-fired" value="1" />';

        // Custom Search Fields Creation Ends
        // Submit Button
        $display.='<p class="upme-search-submit-p">';
        $display.=UPME_Html::button('submit', array(
                    'class' => 'upme-button-alt upme-search-submit',
                    'name' => 'upme-search',
                    'value' => $this->search_args['button_text']
                ));
        $display.='&nbsp;';


        $display.= apply_filters('upme_search_custom_actions','');
        
        $display.='&nbsp;';
        $display.=UPME_Html::button('button', array(
                    'class' => 'upme-button-alt upme-search-reset',
                    'name' => 'upme-search-reset',
                    'value' => $this->search_args['reset_button_text'],
                    'id' => 'upme-reset-search'
                ));

        

        $display.='</p>';
        $display.='</form>';

        $display.='</div>';
        $display.='</div>';
        /* Extra Clearfix for Avada Theme */
        $display.='<div class="upme-clearfix"></div>';

        return $display;
    }

    /* Apply search params and Generate Results */

    function search_result($args) {

        global $wpdb,$wp_version;

        if(!isset($this->search_form_name)){
            $this->search_form_name = '';
        }

        $this->search_query = array();

        $this->search_user_argument = array();

        // Set search args when search is not used in the list page
        if(!isset($this->search_args)){
            $this->search_args = array();
            $this->search_args['operator'] = 'AND';
        }

        // Insert additional inner join to order the results by a custom meta field
        $orderby_meta_join = '';
        $orderby_meta_default_where = '';
        if($this->profile_orderby_custom_status || 'yes' == $this->profile_orderby_custom_status){
            $orderby_meta_join = ' INNER JOIN ' . $wpdb->usermeta . ' as mta ON (users.ID = mta.user_id) ';
            $orderby_meta_default_where = ' (mta.meta_key = "'.$this->profile_order_field.'" )  ' ;
        }
        // End
        $this->search_query_string = "SELECT DISTINCT users.* FROM " . $wpdb->users . " as users ";
        
        $this->count_search_query_string = "SELECT COUNT(DISTINCT(users.ID)) FROM " . $wpdb->users . " as users";
        
        $current_option = get_option('upme_options');

        // Only consider active users for results
        $this->search_query_search_param = array();


        $search_user_status_filters = array();
        if(!current_user_can('manage_options')){
            // When profile_view_status is not enabled, all users will be active

            /* UPME Filters for enable/disable profile status on search */
            $search_profile_status_params = array('form_name' => $this->search_form_name);
            $search_profile_status = apply_filters( 'upme_search_profile_status', true , $search_profile_status_params);
            // End Filter

            if($search_profile_status){
                array_push($search_user_status_filters, "(mt.meta_key = '_upme_search_cache' AND mt.meta_value LIKE '%upme_user_profile_status::ACTIVE%')");       
            }           
        }

        if(!current_user_can('manage_options')){

            /* UPME Filters for enable/disable profile status on search */
            $search_approval_status_params = array('form_name' => $this->search_form_name);
            $search_approval_status = apply_filters( 'upme_search_approval_status', true , $search_approval_status_params);
            // End Filter

            if($search_approval_status){
                array_push($search_user_status_filters, "(mt.meta_key = '_upme_search_cache' AND mt.meta_value LIKE '%upme_approval_status::ACTIVE%')");
            }

            /* UPME Filters for enable/disable profile status on search */
            $search_activation_status_params = array('form_name' => $this->search_form_name);
            $search_activation_status = apply_filters( 'upme_search_activation_status', true , $search_activation_status_params);
            // End Filter

            if($search_activation_status){
                array_push($search_user_status_filters, "(mt.meta_key = '_upme_search_cache' AND mt.meta_value LIKE '%upme_activation_status::ACTIVE%')");
            }
              
        }     

        if('' != $orderby_meta_default_where){
            array_push($search_user_status_filters,$orderby_meta_default_where);
        }   

        // Apply mandatory filters on search results
        $this->search_query_custom_param = apply_filters('upme_search_user_status',$search_user_status_filters);

        $this->combined_search_query_search_param = array();

        if (is_post ()) {
            if (is_in_post('upme_combined_search') && is_in_post('upme_combined_search_fields')) {
                if (post_value('upme_combined_search_fields') != '' && post_value('upme_combined_search') != '') {
                    $fields = explode(',', post_value('upme_combined_search_fields'));

                    if ( version_compare( $wp_version, '4.0', '>=' ) ) {
                        $escaped_value = $wpdb->esc_like(post_value('upme_combined_search'));
                    }else{
                        $escaped_value = like_escape(post_value('upme_combined_search'));
                    }
                    
                    $combined_search_text = esc_sql($escaped_value);

                    foreach ($fields as $key => $value) {

                        $this->combined_search_query_search_param[] = "(mt.meta_key = '_upme_search_cache' AND mt.meta_value LIKE '%" . $value . '::' . $combined_search_text . "%')";
                    }

                    $this->search_query_search_param[] = '( ' . implode(' OR ', $this->combined_search_query_search_param) . ' )';
                
                }
            }

            if (is_in_post('upme_search')) {
                foreach ($_POST['upme_search'] as $key => $value) {
                    $process = false;

                    if (is_array($value) && count($value) > 0)
                        $process = true;
                    else if ($value != '' && $value != '0')
                        $process = true;
                    else
                        $process = false;

                    if ($process === true) {
                        if (is_array($value)) {

                            foreach ($value as $k => $v) {
                                $this->search_query_search_param[] = "(mt.meta_key = '_upme_search_cache' AND mt.meta_value LIKE '%" . $key . '::' . esc_sql(trim($v)) . "%')";
                            }
                        } else {

                            $this->search_query_search_param[] = "(mt.meta_key = '_upme_search_cache' AND mt.meta_value LIKE '%" . $key . '::' . esc_sql($value) . "%')";
                        }
                    }
                }
            }
        }

        $this->search_query_role_param = '';

        if ($this->profile_role) {

            $this->profile_role = explode(',', $this->profile_role);

            if (count($this->profile_role) > 0) {
                foreach ($this->profile_role as $key => $value) {
                    $this->search_query_role_param[] = "(mt.meta_key = '_upme_search_cache' AND mt.meta_value LIKE '%role::" . $value . "%')";
                }
            }

            $role_operator = 'AND';

            $this->search_query_role_param = ' ' . $role_operator . ' ( ' . implode(' OR ', $this->search_query_role_param) . ' ) ';
        }

        // Hiding Admins from the list when attribute is set
        if (!current_user_can('manage_options') && $this->hide_admin_role && ($this->hide_admin_role == 'yes' || $this->hide_admin_role == 'true' )) {

            $search_query_role_param[] = "(mt.meta_key = '_upme_search_cache' AND mt.meta_value NOT LIKE '%role::administrator%')";

            $role_operator = 'AND';

            $this->search_query_role_param .= ' ' . $role_operator . ' ( ' . implode(' OR ', $search_query_role_param) . ' ) ';
        }

        // Setting up order data, This is required before adding search conditions
        $post_count_sort = '';
        if (in_array($this->profile_order_field, array('nicename', 'email', 'url', 'registered')))
            $orderby = 'users.user_' . $this->profile_order_field;
        elseif (in_array($this->profile_order_field, array('user_nicename', 'user_email', 'user_url', 'user_registered')))
            $orderby = 'users.' . $this->profile_order_field;
        elseif ('name' == $this->profile_order_field || 'display_name' == $this->profile_order_field)
            $orderby = 'users.display_name';
        elseif ('ID' == $this->profile_order_field || 'id' == $this->profile_order_field)
            $orderby = 'users.ID';
        else if ('post_count' == $this->profile_order_field) {

            $where = get_posts_by_author_sql('post');

            $post_count_sort = " LEFT OUTER JOIN (
            SELECT post_author, COUNT(*) as post_count
            FROM $wpdb->posts
            $where
            GROUP BY post_author
            ) p ON (users.ID = p.post_author)
            ";

            $orderby = 'post_count';
        }else if($this->profile_orderby_custom_status || 'yes' == $this->profile_orderby_custom_status){ 
            $orderby = 'mta.meta_value';
        }
        else
            $orderby = 'users.user_login';

        // Add custom query conditions into the query
        $search_query_custom_params = '';
        if(count($this->search_query_custom_param) > 0){
            $search_query_custom_params = ' AND '.implode('  AND ', $this->search_query_custom_param);        
        }


        if (count($this->search_query_search_param) > 0 ) {
            // Results after searching - POST variables are available

            $this->search_query_string.=' INNER JOIN ' . $wpdb->usermeta . ' as mt ON (users.ID = mt.user_id) '.$orderby_meta_join.' ' . $post_count_sort . ' WHERE 1=1 AND ('. implode(' ' . $this->search_args['operator'] . ' ', $this->search_query_search_param) . ') ' . $search_query_custom_params;

            $this->count_search_query_string.=' INNER JOIN ' . $wpdb->usermeta . ' as mt ON (users.ID = mt.user_id) '.$orderby_meta_join.' ' . $post_count_sort . ' WHERE 1=1 AND ('. implode(' ' . $this->search_args['operator'] . ' ', $this->search_query_search_param) . ') ' . $search_query_custom_params;

            // Search users with provided cusotm field and provided custom value
            if ($this->upme_args['group_meta'] != '' && $this->upme_args['group_meta_value'] != '') {

                $this->search_query_string.=" AND (mt.meta_key = '_upme_search_cache' AND mt.meta_value LIKE '%" . $this->upme_args['group_meta'] . "::" . $this->upme_args['group_meta_value'] . "%')";

                $this->count_search_query_string.=" AND (mt.meta_key = '_upme_search_cache' AND mt.meta_value LIKE '%" . $this->upme_args['group_meta'] . "::" . $this->upme_args['group_meta_value'] . "%')";
            }
        } else {

            // Default results before searching - POST variables not available
            if ($this->upme_args['group_meta'] != '' && $this->upme_args['group_meta_value'] != '') {

                // Search users with provided cusotm field and provided custom value
                $this->search_query_string.=" INNER JOIN " . $wpdb->usermeta . " as mt ON (users.ID = mt.user_id) ".$orderby_meta_join." ". $post_count_sort ." WHERE 1=1 AND  (mt.meta_key = '_upme_search_cache' AND mt.meta_value LIKE '%" . $this->upme_args['group_meta'] . "::" . $this->upme_args['group_meta_value'] . "%')" . $search_query_custom_params;

                $this->count_search_query_string.=" INNER JOIN " . $wpdb->usermeta . " as mt ON (users.ID = mt.user_id) ".$orderby_meta_join. " " . $post_count_sort ." WHERE 1=1 AND  (mt.meta_key = '_upme_search_cache' AND mt.meta_value LIKE '%" . $this->upme_args['group_meta'] . "::" . $this->upme_args['group_meta_value'] . "%')" . $search_query_custom_params;
            } else {

                $default_join_status = false;
                if ($this->search_query_role_param != '' || count($this->search_query_custom_param) > 0) {
                    $default_join_status = true;
                }

                // Set default join state when role status is available or custom params are available
                // without default search params
                $default_inner_join = '';
                if($default_join_status){
                    $default_inner_join = ' INNER JOIN ' . $wpdb->usermeta . ' as mt ON (users.ID = mt.user_id) ';
                }

                $this->search_query_string.= $post_count_sort.$default_inner_join.$orderby_meta_join.$search_query_custom_params;
                $this->count_search_query_string.= $post_count_sort.$default_inner_join.$orderby_meta_join.$search_query_custom_params;
            }
        }

        if ($this->search_query_role_param != '') {
            $this->search_query_string.= $this->search_query_role_param;
            $this->count_search_query_string.= $this->search_query_role_param;
        }

       

        $this->search_query_string.= ' ORDER BY ' . $orderby . ' ' . $this->profile_order;
        $this->count_search_query_string.= ' ORDER BY ' . $orderby . ' ' . $this->profile_order;

        // Execute custom actions on search result users
        do_action('upme_search_result_export',$this->search_query_string);

        // Setting Limit Data
        if (isset($this->current_users_page) && $this->current_users_page > 1) {
            $offset = ($this->current_users_page - 1) * $args['per_page'];
            $this->search_query_string.= ' LIMIT ' . $offset . ',' . $args['per_page'];
        } else {
            $this->search_query_string.= ' LIMIT ' . $args['per_page'];
        }

        $this->count_search_query_string.= ' LIMIT 1';

        $this->total_matching_user = $wpdb->get_var($this->count_search_query_string);
        $this->searched_users = $wpdb->get_results($this->search_query_string);

    }

    //  Function to check if user have enterred search criteria or not
    function check_search_input() {
        if (is_post ()) {
            if (is_in_post('upme_combined_search') && post_value('upme_combined_search') != '') {
                return true;
            }

            if (is_in_post('upme_search')) {
                foreach ($_POST['upme_search'] as $key => $value) {
                    if (is_array($value) && count($value) > 0)
                        return true;
                    else if ($value != '' && $value != '0')
                        return true;
                }
            }
        }

        return false;
    }

    /* View profile area */

    function view_profile($id=null, $width=null, $view=null, $group=null, $show_stats=null, $show_social_bar=null, $use_in_sidebar=null, $users_per_page=null, $hide_until_search=null, $role=null, $recent_posts=null,$logout_redirect=null,$new_window=null,$modal=null,$modal_view=null,$show_result_count=null,$limit_results=null) {

        global $upme_save;

        $display = null;

        unset($this->searched_users);

        /* Search running? */
        if (isset($_REQUEST['upme-search-fired'])) {

            $current_option = get_option('upme_options');

            if ($hide_until_search == 'true' && $current_option['require_search_input'] == '1') {
                if ($this->check_search_input()) {
                    $hide_until_search = false;
                } else {
                    $this->no_search_input = true;
                    $hide_until_search = true;
                }
            } else {
                $hide_until_search = false;
            }
        }

        $sidebar_class = null;
        if ($use_in_sidebar)
            $sidebar_class = 'upme-sidebar';


        // Manage restricted viewing of user profiles in list pages
        $restricted_message = $this->get_option('html_other_profiles_restricted');
        $restricted_profile_status = false;
        if(is_user_logged_in()){
           $restricted_profile_status = $this->restricted_user_profile($this->logged_in_user,$this->get_option('users_can_view'));
        }
        
        $users = array();
        /* Ignore id if group is used */
        if ($group && !$restricted_profile_status) {    

            /* allow search */
            $this->allow_search = true;

            /* pagination */
            $pagination_bar = '';
            if (!$hide_until_search && $group == 'all') {

                if ($users_per_page){                    

                    $pagination_bar .= $this->pagination($users_per_page, $group, $role);
                     /*  Show limited number of users from given list */
                    if ('true' == $limit_results || 'yes' == $limit_results ){
                        $pagination_bar  = '';
                    }
                }

               
                    
            }

            /* Loop of users */
            $args = array('orderby' => $this->profile_order_field, 'order' => $this->profile_order);

            if ($group != 'all') {
                $users = explode(',', $group);
            }

            /* Setup offset/page and array of users */
            if ($users_per_page) {
                $args = $this->setup_page($args, $users_per_page);
            }

            /* Modify args */
            if (!$hide_until_search) {

                if (isset($_REQUEST['upme-search-fired']) || $group == 'all') {
                    
                    if (!isset($this->searched_users)) {
                        $this->search_result($args);
                    }

                    foreach ($this->searched_users as $user) {
                        $users[] = $user->ID;
                    }
                }
            }

            if('true' == strtolower($show_result_count) || 'yes' == strtolower($show_result_count)){
                $display .= '<div class="upme-search-result-count">';
                $search_result_msg = __('Your search has returned <span>'.$this->total_matching_user.'</span> results','upme');
                $display .= apply_filters('upme_search_result_count_message',$search_result_msg,$this->total_matching_user);
                $display .= '</div>';
            }
            $display .= $pagination_bar;

        } 
        else if($group && $restricted_profile_status){
            return $restricted_message;       
        }
        else if(!$group && $restricted_profile_status){   

            if(($this->logged_in_user == $id) || $this->author_filtering_status){
                $users[] = $id;                       
            }else{
                return $restricted_message;
            }        
                 
        }else {
            $users[] = $id;
        }

        $pic_class = 'upme-pic';
        if (is_safari ())
            $pic_class = 'upme-pic safari';


        /* Loop and display users */
        if (!$hide_until_search) {

            if ($users) {

                $display .= '<div class="upme-column-wrap">';

                foreach ($users as $id) {

                    // Show custom field as profile title
                    $current_options = $this->upme_options;
                    $profile_title_field = $this->upme_options['profile_title_field'];

                    $profile_title_display = $this->upme_profile_title_value($profile_title_field, $id); 

                    /* Block profile based on custom status and display information to user */
                    $validate_profile_visibility_params = array('user_id' => $id, 'status' => 'true', 'info' => '', 'context' => 'full_profile');
                    $profile_visibility = apply_filters('upme_validate_profile_visibility', $validate_profile_visibility_params);
                    if( isset($profile_visibility['status']) && ! $profile_visibility['status'] ){
                        $display .= upme_profile_visibility_info($profile_visibility,$profile_title_display);
                        continue;
                    }
                    /* <-- Block profile --> */

                    $display .= '<div class="upme-wrap upme-' . $id . ' upme-width-' . $width . ' ' . $sidebar_class . '">
                    <div class="upme-inner upme-clearfix upme-view-panel">';

                    /* UPME Filters for after profile head section */
                    $display .= apply_filters( 'upme_profile_before_head', '' , $id);

                    if('compact' == $view){
                        $display .= apply_filters( 'upme_compact_profile_before_head', '', $id);
                    }else{
                        $display .= apply_filters( 'upme_full_profile_before_head', '', $id);
                    }
                    // End Filters

                    $display .= '<div class="upme-head">
                     
                    <div class="upme-left">';

                    // Enable profile loading on new window
                    $new_window_display = ('yes' == $new_window || 'true' == $new_window) ? ' target="_blank" ' : '';
                    $new_window_display_pic = $new_window_display;

                    $params = array('id' => $id, 'view' => $view, 'modal' => $modal, 'group'=>$group , 'use_in_sidebar'=>$use_in_sidebar, 'context' => 'normal');
                    /* UPME Filter for customizing profile URL */
                    $profile_url = apply_filters('upme_custom_profile_url',$this->profile_link($id),$params);
                    // End Filter
                                
                    // Override new window setting when modal is set
                    if('yes' == $modal || 'true' == $modal){
                        $new_window_display = ' class="profile-fancybox " data-url="'.$profile_url.'"';
                        $profile_url = '#upme_inner_modal';
                    }

                    $profile_pic_display = '';
                    if ($this->get_option('clickable_profile')) {
                        if ($this->get_option('clickable_profile') == 1) {
                            if ('compact' == $view) {
                                $profile_pic_display .= '<a href="'.$profile_url.'" upme-data-user-id="'.$id.'" '.$new_window_display.'>' . $this->pic($id, 50) . '</a>';
                            } else {
                                $profile_pic_display .= '<a href="'.$profile_url.'">' . $this->pic($id, 50) . '</a>';
                            }
                        }else{
                            $profile_pic_display .= '<a href="' . get_author_posts_url($id) . '" '.$new_window_display_pic.'>' . $this->pic($id, 50) . '</a>';
                           
                        }
                    }else{
                        $profile_pic_display .= $this->pic($id, 50);                           
                    }

                    $display .= '<div class="' . $pic_class . '">';
                    /* UPME Filter for customizing profile picture */
                    $params = array('id'=> $id, 'view' => $view, 'modal' => $modal, 'use_in_sidebar'=>$use_in_sidebar, 'context' => 'normal');
                    $profile_pic_display = apply_filters('upme_custom_profile_pic',$profile_pic_display,$params);
                    $display .= $profile_pic_display;
                    // End Filter
                    $display .= '</div>';
                       
                    if ($this->can_edit_profile($this->logged_in_user, $id)) {

                        $display .= '<div class="upme-name">
                        <div class="upme-field-name">';                        
        
                        if ($this->get_option('clickable_profile')) {
                            if ($this->get_option('clickable_profile') == 1) { 
                                if('compact' == $view){
                                    $display .= '<a href="'.$profile_url.'" upme-data-user-id="'.$id.'" ' .$new_window_display. ' >';
                                }else if('yes' != $modal_view && 'true' != $modal_view){
                                    $display .= '<a href="' . $profile_url . '" >';
                                }
                                
                            } else if('yes' != $modal_view && 'true' != $modal_view){ 
                                $display .= '<a href="' . get_author_posts_url($id) . '" ' .$new_window_display_pic. '>';
                            }


                            $display .= $profile_title_display;
                            $display .= '</a>';
                        } else {
                            $display .= $profile_title_display;
                        }

                        $display .= '</div>';
                        
                        /*  UPME filter for adding contents into header section between profile title and buttons */
                        $profile_header_fields_params = array('id'=> $id, 'view' => $view, 'modal' => $modal, 'use_in_sidebar'=>$use_in_sidebar, 'context' => 'normal');
                        $display .= apply_filters('upme_profile_header_fields','',$profile_header_fields_params );

                        if ($use_in_sidebar == 'yes' || $use_in_sidebar) {
                            $link = get_permalink($this->get_option('profile_page_id'));
                            $class = "upme-button-alt";
                            $link_text = __('View Profile', 'upme');
                        } else {
                            $link = '#edit';
                            $class = "upme-button-alt upme-fire-editor";
                            $link_text = __('Edit Profile', 'upme');
                        }

                        //Enable customlogout url
                        $logout_url = '';
                        if($logout_redirect){
                            $logout_url = ' redirect_to='.$logout_redirect;
                        }

                        //Change link for modal edit button
                        $target_window = '';

                        // Enable  profile view/edit modes based on loading window
                        $params   = array('logout_url'=>$logout_url,'group'=>$group,'use_in_sidebar' => $use_in_sidebar);


                        if(isset($_POST['upme_modal_profile']) && 'yes' == $_POST['upme_modal_profile']){

                            $link = $this->profile_link($id);
                            $link = upme_add_query_string($profile_url, 'upme_modal_target_link=yes');
                            $target_window = ' target="_blank" ';

                            $edit_buttons = '<a '.$target_window.' href="' . $link . '" class="' . $class . '">' . $link_text . '</a>&nbsp;' . do_shortcode('[upme_logout wrap_div="false" user_id="' . $id . '"  '.$logout_url.']');
                            $params['type'] = 'modal';

                            $display .= '<div class="upme-field-edit-modal">';
                            /* UPME Filters for profile edit buttons panel */
                            $display .= apply_filters( 'upme_profile_edit_bar', $edit_buttons , $id, $params);
                            // End Filter
                            $display .= '</div>
                        </div>';

                        }else{   
                            
                            $edit_buttons = '<a  href="' . $link . '" class="' . $class . '">' . $link_text . '</a>&nbsp;' . do_shortcode('[upme_logout wrap_div="false" user_id="' . $id . '"  '.$logout_url.']');
                            $params['type'] = $view;

                            $display .= '<div class="upme-field-edit">';
                            /* UPME Filters for profile edit buttons panel */
                            $display .= apply_filters( 'upme_profile_edit_bar', $edit_buttons , $id, $params);
                            // End Filter
                            $display .='</div>
                                            </div>';
                        }
                        
                        
                    } else {

                        $display .= '<div class="upme-name">
                        <div class="upme-field-name upme-field-name-wide">';

                        if ($this->get_option('clickable_profile')) {
                            if ($this->get_option('clickable_profile') == 1) {

                                if('compact' == $view){
                                    $display .= '<a href="' . $profile_url . '" upme-data-user-id="'.$id.'" ' .$new_window_display. '>';
                                }else if('yes' != $modal_view && 'true' != $modal_view){
                                    $display .= '<a href="' . $profile_url . '" >';
                                }
                               
                            } else if('yes' != $modal_view && 'true' != $modal_view){  
                                $display .= '<a href="' . get_author_posts_url($id) . '" ' .$new_window_display_pic. '>';
                            }

                            $display .= $profile_title_display;
                            $display .= '</a>';
                        } else {
                            $display .= $profile_title_display;
                        }

                        $display .= '</div>';
                        
                        $profile_header_fields_params = array('id'=> $id, 'view' => $view, 'modal' => $modal, 'use_in_sidebar'=>$use_in_sidebar, 'context' => 'normal');
                        $display .= apply_filters('upme_profile_header_fields','',$profile_header_fields_params );
                        
                        $display .= '</div>';
                    }

                    $display .= '</div>';



                    if (($width == '2' || $width == '3') && ($view != 'compact')) {
                        $display .= '<div class="upme-clear"></div>';
                    }

                    $display .= '<div class="upme-right">';

                    if ($show_social_bar != 'no' && $show_social_bar != 'false') {
                        $display .= $this->show_user_social_profiles($id);
                    }

                    if ($show_stats != 'no' && $show_stats != 'false') {
                        $display .= $this->show_user_stats($id);
                    }

                    $display .= '</div><div class="upme-clear"></div>';

                    
                    $user_profile_form_name = get_user_meta($id,'upme-register-form-name',true);
                    if( $user_profile_form_name == '' ){
                        $user_profile_form_name = $this->profile_form_name;
                    }

                    /* UPME Filters for customizing profile form name */
                    $profile_form_name_params = array('user_id' => $id ,'view'=>$view , 'page_form_name' => $this->profile_form_name, 'profile_form_name' => $user_profile_form_name, 'width'=>$width);
                    $user_profile_form_name = apply_filters( 'upme_profile_form_name', $user_profile_form_name, $profile_form_name_params);
                    // End Filter

                    /* UPME Filters for profile header buttons panel */
                    $header_bar_params = array('view'=>$view , 'form_name' => $user_profile_form_name, 'width'=>$width);
                    $display .= apply_filters( 'upme_profile_header_bar', '' ,$id, $header_bar_params);
                    // End Filter
                     
                    $display .= '</div>';

                    /* UPME Filters for after profile head section */
                    $display .= apply_filters( 'upme_profile_after_head', '', $id);

                    if('compact' == $view){
                        $display .= apply_filters( 'upme_compact_profile_after_head', '',$id);
                    }else{
                        $display .= apply_filters( 'upme_full_profile_after_head', '',$id);
                    }
                    // End Filters

                    $display .= '<div class="upme-main upme-main-' . $view . '">';

                    /* Display errors */
                    if (isset($_POST['upme-submit-' . $id])) {
                        $display .= $upme_save->get_errors($id);
                    }

                    $display .= $this->show_profile_fields($id, $view);
                    $display .= $this->edit_profile_fields($id, $width, $sidebar_class);

                    $display .= '</div>';

                    // Add dynamic AJAX  based forms into View Profile section
                    $form_params = array('id'=>$id,'view'=>$view);
                    $display .= apply_filters('upme_profile_view_forms','',$form_params);
                    /////////////////////////

                    /* UPME Filters for after profile head section */
                    $display .= apply_filters( 'upme_profile_after_fields', '' , $id);

                    if('compact' == $view){
                        $display .= apply_filters( 'upme_compact_profile_after_fields', '', $id);
                    }else{
                        $display .= apply_filters( 'upme_full_profile_after_fields', '', $id);
                    }
                    // End Filters

                    if ('1' == $current_options['show_recent_user_posts'] && 'no' != $recent_posts && !($view)) {
                        $post_limit = $current_options['maximum_allowed_posts'];
                        $feature_image_status = $current_options['show_feature_image_posts'];
                        $display .= $this->show_profile_posts($id, $post_limit, $feature_image_status, $view);
                    }                    

                    $display .= '</div>
                                 </div>';
                }


                // Display inline fancybox container and loading image, when modal window is not enables
                if('yes' != $modal_view && 'true' != $modal_view){
                    $display  .= '<div id="upme_inner_modal" style="display:none"></div>';
                    $display  .= '<div id="upme_inner_modal_loader" style="display:none"><img src="'.upme_url.'css/images/fancybox/fancybox_loading.gif" /></div>';
                }

                $display .= '</div>';
                
            } else {
                $display .= '<p>' . sprintf(__('Nothing found matching the selected criteria.', 'upme')) . '</p>';
            }
        } /* hide_until_search argument */ else {
            if (isset($this->no_search_input) && $this->no_search_input == true) {
                $display .= '<p>' . sprintf(__('Please enter search criteria.', 'upme')) . '</p>';
            }
        }


        /* pagination */
        $pagination_bar_bottom = '';
        if (!$hide_until_search) {
            if ($group == 'all') {
                if ($users_per_page) {
                    $display .= '<div class="upme-clear"></div>';
                    $pagination_bar_bottom .= $this->pagination($users_per_page, $group, $role);                     

                     /*  Show limited number of users from given list */
                    if ('true' == $limit_results || 'yes' == $limit_results ){
                        $pagination_bar_bottom  = '';
                    }

                    $display .= $pagination_bar_bottom;

                    if (!isset($this->upme_search) || (isset($this->upme_search) && $this->upme_search == false)) {
                        // Show Hidden Form in case there is no search form
                        $display.='<form action="" method="post" id="upme-pagination-form">';
                        $display.='<input type="hidden" name="userspage" id="upme-pagination-form-per-page" />';
                        $display.='</form>';
                    }
                }
            }
        }

        return $display;
    }

    /* Show user stats */

    function show_user_stats($id) {

        $author_posts_text = '';
        // Include the link to author posts page based on the setting in admin
        if ($this->get_option('link_author_posts_page') == '1') {
            $author_posts_url = get_author_posts_url($id);

            // Remove link for author who has no post entries
            if (0 == $this->get_entries_num($id)) {
                $author_posts_text = $this->get_entries_num($id);
            } else {
                $author_posts_text = '<a href="' . $author_posts_url . '">' . $this->get_entries_num($id) . '</a>';
            }
        } else {
            $author_posts_text = $this->get_entries_num($id);
        }

        $upme_stats_items = array(
                                'posts' => '<div class="upme-stats-i upme-stats-posts"><i class="upme-icon upme-icon-rss"></i><span class="upme-posts-link">' . $author_posts_text . '</span></div>',
                                'comments' => '<div class="upme-stats-i upme-stats-comments"><i class="upme-icon upme-icon-comments-alt"></i><span class="upme-comments-link">' . $this->get_comments_num($id) . '</span></div>',                    
                            );

        /* UPME Filter for customizing items in  profile stats section */
        $upme_stats_items = apply_filters('upme_stats_items',$upme_stats_items,$id);
        // End Filter

        $display  = '<div class="upme-stats">';
        foreach ($upme_stats_items as $key => $itm) {
            $display .= $itm;
        }
                   
        $display .= '</div>';

        return $display;
    }

    /* Can edit user profile */

    function can_edit_profile($logged_in, $profile_id) {
        if ($logged_in == $profile_id || ( current_user_can('edit_user', $profile_id) ))
            return true;
    }

    /* Bool user exists by ID */

    function user_exists($id) {
        $userdata = get_userdata($id);
        if ($userdata == false)
            return false;
        return true;
    }

    /* Get picture by ID */

    function pic($id, $size) {
        // Check the existance of image path in upload folder and remove the data
        // in case its not available
        $user_pic = get_the_author_meta('user_pic', $id);

        if ($upload_dir = upme_get_uploads_folder_details()) {
            $upme_upload_path = $upload_dir['basedir'] . "/upme/";
            $upme_upload_url = $upload_dir['baseurl'] . "/upme/";

            $user_pic_path = str_replace($upme_upload_url, $upme_upload_path, $user_pic);
            if (!file_exists($user_pic_path)) {
                delete_user_meta($id, 'user_pic');
                $user_pic = '';
            }
        }

        if ($user_pic != '') {
            return '<img id="upme-avatar-user_pic" src="' . $user_pic . '" class="avatar avatar-50" />';
        } else {
            return get_avatar($id, $size);
        }
    }

    /* Edit profile fields */

    function edit_profile_fields($id, $width=null, $sidebar_class=null) {

        global $predefined, $upme_roles, $upme_profile_fields;

        $this->upme_load_edit_form_scripts();

        if ($this->can_edit_profile($this->logged_in_user, $id)) {
            $display = null;

            // Set date format from admin settings
            $upme_settings = get_option('upme_options');
            $upme_date_format = (string) isset($upme_settings['date_format']) ? $upme_settings['date_format'] : 'mm/dd/yy';

            $display .= '<div id="upme-edit-form-err-holder" style="display: none;" class="upme-errors"></div>';

            // Change URL to remove query parameter for edit profile using modal 
            if(isset($_GET['upme_modal_target_link'])){
                $action  = remove_query_arg( 'upme_modal_target_link');
                $display .= '<form id="upme-edit-profile-form" class="upme-edit-profile-form" action="'.$action.'" method="post" enctype="multipart/form-data">';

            }else{
                $display .= '<form id="upme-edit-profile-form" class="upme-edit-profile-form" action="" method="post" enctype="multipart/form-data">';
            }
            


            /* Add mandatory profile fields, which are not avaiable in custom fields section */
            $display .= $upme_profile_fields->upme_frontend_mandatory_fields($upme_settings,$this->logged_in_user,$id);

            $array = get_option('upme_profile_fields');
            //echo "<pre>";print_r($array);exit;

            $edit_by_user_role_list = '';
            $show_to_user_role_list = '';

            
            $user_profile_form_name = get_user_meta($id,'upme-register-form-name',true);
            if( $user_profile_form_name == '' ){
                $user_profile_form_name = $this->profile_form_name;
            }

            /* UPME Filters for customizing profile form name */
            $profile_form_name_params = array('user_id' => $id , 'page_form_name' => $this->profile_form_name, 'profile_form_name' => $user_profile_form_name, 'width'=>$width);
            $user_profile_form_name = apply_filters( 'upme_profile_form_name', $user_profile_form_name, $profile_form_name_params);
            // End Filter

            /* UPME Filters for customizing profile display fields */
            $profile_fields_params = array('user_id' => $id, 'form_name' => $user_profile_form_name);
            $array = apply_filters( 'upme_profile_edit_fields', $array, $profile_fields_params);
            // End Filters

            foreach ($array as $key => $field) {

                extract($field);

                // WP 3.6 Fix
                if (!isset($deleted))
                    $deleted = 0;

                if (!isset($private))
                    $private = 0;

                // Set the default value for required attribute
                if (!isset($required))
                    $required = 0;
                // Assign the required class for required fields
                $required_class = '';
                $required_sign  = '';
                if ($required == 1 && in_array($field, $this->include_for_validation)) {
                    $required_class = ' required';
                    $required_sign  = '<span class="upme-required">&nbsp;*</span>';                        
                }


                $display_field = 0;

                $show_to_user_role = isset($show_to_user_role) ? $show_to_user_role : '0';
                $show_to_user_role_list = isset($show_to_user_role_list) ? $show_to_user_role_list : '';

                $upme_roles->upme_get_user_roles_by_id($id);
                $show_field_status = $upme_roles->upme_empty_fields_by_user_role($show_to_user_role, $show_to_user_role_list);

                $edit_by_user_role = isset($edit_by_user_role) ? $edit_by_user_role : '0';
                $edit_by_user_role_list = isset($edit_by_user_role_list) ? $edit_by_user_role_list : '';

                $upme_roles->upme_get_user_roles_by_id($id);
                $edit_field_status = $upme_roles->upme_fields_by_user_role($edit_by_user_role, $edit_by_user_role_list);
   
                //if ($edit_field_status) {
                // Checking wether to show field or not.
                if (current_user_can('manage_options')) {
                    // For admin Always allow
                    $display_field = 1;
                } else {
                    if ($field == 'password')
                        $display_field = 1;
                    else if ( ($field == 'fileupload' || in_array($field, $this->custom_file_field_types)) && $can_edit == 1) //
                        $display_field = 1;
                    else if ($private == 0 && ($field != 'fileupload' && !in_array($field, $this->custom_file_field_types)))
                        $display_field = 1;
                    else
                        $display_field = 0;
                }

                /* Separator */
                if ($type == 'separator' && $deleted == 0) {

                    // Hiding fields based on show by user role setting
                    if ($show_field_status) {
                        $display_show_status = true;
                    } else {
                        $display_show_status = false;
                    }

                    if ($display_show_status) {
                        $display .= '<div class="upme-field upme-separator upme-edit upme-clearfix upme-'.$meta.'">' . __($name,'upme') . '</div>';

                        /* UPME Filters for before registration head section */
                        $sep_params = array('meta' => $meta);
                        $display .= apply_filters( 'upme_edit_separator_after_text', '', $sep_params);
                        // End Filters
                    }
                }

                /* user meta - editing fields */
                if ($type == 'usermeta' && $deleted == 0 && $display_field == 1) {

                    // Hiding fields based on show by user role setting
                    if (current_user_can('manage_options') && $show_field_status) {
                        $display_show_status = true;
                    } else if (!$show_field_status) {
                        $display_show_status = false;
                    } else {
                        $display_show_status = true;
                    }


                    // Disabling fields based on editable by user role setting
                    if (current_user_can('manage_options') && $edit_field_status) {
                        $display_edit_status = true;
                    } else if (!$edit_field_status) {
                        $display_edit_status = false;
                    } else {
                        $display_edit_status = true;
                    }


                    if ($display_show_status) {

                        $display .= '<div class="upme-field upme-edit upme-'.$meta.'">';

                        /* Show the label */
                        if (isset($array[$key]['name']) && $name) {
                            $display .= '<label class="upme-field-type" for="' . $meta . '-' . $id . '">';
                            if (isset($array[$key]['icon']) && $icon) {
                                $display .= '<i class="upme-icon upme-icon-' . $icon . '"></i>';
                            } else {
                                $display .= '<i class="upme-icon upme-icon-none"></i>';
                            }
                            $display .= '<span>' . apply_filters('upme_edit_profile_label_' . $meta, __($name,'upme')) . '</span>' .$required_sign. '</label>';
                        } else {
                            if (isset($array[$key]['icon']) && $icon) {
                                $display .= '<label class="upme-field-type upme-field-type-width-' . $width . '" for="' . $meta . '-' . $id . '"><i class="upme-icon upme-icon-' . $icon . '"></i>' .$required_sign. '</label>';
                            } else {
                                $display .= '<label class="upme-field-type upme-field-type-width-' . $width . ' upme-field-type-' . $sidebar_class . '">&nbsp;' .$required_sign. '</label>';
                            }
                        }

                        $display .= '<div class="upme-field-value">';


                        // Checking if field should be editable or not
                        // For admin always allow
                        if (current_user_can('manage_options')) {
                            $disabled = null;
                        } 
                        else if(!$display_edit_status){
                            $disabled = 'disabled="disabled"';
                        }
                        else {
                            if ($can_edit == 0)
                                $disabled = 'disabled="disabled"';
                            else
                                $disabled = null;
                        }


                        switch ($field) {
                            case 'textarea':
                                $params = array('meta'=>$meta, 'name'=> $name, 'id' => $id);
                                $custom_editor_styles = apply_filters('upme_text_editor_styles','',$params);
                                $display .= '<textarea title="' . $name . '" ' . $disabled . ' class="upme-input ' . $required_class . ' '.$custom_editor_styles.' " name="' . $meta . '-' . $id . '" id="' . $meta . '-' . $id . '">' . get_the_author_meta($meta, $id) . '</textarea>';
                                break;

                            case 'text':
                                $display .= '<input title="' . $name . '" ' . $disabled . ' type="text" class="upme-input ' . $required_class . ' upme-edit-' . $meta . '" name="' . $meta . '-' . $id . '" id="' . $meta . '-' . $id . '" value="' . get_the_author_meta($meta, $id) . '" />';
                                break;

                            case 'datetime':
                                $formatted_date_value = '';
                                $date_values = esc_attr(get_the_author_meta($meta, $id));
                                if('' != $date_values){
                                    $formatted_date_value = upme_date_format_to_custom($date_values, $upme_date_format);
                                }
                                
                                $display .= '<input readonly="readonly" title="' . $name . '" ' . $disabled . ' type="text" class="upme-input upme-datepicker ' . $required_class . '" name="' . $meta . '-' . $id . '" id="' . $meta . '-' . $id . '" value="' . $formatted_date_value . '" />';
                                $display .= '<input type="button" class="upme-button-alt upme-datepicker-reset" value="'.__('Clear Date','upme').'" />';
                         
                                break;

                            case 'select':
                                $loop = array();
                                if (isset($array[$key]['predefined_loop']) && $array[$key]['predefined_loop'] != '' && $array[$key]['predefined_loop'] != '0') {

                                    $loop = $predefined->get_array($array[$key]['predefined_loop']);
                                    if ('countries' == $array[$key]['predefined_loop']) {
                                        array_shift($loop);
                                    }
                                } else if (isset($array[$key]['choices']) && $array[$key]['choices'] != '') {
                                    $loop = explode(PHP_EOL, $choices);
                                }

                                /* UPME filter for customizing select field values */
                                $select_field_custom_values_params = array('meta' => $meta, 'name' => $name, 'id' => $id );
                                $loop = apply_filters('upme_select_field_custom_values',$loop,$select_field_custom_values_params);
                                /* End filter */ 

                                if (isset($loop)) {

                                    $profile_user_meta = '';
                                    $profile_user_meta = html_entity_decode(get_the_author_meta($meta, $id));


                                    // Check for country loop
                                    $country_loop_status = isset($array[$key]['predefined_loop']) ? $array[$key]['predefined_loop'] : '';

                                    if ('' == $profile_user_meta && '' != $country_loop_status && 'countries' == $country_loop_status) {

                                        $profile_user_meta = $loop[$upme_settings['default_predefined_country']];
                                    }

                                    $display .= '<select title="' . $name . '" ' . $disabled . ' class="upme-input ' . $required_class . '" name="' . $meta . '-' . $id . '" id="' . $meta . '-' . $id . '">';
                                    $display .= '<option value="" ' . selected($profile_user_meta, "", 0) . '>' . __('Please Select', 'upme') . '</option>';
                                    foreach ($loop as $option) {
                                        // Added as per http://codecanyon.net/item/user-profiles-made-easy-wordpress-plugin/discussion/4109874?filter=All+Discussion&page=27#comment_4352415
                                        $option = upme_stripslashes_deep(trim($option));

                                        $display .= '<option value="' . $option . '" ' . selected($profile_user_meta, $option, 0) . '>' . $option . '</option>';
                                    }
                                    $display .= '</select>';
                                }
                                $display .= '<div class="upme-clear"></div>';
                                break;

                            case 'radio':
                                if (isset($array[$key]['choices'])) {
                                    $loop = explode(PHP_EOL, $choices);
                                }

                                /* UPME filter for customizing checkbox field values */
                                $radio_field_custom_values_params = array('meta' => $meta, 'name' => $name, 'id' => $id );
                                $loop = apply_filters('upme_radio_field_custom_values',$loop,$radio_field_custom_values_params);
                                /* End filter */ 

                                if (isset($loop) ) {
                                    foreach ($loop as $option) {
                                        // Added as per http://codecanyon.net/item/user-profiles-made-easy-wordpress-plugin/discussion/4109874?filter=All+Discussion&page=27#comment_4352415
                                        $option = upme_stripslashes_deep(trim($option));
                                        $display .= '<label class="upme-radio"><input title="' . $name . '" ' . $disabled . ' class="' . $required_class . '" type="radio" name="' . $meta . '-' . $id . '" value="' . $option . '" ' . checked(html_entity_decode(get_the_author_meta($meta, $id)), $option, 0);
                                        $display .= '/> ' . $option . '</label>';
                                    }
                                }
                                $display .= '<div class="upme-clear"></div>';
                                break;

                            case 'checkbox':
                                if (isset($array[$key]['choices'])) {
                                    $loop = explode(PHP_EOL, $choices);
                                }

                                /* UPME filter for customizing checkbox field values */
                                $checkbox_field_custom_values_params = array('meta' => $meta, 'name' => $name, 'id' => $id );
                                $loop = apply_filters('upme_checkbox_field_custom_values',$loop,$checkbox_field_custom_values_params);
                                /* End filter */ 

                                if (isset($loop) ) {
                                    foreach ($loop as $option) {
                                        // Added as per http://codecanyon.net/item/user-profiles-made-easy-wordpress-plugin/discussion/4109874?filter=All+Discussion&page=27#comment_4352415
                                        $option = upme_stripslashes_deep(trim($option));
                                        //echo $option;
                                        
                                        $display .= '<label class="upme-checkbox"><input title="' . $name . '" ' . $disabled . ' class="' . $required_class . '" type="checkbox" name="' . $meta . '-' . $id . '[]" value="' . $option . '" ';
                                        $values = explode(', ', html_entity_decode(get_the_author_meta($meta, $id)));
                                        if (in_array($option, $values)) {
                                            $display .= 'checked="checked"';
                                        }
                                        $display .= '/> ' . $option . '</label>';
                                    }
                                }
                                $display .= '<div class="upme-clear"></div>';
                                break;

                            case 'password':
                                $display .= '<input title="' . $name . '" ' . $disabled . ' type="password" class="upme-input ' . $required_class . ' upme-edit-' . $meta . '" name="' . $meta . '-' . $id . '" id="' . $meta . '-' . $id . '" value="" autocomplete="off"  />';

                                if ($meta == 'user_pass') {
                                    $display .= '<div class="upme-help">' . __('If you would like to change the password type a new one. Otherwise leave this blank.', 'upme') . '</div>';
                                } elseif ($meta == 'user_pass_confirm') {
                                    $display .= '<div class="upme-help">' . __('Type your new password again.', 'upme') . '</div>';
                                    $display .='<div class="password-meter"><div id="password-meter-message" class="password-meter-message">' . __('Strength Indicator', 'upme') . '</div></div>';
                                }

                                break;

                            case 'fileupload':

                                if ($meta == 'user_pic') {

                                    // Include removal link for profile images
                                    $display_delete_link = '<div class="upme-delete-userpic-wrapper" upme-data-field-name="' . $meta . '" upme-data-user-id="' . $id . '"><i class="upme-icon upme-icon-remove" original-title="remove"></i> <label class="upme-delete-image"  >' . __('Delete Image', 'upme') . '</label></div>';
                                    $display_delete_link .= '<div id="upme-spinner-' . $meta . '" class="upme-delete-spinner"><i original-title="spinner" class="upme-icon upme-icon-spinner upme-tooltip3"></i><label>' . __('Loading', 'upme') . '</label></div>';


                                    $display .= '<div id="upme-current-picture" class="upme-note"><strong>' . __('Current Picture', 'upme') . ':</strong></div>';
                                    if (get_the_author_meta('user_pic', $id) != '') {
                                        $display .= '<div class="upme-note upme-current-pic-note"><img id="upme-preview-user_pic" src="' . get_the_author_meta('user_pic', $id) . '" alt="" />' . $display_delete_link . '</div>';
                                    } else {
                                        $display .= '<div class="upme-note upme-current-pic-note">' . get_avatar($id, 50) . '</div>';
                                        $display .= '<div class="upme-note upme-current-pic-note">' . __('You can sign up at <a href="http://en.gravatar.com/">Gravatar</a> to have a globally recognized avatar or upload a custom profile picture below.', 'upme') . '</div><div class="upme-clear"></div>';
                                    }
                                } else {

                                    // Include removal link for profile images
                                    $display_delete_link = '<div class="upme-delete-image-wrapper" upme-data-field-name="' . $meta . '" upme-data-user-id="' . $id . '"><i class="upme-icon upme-icon-remove" original-title="remove"></i> <label class="upme-delete-image"  >' . __('Delete Image', 'upme') . '</label></div>';
                                    $display_delete_link .= '<div id="upme-spinner-' . $meta . '" class="upme-delete-spinner"><i original-title="spinner" class="upme-icon upme-icon-spinner upme-tooltip3"></i><label>' . __('Loading', 'upme') . '</label></div>';


                                    if (get_the_author_meta($meta, $id) != '') {
                                        $display_fileupload_field = '<div class="upme-note"><img src="' . get_the_author_meta($meta, $id) . '" alt="" />' . $display_delete_link . '</div>';
                                        
                                        $display .= apply_filters('upme_edit_non_image_fileupload_fields',$display_fileupload_field,$id,$meta,$display_delete_link);
                                    }
                                }

                                // Showing default file upload control for Opera and Safari
                                $uploader_box_url = admin_url('admin-ajax.php') . '?action=upme_initialize_upload_box&upme_disabled=' . $disabled . '&upme_meta=' . $meta . '&upme_id=' . $id . '&TB_iframe=true&width=720&height=530&scrolling=no';

                                $display_upload_btn = '<div class="clear"></div>';
                                $display_upload_btn .= '<a id="user-avatar-link" class="fancybox fancybox.iframe" href="' . $uploader_box_url . '">';
                                //$display_upload_btn .= '<a id="user-avatar-link" class="thickbox" href="' . $uploader_box_url . '"  >';
                                $display_upload_btn .= '<input type="button" name="' . $meta . '-' . $id . '" id="file_' . $meta . '-' . $id . '" class="upme-init-uploadbox upme-button-alt-wide upme-fire-editor" value="' . __('Update Image', 'upme') . '"></a>';

                                if (is_safari() || is_opera()) {

                                    if ($meta == 'user_pic') {
                                        $display .= $display_upload_btn;
                                    } else {
                                        $display .= '<input title="' . $name . '" ' . $disabled . ' type="file" name="' . $meta . '-' . $id . '" id="file_' . $meta . '-' . $id . '" style="display:block;" />';
                                    }
                                } else {

                                    if ($meta == 'user_pic' && '1' == $this->get_option('lightbox_avatar_cropping')) {
                                        $display .= $display_upload_btn;
                                    } else {
                                        $display .= '<input title="' . $name . '" class="upme-fileupload-field ' . $required_class . '" ' . $disabled . ' type="file" name="' . $meta . '-' . $id . '" id="file_' . $meta . '-' . $id . '" style="display:block;" />';
                                    }
                                }


                                break;

                            case 'video':
                                $display .= '<input title="' . $name . '" ' . $disabled . ' type="text" class="upme-input ' . $required_class . ' upme-edit-' . $meta . '" name="' . $meta . '-' . $id . '" id="' . $meta . '-' . $id . '" value="' . get_the_author_meta($meta, $id) . '" />';
                                break;

                            case 'soundcloud':
                                $display .= '<input title="' . $name . '" ' . $disabled . ' type="text" class="upme-input ' . $required_class . ' upme-edit-' . $meta . '" name="' . $meta . '-' . $id . '" id="' . $meta . '-' . $id . '" value="' . get_the_author_meta($meta, $id) . '" />';
                                break;

                            default:
                                /* UPME Filter for showing custom field types in frontend edit mode */
                                $edit_custom_field_type_input_params = array('name' => $name, 'user_id' => $id, 'disabled' => $disabled, 'required_class' => $required_class, 'meta' => $meta, 'field' => $field,'value' => get_the_author_meta($meta, $id),'array' => $array[$key]);
                                $display .= apply_filters('upme_edit_custom_field_type_input','', $edit_custom_field_type_input_params);                   
                                // End filter
                                break;
                        }



                        if (isset($help_text) && !empty($help_text)) {
                            $display .= '<div class="upme-help-text upme-help">' . esc_html($help_text) . '</div>';
                        }

                        /* User can hide this from public */
                        if (isset($array[$key]['can_hide']) && $can_hide == 1) {

                            /* user hide from public */
                            if (get_the_author_meta('hide_' . $meta, $id) == 1) {
                                $class = 'upme-icon upme-icon-check-square-o';
                            } else {
                                $class = 'upme-icon upme-icon-square-o';
                            }

                            $display .= '<div class="upme-hide-from-public">
                        <i class="' . $class . '"></i>' . __('Hide from Public', 'upme') . '
                        <input type="hidden" name="hide_' . $meta . '-' . $id . '" id="hide_' . $meta . '-' . $id . '" value="' . get_the_author_meta('hide_' . $meta, $id) . '" />
                        </div>';
                        } elseif ($can_hide == 0 && $private == 0) {
                            //$display .= '<div class="upme-hide-from-public upme-disable">
                            //              '.sprintf(__('%s must be publicly visible.','upme'), $name).'
                            //          </div>';
                        }



                        $display .= '</div>';

                        $display .= '</div><div class="upme-clear"></div>';
                    }
                }
                //}

                $edit_by_user_role_list = '';
                $show_to_user_role_list = '';
            }

            $user_info = get_userdata($id);
            $usr_login = isset($user_info->user_login) ? $user_info->user_login : '0';

            /* UPME Filters for adding extra fields or hidden data in profile edit form */
            $params = array( 'user_id' => $id );
            $display .= apply_filters('upme_profile_edit_form_extra_fields','',$params);
            // End Filter

            $display .= '<div class="upme-field upme-edit">
            <label class="upme-field-type upme-field-type-width-' . $width . ' upme-field-type-' . $sidebar_class . '">&nbsp;</label>
            <div class="upme-field-value">
            <input type="hidden" id="upme-edit-usr-login" value="' . $usr_login . '" />
            <input type="hidden" id="upme-edit-usr-id" value="' . $id . '" />
            <input type="hidden" name="upme-submit-' . $id . '" value="' . $id . '" />
            <input type="submit" name="upme-submit-' . $id . '" class="upme-button" value="' . __('Update Profile', 'upme') . '" />
            </div>
            </div><div class="upme-clear"></div>';

            $display .= '</form>';

            return $display;
        }
    }

    /* user flag */

    function user_flag($meta, $id) {
        global $predefined;
        $user_country = get_the_author_meta($meta, $id);
        $countries = $predefined->get_array('countries');

        if ($user_country == '0' || $user_country == '' || $user_country == 'Select Country') {
            return 'No Country Selected';
        } else {
            foreach ($countries as $code => $country) {
                if ($country == $user_country) {
                    return '<img src="' . upme_url . 'img/assets/flags/' . strtolower($code) . '.png" class="upme-img-normal" />';
                }
            }
        }
    }

    /* Display profile fields */

    function show_profile_fields($id, $view) {
        global $upme_roles,$wp_roles;

        $role_names = $wp_roles->role_names;

        $display = null;

        $fullname = null;

        // Set date format from admin settings
        $upme_settings = get_option('upme_options');
        $upme_date_format = (string) isset($upme_settings['date_format']) ? $upme_settings['date_format'] : 'mm/dd/yy';


        $profile_fields = get_option('upme_profile_fields');

        /* If user specified view (specific fields)
          It should be included (filter profile fields
          to show only these fields */
        if ($view) {
            $view_fields = explode(',', $view);

            foreach ($profile_fields as $key => $array) {
                if (!in_array($key, $view_fields) && (isset($array['meta']) && !in_array($array['meta'], $view_fields))) {
                    unset($profile_fields[$key]);
                }
            }
        }

        /* Done filtering */
        // Showing ID
        if ($upme_settings['display_profile_status'] || ('true' == $this->profile_show_status || 'yes' == $this->profile_show_status) ) {

            $profile_status_label = apply_filters('upme_profile_status_label',__('Profile Status', 'upme'));
            $current_profile_status = esc_attr(get_the_author_meta('upme_user_profile_status', $id));

            $display.='<div class="upme-field upme-view">';
            $display.='<div class="upme-field-type"><i class="upme-icon upme-icon-user"></i><span>' . $profile_status_label . '</span></div>';
            $display.='<div class="upme-field-value"><span>' . $current_profile_status . '</span></div>';
            $display.='</div>';
            $display.='<div class="upme-clear"></div>';
        }

        // Showing ID
        if ($this->profile_show_id == 'true' || $this->profile_show_id == 'yes') {

            $user_id_label = apply_filters('upme_profile_id_label',__('User ID', 'upme'));

            $display.='<div class="upme-field upme-view">';
            $display.='<div class="upme-field-type"><i class="upme-icon upme-icon-user"></i><span>' . $user_id_label . '</span></div>';
            $display.='<div class="upme-field-value"><span>' . $id . '</span></div>';
            $display.='</div>';
            $display.='<div class="upme-clear"></div>';
        }

        // Showing Role
        if ($this->profile_show_role == 'true' || $this->profile_show_role == 'yes') {

            // Display user role name using user role key
            $user_roles = $upme_roles->upme_get_user_roles_by_id($id);
            if(is_array($user_roles)){
                foreach ($user_roles as $key => $value) {
                    $user_roles[$key] = $role_names[$value];
                }
                $user_roles = implode(',', $user_roles);
            }else{
                $user_roles = $role_names[$user_roles];
            }

            
            $user_role_label = apply_filters('upme_profile_role_label',__('User Role', 'upme'));

            $display.='<div class="upme-field upme-view">';
            $display.='<div class="upme-field-type"><i class="upme-icon upme-icon-user"></i><span>' . $user_role_label . '</span></div>';
            $display.='<div class="upme-field-value"><span>' . $user_roles . '</span></div>';
            $display.='</div>';
            $display.='<div class="upme-clear"></div>';
        }

        $user_profile_form_name = get_user_meta($id,'upme-register-form-name',true);
        if( $user_profile_form_name == '' ){
            $user_profile_form_name = $this->profile_form_name;
        }

        /* UPME Filters for customizing profile form name */
        $profile_form_name_params = array('user_id' => $id , 'view' => $view, 'page_form_name' => $this->profile_form_name, 'profile_form_name' => $user_profile_form_name);
        $user_profile_form_name = apply_filters( 'upme_profile_form_name', $user_profile_form_name, $profile_form_name_params);
        // End Filter

        /* UPME Filters for customizing profile display fields */
        $profile_fields_params = array('user_id' => $id, 'form_name' => $user_profile_form_name, 'view' => $view);
        $profile_fields = apply_filters( 'upme_profile_display_fields', $profile_fields, $profile_fields_params);
        // End Filters

        /* echo "<pre>";
          print_r($profile_fields); die; */
        foreach ($profile_fields as $key => $field) {
            //echo "<pre>";
            //print_r($profile_fields);exit;
            $field_params = $field;
            extract($field);

            // WP 3.6 Fix
            if (!isset($deleted))
                $deleted = 0;

            if (!isset($private))
                $private = 0;

            /* Displaying separators or labels for fields which has empty values */
            if ($type == 'separator' || ($type == 'usermeta' && $field != 'password' && get_the_author_meta($meta, $id) == '')) {
                if ($type == 'separator') {

                    $show_to_user_role = isset($show_to_user_role) ? $show_to_user_role : '0';
                    $show_to_user_role_list = isset($show_to_user_role_list) ? $show_to_user_role_list : '';

                    $upme_roles->upme_get_user_roles_by_id($id);
                    $show_field_status = $upme_roles->upme_empty_fields_by_user_role($show_to_user_role, $show_to_user_role_list);


                    if ($show_field_status && $this->get_option('show_separator_on_profile') == '1') {
                        $display .='<div class="upme-field upme-separator upme-view upme-clearfix upme-'.$meta.'">' . __($field['name'],'upme') . '</div>';

                        /* UPME Filters for before registration head section */
                        $sep_params = array('meta' => $meta);
                        $display .= apply_filters( 'upme_view_separator_after_text', '', $sep_params);
                        // End Filters
                    }
                } else {
                    if ($this->get_option('show_empty_field_on_profile') == '1') {

                        $profile_fields_icon = isset($profile_fields[$key]['icon']) ? $profile_fields[$key]['icon'] : '';
                        $icon = isset($icon) ? $icon : '';
                        $name = isset($name) ? $name : '';

                        $show_to_user_role = isset($show_to_user_role) ? $show_to_user_role : '0';
                        $show_to_user_role_list = isset($show_to_user_role_list) ? $show_to_user_role_list : '';

                        $upme_roles->upme_get_user_roles_by_id($id);
                        $show_field_status = $upme_roles->upme_empty_fields_by_user_role($show_to_user_role, $show_to_user_role_list);

                        if ($show_field_status) {
                            $display .= '<div class="upme-field upme-view upme-'.$meta.'">';
                            $display.= '<div class="upme-field-type">';

                            if (isset($profile_fields_icon) && $icon) {
                                $display .= '<i class="upme-icon upme-icon-' . $icon . '"></i>';
                            } else {
                                $display .= '<i class="upme-icon upme-icon-none"></i>';
                            }
                            $display.= '<span>' . apply_filters('upme_profile_label_' . $meta, __($name,'upme')) . '</span>';
                            $display .= '</div>';

                            $display.= '<div class="upme-field-value">';
                            $display.= '<span>' . apply_filters('upme_profile_blank_value_' . $meta, '-') . '</span>';
                            $display.= '</div>';

                            $display .= '</div><div class="upme-clear"></div>';
                        }
                    }
                }
            }
            
            /* Displaying labels for fields which are not empty */
            
            if ($type == 'usermeta' && get_the_author_meta($meta, $id) != '' && $deleted == 0) {
                if ($social == 0 || ( $social == 1 && $meta == 'user_email' ) || !isset($profile_fields[$key]['social'])) {
                    /* Do not show private fields */
                    if ($private == 0 || ($private == 1 && current_user_can('manage_options') )) {

                        $show_to_user_role = isset($show_to_user_role) ? $show_to_user_role : '0';
                        $show_to_user_role_list = isset($show_to_user_role_list) ? $show_to_user_role_list : '';

                        $upme_roles->upme_get_user_roles_by_id($id);
                        $show_field_status = $upme_roles->upme_fields_by_user_role($show_to_user_role, $show_to_user_role_list);

                        if ($show_field_status) {
                            if (2 != $can_hide || ( 2 == $can_hide && $this->can_edit_profile($this->logged_in_user, $id) )) {
                                if (get_the_author_meta('hide_' . $meta, $id) == 0 || ( get_the_author_meta('hide_' . $meta, $id) == 1 && $this->can_edit_profile($this->logged_in_user, $id) )) {

                                    $profile_value_params = array('field_meta' => $field_params, 'user_id' => $id);

                                    if ($meta == 'first_name') {
                                        $display .= '<div class="upme-field upme-view upme-'.$meta.'">
                                    <div class="upme-field-type">';

                                        if (isset($profile_fields[$key]['icon']) && $icon) {
                                            $display .= '<i class="upme-icon upme-icon-' . $icon . '"></i>';
                                        } else {
                                            $display .= '<i class="upme-icon upme-icon-none"></i>';
                                        }


                                        $display .= '<span>' . apply_filters('upme_profile_label_' . $meta, __('Name', 'upme')) . '</span></div>
                                    <div class="upme-field-value"><span>' . apply_filters('upme_profile_value_' . $meta, $this->get_user_name($id), $profile_value_params) . '</span></div>
                                    </div><div class="upme-clear"></div>';
                                    } elseif ($meta == 'last_name') {
                                        
                                    } else {

                                        /* Do not show these fields */
                                        if ($meta == 'display_name')
                                            continue;
                                        if ($meta == 'user_pass')
                                            continue;
                                        if ($meta == 'user_pass_confirm')
                                            continue;

                                        if ($meta == 'user_pic'){
                                            
                                            /* UPME Filters for displayig profile picture along with custom fields */
                                            $profile_display_user_pic_params = array('user_id' => $id, 'meta' => $meta, 'view' => $view, 'name'=>$name);
                                            $display .= apply_filters( 'upme_profile_display_user_pic', '', $profile_display_user_pic_params);
                                            // End Filters
                                            continue;
                                        }

                                        /* Show these fields */
                                        $display .= '<div class="upme-field upme-view upme-'.$meta.'">
                                    <div class="upme-field-type">';

                                        if (isset($profile_fields[$key]['icon']) && $icon) {
                                            $display .= '<i class="upme-icon upme-icon-' . $icon . '"></i>';
                                        } else {
                                            $display .= '<i class="upme-icon upme-icon-none"></i>';
                                        }


                                        $display .= '<span>' . apply_filters('upme_profile_label_' . $meta, $name) . '</span></div>
                                    <div class="upme-field-value">';

                                        if ($field == 'fileupload') {

                                            $display_fileupload_field = '<img src="' . get_the_author_meta($meta, $id) . '" alt="" />';
                                            $display .= apply_filters('upme_view_non_image_fileupload_fields',$display_fileupload_field,$id,$meta,$this->logged_in_user);


                                        } else if ($field == 'datetime') {
                                            $display .= '<span>';

                                            $date_time_value = get_the_author_meta($meta, $id);
                                            $display .= apply_filters('upme_profile_value_' . $meta, upme_date_format_to_custom($date_time_value, $upme_date_format), $profile_value_params);


                                            $display .= '</span>';
                                        } else if ($field == 'video') {

                                            $video_url = get_the_author_meta($meta, $id);
                                            $player_details = upme_video_type_css($video_url);
                                            $player_url = upme_video_url_customizer($video_url);

                                            $player_url = apply_filters('upme_profile_value_' . $meta, $player_url,$profile_value_params);

                                            $display .= '<div class="upme-video-container">';
                                            $display .= '<iframe  width="' . $player_details['width'] . '" height="' . $player_details['height'] . '" src="' . $player_url . '" frameborder="0" allowfullscreen ></iframe>';
                                            $display .= '</div>';

                                        } else if ($field == 'soundcloud') {

                                            $soundcloud_url = get_the_author_meta($meta, $id);
                                            $soundcloud_url = apply_filters('upme_profile_value_' . $meta, $soundcloud_url,$profile_value_params);

                                            $sound_cloud_player = upme_sound_cloud_player($soundcloud_url);

                                            $display .= '<div class="upme-sound-container upme-sound-cloud-container">';
                                            $display .= $sound_cloud_player;
                                            $display .= '</div>';
                                        
                                        } 
                                        else if (in_array($field, apply_filters('upme_user_defined_custom_field_types', array(), array() ) ) ) {

                                            /* UPME Filters for customizing head section */
                                            $view_custom_field_type_input_params = array('user_id' => $id, 'meta' => $meta, 'field'=> $field ,'value' => get_the_author_meta($meta, $id));
                                            $display .= apply_filters( 'upme_view_custom_field_type_input', '' , $view_custom_field_type_input_params);
                                            // End Filters
                                        
                                        } 
                                        else {


                                            if (isset($profile_fields[$key]['allow_html']) && $allow_html == 1) {
                                                $html_field_value = apply_filters('upme_profile_value_' . $meta, get_the_author_meta($meta, $id),$profile_value_params);
                                                $display .= str_replace("\r\n", "<br />", html_entity_decode($html_field_value));
                                            } else {
                                                $display .= '<span>';

                                                /* Append country with flag */
                                                if (isset($profile_fields[$key]['predefined_loop']) && $predefined_loop == 'countries') {
                                                    $display .= $this->user_flag($meta, $id);
                                                }

                                                if ($meta == 'user_url' && $this->get_option('website_link_on_profile') == 1) {
                                                    $display .= '<a rel="external nofollow" target="_blank" href="' . get_the_author_meta($meta, $id) . '">' . apply_filters('upme_profile_value_' . $meta, get_the_author_meta($meta, $id),$profile_value_params) . '</a>';
                                                } else {
                                                    $display .= apply_filters('upme_profile_value_' . $meta, get_the_author_meta($meta, $id), $profile_value_params);

                                                }


                                                $display .= '</span>';
                                            }
                                        }

                                        $display .= '</div>
                                    </div><div class="upme-clear"></div>';
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $display;
    }

    /* Get social profiles of user */

    function show_user_social_profiles($id,$params=array()) {
        global $upme_roles;

        $display = null;
        $array = get_option('upme_profile_fields');
        
        $tag = 'div';
        if(isset($params['tag'])){
             $tag = $params['tag'];
        }
        
        $sub_tag = 'div';
        if(isset($params['sub_tag'])){
             $sub_tag = $params['sub_tag'];
        }
        
        $display .= '<'.$tag.' class="upme-social">';
        $profile_fields = get_option('upme_profile_fields');
        foreach ($profile_fields as $key => $field) {
            extract($field);

            if (!isset($private))
                $private = 0;

            /* Do not show private fields */
            if ($private == 0 || ($private == 1 && current_user_can('manage_options') )) {

                $show_to_user_role = isset($show_to_user_role) ? $show_to_user_role : '0';
                $show_to_user_role_list = isset($show_to_user_role_list) ? $show_to_user_role_list : '';

                $upme_roles->upme_get_user_roles_by_id($id);
                $show_field_status = $upme_roles->upme_fields_by_user_role($show_to_user_role, $show_to_user_role_list);

                if($show_field_status){

                    if ($type == 'usermeta' && isset($profile_fields[$key]['social']) && $social == 1 && get_the_author_meta($meta, $id) != '' && ( get_the_author_meta('hide_' . $meta, $id) == 0 || ( get_the_author_meta('hide_' . $meta, $id) == 1 && $this->can_edit_profile($this->logged_in_user, $id) ) )) {

                        $display .= '<'. $sub_tag .' class="upme-' . $icon . '"><a target="_blank" rel="external nofollow" href="';

                        $display .= apply_filters('upme_social_url_' . $meta, get_the_author_meta($meta, $id));

                        $display .= '"';
                        if (isset($array[$key]['tooltip']) && $tooltip) {
                            $display .= ' class="upme-tooltip" title="' . $tooltip . '"';
                        }
                        $display .= '><i class="upme-icon upme-icon-' . $icon . '"></i></a></'. $sub_tag .'>';
                    }
                }
            }
        }
        $display .= '</'. $tag .'><div class="upme-clear"></div>';
        return $display;
    }

    /* Get full name of user */

    function get_user_name($id) {
        $name = null;

        $fname_hide_status = get_the_author_meta('hide_first_name', $id);
        $lname_hide_status = get_the_author_meta('hide_last_name', $id);

        if (get_the_author_meta('first_name', $id) || get_the_author_meta('last_name', $id)) {
            if (get_the_author_meta('first_name', $id) != '' && $fname_hide_status == 0) {
                $name .= get_the_author_meta('first_name', $id) . ' ';
            }
            if (get_the_author_meta('last_name', $id) != '' && $lname_hide_status == 0) {
                $name .= get_the_author_meta('last_name', $id);
            }
        }
        return $name;
    }

    /* Get number of entries */

    function get_entries_num($id) {
        $count = count_user_posts($id);
        if ($count == 1) {
            return sprintf(__('%s entry', 'upme'), $count);
        } else {
            return sprintf(__('%s entries', 'upme'), $count);
        }
    }

    /* Get number of comments */

    function get_comments_num($id) {
        $args = array('user_id' => $id);
        $comments = get_comments($args);
        $count = count($comments);
        if ($count == 1) {
            return sprintf(__('%s comment', 'upme'), $count);
        } else {
            return sprintf(__('%s comments', 'upme'), $count);
        }
    }

    /* Post value */

    function post_value($meta) {
        global $upme_register;

        if (isset($_POST['upme-register-form'])) {
            if (isset($_POST[$meta])) {
                return $_POST[$meta];
            }
        } else {
            if (strstr($meta, 'country')) {
                return 'United States';
            }
        }
    }

    /* Show registration form */

    function show_registration($args=array()) {

        global $post, $upme_register;


        // Loading scripts and styles only when required
        /* Password Stregth Checker Script */
        if (!wp_script_is('form-validate')) {
            wp_register_script('form-validate', upme_url . 'js/form-validate.js', array('jquery'));
            wp_enqueue_script('form-validate');

            $validate_strings = upme_form_validate_setting();

            wp_localize_script('form-validate', 'Validate', $validate_strings);

        }

        // Include password strength meter from WordPress core
        wp_enqueue_script('password-strength-meter');

        if (!wp_style_is('upme_password_meter')) {
            wp_register_style('upme_password_meter', upme_url . 'css/password-meter.css');
            wp_enqueue_style('upme_password_meter');
        }

        if (!wp_style_is('upme_date_picker')) {
            wp_register_style('upme_date_picker', upme_url . 'css/upme-datepicker.css');
            wp_enqueue_style('upme_date_picker');
        }


        if (!wp_script_is('upme_date_picker_js')) {
            wp_register_script('upme_date_picker_js', upme_url . 'js/upme-datepicker.js', array('jquery'));
            wp_enqueue_script('upme_date_picker_js');

            // Set date picker default settings
            $date_picker_array = upme_date_picker_setting();

            wp_localize_script('upme_date_picker_js', 'UPMEDatePicker', $date_picker_array);
        }

        do_action('upme_add_registration_scripts');

        /* Arguments */
        $defaults = array(
            'use_in_sidebar' => null,
            //'redirect_to' => $this->current_page
            'redirect_to' => null,
            'captcha' => 'no',
            'name' => wp_generate_password(12,false),
            'user_role' => null
        );

        $args = wp_parse_args($args, $defaults);
        extract($args, EXTR_SKIP);

        $pic_class = 'upme-pic';
        if (is_safari ())
            $pic_class = 'upme-pic safari';

        // Default set to blank
        $this->captcha = '';
        if (isset($captcha))
            $this->captcha = $captcha;

        $sidebar_class = null;
        if ($use_in_sidebar)
            $sidebar_class = 'upme-sidebar';

        /*  Set registration user role on individual registration forms */
        $this->registration_user_role = $user_role;
        /*  Set form name for allowing multiple registration forms through filters */
        $this->registration_form_name = $name;

        $display = null;


        if (get_option('users_can_register') == '1') {

            /* Get errors */
            if (isset($_POST['upme-register-form'])) {
                $display_errors = $upme_register->get_errors();
            }

            $display .= '<div id="upme-registration" class="upme-wrap upme-registration ' . $sidebar_class . '">
            <div class="upme-inner upme-registration-wrapper upme-clearfix">';


            // Display the head section for default screen and error messages
            if (!isset($display_errors['status']) || (isset($display_errors['status']) && ("error" == $display_errors['status']))) {

                /* UPME Filters for before registration head section */
                $display .= apply_filters( 'upme_register_before_head', '');
                // End Filters

                $display_head = '<div class="upme-head">
                 
                                <div class="upme-left">
                                    <div class="' . $pic_class . '">';

                if (isset($_POST['upme-register']) && $_POST['user_email'] != '') {
                    $display_head .= $this->pic($_POST['user_email'], 50);
                } else {
                    $display_head .= $this->pic('john@doe.com', 50);
                }

                $display_head .= '       </div>';

                $display_head .= '       <div class="upme-name">
                                        <div class="upme-field-name upme-field-name-wide">';

                if (isset($_POST['upme-register']) && $_POST['display_name'] != '') {
                    $display_head .= $_POST['display_name'];
                } else {
                    $display_msg = __('Your display name will appear here.', 'upme');
                    $display_head .= apply_filters('upme_profile_head_default_message',$display_msg);
                }

                $display_head .= '           </div>
                                    </div>';

                $display_head .= '   </div>';


                $display_head .= '<div class="upme-right"></div>';

                $display_head .= '<div class="upme-clear"></div>
                 
                </div>';

                /* UPME Filters for customizing head section */
                $registration_head_params = array();
                $display .= apply_filters( 'upme_registration_head', $display_head , $registration_head_params);
                // End Filters

            }

            /* UPME Filters for after registration head section */
            $display .= apply_filters( 'upme_register_after_head', '');
            // End Filters

            $display .= '               <div class="upme-main">
             
            <div class="upme-errors" style="display:none;" id="pass_err_holder">
            <span class="upme-error upme-error-block" id="pass_err_block">
            <i class="upme-icon upme-icon-remove"></i>Please enter a username.
            </span>
            </div>
            ';

            /* Display errors */
            if (isset($_POST['upme-register-form'])) {
                $display .= $display_errors['display'];
            }

            $display .= $this->show_register_form($sidebar_class, $redirect_to, $user_role, $this->registration_form_name);

            $display .= '</div>';

            /* UPME Filters for after registration fields section */
            $display .= apply_filters( 'upme_register_after_fields', '');
            // End Filters

            $display .= '</div>
            </div>';
        } else {
            $display .= '<div id="upme-registration" class="upme-wrap upme-registration ' . $sidebar_class . '"><div class="upme-inner upme-clearfix"><div class="upme-head">';
            if ($this->get_option('html_registration_disabled') != '')
                $display.=$this->get_option('html_registration_disabled');
            else
                $display.=__('User registration is currently not allowed.', 'upme');

            $display .= '</div></div></div>';
        }

        return $display;
    }

    /* Display registration form */

    function show_register_form($sidebar_class=null, $redirect_to=null, $user_role=null, $registration_form_name=null ) {
        global $upme_register, $predefined, $upme_captcha_loader;
        $display = null;

        /* Get end of array */
        $array = get_option('upme_profile_fields');

        $current_option = get_option('upme_options');
        $allow_user_role_registration = $current_option['select_user_role_in_registration'];

        // Optimized condition and added strict conditions
        if (!isset($upme_register->registered) || $upme_register->registered != 1) {

            $display .= '<form action="" method="post" id="upme-registration-form">';

            $display .= '<div class="upme-field upme-separator upme-edit upme-edit-show upme-clearfix upme-account_info_separator">' . __('Account Info', 'upme') . '</div>';

            /* UPME Filters for customizing registration fields */
            $registration_fields_params = array('user_role' => $user_role, 'form_name' => $registration_form_name, 'sidebar_class' => $sidebar_class);
            $this->registration_fields = apply_filters( 'upme_registration_fields', $this->registration_fields, $registration_fields_params);
            // End Filters

            /* Add Account Information Fields to top of Registration fields */
            foreach ($this->registration_fields as $key => $field) {

                /* Remove user role selection when user role is forced */
                if($user_role && isset($field['meta']) && $field['meta'] == 'user_role'){
                    continue;
                }

                extract($field);

                if ($type == 'usermeta') {

                    if (!('user_role' == $meta && !$allow_user_role_registration)) {

                        if (!isset($required))
                            $required = 0;

                        $required_class = '';
                        $required_sign  = '';
                        if ($required == 1 && in_array($field, $this->include_for_validation)) {
                            $required_class = ' required';
                            $required_sign  = '<span class="upme-required">&nbsp;*</span>';
                        }

                        $display .= '<div class="upme-field upme-edit upme-edit-show upme-'.$meta.'">';

                        /* Show the label */
                        if (isset($this->registration_fields[$key]['name']) && $name) {

                            $display .= '<label class="upme-field-type" for="' . $meta . '">';


                            if (isset($this->registration_fields[$key]['icon']) && $icon) {
                                $display_icon = '<i class="upme-icon upme-icon-' . $icon . '"></i>';
                            } else {
                                $display_icon = '<i class="upme-icon upme-icon-none"></i>';
                            }

                            /* UPME Filters for customizing registration fields icons */
                            $registration_field_icon_params = array('meta'=> $meta, 'name'=>$name, 'field'=> $field);
                            $display .= apply_filters( 'upme_registration_field_icon',$display_icon, $registration_field_icon_params);
                            // End Filters


                            $display .= '<span>' . apply_filters('upme_registration_label_' . $meta, $name) . '</span>' . $required_sign . '</label>';
                        } else {
                            $display .= '<label class="upme-field-type">&nbsp;' . $required_sign . '</label>';
                        }



                        $display .= '<div class="upme-field-value">';

                        switch ($field) {


                            case 'textarea':
                                $params = array('meta'=>$meta, 'name'=> $name);
                                $custom_editor_styles = apply_filters('upme_text_editor_styles','',$params);

                                $display .= '<textarea class="upme-input' . $required_class . ' '.$custom_editor_styles.' " name="' . $meta . '" id="reg_' . $meta . '" title="' . $name . '">' . $this->post_value($meta) . '</textarea>';
                                break;

                            case 'text':
                                $display .= '<input type="text" class="upme-input' . $required_class . '" name="' . $meta . '" id="reg_' . $meta . '" value="' . $this->post_value($meta) . '" title="' . $name . '" />';

                                //if (isset($this->registration_fields[$key]['help']) && $help != '') {
                                //$display .= '<div class="upme-help">' . $help . '</div><div class="upme-clear"></div>';
                                //}

                                break;

                            case 'datetime':

                                $display .= '<input type="text" readonly="readonly" class="upme-input' . $required_class . ' upme-datepicker" name="' . $meta . '" id="reg_' . $meta . '" value="' . $this->post_value($meta) . '" title="' . $name . '" />';
                                $display .= '<input type="button" class="upme-button-alt upme-datepicker-reset" value="'.__('Clear Date','upme').'" />';
                                //if (isset($this->registration_fields[$key]['help']) && $help != '') {
                                //$display .= '<div class="upme-help">' . $help . '</div><div class="upme-clear"></div>';
                                //}
                                break;

                            case 'password':

                                $display .= '<input type="password" class="upme-input password' . $required_class . '" name="' . $meta . '" id="reg_' . $meta . '" value="" autocomplete="off" title="' . $name . '" />';

                                //if (isset($this->registration_fields[$key]['help']) && $help != '') {
                                //$display .= '<div class="upme-help">' . $help . '</div><div class="upme-clear"></div>';
                                //}

                                break;

                            case 'password_indicator':
                                $display .= '<div class="password-meter"><div class="password-meter-message" id="password-meter-message">' . __('Strength Indicator', 'upme') . '</div></div>';
                                break;

                            case 'video':
                                $display .= '<input type="text" class="upme-input' . $required_class . '" name="' . $meta . '" id="reg_' . $meta . '" value="' . $this->post_value($meta) . '" title="' . $name . '" />';


                                //if (isset($this->registration_fields[$key]['help']) && $help != '') {
                                //$display .= '<div class="upme-help">' . $help . '</div><div class="upme-clear"></div>';
                                //}

                                break;

                            case 'soundcloud':
                                $display .= '<input type="text" class="upme-input' . $required_class . '" name="' . $meta . '" id="reg_' . $meta . '" value="' . $this->post_value($meta) . '" title="' . $name . '" />';
                                break;

                            case 'select':
                                if ('user_role' == $meta) {

                                    global $upme_roles;
                                    $allowed_user_roles = $upme_roles->upme_allowed_user_roles_registration();


                                    $display .= '<select class="upme-input' . $required_class . '" name="' . $meta . '" id="reg_' . $meta . '" value="' . $this->post_value($meta) . '" title="' . $name . '" >';
                                    $display .= '<option value="">' . $current_option['label_for_registration_user_role'] . '</option>';
                                    foreach ($allowed_user_roles as $key => $val) {
                                        $display .= '<option value="' . $key . '">' . $val . '</option>';
                                    }
                                    $display .= '</select>';
                                }
                        }

                        if (isset($help_text) && !empty($help_text)) {
                            $display .= '<div class="upme-help-text upme-help">' . esc_html($help_text) . '</div>';
                        }

                        /* User can hide this from public */
                        if (isset($this->registration_fields[$key]['can_hide']) && $can_hide == 1) {

                            foreach ($array as $key => $meta_field) {
                                if ('user_email' == $meta_field['meta'] && 1 == $meta_field['can_hide']) {
                                    $display .= '<div class="upme-hide-from-public">
                        <i class="upme-icon upme-icon-square-o"></i>' . __('Hide from Public', 'upme') . '
                        <input type="hidden" name="hide_' . $meta . '" id="hide_' . $meta . '" value="" />
                        </div>';
                                }
                            }
                        }



                        $display .= '</div>';

                        $display .= '</div><div class="upme-clear"></div>';
                    }
                }
            }



            $upme_fields_meta_value_array = array();
            foreach ($array as $key => $field) {
                // Optimized condition and added strict conditions
                $exclude_array = array('user_pass', 'user_pass_confirm', 'user_email');
                if (isset($field['meta']) && in_array($field['meta'], $exclude_array)) {
                    unset($array[$key]);
                }

                /* Filter the fields for regiistration */
                if(!( isset($array[$key]['show_in_register']) && $array[$key]['show_in_register'] == 1 ) ){
                    unset($array[$key]);
                }

                $upme_fields_meta_value_array[$field['meta']] = $field['name'];
            }

            $i_array_end = end($array);

            if (isset($i_array_end['position'])) {
                $array_end = $i_array_end['position'];
                if ($array[$array_end]['type'] == 'separator') {
                    unset($array[$array_end]);
                }
            }

            /* UPME Filters for customizing registration fields */
            $custom_registration_fields_params = array('user_role' => $user_role, 'form_name' => $registration_form_name, 'sidebar_class' => $sidebar_class, 'fields_meta_values' => $upme_fields_meta_value_array);
            $array = apply_filters( 'upme_custom_registration_fields', $array, $custom_registration_fields_params);
            // End Filters

            // echo "<pre>";print_r($array);exit;

            /* Show the fields that user added to customizer */
            foreach ($array as $key => $field) {

                extract($field);

                // WP 3.6 Fix
                if (!isset($deleted))
                    $deleted = 0;

                if (!isset($private))
                    $private = 0;

                if (!isset($required))
                    $required = 0;

                $required_class = '';
                $required_sign  = '';
                if ($required == 1 && in_array($field, $this->include_for_validation)) {
                    $required_class = ' required';
                    $required_sign  = '<span class="upme-required">&nbsp;*</span>';
                }


                /* separator */
                if ($type == 'separator' && $deleted == 0 && $private == 0 && isset($array[$key]['show_in_register']) && $array[$key]['show_in_register'] == 1) {
                    $display .= '<div class="upme-field upme-separator upme-edit upme-edit-show upme-clearfix">' . $name . '</div>';
                }

                /* UPME filter for customizing registration fields on individual forms */
                $custom_registration_field_status_params = array('meta' => $meta, 'user_role' => $user_role, 'form_name' => $registration_form_name, 'sidebar_class' => $sidebar_class);
                $custom_registration_field_status = apply_filters('upme_custom_registration_field_status', true ,$custom_registration_field_status_params);
                /* END filter */        

                /* user meta - registration fields */
                if ($type == 'usermeta' && $deleted == 0 && $private == 0 && isset($array[$key]['show_in_register']) && $array[$key]['show_in_register'] == 1 && $custom_registration_field_status) {

                    $display .= '<div class="upme-field upme-edit upme-edit-show">';

                    /* Show the label */
                    if (isset($array[$key]['name']) && $name) {
                        $display .= '<label class="upme-field-type" for="' . $meta . '">';
                        if (isset($array[$key]['icon']) && $icon) {
                            $display_icon = '<i class="upme-icon upme-icon-' . $icon . '"></i>';
                        } else {
                            $display_icon = '<i class="upme-icon upme-icon-none"></i>';
                        }

                        /* UPME Filters for customizing registration fields icons */
                        $registration_custom_field_icon_params = array('meta'=> $meta, 'name'=>$name, 'field'=> $field);
                        $display .= apply_filters( 'upme_registration_custom_field_icon',$display_icon, $registration_custom_field_icon_params);
                        // End Filters


                        $display .= '<span>' . $name . '</span>' . $required_sign . '</label>';
                    } else {
                        $display .= '<label class="upme-field-type">&nbsp;' . $required_sign . '</label>';
                    }



                    $display .= '<div class="upme-field-value">';

                    switch ($field) {

                        case 'textarea':
                            $params = array('meta'=>$meta, 'name'=> $name);
                            $custom_editor_styles = apply_filters('upme_text_editor_styles','',$params);

                            $display .= '<textarea class="upme-input' . $required_class . ' '.$custom_editor_styles.' " name="' . $meta . '" id="' . $meta . '" title="' . $name . '">' . $this->post_value($meta) . '</textarea>';
                            break;

                        case 'text':
                            $display .= '<input type="text" class="upme-input' . $required_class . '" name="' . $meta . '" id="' . $meta . '" value="' . $this->post_value($meta) . '"  title="' . $name . '" />';
                            break;


                        case 'datetime':
                            $display .= '<input type="text" readonly="readonly" class="upme-input' . $required_class . ' upme-datepicker" name="' . $meta . '" id="' . $meta . '" value="' . $this->post_value($meta) . '"  title="' . $name . '" />';
                            $display .= '<input type="button" class="upme-button-alt upme-datepicker-reset" value="'.__('Clear Date','upme').'" />';
                            break;

                        case 'select':
                            $loop = array();

                            if (isset($array[$key]['predefined_loop']) && $array[$key]['predefined_loop'] != '' && $array[$key]['predefined_loop'] != '0') {
                                $loop = $predefined->get_array($array[$key]['predefined_loop']);
                                if ('countries' == $array[$key]['predefined_loop']) {
                                    array_shift($loop);
                                }
                            } else if (isset($array[$key]['choices']) && $array[$key]['choices'] != '') {
                                $loop = explode(PHP_EOL, $choices);
                            }

                            /* UPME filter for customizing select field values */
                            $reg_select_field_custom_values_params = array('meta' => $meta, 'name' => $name );
                            $loop = apply_filters('upme_reg_select_field_custom_values',$loop,$reg_select_field_custom_values_params);
                            /* End filter */

                            if (isset($loop)) {

                                // Check for country loop
                                $country_loop_status = isset($array[$key]['predefined_loop']) ? $array[$key]['predefined_loop'] : '';

                                $selected_value = $this->post_value($meta);
                                if ('' == $this->post_value($meta) && '' != $country_loop_status && 'countries' == $country_loop_status) {
                                    $upme_settings = get_option('upme_options');
                                    $selected_value = $loop[$upme_settings['default_predefined_country']];
                                }

                                $display .= '<select class="upme-input' . $required_class . '" name="' . $meta . '" id="' . $meta . '" title="' . $name . '">';
                                $display .= '<option value="" >' . __('Please Select', 'upme') . '</option>';
                                foreach ($loop as $option) {

                                    // Added as per http://codecanyon.net/item/user-profiles-made-easy-wordpress-plugin/discussion/4109874?filter=All+Discussion&page=27#comment_4352415

                                    $option = upme_stripslashes_deep(trim($option));

                                    $display .= '<option value="' . $option . '" ' . selected($selected_value, $option, 0) . '>' . $option . '</option>';
                                }
                                $display .= '</select>';
                            }
                            $display .= '<div class="upme-clear"></div>';
                            break;

                        case 'radio':
                            if (isset($array[$key]['choices'])) {
                                $loop = explode(PHP_EOL, $choices);
                            }

                            /* UPME filter for customizing radio field values */
                            $reg_radio_field_custom_values_params = array('meta' => $meta, 'name' => $name );
                            $loop = apply_filters('upme_reg_radio_field_custom_values',$loop,$reg_radio_field_custom_values_params);
                            /* End filter */

                            if (isset($loop) ) {
                                $counter = 0;
                                foreach ($loop as $option) {
                                    if ($counter > 0)
                                        $required_class = '';
                                    // Added as per http://codecanyon.net/item/user-profiles-made-easy-wordpress-plugin/discussion/4109874?filter=All+Discussion&page=27#comment_4352415
                                    $option = upme_stripslashes_deep(trim($option));
                                    $display .= '<label class="upme-radio"><input type="radio" class="' . $required_class . '" title="' . $name . '" name="' . $meta . '" value="' . $option . '" ' . checked($this->post_value($meta), $option, 0);
                                    $display .= '/> ' . $option . '</label>';

                                    $counter++;
                                }
                            }
                            $display .= '<div class="upme-clear"></div>';
                            break;

                        case 'checkbox':
                            if (isset($array[$key]['choices'])) {
                                $loop = explode(PHP_EOL, $choices);
                            }

                            /* UPME filter for customizing checkbox field values */
                            $reg_checkbox_field_custom_values_params = array('meta' => $meta, 'name' => $name );
                            $loop = apply_filters('upme_reg_checkbox_field_custom_values',$loop,$reg_checkbox_field_custom_values_params);
                            /* End filter */

                            if (isset($loop) ) {
                                $counter = 0;
                                foreach ($loop as $option) {

                                    if ($counter > 0)
                                        $required_class = '';

                                    // Added as per http://codecanyon.net/item/user-profiles-made-easy-wordpress-plugin/discussion/4109874?filter=All+Discussion&page=27#comment_4352415
                                    $option = upme_stripslashes_deep(trim($option));
                                    $display .= '<label class="upme-checkbox">';
                                    $display .= '<input type="checkbox" class="'.$required_class.'" title="'.$name.'" name="'.$meta.'[]" value="'.$option.'" ';
                                    if (is_array($this->post_value($meta)) && in_array($option, $this->post_value($meta) )) {
                                        $display .= 'checked="checked"';
                                    }
                                    $display .= '/>'.$option;
                                    
                                    $display .= '</label>';

                                    $counter++;
                                }
                            }
                            $display .= '<div class="upme-clear"></div>';
                            break;

                        case 'password':
                            $display .= '<input type="password" class="upme-input' . $required_class . '" title="' . $name . '" name="' . $meta . '" id="' . $meta . '" value="' . $this->post_value($meta) . '" />';

                            if ($meta == 'user_pass') {
                                $display .= '<div class="upme-help">' . __('If you would like to change the password type a new one. Otherwise leave this blank.', 'upme') . '</div>';
                            } elseif ($meta == 'user_pass_confirm') {
                                $display .= '<div class="upme-help">' . __('Type your new password again.', 'upme') . '</div>';
                            }
                            break;

                        case 'video':
                            $display .= '<input type="text" class="upme-input' . $required_class . '" name="' . $meta . '" id="reg_' . $meta . '" value="' . $this->post_value($meta) . '" title="' . $name . '" />';

                            if (isset($this->registration_fields[$key]['help']) && $help != '') {
                                $display .= '<div class="upme-help">' . $help . '</div><div class="upme-clear"></div>';
                            }

                            break;

                        case 'soundcloud':
                            $display .= '<input type="text" class="upme-input' . $required_class . '" name="' . $meta . '" id="reg_' . $meta . '" value="' . $this->post_value($meta) . '" title="' . $name . '" />';

                            if (isset($this->registration_fields[$key]['help']) && $help != '') {
                                $display .= '<div class="upme-help">' . $help . '</div><div class="upme-clear"></div>';
                            }

                            break;

                        default:
                            /* UPME Filter for working with custom registration field types */
                            $registration_custom_field_types_params = array('id' => 'reg_' . $meta , 'meta'=> $meta, 'name'=>$name, 'field'=> $field, 'required_class' => $required_class, 'value' => $this->post_value($meta), 'array' =>  $array[$key]);
                            $reg_custom_fields_display = apply_filters( 'upme_registration_custom_field_types','', $registration_custom_field_types_params);
                            // End filter

                            if('' != $reg_custom_fields_display){
                                $display .= $reg_custom_fields_display;
                                if (isset($this->registration_fields[$key]['help']) && $help != '') {
                                    $display .= '<div class="upme-help">' . $help . '</div><div class="upme-clear"></div>';
                                }
                            }
                            break;
                    }

                    if (isset($help_text) && !empty($help_text)) {
                        $display .= '<div class="upme-help-text upme-help">' . esc_html($help_text) . '</div>';
                    }

                    /* User can hide this from public */
                    if (isset($array[$key]['can_hide']) && $can_hide == 1) {

                        $display .= '<div class="upme-hide-from-public">
                        <i class="upme-icon upme-icon-square-o"></i>' . __('Hide from Public', 'upme') . '
                        <input type="hidden" name="hide_' . $meta . '" id="hide_' . $meta . '" value="" />
                        </div>';
                    } elseif ($can_hide == 0 && $private == 0) {
                        // Commented Out text Field Must be Publicly Visible
                        //$display .= '<div class="upme-hide-from-public upme-disable">
                        //              '.sprintf(__('%s must be publicly visible.','upme'), $name).'
                        //          </div>';
                    }



                    $display .= '</div>';

                    $display .= '</div><div class="upme-clear"></div>';
                }
            }
            
            $display.=$upme_captcha_loader->load_captcha($this->captcha);

            //if($this->use_captcha == 'yes')
            //else
            //  $display.='<input type="hidden" name="no_captcha" value="yes" />';

            /* UPME Filters for adding extra fields or hidden data in registration form */
            $display .= apply_filters('upme_registration_form_extra_fields','');
            // End Filter

            if((boolean) $current_option['accepting_terms_and_conditions']){
                $terms_field_title = apply_filters('upme_terms_field_title',__('Please Accept Terms and Conditions','upme'));
                $display .= '<div class="upme-field upme-edit upme-edit-show">
                                <label class="upme-field-type upme-field-type-' . $sidebar_class . '">&nbsp;</label>
                                <div class="upme-field-value">
                                    <input type="checkbox" title="'.$terms_field_title.'" class="required" name="upme-terms-agreement" value="accept" />
                                    '.$current_option['html_terms_and_conditions'].'                
                                </div>
                                </div><div class="upme-clear"></div>';
            }
            

            $display .= '<div class="upme-field upme-edit upme-edit-show">
            <label class="upme-field-type upme-field-type-' . $sidebar_class . '">&nbsp;</label>
            <div class="upme-field-value">';

            /* Force custom user role on registration using shortcode attributes */
            $upme_secret_key = get_option('upme_secret_key');
            if(! $upme_secret_key ){
                $upme_secret_key = wp_generate_password(20);
                update_option('upme_secret_key',$upme_secret_key);
            }

            if($user_role){
                

                $hash = hash('sha256', $user_role.$upme_secret_key);

                $display .= '<input type="hidden" name="upme-hidden-register-form-user-role" value="' . $user_role . '" />';
                $display .= '<input type="hidden" name="upme-hidden-register-form-user-role-hash" value="' . $hash . '" />';
                
                              
            }
            
            $hash_register = hash('sha256', $this->registration_form_name.$upme_secret_key); 
            $display .= '<input type="hidden" name="upme-register-form-name" value="' . $registration_form_name . '" />
                         <input type="hidden" name="upme-hidden-register-form-name-hash" value="' . $hash_register . '" />
                         <input type="hidden" name="upme-register-form" value="upme-register-form" />
                         <input type="submit" name="upme-register" id="upme-register" class="upme-button" value="' . __('Register', 'upme') . '" />
            </div>
            </div><div class="upme-clear"></div>';

            if ($redirect_to != '') {
                $display .= '<input type="hidden" name="redirect_to" value="' . $redirect_to . '" />';
            }

            $display .= '</form>';
        } // Registration complete

        return $display;
    }

    /* Login Form on Front end */

    function login($args=array()) {

        global $upme_login, $upme_register;

        // Increasing Counter for Shortcode number
        $this->login_code_count++;

        // Check if redirect to is not set and redirect to is availble in URL
        $default_redirect = "";
        if (isset($_GET['redirect_to']) && $_GET['redirect_to'] != '')
            $default_redirect = $_GET['redirect_to'];

        /* Arguments */
        $defaults = array(
            'use_in_sidebar' => null,
            'redirect_to' => $default_redirect,
            'register_link' => 'yes',
            'register_text' => __('Register', 'upme'),
            'forgot_link' => 'yes',
            'forgot_text' => __('Forget?', 'upme'),
            'custom_forgot_url' => '',
            'custom_register_url' => '',
            'name' => wp_generate_password(12,false),
        );

        $args = wp_parse_args($args, $defaults);
        extract($args, EXTR_SKIP);

        // Default set to no captcha
        $this->captcha = 'no';
        if (isset($captcha))
            $this->captcha = $captcha;

        /*  Set form name for allowing multiple registration forms through filters */
        $this->login_form_name = $name;

        // Check activation status to display the activation message
        $act_status_message = $upme_register->upme_user_activation_handler();

        $sidebar_class = null;
        if ($use_in_sidebar)
            $sidebar_class = 'upme-sidebar';

        $display = null;
        $display .= '<div class="upme-wrap upme-login ' . $sidebar_class . '">
        <div class="upme-inner upme-clearfix upme-login-wrapper">';

        /* UPME Filters for before login head section */
        $display .= apply_filters( 'upme_login_before_head', '');
        // End Filters

        /* UPME Filters for before login head section */
        $login_form_title_params = array();
        $login_form_title = apply_filters( 'upme_login_form_title', __('Login', 'upme') , $login_form_title_params);
        // End Filters

        $display_head  = '<div class="upme-head">';
        $display_head .='<div class="upme-left">';
        $display_head .='<div class="upme-field-name upme-field-name-wide login-heading" id="login-heading-' . $this->login_code_count . '">' . $login_form_title . '</div>';
        $display_head .='</div>';
        $display_head .='<div class="upme-right"></div><div class="upme-clear"></div>';
        $display_head .= '</div>';

        /* UPME Filters for customizing head section */
        $login_head_params = array();
        $display .= apply_filters( 'upme_login_head', $display_head , $login_head_params);
        // End Filters

        /* UPME Filters for after login head section */
        $display .= apply_filters( 'upme_login_after_head', '');
        // End Filters

        $display .='<div class="upme-main">';

        /* Display errors */
        if (isset($_POST['upme-login']) || ( isset($upme_login->errors) && count($upme_login->errors) > 0) ) {
            $display .= $upme_login->get_errors();
        }

        if (!empty($act_status_message['msg'])) {
            $display .= '<div class="upme-' . $act_status_message['status'] . '">';
            $display .= '<span class="upme-error upme-error-block"><i class="upme-icon upme-icon-remove"></i>' . $act_status_message['msg'] . '</span>';
            $display .= '</div>';
        }



        $display .= $this->show_login_form($register_link, $register_text, $custom_register_url, $forgot_link, $forgot_text, $custom_forgot_url, $sidebar_class, $redirect_to);

        $display .= '</div>';

        /* UPME Filters for after login fields section */
        $display .= apply_filters( 'upme_login_after_fields', '');
        // End Filters

        $display .= '</div>
        </div>';

        return $display;
    }

    /* Show login forms */

    function show_login_form($register_link_status, $register_text, $custom_register_url, $forgot_link_status, $forgot_text, $custom_forgot_url, $sidebar_class=null, $redirect_to=null) {
        global $upme_login, $upme_captcha_loader;

        $display = null;
        $display .= '<form action="" method="post" id="upme-login-form-' . $this->login_code_count . '">';
        //$display .= wp_nonce_field('upme_login_action','upmeloginverify',true,false);

        /* UPME Filters for customizing login fields */
        $login_fields_params = array();
        $this->login_fields  = apply_filters( 'upme_login_fields', $this->login_fields, $login_fields_params);
        // End Filters

        foreach ($this->login_fields as $key => $field) {
            $field_meta = $field;

            extract($field);

            if ($type == 'usermeta') {

                $display .= '<div class="upme-field upme-edit upme-edit-show upme-'.$meta.'">';

                /* Show the label */
                $placeholder = '';
                $icon_name = '';
                $input_ele_class = '';
                if ($sidebar_class == null) {
                    if (isset($this->login_fields[$key]['name']) && $name) {
                        $display .= '<label class="upme-field-type" for="' . $meta . '">';
                        if (isset($this->login_fields[$key]['icon']) && $icon) {
                            $display_icon = '<i class="upme-icon upme-icon-' . $icon . '"></i>';
                        } else {
                            $display_icon = '<i class="upme-icon upme-icon-none"></i>';
                        }

                        /* UPME Filters for customizing login fields icons */
                        $login_field_icon_params = array('meta'=> $meta, 'name'=>$name, 'field_meta'=> $field_meta);
                        $display .= apply_filters( 'upme_login_field_icon',$display_icon, $login_field_icon_params);
                        // End Filters

                        $display .= '<span>' . apply_filters('upme_login_label_' . $meta, $name) . '</span></label>';
                    } else {
                        $display .= '<label class="upme-field-type">&nbsp;</label>';
                    }
                } else {
                    $icon_name.='<label class="upme-field-type-sidebar">';
                    if (isset($this->login_fields[$key]['icon']) && $icon) {
                        $display_icon =  '<i class="upme-icon upme-icon-' . $icon . '"></i>';
                    } else {
                        $display_icon =  '<i class="upme-icon upme-icon-none"></i>';
                    }

                    /* UPME Filters for customizing login fields icons */
                    $login_sidebar_field_icon_params = array('meta'=> $meta, 'name'=>$name, 'field'=> $field);
                    $display .= apply_filters( 'upme_login_sidebar_field_icon',$display_icon, $login_sidebar_field_icon_params);
                    // End Filters


                    $icon_name.='</label>';
                    $placeholder = ' placeholder="' . $name . '"';
                    $input_ele_class = ' in_sidebar_value';
                }



                $display .= '<div class="upme-field-value">';

                $display .=$icon_name;

                switch ($field) {
                    case 'textarea':
                        $display .= '<textarea class="upme-input' . $input_ele_class . '" name="' . $meta . '" id="' . $meta . '" ' . $placeholder . '>' . $this->post_value($meta) . '</textarea>';
                        break;
                    case 'text':
                        $display .= '<input type="text" class="upme-input' . $input_ele_class . '" name="' . $meta . '" id="' . $meta . '" value="' . $this->post_value($meta) . '" ' . $placeholder . ' />';

                        if (isset($this->login_fields[$key]['help']) && $help != '') {
                            $display .= '<div class="upme-help">' . $help . '</div><div class="upme-clear"></div>';
                        }

                        break;
                    case 'password':
                        $display .= '<input type="password" class="upme-input' . $input_ele_class . '" name="' . $meta . '" id="' . $meta . '" value="" ' . $placeholder . ' />';
                        break;
                    default:
                        /* UPME Filters for adding custom login field types */
                        $login_custom_field_types_params = array();
                        $display .= apply_filters( 'upme_login_custom_field_types','', $login_custom_field_types_params);
                        // End Filters 
                }

                if ($field == 'password') {

                }



                $display .= '</div>';

                $display .= '</div><div class="upme-clear"></div>';
            }
        }


        $display.=$upme_captcha_loader->load_captcha($this->captcha);

        $display .= '<div class="upme-field upme-edit upme-edit-show">
        <label class="upme-field-type upme-field-type-' . $sidebar_class . '">&nbsp;</label>
        <div class="upme-field-value">';

        if (isset($_POST['rememberme']) && $_POST['rememberme'] == 1) {
            $class = 'upme-icon upme-icon-check-square-o';
        } else {
            $class = 'upme-icon upme-icon-square-o';
        }


        $forgot_pass_url = 'javascript:void(0);';
        if('' != $custom_forgot_url){
            $forgot_pass_url = $custom_forgot_url;
        }

        // Displaying/ Hiding forget password link based on shortcode options
        $forgot_pass = '';
        if ('yes' == $forgot_link_status) {
            // Forgot Pass Link
            $forgot_pass = '<a href="'.$forgot_pass_url.'" id="upme-forgot-pass-' . $this->login_code_count . '" class="upme-login-forgot-link ' . $sidebar_class . '" title="' . $forgot_text . '">' . $forgot_text . '</a>';
        }


        // Register Link
        $register_link = site_url('/wp-login.php?action=register');

        $page_url = '';
        $page_url = get_permalink($this->get_option('registration_page_id'));
        if ($page_url != '')
            $register_link = $page_url;


        // Assining custom url's when available
        if('' != $custom_register_url){
            $register_link = $custom_register_url;
        }


        $registration_status = get_option('users_can_register');

        // Displaying/ Hiding register link based on shortcode options        
        if ('yes' == $register_link_status &&  '1' == $registration_status) {
            $register_link = '<a href="' . $register_link . '" class="upme-login-register-link ' . $sidebar_class . '">' . $register_text . '</a>';
        } else {
            $register_link = '';
        }

        $separator_text = '';
        if ('yes' == $register_link_status && 'yes' == $forgot_link_status && '1' == $registration_status) {
            $separator_text = ' | ';
        }

        $remember_me_class = '';
        $login_btn_class = '';
        if ($sidebar_class != null) {
            $login_btn_class = ' in_sidebar';
            $remember_me_class = ' in_sidebar_remember';
        }

        /* UPME Filters for adding extra fields or hidden data in forgot form */
        $display .= apply_filters('upme_forgot_form_extra_fields','');
        // End Filter


        /* Apply hash on login form name for verification */
        $upme_secret_key = get_option('upme_secret_key');
        if(! $upme_secret_key ){
            $upme_secret_key = wp_generate_password(20);
            update_option('upme_secret_key',$upme_secret_key);
        }

        $hash = hash('sha256', $this->login_form_name.$upme_secret_key);

        $display .= '<input type="hidden" name="upme-hidden-login-form-name" value="' . $this->login_form_name . '" />';
        $display .= '<input type="hidden" name="upme-hidden-login-form-name-hash" value="' . $hash . '" />';
        /* End verification */


        $display .= '<div class="upme-rememberme' . $remember_me_class . '">
        <i class="' . $class . '"></i>' . __('Remember me', 'upme') . '
        <input type="hidden" name="rememberme" id="rememberme-' . $this->login_code_count . '" value="0" />
        </div><input type="submit" name="upme-login" class="upme-button upme-login' . $login_btn_class . '" value="' . __('Log In', 'upme') . '" /><br />';


        $display .= $forgot_pass . $separator_text . $register_link;


        $display .= ' </div>
        </div><div class="upme-clear"></div>';

        $display .= '<input type="hidden" name="redirect_to" value="' . $redirect_to . '" />';

        /* UPME Filters for social login buttons section */
        $display .= apply_filters( 'upme_social_logins', '');
        // End Filters

        $display .= '</form>';

        // Generating Forgot Password Form
        $forgot_pass = '';

        $forgot_pass .= '<div class="upme-forgot-pass" id="upme-forgot-pass-holder-' . $this->login_code_count . '">';

        $forgot_pass .= '<div class="upme-field upme-edit upme-edit-show">';
        $forgot_pass .= '<label class="upme-field-type" for="user_name_email-' . $this->login_code_count . '"><i class="upme-icon upme-icon-user"></i><span>' . __('Username or Email', 'upme') . '</span></label>';
        $forgot_pass .= '<div class="upme-field-value"><input type="text" class="upme-input" name="user_name_email" id="user_name_email-' . $this->login_code_count . '" value=""></div>';
        $forgot_pass .= '</div>';

        $register_link_forgot = '';
        if ('yes' == $register_link_status) {
            $register_link_forgot = ' | ' . $register_link;
        }

        $forgot_pass.='<div class="upme-field upme-edit upme-edit-show">';
        $forgot_pass.='<label class="upme-field-type upme-blank-lable">&nbsp;</label>';
        $forgot_pass.='<div class="upme-field-value">';

        /* UPME Filters for adding extra fields or hidden data in login form */
        $display .= apply_filters('upme_login_form_extra_fields','');
        // End Filter

        $forgot_pass.='<div class="upme-back-to-login">';
        $forgot_pass.='<a href="javascript:void(0);" title="' . __('Back to Login', 'upme') . '" id="upme-back-to-login-' . $this->login_code_count . '">' . __('Back to Login', 'upme') . '</a> ' . $register_link_forgot;
        $forgot_pass.='</div>';

        $forgot_pass.='<input type="button" name="upme-forgot-pass" id="upme-forgot-pass-btn-' . $this->login_code_count . '" class="upme-button upme-login" value="' . __('Forgot Password', 'upme') . '">';

        $forgot_pass.='</div>';
        $forgot_pass.='</div>';



        $forgot_pass .= '</div>';


        $display.=$forgot_pass;


        return $display;
    }

    /* TRUE or false user can view content */

    function user_can_view_content() {
        if (!is_user_logged_in())
            return false;
        return true;
    }

    /* Private content plugin */

    function hidden_content($args=array(), $content) {

        $display = null;

        /* Arguments */
        $defaults = array(
            'message' => 'on'
        );
        $args = wp_parse_args($args, $defaults);
        extract($args, EXTR_SKIP);

        /* Require login */
        if (!$this->user_can_view_content()) {

            if ($message !== 'off') {

                /* filter wildcards */
                $html = $this->get_option('html_private_content');
                $html = str_replace("{upme_current_uri}", $this->current_page, $html);
                $display .= wpautop($html);
                if($this->get_option('html_private_content_form')){
                  $display .= do_shortcode('[upme_login]');  
                }                
            }
        } else { /* Show hidden content */
            /* Adding do_shortcode again to allow shortcode inside shortcode, now private content can have shortcode too */
            $display .= do_shortcode($content);
        }

        return $display;
    }

    /* Logout button */

    function logout($args=array()) {
        $display = null;

        $defaults = array(
            'redirect_to' => $this->current_page,
            'class' => 'upme-button-alt'
        );
        $args = wp_parse_args($args, $defaults);
        extract($args, EXTR_SKIP);

        // WP 3.6 Fix
        if (is_user_logged_in ()) {
            if (isset($warp_div) && $warp_div == true)
                $display .= '<div class="upme-wrap">';

            $user_id = isset($user_id) ? trim($user_id) : 0;
            $current_user = wp_get_current_user();
            $current_user_id = trim($current_user->ID);
            // Display the Log out link only for the logged in users
            if ($user_id == 0 || ($user_id == $current_user_id)) {
                $display .= '<a href="' . wp_logout_url($redirect_to) . '" class="' . $class . '">' . __('Log Out', 'upme') . '</a>';
            }

            if (isset($warp_div) && $warp_div == true)
                $display .= '</div>';
        }

        return $display;
    }

    function upme_load_edit_form_scripts() {
        global $post;

        $upme_settings = get_option('upme_options');

        // Loading scripts and styles only when required
        /* Password Stregth Checker Script */
        if (!wp_script_is('form-validate')) {
            wp_register_script('form-validate', upme_url . 'js/form-validate.js', array('jquery'));
            wp_enqueue_script('form-validate'); 

            $validate_strings = upme_form_validate_setting();
            wp_localize_script('form-validate', 'Validate', $validate_strings);     

        }

        if (!wp_script_is('upme_fancy_box') && '0' == $upme_settings['disable_fancybox_script_styles']) {
            wp_register_script('upme_fancy_box', upme_url . 'js/upme-fancybox.js', array('jquery'));
            wp_enqueue_script('upme_fancy_box');
        }

        // Include password strength meter from WordPress core
        wp_enqueue_script('password-strength-meter');

        if (!wp_style_is('upme_password_meter')) {
            wp_register_style('upme_password_meter', upme_url . 'css/password-meter.css');
            wp_enqueue_style('upme_password_meter');
        }

        if (!wp_style_is('upme_fancy_box_styles') && '0' == $upme_settings['disable_fancybox_script_styles']) {
            wp_register_style('upme_fancy_box_styles', upme_url . 'css/jquery.fancybox.css');
            wp_enqueue_style('upme_fancy_box_styles');
        }

        do_action('upme_edit_form_scripts');
    }

    function upme_hide_admin_bar() {

        $current_option = get_option('upme_options');
        if ('hide_from_non_admin' == $current_option['hide_frontend_admin_bar']) {
            if (!current_user_can('manage_options')) {
                show_admin_bar(false);
            }
        } else if ('hide_from_all' == $current_option['hide_frontend_admin_bar']) {
            show_admin_bar(false);
        } 
    }

    /* Add new rewriting rule to manage userId/username as pretty permalinks */

    function upme_profile_custom_routes() {
        $current_option = get_option('upme_options');

        // Add custom rewrite rules when not in default permalinks
        if (isset($current_option['profile_page_id']) && !isset($_REQUEST['page_id'])) {
            $link = get_permalink($current_option['profile_page_id']);
            $filter_link = rtrim(substr(str_replace(home_url(), '', $link), 1), '/') . '/';
            $filter_link_with_slash = '^' . $filter_link . '([^/]+)/?';
            $filter_link_empty_slash = '^' . $filter_link . '([^/]+)?';
            $profile_page_id = url_to_postid($link);

            add_rewrite_rule($filter_link_empty_slash, 'index.php?page_id=' . $profile_page_id . '&upme_profile_filter=$matches[1]', 'top');
            add_rewrite_rule($filter_link_with_slash, 'index.php?page_id=' . $profile_page_id . '&upme_profile_filter=$matches[1]', 'top');
        }
    }

    /* Add new query variable to handle userID/username in custom permalinks */

    function upme_profile_custom_routes_query_vars($query_vars) {
        $query_vars[] = 'upme_profile_filter';
        return $query_vars;
    }

    function upme_invalid_user_profile() {
        $display = null;
        $display .= '<div class="upme-wrap">';
        $display .= '<p class="upme-login-spacer">&nbsp;</p>';
        $display .= __('User not found.', 'upme');
        $display .= '</div>';
        return $display;
    }

    function upme_sidebar_login($widget_settings) {

        global $upme_login;

        $this->login_code_count++;

        // Check if redirect to is not set and redirect to is availble in URL
        $default_redirect = "";
        if (isset($_GET['redirect_to']) && $_GET['redirect_to'] != '')
            $default_redirect = $_GET['redirect_to'];

        /* Arguments */
        $use_in_sidebar = null;
        $redirect_to = $default_redirect;




        // Default set to no captcha
        $this->captcha = 'no';
        if (isset($captcha))
            $this->captcha = $captcha;

        $sidebar_class = 'upme-sidebar-widget';

        $display = null;
        $display .= '<div class="upme-widget-wrap upme-login ' . $sidebar_class . '">
        <div class="upme-inner upme-login-wrapper">';

        $display .= '<div class="upme-widget-head upme-clearfix">';
        $display .='<div class="upme-widget-left">';
        $display .='<div class="upme-field-name upme-field-name-wide login-heading" id="login-heading-' . $this->login_code_count . '">' . __('Login', '') . '</div>';
        $display .='</div>';
        $display .='<div class="upme-widget-right"></div><div class="upme-clear"></div>';
        $display .= '</div>';

        $display .='<div class="upme-widget-main">';

        /* Display errors */
        if (isset($_POST['upme-login'])) {
            $display .= $upme_login->get_errors();
        }


        $display .= $this->upme_show_widget_login_form($widget_settings, $sidebar_class, $redirect_to);

        $display .= '</div>

        </div>
        </div><div style="clear:both;"></div>';

        return $display;
    }

    function upme_show_widget_login_form($widget_settings, $sidebar_class=null, $redirect_to=null) {
        global $upme_login, $upme_captcha_loader;


        $sidebar_class = 'upme-sidebar';
        $display = null;
        $display .= '<form action="" method="post" id="upme-login-form-' . $this->login_code_count . '">';


        foreach ($this->login_fields as $key => $field) {
            extract($field);

            if ($type == 'usermeta') {

                $display .= '<div class="upme-field upme-edit upme-edit-show upme-'.$meta.'">';

                /* Show the label */
                $placeholder = '';
                $icon_name = '';
                $input_ele_class = '';

                $icon_name.='<label class="upme-field-type-sidebar">';
                if (isset($this->login_fields[$key]['icon']) && $icon) {
                    $icon_name .= '<i class="upme-icon upme-icon-' . $icon . '"></i>';
                } else {
                    $icon_name .= '<i class="upme-icon upme-icon-none"></i>';
                }
                $icon_name.='</label>';
                $placeholder = ' placeholder="' . $name . '"';
                $input_ele_class = ' in_sidebar_value';




                $display .= '<div class="upme-field-value">';

                $display .=$icon_name;

                switch ($field) {
                    case 'textarea':
                        $display .= '<textarea class="upme-input' . $input_ele_class . '" name="' . $meta . '" id="' . $meta . '" ' . $placeholder . '>' . $this->post_value($meta) . '</textarea>';
                        break;
                    case 'text':
                        $display .= '<input type="text" class="upme-input' . $input_ele_class . '" name="' . $meta . '" id="' . $meta . '" value="' . $this->post_value($meta) . '" ' . $placeholder . ' />';

                        if (isset($this->login_fields[$key]['help']) && $help != '') {
                            $display .= '<div class="upme-help">' . $help . '</div><div class="upme-clear"></div>';
                        }

                        break;
                    case 'password':
                        $display .= '<input type="password" class="upme-input' . $input_ele_class . '" name="' . $meta . '" id="' . $meta . '" value="" ' . $placeholder . ' />';
                        break;
                }

                if ($field == 'password') {

                }



                $display .= '</div>';

                $display .= '</div><div class="upme-clear"></div>';
            }
        }


        $display.=$upme_captcha_loader->load_captcha($this->captcha);

        $display .= '<div class="upme-field upme-edit upme-edit-show">
        <label class="upme-field-type upme-field-type-' . $sidebar_class . '">&nbsp;</label>
        <div class="upme-field-value">';

        if (isset($_POST['rememberme']) && $_POST['rememberme'] == 1) {
            $class = 'upme-icon upme-icon-check-square-o';
        } else {
            $class = 'upme-icon upme-icon-square-o';
        }


        $forgot_pass = '';
        if ($widget_settings['display-forgot-password-link']) {
            // Forgot Pass Link
            if (empty($widget_settings['forgot-password-link'])) {
                $forgot_pass = '<a href="javascript:void(0);" id="upme-forgot-pass-' . $this->login_code_count . '" class="upme-login-forgot-link ' . $sidebar_class . '" title="' . __('Forget?', 'upme') . '">' . __('Forget?', 'upme') . '</a> ';
            } else {
                $forgot_pass = '<a href="' . $widget_settings['forgot-password-link'] . '" id="upme-forgot-pass-' . $this->login_code_count . '" class="upme-login-forgot-link ' . $sidebar_class . '" title="' . __('Forget?', 'upme') . '">' . __('Forget?', 'upme') . '</a> ';
            }
        }

        // Register Link
        $register_link = site_url('/wp-login.php?action=register');

        $page_url = '';
        $page_url = get_permalink($this->get_option('registration_page_id'));
        if (!empty($widget_settings['custom-register-link'])) {
            $register_link = $widget_settings['custom-register-link'];
        } else if ($page_url != '') {
            $register_link = $page_url;
        }

        $registration_status = get_option('users_can_register');

        if ($widget_settings['display-register-link'] && '1' == $registration_status) {
            $register_link = '<a href="' . $register_link . '" class="upme-login-register-link ' . $sidebar_class . '">' . __('Register', 'upme') . '</a>';
        } else {
            $register_link = '';
        }

        $remember_me_class = '';
        $login_btn_class = '';
        if ($sidebar_class != null) {
            $login_btn_class = ' in_sidebar';
            $remember_me_class = ' in_sidebar_remember';
        }


        $link_separator = '';
        if (!empty($register_link) && !empty($forgot_pass) && '1' == $registration_status ) {
            $link_separator = '<label class="upme-widget-link-separator">|</label>';
        }

        /* UPME Filters for adding extra fields or hidden data in widget login form */
        $display .= apply_filters('upme_widget_login_form_extra_fields','');
        // End Filter

        $display .= '<div class="upme-rememberme' . $remember_me_class . '">
        <i class="' . $class . '"></i>' . __('Remember me', 'upme') . '
        <input type="hidden" name="rememberme" id="rememberme-' . $this->login_code_count . '" value="0" />
        </div><input type="submit" name="upme-login" class="upme-button upme-login' . $login_btn_class . '" value="' . __('Log In', 'upme') . '" /><br />' . $forgot_pass . " " . $link_separator . " " . $register_link;


        $display .= ' </div>
        </div><div class="upme-clear"></div>';

        $display .= '<input type="hidden" name="redirect_to" value="' . $redirect_to . '" />';

        $display .= '</form>';


        // Generating Forgot Password Form
        $forgot_pass = '';

        $forgot_pass .= '<div class="upme-forgot-pass" id="upme-forgot-pass-holder-' . $this->login_code_count . '">';

        $forgot_pass .= '<div class="upme-field upme-edit upme-edit-show">';
        $forgot_pass .= '<label class="upme-field-type" for="user_name_email-' . $this->login_code_count . '"><i class="upme-icon upme-icon-user"></i><span>' . __('Username or Email', 'upme') . '</span></label>';
        $forgot_pass .= '<div class="upme-field-value"><input type="text" class="upme-input" name="user_name_email" id="user_name_email-' . $this->login_code_count . '" value=""></div>';
        $forgot_pass .= '</div>';

        $forgot_pass.='<div class="upme-field upme-edit upme-edit-show">';
        $forgot_pass.='<label class="upme-field-type upme-blank-lable">&nbsp;</label>';
        $forgot_pass.='<div class="upme-field-value">';

        /* UPME Filters for adding extra fields or hidden data in widget forgot form */
        $display .= apply_filters('upme_widget_forgot_form_extra_fields','');
        // End Filter

        $forgot_pass.='<div class="upme-back-to-login">';
        $forgot_pass.='<a href="javascript:void(0);" class="upme-login-register-link ' . $sidebar_class . '" title="' . __('Back to Login', 'upme') . '" id="upme-back-to-login-' . $this->login_code_count . '">' . __('Back to Login', 'upme') . '</a> ' . $link_separator . ' ' . $register_link;
        $forgot_pass.='</div>';

        $forgot_pass.='<input type="button" name="upme-forgot-pass" id="upme-forgot-pass-btn-' . $this->login_code_count . '" class="upme-button upme-login" value="' . __('Forgot Password', 'upme') . '">';

        $forgot_pass.='</div>';
        $forgot_pass.='</div>';
        $forgot_pass .= '</div>';


        $display.=$forgot_pass;


        return $display;
    }

    function upme_sidebar_mini_profile($widget_settings = array()) {
        global $post;

        /* Capture logged in user ID */

        $current_user = wp_get_current_user();
        if (($current_user instanceof WP_User)) {
            $this->logged_in_user = $current_user->ID;
        }

        $sidebar_class = 'upme-sidebar-widget';
        $name_holder_width = '100%';
        $width = 1;

        // Show custom field as profile title
        $profile_title_field = $this->get_option('profile_title_field');
        // Get value of profile title field or default display name if empty
        $profile_title_display = $this->upme_profile_title_value($profile_title_field, $this->logged_in_user);

        /* Block profile based on custom status and display information to user */
        $validate_profile_visibility_params = array('user_id' => $this->logged_in_user, 'status' => 'true', 'info' => '', 'context' => 'sidebar_profile');
        $profile_visibility = apply_filters('upme_validate_profile_visibility', $validate_profile_visibility_params);
        if( isset($profile_visibility['status']) && ! $profile_visibility['status'] ){
            $info_display = upme_profile_visibility_info($profile_visibility,$profile_title_display);
            return $info_display;
        }
        /* <-- Block profile --> */

        /* If no ID is set, normally logged out */
        /* User must login to view his profile. */

        $pic_class = 'upme-pic mini_profile';
        if (is_safari ())
            $pic_class = 'upme-pic safari mini_profile';

        $display = '';

        $display .= '<div class="upme-widget-wrap  upme-width-' . $width . ' ' . $sidebar_class . '">
        <div class="upme-inner upme-clearfix">
         
        <div class="upme-widget-head upme-clearfix">
         
        <div class="upme-widget-left upme-profile-holder">
        <div class="' . $pic_class . '" style="width:' . $name_holder_width . ';">';

        /* UPME Filter for customizing profile URL */
        $params = array('id' => $this->logged_in_user, 'view' => null, 'modal' => null, 'group'=> null , 'use_in_sidebar'=> 'yes', 'context'=>'sidebar_widget');
        $profile_url = apply_filters('upme_custom_profile_url',$this->profile_link($this->logged_in_user),$params);
        // End Filter

        /* UPME Filter for customizing profile picture */
        $params = array('id'=> $this->logged_in_user, 'view' => null, 'modal' => null, 'use_in_sidebar'=> 'yes', 'context'=>'sidebar_widget' );
        $profile_pic_display = '<a href="' . $profile_url . '">' . $this->pic($this->logged_in_user, 50) . '</a>';
        $profile_pic_display = apply_filters('upme_custom_profile_pic',$profile_pic_display,$params);
        $display .= $profile_pic_display;
        // End Filter
        
        $display.='<div class="upme-field-name">';
        if ($this->get_option('clickable_profile')) {
            if ($this->get_option('clickable_profile') == 1) {

                

                $display .= '<a href="' . $profile_url . '">';
            } else {
                $display .= '<a href="' . get_author_posts_url($this->logged_in_user) . '">';
            }

            $display .= $profile_title_display;
            $display .= '</a>';
        } else {
            $display .= $profile_title_display;
        }
        $display.='</div>';

        $display.='</div>';

        if (is_user_logged_in ()) {

            $display .= '<div class="upme-name upme-button-holder">';
            $link = get_permalink($this->get_option('profile_page_id'));
            $class = "upme-widget-button-alt";
            $link_text = __('View Profile', 'upme');

            //Enable customlogout url
            $logout_url = '';
            if(!empty($widget_settings['logout-link'])){
                $logout_url = ' redirect_to='.$widget_settings['logout-link'];
            }

            $display .= '<div class="upme-field-edit upme-widget-profile-button">
                        <a href="' . $link . '" class="' . $class . '">' . $link_text . '</a>&nbsp;' . do_shortcode('[upme_logout wrap_div="false" class="upme-widget-button-alt" user_id="' . $this->logged_in_user . '"  '.$logout_url.']') . '</div>
            </div>';
        }

        $display .= '</div><div class="upme-clear"></div>';

        $display .= '</div>
         
        </div>
        </div>';

        return $display;
    }

    // Get value of profile title field
    function upme_profile_title_value($profile_title_field, $id) {

        $profile_title_display = '';

        if ('combined_fname_lname' == $profile_title_field) {
            $profile_title_display = trim(get_the_author_meta('first_name', $id) . " " . get_the_author_meta('last_name', $id));
        } else if ('combined_lname_fname' == $profile_title_field) {
            $last_name = get_the_author_meta('last_name', $id);
            $first_name = get_the_author_meta('first_name', $id);
            if (!empty($last_name) && !empty($first_name)) {
                $profile_title_display = $last_name . ', ' . $first_name;
            } else {
                $profile_title_display = $last_name . $first_name;
            }
        } else {
            $profile_title_display = get_the_author_meta($profile_title_field, $id);
        }

        if (empty($profile_title_display)) {
            $profile_title_display = get_the_author_meta('display_name', $id);
        }

        return $profile_title_display;
    }

    // Reset password form on frontend
    function upme_reset_password($args=array()) {
        global $upme_reset_password;



        /* Arguments */
        $defaults = array();

        $args = wp_parse_args($args, $defaults);
        extract($args, EXTR_SKIP);

        $display = null;
        $display .= '<div class="upme-wrap upme-reset-password">
        <div class="upme-inner upme-clearfix upme-reset-password-wrapper">';

        $display .= '<div class="upme-head">';
        $display .='<div class="upme-left">';
        $display .='<div class="upme-field-name upme-field-name-wide reset-password-heading" id="reset-password-heading">' . __('Reset Password', 'upme') . '</div>';
        $display .='</div>';
        $display .='<div class="upme-right"></div><div class="upme-clear"></div>';
        $display .= '</div>';

        $display .='<div class="upme-main">';

        /* Display errors */
        if (isset($_POST['upme-reset-password']) || isset($_GET['action'])) {
            $display .= $upme_reset_password->get_errors();
        } else if (isset($_GET['upme_reset_status']) && 'expired' == $_GET['upme_reset_status']) {

            $display .= $upme_reset_password->get_errors();
        }

        $display .= $this->show_reset_password_form();

        $display .= '</div>

        </div>
        </div>';

        return $display;
    }

    /* Show reset password forms */

    function show_reset_password_form() {

        global $upme_reset_password;

        $this->login_code_count++;

        // Check whether action parameter is available to show password reset form
        // or forget password form
        $action = isset($_GET['action']) ? $_GET['action'] : '';
        $login = isset($_GET['login']) ? $_GET['login'] : '';
        $upme_reset_status = isset($_GET['upme_reset_status']) ? $_GET['upme_reset_status'] : '';


        // Loading scripts and styles only when required
        /* Password Stregth Checker Script */
        if (!wp_script_is('form-validate')) {
            wp_register_script('form-validate', upme_url . 'js/form-validate.js', array('jquery'));
            wp_enqueue_script('form-validate');

            $validate_strings = upme_form_validate_setting();

            wp_localize_script('form-validate', 'Validate', $validate_strings);
        }

        if (!wp_style_is('upme_password_meter')) {
            wp_register_style('upme_password_meter', upme_url . 'css/password-meter.css');
            wp_enqueue_style('upme_password_meter');
        }

        // Include password strength meter from WordPress core
        wp_enqueue_script('password-strength-meter');

        $display = null;

        if (('upme_reset_pass' == $action || 'expired' == $upme_reset_status) && '' != $action) {
            $display .= '<div id="upme-reset-form-err-holder" style="display: none;" class="upme-errors"></div>';
            $display .= '<form action="" method="post" id="upme-reset-password-form">';

            $display .= '<div class="upme-field upme-edit upme-edit-show upme-user_pass ">';
            $display .= '<label class="upme-field-type" for="' . __('New Password', 'upme') . '">';
            $display .= '<i class="upme-icon upme-icon-lock"></i>';
            $display .= '<span>' . __('New Password', 'upme') . '</span></label>';
            $display .= '<div class="upme-field-value">';
            $display .= '<input type="password" class="upme-input" name="upme_new_password" id="upme_new_password" value=""  />';
            $display .= '<div class="upme-help-text upme-help">' . apply_filters('upme_password_help_text',__('Password must be at least 7 characters long. To make it stronger, use upper and lower case letters, numbers and symbols.', 'upme')) . '</div>';
            $display .= '</div>';
            $display .= '</div><div class="upme-clear"></div>';

            $display .= '<div class="upme-field upme-edit upme-edit-show upme-user_pass_confirm">';
            $display .= '<label class="upme-field-type" for="' . __('Confirm', 'upme') . '">';
            $display .= '<i class="upme-icon upme-icon-lock"></i>';
            $display .= '<span>' . __('Confirm', 'upme') . '</span></label>';
            $display .= '<div class="upme-field-value">';
            $display .= '<input type="password" class="upme-input" name="upme_confirm_new_password" id="upme_confirm_new_password" value=""  />';
            $display .= '<div class="password-meter"><div id="password-meter-message" class="password-meter-message">' . __('Strength Indicator', 'upme') . '</div></div>';
            $display .= '</div>';

            $display .= '</div><div class="upme-clear"></div>';

            $display .= '<div class="upme-field upme-edit upme-edit-show">
                        <input type="hidden" id="upme-reset-pass-login" value="' . $login . '" />';
            $display.=UPME_Html::button('submit', array(
                        'class' => 'upme-button upme-reset-password',
                        'name' => 'upme-reset-password',
                        'value' => __('Reset Password', 'upme')
                    ));

            $display .= ' </div><div class="upme-clear"></div>';
            $display .= '</form>';
        } else {

            $display .= '<form action="" method="post" id="upme-forgot-password-form">';

            $display .= '<div class="upme-field upme-edit upme-edit-show upme-user_name_email">';
            $display .= '<label class="upme-field-type" for="user_name_email">
            <i class="upme-icon upme-icon-user"></i><span>' . __('Username or Email', 'upme') . '</span></label>';

            $display .= '<div class="upme-field-value">';
            $display .= UPME_Html::text_box(array(
                        'class' => 'upme-input',
                        'id' => 'user_name_email-' . $this->login_code_count,
                        'name' => 'user_name_email'
                    ));
            $display .= '</div>';

            $display .= '</div>';

            $display .= '<div class="upme-field upme-edit upme-edit-show">';
            $display.=UPME_Html::button('button', array(
                        'class' => 'upme-button upme-login',
                        'id' => 'upme-forgot-pass-btn-' . $this->login_code_count,
                        'name' => 'upme-forgot-pass-btn',
                        'value' => __('Forgot Password', 'upme')
                    ));

            $display .= ' </div><div class="upme-clear"></div>';
            $display .= '</form>';
        }

        return $display;
    }

    // Display user posts on profiles based on the configured settings
    function show_profile_posts($id, $post_limit, $feature_image_status, $view) {

        $display = '';

        if ('compact' != $view) {


            // Get the authored posts for the viewed profile
            $args = array(
                'author' => $id,
                'order' => 'DESC',
                'orderby' => 'date',
                'posts_per_page' => $post_limit,
            );

            $query = new WP_Query($args);

            if ($query->have_posts()) {

                $display .= '<div class="upme-post-head upme-clearfix">                     
                            <p class="upme-posts-title">' . __('Recent Posts', 'upme') . '</p>
                        </div>';

                $display .= '<div class="upme-main upme-main-">';

                // Display different views based on posts with featured images or posts as text
                if ('1' == $feature_image_status) {

                    while ($query->have_posts()) : $query->the_post();

                        $image_attributes = wp_get_attachment_image_src(get_post_thumbnail_id(), 'thumbnail');
                        $image_src = upme_url . 'img/default-post-thumbnail.png';
                        if (is_array($image_attributes) && ('' != $image_attributes[0])) {
                            $image_src = $image_attributes[0];
                        }

                        $display .= '<div class="upme-field upme-view">
                            <div class="upme-post-feature-image"><img src="' . $image_src . '" /></div>
                            <div class="upme-post-feature-value"><span><a href="' . get_permalink() . '">' . get_the_title() . '</a></span></div>
                         </div>';

                    endwhile;

                    wp_reset_query();
                } else {

                    while ($query->have_posts()) : $query->the_post();

                        $display .= '<div class="upme-field upme-view">
                                <div class="upme-post-field-type"><i class="upme-icon upme-icon-file-text"></i></div>
                                <div class="upme-post-field-value"><span><a href="' . get_permalink() . '">' . get_the_title() . '</a></span></div>
                             </div>';
                    endwhile;

                    wp_reset_query();
                }

                $display .= '</div>';
            }
        }

        return $display;
    }

    // Restrict other user profile view to certain user types
    function restricted_user_profile($id,$status){
        global $upme_roles;

        $restricted = true;
        if( '2' == $status ){

            $current_option = get_option('upme_options');
            
            $restricted_roles = ('' != $current_option['choose_roles_for_view_profile']) ? $current_option['choose_roles_for_view_profile'] : array();
            
            if(!is_array($restricted_roles)){
                $restricted_roles = array();
            }

            array_push($restricted_roles, 'administrator');

            $current_user_roles = $upme_roles->upme_get_user_roles_by_id($id);
            
            if(is_array($current_user_roles)){
                foreach ($current_user_roles as $key => $role) {
                    if(in_array($role, $restricted_roles)){
                        $restricted = false;
                    }
                }
            }
                        

        } else if('0' == $status && !current_user_can('manage_options') ){

            $restricted = true;
        } else{
            $restricted = false;
        }

        if($restricted){            
            return true;
        }else{
            return false;
        }

    }

    function upme_author_link($url, $user_id){
        if ($this->get_option('link_post_author_to_upme') == '1') {
            $url = $this->profile_link($user_id);
        }
        
        return $url;
    }

    function upme_profile_after_post($content){
        global $post;

        $author_id = isset($post->post_author) ? $post->post_author : 0;

        if(is_single() && $this->get_option('display_profile_after_post') == '1' && $author_id != 0){
            $template = $this->get_option('author_post_profile_template');
            if ( $template == '0') {
                $content .= do_shortcode('[upme view=compact id=' . $author_id . ']');
            }else{                
                $content .= do_shortcode('[upme_author_card template='.$template.' id=' . $author_id . ']');
            }
            
        }
        return $content;
    }
}

$upme = new UPME();

