<?php
function carouselCustom($display_id,$columns,$arrowandpagination){
    switch ($arrowandpagination) {
        case 1:
            $arrow='enable';
            $dot='disable';
            break;
        case 2:
            $arrow='disable';
            $dot='enable';
            break;
        case 3:
            $arrow='enable';
            $dot='enable';
            break;
        default:
            $arrow='enable';
            $dot='disable';
            break;
    }
    
    if($dot=='disable'){
        $disabledot=',
            navigation : false,
            pagination : false';
    }
    if($arrow=='enable'){
        // Custom Navigation Events
        $enablearrow='jQuery("#displayProduct-'.$display_id.' .customNavigation .next").click(function(){
          owl.trigger("owl.next");
        });
        jQuery("#displayProduct-'.$display_id.' .customNavigation .prev").click(function(){
          owl.trigger("owl.prev");
        });

        owlHeight=owl.height();
        customArrowMiddlePosition=(owlHeight/2)-20;
        jQuery("#displayProduct-'.$display_id.' .customNavigation.sideMiddle a").css("top",customArrowMiddlePosition);
       ';
    }
    $carouselcustom=' <script type="text/javascript">
    jQuery(document).ready(function() {
        var owl = jQuery( "#displayProduct-'.$display_id.' .owl-carousel");
            
        owl.owlCarousel({
            items : '.$columns.$disabledot.'
        });
        '.$enablearrow.'
   });</script>';
    $carouselcustom=preg_replace('/^\s+|\n|\r|\s+$/m', '', $carouselcustom);
    return $carouselcustom;
}
?>
