<?php
class WooProductsSkuManager
{
    private $_engine;
    public $config;
    private $_mapingOptions;
    public $languageDomain;
    
    public $newPostId;
    public $newPost = array();
    public $newPostDefaults = array();
    public $newPostMeta = array();
    public $newPostMetaDefaults = array();
    public $newPostTerms = array();
    public $newPostCustomFields = array();
    public $newPostCustomFieldCount = 0;
    public $newPostImageUrls = array();
    public $newPostImagePaths = array();
    public $newPostErrors = array();
    public $newPostMessages = array();
    public $newPostInsertSuccess = false;
    public $newPricesByUserRole = array();
    public $exsistProduct = null;
    public $uploadImagesDir = '';

    public function __construct($config, $engine, $languageDomain)
    {
        $this->_engine = $engine;
        $this->config = $config;
        $this->languageDomain = $languageDomain;
    } // end __construct
    
    protected function onInitDefaultPostValues()
    {
        $this->newPostId = null;
        $this->newPost = array();
        $this->newPostDefaults = $this->_getDefaultPostData();
        $this->newPostMeta = array();
        $this->newPostMetaDefaults = $this->_getDefaultPostMetaData();
        $this->newPostTerms = array();
        $this->newPostCustomFields = array();
        $this->newPostCustomFieldCount = 0;
        $this->newPostImageUrls = array();
        $this->newPostImagePaths = array();
        $this->newPostErrors = array();
        $this->newPostMessages = array();
        $this->newPostInsertSuccess = false;
        $this->newPricesByUserRole = array();
        $this->uploadImagesDir = wp_upload_dir();

        $this->mapingOptions = $this->_engine->mapingOptions->get();
        $this->mapingOptions = $this->_deleteMappingOptionsGroup();        
    } // end onInitDefaultPostValues
    
    protected function setValuesThatRequireValidation(
        $mapingActions, $col, $key
    )
    {
        $mapTo = $this->config['mapTo'][$key];
            
        if ($this->_isNotImportColum($mapTo) || !$col) {
            return false;
        }
        
        if (!$this->_isValidateValue($col, $mapTo)) {
             return false;
        }
        
        foreach ($mapingActions as $ident => $item) {
            if (in_array($mapTo, $item)) {
                $methodeName = $ident;
            } else {
                $methodeName = 'setRolePriceValue';
            }
            
            $method = array($this->_engine->mapingOptions, $methodeName);
            call_user_func_array(
                $method,
                array($this, $mapTo, $col, $key)
            );
        }
    } // end setValuesThatRequireValidation
    
    public function start($row) 
    {
        $this->onInitDefaultPostValues();
        
        $mapingActions = $this->_engine->mapingOptions->getImportMapingActions(
            $this
        );
        
        foreach($row as $key => $col) {
            $this->setValuesThatRequireValidation($mapingActions, $col, $key);
        }
        
        $this->newPostMeta['_price'] = $this->setPriceForProduct();
        $this->newPostMeta['_manage_stock'] = $this->setManageStockForProduct();
        $this->newPostMeta['_stock_status'] = $this->setStockStatusForProduct();
        
        $this->exsistProduct = $this->_getExsistProduct();
        
        $value = $this->setUserRolePriceForProduct();
        $this->newPostMeta['festiUserRolePrices'] = $value;
        
        if (!$this->_hasValidateProductNameInData() && !$this->exsistProduct) {
            $this->newPostErrors[] = __(
                'Skipped import of product without a name',
                $this->languageDomain
            );
            
            return $this->getImportReport();
        }
        
        if ($this->exsistProduct) {
            $this->updatePost();
        } else {
            $this->insertPost();
        }
        
        if ($this->_isValidateNewPostId()) {
            $this->newPostInsertSuccess = true;
        
            $this->updateProduct($key);
        }
        
        return $this->getImportReport();   
    } //end  updateProduct
    
    protected function updateProduct($key)
    {
        $this->updatePostMeta();

        $this->updateProductAttributes();

        $this->updatePostTerms();

        $this->updateProductImagesByUrl();
        
        $this->updateProductAtachmentData($key);
    } // end updateProduct
    
    private function _isEnabledSkipDuplicatesImagesOption($numCol)
    {
        $skipDuplicates = intval(
            $this->config['product_image_skip_duplicates'][$numCol]
        );
        
        return $this->exsistProduct !== null && $skipDuplicates == 1;
    } // end _isEnabledSkipDuplicatesImagesOption
    
