jQuery(function($){
    var jcrop_api, boundx, boundy,

    // Grab some information about the preview pane
    preview = jQuery("#upme-preview-pane"),
    cnt = jQuery("#upme-preview-pane .upme-preview-container"),
    pimg = jQuery("#upme-preview-pane .upme-preview-container img"),

    xsize = 150,
    ysize = 150;

    var jcrop_api;

    jQuery("#target").Jcrop({
        aspectRatio: 1,
        setSelect: [ 0, 0, 100, 100 ],
        minSize: [25,25],
        boxWidth: 400,
        boxHeight: 350,
        onChange: updatePreview,
        onSelect: updatePreview,
    },function(){
        var bounds = this.getBounds();
        boundx = bounds[0];
        boundy = bounds[1];
        // Store the API in the jcrop_api variable
        jcrop_api = this;

        // Move the preview into the jcrop container for css positioning
        preview.appendTo(jcrop_api.ui.holder);
    });

    function updatePreview(c){
        if (parseInt(c.w) > 0){
            var rx = xsize / c.w;
            var ry = ysize / c.h;

            //jQuery("#upme-preview-pane").html("x1:"+c.x+"-x2:"+c.x2+"-y1:"+c.y);
            jQuery("#upme-crop-x1").val(Math.round(c.x));
            jQuery("#upme-crop-x2").val(Math.round(c.x2));
            jQuery("#upme-crop-y1").val(Math.round(c.y));
            jQuery("#upme-crop-y2").val(Math.round(c.y2));
            jQuery("#upme-crop-width").val(Math.round(c.w));
            jQuery("#upme-crop-height").val(Math.round(c.h));
    

            pimg.css({
                width: Math.round(rx * boundx) + "px",
                height: Math.round(ry * boundy) + "px",
                marginLeft: "-" + Math.round(rx * c.x) + "px",
                marginTop: "-" + Math.round(ry * c.y) + "px"
            });
                                    
        }
    }
                                
});