</div>
<div class="box">
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
                    <?php foreach($slides as $i => $slide) : 
                        $title_color = !empty($slide['options']['title_color']) ? ' style="color:' . $slide['options']['title_color'] . '"' : '';
                        $icon_bg_color = !empty($slide['options']['icon']['def_icon']['bg_color']) ? 'background-color:' . $slide['options']['icon']['def_icon']['bg_color'] . ';' : '';
                        $icon_color = !empty($slide['options']['icon']['def_icon']['icon_color']) ? 'color:'.$slide['options']['icon']['def_icon']['icon_color'].';' : '';
                        $icon_style = !empty($icon_bg_color) || !empty($icon_color) ? " style='$icon_bg_color$icon_color'" :'';?>
                        <div class="col-md-4">
                            <div class="service-1"<?php echo $icon_bg_color ?>>
                                <h4<?php echo $title_color ?>><?php echo get_the_title($slide['post']->ID); ?></h4>
                                <?php if(!empty($slide['options']['icon']['custom_icon'])) : ?>
                                    <img src="<?php echo $slide['options']['icon']['custom_icon'] ?>" alt="">
                                <?php else: ?>
                                    <i class="icon-<?php echo $slide['options']['icon']['def_icon']['icon_nr']?>"<?php echo $icon_style ?>></i>
                                <?php endif; ?>
                                <div class="service-description">
                                    <?php echo apply_filters( 'the_content' ,$slide['post']->post_content);?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container">
<!-- =====================================================================
                                 END SERVICES
====================================================================== -->