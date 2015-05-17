jQuery(function ($) {
	jQuery('#recipients').textboxlist({unique: true, bitsOptions: {editable: {addKeys: [188]}}});
});

jQuery('document').ready(function($){
    
    if($('#status_colour').length > 0){
      $("#status_colour").wpColorPicker();
      var icm_icons = {
            'Web Applications' : [57436, 57437, 57438, 57439, 57524, 57525, 57526, 57527, 57528, 57531, 57532, 57533, 57534, 57535, 57536, 57537, 57541, 57545, 57691, 57692],
            'Business Icons' : [57347, 57348, 57375, 57376, 57377, 57379, 57403, 57406, 57432, 57433, 57434, 57435, 57450, 57453, 57456, 57458, 57460, 57461, 57463],
            'eCommerce' : [57392, 57397, 57398, 57399, 57402],
            'Currency Icons' : [],
            'Form Control Icons' : [57383, 57384, 57385, 57386, 57387, 57388, 57484, 57594, 57595, 57600, 57603, 57604, 57659, 57660, 57693],
            'User Action & Text Editor' : [57442, 57443, 57444, 57445, 57446, 57447, 57472, 57473, 57474, 57475, 57476, 57477, 57539, 57662, 57668, 57669, 57670, 57671, 57674, 57675, 57688, 57689],
            'Charts and Codes' : [57493],
            'Attentive' : [57543, 57588, 57590, 57591, 57592, 57593, 57596],
            'Multimedia Icons' : [57356, 57357, 57362, 57363, 57448, 57485, 57547, 57548, 57549, 57605, 57606, 57609, 57610, 57611, 57614, 57617, 57618, 57620, 57621, 57622, 57623, 57624, 57625, 57626],
            'Location and Contact' : [57344, 57345, 57346, 57404, 57405, 57408, 57410, 57411, 57413, 57414, 57540],
            'Date and Time' : [57415, 57416, 57417, 57421, 57422, 57423],
            'Devices' : [57359, 57361, 57364, 57425, 57426, 57430],
            'Tools' : [57349, 57350, 57352, 57355, 57365, 57478, 57479, 57480, 57481, 57482, 57483, 57486, 57487, 57488, 57663, 57664],
            'Social and Networking' : [57694, 57700, 57701, 57702, 57703, 57704, 57705, 57706, 57707, 57709, 57710, 57711, 57717, 57718, 57719, 57736, 57737, 57738, 57739, 57740, 57741, 57742, 57746, 57747, 57748, 57755, 57756, 57758, 57759, 57760, 57761, 57763, 57764, 57765, 57766, 57767, 57776],
            'Brands' : [57743, 57750, 57751, 57752, 57753, 57754, 57757, 57773, 57774, 57775, 57789, 57790, 57792, 57793],
            'Files & Documents' : [57378, 57380, 57381, 57382, 57390, 57391, 57778, 57779, 57780, 57781, 57782, 57783, 57784, 57785, 57786, 57787],
            'Like & Dislike Icons' : [57542, 57544, 57550, 57551, 57552, 57553, 57554, 57555, 57556, 57557],
            'Emoticons' : [57558, 57559, 57560, 57561, 57562, 57563, 57564, 57565, 57566, 57567, 57568, 57569, 57570, 57571, 57572, 57573, 57574, 57575, 57576, 57577, 57578, 57579, 57580, 57581, 57582, 57583],
            'Directional Icons' : [57584, 57585, 57586, 57587, 57631, 57632, 57633, 57634, 57635, 57636, 57637, 57638, 57639, 57640, 57641, 57642, 57643, 57644, 57645, 57646, 57647, 57648, 57649, 57650, 57651, 57652, 57653, 57654],
            'Other Icons' : [57351, 57353, 57354, 57358, 57360, 57366, 57367, 57368, 57369, 57370, 57371, 57372, 57373, 57374, 57389, 57393, 57394, 57395, 57396, 57400, 57401, 57407, 57409, 57412, 57418, 57419, 57420, 57424, 57427, 57428, 57429, 57431, 57440, 57441, 57449, 57451, 57452, 57454, 57455, 57457, 57459, 57462, 57464, 57465, 57466, 57467, 57468, 57469, 57470, 57471, 57489, 57490, 57491, 57492, 57494, 57495, 57496, 57497, 57498, 57499, 57500, 57501, 57502, 57503, 57504, 57505, 57506, 57507, 57508, 57509, 57510, 57511, 57512, 57513, 57514, 57515, 57516, 57517, 57518, 57519, 57520, 57521, 57522, 57523, 57529, 57530, 57538, 57546, 57589, 57597, 57598, 57599, 57601, 57602, 57607, 57608, 57612, 57613, 57615, 57616, 57619, 57627, 57628, 57629, 57630, 57655, 57656, 57657, 57658, 57661, 57665, 57666, 57667, 57672, 57673, 57676, 57677, 57678, 57679, 57680, 57681, 57682, 57683, 57684, 57685, 57686, 57687, 57690, 57695, 57696, 57697, 57698, 57699, 57708, 57712, 57713, 57714, 57715, 57716, 57720, 57721, 57722, 57723, 57724, 57725, 57726, 57727, 57728, 57729, 57730, 57731, 57732, 57733, 57734, 57735, 57744, 57745, 57749, 57762, 57768, 57769, 57770, 57771, 57772, 57777, 57788, 57791, 57794]
        };
         
        var icm_icon_search = {
            'Web Applications' : ['Box add', 'Box remove', 'Download', 'Upload', 'List', 'List 2', 'Numbered list', 'Menu', 'Menu 2', 'Cloud download', 'Cloud upload', 'Download 2', 'Upload 2', 'Download 3', 'Upload 3', 'Globe', 'Attachment', 'Bookmark', 'Embed', 'Code'],
            'Business Icons' : ['Office', 'Newspaper', 'Book', 'Books', 'Library', 'Profile', 'Support', 'Address book', 'Cabinet', 'Drawer', 'Drawer 2', 'Drawer 3', 'Bubble', 'Bubble 2', 'User', 'User 2', 'User 3', 'User 4', 'Busy'],
            'eCommerce' : ['Tag', 'Cart', 'Cart 2', 'Cart 3', 'Calculate'],
            'Currency Icons' : [],
            'Form Control Icons' : ['Copy', 'Copy 2', 'Copy 3', 'Paste', 'Paste 2', 'Paste 3', 'Settings', 'Cancel circle', 'Checkmark circle', 'Spell check', 'Enter', 'Exit', 'Radio checked', 'Radio unchecked', 'Console'],
            'User Action & Text Editor' : ['Undo', 'Redo', 'Flip', 'Flip 2', 'Undo 2', 'Redo 2', 'Zoomin', 'Zoomout', 'Expand', 'Contract', 'Expand 2', 'Contract 2', 'Link', 'Scissors', 'Bold', 'Underline', 'Italic', 'Strikethrough', 'Table', 'Table 2', 'Indent increase', 'Indent decrease'],
            'Charts and Codes' : ['Pie'],
            'Attentive' : ['Eye blocked', 'Warning', 'Question', 'Info', 'Info 2', 'Blocked', 'Spam'],
            'Multimedia Icons' : ['Image', 'Image 2', 'Play', 'Film', 'Forward', 'Equalizer', 'Brightness medium', 'Brightness contrast', 'Contrast', 'Play 2', 'Pause', 'Forward 2', 'Play 3', 'Pause 2', 'Forward 3', 'Previous', 'Next', 'Volume high', 'Volume medium', 'Volume low', 'Volume mute', 'Volume mute 2', 'Volume increase', 'Volume decrease'],
            'Location and Contact' : ['Home', 'Home 2', 'Home 3', 'Phone', 'Phone hang up', 'Envelope', 'Location', 'Location 2', 'Map', 'Map 2', 'Flag'],
            'Date and Time' : ['History', 'Clock', 'Clock 2', 'Stopwatch', 'Calendar', 'Calendar 2'],
            'Devices' : ['Camera', 'Headphones', 'Camera 2', 'Keyboard', 'Screen', 'Tablet'],
            'Tools' : ['Pencil', 'Pencil 2', 'Pen', 'Paint format', 'Dice', 'Key', 'Key 2', 'Lock', 'Lock 2', 'Unlocked', 'Wrench', 'Cog', 'Cogs', 'Cog 2', 'Filter', 'Filter 2'],
            'Social and Networking' : ['Share', 'Googleplus', 'Googleplus 2', 'Googleplus 3', 'Googleplus 4', 'Google drive', 'Facebook', 'Facebook 2', 'Facebook 3', 'Twitter', 'Twitter 2', 'Twitter 3', 'Vimeo', 'Vimeo 2', 'Vimeo 3', 'Github', 'Github 2', 'Github 3', 'Github 4', 'Github 5', 'Wordpress', 'Wordpress 2', 'Tumblr', 'Tumblr 2', 'Yahoo', 'Soundcloud', 'Soundcloud 2', 'Reddit', 'Linkedin', 'Lastfm', 'Lastfm 2', 'Stumbleupon', 'Stumbleupon 2', 'Stackoverflow', 'Pinterest', 'Pinterest 2', 'Yelp'],
            'Brands' : ['Joomla', 'Apple', 'Finder', 'Android', 'Windows', 'Windows 8', 'Skype', 'Paypal', 'Paypal 2', 'Paypal 3', 'Chrome', 'Firefox', 'Opera', 'Safari'],
            'Files & Documents' : ['File', 'File 2', 'File 3', 'File 4', 'Folder', 'Folder open', 'File pdf', 'File openoffice', 'File word', 'File excel', 'File zip', 'File powerpoint', 'File xml', 'File css', 'Html 5', 'Html 52'],
            'Like & Dislike Icons' : ['Eye', 'Eye 2', 'Star', 'Star 2', 'Star 3', 'Heart', 'Heart 2', 'Heart broken', 'Thumbs up', 'Thumbs up 2'],
            'Emoticons' : ['Happy', 'Happy 2', 'Smiley', 'Smiley 2', 'Tongue', 'Tongue 2', 'Sad', 'Sad 2', 'Wink', 'Wink 2', 'Grin', 'Grin 2', 'Cool', 'Cool 2', 'Angry', 'Angry 2', 'Evil', 'Evil 2', 'Shocked', 'Shocked 2', 'Confused', 'Confused 2', 'Neutral', 'Neutral 2', 'Wondering', 'Wondering 2'],
            'Directional Icons' : ['Point up', 'Point right', 'Point down', 'Point left', 'Arrow up left', 'Arrow up', 'Arrow up right', 'Arrow right', 'Arrow down right', 'Arrow down', 'Arrow down left', 'Arrow left', 'Arrow up left 2', 'Arrow up 2', 'Arrow up right 2', 'Arrow right 2', 'Arrow down right 2', 'Arrow down 2', 'Arrow down left 2', 'Arrow left 2', 'Arrow up left 3', 'Arrow up 3', 'Arrow up right 3', 'Arrow right 3', 'Arrow down right 3', 'Arrow down 3', 'Arrow down left 3', 'Arrow left 3'],
            'Other Icons' : ['Quill', 'Blog', 'Droplet', 'Images', 'Music', 'Pacman', 'Spades', 'Clubs', 'Diamonds', 'Pawn', 'Bullhorn', 'Connection', 'Podcast', 'Feed', 'Stack', 'Tags', 'Barcode', 'Qrcode', 'Ticket', 'Coin', 'Credit', 'Notebook', 'Pushpin', 'Compass', 'Alarm', 'Alarm 2', 'Bell', 'Print', 'Laptop', 'Mobile', 'Mobile 2', 'Tv', 'Disk', 'Storage', 'Reply', 'Bubbles', 'Bubbles 2', 'Bubbles 3', 'Bubbles 4', 'Users', 'Users 2', 'Quotes left', 'Spinner', 'Spinner 2', 'Spinner 3', 'Spinner 4', 'Spinner 5', 'Spinner 6', 'Binoculars', 'Search', 'Hammer', 'Wand', 'Aid', 'Bug', 'Stats', 'Bars', 'Bars 2', 'Gift', 'Trophy', 'Glass', 'Mug', 'Food', 'Leaf', 'Rocket', 'Meter', 'Meter 2', 'Dashboard', 'Hammer 2', 'Fire', 'Lab', 'Magnet', 'Remove', 'Remove 2', 'Briefcase', 'Airplane', 'Truck', 'Road', 'Accessibility', 'Target', 'Shield', 'Lightning', 'Switch', 'Powercord', 'Signup', 'Tree', 'Cloud', 'Earth', 'Bookmarks', 'Notification', 'Close', 'Checkmark', 'Checkmark 2', 'Minus', 'Plus', 'Stop', 'Backward', 'Stop 2', 'Backward 2', 'First', 'Last', 'Eject', 'Loop', 'Loop 2', 'Loop 3', 'Shuffle', 'Tab', 'Checkbox checked', 'Checkbox unchecked', 'Checkbox partial', 'Crop', 'Font', 'Text height', 'Text width', 'Omega', 'Sigma', 'Insert template', 'Pilcrow', 'Lefttoright', 'Righttoleft', 'Paragraph left', 'Paragraph center', 'Paragraph right', 'Paragraph justify', 'Paragraph left 2', 'Paragraph center 2', 'Paragraph right 2', 'Paragraph justify 2', 'Newtab', 'Mail', 'Mail 2', 'Mail 3', 'Mail 4', 'Google', 'Instagram', 'Feed 2', 'Feed 3', 'Feed 4', 'Youtube', 'Youtube 2', 'Lanyrd', 'Flickr', 'Flickr 2', 'Flickr 3', 'Flickr 4', 'Picassa', 'Picassa 2', 'Dribbble', 'Dribbble 2', 'Dribbble 3', 'Forrst', 'Forrst 2', 'Deviantart', 'Deviantart 2', 'Steam', 'Steam 2', 'Blogger', 'Blogger 2', 'Tux', 'Delicious', 'Xing', 'Xing 2', 'Flattr', 'Foursquare', 'Foursquare 2', 'Libreoffice', 'Css 3', 'IE', 'IcoMoon']
        };
        var $picker = $('#wc_crm_customer_statuses #status_icon').fontIconPicker({
            source: icm_icons,
            searchSource: icm_icon_search,
            useAttribute: true,
            theme: 'fip-darkgrey',
            attributeName: 'data-icomoon',
            emptyIconValue: 'none'
        });
        $('#wc_crm_customer_statuses').submit(function() {
            $('.form-invalid').removeClass('form-invalid');
            var err = 0;
            $('input#status_name, input#status_icon, input#status_colour').each(function(index, el) {
                if($(el).val() == ''){
                    $(el).closest('.form-field').addClass('form-invalid');
                    err++;
                }
            });
            if(err){
                return false;
            }
        });
    }
    
    if($('#wcrm_import_customers').length > 0){
      $('#wcrm_import_customers').submit(function(event) {
        $('.form-invalid').removeClass('form-invalid');
        if( $('input[name="wcrm_import_customers"]').val() == '' ){
            $('input[name="wcrm_import_customers"]').closest('tr').addClass('form-invalid');
            return false;
        }else{
            $('.spiner_wrap').show();
        }
      });
    }

    if( $('.chosen_select').length > 0 ){
        $('select.chosen_select').css('min-width', '400px').chosen();
    }
    if($('#woocommerce-customer-orders').length > 0 || $('#wc_crm_customers_form table.orders').length > 0){
        $('body').on( 'click', '.show_order_items', function() {
            $(this).closest('td').find('table').toggle();
            return false;
        });
    }
    
    if($('#wp-emaileditor-wrap').length > 0){
        $('#wc_crm_customers_form').submit(function(){
            if($('#subject').val() == ''){
                if(!confirm('The subject field is empty. Are you sure you want to send?')){
                    return false;
                }
            }
        });
    }
    
    $('select#group_customer_status, select#group_product_categories, select#group_order_status').css('width', '95%').chosen();

    $('#woocommerce_crm_mailchimp').change(function(){
        $('#woocommerce_crm_mailchimp_api_key, #woocommerce_crm_mailchimp_list').closest('tr').hide();

        if ( $(this).attr('checked') ) {
            $('#woocommerce_crm_mailchimp_api_key, #woocommerce_crm_mailchimp_list').closest('tr').show();
        }
    }).change();

    if($('#customer_data #_billing_country').length > 0){
        $('a.edit_address').click(function () {
            $( this ).hide();
            $( this ).closest( '.order_data_column' ).find( 'div.address' ).hide();
            $( this ).closest( '.order_data_column' ).find( 'div.edit_address' ).show();
            return false;
        });
        $('#customer_data').on('change', '#_billing_country, #_shipping_country', function(){

            var country = $(this).val();
            var state   = $('#_billing_state').val();
            var id      = $(this).attr('id').replace('_countries', '');

            var data =  {
                action   : 'woocommerce_crm_loading_states',
                id       : id,
                security : wc_crm_customer_params.wc_crm_loading_states,
                country  : country,
                state    : state,
            };

            xhr = $.ajax({
              type:   'POST',
              url:    wc_crm_customer_params.ajax_url,
              data:   data,
              beforeSend: function(xhr) {
                    $('#customer_data').block({message: null, overlayCSS: {background: '#fff url(' + wc_crm_customer_params.ajax_loader_url + ') no-repeat center', backgroundSize: '16px 16px', opacity: 0.6}});
                },
                complete: function(xhr) {
                    if($(id == '_billing_country' && 'select#_billing_state').length > 0){
                        $('select#_billing_state').chosen();
                    }                

                    if($(id == '_shipping_country' && 'select#_shipping_state').length > 0){
                        $('select#_shipping_state').chosen(); 
                    }                

                    $('#customer_data').unblock()
                },
                success: function(response) {
                    var j_data = JSON.parse(response);
                    var html = $($.parseHTML($.trim(j_data.state_html)));
                    if(id == '_billing_country'){
                        $('#_billing_state').remove();
                        if($('#_billing_state_chosen').length > 0){
                            $('#_billing_state_chosen').remove();
                        }
                        $('label[for="_billing_state"]').after($(html));
                        $('label[for="_billing_state"]').html(j_data.state_label);
                        $('label[for="_billing_postcode"]').html(j_data.zip_label);
                        $('label[for="_billing_city"]').html(j_data.city_label);
                    }
                    if(id == '_shipping_country'){
                        $('#_shipping_state').remove();
                        if($('#_shipping_state_chosen').length > 0){
                            $('#_shipping_state_chosen').remove();
                        }
                        $('label[for="_shipping_state"]').after($(html));
                        $('label[for="_shipping_state"]').html(j_data.state_label);
                        $('label[for="_shipping_postcode"]').html(j_data.zip_label);
                        $('label[for="_shipping_city"]').html(j_data.city_label);
                    }
                }
            });
        });
    }

    if($('#customer_data #_shipping_country').length > 0){
        $('#customer_data').on('click', '#copy-billing-same-as-shipping', function(){
            var answer = confirm(wc_crm_customer_params.copy_billing);
            if (answer){
                $('#order_data_column_billing div.edit_address input, #order_data_column_billing div.edit_address select').each(function(){
                    var b_id  = $(this).attr('id');
                    if(typeof b_id !== typeof undefined && b_id !== false){
                        var b_val = $(this).val();
                        var id    = b_id.replace('_billing', '');
                        var s_id  = '_shipping'+id;
                        if($('#'+s_id).length > 0){
                            $('#'+s_id).val(b_val);
                        }
                    }                    
                });
                $('#order_data_column_shipping div.edit_address select').trigger( 'change' ).trigger( 'chosen:updated' );
            }
        });
    }


    if($('#related_to').length > 0){
        $('#related_to').change(function(){
            $('.related_by').hide();
            if($(this).val() == 'order') $('#related_by_order').show();
            if($(this).val() == 'product') $('#related_by_product').show();
        });
    }
    if($(".display_time").length > 0){
        var callTimer = new (function() {

        // Stopwatch element on the page
        var $stopwatch;

        // Timer speed in milliseconds
        var incrementTime = 60;

        // Current timer position in milliseconds
        var currentTime = 0;

        // Start the timer
        $(function() {
            $stopwatch = $('.display_time');
            callTimer.Timer = $.timer(updateTimer, incrementTime, false);
        });

        // Output time and increment
        function updateTimer() {
            formatTimeDuration(currentTime);
            var timeString = formatTime(currentTime);
            $stopwatch.html(timeString);
            currentTime += incrementTime;
        }

        // Reset timer
        this.resetStopwatch = function() {
            currentTime = 0;
            var timeString = formatTime(currentTime);
            $stopwatch.html(timeString);
            callTimer.Timer.stop();
            $('#stop_timer, #pause_timer, #reset_timer').hide().removeClass('play');
            $('.completed_call_wrap').hide();
            $('#start_timer').show();
        };

    });
        $('#start_timer').click(function(){
            callTimer.Timer.play();
            setCurrentTime();
            $('#stop_timer, #pause_timer, #reset_timer').show();
            $('#start_timer').hide();
            return false;
        });
        $('#stop_timer').click(function(){
            callTimer.Timer.stop();
            $('.completed_call_wrap').show();
            $('#pause_timer').removeClass('play').hide();
            return false;
        });
        $('#pause_timer').click(function(){
            $(this).toggleClass('play');
            callTimer.Timer.toggle();
            return false;
        });
        $('#reset_timer').click(function(){
            callTimer.resetStopwatch();
            return false;
        });

        $('#related_to').change(function(){
            var related_to = $('#related_to').val();
            $('#view_info').attr('href', '?page=wc-customer-relationship-manager&'+related_to+'_list='+related_to+'&order_id='+$('#order_id').val());
        });



        var prettyDate = wc_crm_params.curent_time;
        $("#call_date").val(prettyDate);
        $( "#call_date" ).datepicker({
            dateFormat: "yy-mm-dd",
            numberOfMonths: 1,
            showButtonPanel: true,
            maxDate: prettyDate,
            changeMonth: true,
            changeYear: true

        });


        $('#new_call').click(function(){
            if( $('#user_phone').val() == '' ){
                $( '.error_message', $('#user_phone').parent() ).text('Please enter user phone!').show();
                return false;
            }else if( !checkPhone($('#user_phone').val()) ){
                $( '.error_message', $('#user_phone').parent() ).text('Please enter valid phone number!').show();
                return false;
            }
            else{
                $( '.error_message', $('#user_phone').parent() ).hide();
            }
        });
        $('#wc_crm_customers_form').submit(function(){
            $('.error.below-h2').hide();
            $('.form-invalid').removeClass('form-invalid');
            var err = '';
            if( $('#subject_of_call').val() == '' ){
                var error_text = $( '.error_message', $('#subject_of_call').parent() ).html();
                err += '<p>'+error_text+'</p>';
                $('#subject_of_call').parents('tr').addClass('form-invalid');
            }
            if( $('#call_date').val() == '' && $('#call_date').is(':visible') ){
                var error_text = $( '.error_message', $('#call_date').parent() ).html();
                err += '<p>'+error_text+'</p>';
                $('#call_date').parents('tr').addClass('form-invalid');
            }
            var order_num = $('#number_order_product').val();
            order_num = order_num.replace('#', '') ;

            if( $('#related_to').val() == 'order' && order_num == '' ){
                var error_text = '<strong>ERROR</strong>: Please enter Order Number.';
                err += '<p>'+error_text+'</p>';
                $('#related_to').parents('tr').addClass('form-invalid');
            }
            if( $('#related_to').val() == 'product' && order_num == '' ){
                var error_text = '<strong>ERROR</strong>: Please enter Product Number.';
                err += '<p>'+error_text+'</p>';
                $('#related_to').parents('tr').addClass('form-invalid');
            }
            order_num = order_num.replace(/[0-9]/g, '') ;
            if( order_num != ''){
                var error_text = '<strong>ERROR</strong>: Please enter valid Number.';
                err += '<p>'+error_text+'</p>';
                $('#related_to').parents('tr').addClass('form-invalid');
            }
            if( $('#call_time_h').is(':visible') ){
                var h = $('#call_time_h').val();
                var m = $('#call_time_m').val();
                var s = $('#call_time_s').val();
                if(h=='' || m == '' || s==''){
                    var error_text = $( '.error_message', $('#call_time_h').parent() ).html();
                    err += '<p>'+error_text+'</p>';
                    $('#call_time_h').parents('tr').addClass('form-invalid');
                }
                else if( h.replace(/[0-9]/g, '')!='' || m.replace(/[0-9]/g, '')!='' || s.replace(/[0-9]/g, '')!='' || h>23 || m>59 || s>59){
                    var error_text = $( '.error_message', $('#call_time_h').parent() ).html();
                    err += '<p>'+error_text+'</p>';
                    $('#call_time_h').parents('tr').addClass('form-invalid');
                }
            }
            if( $('#call_duration_h').is(':visible') ){
                var d_h = $('#call_duration_h').val();
                var d_m = $('#call_duration_m').val();
                var d_s = $('#call_duration_s').val();
                if(d_h=='' || d_m == '' || d_s==''){
                    var error_text = $( '.error_message', $('#call_duration_h').parent() ).html();
                    err += '<p>'+error_text+'</p>';
                    $('#call_duration_h').parents('tr').addClass('form-invalid');
                }
                else if( d_h.replace(/[0-9]/g, '')!='' || d_m.replace(/[0-9]/g, '')!='' || d_s.replace(/[0-9]/g, '')!='' || d_h>23 || d_m>59 || d_s>59 ){
                    var error_text = $( '.error_message', $('#call_duration_h').parent() ).html();
                    err += '<p>'+error_text+'</p>';
                    $('#call_duration_h').parents('tr').addClass('form-invalid');
                }else if( d_h == 0 && d_m == 0 && d_s == 0 ){
                    var error_text = $( '.error_message', $('#call_duration_h').parent() ).html();
                    err += '<p>'+error_text+'</p>';
                    $('#call_duration_h').parents('tr').addClass('form-invalid');
                }
            }

            if(err != ''){
                $('.error.below-h2').html(err).show();
                return false;
            }
        });

        $('.call_details input').change(function(){
            var val = $(this).val();
            currentTime = 0;
            if(callTimer.Timer != undefined ) {
                callTimer.Timer.stop();

            }
            var timeString = formatTime(currentTime);
            $('.display_time').html(timeString);
            $('#stop_timer, #pause_timer, #reset_timer').hide().removeClass('play');
            $('#start_timer').show();
            if(val == 'completed_call'){
                $('.completed_call_wrap').removeClass('disabled').show();
                $('#current_call_wrap').hide();
                $('#call_time_h, #call_time_m, #call_time_s, #call_duration_h, #call_duration_m, #call_duration_s').val('');
            }else{
                $('.completed_call_wrap').addClass('disabled').hide();
                $('#current_call_wrap').show();
            }
        });
        $('#current_call').click();
    }
    if( $('#group_last_order_to').length > 0 ){
        $( "#group_last_order_to, #group_last_order_from" ).datepicker({
            dateFormat: "yy-mm-dd",
            numberOfMonths: 1,
            showButtonPanel: true,
            changeMonth: true,
            changeYear: true

        });
    }
    if( $('#customer_data #excerpt').length > 0 ){
        $('#customer_data #excerpt').closest('p').remove();
    }
    if( $('#wc_crm_edit_customer_form').length > 0 ){
        $('#wc_crm_edit_customer_form').submit(function(){
            var user_id = $('input#customer_user').val();
            var order_id = $('input#order_id').val();
            var action = $('#wc_crm_customer_action').val();
            if(user_id != '' && user_id != undefined){
                if(action == 'wc_crm_customer_action_new_order'){
                    var url = 'post-new.php?post_type=shop_order&user_id='+user_id;
                    window.open(url,'_self');
                    return false;
                }else if(action == 'wc_crm_customer_action_send_email'){
                    var url = 'admin.php?page=wc-customer-relationship-manager&action=email&user_id='+user_id;
                    window.open(url,'_blank');
                    return false;
                }else if(action == 'wc_crm_customer_action_phone_call'){
                    var url = 'admin.php?page=wc-customer-relationship-manager&action=phone_call&user_id='+user_id;
                    window.open(url,'_blank');
                    return false;
                }
            }else if(order_id != '' && order_id != undefined){
                if(action == 'wc_crm_customer_action_new_order'){
                    var url = 'post-new.php?post_type=shop_order&last_order_id='+order_id;
                    window.open(url,'_self');
                    return false;
                }else if(action == 'wc_crm_customer_action_send_email'){
                    var url = 'admin.php?page=wc-customer-relationship-manager&action=email&order_id='+order_id;
                    window.open(url,'_blank');
                    return false;
                }else if(action == 'wc_crm_customer_action_phone_call'){
                    var url = 'admin.php?page=wc-customer-relationship-manager&action=phone_call&order_id='+order_id;
                    window.open(url,'_blank');
                    return false;
                }
            }
        });
    }
    if( $('#date_of_birth').length > 0 ){
        $('#date_of_birth').datepicker({
            dateFormat: "yy-mm-dd",
            numberOfMonths: 1,
            showButtonPanel: true,
            changeMonth: true,
            changeYear: true,
            yearRange: '-100y:c+nn',
            maxDate: '-1d'
        });

        $('.handlediv').click(function(){
            $(this).parent().toggleClass('closed');
        });
   }
   if( $('#f_group_type').length > 0){
        $('#f_group_type').change(function(){
            if( $(this).val() == 'dynamic'){
                $('.dynamic_group_type').show();
            }else{
                $('.dynamic_group_type').hide();
            }
        }).change();
   }
   if( $('#group_last_order').length > 0){
        $('#group_last_order').change(function(){
            if( $(this).val() == 'between'){
                $('.group_last_order_between').show();
            }else{
                $('.group_last_order_between').hide();
            }
        }).change();
   }
   if( $('#woocommerce-customer-notes').length > 0 ){
        // Customer notes
        $('#woocommerce-customer-notes').on( 'click', 'a.add_note_customer', function() {
            if ( ! $('textarea#add_order_note').val() ) return;

            $('#woocommerce-customer-notes').block({ message: null, overlayCSS: { background: '#fff url(' + wc_crm_customer_params.plugin_url + '/assets/images/ajax-loader.gif) no-repeat center', opacity: 0.6 } });
            var data = {
                action:         'woocommerce_crm_add_customer_note',
                user_id:        $('#customer_user').val(),
                note:           $('textarea#add_order_note').val()
            };

            $.post( wc_crm_customer_params.ajax_url, data, function(response) {
                $('ul.order_notes').prepend( response );
                $('#woocommerce-customer-notes').unblock();
                $('#add_order_note').val('');
            });

            return false;

        });
        $('#woocommerce-customer-notes').on( 'click', 'a.delete_customer_note', function() {
            var note = $(this).closest('li');
            $(note).block({ message: null, overlayCSS: { background: '#fff url(' + wc_crm_customer_params.plugin_url + '/assets/images/ajax-loader.gif) no-repeat center', opacity: 0.6 } });

            var data = {
                action:         'woocommerce_crm_delete_customer_note',
                note_id:        $(note).attr('rel'),
            };

            $.post( wc_crm_customer_params.ajax_url, data, function(response) {
                $(note).remove();
            });

            return false;
        });
    }

    jQuery('.fancybox').fancybox({
        'width'         : '75%',
        'height'        : '75%',
        'autoScale'     : false,
        'transitionIn'  : 'none',
        'transitionOut' : 'none',
        'type'          : 'iframe'
    });

    jQuery(".tips").tipTip({
        'attribute' : 'data-tip',
        'fadeIn' : 50,
        'fadeOut' : 50,
        'delay' : 200
    });


});
// Common functions
function pad(number, length) {
    var str = '' + number;
    while (str.length < length) {str = '0' + str;}
    return str;
}
function formatTime(time) {
    time = time / 10;
    var h   = parseInt(time / 360000),
        min = parseInt(time / 6000) - (h * 60),
        sec = parseInt(time / 100) - (h*60*60+min*60);
        hundredths = pad(time - (sec * 100) - (min * 6000), 2);
    return (h > 0 ? pad(h, 2) : "00") + ":" + ((min > 0 && min < 60) ? pad(min, 2) : "00") + ":" + pad(sec, 2) + ':' + hundredths;
}
function formatTimeDuration(time) {
     time = time / 10;
   var h   = parseInt(time / 360000),
        min = parseInt(time / 6000) - (h * 60),
        sec = parseInt(time / 100) - (h*60*60+min*60);
    document.getElementById("call_duration_h").value = h;
    document.getElementById("call_duration_m").value = min;
    document.getElementById("call_duration_s").value = sec;
}
function setCurrentTime() {
    document.getElementById("call_time_h").value = wc_crm_params.curent_time_h;
    document.getElementById("call_time_m").value = wc_crm_params.curent_time_m;
    document.getElementById("call_time_s").value = wc_crm_params.curent_time_s;
}
function isInt(n) {
   return typeof n === 'number' && n % 1 == 0;
}
function checkPhone(e){
    var number_count = 0;
    for(i=0; i < e.length; i++)
        if((e.charAt(i)>='0') && (e.charAt(i) <=9))
            number_count++;

    if (number_count == 10)
        return true;

    return false;
}

jQuery(document).ready(function($){
    if($('#customer_address_map_canvas').length > 0){
        $('#customer_address_map_canvas').gmap({
            zoom : 14,
            'zoomControl': true,
            'mapTypeControl' : false, 
            'navigationControl' : false,
            'streetViewControl' : false 
        }).bind('init', function() {
                $('#customer_address_map_canvas').gmap('search', { 'address': wc_pos_customer_formatted_billing_address }, function(results, status) {
                        if ( status === 'OK' ) {
                            $('#customer_address_map_canvas').gmap('get', 'map').panTo(results[0].geometry.location); 
                            
                            $('#customer_address_map_canvas').gmap(
                                'addMarker',{'position': results[0].geometry.location, 'bounds': false });
                        }
                        //google = undefined;
                });
        });
    }
});
jQuery(document).on('acf/setup_fields', function(e, el){
    google = undefined;
});