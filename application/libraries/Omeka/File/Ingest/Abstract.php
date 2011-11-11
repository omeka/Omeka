<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2009-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */

/**
 * An abstract class that handles ingesting files into the Omeka archive and
 * database.  
 * 
 * Specific responsibilities handled by this class:
 * - Parsing/validating arbitrary inputs that somehow identify the files to 
 *   be ingested.
 * - Iterating through the parsed file information, validating, and 
 *   transferring each file to the Omeka archive.
 * - Inserting a new record into the files table that corresponds to the
 *   transferred file's metadata.
 * - Returning a collection of the records associated with the ingested
 *   files.
 *
 * Typical usage is via the factory() method:
 * 
 * <code>
 * $ingest = Omeka_File_Ingest_Abstract::factory('Url', $item);
 * $fileRecords = $ingest->ingest('http://www.example.com');
 * </code>
 *
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @see ItemBuilder::addFiles()
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2009-2010
 */
abstract class Omeka_File_Ingest_Abstract
{
    /**
     * @var Item
     */
    protected $_item;
    
    /**
     * Set of arbitrary options to use when ingesting files.
     *
     * @var array
     */
    protected $_options = array();
    
    /**
     * Set of validators implementing Zend_Validate_Interface.
     * 
     * @var array
     * @see Omeka_File_Ingest_Abstract::addValidator()
     */
    private $_validators = array();

    /**
     * Set the item to use as a target when ingesting files.
     * 
     * @param Item $item
     * @return void
     */        
    public function setItem(Item $item)
    {
        $this->_item = $item;
    }
    
    /**
     * Factory to retrieve Omeka_File_Ingest_* instances.
     * 
     * @param string $adapterName Ingest adapter.
     * @param Item $item
     * @param array $options
     * @return Omeka_File_Ingest_Abstract
     */
    final static public function factory($adapterName, $item, $options = array())
    {
        $className = 'Omeka_File_Ingest_' . $adapterName;
        if (class_exists($className, true)) {
            $instance = new $className;
            $instance->setItem($item);
            $instance->setOptions($options);
            return $instance;
        } else {
            throw new Omeka_File_Ingest_Exception('Could not load ' . $className);
        }
    }
    
    /**
     * Retrieve the original filename of the file.
     * 
     * @param array $fileInfo
     * @return string
     */
    abstract protected function _getOriginalFilename($fileInfo);
    
    /**
     * Transfer the file to the archive.  
     * 
     * To indicate validation errors, Omeka_File_Ingest_InvalidException can be
     * thrown at any time.  To indicate other types of non-recoverable errors 
     * related to file ingest, throw Omeka_File_Ingest_Exception.
     * 
     * @param array $fileInfo
     * @param string $originalFilename
     * @throws Omeka_File_Ingest_InvalidException
     * @throws Omeka_File_Ingest_Exception
     * @return string Real path to the transferred file.
     */
    abstract protected function _transferFile($fileInfo, $originalFilename);
        
    /**
     * Ingest classes receive arbitrary information.  This method needs to
     * parse that information into an iterable array so that multiple files
     * can be ingested from a single identifier.
     * 
     * Example use case is Omeka_File_Ingest_Upload.
     * 
     * @internal Formerly known as setFiles()
     * @param mixed $files
     * @return array
     */
    abstract protected function _parseFileInfo($files);
    
    /**
     * Set options for ingesting files.
     * 
     * @param array $options Available options include:  
     * - 'ignore_invalid_files': boolean false by default.  Determine 
     *   whether or not to throw exceptions when a file is not valid.  This can 
     *   be based on a number of factors:  whether or not the original identifier
     *   is valid (i.e. a valid URL), whether or not the file itself is valid
     *   (i.e. invalid file extension), or whether the basic algorithm for 
     *   ingesting the file fails (i.e., files cannot be transferred because the
     *   archive/ directory is not writeable).  
     *   This option is primarily useful for skipping known invalid files when 
     *   ingesting large data sets.
     * @return void
     */        
    public function setOptions($options)
    {
        $this->_options = $options;
        
         // Set the default options.
        if (!array_key_exists('ignore_invalid_files', $options)) {
            $this->_options['ignore_invalid_files'] = false;
        }
    }
    
    /**
     * Ingest based on arbitrary file identifier info.
     * 
     * @param mixed $fileInfo An arbitrary input (array, string, object, etc.)
     * that corresponds to one or more files to be ingested into Omeka.  
     * 
     * If this is an array that has a 'metadata' key, that should be an array
     * representing element text metadata to assign to the file.  See 
     * ActsAsElementText::addElementTextsByArray() for more details.
     * @return array Ingested file records.
     */
    final public function ingest($fileInfo)
    {
        // Don't catch or suppress parsing errors.
        $fileInfoArray = $this->_parseFileInfo($fileInfo);
        
        // Iterate the files.
        $fileObjs = array();
        foreach ($fileInfoArray as $file) {            
            
            try {                
                // This becomes the file's identifier (stored in the 
                // 'original_filename' column and used to derive the archival filename).
                $originalFileName = $this->_getOriginalFilename($file);

                $fileDestinationPath = $this->_transferFile($file, $originalFileName);

                // Create the file object.
                if ($fileDestinationPath) {
                    $fileMetadata = isset($file['metadata']) 
                        ? $file['metadata'] : array();
                    $fileObjs[] = $this->_createFile($fileDestinationPath, $originalFileName, $fileMetadata);
                }
            
            } catch (Omeka_File_Ingest_InvalidException $e) {
                if ($this->_ignoreIngestErrors()) {
                    $this->_logException($e);
                    continue;
                } 
                
                // If not suppressed, rethrow it.
                throw $e;
            }
        }
        return $fileObjs;
    }
    
