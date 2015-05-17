<?php if(count($slides)): ?>
<?php
$nr = (int)$shortcode['nr'];
if(!$nr)
    $nr = count($slides);
?>
<!-- =====================================================================
                                 START THE SLIDER
====================================================================== -->
<div class="the-slider<?php if ( is_page_template('template-home-slider-wide.php') ) echo " slider" ?>" data-tesla-plugin="slider" data-tesla-item=".slide" data-tesla-next=".slide-right" data-tesla-prev=".slide-left" data-tesla-container=".slide-wrapper">
    <div class="slide-arrows">
        <div class="slide-left"></div>
        <div class="slide-right"></div>
    </div>
    <ul class="slide-wrapper">
        <?php foreach($slides as $i => $slide): if($i>=$nr) break; ?>
            <li class="slide"><img src="<?php echo esc_attr($slide['options']['image']); ?>" alt="slider image"></li>
        <?php endforeach; ?>
    </ul>
    <ul class="the-bullets-dots" data-tesla-plugin="bullets">
        <?php foreach($slides as $i => $slide): if($i>=$nr) break; ?>
            <li>
                <span></span>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
<!-- =====================================================================
                                 END THE SLIDER
====================================================================== -->
<?php endif; ?>