<?php 
class Omeka_File_Transfer_Adapter_Filesystem implements Omeka_File_Transfer_Adapter_Interface
{
    /**
     * Set of info about the file to be transferred.
     * 
     * @var array $fileInfo In addition to the defaults, this may contain a 
     * 'rename' = (boolean) flag, which indicates defaults to false and indicates
     * whether or not to attempt to move the file instead of copying it.
     **/
    protected $_fileInfo;
    
    public function setFileInfo(array $fileInfo)
    {
        if (!array_key_exists('rename', $fileInfo)) {
            $fileInfo['rename'] = false;
        }
        
        $this->_fileInfo = $fileInfo;
    }
    
    public function getOriginalFileName()
    {
        return basename($this->_fileInfo['source']);
    }
    
    public function transferFile($destination)
    {
        $source = $this->_fileInfo['source'];
        
        if ($this->_fileInfo['rename']) {
            rename($source, $destination);
        } else {
            copy($source, $destination);
        }        
    }
    
    public function isValid()
    {
        if ($this->_fileInfo['rename']) {
            $valid = is_writable(dirname($this->_fileInfo['source']));
            if (!$valid) {
                throw new Exception("File's parent directory is not writable or does not exist: {$this->_fileInfo['source']}");
            }
            $valid = is_writable($this->_fileInfo['source']);
            if (!$valid) {
                throw new Exception("File is not writable or does not exist: {$this->_fileInfo['source']}");
            }
        } else {
            $valid = is_readable($this->_fileInfo['source']);
            if (!$valid) {
                throw new Exception("File is not readable or does not exist: {$this->_fileInfo['source']}");
            }
        }
    }
}