<?php

class TT_Load extends TeslaFramework{

    function __construct() {

    }

    /**
     * Includes a helper file from Framework_root/helpers/
     * @param  string $helper file name
     */
    function helper( $helper ) {
        if ( $this->helper_exists( $helper ) ) {
            require_once TTF . '/helpers/' . $helper . '.php';
        }
        else
            exit( "Helper $helper was not found" );
    }

    /**
     * Checks if helper file exists
     * @param  string $helper file name
     * @return boolean        Whether found the file or not
     */
    function helper_exists( $helper ) {
        if ( file_exists( TTF . '/helpers/' . $helper . '.php' ) )
            return TRUE;
        else
            return FALSE;
    }

    /**
     * Checks if a view exists in TeslaFramework root/views/ or Theme_Root/theme_config/
     * @param  string $name   file name
     * @param  boolean $config if false looks for the view in TeslaFramework_Root/vies/ else in Theme_Root/theme_config/
     * @return boolean         If found the view or not
     */
    function view_exists( $name, $config = NULL ) {
        if($config === NULL)
            $result = file_exists( TT_STYLE_DIR . '/tesla_framework/views/' . $name . '.php' ) || file_exists( TT_THEME_DIR . '/tesla_framework/views/' . $name . '.php' );
        else
            $result = file_exists( TT_STYLE_DIR . '/theme_config/'.$name.'.php' ) || file_exists( TT_THEME_DIR . '/theme_config/'.$name.'.php' );
        if ( $result )
            return TRUE;
        else
            return FALSE;
    }

    /**
     * Includes a view file from TeslaFramework root/views/ or Theme_Root/theme_config/
     * @param  string  $_name    name of the view file
     * @param  array  $_data    array of the variables to be sent to the view
     * @param  boolean $__return if false will echo the view else will return it (f or shortcodes use TRUE !!! )
     * @param  boolean  $config   if false looks for the view in TeslaFramework_Root/vies/ else in Theme_Root/theme_config/
     * @return html            If $__return is set to true , returns the view content
     */
    function view( $_name, $_data = NULL, $__return = FALSE, $config = NULL ) {
        $_name = strtolower( $_name );
        if ( !$this->view_exists( $_name, $config ) )
            exit( 'View not found: ' . $_name );
        if ( $_data !== NULL && count( $_data ) > 0 )
            foreach ( $_data as $_name_var => $_value )
                ${$_name_var} = $_value;
        ob_start();
        if($config === NULL)
            require locate_template('tesla_framework/views/'.$_name.'.php');
        else
            require locate_template('theme_config/'.$_name.'.php');
        $buffer = ob_get_clean();
        if ( $__return === TRUE )
            return $buffer;
        else
            echo $buffer;
    }
}