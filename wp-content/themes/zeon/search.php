<?php
/**
 * Search results page
 */
get_header(); ?>
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <?php if (have_posts()) : ?>
                <h2><?php _e('Search Results for ','zeon') ?><i>'<?php echo get_search_query(); ?>'</i> : </h2>
                <?php while(have_posts()) : the_post();
                    add_filter('the_content', 'cut_shortcodes');
                    get_template_part('content','blog');

                endwhile; ?>
            <?php else:?>
                <h2><?php _e('No matching posts found for ','zeon');?><i>'<?php echo get_search_query(); ?>'</i></h2>
            <?php endif; ?>
            <?php get_template_part('nav','main')?>
        </div>
        <div class="col-md-4">
            <?php get_sidebar(); ?>
        </div>
    </div>
</div><!-- Container -->
<?php get_footer(); ?>