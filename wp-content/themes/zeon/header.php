<?php global $woocommerce ?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo('charset'); ?>">
    <title><?php wp_title('-', true, 'right'); ?><?php bloginfo('name'); ?></title>
    <meta name="description" content="<?php bloginfo('description'); ?>">
    <!-- Mobile Specific Meta -->
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
     <!-- Pingbacks -->
	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">
    <?php echo "<script type='text/javascript'>var TemplateDir='".TT_THEME_URI."'</script>" ?>
	<!-- Favicon -->
	<?php if(_go('favicon')): ?>
		<link rel="shortcut icon" href="<?php _eo('favicon') ?>">
	<?php endif; ?>
	<?php wp_head(); ?>
</head>
<body <?php body_class();?>>
    <!-- ======================================================================
                                    START Header
    ======================================================================= -->
    <div class="header">
        <div class="container">
            <div class="header-top-info">
                <ul class="header-top-socials">
                    <?php $social_platforms = array(
                        'facebook',
                        'twitter',
                        'pinterest',
                        'google',
                        'vimeo');
                        foreach($social_platforms as $platform): 
                            if (_go('social_platforms_' . $platform)):?>
                                <li>
                                    <a href="<?php echo _go('social_platforms_' . $platform) ?>"><i class="icon-<?php echo $platform ?>" title="<?php echo $platform ?>"></i></a>
                                </li>
                            <?php endif;
                        endforeach;?>
                </ul>
                <?php _eo('header_text') ?>
            </div>
            <div class="header-middle-info">
                <?php if(_go('logo_wrapper_size')) {
                    $logo_width = _go('logo_wrapper_size');
                    $menu_width = 12 - (int)_go('logo_wrapper_size');
                }else{
                    $logo_width = 4;
                    $menu_width = 8;
                }?>
                <div class="col-md-<?php echo $logo_width?>">
                    <div class="logo">
                        <a href="<?php echo home_url() ?>" style="<?php _estyle_changer('logo_text') ?>" >
                            <?php if(_go('logo_text')): ?>
                                <?php _eo('logo_text') ?>
                            <?php elseif(_go('logo_image')): ?>
                                <img src="<?php _eo('logo_image') ?>" alt="<?php echo THEME_PRETTY_NAME ?> logo">
                            <?php else: ?>
                                <?php echo THEME_PRETTY_NAME; ?>
                            <?php endif; ?>
                        </a>
                    </div>
                </div>
                <div class="col-md-<?php echo $menu_width?>">
                    <?php if(tesla_has_woocommerce()) : ?>
                        <ul class="header-middle-account">
                            <?php if ( is_user_logged_in() ) : ?>
                                <li><a href="<?php echo get_permalink( wc_get_page_id( 'myaccount' ) ); ?>" title="<?php _e('My Account','zeon'); ?>"><i class="icon-330" title="<?php _e('My Account','zeon'); ?>"></i><?php _e('My Account','zeon'); ?></a></li>
                                <li><a href="<?php echo wp_logout_url( get_permalink( wc_get_page_id( 'myaccount' ) ) ); ?>" title="<?php _e('Logout','zeon'); ?>"><i class="icon-351" title="<?php _e('Logout','zeon'); ?>"></i><?php _e('Logout','zeon'); ?></a></li>
                             <?php else : ?>
                                <li><a href="<?php echo get_permalink( wc_get_page_id( 'myaccount' ) ); ?>" title="<?php _e('Login / Register','zeon'); ?>"><i class="icon-351" title="<?php _e('Login / Register','zeon'); ?>"></i><?php _e('Login / Register','zeon'); ?></a></li>
                             <?php endif; ?>
                                <li><a href="#"><?php echo get_option('woocommerce_currency') ?></a></li>
                            <?php if (sizeof($woocommerce->cart->cart_contents)>0) :?>
                                <li><a href="<?php echo $woocommerce->cart->get_checkout_url()?>" title="<?php _e('Checkout','zeon') ?>"><i class="icon-259" title="<?php _e('Checkout','zeon') ?>"></i><?php _e('Checkout','zeon') ?></a></li>
                            <?php endif; ?>
                        </ul>
                    <?php endif ?>
                </div>
                <div class="clear"></div>
            </div>
            <div class="menu">
                <div class="search-cart">
                    <form class="search" action="<?php echo home_url( ); ?>">
                        <input type="text" name="s" placeholder="<?php _e('Search ...','zeon')?>" class="search-line">
                        <input type="submit" value="" class="search-button" />
                    </form>
                    <?php if(tesla_has_woocommerce()) : ?>
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
                                    <p><?php _e('No items in cart. Keep shopping.', 'zeon'); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="clear"></div>
                </div>
                <div class="repsonsive-menu"><i class="icon-406" title="Menu"></i> <?php _e('Menu','zeon') ?></div>
                <ul>
                    <?php wp_nav_menu( array( 
                                'title_li'=>'',
                                'theme_location' => 'main_menu',
                                'container' => false,
                                'items_wrap' => '%3$s',
                                'fallback_cb' => 'wp_list_pages'
                            ) );?>
                </ul>
                <div class="clear"></div>
            </div>
        </div>
    </div>
    <!-- ======================================================================
                                    END HEADER
    ======================================================================= -->  



    <!-- ======================================================================
                                    START CONTENT
    ======================================================================= -->
    <div class="content">