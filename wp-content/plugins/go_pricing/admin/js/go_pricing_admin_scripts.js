/* -------------------------------------------------------------------------------- /	
	
	Plugin Name: Go - Responsive Pricing & Compare Tables
	Plugin URI: http://codecanyon.net/item/go-responsive-pricing-compare-tables-for-wp/3725820
	Description: The New Generation Pricing Tables. If you like traditional Pricing Tables, but you would like get much more out of it, then this rodded product is a useful tool for you.
	Author: Granth
	Version: 2.4.5
	Author URI: http://themeforest.net/user/Granth
	
	+----------------------------------------------------+
		TABLE OF CONTENTS
	+----------------------------------------------------+
	
	[0] CUSTOM PLUGINS
	[1] SETUP & COMMON
	[2] SETTINGS PAGE
	[3] TABLE CREATOR PAGE
	[4] POPUP FUNCTIONS & EVENTS 
	
/ -------------------------------------------------------------------------------- */

	/* ---------------------------------------------------------------------- /
		[0] CUSTOM PLUGINS
	/ ---------------------------------------------------------------------- */	

;(function($) {
	
	/* ---------------------------------------------------------------------- /
		[1.2] gwPngAnim - Animate png (v1.0) - by Granth
		
		@options 
		duration : duration for a step (integer)
		steps : number of animation step (integer)
		startStep : start step index (integer)
		offsetX : horizontal background position offset (integer)
		offsetY : vertical background position offset (integer)
		
		@notes
		smaller duration value makes animataion faster,
		startStep index starts form 1 (not 0), if it reaches the maximum value
		animation continues from the first step
		
	/ ---------------------------------------------------------------------- */	
	
		$.fn.gwPngAnim = function(options) {
			var defaults = {
					'duration'	: 60, 
					'steps'		: 13,
					'startStep'	: 1,
					'offsetX'	: 0,
					'offsetY'	: -20
				},
				settings = $.extend({}, defaults, options);
				
			return this.each(function(index) {
				var $this=$(this),
					timer,
					bgPos=[],
					step=settings.startStep,
					/* old IEs (including IE8 does not support "backgroundPosition" css prop, only support "backgroundPositionX(Y)" */
					IE = $this.css("backgroundPosition") == 'undefined' || $this.css("backgroundPosition") == null ? true : false;
				
				$this.addClass('animated');
				
				if (IE) {
					bgPos[0]=$this.css("backgroundPositionX");
					bgPos[1]=$this.css("backgroundPositionY");
				} else {
					bgPos=$this.css("backgroundPosition").split(' ');	
				};
				
				timer = setInterval(function () {
					step = step == settings.steps ? 1 : step;
					var xPos=step*settings.offsetX+parseFloat(bgPos[0]),
						yPos=step*settings.offsetY+parseFloat(bgPos[1]);
					
					if (IE) {
						$this.css('backgroundPositionX',xPos+'px');
						$this.css('backgroundPositionY',yPos+'px');
					} else {
						$this.css({"backgroundPosition":xPos+"px "+yPos+"px"});
					};
					step++;
				}, settings.duration); 
			});		
		};
})(jQuery);

