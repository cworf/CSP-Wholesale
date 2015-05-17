jQuery(document).ready(function($) {
    $('.flexslider-one,.flexslider-two,.flexslider-three,.flexslider-five').flexslider({
        animation: "fade",
        direction : "vertical",
        controlNav : false
    });
    
    $('.flexslider-four').flexslider({
        animation: "slide",
        itemWidth: 100,
        itemMargin: 5,
        minItems: 3,
        maxItems: 5,
        controlNav : false
      });
    
    $('.flexslider-six').flexslider({
        animation: "fade",
        controlNav: "thumbnails"
      });
    
    

});

