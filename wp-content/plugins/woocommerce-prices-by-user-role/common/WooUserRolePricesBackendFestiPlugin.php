<?php

if (!class_exists('SettingsFestiPlugin')) {
    require_once dirname(__FILE__).'/backend/SettingsFestiPlugin.php';    
}

if (!class_exists("CsvWooProductsImporter")) {
    $fileName = 'CsvWooProductsImporter.php';
    require_once $this->_pluginPath.'common/backend/import/'.$fileName;
}

class WooUserRolePricesBackendFestiPlugin extends WooUserRolePricesFestiPlugin
{
    protected $_menuOptions = array(
        'settings' => "Settings",
        'importPrice'   => 'Import Products'
    );
    
    protected $uploadImportFields;
    
    protected $_defaultMenuOption = 'settings';
    
    protected $importer;

    protected function onInit()
    {
        $this->addActionListener('admin_menu', 'onAdminMenuAction', 100);
        
        $this->addActionListener(
            'wp_ajax_setUserIdForAjaxAction',
            'setUserIdForAjaxAction'
        );
        
        $this->addActionListener(
            'woocommerce_product_write_panel_tabs',
            'appendTabToAdminProductPanelAction')
        ;
        
        $this->addActionListener(
            'woocommerce_product_write_panels',
            'appendTabContentToAdminProductPanelAction')
        ;
        
        $this->addActionListener(
            'woocommerce_product_options_pricing',
            'appendFildsToSimpleOptionsAction')
        ;
        
        $this->addActionListener(
            'woocommerce_product_after_variable_attributes',
            'appendFildsToVariableOptionsAction',
            11,
            3
        );
        
        $this->addActionListener(
            'woocommerce_process_product_meta',
            'updateProductMetaOptionsAction',
            10,
            1
        );

        $this->addActionListener(
            'woocommerce_process_product_meta_simple',
            'updateSimpleProductMetaOptionsAction',
            10,
            1)
        ;
        
        $this->addActionListener(
            'woocommerce_save_product_variation',
            'updateVariableProductMetaOptionsAction',
            10,
            2
        );
        
        $this->addActionListener(
            'admin_print_styles', 
            'onInitCssForWoocommerceProductAdminPanelAction'
        );
        
        $this->addActionListener(
            'admin_print_scripts', 
            'onInitJsForWoocommerceProductAdminPanelAction'
        );
        
        $this->importer = new CsvWooProductsImporter($this);
    } // end onInit
    
    public function setUserIdForAjaxAction()
    {
        $result = array('status' => true);
        
        if (!isset($_POST['userId'])) {
            $result['status'] = false;
        } else {
            $_SESSION['userIdForAjax'] = $_POST['userId'];
        }
 
        wp_send_json($result);
        exit();
    } // end setUserIdForAjaxAction
    
    public function onInitCssForWoocommerceProductAdminPanelAction()
    {
        $this->onEnqueueCssFileAction(
            'festi-user-role-prices-product-admin-panel-styles',
            'product_admin_panel.css',
            array(),
            $this->_version
        );
        
        $this->onEnqueueCssFileAction(
            'festi-user-role-prices-product-admin-panel-tooltip',
            'tooltip.css',
            array(),
            $this->_version
        );
    } // end onInitCssForWoocommerceProductAdminPanelAction
    
    public function onInitJsForWoocommerceProductAdminPanelAction()
    {
        $this->onEnqueueJsFileAction('jquery');

        $this->onEnqueueJsFileAction(
            'festi-checkout-steps-wizard-tooltip',
            'tooltip.js',
            'jquery',
            $this->_version
        );
        
        $this->onEnqueueJsFileAction(
            'festi-user-role-prices-product-admin-panel-tooltip',
            'product_admin_panel.js',
            'jquery',
            $this->_version
        );
        
        $this->onEnqueueJsFileAction(
            'festi-user-role-prices-product-admin-add-new-order',
            'add_new_order.js',
            'jquery',
            $this->_version,
            true
        );
        
        $vars = array(
            'ajaxurl' => admin_url('admin-ajax.php'),
        );
        
        wp_localize_script(
            'festi-user-role-prices-product-admin-add-new-order',
            'fesiWooPriceRole',
            $vars
        );
    } // end onInitJsForWoocommerceProductAdminPanelAction
    
