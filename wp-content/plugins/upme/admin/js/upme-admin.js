/* Confirm deletion */
function confirmAction(){
    var confirmed = confirm(UPMEAdmin.fieldDeleteConfirm);
    return confirmed;
}

jQuery(document).ready(function($) {

    /* show/toggle choices */
    $('.upme-inputtype select').change(function(){
        val = $(this).val();
        if (val == 'select' || val == 'radio' || val == 'checkbox' || val == 'chosen_select' || val == 'chosen_multiple') {
            $(this).parent().parent().parent().find('.upme-choices').show();
        } else {
            $(this).parent().parent().parent().find('.upme-choices').hide();
        }
    });

    // Check the availability of tipsy plugin before calling
    if($.isFunction($.fn.tipsy)){
        /* Tooltips */
        $('.upme-tooltip').tipsy({
            trigger: 'hover',
            gravity: 's',
            offset: 4
        });
        
        /* Tooltip in table */
        $('.upme-tooltip2').tipsy({
            trigger: 'hover',
            gravity: 'w',
            offset: 4
        });
        
        /* Tooltip for icon */
        $('.upme-tooltip3').tipsy({
            trigger: 'hover',
            offset: 4
        });
    }

    
	
    /* Toggle ADD form */
    $('.upme-toggle').click(function(){
        $('.upme-add-form').toggle();
    });
	
    $('.upme-add-form-cancel').click(function(){
        $('.upme-add-form').hide();
    });
	
    /* Toggle inline edit 
    $('.upme-edit').click(function(){
        if ($(this).parent().parent().next('tr.upme-editor').is(':hidden')) {
            $(this).parent().parent().next('tr.upme-editor').show();
        } else {
            $(this).parent().parent().next('tr.upme-editor').hide();
        }
        $(this).parent().parent().parent().find('tr.upme-editor').not($(this).parent().parent().next('tr.upme-editor')).hide();
        return false;
    });*/

    /* Toggle inline edit */
    $('.upme-edit').click(function(){

        jQuery('#upme_all_update_processing').hide();
        jQuery('.upme_single_update_processing').hide();

        var editor = $(this).parent().parent().find('div.upme-editor');
        if (editor.is(':hidden')) {
            editor.show();
        } else {
            editor.hide();
        }
        $(this).parent().parent().parent().find('div.upme-editor').not($(this).parent().parent().find('div.upme-editor')).hide();
        return false;
    });
	
    $('.upme-inline-cancel').click(function(){
        $(this).parent().parent().parent().hide();
    });
	
    /* Toggle icon edit */
    $('.upme-inline-icon-upme-edit').click(function(e){
        e.preventDefault();
        if ($(this).parent().parent().find('.upme-icons').is(':hidden')) {
            $(this).parent().parent().find('.upme-icons').show();
        } else {
            $(this).parent().parent().find('.upme-icons').hide();
        }
    });
	
    /* Switch field type */
    $('#up_type').change(function(){
        if ($(this).val() == 'separator') {
            $('#up_social,#up_can_edit,#up_can_hide,#up_tooltip,#up_field, #up_allow_html, #up_private, #up_required,#up_meta,#up_edit_by_user_role,#up_edit_by_user_role_list,#up_help_text').parent().parent().hide();
            //$('#up_name').parent().parent().after($('#up_meta_custom').parent().parent());
            //$('#up_name').parent().parent().after($('#up_meta').parent().parent());


            $('#up_meta_custom').parent().parent().show().find('label').html(UPMEAdmin.separatorKey);  
            $('#up_name').parent().parent().show().find('label').html(UPMEAdmin.separatorLabel);  
            $('#up_meta_custom').parent().find('i').attr('original-title',UPMEAdmin.separatorHelp);        
            $('.upme-icons-holder').hide();
        } else {
            $('#up_social,#up_can_edit,#up_can_hide,#up_tooltip,#up_field, #up_allow_html, #up_private, #up_required,#up_meta,#up_edit_by_user_role,#up_edit_by_user_role_list,#up_help_text').parent().parent().show();
            $('#up_meta_custom').parent().parent().hide().find('label').html(UPMEAdmin.profileKey);
            $('#up_name').parent().parent().hide().find('label').html(UPMEAdmin.profileLabel); 
            $('#up_meta_custom').parent().find('i').attr('original-title',UPMEAdmin.profileHelp);  
            $('.upme-icons-holder').show();

        }
    });
	
    $('#up_meta').change(function(){
        if ($(this).val() == '1') {
            $('#up_meta_custom').parent().parent().show();
        } else {
            $('#up_meta_custom').parent().parent().hide();
            $('#up_meta_custom').val('');
        }
    });
	
    $('#up_field').change(function(){
        if ($(this).val() == 'fileupload' || $('#up_private').val() == '1' || $.inArray( $(this).val() , UPMEAdmin.customFileFieldTypes ) != '-1') {
            $('#up_show_in_register').parent().parent().hide();
            $('#up_show_in_register').val(0);
        } else {
            $('#up_show_in_register').parent().parent().show();
        }


        if ($(this).val() == 'fileupload' || $.inArray( $(this).val() , UPMEAdmin.customFileFieldTypes ) != '-1'){
            $("#up_required").parent().parent().hide();
        }else{
            $("#up_required").parent().parent().show();
        }
    });
	
    $('#captcha_plugin').change(function(){
        if ($(this).val() == 'recaptcha')
        {
            $('#recaptcha_public_key_holder').show();
            $('#recaptcha_private_key_holder').show();
            $('#recaptcha_theme_holder').show();
        }
        else
        {
            $('#recaptcha_public_key_holder').hide();
            $('#recaptcha_private_key_holder').hide();
            $('#recaptcha_theme_holder').hide();
        }
		
        if($(this).val() == 'none')
        {
            $('#captcha_label_holder').hide();
        }
        else
        {
            $('#captcha_label_holder').show();
        }
		
    });
    
	
    $('#captcha_plugin').trigger('change');
	
	
	
    $('#up_private').change(function(){
        $('#up_field').trigger('change');
    });
	
	
    if($('#upme-shortcode-popup-tabs').length > 0)
    {
        $('#upme-shortcode-popup-tabs').tabify();
    }

    // Validate automtic login and email confirmation on user selected password setting
    $('#set_password').change(function(){
        if ($(this).val() == '1')
        {
            $('#automatic_login_holder').show();

            if($('#automatic_login').val() == '0'){
                $('#set_email_confirmation_holder').show();
                $('#profile_approval_status_holder').show();
            }
        }
        else
        {
            $('#automatic_login_holder').hide();
            $('#set_email_confirmation').val('0');
            $('#set_email_confirmation_holder').hide();

            
            $('#profile_approval_status').val('0');
            $('#profile_approval_status_holder').hide();
        }

    });

    $('#set_password').trigger('change');

    // Validate email confirmation on automtic login setting
    $('#automatic_login').change(function(){
        if ($(this).val() == '1')
        {
            $('#set_email_confirmation').val('0');
            $('#set_email_confirmation_holder').hide();

            $('#profile_approval_status').val('0');
            $('#profile_approval_status_holder').hide();
        }
        else
        {
            
            $('#set_email_confirmation_holder').show();
            $('#profile_approval_status_holder').show();
        }

    });

    $('#automatic_login').trigger('change');

    // Remove required validation for file upload fields
    $('.upme_edit_field_type').change(function(){
        var fieldId = $(this).attr("id");
		
        userId = fieldId.replace("upme_","").replace("_field","");
        fieldId = "#"+fieldId;

        if($(fieldId).val() == 'fileupload' || $.inArray( $(this).val() , UPMEAdmin.customFileFieldTypes ) != '-1'){
            $("#upme_"+ userId +"_required").parent().hide();
        }else{
            $("#upme_"+ userId +"_required").parent().show();
        }
    });

    /* Enable user roles list on settings change */
    $('#up_show_to_user_role').change(function(){
        if ($(this).val() == '1') {
            $('#up_show_to_user_role_list').parent().parent().show();
        } else {
            $('#up_show_to_user_role_list').parent().parent().hide();
        }
    });

    $('#up_edit_by_user_role').change(function(){
        if ($(this).val() == '1') {
            $('#up_edit_by_user_role_list').parent().parent().show();
        } else {
            $('#up_edit_by_user_role_list').parent().parent().hide();
        }
    });

    $('.upme_show_to_user_role').change(function(){
        
        var elementId = $(this).attr("id");
        if ($(this).val() == '1') {
            $('#'+elementId+'_list').parent().parent().show();
        } else {
            
            var elementClass = $('#'+elementId+'_list').attr("class");

            $('.'+elementClass).each(
                function() {
              
                    $(this).attr('checked', false);
                }
                );
            $('#'+elementId+'_list').parent().parent().hide();
        }
    });

    $('.upme_edit_by_user_role').change(function(){

        var elementId = $(this).attr("id");

        if ($(this).val() == '1') {
            $('#'+elementId+'_list').parent().parent().show();
        } else {
            var elementClass = $('#'+elementId+'_list').attr("class");
            $('.'+elementClass).each(
                function() {
                    $(this).attr('checked', false);
                }
                );
            $('#'+elementId+'_list').parent().parent().hide();
        }
    });

    $('.upme-edit-field-type').change(function(){

        var fieldTypeId = $(this).attr('id');
        var fieldInputId = '#'+ fieldTypeId.replace('_type','_field');
        var fieldMetaId = '#'+ fieldTypeId.replace('_type','_meta');
        var fieldMetaCustomId = '#'+ fieldTypeId.replace('_type','_meta_custom');
        var fieldNameId = '#'+ fieldTypeId.replace('_type','_name');


        var fieldTooltipId = '#'+ fieldTypeId.replace('_type','_tooltip');
        var fieldSocialId = '#'+ fieldTypeId.replace('_type','_social');
        var fieldCanEditId = '#'+ fieldTypeId.replace('_type','_can_edit');
        var fieldAllowHTMLId = '#'+ fieldTypeId.replace('_type','_allow_html');
        var fieldCanHideId = '#'+ fieldTypeId.replace('_type','_can_hide');
        var fieldPrivateId = '#'+ fieldTypeId.replace('_type','_private');
        var fieldRequiredId = '#'+ fieldTypeId.replace('_type','_required');
        var fieldChoicesId = '#'+ fieldTypeId.replace('_type','_choices');
        var fieldPredefinedLoopId = '#'+ fieldTypeId.replace('_type','_predefined_loop');
        var fieldIconId = '#'+ fieldTypeId.replace('_type','_icon');
        var fieldIconsClass = '.'+ fieldTypeId.replace('_type','_icons');

        var itemIds = fieldMetaId + "," +fieldInputId + "," +fieldTooltipId + "," +fieldSocialId + "," +fieldCanEditId 
        + "," +fieldAllowHTMLId + "," +fieldCanHideId + "," +fieldPrivateId + "," +fieldRequiredId + "," +fieldChoicesId 
        + "," +fieldPredefinedLoopId + "," +fieldIconId;

        if ($(this).val() == 'separator') {
            $(fieldMetaCustomId).parent().find('label').html(UPMEAdmin.separatorKey);  
            $(fieldNameId).parent().find('label').html(UPMEAdmin.separatorLabel);  

            $(itemIds).attr('disabled','disabled');
            $(fieldIconsClass).attr('disabled','disabled');
            //$(fieldInputId).attr('disabled','disabled');
            //$(fieldMetaId).parent().hide();
            $(itemIds).parent().hide();
            $(fieldIconsClass).parent().parent().hide();
            $(fieldMetaCustomId).val($(fieldMetaId).val());
            

        }else{
            $(fieldMetaCustomId).parent().find('label').html(UPMEAdmin.profileKey);
            $(fieldNameId).parent().find('label').html(UPMEAdmin.profileLabel);  
    
            $(itemIds).removeAttr('disabled');
            $(fieldIconsClass).removeAttr('disabled');
            //$(fieldInputId).removeAttr('disabled');
            $(itemIds).parent().show();
            $(fieldIconsClass).parent().parent().show();
            //$(fieldInputId).parent().show();
            
        }
    });

    /*$('.upme-add-form-cancel').click(function(){
        alert("ddd");
        $('#up_show_to_user_role_list').parent().parent().hide();
        $('#up_edit_by_user_role_list').parent().parent().hide();
        $('#upme-custom-field-add').reset();
        document.getElementById("upme-custom-field-add").reset();
    });*/

    $(".upme-add-form-cancel").on("click", function(event){

        $('#up_show_to_user_role_list').parent().parent().hide();
        $('#up_edit_by_user_role_list').parent().parent().hide();

        event.preventDefault();
        $(this).closest('form').get(0).reset();
    

    });

    $(".upme-inline-cancel").on("click", function(event){

        $(this).closest('tr').find('.upme-user-roles-list').parent().hide();

        event.preventDefault();
        $(this).closest('form').get(0).reset();
    

    });
    
    
    $('#upme-update-user-cache').click(function(){
    	
        $('#upme-update-user-cache').data('last_procesed_user','0');
    	
        $('#upme-processing-tag').show();
    	
        $("#upme-update-user-cache").attr("disabled", "disabled");
    	
        update_user_cache(0);
    	
    });
    
    // New Setting Page Tabs 
    
    $('.upme-tab').click(function(){
        if(!$(this).hasClass('active'))
        {
        	// Change Active Class for Tabs 
        	$('.upme-tab').removeClass('active');
        	$(this).addClass('active');
        	
        	// Show relevant field box
        	var content_id = $(this).attr('id').replace('-tab','-content');
        	
        	$('.upme-tab-content').hide();
        	$('#'+content_id).show();
        }
    });
    
    $('.upme-save-options').click(function(){
    	
    	var btn_id = $(this).attr('id');
    	var form_id = btn_id.replace('save-','');
    	form_id = form_id.replace('-tab','-form');
    	
    	$('#'+btn_id).attr("disabled", "disabled");
    	$('#'+btn_id).val(UPMEAdmin.savingSetting);
    	
    	$.post(
    	        UPMEAdmin.AdminAjax,
    	        {
    	            'action': 'save_upme_settings',
    	            'data':   $("#"+form_id).serialize(),
    	            'current_tab' : form_id
    	        },
    	        function(response){
    	        	$('#'+btn_id).val(UPMEAdmin.saveSetting);
    	        	$('#'+btn_id).removeAttr("disabled");
    	        	
    	        	$('#upme-settings-saved').show();
    	        	setTimeout(function(){
                        jQuery("#upme-settings-saved").hide();
                    }, 3000);
    	        	
    	        }
    		);
    });
    
    $('.upme-reset-options').click(function(){
    	
    	var btn_id = $(this).attr('id');
    	var form_id = btn_id.replace('reset-','');
    	var tab_id = form_id; 
    	form_id = form_id.replace('-tab','');
    	
    	$('#'+btn_id).attr("disabled", "disabled");
    	$('#'+btn_id).val(UPMEAdmin.resettingSetting);
    	
    	$.post(
    	        UPMEAdmin.AdminAjax,
    	        {
    	            'action': 'reset_upme_settings',
    	            'current_tab' : form_id
    	        },
    	        function(response){
    	        	$('#'+btn_id).val(UPMEAdmin.resetSetting);
    	        	$('#'+btn_id).removeAttr("disabled");

    	        	window.location = UPMEAdmin.adminURL + '&reset=' +tab_id;
    	        }
    		);
    });
    
    
    var query_var = [], hash;
    var q = document.URL.split('?')[1];
    if(q != undefined)
    {
        q = q.split('&');
        for(var i = 0; i < q.length; i++){
            hash = q[i].split('=');
            query_var.push(hash[1]);
            query_var[hash[0]] = hash[1];
        }
    }
    if(query_var['reset'] != undefined)
    {
    	$('#'+query_var['reset']).trigger('click');
    	$('#upme-settings-reset').show();
    	setTimeout(function(){
            jQuery("#upme-settings-reset").hide();
        }, 3000);
    }
    

    // Display/hide sub settings for user role selection
    $('#select_user_role_in_registration').click(function(){
        upme_show_hide_user_role_rields();
        
    });
    
    $('.upme-save-options').click(function(){
        upme_show_hide_user_role_rields();
    });

    if(jQuery('#select_user_role_in_registration')){
        upme_show_hide_user_role_rields();
    }

    // Display/hide sub settings for user posts on profiles
    $('#show_recent_user_posts').click(function(){
        upme_show_hide_user_post_fields();        
    });

    $('.upme-save-options').click(function(){
        upme_show_hide_user_post_fields();
    });

    if(jQuery('#show_recent_user_posts')){
        upme_show_hide_user_post_fields();
    }

    // Display/hide sub settings for user profile status
    $('#profile_view_status').click(function(){
        upme_show_hide_user_profile_status_fields();        
    });

    if(jQuery('#profile_view_status')){
        upme_show_hide_user_profile_status_fields();
    }

    //Show hide user roles for view profile based on - Logging in user viewing of other profiles
    $('#users_can_view').change(function(){

        if($(this).val() == '2'){
            $('#choose_roles_for_view_profile').parent().parent().show();
        }else{
            $('#choose_roles_for_view_profile').parent().parent().hide();
        }          
    });

    $('#users_can_view').trigger('change');


    // Update options of custom fields when clicking Update button
    $('.upme-field-update').click(function(){
        upme_update_field_settings($(this),'single');
    });

    $('.upme-all-field-update').click(function(){
        upme_update_field_settings($(this),'all');
    });

    // Create new custom field using AJAX
    $('.upme-field-create').click(function(){
        $('.upme_field_add_msg').remove();
        upme_create_field_settings();
    });


    // Reset custom fields through AJAX
    $('.upme-field-reset').click(function(){
        upme_reset_field_settings();
    });
    
    if(jQuery("#upme-form-customizer-table-data").length > 0){
        $( "#upme-form-customizer-table-data" ).sortable({
            update: function(e,ui){

                var counter = 1;
                jQuery('#upme-form-customizer-table-data li').each(function(index,ele){
                        var id = jQuery(ele).attr('id').replace('value-holder-tr-','');
                        jQuery('#upme_'+id+'_position').val(counter);
                        counter++;
           
                });

                
            },
        });
    }

});

