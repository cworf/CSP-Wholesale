<?php 
    global $upme_template_args;     
    extract($upme_template_args);
?>

<div class="upme-wrap">
    <div class="upme-team-design-four upme-team-design" style="background:<?php echo $background_color; ?>;color:<?php echo $font_color; ?>">
        <?php if($team_name != ''){ ?>
            <div class="upme-team-name"><?php echo $team_name; ?></div>
        <?php } ?>
        
        <?php if($team_description != ''){ ?>
            <div class="upme-team-desc"><?php echo $team_description; ?></div>
        <?php } ?>
        
        
        <div class="upme-single-profile-panel">
        <?php 
            foreach($users as $key => $user){ 
                extract($user);
        ?>
        
        <div class="upme-single-profile">
            <div class="upme-profile-pic <?php echo $pic_style; ?> "  >
            <?php echo $profile_pic_display; ?>
            </div>
            
            <div class="upme-clear"></div>
        </div>
        <?php } ?>
        </div>
        <div class="upme-clear"></div>
    </div>
</div>