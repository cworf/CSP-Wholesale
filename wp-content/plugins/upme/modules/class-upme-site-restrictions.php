<?php


class UPME_Site_Restrictions{

    public $restriction_rules;
    public $restriction_rule;

    function __construct(){
        
        add_action('wp_ajax_upme_save_site_restriction_rules', array($this, 'upme_save_site_restriction_rules'));
        add_action('wp_ajax_upme_delete_site_restriction_rules', array($this, 'upme_delete_site_restriction_rules'));
        add_action('wp_ajax_upme_enable_site_restriction_rules', array($this, 'upme_enable_site_restriction_rules'));


    }

    /**
     * Save new global restriction rules for site content
     * 
     * @param   -
     * @return  -
     */
    function upme_save_site_restriction_rules(){

        parse_str($_POST['data'], $setting_data);

        $this->restriction_rules = get_option('upme_site_restriction_rules');

        // Create restriction rules in db when its not available
        if (!get_option('upme_site_restriction_rules')) {
            update_option('upme_site_restriction_rules', array());
            $this->restriction_rules = array();
        }

        $this->restriction_rule = $setting_data;
        $this->restriction_rule['site_restriction_rule_status'] = '1';
        array_push($this->restriction_rules,$this->restriction_rule);
        $res = update_option('upme_site_restriction_rules', $this->restriction_rules);

        
        $rules = $this->upme_restriction_rules_list();
        if($res){
            echo json_encode(array('status'=>'success','rules'=> $rules, 'msg' => __('New restriction rule added.','upme')));
        
        }else{
            echo json_encode(array('status'=>'fail','rules'=> $rules, 'msg' => __('Failed to add new restriction rule.','upme')));
        
        }
        exit;
    }

    /**
     * Delete new global restriction rules for site content
     * 
     * @param   -
     * @return  -
     */
    function upme_delete_site_restriction_rules(){

        $rule_id = post_value('rule_id');

        $this->restriction_rules = get_option('upme_site_restriction_rules');

        if (!get_option('upme_site_restriction_rules')) {
            update_option('upme_site_restriction_rules', array());
            $this->restriction_rules = array();
        }

        if(isset($this->restriction_rules[$rule_id])){
            unset($this->restriction_rules[$rule_id]);
        }

        $res = update_option('upme_site_restriction_rules', $this->restriction_rules);
        
        $rules = $this->upme_restriction_rules_list();
        if($res){
            echo json_encode(array('status'=>'success','rules'=> $rules, 'msg' => __('Restriction rule deleted.','upme')));
        
        }else{
            echo json_encode(array('status'=>'fail','rules'=> $rules, 'msg' => __('Failed to delete restriction rule.','upme')));
        
        }
        exit;
    }

    /**
     * Generate the table of global restriction rules
     * 
     * @param   -
     * @return  -
     */
    function upme_restriction_rules_list(){

        $display = '';

        $restriction_rules = get_option('upme_site_restriction_rules');
        if(is_array($restriction_rules) && count($restriction_rules) != 0){


            foreach ($restriction_rules as $key=>$rule) {
// echo "<pre>";print_R($rule);exit;
                $res_user_types = array('by_all_users' => 'All Logged in Users','by_user_roles' => 'User Roles');

                $res_conditions = $this->upme_get_restriction_conditions($rule);
                $res_content    = $this->upme_get_restriction_contents($rule);

                $checked = ($rule['site_restriction_rule_status'] == '1') ? 'checked="checked"' : '';

                $display .= '<tr>
                                <td>'.$res_user_types[$rule['site_content_user_restrictions']].'</td>
                                <td>'.$res_conditions.'</td>
                                <td>'.$res_content.'</td>
                                <td>'.get_permalink($rule['site_content_redirect_url']).'</td>
                                <td><input type="hidden" id="upme_rule_id" value="'.$key.'" />
                                    <input type="button" id="upme_delete_restriction_rule" value="'.__('Delete Rule','upme').'" 
                                    class="button button-primary upme_delete_restriction_rule" />
                                </td>
                                <td><input type="checkbox" '.$checked.' class="site_content_enable_restriction" name="site_content_enable_restriction" value="1" id="site_content_enable_restriction-'.$key.'" /></td>
                            </tr>';
            }
        }else{
            $display .= '<tr >
                            <td colspan="6" style="text-align:center;">'.__('Restriction rules are not available.','upme').'</td>
                        </tr>';
        }

        return $display;
    }

    function upme_get_restriction_conditions($rule){

        $condition = '';

        switch ($rule['site_content_user_restrictions']) {
            case 'by_all_users':
                $condition = __('All Logged in Users','upme');
                break;

            case 'by_user_roles':
                $condition = implode(', ',$rule['site_content_allowed_roles']);
                break;
            
        }

        return $condition;
    }

    function upme_get_restriction_contents($rule){

        $content = '';

        switch ($rule['site_content_section_restrictions']) {
            case 'all_pages':
                $content = __('All Pages','upme');
                break;

            case 'all_posts':
                $content = __('All Posts','upme');
                break;

            case 'restrict_selected_pages':
                $pages_list = $rule['site_content_page_restrictions'];
                $display_pages = '';
                foreach ($pages_list as $key => $page) {
                   $display_pages .= '<p><a target="_blank" href="'.get_permalink($page).'">'.get_the_title($page).'</a></p>';
                }
                $content = __('Selected Pages:','upme').'<br/>'.$display_pages;
                break;

            case 'restrict_selected_posts':
                $posts_list = $rule['site_content_post_restrictions'];
                $display_posts = '';
                foreach ($posts_list as $key => $post) {
                   $display_posts .= '<p><a target="_blank" href="'.get_permalink($post).'">'.get_the_title($post).'</a></p>';
                }
                $content = __('Selected Posts:','upme').'<br/>'.$display_posts;
                break;
           
        }

        return $content;
    }

    /**
     * Enable/ Disable restriction rules for site content
     * 
     * @param   -
     * @return  -
     */
    function upme_enable_site_restriction_rules(){

        $rule_id = post_value('rule_id');
        $rule_status = post_value('rule_status');

        $this->restriction_rules = get_option('upme_site_restriction_rules');

        if (!get_option('upme_site_restriction_rules')) {
            update_option('upme_site_restriction_rules', array());
            $this->restriction_rules = array();
        }

        $this->restriction_rules[$rule_id]['site_restriction_rule_status'] = $rule_status;

        $res = update_option('upme_site_restriction_rules', $this->restriction_rules);
        
        $rules = $this->upme_restriction_rules_list();
        if($res){
            echo json_encode(array('status'=>'success','rules'=> $rules, 'msg' => __('Restriction rule staus updated.','upme')));
        
        }else{
            echo json_encode(array('status'=>'fail','rules'=> $rules, 'msg' => __('Failed to update restriction rule status.','upme')));
        
        }
        exit;
    }

    

}

$upme_site_restrictions = new UPME_Site_Restrictions();