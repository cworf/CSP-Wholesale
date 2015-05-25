<?php

class WooUserRolePricesFrontendFestiPlugin extends WooUserRolePricesFestiPlugin
{
    protected $settings;
    protected $userRole;
    protected $textInsteadPrices;
    protected $mainProductId;
    protected $eachProductId = 0;
    protected $userPrice = 0;
    
    protected function onInit()
    {
        if (!$this->_isSesionStarted()) {
            session_start();
        }

        $this->settings = $this->getOptions('settings');

        $this->addActionListener(
            'woocommerce_init',
            'onInitFiltersAction',
            10,
            2
        );
    } // end onInit
    
    private function _isSesionStarted()
    {
        if (php_sapi_name() !== 'cli') {
            if (version_compare(phpversion(), '5.4.0', '>=')) {
                return session_status() === PHP_SESSION_ACTIVE;
            } else {
                return session_id() === '';
            }
        }
        return false;
    } // end _isSesionStarted
    
    public function onInitFiltersAction()
    {
        $options = $this->settings;

        $this->userRole = $this->getUserRole();
        
        if ($this->_isEnabledHideAddToCartButtonOption()) {
            $this->removeAddToCartButtons();
        }
        
        if (!$this->_hasAvailableRoleToViewPrices()) {
            $this->addFilterListener(
                'woocommerce_get_price_html',
                'onDisplayPriceContentInAllProductFilter',
                10,
                2
            );
            
            $this->removeAddToCartButtons();
        } else {  
            $this->addFilterListener(
                'woocommerce_get_price_html',
                'onDisplayPriceContentForSingleProductFilter',
                10,
                2
            );

            $this->addFilterListener(
                'woocommerce_loop_add_to_cart_link',
                'removeAddToCartButtonInSingleProductFilter',
                10,
                2
            );
        }

        $this->addFilterListener(
            'woocommerce_get_price',
            'onDisplayPriceFilter',
            10,
            2
        );
        
        $this->addFilterListener(
            'woocommerce_variation_price_html',
            'onDisplayPriceContentForVariableProductFilter',
            10,
            2
        );
        
        $this->addFilterListener(
            'woocommerce_variation_sale_price_html',
            'onDisplayPriceContentForVariableProductFilter',
            10,
            2
        );
        
        
        $this->addFilterListener(
            'woocommerce_grouped_price_html',
            'onDisplayGroupedProductPriceFilter',
            10,
            2
        );
        
        $this->addFilterListener(
            'woocommerce_variable_sale_price_html',
            'onDisplayGroupedProductPriceFilter',
            10,
            2
        );
        
        $this->addFilterListener(
            'woocommerce_cart_total',
            'onDisplayCustomerTotalSavingsAction',
            10,
            2
        );
        
        $this->addActionListener('wp_print_styles', 'onInitCssAction');
    } // end onInitFiltersAction
    
    public function getPluginJsUrl($fileName)
    {
        return $this->_pluginJsUrl.'frontend/'.$fileName;
    } // end getPluginJsUrl

    public function onDisplayCustomerTotalSavingsAction($total)
    {
        if (!$this->_hasOptionInSettings('showCustomerSavings')
            || !$this->_isEnabledPageInCustomerSavingsOption('cartTotal')
            || !$this->_isRegisteredUser()) {
            return $total;
        }
        
        $woocommerce = $this->getWoocommerceInstance();
        
        $total = $woocommerce->cart->total;

        $totalDiff = $total - $woocommerce->cart->subtotal;
        
        $userTotal = $total;
        
        $retailTotal = $this->getRetailTotal($woocommerce) + $totalDiff;
        
        if (!$this->_isRetailTotalMoreThanUserTotal($retailTotal, $userTotal)) {
            return $total;
        }
        
        $totalSavings = $this->getTotalSavings($retailTotal, $userTotal);

        $vars = array(
            'regularPrice' => $this->fetchPrice($retailTotal),
            'userPrice' => $this->fetchPrice($userTotal),
            'userDiscount' => $this->fetchTotalSavings($totalSavings)
        );
        
        return $this->fetch('customer_total_savings_price.phtml', $vars);
    } // end onDisplayCustomerTotalSavingsAction
    
