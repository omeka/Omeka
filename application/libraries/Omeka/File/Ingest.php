<?php
class Omeka_File_Ingest
{
    protected static $_archiveDirectory = FILES_DIR;
    
    protected $_item;
    protected $_files = array();
    protected $_options = array();
    protected $_type;
    
    public function __construct(Item $item, $files = array(), $options = array())
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
         // Set the default options.
        if (!array_key_exists('ignore_invalid_files', $options)) {
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
        if (!array_key_exists('type', $this->_options)) {
            $this->_options['type'] = 'copy';
        }
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
            
            // If the file is invalid, throw an error or continue to the next file.
            if (!$this->_isValid($file)) {
                continue;
            }
            
            $this->_saveFile($file['source'], $file['destination']);
            
            // Create the file object.
            $fileObjs[] = $this->_createFile($file['destination'], $file['filename']);
        }
        return $fileObjs;
    }
    
    // Check to see if the file is valid.
    protected function _isValid($file)
    {
        $ignore = $this->_options['ignore_invalid_files'];
        switch ($this->_type) {
            case 'url':
                $valid = fopen($file['source'], 'r');
                if (!$valid && !$ignore) {
                    throw new Exception("URL is not readable or does not exist: {$file['source']}");
                }
                break;
            case 'filesystem':
                switch ($this->_options['type']) {
                    case 'move':
                        $valid = is_writable(dirname($file['source']));
                        if (!$valid && !$ignore) {
                            throw new Exception("File's parent directory is not writable or does not exist: {$file['source']}");
                        }
                        $valid = is_writable($file['source']);
                        if (!$valid && !$ignore) {
                            throw new Exception("File is not writable or does not exist: {$file['source']}");
                        }
                        break;
                    case 'copy':
                    default:
                        $valid = is_readable($file['source']);
                        if (!$valid && !$ignore) {
                            throw new Exception("File is not readable or does not exist: {$file['source']}");
                        }
                        break;
                }
                break;
            default;
                throw new Exception('Invalid file transfer type.');
                break;
        }
        return $valid;
    }
    
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
                switch ($this->_options['type']) {
                    case 'move':
                        rename($source, $destination);
                        break;
                    case 'copy':
                    default:
                        copy($source, $destination);
                        break;
                }
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