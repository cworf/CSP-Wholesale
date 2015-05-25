<?php
class WooMappingImportOptions
{
    protected $languageDomain;
    
    public function __construct($languageDomain)
    {
        $this->languageDomain = $languageDomain;
    } // end __construct

    public function get()
    {
        $options = array(
            'no_group' => array(
                'key' => 'no',
            ),
            'optgroup_general' => array(
                'key' => 'general',
                'label' => __( 'General', $this->languageDomain)
            ),
            'optgroup_status' => array(
                'key' => 'status',
                'label' => __('Status and Visibility', $this->languageDomain)
            ),
            'prices_by_user_role' => array(
                'key' => 'priceUserRole',
                'label' => __('Prices by User Role', $this->languageDomain)
            ),
            'optgroup_pricing' => array(
                'key' => 'pricing',
                'label' => __(
                    'Pricing, Tax, and Shipping',
                    $this->languageDomain
                )
            ),
            'optgroup_product_types' => array(
                'key' => 'types',
                'label' => __(
                    'Special Product Types',
                    $this->languageDomain
                )
            ),
            'optgroup_taxonomies' => array(
                'key' => 'taxonomies',
                'label' => __(
                    'Categories and Tags',
                    $this->languageDomain
                )
            ),
            'optgroup_custom' => array(
                'key' => 'custom',
                'label' => __(
                    'Custom Attributes and Post Meta',
                    $this->languageDomain
                )
            ),
            'optgroup_images' => array(
                'key' => 'images',
                'label' => __(
                    'Product Images',
                    $this->languageDomain
                )
            ),
        );
        
        foreach ($options as $key => $item) {
            $methodName = 'get'.ucfirst($item['key']).'GroupOptions';
            $method = array($this, $methodName);
            
            if (!is_callable($method)) {
                throw new Exception("Undefined method name: ".$methodName);
            }
            
            $options[$key]['options'] = call_user_func_array($method, array());
        }
        
        return $options;
    }

    protected function getNoGroupOptions()
    {
        $options = array(
            'do_not_import' => array(
                'label' => __('Do Not Import', $this->languageDomain),
                'mapping_hints' => array()
            ),
        );
        
        return $options;
    } // end getNoGroupOptions
    
    protected function getGeneralGroupOptions()
    {
        $options = array(
            'post_title' => array(
                'label' => __('Name', $this->languageDomain),
                'mapping_hints' => array(
                    'title', 
                    'product name'
                )
            ),
            '_sku' => array(
                'label' => __('SKU', $this->languageDomain),
                'mapping_hints' => array()
            ),
            'post_content' => array(
                'label' => __( 'Description', $this->languageDomain ),
                'mapping_hints' => array(
                    'desc', 
                    'content'
                )
            ),
            'post_excerpt' => array(
                'label' => __( 'Short Description', $this->languageDomain ),
                'mapping_hints' => array(
                    'short desc', 
                    'excerpt'
                )
            ),
        );
        return $options;
    } // end getGeneralGroupOptions
    
