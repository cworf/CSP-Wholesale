jQuery("select.dpOrderby,select.dpPerpage").change(function(){
    jQuery(this).closest("form").submit();
});

