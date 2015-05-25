jQuery(document).ready(function() 
{
    if (jQuery( ".woocommerce #order_data select[name='customer_user']" ).length > 0) {
            setUserIdForAjaxAction(jQuery( "select[name='customer_user']" ).val());
    }

    jQuery( ".woocommerce #order_data select[name='customer_user']" ).change(function () {
        setUserIdForAjaxAction(jQuery(this).val());
    })
    
    function setUserIdForAjaxAction(userId)
    {
        var data = {
            action: 'setUserIdForAjaxAction',
            userId: userId
        };
        
        jQuery.post(fesiWooPriceRole.ajaxurl, data, function(response) {
                if (response.status === false) {
                    alert('Woocommerce Price By Role: Error!');
                    return false;
                }
                //alert(response);
                return true;
        })
    } // end etUserIdForAjaxAction
}); 