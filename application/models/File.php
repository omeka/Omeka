<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * A file and its metadata.
 * 
 * @package Omeka\Record
 */
class File extends Omeka_Record_AbstractRecord implements Zend_Acl_Resource_Interface 
{ 
    const DISABLE_DEFAULT_VALIDATION_OPTION = 'disable_default_file_validation';
    const DERIVATIVE_EXT = 'jpg';
    
    public $item_id;
    public $order;
    public $filename;
    public $original_filename;
    public $size = '0';
    public $authentication;
    public $mime_type;
    public $type_os;
    public $has_derivative_image = '0';
    public $added;
    public $modified;
    public $stored = '0';
    public $metadata;
    
    static private $_pathsByType = array(
        'original' => 'original',
        'fullsize' => 'fullsize',
        'thumbnail' => 'thumbnails',
        'square_thumbnail' => 'square_thumbnails'
    );
    
    /**
     * Get a property or special value of this record.
     * 
     * @param string $property
     * @return mixed
     */
    public function getProperty($property)
    {
        switch ($property) {
            case 'uri':
                return $this->getWebPath('original');
            case 'fullsize_uri':
                return $this->getWebPath('fullsize');
            case 'thumbnail_uri':
                return $this->getWebPath('thumbnail');
            case 'square_thumbnail_uri':
                return $this->getWebPath('square_thumbnail');
            case 'permalink':
                return absolute_url(array('controller' => 'files', 'action' => 'show', 'id' => $this->id));
            case 'display_title':
                $titles = $this->getElementTexts('Dublin Core', 'Title');
                if ($titles) {
                    $title = $titles[0]->text;
                } else {
                    $title = $this->original_filename;
                }
                return $title;
            default:
                return parent::getProperty($property);
        }
    }
    
    /**
     * Initialize mixins.
     */
    protected function _initializeMixins()
    {
        $this->_mixins[] = new Mixin_ElementText($this);
        $this->_mixins[] = new Mixin_Timestamp($this);
        $this->_mixins[] = new Mixin_Search($this);
    }
    
    /**
     * Unset immutable properties from $_POST.
     * 
     * @param array $post
     * @return array
     */
    protected function filterPostData($post)
    {
        $immutable = array('id', 'modified', 'added', 'authentication', 'filename', 
                           'original_filename', 'mime_type', 'type_os', 'item_id');
        foreach ($immutable as $value) {
            unset($post[$value]);
        }
        return $post;
    }
    
    /**
     * Do something before saving this record.
     * 
     * @param array $args
     */
    protected function beforeSave($args)
    {
        if ($args['post']) {
            $this->beforeSaveElements($args['post']);
        }
    }
    
    /**
     * Do something after saving this record.
     * 
     * @param array $args
     */
    protected function afterSave($args)
    {
        if ($args['insert']) {
            $dispatcher = Zend_Registry::get('job_dispatcher');
            $dispatcher->setQueueName('uploads');
            $dispatcher->send('Job_FileProcessUpload', array('fileData' => $this->toArray()));
        }
        
        $item = $this->getItem();
        if (!$item->public) {
            $this->setSearchTextPrivate();
        }
        $this->setSearchTextTitle($this->original_filename);
    }
    
    /**
     * Retrieve the parent item of this record.
     * 
     * @return Item
     */
    public function getItem()
    {
        return $this->getTable('Item')->find($this->item_id);
    }
    
    /**
     * Retrieve a system path for this file.
     *
     * @param string $type
     * @return string
     */
    public function getPath($type = 'original')
    {
        $fn = $this->getDerivativeFilename();
        if ($this->stored) {
            throw new RuntimeException(__('Cannot get the local path for a stored file.'));
        }
        $dir = $this->getStorage()->getTempDir();
        if ($type == 'original') {
            return $dir . '/' . $this->filename;
        } else {
            return $dir . "/{$type}_{$fn}";
        }
    }
    
    /**
     * Retrieve a web path for this file.
     *
     * @param string $type
     * @return string
     */
    public function getWebPath($type = 'original')
    {
        return $this->getStorage()->getUri($this->getStoragePath($type));
    }
    
    /**
     * Retrieve the derivative filename.
     * 
     * @return string
     */
    public function getDerivativeFilename()
    {
        $filename = basename($this->filename);
        $parts = explode('.', $filename);
        // One or more . in the filename, pop the last section to be replaced.
        if (count($parts) > 1) {
            $ext = array_pop($parts);
        }
        array_push($parts, self::DERIVATIVE_EXT);
        return join('.', $parts);
    }
    
    /**
     * Determine whether this record has a thumbnail image.
     * 
     * @return bool
     */
    public function hasThumbnail()
    {
        return $this->has_derivative_image;
    }
    
    /**
     * Determine whether this record has a fullsize image.
     * 
     * @return bool
     */
    public function hasFullsize()
    {
        return $this->has_derivative_image;
    }
    