    public function fetchUserTotal($woocommerce)
    {
        return $woocommerce->cart->get_cart_total();
    } // end fetchRegularPrice
    
    public function getRetailTotal($woocommerce)
    {
        $products = $woocommerce->cart->cart_contents;

        $total = 0;
        
        foreach ($products as $key => $product) {
            if ($this->_isVariableProduct($product)) {
                $productId = $product['variation_id'];
            } else {
                $productId = $product['product_id'];
            }

            $price = $this->getRegularPrice($productId);
            $total += $price * $product['quantity'];
        }
        
        return $total;
    } // end getRetailTotal
    
    private function _isVariableProduct($product)
    {
        return array_key_exists('variation_id', $product)
               && !empty($product['variation_id']);
    } // end _isVariableProduct
    
    protected function getTotalSavings($retailTotal, $userTotal)
    {        
        $savings = round(100 - ($userTotal/$retailTotal * 100), 2);
        
        return $savings;
    } // end getTotalSavings
    
    private function _isRetailTotalMoreThanUserTotal($retailTotal, $userTotal)
    {
        return $retailTotal > $userTotal;
    } // end _isRetailTotalMoreThanUserTotal
    
    public function fetchTotalSavings($totalSavings)
    {
        $vars = array(
            'discount' => $totalSavings
        );

        return $this->fetch('discount.phtml', $vars);
    } // end fetchTotalSavings
    
    public function getPluginCssUrl($path) 
    {
        return $this->_pluginUrl.$path;
    } // end getPluginCssUrl
    
    private function _hasOptionInSettings($option)
    {
        return array_key_exists($option, $this->settings);
    } // end _hasOptionInSettings
    
    public function onInitCssAction()
    {
        $this->addActionListener(
            'wp_head',
            'appendCssToHeaderForCustomerSavingsCustomize'
        );

        $this->onEnqueueCssFileAction(
            'festi-user-role-prices-styles',
            'static/styles/frontend/style.css',
            array(),
            $this->_version
        );
    } // end onInitCssAction
    
    
    public function appendCssToHeaderForCustomerSavingsCustomize()
    {
        if (!$this->_hasOptionInSettings('showCustomerSavings')) {
            return false;
        }
        
        $vars = array(
            'settings' => $this->settings,
        );

        echo $this->fetch('customer_savings_customize_style.phtml', $vars);
    } // end appendCssToHeaderForPriceCustomize
    
    public  function onDisplayPriceContentForVariableProductFilter(
        $content, $product
    )
    {
        $hasAvaliableRole = $this->_hasAvailableRoleToViewPricesInEachProduct(
            $product->id
        );
        
        if (!$this->_hasAvailableRoleToViewPrices()
            || !$hasAvaliableRole) {
            return '';
        }
        
        if (!$this->_hasIdInProductObject($product)) {
            return $content;    
        }
        
        $result = $this->_hasConditionsForDisplayCustomerSavingsInProduct(
            $product
        );
        
        if (!$result) {
            return $content;
        }
        
        $vars = array(
            'regularPrice' => $this->fetchRegularPrice(
                $product->variation_id,
                $product
            ),
            'userPrice' => $this->fetchUserPrice($product),
            'userDiscount' => $this->fetchUserDiscount($product),
            'priceSuffix' => $product->get_price_suffix()
        );
        
        return $this->fetch('customer_product_savings_price.phtml', $vars);
    } // end woocommerce_variation_price_html
    
    
    private function _isEnabledHideAddToCartButtonOption()
    {
        return (!$this->_isRegisteredUser() 
                  && $this->_hasHideAddToCartButtonOptionInSettings())
               || ($this->_isRegisteredUser() 
                  && ($this->_hasHideAddToCartButtonOptionForUserRole()
                     || $this->_hasHidePriceOptionForUserRole()));
    } // end _isEnabledHideAddToCartButtonOption
    
