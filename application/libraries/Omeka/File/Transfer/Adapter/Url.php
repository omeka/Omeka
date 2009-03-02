<?php 
/**
* 
*/
class Omeka_File_Transfer_Adapter_Url implements Omeka_File_Transfer_Adapter_Interface
{
    protected $_fileInfo;
    
    protected $_transferMethods = array('wget', 'copy');
    
    public function setFileInfo(array $fileInfo)
    {
        $this->_fileInfo = $fileInfo;
    }
    
    public function getOriginalFileName()
    {
        return $this->_fileInfo['source'];
    }
        
    protected function _wget($source, $destination)
    {
        if (!$this->_isWgetAvailable()) {
            return false;
        }
        
        // Only create the file if the URL is valid, otherwise the -O option 
        // will create an empty file, which is not expected behavior.
        $sourceArg      = escapeshellarg($source);
        $destinationArg = escapeshellarg($destination);
        $command        = "wget -O $destinationArg $sourceArg";
        exec($command, $output, $returnVar);
        
        return ($returnVar === 0);
    }
    
    protected function _copy($source, $destination)
    {
        if (!$this->_canCopyFromUrl($source)) {
            return false;
        }

        return copy($source, $destination);
    }

    protected function _isWgetAvailable()
    {
        exec('which wget', $output, $returnVar);
        return !empty($output);      
    }
        
    protected function _canCopyFromUrl($source)
    {
        // Only throw an exception here because this is our fallback.
        if (!ini_get('allow_url_fopen')) {
            throw new Exception('fopen stream wrappers must be enabled in order to copy files from a URL!');
        }
        
        return true;
    }
    
    public function transferFile($source, $destination)
    {
        $transferred = false;
        foreach ($this->_transferMethods as $method) {
            $classMethod = '_' . $method;
            if ($transferred = $this->$classMethod($source, $destination)) {
                break;
            }
        }
        
        if (!$transferred) {
            throw new Exception('Could not transfer the file from "' . $source 
                              . '" to "' . $destination . '"!');
        }
    }
    
    public function isValid()
    {
        $valid = fopen($this->_fileInfo['source'], 'r');
        if (!$valid) {
            throw new Exception("URL is not readable or does not exist: {$this->_fileInfo['source']}");
        }
    }
}