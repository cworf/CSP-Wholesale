<!-- START SERVICES -->
<?php 
if($shortcode['nr'])
    $slides = array_slice($slides, 0, (int)$shortcode['nr']);

?>
<div class="site-title">
    <div class="site-inside">
        <span><?php echo $shortcode['title']; ?></span>
    </div>
</div> 
<div class="row">
    <?php
    foreach($slides as $i => $slide):
        $title_color = !empty($slide['options']['title_color']) ? ' style="color:' . $slide['options']['title_color'] . '"' : '';
        $icon_bg_color = !empty($slide['options']['icon']['def_icon']['bg_color']) ? 'background-color:' . $slide['options']['icon']['def_icon']['bg_color'] . ';' : '';
        $custom_icon_fix = !empty($slide['options']['icon']['custom_icon']) ? 'padding-top:0;' : '';
        $service_style = !empty($icon_bg_color) || !empty($custom_icon_fix) ? ' style="'.$icon_bg_color.$custom_icon_fix.'"':'';
        $icon_color = !empty($slide['options']['icon']['def_icon']['icon_color']) ? ' style="color:'.$slide['options']['icon']['def_icon']['icon_color'].'"' : '';
        if($i && !($i%3)):?>
            </div><div class="row">
        <?php endif;?>
        <div class="col-md-4">
            <div class="service"<?php echo $service_style ?>>
                <?php if(!empty($slide['options']['icon']['custom_icon'])) : ?>
                    <img src="<?php echo $slide['options']['icon']['custom_icon'] ?>" alt="">
                <?php else: ?>
                    <i class="icon-<?php echo $slide['options']['icon']['def_icon']['icon_nr']?>"<?php echo $icon_color ?>></i>
                <?php endif; ?>
                <div class="service-description"<?php echo !empty($slide['options']['icon']['custom_icon']) ? " style='margin-top:0;'" : "" ?>>
                    <h4<?php echo $title_color ?>><?php echo get_the_title($slide['post']->ID); ?></h4>
                    <?php echo apply_filters( 'the_content' ,$slide['post']->post_content);?>
                </div>
            </div>
        </div>
    <?php endforeach;?>
</div>
<!-- END SERVICES -->