<?php get_header(); ?>
<div class="content">
    <div class="container">
        <div class="site-title"><div class="site-inside"><span><?php 
                                if (_go('error_title')) 
                                    _eo('error_title'); 
                                else 
                                    _e('Error 404','zeon') ?></span></div></div> 

        <div class="error-404">
            <img src="<?php if (_go('error_image')) 
                            _eo('error_image'); 
                          else 
                            echo IMAGES . 'elements/error-404.png'?>" alt="error-404">
            <h3><?php _e('Error','zeon') ?></h3>

            <form class="error-404-search">
                <input type="text" class="search-line" placeholder="<?php _e('Search','zeon') ?>" name="s" />
                <input type="submit" value="" class="search-button" />
            </form>

            <?php if (_go('error_message')) :
                _eo('error_message'); 
            else : ?>
                <h1><?php _e('Are you lost?','zeon') ?></h1>
                <h2><?php _e('SORRY, the page you asked for couldn\'t be found.','zeon') ?></h2>
                <p><?php _e('Please try using the search form above','zeon') ?></p>
            <?php endif;?>

        </div>
    </div>
</div>
<?php get_footer(); ?>