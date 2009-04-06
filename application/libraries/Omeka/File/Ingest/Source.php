<?php
/**
 * This abstract class encapsulates all the behavior that facilitates file 
 * ingest based on the assumption that each file can be retrieved via a
 * string containing both the name and location of that file.  
 * 
 * Applies to: URLs, file paths on a server.
 * Does not apply to: direct HTTP uploads.
 * 
 * Also, if the original filename is not properly represented by the source 
 * identifier (incorrect file extension, etc.), a more accurate filename can be
 * provided via the 'filename' attribute.
 * 
 * @package Omeka
 * @copyright Center for History and New Media, 2009
 **/
abstract class Omeka_File_Ingest_Source extends Omeka_File_Ingest_Abstract
{
    /**
     * The 'source' key of the file info is parsed out by default.
     * 
     * @param array
     * @return string
     **/
    protected function _getFileSource($fileInfo)
    {
        return $fileInfo['source'];
    }
    
    /**
     *  Files can be represented as one of the following: 
     *      A) a string, representing the source identifier for a single file, 
     *      B) an array containing a 'source' key, 
     *      C) an array of strings, or 
     *      D) an array of arrays that each contain a 'source' key.
     * 
     * @param mixed
     * @return array
     **/
    protected function _parseFileInfo($files)
    {
        $infoArray = array();
        
        if (is_array($files)) {
            // If we have an array representing a single source.
            if (array_key_exists('source', $files)) {
                $infoArray = array($files);
            } else {
                foreach ($files as $key => $value) {
                    // Convert an array of strings, an array of arrays, or a 
                    // mix of the two, into an array of arrays.
                    $infoArray[$key] = !is_array($value) 
                                          ? array('source'=>$value) 
                                          : $value;
                }
            }
        // If it's a string, make sure that represents the 'source' attribute.
        } else if (is_string($files)) {
            $infoArray = array(array('source' => $files));
        } else {
            throw new Omeka_File_Ingest_Exception('File info is incorrectly formatted!');
        }
        
        return $infoArray;
    }
    
    /**
     * Retrieve the original filename.
     * 
     * By default, this is stored as the 'name' attribute in the array.
     * 
     * @param array
     * @return string
     **/
    protected function _getOriginalFilename($fileInfo)
    {
        if (array_key_exists('name', $fileInfo)) {
            return $fileInfo['name'];
        }
    }
    
    /**
     * Transfer the file to the Omeka archive.
     * 
     * TODO Intercept this file and put it in a temporary directory, run validation
     * on it, and then move to the archive and delete the temporary file.
     * FIXME Multiple stages of validation may or may not be a design flaw.  It
     * seems like the first stage of validation determines whether the source
     * is valid, and the second set of validation determines whether the file
     * itself adheres to security criteria (can be used to add a plugin hook).
     * @param array
     * @param string
     * @return string
     **/
    protected function _transferFile($info, $originalFilename)
    {        
        $fileSourcePath = $this->_getFileSource($info);
        $this->_validateSource($fileSourcePath, $info);
        
        $fileDestinationPath = $this->_getDestination($originalFileName);
        $this->_transfer($fileSourcePath, $fileDestinationPath);
        return $fileDestinationPath;
    }
    
    abstract protected function _transfer($source, $destination);
    
    abstract protected function _validateSource($source, $info);
}
