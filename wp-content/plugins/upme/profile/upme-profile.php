<?php
add_action('wp_ajax_upme_delete_profile_images', 'upme_delete_profile_images');
add_action('wp_ajax_nopriv_upme_delete_profile_images', 'upme_delete_profile_images');

// Delete the profile images from the edit screen
function upme_delete_profile_images() {
    global $user;

    $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : '';
    $custom_field_name = isset($_POST['field_name']) ? $_POST['field_name'] : '';

    if (is_user_logged_in ()) {
        $current_user_id = get_current_user_id();

        if (current_user_can('edit_user', $current_user_id)) {

            $image_url = esc_url(get_the_author_meta($custom_field_name, $user_id));
            $del_status = upme_delete_uploads_folder_files($image_url);
            if ($del_status && delete_user_meta($user_id, $custom_field_name)) {
                echo json_encode(array("status" => TRUE));
            } else {
                echo json_encode(array("status" => FALSE));
            }           

            //upme_path
        }
    }

    die();
}

// Include the frontend scripts to Iframe for crop funcitionality
add_action('upme_crop_iframe_head', 'upme_crop_iframe_head');

function upme_crop_iframe_head() {

    $html  = '<link type="text/css" href="'.upme_url . 'css/font-awesome.min.css." rel="stylesheet" />';
    $html .= '<link type="text/css" href="'.upme_url . 'css/upme.css" rel="stylesheet" />';
    $html .= '<link type="text/css" href="'.site_url('wp-includes/js/jcrop/jquery.Jcrop.min.css').'" rel="stylesheet" />';
    
    /* Add style */
    $settings = get_option('upme_options');
    if ($settings['style']) {
        $html .= '<link type="text/css" href="'.upme_url . 'styles/' . $settings['style'] . '.css" rel="stylesheet" />';
    }

    $html .= '<link type="text/css" href="'.upme_url . 'css/upme-responsive.css" rel="stylesheet" />';

    $html .= '<script type="text/javascript" src="'.site_url('wp-includes/js/jquery/jquery.js') . '" ></script>';
    
    $html .= '<script type="text/javascript" src="'.site_url('wp-includes/js/jquery/jquery-migrate.min.js').'" ></script>';
    
    $html .= '<script type="text/javascript" src="'.site_url('wp-includes/js/jcrop/jquery.Jcrop.min.js') . '" ></script>';
    $html .= '<script type="text/javascript" src="'.upme_url . 'js/upme-custom.js" ></script>';
    $html .= '<script type="text/javascript" src="'.upme_url . 'js/upme-crop.js" ></script>';

    $del_msg = __("Are you sure you want to delete this image?", "upme");
    $upload_msg = __("Please select an image to upload.", "upme");
    $admin_ajax = admin_url("admin-ajax.php");

    $html .= '<script type="text/javascript" >
                var UPMECustom = {"Messages":{"DelPromptMessage":"'.$del_msg.'","UploadEmptyMessage":"'.$upload_msg.'"},"AdminAjax":"'.$admin_ajax.'"};
              </script>';

    echo $html;
}

add_action('wp_ajax_upme_initialize_upload_box', 'upme_initialize_upload_box');