    /**
     * Get the original file's extension.
     * 
     * @return string
     */
    public function getExtension()
    {
        return pathinfo($this->original_filename, PATHINFO_EXTENSION);
    }
    
    /**
     * Set the default values that will be stored for this record in the 'files' 
     * table.
     * 
     * @param string
     * @return void
     */
    public function setDefaults($filepath, array $options = array())
    {
        $this->size = filesize($filepath);
        $this->authentication = md5_file($filepath);
        $this->type_os = trim(exec('file -b ' . trim(escapeshellarg($filepath))));
        $this->filename = basename($filepath);
        $this->metadata = '';
    }
    
    /**
     * Unlink the file and file derivatives belonging to this record.
     */
    public function unlinkFile() 
    {
        $storage = $this->getStorage();
        $files = array($this->getStoragePath('original'));
        if ($this->has_derivative_image) {
            $types = self::$_pathsByType;
            unset($types['original']);
            foreach($types as $type => $path) {
                $files[] = $this->getStoragePath($type);
            }
        }
        foreach($files as $file) {
            $storage->delete($file);
        }
    }
    
    /**
     * Perform any further deletion when deleting this record.
     */
    protected function _delete() 
    {
        $this->unlinkFile();
        $this->deleteElementTexts();
    }
    
    /**
     * Create derivatives of the original file.
     */
    public function createDerivatives()
    {        
        if (!($convertDir = get_option('path_to_convert'))) {
            return;
        }
        $creator = new Omeka_File_Derivative_Image_Creator($convertDir);
        $creator->addDerivative('fullsize', get_option('fullsize_constraint'));
        $creator->addDerivative('thumbnail', get_option('thumbnail_constraint'));
        $creator->addDerivative('square_thumbnail', get_option('square_thumbnail_constraint'), true);
        if ($creator->create($this->getPath('original'), 
                             $this->getDerivativeFilename(),
                             $this->mime_type)) {
            $this->has_derivative_image = 1;
            $this->save();
        }
    }
    
    /**
     * Extract ID3 metadata associated with the file.
     * 
     * @return boolean
     */
    public function extractMetadata()
    {
        if (!is_readable($this->getPath('original'))) {
            throw new Exception('Could not extract metadata: unable to read file at the following path: "' . $this->_filePath . '"');
        }
        // Skip if getid3 did not return a valid object.
        if (!$id3 = $this->_getId3()) {
            return false;
        }
        getid3_lib::CopyTagsToComments($id3->info);
        $metadata = array();
        $keys = array(
            'mime_type', 'audio', 'video', 'comments', 'comments_html',
            'iptc', 'jpg'
        );
        foreach($keys as $key) {
            if (array_key_exists($key, $id3->info)) {
                $metadata[$key] = $id3->info[$key];
            }
        }
        $this->metadata = json_encode($metadata);      
        return true;
    }
    
    /**
     * Pull down the file's extra metadata via getID3 library.
     *
     * @param string $path Path to file.
     * @return getID3
     */
    private function _getId3()
    {
        if (!$this->_id3) {
            require_once 'getid3/getid3.php';
            $id3 = new getID3;
            $id3->encoding = 'UTF-8';
            try {
                $id3->Analyze($this->getPath('original'));
                $this->_id3 = $id3;
            } catch (getid3_exception $e) {
                $message = $e->getMessage();
                _log("getID3: $message");
                return false;
            }        
        }
        return $this->_id3;
    }
    
    /**
     * Store files belonging to this record.
     */
    public function storeFiles()
    {
        $storage = $this->getStorage();
        $filename = $this->filename;
        $derivativeFilename = $this->getDerivativeFilename();
        $storage->store($this->getPath('original'), $this->getStoragePath('original'));
        if ($this->has_derivative_image) {
            $types = array_keys(self::$_pathsByType);
            foreach ($types as $type) {
                if ($type != 'original') {
                    $storage->store($this->getPath($type), $this->getStoragePath($type));
                }
            }
        }
        $this->stored = '1';
        $this->save();
    }
    
    /**
     * Get the storage path.
     * 
     * @param string $type
     * @return string
     */
    public function getStoragePath($type = 'fullsize')
    {
        $storage = $this->getStorage();
        if ($type == 'original') {
            $fn = $this->filename;
        } else {
            $fn = $this->getDerivativeFilename();
        }
        if (!isset(self::$_pathsByType[$type])) {
            throw new RuntimeException(__('"%s" is not a valid file derivative.', $type));
        }
        return $storage->getPathByType($fn, self::$_pathsByType[$type]);
    }
    
    /**
     * Set the storage object.
     * 
     * @param Omeka_Storage $storage
     */
    public function setStorage($storage)
    {
        $this->_storage = $storage;
    }
    
    /**
     * Get the storage object.
     * 
     * @return Omeka_Storage
     */
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