    public function appendTabToAdminProductPanelAction()
    {
        echo $this->fetch('product_tab.phtml');
    } // end appendTabToAdminProductPanel
    
    public function appendTabContentToAdminProductPanelAction()
    {
        $vars = array(
            'onlyRegisteredUsers' => $this->getValueFromProductMetaOption(
                'onlyRegisteredUsers'
            ),
            'hidePriceForUserRoles' => $this->getValueFromProductMetaOption(
                'hidePriceForUserRoles'
            ),
            'settings' => $this->getOptions('settings')
        );
        
        echo $this->fetch('product_tab_content.phtml', $vars);
    } // end appendTabContentToAdminProductPanelAction
    
    public function hasOnlyRegisteredUsersOptionInPluginSettings($settings)
    {
        return array_key_exists('onlyRegisteredUsers', $settings);
    } // end _hasOnlyRegisteredUsersOptionInPluginSettings
    
    public function hasRoleInHidePriceForUserRolesOption(
        $settings, $role
    )
    {
        return array_key_exists('hidePriceForUserRoles', $settings)
               && array_key_exists($role, $settings['hidePriceForUserRoles']);
    } // end hasOnlyRegisteredUsersOptionInPluginSettings
    
    public function getValueFromProductMetaOption($optionName)
    {
        $options = $this->getMetaOptionsForProduct(
            false,
            'festiUserRoleHidenPrices'
        );

        if (!$this->_hasItemInOptionsList($optionName, $options)) {
            return false;
        }
        
        return $options[$optionName];
    } //end getValueFromProductMetaOption
    
    private function _hasItemInOptionsList($optionName, $options)
    {
        return array_key_exists($optionName, $options);
    } //end _hasItemInOptionsList

    public function updateVariableProductMetaOptionsAction($variationId, $loop)
    {
        if (!$this->_hasVariableItemInRequest($loop)) {
            $_POST['festiVariableUserRolePrices'][$loop] = array();
        }
        
        $value = $_POST['festiVariableUserRolePrices'][$loop];
        
        $this->updateMetaOptions($variationId, $value, 'festiUserRolePrices');
    } // end updateVariableProductMetaOptionsAction
    
        
    public function updateProductMetaOptionsAction($postId)
    {
        if (!$this->_hasHidePriceProductOptionsInRequest()) {
            $_POST['festiUserRoleHidenPrices'] = array();
        }
        
        $value = json_encode($_POST['festiUserRoleHidenPrices']);

        $this->updateMetaOptions(
            $postId,
            $_POST['festiUserRoleHidenPrices'],
            'festiUserRoleHidenPrices'
        );
    } // end updateProductMetaOptionsAction
    
    private function _hasHidePriceProductOptionsInRequest()
    {
        return array_key_exists('festiUserRoleHidenPrices', $_POST)
               && !empty($_POST['festiUserRoleHidenPrices']);
    } // end _hasHidePriceProductOptionsInRequest
    
    public function getSelectorClassForDisplayEvent($class)
    {
        $selector = $class.'-visible';
        
        $options = $this->getOptions('settings');
                
        if (!isset($options[$class]) || $options[$class] == 'disable') {
            $selector.=  ' festi-user-role-prices-hidden ';
        }
        
        return $selector;
    } // end getSelectorClassForDisplayEvent
    
    private function _hasVariableItemInRequest($loop)
    {
        if (!array_key_exists('festiVariableUserRolePrices', $_POST)) {
            return false;
        }
        
        $items = $_POST['festiVariableUserRolePrices'];
        
        return array_key_exists($loop, $items);
    } // end _hasVariableItemInRequest
    
    public function updateSimpleProductMetaOptionsAction($postId)
    {
        $this->updateMetaOptions(
            $postId,
            $_POST['festiUserRolePrices'],
            'festiUserRolePrices'
        );
    } // end updateSimpleProductMetaOptionsAction
    
    public function updateMetaOptions($id, $value, $optionName)
    {
        $value = json_encode($value);
        
        update_post_meta(
            $id,
            $optionName,
            $value
        );
    } // end updateMetaOptions
    
