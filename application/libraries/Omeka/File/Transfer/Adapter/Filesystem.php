<?php 
class Omeka_File_Transfer_Adapter_Filesystem extends Omeka_File_Transfer_Adapter_Abstract
{    
    /**
     * Set of info about the file to be transferred.
     * 
     * @param array $fileInfo In addition to the defaults, this may contain a 
     * 'rename' = (boolean) flag, which indicates defaults to false and indicates
     * whether or not to attempt to move the file instead of copying it.
     **/
    public function setFileInfo(array $fileInfo)
    {
        if (!array_key_exists('rename', $fileInfo)) {
            $fileInfo['rename'] = false;
        }
        
        parent::setFileInfo($fileInfo);
    }
    
    public function getOriginalFileName()
    {
        return basename($this->_getSource());
    }
    
    public function transferFile($destination)
    {
        $source = $this->_getSource();
        
        if ($this->_fileInfo['rename']) {
            rename($source, $destination);
        } else {
            copy($source, $destination);
        }        
    }
    
    public function isValid()
    {
        $source = $this->_getSource();
        
        if ($this->_fileInfo['rename']) {
            if (!is_writable(dirname($source))) {
                throw new Exception("File's parent directory is not writable or does not exist: $source");
            }
            if (!is_writable($source)) {
                throw new Exception("File is not writable or does not exist: $source");
            }
        } else {
            if (!is_readable($source)) {
                throw new Exception("File is not readable or does not exist: $source");
            }
        }
    }
}