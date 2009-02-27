<?php 
/**
* A wrapper for file upload/transfer to the Omeka archive.
*/
class Omeka_File_Ingest
{
    protected static $_archiveDirectory = FILES_DIR;
    
    /**
     * Process all of the internals related to uploading a file through Omeka.
     * 
     * The list of things that has to happen includes:
     *  1) Move the file to its final location in the archive/files directory.
     *  2) Set all of the default values to be stored in the 'files' table.
     *  3) Create derivative images based on this file, if applicable.
     *  4) Extract and store additional metadata depending on the MIME type of the file.
     * 
     * @internal There is a lot of duplication between this and the
     * moveToFileDir() method, which I suspect is used exclusively by the
     * Dropbox plugin. We should factor that out so that there is only method
     * that can be called from anywhere within the Omeka environment. This should
     * also be extensible by plugins so that plugins could potentially redefine
     * information about how the files are stored (perhaps storing in a database,
     * or uploading to Flickr, etc.).
     * @return void
     **/
    public function upload($form_name, $index) {
        
        $tmp             = $_FILES[$form_name]['tmp_name'][$index];
        $name            = $_FILES[$form_name]['name'][$index];
        
        $path = $this->moveFileToArchive($tmp, $name);
        
        $this->setDefaults($path);
        
        // 'mime_browser' is also set in the setDefaults() method, but this may override that value.
        $this->mime_browser = $_FILES[$form_name]['type'][$index];
        $this->original_filename = $name;
        
        $this->createDerivativeImages($path);
        
        $this->extractMimeMetadata($path);
    }
    
    /**
     * Move a file from wherever it is (typically in a temporary upload 
     * directory, or if you're a plugin, anywhere else) to the archive/files 
     * directory.
     * 
     * @throws Omeka_Upload_Exception
     * @param string The path to the file's current location.
     * @param string The name that the file should be given when stored in the
     * archive/ directory. This will be sanitized and have nonsense appended to
     * it to avoid naming collisions.
     * @return string The full path to the location that file has been moved to.
     **/
    public function moveFileToArchive($oldFilePath, $newFilename, $isUpload = true)
    {
        $newFilePath = FILES_DIR . DIRECTORY_SEPARATOR . $this->renameFileForArchive($newFilename);
        
        if (is_uploaded_file($oldFilePath)) {
            // Moving uploaded files through PHP requires this special function.
            if (!move_uploaded_file($oldFilePath, $newFilePath)) {
                // The file could not be moved for some reason.
                throw new Omeka_Upload_Exception("Uploaded file could not be saved to the filesystem.  Please notify an administrator.");
            }
        } else if ($isUpload) {
            // If this is flagged as an upload, but PHP doesn't think it's a
            // valid upload, throw an error.
            throw new Omeka_Upload_Exception("Path to uploaded file is not valid.  This may indicate a possible upload attack.");
        } else {
            // Otherwise this is indicated as not an upload, so just move the file.
            rename($oldFilePath, $newFilePath);
        }
        
        return $newFilePath;
    }
    
    /**
     * This could be refactored to combine better with upload().  It goes through 
     * the same flow as upload(), the only different being that the original 
     * filename is set directly rather than being extracted from the $_FILES array.
     * 
     * This may be used exclusively by the Dropbox plugin, in which case it should
     * be factored out in favor of a public interface that a plugin could use to
     * simulate the same behavior.  At the very least, the code in this method 
     * could be copied directly into the plugin so that it wouldn't be repeated 
     * in the core codebase.
     * 
     * @param string
     * @param string
     * @return void
     **/
    public function moveToFileDir($oldpath, $name) {        
        $path = $this->moveFileToArchive($oldpath, $name);
        
        $this->setDefaults($path);
        
        $this->original_filename = $name;
        
        $this->createDerivativeImages($path);
        
        $this->extractMimeMetadata($path);
    }
    
    
    public static function uploadForItem(Item $item, $fileFieldName)
    {        
        $zfUpload = new Zend_File_Transfer_Adapter_Http;
        $zfUpload->setDestination(self::$_archiveDirectory);
        
        // Add filters to rename the file to something archive-friendly.
        $filenameFilter = new Omeka_Filter_Filename;
        $zfUpload->addFilter($filenameFilter);
        
        // Grab the info from $_FILES array (prior to receiving the files).
        // Also validate the file uploads (will throw exception if failed).
        $origFileInfo = $zfUpload->getFileInfo('file') and $zfUpload->isValid();
        
        // Ingest the files into the archive directory.
        if (!$zfUpload->receive()) {
            throw new Omeka_Validator_Exception(join("\n\n", $zfUpload->getMessages()));
        }
        
        foreach ($origFileInfo as $fileKey => $info) {
            $file = new File;
            try {
                $file->original_filename = $info['name'];
                $file->item_id = $item->id;
                $filePath = $zfUpload->getFileName($fileKey);
                $file->setDefaults($filePath);
                // If there is an error in saving this file to the database,
                // don't bother creating derivative images or extracting MIME
                // type metadata for it.
                $file->forceSave();
                fire_plugin_hook('after_upload_file', $file, $item);
            } catch(Exception $e) {
                if (!$file->exists()) {
                    $file->unlinkFile();
                }
                throw $e;
            }
        }
    }
}
