(function($) {
    // creates the plugin
    tinymce.create('tinymce.plugins.displayProduct', {
       init : function(ed, url) {
            ed.addButton('displayProduct_button', {
                title : 'Display Product Shortcode',
                image : displayProduct.plugin_folder + 'assets/js/display-icon.png',
                onclick: function() {
                    // triggers the thickbox
                    var width = jQuery(window).width(), H = jQuery(window).height(), W = (720 < width) ? 720 : width;
                    W = 980;
                    H = H - 115;
                   tb_show('Display Product Shortcode', displayProduct.ajax_url + '?action=dpshortcodegenerator&width=' + W + '&height=' + H);
                }
            });
        },
        createControl: function(n, cm) {
            return null;
        },
        getInfo : function() {
            return {
                longname : "Display Product Shortcode",
                author : 'Sureshopress',
                authorurl : 'http://www.sureshopress.com/',
                infourl : 'http://www.sureshopress.com/',
                version : "1.8.3"
            };
        }
    });
    tinymce.PluginManager.add('displayProduct', tinymce.plugins.displayProduct);
    // executes this when the DOM is ready
    
})(window.jQuery);