    private function _isDuplicateImage($source, $numCol)
    {
        if (!$this->_isEnabledSkipDuplicatesImagesOption($numCol)) {
            return false;
        }
        
        $existingAttachmentQuery = array(
            'numberposts' => 1,
            'meta_key' => '_import_source',
            'post_status' => 'inherit',
            'post_parent' => $this->exsistProduct->ID,
            'meta_query' => array(
                array(
                    'key'=>'_import_source',
                    'value'=> $source,
                    'compare' => '='
                )
            ),
            'post_type' => 'attachment'
        );
        
        $existingAttachments = get_posts($existingAttachmentQuery);
        
        if ($this->_hasImageInProductData($existingAttachments)) {
            $message = __(
                'Skipping import of duplicate image %s.',
                $this->languageDomain
            );
            
            $this->newPostMessages[] = sprintf(
                $message,
                $source
            );
            return true;
        }
        
        return false;
    } // end _isDuplicateImage
    
    private function _isExistsLocalImage($path)
    {
        if (!file_exists($path)) {
            $message = __(
                'Couldn\'t find local file %s.',
                $this->languageDomain
            );
                
            $this->newPostErrors[] = sprintf($message, $path);
            return false;
        }
        
        return true;
    } // end _isExistsLocalImage
    
    protected function doInsertAtachmentImage($path)
    {
        $destUrl = str_ireplace(ABSPATH, home_url('/'), $path);
        
        $pathParts = pathinfo($path);

        $wpFiletype = wp_check_filetype($path);
        
        $postTitle = preg_replace(
            '/\.[^.]+$/',
            '',
            $pathParts['filename']
        );
        
        $attachment = array(
            'guid' => $destUrl,
            'post_mime_type' => $wpFiletype['type'],
            'post_title' => $postTitle,
            'post_content' => '',
            'post_status' => 'inherit'
        );
        
        $attachmentId = wp_insert_attachment(
            $attachment,
            $path, 
            $this->newPostId
        );
        
        return $attachmentId;
    } // end doInsertAtachmentImage
    
    protected function updateAtachmentMetaData($attachmentId, $path)
    {
        require_once(ABSPATH . 'wp-admin/includes/image.php');
            
        $attachData = wp_generate_attachment_metadata(
            $attachmentId,
            $path
        );
        
        wp_update_attachment_metadata($attachmentId, $attachData);
    } // end updateAtachmentMetaData
    
    protected function updateProductImagePostMeta($attachmentId, $source)
    {
        $result = add_post_meta(
            $attachmentId,
            '_import_source',
            $source,
            true
        );
                
        if (!$result) {
            update_post_meta(
                $attachmentId,
                '_import_source',
                $source
            ); 
        }
    } // end updateProductImagePostMeta
    
    private function _isEnabledFirstImageFeaturedOption($numCol)
    {
        $setFeatured = intval(
            $this->config['product_image_set_featured'][$numCol]
        );
        
        return $setFeatured;
    } // end _isEnabledFirstImageFeaturedOption
    
    private function _isFirstImage($imageIndex)
    {
        return $imageIndex == 0;
    } // end _isFirstImage

    public function updateProductAtachmentData($numCol)
    {
        $imageGalleryIds = array();
    
        foreach($this->newPostImagePaths as $imageIndex => $destPathInfo) {
            $path = $destPathInfo['path'];
            $source = $destPathInfo['source'];
            
            if ($this->_isDuplicateImage($source, $numCol)) {
                return false;
            }

            if (!$this->_isExistsLocalImage($path)) {
                return false;
            }

            $attachmentId = $this->doInsertAtachmentImage($path);
            
            $this->updateAtachmentMetaData($attachmentId, $path);

            $this->updateProductImagePostMeta($attachmentId, $source);
            
            $setFeatured = $this->_isEnabledFirstImageFeaturedOption($numCol);

            if (!$this->_isFirstImage($imageIndex) || !$setFeatured) {
                $imageGalleryIds[] = $attachmentId;
                continue;
            }
            
            update_post_meta($this->newPostId, '_thumbnail_id', $attachmentId);
        }

        if (count($imageGalleryIds) <= 0) {
            update_post_meta(
                $this->newPostId,
                '_product_image_gallery',
                implode(',', $imageGalleryIds)
            );
        }
    } // end updateProductAtachmentData
    