// Display the upload box for image uploading and cropping
function upme_initialize_upload_box() {
    global $current_user,$upme_save;

    $id = $_GET['upme_id'];
    $meta = isset($_GET['upme_meta']) ? $_GET['upme_meta'] : '';
    $disabled = isset($_GET['upme_disabled']) ? $_GET['upme_disabled'] : '';

    $settings = get_option('upme_options');


    $display = '<html>
                    <head>
                        ' . upme_crop_iframe_head() . '
                        <style type="text/css">
                            html{
                                overflow: hidden;
                            }
                            
                        </style>
                    </head>
                    <body>
                        <form id="upme-crop-frm" action="" method="post" enctype="multipart/form-data">';
    $display .= '           <div class="upme-crop-wrap">';
    $display .= '           <div class="upme-wrap">';

    $display .= '               <div class="upme-field upme-separator upme-edit upme-clearfix" style="display: block;">' . __('Update Profile Picture', 'upme') . '</div>';


    $profile_pic_url = get_the_author_meta($meta, $id);

    if (is_array($upme_save->errors) && count($upme_save->errors) != 0 ) {
        
        if (($id == $current_user->ID || current_user_can('edit_users')) && is_numeric($id)) {


            $display .= upme_display_upload_box($id, $meta, $disabled, $profile_pic_url, 'block');
            $display .= upme_display_crop_box($id, $meta, $profile_pic_url, 'none');
        }
        
    } elseif( isset($_POST['upme-upload-submit-' . $id]) || isset($_POST['upme-crop-request-' . $id]) ) {
        // Display crop area on file upload or crop link click
        if (($id == $current_user->ID || current_user_can('edit_users')) && is_numeric($id)) {

            $display .= upme_display_crop_box($id, $meta, $profile_pic_url, 'block');
        }
    } elseif (isset($_POST['upme-crop-submit-' . $id])) {
        // Crop the image on area selection and submit
        $data_x1 = isset($_POST['upme-crop-x1']) ? $_POST['upme-crop-x1'] : 0;
        $data_y1 = isset($_POST['upme-crop-y1']) ? $_POST['upme-crop-y1'] : 0;
        $data_width = isset($_POST['upme-crop-width']) ? $_POST['upme-crop-width'] : 50;
        $data_height = isset($_POST['upme-crop-height']) ? $_POST['upme-crop-height'] : 50;

        $src = get_the_author_meta($meta, $id);

        $upme_upload_path = '';
        $upme_upload_url = '';

        if ($upload_dir = upme_get_uploads_folder_details()) {
            $upme_upload_path = $upload_dir['basedir'] . "/upme/";
            $upme_upload_url = $upload_dir['baseurl'] . "/upme/";
            $src = str_replace($upme_upload_url, $upme_upload_path, $src);
        }


        if (is_readable($src)) {

            $result = wp_crop_image($src, $data_x1, $data_y1, $data_width, $data_height, $data_width, $data_height);
            if (!is_wp_error($result)) {

                $cropped_path = str_replace($upme_upload_path, $upme_upload_url, $result);
                update_user_meta($id, $meta, $cropped_path);

                $display .= upme_display_upload_box($id, $meta, $disabled, $profile_pic_url, 'block');
            }
        }

        update_crop_image_display($id,$meta,$cropped_path);

    } elseif (isset($_POST['upme-crop-save-' . $id])) {

        $src = get_the_author_meta($meta, $id);

        update_crop_image_display($id,$meta,$src);

    } else {

        if (($id == $current_user->ID || current_user_can('edit_users')) && is_numeric($id)) {


            $display .= upme_display_upload_box($id, $meta, $disabled, $profile_pic_url, 'block');
            $display .= upme_display_crop_box($id, $meta, $profile_pic_url, 'none');
        }
    }

    $display .= '           </div>';
    $display .= '           </div>';

    $display .= '       </form>
                    </body>
                </html>';

    echo $display;
    exit;
}

/* Display the exisitng profile picture with image upload field  */

