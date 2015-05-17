jQuery(document).ready(function($) {
	$('.tt-form').on('submit', function(event) {
		if($(this).parsley( 'isValid' )){
			event.preventDefault();
			var form = $(this);
			var submit = form.find('input[type=submit]');
			var submit_val = $(submit).val();
			var timeout;
			if(typeof contact_form_send === "function"){
				contact_form_send(form,submit);
			}else{
				$(submit).val($(submit).data('init_val',submit_val).data('sending'));
			}
			var action = "action=" + $(this).attr('action') + "&";
			//console.log(form.serialize());
			$.ajax({
				url: ajaxurl,
				type: 'POST',
				data: action + form.serialize(),
				success: function(response) {
					//console.log(form,response);
					if(typeof(contact_form_results) === "function"){
						contact_form_results(form,response);
					}else{
						if (response === '1') {
							form.find('.tt-form-result').html(response);
						} else {
							form.find('.tt-form-result').html(response + "error");
						}
						$(submit).val($(submit).data('sent'));
						clearTimeout(timeout);
						t_timeout = setTimeout(function () {
							$(submit).val(submit_val);
						}, 3000);
					}
					
				}
			});
		}
		return false;
	});
});