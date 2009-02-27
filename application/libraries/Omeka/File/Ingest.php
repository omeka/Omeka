<?php 
/**
* A wrapper for file upload/transfer to the Omeka archive.
*/
class Omeka_File_Ingest
{
    protected $_archiveDirectory;
    
    /**
     * Strip out whitespace, non-printable characters, extra . characters, and 
     * convert all spaces to dashes.  This is applied to every file that is uploaded
     * to Omeka so that there will be no problems with funky characters in filenames.  
     * 
     * @todo It may be easier just to generate a long string of random numbers 
     * and characters for each new file, rather than actually trying to maintain 
     * the old filename, which is still stored in the database.  This would only 
     * be an issue if the archives directory needs to be human-readable, and there 
     * is no guarantee that it does.
     * 
     * @param string
     * @return string
     **/
    public function sanitizeFilename($name)
    {
        //Strip whitespace
        $name = trim($name);
        
        /*    Remove all but last .
            I wish there was an easier way of doing this */
        if(substr_count($name,'.') > 1) {
            $array = explode('.',$name);
            if(count($array) > 2) {
                $last = array_pop($array);
                $first = join('', $array);
                $name = array();
                if(!empty($first)) {
                    $name = $first;
                }
                if(!empty($last)) {
                    $name .= '.'.$last;
                }
            }
        }
        
        //Strip out invalid characters
        $invalid = array('"','*','/',':','<','>','?','|',"'",'&',';','#','\\');
        $name = str_replace($invalid, '', $name);
        
        //Strip out non-printable characters
        for ($i = 0; $i < 32; $i++) { 
            $nonPrintable[$i] = chr($i);
        }
        $name = str_replace($nonPrintable, '', $name);
        
        //Convert to lowercase (avoid corrupting UTF-8)
        $name = strtolower($name);
        
        //Convert remaining spaces to hyphens
        $name = str_replace(' ', '-', $name);
        
        return $name;
    }
    
    /**
     * Discover all the potential errors for uploaded files before going through the 
     * arduous process of actually uploading them.
     *
     * Throws an error for the first problem it finds.
     *
     * @throws Omeka_Upload_Exception
     * @return void
     **/
    public function handleUploadErrors($file_form_name)
    {    
        
        $file_form = $_FILES[$file_form_name];
        //$file_form = $_POST['file'];
        // Check the $_FILES array for errors
        foreach ($file_form['error'] as $key => $error) {
            
            if ($error != UPLOAD_ERR_OK) {
                
                switch( $error ) {

                    // 1 - File exceeds upload size in php.ini
                    // 2 - File exceeds upload size set in MAX_FILE_SIZE
                    case UPLOAD_ERR_INI_SIZE:
                    case UPLOAD_ERR_FORM_SIZE:
                        throw new Omeka_Upload_Exception(
                            $_FILES[$file_form_name]['name'][$key] . ' exceeds the maximum file size.' . $_FILES[$file_form_name]['size'][$key]
                        );
                    break;
                    
                    // 3 - File partially uploaded
                    case UPLOAD_ERR_PARTIAL:
                        throw new Omeka_Upload_Exception(
                            $_FILES[$file_form_name]['name'][$key] . ' was only partially uploaded.  Please try again.'
                        );
                    break;
                    
                    //4 - No file was provided on the form
                    case UPLOAD_ERR_NO_FILE:
                        //Make sure this doesn't upload and gum up the works, otherwise ignore it
                        unset($_FILES[$file_form_name]['error'][$key]);
                        unset($_FILES[$file_form_name]['name'][$key]);
                        unset($_FILES[$file_form_name]['type'][$key]);
                        unset($_FILES[$file_form_name]['tmp_name'][$key]);
                        unset($_FILES[$file_form_name]['size'][$key]);
                        continue;
                        //throw new Omeka_Upload_Exception( 'No file was uploaded!' );
                    break;
                    
                    // 6 - Missing Temp folder
                    // 7 - Can't write file to disk
                    case UPLOAD_ERR_NO_TMP_DIR:
                    case UPLOAD_ERR_CANT_WRITE:
                        throw new Omeka_Upload_Exception(
                            'There was a problem saving the files to the server.  Please contact an administrator for further assistance.'
                        );
                    break;
                }                
            }
            
            //Otherwise the file was uploaded correctly, so check to see if it is an image
            if (getimagesize($file_form['tmp_name'][$key])) {
                self::checkOmekaCanMakeDerivativeImages();
            }
        }
        
        self::checkIfPostSizeExceedsLimit();
                
        //Check directory permissions
        //@todo Replace this with a call to the DB to retrieve the paths to the upload directories        
        $writable_directories = array(FILES_DIR, FULLSIZE_DIR, 
                                      THUMBNAIL_DIR, SQUARE_THUMBNAIL_DIR);
        foreach ($writable_directories as $dir) {
            if (!is_dir($dir)) {
                throw new Omeka_Upload_Exception ("The $dir directory does not exist on the filesystem.  Please create this directory and have a systems administrator");
            }
            if (!is_writable($dir)) {
                throw new Omeka_Upload_Exception ('Unable to write to '. $dir . ' directory; improper permissions');
            }
        }        
    }
    
    /**
     * This method supposedly determines whether or not the POST size has been
     *  exceeded for a given file upload.
     *
     * @todo I'm not convinced that it actually works, but will have to be
     * tested further to determine whether it should remain in the codebase.
     * 
     * @return boolean
     **/
    protected static function checkIfPostSizeExceedsLimit()
    {
        //Check whether the POST upload content size is too big
        $POST_MAX_SIZE = ini_get('post_max_size');
        $mul = substr($POST_MAX_SIZE, -1);
        $mul = ($mul == 'M' ? 1048576 : ($mul == 'K' ? 1024 : ($mul == 'G' ? 1073741824 : 1)));
        if ($_SERVER['CONTENT_LENGTH'] > $mul * (int)$POST_MAX_SIZE && $POST_MAX_SIZE) {
            throw new Omeka_Upload_Exception('The size of uploaded files exceeds the maximum size allowed by your hosting provider (' . $POST_MAX_SIZE . ')');
        }        
    }
    
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
     * Sanitize the filename and append a set of random characters to the end of
     *  the filename but before the suffix.
     * 
     * @param string
     * @return string
     **/
    public function renameFileForArchive($name) {
        
        $name = $this->sanitizeFilename($name);
        
        $new_name     = explode('.', $name);
        $new_name[0] .= '_' . substr(md5(mt_rand() + microtime(true)), 0, 10);
        $new_name_string = implode('.', $new_name);
        
        return $new_name_string;
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
        
}
