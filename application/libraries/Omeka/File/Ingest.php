<?php
class Omeka_File_Ingest
{
    protected static $_archiveDirectory = FILES_DIR;
    
    protected $_item;
    protected $_files = array();
    protected $_options = array();
    protected $_type;
    
    protected $_adapter;
    
    public function __construct(Item $item, $files = array(), array $options = array())
    {
        $this->_item = $item;
        $this->setFiles($files);
        $this->setOptions($options);
     }
    
    public function setFiles($files)
    {
        if (is_array($files)) {
            foreach ($files as $key => $value) {
                // Convert an array of strings, an array of arrays, or a 
                // mix of the two, into an array of arrays.
                $this->_files[$key] = !is_array($value) 
                                      ? array('source'=>$value) 
                                      : $value;
            }
        // If it's a string, make sure that represents the 'source' attribute.
        } else if (is_string($files)) {
            $this->_files = array(array('source' => $files));
        // Sure hope you know what you're doing.
        } else {
            $this->_files = $files;
        }
    }
    
    public function setOptions($options)
    {
        $this->_options = $options;
        
         // Set the default options.
        if (!array_key_exists('ignore_invalid_files', $options)) {
            $this->_options['ignore_invalid_files'] = false;
        }
    }
    
    public function ingest(Omeka_File_Transfer_Adapter_Interface $transferAdapter)
    {
        $this->_adapter = $transferAdapter;
        
        // Iterate the files.
        $fileObjs = array();
        foreach ($this->_files as $file) {            
            $this->_adapter->setFileInfo($file);
            
            if (!array_key_exists('filename', $file)) {
                $file['filename'] = $this->_adapter->getOriginalFileName();
            }
            
            $fileDestinationPath = $this->_getDestination($file);

            // If the file is invalid, throw an error or continue to the next file.
            if (!$this->_isValid($file)) {
                continue;
            }
            
            $this->_adapter->transferFile($file['source'], $fileDestinationPath);
            
            // Create the file object.
            $fileObjs[] = $this->_createFile($fileDestinationPath, $file['filename']);
        }
        return $fileObjs;
    }
    
    /**
     * Check to see whether or not the file is valid.
     * 
     * @param array $fileInfo
     * @return boolean Return false if we are ignoring invalid files and an 
     * exception was thrown from one of the adapter classes.  
     **/
    protected function _isValid($file)
    {
        $ignore = $this->_options['ignore_invalid_files'];
        
        // If we have set the ignore flag, suppress all exceptions that are 
        // thrown from the adapter classes.
        try {
            $this->_adapter->isValid();
        } catch (Exception $e) {
            if (!$ignore) {
                throw $e;
            }
            return false;
        }
        
        return true;
    }
    
    /**
     * Upload files to Omeka via HTTP POST.  
     * 
     * @internal This uses the Zend Framework's Zend_File_Transfer component to 
     * upload files, which is the reason why it is disconnected from the main 
     * ingest() functionality of this class.  A better design may be able to 
     * encompass both, but the two interfaces seem irreconcilable without 
     * significant changes either way.  Designing a new component that builds off
     * the Zend_File_Transfer_Adapter_Abstract class does not seem viable either,
     * given the current state of the API and documentation.
     * 
     * @param string $fileFormName The parameter name of the file POST.  This
     * typically comes from a form submission containing a file input.
     * @return array Set of File records that have been ingested into Omeka.
     **/
    public function upload($fileFormName)
    {
        // Check if we are supposed to ignore it if there are no uploaded files.
        $adapterOptions = array();
        if ($this->_options['ignoreNoFile']) {
            $adapterOptions['ignoreNoFile'] = true;
        }

        $upload = new Zend_File_Transfer_Adapter_Http($adapterOptions);
        $upload->setDestination(self::$_archiveDirectory);
        
        // Add a filter to rename the file to something archive-friendly.
        $upload->addFilter(new Omeka_Filter_Filename);
        
        // Grab the info from $_FILES array (prior to receiving the files).
        $fileInfo = $upload->getFileInfo($fileFormName);
        
        if (!$upload->receive($fileFormName)) {
            throw new Omeka_Validator_Exception(join("\n\n", $upload->getMessages()));
        }
        
        $files = array();
        foreach ($fileInfo as $key => $info) {
            $pathToArchivedFile = $upload->getFileName($key);
            // Only add files to the database if the file was uploaded.  
            // It would have thrown an exception before if 'ignoreNoFile' was false.
            if ($pathToArchivedFile) {
                $files[] = $this->_createFile($pathToArchivedFile, $info['name']);
            }         
        }
        return $files;
    }
        
    protected function _createFile($newFilePath, $oldFilename)
    {
        $file = new File;
        try {
            $file->original_filename = $oldFilename;
            $file->item_id = $this->_item->id;
            
            $file->setDefaults($newFilePath);
            
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
        
    protected function _getDestination($file)
    {
        $filter = new Omeka_Filter_Filename;
        $filename = $filter->renameFileForArchive($file['filename']);
        if (!is_writable(self::$_archiveDirectory)) {
            throw new Exception('Cannot write to the following directory: "'
                              . self::$_archiveDirectory . '"!');
        }
        return self::$_archiveDirectory . DIRECTORY_SEPARATOR . $filename;
    }
}