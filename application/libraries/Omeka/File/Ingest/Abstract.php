<?php
abstract class Omeka_File_Ingest_Abstract
{
    protected static $_archiveDirectory = FILES_DIR;
    
    protected $_item;
    protected $_options = array();
            
    public function setItem(Item $item)
    {
        $this->_item = $item;
    }
    
    /**
     * Factory to retrieve Omeka_File_Ingest_* instances.
     * 
     * @param string
     * @return Omeka_File_Ingest_Abstract
     **/
    final public function factory($adapterName, $item, $options = array())
    {
        $className = 'Omeka_File_Ingest_' . $adapterName;
        if (class_exists($className, true)) {
            $instance = new $className;
            $instance->setItem($item);
            $instance->setOptions($options);
            return $instance;
        } else {
            throw new Exception('Could not load ' . $className);
        }
    }
    
    /**
     * Retrieve the original filename of the file.
     * 
     * @param array
     * @return string
     **/
    abstract protected function _getOriginalFilename($fileInfo);
    
    /**
     * Transfer the file to the archive.
     * 
     * @param array $fileInfo
     * @param string $originalFilename
     * @return string Real path to the transferred file.
     **/
    abstract protected function _transferFile($fileInfo, $originalFilename);
    abstract protected function _fileIsValid($fileInfo);
    
    /**
     * Ingest classes receive arbitrary information.  This method needs to
     * parse that information into an iterable array so that multiple files
     * can be ingested from a single identifier.
     * 
     * Example use case is Omeka_File_Ingest_Upload.
     * 
     * @internal Formerly known as setFiles()
     * @param mixed $fileInfo
     * @return array
     **/
    abstract protected function _parseFileInfo($files);
            
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
     * @param mixed $fileInfo 
     * @return array Ingested file records.
     **/
    final public function ingest($fileInfo)
    {
        $fileInfoArray = $this->_parseFileInfo($fileInfo);
        
        // Iterate the files.
        $fileObjs = array();
        foreach ($fileInfoArray as $file) {            
            
            // If the file is invalid, throw an error or continue to the next file.
            if (!$this->_isValid($file)) {
                continue;
            }

            // This becomes the file's identifier (stored in the 
            // 'original_filename' column and used to derive the archival filename).
            $originalFileName = $this->_getOriginalFilename($file);
                        
            $fileDestinationPath = $this->_transferFile($file, $originalFileName);
            
            // Create the file object.
            if ($fileDestinationPath) {
                $fileObjs[] = $this->_createFile($fileDestinationPath, $originalFileName, $file['metadata']);
            }
        }
        return $fileObjs;
    }
    
    /**
     * Check to see whether or not the file is valid.
     * 
     * @return boolean Return false if we are ignoring invalid files and an 
     * exception was thrown from one of the adapter classes.  
     **/
    private function _isValid($fileInfo)
    {
        $ignore = $this->_options['ignore_invalid_files'];
        
        // If we have set the ignore flag, suppress all exceptions that are 
        // thrown from the adapter classes.
        try {
            $this->_fileIsValid($fileInfo);
        } catch (Exception $e) {
            if (!$ignore) {
                throw $e;
            }
            return false;
        }
        
        return true;
    }
            
    private function _createFile($newFilePath, $oldFilename, $elementMetadata = array())
    {
        $file = new File;
        try {
            $file->original_filename = $oldFilename;
            $file->item_id = $this->_item->id;
            
            $file->setDefaults($newFilePath);
            
            if ($elementMetadata) {
                $file->addElementTextsByArray($elementMetadata);
            }
            
            $file->forceSave();
            
            fire_plugin_hook('after_upload_file', $file, $this->_item);
            
        } catch(Exception $e) {
            if (!$file->exists()) {
                $file->unlinkFile();
            }
            throw $e;
        }
        return $file;
    }
        
    protected function _getDestination($fromFilename)
    {
        $filter = new Omeka_Filter_Filename;
        $filename = $filter->renameFileForArchive($fromFilename);
        if (!is_writable(self::$_archiveDirectory)) {
            throw new Exception('Cannot write to the following directory: "'
                              . self::$_archiveDirectory . '"!');
        }
        return self::$_archiveDirectory . DIRECTORY_SEPARATOR . $filename;
    }
}
