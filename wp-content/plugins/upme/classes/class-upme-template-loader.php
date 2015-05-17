<?php

class UPME_Template_Loader{
    
    public function get_template_part( $slug, $name = null, $load = true ) {

        do_action( 'upme_get_template_part_' . $slug, $slug, $name );

        // Setup possible parts
        $templates = array();
        if ( isset( $name ) )
            $templates[] = $slug . '-' . $name . '.php';
        $templates[] = $slug . '.php';

        // Allow template parts to be filtered
        $templates = apply_filters( 'upme_get_template_part', $templates, $slug, $name );

        // Return the part that is found
        return $this->locate_template( $templates, $load, false );
    }
    
    public function locate_template( $template_names, $load = false, $require_once = true ) {
        // No file found yet
        $located = false;

        // Traverse through template files
        foreach ( (array) $template_names as $template_name ) {

            // Continue if template is empty
            if ( empty( $template_name ) )
                continue;

            $template_name = ltrim( $template_name, '/' );

            // Check templates for frontend section
            if ( file_exists( trailingslashit( upme_path ) . 'templates/cards/' . $template_name ) ) {
                $located = trailingslashit( upme_path ) . 'templates/cards/' . $template_name;
                break;

            // Check templates for admin section
            } elseif ( file_exists( trailingslashit( upme_path ) . 'admin/templates/' . $template_name ) ) {
                $located = trailingslashit( upme_path ) . 'admin/templates/' . $template_name;
                break;
            } else{
               
                /* Enable additional template locations using filters for addons */
                $template_locations = apply_filters('upme_template_loader_locations',array());
                 
                foreach($template_locations as $location){
                    
                    if(file_exists( $location . $template_name)){
                        
                        $located = $location . $template_name;
                        break;
                    }
                }
                
            }
        }
        
        
        if ( ( true == $load ) && ! empty( $located ) )
            load_template( $located, $require_once );

        return $located;
    }
}

$upme_template_loader = new UPME_Template_Loader();

?>