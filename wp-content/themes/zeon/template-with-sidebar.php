<?php
/*
    Template Name: With Sidebar
*/
get_header(); ?>
<div class="container">
    <div class="row">
        <div class="col-md-8">
			<?php if (have_posts()) : 
			    while(have_posts()) : the_post();

			    	the_content();

			    endwhile; ?>
			<?php endif; ?>
		</div>
		<div class="col-md-4">
			<?php get_sidebar('page'); ?>
		</div>
	</div>
</div><!-- Container -->
<?php get_footer(); ?>