    public function appendFildsToSimpleOptionsAction()
    {
        $roles = $this->getActiveRoles();
        
        if (!$roles) {
            return false;
        }
        
        $values = $this->getMetaOptionsForProduct(false, 'festiUserRolePrices');

        $symbol = get_woocommerce_currency_symbol();
        $decimalSeparator =$this->getWoocommerceDecimalSeparator();
                    
        foreach ($roles as $key => $value) {
            if (!array_key_exists($key, $values)  || $values[$key] == '') {
                $values[$key] = 0;
            }  
            
            $label = $value['name'].' ';
            $label .= __('Price', $this->_languageDomain).' ('.$symbol.')';

            $search = array('.', ',');
            $value = str_replace($search, $decimalSeparator, $values[$key]);
            
            $args = array(
                'name'  => 'festiUserRolePrices['.$key.']',
                'class' => 'short wc_input_price',
                'label' => $label,
                'id' => 'festiUserRolePrices_'.$key,
                'value' => $value
            );
            
            woocommerce_wp_text_input($args);  
        }
    } // end appendFildsToSimpleOptionsAction
    
    public function getWoocommerceDecimalSeparator()
    {
        $decimalSeparator = get_option('woocommerce_price_decimal_sep');
        
        return stripslashes($decimalSeparator);
    } // end getWoocommerceDecimalSeparator
    
    public function appendFildsToVariableOptionsAction($loop, $data, $item)
    {
        $value = '';
        
        $roles = $this->getActiveRoles();
        
        if (!$roles) {
            return false;
        }
        
        $symbol = get_woocommerce_currency_symbol();
        
        foreach ($roles as $key => $value) {
            $values = $this->getMetaOptionsForProduct(
                $item->ID,
                'festiUserRolePrices'
            );

            $label = $value['name'].' ';
            $label .= __('Price', $this->_languageDomain).' ('.$symbol.')';
            
            $vars = array(
                'loop'   => $loop,
                'data'   => $data,
                'label'  => $label,
                'key'    => $key,
                'values' => $values
            );   
            
            echo $this->fetch('variable_field.phtml', $vars);
        }
    } // end appendFildsToVariableOptionsAction
    
    public function getMetaOptionsForProduct($productId, $optionName)
    {
        if (!$productId) {
            $post = $this->getWordpressPostInstance();
            $productId = $post->ID;
        }
        
        $values = $this->getMetaOptions($productId, $optionName);

        if (!$values) {
            $values = array();
        }
        
        return $values;
    } // end getMetaOptionsForProduct
    
    public function &getWordpressPostInstance()
    {
        return $GLOBALS['post'];
    } // end getWoocommerceInstance

    public function onInstall($refresh = false, $settings = false)
    {        
        if (!$this->_fileSystem) {
            $this->_fileSystem = $this->getFileSystemInstance();
        }
        
        if ($this->_hasPermissionToCreateCacheFolder()) {
            $this->_fileSystem->mkdir($this->_pluginCachePath, 0777);
        }
        
        if (!$refresh) {
            $settings = $this->getOptions('settings');    
        }
              
        if (!$refresh && !$settings) {
            $this->_doInitDefaultOptions('settings');
            $this->updateOptions('roles', array());
        }    
    } // end onInstal
    
    private function _hasPermissionToCreateCacheFolder()
    {
        return ($this->_fileSystem->is_writable($this->_pluginPath)
               && !file_exists($this->_pluginCachePath));
    } // end _hasPermissionToCreateFolder
    
    public function getPluginTemplatePath($fileName)
    {
        return $this->_pluginTemplatePath.'backend/'.$fileName;
    } // end getPluginTemplatePath
    
    public function getPluginCssUrl($fileName) 
    {
        return $this->_pluginCssUrl.'backend/'.$fileName;
    } // end getPluginCssUrl
    
    public function getPluginJsUrl($fileName)
    {
        return $this->_pluginJsUrl.'backend/'.$fileName;
    } // end getPluginJsUrl
    
    protected function hasOptionPageInRequest()
    {
        return array_key_exists('tab', $_GET)
               && array_key_exists($_GET['tab'], $this->_menuOptions);
    } // end hasOptionPageInRequest
    
    public function _onFileSystemInstanceAction()
    {
        $this->_fileSystem = $this->getFileSystemInstance();
    } // end _onFileSystemInstanceAction
    
