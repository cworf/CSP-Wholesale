<!-- START ACCORDION -->
<?php
global $tesla_toggle_list;
if(isset($tesla_toggle_list))
    $tesla_toggle_list++;
else
    $tesla_toggle_list = 0;
?>

<div class="site-title">
    <div class="site-inside"><span><?php echo $shortcode['title'] ?></span></div>
</div> 
<div class="panel-group panel-group-2" id="accordion-<?php echo "$tesla_toggle_list"?>">
    <?php foreach($slides as $i => $slide): ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion-<?php echo $tesla_toggle_list; ?>" href="#collapse-<?php echo $i.'-'.$tesla_toggle_list; ?>"<?php if($i!==0) echo ' class="collapsed"'?>>
                        <i class="icon-473" title="473"></i>
                        <?php echo get_the_title($slide['post']->ID); ?>
                    </a>
                </h4>
            </div>
            <div id="collapse-<?php echo $i.'-'.$tesla_toggle_list; ?>" class="panel-collapse collapse<?php echo !$i?' in':''; ?>">
                <div class="panel-body">
                    <?php echo apply_filters('the_content', $slide['post']->post_content); ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>    
</div>
<!-- END ACCORDION -->