    public function onDisplayPriceContentForSingleProductFilter($content, $item)
    {
        if(!$this->_hasIdInProductObject($item)) {
            return $content;    
        }
     
        if ($this->_isMainProductInSimpleProductPage($item)) {
            $mainProduct = true;
        } else {
            $mainProduct = false;
        }

        if (
            !$this->_hasAvailableRoleToViewPricesInEachProduct($item->id)) {
            $this->removeAddToCartButtons(true, $mainProduct);
            return $this->fetchContentInsteadOfPrices();
        }
        
        $result = $this->_hasConditionsForDisplayCustomerSavingsInProduct(
            $item
        );

        if (!$result) {
            return $content;
        }

        $vars = array(
            'regularPrice' => $this->fetchRegularPrice($item->id, $item),
            'userPrice' => $this->fetchUserPrice($item),
            'userDiscount' => $this->fetchUserDiscount($item),
            'priceSuffix' => $item->get_price_suffix()
        );
        
        return $this->fetch('customer_product_savings_price.phtml', $vars);
    } //end onDisplayPriceContentForSingleProductFilter
    
    private function _hasConditionsForDisplayCustomerSavingsInProduct(
        $product
    )
    {
        return $this->_hasOptionInSettings('showCustomerSavings')
               && $this->_isRegisteredUser()
               && $this->_isAllowedPageToDisplayCustomerSavings($product)
               && $this->_isAvaliableProductTypeToDispalySavings($product)
               && $this->_isAvaliablePricesToDisplayCustomerSavings($product);
    } // end _hasConditionsForDisplayCustomerSavingsInProduct
    
    private function _isAllowedPageToDisplayCustomerSavings($product)
    {
        $isEnabledProductPage = $this->_isEnabledPageInCustomerSavingsOption(
            'product'
        );
        
        $isEnabledArchivePage = $this->_isEnabledPageInCustomerSavingsOption(
            'archive'
        );
        
        $mainProduct = $this->_isMainProductInSimpleProductPage($product);
        
        $isWooProductPage = $this->isWoocommerceProductPage();

        if ($isWooProductPage && $isEnabledProductPage && $mainProduct) {
            return true;
        }

        if (!$isWooProductPage && $isEnabledArchivePage) {
            return true;
        }

        return false;
    } // end _isAllowedPageToDisplayCustomerSavings
    
    private function _isEnabledPageInCustomerSavingsOption($page)
    {
        return in_array($page, $this->settings['showCustomerSavings']);
    } // end _isEnabledPageInCustomerSavingsOption
    
    private function _isAvaliableProductTypeToDispalySavings($product)
    {
        $result = $this->_hasTypeInAvaliableProductTypes($product);

        if (!$product->post->post_parent) {
            return $result;
        }

        $product = get_product($product->post->post_parent);
        
        if (!$product) {
            return false;
        }

        $result = $this->_hasTypeInAvaliableProductTypes($product);
        
        return $result;
    } // end _isAvaliableProductTypeToDispalyCustomerSavings
    
    private function _hasTypeInAvaliableProductTypes($product)
    {
        $avaliableTypes = array(
            'simple',
            'variation',
        );
        
        if (!isset($product->product_type)) {
            return false;
        }
        
        return in_array($product->product_type, $avaliableTypes);
    } // end _hasTypeInAvaliableProductTypes

    private function _isAvaliablePricesToDisplayCustomerSavings($product)
    {        
        if ($product->is_type('simple') || $product->is_type('variable')) {
            $regularPrice = $this->getRegularPrice($product->id);
        } elseif ($product->is_type('variation')) {
            $regularPrice = $this->getRegularPrice($product->variation_id);
        }
        
        $regularPrice = $this->getPriceTax($product, $regularPrice, false);
        
        $userPrice = $this->userPrice;
        
        $userPrice = $this->_getPriceWithFixedFloat($userPrice);

        $userPrice = $this->getPriceTax($product, $userPrice, false);
        
        
        if (!$userPrice) {
            return false;
        }

        return $userPrice < $regularPrice;
    } // end _isAvaliablePricesToDisplayCustomerSavings
    
    private function _isMainProductInSimpleProductPage($product)
    {
        if (!$this->mainProductId) {
            $this->mainProductId = get_the_ID();
        }
        
        return $product->id == $this->mainProductId;
    } // end _isMainProductInSimpleProductPage
    
