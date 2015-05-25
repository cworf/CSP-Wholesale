<?php
class SettingsFestiPlugin
{
    private $_languageDomain = '';
    
    public function __construct($languageDomain)
    {
        $this->_languageDomain = $languageDomain;
    } // end __construct
    
    public function get()
    {
        $settings = array(
            'hideAddToCartButton' => array(
                'caption' => __(
                    'Hide Add to Cart Button for Non-Registered Users',
                    $this->_languageDomain
                ),
                'type' => 'input_checkbox',
                'fieldsetKey' => 'general'
            ),
            'hideAddToCartButtonForUserRoles' => array(
                'caption' => __(
                    'Hide Add to Cart Button for User Roles',
                    $this->_languageDomain
                ),
                'type' => 'multicheck',
                'default' => array(),
                'fieldsetKey' => 'general',
                'deleteButton' => false,
            ),
            'onlyRegisteredUsers' => array(
                'caption' => __(
                    'Show Prices for all products only for Registered Users',
                    $this->_languageDomain
                ),
                'type' => 'input_checkbox',
                'fieldsetKey' => 'general',
                'classes' => 'festi-user-role-prices-top-border'
            ),
            'textForUnregisterUsers' => array(
                'caption' => __(
                    'Text for Non-Registered Users',
                    $this->_languageDomain
                ),
                'type' => 'textarea',
                'default' => __(
                    'Please login or register to see price',
                    $this->_languageDomain
                ),
                'fieldsetKey' => 'general',
            ),
            'hidePriceForUserRoles' => array(
                'caption' => __(
                    'Hide Prices for User Roles',
                    $this->_languageDomain
                ),
                'type' => 'multicheck',
                'default' => array(),
                'fieldsetKey' => 'general',
                'deleteButton' => false,
                'classes' => 'festi-user-role-prices-top-border'
            ),
            'textForRegisterUsers' => array(
                'caption' => __(
                    'Text for Registered Users with Hidden Price',
                    $this->_languageDomain
                ),
                'type' => 'textarea',
                'default' => __(
                    'Price for your role is hidden',
                    $this->_languageDomain
                ),
                'fieldsetKey' => 'general',
            ),
            'discountOrMakeUp' => array(
                'caption' => __(
                    'Discount or Markup for all products',
                    $this->_languageDomain
                ),
                'type' => 'input_select',
                'values' => array(
                    'discount' => __('discount', $this->_languageDomain),
                    'markup' => __('markup',$this->_languageDomain)
                ),
                'default' => 'discount',
                'fieldsetKey' => 'general',
                'classes' => 'festi-user-role-prices-top-border'
            ),
            'discountByRoles' => array(
                'caption' => __(
                    '',
                    $this->_languageDomain
                ),
                'type' => 'multidiscount',
                'default' => array(),
                'fieldsetKey' => 'general',
                'deleteButton' => false,
            ),
            'roles' => array(
                'caption' => __(
                    'Pricing Roles',
                    $this->_languageDomain
                ),
                'type' => 'multicheck',
                'default' => array(),
                'fieldsetKey' => 'general',
                'classes' => 'festi-user-role-prices-top-border',
                'deleteButton' => true
            ),
            'showCustomerSavings' => array(
                'caption' => __(
                    'Display the Regular Price on',
                    $this->_languageDomain
                ),
                'hint' => __(
                    'Display the regular price as well as the user role '.
                    'price, and percent difference between it',
                    $this->_languageDomain
                ),
                'type' => 'multi_select',
                'values' => array(
                    'product' => __('Product Page', $this->_languageDomain),
                    'archive' => __(
                        'Products Archive Page (for Simple product)',
                        $this->_languageDomain
                    ),
                    'cartTotal' => __(
                        'Cart Page (for Order Total)',
                        $this->_languageDomain
                    ),
                ),
                'default' => array(),
                'fieldsetKey' => 'general',
                'classes' => 'festi-user-role-prices-top-border'
            ),      
            'customerSavingsLableColor' => array(
                'caption' => __(
                    'Color for Price Title on Product Page',
                    $this->_languageDomain
                ),
                'type'    => 'color_picker',
                'fieldsetKey' => 'general',
                'default' => '#ff0000',
                'eventClasses' => 'showCustomerSavings',
            ),
        );
        
        return $settings;
    } // end get
}