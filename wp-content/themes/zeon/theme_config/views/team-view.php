<div class="our-team" data-tesla-plugin="slider" data-tesla-item=".slide" data-tesla-next=".slide-right" data-tesla-prev=".slide-left" data-tesla-container=".slide-wrapper" data-tesla-autoplay="false">
    <div class="row">
        <ul class="the-bullets-dots" data-tesla-plugin="bullets">
            <?php foreach($slides as $i => $slide):?>
            <li>
                <div class="col-md-2 col-xs-4">
                    <div class="our-team-member">
                        <div class="hover-effect"><i class="icon-473" title="473"></i></div>
                        <?php echo get_the_post_thumbnail($slide['post']->ID, 'medium'); ?>
                    </div>    
                </div>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <ul class="slide-wrapper">
        <?php foreach($slides as $i => $slide):?>
            <li class="slide">
                <div class="row">
                    <div class="col-md-5">
                        <div class="our-team-member">
                            <?php echo get_the_post_thumbnail($slide['post']->ID, 'full'); ?>
                        </div> 
                    </div>
                    <div class="col-md-7">
                        <div class="our-team-member-details">
                            <h3><?php echo get_the_title($slide['post']->ID); ?></h3>
                            <h4><?php echo $slide['options']['position']; ?></h4>
                            <ul class="our-team-member-socials">
                                <?php foreach($slide['options']['social'] as $social): $social_type = key($social); $social_data = current($social); ?>

                                    <?php if($social_type!=='custom'): ?>
                                        <li><a href="<?php echo $social_data; ?>"><i class="icon-<?php echo $social_type; ?>" title="<?php echo $social_type; ?>"></i></a></li>
                                    <?php else: ?>
                                        <li><a href="<?php echo $social_data['url']; ?>"><img src="<?php echo $social_data['icon']; ?>" /></a></li>
                                    <?php endif; ?>

                                <?php endforeach; ?>
                            </ul>
                            <?php echo apply_filters('the_content', $slide['post']->post_content); ?>
                        </div>
                    </div>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</div>