    private function isWoocommerceProductPage()
    {
        return is_product();
    } // end isWoocommerceProductPage
    
    public function fetchPrice($price, $formatPrice = true)
    {
        if ($formatPrice) {
            $price = wc_price($price);
        }

        $vars = array(
            'price' => $price
        );
        
        return $this->fetch('price.phtml', $vars);
    } // end fetchRegularPrice
    
    public function fetchRegularPrice($productId, $product)
    {
        $price = $this->getRegularPrice($productId);
      
        $price = $this->_getPriceWithFixedFloat($price);

        $price = $this->getPriceTax($product, $price);

        return $this->fetchPrice($price, false);
    } // end fetchRegularPrice
    
    public function fetchUserPrice($product)
    {
        $userPrice = $this->userPrice;
    
        $price = $this->_getPriceWithFixedFloat($userPrice);
        
        $price = $this->getPriceTax($product, $price);

        return $this->fetchPrice($price, false);
    } // end fetchUserPrice
    
    public function getRegularPrice($productId)
    {
        $data = get_post_meta($productId, '_regular_price');
        
        if (!$data) {
            return 0;
        }

        return $data[0];
    } // end getRegularPrice
    
    public function fetchUserDiscount($product)
    {
        if ($product->is_type('simple') || $product->is_type('variable')) {
            $regularPrice = $this->getRegularPrice($product->id);
        } elseif ($product->is_type('variation')) {
            $regularPrice = $this->getRegularPrice($product->variation_id);
        }
        
        $regularPrice = $this->getPriceTax($product, $regularPrice, false);
        
        $userPrice = $this->userPrice;
        
        $userPrice = $this->_getPriceWithFixedFloat($userPrice);

        $userPrice = $this->getPriceTax($product, $userPrice, false);
        
        if(!$regularPrice || !$userPrice) {
            return 0;
        }

        $discount = round(100 - ($userPrice/$regularPrice * 100), 2);
        $vars = array(
            'discount' => $discount
        );

        return $this->fetch('discount.phtml', $vars);
    } // end fetchRegularPrice

    private function _hasIdInProductObject($product)
    {
        return isset($product->id) || isset($product->variation_id);   
    } // end  _hasIdInProductObject
    
    private function _isNotAvailableForDisplayAddToCartButtons()
    {
        return $this->_hasHideAddToCartButtonOptionInSettings()
               || !$this->_hasAvailableRoleToViewPrices();
    } //end _isNotAAvailableForDisplayAddToCartButtons
    
    public function onDisplayPriceContentInAllProductFilter()
    {
        return $this->fetchContentInsteadOfPrices();
    } //end onDisplayPriceContentInAllProductFilter
    
    public function removeAddToCartButtonInSingleProductFilter($button, $item)
    {
        if (!$this->_hasAvailableRoleToViewPricesInEachProduct($item->id)) {
            return '';
        }

        return $button;
    } //end removeAddToCartButtonInSingleProductFilter
    
    private function _hasHideAddToCartButtonOptionInSettings()
    {
        return array_key_exists('hideAddToCartButton', $this->settings);
    } //end _hasHideAddToCartButtonOptionInSettings
    
    public function onHideAddToCartButtonsAction()
    {
        echo $this->fetch('hide_add_to_buttons.phtml');
    } // end onHideAddToCartButtonsAction
    
    public function removeAddToCartButtons(
        $productPage = false, $mainProduct = false
    )
    {
        if ($productPage && $mainProduct
            || $this->_isEnabledHideAddToCartButtonOption()) {
            $this->addActionListener(
                'wp_footer',
                'onHideAddToCartButtonsAction'
            );
        }

        if ($productPage) {
            return false;
        }
        
        $this->removeActionListener(
            'woocommerce_after_shop_loop_item',
            'woocommerce_template_loop_add_to_cart',
            10
        );
    } //end removeAddToCartButtons
    
    
    public function removeActionListener($tag, $function, $priority)
    {
        remove_action($tag, $function, $priority);
    } //end removeActionListener
    
    public function getPluginTemplatePath($fileName)
    {
        return $this->_pluginTemplatePath.'frontend/'.$fileName;
    } // end getPluginTemplatePath

