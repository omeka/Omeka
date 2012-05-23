<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @subpackage Models
 * @author CHNM
 */

/**
 * Defines mime_content_type() if it is not available in the current 
 * installation environment.
 */
if (!function_exists('mime_content_type')) {
   function mime_content_type($f) {
       return trim(exec('file -bi ' . escapeshellarg ($f))) ;
   }
}

/**
 * Represents a file and its metadata.
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class File extends Omeka_Record implements Zend_Acl_Resource_Interface 
{ 
    const DISABLE_DEFAULT_VALIDATION_OPTION = 'disable_default_file_validation';

    public $item_id;
    public $order;
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
    public $stored = '0';

    static private $_pathsByType = array(
        'archive' => 'files',
        'fullsize' => 'fullsize',
        'thumbnail' => 'thumbnails',
        'square_thumbnail' => 'square_thumbnails'
    );

    /**
     * Get a file's property for display.
     *
     * Available properties:
     * - id
     * - archive filename
     * - original filename
     * - size
     * - mime type
     * - date added
     * - date modified
     * - authentication
     * - mime type os
     * - file type os
     * - uri
     * - fullsize uri
     * - thumbnail uri
     * - square thumbnail uri
     * - permalink
     *
     * @param string $property
     * @return mixed
     */
    public function getProperty($property)
    {
        switch ($property) {
            case 'id':
                return $this->id;
            case 'archive filename':
                return $this->archive_filename;
            case 'original filename':
                return $this->original_filename;
            case 'size':
                return $this->size;
            case 'mime type':
                return $this->getMimeType();
            case 'date added':
                return $this->added;
            case 'date modified':
                return $this->modified;
            case 'authentication':
                return $this->authentication;
            case 'mime type os':
                return $this->mime_os;
            case 'file type os':
                return $this->type_os;
            case 'uri':
                return $this->getWebPath('archive');
            case 'fullsize uri':
                return $this->getWebPath('fullsize');
            case 'thumbnail uri':
                return $this->getWebPath('thumbnail');
            case 'square thumbnail uri':
                return $this->getWebPath('square_thumbnail');
            case 'permalink':
                return abs_uri(array('controller' => 'files', 'action' => 'show', 'id' => $this->id));
            default:
                throw new Exception(__("'%s' is an invalid special value.", $property));
        }
    }

    protected function _initializeMixins()
    {
        $this->_mixins[] = new ActsAsElementText($this);
    }

    protected function beforeInsert()
    {
        $now = Zend_Date::now()->toString(self::DATE_FORMAT);
        $this->added = $now;
        $this->modified = $now;
        $fileInfo = new Omeka_File_Info($this);
        $fileInfo->setMimeTypeIfAmbiguous();
    }

    protected function afterInsert()
    {
        $dispatcher = Zend_Registry::get('job_dispatcher');
        $dispatcher->setQueueName('uploads');
        $dispatcher->send('File_ProcessUploadJob', 
                          array('fileId' => $this->id));
    }
    
    protected function beforeUpdate()
    {
        $this->modified = Zend_Date::now()->toString(self::DATE_FORMAT);
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
     */
    public function getPath($type='fullsize')
    {
        $fn = $this->getDerivativeFilename();

        if ($this->stored) {
            throw new Exception(__('Cannot get the local path for a stored file.'));
        }

        $dir = $this->getStorage()->getTempDir();
        
        if ($type == 'archive') {
            return $dir . '/' . $this->archive_filename;
        } else {
            return $dir . "/{$type}_{$fn}";
        }
    }
    
    /**
     * Retrieve the web path for the file
     *
     * @return void
     */
    public function getWebPath($type='fullsize')
    {
        return $this->getStorage()->getUri($this->getStoragePath($type));
    }
    
    public function getDerivativeFilename()
    {
        $filename = basename($this->archive_filename);
        $parts = explode('.', $filename);
        // One or more . in the filename, pop the last section to be replaced.
        if (count($parts) > 1) {
            $ext = array_pop($parts);
        }
        array_push($parts, Omeka_File_Derivative_Image_Creator::DERIVATIVE_EXT);
        return join('.', $parts);        
    }
    
    public function hasThumbnail()
    {        
        return $this->has_derivative_image;
    }
    
    public function hasFullsize()
    {
        return $this->has_derivative_image;
    }
    
    /**
     * Set the default values that will be stored for this file in the 'files' table.
     * 
     * These values include 'size', 'authentication', 'mime_browser', 'mime_os', 'type_os'
     * and 'archive_filename.
     * 
     * @param string
     * @return void
     */
    public function setDefaults($filepath, array $options = array())
    {
        $this->size = filesize($filepath);
        $this->authentication = md5_file($filepath);
        
        $this->setMimeType(mime_content_type($filepath));
        
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
     */
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
     */
    public function setMimeType($mimeType)
    {
        $this->mime_browser = $this->_filterMimeType($mimeType);
    }
    
    /**
     * Filters the mime type.  In particular, it removes the charset information.
     * 
     * @param string $mimeType The raw mime type
     * @return string Filtered mime type.
     */
    protected function _filterMimeType($mimeType)
    {
        $mimeTypeParts = explode(';', $mimeType);
        return trim($mimeTypeParts[0]);
    }
    
    public function unlinkFile() 
    {
        $storage = $this->getStorage();

        $files = array($this->getStoragePath('archive'));

        if ($this->has_derivative_image) {
            $types = self::$_pathsByType;
            unset($types['archive']);

            foreach($types as $type => $path) {
                $files[] = $this->getStoragePath($type);
            }
        }
        
        foreach($files as $file) {
            $storage->delete($file);
        }
    }
    
    protected function _delete() 
    {
        $this->unlinkFile();
        $this->deleteElementTexts();
    }
    
    public function createDerivatives()
    {        
        if (!($convertDir = get_option('path_to_convert'))) {
            return;
        }
        $creator = new Omeka_File_Derivative_Image_Creator($convertDir);
        
        $creator->addDerivative('fullsize', get_option('fullsize_constraint'));
        $creator->addDerivative('thumbnail', get_option('thumbnail_constraint'));
        $this->_makeSquareThumbnails($creator);
        
        if ($creator->create($this->getPath('archive'), 
                             $this->getDerivativeFilename(),
                             $this->getMimeType())) {
            $this->has_derivative_image = 1;
            $this->save();
        }
    }
    
    private function _makeSquareThumbnails($creator)
    {
        $constraint = get_option('square_thumbnail_constraint');
        $args = join(' ', array(
                    '-thumbnail ' . escapeshellarg('x' . $constraint*2),
                    '-resize ' . escapeshellarg($constraint*2 . 'x<'),
                    '-resize 50%',
                    '-gravity center',
                    '-crop ' . escapeshellarg($constraint . 'x' . $constraint . '+0+0'),
                    '+repage'));
        $creator->addDerivative('square_thumbnail', $args);
    }
    
    /**
     * Extract metadata associated with the file.
     * 
     * @return boolean
     */
    public function extractMetadata()
    {
        $extractor = new Omeka_File_Info($this);
        return $extractor->extract();
    }

    public function storeFiles()
    {
        $storage = $this->getStorage();

        $archiveFilename = $this->archive_filename;
        $derivativeFilename = $this->getDerivativeFilename();
        
        $storage->store($this->getPath('archive'), $this->getStoragePath('archive'));
                
        if ($this->has_derivative_image) {
            $types = array_keys(self::$_pathsByType);

            foreach ($types as $type) {
                if ($type != 'archive') {
                    $storage->store($this->getPath($type), $this->getStoragePath($type));
                }
            }
        }
        $this->stored = '1';
        $this->save();
    }

    public function getStoragePath($type = 'fullsize')
    {
        $storage = $this->getStorage();
        
        if ($type == 'archive') {
            $fn = $this->archive_filename;
        } else {
            $fn = $this->getDerivativeFilename();
        }

        if (!isset(self::$_pathsByType[$type])) {
            throw new Exception(__('"%s" is not a valid file derivative.', $type));
        }
        return $storage->getPathByType($fn, self::$_pathsByType[$type]);
    }

    public function setStorage($storage)
    {
        $this->_storage = $storage;
    }

    public function getStorage()
    {
        if (!$this->_storage) {
            $this->_storage = Zend_Registry::get('storage');
        }

        return $this->_storage;
    }

    /**
     * Get the ACL resource ID for the record.
     *
     * File records are 'Files' resources.
     *
     * @return string
     */
    public function getResourceId()
    {
        return 'Files';
    }

    /**
     * Return whether this file is owned by the given user.
     *
     * Proxies to the Item's isOwnedBy.
     *
     * @uses Ownable::isOwnedBy
     * @param User $user
     * @return boolean
     */
    public function isOwnedBy($user)
    {
        if (($item = $this->getItem())) {
            return $item->isOwnedBy($user);
        } else {
            return false;
        }
    }
}