    public function onAdminMenuAction() 
    {
        $args = array(
             'parent'     => 'woocommerce',
             'title'      => __('Prices by User Role', $this->_languageDomain),
             'caption'    => __('Prices by User Role', $this->_languageDomain),
             'capability' => 'manage_options',
             'slug'       => 'festi-user-role-prices',
             'method'     => array(&$this, 'onDisplayOptionPage')  
        );

        $page = $this->doAppendSubMenu($args);
        
        $this->addActionListener(
            'admin_print_styles-'.$page, 
            'onInitCssAction'
        );
        
        $this->addActionListener(
            'admin_print_scripts-'.$page, 
            'onInitJsAction'
        );
        
        $this->addActionListener(
            'admin_head-'.$page,
            '_onFileSystemInstanceAction'
        );
    } // end onAdminMenuAction
    
    public function onInitCssAction()
    {
        $this->onEnqueueCssFileAction(
            'festi-user-role-prices-styles',
            'style.css',
            array(),
            $this->_version
        );
        
        $this->onEnqueueCssFileAction(
            'festi-admin-menu',
            'menu.css',
            array(),
            $this->_version
        );
        
        $this->onEnqueueCssFileAction(
            'festi-checkout-steps-wizard-colorpicker',
            'colorpicker.css',
            array(),
            $this->_version
        );
    } // end onInitCssAction
    
    public function onInitJsAction()
    {
        $this->onEnqueueJsFileAction('jquery');
        $this->onEnqueueJsFileAction(
            'festi-user-role-prices-colorpicker',
            'colorpicker.js',
            'jquery',
            $this->_version
        );
        $this->onEnqueueJsFileAction(
            'festi-user-role-prices-general',
            'general.js',
            'jquery',
            $this->_version
        );
    } // end onInitJsAction
    
    public function doAppendSubMenu($args = array())
    {
        $page = add_submenu_page(
            $args['parent'],
            $args['title'], 
            $args['caption'], 
            $args['capability'], 
            $args['slug'], 
            $args['method']
        );
        
        return $page;  
    } //end doAppendSubMenu
    
    public function onDisplayOptionPage()
    {
        if ($this->_isRefreshPlugin()) {
            $this->onRefreshPlugin();
        }
        
        if ($this->_isRefreshCompleted()) {
            $message = __(
                'Success refresh plugin',
                $this->_languageDomain
            );
            
            $this->displayUpdate($message);   
        }
        
        $this->_displayPluginErrors();
        
        $this->displayOptionsHeader();
        
        if ($this->_menuOptions) {         
            $menu = $this->fetch('menu.phtml');
            echo $menu;
        }
        
        $methodName = 'fetchOptionPage';
        
        if ($this->hasOptionPageInRequest()) {
            $postfix = $_GET['tab'];
        } else {
            $postfix = $this->_defaultMenuOption;
        }
        $methodName.= ucfirst($postfix);
        
        $method = array(&$this, $methodName);
        
        if (!is_callable($method)) {
            throw new Exception("Undefined method name: ".$methodName);
        }
        
        call_user_func_array($method, array());
    } // end onDisplayOptionPage
    
    public function fetchOptionPageImportPrice()
    {
        $this->importer->doAction();
    } // end fetchOptionPageSettings

    public function fetchOptionPageSettings()
    {
        $vars = array();

        if ($this->_isDeleteRole()) {
             try {
                $this->deleteRole();
                           
                $this->displayOptionPageUpdateMessage(
                    'Success deleted the role'
                );               
            } catch (Exception $e) {
                $message = $e->getMessage();
                $this->displayError($message);
            }
        }

        if ($this->isUpdateOptions('save')) {
            try {
                $this->_doUpdateOptions($_POST);
                           
                $this->displayOptionPageUpdateMessage(
                    'Success update settings'
                );               
            } catch (Exception $e) {
                $message = $e->getMessage();
                $this->displayError($message);
            }
        }
        
        if ($this->isUpdateOptions('new_role')) {
            try {
                $this->_doAppandNewRoleToWordpressRolesList();
    
                $this->displayOptionPageUpdateMessage(
                    'Success adding the role'
                );   
            } catch (Exception $e) {
                $message = $e->getMessage();
                $this->displayError($message);
            }
        }
        
        $options = $this->getOptions('settings');
        
        $vars['fieldset'] = $this->getOptionsFieldSet();        
        $vars['currentValues'] = $options;
        
        echo $this->fetch('settings_page.phtml', $vars);
    } // end fetchOptionPageSettings
    