    public function fetchContentInsteadOfPrices()
    {
        $vars = array(
            'text' => $this->textInsteadPrices
        );
        
        return $this->fetch('custom_text.phtml', $vars);
    } // end fetchContentInsteadOfPrices

    private function _isActiveOnlyRegisteredUsersMode()
    {
        return $this->_hasOnlyRegisteredUsersInGeneralSettings()
               || $this->_hasOnlyRegisteredUsersInProductSettings();
    } // end _isActiveOnlyRegisteredUsersMode
    
    private function _hasOnlyRegisteredUsersInGeneralSettings()
    {
        return array_key_exists('onlyRegisteredUsers', $this->settings);
    } // end _hasOnlyRegisteredUsersInGeneralSettings
    
    private function _hasOnlyRegisteredUsersInProductSettings()
    {
        if (!$this->eachProductId) {
            return false;
        }
        
        $options = $this->getMetaOptions(
            $this->eachProductId,
            'festiUserRoleHidenPrices'
        );
        
        if (!$options) {
            return false;
        }

        return array_key_exists(
            'onlyRegisteredUsers',
            $options
        );
    } // end _hasOnlyRegisteredUsersInProductSettings

    private function _hasAvailableRoleToViewPrices()
    {
        if (!$this->_isAvailableForUnregisteredUsers()) {
            $this->setValueForContentInsteadOfPrices('textForUnregisterUsers');
            return false;
        }

        if (!$this->_isAvailableForRegisteredUsers()) {
            $this->setValueForContentInsteadOfPrices('textForRegisterUsers');
            return false;
        }

        return true;
    } // end _hasAvailableRoleToViewPrices
    
    private function _hasAvailableRoleToViewPricesInEachProduct($productId)
    {
        $this->eachProductId = $productId;

        return $this->_hasAvailableRoleToViewPrices();
    } // end _hasAvailableRoleToViewPrices
    
    public function setValueForContentInsteadOfPrices($optionName)
    {
        $this->textInsteadPrices = $this->settings[$optionName];
    } // end getContentInsteadOfPrices
    
    private function _isAvailableForUnregisteredUsers()
    {
        return $this->_isRegisteredUser() || (!$this->_isRegisteredUser()
               && !$this->_isActiveOnlyRegisteredUsersMode());
    } //end _isAvailableForUnregisteredUsers
    
    private function _isAvailableForRegisteredUsers()
    {
        return !$this->_isRegisteredUser() || ($this->_isRegisteredUser()
               && !$this->_hasHidePriceOptionForUserRole());
    } //end _isAvailableForRegisteredUsers
    
    private function _hasHidePriceOptionForUserRole()
    {
        return $this->_hasHidePriceOptionForRoleInGeneralSettings()
               || $this->_hasHidePriceOptionForRoleInProductSettings();
    } //end _hasHidePriceOptionForUserRole
    
    private function _hasHidePriceOptionForRoleInGeneralSettings()
    {   
        return array_key_exists('hidePriceForUserRoles', $this->settings)
               && array_key_exists(
                    $this->userRole,
                    $this->settings['hidePriceForUserRoles']
               );
    } // end _hasHidePriceOptionForRoleInGeneralSettings
    
    private function _hasHidePriceOptionForRoleInProductSettings()
    {
        if (!$this->eachProductId) {
            return false;
        }
        
        $options = $this->getMetaOptions(
            $this->eachProductId,
            'festiUserRoleHidenPrices'
        );
        
        if (!$options) {
            return false;
        }
        
        return $options && array_key_exists(
            $this->userRole,
            $options['hidePriceForUserRoles']
        );
    } // end _hasHidePriceOptionForRoleInProductSettings
    
    private function _hasHideAddToCartButtonOptionForUserRole()
    {
        $key = 'hideAddToCartButtonForUserRoles';
        
        return array_key_exists($key, $this->settings)
               && array_key_exists($this->userRole, $this->settings[$key]);
    } //end _hasHideAddToCartButtonOptionForUserRole

    private function _isRegisteredUser()
    {
        return $this->userRole;
    } // end _isRegisteredUser
    
