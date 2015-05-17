<?php
if (!defined('ABSPATH'))
    die("Can't load this file directly");

add_action('admin_menu', 'dp_register_my_custom_submenu_page');

function dp_register_my_custom_submenu_page() {
        global $menu, $woocommerce;
        if ( current_user_can( 'manage_woocommerce' ) )
            add_submenu_page( 'woocommerce', __( 'Display Product', DP_TEXTDOMAN ),  __( 'Display Product', DP_TEXTDOMAN ), 'manage_options', 'display-product-page', 'display_product_callback' ); 
}

function display_product_callback() {
    if (isset($_POST["update_settings"])) {
        update_option("dp_replace_woo_page", esc_attr($_POST["dp_replace_woo_page"]));
        update_option("dp_product_shop_page", esc_attr($_POST["dp_product_shop_page"]));
        update_option("dp_product_category_page", esc_attr($_POST["dp_product_category_page"]));
        update_option("dp_product_tag_page", esc_attr($_POST["dp_product_tag_page"]));
        update_option("dp_product_search_page", esc_attr($_POST["dp_product_search_page"]));
        
        update_option("display_product_thumbnail_image_size-width", esc_attr($_POST["display_product_thumbnail_image_size-width"]));
        update_option("display_product_thumbnail_image_size-height", esc_attr($_POST["display_product_thumbnail_image_size-height"]));
        update_option("display_product_thumbnail_image_size-crop", esc_attr($_POST["display_product_thumbnail_image_size-crop"]));
        echo '<div id="message" class="updated">Settings saved</div>';
    }
    $ex_page_id=array();
    $ex_page_id[]=get_option('woocommerce_shop_page_id'); 
    $ex_page_id[]=get_option('woocommerce_cart_page_id'); 
    $ex_page_id[]=get_option('woocommerce_checkout_page_id');
    $ex_page_id[]=get_option(' woocommerce_pay_page_id '); 
    $ex_page_id[]=get_option(' woocommerce_thanks_page_id '); 
    $ex_page_id[]=get_option(' woocommerce_myaccount_page_id '); 
    $ex_page_id[]=get_option(' woocommerce_edit_address_page_id '); 
    $ex_page_id[]=get_option(' woocommerce_view_order_page_id '); 
    $ex_page_id[]=get_option(' woocommerce_terms_page_id '); 
    $ex_page_id[]=get_option(' woocommerce_logout_page_id '); 
    $ex_page_id[]=get_option(' woocommerce_lost_password_page_id '); 
    
    $args = array(
        'posts_per_page'   => -1,
        'orderby'          => 'modified',
        'order'            => 'DESC',
        'post_type'        => 'page',
        'post_status'      => 'publish',
        'exclude'           => $ex_page_id);
    $posts = get_posts($args);
    echo '<form action="" method="post"><div class="dp-container">
            <div class="wrap">';
	echo '<div class="dp-title-block"><div class="wrap"><div id="icon-tools"><img src="'.plugin_dir_url(__FILE__).'/assets/js/display-icon.png"></div>';
		echo '<h2>Display product Options</h2>
                        <h5>
                            <span> &nbsp;| <a href="http://sureshopress.com/display-product-for-woocommerce/document" target="_blank">View Plugin Documentation</a></span>
                            <span>Display Product Version '.DP_VER.'</span>
                        </h5>';
	echo '</div></div>';
        
        echo '<div id="dp-header-block" class="dp-header-block clearfix">
                        <h3>Display Product for WooCommerce Customizer </h3>
              </div>';
        
        echo '<div id="dp-content-block" class="clearfix">
                    <ul class="dp-content clearfix" id="tab-1">
                        <div class="dp-rss-widget">
                            <div class="dp-table table_content">
                            <table class="dp-widefat">
                                <thead>
                                        <tr>
                                                <th>Replace WooCommerce Page</th>
                                        </tr>
                                </thead>
                                <tbody>
                                <tr><td><div class="admin-description">
                                                <h4>Replace WooCommerce Page</h4>
                                                <p> </p>
                                            </div>
                                            <div class="admin-content">
                                                <div class="form-wrap">
                                                    <label><input '.(get_option('dp_replace_woo_page',0) ? 'checked="checked"':'').' type="radio" class="dp-rwp" name="dp_replace_woo_page" value="1"> Yes</label>
                                                    <label><input '.(get_option('dp_replace_woo_page',1)? '':'checked="checked" ').' type="radio" class="dp-rwp" name="dp_replace_woo_page" value="0"> No</label>
                                                </div>
                                            </div>
                                    </td></tr>
                                    <script type="text/javascript">
                                    jQuery(document).ready(function(){
                                        jQuery("input.dp-rwp").on( "click", function() {
                                            if(jQuery(this).val()==1){
                                                jQuery(".dp_wsp").removeClass("dp_hide");
                                            }else{
                                                jQuery(".dp_wsp").addClass("dp_hide");
                                            }
                                        });
                                    });
                                    </script>';
                            if(!get_option('dp_replace_woo_page',false)){ $dp_replace_woo_page=' dp_hide ';}
                                    echo '<tr class="dp_wsp '.$dp_replace_woo_page.'"><td><div class="admin-description">
                                                <h4>Shop Page</h4>
                                                <p>Redirect WooCommerce Shop page to display product for woocommerce page.</p>
                                            </div>
                                            <div class="admin-content">
                                                <div class="form-wrap">
                                                    <select size="1" name="dp_product_shop_page" id="dp_product_shop_page">
                                                    
                                                           <option value="disable">Disable</option>';
                                        foreach ($posts as $post): ?>  
                                                <?php $selected='';if(get_option("dp_product_shop_page") == $post->ID) $selected=' selected="selected" '; ?>
                                                <option value="<?php echo $post->ID; ?>" <?php echo $selected;?>>  
                                                    <?php echo $post->post_title; ?>  
                                                </option>  
                                        <?php endforeach;
                                                    echo '</select>
                                                </div>
                                            </div>
                                    </td></tr>
                                    <tr  class="dp_wsp '.$dp_replace_woo_page.'"><td><div class="admin-description">
                                                <h4>Category Page</h4>
                                                <p>Redirect WooCommerce Category page to display product for woocommerce page.</p>
                                            </div>
                                            <div class="admin-content">
                                                <div class="form-wrap">
                                                    <select size="1" name="dp_product_category_page" id="dp_product_category_page">
                                                       <option value="disable">Disable</option>';
                                        foreach ($posts as $post): ?>  
                                                <?php $selected='';if(get_option("dp_product_category_page") == $post->ID) $selected=' selected="selected" '; ?>
                                                <option value="<?php echo $post->ID; ?>" <?php echo $selected;?>>
                                                    <?php echo $post->post_title; ?>  
                                                </option>  
                                        <?php endforeach;
                                                    echo '</select>
                                                </div>
                                            </div>
                                    </td></tr>
                                    <tr  class="dp_wsp '.$dp_replace_woo_page.'"><td><div class="admin-description">
                                                <h4>Tag Page</h4>
                                                <p>Redirect WooCommerce Tag page to display product for woocommerce page.</p>
                                            </div>
                                            <div class="admin-content">
                                                <div class="form-wrap">
                                                    <select size="1" name="dp_product_tag_page" id="dp_product_tag_page">
                                                            <option value="disable">Disable</option>';
                                        foreach ($posts as $post): ?>  
                                                <?php $selected='';if(get_option("dp_product_tag_page") == $post->ID) $selected=' selected="selected" '; ?>
                                                <option value="<?php echo $post->ID; ?>" <?php echo $selected;?>>
                                                    <?php echo $post->post_title; ?>  
                                                </option>  
                                        <?php endforeach;
                                                    echo '</select>
                                                </div>
                                            </div>
                                    </td></tr>
                                    <tr  class="dp_wsp '.$dp_replace_woo_page.'"><td><div class="admin-description">
                                                <h4>Search Page</h4>
                                                <p>Redirect WooCommerce Search page to display product for woocommerce page.</p>
                                            </div>
                                            <div class="admin-content">
                                                <div class="form-wrap">
                                                    <select size="1" name="dp_product_search_page" id="dp_product_search_page">
                                                            <option value="disable">Disable</option>';
                                        foreach ($posts as $post): ?>  
                                                <?php $selected='';if(get_option("dp_product_search_page") == $post->ID) $selected=' selected="selected" '; ?>
                                                <option value="<?php echo $post->ID; ?>" <?php echo $selected;?>>
                                                    <?php echo $post->post_title; ?>  
                                                </option>  
                                        <?php endforeach;
                                                    echo '</select>
                                                </div>
                                            </div>
                                    </td></tr>
                                    ';
                        
                        echo '</tbody>
                            </table><table class="dp-widefat">
                                <thead>
                                        <tr>
                                            <th>Display Product Options</th>
                                        </tr>
                                </thead>
                                <tbody>
                                    <tr><td><div class="admin-description">
                                                <h4>Default Display Product Thumbnails</h4>
                                                <p>These settings affect the actual dimensions of images in your catalog – the display on the front-end will still be affected by CSS styles. After changing these settings you may need to <a href="http://wordpress.org/extend/plugins/regenerate-thumbnails/">regenerate your thumbnails</a>.</p>
                                            </div>
                                            <div class="admin-content">
                                                <div class="dp-form-wrap">';
                                                    if(get_option("display_product_thumbnail_image_size-width")){ $width=get_option("display_product_thumbnail_image_size-width"); }else{$width=250;}
                                                    echo 'Width <input name="display_product_thumbnail_image_size-width" id="display_product_thumbnail_image_size-width" type="text" size="3" value="'.$width.'">';
                                                    
                                                    if(get_option("display_product_thumbnail_image_size-height") ){$height=get_option("display_product_thumbnail_image_size-height"); }else{$height=250;}
                                                    echo 'Height <input name="display_product_thumbnail_image_size-height" id="display_product_thumbnail_image_size-height" type="text" size="3" value="'.$height.'">';
                                                    
                                                    if(get_option("display_product_thumbnail_image_size-crop") ){ $crop='checked="checked"'; }
                                                    echo '<label>Hard Crop <input name="display_product_thumbnail_image_size-crop" id="display_product_thumbnail_image_size-crop" type="checkbox" '.$crop.'></label>

                                                </div>
                                            </div>
                                    </td></tr>
                                    
                                </tbody>
                            </table>
                            <table class="dp-widefat">
                                <thead>
                                        <tr>
                                            <th>Reset to default woocommerce page</th>
                                        </tr>
                                </thead>
                                <tbody>
                                    <tr><td><div class="admin-description">
                                                <h4>Reset Options</h4>
                                                <p>If you have issue redirect page error please click the Reset to default button.</p>
                                            </div>
                                            <div class="admin-content">
                                                <div class="dp-form-wrap">
                                                    <a href="http://localhost/display-product.com/wp-admin/admin.php?page=display-product-page&amp;reset_install_dp_pages=true" class="button-secondary">Reset to default</a>
                                                </div>
                                            </div>
                                    </td></tr>
                                    
                                </tbody>
                            </table>
                            <input type="hidden" name="update_settings" value="Y" />
                            <input name="Submit" type="submit" class="button-primary" value="Save Changes" />
                            <br/><br/>
                            <hr/>
                            <h3 style="margin-top: 60px;">Video Tutorial</h3>
                            <iframe width="916" height="515" src="//www.youtube.com/embed/O6-CeRHL7bQ?list=UUrj2Opzepvs4QYuvlUMMhKw" frameborder="0" allowfullscreen></iframe>
                        </div></div>
            </ul></div>';
    echo '</div></div></form>';

}

