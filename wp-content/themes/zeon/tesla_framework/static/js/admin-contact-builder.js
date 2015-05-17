/*jshint multistr: true */
jQuery(function($) {
  var out_of_container = false;

  function enable_draggable() {
    $(".column > ul").sortable({
      over: function(event, ui) {
        out_of_container = false;
        //ui.item.css('background-color','green')
      },
      out: function(event, ui) {
        out_of_container = true;
        //ui.item.css('background-color','cyan')
      },
      beforeStop: function(event, ui) {
        if (out_of_container === true) {
          ui.item.remove();
        }
      }
    }).droppable({
      tolerance: 'pointer',
      drop: function(event, ui) {
        ui.draggable.css('background-color', 'green');
      }
    });

    $(".form-elements li").draggable({
      connectToSortable: ".column > ul",
      helper: "clone",
      revert: "invalid",
      opacity: 0.8,
    });

    $('.form-row').sortable();
    $("ul, li").disableSelection();
  }

  enable_draggable();

  //Add new row===========
  $('.main-container').on('click', '.add-new-row', function() {
    $(this).before('<div class="row-fluid form-row">\
                      <div class="span12 column">\
                        <ul></ul>\
                      </div>\
                      <span class="row-edit">+</span>\
                      <span class="row-delete">&times;</span>\
                      <ul class="column-picker clearfix">\
                        <li data-columns="6,6">6-6</li>\
                        <li data-columns="8,4">8-4</li>\
                        <li data-columns="4,4,4">4-4-4</li>\
                        <li data-columns="4,8">4-8</li>\
                      </ul>\
                    </div>');
    enable_draggable();
  });

  //Delete row=================
  $('.main-container').on('click', '.row-delete', function() {
    $(this).parent().remove();
  });

  //Edit row=================
  $('.main-container').on('click', '.row-edit', function() {
    $(this).siblings('.column-picker').toggle();
  });

  //Add new form===========
  $('#add-new-form').on('click', function() {
    var form_location_selector = $('.form_location')[0].outerHTML;
    //console.log(form_location_selector);
    $(this).before('<div class="builder-body form-template">\
                      <header class="form-meta">\
                      <input type="text" class="form-id" placeholder="Unique Form ID">\
                      <input type="text" class="form-receiver-email" placeholder="Receiver\'s email (empty for admin email)">\
                      '+form_location_selector+'\
                      </header>\
                      <div class="row-fluid form-row">\
                        <div class="span12 column">\
                          <ul></ul>\
                        </div>\
                        <span class="row-edit">+</span>\
                        <span class="row-delete">&times;</span>\
                        <ul class="column-picker clearfix">\
                          <li data-columns="6,6">6-6</li>\
                          <li data-columns="8,4">8-4</li>\
                          <li data-columns="4,4,4">4-4-4</li>\
                          <li data-columns="4,8">4-8</li>\
                        </ul>\
                      </div>\
                      <button class="add-new-row btn btn-info">Add row</button>\
                      <span class="form-delete">&times;</span>\
                    </div>');
    enable_draggable();
  });

  //Delete form=================
  $('.main-container').on('click', '.form-delete', function() {
    if($('.form-template').length === 1){
      $('.form-template').find('.form-row').remove();
      $('.form-id').val('');
      $('.form-receiver-email').val('');
    }else{
      $(this).parent().remove();
    }
  });

  //Save forms===========================================
  $('.save-forms').on('click', function() {
    btn = $(this);
    btn.button('loading');
    var form_builder = {};
    form_builder.forms = [];
    $('.form-template').each(function() { //forms
      var form              = {};
      form.id               = $(this).find('.form-id').val();
      form.receiver_email   = $(this).find('.form-receiver-email').val();
      form.disable_headers  = $(this).find('.form-disable-headers').is(':checked') ? 1 : '';
      form.location         = $(this).find('.form_location').val();
      form.rows             = [];
      $(this).find('.form-row').each(function() { //rows
        var row = {};
        row.columns = [];
        $(this).find('.column').each(function() { //columns
          var column = {};
          column.form_elements = [];
          column.size = $(this).attr('class').charAt(4) + $(this).attr('class').charAt(5).replace(' ', '');
          $(this).find('.form-element').each(function() { //form elements
            var form_element = {};
            //form_element.type = $(this).attr('data-element-type');
            form_element = $(this).data('element');
            //form_element.placeholder = $(this).attr('data-placeholder');
            column.form_elements.push(form_element);
            //console.log(form_element);
          });

          row.columns.push(column);
        });

        form.rows.push(row);
      });

      form_builder.forms.push(form);
    });
    //console.log(form_builder);
    $.ajax({
      url: ajaxurl,
      type: 'POST',
      data: {
        action: "contact_builder_save_forms",
        form_builder: form_builder
      },
      success: function(response) {
        //console.log(response);
        if (response === '1') {
          btn.button('success');
        } else {
          btn.button('fail');
          btn.removeClass('btn-success').addClass('btn-danger');
        }
      }
    });
    setTimeout(function() {
      btn.button('reset');
      btn.removeClass('btn-danger').addClass('btn-success');
    }, 3000);
  });

  //Edit row columns====================================================
  $('.main-container').on('click', '.column-picker li', function() {
    var columns_str = $(this).attr('data-columns');
    var columns = columns_str.split(',');
    var row = '';
    $(this).parent().parent().find('.column').remove();
    for (var i = 0; i < columns.length; i++) {
      row += '<div class="span' + columns[i] + ' column">\
                        <ul></ul>\
                      </div>';
    }
    $(this).parent().parent().prepend(row);
    enable_draggable();
  });

  //Edit element========================================================
  $('.main-container').on('click', '.form-template .form-element .config', function(e) {
    e.preventDefault();
    var form_element = $(this).parent();
    var element_parameters = form_element.data('element');
    var element_type = element_parameters.type;
    var edit_form = $('#edit-form-element-modal form');
    edit_form.find('.element-property').hide();
    $('.modal').modal('show');
    edit_form[0].reset();
    $.each(element_parameters,function(parameter,value){
      console.log(parameter,value);
      if($.inArray( parameter, ['type','title'] ) === -1){
        if($.inArray( parameter, ['required'] ) !== -1 && (value === true || value === "true")){
          edit_form.find('[name='+parameter+']').prop('checked', true);
        }else{
          edit_form.find('[name='+parameter+']').val(value);
        }
        edit_form.find('[name='+parameter+']').parent().show();
      }
    });
    //console.log(element_parameters);
    
    //Saving new/updated element parameters
    $('#edit-form-element-modal .submit').unbind().on('click',function(event) {
      event.preventDefault();
      $.each(element_parameters,function(parameter,value){
        if($.inArray( parameter, ['type','title'] ) === -1){
          if($.inArray( parameter, ['required'] ) !== -1){
            element_parameters[parameter] = edit_form.find('[name='+parameter+']').is(':checked');
            //console.log(edit_form.find('[name='+parameter+']').is(':checked'));
          }else{
            element_parameters[parameter] = edit_form.find('[name='+parameter+']').val();
          }
        }
      });

      form_element.data('element', element_parameters);
      $('.modal').modal('hide');
    });
  });
});