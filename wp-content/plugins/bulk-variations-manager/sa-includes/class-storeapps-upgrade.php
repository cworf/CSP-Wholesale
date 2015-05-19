<?php
class Store_Apps_Upgrade {

    var $base_name;
    var $check_update_timeout;
    var $last_checked;
    var $plugin_data;
    var $sku;
    var $license_key;
    var $download_url;
    var $installed_version;
    var $live_version;
    var $changelog;
    var $slug;
    var $name;
    var $documentation_link;
    var $prefix;
    var $text_domain;
    var $login_link;
    var $due_date;
    
    function __construct( $file, $sku, $prefix, $plugin_name, $text_domain, $documentation_link ) {
        
        $this->check_update_timeout = (24 * 60 * 60); // 24 hours
            
        if (! function_exists( 'get_plugin_data' )) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        $this->plugin_data = get_plugin_data( $file );
        $this->base_name = plugin_basename( $file );
        $this->slug = dirname( $this->base_name );
        $this->name = $plugin_name;
        $this->sku = $sku;
        $this->documentation_link = $documentation_link;
        $this->prefix = $prefix;
        $this->text_domain = $text_domain;
        
        add_site_option( $this->prefix.'_last_checked', '' );
        add_site_option( $this->prefix.'_license_key', '' );
        add_site_option( $this->prefix.'_download_url', '' );
        add_site_option( $this->prefix.'_installed_version', '' );
        add_site_option( $this->prefix.'_live_version', '' );
        add_site_option( $this->prefix.'_changelog', '' );
        add_site_option( $this->prefix.'_login_link', '' );
        add_site_option( $this->prefix.'_due_date', '' );

        if ( empty( $this->last_checked ) ) {
            $this->last_checked = (int)get_site_option( $this->prefix.'_last_checked' );
        }

        if (get_site_option( $this->prefix.'_installed_version' ) != $this->plugin_data ['Version']) {
            update_site_option( $this->prefix.'_installed_version', $this->plugin_data ['Version'] );
        }

        if ( ( get_site_option( $this->prefix.'_live_version' ) == '' ) || ( get_site_option( $this->prefix.'_live_version' ) < get_site_option( $this->prefix.'_installed_version' ) ) ) {
            update_site_option( $this->prefix.'_live_version', $this->plugin_data['Version'] );
        }

        if ( empty( $this->license_key ) ) {
            $this->license_key = get_site_option( $this->prefix.'_license_key' );
        }

        if ( empty( $this->changelog ) ) {
            $this->changelog = get_site_option( $this->prefix.'_changelog' );
        }

        if ( empty( $this->login_link ) ) {
            $this->login_link = get_site_option( $this->prefix.'_login_link' );
        }

        if ( empty( $this->due_date ) ) {
            $this->due_date = get_site_option( $this->prefix.'_due_date' );
        }

        // Actions for License Validation & Upgrade process
        add_action( 'admin_footer', array ($this, 'add_support_ticket_content' ) );
        add_action( "after_plugin_row_".$this->base_name, array ($this, 'update_row' ), 10, 2 );
        add_action( 'wp_ajax_'.$this->prefix.'_validate_license_key', array ($this, 'validate_license_key' ) );
        add_action( 'wp_ajax_'.$this->prefix.'_force_check_for_updates', array ($this, 'force_check_for_updates' ) );
        add_action( 'install_plugins_pre_plugin-information', array ($this, 'overwrite_plugin_information' ) );

        // Filters for pushing WooCommerce Serial Key plugin details in Plugins API of WP
        add_filter( 'plugins_api', array ($this, 'overwrite_wp_plugin_api_for_plugin' ), 10, 3 );
        add_filter( 'site_transient_update_plugins', array ($this, 'overwrite_site_transient' ), 10, 2 );
        
        add_filter( 'plugin_row_meta', array( $this, 'add_support_link' ), 10, 4 );

    }

    function overwrite_plugin_information() {
        if( isset( $_REQUEST['plugin'] ) && $_REQUEST['plugin'] == $this->slug ) {
            _e( 'Changelog: ', $this->text_domain );
            echo $this->changelog;
            exit();
        }
    }