    private function _hasImageInProductData($existingAttachments)
    {
        return is_array($existingAttachments)
               && sizeof($existingAttachments) > 0;
    } // end _hasImageInProductData
    
    public function updateProductImagesByUrl()
    {
        if (!$this->newPostImageUrls) {
            return false;
        }
        
        foreach($this->newPostImageUrls as $imageIndex => $imageUrl) {
            $imageUrl = str_replace(' ', '%20', trim($imageUrl));
            $parsedUrl = parse_url($imageUrl);
            $pathinfo = pathinfo($parsedUrl['path']);
            $imageExt = strtolower($pathinfo['extension']);
            
            if (!$this->_isAllowedImageExtension($imageExt, $imageUrl)) {
                return false;
            }

            $destFilename = wp_unique_filename(
                $this->uploadImagesDir['path'],
                $pathinfo['basename']
            );
            
            $destPath = $this->uploadImagesDir['path'].'/'.$destFilename;
    
            $this->copyImageFromUrl($imageUrl, $destPath);
    
            if (!file_exists($destPath)) {
                $message = __(
                    'Couldn\'t download file %s.', 
                    $this->languageDomain
                );
                
                $this->newPostErrors[] = sprintf($message, $imageUrl);
                return false;
            }
    
            $this->newPostImagePaths[] = array(
                'path' => $destPath,
                'source' => $imageUrl
            );
        }
    } // end updateProductImagesByUrl
    
    protected function copyImageFromUrl($imageUrl, $destPath)
    {
        if ($this->_isAllowUrlFopen()) {
            $result = @copy($imageUrl, $destPath);
            
            if (!$result) {
                $message = __(
                    'Error Encountered while attempting to download %s',
                    $this->languageDomain
                );
                $this->newPostErrors[] = sprintf($message, $imageUrl);
            }
        } elseif (function_exists('curl_init')) {
            $this->_copyImagesWithCurlFunction($imageUrl, $destPath);
        }
    } // end copyImageFromUrl
    
    private function _copyImagesWithCurlFunction($imageUrl, $destPath)
    {
        $ch = curl_init($imageUrl);
        $fp = fopen($destPath, "wb");

        $options = array(
            CURLOPT_FILE => $fp,
            CURLOPT_HEADER => 0,
            CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_TIMEOUT => 60
        );

        curl_setopt_array($ch, $options);
        curl_exec($ch);
        
        $result = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        $httpStatus = intval($result);
        
        curl_close($ch);
        
        fclose($fp);

        if ($httpStatus == 200) {
            return true;    
        }
        
        unlink($destPath);
        
        $message = __(
            'HTTP status %s encountered while attempting to download %s',
            'woo-product-importer'
        );
        
        $this->newPostErrors[] = sprintf($message, $httpStatus, $imageUrl);
    } // end _copyImagesWithCurlFunction
    
    private function _isAllowUrlFopen()
    {
        return ini_get('allow_url_fopen');
    } // end _isAllowUrlFopen
    private function _isAllowedImageExtension($imageExt, $imageUrl)
    {
        $allowedExtensions = array('jpg', 'jpeg', 'gif', 'png');
        
        if (in_array($imageExt, $allowedExtensions)) {
            return true;
        }
        
        $message = __(
            'A valid file extension wasn\'t found in %s. Extension '.
            'found was %s. Allowed extensions are: %s.', 
            $this->languageDomain
        );
        
        $this->newPostErrors[] = sprintf(
            $message,
            $imageUrl,
            $imageExt,
            implode( ',', $allowedExtensions)
        );

        return false;
    } // end _isAllowedImageExtension
    
    public function updatePostTerms()
    {
        foreach($this->newPostTerms as $tax => $termIds) {
            wp_set_object_terms($this->newPostId, $termIds, $tax);
        }
    } // end updatePostTerms
    
    public function updateProductAttributes()
    {
        if ($this->exsistProduct !== null) {
            $this->setProductExistsAttributes();
        }
        
        $result = add_post_meta(
            $this->newPostId, 
            '_product_attributes',
            $this->newPostCustomFields,
            true
        );
            
        if (!$result) {
            update_post_meta(
                $this->newPostId, 
                '_product_attributes',
                $this->newPostCustomFields
            ); 
        }
    } // end updateProductAttributes
    
