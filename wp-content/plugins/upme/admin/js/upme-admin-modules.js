jQuery(document).ready(function($) {

    /* Email Template Settings */
    $('#email_template').val('0').trigger("chosen:updated");

    $('#reset-upme-email-template').click(function(){
        $('#upme-email-settings-msg').html('').removeClass('').hide();

        if($('#email_template').val() == '0'){
            $('#upme-email-settings-msg').html('<p>' + UPMEAdminModules.emailTitleRequired + '</p>');
            $('#upme-email-settings-msg').addClass('error').show();
        }else{

            $.post(
                UPMEAdminModules.AdminAjax,
                {
                    'action'                : 'upme_reset_email_template',
                    'template_name'         : $('#email_template').val(),
                },
                function(response){

                    if(response.status == 'success'){
                        $('#upme-email-settings-msg').html('<p>' + response.html + '</p>');
                        $('#upme-email-settings-msg').addClass('success').show();
                        $('#email_subject').val(response.temp_subject).show();
                        $('#email_template_editor').val(response.temp_content).show();
                        $('#email_status').val(response.temp_status).trigger("chosen:updated");
                    }

                },"json"
            );

        }
    });

    $('#save-upme-email-template').click(function(){

        $('#upme-email-settings-msg').html('').removeClass('').hide();

        if($('#email_template').val() == '0'){
            $('#upme-email-settings-msg').html('<p>' + UPMEAdminModules.emailTitleRequired + '</p>');
            $('#upme-email-settings-msg').addClass('error').show();
        }
        else if($('#email_subject').val().trim() == ''){
            $('#upme-email-settings-msg').html('<p>' + UPMEAdminModules.emailSubjectRequired + '</p>');
            $('#upme-email-settings-msg').addClass('error').show();
        }
        else{

            $.post(
                UPMEAdminModules.AdminAjax,
                {
                    'action'                : 'upme_save_email_template',
                    'template_name'         : $('#email_template').val(),
                    'template_content'      : $('#email_template_editor').val(),
                    'template_status'       : $('#email_status').val(),
                    'template_subject'       : $('#email_subject').val(),
                },
                function(response){

                    if(response.status == 'success'){
                        $('#upme-email-settings-msg').html('<p>' + response.html + '</p>');
                        $('#upme-email-settings-msg').addClass('success').show();
                    }

                },"json"
            );

        }

    });

    $('#email_template').change(function(){

        var email_template_editor = $('#email_template_editor');
        var email_template_editor_parent = $(email_template_editor).parent().parent();
        var email_status = $('#email_status');
        var email_status_parent = $(email_status).parent().parent();
        var email_subject = $('#email_subject');
        var email_subject_parent = $(email_subject).parent().parent();

        $(email_status_parent).hide();
        $(email_template_editor_parent).hide();
        $(email_subject_parent).hide();
        $(email_status).attr('checked', false);

        if($(this).val() == '0'){
            return;
        }else{
            $.post(
                UPMEAdminModules.AdminAjax,
                {
                    'action'        : 'upme_get_email_template',
                    'template_name'   : $(this).val(),
                },
                function(response){

                    if(response.status == 'success'){
                        $(email_template_editor).val(response.temp_content).show();
                        $(email_template_editor_parent).show();
                        $('#email_status').val(response.temp_status).trigger("chosen:updated");
                        $(email_status_parent).show();
                        $(email_subject).val(response.temp_subject).show();
                        $(email_subject_parent).show();
                    }

                },"json"
            );
        }
    });

	/* Private Content Restriction Settings */
    $('#upme-display-create-res-rule').click(function(){
    	$('#upme-site-restrictions-list').hide();
    	$('#upme-site-restrictions-create').show();
    });
    
    $('#upme-display-list-res-rule').click(function(){
    	$('#upme-site-restrictions-list').show();
    	$('#upme-site-restrictions-create').hide();
    });

    if($('#site_content_section_restrictions')){
		upme_show_content_section_params();
	}

	$('#site_content_section_restrictions').change(function(){
		upme_show_content_section_params();
	});

	if($('#site_content_user_restrictions')){
		upme_show_user_restriction_params();
	}	

	$('#site_content_user_restrictions').change(function(){
		upme_show_user_restriction_params();
	});

	

	$('#add-upme-site-restriction-rule').click(function(){
		
		$('#upme-site-restrictions-settings-msg').html('').removeClass('').hide();
		$('#upme-add-site-restrictions-settings-msg').html('').removeClass('').hide();
		$('#upme-modules-settings-saved').hide();
    	
    	var data = $('#upme-site-restrictions-create-form').serialize();
    	var user_restrictions = $('#site_content_user_restrictions').val();

    	var error = 0;
    	var error_msg = '';
    	$('#add-upme-site-restriction-rule').attr("disabled", "disabled");
    	$('#add-upme-site-restriction-rule').val(UPMEAdminModules.savingResRule);

    	if($('#site_content_redirect_url').val() == '0'){
    		error_msg += '<p>'+ UPMEAdminModules.redirectURLRequired +'</p>';
    		error++;
    	}
    	if(user_restrictions == 'by_user_roles'){
    		var checked = false;
    		$(".site_content_allowed_roles :checkbox:checked").each(
                function() {
                    checked = true;
                }
            );

            if(!checked){
            	error_msg += '<p>'+ UPMEAdminModules.userRoleRequired +'</p>';
            	error++;
            }    		
    	}

    	var site_content_section_restrictions = $('#site_content_section_restrictions').val();
    	if(site_content_section_restrictions == 'restrict_selected_pages' || site_content_section_restrictions == 'restrict_selected_posts'){
    		var site_content_page_restrictions = $('#site_content_page_restrictions').val();
    		
    		var site_content_post_restrictions = $('#site_content_post_restrictions').val();
    		if(site_content_section_restrictions == 'restrict_selected_pages' && site_content_page_restrictions == null){
    			error_msg += '<p>'+ UPMEAdminModules.pageRequired +'</p>';
            	error++;
    		}else if(site_content_section_restrictions == 'restrict_selected_posts' && site_content_post_restrictions == null){
    			error_msg += '<p>'+ UPMEAdminModules.postRequired +'</p>';
            	error++;
    		}
    		
    	}

    	if(error != 0){
    		$('#upme-add-site-restrictions-settings-msg').html(error_msg).addClass('error').show();
    		
    		$('#add-upme-site-restriction-rule').removeAttr("disabled");
    		$('#add-upme-site-restriction-rule').val(UPMEAdminModules.saveResRule);

    	}else{
    	
	    	$.post(
		        UPMEAdminModules.AdminAjax,
		        {
		            'action': 'upme_save_site_restriction_rules',
		            'data':   data,
		        },
		        function(response){

		        	if(response.status == 'success'){
		        		$('#upme-modules-settings-saved').show();
	

	                    var htm = $('#upme_site_restriction_rules_titles').clone().wrap('<div>').parent().html() + response.rules;

	                    $('#upme_site_restriction_rules').html(htm);
		        	}

		        	// Reset form after adding a rule
		        	$('#site_content_user_restrictions').val('by_all_users').trigger('change').trigger("chosen:updated");
		        	$('#site_content_section_restrictions').val('all_pages').trigger('change').trigger("chosen:updated");
		        	$('#site_content_page_restrictions').val('').trigger("chosen:updated");
		        	$('#site_content_post_restrictions').val('').trigger("chosen:updated");
		        	$('#site_content_redirect_url').val('0').trigger("chosen:updated");
		        	$(".site_content_allowed_roles :checkbox").each(function(){
		                $(this).removeAttr('checked');
		            });

		        	$('#add-upme-site-restriction-rule').removeAttr("disabled");
    				$('#add-upme-site-restriction-rule').val(UPMEAdminModules.saveResRule);

		        },"json"
			);
	    }
    });

	$(document.body).on('click', '.upme_delete_restriction_rule',function(){
		var rule_id = $(this).parent().find('#upme_rule_id').val();

		$.post(
	        UPMEAdminModules.AdminAjax,
	        {
	            'action': 'upme_delete_site_restriction_rules',
	            'rule_id':   rule_id,
	        },
	        function(response){
	        	
	        	if(response.status == 'success'){
	        		$('#upme-modules-settings-saved').show();

                    var htm = $('#upme_site_restriction_rules_titles').clone().wrap('<div>').parent().html() + response.rules;

                    $('#upme_site_restriction_rules').html(htm);
	        	}
	        },"json"
		);
	});

    $(document.body).on('change', '.site_content_enable_restriction',function(){
        
        var rule_status = 0;
        if($(this).is(':checked')){
            rule_status = 1;
        }else{
            rule_status = 0;
        }
        
        var rule_id = $(this).parent().parent().find('#upme_rule_id').val();

        $.post(
            UPMEAdminModules.AdminAjax,
            {
                'action': 'upme_enable_site_restriction_rules',
                'rule_id':   rule_id,
                'rule_status' : rule_status,
            },
            function(response){
                
                if(response.status == 'success'){
                    $('#upme-modules-settings-saved').show();

                    var htm = $('#upme_site_restriction_rules_titles').clone().wrap('<div>').parent().html() + response.rules;

                    $('#upme_site_restriction_rules').html(htm);
                }
            },"json"
        );
    });

	// TODO - SAVE Modules Options
	$('.upme-save-module-options').click(function(){
    	
    	var btn_id = $(this).attr('id');    	
    	var current_tab = btn_id.replace('save-','');
    	var form_id = current_tab + '-form';
    	
    	$('#'+btn_id).attr("disabled", "disabled");
    	$('#'+btn_id).val(UPMEAdminModules.savingSetting);
    	
    	$.post(
    	        UPMEAdminModules.AdminAjax,
    	        {
    	            'action': 'save_upme_module_settings',
    	            'data':   $("#"+form_id).serialize(),
    	            'current_tab' : current_tab
    	        },
    	        function(response){
    	        	if(response.status == 'success'){
    	        		$('#'+btn_id).val(UPMEAdminModules.saveSetting);
	    	        	$('#'+btn_id).removeAttr("disabled");
	    	        	
	    	        	$('#upme-modules-settings-saved').show();
	    	        	setTimeout(function(){
	                        jQuery("#upme-modules-settings-saved").hide();
	                    }, 3000);
    	        	}
    	        	
    	        	
    	        }
    		,'json');
    });

	$('.upme-reset-module-options').click(function(){
	    	
	    var btn_id = $(this).attr('id');    	
    	var current_tab = btn_id.replace('reset-','');
    	var form_id = current_tab + '-form';
    	
    	$('#'+btn_id).attr("disabled", "disabled");
    	$('#'+btn_id).val(UPMEAdminModules.resettingSetting);    	
    	
    	$.post(
    	        UPMEAdminModules.AdminAjax,
    	        {
    	            'action': 'reset_upme_module_settings',
    	            'current_tab' : current_tab
    	        },
    	        function(response){
    	        	if(response.status == 'success'){
    	        		$('#'+btn_id).val(UPMEAdminModules.resetSetting);
	    	        	$('#'+btn_id).removeAttr("disabled");
	    	        	
	    	        	window.location = UPMEAdminModules.adminURL + '&reset=' +form_id;
    	        	}

	   	        }
    		,'json');
    });

    $('#site_lockdown_status').click(function(){
    	upme_show_lockdown_fields();
    });
    
    upme_show_lockdown_fields();

});

