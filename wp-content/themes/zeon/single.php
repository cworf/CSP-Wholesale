<?php
/**
 * Single
 */
get_header(); ?>
<div class="container">
    <div class="row">
        <div class="col-md-8">
			<?php if (have_posts()) : 
			    while(have_posts()) : the_post();

			    	get_template_part('content','single');

			    endwhile; ?>
			<?php endif; ?>
		</div>
		<div class="col-md-4">
			<?php get_sidebar(); ?>
		</div>
	</div>
</div><!-- Container -->
<?php get_footer(); ?>