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
	
	[1] INIT
	[2] COMMON		
	[3] GOOGLE MAP
	[4] EQUALIZE ROWS
	
/ -------------------------------------------------------------------------------- */

(function ($, undefined) {
	"use strict";
	
	$(function () {	
	
	/* ---------------------------------------------------------------------- /
		[1] INIT
	/ ---------------------------------------------------------------------- */	
		
		$.GW_GoPricing = {

			/* Init function */
			Init : function () {
				this.$wrap = $('.gw-go');
				this.equalize = this.$wrap.data('equalize');
				this.InitMediaElementPlayer();
				this.InitGoogleMap();
				this.isIE = document.documentMode != undefined && document.documentMode >5 ? document.documentMode : false;
				if (this.isIE) {
					this.$wrap.addClass('gw-go-ie8');
				};
				if ($.GW_GoPricing!=undefined && $.GW_GoPricing.equalize==true) {
					this.EqualizeRows();
				};
				this.eventType = this.detectEvent();
			},
			
			/* Mediaelement Player init */
			InitMediaElementPlayer : function () {
				if (jQuery().mediaelementplayer && $.GW_GoPricing.$wrap.find('audio, video').length) {	
					$.GW_GoPricing.$wrap.find('audio, video').mediaelementplayer({
						audioWidth: '100%',
						videoWidth: '100%'
					});			
				};									
			},
			
			/* Google map init */
			InitGoogleMap : function () {
				if (jQuery().goMap && $.GW_GoPricing.$wrap.find('.gw-go-gmap').length) {
					$.GW_GoPricing.$wrap.find('.gw-go-gmap').each(function(index) {
						var $this=$(this);
						$this.goMap($this.data('map'));
					});
				};
			},
			
			/* Equalize rows */
			EqualizeRows : function () {
				$.GW_GoPricing.$wrap.each(function(index, element) {
					var $this = $(this),
						$GW_GoPricing_Cells = $this.find('.gw-go-body li .gw-go-body-cell');
	
					for (var row=0;row<$this.data('rownum');row++) {
						var GW_GoPricing_Heights = [], 
							$GW_GoPricing_TempItems = [];
						
						for (var x=0;x<$this.data('colnum');x++) {
							$GW_GoPricing_Cells.eq(x*$this.data('rownum')+row).css('height','auto')
							GW_GoPricing_Heights.push(parseInt($GW_GoPricing_Cells.eq(x*$this.data('rownum')+row).height()));
							$GW_GoPricing_TempItems[x] = $GW_GoPricing_Cells.eq(x*$this.data('rownum')+row);
							if (x==$this.data('colnum')-1) {
								for (var y in $GW_GoPricing_TempItems) {
									$($GW_GoPricing_TempItems[y]).css('height','auto').css('height',Math.max.apply(Math,GW_GoPricing_Heights)+'px');
								};
							};
						};
					};									
				});
			},

			/* Detect event type */
			detectEvent : function() {
				var eventType = 'mouseenter mouseleave';
				if ('ontouchstart' in window) {
					eventType = 'touchstart';
				} else if  (window.navigator.pointerEnabled && navigator.msMaxTouchPoints) {
					eventType = "pointerdown";
				} else if (window.navigator.msPointerEnabled && navigator.msMaxTouchPoints) {
					eventType = "MSPointerDown";
				} 
				return eventType;
			}
		
		};
		
		/* JS init */
		$.GW_GoPricing.Init();			
		
		/* Submit button event if form found */
		$.GW_GoPricing.$wrap.delegate('span.gw-go-btn', 'click', function(){	
			var $this=$(this);
			if ($this.find('form').length) { $this.find('form').submit(); };
		});	

		/* Event handling */
		$('body').on($.GW_GoPricing.eventType, '.gw-go-col-wrap', function(e) {
			var $this = $(this);
			if (e.type == 'mouseenter' && !$this.hasClass('gw-go-disable-hover')) {
				$this.addClass('gw-go-hover').siblings(':not(.gw-go-disable-hover)').removeClass('gw-go-hover');
				$this.closest('.gw-go').addClass('gw-go-hover');
			} else if (e.type == 'mouseleave' && !$this.hasClass('gw-go-disable-hover')) {
				$this.removeClass('gw-go-hover');
				$this.closest('.gw-go').removeClass('gw-go-hover')
			} else if (!$this.hasClass('gw-go-disable-hover')) {
				$this.closest('.gw-go').addClass('gw-go-hover')
				$this.addClass('gw-go-hover').siblings(':not(.gw-go-disable-hover)').removeClass('gw-go-hover');
			};
			
		});
		
		if ($.GW_GoPricing.eventType != 'mouseenter mouseleave') {
			$(document).on($.GW_GoPricing.eventType, this, function(e) {
				$('.gw-go').removeClass('gw-go-hover');
				if ($(e.target).closest('.gw-go').length) {
					$(e.target).closest('.gw-go').addClass('gw-go-hover')	
				} 
				$.GW_GoPricing.$wrap.not('.gw-go-hover').find('.gw-go-col-wrap:not(.gw-go-disable-hover)').removeClass('gw-go-hover');	
			});
		};		
	
	/* ---------------------------------------------------------------------- /
		[3] GOOGLE MAP
	/ ---------------------------------------------------------------------- */	
	
		if (jQuery().goMap && $.GW_GoPricing.$wrap.find('.gw-go-gmap').length) {
			var GW_GoPricing_MapResize=false;
			$(window).on('resize', function(e) {
				if (GW_GoPricing_MapResize) { clearTimeout(GW_GoPricing_MapResize); }
				GW_GoPricing_MapResize = setTimeout(function() {
					$.GW_GoPricing.$wrap.find('.gw-go-gmap').each(function(index, element) {
					  $(this).goMap();
					  $.goMap.map.panTo($.goMap.getMarkers('markers')[0].getPosition());
					});
				}, 400);
			});			
		};
		
	/* ---------------------------------------------------------------------- /
		[4] EQUALIZE ROWS
	/ ---------------------------------------------------------------------- */	
	

		if ($.GW_GoPricing!=undefined && $.GW_GoPricing.equalize==true) {
			var GW_GoPricing_TableResize=false;
			
			$(window).on('resize', function(e) {
				if (GW_GoPricing_TableResize) { clearTimeout(GW_GoPricing_TableResize); }
				GW_GoPricing_TableResize = setTimeout(function() {
					$.GW_GoPricing.EqualizeRows();
				}, 210);
			});	
		};

	});
}(jQuery));	