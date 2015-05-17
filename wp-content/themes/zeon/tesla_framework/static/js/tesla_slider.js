// SLIDER \\_______________________________________________________________________________________________________________________________________________

(function($){

    $(function(){

    	var t_recursive;

    	t_recursive = function(t_parent){

    		var t = t_parent.children('select:not([name])');
    		var t_options = t_parent.children('.tesla-option');

    		if(t.length){

    			var t_disabled = t_options.not('[data-option="'+t.val()+'"]');
    			var t_selected = t_options.filter('[data-option="'+t.val()+'"]');

    			t_disabled.find('input, textarea, select[name]').not('.tesla-option-template input, .tesla-option-template textarea, .tesla-option-template select[name]').prop('disabled',true);
    			t_disabled.css({display:'none'});

    			t_selected.css({display:'block'});
    			t_selected.each(function(){
    				t_recursive($(this));
    			});

    		}else{

    			if(t_options.length){

    				t_options.css({display:'block'});
    				t_options.each(function(){
    					t_recursive($(this));
    				});

    			}else{

    				t_options = t_options.children('.tesla-option-container').children('.tesla-option');

    				if(t_options.length){

    					t_options.css({display:'block'});
    					t_options.each(function(){
    						t_recursive($(this));
    					});

    				}else{

    					t_parent.find('input, textarea, select[name]').prop('disabled',false);

    				}

    			}

    		}

    	};

		//add button
		$('#slide_options').on('click','.tesla-option>button',function(){

			var t = $(this);
			var t_index = t.data('index');

			var t_template = undefined;
			var t_clone = undefined;

			var t_parent = t.parent();
			var t_options = t_parent.children('.tesla-option-container');
			var t_legend = t_parent.children('legend');

			var t_select = undefined;

			if(t_index===undefined)
				t_index = t_options.length;

			t_template = t.next('.tesla-option-template').html().replace(/(\sname="[^"]*?\[)(\][^"]*")/g,'$1'+t_index+'$2');
			t_clone = $('<div>').addClass('tesla-option-container').html(t_template);

			t_index++;
			t.data('index',t_index);

			t_recursive(t_clone);

			t_clone.find('.tesla-option-date').not('.tesla-option-template .tesla-option-date').datepicker({ dateFormat: "yy-mm-dd" });
			t_clone.find('.tesla-option-color').not('.tesla-option-template .tesla-option-color').wpColorPicker();

			t_clone.find('.tesla-option>button').each(function(){
				var t = $(this);
				var t_parent = t.parent();
				t_parent.sortable({
					axis: "y",
					containment: "parent",
					cursor: "move",
					items: '>.tesla-option-container',
					handle: false,
					placeholder: "tesla_option_holder",
					forcePlaceholderSize: true
				});
			});

			if(t_options.length)
				t_options.filter(':last').after(t_clone);
			else
				if(t_legend.length)
					t_legend.after(t_clone);
				else
					t_parent.prepend(t_clone);

		});

		//remove button
		$('#slide_options').on('click','.tesla-option-container>button',function(){
			$(this).parent().remove();
		});

		//image type
		$('#slide_options').on('click','.tesla-option-container>img',function(){
			var t = $(this);
			var t_parent = t.parent();
			var frame = t.data('frame');
			var t_hidden = t_parent.children('input[type="hidden"]');
			var id = t_hidden.prop('value');
			var a = wp.media.model.Attachment;
			if(frame===undefined){
				frame = wp.media();
				t.data('frame',frame);

				frame.on( 'open',function(){

					var s = this.get('library').get('selection');
					var f;
					var x;

					if(id&&''!==id&&-1!==id&&'0'!==id){
						f = a.get(id);
						f.fetch();
						x = [f];
					}else
						x = [];

					s.reset(x);

				}).state('library').on('select',function(){

					var s = this.get('selection').first().toJSON();
					var url;

					id = s.id;
					url = s.url;

					t_hidden.prop('value',id);
					t.prop('src',url);

				});
			}
			
			frame.open();
		});

		//non grouped options
		$('#slide_options').on('change','select:not([name])',function(){

			t_recursive($(this).parent('.tesla-option,.tesla-option-container'));
			
		});
		
		//date & color types
		$('#slide_options .tesla-option-date').not('.tesla-option-template .tesla-option-date').datepicker({ dateFormat: "yy-mm-dd" });
		$('#slide_options .tesla-option-color').not('.tesla-option-template .tesla-option-color').wpColorPicker();

		//sortable for options with multiple attribute (except editor type)
		$('#slide_options .tesla-option>button').each(function(){
			var t = $(this);
			var t_parent = t.parent();
			if(!t_parent.find('.tesla-input-editor').length){
				t_parent.sortable({
					axis: "y",
					containment: "parent",
					cursor: "move",
					items: '>.tesla-option-container',
					handle: false,
					placeholder: "tesla_option_holder",
					forcePlaceholderSize: true,
				});
			}
		});

    });

})(jQuery);