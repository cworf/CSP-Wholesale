(function($){

	$(function(){

		$('#the-list').on('click', 'a.editinline', function(){

			$('#the-list .inline-edit-row [name="tt_tax_order_input"]').val($(this).closest('tr').find('.column-order').text());

			return false;

		});

	});

})(jQuery);