    public function displayOptionsHeader()
    { 
        $vars = array(
            'content' => __(
                'Prices by User Role Options',
                $this->_languageDomain
            )
        );
        
        echo $this->fetch('options_header.phtml', $vars);
    } // end displayOptionsHeader
    
    public function deleteRole()
    {
        $roleKey = $_GET['delete_role'];
        
        $roles = $this->getUserRoles();
        
        if (!$this->_isRoleCreatedOfPlugin($roleKey)) {
             $message = __(
                'Unable to remove a role. Key does not exist.',
                $this->_languageDomain
            );
            throw new Exception($message);
        }
        
        $this->doDeleteWordpressUserRole($roleKey);
    } // end deleteRole
    
    private function _isRoleCreatedOfPlugin($key)
    {
        $roles = $this->getUserRoles();
        $pluginRoles = $this->getCreatedRolesOptionsOfPlugin();
        
        return array_key_exists($key, $roles)
               && array_key_exists($key, $pluginRoles);
    } // end _isRoleCreatedOfPlugin
    
    public function doDeleteWordpressUserRole($key)
    {
        $result = remove_role($key);
    } // end doDeleteWordpressUserRole
    
    private function _isDeleteRole()
    {
        return array_key_exists('delete_role', $_GET)
               && !empty($_GET['delete_role']);
    } // end _isDeleteRole
    
    private function _doAppandNewRoleToWordpressRolesList()
    {
        if (!$this->_hasNewRoleInRequest()) {
            $message = __(
                'You have not entered the name of the role',
                $this->_languageDomain
            );
            throw new Exception($message);
        }
        
        $key = $this->getKeyForNewRole();
        
        $this->doAddWordpressUserRole($key, $_POST['roleName']);
        
        $this->updateCreatedRolesOptions($key);
        
        if ($this->_hasActiveOptionForNewRoleInRequest()) {
            $this->updateListOfEnabledRoles($key);
        } 
    } // end _doAppandNewRoleToWordpressRolesList
    
    public function updateListOfEnabledRoles($key)
    {
        $settings = $this->getOptions('settings');
        
        $settings['roles'][$key] = true;
        
        $this->updateOptions('settings', $settings);
    } // end updatelistOfEnabledRoles
    
    public function updateCreatedRolesOptions($newKey)
    {
        $roleOptions = $this->getCreatedRolesOptionsOfPlugin();

        if (!$roleOptions) {
            $roleOptions = array();
        }
        
        $roleOptions[$newKey] = $_POST['roleName'];

        $this->updateOptions('roles', $roleOptions);
    } // end updateCreatedRolesOptions
    
    public function getCreatedRolesOptionsOfPlugin()
    {
        return $this->getOptions('roles');
    } // end getCreatedRolesOptionsOfPlugin
    
    public function doAddWordpressUserRole($key, $name)
    {
        $capabilities = array(
            'read' => true
        );
        
        $result = add_role($key, $name, $capabilities);
        
        if (!$result) {
            $message = __(
                'Unsuccessful attempt to create a role',
                $this->_languageDomain
            );
            throw new Exception($message);
        }
    } // end doAddWordpressUserRole
    
    public function getKeyForNewRole()
    {
        $key = $this->_cleaningExtraCharacters($_POST['roleName']);

        $keyName = $this->getAvailableKeyName($key);
       
        return $keyName;
    } // end getKeyForNewRole
    
    public function getAvailableKeyName($key)
    {
        $result = false;
        $sufix = '';
        $i = 0;
        
        $rols = $this->getUserRoles();
        
        while ($result === false) {
            $keyName = $key.$sufix;
            
            if (!$this->_hasKeyInExistingRoles($keyName, $rols)) {
                return $keyName;
            }

            $i++;
            $sufix = '_'.$i;
        }
    } // edn getAvailableKeyName
    
    private function _hasKeyInExistingRoles($keyName, $rols)
    {
        return array_key_exists($keyName, $rols);      
    } // end _hasKeyInExistingRoles
    
    private function _cleaningExtraCharacters($string)
    {
        $key = strtolower($string);
        $key = preg_replace('/[^a-z0-9\s]+/', '', $key);
        $key = trim($key);
        $key = preg_replace('/\s+/', '_', $key);
        
        return $key;
    } // end _cleaningExtraCharacters
    
