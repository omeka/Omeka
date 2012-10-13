<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Represents the detected MIME types of a file.
 * 
 * @package Omeka\File\MimeType\Detect
 */
class Omeka_File_MimeType_Detect
{
    /**
     * @var string The full path to the file.
     */
    protected $_file;
    
    /**
     * @var array The MIME type detection strategies in priority order.
     */
    protected $_strategies = array();
    
    /**
     * @var string The definitive MIME type of this file.
     */
    protected $_mimeType;
    
    /**
     * @var array The MIME types of this file, one for each detection strategy.
     */
    protected $_mimeTypes = array();
    
    /**
     * @var array A list of MIME types that are deemed ambiguous.
     */
    protected $_ambiguousMimeTypes = array(
        'text/plain', 
        'application/octet-stream', 
        'application/x-empty', 
        'regular file',
    );
    
    /**
     * Set the required properties for detecting the MIME types of a file.
     * 
     * @param string|File $file The full path to the file or a File record.
     * @param array $strategies An array of file detection strategies in 
     * priority order. If none are passed, a default list will be set. All 
     * strategies must implement Omeka_File_MimeType_Detect_StrategyInterface.
     */
    public function __construct($file, array $strategies = array())
    {
        // Set the file path if a File record was passed.
        if ($file instanceof File) {
            $file = $file->getPath();
        }
        
        $file = trim($file);
        
        // Check for a valid file.
        if (!is_file($file)) {
            throw new Omeka_File_MimeType_Exception("The file \"$file\" does not exist.");
        }
        
        $this->_file = $file;
        
        if (empty($strategies)) {
            // Set the default strategies, in default priority order.
            $strategies = array(
                new Omeka_File_MimeType_Detect_Strategy_Fileinfo, 
                new Omeka_File_MimeType_Detect_Strategy_FileCommand, 
                new Omeka_File_MimeType_Detect_Strategy_MimeContentType, 
                new Omeka_File_MimeType_Detect_Strategy_GetId3, 
                new Omeka_File_MimeType_Detect_Strategy_Browser, 
            );
        } else {
            // Validate the passed strategies.
            foreach ($strategies as $strategy) {
                if (!($strategy instanceof Omeka_File_MimeType_Detect_StrategyInterface)) {
                    throw new Omeka_File_MimeType_Exception('Invalid MIME type detection strategy passed to Omeka_File_MimeType_Detect.');
                }
            }
        }
        
        $this->_strategies = $strategies;
    }
    
    /**
     * Detect the MIME type of this file.
     * 
     * @return string The definitive MIME type.
     */
    public function detect()
    {
        foreach ($this->_strategies as $strategy) {
            
            // Use the strategy to detect the MIME type.
            $mimeType = $strategy->detect($this->_file);
            
            // If the detection returns false, assume the strategy is invalid.
            if (false === $mimeType) {
                continue;
            }
            
            // Remove all properties, retaining only the type and subtype.
            $mimeTypeParts = explode(';', $mimeType);
            $mimeType = trim($mimeTypeParts[0]);
            
            // Cache the MIME type, keyed to the strategy class name.
            $this->_mimeTypes[get_class($strategy)] = $mimeType;
            
            // Set the definitive MIME type if it's not ambiguous and if it 
            // hasn't already been detected by a previous strategy.
            if (!in_array($mimeType, $this->_ambiguousMimeTypes) && !$this->_mimeType) {
                $this->_mimeType = $mimeType;
            }
        }
        
        // If no strategy detects a definitive MIME type, set the MIME type to 
        // the one detected by the highest priority strategy. If there are no 
        // valid strategies, this sets the definitive MIME type to the generic 
        // "application/octet-stream".
        if (!$this->_mimeType) {
            $fallbackMimeType = reset($this->_mimeTypes);
            $this->_mimeType = (false === $fallbackMimeType) ? 'application/octet-stream' : $fallbackMimeType;
        }
        
        // Return the definitive MIME type.
        return $this->_mimeType;
    }
    
    /**
     * Get the definitive MIME type of this file.
     * 
     * @return string
     */
    public function getMimeType()
    {
        return $this->_mimeType;
    }
    
    /**
     * Get the MIME types of this file, one for each detection strategy.
     * 
     * @return array
     */
    public function getMimeTypes()
    {
        return $this->_mimeTypes;
    }
}
