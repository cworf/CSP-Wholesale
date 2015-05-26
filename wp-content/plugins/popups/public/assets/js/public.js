(function($){
    "use strict";

var SPU_master = function() {

	var windowHeight 	= $(window).height();
	var isAdmin 		= spuvar.is_admin;
	var $boxes 			= [];

	SPU_reload_socials();

	//remove paddings and margins from first and last items inside box
	$(".spu-content").children().first().css({
		"margin-top": 0,
		"padding-top": 0
	}).end().last().css({
		'margin-bottom': 0,
		'padding-bottom': 0
	});

	// loop through boxes
	$(".spu-box").each(function() {

		// move to parent in safe mode
		if( spuvar.safe_mode ){

			$(this).prependTo('body');
			
		}

		// vars
		var $box 			= $(this);
		var triggerMethod 	= $box.data('trigger');
		var timer 			= 0;
		var testMode 		= (parseInt($box.data('test-mode')) === 1);
		var id 				= $box.data('box-id');
		var autoHide 		= (parseInt($box.data('auto-hide')) === 1);
		var secondsClose    = parseInt($box.data('seconds-close'));			
		var triggerSeconds 	= parseInt( $box.data('trigger-number'), 10 );
		var triggerPercentage = ( triggerMethod == 'percentage' ) ? ( parseInt( $box.data('trigger-number'), 10 ) / 100 ) : 0.8;
		var triggerHeight 	= ( triggerPercentage * $(document).height() );
		
		facebookFix( $box );
		//correct widths of sharing icons
		$('.spu-google').width($('.spu-google').width()-20);
		$('.spu-twitter').width($('.spu-twitter ').width()-50);
		
		//center spu-shortcodes
		var swidth 		= 0;
		var free_width 	= 0;
		var boxwidth	= $box.outerWidth();
		var cwidth 		= $box.find(".spu-content").width();
		var total  		= $box.data('total'); //total of shortcodes used


		//wrap them all
		$box.find(".spu-shortcode").wrapAll('<div class="spu_shortcodes"/>');
		if( total && ! spuvar.disable_style && $(window).width() > boxwidth ){ 
		
			//calculate total width of shortcodes all togheter
			$box.find(".spu-shortcode").each(function(){
				swidth = swidth + $(this).width();
			});
			//available space to split margins
			free_width = cwidth - swidth - total;

		}
		if( free_width > 0 ) {
			//leave some margin
			$box.find(".spu-shortcode").each(function(){

                $(this).css('margin-left',(free_width / 2 ));

			});
			//remove margin when neccesary
			if( total == 2) {

				$box.find(".spu-shortcode").last().css('margin-left',0);

			} else if( total == 3) {

				$box.find(".spu-shortcode").first().css('margin-left',0);
			
			}
		}

		
		//close with esc
		$(document).keyup(function(e) {
			if (e.keyCode == 27) {
				toggleBox( id, false, false );
			}
		});
		//close on ipads // iphones
		var ua = navigator.userAgent,
		event = (ua.match(/iPad/i) || ua.match(/iPhone/i)) ? "touchstart" : "click";
		
		$('body').on(event, function (ev) {
			// test that event is user triggered and not programatically
			if( ev.originalEvent !== undefined ) {

				toggleBox( id, false, false );
				
			}				
		});
		//not on the box
		$('body' ).on(event,'.spu-box', function(event) {
			event.stopPropagation();
		});

		//hide boxes and remove left-99999px we cannot since beggining of facebook won't display
		$box.hide().css('left','');

		// add box to global boxes array
		$boxes[id] = $box;

		// functions that check % of height
		var triggerHeightCheck = function() 
		{
			if(timer) { 
				clearTimeout(timer); 
			}

			timer = window.setTimeout(function() { 
				var scrollY = $(window).scrollTop();
				var triggered = ((scrollY + windowHeight) >= triggerHeight);

				// show box when criteria for this box is matched
				if( triggered ) {

					// remove listen event if box shouldn't be hidden again
					if( ! autoHide ) {
						$(window).unbind('scroll', triggerHeightCheck);
					}

					toggleBox( id, true, false );
				} else {
					toggleBox( id, false, false );
				}

			}, 100);
		}
		// function that show popup after X secs
		var triggerSecondsCheck = function() 
		{
			if(timer) { 
				clearTimeout(timer); 
			}

			timer = window.setTimeout(function() { 

				toggleBox( id, true, false );

			}, triggerSeconds * 1000);
		}

		// show box if cookie not set or if in test mode
		var cookieValue = spuReadCookie( 'spu_box_' + id );

		if( ( cookieValue == undefined || cookieValue == '' ) || ( isAdmin && testMode ) ) {
			
			if(triggerMethod == 'seconds') {
				triggerSecondsCheck();
			} else {
				$(window).bind( 'scroll', triggerHeightCheck );
				// init, check box criteria once
				triggerHeightCheck();
			}	

			// shows the box when hash refers to a box
			if(window.location.hash && window.location.hash.length > 0) {

				var hash = window.location.hash;
				var $element;

				if( hash.substring(1) === $box.attr( 'id' ) ) {
					setTimeout(function() {
						toggleBox( id, true, false );
					}, 100);
				}
			}
		}	/* end check cookie */
        //close popup
        $box.on('click','.spu-close-popup',function() {

			// hide box
			toggleBox( id, false, false );

			if(triggerMethod == 'percentage') {
				// unbind 
				$(window).unbind( 'scroll', triggerHeightCheck );
			}	
			
		});
		
		// add link listener for this box
		$('a[href="#' + $box.attr('id') +'"]').click(function() { 
			
			toggleBox(id, true, false);
			return false;
		});

		// add class to the gravity form if they exist within the box
		$box.find('.gform_wrapper form').addClass('gravity-form');

        // Disable ajax on form by adding .spu-disable-ajax class to it
        $box.on('submit','form.spu-disable-ajax', function(){

            $box.trigger('spu.form_submitted', [id]);
            toggleBox(id, false, true );
        });

        // Add generic form tracking
        $box.on('submit','form:not(".wpcf7-form, .gravity-form, .infusion-form, .spu-disable-ajax")', function(e){
         	e.preventDefault();

            
            var submit 	= true,
            form 		= $(this),
            data 	 	= form.serialize(),
            url  	 	= form.attr('action'),
            error_cb 	= function (data, error, errorThrown){
            	console.log('Spu Form error: ' + error + ' - ' + errorThrown);
            },	
            success_cb 	= function (data){
            	
            	var response = $(data).filter('#spu-'+ id ).html();
            	$('#spu-' + id ).html(response);

            	// check if an error was returned for m4wp
            	if( ! $('#spu-' + id ).find('.mc4wp-form-error').length ) {

                	// give 2 seconds for response
                	setTimeout( function(){

                		toggleBox(id, false, true );
                		
                	}, spuvar.seconds_confirmation_close * 1000);

                }	
            };
            // Send form by ajax and replace popup with response
            request(data, url, success_cb, error_cb, 'html');

            $box.trigger('spu.form_submitted', [id]);

            return submit;
         });

        // CF7 support
        $('body').on('mailsent.wpcf7', function(){
            $box.trigger('spu.form_submitted', [id]);
        	toggleBox(id, false, true );
        }); 

        // Gravity forms support (only AJAX mode)
        $(document).on('gform_confirmation_loaded', function(){
            $box.trigger('spu.form_submitted', [id]);
        	toggleBox(id, false, true );
        });

        // Infusion Software - not ajax
        $box.on('submit','.infusion-form', function(e){
            e.preventDefault();
            $box.trigger('spu.form_submitted', [id]);
        	toggleBox(id, false, true );
            this.submit();
        });

	});
	

	//function that center popup on screen
	function fixSize( id ) {
		var $box 			= $boxes[id];
		var windowWidth 	= $(window).width();
		var windowHeight 	= $(window).height();
		var popupHeight 	= $box.outerHeight();
		var popupWidth 		= $box.outerWidth();
		var intentWidth		= $box.data('width');
		var left 			= 0;
		var top 			= windowHeight / 2 - popupHeight / 2;
		var position 		= 'fixed';
		var currentScroll   = $(document).scrollTop();

		if( $box.hasClass('spu-centered') ){
			if( intentWidth < windowWidth ) {
				left = windowWidth / 2 - popupWidth / 2;
			}
			$box.css({
				"left": 	left,
				"position": position,
				"top": 		top,
			});
		}

		// if popup is higher than viewport we need to make it absolute
		if( (popupHeight + 50) > windowHeight ) {
			position 	= 'absolute';
			top 		= currentScroll;
			
			$box.css({
				"position": position,
				"top": 		top,
				"bottom": 	"auto",
				//"right": 	"auto",
				//"left": 	"auto",
			});
		}

	}

	//facebookBugFix
	function facebookFix( box ) {

		// Facebook bug that fails to resize
		var $fbbox = $(box).find('.spu-facebook');
		if( $fbbox.length ){
			//if exist and width is 0
			var $fbwidth = $fbbox.find('.fb-like > span').width();
			if ( $fbwidth == 0 ) {
				var $fblayout = $fbbox.find('.fb-like').data('layout');
				 if( $fblayout == 'box_count' ) {

				 	$fbbox.append('<style type="text/css"> #'+$(box).attr('id')+' .fb-like iframe, #'+$(box).attr('id')+' .fb_iframe_widget span, #'+$(box).attr('id')+' .fb_iframe_widget{ height: 63px !important;width: 80px !important;}</style>');

				 } else {
					
					$fbbox.append('<style type="text/css"> #'+$(box).attr('id')+' .fb-like iframe, #'+$(box).attr('id')+' .fb_iframe_widget span, #'+$(box).attr('id')+' .fb_iframe_widget{ height: 20px !important;width: 80px !important;}</style>');

				 }	
			}
		}
	}

    /**
     * Main function to show or hide the popup
     * @param id int box id
     * @param show boolean it's hiding or showing?
     * @param conversion boolean - Its a conversion or we are just closing
     * @returns {*}
     */
    function toggleBox( id, show, conversion ) {
		var $box 	= $boxes[id];
		var $bg	 	= $('#spu-bg-'+id);
		var $bgopa 	= $box.data('bgopa');

		// don't do anything if box is undergoing an animation
		if( $box.is( ":animated" ) ) {
			return false;
		}

		// is box already at desired visibility?
		if( ( show === true && $box.is( ":visible" ) ) || ( show === false && $box.is( ":hidden" ) ) ) {
			return false;
		}

		//if we are closing , set cookie
		if( show === false) {
			// set cookie
			var days = parseInt( $box.data('cookie') );
			if( days > 0 ) {
				spuCreateCookie( 'spu_box_' + id, true, days );
			}
            $box.trigger('spu.box_close', [id]);
		} else {
            $box.trigger('spu.box_open', [id]);
			//bind for resize
			$(window).resize(function(){
				
				fixSize( id );

			});
			fixSize( id );
		
		}
		
		// show box
		var animation = $box.data('spuanimation'),
            conversion_close = $box.data('close-on-conversion');


        if (animation === 'fade') {
            if (show === true) {
                $box.fadeIn('slow');
            } else if (show === false && ( (conversion_close && conversion ) || !conversion )  ) {
                    $box.fadeOut('slow');
            }
        } else {
            if (show === true ) {
                $box.slideDown('slow');
            } else if (show === false && ( (conversion_close && conversion ) || !conversion )  ) {
                $box.slideUp('slow');
            }
        }

        //background
        if (show === true && $bgopa > 0) {
            $bg.fadeIn();
        } else if (show === false && conversion_close ) {
            $bg.fadeOut();
        }

		return show;
	}

	return {
		show: function( box_id ) {
			return toggleBox( box_id, true, false );
		},
		hide: function( box_id ) {
			return toggleBox( box_id, false, false );
		},
		request: function( data, url, success_cb, error_cb ) {
			return request( data, url, success_cb, error_cb );
		}
	}

}
if( spuvar.ajax_mode ) {

    var data = {
        pid : spuvar.pid,
        referrer : document.referrer,
        is_category : spuvar.is_category,
        is_archive : spuvar.is_archive
    }
    ,success_cb = function(response) {
    	
    	$('body').append(response);
    	window.SPU = SPU_master();
		SPU_reload_forms(); //remove spu_Action from forms
    	
    },
    error_cb 	= function (data, error, errorThrown){
        console.log('Problem loading popups - error: ' + error + ' - ' + errorThrown);
    }
    request(data, spuvar.ajax_mode_url , success_cb, error_cb, 'html');
} else {

	jQuery(window).load(function() {

		window.SPU = SPU_master();
	
	});
}

    /**
     * Ajax requests
     * @param data
     * @param url
     * @param success_cb
     * @param error_cb
     * @param dataType
     */
    function request(data, url, success_cb, error_cb, dataType){
        // Prepare variables.
        var ajax       = {
                url:      spuvar.ajax_url,
                data:     data,
                cache:    false,
                type:     'POST',
                dataType: 'json',
                timeout:  30000
            },
            dataType   = dataType || false,
            success_cb = success_cb || false,
            error_cb   = error_cb   || false;

        // Set ajax url is supplied
        if ( url ) {
            ajax.url = url;
        }
        // Set success callback if supplied.
        if ( success_cb ) {
            ajax.success = success_cb;
        }

        // Set error callback if supplied.
        if ( error_cb ) {
            ajax.error = error_cb;
        }

        // Change dataType if supplied.
        if ( dataType ) {
            ajax.dataType = dataType;
        }
        // Make the ajax request.
        $.ajax(ajax);
        
    }
/**
 * Cookie functions
 */
function spuCreateCookie(name, value, days) {
	if (days) {
		var date = new Date();
		date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
		var expires = "; expires=" + date.toGMTString();
	} else var expires = "";
	document.cookie = name + "=" + value + expires + "; path=/";
}

function spuReadCookie(name) {
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for (var i = 0; i < ca.length; i++) {
		var c = ca[i];
		while (c.charAt(0) == ' ') c = c.substring(1, c.length);
		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
	}
	return null;
}

/** 
 * Social Callbacks
 */
var SPUfb = false;

var FbTimer = setInterval(function(){
	if( typeof FB !== 'undefined' && ! SPUfb) {
		subscribeFbEvent();

	}
},1000);

if ( typeof twttr !== 'undefined') {
    try{
        twttr.ready(function(twttr) {
            twttr.events.bind('tweet', twitterCB);
            twttr.events.bind('follow', twitterCB);
        });
    }catch(ex){}
}


function subscribeFbEvent(){
    try {
        FB.Event.subscribe('edge.create', function (href, html_element) {
            var box_id = $(html_element).parents('.spu-box').data('box-id');
            if (box_id) {
                SPU.hide(box_id);
            }
        });
    }catch(ex){}
	SPUfb = true;
	clearInterval(FbTimer);
}
function twitterCB(intent_event) {

	var box_id = $(intent_event.target).parents('.spu-box').data('box-id');

	if( box_id) {
		SPU.hide(box_id);
	}
}
function googleCB(a) {

	if( "on" == a.state ) {

		var box_id = jQuery('.spu-gogl').data('box-id');
		if( box_id) {
			SPU.hide(box_id);
		}
	}
}
function closeGoogle(a){
	if( "confirm" == a.type )
	{
		var box_id = jQuery('.spu-gogl').data('box-id');
		if( box_id) {
			SPU.hide(box_id);

		}
	}
}
function SPU_reload_socials(){
	if( spuvar_social.facebook ) {

		// reload fb
		try{
			FB.XFBML.parse();
		}catch(ex){}
	}
	if( spuvar_social.google ){
        try {
            // reload google
            gapi.plusone.go();
        }catch(ex){}
	}
	if( spuvar_social.twitter ){
        try {
            //reload twitter
            twttr.widgets.load();
        }catch(ex){}
	}
}
function SPU_reload_forms(){
	// Clear actions
	$('.spu-box form').each( function(){
		var action = $(this).attr('action');
        if( action ){
            $(this).attr('action' , action.replace('?spu_action=spu_load',''));
        }
	});
	if ($.fn.wpcf7InitForm) {
		$('.spu-box div.wpcf7 > form').wpcf7InitForm();
	}
}
})(jQuery);