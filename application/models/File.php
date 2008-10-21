<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

if (!function_exists('mime_content_type')) {
   function mime_content_type($f) {
       return trim(exec('file -bi ' . escapeshellarg ($f))) ;
   }
}

define('IMAGE_DERIVATIVE_EXT', 'jpg');

require_once 'Item.php';
require_once 'ActsAsElementText.php';
require_once 'FileTable.php';
require_once 'FilesImages.php';
require_once 'FilesVideos.php';
require_once 'MimeElementSetLookup.php';

/**
 * @package Omeka
 * @subpackage Models
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class File extends Omeka_Record { 

    public $item_id;
    public $archive_filename;
    public $original_filename;
    public $size = '0';
    public $authentication;
    public $mime_browser;
    public $mime_os;
    public $type_os;
    public $has_derivative_image = '0';
    public $added;
    public $modified;

    
    public function construct()
    {
        $this->_mixins[] = new ActsAsElementText($this);
    }
        
    protected function beforeInsert()
    {
        $this->added = date("Y-m-d H:i:s");
        $this->modified = date("Y-m-d H:i:s");        
    }
    
    protected function beforeUpdate()
    {
        $this->modified = date("Y-m-d H:i:s");
    }
    
    protected function beforeSaveForm(&$post)
    {
        $immutable = array('id', 'modified', 'added', 
                           'authentication', 'archive_filename', 
                           'original_filename', 'mime_browser', 
                           'mime_os', 'type_os', 'item_id');
        foreach ($immutable as $value) {
            unset($post[$value]);
        }
    }
    
    protected function afterSave()
    {
        $this->saveElementTexts();
    }
    
    public function getItem()
    {
        return $this->getTable('Item')->find($this->item_id);
    }
    
    /**
     * Retrieve the path for the file
     *
     * @return string
     **/
    public function getPath($type='fullsize')
    {
        $fn = $this->getDerivativeFilename();
        
        $path = array('fullsize'         => FULLSIZE_DIR.DIRECTORY_SEPARATOR . $fn,
                      'thumbnail'        => THUMBNAIL_DIR.DIRECTORY_SEPARATOR . $fn,
                      'square_thumbnail' => SQUARE_THUMBNAIL_DIR.DIRECTORY_SEPARATOR . $fn,
                      'archive'          => FILES_DIR.DIRECTORY_SEPARATOR . $this->archive_filename);
        
        $hookdata = fire_plugin_hook('append_to_file_path', $path);
        
        if ($hookdata) {
            $path = array_merge($path, $hookdata);
        }
        
        return $path[$type];
    }
    
    /**
     * Retrieve the web path for the file
     *
     * @return void
     **/
    public function getWebPath($type='fullsize')
    {
        $fn = $this->getDerivativeFilename();
        
        $path = array('fullsize'         => WEB_FULLSIZE.'/' . $fn,
                      'thumbnail'        => WEB_THUMBNAILS.'/' . $fn,
                      'square_thumbnail' => WEB_SQUARE_THUMBNAILS.'/' . $fn,
                      'archive'          => WEB_FILES.'/' . $this->archive_filename);
        
        $hookdata = fire_plugin_hook('append_to_file_web_path', $path);
        
        if ($hookdata) {
            $path = array_merge($path, $hookdata);            
        }
        
        return $path[$type];
    }
    
    public function getDerivativeFilename()
    {
        list($base, $ext) = explode('.', $this->archive_filename);
        $fn = $base.'.'.IMAGE_DERIVATIVE_EXT;
        return $fn;        
    }
    
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
    
    public function hasThumbnail()
    {        
        return file_exists($this->getPath('thumbnail'));
    }
    
    public function hasFullsize()
    {
        return file_exists($this->getPath('fullsize'));
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
    
    protected static function checkOmekaCanMakeDerivativeImages()
    {
        //Check to see if ImageMagick is installed
        if (!self::checkForImageMagick(get_option('path_to_convert'))) {
            throw new Omeka_Upload_Exception('ImageMagick is not properly configured.  Please check your settings and then try again.' );
        }        
        
        //Check the constraints to make sure they are valid
        $constraints = array('fullsize_constraint', 
                             'thumbnail_constraint', 
                             'square_thumbnail_constraint');
        
        foreach ($constraints as $constraint) {
            $constraint_size = get_option($constraint);
            
            if (!$constraint_size or !is_numeric($constraint_size)) {
                throw new Omeka_Upload_Exception( 
                    "The sizes for derivative images have not been configured properly." );
            }
        }
    }
    
    /**
     * Determine whether or not ImageMagick has been correctly installed or configured for Omeka to use.  
     * 
     * This appears to work on most hosting environments, but there are some
     * where ImageMagick may return codes other than 0 even though it appears to
     * be loaded on the host machine. It remains to be seen whether this is an
     * error in configuring their servers or an error where Omeka should
     * examine/accept other return status codes.
     *
     * @param string
     * @return boolean True if the command line return status is 0 when
     * attempting to run ImageMagick's convert utility, false otherwise.
     **/
    protected static function checkForImageMagick($path) {
        exec($path, $convert_version, $convert_return);
        return($convert_return == 0);
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
     * Set the default values that will be stored for this file in the 'files' table.
     * 
     * These values include 'size', 'authentication', 'mime_browser', 'mime_os', 'type_os'
     * and 'archive_filename.
     * 
     * @param string
     * @return void
     **/
    public function setDefaults($filepath, array $options = array())
    {
        $this->size = filesize($filepath);
        $this->authentication = md5_file( $filepath );
        
        $this->mime_browser = mime_content_type($filepath);
        $this->mime_os      = trim(exec('file -ib ' . trim(escapeshellarg($filepath))));
        $this->type_os      = trim(exec('file -b ' . trim(escapeshellarg($filepath))));
        
        $this->archive_filename = basename($filepath);
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
    
    /**
     * Generate all the derivative images for this file.  
     * 
     * Currently, derivative images include 'fullsize', 'thumbnail' and 
     * 'square_thumbnail' sizes. New sizes could be added if a plugin were to 
     * hook into the 'after_file_upload' hook, so this method does not need to 
     * be extensible.
     * 
     * @param string
     * @return void
     **/
    public function createDerivativeImages($path)
    {
        //Function processes derivatives of every image uploaded - additional images may be created using createImage function.  Additionally, plugin hooks allow you to add your own additional image sizes [DL]
        
        //Retrieve the image sizes from the database
        $full_constraint = get_option('fullsize_constraint');
        $thumb_constraint = get_option('thumbnail_constraint');
        $square_thumbnail_constraint = get_option('square_thumbnail_constraint');
        
        $this->createImage($path, FULLSIZE_DIR, $full_constraint);
        $this->createImage($path, THUMBNAIL_DIR, $thumb_constraint);
        $this->createImage($path, SQUARE_THUMBNAIL_DIR, $square_thumbnail_constraint, "square");
        
    }
    
    /**
     * Take a set of Element records and populate them with element text that is 
     * auto-generated based on the getID3 metadata extraction library.
     * 
     * @param array Set of Element records.
     * @param array Info extracted from the file by the getID3 library.
     * @param string Either 'FilesVideos' or 'FilesImages' depending.
     * @return void
     **/
    protected function populateMimeTypeElements($elements, $id3Info, $extractionStrategy)
    {
        $helperClass = new $extractionStrategy;
        
        $helperClass->initialize($id3Info, $this->getPath('archive'));
        
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
                $this->addTextForElement($element, $elementText);
            }
        }        
    }
    
    public function getMimeTypeElements($mimeType = null)
    {
        if (!$mimeType) {
            $mimeType = $this->getMimeType();
        }
        
        return $this->getTable('Element')->findForFilesByMimeType($mimeType);
    }
    
    /**
     * Retrieve the definitive MIME type for this file.
     * 
     * @param string
     * @return string
     **/
    public function getMimeType()
    {
        return $this->mime_browser;
    }
    
    /**
     * @internal Seems kind of arbitrary that 'mime_browser' contains the
     * definitive MIME type, but at least we can abstract it so that it's
     * easier to change later if necessary.
     * 
     * @param string
     * @return void
     **/
    public function setMimeType($mimeType)
    {
        $this->mime_browser = $mimeType;
    }
    
    /**
     * Process the extended set of metadata for a file (contingent on its MIME type).
     *
     * @return void
     **/
    public function extractMimeMetadata($path)
    {
        if (!is_readable($path)) {
            throw new Exception( 'File cannot be read!' );
        }
                
        // If we can use the browser mime_type instead of the ID3 extrapolation, 
        // do that
        $mime_type = $this->getMimeType();    
        
        // Return if getid3 did not return a valid object.
        if (!$id3 = $this->retrieveID3Info($path)) {
            return;
        }
        
        if ($this->mimeTypeIsAmbiguous($mime_type)) {
            // If we can't determine MIME type via the browser, we will use the 
            // ID3 data, but be warned that this may cause a memory error on 
            // large files
            $mime_type = $id3->info['mime_type'];
        }
        
        if (!$mime_type) {
            return false;
        } else {
            $this->setMimeType($mime_type);
        }
        
        $elements = $this->getMimeTypeElements($mime_type);

        if (empty($elements)) {
            return;
        }
                
        // Figure out what kind of extraction strategy to use for retrieving the 
        // metadata from ID3. Current possibilities include either FilesImages 
        // or FilesVideos
        switch (current($elements)->set_name) {
            case 'Omeka Video File':
                $extraction = 'FilesVideos';
                break;
            case 'Omeka Image File':
                $extraction = 'FilesImages';
                break;
            default:
                throw new Exception('Cannot extract metadata for these elements!');
                break;
        }
                
        $this->populateMimeTypeElements($elements, $id3->info, $extraction);        
    }
    
    /**
     * References a list of ambiguous mime types from "http://msdn2.microsoft.com/en-us/library/ms775147.aspx".
     * 
     * @param string
     * @return boolean
     **/
    protected function mimeTypeIsAmbiguous($mime_type)
    {
        return in_array($mime_type, array("text/plain", "application/octet-stream", '', null));
    }
    
    /**
     * Pull down the file's extra metadata via getID3 library.
     *
     * @return getID3
     **/
    private function retrieveID3Info($path)
    {
        // Do not extract metadata if the exif module is not loaded. This 
        // applies to all files, not just files with Exif data -- i.e. images.
        if (!extension_loaded('exif')) {
            return false;
        }
        
        require_once 'getid3/getid3.php';
        $id3 = new getID3;
        $id3->encoding = 'UTF-8';
        
        try {
            $id3->Analyze($path);
            return $id3;
        } catch (Exception $e) {
            return false;
        }        
    }
    
    /**
     * Generate a derivative image from an existing image stored in Omeka's archive.  
     * 
     * This image will be generated based on a constraint given in pixels.  For 
     * example, if the constraint is 500, the resulting image file will be scaled 
     * so that the largest side is 500px. If the image is less than 500px on both 
     * sides, the image will not be resized.
     * 
     * All derivative images will be JPEG, which is specified by the constant 
     * IMAGE_DERIVATIVE_EXT.  
     * 
     * Currently, derivative images will only be generated for file types that 
     * can be read by PHP's getimagesize() function.  Documentation for supported 
     * file types can be found on PHP.net's doc page for getimagesize() or 
     * image_type_to_mime_type().
     * 
     * @throws Omeka_Upload_Exception
     * @param string The full path to the archived file.
     * @param string The full path to the directory in which to create the derivative image.
     * @param integer The size constraint for the image (in pixels).
     * @param string The type of the image to generate (optional).  If the type 
     * specified is "square", Omeka will generated a derivative image that is 
     * centered and cropped to a square.  This is primarily used for generation 
     * of square thumbnails, though a plugin could also take advantage of it.
     * @return string The filename of the generated image file.
     **/
    protected function createImage( $old_path, $new_dir, $constraint, $type=null) {
            
        $convertPath = get_option('path_to_convert');
                        
        if (file_exists($old_path) && is_readable($old_path) && getimagesize($old_path)) {    
            
            $filename = basename($old_path);
            $new_name = explode('.', $filename);
            //ensures that all generated files are jpeg
            $new_name[1] = IMAGE_DERIVATIVE_EXT;
            $imagename = implode('.', $new_name);
            $new_path = rtrim($new_dir, DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR . $imagename;

            $old_path = escapeshellarg( $old_path );
            $new_path = escapeshellarg( $new_path );

            if (!$constraint) {
                throw new Omeka_Upload_Exception('Image creation failed - Image size constraint must be specified within application settings');
            }

            switch ($type) {
            case "square":
                $command = ''.$convertPath.' '.$old_path.' -thumbnail x'.($constraint*2).' -resize "'.($constraint*2).'x<" -resize 50% -gravity center -crop '.$constraint.'x'.$constraint.'+0+0 +repage '.$new_path.'';
                break;
            default:
                $command = ''.$convertPath.' '.$old_path.' -resize '.escapeshellarg($constraint.'x'.$constraint.'>').' '.$new_path.'';                        
            }

            exec($command, $result_array, $result_value);

            if ($result_value == 0) {
                //Image was created, so set the derivative bitflag
                if (!$this->has_derivative_image) {
                    $this->has_derivative_image = 1;
                }

                return $imagename;    
            } else {
                throw new Omeka_Upload_Exception('Something went wrong with image creation.  Please notify an administrator.');
            }
        }
    }
    
    public function unlinkFile() {
        $files = array($this->getPath('fullsize'), 
                       $this->getPath('thumbnail'), 
                       $this->getPath('archive'),
                       $this->getPath('square_thumbnail'));
        
        foreach($files as $file) {
            if (file_exists($file) && !is_dir($file)) {
                unlink($file);
            }
        }
    }
    
    protected function _delete() {
        $this->unlinkFile();
        $this->deleteElementTexts();
    }
}       