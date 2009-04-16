<?php 
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * 
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2009
 **/
class Omeka_File_Ingest_Filesystem extends Omeka_File_Ingest_Source
{
    /**
     * Set of info about the file to be transferred.
     * 
     * @param array $fileInfo In addition to the defaults, this may contain a 
     * 'rename' = (boolean) flag, which indicates defaults to false and indicates
     * whether or not to attempt to move the file instead of copying it.
     **/
    protected function _parseFileInfo($fileInfo)
    {
        $infoArray = parent::_parseFileInfo($fileInfo);
        foreach ($infoArray as $key => $info) {
            if (!array_key_exists('rename', $info)) {
                $infoArray[$key]['rename'] = false;
            }
        }
        
        return $infoArray;
    }
    
    /**
     * Retrieve the original filename of the file to be transferred.
     * 
     * Check for the 'name' attribute first, otherwise extract the basename() 
     * from the given file path.
     * 
     * @param array
     * @return string
     **/
    protected function _getOriginalFilename($info)
    {
        if (!($original = parent::_getOriginalFilename($info))) {
            $original = basename($this->_getFileSource($info));
        }
        return $original;
    }
    
    protected function _transfer($source, $destination)
    {        
        if ($info['rename']) {
            rename($source, $destination);
        } else {
            copy($source, $destination);
        }        
    }
    
    protected function _validateSource($source, $info)
    {        
        if ($info['rename']) {
            if (!is_writable(dirname($source))) {
                throw new Omeka_File_Ingest_InvalidException("File's parent directory is not writable or does not exist: $source");
            }
            if (!is_writable($source)) {
                throw new Omeka_File_Ingest_InvalidException("File is not writable or does not exist: $source");
            }
        } else {
            if (!is_readable($source)) {
                throw new Omeka_File_Ingest_InvalidException("File is not readable or does not exist: $source");
            }
        }
    }
    
    /**
     * Retrieve the MIME type of a file located on the same server as Omeka.
     * 
     * Use mime_content_type() to check the file at its original location.
     * 
     * @param array
     * @return string
     **/
    protected function _getFileMimeType($fileInfo)
    {
        $sourcePath = $this->_getFileSource($fileInfo);
        return mime_content_type($sourcePath);
    }
}