    function force_check_for_updates() {
        $current_transient = get_site_transient( 'update_plugins' );
        $new_transient = apply_filters( 'site_transient_update_plugins', $current_transient, true );
        set_site_transient( 'update_plugins', $new_transient, $this->check_update_timeout );
        echo json_encode( 'checked' );
        exit();
    }

    function check_for_updates() {
        
        $this->live_version = get_site_option( $this->prefix.'_live_version' );
        $this->installed_version = get_site_option( $this->prefix.'_installed_version' );
        
        if (version_compare( $this->installed_version, $this->live_version, '<=' )) {

            $license_query = ( !empty( $this->license_key ) ) ? '&serial=' . $this->license_key : '';
        
            $result = wp_remote_post( 'http://www.storeapps.org/wp-admin/admin-ajax.php?action=get_products_latest_version&sku=' . $this->sku . $license_query . '&uuid=' . urlencode( admin_url( '/' ) ) );
            
            if (is_wp_error($result)) {
                return;
            }
            
            $response = json_decode( $result ['body'] );
            
            $live_version = $response->version;
            
            update_site_option( $this->prefix.'_login_link', $response->link );
            update_site_option( $this->prefix.'_due_date', $response->due_date );

            if ($this->live_version == $live_version || $response == 'false') {
                return;
            }
            
            $this->changelog = $response->changelog;

            update_site_option( $this->prefix.'_live_version', $live_version );
            update_site_option( $this->prefix.'_changelog', $response->changelog );

        }
    }

    function overwrite_site_transient($plugin_info, $force_check_updates = false) {
        
        if (empty( $plugin_info->checked ))
            return $plugin_info;

        $time_not_changed = isset( $this->last_checked ) && $this->check_update_timeout > ( time() - $this->last_checked );

        if ( $force_check_updates || !$time_not_changed ) {
            $this->check_for_updates();
            $this->last_checked = time();
            update_site_option( $this->prefix.'_last_checked', $this->last_checked );
        }

        $plugin_base_file = $this->base_name;
        $live_version = get_site_option( $this->prefix.'_live_version' );
        $installed_version = get_site_option( $this->prefix.'_installed_version' );

        if (version_compare( $live_version, $installed_version, '>' )) {
            $plugin_info->response [$plugin_base_file] = new stdClass();
            $plugin_info->response [$plugin_base_file]->slug = substr( $plugin_base_file, 0, strpos( $plugin_base_file, '/' ) );
            $plugin_info->response [$plugin_base_file]->new_version = $live_version;
            $plugin_info->response [$plugin_base_file]->url = 'http://www.storeapps.org';
            $plugin_info->response [$plugin_base_file]->package = get_site_option( $this->prefix.'_download_url' );
        }

        return $plugin_info;
    }

    function overwrite_wp_plugin_api_for_plugin($api = false, $action = '', $args = '') {

        if ($args->slug != $this->slug)
            return $api;

        if ('plugin_information' == $action || false === $api || $_REQUEST ['plugin'] == $args->slug) {
            $api->name = $this->name;
            $api->version = get_site_option( $this->prefix.'_live_version' );
            $api->download_link = get_site_option( $this->prefix.'_download_url' );
        }

        return $api;
    }

    function validate_license_key() {
        $this->license_key = (isset( $_REQUEST ['license_key'] ) && ! empty( $_REQUEST ['license_key'] )) ? $_REQUEST ['license_key'] : '';
        $storeapps_validation_url = 'http://www.storeapps.org/wp-admin/admin-ajax.php?action=woocommerce_validate_serial_key&serial=' . urlencode( $this->license_key ) . '&is_download=true&sku=' . $this->sku;
        $resp_type = array ('headers' => array ('content-type' => 'application/text' ) );
        $response_info = wp_remote_post( $storeapps_validation_url, $resp_type ); //return WP_Error on response failure

        if (is_array( $response_info )) {
            $response_code = wp_remote_retrieve_response_code( $response_info );
            $response_msg = wp_remote_retrieve_response_message( $response_info );

            if ($response_code == 200) {
                $storeapps_response = wp_remote_retrieve_body( $response_info );
                $decoded_response = json_decode( $storeapps_response );
                if ($decoded_response->is_valid == 1) {
                    update_site_option( $this->prefix.'_license_key', $this->license_key );                
                    update_site_option( $this->prefix.'_download_url', $decoded_response->download_url );
                } else {
                    $this->remove_license_download_url();
                }
                echo $storeapps_response;
                exit();
            }
            $this->remove_license_download_url();
            echo json_encode( array ('is_valid' => 0 ) );
            exit();
        }
        $this->remove_license_download_url();
        echo json_encode( array ('is_valid' => 0 ) );
        exit();
    }

