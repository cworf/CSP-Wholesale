</div>
<!-- =====================================================================
                                START OUR BRANDS
====================================================================== -->

<div class="container">
    <div class="tesla-carousel" data-tesla-plugin="carousel" data-tesla-container=".tesla-carousel-items" data-tesla-item=">div" data-tesla-autoplay="false" data-tesla-rotate="false">
        <div class="site-title">
            <ul class="wrapper-arrows">
                <li><i class="icon-517 prev" title="left arrow"></i></li>
                <li><i class="icon-501 next" title="right arrow"></i></li>
            </ul>
            <div class="site-inside">
                <span><?php echo $shortcode['title']; ?></span>
            </div>
        </div> 
        <div class="row">
            <div class="tesla-carousel-items">
                <?php foreach ($slides as $slide_nr => $slide) : ?>
                    <div class="col-md-2 col-xs-6">
                        <a href="<?php echo $slide['options']['link'] ?>">
                            <?php echo get_the_post_thumbnail($slide['post']->ID, 'full'); ?>
                        </a>                    
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- =====================================================================
                                 END OUR BRANDS
====================================================================== -->
<div class="container"> <!-- Clients Re-opening container -->