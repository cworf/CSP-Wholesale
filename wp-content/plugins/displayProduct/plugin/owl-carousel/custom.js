jQuery(document).ready(function() {
    var owl = jQuery(".owl-carousel");
 
    owl.owlCarousel({
        items : dpcustomjs.columns, //10 items above 1000px browser width
        navigation : false,
        pagination : false
    });

    // Custom Navigation Events
    jQuery(".next").click(function(){
      owl.trigger("owl.next");
    });
    jQuery(".prev").click(function(){
      owl.trigger("owl.prev");
    });
    
        owlHeight=owl.height();
        customArrowMiddlePosition=(owlHeight/2)-20;
        jQuery(".customNavigation.sideMiddle a").css("top",customArrowMiddlePosition);
});