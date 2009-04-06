<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/
 
/**
 * Ingest URLs into the Omeka filesystem.
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2009
 **/
class Omeka_File_Ingest_Url extends Omeka_File_Ingest_Source
{
    protected $_transferMethods = array('wget', 'copy');
        
    protected function _getOriginalFilename($fileInfo)
    {
        if (!($original = parent::_getOriginalFilename($fileInfo))) {
            $original = $fileInfo['source'];
        }
        return $original;
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
    
    protected function _transfer($source, $destination)
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
        
    protected function _fileIsValid($info)
    {
        $source = $this->_getFileSource($info);
        if (!fopen($source, 'r')) {
            throw new Exception("URL is not readable or does not exist: $source");
        }
    }    
}
