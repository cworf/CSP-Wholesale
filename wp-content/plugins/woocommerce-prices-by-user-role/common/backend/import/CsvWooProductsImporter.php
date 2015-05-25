<?php

class CsvWooProductsImporter
{
    private $_action = 'upload';
    private $_engine;
    protected $fileSystem;
    private $_reader;
    private $_skuManager;
    public $mapingOptions;
    public $importLimit = 5;
    
    
    public function __construct(
        WooUserRolePricesBackendFestiPlugin $engine
    )
    {
        if ($this->_hasActionInRequest()) {
            $this->_action = $_GET['action'];
        }
        
        add_action(
            'wp_ajax_importProductData',
            array($this, 'onImportProductDataAction')
        );

        $this->_engine = $engine;
        
        add_action(
            'admin_print_scripts',
            array($this, 'onInitJsAction')
        );
        
        add_action(
            'admin_print_styles',
            array($this, 'onInitCssAction')
        );
        
        $this->onInitCsvReader();
        $this->onInitCsvMappingOptions();
    } // end __construct
    
    public function onImportProductDataAction()
    {
        $importConfig = $this->_engine->getOptions('import_config');

        $offset = $_POST['offset'];

        $filePath = $importConfig['filePath'];
        $delimiter = $importConfig['csvSeparator'];        
        $this->_reader = new CsvReaderComponent($filePath, $delimiter);
        
        $importData = $this->_getLimitImportData(
            $offset,
            $importConfig
        );
        
        $this->onInitSkuManager();
        
        $this->_skuManager = new WooProductsSkuManager(
            $importConfig,
            $this,
            $this->_engine->_languageDomain
        );
        
        
        $report = $this->_doProcessImportData($importData);

        $vars = array(
            'reportData' => $report
        );

        $content = $this->_engine->fetch(
            'import/report_table_part.phtml',
            $vars
        );
        
        $vars = array(
            'content' => $content
        );

        wp_send_json($vars);
        exit();
    } // end onImportProductDataAction
    
    private function _doProcessImportData($importData)
    {
        $report = array();
        
        foreach ($importData as $key => $row) {
            $result = $this->_skuManager->start($row);
            $report[$key] = $result;
        }

        return $report;
    } // end _doProcessImportData
    
    
    public function onInitSkuManager() {
        if (!class_exists('WooProductsSkuManager')) {
            $filename = 'common/backend/import/WooProductsSkuManager.php';
            require_once $this->_engine->getPluginPath().$filename;
        }
    } // end onInitSkuManager
    
    private function _getLimitImportData($offset, $importConfig)
    {
        $currentRow = 0;
        $importData = array();

        if ($this->isFirstRowHeader($importConfig)) {
            $offset++; 
        }
        
        $rowNum = 0;
        
        while ($currentRow < ($offset + $importConfig['limit'])) {
            $rowNum++;
            
            $row = $this->_reader->getNextRow();
            
            if ($row === false) {
                break;
            }
            
            if ($currentRow < $offset) {
                $currentRow++;
                continue;
            }
            
            $importData[$rowNum] = $row;
            $currentRow++;
        }

        return $importData;
    } // end _getLimitImportData
    
    public function onInitJsAction()
    {
        $this->_engine->onEnqueueJsFileAction(
            'festi-user-role-prices-admin-import-'.$this->_action,
            'import/'.$this->_action.'.js',
            'jquery',
            $this->_engine->_version,
            true
        );
        
        $vars = array(
            'ajaxurl' => admin_url('admin-ajax.php'),
        );

        $options = $this->_engine->getOptions('import_config');
        
        if (is_array($options)) {
            $vars = array_merge($vars, $options);
        };
        
        wp_localize_script(
            'festi-user-role-prices-admin-import-'.$this->_action,
            'fesiImportOptions',
            $vars
        );
    } // end onInitJsAction
    
    public function onInitCssAction()
    {
        $this->_engine->onEnqueueCssFileAction(
            'festi-user-role-prices-admin-import-'.$this->_action,
            'import/'.$this->_action.'.css',
            array(),
            $this->_engine->_version
        );
    } // end onInitCssAction
    
