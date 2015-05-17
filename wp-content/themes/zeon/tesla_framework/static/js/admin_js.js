jQuery(document).ready(function($) {
    //Disable submiting with enter key==================================================================================
    $('input').keydown(function(event){
      if($(this).siblings('.upload_image_button').length === 0 && !$(this).hasClass('map_search'))
        if(event.keyCode == 13) {
          return false;
        }
      });
    //====================Color Picker===============================================
    $('.my-color-field').wpColorPicker();
    //====================Text Color Picker===============================================
    $('.text_color').wpColorPicker({
            change: function(event, ui){
                var color = $(this).attr('value');
                $(this).parents('.tt_content_box_content').find('.tt_show_logo').css('color',color);
            }
    });
    //====================Font Changer===============================================
    $('.font_changer').on('change',function(){
        var apiUrl = [];
        var font;
        font = $(this).val();
        if (font){
        //==============================================
          $('body').append("<link rel='stylesheet' href='http://fonts.googleapis.com/css?family=" + escape(font) +"' type='text/css' media='all' />");
          $(this).parents('.tt_content_box_content').find('.font_preview').css({'font-family':'"'+ font +'"'});
        }
    }).each(function(endex,element){
        font = $(this).val();
        if (font){
          $('body').append("<link rel='stylesheet' href='http://fonts.googleapis.com/css?family=" + escape(font) +"' type='text/css' media='all' />");
        }
    });
    //--------------------Font Size Changer------------------------------------------
    $('.font_size_changer').on('change',function(){
        var size = $(this).val() + $(this).attr('data-size-unit');
        $(this).parents('.tt_content_box_content').find('.change_font_size').css('font-size',size);
    });
    $('.font_preview').siblings('input[type=text]').on('keyup',function(){
        $('.font_preview').html($(this).val());
    });
    //====================Image uploader=============================================
    // Uploading files
    var file_frame;
    $('.upload_image_button').live('click', function( event ){
        event.preventDefault();
        var button = $(this);
        // If the media frame already exists, reopen it.
//        if ( file_frame ) {
//          file_frame.open();
//          return;
//        }
        // Create the media frame.
        file_frame = wp.media.frames.file_frame = wp.media({
          title: jQuery( this ).data( 'uploader_title' ),
          button: {
            text: jQuery( this ).data( 'uploader_button_text' )
          },
          editing_sidebar : true,
          displaySettings: true,
          displayUserSettings: true,
          multiple: false  // Set to true to allow multiple files to be selected
        });
     
        // When an image is selected, run a callback.
        file_frame.on( 'select', function() {
          // We set multiple to false so only get one image from the uploader
          attachment = file_frame.state().get('selection').first().toJSON();
     
          // Do something with attachment.id and/or attachment.url here or do console.log(attachment) to get the list
          button.prev('input').attr('value',attachment.url);
          button.parent().find('.tt_show_logo img').attr('src',attachment.url);
        });
     
        // Finally, open the modal
        file_frame.open();
      });
      //---------------REMOVE UPLOADED IMAGE--------------------------------
      $('.remove_img').on('click',function( event ){
              event.preventDefault();
              var def = TT_FW + '/static/images/tesla_logo.png';
              $(this).next().next('.tt_show_logo').find('img').attr('src',def).siblings('input[type=text]').attr('value','');
              $(this).prev().prev('input[type=text]').attr('value','');
          });
      //=================DATEPICEKR========================================
          $( ".datepicker" ).datepicker();
      //=================TT Interact (show/hide elements in admin through other elements) =======================================
          $(".tt_interact").each(function(i,e){
              action = $(this).attr('data-tt-interact-action');
              if (!$(this).is(':checked') && action == 'show'){
                  objs = $.parseJSON($(this).attr('data-tt-interact-objs'));
                  $.each(objs,function(i,e){
                      if (action == 'show'){
                          $('#'+e).hide();
                          $('#'+e).next('.tt_explain').hide();
                          $('#'+e).prev('.tt_option_title').hide();
                      }
                  });
              }
          });
          $(".tt_interact").on('change', function(){
              objs = $.parseJSON($(this).attr('data-tt-interact-objs'));
              action = $(this).attr('data-tt-interact-action');
              $.each(objs,function(i,e){
                  if (action == 'show'){
                      $('#'+e).toggle('fast');
                      $('#'+e).next('.tt_explain').toggle('fast');
                      $('#'+e).prev('.tt_option_title').toggle('fast');
                  }
              });
          });
      //=================Social PLatforms===================================
          $('.tt_share_platform li input').on('keyup',function() {
              if(!$(this).val())
                  $(this).removeClass('social_active');
              else
                  $(this).addClass('social_active');
          });
          //-------------ShareThis------requires select2.min.js library-----
          function format(item) {
              if (!item.id) return item.text; // selected option
              return "<img class='social_icon' src='"+TT_FW+"/static/images/social/" + item.id.toLowerCase() + "_32.png'/> ";
          }
          function format_result(item) {
              if (!item.id) return item.text; // result option
              return "<img class='social_icon2' src='"+TT_FW+"/static/images/social/" + item.id.toLowerCase() + "_32.png'/> " + item.text;
          }
          $(".social_search").select2({
              formatResult: format_result,
              formatSelection: format,
              escapeMarkup: function(m) { return m; }
          });
      //=================SELECT WITH SEARCH=================================
          $('.font_search').select2({
              placeholder: "Select a Font",
              allowClear: true
          });  //requires select2.min.js library
      //=================GOOGLE MAP=========================================
        $('.tt_map_container').each(function(index,element){
          var map;
          var map_zoom;
          var coords = $(this).find(".map-coords").val();
          var latlong;
          if (coords){
              latlong = coords.split(',');
          }else
              latlong = [42.60,-41.16];
          var saved_coords = new google.maps.LatLng(latlong[0],latlong[1]);
          if ($(this).find(".map-zoom").val())
              map_zoom = parseInt($(this).find(".map-zoom").val(), 10);
          else
              map_zoom = 2;
          /**
           * The HomeControl adds a control to the map that
           * returns the user to the control's defined home.
           */
          // Define a property to hold the Home state
          HomeControl.prototype.home_ = null;

          // Define setters and getters for this property
          HomeControl.prototype.getHome = function() {
            return this.home_;
          }

          HomeControl.prototype.setHome = function(home) {
            this.home_ = home;
          }
          
          /** @constructor */
          function HomeControl(controlDiv, map, home) {

            // We set up a variable for this since we're adding
            // event listeners later.
            var control = this;

            // Set the home property upon construction
            control.home_ = home;

            // Set CSS styles for the DIV containing the control
            // Setting padding to 5 px will offset the control
            // from the edge of the map
            controlDiv.style.padding = '5px';
            
            //------------------SAVE MAP LOCATION-----------------------------------
            // Set CSS for the setHome control border
            var setHomeUI = document.createElement('div');
            setHomeUI.setAttribute('class' , "tt_drop_marker");
            setHomeUI.style.backgroundColor = 'white';
            setHomeUI.style.borderStyle = 'solid';
            setHomeUI.style.borderWidth = '1px';
            setHomeUI.style.borderColor = '#888';
            setHomeUI.style.cursor = 'pointer';
            setHomeUI.style.textAlign = 'center';
            setHomeUI.title = 'drop the pin';
            controlDiv.appendChild(setHomeUI);

            // Set CSS for the control interior
            var setHomeText = document.createElement('div');
            setHomeText.style.fontFamily = 'Arial,sans-serif';
            setHomeText.style.fontSize = '12px';
            setHomeText.style.paddingLeft = '4px';
            setHomeText.style.paddingRight = '4px';
            setHomeText.innerHTML = '<b>Drop marker here</b>';
            setHomeUI.appendChild(setHomeText);

            // Setup the click event listener for Set Home:
            // Set the control's home to the current Map center.
            google.maps.event.addDomListener(setHomeUI, 'click', function() {
              $(element).find('.marker-coords').get(0).value = map.getCenter().lat() + "," + map.getCenter().lng();
              marker.setPosition(map.getCenter());
              marker.setMap(map);
            });
          }
          
          //Create the default marker-----------------------------------
          var image = '';
          if($(this).find('.map-icon input[type=radio]:checked').val())
              image = new google.maps.MarkerImage($(element).find('.map-icon input[type=radio]:checked').val(),
                  new google.maps.Size(32.0, 37.0),
                  new google.maps.Point(0, 0),
                  new google.maps.Point(16.0, 34.0)
              );
          var shadow_link = ($(this).find('.map-icon input[type=radio]:checked').val())? $(element).find('.map-icon input[type=radio]:checked').val() + '.shadow.png' : "";
          var shadow = '';
          if(shadow_link !== '')
              shadow = new google.maps.MarkerImage(shadow_link ,
                  new google.maps.Size(51.0, 37.0),
                  new google.maps.Point(0, 0),
                  new google.maps.Point(16.0, 34.0)
              );
          var marker = new google.maps.Marker({
              draggable:true,
              animation: google.maps.Animation.DROP,
              icon:image,
              shadow: shadow
          });
          //change marker icon on clik of radio button with icon--------------------------------------------------------
         $(this).find('.map-icon input[type=radio]').change(function(){
             var image = new google.maps.MarkerImage($(this).val(),
                     new google.maps.Size(32.0, 37.0),
                     new google.maps.Point(0, 0),
                     new google.maps.Point(16.0, 34.0)
                 );
             var shadow = new google.maps.MarkerImage($(this).val() + '.shadow.png',
                     new google.maps.Size(51.0, 37.0),
                     new google.maps.Point(0, 0),
                     new google.maps.Point(16.0, 34.0)
                 );
             marker.setIcon(image);
             marker.setShadow(shadow);
         });
          function initialize() {
            //init the map---------------------------------
            var mapDiv = $(element).find(".map-canvas").get(0);
            var mapOptions = {
              zoom: map_zoom,
              center: saved_coords,
              mapTypeId: google.maps.MapTypeId.ROADMAP
            };
            map = new google.maps.Map(mapDiv, mapOptions);
            //autocomplete----------------------------------------------------------------------------------------------
            function TheAutoComplete(controlDiv, map, home) {
                controlDiv.style.padding = '5px';
                controlDiv.style.width = '35%';
                //------------------Autocomplete address-----------------------------------
                // Set CSS for the control
                autocompleteinput = document.createElement('input');
                autocompleteinput.id = 'map_autocomplete_input';
                autocompleteinput.style.paddingLeft = '4px';
                autocompleteinput.style.paddingRight = '4px';
                autocompleteinput.type = 'text';
                autocompleteinput.placeholder = 'Adress';
                controlDiv.appendChild(autocompleteinput);
            }
            // Controls Init-----------------------------------------------
            var homeControlDiv = document.createElement('div');
            var homeControl = new HomeControl(homeControlDiv, map, saved_coords);
            var autocompleteinput;
            homeControlDiv.index = 1;
            map.controls[google.maps.ControlPosition.TOP_RIGHT].push(homeControlDiv);
            autocompleteinput= $(element).find('.map_search').get(0);
            var autocomplete = new google.maps.places.Autocomplete(autocompleteinput);
            autocomplete.bindTo('bounds', map);
            //var infowindow = new google.maps.InfoWindow();
            google.maps.event.addListener(autocomplete, 'place_changed', function() {
                //infowindow.close();
                marker.setVisible(false);
                $(autocompleteinput).removeClass('notfound');
                var place = autocomplete.getPlace();
                if (!place.geometry) {
                  // Inform the user that the place was not found and return.
                  $(autocompleteinput).addClass('notfound');
                  return;
                }

                // If the place has a geometry, then present it on a map.
                if (place.geometry.viewport) {
                  map.fitBounds(place.geometry.viewport);
                } else {
                  map.setCenter(place.geometry.location);
                  map.setZoom(17);  // Why 17? Because it looks good.
                }
                marker.setPosition(map.getCenter());
                $(element).find('.marker-coords').get(0).value = map.getCenter().lat() + "," + map.getCenter().lng();
                marker.setVisible(true);
                
                //Infoview----------------------
//                var address = '';
//                if (place.address_components) {
//                  address = [
//                    (place.address_components[0] && place.address_components[0].short_name || ''),
//                    (place.address_components[1] && place.address_components[1].short_name || ''),
//                    (place.address_components[2] && place.address_components[2].short_name || '')
//                  ].join(' ');
//                }
//
//                infowindow.setContent('<div><strong>' + place.name + '</strong><br>' + address);
                //infowindow.open(map, marker);
                //Infoview end-------------------
           });
           //Selecting the first option of autocomplete-----------------
            $(element).find(".map_search").focusin(function () {
                $(element).find(".map_search").keypress(function (e) {
                    if (e.which == 13) {
                         selectFirstResult();
                         event.preventDefault();
                         return false;
                    }
                });
            });
            $(element).find(".map_search").focusout(function () {
                if(!$(element).find(".pac-container").is(":focus") && !$(element).find(".pac-container").is(":visible"))
                    selectFirstResult();
            });
             
             function selectFirstResult() {
                //infowindow.close();
                $(".pac-container").hide();
                var firstResult = $(".pac-container").eq(index).find(".pac-item:first").text();
                var geocoder = new google.maps.Geocoder();
                geocoder.geocode({"address":firstResult }, function(results, status) {
                    if (status == google.maps.GeocoderStatus.OK) {
                        var lat = results[0].geometry.location.lat(),
                            lng = results[0].geometry.location.lng(),
                            placeName = results[0].address_components[0].long_name,
                            latlng = new google.maps.LatLng(lat, lng);
                        $(element).find(".map_search").val(firstResult);
                     // If the place has a geometry, then present it on a map.
                        if (results[0].geometry.viewport) {
                          map.fitBounds(results[0].geometry.viewport);
                        } else {
                          map.setCenter(results[0].geometry.location);
                          map.setZoom(17);  // Why 17? Because it looks good.
                        }
                        marker.setPosition(latlng);
                        $(element).find('.marker-coords').get(0).value = latlng.lat() + "," + latlng.lng();
                        marker.setVisible(true);
                    }
                });
             }
             //Selecting the first option of autocomplete END-----------------
            //-------------------Autocomplete end-----------------------------------------------------------------------
            if ($(element).find(".marker-coords").val()){
                var coords = $(element).find(".marker-coords").val().split(',');
                marker.setPosition(new google.maps.LatLng(coords[0],coords[1]));
                marker.setMap(map);
            }
            
            // adds a listener to the marker
            // gets the coords when drag event ends
            // then updates the input with the new coords
            google.maps.event.addListener(marker, 'dragend', function(evt){
                $(element).find('.marker-coords').get(0).value = evt.latLng.lat() + "," + evt.latLng.lng();
//                marker.setPosition(evt.latLng);
//                marker.setMap(map);
            });
            
            google.maps.event.addListener(map, 'idle', update_inputs);
            function update_inputs(){
                var newHome = map.getCenter();
                var coords = newHome.lat() + ", " + newHome.lng();
                var newZoom = map.getZoom();
                $(element).find(".map-coords").val(coords);
                $(element).find(".map-zoom").val(newZoom);
                
            }
          }//function initialize map end
          //Bounce the marker
          google.maps.event.addListener(marker, 'click', toggleBounce);
          function toggleBounce() {
              if (marker.getAnimation() !== null) {
                marker.setAnimation(null);
              } else {
                marker.setAnimation(google.maps.Animation.BOUNCE);
              }
          }
          
          google.maps.event.addDomListener(window, 'load', initialize);
        //reinit the map when tab is shown (fixes the map not showing in full size when page loads with the tab that doesnt contain the map)
        $('.tt_left_menu li').on('click',function(){
          setTimeout(function(){
            if( $('.tt_tab.active').find('.map-canvas').length > 0 ){
              x = map.getZoom();
              c = map.getCenter();
              google.maps.event.trigger(map, 'resize');
              map.setZoom(x);
              map.setCenter(c);
            }
          },25);
        });
      }); //end .each(map)
//>>>>>>>>>>>>>>>================MAP END==================================<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<

//===========================AJAX SAVE OPTIONS======================================================================
  jQuery('.tt_admin form').submit(function() {
      $('.tt_submit').addClass('tt_button_loading').attr('disabled','disabled').attr('value','Saving...');
      this.action.value='save_options';//changing form action that should be the suffix of ajax handle function wp_ajax_$action
      //updating codemirror textreas before ajax magic
      if($('#custom_css').length){
        custom_css_codemirror.save();
      }
      if($('#custom_js').length){
        custom_js_codemirror.save();
      }
      var data = jQuery(this).serialize();
      jQuery.post(ajaxurl, data, function(response) {
        $('.tt_submit').removeClass('tt_button_loading').removeAttr('disabled').attr('value','Save Options');
        if(response == 'options updated' || response == 'options did not change') {
          //show options saved alert----that fades out
          $('.tt_bottom_note').fadeIn('slow');
          var note = setTimeout(function(){
              $('.tt_bottom_note').fadeOut('slow');
          },4000);
        } else {
          console.log(response);
        }
      });
      return false;
    });
  //===========================AJAX MailChimp Get Lists======================================================================
  $('#get_mailchimp_lists').on('click',function(event){
    event.preventDefault();
    $('.mailchimp_modal').show().find('form label,p').remove();
    $('.mailchimp_modal').prepend("<p>Loading MailChimp lists...</p>");
    $.ajax({
        url:ajaxurl,
        type:'POST',
        data:{action:"get_mailchimp_lists",api_key:$('#mailchimp_api_key').val()},
        dataType:'json',
        success:function(lists){
          $('.mailchimp_modal p').remove();
          if($.type(lists)==='string'){
            $('.mailchimp_modal form').prepend("<label>" + lists + "</label>");
          }else{
            $.each(lists,function(index,list){
              $('.mailchimp_modal form').prepend("<label><input type='radio' name='mailchimp_list_id' value='"+list.id+"'>"+list.name+" - id : "+list.id+"</label>");
            });
          }
        }
      });
    $('.mailchimp_modal .tt_button.choose').on('click',function(event){
      event.preventDefault();
      $('#mailchimp_list_id').attr('value',$('.mailchimp_modal form input[name=mailchimp_list_id]:checked').val());
      $('.mailchimp_modal').hide();
    });
    $('.mailchimp_modal .tt_button.close').on('click',function(event){
      event.preventDefault();
      $('.mailchimp_modal').hide();
    });
  });
  //===========================AJAX Clear subscribtions======================================================================
  $('.tt_btn.clear').on('click',function(event) {
      event.preventDefault();
      if(confirm("Are you sure you want to delete all your subscribers' info from database ? "))
        $.post(ajaxurl, {
            action:'clear_subscriptions',//changing form action that should be the suffix of ajax handle function wp_ajax_$action
            c:true
          }, function(response) {
          if(response === 'Done'){
            $('.subscribers_container').html('<p>No subscribers yes...</p>');
          }
        });
      return false;
    });
  //==========================Add new item/option==============================================================================
  $('.repeat_box').on('click',function(event){
    var cloned = $(this).parent().parent().prev('.tt_content_box_content').find('.options_block:eq(0)').clone();
    var nr = 1;

    if($(this).parent().prev('.tt_content_box_content').find('.options_block')){
      nr = $(this).parent().parent().prev('.tt_content_box_content').find('.options_block').length;
    }

    $(cloned).addClass('cloned')
      .find('input:not(:checkbox),textarea')
        .val('')
        .removeAttr('id');
    $(cloned).find('input:checkbox').removeAttr('checked');
    $(cloned).find('.img_preview').attr('src',TT_FW + '/static/images/tesla_logo.png');
    $(cloned).find('.datepicker').removeClass('hasDatepicker');

    $(cloned).find('.tt_option_title span.tt_the_title').each(function(){
      var title = ($.isNumeric($(this).text().slice(-1))) ? $(this).text().slice(0,-1) : $(this).text();
      $(this).text(title + ' ' + nr);
    });
    //repeating a colorpciker:
    if ($(cloned).find('.wp-picker-input-wrap').length > 0){
      $(cloned).find('.wp-picker-input-wrap input[type=text]').each(function(){
        $(this).val('');
        $(this).insertAfter($(this).parents('.wp-picker-container').prev('.tt_option_title'));
      });
      $(cloned).find('.wp-picker-container').remove();
      //initializing newly created colorpickers (1ms needed for the dom to get ready)
      setTimeout(function(){
        $('.my-color-field').wpColorPicker();
      },1);
    }
    $(cloned).hide().appendTo($(this).parent().parent().prev('.tt_content_box_content')).slideDown('fast');
    //initializing newly created datepickers
    $('body').on('focusin','.datepicker:not(.hasDatepicker)',function(){
      $(this).datepicker();
    });

    return false;
  });
  //==========================REMOVE new item/option==============================================================================
  $('.remove_option').live('click',function(event){
    var box = $(this).parents('.tt_content_box_content');
    if($(this).parents('.tt_box').find('.options_block').length == 1){
      box.find('input[type=text],textarea').val('');
      var def = TT_FW + '/static/images/tesla_logo.png';
      box.find('.tt_show_logo img').attr('src',def).siblings('input[type=text]').attr('value','');
    }else{
      $(this).parents('.options_block').slideUp('fast',function(){
        $(this).remove();
        box.find('.options_block').each(function(index,block){
          $(this).find('.tt_option_title span.tt_the_title').each(function(){
            var title = ($.isNumeric($(this).text().slice(-1))) ? $(this).text().slice(0,-1) : $(this).text();
            $(this).text( title + ' ' + index );
          });
        });
      });
    }
  });
  //===========================AutoUpdate Checker==================================================================================
  $('.check_update').on('click',function(event){
    event.preventDefault();
    var button = $(this);
    $.ajax({
      url:ajaxurl,
      data:{action:"check_update"},
      dataType:'json',
      success:function(result){
        console.log(result);
        if (result !== null){
          $('.check_update_result').html('Version <b>'+result.version+'</b> available <a class="update_now" title="Go to updates page" href="update-core.php">Go to Updates Page</a>').show();
        }else
          $('.check_update_result').html('You have the latest version installed.').show();
      }
    });
  });

  //=================CodeMirror==================================
  if($('#custom_css').length){
    var custom_css_codemirror = CodeMirror.fromTextArea(document.getElementById("custom_css"), {
      lineNumbers: true,
      mode: "css",
      theme: "mdn-like",
      matchBrackets: true,
      autoCloseBrackets: true
    });
  }
  if($('#custom_js').length){
    var custom_js_codemirror = CodeMirror.fromTextArea(document.getElementById("custom_js"), {
      lineNumbers: true,
      mode: "javascript",
      theme: "mdn-like",
      matchBrackets: true,
      autoCloseBrackets: true
    });
  }

});//end document.ready

//=================TAB REMEMBER======================================
jQuery(function($) {
  $('a[data-toggle="tab"]').on('shown', function(e){
    //save the latest tab using a cookie:
      jQuery.cookie('last_tab_' + THEME_NAME, $(e.target).attr('href'));
  });
  //activate latest tab, if it exists:
  var lastTab = $.cookie('last_tab_' + THEME_NAME);
  if (lastTab) {
      $('ul.tt_left_menu').children().removeClass('active');
      $('a[href='+ lastTab +']').parents('li:first').addClass('active');
      $('div.tt_content').children().removeClass('active');
      $(lastTab).addClass('active');
  }
});

