<?php
/*
	Template Name: About
*/
?>
<?php get_header(); ?>
<div class="container">
    <div class="site-title">
    	<div class="site-inside">
    		<span><?php the_title( ); ?></span>
    	</div>
    </div>
    <?php echo Tesla_slider::get_slider_html('team');?>
    <?php if (have_posts()) : 
        while(have_posts()) : the_post();

            the_content();

        endwhile; ?>
    <?php endif; ?>
</div>
<?php get_footer(); ?>