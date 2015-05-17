<?php
/**
 * TT class that handles enqueues ( styles and scripts )
 */
class TT_ENQUEUE {

    /**
     * Use TT_ENQUEUE or not
     * @var boolean
     */
    public static $enabled = TRUE;
    /**
     * Directory to the javascript files relative to Theme Root
     * @var string
     */
    public static $js_dir = "/js";

    /**
     * Directory to the css files relative to the Theme Root
     * @var string
     */
    public static $css_dir = "/css";

    /**
     * Array with js files' paths to be enqueued
     * @var array
     */
    public static $js_files = array();

    /**
     * Array with css files' paths to be enqueued
     * @var array
     */
    public static $css_files = array();

    /**
     * Main javascript file to be enqueued last
     * @var string
     */
    public static $main_js = "options.js";

    /**
     * Main css file to be enqueued last
     * @var string
     */
    public static $main_css = "screen.css";

    /**
     * Google font to be always imported to page
     * @var array
     */
    public static $base_gfonts = array();

    /**
     * Google fonts that comes from font pickers in admin or just other fonts to be imported
     * @var array
     */
    public static $gfont_changer;

    /**
     * Whether to import the shim script for IE
     * @var boolean
     */
    public static $shim = TRUE;

    /**
     * Array that stores temp files (like cache)
     * @var array
     */
    private static $files_temp;

    /**
     * Adds the specific actions for enqueueing scripts
     */
    public static function init_enqueue(){
        add_action( "wp_enqueue_scripts", array( 'TT_ENQUEUE', 'load_scripts' ) , 11);
        add_action( "wp_enqueue_scripts", array( 'TT_ENQUEUE', 'load_stylesheet' ) ,10);
        add_action( 'admin_enqueue_scripts', array( 'TT_ENQUEUE','admin_load_scripts' ) , 11);
        add_action( 'admin_enqueue_scripts', array( 'TT_ENQUEUE','admin_load_stylesheet' ) ,10);
    }

    /**
     * Scans a directory for specific extension files and adds them to an temporary array
     * @param  string $dir
     * @param  string $ext
     * @return array
     */
    private static function scan_directory($dir,$ext){
        $scaned_files = array_diff( scandir($dir), array('..', '.') );
        foreach ($scaned_files as $key => $file) {
            if (is_dir($dir . '/' . $file))
                self::scan_directory($dir . '/' . $file,$ext);
            else{
                $file_parts = pathinfo($file);
                if(!empty($file_parts['extension']) && $file_parts['extension'] == $ext)
                    self::$files_temp[] = str_replace(TT_THEME_DIR,'',$dir . '/' . $file);
            }

        }
        return self::$files_temp;
    }

    /**
     * Gets the js and css files from respective folders
     * @return array
     */
    private static function get_scripts(){
        self::reset_temp();
        $js_files = self::$js_files = array_merge(self::$js_files ,self::scan_directory( TT_THEME_DIR . self::$js_dir , 'js') );
        self::reset_temp();
        $css_files = self::$css_files = array_merge(self::$css_files ,self::scan_directory( TT_THEME_DIR . self::$css_dir , 'css') );
        return array('js'=>$js_files,'css'=>$css_files);
    }

    /**
     * Enqueues scripts to the page.
     */
    public static function load_scripts(){
        if(!self::$enabled)
            return;
        global $is_IE;
        if ($is_IE)
            wp_enqueue_script('html5shim', "http://html5shim.googlecode.com/svn/trunk/html5.js");
        foreach (self::$js_files as $js_file) {
            if(strpos($js_file, '/admin/') === FALSE)
                if(basename($js_file) != self::$main_js)
                    if(strpos($js_file, '//') !== FALSE){     //if external link
                        wp_enqueue_script('tt-'.basename($js_file), $js_file, array('jquery'), false, true);
                    }elseif(strpos($js_file, '.js') === FALSE){ //if handler
                        wp_enqueue_script($js_file);
                    }else   //if local in /js folder
                        wp_enqueue_script('tt-' . basename($js_file), TT_THEME_URI . $js_file, array('jquery'), false, true);
        }
               
        wp_enqueue_script(self::$main_js, TT_THEME_URI . self::$js_dir .'/'. self::$main_js , array('jquery'), false, true);
        
        if ( is_singular() )
            wp_enqueue_script( "comment-reply" );
    }

    /**
     * Enqueues stylesheets to the page.
     */
    public static function load_stylesheet(){
        if(!self::$enabled)
            return;
        self::get_scripts();
        $protocol = is_ssl() ? 'https' : 'http';

        if (!empty(self::$base_gfonts)){
            foreach (self::$base_gfonts as $gfont)
                wp_enqueue_style( 'tt-base-font' . rand(), $protocol . $gfont);
        }

        if(!empty(self::$gfont_changer)){
            foreach(self::$gfont_changer as $font){
                $font = str_replace(' ', '+', $font);
                if($font !== '')
                    wp_enqueue_style( 'tt-custom-font-' . $font, "$protocol://fonts.googleapis.com/css?family=$font");
            }
        }
        foreach (self::$css_files as $css_file) {
            if(strpos($css_file, '/admin/') === FALSE)
                if(basename($css_file) != self::$main_css)
                    if(strpos($css_file, '//') !== FALSE){
                        wp_enqueue_style('tt-'.basename($css_file), $css_file);
                    }else
                        wp_enqueue_style('tt-'.basename($css_file), TT_THEME_URI . $css_file);
        }
        wp_enqueue_style( 'tt-main-style', TT_THEME_URI . self::$css_dir . "/" . self::$main_css );
        wp_enqueue_style( 'tt-theme-style', get_stylesheet_uri() );
    }

    /**
     * Enqueues scripts to the admin page.
     */
    public static function admin_load_scripts(){
        if(!self::$enabled)
            return;
        wp_enqueue_media();
        foreach (self::$js_files as $js_file) {
            if(strpos($js_file, '/admin/') !==FALSE )
                wp_enqueue_script('tt-'.basename($js_file), TT_THEME_URI . $js_file, array('jquery'), false, true);
        }
    }

    /**
     * Enqueues stylesheets to the admin page.
     */
    public static function admin_load_stylesheet(){
        if(!self::$enabled)
            return;
        self::get_scripts();
        foreach (self::$css_files as $css_file) {
            if(strpos($css_file, '/admin/') !==FALSE )
                wp_enqueue_style('tt-'.basename($css_file), TT_THEME_URI . $css_file);
        }
    }

    /**
     * Adds file/files to the scripts to be enqueued
     * @param string/array $js_files
     */
    public static function add_js($new_js_files){
        if(is_array($new_js_files))
            foreach ($new_js_files as $key => $new_js_file_link) {
                self::$js_files[] = $new_js_file_link;
            }
        else
            self::$js_files[] = $new_js_files;
    }

    /**
     * Adds file/files to the stylesheets to be enqueued
     * @param string/array $css_files
     */
    public static function add_css($new_css_files){
        if(is_array($new_css_files))
            foreach ($new_css_files as $key => $new_css_file_link) {
                self::$css_files[] = $new_css_file_link;
            }
        else
            self::$css_files[] = $new_css_files;
    }

    /**
     * Empties the temporary files array
     */
    private static function reset_temp(){
        self::$files_temp = array();
    }

}