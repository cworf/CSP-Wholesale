<div class="blog-entry">
    <div class="entry-header">
        <h1><?php the_title() ?></h1>
        <ul class="blog-entry-details">
            <li class="entry-date"><a href="<?php the_permalink() ?>"><?php the_time( get_option('date_format') ); ?></a></li>
            <li class="entry-author"><?php the_author_link(); ?></li>
            <li class="entry-tags"><?php the_tags( '', ', ', '' ); ?></li>
            <li class="entry-comments"><a href="<?php the_permalink() ?>"><?php _ex('Comments','blog','zeon')?> (<?php comments_number( '0', '1', '%' ) ?>)</a></li>
        </ul>
    </div>    
    <?php tt_video_or_image_featured()?>
    <div class="entry-content">
        <?php the_content();?>
    </div>

    <div class="entry-footer">
        <div class="social-share-border">
            <?php tt_share(); ?>
        </div>
        <div class="post_pagination">
            <?php wp_link_pages(array(
                'before'           => '<ul class="page-numbers center">',
                'after'            => '</ul>',
                'link_before'      => '',
                'link_after'       => '',
                'next_or_number'   => 'number',
                'separator'        => '</li><li>',
                'nextpagelink'     => __( 'Next page','zeon' ),
                'previouspagelink' => __( 'Previous page','zeon' ),
                'pagelink'         => '%',
                'echo'             => 1
            )); ?>
        </div>
    </div>
    <!-- Related Posts -->
    <?php 
    global $post;
    if(_go('show_related_posts')) 
            tt_related_posts($post)?>
    <!-- Comments area -->
    <?php comments_template( ); ?>
</div>