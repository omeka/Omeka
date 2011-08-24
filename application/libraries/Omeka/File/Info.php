<?php 
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2009-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */

/**
 * A wrapper for the getID3 library that extracts metadata from files in the 
 * Omeka filesystem.
 *
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2009-2010
 */
class Omeka_File_Info
{
    /**
     * The File record for which to extract metadata.
     * 
     * @var File
     */
    protected $_file;
    
    /**
     * List of MIME types that could be considered ambiguous.
     * 
     * @see Omeka_File_Info::_mimeTypeIsAmbiguous()
     * @var array
     */    
    protected $_ambiguousMimeTypes = array(
        'text/plain', 
        'application/octet-stream', 
        'regular file');
    
    private $_id3;

    /**
     * @param File $file File to get info for.
     */
    public function __construct(File $file)
    {
        $this->_file = $file;
        $this->_filePath = $file->getPath('archive');
    }

    /**
     * References a list of ambiguous mime types from "http://msdn2.microsoft.com/en-us/library/ms775147.aspx".
     * 
     * @param string $mimeType
     * @return boolean
     */
    public function isAmbiguousMimeType($mimeType)
    {
        return (empty($mimeType) || in_array($mimeType, $this->_ambiguousMimeTypes));
    }

    /**
     * Sets the MIME type for the file to the one detected by getID3, but only
     * if the existing MIME type is 'ambiguous' and getID3 can detect a better
     * one.
     *
     * @uses Omeka_File_Info::isAmbiguousMimeType() 
     */
    public function setMimeTypeIfAmbiguous()
    {
        $mimeType = $this->_file->getMimeType();    
        if ($this->isAmbiguousMimeType($mimeType)) {
            // WARNING: this may cause a memory error on large files.
            if ($id3 = $this->_getId3()) {
                $mimeType = $id3->info['mime_type'];
            }
            if ($mimeType) {
                $this->_file->setMimeType($mimeType);
            }
        }
    }

    /**
     * Take a set of Element records and populate them with element text that is 
     * auto-generated based on the getID3 metadata extraction library.
     * 
     * @param array $elements Set of Element records.
     * @param array $id3Info Info extracted from the file by the getID3 library.
     * @param string $extractionStrategy Either 'FilesVideos' or 'FilesImages'.
     * @return void
     */
    protected function _populateMimeTypeElements($elements, $id3Info, $extractionStrategy)
    {
        $helperClass = new $extractionStrategy;
        
        $helperClass->initialize($id3Info, $this->_filePath);
        
        // Loop through the elements provided and extract the auto-generated text
        // for each of them.
        foreach ($elements as $element) {
            // Method that is named the same as the element, which is how the data 
            // gets retrieved. E.g. FilesVideos::getBitrate() for the Bitrate element. 
            
            // Strip out whitespace and prepend 'get' to adhere to naming conventions.
            $helperFunction = 'get' . preg_replace('/\s*/', '', $element->name);
            
            if (!method_exists($helperClass, $helperFunction)) {
                throw new Exception("Cannot retrieve metadata for the element called '$element->name'!");
            }
            $elementText = $helperClass->$helperFunction();
            
            // Don't bother saving element texts with null values.
            if ($elementText) {
                $this->_file->addTextForElement($element, $elementText);
            }
        }        
    }
    
    /**
     * Extract EXIF, IPTC, etc. metadata from the file.
     * 
     * @return boolean False on failure, true on success.
     */
    public function extract()
    {
        if (!is_readable($this->_filePath)) {
            throw new Exception('Could not extract metadata: unable to read file at the following path: "' . $this->_filePath . '"');
        }
        
        // Skip if getid3 did not return a valid object.
        if (!$id3 = $this->_getId3()) {
            return false;
        }
        
        $elements = $this->_file->getMimeTypeElements($this->_file->getMimeType());
        if (empty($elements)) {
            return false;
        }
                
        // Figure out what kind of extraction strategy to use for retrieving the 
        // metadata from ID3. Current possibilities include either FilesImages 
        // or FilesVideos
        $elementSetToExtract = current($elements)->set_name;
        switch ($elementSetToExtract) {
            case 'Omeka Video File':
                $extraction = 'FilesVideos';
                break;
            case 'Omeka Image File':
                $extraction = 'FilesImages';
                break;
            default:
                throw new Exception('Cannot extract metadata for element set: ' . $elementSetToExtract . '.');
                break;
        }
                
        $this->_populateMimeTypeElements($elements, $id3->info, $extraction); 
        
        return true;       
    }
    
    /**
     * Pull down the file's extra metadata via getID3 library.
     *
     * @param string $path Path to file.
     * @return getID3
     */
    private function _getId3()
    {
        // Do not extract metadata if the exif module is not loaded. This 
        // applies to all files, not just files with Exif data -- i.e. images.
        if (!extension_loaded('exif')) {
            return false;
        }
        
        if (!$this->_id3) {
            require_once 'getid3/getid3.php';
            $id3 = new getID3;
            $id3->encoding = 'UTF-8';
            
            try {
                $id3->Analyze($this->_filePath);
                $this->_id3 = $id3;
            } catch (getid3_exception $e) {
                $message = $e->getMessage();
                _log("getID3: $message");
                return false;
            }        
        }
        return $this->_id3;
    }
}
