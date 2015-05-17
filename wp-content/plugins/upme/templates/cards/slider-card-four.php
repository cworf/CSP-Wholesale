<?php 
    global $upme_template_args;     
    extract($upme_template_args);
?>

<div class="upme-wrap">
    
    
    
    <div class="upme-slider-design-four  upme-team-design-four upme-team-design" style="background:<?php echo $background_color; ?>;color:<?php echo $font_color; ?>" >
        
        <div class="flexslider flexslider-four">
            <ul class="slides">            
        
                <?php 
                    $x = 0;
                    foreach($users as $key => $user){ 
                        extract($user);
                        $x++;
                ?>
                
                
                  
                    <li class="upme-single-profile-li" >
          
                        
                        <div class="upme-single-profile">
                            <div class="upme-profile-pic <?php echo $pic_style; ?> "  >
                            <?php echo $profile_pic_display; ?>
                            </div>

                            <div class="upme-clear"></div>
                        </div>
                    
         
                    </li>
        
                
                <?php } ?>
                
                
            </ul>
        </div>

    </div>
</div>