jQuery(document).ready(function($, undefined) {	
	
	/* ---------------------------------------------------------------------- /
		[1] SETUP & COMMON
	/ ---------------------------------------------------------------------- */	
	
		var $wpWrap = $('#wpwrap'),
			$goPricingAdmin=$wpWrap.find('#go-pricing-admin-wrap'),
			$goTablesForm=$goPricingAdmin.find('#go-pricing-form'),
			pluginUrl=$goPricingAdmin.data('plugin-url');

		/* open close panels */
		$goPricingAdmin.delegate('h3.hndle', 'click', function(){	
			var $this=$(this);
			
			if ($this.next('.inside').is(':visible')) {
				$this.next('.inside').slideUp().end().find('span').addClass('go-pricing-closed');
			} else {
				$this.next('.inside').slideDown().end().find('span').removeClass('go-pricing-closed');
			};
			if ($this.closest('.go-pricing-column').length) {
				var $collapseBtn=$this.closest('.go-pricing-column').find('.go-pricing-collapse-column');
				if (!$this.closest('.go-pricing-column').find('.go-pricing-closed').length) { 
					$collapseBtn.html('<span class="go-pricing-button-icon-collapse"></span>'+$collapseBtn.data('open'));
				} else {
					$collapseBtn.html('<span class="go-pricing-button-icon-collapse"></span>'+$collapseBtn.data('closed'));
				};
			};
		});
		
		/* checkbox list - open if child checked */
		$goPricingAdmin.delegate('.go-pricing-checkbox-parent', 'click', function(){
			var $this=$(this);
			if ($this.is(':checked')) {
				$this.closest('li').find('ul input[type="checkbox"]').removeAttr('checked');
			};		
		});
		
		$goPricingAdmin.find('.go-pricing-checkbox-list').each(function(index, element) {
			var $this=$(this);
			if ($this.find('input[type="checkbox"]:checked').length) {
				$this.prev().find('>span').addClass('go-pricing-closed').end().closest('li').find('>ul').show();
			};
		});

		/* check & uncheck all checkbox */
		$goPricingAdmin.delegate('.go-pricing-check-all, .go-pricing-uncheck-all', 'click', function(e){	
			var $this=$(this);
			e.preventDefault();
			if ($this.hasClass('go-pricing-check-all')) {
				$this.closest('li').siblings().find('>label input[type="checkbox"]').not(':checked').each(function(index, element) {
                    $(this).attr('checked','checked').trigger('click').attr('checked','checked');
                });
			} else {
				$this.closest('li').siblings().find('>label input[type="checkbox"]').removeAttr('checked');
			};
		});
		
		/* checkbox list event */
		$goPricingAdmin.delegate('.go-pricing-checkbox-list input[type="checkbox"]', 'click', function(){
			var $this=$(this);
			if ($this.is(':checked')) {
				if ($this.parents('.go-pricing-checkbox-list').length>1) {
					$this.parents('.go-pricing-checkbox-list').each(function(index, element) {
						var $obj=$(this);
						$obj.closest('.go-pricing-checkbox-list').prev('label').find('.go-pricing-checkbox-parent:first').removeAttr('checked');	
					});
				};
			};			
		});
		
		$goPricingAdmin.delegate('.go-pricing-checkbox-list label span', 'click', function(){
			var $this=$(this);
			if ($this.closest('label').find('input[type="checkbox"]').hasClass('go-pricing-checkbox-parent')) { 
				if (!$this.hasClass('go-pricing-closed')) {
				$this.addClass('go-pricing-closed')
				.closest('li').find('.go-pricing-checkbox-list:first').slideDown(200);
				} else {
					$this.removeClass('go-pricing-closed')
					.closest('li').find('.go-pricing-checkbox-list:first').slideUp(200);
				};
			};
			return false;
		});

		/* Show & Hide data groups */
		$goPricingAdmin.delegate('select[data-parent]', 'change', function(e) {
			var $this=$(this);
			$goPricingAdmin.find('.go-pricing-group[data-parent~="'+$this.data('parent')+'"]').hide();
			$goPricingAdmin.find('.go-pricing-group[data-parent~="'+$this.data('parent')+'"][data-children~="'+$this.find(':selected').data('children')+'"]').show();
			$goPricingAdmin.find('.go-pricing-group[data-parent~="'+$this.data('parent')+'"][data-children~="'+$this.find(':selected').data('children')+'"]').find('select').trigger('change');
		});

		$goPricingAdmin.find('#go-pricing-select').trigger('change');
		
			
	/* ---------------------------------------------------------------------- /
		[2] SETTINGS PAGE
	/ ---------------------------------------------------------------------- */			
		
		/* form ajax submit */
		$goPricingAdmin.find('#go-pricing-settings-form').submit(function(){
			var $this=$(this);
			$.ajax({  
				type: 'post', 
				url: ajaxurl,
				data: jQuery.param({ action: 'go_pricing_settings_ajax_submit' })+'&'+$this.serialize(),
				beforeSend: function () {
					$goPricingAdmin.find('.ajax-loading').css('visibility','visible');
				}
			}).always(function() {
				$goPricingAdmin.find('.ajax-loading').css('visibility','hidden');
			}).fail(function(jqXHR, textStatus) {
				$this.before('<div id="result" class="error"><p><strong>'+$this.data('ajaxerrormsg')+'</p></div>').delay(3000).slideUp(function(){ $(this).remove(); });	
			}).done(function(data) {
				var $ajaxResponse=$('<div />', { 'class':'ajax-response', 'html' : data }),
					$ajaxResult=$ajaxResponse.find('#result').delay(3000).slideUp(function(){ $(this).remove(); });
				
				$goPricingAdmin.find('#result').length ? $goPricingAdmin.find('#result').stop(true,true).slideDown(0).replaceWith($ajaxResult) : $this.before($ajaxResult); 
			});
			return false;
		});			

	/* ---------------------------------------------------------------------- /
		[3] TABLE CREATOR PAGE
	/ ---------------------------------------------------------------------- */

		/* common functions */

		/* source: http://stackoverflow.com/questions/7501761/div-scrollbar-width#7501799 */
		var getScrollbarWidth = function () { 
			var div = $('<div style="width:50px;height:50px;overflow:hidden;position:absolute;top:-200px;left:-200px;"><div style="height:100px;"></div></div>'); 
			$('body').append(div); 
			var w1 = $('div', div).innerWidth(); 
			div.css('overflow-y', 'auto'); 
			var w2 = $('div', div).innerWidth(); 
			$(div).remove(); 
			return (w1 - w2);
		}	
		
		var setThumbContainerHeight = function () {
			var $container=$goPricingAdmin.find('.go-pricing-thumbs'),
				thumbCount=$container.find('a').length,
				thumRowCount=Math.ceil(thumbCount/3);
				
			if (thumRowCount>=5) { $container.css('height','625px'); } else { $container.css('height',thumRowCount*125+'px'); };
		};
		
		var setThumbContainerWidth = function () {
			var $container=$goPricingAdmin.find('.go-pricing-thumbs'),
				thumbCount=$container.find('a').length,
				thumRowCount=Math.ceil(thumbCount/3);
				
			if (thumRowCount<=5) {
				$container.css('width','625px');
			} else {
				$container.css({
					'width':625+getScrollbarWidth()+'px',
					'overflow':'auto'
				});
			};
		};		

		var setWidth = function () {
			var currentWidth=($goPricingAdmin.find('.go-pricing-columns').find('li:first').width()+12)*($goPricingAdmin.find('.go-pricing-columns').find('li').length-1),
				defaultWidth=$goPricingAdmin.find('#go-pricing-settings-wrapper').width();

			$goPricingAdmin.find('.go-pricing-columns').css('width',currentWidth+12+'px');
			if (currentWidth>defaultWidth) { $goPricingAdmin.find('#go-pricing-column-wrapper').css('width',currentWidth+12+'px'); } else { $goPricingAdmin.find('#go-pricing-column-wrapper').css('width',defaultWidth+'px'); };
		};

		$(window).resize(function(e) { popupPos(); });		

		/* setup for main page */
		setThumbContainerHeight();
		setThumbContainerWidth();
		
		/* thumb & button behaviour for main page */
		$goPricingAdmin.delegate('.go-pricing-thumbs a', 'click dblclick mouseenter mouseleave', function(e) {
			var $this=$(this);
			
			e.preventDefault();
			if (e.type=='click') {
				$this.addClass('go-pricing-current').siblings().removeClass('go-pricing-current');
				$this.siblings().css('opacity','0.35').end().css('opacity','1');
				if ($(this).hasClass('go-pricing-thumb-create')) {
					$goPricingAdmin.find('.go-pricing-delete').css('display','none');
					$goPricingAdmin.find('.go-pricing-copy').css('display','none');
					$goPricingAdmin.find('.go-pricing-edit').val($goPricingAdmin.find('.go-pricing-edit').data('create'));
					$goPricingAdmin.find('#go-pricing-select').val('');
				} else {
					$goPricingAdmin.find('.go-pricing-delete').css('display','inline');
					$goPricingAdmin.find('.go-pricing-copy').css('display','inline');
					$goPricingAdmin.find('.go-pricing-edit').val($goPricingAdmin.find('.go-pricing-edit').data('edit'));
					$goPricingAdmin.find('#go-pricing-select').val($this.data('id'));				
				};
			} else if (e.type=='dblclick') {
				if ($goPricingAdmin.find('.go-pricing-edit')) {
					$goPricingAdmin.find('.go-pricing-edit').trigger('click');
				};
			} else if (e.type=='mouseenter') {
				$this.siblings().not('.go-pricing-current').css('opacity','0.35').end().end().css('opacity','1');
			} else if (e.type=='mouseleave') {
				if ($this.parent().find('a.go-pricing-current').length) {
					$this.parent().find('a').not('.go-pricing-current').css('opacity','0.35');
				} else {
					$this.parent().find('a').css('opacity','1');
				};
			};
		});
		
		/* align icon click event */
		$goPricingAdmin.delegate('a[class*="go-pricing-align-icon"]', 'click', function(e) {
			var $this=$(this);
			e.preventDefault();
			$this.siblings().removeClass('go-pricing-current');
			if (!$this.hasClass('go-pricing-current')) { $this.addClass('go-pricing-current'); };
			$this.prevAll('.go-pricing-col-align').val($this.data('id'));
		});

		/* shortcode selector click event */
		$goPricingAdmin.delegate('.go-pricing-sc-selector', 'change', function() {
			var $this=$(this);
			if ($this.val().indexOf('img-upload')!=0 && $this.val()!='') {
				$this.closest('tr').next().find('textarea, input').val($this.val());
			} else if ($this.val()!='') {
				tb_show('', 'media-upload.php?post_id=0&amp;type=image&amp;TB_iframe=true');
				var tbframe_interval = setInterval(function() {
					if ($('#TB_iframeContent').contents().find('.savesend .button').val()!=goPricingText.insert) {
						$('#TB_iframeContent').contents().find('.savesend .button').val(goPricingText.insert);
					} else {
						clearInterval(tbframe_interval);
					}
				}, 500);
				window.send_to_editor = function(html) {
					var classes, $html=$('<div />', { 'class':'media-html', 'html': html });
						if ($this.val()=='img-upload-responsive') { 
							classes='go-pricing-responsive-img';
						} else {
							classes=$html.find('img').attr('class');
						}
						$html.find('img').attr('class',classes);
					
					$this.closest('tr').next().find('textarea, input').val($html.find('img').wrap('<div class="imgwrap" />').end().find('.imgwrap').html());
				tb_remove();			
				};				
			};
		});

		/* checkbox change event */
		$goPricingAdmin.delegate('input[type="checkbox"]', 'change', function(e){	
			var $this=$(this);
			if ($this.is(':checked') ) {
				$this.next('input[type="hidden"]').val('1');
			} else {
				$this.next('input[type="hidden"]').val('0');
			};
		});
		
		/* label click event -> toggle checkbox or focus inputs */
		$goPricingAdmin.delegate('#go-pricing-column-wrapper label, #go-pricing-popup-content label', 'click', function(){
			var $this=$(this), labelInnput=$this.parent().next().find('input, select, textarea');
			if (labelInnput.length) {
				if (labelInnput.filter('input[type="checkbox"]').length) {
					labelInnput.filter('input[type="checkbox"]').trigger('click');
				} else {
					labelInnput.focus();
				};
			};
		});

		/* column style change */
		$goPricingAdmin.delegate('select[name*=col-style]', 'change', function(e){	
			var $this=$(this), $currentColumn=$this.closest('.go-pricing-column'), 
				headerType=$this.val().split('_')[1];
			
			if (headerType!=undefined) {
				$currentColumn.find('table[class*=go-pricing-header-type]').css('display','none');
				$currentColumn.find('.go-pricing-header-type-'+headerType).css('display','block');
			};
		});	
		
		/* add image button -> load img & and add src value to hidden input */
		$goPricingAdmin.delegate('.go-pricing-add-image', 'click', function(e){	
			var $this=$(this), 
				$prevImg=$this.closest('table').find('.'+$this.data('img-class')), 
				$prevInput=$prevImg.next('input[type="hidden"]'), 
				$prevImgWrapper=$prevImg.closest('tr');
			
			e.preventDefault();
			/* add image */	
			tb_show('', 'media-upload.php?post_id=0&amp;type=image&amp;TB_iframe=true');
			var tbframe_interval = setInterval(function() {
				if ($('#TB_iframeContent').contents().find('.savesend .button').val()!=goPricingText.insert) {
					$('#TB_iframeContent').contents().find('.savesend .button').val(goPricingText.insert);
				} else {
					clearInterval(tbframe_interval);
				}
			}, 500);
			window.send_to_editor = function(html) {
				var $html=$('<div />', { 'class':'media-html', 'html': html }), 
					classes=$html.find('img').attr('class');
				
				$prevImg[0].src=$html.find('img')[0].src;
				$prevInput[0].value=$html.find('img')[0].src;
				if ($prevImgWrapper.is(':hidden')) { $prevImgWrapper.css('display','table-row'); };
			tb_remove();			
			};
		});
		
		/* add new or clone column */
		$goPricingAdmin.delegate('.go-pricing-add-column, .go-pricing-clone-column', 'click', function(e){
			var goPricingCustomStylesCode = '';

			/* Add custom styles if exist */
			if (typeof goPricingCustomStyles !== "undefined") {
				$.each(goPricingCustomStyles, function(index, value) {
					goPricingCustomStylesCode += '<optgroup label="'+value.name+' '+goPricingText.styles+'"></optgroup>'	
					for(var i = 0; i < value.styles.length; i++) {
						if (value.styles[i].id!=undefined && value.styles[i].name!=undefined && value.styles[i].type!=undefined) {
							goPricingCustomStylesCode +='<option value="'+value.styles[i].id+'">'+value.styles[i].name+' ('+value.styles[i].type+')</option>';
						};
					};
				}); 
			}
			
			var $this=$(this), $newColumn, $newRow, currentWidth,
				colLength=$goPricingAdmin.find('.go-pricing-column').length,
				rowLength=$goPricingAdmin.find('.go-pricing-sortable-row').length,
				rowCount=rowLength/colLength;

			if (colLength==7) { alert(goPricingText.maxCol); return false; };				
			e.preventDefault();
			if ($this.hasClass('go-pricing-add-column')) {
				var $newColumn=$('<li />', { 
					'class':'go-pricing-column', 
					'html':'<div class="postbox"><h3 class="hndle"><div class="go-pricing-handle-icon-general-options">'+goPricingText.generalOptions+'<small>'+goPricingText.setColStyle+'</small></div><span class="go-pricing-closed"></span></h3><div class="inside"><table class="form-table"><tr><th class="w100"><label>'+goPricingText.highlightCol+'</label></th><td><input type="checkbox" name="col-highlight-chk[]" /><input type="hidden" name="col-highlight[]" value="" />&nbsp;'+goPricingText.yes+'</td> </tr><tr><th class="w100"><label>'+goPricingText.disableEnlargeCol+'</label></th><td><input type="checkbox" name="col-disable-enlarge-chk[]"><input type="hidden" name="col-disable-enlarge[]" value="">&nbsp;'+goPricingText.yes+'</td></tr><tr><th class="w100"><label>'+goPricingText.disableHoverCol+'</label></th><td><input type="checkbox" name="col-disable-hover-chk[]"><input type="hidden" name="col-disable-hover[]" value="">&nbsp;'+goPricingText.yes+'</td></tr>      <tr><th class="w100"><label>'+goPricingText.Style+'</label></th><td><select name="col-style[]" class="w255 widefat">'+goPricingCustomStylesCode+'<optgroup label="'+goPricingText.blue+' '+goPricingText.styles+'"></optgroup><option value="blue1_pricing">'+goPricingText.blue+'1 ('+goPricingText.pricingHeader+')</option><option value="blue2_pricing">'+goPricingText.blue+'2 ('+goPricingText.pricingHeader+')</option><option value="blue3a_pricing">'+goPricingText.blue+'3a ('+goPricingText.pricingHeader+')</option><option value="blue3b_pricing">'+goPricingText.blue+'3b ('+goPricingText.pricingHeader+')</option><option value="blue3c_pricing">'+goPricingText.blue+'3c ('+goPricingText.pricingHeader+')</option><option value="blue3d_pricing">'+goPricingText.blue+'3d ('+goPricingText.pricingHeader+')</option><option value="blue4a_pricing">'+goPricingText.blue+'4a ('+goPricingText.pricingHeader+')</option><option value="blue4b_pricing">'+goPricingText.blue+'4b ('+goPricingText.pricingHeader+')</option><option value="blue4c_pricing">'+goPricingText.blue+'4c ('+goPricingText.pricingHeader+')</option><option value="blue4d_pricing">'+goPricingText.blue+'4d ('+goPricingText.pricingHeader+')</option><option value="blue5_pricing">'+goPricingText.blue+'5 ('+goPricingText.pricingHeader+')</option><option value="blue6_pricing">'+goPricingText.blue+'6 ('+goPricingText.pricingHeader+')</option><option value="blue7_html">'+goPricingText.blue+'7 ('+goPricingText.htmlHeader+')</option><option value="blue8_html">'+goPricingText.blue+'8 ('+goPricingText.htmlHeader+')</option><option value="blue9_pricing2">'+goPricingText.blue+'9 ('+goPricingText.pricingHtmlHeader+')</option><option value="blue10_pricing3">'+goPricingText.blue+'10 ('+goPricingText.pricingImgHeader+')</option><option value="blue11a_pricing">'+goPricingText.blue+'11a ('+goPricingText.pricingHeader+')</option><option value="blue11b_pricing">'+goPricingText.blue+'11b ('+goPricingText.pricingHeader+')</option><option value="blue11c_pricing">'+goPricingText.blue+'11c ('+goPricingText.pricingHeader+')</option><option value="blue11d_pricing">'+goPricingText.blue+'11d ('+goPricingText.pricingHeader+')</option><option value="blue12_team">'+goPricingText.blue+'12 ('+goPricingText.teamHeader+')</option><option value="blue13_product">'+goPricingText.blue+'13 ('+goPricingText.productHeader+')</option><option value="blue14_pricing">'+goPricingText.blue+'14 ('+goPricingText.pricingHeader+')</option><option value="blue15_html">'+goPricingText.blue+'15 ('+goPricingText.htmlHeader+')</option><optgroup label="'+goPricingText.green+' '+goPricingText.styles+'"></optgroup><option value="green1_pricing">'+goPricingText.green+'1 ('+goPricingText.pricingHeader+')</option><option value="green2_pricing">'+goPricingText.green+'2 ('+goPricingText.pricingHeader+')</option><option value="green3a_pricing">'+goPricingText.green+'3a ('+goPricingText.pricingHeader+')</option><option value="green3b_pricing">'+goPricingText.green+'3b ('+goPricingText.pricingHeader+')</option><option value="green3c_pricing">'+goPricingText.green+'3c ('+goPricingText.pricingHeader+')</option><option value="green3d_pricing">'+goPricingText.green+'3d ('+goPricingText.pricingHeader+')</option><option value="green4a_pricing">'+goPricingText.green+'4a ('+goPricingText.pricingHeader+')</option><option value="green4b_pricing">'+goPricingText.green+'4b ('+goPricingText.pricingHeader+')</option><option value="green4c_pricing">'+goPricingText.green+'4c ('+goPricingText.pricingHeader+')</option><option value="green4d_pricing">'+goPricingText.green+'4d ('+goPricingText.pricingHeader+')</option><option value="green5_pricing">'+goPricingText.green+'5 ('+goPricingText.pricingHeader+')</option><option value="green6_pricing">'+goPricingText.green+'6 ('+goPricingText.pricingHeader+')</option><option value="green7_html">'+goPricingText.green+'7 ('+goPricingText.htmlHeader+')</option><option value="green8_html">'+goPricingText.green+'8 ('+goPricingText.htmlHeader+')</option><option value="green9_pricing2">'+goPricingText.green+'9 ('+goPricingText.pricingHtmlHeader+')</option><option value="green10_pricing3">'+goPricingText.green+'10 ('+goPricingText.pricingImgHeader+')</option><option value="green11a_pricing">'+goPricingText.green+'11a ('+goPricingText.pricingHeader+')</option><option value="green11b_pricing">'+goPricingText.green+'11b ('+goPricingText.pricingHeader+')</option><option value="green11c_pricing">'+goPricingText.green+'11c ('+goPricingText.pricingHeader+')</option><option value="green11d_pricing">'+goPricingText.green+'11d ('+goPricingText.pricingHeader+')</option><option value="green12_team">'+goPricingText.green+'12 ('+goPricingText.teamHeader+')</option><option value="green13_product">'+goPricingText.green+'13 ('+goPricingText.productHeader+')</option><option value="green14_pricing">'+goPricingText.green+'14 ('+goPricingText.pricingHeader+')</option><option value="green15_html">'+goPricingText.green+'15 ('+goPricingText.htmlHeader+')</option><optgroup label="'+goPricingText.red+' '+goPricingText.styles+'"></optgroup><option value="red1_pricing">'+goPricingText.red+'1 ('+goPricingText.pricingHeader+')</option><option value="red2_pricing">'+goPricingText.red+'2 ('+goPricingText.pricingHeader+')</option><option value="red3a_pricing">'+goPricingText.red+'3a ('+goPricingText.pricingHeader+')</option><option value="red3b_pricing">'+goPricingText.red+'3b ('+goPricingText.pricingHeader+')</option><option value="red3c_pricing">'+goPricingText.red+'3c ('+goPricingText.pricingHeader+')</option><option value="red3d_pricing">'+goPricingText.red+'3d ('+goPricingText.pricingHeader+')</option><option value="red4a_pricing">'+goPricingText.red+'4a ('+goPricingText.pricingHeader+')</option><option value="red4b_pricing">'+goPricingText.red+'4b ('+goPricingText.pricingHeader+')</option><option value="red4c_pricing">'+goPricingText.red+'4c ('+goPricingText.pricingHeader+')</option><option value="red4d_pricing">'+goPricingText.red+'4d ('+goPricingText.pricingHeader+')</option><option value="red5_pricing">'+goPricingText.red+'5 ('+goPricingText.pricingHeader+')</option><option value="red6_pricing">'+goPricingText.red+'6 ('+goPricingText.pricingHeader+')</option><option value="red7_html">'+goPricingText.red+'7 ('+goPricingText.htmlHeader+')</option><option value="red8_html">'+goPricingText.red+'8 ('+goPricingText.htmlHeader+')</option><option value="red9_pricing2">'+goPricingText.red+'9 ('+goPricingText.pricingHtmlHeader+')</option><option value="red10_pricing3">'+goPricingText.red+'10 ('+goPricingText.pricingImgHeader+')</option><option value="red11a_pricing">'+goPricingText.red+'11a ('+goPricingText.pricingHeader+')</option><option value="red11b_pricing">'+goPricingText.red+'11b ('+goPricingText.pricingHeader+')</option><option value="red11c_pricing">'+goPricingText.red+'11c ('+goPricingText.pricingHeader+')</option><option value="red11d_pricing">'+goPricingText.red+'11d ('+goPricingText.pricingHeader+')</option><option value="red12_team">'+goPricingText.red+'12 ('+goPricingText.teamHeader+')</option><option value="red13_product">'+goPricingText.red+'13 ('+goPricingText.productHeader+')</option><option value="red14_pricing">'+goPricingText.red+'14 ('+goPricingText.pricingHeader+')</option><option value="red15_html">'+goPricingText.red+'15 ('+goPricingText.htmlHeader+')</option><optgroup label="'+goPricingText.purple+' '+goPricingText.styles+'"></optgroup><option value="purple1_pricing">'+goPricingText.purple+'1 ('+goPricingText.pricingHeader+')</option><option value="purple2_pricing">'+goPricingText.purple+'2 ('+goPricingText.pricingHeader+')</option><option value="purple3a_pricing">'+goPricingText.purple+'3a ('+goPricingText.pricingHeader+')</option><option value="purple3b_pricing">'+goPricingText.purple+'3b ('+goPricingText.pricingHeader+')</option><option value="purple3c_pricing">'+goPricingText.purple+'3c ('+goPricingText.pricingHeader+')</option><option value="purple3d_pricing">'+goPricingText.purple+'3d ('+goPricingText.pricingHeader+')</option><option value="purple4a_pricing">'+goPricingText.purple+'4a ('+goPricingText.pricingHeader+')</option><option value="purple4b_pricing">'+goPricingText.purple+'4b ('+goPricingText.pricingHeader+')</option><option value="purple4c_pricing">'+goPricingText.purple+'4c ('+goPricingText.pricingHeader+')</option><option value="purple4d_pricing">'+goPricingText.purple+'4d ('+goPricingText.pricingHeader+')</option><option value="purple5_pricing">'+goPricingText.purple+'5 ('+goPricingText.pricingHeader+')</option><option value="purple6_pricing">'+goPricingText.purple+'6 ('+goPricingText.pricingHeader+')</option><option value="purple7_html">'+goPricingText.purple+'7 ('+goPricingText.htmlHeader+')</option><option value="purple8_html">'+goPricingText.purple+'8 ('+goPricingText.htmlHeader+')</option><option value="purple9_pricing2">'+goPricingText.purple+'9 ('+goPricingText.pricingHtmlHeader+')</option><option value="purple10_pricing3">'+goPricingText.purple+'10 ('+goPricingText.pricingImgHeader+')</option><option value="purple11a_pricing">'+goPricingText.purple+'11a ('+goPricingText.pricingHeader+')</option><option value="purple11b_pricing">'+goPricingText.purple+'11b ('+goPricingText.pricingHeader+')</option><option value="purple11c_pricing">'+goPricingText.purple+'11c ('+goPricingText.pricingHeader+')</option><option value="purple11d_pricing">'+goPricingText.purple+'11d ('+goPricingText.pricingHeader+')</option><option value="purple12_team">'+goPricingText.purple+'12 ('+goPricingText.teamHeader+')</option><option value="purple13_product">'+goPricingText.purple+'13 ('+goPricingText.productHeader+')</option><option value="purple14_pricing">'+goPricingText.purple+'14 ('+goPricingText.pricingHeader+')</option><option value="purple15_html">'+goPricingText.purple+'15 ('+goPricingText.htmlHeader+')</option><optgroup label="'+goPricingText.yellow+' '+goPricingText.styles+'"></optgroup><option value="yellow1_pricing">'+goPricingText.yellow+'1 ('+goPricingText.pricingHeader+')</option><option value="yellow2_pricing">'+goPricingText.yellow+'2 ('+goPricingText.pricingHeader+')</option><option value="yellow3a_pricing">'+goPricingText.yellow+'3a ('+goPricingText.pricingHeader+')</option><option value="yellow3b_pricing">'+goPricingText.yellow+'3b ('+goPricingText.pricingHeader+')</option><option value="yellow3c_pricing">'+goPricingText.yellow+'3c ('+goPricingText.pricingHeader+')</option><option value="yellow3d_pricing">'+goPricingText.yellow+'3d ('+goPricingText.pricingHeader+')</option><option value="yellow4a_pricing">'+goPricingText.yellow+'4a ('+goPricingText.pricingHeader+')</option><option value="yellow4b_pricing">'+goPricingText.yellow+'4b ('+goPricingText.pricingHeader+')</option><option value="yellow4c_pricing">'+goPricingText.yellow+'4c ('+goPricingText.pricingHeader+')</option><option value="yellow4d_pricing">'+goPricingText.yellow+'4d ('+goPricingText.pricingHeader+')</option><option value="yellow5_pricing">'+goPricingText.yellow+'5 ('+goPricingText.pricingHeader+')</option><option value="yellow6_pricing">'+goPricingText.yellow+'6 ('+goPricingText.pricingHeader+')</option><option value="yellow7_html">'+goPricingText.yellow+'7 ('+goPricingText.htmlHeader+')</option><option value="yellow8_html">'+goPricingText.yellow+'8 ('+goPricingText.htmlHeader+')</option><option value="yellow9_pricing2">'+goPricingText.yellow+'9 ('+goPricingText.pricingHtmlHeader+')</option><option value="yellow10_pricing3">'+goPricingText.yellow+'10 ('+goPricingText.pricingImgHeader+')</option><option value="yellow11a_pricing">'+goPricingText.yellow+'11a ('+goPricingText.pricingHeader+')</option><option value="yellow11b_pricing">'+goPricingText.yellow+'11b ('+goPricingText.pricingHeader+')</option><option value="yellow11c_pricing">'+goPricingText.yellow+'11c ('+goPricingText.pricingHeader+')</option><option value="yellow11d_pricing">'+goPricingText.yellow+'11d ('+goPricingText.pricingHeader+')</option><option value="yellow12_team">'+goPricingText.yellow+'12 ('+goPricingText.teamHeader+')</option><option value="yellow13_product">'+goPricingText.yellow+'13 ('+goPricingText.productHeader+')</option><option value="yellow14_pricing">'+goPricingText.yellow+'14 ('+goPricingText.pricingHeader+')</option><option value="yellow15_html">'+goPricingText.yellow+'15 ('+goPricingText.htmlHeader+')</option><optgroup label="'+goPricingText.earth+' '+goPricingText.styles+'"></optgroup><option value="earth1_pricing">'+goPricingText.earth+'1 ('+goPricingText.pricingHeader+')</option><option value="earth2_pricing">'+goPricingText.earth+'2 ('+goPricingText.pricingHeader+')</option><option value="earth3a_pricing">'+goPricingText.earth+'3a ('+goPricingText.pricingHeader+')</option><option value="earth3b_pricing">'+goPricingText.earth+'3b ('+goPricingText.pricingHeader+')</option><option value="earth3c_pricing">'+goPricingText.earth+'3c ('+goPricingText.pricingHeader+')</option><option value="earth3d_pricing">'+goPricingText.earth+'3d ('+goPricingText.pricingHeader+')</option><option value="earth4a_pricing">'+goPricingText.earth+'4a ('+goPricingText.pricingHeader+')</option><option value="earth4b_pricing">'+goPricingText.earth+'4b ('+goPricingText.pricingHeader+')</option><option value="earth4c_pricing">'+goPricingText.earth+'4c ('+goPricingText.pricingHeader+')</option><option value="earth4d_pricing">'+goPricingText.earth+'4d ('+goPricingText.pricingHeader+')</option><option value="earth5_pricing">'+goPricingText.earth+'5 ('+goPricingText.pricingHeader+')</option><option value="earth6_pricing">'+goPricingText.earth+'6 ('+goPricingText.pricingHeader+')</option><option value="earth7_html">'+goPricingText.earth+'7 ('+goPricingText.htmlHeader+')</option><option value="earth8_html">'+goPricingText.earth+'8 ('+goPricingText.htmlHeader+')</option><option value="earth9_pricing2">'+goPricingText.earth+'9 ('+goPricingText.pricingHtmlHeader+')</option><option value="earth10_pricing3">'+goPricingText.earth+'10 ('+goPricingText.pricingImgHeader+')</option><option value="earth11a_pricing">'+goPricingText.earth+'11a ('+goPricingText.pricingHeader+')</option><option value="earth11b_pricing">'+goPricingText.earth+'11b ('+goPricingText.pricingHeader+')</option><option value="earth11c_pricing">'+goPricingText.earth+'11c ('+goPricingText.pricingHeader+')</option><option value="earth11d_pricing">'+goPricingText.earth+'11d ('+goPricingText.pricingHeader+')</option><option value="earth12_team">'+goPricingText.earth+'12 ('+goPricingText.teamHeader+')</option><option value="earth13_product">'+goPricingText.earth+'13 ('+goPricingText.productHeader+')</option><option value="earth14_pricing">'+goPricingText.earth+'14 ('+goPricingText.pricingHeader+')</option><option value="earth15_html">'+goPricingText.earth+'15 ('+goPricingText.htmlHeader+')</option></select></td></tr><tr><th class="w100"><label>'+goPricingText.shadowStyle+'</label></th><td><select name="col-shadow[]" class="w255 widefat go-pricing-shadow-selector"><option value="">'+goPricingText.noShadow+'</option><option value="shadow1">'+goPricingText.shadow+' '+goPricingText.style+'1</option><option value="shadow2">'+goPricingText.shadow+' '+goPricingText.style+'2</option><option value="shadow3">'+goPricingText.shadow+' '+goPricingText.style+'3</option><option value="shadow4">'+goPricingText.shadow+' '+goPricingText.style+'4</option><option value="shadow5">'+goPricingText.shadow+' '+goPricingText.style+'5</option></select></td></tr><tr class="go-pricing-img-shadow-wrapper"><th class="w100"></th><td><img src="'+pluginUrl+'admin/images/shadow_6.png" class="go-pricing-img-shadow" /></td></tr><tr><th class="w100"><label>'+goPricingText.ribbon+'</label></th><td><select name="col-ribbon[]" class="w255 widefat go-pricing-ribbon-selector"><option value="">'+goPricingText.noRibbon+'</option><option value="custom">'+goPricingText.customRibbon+'</option><optgroup label="'+goPricingText.blue+' '+goPricingText.ribbons+'"></optgroup><option value="left-blue-50percent">50% ('+goPricingText.leftSide+')</option><option value="right-blue-50percent">50% ('+goPricingText.rightSide+')</option><option value="left-blue-new">New ('+goPricingText.leftSide+')</option><option value="right-blue-new">New ('+goPricingText.rightSide+')</option><option value="left-blue-top">Top ('+goPricingText.leftSide+')</option><option value="right-blue-top">Top ('+goPricingText.rightSide+')</option><option value="left-blue-save">Save ('+goPricingText.leftSide+')</option><option value="right-blue-save">Save ('+goPricingText.rightSide+')</option><optgroup label="'+goPricingText.green+' '+goPricingText.ribbons+'"></optgroup><option value="left-green-50percent">50% ('+goPricingText.leftSide+')</option><option value="right-green-50percent">50% ('+goPricingText.rightSide+')</option><option value="left-green-new">New ('+goPricingText.leftSide+')</option><option value="right-green-new">New ('+goPricingText.rightSide+')</option><option value="left-green-top">Top ('+goPricingText.leftSide+')</option><option value="right-green-top">Top ('+goPricingText.rightSide+')</option><option value="left-green-save">Save ('+goPricingText.leftSide+')</option><option value="right-green-save">Save ('+goPricingText.rightSide+')</option><optgroup label="'+goPricingText.red+' '+goPricingText.ribbons+'"></optgroup><option value="left-red-50percent">50% ('+goPricingText.leftSide+')</option><option value="right-red-50percent">50% ('+goPricingText.rightSide+')</option><option value="left-red-new">New ('+goPricingText.leftSide+')</option><option value="right-red-new">New ('+goPricingText.rightSide+')</option><option value="left-red-top">Top ('+goPricingText.leftSide+')</option><option value="right-red-top">Top ('+goPricingText.rightSide+')</option><option value="left-red-save">Save ('+goPricingText.leftSide+')</option><option value="right-red-save">Save ('+goPricingText.rightSide+')</option><optgroup label="'+goPricingText.yellow+' '+goPricingText.ribbons+'"></optgroup><option value="left-yellow-50percent">50% ('+goPricingText.leftSide+')</option><option value="right-yellow-50percent">50% ('+goPricingText.rightSide+')</option><option value="left-yellow-new">New ('+goPricingText.leftSide+')</option><option value="right-yellow-new">New ('+goPricingText.rightSide+')</option><option value="left-yellow-top">Top ('+goPricingText.leftSide+')</option><option value="right-yellow-top">Top ('+goPricingText.rightSide+')</option><option value="left-yellow-save">Save ('+goPricingText.leftSide+')</option><option value="right-yellow-save">Save ('+goPricingText.rightSide+')</option></select></td></tr><tr class="go-pricing-img-custom-ribbon-wrapper"><th class="w100"><label>'+goPricingText.ribbonAlign+'</label></th><td><select name="col-custom-ribbon-align[]" class="w255 widefat"><option value="left">'+goPricingText.alignLeft+'</option><option value="right">'+goPricingText.alignRight+'</option></select></td></tr><tr class="go-pricing-img-ribbon-wrapper"><th class="w100"></th><td><img src="'+pluginUrl+'admin/images/blank.png" class="go-pricing-img-ribbon" /><input type="hidden" name="col-custom-ribbon[]" value="" /></td></tr><tr class="go-pricing-img-custom-ribbon-wrapper"><th class="w100"></th><td><a href="#" data-img-class="go-pricing-img-ribbon" class="go-pricing-add-image button-secondary" style="margin:0 3px;"><span class="go-pricing-button-icon-add"></span>'+goPricingText.addImg+'</a></td></tr></table></div></div><div class="postbox"><h3 class="hndle"><div class="go-pricing-handle-icon-header-options">'+goPricingText.headerOptions+'<small>'+goPricingText.headerSet+'</small></div><span class="go-pricing-closed"></span></h3><div class="inside"><table class="form-table go-pricing-header-types go-pricing-header-type-pricing go-pricing-header-type-pricing2 go-pricing-header-type-pricing3 go-pricing-header-type-team go-pricing-header-type-product"><tr><th class="w100"><label>'+goPricingText.colTitle+'</label></th><td><input type="text" name="col-title[]" value="" class="w255" /></td></tr></table><table class="form-table go-pricing-header-types go-pricing-header-type-pricing go-pricing-header-type-pricing2 go-pricing-header-type-pricing3 go-pricing-header-type-product"><tr><th class="w100"><label>'+goPricingText.price+'</label></th><td><input type="text" name="col-price[]" value="" class="w255" /></td></tr></table><table class="form-table go-pricing-header-types go-pricing-header-type-pricing2"><tr><th class="w100"></th><td><a href="#" class="go-pricing-add-sc" data-popup-title="'+goPricingText.scEditor+'" data-popup-width="670" data-popup-height="500" data-popup-action="go_pricing_sc_popup_header" data-target-class="go-pricing-popup-target">'+goPricingText.addSc+'</a></td></tr><tr><th class="w100"><label>'+goPricingText.htmlContent+'</label></th><td><textarea name="col-pricing-html[]" class="go-pricing-popup-target w255"></textarea></td></tr></table><table class="form-table go-pricing-header-types go-pricing-header-type-pricing3 go-pricing-header-type-product go-pricing-header-type-team"><tr class="go-pricing-img-wrapper"><th class="w100"><label>'+goPricingText.selectImg+'</label></th><td><img src="" class="go-pricing-img" /><input type="hidden" name="col-pricing-img[]" value="" /></td></tr><tr><th class="w100"></th><td><a href="#" data-img-class="go-pricing-img" class="go-pricing-add-image button-secondary" style="margin:0 3px;"><span class="go-pricing-button-icon-add"></span>'+goPricingText.addImg+'</a></td></tr></table><table class="form-table go-pricing-header-types go-pricing-header-type-pricing2 go-pricing-header-type-pricing3 go-pricing-header-type-team"><tr><th class="w100"><label>'+goPricingText.cssExtension+'</label></th><td><textarea name="col-pricing-css[]" class="w255"></textarea></td></tr></table><table class="form-table go-pricing-header-types go-pricing-header-type-pricing go-pricing-header-type-pricing2 go-pricing-header-type-pricing3 go-pricing-header-type-team go-pricing-header-type-product"><tr><th class="w100"><label>'+goPricingText.replaceDefault+'</label></th><td><input type="checkbox" name="col-replace-chk[]" /><input type="hidden" name="col-replace[]" value="" />&nbsp;'+goPricingText.yes+'</td></tr></table><table class="form-table"><tr><th class="w100"></th><td><a href="#" class="go-pricing-add-sc" data-popup-title="'+goPricingText.scEditor+'" data-popup-width="670" data-popup-height="500" data-popup-action="go_pricing_sc_popup_header" data-target-class="go-pricing-popup-target">'+goPricingText.addSc+'</a></td></tr><tr><th class="w100"><label>'+goPricingText.htmlContent+'</label></th><td><textarea name="col-html[]" class="go-pricing-popup-target w255"></textarea></td></tr><tr><th class="w100"><label>'+goPricingText.cssExtension+'</label></th><td><textarea name="col-css[]" class="w255"></textarea></td></tr></table></div></div><div class="postbox"><h3 class="hndle"><div class="go-pricing-handle-icon-body-options">'+goPricingText.bodyOptions+'<small>'+goPricingText.bodySet+'</small></div><span class="go-pricing-closed"></span></h3><div class="inside"><div class="go-pricing-sortable-rows"></div><table class="form-table"><tr class="go-pricing-add-detail-row"><th class="w100"></th><td><a href="#" class="go-pricing-add-detail button-secondary" style="margin:0 3px;"><span class="go-pricing-button-icon-add"></span>'+goPricingText.addDetail+'</a></td></tr></table></div></div><div class="postbox"><h3 class="hndle"><div class="go-pricing-handle-icon-button-options">'+goPricingText.buttonOptions+'<small>'+goPricingText.buttonSet+'</small></div><span class="go-pricing-closed"></span></h3><div class="inside"><table class="form-table"><tr><th class="w100"><label>'+goPricingText.buttonSize+'</label></th><td><select name="col-button-size[]" class="w255"><option value="small">'+goPricingText.small+'</option><option value="medium">'+goPricingText.medium+'</option><option value="large">'+goPricingText.large+'</option></select></td></tr><tr><th class="w100"><label>'+goPricingText.buttonType+'</label></th><td><select name="col-button-type[]" class="w255"><option value="button">'+goPricingText.regButton+'</option><option value="submit">'+goPricingText.submitButton+'</option><option value="submit">'+goPricingText.customButton+'</option></select></td></tr><tr><th class="w100"></th><td><a href="#" class="go-pricing-add-sc" data-popup-title="'+goPricingText.scEditor+'" data-popup-width="670" data-popup-height="500" data-popup-action="go_pricing_sc_popup_button" data-target-class="go-pricing-popup-target">'+goPricingText.addSc+'</a></td></tr><tr><th class="w100"><label>'+goPricingText.buttonText+'</label></th><td><textarea name="col-button-text[]" value=""class="go-pricing-popup-target w255"></textarea></td></tr><tr><th class="w100"><label>'+goPricingText.buttonLink+'</label></th><td><textarea  name="col-button-link[]" value="" rows="5" class="w255"></textarea></td></tr><tr><th class="w100"><label>'+goPricingText.buttonOpen+'</label></th><td><input type="checkbox" name="col-button-target-chk[]" /><input type="hidden" name="col-button-target[]" value="" />&nbsp;'+goPricingText.yes+'</td></tr><tr><th class="w100"><label>'+goPricingText.buttonNofollow+'</label></th><td><input type="checkbox" name="col-button-nofollow-chk[]" /><input type="hidden" name="col-button-nofollow[]" value="" />&nbsp;'+goPricingText.yes+'</td></tr></table></div></div><div class="inside postbox go-pricing-column-controls"><a href="#" class="go-pricing-remove-column button-secondary"><span class="go-pricing-button-icon-remove"></span>'+goPricingText.del+'</a><a href="#" class="go-pricing-clone-column button-secondary" ><span class="go-pricing-button-icon-clone"></span>'+goPricingText.clone+'</a><a href="#" class="go-pricing-collapse-column button-secondary" data-closed="'+goPricingText.expandAll+'" data-open="'+goPricingText.collapseAll+'"><span class="go-pricing-button-icon-collapse"></span>'+goPricingText.expandAll+'</a></div>'
				});
				$newColumn.insertBefore($goPricingAdmin.find('li.go-pricing-add-column'));
				$newColumn.find('select').not('[class*="go-pricing-sc-selector"]').trigger('change');
				for (var i=0;i<rowCount;i++) { $newColumn.find('.go-pricing-add-detail').trigger('click', true); };
			} else {
				var colIndex=$this.closest('.go-pricing-columns').find('.go-pricing-column').index($this.closest('.go-pricing-column'));
				$goPricingAdmin.find('.go-pricing-columns').each(function(index) {
					var $newCol=$(this).find('.go-pricing-column').eq(colIndex).clone();
					var $inputs=$(this).find('.go-pricing-column').eq(colIndex).find('textarea, select, input');
					$newCol.insertAfter($(this).find('.go-pricing-column').eq(colIndex));
					$newCol.find('textarea, select, input').each(function(index) {
						$(this).val($inputs.eq(index).val());
					});
				});
			};
			
			/* make rows sortable */
			$goPricingAdmin.find('.go-pricing-sortable-rows').sortable({
				axis:'y',
				items:'table.go-pricing-sortable-row', 
				opacity:0.8,
				placeholder:'form-table go-pricing-sortable-row-placeholder'
			});	
			setWidth();
		});
		
		/* show & hide color picker */
		$goPricingAdmin.delegate('#go-pricing-tooltip-bg-color, #go-pricing-tooltip-text-color', 'focusin focusout', function(e){ 
			var $this=$(this);
			if (e.type=='focusin') {
				$this.closest('tr').next().css('display','table-row');
			} else {
				$this.closest('tr').next().css('display','none');
			};
		
		});
		
		/* select shadow & ribbon */
		$goPricingAdmin.delegate('.go-pricing-shadow-selector, .go-pricing-ribbon-selector', 'change', function(e){
			var $this=$(this),
				$ribbonWrapper=$this.closest('tr').nextAll('.go-pricing-img-ribbon-wrapper'),
				$customRibbonWrapper=$this.closest('tr').nextAll('.go-pricing-img-custom-ribbon-wrapper');
			if ($this.hasClass('go-pricing-shadow-selector')) {
				if ($this.val()=='') { $this.closest('tr').next().find('img')[0].src=pluginUrl+'admin/images/shadow_6.png';
				} else if ($this.val()=='shadow1') { $this.closest('tr').next().find('img')[0].src=pluginUrl+'admin/images/shadow_1.png';
				} else if ($this.val()=='shadow2') { $this.closest('tr').next().find('img')[0].src=pluginUrl+'admin/images/shadow_2.png';
				} else if ($this.val()=='shadow3') { $this.closest('tr').next().find('img')[0].src=pluginUrl+'admin/images/shadow_3.png';
				} else if ($this.val()=='shadow4') { $this.closest('tr').next().find('img')[0].src=pluginUrl+'admin/images/shadow_4.png';
				} else if ($this.val()=='shadow5') { $this.closest('tr').next().find('img')[0].src=pluginUrl+'admin/images/shadow_5.png'; }
			} else if ($this.hasClass('go-pricing-ribbon-selector')) {
				if ($this.val()=='') { $ribbonWrapper.css('display','none'); $customRibbonWrapper.css('display','none');
				} else if ($this.val()=='custom') { if ($ribbonWrapper.find('.go-pricing-img-ribbon').next('input[type="hidden"]').val()!='') { $ribbonWrapper.css('display','table-row').find('.go-pricing-img-ribbon')[0].src=$ribbonWrapper.find('.go-pricing-img-ribbon').next('input[type="hidden"]').val(); } else { $ribbonWrapper.css('display','none'); }; $customRibbonWrapper.css('display','table-row'); 
				} else if ($this.val()=='left-blue-50percent') { $customRibbonWrapper.css('display','none'); $ribbonWrapper.css('display','table-row').find('img')[0].src=pluginUrl+'assets/images/ribbons/ribbon_blue_left_50percent.png';
				} else if ($this.val()=='right-blue-50percent') { $customRibbonWrapper.css('display','none'); $ribbonWrapper.css('display','table-row').find('img')[0].src=pluginUrl+'assets/images/ribbons/ribbon_blue_right_50percent.png';
				} else if ($this.val()=='left-blue-new') { $customRibbonWrapper.css('display','none'); $ribbonWrapper.css('display','table-row').find('img')[0].src=pluginUrl+'assets/images/ribbons/ribbon_blue_left_new.png';
				} else if ($this.val()=='right-blue-new') { $customRibbonWrapper.css('display','none'); $ribbonWrapper.css('display','table-row').find('img')[0].src=pluginUrl+'assets/images/ribbons/ribbon_blue_right_new.png';
				} else if ($this.val()=='left-blue-top') { $customRibbonWrapper.css('display','none'); $ribbonWrapper.css('display','table-row').find('img')[0].src=pluginUrl+'assets/images/ribbons/ribbon_blue_left_top.png';
				} else if ($this.val()=='right-blue-top') { $customRibbonWrapper.css('display','none'); $ribbonWrapper.css('display','table-row').find('img')[0].src=pluginUrl+'assets/images/ribbons/ribbon_blue_right_top.png';
				} else if ($this.val()=='left-blue-save') { $customRibbonWrapper.css('display','none'); $ribbonWrapper.css('display','table-row').find('img')[0].src=pluginUrl+'assets/images/ribbons/ribbon_blue_left_save.png';
				} else if ($this.val()=='right-blue-save') { $customRibbonWrapper.css('display','none'); $ribbonWrapper.css('display','table-row').find('img')[0].src=pluginUrl+'assets/images/ribbons/ribbon_blue_right_save.png';
				} else if ($this.val()=='left-green-50percent') { $customRibbonWrapper.css('display','none'); $ribbonWrapper.css('display','table-row').find('img')[0].src=pluginUrl+'assets/images/ribbons/ribbon_green_left_50percent.png';
				} else if ($this.val()=='right-green-50percent') { $customRibbonWrapper.css('display','none'); $ribbonWrapper.css('display','table-row').find('img')[0].src=pluginUrl+'assets/images/ribbons/ribbon_green_right_50percent.png';
				} else if ($this.val()=='left-green-new') { $customRibbonWrapper.css('display','none'); $ribbonWrapper.css('display','table-row').find('img')[0].src=pluginUrl+'assets/images/ribbons/ribbon_green_left_new.png';
				} else if ($this.val()=='right-green-new') { $customRibbonWrapper.css('display','none'); $ribbonWrapper.css('display','table-row').find('img')[0].src=pluginUrl+'assets/images/ribbons/ribbon_green_right_new.png';
				} else if ($this.val()=='left-green-top') { $customRibbonWrapper.css('display','none'); $ribbonWrapper.css('display','table-row').find('img')[0].src=pluginUrl+'assets/images/ribbons/ribbon_green_left_top.png';
				} else if ($this.val()=='right-green-top') { $customRibbonWrapper.css('display','none'); $ribbonWrapper.css('display','table-row').find('img')[0].src=pluginUrl+'assets/images/ribbons/ribbon_green_right_top.png';
				} else if ($this.val()=='left-green-save') { $customRibbonWrapper.css('display','none'); $ribbonWrapper.css('display','table-row').find('img')[0].src=pluginUrl+'assets/images/ribbons/ribbon_green_left_save.png';
				} else if ($this.val()=='right-green-save') { $customRibbonWrapper.css('display','none'); $ribbonWrapper.css('display','table-row').find('img')[0].src=pluginUrl+'assets/images/ribbons/ribbon_green_right_save.png';
				} else if ($this.val()=='left-red-50percent') { $customRibbonWrapper.css('display','none'); $ribbonWrapper.css('display','table-row').find('img')[0].src=pluginUrl+'assets/images/ribbons/ribbon_red_left_50percent.png';
				} else if ($this.val()=='right-red-50percent') { $customRibbonWrapper.css('display','none'); $ribbonWrapper.css('display','table-row').find('img')[0].src=pluginUrl+'assets/images/ribbons/ribbon_red_right_50percent.png';
				} else if ($this.val()=='left-red-new') { $customRibbonWrapper.css('display','none'); $ribbonWrapper.css('display','table-row').find('img')[0].src=pluginUrl+'assets/images/ribbons/ribbon_red_left_new.png';
				} else if ($this.val()=='right-red-new') { $customRibbonWrapper.css('display','none'); $ribbonWrapper.css('display','table-row').find('img')[0].src=pluginUrl+'assets/images/ribbons/ribbon_red_right_new.png';
				} else if ($this.val()=='left-red-top') { $customRibbonWrapper.css('display','none'); $ribbonWrapper.css('display','table-row').find('img')[0].src=pluginUrl+'assets/images/ribbons/ribbon_red_left_top.png';
				} else if ($this.val()=='right-red-top') { $customRibbonWrapper.css('display','none'); $ribbonWrapper.css('display','table-row').find('img')[0].src=pluginUrl+'assets/images/ribbons/ribbon_red_right_top.png';
				} else if ($this.val()=='left-red-save') { $customRibbonWrapper.css('display','none'); $ribbonWrapper.css('display','table-row').find('img')[0].src=pluginUrl+'assets/images/ribbons/ribbon_red_left_save.png';
				} else if ($this.val()=='right-red-save') { $customRibbonWrapper.css('display','none'); $ribbonWrapper.css('display','table-row').find('img')[0].src=pluginUrl+'assets/images/ribbons/ribbon_red_right_save.png';
				} else if ($this.val()=='left-yellow-50percent') { $customRibbonWrapper.css('display','none'); $ribbonWrapper.css('display','table-row').find('img')[0].src=pluginUrl+'assets/images/ribbons/ribbon_yellow_left_50percent.png';
				} else if ($this.val()=='right-yellow-50percent') { $customRibbonWrapper.css('display','none'); $ribbonWrapper.css('display','table-row').find('img')[0].src=pluginUrl+'assets/images/ribbons/ribbon_yellow_right_50percent.png';
				} else if ($this.val()=='left-yellow-new') { $customRibbonWrapper.css('display','none'); $ribbonWrapper.css('display','table-row').find('img')[0].src=pluginUrl+'assets/images/ribbons/ribbon_yellow_left_new.png';
				} else if ($this.val()=='right-yellow-new') { $customRibbonWrapper.css('display','none'); $ribbonWrapper.css('display','table-row').find('img')[0].src=pluginUrl+'assets/images/ribbons/ribbon_yellow_right_new.png';
				} else if ($this.val()=='left-yellow-top') { $customRibbonWrapper.css('display','none'); $ribbonWrapper.css('display','table-row').find('img')[0].src=pluginUrl+'assets/images/ribbons/ribbon_yellow_left_top.png';
				} else if ($this.val()=='right-yellow-top') { $customRibbonWrapper.css('display','none'); $ribbonWrapper.css('display','table-row').find('img')[0].src=pluginUrl+'assets/images/ribbons/ribbon_yellow_right_top.png';
				} else if ($this.val()=='left-yellow-save') { $customRibbonWrapper.css('display','none'); $ribbonWrapper.css('display','table-row').find('img')[0].src=pluginUrl+'assets/images/ribbons/ribbon_yellow_left_save.png';
				} else if ($this.val()=='right-yellow-save') { $customRibbonWrapper.css('display','none'); $ribbonWrapper.css('display','table-row').find('img')[0].src=pluginUrl+'assets/images/ribbons/ribbon_yellow_right_save.png';	}
			};
		});

		/* add row, clone row & remove row (detail) */
		$goPricingAdmin.delegate('.go-pricing-add-detail, .go-pricing-remove-detail, .go-pricing-clone-detail', 'click', function(e, wasTriggered){	
			var $this=$(this);
			e.preventDefault();
			if ($this.hasClass('go-pricing-remove-detail')) {
				var rowIndex=$this.closest('.go-pricing-sortable-rows').find('.go-pricing-sortable-row').index($this.closest('table'));
				$goPricingAdmin.find('.go-pricing-sortable-rows').each(function(index) {
					$(this).find('.go-pricing-sortable-row').eq(rowIndex).remove();
				});
			} else if ($this.hasClass('go-pricing-clone-detail')) {
				var rowIndex=$this.closest('.go-pricing-sortable-rows').find('.go-pricing-sortable-row').index($this.closest('table'));
				$goPricingAdmin.find('.go-pricing-sortable-rows').each(function(index) {
					var $newRow=$(this).find('.go-pricing-sortable-row').eq(rowIndex).clone();
					var $inputs=$(this).find('.go-pricing-sortable-row').eq(rowIndex).find('textarea, select, input');
					$newRow.insertAfter($(this).find('.go-pricing-sortable-row').eq(rowIndex));
					$newRow.find('textarea, select, input').each(function(index) {
						$(this).val($inputs.eq(index).val());
					});
				});
			} else {
				var $newRow=$('<table />', {	'class' : 'form-table go-pricing-sortable-row', 
												'html'  : '<tr><th class="w100"><input type="hidden" name="col-align[]" class="go-pricing-col-align" value="" /><a href="#" class="go-pricing-align-icon-left" data-id="left"></a><a href="#" class="go-pricing-align-icon-center go-pricing-current" data-id=""></a><a href="#" class="go-pricing-align-icon-right" data-id="right"></a></th><td><a href="#" class="go-pricing-add-sc" data-popup-title="'+goPricingText.scEditor+'" data-popup-width="670" data-popup-height="500" data-popup-action="go_pricing_sc_popup_body" data-target-class="go-pricing-popup-target">'+goPricingText.addSc+'</a></td><tr><th class="w60"><label>'+goPricingText.description+'</label></th><td><textarea name="col-detail[]" value="" class="go-pricing-popup-target w255"></textarea></td><tr></tr><th class="w60"><label>'+goPricingText.tooltip+'</label></th><td><textarea name="col-detail-tip[]" class="w255"></textarea></td></tr><tr><th class="w60"></th><td colspan="2" style="padding-bottom:22px !important;"><a href="#" class="go-pricing-remove-detail button-secondary" style="margin:0 3px;"><span class="go-pricing-button-icon-remove"></span>'+goPricingText.del+' '+goPricingText.row+'</a><a href="#" class="go-pricing-clone-detail button-secondary" style="margin:0 3px;"><span class="go-pricing-button-icon-clone"></span>'+goPricingText.clone+' '+goPricingText.row+'</a></td></tr>' 
				});
				if (wasTriggered) {
					$this.closest('.inside').find('.go-pricing-sortable-rows').append($newRow);
				} else {
					$goPricingAdmin.find('.go-pricing-sortable-rows').append($newRow);
				}
				
			};
		});

		/* collapse/expand options */
		$goPricingAdmin.delegate('.go-pricing-collapse-column', 'click', function(e){	
			var $this=$(this), 
				$panels=$this.closest('li').find('.postbox > .inside'),
				$hiddenPanels=$panels.filter(':hidden');
			e.preventDefault();
			
			if ($hiddenPanels.length) { 
				$hiddenPanels.closest('.postbox').find('h3.hndle').trigger('click'); 
			} else {
				$panels.closest('.postbox').find('h3.hndle').trigger('click');
			};
		});	

		/* remove columns */
		$goPricingAdmin.delegate('.go-pricing-remove-column', 'click', function(e){	
			var $this=$(this);
			e.preventDefault();
			$this.closest('li').remove();
			setWidth();
		});

		/* action button events */
		$goPricingAdmin.delegate('.go-pricing-save, .go-pricing-edit, .go-pricing-delete, .go-pricing-copy, .go-pricing-cancel, .go-pricing-prev', 'click', function(e){
			var $this=$(this), $form=$this.closest('form');

			e.preventDefault();
			if ($this.hasClass('go-pricing-edit')) {
				$form.find('#action-type').val('select');
			} else if ($this.hasClass('go-pricing-delete')) {
				$form.find('#action-type').val('delete');	
			} else if ($this.hasClass('go-pricing-copy')) {
				$form.find('#action-type').val('copy');	
			} else if ($this.hasClass('go-pricing-cancel')) {
				$form.find('#action-type').remove();
			}
			$form.data('id',$this[0].className.split(' ')[1]);
			$form.submit();
		});
			
		/* form ajax submit */
		$goPricingAdmin.find('#go-pricing-form').submit(function(){
			var $this=$(this);
			$.ajax({  
				type: 'post',
				url: ajaxurl, 
				data: jQuery.param({ action: 'go_pricing_ajax_submit' })+'&'+$this.serialize(),
				beforeSend: function () {
					$goPricingAdmin.find('.ajax-loading').css('visibility','visible');
				}
			}).always(function() {
				$goPricingAdmin.find('.ajax-loading').css('visibility','hidden');
			}).fail(function(jqXHR, textStatus) {
				$goTablesForm.before('<div id="result" class="error"><p><strong>'+$this.data('ajaxerrormsg')+'</p></div>').delay(3000).slideUp(function(){ $(this).remove(); });	
			}).done(function(data) {
				var $ajaxResponse=$('<div />', { 'class':'ajax-response', 'html' : data }),
					$ajaxResult=$ajaxResponse.find('#result').delay(3000).slideUp(function(){ $(this).remove(); });
					
				$goPricingAdmin.find('#result').length ? $goPricingAdmin.find('#result').stop(true,true).slideDown(0).replaceWith($ajaxResult) : $goTablesForm.before($ajaxResult); 
				if ($goTablesForm.find('#go-pricing-select').length || !$goTablesForm.find('#action-type').length ) { $goTablesForm.html($ajaxResponse.find('#go-pricing-form').html()); };
				setWidth();
				setThumbContainerHeight();
				setThumbContainerWidth();

				$('.go-pricing-colorpicker').each(function() {
					var $this=$(this);
					$this.farbtastic(function(color) { $this.closest('tr').prev().find('input').val(color).css({'background-color':color}); });
				});
				
				$('#go-pricing-admin-wrap select').not('[class*="go-pricing-sc-selector"]').trigger('change');
				
				
				/* make rows sortable */
				$goPricingAdmin.find('.go-pricing-sortable-rows').sortable({
					axis: 'y',
					items: 'table.go-pricing-sortable-row', 
					opacity: 0.8,
					placeholder: 'form-table go-pricing-sortable-row-placeholder'
				});

				/* make cols sortable */
				$goPricingAdmin.find('.go-pricing-columns').sortable({
					axis: 'x',
					items: 'li.go-pricing-column', 
					opacity:0.8,
					placeholder: 'go-pricing-column-placeholder',
					start: function( event, ui ) { 
						ui.item.next().css('height',ui.item.height()+'px'); 
						ui.item.find('input, textarea').trigger('blur');
					}
				});
			});
			return false;		
		});
		
	/* ---------------------------------------------------------------------- /
		[4] POPUP FUNCTIONS & EVENTS 
	/ ---------------------------------------------------------------------- */

		/* get cursor positions */
		var getCurPos = function($obj) {
			var input = $obj[0];
			var pos = {start: 0, end:0};
			if (input.setSelectionRange) {
				pos.start=input.selectionStart;
				pos.end=input.selectionEnd;
			} else if (input.createTextRange) { 
				input.focus();
				var c = "\001",
					sel=document.selection.createRange(),
					dul=sel.duplicate(),
					len=0;
					
				dul.moveToElementText(input);
				sel.text = c;
				len	= (dul.text.indexOf(c));
				sel.moveStart('character',-1);
				sel.text = "";
				pos.start = len;
				pos.end = len + sel.text.length;
			}
			return pos;
		};
		
		var setCurPos = function($obj, pos) {
			var input = $obj[0];
			if (input.setSelectionRange) {
				input.setSelectionRange(pos.start, pos.end);
			} else if (input.createTextRange) {
				var selection = input.createTextRange();
				selection.collapse(true);
				selection.moveEnd('character', pos.end);
				selection.moveStart('character', pos.start);
				selection.select();
			}
		};		

		/* positioning & resize popup */
		var popupPos = function () {
			var $goTablesPopup=$goPricingAdmin.find('#go-pricing-popup'),
				$window=$(window),
				minW=350,
				minH=200,
				popupW=$goTablesPopup.width(),
				popupH=$goTablesPopup.height(),
				winW=$window.width(),
				winH=$window.height();

			$goTablesPopup.css({
					'width': popupW=(winW>minW*1.25 ? (winW<$goTablesPopup.data('width')*1.25?winW*0.8:$goTablesPopup.data('width')) : minW),
					'height': popupH=(winH>(minH+30)*1.25 ? (winH<$goTablesPopup.data('height')*1.25?winH*0.8-30:$goTablesPopup.data('height')) : minH),
					'left': function() { return parseFloat($(this).css('left').split('px')[0])<20 ? 20 : winW*0.5-popupW*0.5 },
					'top': function() { return parseFloat($(this).css('top').split('px')[0])<50 ? 50 : winH*0.5-popupH*0.5 }
				});
		};				
		
		/* hide popup */
		var hidePopup = function () {
			$goPricingAdmin.find('#go-pricing-popup').remove().end().find('#go-pricing-overlay').remove();
			$('body').removeClass('no-tb-overlay');	
		};
		
		/* show popup */
		var showPopup = function (data) {
			if (data.popupAction!=undefined) { 
				var $goTablesOverlay=$('<div />', { 'id':'go-pricing-overlay' }),
					$goTablesPopup=$('<div />', { 'id':'go-pricing-popup', 'data-width':(data.popupWidth!=undefined ? data.popupWidth : ''), 'data-height':(data.popupHeight!=undefined ? data.popupHeight : '') ,'html':'<div id="go-pricing-popup-title">'+ (data.popupTitle!=undefined ? data.popupTitle : '') +'<span></span><div id="go-pricing-popup-loader" class="preloader"></div></div><div id="go-pricing-popup-content" class="postbox"></div>' });
	
				$goPricingAdmin.append($goTablesOverlay).append($goTablesPopup)
				$goTablesPopup.css({ 'width':data.popupWidth!=undefined ? data.popupWidth : false , 'height':data.popupHeight!=undefined ? data.popupHeight : false });
				popupPos();
				loadAjaxContent(data.popupAction, data.popupActionType!=undefined ? data.popupActionType : 'get', data.popupTarget!=undefined ? data.popupTarget : null );
			};			
		};
		
		/* load content via ajax */
		var loadAjaxContent = function (ajaxAction, ajaxType, ajaxTarget) {
			var $goTablesPopup=$goPricingAdmin.find('#go-pricing-popup'),
				$goTablesPopupLoader=$goPricingAdmin.find('#go-pricing-popup-loader'),
				dataParams=ajaxAction.split('&').splice(1,1);
			
			$.ajax({  
				type: ajaxType, 
				url: ajaxurl,
				data: jQuery.param({ action: ajaxAction.split('&')[0] })+ (dataParams.length ? '&'+ dataParams.join('&') : ''),
				beforeSend: function () {
					if (!$goTablesPopupLoader.hasClass('animated')) {
						$goTablesPopupLoader.gwPngAnim();
					};
					$goTablesPopupLoader.fadeIn();
				}
			}).always(function() {
					$goTablesPopupLoader.fadeOut();
			}).fail(function(jqXHR, textStatus) {
					$goTablesPopup.html('<div id="result" class="error"><p><strong>'+goPricingText.ajaxError+'</p></div>');
			}).done(function(data) {
				ajaxTarget = !ajaxTarget ? 'go-pricing-popup-content' : ajaxTarget;
				$('#'+ajaxTarget).html(data);
					if ($('#'+ajaxTarget).find('select.go-pricing-popup-sc-selector').length) {
						$('#'+ajaxTarget).find('.go-pricing-popup-sc, .go-pricing-popup-submit').hide();
						$('#'+ajaxTarget).find('.go-pricing-popup-sc:first-child').show();
					};				
			});		
		};						

		/* click event -> open popup */
		$goPricingAdmin.delegate('a.go-pricing-add-sc', 'click', function(e) {
			var $this=$(this);
			e.preventDefault();
			$this.closest('tr').next().find('textarea, input').addClass('go-pricing-popup-target-current');
			showPopup($this.data());
		});
		/* save cur post of popup target input or textarea */
		$goPricingAdmin.delegate('.go-pricing-popup-target', 'blur', function(e) {
			var pos=getCurPos($(this))
			$(this).data('cur-start',pos.start).data('cur-end',pos.end);
		});

		/* popup events */
		
		/* close popup - click to [x] */
		$goPricingAdmin.delegate('#go-pricing-popup-title span', 'click', function(e) { 
			hidePopup();
		});

		/* close popup - click to overlay */
		$goPricingAdmin.delegate('#go-pricing-overlay', 'click', function(e) {
			hidePopup();
		});
		
		/* close popup - ESC button event */
		$(document).keyup(function(e){
			if (e.keyCode  == 27|| e.which == 27) {
				if ($goPricingAdmin.find('#go-pricing-overlay').length && $('#TB_overlay').length==0) {
					hidePopup();					
				} else if ($('#TB_overlay').length) {
					/* empty */	
				};
			};
		});			

		/* if tb class attached to link */
		$goPricingAdmin.delegate('#go-pricing-popup .thickbox', 'click', function(e) {
			$('body').addClass('no-tb-overlay');
		});

		/* popup sc selector event */
		$goPricingAdmin.delegate('.go-pricing-popup-sc-selector', 'change', function(e) {
			var $this=$(this);
			$goPricingAdmin.find('.go-pricing-popup-sc').hide();
			if ($this.val()!='') {$goPricingAdmin.find('.go-pricing-popup-sc.'+$this.val()).show(); };
			if ($this.val()!='') { 
				$goPricingAdmin.find('.go-pricing-popup-submit').show();
			} else {
				$goPricingAdmin.find('.go-pricing-popup-submit').hide();
			};
		});

		/* popup add file button event */
		$goPricingAdmin.delegate('.go-pricing-add-file-input, .go-pricing-add-img-input', 'click', function(e) {
			var $this=$(this);
			
			e.preventDefault();
			if ($this.hasClass('go-pricing-popup-tb')) { $('body').addClass('no-tb-overlay'); };
			tb_show('', 'media-upload.php?post_id=0&amp;type=image&amp;TB_iframe=true');
			var tbframe_interval = setInterval(function() {
				if ($('#TB_iframeContent').contents().find('.savesend .button').val()!=goPricingText.insert) {
					$('#TB_iframeContent').contents().find('.savesend .button').val(goPricingText.insert);
				} else {
					clearInterval(tbframe_interval);
				}
			}, 500);
			window.send_to_editor = function(html) {
				$html=$('<div />', { 'class':'media-html', 'html': html });
				if ($this.hasClass('go-pricing-add-img-input')) {
					var goImg = $html.find('img');
					$this.closest('td').prev().find('input').val(goImg[0].src);
					$this.closest('tr').next().find('input[name="width"]')[0].value=goImg.attr('width');
					$this.closest('tr').next().next().find('input[name="height"]')[0].value=goImg.attr('height');
					$this.closest('tr').next().next().next().find('input[name="class"]')[0].value=goImg[0].className;
				} else {
					
					$this.closest('td').prev().find('input').val($html.find('a')[0].href).focus();
				}
				tb_remove();	
			};
		});
		
		/* build shortcode */
		$goPricingAdmin.delegate('.go-pricing-popup-insert-sc', 'click', function(e) {
			var $this=$(this), $targetInput=$('.go-pricing-popup-target-current'), shortcode='', scObj={}, scType, cpos={};
			
			e.preventDefault();
			scObj.sc=$goPricingAdmin.find('.go-pricing-popup-sc-selector').val();
			var Inputs=$goPricingAdmin.find('.go-pricing-popup-sc.'+scObj.sc+' input, .go-pricing-popup-sc.'+scObj.sc+' textarea, .go-pricing-popup-sc.'+scObj.sc+' select[name="zoom"]');
			if (Inputs.length) {
				Inputs.each(function(index) {
					var $obj=$(this);
					if ($obj[0].name=='custom-icon') { 
						if ($obj[0].value!='') { $obj[0].value='background-image:url('+$obj[0].value+');'; };
						$obj[0].name="style";
					};
					if ($obj[0].name!=undefined && $obj[0].value!='' && (($obj[0].type=='checkbox' && $obj[0].checked===true) || $obj[0].type!='checkbox')) {
						scObj[$obj[0].name]=$obj[0].value;		
					};
					if ($obj.data('attr')!=undefined) {
						var scAttrs=$obj.data('attr').split('&');
						for (var i=0;i<scAttrs.length;i++) { 
							var scAttr=scAttrs[i].split('=');
							if (scAttr.length>1) { scObj[scAttr[0]]=scAttr[0]; };
						};
					};						
				});
			};
			scType=scObj.sc;
			delete scObj.sc;
			$.each(scObj, function(key, value) { shortcode+=' '+key+'="'+value+'"'; });
			if (scType=='go_pricing_image') { 
				shortcode='<img' +shortcode+'>';
			} else if (scType=='go_pricing_span') { 
				shortcode='<span' +shortcode+'></span>';
			} else {
				shortcode='['+scType+shortcode+']';
			};
			var oldContent=$targetInput.val();
			if ($targetInput.data('cur-start')!=undefined && $targetInput.data('cur-end')!=undefined) {
				var newContent=oldContent.split('');
				newContent.splice($targetInput.data('cur-start'),$targetInput.data('cur-end')-$targetInput.data('cur-start'),shortcode);
				$targetInput.val(newContent.join(''));
				cpos.start=$targetInput.data('cur-start')+shortcode.length;
				cpos.end=$targetInput.data('cur-start')+shortcode.length;
				setCurPos($targetInput, cpos);
				
			} else {
				$targetInput.val(oldContent+shortcode);	
			}
			hidePopup();
			$targetInput.removeClass('go-pricing-popup-target-current');		
		});
		
		/* map sc pin click event */
		$goPricingAdmin.delegate('.go-pricing-pin', 'click', function(e) {
			var $this=$(this);
			$this.closest('tr').next().find('input').val($this[0].src);
			$goPricingAdmin.find('.go-pricing-pin').removeClass('go-pricing-pin-current');
			$this.addClass('go-pricing-pin-current');
		});
		
		/* sc icon click event */
		$goPricingAdmin.delegate('.go-pricing-icon', 'click', function(e) {
			var $this=$(this),
				$input=$this.closest('p').nextAll('input'),
				$input2=$this.closest('tr').next().find('input'),
				defVal=$input.val(),
				classes=defVal.split(' ');

			$goPricingAdmin.find('.go-pricing-icon').removeClass('go-pricing-icon-current');
			$this.addClass('go-pricing-icon-current');			
			classes[0]=$this.data('attr')
			$input2.val('');
			$input.val(classes.join(' '));
		});
		
		/* sc icon align select event */
		$goPricingAdmin.delegate('.go-pricing-icon-align', 'change', function(e) {
			var $this=$(this),
				$input=$this.closest('tr').prev().prev().find('input'),
				defVal=$input.val(),
				classes=defVal.split(' ');

			if (classes.length>1 && classes[0]=='') { 
				classes.splice(0,1);
				classes[0]=$this.val();
			} else {
				classes[1]=$this.val();
			};
			$input.val(classes.join(' '));
		});
		
		/* sc icon size select event */
		$goPricingAdmin.delegate('.go-pricing-icon-size', 'change', function(e) {
			var $this=$(this),
				$input=$this.closest('tr').prev().prev().prev().find('input'),
				defVal=$input.val(),
				classes=defVal.split(' '),
				newClasses=[];

			classes.push($this.val());
			for (var i=0;i<classes.length;i++) { 
				if (classes[i]!='') {
					newClasses.push(classes[i]);
				}; 
			};
			$input.val(newClasses.join(' '));
		});		

		/* sc custom icon event */
		$goPricingAdmin.delegate('.go_pricing_span input[name="custom-icon"]', 'focus', function(e) {
			var $this=$(this),
				$input=$this.closest('tr').prev().find('input'),
				$input2=$this.closest('tr').next().find('select'),
				defVal=$input.val(),
				classes=defVal.split(' ');

			classes[0]='gw-go-icon';
			$input.val(classes.join(' '));
			$goPricingAdmin.find('.go-pricing-icon-current').removeClass('go-pricing-icon-current');
		});

		/* sc custom pin event */
		$goPricingAdmin.delegate('.go_pricing_map input[name="icon"]', 'focus', function(e) {
			$goPricingAdmin.find('.go-pricing-pin-current').removeClass('go-pricing-pin-current');
		});					
		
});	