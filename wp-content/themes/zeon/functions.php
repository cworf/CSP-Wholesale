<?php
define('IMAGES', get_template_directory_uri() . '/images/');
/***********************************************************************************************/
/*  Tesla Framework */
/***********************************************************************************************/
require_once(get_template_directory() . '/tesla_framework/tesla.php');

/***********************************************************************************************/
/*  Register Plugins */
/***********************************************************************************************/
if ( is_admin() && current_user_can( 'install_themes' ) ) {
    require_once( get_template_directory() . '/plugins/tgm-plugin-activation/register-plugins.php' );
}

/***********************************************************************************************/
/* Load JS and CSS Files - done with TT_ENQUEUE */
/***********************************************************************************************/

/***********************************************************************************************/
/* Google fonts + Fonts changer */
/***********************************************************************************************/
TT_ENQUEUE::$base_gfonts = array('://fonts.googleapis.com/css?family=Enriqueta:300,400,700|Open+Sans:300italic,400italic,700italic,300,400,600,700');
TT_ENQUEUE::$gfont_changer = array(
        _go('logo_text_font'),
        _go('main_content_text_font'),
        _go('sidebar_text_font'),
        _go('menu_text_font')
    );
TT_ENQUEUE::add_js(array('http://w.sharethis.com/button/buttons.js'));
/***********************************************************************************************/
/* Custom CSS */
/***********************************************************************************************/
add_action('wp_enqueue_scripts', 'tesla_custom_css', 99);
function tesla_custom_css() {
    $custom_css = _go('custom_css') ? _go('custom_css') : '';
    wp_add_inline_style('tt-main-style', $custom_css);
}