   protected function getStatusGroupOptions()
   {
       $options = array(
            'post_status' => array(
                'label' => __(
                    'Status (Valid: publish/draft/trash/[more in Codex])', 
                    $this->languageDomain
                ),
                'mapping_hints' => array(
                    'status', 
                    'product status', 
                    'post status'
                )
            ),
            'menu_order' => array(
                'label' => __(
                    'Menu Order', 
                    $this->languageDomain
                ),
                'mapping_hints' => array(
                    'menu order'
                )
            ),
            '_visibility' => array(
                'label' => __(
                    'Visibility (Valid: visible/catalog/search/hidden)',
                     $this->languageDomain
                 ),
                'mapping_hints' => array(
                     'visibility',
                     'visible'
                 ),
                 'validationValues' => 'getValidateVisibilityValues'
             ),
            '_featured' => array(
                'label' => __(
                    'Featured (Valid: yes/no)', 
                    $this->languageDomain
                ),
                'mapping_hints' => array(
                    'featured'
                ),
                'validationValues' => 'getYesOrNoValues'
            ),
            '_stock' => array(
                'label' => __(
                    'Stock', 
                    $this->languageDomain
                ),
                'mapping_hints' => array(
                    'qty', 
                    'quantity'
                )
            ),
            '_stock_status' => array(
                'label' => __(
                    'Stock Status (Valid: instock/outofstock)',
                     $this->languageDomain
                 ),
                'mapping_hints' => array(
                    'stock status', 
                    'in stock'
                ),
                'validationValues' => 'getValidateStockStatusValues'
            ),
            '_backorders' => array(
                'label' => __(
                    'Backorders (Valid: yes/no/notify)',
                     $this->languageDomain
                 ),
                'mapping_hints' => array(
                    'backorders'
                ),
                'validationValues' => 'getValidateBackordersValues'
            ),
            '_manage_stock' => array(
                'label' => __(
                    'Manage Stock (Valid: yes/no)', 
                    $this->languageDomain
                ),
                'mapping_hints' => array(
                    'manage stock'
                ),
                'validationValues' => 'getYesOrNoValues'
            ),
            'comment_status' => array(
                'label' => __(
                    'Comment/Review Status (Valid: open/closed)',
                     $this->languageDomain
                 ),
                'mapping_hints' => array(
                    'comment status'
                ),
                'validationValues' => 'getValidateCommentOrPingStatusValues'
            ),
            'ping_status' => array(
                'label' => __(
                    'Pingback/Trackback Status (Valid: open/closed)',
                     $this->languageDomain
                 ),
                'mapping_hints' => array(
                     'ping status',
                     'pingback status', 
                     'pingbacks',
                     'trackbacks',
                     'trackback status'
                ),
                'validationValues' => 'getValidateCommentOrPingStatusValues'
            )
        );
        
        return $options;
    } // end getStatusGroupOptions
    
    public function getYesOrNoValues()
    {
        $values = array(
            'yes',
            'no'
        );
        
        return $values;
    } // end getYesOrNoValues
    
    public function getValidateCommentOrPingStatusValues()
    {
        $values = array(
            'open',
            'closed'
        );
        
        return $values;
    } // end getYesOrNoValues
    
    public function getValidateVisibilityValues()
    {
        $values = array(
            'visible',
            'catalog',
            'search',
            'hidden'
        );
        
        return $values;
    } // end getValidateVisibilityValues
    
    public function getValidateStockStatusValues()
    {
        $values = array(
            'instock',
            'outofstock'
        );
        
        return $values;
    } // end getValidateStockStatusValues
    
    public function getValidateBackordersValues()
    {
        $values = array(
            'yes',
            'no',
            'notify'
        );
        
        return $values;
    } // end getValidateBackordersValues
    
    public function getValidateTaxStatusValues()
    {
        $values = array(
            'taxable',
            'shipping',
            'none'
        );
        
        return $values;
    } // end getValidateTaxStatusValues
    
    public function getValidateProductTypeValues()
    {
        $values = array(
            'simple',
            'variable',
            'grouped',
            'external'
        );
        
        return $values;
    } // end getValidateProductTypeValues
    
    protected function getPriceUserRoleGroupOptions()
    {  
        $userRoleOptions = array();
        
        $roles = $this->_getUserRoles();
        
        if (!$roles) {
            return $userRoleOptions;
        }
        
        foreach ($roles as $key => $name) {
            $priceKey = $key.'_festi_price';
            $userRoleOptions[$priceKey] = array(
                'label' => __($name.' Price', $this->languageDomain),
                'mapping_hints' => array()
            );
        }
        
        return $userRoleOptions;
    } // end getPriceUserRoleGroupOptions
    
    private function _getUserRoles()
    {
        if (!$this->_hasRolesInGlobalArray()) {
            return false;
        }
        
        $data = $GLOBALS['wp_roles'];
        
        if (!$data) {
            return false;
        }
        
        $roles = array();
              
        foreach ($data->roles as $ident => $item) {
            $roles[$ident] = $item['name'];
        }
        
        return $roles;
    } // end _getUserRoles
    
