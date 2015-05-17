</div>
<div class="testimonials">
    <div class="container">
         <div data-tesla-plugin="slider" data-tesla-item=".testimonial" data-tesla-next=".testimonial-right" data-tesla-prev=".testimonial-left" data-tesla-container=".testimonials-wrapper">
            <ul class="testimonials-wrapper">
                <?php foreach($slides as $i => $slide): ?>
                    <li class="testimonial">
                        <div class="testimonials-title"><?php echo $shortcode['title'] ?></div>
                        <?php echo apply_filters('the_content', $slide['post']->post_content); ?>
                        <div class="testimonials-avatar">
                            <?php echo get_the_post_thumbnail( $slide['post']->ID ); ?>
                        </div>
                        <h4><?php echo get_the_title($slide['post']->ID); ?></h4>
                    </li>
                <?php endforeach; ?>
            </ul>
            <ul class="testimonials-dots" data-tesla-plugin="bullets">
                <?php foreach($slides as $i => $slide): ?>
                    <li<?php if($i==0) echo ' class="active"'?>><span></span></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>
<div class="container">