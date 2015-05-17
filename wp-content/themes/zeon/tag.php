<?php get_header(); ?>
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <?php if (have_posts()) : ?>
                <h2><?php _e('Tag Archive: ','zeon'); echo single_tag_title( '', false ); ?></h2>
                <?php while(have_posts()) : the_post();

                    get_template_part('content','blog');

                endwhile; ?>
            <?php else: ?>
                <h2><?php _e('No posts to display in ','zeon'); echo single_tag_title( '', false ); ?></h2>
            <?php endif; ?>
            <?php get_template_part('nav','main')?>
        </div>
        <div class="col-md-4">
            <?php get_sidebar(); ?>
        </div>
    </div>
</div><!-- Container -->
<?php get_footer(); ?>