add_action('wp_enqueue_scripts', 'tt_style_changers',99);
function tt_style_changers(){
    $background_color = _go('bg_color') ;
    $background_image = _go('bg_image') ;
    if($background_image || $background_color)
        wp_add_inline_style('tt-main-style', "body{background-color: $background_color;background-image: url('$background_image')}");

    $colopickers_css = '';
    if (_go('site_color')) : 
        $colopickers_css .= '
        .product .product-details,
        .product .product-cover .product-cover-hover span
        {
            background-color: ' . _go('site_color') . ';
        }';
    endif;
    if (_go('site_color_2')) :
        $colopickers_css .= '
            a:hover {
                color: ' . _go('site_color_2') . ';
            }
            .neccesary {
                color: ' . _go('site_color_2') . ';
            }
            .site-title .wrapper-arrows li i:hover {
                color: ' . _go('site_color_2') . ';
            }
            .alert.alert-warning h4 {
                color: ' . _go('site_color_2') . ';
            }
            .button-2 {
                background-color: ' . _go('site_color_2') . ';
                border-color: ' . _go('site_color_2') . '; 
            }
            .button-4 {
                background-color: ' . _go('site_color_2') . ';
            }
            .button-7 {
                background-color: ' . _go('site_color_2') . ';
            }
            .shop-links .shop-links-box {
                background-color: ' . _go('site_color_2') . ';
            }
            .shop-links .shop-links-box a {
                color: ' . _go('site_color_2') . ';
            }
            .the-slider .the-bullets-dots li.active span {
                background-color: ' . _go('site_color_2') . ';
            }
            .header .search-cart .cart-all .inside-cart ul li a:hover {
                color: ' . _go('site_color_2') . ';
            }
            .header .header-top-info .header-top-socials li a:hover {
                color: ' . _go('site_color_2') . ';
            }
            .header .header-middle-account li a:hover {
                color: ' . _go('site_color_2') . ';
            }
            .header .menu .repsonsive-menu:hover {
                background-color: ' . _go('site_color_2') . ';
            }
            .header .menu ul li.active ul li a:hover,
            .header .menu ul li.active a,
            .header .menu ul li a:hover,
            .header .menu ul li.menu-item-has-children ul li a:hover,
            .header .menu ul li.menu-item-has-children.active ul li a:hover {
                background-color: ' . _go('site_color_2') . ';
            }
            .header .menu ul li.menu-item-has-children a:hover,
            .header .menu ul li.menu-item-has-children.active a {
                background-color: ' . _go('site_color_2') . ';
            }
            .login-form-box .lost-password {
                color: ' . _go('site_color_2') . ';
            }
            .pricing-table-2.pricing-table-favorite .pricing-table-name,
            .pricing-table-1.pricing-table-favorite .pricing-table-name {
                background-color: ' . _go('site_color_2') . ';
            }
            .pricing-table-1 .pricing-table-price {
                background-color: ' . _go('site_color_2') . '; 
            }
            .pricing-table-2 .pricing-table-price a {
                background-color: ' . _go('site_color_2') . ';
            }
            .our-team .our-team-member .hover-effect i {
                background-color: ' . _go('site_color_2') . ';
            }
            .our-team .our-team-member-details .our-team-member-socials li a i {
                color: ' . _go('site_color_2') . ';
            }
            .shopping-cart .shopping-product-detail li.shopping-2 a:hover {
                color: ' . _go('site_color_2') . ';
            }
            .statistics-circle {
                color: ' . _go('site_color_2') . ';  
            }
            .testimonials .testimonials-dots li.active span {
                background-color: ' . _go('site_color_2') . ';
            }
            .sort-dropdown:hover span,
            .sort-dropdown ul li a:hover {
                color: ' . _go('site_color_2') . ';
            }
            .products-dropdown-close:hover i,
            .products-dropdown-close:hover {
                color: ' . _go('site_color_2') . ';
            }
            .product-rate {
                color: ' . _go('site_color_2') . ';
            }
            .product-one .tab-content .product-details li a:hover {
                color: ' . _go('site_color_2') . ';
            }
            .shopping-product-detail .plus:hover,
            .shopping-product-detail .minus:hover,
            .product-one .quantity .plus:hover,
            .product-one .quantity .minus:hover {
                background-color: ' . _go('site_color_2') . ';
                border-color: ' . _go('site_color_2') . ';
            }
            .item h5 a:hover {
                color: ' . _go('site_color_2') . ';
            }
            .item .item-price {
                color: ' . _go('site_color_2') . ';
            }
            .widget .widget-archives li a:hover,
            .widget .widget-category li a:hover {
                color: ' . _go('site_color_2') . ';
            }
            .widget .tagcloud a:hover {
                background-color: ' . _go('site_color_2') . ';
            }
            .widget .widget-best-seller li .item-price {
                color: ' . _go('site_color_2') . ';
            }
            .blog-entry .entry-header .blog-entry-details li a:hover,
            .blog-entry .entry-header h1 a:hover {
                color: ' . _go('site_color_2') . ';
            }
            .home-blog-show .blog-entry .entry-header .blog-entry-details li a:hover {
                color: ' . _go('site_color_2') . ';
            }
            .comments-area .commentlist .comment .comment-info a:hover {
                color: ' . _go('site_color_2') . ';
            }
            .page-numbers li span {
                color: ' . _go('site_color_2') . ';
            }
            .page-numbers li a:hover {
                color: ' . _go('site_color_2') . ';
            }
            .related-post a:hover {
                color: ' . _go('site_color_2') . ';
            }
            .related-post .related-post-cover {
                background-color: ' . _go('site_color_2') . ';
            }
            .content .social-share li a:hover {
                color: ' . _go('site_color_2') . ';
            }
            .subscription .subscription-line.s_error {
                border-color: ' . _go('site_color_2') . ' !important;
            }
            .wrapper-arrows li i {
                background-color: ' . _go('site_color_2') . ';
            }
            .widget_rss li a:hover,
            .widget_pages li a:hover,
            .widget_meta li a:hover, 
            .widget_categories li a:hover,
            .widget_archive li a:hover,
            .widget_recent_comments li a:hover,
            .widget_recent_entries li a:hover {
                color: ' . _go('site_color_2') . ';
            }
            .woocommerce-breadcrumb a:hover {
                color: ' . _go('site_color_2') . ';
            }
            .single-product .single_add_to_cart_button {
                background-color: ' . _go('site_color_2') . ';
                border-color: ' . _go('site_color_2') . ';
            }
            #commentform #submit {
                background-color: ' . _go('site_color_2') . ';
            }

            .widget button,
            .widget .button,
            .widget input[type="button"],
            .widget input[type="reset"],
            .widget input[type="submit"] {
                background-color: ' . _go('site_color_2') . ';
            }';
    endif;

    wp_add_inline_style('tt-main-style', $colopickers_css);

    //Custom Fonts Changers
    wp_add_inline_style('tt-main-style', tt_text_css('main_content_text','body','px'));
    wp_add_inline_style('tt-main-style', tt_text_css('sidebar_text','.shop-sidebar,.main-sidebar','px'));
    wp_add_inline_style('tt-main-style', tt_text_css('menu_text','.menu ul li a','px'));
    //Custom Styler
    wp_add_inline_style('tt-main-style', _gcustom_styler('Custom Styler'));
}