    public function doAction()
    {
        $this->fileSystem = $this->_engine->getFileSystemInstance();

        $methodName = 'displayImport'.ucfirst($this->_action).'Page';

        $method = array($this, $methodName);
        
        if (!is_callable($method)) {
            throw new Exception("Undefined method name: ".$methodName);
        }
        
        try {
            call_user_func_array($method, array());
        } catch (Exception $e) {
            $message = $e->getMessage();
            $this->_engine->displayError($message);
            $this->_action = 'upload';
            $this->doAction();
        }
    } // end doAction
    
    private function _hasCompleteOptionInImportConfig()
    {
        $importConfig = $this->_engine->getOptions('import_config');

        return in_array('complete', $importConfig);
    } // end _hasCompleteOptionInImportConfig
    
    private function _hasActionInRequest()
    {
        return array_key_exists('action', $_GET)
               && !empty($_GET['action']);
    } // end _hasActionInRequest
    
    public function onInitCsvReader()
    {
        if (!class_exists('CsvReaderComponent')) {
            $filename = 'common/backend/import/CsvReaderComponent.php';
            require_once $this->_engine->getPluginPath().$filename;
        }
    } // end onInitCsvReader
    
    public function onInitCsvMappingOptions()
    {
        if (!class_exists('WooMappingImportOptions')) {
            $filename = 'backend/import/WooMappingImportOptions.php';
            require_once $this->_engine->getPluginPath().'common/'.$filename;
        }
        
        $this->mapingOptions = new WooMappingImportOptions(
            $this->_engine->_languageDomain
        );
    } // end onInitCsvMappingOptions

    public function displayImportUploadPage()
    {
        $params = array(
            'refresh_completed' => '',
            'refresh_plugin' => '',
            'delete_role' => '',
            'action' => 'preview'
        );
        
        $vars = array(
            'url' => $this->_engine->getUrl($params),
            'fields' => $this->_getUploadFields()
        );
        
        echo $this->_engine->fetch('import/import_upload_page.phtml', $vars);

        $this->_updateImportOptions(array(), true);
    } // end displayImportUploadPage
    
    public function displayImportResultPage()
    {
        $options = $this->_engine->getOptions('import_config');
        
        $vars = array(
            'rowsCount' => $options['rowsCount']
        );
                  
        echo $this->_engine->fetch('import/import_result_page.phtml', $vars);

        $this->_updateImportOptions($_POST);
    } // end  displayImportResultPage
    
    private function _getUploadFields()
    {
        if (!class_exists('ImportUploadFields')) {
            $filename = 'common/backend/import/ImportUploadFields.php';
            require_once $this->_engine->getPluginPath().$filename;
        }
        
        $importFields = new ImportUploadFields($this->_engine->_languageDomain);
        
        return $importFields->get();  
    } // end _getUploadFields
    
    public function displayImportPreviewPage()
    {
        $filePath = $this->_getFilePathFromRequest();
        $delimiter = $this->_getDelimiter();
        $decimalSeparator = $this->_getDecimalSeparator();      
        $this->_reader = new CsvReaderComponent($filePath, $delimiter);
        
        if (!$this->_isValidateDelimiter($delimiter)) {
            $this->_fetchException(
                "Is not a valid CSV field separator"
            );
        }

        $this->_reader->resetHandle();
                
        $importData = $this->getImportData();

        $params = array(
            'action' => 'result'
        );

        $vars = array(
            'url' => $this->_engine->getUrl($params),
            'isFirstRowHeader' => $this->isFirstRowHeader($_POST),
            'importData' => $importData,
            'mapingOptions' => $this->mapingOptions->get()
        );
        
        echo $this->_engine->fetch('import/import_preview_page.phtml', $vars);

        $values = array(
            'filePath' => $filePath,
            'rowsCount' => $this->_getCountRowsOfImportData($importData),
            'limit' => $this->importLimit,
        );
        
        $values = array_merge($values, $_POST);

        $this->_updateImportOptions($values);
    } // end displayImportPreviewPage
    
    private function _getCountRowsOfImportData($importData)
    {
        $count = count($importData);
        
        if ($this->isFirstRowHeader($_POST)) {
            $count--;
        }
        
        return $count;
    } // end _getCountRowsOfImportData
    
    private function _updateImportOptions($values, $new = false)
    {
        $options = $this->_engine->getOptions('import_config');

        if (!$new && $options) {
            $values = array_merge($options, $values);
        }
        
        $this->_engine->updateOptions('import_config', $values);
    } // end _updateImportOptions
    
