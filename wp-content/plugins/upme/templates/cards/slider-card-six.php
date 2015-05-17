<?php 
    global $upme_template_args;     
    extract($upme_template_args);
?>

<div class="upme-wrap">
    
    
    
    <div class="upme-slider-design-six  " style="background:<?php echo $background_color; ?>;color:<?php echo $font_color; ?>" >
        
        <div class="flexslider flexslider-six">
            <ul class="slides">            
        
                <?php 
                    $x = 0;
                    foreach($users as $key => $user){ 
                        extract($user);
                        $x++;
                ?>
                
                
                  
                    <li class="upme-single-profile-li" data-thumb="<?php echo $user_pic; ?>" >
          
                        
                        <div class="upme-author-design-one upme-author-design" style="background:<?php echo $background_color; ?>;color:<?php echo $font_color; ?>">

                          <div class="upme-left">
                            <div class="upme-profile-pic <?php echo $pic_style; ?> ">
                              <?php echo $profile_pic_display; ?>

                            </div>
                            <div class="upme-author-name">
                              <div class="upme-field-name">
                                <a style="color:<?php echo $font_color; ?>" upme-data-user-id="<?php echo $id; ?>" href="<?php echo $profile_url; ?>"  ><?php echo $profile_title_display; ?></a>
                              </div>

                              <?php echo $social_buttons; ?>

                              <div class="upme-field-desc">
                              <?php echo $description; ?>
                              </div>

                              <div class="upme-clear"></div>
                            </div>
                          </div>

                          <div class="upme-clear"></div>

                        </div>
                    
         
                    </li>
        
                
                <?php } ?>
                
                
            </ul>
        </div>

    </div>
</div>