    private function _hasNewRoleInRequest()
    {
        return array_key_exists('roleName', $_POST)
               && !empty($_POST['roleName']);
    } // end _hasNewRoleInRequest
    
    private function _hasActiveOptionForNewRoleInRequest()
    {
        return array_key_exists('active', $_POST);
    } // end _hasActiveOptionForNewRoleInRequest
    
    public function displayOptionPageUpdateMessage($text)
    {
        $message = __(
            $text,
            $this->_languageDomain
        );
            
        $this->displayUpdate($message);   
    } // end displayOptionPageUpdateMessage
    
    public function getOptionsFieldSet()
    {
        $fildset = array(
            'general' => array(),
        );
        
        $settings = $this->loadSettings();
        
        if ($settings) {
            foreach ($settings as $ident => &$item) {
                if (array_key_exists('fieldsetKey', $item)) {
                   $key = $item['fieldsetKey'];
                   $fildset[$key]['filds'][$ident] = $settings[$ident];
                }
            }
            unset($item);
        }
        
        return $fildset;
    } // end getOptionsFieldSet
    
    public function loadSettings()
    {
        $settings = new SettingsFestiPlugin($this->_languageDomain);
        
        $options = $settings->get();

        $values = $this->getOptions('settings');
        if ($values) {
            foreach ($options as $ident => &$item) {
                if (array_key_exists($ident, $values)) {
                    $item['value'] = $values[$ident];
                }
            }
            unset($item);
        }
        
        return $options;
    } // end loadSettings
    
    private function _displayPluginErrors()
    {        
        $caheFolderErorr = $this->_detectTheCacheFolderAccessErrors();

        if ($caheFolderErorr) {
            echo $this->fetch('refresh.phtml');
        }
    } // end _displayPluginErrors
    
    private function _isRefreshPlugin()
    {
        return array_key_exists('refresh_plugin', $_GET);
    } // end _isRefreshPlugin
    
    public function onRefreshPlugin()
    {
        $this->onInstall(true);
    } // end onRefreshPlugin
    
    private function _doInitDefaultOptions($option, $instance = NULL)
    {
        $methodName = $this->getMethodName('load', $option);
        
        if (is_null($instance)) {
            $instance = $this;
        }

        $method = array($instance, $methodName);
        
        if (!is_callable($method)) {
            throw new Exception("Undefined method name: ".$methodName);
        }

        $options = call_user_func_array($method, array());
        foreach ($options as $ident => &$item) {
            if ($this->_hasDefaultValueInItem($item)) {
                $values[$ident] = $item['default'];
            }
        }
        unset($item);
        
        $this->updateOptions($option, $values);
    } // end _doInitDefaultOptions
    
    private function _hasDefaultValueInItem($item)
    {
        return isset($item['default']);
    } //end _hasDefaultValueInItem
    
    public function getMethodName($prefix, $option)
    {
        $option = explode('_', $option);
        
        $option = array_map('ucfirst', $option);
        
        $option = implode('', $option);
        
        $methodName = $prefix.$option;
        
        return $methodName;
    } // end getMethodName
    
    private function _isRefreshCompleted()
    {
        return array_key_exists('refresh_completed', $_GET);
    } // end _isRefreshCompleted
    
    private function _detectTheCacheFolderAccessErrors()
    {
        if (!$this->_fileSystem->is_writable($this->_pluginCachePath)) {

            $message = __(
                "Caching does not work! ",
                $this->_languageDomain
            );
            
            $message .= __(
                "You don't have permission to access: ",
                $this->_languageDomain
            );
            
            $path = $this->_pluginCachePath;
            
            if (!$this->_fileSystem->exists($path)) {
                $path = $this->_pluginPath;
            }
            
            $message .= $path;
            //$message .= $this->fetch('manual_url.phtml');
            
            $this->displayError($message);
            
            return true;
        }
        
        return false;
    } // end _detectTheCacheFolderAccessErrors
    
    public function isUpdateOptions($action)
    {
        return array_key_exists('__action', $_POST)
               && $_POST['__action'] == $action;
    } // end isUpdateOptions
    
    private function _doUpdateOptions($newSettings = array())
    {
        $this->updateOptions('settings', $newSettings);
    } // end _doUpdateOptions
}