<?php
/*
    Template Name: FAQ
*/
get_header();?>
<?php if (have_posts()) : 
    while(have_posts()) : the_post(); ?>

            <div class="container">
                <div class="site-title"><div class="site-inside"><span><?php the_title() ?></span></div></div>
                <div class="panel-group" id="accordion">
					<?php echo Tesla_slider::get_slider_html('faq');?>
				</div>	
			</div>

		    <div class='container'>
		        <?php the_content(); ?>
		    </div>

    <?php endwhile; ?>
<?php endif; ?>
<?php get_footer();