<?php 
    global $upme_template_args;     
    extract($upme_template_args);
?>

<div class="upme-wrap">
    
    
    
    <div class="upme-slider-design-one upme-team-design-one upme-team-design" style="background:<?php echo $background_color; ?>;color:<?php echo $font_color; ?>">
        
        <div class="flexslider flexslider-one">
            <ul class="slides">            
        
                <?php 
                    $x = 0;
                    foreach($users as $key => $user){ 
                        extract($user);
                        $x++;
                ?>
                
                
                    <?php if($x%3 == 1){ ?>
                    <li class="upme-single-profile-li" >
                    <?php } ?>
                        <div class="upme-single-profile">
                        <div class="upme-profile-pic <?php echo $pic_style; ?> "  >
                        <?php echo $profile_pic_display; ?>
                        </div>
                        <div class="upme-author-name">
                            <a style="color:<?php echo $font_color; ?>" upme-data-user-id="<?php echo  $id; ?>" href="<?php echo $profile_url; ?>"  ><?php echo  $profile_title_display; ?></a>
                        </div>

                        <div class="upme-social-boxes">
                        <?php echo $social_buttons; ?>
                        </div>

                        <div class="upme-clear"></div>
                        </div>
                    <?php if($x%3 == 0){ ?>
                    </li>
                    <?php } ?>
                
                <?php } ?>
                
                
            </ul>
        </div>

    </div>
</div>