<?php
/*
 * Title META tag
 */
if(!function_exists('a13_wp_title')){
    function a13_wp_title( $title, $sep ) {
        global $paged, $page;

        if ( is_feed() )
            return $title;

        // Add the site name.
        $title .= get_bloginfo( 'name' );

        // Add the site description for the home/front page.
        $site_description = get_bloginfo( 'description', 'display' );
        if ( $site_description && ( is_home() || is_front_page() ) )
            $title = "$title $sep $site_description";

        // Add a page number if necessary.
        if ( $paged >= 2 || $page >= 2 )
            $title = "$title $sep " . sprintf( __( 'Page %s', 'fame' ), max( $paged, $page ) );

        return $title;
    }
}


/*
 * Prints favicon
 */
if(!function_exists('a13_favicon')){
    function a13_favicon() {
        global $apollo13;
        $fav_icon = $apollo13->get_option( 'appearance', 'favicon' );
        if(!empty($fav_icon))
            echo '<link rel="shortcut icon" href="'.esc_url($fav_icon).'" />';
    }
}

add_filter( 'wp_title', 'a13_wp_title', 10, 2 );