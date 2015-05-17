<?php foreach ($slides as $slide_nr=>$slide): ?>
	<div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion" href="#collapse-<?php echo $slide_nr?>"<?php if($slide_nr !== 0) echo " class='collapsed'" ?>>
                    <?php echo get_the_title($slide['post']->ID); ?>
                </a>
            </h4>
        </div>
        <div id="collapse-<?php echo $slide_nr?>" class="panel-collapse collapse<?php if($slide_nr==0) echo " in"?>">
            <div class="panel-body">
                <?php echo apply_filters( 'the_content' ,$slide['post']->post_content);?>
            </div>
        </div>
    </div>
<?php endforeach; ?>