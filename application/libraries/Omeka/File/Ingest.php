<?php
class Omeka_File_Ingest
{
    protected static $_archiveDirectory = FILES_DIR;
    
    protected $_item;
    protected $_files;
    protected $_options;
    protected $_type;
    
    public function __construct(Item $item, $files = array(), $options = array())
    {
        $this->_item = $item;
        $this->_files = $files;
        $this->_options = $options;

        // Build the $files array.
        if (is_string($this->_files)) {
            $this->_files = array(array('source' => $this->_files));
        }
        if (array_key_exists('source', $this->_files)) {
            $this->_files = array($this->_files);
        }
        
        // Set the default options.
        if (!array_key_exists('ignore_invalid_files', $this->_options)) {
            $this->_options['ignore_invalid_files'] = false;
        }
        
     }
    
    public function url()
    {
        // Handle url-specific options here.
        $this->_type = 'url';
        $this->_ingest();
    }
    
    public function filesystem()
    {
        // Handle filesystem-specific options here.
        $this->_type = 'filesystem';
        $this->_ingest();
    }
    
    protected function _ingest()
    {
        // Iterate the files.
        $fileObjs = array();
        foreach ($this->_files as $file) {
            
            // Build the $file array.
            if (is_string($file)) {
                $file = array('source' => $file);
            }
            $file['filename']    = $this->_getFilename($file);
            $file['destination'] = $this->_getDestination($file);
            
            // Check to see if the file is valid.
            $valid = fopen($file['source'], 'r');
            
            // If the file is invalid AND ignore_invalid_files is false, throw 
            // an exception.
            if (!$valid && !$options['ignore_invalid_files']) {
                throw new Exception("File is not readable or does not exist: {$file['source']}");
            }
            
            // If the file is invalid, continue to the next file.
            if (!$valid) {
                continue;
            }
            
            $this->_saveFile($file['source'], $file['destination']);
            
            // Create the file object.
            $fileObjs[] = $this->_createFile($file['destination'], $file['filename']);
        }
        return $fileObjs;
    }
    
    // TODO: pass the $_FILES['foobar'] name into $upload->getFileInfo() & $upload->receive()
    public function upload($fileFormName)
    {
        $upload = new Zend_File_Transfer_Adapter_Http;
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
            $files[] = $this->_createFile($upload->getFileName($key), $info['name']);
        }
        return $files;
    }
    
    protected function _saveFile($source, $destination)
    {
        switch ($this->_type) {
            case 'url':
                // Only create the file if the URL is valid, otherwise the -O option 
                // will create an empty file, which is not expected behavior.
                $sourceArg      = escapeshellarg($source);
                $destinationArg = escapeshellarg($destination);
                $command        = "wget -O $destinationArg $sourceArg";
                exec($command, $output, $returnVar);
                break;
            case 'filesystem':
                copy($source, $destination);
                break;
            default:
                throw new Exception('No file transfer type given.');
                break;
        }
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
    
    protected function _getFilename($file)
    {
        if (array_key_exists('filename', $file)) {
            return $file['filename'];
        }
        switch ($this->_type) {
            case 'url':
                return $file['source'];
            case 'filesystem':
            default:
                return basename($file['source']);
        }
    }
    
    protected function _getDestination($file)
    {
        $filter = new Omeka_Filter_Filename;
        $filename = $filter->renameFileForArchive($file['filename']);
        return self::$_archiveDirectory . DIRECTORY_SEPARATOR . $filename;
    }
}