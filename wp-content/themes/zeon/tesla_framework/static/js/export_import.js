jQuery(document).ready(function($){
  //===================Clearing options START============================================================
  var timer;
  function clear_result(){
    clearTimeout(timer);
    timer = setTimeout(function(){
      $('progress').animate({'value':'0'},1000);
      $('#result').fadeOut('slow',function(){$(this).html('').show().css('color','black');});
    },3000);
  }

  $('#clear').on('click',function(){
    var btn = $(this);
    var action_long = btn.attr('data-action');
    var action = btn.attr('id');
    var old_text = btn.text();
    btn.text( action_long );
    $('progress').stop().animate({'value':'50'},500,function(){
      $.post(ajaxurl, {
        option_action:action,
        action:'options_actions'
      }, function(response) {
          if (response === '1'){
            $('progress').stop().animate({'value':'100'},1000,function(){$('#result').html('Options Cleared.').hide().fadeIn('slow',function(){
                clear_result();
                btn.text( old_text );
              });
            });
          }else{
            $('#result').html("Options failed to be cleared or already clear.").hide().fadeIn().css('color','red');
          }
      });
    });
  });
  //===================Clearing options END============================================================
  //===================Exporting options START============================================================
  $('#export').on('click',function(){
    var btn = $(this);
    var action_long = btn.attr('data-action');
    var action = btn.attr('id');
    var old_text = btn.text();
    btn.text( action_long );
    $('progress').stop().animate({'value':'50'},500,function(){
      $.post(ajaxurl, {
      option_action:action,
      action:'options_actions'
    }, function(response) {
          btn.text( old_text );
          if (response === '1'){
            $('progress').stop().animate({'value':'100'},1000,function(){
              $('#result').html('Options Exported.').hide().fadeIn('slow',function(){
                var url = TT_FW + '/helpers/download.php?file=theme_options&TT_FW='+TT_FW+'&nonce=' + downloadNonce;
                window.location.href = url;
                clear_result();
              });
            });
          }else{
            $('#result').html(response).hide().fadeIn().css('color','red');
          }
      });
    });
  });
  //===================Exporting options END============================================================
  //===================Importing options START============================================================
  $('#import').on('click',function(){
    var btn = $(this);
    var action_long = btn.attr('data-action');
    var action = btn.attr('id');
    btn.fadeOut();
    $('#controls button.btn').animate({'opacity':'0.5'}).attr('disabled','disabled');
    $('#upload_form').slideDown('slow');
    $('#cancel').on('click',function(event){
      event.preventDefault();
      $('#upload_form').slideUp('slow',function(){
        btn.fadeIn('fast');
      });
      $('#controls button.btn').animate({'opacity':'1'}).removeAttr('disabled');
    });

    var options = {
        target:        '',      // target element(s) to be updated with server response 
        beforeSubmit:  showRequest,     // pre-submit callback 
        success:       showResponse,    // post-submit callback 
        url:    ajaxurl                 // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php     
      };

    // bind form using 'ajaxForm' 
    $('#upload_form').ajaxForm(options);

    function showRequest(formData, jqForm, options) {
      //do extra stuff before submit like disable the submit button
      $('progress').stop().animate({'value':'50'},500);
    }

    function showResponse(responseText, statusText, xhr, $form)  {
      if (responseText === '1'){
        $('progress').stop().animate({'value':'100'},1000,function(){$('#result').html('Options Imported.').hide().fadeIn('slow',function(){
            clear_result();
          });
        });
      }else{
        $('#result').html(responseText).css('color','red');
        clear_result();
      }
    }
  });
  //===================Importing options END============================================================
  //===================Importing Demo options START============================================================
  $('#reset').on('click',function(){
    var btn = $(this);
    var action_long = btn.attr('data-action');
    var action = btn.attr('id');
    var old_text = btn.text();
    btn.text( action_long );
    $('progress').stop().animate({'value':'50'},500,function(){
      $.post(ajaxurl, {
        option_action:action,
        action:'options_actions'
      }, function(response) {
        if(response === '1'){
          $('progress').stop().animate({'value':'100'},500,function(){$('#result').html('Demo Options Imported.').hide().fadeIn('slow',function(){
              clear_result();
              btn.text( old_text );
            });
          });
        }else{
          $('#result').html(response).css('color','red');
        }
      });
    });
  });
  //===================Importing Demo options END============================================================

}); //>>>>>>>>>>>>>>>>>>>END DONCUMENT READY<<<<<<<<<<<<<<<<<<<<<<<<<<<<<