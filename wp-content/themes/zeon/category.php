<?php get_header(); ?>
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <?php if (have_posts()) : ?>
                <h3><?php _e('Category Archive: ','zeon');echo single_cat_title('', false); ?></h3>
                <?php while(have_posts()) : the_post();

                    get_template_part('content','blog');

                endwhile; ?>
            <?php endif; ?>
            <?php get_template_part('nav','main')?>
        </div>
        <div class="col-md-4">
            <?php get_sidebar(); ?>
        </div>
    </div>
</div><!-- Container -->
<?php get_footer(); ?>