function update_user_cache(user_id)
{
    jQuery.post(
        UPMEAdmin.AdminAjax,
        {
            'action': 'update_user_cache',
            'last_user':   user_id
        },
        function(response){
            	
            jQuery('#upme-completed-users').show();
            	
            if(response == 'completed')
            {
                jQuery('#upme-completed-users').hide();
                jQuery('#upme-processing-tag').hide();
                jQuery("#upme-update-user-cache").removeAttr("disabled");
                jQuery("#upme-upgrade-success").show();
                setTimeout(function(){
                    jQuery("#upme-upgrade-success").hide();
                }, 5000);
            }
            else
            {
                jQuery('#upme-completed-users').html(response+UPMEAdmin.cacheCompletedUsers);
                update_user_cache(response);
            }
        }
        );
}

function upme_show_hide_user_role_rields(){

    if(jQuery('#select_user_role_in_registration').is(':checked')) {
            jQuery('#label_for_registration_user_role_holder').show();
            jQuery('#choose_roles_for_registration_holder').show();
        }else{
            jQuery('#label_for_registration_user_role_holder').hide();
            jQuery('#choose_roles_for_registration_holder').hide();
        }
}

// Display/hide sub settings for user post on profile
function upme_show_hide_user_post_fields(){

    if(jQuery('#show_recent_user_posts').is(':checked')) {
            jQuery('#maximum_allowed_posts_holder').show();
            jQuery('#show_feature_image_posts_holder').show();
        }else{
            jQuery('#maximum_allowed_posts_holder').hide();
            jQuery('#show_feature_image_posts_holder').hide();
        }
}

