<div class="wrap">
    <h2><?php _e('UPME - Modules','upme')?></h2>
    <div class="updated" id="upme-modules-settings-saved" style="display:none;">
        <p><?php _e('Settings Saved','upme');?></p>
    </div>
    
    <div class="updated" id="upme-modules-settings-reset" style="display:none;">
        <p><?php _e('Settings Reset Completed.','upme');?></p>
    </div>
    
    <div id="upme-tab-group" class="upme-tab-group vertical_tabs">
        <ul id="upme-tabs" class="upme-tabs">
            <li class="upme-tab active" id="upme-site-lockdown-settings-tab"><?php _e('Site Lockdown','upme');?></li>
            <li class="upme-tab " id="upme-site-restrictions-settings-tab"><?php _e('Content Restriction Rules','upme');?></li>
            <li class="upme-tab " id="upme-email-settings-tab"><?php _e('Email Templates','upme');?></li>
            
            <?php do_action('upme_addon_module_tabs'); ?>
        </ul>
        <div id="upme-tab-container" class="upme-tab-container" style="min-height: 325px;">
            <div class="upme-tab-content-holder">
                <div class="upme-tab-content" id="upme-site-lockdown-settings-content">
                    <h3><?php _e('Site Lockdown','upme');?></h3>
                    <div class="updated" id="upme-site-lockdown-settings-msg" style="display:none;">
                        
                    </div>
                    <form id="upme-site-lockdown-settings-form">
                        <table class="form-table" cellspacing="0" cellpadding="0">
                            <tbody>
                                <?php
                                    include_once(upme_path.'modules/site_lockdown_view.php');
                                ?>
                                
                                <tr valign="top">
                                    <th scope="row"><label>&nbsp;</label></th>
                                    <td>
                                        <?php 
                                            echo UPME_Html::button('button', array('name'=>'save-upme-site-lockdown-settings', 'id'=>'save-upme-site-lockdown-settings', 'value'=>'Save Changes', 'class'=>'button button-primary upme-save-module-options'));
                                            echo '&nbsp;&nbsp;';
                                            echo UPME_Html::button('button', array('name'=>'reset-upme-site-lockdown-settings', 'id'=>'reset-upme-site-lockdown-settings', 'value'=>__('Reset Options','upme'), 'class'=>'button button-secondary upme-reset-module-options'));
                                        ?>
                                    </td>
                                </tr>                                
                                
                            </tbody>
                        </table>
                    </form>
                </div> 
                <div class="upme-tab-content" id="upme-site-restrictions-settings-content" style="display:none;">
                    <div id="upme-site-restrictions-create" style="display:none">
                        <h3><?php _e('Content Restriction Rules','upme');?>
                        <?php echo UPME_Html::button('button', array('name'=>'upme-display-list-res-rule', 'id'=>'upme-display-list-res-rule'
                            , 'value'=> __('Back To Restriction Rules','upme'), 'class'=>'button button-primary')); ?></h3>


                        <div class="updated" id="upme-add-site-restrictions-settings-msg" style="display:none;">
                            
                        </div>
                        <form id="upme-site-restrictions-create-form">
                            <table class="form-table" cellspacing="0" cellpadding="0">
                                <tbody>
                                    <?php
                                        include_once(upme_path.'modules/site_content_restriction_rules_view.php');
                                    ?>
                                    
                                    <tr valign="top">
                                        <th scope="row"><label>&nbsp;</label></th>
                                        <td>
                                            <?php 

                                                echo UPME_Html::button('button', array('name'=>'add-upme-site-restriction-rule', 'id'=>'add-upme-site-restriction-rule', 'value'=>'Add Restriction Rule', 'class'=>'button button-primary '));
                                                echo '&nbsp;&nbsp;';
                                            ?>
                                        </td>
                                    </tr>
                                   
                                </tbody>
                            </table>
                        </form>
                    </div>
                    <div id="upme-site-restrictions-list">
                        <h3><?php _e('Content Restriction Rules','upme');?>
                        <?php echo UPME_Html::button('button', array('name'=>'upme-display-create-res-rule', 'id'=>'upme-display-create-res-rule'
                            , 'value'=> __('Create New Restriction Rule','upme'), 'class'=>'button button-primary')); ?></h3>

                        <div class="updated" id="upme-site-restrictions-settings-msg" style="display:none;">
                            
                        </div>
                        <form id="upme-site-restrictions-settings-form">
                            <table class="form-table" cellspacing="0" cellpadding="0">
                                <tbody>                                

                                    <tr valign="top">
                                        <td colspan="2">
                                            <table id="upme_site_restriction_rules">
                                                <tr id="upme_site_restriction_rules_titles">
                                                    <th><?php echo __('Allowed for','upme'); ?></th>
                                                    <th><?php echo __('Allowed Conditions','upme'); ?></th>
                                                    <th><?php echo __('Restrictions','upme'); ?></th>
                                                    <th><?php echo __('Redirection','upme'); ?></th>
                                                    <th><?php echo __('Delete','upme'); ?></th>
                                                    <th><?php echo __('Enable/Disable','upme'); ?></th>
                                                </tr>
                                                <?php
                                                    global $upme_site_restrictions;
                                                    echo $upme_site_restrictions->upme_restriction_rules_list();
                                                ?>
                                            </table>
                                        </td>
                                    </tr>
                                    
                                </tbody>
                            </table>
                        </form>
                    </div>
                </div>



                <div class="upme-tab-content" id="upme-email-settings-content" style="display:none;">
                    <h3><?php _e('Email Templates','upme');?></h3>
                    <div class="updated" id="upme-email-settings-msg" style="display:none;">
                        
                    </div>
                    <form id="upme-email-settings-form">
                        <table class="form-table" cellspacing="0" cellpadding="0">
                            <tbody>
                                <?php
                                   include_once(upme_path.'modules/email_templates_view.php');
                                ?>
                                
                                <tr valign="top">
                                    <th scope="row"><label>&nbsp;</label></th>
                                    <td>
                                        <?php 
                                            echo UPME_Html::button('button', array('name'=>'save-upme-email-template', 'id'=>'save-upme-email-template', 'value'=>'Save Changes', 'class'=>'button button-primary '));
                                            echo '&nbsp;&nbsp;';
                                            echo UPME_Html::button('button', array('name'=>'reset-upme-email-template', 'id'=>'reset-upme-email-template', 'value'=>__('Reset Templates','upme'), 'class'=>'button button-secondary '));
                                        ?>
                                    </td>
                                </tr>                                
                                
                            </tbody>
                        </table>
                    </form>
                </div> 


                <?php do_action('upme_addon_module_settings'); ?>

            </div>
        </div>
        
    </div>
    
</div>
