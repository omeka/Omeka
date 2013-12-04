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
    /**
     * Option name for whether the file validation is disabled.
     */
    const DISABLE_DEFAULT_VALIDATION_OPTION = 'disable_default_file_validation';

    /**
     * File extension for all image derivatives.
     */
    const DERIVATIVE_EXT = 'jpg';

    /**
     * ID of the Item this File belongs to.
     *
     * @var int
     */
    public $item_id;

    /**
     * Relative order of this File within the parent Item.
     *
     * @var int
     */
    public $order;

    /**
     * Current filename, as stored.
     *
     * @var string
     */
    public $filename;

    /**
     * Original filename, as uploaded.
     *
     * @var string
     */
    public $original_filename;

    /**
     * Size of the file, in bytes.
     *
     * @var int
     */
    public $size = 0;

    /**
     * MD5 hash of the file.
     *
     * @var string
     */
    public $authentication;

    /**
     * MIME type of the file.
     *
     * @var string
     */
    public $mime_type;

    /**
     * Longer description of the file's type.
     *
     * @var string
     */
    public $type_os;

    /**
     * Whether the file has derivative images.
     *
     * @var int
     */
    public $has_derivative_image = 0;

    /**
     * Date the file was added.
     *
     * @var string
     */
    public $added;

    /**
     * Date the file was last modified.
     *
     * @var string
     */
    public $modified;

    /**
     * Whether the file has been moved to storage.
     *
     * @var int
     */
    public $stored = 0;

    /**
     * Embedded metadata from the file.
     *
     * @var array
     */
    public $metadata;

    /**
     * Folder paths for each type of files/derivatives.
     *
     * @var array
     */
    static private $_pathsByType;

    /**
     * Get a property or special value of this record.
     *
     * @param string $property
     * @return mixed
     */
    public function getProperty($property)
    {
        if (substr($property, -4) == '_uri') {
            $derivativePaths = $this->_getDerivativePathsByType();
            $path = substr($property, 0, -4);
            if (isset($derivativePaths[$path])) {
                return $this->getWebPath($path);
            }
        };
        switch ($property) {
            case 'uri':
                return $this->getWebPath('original');
            case 'permalink':
                return absolute_url(array('controller' => 'files', 'action' => 'show', 'id' => $this->id));
            case 'display_title':
                $titles = $this->getElementTexts('Dublin Core', 'Title');
                if ($titles) {
                    $title = strip_formatting($titles[0]->text);
                } else {
                    $title = $this->original_filename;
                }
                return $title;
            default:
                return parent::getProperty($property);
        }
    }

    /**
     * Initialize the mixins.
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
     * Before-save hook.
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
     * After-save hook.
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
     * Get the Item this file belongs to.
     *
     * @return Item
     */
    public function getItem()
    {
        return $this->getTable('Item')->find($this->item_id);
    }

    /**
     * Get a system path for this file.
     *
     * Local paths are only available before the file is stored.
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
     * Get a web path for this file.
     *
     * @param string $type
     * @return string
     */
    public function getWebPath($type = 'original')
    {
        return $this->getStorage()->getUri($this->getStoragePath($type));
    }

    /**
     * Get the filename for this file's derivative images.
     *
     * @return string
     */
    public function getDerivativeFilename()
    {
        $base = pathinfo($this->filename, PATHINFO_EXTENSION) ? substr($this->filename, 0, strrpos($this->filename, '.')) : $this->filename;
        return $base . '.' . self::DERIVATIVE_EXT;
    }

    /**
     * Determine whether this file has a thumbnail image.
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
     * This is an alias for hasThumbnail().
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
            $derivativePaths = $this->_getDerivativePathsByType();
            foreach ($derivativePaths as $type => $path) {
                $files[] = $this->getStoragePath($type);
            }
        }
        foreach ($files as $file) {
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
        $derivative_types = unserialize(get_option('derivative_types'));
        foreach ($derivative_types as $type) {
            $creator->addDerivative($type, get_option($type . '_constraint'), (boolean) get_option($type . '_constraint_square'));
        }
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
     * @return bool Whether getID3 was able to read the file.
     */
    public function extractMetadata()
    {
        if (!is_readable($this->getPath('original'))) {
            throw new Exception('Could not extract metadata: unable to read file at the following path: "' . $this->getPath('original') . '"');
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
        foreach ($keys as $key) {
            if (array_key_exists($key, $id3->info)) {
                $metadata[$key] = $id3->info[$key];
            }
        }

        $this->metadata = json_encode($metadata);
        return true;
    }

    /**
     * Read the file's embedded metadata with the getID3 library.
     *
     * @return getID3|bool Returns getID3 object, or false if there was an
     *  exception.
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
     * Store the files belonging to this record.
     */
    public function storeFiles()
    {
        $storage = $this->getStorage();
        $filename = $this->filename;
        $derivativeFilename = $this->getDerivativeFilename();
        $storage->store($this->getPath('original'), $this->getStoragePath('original'));
        if ($this->has_derivative_image) {
            $types = array_keys($this->_getDerivativePathsByType());
            foreach ($types as $type) {
                $storage->store($this->getPath($type), $this->getStoragePath($type));
            }
        }
        $this->stored = '1';
        $this->save();
    }

    /**
     * Get list of storage paths of files (original and derivative) by type.
     *
     * @return array
     *   Associative array of paths for original and derivatives by type.
     */
    public function getStoragePathsByType()
    {
        if (is_null(self::$_pathsByType)) {
            // TODO To be removed once the patch will be integrated in install.
            $derivative_types = get_option('derivative_types');
            if (empty($derivative_types)) {
                $derivative_types = array(
                    'fullsize',
                    'thumbnail',
                    'square_thumbnail',
                );
                set_option('derivative_types', serialize($derivative_types));
                set_option('original_path', 'original');
                set_option('fullsize_path', 'fullsize');
                set_option('thumbnail_path', 'thumbnails');
                set_option('square_thumbnail_path', 'square_thumbnails');
                set_option('square_thumbnail_constraint_square', true);
                delete_option('storage_paths');
            }
            else {
                $derivative_types = unserialize($derivative_types);
            }

            self::$_pathsByType = array(
                'original' => get_option('original_path'),
            );
            foreach ($derivative_types as $type) {
                self::$_pathsByType[$type] = get_option($type . '_path');
            }
        }

        return self::$_pathsByType;
    }

    /**
     * Get list of storage paths of derivative files by type.
     *
     * @return array
     *   Associative array of paths for derivative files by type.
     */
    protected function _getDerivativePathsByType()
    {
        $storagePaths = $this->getStoragePathsByType();
        unset($storagePaths['original']);
        return $storagePaths;
    }

    /**
     * Get a storage path for the file.
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
        $storagePaths = $this->getStoragePathsByType();
        if (!isset($storagePaths[$type])) {
            throw new RuntimeException(__('"%s" is not a valid file derivative.', $type));
        }
        return $storage->getPathByType($fn, $storagePaths[$type]);
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
     * @return bool
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