// Display/hide sub settings for user profile status
function upme_show_hide_user_profile_status_fields(){

    if(jQuery('#profile_view_status').is(':checked')) {
        jQuery('#display_profile_status').parent().parent().show();
    }else{
        jQuery('#display_profile_status').parent().parent().hide();
    }
}

function upme_update_field_settings(obj,type){

    jQuery('#upme_all_update_processing').hide();
    var single_update_processing = obj.parent().find('.upme_single_update_processing');
    single_update_processing.hide();

    if('all' == type){
        jQuery('#upme_all_update_processing').html(UPMEAdmin.fieldUpdateProcessing);
        jQuery('#upme_all_update_processing').show();
    }else{
        single_update_processing.html(UPMEAdmin.fieldUpdateProcessing);
        single_update_processing.show();
    }

    var field_options   = jQuery( '#upme-custom-field-edit').serialize();

    jQuery.post(
        UPMEAdmin.AdminAjax,
        {
            'action': 'upme_update_custom_field',
            'field_options':   field_options
        },
        function(response){
                if(response.status == 'success'){
                    if('all' == type){
                        jQuery('#upme_all_update_processing').html(UPMEAdmin.fieldUpdateCompleted);
                    }else{
                        single_update_processing.html(UPMEAdmin.fieldUpdateCompleted);
                    }
                }

        },"json"
    );
}

function upme_create_field_settings(obj,type){

    jQuery('#upme_create_processing').hide();
    jQuery('#upme_create_processing').html(UPMEAdmin.fieldUpdateProcessing);
    jQuery('#upme_create_processing').show();

    var field_options = jQuery('#upme-custom-field-add').serialize();

    jQuery.post(
        UPMEAdmin.AdminAjax,
        {
            'action': 'upme_create_custom_field',
            'field_options':   field_options
        },
        function(response){
                if(response.status == 'success'){
                    var curr_pos = jQuery('#up_position').val();
                    jQuery('#up_position').val(parseInt(curr_pos)+1);
                  
                }
                
                jQuery('#upme_create_processing').html('');
                jQuery('#upme-custom-field-add').prepend(response.msg);
                

                var createFormCordinates = jQuery('#upme-custom-field-add').position();
                jQuery("html, body").animate({
                   scrollTop: createFormCordinates.top
                }, 2000);

        },"json"
    );
}

function upme_reset_field_settings(){
    jQuery.post(
        UPMEAdmin.AdminAjax,
        {
            'action': 'upme_reset_custom_fields'
        },
        function(response){

                if(response.status == 'success'){
                    
                    window.location.href = response.redirect_to;
                }           
        },"json"
    );
}