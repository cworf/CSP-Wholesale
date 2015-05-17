<?php
function tesla_has_woocommerce() {
    static $flag = NULL;
    if ($flag === NULL) {
        if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))))
            $flag = TRUE;
        else
            $flag = FALSE;
    }
    return $flag;
}

function curl_mailchimp( $url, $postdata = array( ) , $grab_error = false , $get_response = false) {
    $ch = curl_init( $url );
    curl_setopt( $ch, CURLOPT_URL, $url );
    curl_setopt( $ch, CURLOPT_HEADER, 0 );
    curl_setopt( $ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY );
    curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1 );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, TRUE );
    curl_setopt( $ch, CURLOPT_USERAGENT, "Mozilla/6.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.1) Gecko/2008070208 Firefox/3.0.3" );
    curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
    curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
    if ( ! empty( $postdata ) ) {
        curl_setopt( $ch, CURLOPT_POST, 1 );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $postdata ));
    }

    $data = curl_exec( $ch );
    $error = curl_error( $ch );
    curl_close( $ch );

    if ( $error != '' || empty($data))
        return FALSE;
    else{
        $data = json_decode($data);
        if ($get_response && empty($data->error))
            return $data->data;
        elseif(empty($data->error))
            return TRUE;
        elseif($grab_error)
            return $data->error;
    }
    return FALSE;
}

function curl_get_file_contents($URL){
    if(function_exists('curl_init')){
        $c = curl_init();
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_URL, $URL);
        $contents = curl_exec($c);
        curl_close($c);

        if ($contents) 
            return $contents;
        else 
            return FALSE;
    }else
        return False;
}

function _gstyle_changer($id,$units = 'px'){
    $color = (_go($id."_color"))? "color:"._go($id."_color").";":'';
    $font = (_go($id."_font"))? "font-family:"._go($id."_font").";":'';
    $size = (_go($id."_size"))? "font-size:"._go($id."_size")."$units;":'';
    return array('color'=>$color,'font'=>$font,'size'=>$size);
}

function _estyle_changer($id,$units = 'px'){
    $color = (_go($id."_color"))? "color:"._go($id."_color").";":'';
    $font = (_go($id."_font"))? "font-family:"._go($id."_font").";":'';
    $size = (_go($id."_size"))? "font-size:"._go($id."_size")."$units;":'';
    echo $color.$font.$size;
}

function _gcustom_styler($repeater_id){
    $style = "";
    foreach (_go_repeated($repeater_id) as $styler_index => $styler) {
        if ( !empty($styler['custom_selector']) && ( !empty($styler['custom_color']) || !empty($styler['custom_bg_color']) ) ){
            $style .= $styler['custom_selector'] . "{" ;
            $important = !empty($styler['important']) ? " !important" : "";
            $style .= !empty($styler['custom_color']) ? "color: " . $styler['custom_color']  . $important . ";" : "";
            $style .= !empty($styler['custom_bg_color']) ? "background-color: " . $styler['custom_bg_color']  . $important . ";" : "";
            $style .=  "}";
        }
    }
    return $style;
}

function tt_text_css($option_id,$selector,$units = 'px'){
    $style = $selector . "{" ;
    $settings = _gstyle_changer($option_id,$units);
    foreach ($settings as $setting => $value) {
        $style .= $value;
    }
    $style .=  "}";
    if($style == "$selector{}")
        return NULL;
    return $style;
}

function _esocial_platforms($social_platforms = array(
    'facebook',
    'twitter',
    'pinterest',
    'flickr',
    'dribbble',
    'behance',
    'google',
    'linkedin',
    'youtube',
    'rss'),$prefix='',$suffix='',$fa=false){
    foreach($social_platforms as $platform): 
        if (_go('social_platforms_' . $platform)):?>
            <li>
                <a href="<?php _eo('social_platforms_' . $platform) ?>" target="_blank">
                    <?php if($fa) : ?>
                        <i class="fa fa-<?php echo esc_attr($platform); ?>"></i>
                    <?php else: ?>
                        <img src="<?php echo TT_THEME_URI ?>/images/socials/<?php echo esc_attr($prefix.$platform.$suffix) ?>.png" alt="<?php echo esc_attr($platform) ?>" />
                    <?php endif; ?>
                </a>
            </li>
        <?php endif;
    endforeach;
}

//==========Form Builder functions=============
//Gets one form from contact builder  by id
function tt_get_form($id){
    $forms = get_option( THEME_OPTIONS . '_forms' );
    if(!empty($forms)){
        $the_form = NULL;
    foreach ($forms as $key => $form) {
        if($form['id'] == $id){
            $the_form = $form;
            break;
        }
    }
    if($the_form)
        return $the_form;
    else
        return NULL;
    }else
    return FALSE;
}

//Displays one form from contact builder  by id
function tt_form($id){
    $the_form = tt_get_form($id);
    if($the_form)
        TT_Contact_Form_Builder::render_form($id,$the_form);
    else
        return NULL;
}

//Gets all the forms from contact form builder
function tt_get_forms(){
    $forms = get_option( THEME_OPTIONS . '_forms' );
    return $forms;
}

//gets all the forms by location
function tt_form_location($location){
    $forms = tt_get_forms();
    if(!empty($forms)){
        foreach ($forms as $form) {
            if($form['location'] === $location)
                tt_form($form['id']);
        }
    }else
        return FALSE;
}

function tt_get_page_id($shop=false){
    global $wp_query;
    if(get_query_var('page_id'))
        $page_id = get_query_var('page_id');
    elseif(!empty($wp_query->queried_object) && !empty($wp_query->queried_object->ID))
        $page_id = $wp_query->queried_object->ID;
    elseif($shop)
        $page_id = get_option( 'woocommerce_shop_page_id' );
    else
        $page_id = false;
    return $page_id;
}