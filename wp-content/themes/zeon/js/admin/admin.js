jQuery(document).ready(function($){
	if($('#page_template').val() === 'template-home-slider-wide.php'){
			$('#slider').show();
		}else{
			$('#slider').hide();
		}
	$('#page_template').on('change',function(){
		if($(this).val() === 'template-home-slider-wide.php'){
			$('#slider').show();
		}else{
			$('#slider').hide();
		}
	});
});