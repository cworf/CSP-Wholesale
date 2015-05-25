<?php

function a13_child_style(){
    global $wp_styles;

    //use also for child theme style
    $user_css_deps = $wp_styles->registered['user-css']->deps;
    wp_enqueue_style('child-style', get_stylesheet_directory_uri(). '/style.css', $user_css_deps, A13_THEME_VER);

    //change loading order of user.css
    array_push($user_css_deps, array('child-style'));

    //take it out of queue and insert at end
    wp_dequeue_style('user-css');
    wp_enqueue_style('user-css');
}
add_action('wp_enqueue_scripts', 'a13_child_style',27);

/*
 * Add here your functions below, and overwrite native theme functions
 */
