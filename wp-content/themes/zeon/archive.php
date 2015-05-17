<?php get_header(); ?>
<div class="container">
    <div class="col-md-8">
        <?php if (have_posts()): ?>
            <?php if (is_day()) : ?>
                <h2>Archive: <?php echo get_the_date('D M Y'); ?></h2>							
            <?php elseif (is_month()) : ?>
                <h2>Archive: <?php echo get_the_date('M Y'); ?></h2>	
            <?php elseif (is_year()) : ?>
                <h2>Archive: <?php echo get_the_date('Y'); ?></h2>								
            <?php else : ?>
                <h2>Archive</h2>	
            <?php endif;

            while (have_posts()) : the_post();
                get_template_part('content','blog');
            endwhile;
        else: ?>
            <h2><?php _e('No posts to display','zeon') ?></h2>	
        <?php endif; ?>
    </div>
    <div class="col-md-4">
        <?php get_sidebar(); ?>
    </div>
</div>
<?php get_footer(); ?>