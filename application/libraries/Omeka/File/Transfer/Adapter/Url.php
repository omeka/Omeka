<?php
require_once 'Zend/File/Transfer/Adapter/Abstract.php';

class Omeka_File_Transfer_Adapter_Url extends Zend_File_Transfer_Adapter_Abstract
{
    public function __construct(array $files, $options = array())
    {
        $this->_files = $this->_prepareFiles($files);
        
        if (is_array($options)) {
            $this->setOptions($options);
        }
    }
    
    public function send($options = null)
    {
        require_once 'Zend/File/Transfer/Exception.php';
        throw new Zend_File_Transfer_Exception('Method not implemented');
    }
    
    public function receive($files = null)
    {
        $files = $this->_getFiles($files);
        foreach ($files as $file => $content) {
            $destination = $content['destination'] . DIRECTORY_SEPARATOR;
            $filePathArg = escapeshellarg($destination . $content['name']);
            $urlArg = escapeshellarg($content['url']);
            $command = "wget -O $filePathArg $urlArg";
            exec($command);
        }
        return true;
     }

    public function isSent($files = null)
    {
        require_once 'Zend/File/Transfer/Exception.php';
        throw new Zend_File_Transfer_Exception('Method not implemented');
    }

    public function isReceived($files = null)
    {
        require_once 'Zend/File/Transfer/Exception.php';
        throw new Zend_File_Transfer_Exception('Method not implemented');
    }

    public function isFiltered($files = null)
    {
        require_once 'Zend/File/Transfer/Exception.php';
        throw new Zend_File_Transfer_Exception('Method not implemented');
    }
    
    public function isUploaded($files = null)
    {
        require_once 'Zend/File/Transfer/Exception.php';
        throw new Zend_File_Transfer_Exception('Method not implemented');
    }
    
    public function getProgress()
    {
        require_once 'Zend/File/Transfer/Exception.php';
        throw new Zend_File_Transfer_Exception('Method not implemented');
    }

    protected function _prepareFiles(array $files = array())
    {
        $result = array();
        foreach ($files as $key => $url) {
            $pathInfo = pathinfo($url);
            $result[$key]['name']      = $pathInfo['basename'];
            $result[$key]['url']       = $url;
            $result[$key]['options']   = $this->_options;
            $result[$key]['validated'] = false;
            $result[$key]['received']  = false;
            $result[$key]['filtered']  = false;
        }
        return $result;
    }
}