if(get_option('dp_replace_woo_page')){
    add_action( 'template_redirect', 'dp_page_template_redirect',1 );
    add_filter( 'term_link', 'dp_term_to_type', 12, 3 );
    add_filter( 'the_title','dp_change_page_title' ,12,2);
}

function dp_page_template_redirect()
{
    global $wp_query,$woocommerce,$wp,$_chosen_attributes;


    $dp_posttype=$wp_query->query_vars['post_type'];
    $dp_taxonomy=$wp_query->query_vars['taxonomy'];
    //PRODUCT CATEGORY
    $dp_redirect_to='';
    if ($dp_taxonomy === 'product_cat') {
        $cat_page=get_option("dp_product_category_page");
        if($cat_page!='disable'){
            $query_args=array_merge( $wp_query->query, array( 'dppage' => 1 ) );
            $dp_redirect_to = add_query_arg( $query_args, get_permalink($cat_page) );
        }
    }elseif($dp_posttype === 'product' && is_search()){
        $search_page=get_option("dp_product_search_page");
        if($search_page!='disable'){
            $query_args=array( 'dppage' => 1,'dp_search'=>$_GET['s']);
            $dp_redirect_to = add_query_arg( $query_args, get_permalink($search_page) );
        }
    }elseif ( $dp_posttype === 'product' && is_post_type_archive('product')&& !is_single() ) {
        //SHOP
        $shop_page=get_option("dp_product_shop_page");
        if($shop_page!='disable'){
            $query_args=array( 'dppage' => 1 );
            $dp_redirect_to = add_query_arg( $query_args ,get_permalink($shop_page));
        }
    }elseif ( $dp_taxonomy === 'product_tag' ) {
        // PRODUCT TAG
        $tag_page=get_option("dp_product_tag_page");
        if($tag_page!='disable'){
            $query_args=array_merge( $wp_query->query, array( 'dppage' => 1 ) );
            $dp_redirect_to = add_query_arg( $query_args, get_permalink($tag_page) );
        }
    }elseif ( $dp_posttype === 'product' && is_single() && !is_post_type_archive('product')) {
        //Shop and Product Detail
    }
    if($dp_redirect_to){
        wp_redirect($dp_redirect_to);
    }
    
}