    public function setProductExistsAttributes()
    {
        $existingProductAttributes = get_post_meta(
            $this->newPostId,
            '_product_attributes',
            true
        );
        
        if (!is_array($existingProductAttributes)) {
            return false;
        }

        $maxPosition = 0;
        foreach($existingProductAttributes as $fieldSlug => $fieldData) {
            $position = intval($fieldData['position']);
            $maxPosition = max($position, $maxPosition);
        }
        
        foreach($this->newPostCustomFields as $fieldSlug => $fieldData) {
            $result = $this->_hasValueInDataArray(
                $fieldSlug,
                $existingProductAttributes
            );
            
            if ($result) {
                continue;
            }
            
            $this->newPostCustomFields[$fieldSlug]['position'] = ++$maxPosition;
        }
        $this->newPostCustomFields = array_merge(
            $existingProductAttributes,
            $this->newPostCustomFields
        );
    } // end setProductExistsAttributes
    
    public function updatePostMeta()
    {
        foreach($this->newPostMeta as $key => $value) {
            $result = add_post_meta($this->newPostId, $key, $value, true);
            
            if (!$result) {
               update_post_meta($this->newPostId, $key, $value); 
            }
        }
    } // end updatePostMeta
    
    private function _isValidateNewPostId()
    {
        if (is_wp_error($this->newPostId)) {
            $message = __(
                'Couldn\'t insert product with name %s.',
                $this->languageDomain
            );
            
            $this->newPostErrors = sprintf(
                $message,
                $this->newPost['post_title']
            );
            return false;
        }
        
        if ($this->newPostId == 0) {
            $message = __(
                'Couldn\'t update product with ID %s.',
                $this->languageDomain
            );
            
            $this->newPostErrors = sprintf(
                $message,
                $this->newPost['ID']
            );
            return false;
        }
        
        return true;        
    } // _isValidateNewPostId
    
    public function insertPost()
    {
        $this->newPost = array_merge(
            $this->newPostDefaults,
            $this->newPost
        );
        
        $this->newPostMeta = array_merge(
            $this->newPostMetaDefaults,
            $this->newPostMeta
        );

        $this->newPostId = wp_insert_post($this->newPost, true);
        
        $this->newPostMessages[] = sprintf(
            __('Insert product with ID %s.', $this->languageDomain),
            $this->newPostId
        );
    } // end insertPost
    
    public function updatePost()
    {
        $this->newPostMessages[] = sprintf(
            __('Updating product with ID %s.', $this->languageDomain),
            $this->exsistProduct->ID
        );
    
        $this->newPost['ID'] = $this->exsistProduct->ID;
        $this->newPostId = wp_update_post($this->newPost);
    } // end updatePost
    
    public function getImportReport()
    {
        $reportData  = array(
            'post_id' => '',
            'name' => '',
            'sku' => '',
            'has_errors' => false,
            'errors' => $this->newPostErrors,
            'has_messages' => false,
            'messages' => $this->newPostMessages,
            'success' => $this->newPostInsertSuccess
        );
        
        if ($this->newPostId) {
            $reportData['post_id'] = $this->newPostId;
        }
        
        if (isset($this->newPost['post_title'])) {
            $reportData['name'] = $this->newPost['post_title'];    
        }

        if (isset($this->newPostMeta['_sku'])) {
            $reportData['sku'] = $this->newPostMeta['_sku'];    
        }
        
        if (sizeof($this->newPostErrors) > 0) {
            $reportData['has_errors'] = true;    
        }
        
        if (sizeof($this->newPostMessages) > 0) {
            $reportData['has_messages'] = true;    
        }
        
        return $reportData;
    } // end getImportReport
    
    private function _hasValidateProductNameInData()
    {
        return array_key_exists('post_title', $this->newPost)
               && strlen($this->newPost['post_title']) > 0;
    } // end _hasValidateProductNameInData
    
    public function setUserRolePriceForProduct()
    {
        if (!$this->newPricesByUserRole) {
            return false;
        }
        
        $festiValue = array();
        
        if ($this->exsistProduct !== null) {
            
            $productId = $this->exsistProduct->ID;
            
            $festiValue = get_post_meta(
                $productId,
                'festiUserRolePrices',
                true
            );

            if (!is_null($festiValue) && !is_array($festiValue)) {
                $festiValue = json_decode($festiValue, true);
            }
        }
        
        if (!is_array($festiValue)) {
            $festiValue = array();
        }
        
        $this->newPricesByUserRole = array_merge(
            $festiValue,
            $this->newPricesByUserRole
        );
        
        return json_encode($this->newPricesByUserRole);
    } // end setUserRolePriceForProduct
    
