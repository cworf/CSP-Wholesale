<div class="blog-entry">
    <div class="entry-header">
        <h1><a href="<?php the_permalink() ?>"><?php the_title() ?></a></h1>
        <ul class="blog-entry-details">
            <li class="entry-date"><a href="<?php the_permalink() ?>"><?php the_time( get_option('date_format') ); ?></a></li>
            <li class="entry-author"><?php the_author_link(); ?></li>
            <li class="entry-tags"><?php the_tags( '', ', ', '' ); ?></li>
            <li class="entry-comments"><a href="<?php the_permalink() ?>"><?php _ex('Comments','blog','zeon')?> (<?php comments_number( '0', '1', '%' ) ?>)</a></li>
        </ul>
    </div>    
    <?php tt_video_or_image_featured()?>
    <div class="entry-content">
        <?php
            $excerpt = get_the_excerpt();
            if(!empty($excerpt)){
                the_excerpt();
            }else{
                the_content();
            }
        ?>
    </div>
    <div class="entry-footer">
        <a href="<?php the_permalink() ?>" class="button-4"><?php _ex('read more','blog','zeon')?></a>
    </div>
    <div class="clear"></div>
</div>