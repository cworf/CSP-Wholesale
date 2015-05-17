<div class="info-box">
	<?php foreach ($slides as $slide_nr => $slide) : ?>
    <div class="row">
        <div class="col-md-5">
            <?php echo get_the_post_thumbnail( $slide['post']->ID ); ?>
        </div>
        <div class="col-md-7">
            <h4><?php echo get_the_title($slide['post']->ID) ?></h4>
            <?php echo apply_filters('the_content', $slide['post']->post_content); ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>