    private function _getExsistProduct()
    {
        $var = $this->newPostMeta;

        if (!$this->_hasValueInDataArray('_sku', $var)
            || empty($var['_sku'])) {
            return null;
        }
            
        $existingPostQuery = array(
            'numberposts' => 1,
            'meta_key' => '_sku',
            'meta_query' => array(
                array(
                    'key' =>'_sku',
                    'value' => $this->newPostMeta['_sku'],
                    'compare' => '='
                )
            ),
            'post_type' => 'product'
        );
        
        $existingPosts = get_posts($existingPostQuery);
        
        if (!is_array($existingPosts) || sizeof($existingPosts) <= 0) {
            return null;
        }
        
        return array_shift($existingPosts);
    } // end _getExsistProduct
    
    public function setStockStatusForProduct()
    {
        $var = $this->newPostMeta;
        
        if ($this->_hasValueInDataArray('_stock_status', $var)) {
            $result = $var['_stock_status'] == 'instock';    
            return ($result) ? 'instock' : 'outofstock';
        }

        if (!$this->_hasValueInDataArray('_stock', $var)) {
            return false;
        }

        return (intval($var['_stock']) > 0) ? 'instock' : 'outofstock';
    } // end setStockStatusForProduct
    
    public function setManageStockForProduct()
    {
        $var = $this->newPostMeta;

        if (!$this->_hasValueInDataArray('_manage_stock', $var)) {
            return false;
        }
   
        $result = $this->_hasValueInDataArray('_stock', $var);

        return ($result) ? 'yes' : 'no';
    }// end setManageStockForProduct
    
    public function setPriceForProduct()
    {
        if ($this->_hasValueInDataArray('_sale_price', $this->newPostMeta)) {
            return $this->newPostMeta['_sale_price'];
        } 
        
        if ($this->_hasValueInDataArray('_regular_price', $this->newPostMeta)) {
            return $this->newPostMeta['_regular_price'];
        }
        
        return false;
    } // end setPriceForProduct
    
    private function _hasValueInDataArray($value, $data) {
        return array_key_exists($value, $data);
    } // _hasValueInDataArray
    
    private function _deleteMappingOptionsGroup()
    {
        $onlyOptions = array();
        
        foreach ($this->mapingOptions as $item) {
            $onlyOptions = array_merge($onlyOptions, $item['options']);
        }
        
        return $onlyOptions;
    } // end _deleteMappingOptionsGroup
    
    private function _isValidateValue($value, $mapTo)
    {
        $result = array_key_exists(
            'validationValues',
            $this->mapingOptions[$mapTo]
        );
        
        if (!$result) {
            return true;
        }
        
        $methodName = $this->mapingOptions[$mapTo]['validationValues'];

        $method = array($this->_engine->mapingOptions, $methodName);
        
        if (!is_callable($method)) {
            throw new Exception("Undefined method name: ".$methodName);
        }

        $values = call_user_func_array($method, array());
        
        if (!$values) {
            throw new Exception($methodName."Not return Values");
        }

        return in_array($value, $values);
    } // end _isValidateValue
    
    private function _isNotImportColum($key)
    {
        return $key == 'do_not_import';
    } // end _isNotImportColum
    
    private function _getDefaultPostData()
    {
        $postData = array(
            'post_type' => 'product',
            'post_status' => 'publish',
            'post_title' => '',
            'post_content' => '',
            'menu_order' => 0,
        );
        
        return $postData;
    } // end _getDefaultPostData
    
    private function _getDefaultPostMetaData()
    {      
        $postMetaData = array(
            '_visibility' => 'visible',
            '_featured' => 'no',
            '_weight' => 0,
            '_length' => 0,
            '_width' => 0,
            '_height' => 0,
            '_sku' => '',
            '_stock' => '',
            '_sale_price' => '',
            '_sale_price_dates_from' => '',
            '_sale_price_dates_to' => '',
            '_tax_status' => 'taxable',
            '_tax_class' => '',
            '_purchase_note' => '',
            '_downloadable' => 'no',
            '_virtual' => 'no',
            '_backorders' => 'no',
            'festiUserRolePrices' => ''
        );
        
        return $postMetaData;
    } // end _getDefaultPostMetaData
}