    private function _hasRolesInGlobalArray()
    {
        return array_key_exists('wp_roles', $GLOBALS); 
    } // end _hasRolesInGlobalArray
    
    private function getPricingGroupOptions()
    {
        $options = array(
            '_regular_price' => array(
                'label' => __(
                    'Regular Price', 
                    $this->languageDomain
                ),
                'mapping_hints' => array(
                     'price',
                     '_price', 
                     'msrp'
                 )
             ),
            '_sale_price' => array(
                'label' => __(
                    'Sale Price',
                     $this->languageDomain
                 ),
                'mapping_hints' => array()
            ),
            '_tax_status' => array(
                'label' => __(
                    'Tax Status (Valid: taxable/shipping/none)',
                     $this->languageDomain
                 ),
                 'mapping_hints' => array(
                     'tax status',
                     'taxable'
                 ),
                 'validationValues' => 'getValidateTaxStatusValues'
             ),
            '_tax_class' => array(
                'label' => __(
                     'Tax Class',
                     $this->languageDomain
                 ),
                'mapping_hints' => array()
            ),
            'product_shipping_class_by_id' => array(
                'label' => __(
                    'Shipping Class By ID (Separated by "|")',
                     $this->languageDomain
                ),
                'mapping_hints' => array()
            ),
            'product_shipping_class_by_name' => array(
                'label' => __(
                    'Shipping Class By Name (Separated by "|")',
                     $this->languageDomain
                ),
                'mapping_hints' => array(
                       'product_shipping_class',
                       'shipping_class',
                       'product shipping class',
                       'shipping class'
               )
           ),
            '_weight' => array(
                'label' => __(
                     'Weight',
                     $this->languageDomain
                ),
                'mapping_hints' => array(
                    'wt'
                )
            ),
            '_length' => array(
                'label' => __(
                     'Length',
                     $this->languageDomain
                ),
                'mapping_hints' => array(
                    'l'
                )
            ),
            '_width' => array(
                'label' => __(
                     'Width',
                     $this->languageDomain
                 ),
                'mapping_hints' => array(
                    'w'
                )
            ),
            '_height' => array(
                'label' => __(
                     'Height',
                     $this->languageDomain
                 ),
                'mapping_hints' => array(
                    'h'
                )
            ),
        );
       
        return $options;
    } // end getPricingGroupOptions
    
    private function getTypesGroupOptions()
    {
        $options = array(
            '_downloadable' => array(
                'label' => __(
                     'Downloadable (Valid: yes/no)',
                     $this->languageDomain
                 ),
                'mapping_hints' => array(
                    'downloadable'
                ),
                'validationValues' => 'getYesOrNoValues'
            ),
            '_virtual' => array(
                'label' => __(
                     'Virtual (Valid: yes/no)',
                     $this->languageDomain
                 ),
                'mapping_hints' => array(
                    'virtual'
                ),
                'validationValues' => 'getYesOrNoValues'
            ),
            '_product_type' => array(
                'label' => __(
                     'Product Type (Valid: simple/variable/grouped/external)',
                     $this->languageDomain
                 ),
                'mapping_hints' => array(
                    'product type', 'type'
                ),
                'validationValues' => 'getValidateProductTypeValues'
            ),
            '_button_text' => array(
                'label' => __(
                    'Button Text (External Product Only)',
                     $this->languageDomain
                 ),
                'mapping_hints' => array(
                    'button text'
                )
            ),
            '_product_url' => array(
                'label' => __(
                     'Product URL (External Product Only)',
                     $this->languageDomain
                 ),
                'mapping_hints' => array(
                    'product url', 'url'
                )
            ),
            '_file_paths' => array(
                'label' => __(
                     'File Path (Downloadable Product Only)',
                     $this->languageDomain
                ),
                'mapping_hints' => array(
                      'file path',
                      'file', 'file_path',
                      'file paths'
                )
              ),
            '_download_expiry' => array(
                'label' => __(
                     'Download Expiration (in Days)',
                     $this->languageDomain
                ),
                'mapping_hints' => array(
                     'download expiration',
                     'download expiry'
                )
             ),
            '_download_limit' => array(
                'label' => __(
                     'Download Limit (Number of Downloads)',
                     $this->languageDomain
                ),
                'mapping_hints' => array(
                     'download limit',
                     'number of downloads'
                )
            ),
        );
        
        return $options;
    } // end getTypesGroupOptions
    
