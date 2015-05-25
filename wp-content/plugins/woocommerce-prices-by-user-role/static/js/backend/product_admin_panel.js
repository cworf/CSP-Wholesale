jQuery(document).ready(function() 
{
     jQuery('img.festi-user-role-prices-tooltip').poshytip({
            className: 'tip-twitter',
            showTimeout:100,
            alignTo: 'target',
            alignX: 'center',
            alignY: 'bottom',
            offsetY: 5,
            allowTipHover: false,
            fade: true,
            slide: false
        });
        
    //jQuery('input[value="festiUserRolePrices"]').parent().parent().hide();
    //jQuery('input[value="festiUserRoleHidenPrices"]').parent().parent().hide();
}); 