    public function getImportData()
    {
        $data = array();
        
        $count = 0;
        
        while (($row = $this->_reader->getNextRow()) !== false) {
            $count++;
            $data[] = $row;
        }
        
        if ($this->isFirstRowHeader($_POST)) {
            $count--;
        }
        
        if (!$count) {
            $this->_fetchException(
                "No data to import"
            );
        }
        
        return $data;
    } // end getImportData
    
    public function isFirstRowHeader($options)
    {
        return array_key_exists('isFirtsRowHeader', $options);
    } // end isFirstRowIsHeader
    
    private function _isValidateDelimiter($delimiter)
    {
        if(strlen($delimiter) > 1) {
            return false;
        }

        $row = $this->_reader->getNextRow();
        
        return count($row) > 1;
    } // end _isValidateDelimiter
    
    private function _getDelimiter()
    {
        if (!$this->_hasDelimiterInRequest()) {
            $this->_fetchException(
                "CSV field separator can not be empty"
            );
        }
        
        $delimiter = $_POST['csvSeparator'];
        
        return $delimiter;
    } // end _getDelimiter
    
    private function _getDecimalSeparator()
    {
        if (!$this->_hasDecimalSeparatorInRequest()) {
            $this->_fetchException(
                "Decimal Separator field can not be empty"
            );
        }
        
        $decimalSeparator = $_POST['csvSeparator'];
        
        return $decimalSeparator;
    } // end _getDelimiter
    
    private function _hasDelimiterInRequest()
    {
        return array_key_exists('csvSeparator', $_POST)
               && !empty($_POST['csvSeparator']);
    } // end _hasDelimiterInRequest
    
    private function _hasDecimalSeparatorInRequest()
    {
        return array_key_exists('decimalSeparator', $_POST)
               && !empty($_POST['decimalSeparator']);
    } // end _hasDecimalSeparatorInRequest
    
    private function _getFilePathFromRequest()
    {
        if ($this->_hasFileUrlInRequest()) {
            if (!$this->_isAllowedImportFileExtension($_POST['importUrl'])) {
                $this->_fetchException(
                    "Sorry, your file extension is not correct!"
                );
            }
            return $_POST['importUrl'];
        }        
        
        if (!$this->_hasFileInRequest()) {
            throw new Exception(
                "You have not selected a file or insert url to Import"
            );
        }
        
        $fileName = $_FILES['importFile']['name'];
        
        if (!$this->_isAllowedImportFileExtension($fileName)) {
            $this->_fetchException(
                "Sorry, your file extension is not correct!"
            );
        }
        
        $uploadDir = $this->getUploadDir();
        
        if (!$this->fileSystem->exists($uploadDir)) {
            $result = $this->fileSystem->mkdir($uploadDir, 0777);
            
            if (!$result) {
                $this->_fetchException(
                    "Could not create upload directory ".$uploadDir
                );
            }
        }
        
        $filePath = $this->getUploadDir('price_by_user_role_import.csv');
        
        $result = $this->fileSystem->move(
            $_FILES['importFile']['tmp_name'],
            $filePath,
            true
        );

        if (!$result) {
            $this->_fetchException("Could not move file to folder ".$uploadDir);
        }

        return $filePath;
    } // end _getFilePathFromRequest
    
    private function _fetchException($text)
    {
        $message = __(
            $text,
            $this->_engine->_languageDomain
        );
        
        throw new Exception( 
            $message
        );
    } // end _fetchException
    
    private function _isAllowedImportFileExtension($fileName)
    {
        $ext = pathinfo($fileName, PATHINFO_EXTENSION);

        return in_array($ext, array('csv'));
    } // end _isAllowedImportFileExtension
    

    private function _hasFileInRequest()
    {
        return isset($_FILES)
        && array_key_exists("importFile", $_FILES)
        && $_FILES['importFile']['name'];
    } // end _hasFileInRequest
    
    private function _hasFileUrlInRequest()
    {
        return array_key_exists('importUrl', $_POST)
               && !empty($_POST['importUrl']);
    } // end _hasFileUrlInRequest
    
    protected function getUploadDir($fileName = '')
    {
        $uploadDir = wp_upload_dir();
        return $uploadDir['basedir'].'/'.$fileName;
    } // end getUploadDir
    
}