    public function getUserRole()
    {
        $userId = $this->getUserId();
        
        if (!$userId) {
            return false;    
        }
        
        $userData = get_userdata($userId);

        return $userData->roles[0];
    } // end getUserRole
    
    public function getUserId()
    {
        if (defined('DOING_AJAX') && $this->_hasUserIdInSessionArray()) {
            return $_SESSION['userIdForAjax'];
        }

        $userId = get_current_user_id();
        
        return $userId;
    } // end getUserId
    
    private function _hasUserIdInSessionArray()
    {
        return isset($_SESSION['userIdForAjax']);
    } // end _hasUserIdInSessionArray
    
    public function onDisplayPriceFilter($price, $product)
    {
        $userRole = $this->userRole;
        $this->userPrice = $price;
        
        if (!$userRole) {
            return $this->userPrice;
        }
        
        if ($this->_hasDiscountOrMarkUpForUserRoleInGeneralOptions($userRole)) {
            $this->userPrice = $this->getPriceWithDiscountOrMarkUp($product);
            return $this->_getPriceWithFixedFloat($this->userPrice);
        }

        if (!$this->_hasUserRoleInActivePLuginRoles($userRole)) {
            return $this->_getPriceWithFixedFloat($this->userPrice);
        }

        $newPrice = $this->getPrices($product, $userRole);

        if ($newPrice) {
            $this->userPrice = $newPrice;
            return $this->_getPriceWithFixedFloat($this->userPrice);
        }
        
        return $this->_getPriceWithFixedFloat($this->userPrice);
    } // end onDisplayPriceFilter
    
    private function _getPriceWithFixedFloat($price)
    {
        return str_replace(',', '.', $price);
    } // end _getPriceWithFixedFloat
    
    public function getPriceWithDiscountOrMarkUp($product)
    {
        $amount = $this->getAmountOfDiscountOrMarkUp();
        $emptyRolePrice = false;
        $price = 0;
        
        if ($this->_isRolePriceDiscountTypeEnabled()) {
            $price = $this->getPrices($product, $this->userRole);
            
            if (!$price) {
                $emptyRolePrice = true;
            }
        }
        
        if (!$price) {
            $price = $this->getRegularPrice($product->id);
        
            if (!$price) {
                $price = $this->getRegularPrice($product->variation_id);
            }
        }
        
        if ($emptyRolePrice) {
            return $price;
        }
        
        if ($this->_isPercentDiscountType()) {
            $amount = $this->getAmountOfDiscountOrMarkUpInPercentage(
                $price,
                $amount
            );
        }

        if ($this->_isDiscountTypeEnabled()) {
            $newPrice = ($amount > $price) ? 0 : $price - $amount;
        } else {
            $newPrice = $price + $amount;
        }
                
        return $newPrice;
    } // end getPriceWithDiscountOrMarkUp
    
    private function _isRolePriceDiscountTypeEnabled()
    {
        $options = $this->settings;
        $priceType =  $options['discountByRoles'][$this->userRole]['priceType'];
        
        return $priceType == 'role';
    } // end _isRolePriceDiscountTypeEnabled
    
    private function _isDiscountTypeEnabled()
    {
        return $this->settings['discountOrMakeUp'] == 'discount';
    } // end _isDiscountTypeEnabled

    public function getAmountOfDiscountOrMarkUpInPercentage($price, $discount)
    {
        $discount = $price / 100 * $discount;
        
        return $discount;
    } // end getAmountOfDiscountOrMarkUpInPercentage
        
    public function getAmountOfDiscountOrMarkUp()
    {
        $options = $this->settings;
        return $options['discountByRoles'][$this->userRole]['value'];
    } // end getAmountOfDiscountOrMarkUp

    private function _isPercentDiscountType()
    {
        $options = $this->settings;
        
        return $options['discountByRoles'][$this->userRole]['type'] == 0;
    } // end _isPercentDiscountType
    
    private function _hasDiscountOrMarkUpForUserRoleInGeneralOptions($role)
    {
        if (!$role) {
            return false;
        }
        
        $options = $this->settings;

        return array_key_exists('discountByRoles', $options)
               && array_key_exists($role, $options['discountByRoles'])
               && $options['discountByRoles'][$role]['value'] != 0;
    } // end _hasDiscountOrMarkUpForUserRoleInGeneralOptions
    
