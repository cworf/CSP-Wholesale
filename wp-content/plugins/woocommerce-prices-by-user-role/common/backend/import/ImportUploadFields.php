<?php
class ImportUploadFields extends CsvWooProductsImporter
{
    private $_languageDomain = '';
    
    public function __construct($languageDomain)
    {
        $this->_languageDomain = $languageDomain;
    } // end __construct
    
    public function get()
    {
        $settings = array(
            'importFile' => array(
                'caption' => __(
                    'File to Import',
                    $this->_languageDomain
                ),
                'type' => 'input_file'
            ),
            'importUrl' => array(
                'caption' => __(
                    'URL to Import',
                    $this->_languageDomain
                ),
                'type' => 'input_text',
                'class' => 'festi-user-role-prices-import-url',
                'hint' => __(
                    'Enter the full URL to a CSV file. Leave this field '.
                    ' blank if uploading a file.',
                    $this->_languageDomain
                ),
            ),
            'isFirtsRowHeader' => array(
                'type' => 'input_checkbox',
                'lable' => __(
                    'First Row is Header',
                    $this->_languageDomain
                ),
            ),
            'uploadFolderPath' => array(
                'type' => 'text',
                'caption' => __(
                    'Path to Uploads Folder',
                    $this->_languageDomain
                ),
                'text' => __(
                    $this->getUploadDir(),
                    $this->_languageDomain
                ),
                'value' => $this->getUploadDir()
            ),
            'csvSeparator' => array(
                'caption' => __(
                    'CSV field separator',
                    $this->_languageDomain
                ),
                'type' => 'input_text',
                'value' => ',',
                'hint' => __(
                    'Enter the character used to separate each field in your '.
                    'CSV',
                    $this->_languageDomain
                ),
            ),
            'categorySeparator' => array(
                'caption' => __(
                    'Category hierarchy separator',
                    $this->_languageDomain
                ),
                'type' => 'input_text',
                'value' => '/',
                'hint' => __(
                    'Enter the character used to separate categories in a '.
                    'hierarchical structure',
                    $this->_languageDomain
                ),
            ),
            'decimalSeparator' => array(
                'caption' => __(
                    'Decimal Separator',
                    $this->_languageDomain
                ),
                'type' => 'input_text',
                'value' => '.',
                'hint' => __(
                    'Enter the decimal separator of prices.',
                    $this->_languageDomain
                ),
            ),
        );
        return $settings;
    } // end get
}