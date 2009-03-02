<?php 
/**
* 
*/
class Omeka_File_Transfer_Adapter_Url implements Omeka_File_Transfer_Adapter_Interface
{
    protected $_fileInfo;
    
    public function setFileInfo(array $fileInfo)
    {
        $this->_fileInfo = $fileInfo;
    }
    
    public function getOriginalFileName()
    {
        return $this->_fileInfo['source'];
    }
    
    public function transferFile($source, $destination)
    {
        // Only create the file if the URL is valid, otherwise the -O option 
        // will create an empty file, which is not expected behavior.
        $sourceArg      = escapeshellarg($source);
        $destinationArg = escapeshellarg($destination);
        $command        = "wget -O $destinationArg $sourceArg";
        exec($command, $output, $returnVar);
    }
    
    public function isValid()
    {
        $valid = fopen($this->_fileInfo['source'], 'r');
        if (!$valid) {
            throw new Exception("URL is not readable or does not exist: {$this->_fileInfo['source']}");
        }
    }
}