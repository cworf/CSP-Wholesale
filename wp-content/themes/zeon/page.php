<?php get_header(); ?>
<div class="container">
	<?php if (have_posts()) : 
	    while(have_posts()) : the_post();

	    	the_content();

	    endwhile; ?>
	<?php endif; ?>
	<?php comments_template( ); ?>
</div>
<?php get_footer(); ?>