function upme_display_upload_box($id, $meta, $disabled, $profile_pic_url, $visibility = 'block') {
    global $upme_save;
    
    $display  = '';
    
    
    $display .= '   <div class="upme-field upme-edit" style="display:' . $visibility . '">
                        <div class="upme-field-value"><div class="upme-note"><strong>' . __('Current Picture:', 'upme') . ' </strong></div></div>';


    if (!empty($profile_pic_url)) {
        $display .= '       <div class="upme-field-value">
                            <div class="upme-note">
                                <img class="upme-preview-current" alt="" src="' . $profile_pic_url . '">
                                
                                <div upme-data-user-id="' . $id . '" upme-data-field-name="' . $meta . '" class="upme-delete-userpic-wrapper">
                                    <i original-title="remove" class="upme-icon upme-icon-remove"></i> 
                                    <label class="upme-delete-image">' . __('Delete Image', 'upme') . '</label>
                                </div>

                                <div id="upme-spinner-' . $meta . '" class="upme-delete-spinner">
                                    <i original-title="spinner" class="upme-icon upme-icon-spinner upme-tooltip3"></i>
                                    <label>' . __('Loading', 'upme') . '</label>
                                </div>

                                <div id="upme-crop-request" upme-data-user-id="' . $id . '" upme-data-field-name="' . $meta . '" class="upme-crop-image-wrapper">
                                    <i original-title="crop" class="upme-icon upme-icon-crop"></i> 
                                    <label class="upme-delete-image">' . __('Crop Image', 'upme') . '</label>

                                </div>

                                 <div class="clear"></div>   
                            </div>
                        </div>
                    </div>';
    }

    if(is_array($upme_save->errors) && count($upme_save->errors) != 0){
        $display  .= '<div class="upme-clear"></div><div id="upme-crop-upload-err-holder" style="display: block;" class="upme-errors">
                            <span id="upme-crop-upload-err-block" class="upme-error upme-error-block">';
        foreach($upme_save->errors as $err){
            $display  .= '<span class="upme-error upme-error-block"><i class="upme-icon upme-icon-remove"></i>
                                '.$err.'</span>';
        }
                                
        $display  .= '      </span>         
                    </div>';    
    }

    $display .= '   <div class="upme-field upme-edit" style="display:' . $visibility . '">
                        <div id="upme-crop-upload-err-holder" style="display: none;" class="upme-errors">
                                <span id="upme-crop-upload-err-block" class="upme-error upme-error-block">
   
                                </span>         
                        </div>
                        <div class="upme-field-value">';


    if (is_safari() || is_opera()) {
        $display .= '<input class="upme-fileupload-field" ' . $disabled . ' type="file" name="' . $meta . '-' . $id . '" id="file_' . $meta . '-' . $id . '" style="display:block;" />
                     <input id="upme-upload-image" upme-data-meta="' . $meta . '" upme-data-id="' . $id . '" type="button" name="upme-upload-image-' . $id . '" class="upme-button-alt-wide upme-fire-editor" value="' . __('Upload Image', 'upme') . '" />';
    } else {
        $display .= '
                     <input class="upme-fileupload-field" ' . $disabled . ' type="file" name="' . $meta . '-' . $id . '" id="file_' . $meta . '-' . $id . '"  style="display:block;" />
                     <input id="upme-upload-image" upme-data-meta="' . $meta . '" upme-data-id="' . $id . '" type="button" name="upme-upload-image-' . $id . '" class="upme-button-alt-wide upme-fire-editor" value="' . __('Upload Image', 'upme') . '" />';
    }

    $display .= '       </div>
                    </div>';

    return $display;
}

/* Display the crop image area after image upload or clicking the crop link */

function upme_display_crop_box($id, $meta, $profile_pic_url, $visibility = 'block') {

    $display = '    <div class="upme-field upme-edit" style="display:' . $visibility . '">
                        <div class="upme-crop-column1">
                            <div class="upme-field-value">
                                <div class="upme-note">
                                    <strong>' . __('Crop Your New Profile Picture', 'upme') . '</strong>
                                    <input id="upme-crop-submit" type="submit" value="'. __('Crop Image', 'upme') .'" class="upme-button-alt upme-fire-editor" name="upme-crop-submit-' . $id . '">
                                    <input id="upme-crop-save" type="submit" value="'. __('Save Image', 'upme') .'" class="upme-button-alt upme-fire-editor" name="upme-crop-save-' . $id . '">
                                </div>
                            </div>
                        
                            <div class="upme-crop-field-value">
                                <div class="upme-note">
                                    <img id="target" alt="" src="' . $profile_pic_url . '">
                                </div>
                            </div>
                        </div>
                        <div class="upme-crop-column2">
                            <div class="upme-field-value">
                                <div class="upme-note">
                                    <input type="hidden" name="upme-crop-x1" id="upme-crop-x1" />
                                    <input type="hidden" name="upme-crop-x2" id="upme-crop-x2" />
                                    <input type="hidden" name="upme-crop-y1" id="upme-crop-y1" />
                                    <input type="hidden" name="upme-crop-y2" id="upme-crop-y2" />
                                    <input type="hidden" name="upme-crop-width" id="upme-crop-width" />
                                    <input type="hidden" name="upme-crop-height" id="upme-crop-height" />                                    
                                </div>
                                <div class="upme-note">
                                    <div id="upme-preview-pane">
                                        <div class="upme-preview-container">
                                            <img src="' . $profile_pic_url . '" class="jcrop-preview" alt="Preview" />
                                        </div>
                                    </div> 
                                </div>
                            </div>
                        </div>
                    </div>';

    return $display;
}

// Adding AJAX action for logged in and guest both.
add_action('wp_ajax_upme_check_edit_email', 'upme_check_edit_email');
add_action('wp_ajax_nopriv_upme_check_edit_email', 'upme_check_edit_email');

function upme_check_edit_email() {
    $email_exists = false;
    $email_id = isset($_POST['email_id']) ? $_POST['email_id'] : 0;

    $current_user_id = isset($_POST['user_id']) ? $_POST['user_id'] : 0;

    $user_id = email_exists($email_id);

    if ($user_id && ($user_id != $current_user_id)) {
        $email_exists = true;
    }

    if ($email_exists == false) {
        echo json_encode(array("status" => TRUE, "msg" => "success"));
    } else if ($email_exists == true) {
        echo json_encode(array("status" => FALSE, "msg" => "email_error"));
    }

    die;
}



