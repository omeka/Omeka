<?php 
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2009-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */

/**
 * Implements ingesting files from the local filesystem.
 *
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2009-2010
 */
class Omeka_File_Ingest_Filesystem extends Omeka_File_Ingest_Source
{
    /**
     * Set of info about the file to be transferred.
     * 
     * @param array $fileInfo In addition to the defaults, this may contain a 
     * 'rename' = (boolean) flag, which indicates defaults to false and indicates
     * whether or not to attempt to move the file instead of copying it.
     * @return array Iterable info array.
     */
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
     * @param array $info File info array.
     * @return string
     */
    protected function _getOriginalFilename($info)
    {
        if (!($original = parent::_getOriginalFilename($info))) {
            $original = basename($this->_getFileSource($info));
        }
        return $original;
    }
    
    /**
     * Transfer a file.
     *
     * @param string $source Source path.
     * @param string $destination Destination path.
     * @param array $info File info array.  If 'rename' is specified as true,
     * move the file instead of copying.
     * @return void
     */
    protected function _transfer($source, $destination, array $info)
    {        
        if ($info['rename']) {
            $result = rename($source, $destination);
        } else {
            $result = copy($source, $destination);
        }

        if (!$result) {
            throw new Omeka_File_Ingest_Exception("Could not transfer \"$source\" to \"$destination\".");
        }
    }
    
    /**
     * Validate file transfer.
     *
     * @param string $source Source path.
     * @param array $info File info array.
     * @param void
     */
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
}