    function remove_license_download_url() {
        update_site_option( $this->prefix.'_license_key', '' );                
        update_site_option( $this->prefix.'_download_url', '' );
    }

    function update_row($file, $plugin_data) {
        $license_key = get_site_option( $this->prefix.'_license_key' );
        $valid_color = '#AAFFAA';
        $invalid_color = '#FFAAAA';
        $color = ($license_key != '') ? $valid_color : $invalid_color;
        ?>
            <style type="text/css">
                div#TB_window {
                    background: lightgrey;
                }
                <?php if ( version_compare( get_bloginfo( 'version' ), '3.7.1', '>' ) ) { ?>
                tr.<?php echo $this->prefix; ?>_license_key .key-icon-column:before {
                    content: "\f112";
                    display: inline-block;
                    -webkit-font-smoothing: antialiased;
                    font: normal 1.5em/1 'dashicons';
                }
                tr.<?php echo $this->prefix; ?>_due_date .renew-icon-column:before {
                    content: "\f463";
                    display: inline-block;
                    -webkit-font-smoothing: antialiased;
                    font: normal 1.5em/1 'dashicons';
                }
                <?php } ?>
            </style>
            <script type="text/javascript">
                    jQuery(function(){
                        jQuery('input#<?php echo $this->prefix; ?>_validate_license_button').click(function(){
                            jQuery('img#<?php echo $this->prefix; ?>_license_validity_image').show();
                            jQuery.ajax({
                                url: '<?php echo admin_url("admin-ajax.php") ?>',
                                type: 'post',
                                dataType: 'json',
                                data: {
                                    'action': '<?php echo $this->prefix; ?>_validate_license_key',
                                    'license_key': jQuery('input#<?php echo $this->prefix; ?>_license_key').val()
                                },
                                success: function( response ) {
                                    if ( response.is_valid == 1 ) {
                                        jQuery('tr.<?php echo $this->prefix; ?>_license_key').css('background', '<?php echo $valid_color; ?>');
                                    } else {
                                        jQuery('tr.<?php echo $this->prefix; ?>_license_key').css('background', '<?php echo $invalid_color; ?>');
                                        jQuery('input#<?php echo $this->prefix; ?>_license_key').val('');
                                    }
                                    location.reload();
                                }
                            });
                        });

                        jQuery('input#<?php echo $this->prefix; ?>_check_for_updates').click(function(){
                            jQuery('img#<?php echo $this->prefix; ?>_license_validity_image').show();
                            jQuery.ajax({
                                url: '<?php echo admin_url("admin-ajax.php") ?>',
                                type: 'post',
                                dataType: 'json',
                                data: {
                                    'action': '<?php echo $this->prefix; ?>_force_check_for_updates'
                                },
                                success: function( response ) {
                                    if ( response == 'checked' ) {
                                        location.reload();
                                    } else {
                                        jQuery('img#<?php echo $this->prefix; ?>_license_validity_image').hide();
                                    }
                                }
                            });
                        });

                        jQuery(document).ready(function(){
                            var loaded_url = jQuery('a.<?php echo $this->prefix; ?>_support_link').attr('href');
                            
                            if ( loaded_url != undefined && ( loaded_url.indexOf('width') == -1 || loaded_url.indexOf('height') == -1 ) ) {
                                var width = jQuery(window).width();
                                var H = jQuery(window).height();
                                var W = ( 720 < width ) ? 720 : width;
                                var adminbar_height = 0;

                                if ( jQuery('body.admin-bar').length )
                                    adminbar_height = 28;

                                jQuery('a.<?php echo $this->prefix; ?>_support_link').each(function(){
                                    var href = jQuery(this).attr('href');
                                    if ( ! href )
                                            return;
                                    href = href.replace(/&width=[0-9]+/g, '');
                                    href = href.replace(/&height=[0-9]+/g, '');
                                    jQuery(this).attr( 'href', href + '&width=' + ( W - 80 ) + '&height=' + ( H - 85 - adminbar_height ) );
                                });

                            }

                            jQuery('tr.<?php echo $this->prefix; ?>_license_key').css( 'background', jQuery('tr.<?php echo $this->prefix; ?>_due_date').css( 'background' ) );
                            
                            <?php if ( version_compare( get_bloginfo( 'version' ), '3.7.1', '>' ) ) { ?>
                                jQuery('tr.<?php echo $this->prefix; ?>_license_key .key-icon-column').css( 'border-left', jQuery('tr.<?php echo $this->prefix; ?>_license_key').prev().prev().prev().find('th.check-column').css( 'border-left' ) );
                                jQuery('tr.<?php echo $this->prefix; ?>_due_date .renew-icon-column').css( 'border-left', jQuery('tr.<?php echo $this->prefix; ?>_license_key').prev().prev().prev().find('th.check-column').css( 'border-left' ) );
                            <?php } ?>

                        });

                    });
            </script>
            <?php if ( empty( $license_key ) ) { ?>
            <!-- <tr class="<?php echo $this->prefix; ?>_license_key" style="background: <?php echo $color; ?>">
                <td class="key-icon-column" style="vertical-align: middle;"></td>
                <td style="vertical-align: middle;"><label for="<?php echo $this->prefix; ?>_license_key"><strong><?php _e( 'License Key', $this->text_domain ); ?></strong></label></td>
                <td>
                    <input type="text" id="<?php echo $this->prefix; ?>_license_key" name="<?php echo $this->prefix; ?>_license_key" value="<?php echo $license_key; ?>" size="50" style="text-align: center;" />
                    <input type="button" class="button" id="<?php echo $this->prefix; ?>_validate_license_button" name="<?php echo $this->prefix; ?>_validate_license_button" value="<?php _e( 'Validate', $this->text_domain ); ?>" />
                    <input type="button" class="button" id="<?php echo $this->prefix; ?>_check_for_updates" name="<?php echo $this->prefix; ?>_check_for_updates" value="Check for updates" />
                    <img id="<?php echo $this->prefix; ?>_license_validity_image" src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" style="display: none; vertical-align: middle;" />

                </td>
            </tr>-->
            <?php } ?>
            <?php
                if ( !empty( $this->due_date ) ) {
                    $start = strtotime( $this->due_date . ' -30 days' );
                    $due_date = strtotime( $this->due_date );
                    $now = time();
                    if ( $now >= $start ) {
                        $remaining_days = round( abs( $due_date - $now )/60/60/24 );
                        if ( $now > $due_date ) {
                            $extended_text = __( 'has expired', $this->text_domain );
                        } else {
                            $extended_text = sprintf(__( 'will expire in %d %s', $this->text_domain ), $remaining_days, _n( 'day', 'days', $remaining_days, $this->text_domain ) );
                        }
                        ?>
                            <tr class="<?php echo $this->prefix; ?>_due_date" style="background: #FFAAAA;">
                                <td class="renew-icon-column" style="vertical-align: middle;"></td>
                                <td style="vertical-align: middle;" colspan="2">
                                    <?php echo sprintf(__( 'Your license for %s %s. To continue receiving updates & support, please %s', $this->text_domain ), $this->plugin_data['Name'], '<strong>' . $extended_text . '</strong>', '<a href="' . $this->login_link . '" target="_blank">' . __( 'renew your license now', $this->text_domain ) . ' &rarr;</a>'); ?>
                                </td>
                            </tr>
                        <?php
                    }
                }
    }

    function add_support_ticket_content() {
        global $pagenow;

        if ( $pagenow != 'plugins.php' ) return;
        
        self::support_ticket_content( $this->prefix, $this->sku, $this->plugin_data, $this->license_key, $this->text_domain );
    }

    static function support_ticket_content( $prefix = '', $sku = '', $plugin_data = array(), $license_key = '', $text_domain = '' ) {
        global $current_user, $wpdb;

        if ( !( $current_user instanceof WP_User ) ) return;

        if( isset( $_POST['storeapps_submit_query'] ) && $_POST['storeapps_submit_query'] == "Send" ){
            
            check_admin_referer( 'storeapps-submit-query_' . $sku );

            $additional_info = ( isset( $_POST['additional_information'] ) && !empty( $_POST['additional_information'] ) ) ? ( ( function_exists( 'woocommerce_clean' ) ) ? woocommerce_clean( $_POST['additional_information'] ) : $_POST['additional_information'] ) : '';
            $additional_info = str_replace( '=====', '<br />', $additional_info );
            $additional_info = str_replace( array( '[', ']' ), '', $additional_info );

            $headers = 'From: ';
            $headers .= ( isset( $_POST['client_name'] ) && !empty( $_POST['client_name'] ) ) ? ( ( function_exists( 'woocommerce_clean' ) ) ? woocommerce_clean( $_POST['client_name'] ) : $_POST['client_name'] ) : '';
            $headers .= ' <' . ( ( function_exists( 'woocommerce_clean' ) ) ? woocommerce_clean( $_POST['client_email'] ) : $_POST['client_email'] ) . '>' . "\r\n";
            $headers .= 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";

            ob_start();
            if ( isset( $_POST['include_data'] ) && $_POST['include_data'] == 'yes' ) {
                echo $additional_info . '<br /><br />';
            }
            echo nl2br($_POST['message']) ;
            $message = ob_get_clean();
            if ( empty( $_POST['name'] ) ) {
                wp_mail( 'support@storeapps.org', $_POST['subject'], $message, $headers );
                header('Location: ' . $_SERVER['HTTP_REFERER'] );
            }
            
        }
        
        ?>
        <div id="<?php echo $prefix; ?>_post_query_form" style="display: none;">
            <style>
                table#<?php echo $prefix; ?>_post_query_table {
                    padding: 5px;
                }
                table#<?php echo $prefix; ?>_post_query_table tr td {
                    padding: 5px;
                }
                input.<?php echo $sku; ?>_text_field {
                    padding: 5px;
                }
                label {
                    font-weight: bold;
                }
            </style>
            <?php

                if ( !wp_script_is('jquery') ) {
                    wp_enqueue_script('jquery');
                    wp_enqueue_style('jquery');
                }

                $first_name = get_user_meta($current_user->ID, 'first_name', true);
                $last_name = get_user_meta($current_user->ID, 'last_name', true);
                $name = $first_name . ' ' . $last_name;
                $customer_name = ( !empty( $name ) ) ? $name : $current_user->data->display_name;
                $customer_email = $current_user->data->user_email;
                $license_key = $license_key;
                if ( class_exists( 'SA_WC_Compatibility' ) ) {
                    $ecom_plugin_version = 'WooCommerce ' . SA_WC_Compatibility::get_wc_version();
                } else {
                    $ecom_plugin_version = 'NA';
                }
                $wp_version = ( is_multisite() ) ? 'WPMU ' . get_bloginfo('version') : 'WP ' . get_bloginfo('version');
                $admin_url = admin_url();
                $php_version = ( function_exists( 'phpversion' ) ) ? phpversion() : '';
                $wp_max_upload_size = size_format( wp_max_upload_size() );
                $server_max_upload_size = ini_get('upload_max_filesize');
                $server_post_max_size = ini_get('post_max_size');
                $wp_memory_limit = WP_MEMORY_LIMIT;
                $wp_debug = ( defined( 'WP_DEBUG' ) && WP_DEBUG === true ) ? 'On' : 'Off';
                $this_plugins_version = $plugin_data['Name'] . ' ' . $plugin_data['Version'];
                $ip_address = $_SERVER['REMOTE_ADDR'];
                $additional_information = "===== [Additional Information] =====
                                           [E-Commerce Plugin: $ecom_plugin_version] =====
                                           [WP Version: $wp_version] =====
                                           [Admin URL: $admin_url] =====
                                           [PHP Version: $php_version] =====
                                           [WP Max Upload Size: $wp_max_upload_size] =====
                                           [Server Max Upload Size: $server_max_upload_size] =====
                                           [Server Post Max Size: $server_post_max_size] =====
                                           [WP Memory Limit: $wp_memory_limit] =====
                                           [WP Debug: $wp_debug] =====
                                           [" . $plugin_data['Name'] . " Version: " . $plugin_data['Version'] . "] =====
                                           [License Key: $license_key] =====
                                           [IP Address: $ip_address] =====
                                          ";

            ?>
            <form id="<?php echo $prefix; ?>_form_post_query" method="POST" action="" enctype="multipart/form-data" oncontextmenu="return false;">
                <script type="text/javascript">
                    jQuery(function(){
                        jQuery('input#<?php echo $prefix; ?>_submit_query').click(function(e){
                            var error = false;

                            var client_name = jQuery('input#client_name').val();
                            if ( client_name == '' ) {
                                jQuery('input#client_name').css('border-color', 'red');
                                error = true;
                            } else {
                                jQuery('input#client_name').css('border-color', '');
                            }

                            var client_email = jQuery('input#client_email').val();
                            if ( client_email == '' ) {
                                jQuery('input#client_email').css('border-color', 'red');
                                error = true;
                            } else {
                                jQuery('input#client_email').css('border-color', '');
                            }

                            var subject = jQuery('table#<?php echo $prefix; ?>_post_query_table input#subject').val();
                            if ( subject == '' ) {
                                jQuery('input#subject').css('border-color', 'red');
                                error = true;
                            } else {
                                jQuery('input#subject').css('border-color', '');
                            }

                            var message = jQuery('table#<?php echo $prefix; ?>_post_query_table textarea#message').val();
                            if ( message == '' ) {
                                jQuery('textarea#message').css('border-color', 'red');
                                error = true;
                            } else {
                                jQuery('textarea#message').css('border-color', '');
                            }

                            if ( error == true ) {
                                jQuery('label#error_message').text('* All fields are compulsory.');
                                e.preventDefault();
                            } else {
                                jQuery('label#error_message').text('');
                            }

                        });

                        jQuery("span.<?php echo $prefix; ?>_support a.thickbox").click( function(){                                    
                            setTimeout(function() {
                                jQuery('#TB_ajaxWindowTitle strong').text('Send your query');
                            }, 0 );
                        });

                        jQuery('div#TB_ajaxWindowTitle').each(function(){
                           var window_title = jQuery(this).text(); 
                           if ( window_title.indexOf('Send your query') != -1 ) {
                               jQuery(this).remove();
                           }
                        });

                        jQuery('input,textarea').keyup(function(){
                            var value = jQuery(this).val();
                            if ( value.length > 0 ) {
                                jQuery(this).css('border-color', '');
                                jQuery('label#error_message').text('');
                            }
                        });

                    });
                </script>
                <table id="<?php echo $prefix; ?>_post_query_table">
                    <tr>
                        <td><label for="client_name"><?php _e('Name', $text_domain); ?>*</label></td>
                        <td><input type="text" class="regular-text <?php echo $sku; ?>_text_field" id="client_name" name="client_name" value="<?php echo $customer_name; ?>" autocomplete="off" oncopy="return false;" onpaste="return false;" oncut="return false;"/></td>
                    </tr>
                    <tr>
                        <td><label for="client_email"><?php _e('E-mail', $text_domain); ?>*</label></td>
                        <td><input type="email" class="regular-text <?php echo $sku; ?>_text_field" id="client_email" name="client_email" value="<?php echo $customer_email; ?>" autocomplete="off" oncopy="return false;" onpaste="return false;" oncut="return false;"/></td>
                    </tr>
                    <tr>
                        <td><label for="current_plugin"><?php _e('Product', $text_domain); ?></label></td>
                        <td><input type="text" class="regular-text <?php echo $sku; ?>_text_field" id="current_plugin" name="current_plugin" value="<?php echo $this_plugins_version; ?>" readonly autocomplete="off" oncopy="return false;" onpaste="return false;" oncut="return false;"/><input type="text" name="name" value="" style="display: none;" /></td>
                    </tr>
                    <tr>
                        <td><label for="subject"><?php _e('Subject', $text_domain); ?>*</label></td>
                        <td><input type="text" class="regular-text <?php echo $sku; ?>_text_field" id="subject" name="subject" value="<?php echo ( !empty( $subject ) ) ? $subject : ''; ?>" autocomplete="off" oncopy="return false;" onpaste="return false;" oncut="return false;"/></td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top; padding-top: 12px;"><label for="message"><?php _e('Message', $text_domain); ?>*</label></td>
                        <td><textarea id="message" name="message" rows="10" cols="60" autocomplete="off" oncopy="return false;" onpaste="return false;" oncut="return false;"><?php echo ( !empty( $message ) ) ? $message : ''; ?></textarea></td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top; padding-top: 12px;"></td>
                        <td><input id="include_data" type="checkbox" name="include_data" value="yes" /> <label for="include_data"><?php echo __( 'Include plugins / environment details to help solve issue faster', $text_domain ); ?></label></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><label id="error_message" style="color: red;"></label></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><button type="submit" class="button" id="<?php echo $prefix; ?>_submit_query" name="storeapps_submit_query" value="Send" ><?php _e( 'Send', $text_domain ) ?></button></td>
                    </tr>
                </table>
                <?php wp_nonce_field( 'storeapps-submit-query_' . $sku ); ?>
                <input type="hidden" name="license_key" value="<?php echo $license_key; ?>" />
                <input type="hidden" name="sku" value="<?php echo $sku; ?>" />
                <input type="hidden" class="hidden_field" name="ecom_plugin_version" value="<?php echo $ecom_plugin_version; ?>" />
                <input type="hidden" class="hidden_field" name="wp_version" value="<?php echo $wp_version; ?>" />
                <input type="hidden" class="hidden_field" name="admin_url" value="<?php echo $admin_url; ?>" />
                <input type="hidden" class="hidden_field" name="php_version" value="<?php echo $php_version; ?>" />
                <input type="hidden" class="hidden_field" name="wp_max_upload_size" value="<?php echo $wp_max_upload_size; ?>" />
                <input type="hidden" class="hidden_field" name="server_max_upload_size" value="<?php echo $server_max_upload_size; ?>" />
                <input type="hidden" class="hidden_field" name="server_post_max_size" value="<?php echo $server_post_max_size; ?>" />
                <input type="hidden" class="hidden_field" name="wp_memory_limit" value="<?php echo $wp_memory_limit; ?>" />
                <input type="hidden" class="hidden_field" name="wp_debug" value="<?php echo $wp_debug; ?>" />
                <input type="hidden" class="hidden_field" name="current_plugin" value="<?php echo $this_plugins_version; ?>" />
                <input type="hidden" class="hidden_field" name="ip_address" value="<?php echo $ip_address; ?>" />
                <input type="hidden" class="hidden_field" name="additional_information" value='<?php echo $additional_information; ?>' />
            </form>
        </div>
        <?php
    }

    function add_support_link( $plugin_meta, $plugin_file, $plugin_data, $status ) {
        
        if ( $this->base_name == $plugin_file ) {
            $query_char = ( strpos( $_SERVER['REQUEST_URI'], '?' ) !== false ) ? '&' : '?';
            $plugin_meta[] = '<a href="#TB_inline'.$query_char.'inlineId='.$this->prefix.'_post_query_form" class="thickbox '.$this->prefix.'_support_link" title="' . __( 'Submit your query', $this->text_domain ) . '">' . __( 'Support', $this->text_domain ) . '</a>';
            if ( !empty( $this->documentation_link ) ) {
                $plugin_meta[] = '<a href="'.$this->documentation_link.'" target="_blank" title="' . __( 'Documentation', $this->text_domain ) . '">' . __( 'Docs', $this->text_domain ) . '</a>';
            }
            $plugin_meta[] = '<br>' . self::add_social_links( $this->prefix );
        }
        
        return $plugin_meta;
        
    }

    static function add_social_links( $prefix = '' ) {

        $social_link = '<style type="text/css">
                            div > iframe {
                                max-height: 1.5em;
                                vertical-align: middle;
                                padding: 5px 2px 0px 0px;
                            }
                            iframe[id^="twitter-widget"] {
                                max-width: 10.3em;
                            }
                            iframe#fb_like_' . $prefix . ' {
                                max-width: 6em;
                            }
                            span > iframe {
                                vertical-align: middle;
                            }
                        </style>';
        $social_link .= '<a href="https://twitter.com/storeapps" class="twitter-follow-button" data-show-count="true" data-dnt="true" data-show-screen-name="false">Follow</a>';
        $social_link .= "<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>";
        $social_link .= '<iframe id="fb_like_' . $prefix . '" src="http://www.facebook.com/plugins/like.php?href=https%3A%2F%2Fwww.facebook.com%2Fpages%2FStore-Apps%2F614674921896173&width=100&layout=button_count&action=like&show_faces=false&share=false&height=21"></iframe>';
        $social_link .= '<script src="//platform.linkedin.com/in.js" type="text/javascript">lang: en_US</script><script type="IN/FollowCompany" data-id="3758881" data-counter="right"></script>';

        return $social_link;

    }
}