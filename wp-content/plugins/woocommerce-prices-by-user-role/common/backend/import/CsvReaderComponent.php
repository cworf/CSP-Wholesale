<?php
class CsvReaderComponent
{
    private $_delimiter;
    private $_handle;

    public function __construct($file, $delimiter)
    {
        $this->_handle = $this->_getHandle($file);
        $this->delimiter = $delimiter;
    } // end __construct
    
    public function getNextRow()
    {
        return fgetcsv($this->_handle, 0, $this->delimiter);
    } // end getNextRow
    
    public function resetHandle()
    {
        fseek($this->_handle, 0);
    } // end resetHandle
    
    private function _getHandle($file)
    {
        $handle = fopen($file, "r");
        if ($handle === false) {
            $message = "Error reading file";
            throw new Exception($message);
        }
        
        return $handle;
    } // end _getHandle
    
    public function __destruct()
    {
        fclose($this->_handle);
    } // end __destruct
}