    private function getTaxonomiesGroupOptions()
    {
        $options = array(
            'product_cat_by_name' => array(
                'label' => __(
                    'Categories By Name (Separated by "|")',
                     $this->languageDomain
                 ),
                  'mapping_hints' => array(
                  'category',
                  'categories',
                  'product category',
                  'product categories',
                  'product_cat'
                )
            ),
            'product_cat_by_id' => array(
                'label' => __(
                     'Categories By ID (Separated by "|")',
                     $this->languageDomain
                 ),
                'mapping_hints' => array()
            ),
            'product_tag_by_name' => array(
                'label' => __(
                     'Tags By Name (Separated by "|")',
                     $this->languageDomain
                 ),
                'mapping_hints' => array(
                      'tag',
                      'tags',
                      'product tag',
                      'product tags',
                      'product_tag'
                )
            ),
            'product_tag_by_id' => array(
                'label' => __(
                     'Tags By ID (Separated by "|")',
                     $this->languageDomain
                 ),
                'mapping_hints' => array()
            ),
        );
        
        return $options;
    } // end  getTaxonomiesGroupOptions
    
    private function getCustomGroupOptions()
    {
        $options = array(
            'custom_field' => array(
                'label' => __(
                     'Custom Field / Product Attribute (Set Name Below)',
                     $this->languageDomain
                 ),
                'mapping_hints' => array(
                     'custom field',
                     'custom'
                 )
             ),
            'post_meta' => array(
                'label' => __(
                     'Post Meta',
                     $this->languageDomain
                 ),
                'mapping_hints' => array(
                    'postmeta'
                )
            ),
        );
        
        return $options;
    } // end getCustomGroupOptions
    
    private function getImagesGroupOptions()
    {
        $options = array(
            'product_image_by_url' => array(
                'label' => __(
                     'Images (By URL, Separated by "|")',
                     $this->languageDomain
                ),
                'mapping_hints' => array(
                   'image',
                   'images',
                   'image url',
                   'image urls',
                   'product image url',
                   'product image urls',
                   'product images'
                )
            ),
            'product_image_by_path' => array(
                'label' => __(
                     'Images (By Local File Path, Separated by "|")',
                     $this->languageDomain
                 ),
                'mapping_hints' => array(
                      'image path',
                      'image paths',
                      'product image path',
                      'product image paths'
                )
            )
        );
        
        return $options;
    } // end getImagesGroupOptions
    
    public function getImportMapingActions($instance)
    {
        $actions = array(
            'setPostFields' => array(
                'post_title',
                'post_content',
                'post_excerpt',
                'post_status',
                'comment_status',
                'ping_status'
            ),
            'setIntegerPostFields' => array(
                'menu_order'
            ),
            'setIntegerPostMetaFields' => array(
                '_stock',
                '_download_expiry',
                '_download_limit'
            ),
            'setFloatPostMetaFields' => array(
                '_weight',
                '_length',
                '_width',
                '_height',
                '_regular_price',
                '_sale_price'
            ),
            'setPostMetaSkuValue' => array(
                '_sku',
            ),
            'setPostMetaFilePathValue' => array(
                '_file_path',
                '_file_paths',
            ),
            'setPostMetaFields' => array(
                '_tax_status',
                '_tax_class',
                '_visibility',
                '_featured',
                '_downloadable',
                '_virtual',
                '_stock_status',
                '_backorders',
                '_manage_stock',
                '_button_text',
                '_product_url',
            ),
            'setPostMetaValue' => array(
                'post_meta'
            ),
            'setProductTypeMetaValue' => array(
                '_product_type'
            ),
            'setPostTermsByName' => array(
                'product_cat_by_name',
                'product_tag_by_name',
                'product_shipping_class_by_name'
            ),
            'setPostTermsById' => array(
                'product_cat_by_id',
                'product_tag_by_id',
                'product_shipping_class_by_id'
            ),
            'setCustomFieldValue' => array(
                'custom_field',
            ),
            'setProductImageByUrl' => array(
                'product_image_by_url',
            ),
            'setProductImageByPath' => array(
                'product_image_by_path',
            ),
        );
        
        foreach ($actions as $key => $item) {
            $methodName = $key;

            $method = array($this, $methodName);
        
            if (!is_callable($method)) {
                throw new Exception("Undefined method name: ".$methodName);
            }
        }
        
        return $actions;
    } // end getImportMapingActions
      
