jQuery(document).ready(function(){
    jQuery("select.festi-user-role-map-to-settings-select").change(function(){
            if(jQuery(this).val() == 'custom_field') {
                jQuery(this).closest('td').find('.custom_field_settings').show(400);
            } else {
                jQuery(this).closest('td').find('.custom_field_settings').hide(400);
            }

            if(jQuery(this).val() == 'product_image_by_url' || jQuery(this).val() == 'product_image_by_path') {
                jQuery(this).closest('td').find('.product_image_settings').show(400);
            } else {
                jQuery(this).closest('td').find('.product_image_settings').hide(400);
            }

            if(jQuery(this).val() == 'post_meta') {
                jQuery(this).closest('td').find('.post_meta_settings').show(400);
            } else {
                jQuery(this).closest('td').find('.post_meta_settings').hide(400);
            }
        });


        jQuery("select.festi-user-role-map-to-settings-select").trigger('change');

        jQuery(window).resize(function(){
            jQuery("#import_data_preview").addClass("fixed").removeClass("super_wide");
            jQuery("#import_data_preview").css("width", "100%");

            var cell_width = jQuery("#import_data_preview tbody tr:first td:last").width();
            if(cell_width < 60) {
                jQuery("#import_data_preview").removeClass("fixed").addClass("super_wide");
                jQuery("#import_data_preview").css("width", "auto");
            }
        });


        jQuery(window).trigger('resize');
});