    public function getPrices($product, $role)
    {
        if ($product->is_type('simple')) {
            return $this->getRolePrice($product->id, $role);
        }
        
        if ($product->is_type('variation')) {
            return $this->getRolePrice($product->variation_id, $role);
        }
        
        if ($product->is_type('variable')) {
            $variation_id = get_post_meta(
                $product->id,
                '_min_price_variation_id',
                true
            );

            return $this->getRolePrice($variation_id, $role);
        }

        return false;
    } // end getPrices
    
    public function getRolePrice($id, $role)
    {
        if (!$role) {
            return false;
        } 
        
        $priceList = $this->getMetaOptions($id, 'festiUserRolePrices');
            
        if (!$this->_hasRolePriceInProductOptions($priceList, $role)) {
            return false;
        }

        return $priceList[$role];
    } // end getRolePrice
    
    private function _hasRolePriceInProductOptions($priceList, $role)
    {
        return $priceList && array_key_exists($role, $priceList);
    } // end _hasRolePriceInProductOptions
    
    private function _hasUserRoleInActivePLuginRoles($role)
    {
        if (!$role) {
            return false;
        }
        
        $activeRoles = $this->getActiveRoles();

        if (!$activeRoles) {
            return false;
        }
        
        return array_key_exists($role, $activeRoles);
    } // end _hasUserRoleInActivePLuginRoles
    
    public function onDisplayGroupedProductPriceFilter($price, $product)
    {
        if (!$this->_hasUserRoleInActivePLuginRoles($this->userRole)) {
            return $price;
        }
        
        $childPrices = $this->getPricesOfChieldProduct($product);

        if ($childPrices) {
            $minPrice = min($childPrices);
            $minPrice = $this->_getPriceWithFixedFloat($minPrice);
            
            $maxPrice = max($childPrices);
            $maxPrice = $this->_getPriceWithFixedFloat($maxPrice);
        } else {
            return apply_filters('woocommerce_get_price_html', $price, $this);
        }

        if ($minPrice == $maxPrice) {
            $price = $this->getPriceTax($product, $minPrice);
        } else {
            $minPrice = $this->getPriceTax($product, $minPrice);
            $maxPrice = $this->getPriceTax($product, $maxPrice);
            $price =  sprintf(
                '%1$s-%2$s %3$s',
                $minPrice,
                $maxPrice,
                $product->get_price_suffix()
            );
        }
        return $price;
    } // end onDisplayGroupedProductPriceFilter
    
    public function getPriceTax($product, $price, $formatPrice = true)
    {
        $taxDisplayMode = get_option('woocommerce_tax_display_shop');
        $methodName = 'get_price_'.$taxDisplayMode.'uding_tax';
        
        $price = $product->$methodName(1, $price);
        
        if ($formatPrice) {
            $price = wc_price($price); 
        }
        
        return $price;
    } // getPriceTax
    
    public function getPricesOfChieldProduct($product)
    {
        $productChildrens = $product->get_children();
        $childPrices = array();
        $userRole = $this->userRole;
        
        foreach ($productChildrens as $childId) {
            $result = $this->_hasDiscountOrMarkUpForUserRoleInGeneralOptions(
                $userRole
            );
            
            if ($result) {
                $child = $this->getWoocommerceProductInstance($childId);
                $price = $this->getPriceWithDiscountOrMarkUp(
                    $child
                );
            } else {
                $price = $this->getRolePrice($childId, $userRole); 
            }

            if ($price) {
                $childPrices[] = $price;
            }
        }
        
        if ($childPrices) {
            $childPrices = array_unique($childPrices);   
        }
        
        return $childPrices;
    } // end getPricesOfChieldProduct

    
    public function &getWoocommerceProductInstance($productId)
    {
        $product = new WC_Product($productId);
        return $product;
    } // end getWoocommerceInstance
    
    public function &getWoocommerceInstance()
    {
        return $GLOBALS['woocommerce'];
    } // end getWoocommerceInstance
}