function upme_show_content_section_params(){

	var section_res = jQuery('#site_content_section_restrictions').val();
	if(section_res == 'all_posts' || section_res == 'all_pages'){
		jQuery('#site_content_page_restrictions').parent().parent().hide();
		jQuery('#site_content_post_restrictions').parent().parent().hide();
	}else if(section_res == 'restrict_selected_pages'){
		jQuery('#site_content_post_restrictions').parent().parent().hide();
		jQuery('#site_content_page_restrictions').parent().parent().show();
	}else if(section_res == 'restrict_selected_posts'){
		jQuery('#site_content_page_restrictions').parent().parent().hide();
		jQuery('#site_content_post_restrictions').parent().parent().show();
	}else {
		jQuery('#site_content_page_restrictions').parent().parent().show();
		jQuery('#site_content_post_restrictions').parent().parent().show();
	}
}

function upme_show_user_restriction_params(){
	if(jQuery('#site_content_user_restrictions').val() == 'by_user_roles'){
		jQuery('#site_content_allowed_roles').parent().parent().show();
	}else{
		jQuery('#site_content_allowed_roles').parent().parent().hide();
	}	
}

function upme_show_lockdown_fields(){
		if(jQuery("#site_lockdown_status").is(':checked')){
			jQuery('#site_lockdown_allowed_pages,#site_lockdown_allowed_posts,#site_lockdown_allowed_urls,#site_lockdown_redirect_url').parent().parent().show();
		}else{
			jQuery('#site_lockdown_allowed_pages,#site_lockdown_allowed_posts,#site_lockdown_allowed_urls,#site_lockdown_redirect_url').parent().parent().hide();
		}
}