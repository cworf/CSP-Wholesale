</div>
<div class="box">
    <div class="container">
        <div class="site-title"><div class="site-inside"><span><?php echo $shortcode['title']; ?></span></div></div>
        <div class="row">
            <?php foreach ($slides as $slide_nr => $slide) : ?>
                <div class="col-md-4 col-xs-6">
                    <div class="statistics-circle">
                        <?php echo get_the_title($slide['post']->ID) ?><span><?php echo $slide['post']->post_content?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<div class="container">