function dp_term_to_type( $link, $term, $taxonomy ) {
    if ( $term->taxonomy=== 'product_cat' ) {
            //$post_id = my_get_post_id_by_slug( $term->slug, 'person' );
            $cat_page=get_option("dp_product_category_page");
            if($cat_page!='disable'){
                $dp_redirect_to = add_query_arg( array('product_cat'=>$term->slug,'dppage'=>1), get_permalink($cat_page) );
                if ( !empty( $dp_redirect_to ) ) return $dp_redirect_to;
            }
    }
    if ( $term->taxonomy=== 'product_tag' ) {

            //$post_id = my_get_post_id_by_slug( $term->slug, 'person' );
            $tag_page=get_option("dp_product_tag_page");
            if($tag_page!='disable'){
                $dp_redirect_to = add_query_arg( array('product_tag'=>$term->slug,'dppage'=>1), get_permalink($tag_page) );
                if ( !empty( $dp_redirect_to ) ) return $dp_redirect_to;
            }
    }
    return $link;
}

function dp_change_page_title($title, $id ){
    $admin=is_admin();
    if( ($id!=get_option("dp_product_shop_page")||
        $id!=get_option("dp_product_category_page")||
        $id!=get_option("dp_product_search_page")||
        $id!=get_option("dp_product_tag_page"))||
        $admin
        ){ return $title;}
    if($id==get_option("dp_product_shop_page")){
        return $title;
    }elseif($id==$cat_id=get_option("dp_product_category_page") ){
        $product_slug_cat=get_query_var('product_cat');
        $category_name=get_term_by('slug', $product_slug_cat, 'product_cat');
        return $category_name->name;
    }elseif($id==$tag_id=get_option("dp_product_tag_page") ){
        $product_slug_tag=get_query_var('product_tag');
        $tag_name=get_term_by('slug', $product_slug_tag, 'product_tag');
        return $tag_name->name;
    }elseif($id==get_option("dp_product_search_page") ){
        return $title.' - '.$_GET['dp_search'];
    }
}
    ?>