    /**
     * Determine whether or not to ignore file ingest errors.  Based on 
     * 'ignore_invalid_files', which is false by default.
     * 
     * @return boolean
     */
    private function _ignoreIngestErrors()
    {
        return (boolean)$this->_options['ignore_invalid_files'];
    }
    
    /**
     * Log any exceptions that are thrown as a result of attempting to ingest
     * invalid files.
     * 
     * These are logged as warnings because they are being ignored by the script,
     * so they don't actually kill the file ingest process.
     * 
     * @param Exception $e
     * @return void
     */
    private function _logException(Exception $e)
    {
        $logger = Omeka_Context::getInstance()->getLogger();
        if ($logger) {
            $logger->log($e->getMessage(), Zend_Log::WARN);
        }
    }
        
    /**
     * Insert a File record corresponding to an ingested file and its metadata.
     * 
     * @param string $newFilePath Path to the file within Omeka's archive.
     * @param string $oldFilename The original filename for the file.  This will
     * usually be displayed to the end user.
     * @param array $elementMetadata See ActsAsElementText::addElementTextsByArray()
     * for more information about the format of this array.
     * @uses ActsAsElementText::addElementTextsByArray()
     * @return File
     */        
    private function _createFile($newFilePath, $oldFilename, $elementMetadata = array())
    {
        $file = new File;
        try {
            $file->original_filename = $oldFilename;
            
            $file->setDefaults($newFilePath);
            
            if ($elementMetadata) {
                $file->addElementTextsByArray($elementMetadata);
            }
            
            fire_plugin_hook('after_upload_file', $file, $this->_item);
            fire_plugin_hook('after_ingest_file', $file, $this->_item);
            
            $this->_item->addFile($file);
            
        } catch(Exception $e) {
            if (!$file->exists()) {
                $file->unlinkFile();
            }
            throw $e;
        }
        return $file;
    }
    
    /**
     * Retrieve the destination path for the file to be transferred.
     * 
     * This will generate an archival filename in order to prevent naming 
     * conflicts between ingested files.
     * 
     * This should be used as necessary by Omeka_File_Ingest_Abstract 
     * implementations in order to determine where to transfer any given file.
     * 
     * @param string $fromFilename The filename from which to derive the 
     * archival filename. 
     * @return string
     */    
    protected function _getDestination($fromFilename)
    {
        $filter = new Omeka_Filter_Filename;
        $filename = $filter->renameFileForArchive($fromFilename);

        $storage = Zend_Registry::get('storage');
        $dir = $storage->getTempDir();
        
        if (!is_writable($dir)) {
            throw new Omeka_File_Ingest_Exception('Cannot write to the following directory: "'
                              . $dir . '"!');
        }
        return $dir . '/' . $filename;
    }
    
    /**
     * Add Zend Framework file validators.
     * 
     * Emulates the way Zend Framework adds validators.
     * 
     * @param Zend_Validate_Interface $validator
     * @return Omeka_File_Ingest_Abstract
     */
    public function addValidator(Zend_Validate_Interface $validator)
    {        
        $this->_validators[] = $validator;
        
        return $this;
    }
    
    /**
     * Validate a file that has been transferred to Omeka.
     * 
     * Implementations of Omeka_File_Ingest_Abstract should use this to validate 
     * the uploaded file based on user-defined security criteria.
     * 
     * Important: $fileInfo may need to contain the following keys in order to work
     * with particular Zend_Validate_File_* validation classes:
     * - 'name': string filename (for Zend_Validate_File_Extension) If 
     *   ZF is unable to determine the file extension when validating, it will 
     *   check the 'name' attribute instead.  Current use cases involve saving the 
     *   file to a temporary location before transferring to the Omeka archive.  
     *   Most temporary files do not maintain the original file extension.
     * - 'type': string MIME type (for Zend_Validate_File_MimeType) If ZF
     *   is unable to determine the mime type from the transferred file.  Unless 
     *   the server running Omeka has a mime_magic file or has installed the 
     *   FileInfo extension, this will be necessary.
     *  
     * @internal These required keys may be derived from existing data if 
     * necessary, rather than forcing the end user to include them in the array 
     * of file info that is passed to the ingest script.  See 
     * Omeka_File_Ingest_Source::_addZendValidatorAttributes() for more info.
     * 
     * @throws Omeka_File_Ingest_InvalidException
     * @param string $filePath Absolute path to the file.  The file should 
     * be local and readable, which is required by most (if not all) of the
     * Zend_Validate_File_* classes.
     * @param array $fileInfo Set of file info that describes a given file being 
     * ingested. 
     * @return boolean True if valid, otherwise throws an exception.
     */
    protected function _validateFile($filePath, $fileInfo)
    {
        $validationErrors = array();
        foreach ($this->_validators as $validator) {
            // Aggregate all the error messages.
            if (!$validator->isValid($filePath, $fileInfo)) {
                $errorMessages = $validator->getMessages();
                $validationErrors += $errorMessages;
            }
        }
        if (!empty($validationErrors)) {
            throw new Omeka_File_Ingest_InvalidException(join("\n\n", array_values($validationErrors)));
        }
        return true;
    }
}
