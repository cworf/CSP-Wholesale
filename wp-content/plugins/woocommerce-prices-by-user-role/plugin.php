<?php
/**
 * Plugin Name: WooCommerce Prices By User Role
 * Plugin URI: http://festi.io/app/woocommerce-prices-by-user-role/
 * Description:  With this plugin  for WooCommerce  Products can be offered different prices for each customer group. Also you can do only product catalog without prices and show custom notification instead price.
 * Version: 2.15
 * Author: Festi 
 * Author URI: http://festi.io/
 * Copyright 2014  Festi  http://festi.io/
 */
 
if (!class_exists('FestiPlugin')) {
    require_once dirname(__FILE__).'/common/FestiPlugin.php';
}

class WooUserRolePricesFestiPlugin extends FestiPlugin
{
    public $_languageDomain = 'festi_user_role_prices';
    protected $_optionsPrefix = 'festi_user_role_prices_';
    public $_version = '2.15';
    
    protected function onInit()
    {
        $this->addActionListener('plugins_loaded', 'onLanguagesInitAction');

        if ($this->_isWoocommercePluginNotActiveWhenFestiPluginActive()) {
            $this->addActionListener(
                'admin_notices',
                'onDisplayInfoAboutDisabledWoocommerceAction' 
            );
            
            return false;
        }
        
        parent::onInit();
        
        if (defined('DOING_AJAX')) {
            $this->onBackendInit();
        }
    } // end onInit
    
    public function onInstall()
    {
        if (!$this->_isWoocommercePluginActive()) {
            $message = 'WooCommerce not active or not installed.';
            $this->displayError($message);
            exit();
        } 

        $plugin = $this->onBackendInit();
        
        $plugin->onInstall();
    } // end onInstall
    
    public function onBackendInit()
    {
        $fileName = 'WooUserRolePricesBackendFestiPlugin.php';
        require_once $this->_pluginPath.'common/'.$fileName;
        $backend = new WooUserRolePricesBackendFestiPlugin(__FILE__);
        return $backend;
    } // end onBackendInit
    
    protected function onFrontendInit()
    {
        $fileName = 'WooUserRolePricesFrontendFestiPlugin.php';
        require_once $this->_pluginPath.'common/'.$fileName;
        $frontend = new WooUserRolePricesFrontendFestiPlugin(__FILE__);
        return $frontend;
    } // end onFrontendIn
    
    private function _isWoocommercePluginNotActiveWhenFestiPluginActive()
    {
        return $this->_isFestiPluginActive()
               && !$this->_isWoocommercePluginActive();
    } // end _isWoocommercePluginNotActiveWhenFestiPluginActive
    
    private function _isFestiPluginActive()
    {        
        return $this->isPluginActive('woocommerce-woocartpro/plugin.php'); 
    } // end _isFestiPluginActive
    
    private function _isWoocommercePluginActive()
    {        
        return $this->isPluginActive('woocommerce/woocommerce.php');
    } // end _isWoocommercePluginActive
    
    public function onLanguagesInitAction()
    {
        load_plugin_textdomain(
            $this->_languageDomain,
            false,
            $this->_pluginLanguagesPath
        );
    } // end onLanguagesInitAction
    
    public function getMetaOptions($id, $optionName)
    {
        $value = get_post_meta($id, $optionName, true);
        
        if (!$value) {
            return false;
        }
        
        if (is_array($value)) {
            return $value;
        }
        
        $value = json_decode($value, true);
        
        return $value;
    } // end getMetaOptions
    
    public function getActiveRoles()
    {
        $options = $this->getOptions('settings');
        
        if (!$this->_hasActiveRoleInOptions($options)) {
            return false;
        }

        $wordpressRoles = $this->getUserRoles();
        
        $diff = array_diff_key($wordpressRoles, $options['roles']);
        $roles = array_diff_key($wordpressRoles, $diff);
        
        return $roles;
    } // end getActiveRoles
    
    private function _hasActiveRoleInOptions($options)
    {
        return array_key_exists('roles', $options);
    } // end _hasActiveRoleInOptions
    
    public function getUserRoles()
    {
        if (!$this->_hasRolesInGlobals()) {
            return false;
        }
        
        $roles = $GLOBALS['wp_roles'];

        return $roles->roles; 
    } // getUserRoles
    
    public function getUserRoleNameByRoleKey($key)
    {
        $roles = $this->getUserRoles();

        return $roles[$key]['name']; 
    } // getUserRoleNameByRoleKey
    
    private function _hasRolesInGlobals()
    {
        return array_key_exists('wp_roles', $GLOBALS);   
    } // end _hasWordpessPostTypeInGlobals
    
    public function onDisplayInfoAboutDisabledWoocommerceAction()
    {        
        $message = 'WooCommerce Prices By User Role: ';
        $message .= 'WooCommerce not active or not installed.';
        $this->displayError($message);
    } //end onDisplayInfoAboutDisabledWoocommerceAction
}

$className = 'wooUserRolePricesFestiPlugin';
$GLOBALS[$className] = new WooUserRolePricesFestiPlugin(__FILE__);