/***********************************************************************************************/
/* Custom JS */
/***********************************************************************************************/
add_action('wp_footer', 'tesla_custom_js', 99);
function tesla_custom_js() {
    ?>
    <script type="text/javascript"><?php _eo('custom_js') ?></script>
    <?php
}
/***********************************************************************************************/
/* Add Menus */
/***********************************************************************************************/

function tt_register_menus(){
    register_nav_menus(
        array(
            'main_menu'    => _x('Main menu', 'dashboard','zeon'),
        )
    );
}
add_action('init', 'tt_register_menus');

/***********************************************************************************************/
/* Add Shortcodes */
/***********************************************************************************************/

get_template_part('shortcodes');

/***********************************************************************************************/
/* Add Widgets */
/***********************************************************************************************/

require_once(TT_THEME_DIR . '/widgets/widget-subscription.php');
require_once(TT_THEME_DIR . '/widgets/widget-footer-social.php');

/* ========================================================================================================================

  Comments

  ======================================================================================================================== */
 
function tt_custom_comments( $comment, $args, $depth ) {
    $GLOBALS['comment'] = $comment;
    extract($args, EXTR_SKIP);

    if ( 'div' == $args['style'] ) {
        $tag = 'div';
        $add_below = 'comment';
    } else {
        $tag = 'li';
        $add_below = 'div-comment';
    }
?>

    <<?php echo $tag ?> id="comment-<?php comment_ID() ?>">
        <?php if ( 'div' != $args['style'] ) : ?>
            <div id="div-comment-<?php comment_ID() ?>" <?php comment_class(empty( $args['has_children'] ) ? '' : 'parent') ?>>
        <?php endif; ?>
        <span class="comment-image">
            <?php if ($args['avatar_size'] != 0)
                echo get_avatar( $comment, $args['avatar_size'], false,'avatar image' ); ?>
        </span>
        <?php if ($comment->comment_approved == '0') : ?>
            <em class="comment-awaiting-moderation">
                <?php _e('Your comment is awaiting moderation.','zeon') ?>
            </em>
            <br />
        <?php endif; ?>

        <span class="comment-info">
            <?php comment_reply_link(array_merge( $args, array('add_below' => $add_below, 'depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
            <?php echo get_comment_author_link() ?>
            <?php edit_comment_link(__('(Edit)','zeon'),'  ','' );?>
            <span><?php echo get_comment_time('d M Y') ?></span>
        </span>
        <?php comment_text() ?>
    <?php if ( 'div' != $args['style'] ) : ?>
    </div>
    <?php endif; 

}

function tt_custom_comments_closed( $comment, $args, $depth ) {
    $GLOBALS['comment'] = $comment;
    extract($args, EXTR_SKIP);

    if ( 'div' == $args['style'] ) {
        $tag = 'div';
        $add_below = 'comment';
    } else {
        $tag = 'li';
        $add_below = 'div-comment';
    }
    
    if($comment->comment_type == 'pingback' || $comment->comment_type == 'trackback'):?>
        <<?php echo $tag ?> id="comment-<?php comment_ID() ?>">
        <?php if ( 'div' != $args['style'] ) : ?>
            <div id="div-comment-<?php comment_ID() ?>" <?php comment_class(empty( $args['has_children'] ) ? '' : 'parent') ?>>
        <?php endif; ?>
        <span class="comment-image">
            <?php if ($args['avatar_size'] != 0)
                echo get_avatar( $comment, $args['avatar_size'], false,'avatar image' ); ?>
        </span>
        <?php if ($comment->comment_approved == '0') : ?>
            <em class="comment-awaiting-moderation">
                <?php _e('Your comment is awaiting moderation.','zeon') ?>
            </em>
            <br />
        <?php endif; ?>

        <span class="comment-info">
            <?php comment_reply_link(array_merge( $args, array('add_below' => $add_below, 'depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
            <?php echo get_comment_author_link() ?>
            <?php edit_comment_link(__('(Edit)','zeon'),'  ','' );?>
            <span><?php echo get_comment_time('d M Y') ?></span>
        </span>
        <?php comment_text() ?>
        <?php if ( 'div' != $args['style'] ) : ?>
            </div>
        <?php endif; 
    endif; 

}

/***********************************************************************************************/
/* Add Sidebar Support */
/***********************************************************************************************/
function tt_register_sidebars(){
    if (function_exists('register_sidebar')) {
        register_sidebar(
            array(
                'name'           => __('Blog Sidebar', 'zeon'),
                'id'             => 'blog',
                'description'    => __('Blog Sidebar Area', 'zeon'),
                'before_widget'  => '<div class="col-md-12 col-xs-6"><div class="widget %2$s">',
                'after_widget'   => '</div></div>',
                'before_title'   => '<div class="widget-title">',
                'after_title'    => '</div>'
            )
        );
        register_sidebar(
            array(
                'name'           => __('Page', 'zeon'),
                'id'             => 'page',
                'description'    => __('Page Sidebar Area', 'zeon'),
                'before_widget'  => '<div class="col-md-12 col-xs-6"><div class="widget %2$s">',
                'after_widget'   => '</div></div>',
                'before_title'   => '<div class="widget-title">',
                'after_title'    => '</div>'
            )
        );
        register_sidebar(
            array(
                'name'           => __('Footer', 'zeon'),
                'id'             => 'footer',
                'description'    => __('Footer Area', 'zeon'),
                'before_widget'  => '<div class=" col-xs-6 footer-widget %2$s">',
                'after_widget'   => '</div>',
                'before_title'   => '<h3 class="footer-widget-title">',
                'after_title'    => '</h3>'
            )
        );
        register_sidebar(
            array(
                'name'           => __('Shop', 'zeon'),
                'id'             => 'shop',
                'description'    => __('Sidebar on the shopping page', 'zeon'),
                'before_widget'  => '<div class="widget col-md-12 col-xs-6 %2$s">',
                'after_widget'   => '</div>',
                'before_title'   => '<h3 class="widget-title">',
                'after_title'    => '</h3>'
            )
        );
    }
}
add_action('widgets_init','tt_register_sidebars');

//calculates width for each widget in footer area 
function tt_footer_sidebar_params($params) {
    $sidebar_id = $params[0]['id'];

    if ( $sidebar_id == 'footer' ) {
        $total_widgets = wp_get_sidebars_widgets();
        $sidebar_widgets = count($total_widgets[$sidebar_id]);
        if($sidebar_widgets == 5 && $params[0]['widget_name'] == '[Zeon] Subscription')
            $params[0]['before_widget'] = str_replace('class="', 'class="col-md-' . floor(24 / $sidebar_widgets), $params[0]['before_widget']);    
        else
            $params[0]['before_widget'] = str_replace('class="', 'class="col-md-' . floor(12 / $sidebar_widgets), $params[0]['before_widget']);
    }

    return $params;
}
add_filter('dynamic_sidebar_params','tt_footer_sidebar_params');
// add post-formats to post
//add_theme_support('post-formats', array('quote', 'gallery', 'video', 'audio', 'image'));


function tt_share(){
    $share_this = _go('share_this');
    if(isset($share_this)): ?>
        <ul class="social-share">
            <li><span><?php _ex('Share','single-post','zeon'); ?></span></li>
            <?php foreach($share_this as $val): ?>
                <li>
                    <a href="#"><span class='st_<?php echo $val ?>_large' displayText='<?php echo ucfirst($val) ?>'></span></a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif;
}

/*==== Function Call custom meta boxex ====*/
function tt_video_or_image_featured($echo = false) {
    global $post;
    $embed_code = get_post_meta($post->ID , THEME_NAME . '_video_embed', true);
    $patern = '<div class="entry-cover">%s</div>';

    if($echo){

        if(!empty($embed_code)) {
            return sprintf($patern, $embed_code);
        }else {
            if( has_post_thumbnail() && ! post_password_required() ){
                return sprintf($patern, get_the_post_thumbnail());
            }
        }

    }else{

        if(!empty($embed_code)) {
            printf($patern, $embed_code);
        }else {
            if( has_post_thumbnail() && ! post_password_required() ){
                printf($patern, get_the_post_thumbnail());
            }
        }

    }
}

function tt_ajax_contact_form () {
    $receiver_mail = (_go('email_contact')) ? _go('email_contact') : get_bloginfo( 'admin_email' );

    if (!empty($_POST['name']) && !empty($_POST ['email']) && !empty($_POST ['message'])) {
        $subject = (!empty($_POST['website'])) ? $_POST['name'] . ' from ' . $_POST['website'] : ' from ' . get_bloginfo( 'name' ) . ' Contact form';
        $email = $_POST['email'];
        $message = $_POST['message'];
        $message = wordwrap($message, 70, "\r\n");
        $header []= 'From: '. $_POST['name'] .'<' . $_POST ['email'] . '>';
        $header []= 'Reply-To: ' . $email;
    
        if ( wp_mail( $receiver_mail, $subject, $message, $header ) )
            $result = __('Message successfully sent.', 'zeon');
        else
            $result = __('Message could not be sent.', 'zeon');
    }else
        $result = __('Please fill all the fields','zeon');
    die($result);
}
add_action('wp_ajax_tt_ajax_contact_form', 'tt_ajax_contact_form');           // for logged in user  
add_action('wp_ajax_nopriv_tt_ajax_contact_form', 'tt_ajax_contact_form');    // if user not logged in

//Search page
function cut_shortcodes($content) {
    return preg_replace('@\[.*?\]@', '', $content);
}

function tt_related_posts($the_post){
    //for use in the loop, list 5 post titles related to first tag on current post
    $tags = wp_get_post_tags($the_post->ID);
    if ($tags) {
        $first_tag = $tags[0]->term_id;
        $args=array(
            'tag__in' => array($first_tag),
            'post__not_in' => array($the_post->ID),
            'posts_per_page'=>5,
            'caller_get_posts'=>1
        );
        $related_query = new WP_Query($args);
        if( $related_query->have_posts() ) : ?>
            <div class="site-title">
                <div class="site-inside">
                    <span><?php _e('Related posts','zeon') ?></span>
                </div>
            </div>
            <div class="row">
                <?php while ($related_query->have_posts()) : $related_query->the_post(); ?>
                    <div class="col-md-3 col-xs-6">
                        <div class="related-post">
                            <div class="related-post-cover">
                                <?php if(has_post_thumbnail( )) 
                                        the_post_thumbnail( ); ?>
                            </div>
                            <a href="<?php the_permalink(); ?>"><?php the_title( ); ?></a>
                        </div>
                    </div>
                <?php endwhile;?>
            </div>
        <?php endif;
        wp_reset_query();
    }

}
// =========================================================================
//                         WOOCOMMERCE ACTIONS
// =========================================================================
add_theme_support( 'woocommerce' );
// ------------ Shop Page ---------------------
add_action('woocommerce_before_main_content','tesla_before_main_content');
function tesla_before_main_content(){
    if(is_shop() || is_product_category() || is_product_tag()) : ?>
        <div class="container">
            
    <?php elseif(is_product()) : ?>
        <div class="product-one">
            <div class="container">
                <div class="row">
    <?php endif;
}

add_action('woocommerce_after_main_content','tesla_after_main_content');
function tesla_after_main_content(){
    ?>
                </div>
            </div>
        </div>
    <?php
}



//disable woocommerce styles
add_filter( 'woocommerce_enqueue_styles', '__return_false' );

function manage_action_woo(){
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 10 );
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
    add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 40 );
    add_action( 'woocommerce_after_single_product_summary', 'woocommerce_template_single_meta', 15 );

    remove_action( 'woocommerce_before_shop_loop', 'wc_print_notices');
    add_action( 'tesla_show_wc_archive_messages', 'wc_print_notices' );
}
add_action('init','manage_action_woo');

//Ajaxify cart
// Ensure cart contents update when products are added to the cart via AJAX
add_filter('add_to_cart_fragments', 'woocommerce_header_add_to_cart_fragment');
 
function woocommerce_header_add_to_cart_fragment( $fragments ) {
    global $woocommerce;
    
    ob_start();
    
    ?>
    <div class="cart-all">
        <a href="<?php echo get_permalink( wc_get_page_id( 'cart' ) ); ?>"><i class="icon-19" title="19"></i> <?php echo $woocommerce->cart->get_cart_subtotal(); ?></a>
        <div class="inside-cart">
            <?php if (count($woocommerce->cart->get_cart()) > 0) : ?>
                <p><?php echo $woocommerce->cart->get_cart_contents_count(); _e(' items in the shopping bag','zeon')?></p>
                <ul>
                    <?php foreach ($woocommerce->cart->get_cart() as $cart_item_key => $cart_item) :
                        $_product = $cart_item['data'];
                        // Only display if allowed
                        if (!apply_filters('woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key) || !$_product->exists() || $cart_item['quantity'] == 0)
                            continue;

                        // Get price
                        $product_price = get_option('woocommerce_tax_display_cart') == 'excl' ? $_product->get_price_excluding_tax() : $_product->get_price_including_tax();

                        $product_price = apply_filters('woocommerce_cart_item_price_html', woocommerce_price($product_price), $cart_item, $cart_item_key);
                        ?>
                        <li>
                            <div class="inside-cart-image">
                                <?php echo $_product->get_image(); ?>
                            </div>
                            <?php echo apply_filters('woocommerce_cart_item_remove_link', sprintf('<a href="%s" class="remove" title="%s">&times;</a>', esc_url($woocommerce->cart->get_remove_url($cart_item_key)), __('Remove this item', 'woocommerce')), $cart_item_key); ?>
                            <a href="<?php echo get_permalink($cart_item['product_id']); ?>"><?php echo apply_filters('woocommerce_widget_cart_product_title', $_product->get_title(), $_product); ?></a>
                            <p><?php _e('Unit price ','zeon') ; echo $product_price; ?></p>
                            <p><?php _e('Q-ty: ','zeon') ; echo $cart_item['quantity']; ?></p>
                        </li>
                    <?php endforeach;?>
                </ul>
            <?php else: ?>
                <p><?php _e('No items in cart. Keep shopping.', 'hudson'); ?></p>
            <?php endif; ?>
        </div>
    </div>
    <?php
    
    $fragments['div.cart-all'] = ob_get_clean();
    
    return $fragments;
    
}

//Removing breadcrumbs
if(!_go('show_breadcrumbs'))
    remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0);

/*add_filter( 'locale', 'my_theme_localized' );
function my_theme_localized( $locale )
{
    
    return 'ro_RO';
}*/