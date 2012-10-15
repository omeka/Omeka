<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Implements ingesting files from the local filesystem.
 * 
 * @package Omeka\File\Ingest
 */
class Omeka_File_Ingest_Filesystem extends Omeka_File_Ingest_AbstractSourceIngest
{
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
        $result = copy($source, $destination);

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
        if (!is_readable($source)) {
            throw new Omeka_File_Ingest_InvalidException("File is not readable or does not exist: $source");
        }
    }
}
