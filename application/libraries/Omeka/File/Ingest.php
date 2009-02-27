<?php 
/**
* A wrapper for file upload/transfer to the Omeka archive.
*/
class Omeka_File_Ingest
{
    protected static $_archiveDirectory = FILES_DIR;

    public static function url($item, $urls, $options = array())
    {
        foreach ($urls as $url) {            
            // Trim the trailing slash so it doesn't think it's a directory.
            // ALSO, slashes are not allowed.
            $originalFilename = str_replace('/', '-', rtrim($url, '/ '));
            $destination = self::$_archiveDirectory . DIRECTORY_SEPARATOR;
            $fileDestArg = escapeshellarg($destination . $originalFilename);
            $urlArg = escapeshellarg($url);
            $command = "wget -O $fileDestArg $urlArg";
            exec($command, $wgetOutput, $returnValue);
        }
    }
        
    public static function filesystem($item, $paths, $options)
    {
        
    }
    
    public static function upload($item, $options)
    {
        $upload = new Zend_File_Transfer_Adapter_Http($options);
        $upload->setDestination(self::$_archiveDirectory);
        
        // Add a filter to rename the file to something archive-friendly.
        $upload->addFilter(new Omeka_Filter_Filename);
        
        // Grab the info from $_FILES array (prior to receiving the files).
        $fileInfo = $upload->getFileInfo();
        
        if (!$upload->receive()) {
            throw new Omeka_Validator_Exception(join("\n\n", $upload->getMessages()));
        }
        
        $files = array();
        foreach ($fileInfo as $key => $info) {
            $files[] = self::_createFile($item, $upload->getFileName($key), $info['name']);
        }
        return $files;
    }
    
    protected static function _createFile($item, $newFilePath, $oldFilename)
    {
        $file = new File;
        try {
            $file->original_filename = $oldFilename;
            $file->item_id = $item->id;
            
            $file->setDefaults($newFilePath);
            
            // Create derivatives and extract metadata.
            // TODO: Move these create images / extract metadata events to 
            // the 'after_file_upload' hook whenever it becomes possible to 
            // implement hooks within core Omeka.
            //$file->createDerivatives();
            //$file->extractMetadata();
            
            $file->forceSave();
            
            fire_plugin_hook('after_upload_file', $file, $item);
            
            $files[] = $file;
            
        } catch(Exception $e) {
            if (!$file->exists()) {
                $file->unlinkFile();
            }
            throw $e;
        }
        
        return $file;
    }
}