    public function setPostFields($instance, $mapTo, $value)
    {
        $instance->newPost[$mapTo] = $value;
    } // end setPostFields
    
    public function setPostMetaFields($instance, $mapTo, $value)
    {
        $instance->newPostMeta[$mapTo] = $value;
    } // end setPostMetaFields
    
    public function setIntegerPostFields($instance, $mapTo, $value)
    {
        $value = preg_replace("/[^0-9]/", "", $value);
        
        if(!$value) {
            return false;
        };
    
        $instance->newPost[$mapTo] = $value;
    } // end setPostFields
    
    public function setIntegerPostMetaFields($instance, $mapTo, $value)
    {
        $value = preg_replace("/[^0-9]/", "", $value);
        
        if(!$value) {
            return false;
        };
    
        $instance->newPostMeta[$mapTo] = $value;
    } // end setIntegerPostMetaFields
    
    public function setFloatPostMetaFields($instance, $mapTo, $value)
    {
        $decimalSeparator = $instance->config['decimalSeparator'];
        
        $value = preg_replace("/[^0-9".$decimalSeparator."]/", "", $value);
        
        $value = str_replace(",", ".", $value);
        
        if(!$value) {
            return false;
        };
    
        $instance->newPostMeta[$mapTo] = $value;
    } // end setFloatPostMetaFields
    
    public function setPostMetaSkuValue($instance, $mapTo, $value)
    {
        $value == trim($value);
        
        if(!$value) {
            return false;
        };
    
        $instance->newPostMeta[$mapTo] = $value;
    } // end setPostMetaSkuValue
    
    public function setPostMetaFilePathValue($instance, $mapTo, $value)
    {
        if(!isset($instance->newPostMeta['_file_paths'])
           || !is_array($instance->newPostMeta['_file_paths'])) {
            $instance->newPostMeta['_file_paths'] = array();
        }
    
        $instance->newPostMeta['_file_paths'][md5($value)] = $value;
    } // end setPostMetaFilePathValue
    
    public function setPostMetaValue($instance, $mapTo, $value, $columNum)
    {
        $key = $instance->config['post_meta_key'][$columNum];
        $instance->newPostMeta[$key] = $value;
        $instance->newPostMeta['_file_paths'][md5($value)] = $value;
    } // end setPostMetaValue
    
    public function setProductTypeMetaValue($instance, $mapTo, $value)
    {
        $instance->newPostMeta[$mapTo] = $value;
        $termName = $value;
        $tax = 'product_type';
        $term = get_term_by('name', $termName, $tax, 'ARRAY_A');

        if(!is_array($term)) {
            return false;
        }
        
        $instance->newPostTerms[$tax][] = intval($term['term_id']);
    } // end setProductTypeMetaValue
    
