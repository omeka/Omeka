<?php 
/**
 * An abstract class for Omeka's file transfer adapters. 
 * 
 * This serves only to consolidate duplicated code from the 2 existing transfer
 * adapters.  
 * 
 * The main assumption this class makes is that the information passed
 * to setFileInfo() will be stored as a property of the transfer object.
 * 
 * The second assumption is that the 'source' attribute will always be passed in
 * and will usually mean something to the transfer adapter.  In reality, the 
 * 'source' attribute is not enforced, and a plugin writer can pass in an 
 * arbitrary array containing whatever values the adapter might require.
 * 
 * @package Omeka
 * @copyright Center for History and New Media, 2009
 **/
abstract class Omeka_File_Transfer_Adapter_Abstract implements Omeka_File_Transfer_Adapter_Interface
{
    protected $_fileInfo = array();
    
    public function setFileInfo(array $fileInfo)
    {
        $this->_fileInfo = $fileInfo;
    }
        
    protected function _getSource()
    {
        return $this->_fileInfo['source'];
    }
}