// Adding AJAX actions for validating registration fields.
add_action('wp_ajax_upme_validate_edit_profile_email', 'upme_validate_edit_profile_email');
add_action('wp_ajax_nopriv_upme_validate_edit_profile_email', 'upme_validate_edit_profile_email');

function upme_validate_edit_profile_email() {

    $user_email = isset($_POST['user_email']) ? $_POST['user_email'] : '';

    
    $current_user_id = isset($_POST['user_id']) ? $_POST['user_id'] : 0;

    $user_id = email_exists($user_email);

    $email_exists = false;
    if ($user_id && ($user_id != $current_user_id)) {
        $email_exists = true;
    }

    if (!empty($user_email)) {

        $user_email = sanitize_email($user_email);

        if (is_email($user_email)) {
            // Check the existence of user email from database
            if ($email_exists) {
                echo json_encode(array("status" => TRUE, "msg" => "RegExistEmail"));
            } else {
                echo json_encode(array("status" => FALSE, "msg" => "RegValidEmail"));
            }
        } else {
            echo json_encode(array("status" => TRUE, "msg" => "RegInvalidEmail"));
        }
    } else {
        echo json_encode(array("status" => TRUE, "msg" => "RegEmptyEmail"));
    }


    die();
}

// Update the displayed image after cropping and close the cropping window
function update_crop_image_display($id,$meta,$image_path){

?>
        <!-- Close the window and update the new image after cropping -->
        <style type="text/css">
            @media only screen and (min-width: 480px) and (max-width: 767px) {     
                .jcrop-holder #upme-preview-pane {
                    display: block;
                    position: absolute;
                    z-index: 2000;
                    right: -150px;
                    padding: 0 6px;

                }
            }
        </style>
        <script type="text/javascript">
            jQuery(document).ready(function(){

                var userId = "<?php echo $id; ?>";
                var imageMeta = "<?php echo $meta; ?>";
                var imagePath = "<?php echo $image_path; ?>";
                var profileWindow = jQuery(this).parent();

                if(window.parent.jQuery("#upme-preview-"+imageMeta).length == 0){
                    window.parent.jQuery(".upme-current-pic-note").remove();

                    window.parent.jQuery("#upme-current-picture").after('<div class="upme-note upme-current-pic-note">'+
                        '<img id="upme-preview-user_pic" src="'+imagePath+'" alt="">'+
                        '<div class="upme-delete-userpic-wrapper" upme-data-field-name="'+imageMeta+'" upme-data-user-id="'+ userId +'">'+
                        '<i class="upme-icon upme-icon-remove" original-title="remove"></i> '+
                        '<label class="upme-delete-image"><?php echo __("Delete Image", "upme"); ?> </label>'+
                        '</div>'+
                        '<div id="upme-spinner-'+imageMeta+'" class="upme-delete-spinner"><i original-title="spinner" class="upme-icon upme-icon-spinner upme-tooltip3"></i><label><?php echo __("Loading", "upme") ?></label></div>'+
                        '</div>');

                    window.parent.jQuery(".upme-pic img").attr("src",imagePath);


                }else{
                    window.parent.jQuery("#upme-preview-"+imageMeta).attr("src",imagePath);
                    window.parent.jQuery("#upme-avatar-"+imageMeta).attr("src",imagePath);
                }

                self.parent.tb_remove();
                parent.jQuery.fancybox.close();
      
            });
        </script>
<?php    
}


add_action('wp_ajax_upme_initialize_profile_modal', 'upme_initialize_profile_modal');
add_action('wp_ajax_nopriv_upme_initialize_profile_modal', 'upme_initialize_profile_modal');

function upme_initialize_profile_modal(){
    global $current_user;

    $id = $_POST['upme_id'];

    $settings = get_option('upme_options');
    
    /* UPME Filter for cusmizing profile window shortcode */
    $profile_shortcode = apply_filters('upme_profile_modal_shortcode',$settings['profile_modal_window_shortcode'],$id);
    // End Filter

    $profile_shortcode = str_replace('[upme' ,'[upme modal_view=yes id="'.$id.'" ',$profile_shortcode);

    $display  = do_shortcode($profile_shortcode);

    echo $display;
    exit;
}