    public function setPostTermsByName($instance, $mapTo, $value)
    {
        $tax = str_replace('_by_name', '', $mapTo);
        $termPaths = explode('|', $value);
        
        foreach($termPaths as $termPath) {
            $termNames = explode(
                $instance->config['categorySeparator'],
                $termPath
            );
            
            $termIds = $this->_getTermsIds($tax,$termNames, $termPath);

            if (array_key_exists(count($termNames) - 1, $termIds)) {
                $key = count($termNames) - 1;
                $instance->newPostTerms[$tax][] = $termIds[$key];
            }
        }
    } // end setPostTermsByName
    
    private function _isNotExistsTerm($term)
    {
        return $term === false || $term === 0 || $term === null;
    } // end _isExistsTerm
    
    private function _getTermsIds($tax, $termNames, $termPath)
    {
        $termIds = array();

        for ($depth = 0; $depth < count($termNames); $depth++) {
            $termParent = ($depth > 0) ? $termIds[($depth - 1)] : '';
            $term = term_exists($termNames[$depth], $tax, $termParent);

            if ($this->_isNotExistsTerm($term)) {
                if ($depth > 0) {
                    $insertTermArgs = array(
                        'parent' => $termIds[($depth - 1)]
                    );
                } else {
                    $insertTermArgs = array();
                }

                $term = wp_insert_term(
                    $termNames[$depth],
                    $tax,
                    $insertTermArgs
                );
                
                delete_option("{$tax}_children");
            }

            if (is_array($term)) {
                $termIds[$depth] = intval($term['term_id']);
            } else {
                $instance->newPostErrors[] = __(
                    "Couldn't find or create {$tax} with path {$termPath}.",
                    $this->languageDomain 
                );
            }
        }

        return $termIds;
    } // end _getTermsIds
    
    public function setPostTermsById($instance, $mapTo, $value)
    {
        $tax = str_replace('_by_id', '', $mapTo);
        $termIds = explode('|', $value);
        
        foreach($termIds as $termId) {
            $term = term_exists($termId, $tax);

            if(is_array($term)) {
                $instance->newPostTerms[$tax][] = intval($term['term_id']);
            } else {
                $instance->newPostErrors[] = __(
                    "Couldn't find {$tax} with ID {$termId}.",
                    $this->languageDomain 
                );
            }

        }
    } // end setPostTermsById
    
    public function setCustomFieldValue($instance, $mapTo, $value, $columNum)
    {
        $fieldName = $instance->config['custom_field_name'][$columNum];
        $fieldSlug = sanitize_title($fieldName);
        $visible = intval($instance->config['custom_field_visible'][$columNum]);

        $instance->newPostCustomFields[$fieldSlug] = array (
            "name" => $fieldName,
            "value" => $value,
            "position" => $instance->newPostCustomFieldCount++,
            "is_visible" => $visible,
            "is_variation" => 0,
            "is_taxonomy" => 0
        );
    } // end setCustomFieldValue
    
    public function setProductImageByUrl($instance, $mapTo, $value)
    {
        $imageUrls = explode('|', $value);
        
        if(!is_array($imageUrls)) {
            return false;
        }
        
        $instance->newPostImageUrls = array_merge(
            $instance->newPostImageUrls, $imageUrls
        );
    } // end setProductImageByUrl
    
    public function setProductImageByPath($instance, $mapTo, $value)
    {
        $imagePaths = explode('|', $value);
        
        if(!is_array($imagePaths)) {
            return false;
        }
        
        foreach($imagePaths as $imagePath) {
            $instance->newPostImagePaths[] = array(
                'path' => $imagePath,
                'source' => $imagePath
            );
        }
    } // end setProductImageByPath
    
    public function setRolePriceValue($instance, $mapTo, $value)
    {        
        if (!strpos($mapTo, '_festi_price')) {
            return false;
        }
        
        $decimalSeparator = $instance->config['decimalSeparator'];
        
        $value = preg_replace("/[^0-9".$decimalSeparator."]/", "", $value);
        
        $value = str_replace(",", ".", $value);

        if (!$value) {
            return false;
        }
                                   
        $roleKey =  str_replace("_festi_price", "", $mapTo); 
        $instance->newPricesByUserRole[$roleKey] = $value;
    } // end setRolePriceValue
    
}