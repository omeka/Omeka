<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @subpackage Models
 * @author CHNM
 **/

if (!function_exists('mime_content_type')) {
   function mime_content_type($f) {
       return trim(exec('file -bi ' . escapeshellarg ($f))) ;
   }
}

require_once 'Item.php';
require_once 'ActsAsElementText.php';
require_once 'FileTable.php';
require_once 'FilesImages.php';
require_once 'FilesVideos.php';
require_once 'MimeElementSetLookup.php';

class File extends Omeka_Record 
{ 

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

    protected function _initializeMixins()
    {
        $this->_mixins[] = new ActsAsElementText($this);
    }

    protected function beforeInsert()
    {
        $this->added = date("Y-m-d H:i:s");
        $this->modified = date("Y-m-d H:i:s");   
        
        // Extract the metadata.  This will have one side effect (aside from
        // adding the new metadata): it uses setMimeType() to reset the default
        // mime type for the file if applicable.
        $this->extractMetadata();         
        
        $this->createDerivatives();
    }
    
    protected function beforeUpdate()
    {
        $this->modified = date("Y-m-d H:i:s");
    }
    
    protected function filterInput($post)
    {
        $immutable = array('id', 'modified', 'added', 
                           'authentication', 'archive_filename', 
                           'original_filename', 'mime_browser', 
                           'mime_os', 'type_os', 'item_id');
        foreach ($immutable as $value) {
            unset($post[$value]);
        }
        return $post;
    }
    
    protected function beforeSaveForm($post)
    {        
        $this->beforeSaveElements($post);
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

        return $path[$type];
    }
    
    public function getDerivativeFilename()
    {
        list($base, $ext) = explode('.', $this->archive_filename);
        $fn = $base . '.' . Omeka_File_Derivative_Image::DERIVATIVE_EXT;
        return $fn;        
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
        
        $this->mime_browser = $this->_filterMimeType(mime_content_type($filepath));
        
        $this->mime_os      = trim(exec('file -ib ' . trim(escapeshellarg($filepath))));
        $this->type_os      = trim(exec('file -b ' . trim(escapeshellarg($filepath))));
        
        $this->archive_filename = basename($filepath);
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
        $this->mime_browser = $this->_filterMimeType($mimeType);
    }
    
    /**
     * Filters the mime type.  In particular, it removes the charset information.
     * 
     * @param string $mimeType The raw mime type
     * @return string Filtered mime type.
     **/
    protected function _filterMimeType($mimeType)
    {
        $mimeTypeParts = explode(';', $mimeType);
        return trim($mimeTypeParts[0]);
    }
    
    public function unlinkFile() 
    {
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
    
    protected function _delete() 
    {
        $this->unlinkFile();
        $this->deleteElementTexts();
    }
    
    public function createDerivatives()
    {
        $pathToOriginalFile = $this->getPath('archive');
        
        // Create derivative images if possible.
        if (Omeka_File_Derivative_Image::createAll($pathToOriginalFile, 
                                                   $this->getMimeType())) {
            $this->has_derivative_image = 1;
        }
    }
    
    /**
     * undocumented function
     * 
     * @param 
     * @return boolean
     **/
    public function extractMetadata()
    {
        $extractor = new Omeka_File_Info($this);
        return $extractor->extract();
    }
}       