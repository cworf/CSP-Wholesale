<script type="text/javascript">
jQuery(document).ready(function($) {

	/* Nice file upload */
	// Calling hidden and native element's action
	$('.upme-fileupload').click(function(){
	    if($('#file_'+$(this).attr('id')).length > 0)
	        $('#file_'+$(this).attr('id')).click();
	});

	// replace selected image path in custom div
	$(':file').change(function(){
	    if($('#'+$(this).attr('name')).length > 0)
	        $('#'+$(this).attr('name')).text($(this).val());
		
	});

	/* Tooltips */
	if($('.upme-tooltip').length > 0)
	{
		$('.upme-tooltip').tipsy({
			trigger: 'hover',
			offset: 4
		});
	}
	

    if($('.upme-go-to-page').length > 0)
    {
    	$('.upme-go-to-page').on('change', function(){
    	    if($(this).val() != 0)
            window.location = String($(this).val());
    	});
    }
	
	
	/* Check/uncheck */
	$('.upme-hide-from-public').click(function(e){
		e.preventDefault();
		if ($(this).find('i').hasClass('upme-icon upme-icon-square-o')) {
			$(this).find('i').addClass('upme-icon upme-icon-check-square-o').removeClass('upme-icon upme-icon-square-o');
			$(this).find('input[type=hidden]').val(1);
		} else {
			$(this).find('i').addClass('upme-icon upme-icon-square-o').removeClass('upme-icon upme-icon-check-square-o');
			$(this).find('input[type=hidden]').val(0);
		}
	});

	$('.upme-rememberme').click(function(e){
		e.preventDefault();
		if ($(this).find('i').hasClass('upme-icon upme-icon-square-o')) {
			$(this).find('i').addClass('upme-icon upme-icon-check-square-o').removeClass('upme-icon upme-icon-square-o');
			$(this).find('input[type=hidden]').val(1);
		} else {
			$(this).find('i').addClass('upme-icon upme-icon-square-o').removeClass('upme-icon upme-icon-check-square-o');
			$(this).find('input[type=hidden]').val(0);
		}
	});
	
		
	/* Toggle edit inline */
	$('.upme-field-edit a.upme-fire-editor').click(function(e){
		e.preventDefault();
		this_form = $(this).parent().parent().parent().parent().parent();
		if ($(this_form).find('.upme-edit').is(':hidden')) {
			if ($(this_form).find('.upme-view').length > 0) {
				$(this_form).find('.upme-view').slideUp(function() {
					$(this_form).find('.upme-edit').slideDown();
					$(this_form).find('.upme-field-edit a.upme-fire-editor').html('<?php _e('View Profile','upme'); ?>');
				});
			} else {
				$(this_form).find('.upme-main').show();
				$(this_form).find('.upme-edit').slideDown();
				$(this_form).find('.upme-field-edit a.upme-fire-editor').html('<?php _e('View Profile','upme'); ?>');
			}
		} else {
			$(this_form).find('.upme-edit').slideUp(function() {
				if ($(this_form).find('.upme-main').hasClass('upme-main-compact')) {
				$(this_form).find('.upme-main').hide();
				}
				$(this_form).find('.upme-view').slideDown();
				$(this_form).find('.upme-field-edit a.upme-fire-editor').html('<?php _e('Edit Profile','upme'); ?>');
			});
		}
	});
	
	/* Registration Form: Blur on email */
	$('.upme-registration').find('#reg_user_email').change(function(){
		var new_user_email = $(this).val();
		$('.upme-registration .upme-pic').load('<?php echo upme_url; ?>ajax/upme-get-avatar.php?email=' + new_user_email );
	});
	
	/* Change display name as User type in */
	$('.upme-registration').find('#reg_user_login').bind('change keydown keyup',function(){
		$('.upme-registration .upme-name .upme-field-name').html( $('#reg_user_login').val() );
	});


    // New Password request JS Code

    jQuery('[id^=upme-forgot-pass-]').on('click', function(){

        var counter = jQuery(this).attr('id').replace('upme-forgot-pass-','');
        
        jQuery('#upme-login-form-'+counter).css('display','none');
        jQuery('#upme-forgot-pass-holder-'+counter).css('display','block');
        jQuery('#login-heading-'+counter).html('<?php _e('Forgot Password','upme')?>');
        
    });
    
    jQuery('[id^=upme-back-to-login-]').on('click', function(){

        var counter = jQuery(this).attr('id').replace('upme-back-to-login-',''); 
        
        jQuery('#upme-login-form-'+counter).css('display','block');
        jQuery('#upme-forgot-pass-holder-'+counter).css('display','none');
        jQuery('#login-heading-'+counter).html('<?php _e('Login','upme')?>');
        
    });
    
    jQuery('[id^=upme-forgot-pass-btn-]').on('click', function(){

    	var counter = jQuery(this).attr('id').replace('upme-forgot-pass-btn-','');
        
        if(jQuery('#user_name_email-'+counter).val() == '')
        {
            alert('<?php _e('Please provide username or email address to reset password.','upme')?>');
        }
        else
        {
        	jQuery.post(
    					'<?php echo admin_url( 'admin-ajax.php' )?>', 
    				    {
    				        'action': 'request_password',
    				        'user_details':   jQuery('#user_name_email-'+counter).val()
    				    }, 
    				    function(response){

    				    	var forgot_pass_msg=
        				    	{
    				    	        "invalid_email" : "<?php _e('Please provide email address which is registered with us.','upme')?>",
    				    	        "invalid"       : "<?php _e('Please enter valid user name or email address.','upme')?>",
    				    	        "not_allowed"   : "<?php _e('User with given details is not allowed to change password.','upme')?>",
    				    	        "mail_error"    : "<?php _e('We are unable to deliver email to your email address. Please contact site admin.','upme')?>",
    				    	        "success"       : "<?php _e('We have sent password reset link to your email address.','upme')?>",
    				    	        "default"       : "<?php _e('Something went wrong, Please try again','upme');?>"        
    				    	    }

        				    if(typeof(forgot_pass_msg[response]) == 'undefined')
        				    {
        				    	alert(forgot_pass_msg['default']);
            				}
        				    else
        				    {
        				    	alert(forgot_pass_msg[response]);
        				      if(response == 'success')
        				    	    jQuery('#upme-back-to-login-'+counter).trigger('click');
            				}
    				    	
    				    }
    				);
        }
    });

    jQuery("[id^=upme-forgot-pass-holder-]").css('display','none');

});
</script>
