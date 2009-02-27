<?php 
/**
* A wrapper for file upload/transfer to the Omeka archive.
*/
class Omeka_File_Ingest
{
    protected static $_archiveDirectory = FILES_DIR;
    
    /**
     * Ingest files from URLs.
     * 
     * Uses Wget command line utility. Supports HTTP, HTTPS, and FTP protocols.
     * 
     * @param Item $item The item object to which the files belong
     * @param array|string $urls The URL and/or filename list
     * - 'http://example.com/image.png';
     * - array('url'      => 'http://example.com', 
     *         'filename' => 'example.html');
     * - array(array('url'      => 'http://example.com/image.png', 
     *               'filename' => 'image.png'), 
     *         'http://exmaple.com/document.pdf');
     * @param array $options List of options
     * - ignore_invalid_urls: set to true to skip over invalid URLs; set to 
     *   false to throw an exception when encountering an invalid URL (default 
     *   is false) 
     */
    public static function url($item, $urls, array $options = array())
    {
        // Build the $urls array.
        if (is_string($urls)) {
            $urls = array(array('url' => $urls));
        }
        if (array_key_exists('url', $urls)) {
            $urls = array($urls);
        }
        
        // Set the default options.
        if (!array_key_exists('ignore_invalid_urls', $options)) {
            $options['ignore_invalid_urls'] = false;
        }
        
        // Iterate the URLs.
        $files = array();
        foreach ($urls as $url) {
            
            // Build the $url array.
            if (is_string($url)) {
                $url = array('url' => $url);
            }
            if (!array_key_exists('filename', $url)) {
                $url['filename'] = $url['url'];
            }
            $url['file_path'] = self::getFilePath($url['filename']);
            
            // Check to see if the URL is valid.
            $valid = fopen($url['url'], 'r');
            
            // If the URL is invalid AND ignore_invalid_urls is false, throw an 
            // exception.
            if (!$valid && !$options['ignore_invalid_urls']) {
                throw new Exception("URL is not valid or does not exist: {$url['url']}");
            }
            
            // If the URL is invalid, continue to the next URL.
            if (!$valid) {
                continue;
            }
            
            // Only create the file if the URL is valid, otherwise the -O option 
            // will create an empty file, which is not expected behavior.
            $filePathArg = escapeshellarg($url['file_path']);
            $urlArg      = escapeshellarg($url['url']);
            $command     = "wget -O $filePathArg $urlArg";
            exec($command, $output, $returnVar);
            
            // Create the file object.
            $files[] = self::_createFile($item, $url['file_path'], $url['filename']);
        }
        return $files;
    }
        
    // It would be smart to check if the file is able to be copied/moved prior 
    // to copying/moving it. If not, then throw an error. Also, check if the 
    // file exists!
    public static function filesystem($item, $paths, $options = array())
    {
        $files = array();
        foreach ($paths as $path) {

            $filePath = self::getFilePath($path);
            
            if (!isset($options['type'])) {
                $options['type'] = 'copy';
            }
            
            switch ($options['type']) {
                case 'move':
                    rename($path, $filePath);
                    break;
                case 'copy':
                default:
                    if (!copy($path, $filePath)) {
                        exit;
                    }
                    break;
            }
            
            $files[] = self::_createFile($item, $filePath, basename($path));
        }
        return $files;
    }
    
    public static function upload($item)
    {
        $upload = new Zend_File_Transfer_Adapter_Http;
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
    
    protected static function getFilePath($file)
    {
        $filenameFilter = new Omeka_Filter_Filename;
        $fileName = $filenameFilter->renameFileForArchive($file);
        return self::$_archiveDirectory . DIRECTORY_SEPARATOR . $fileName;
    }
}
