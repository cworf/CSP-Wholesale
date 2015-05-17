<?php   global $upme_addon_template_data;
        extract($upme_addon_template_data); 
?>
<div id="upme-addons-feed">
    
    <?php 
        foreach($addons as $addon){ 
            extract($addon);           
            
            if(in_array($name,$active_plugins)){
                $status = __('Activated','upme');
                $status_class = 'activated';
            }else{
                $status = __('Deactivated','upme');
                $status_class = 'deactivated'; 
            }
    ?>
            <div class="upme-addon-single">
                <div class="upme-addon-single-title"><?php echo $title; ?></div>
                <div class="upme-addon-single-image">
                    <img src="<?php echo $image; ?>" />
                </div>
                <div class="upme-addon-single-desc"><?php echo $desc; ?></div>
                <div class="upme-addon-single-buttons">
                    <div class="upme-addon-single-status <?php echo $status_class; ?> "><?php echo $status; ?></div>
                    <div class="upme-addon-single-type"><?php echo $type; ?></div>
                    <div class="upme-addon-single-get"><a href="<?php echo $download; ?>"><?php echo __('Download','upme'); ?></a></div>
                    <div class="upme-clear"></div>
                </div>
                
            </div>
    <?php } ?>
    
    
</div>