<!-- START ACCORDION -->
<ul class="nav nav-tabs">
    <?php foreach($slides as $i => $slide): ?>
        <li<?php if($i==0) echo ' class="active"'?>><a href="#tab-<?php echo $slide['post']->ID ?>" data-toggle="tab"><?php echo get_the_title($slide['post']->ID); ?></a></li>
    <?php endforeach; ?>
</ul>

<div class="tab-content">
    <?php foreach($slides as $i => $slide): ?>
        <div class="tab-pane<?php if($i==0) echo ' active'?>" id="tab-<?php echo $slide['post']->ID ?>"><?php echo apply_filters('the_content', $slide['post']->post_content); ?></div>
    <?php endforeach; ?>
</div>