<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Standard local filesystem storage adapter.
 *
 * The default adapter; this stores files in the Omeka files directory by 
 * default, but can be set to point to a different path.
 * 
 * @package Omeka\Storage\Adapter
 */
class Omeka_Storage_Adapter_Filesystem implements Omeka_Storage_Adapter_AdapterInterface
{
    /**
     * Local directory where files are stored.
     * 
     * @var string
     */
    protected $_localDir;

    protected $_subDirs = array(
        'thumbnails', 
        'square_thumbnails', 
        'fullsize', 
        'original',
        'theme_uploads'
    );

    /**
     * Web-accesible path that corresponds to $_localDir.
     *
     * @var string
     */
    protected $_webDir;

    /**
     * Set options for the storage adapter.
     *
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        foreach ($options as $key => $value) {
            switch ($key) {
                case 'localDir':
                    $this->_localDir = $value;
                    break;

                case 'webDir':
                    $this->_webDir = $value;
                    break;

                default:
                    throw new Omeka_Storage_Exception("Invalid option: '$key'");
                    break;
            }
        }
        if (!$this->_localDir && defined('FILES_DIR')) {
            $this->_localDir = FILES_DIR;
        }
        if (!$this->_webDir && defined('WEB_FILES')) {
            $this->_webDir = WEB_FILES;
        }
    }

    public function setUp()
    {
        foreach ($this->_subDirs as $filesDirName) {
            $dirToCreate = $this->_getAbsPath($filesDirName);
            if (!is_dir($dirToCreate)) {
                $made = @mkdir($dirToCreate, 0770, true);
                if (!$made || !is_readable($dirToCreate)) {
                    throw new Omeka_Storage_Exception("Error making directory: "
                        . "'$dirToCreate'");
                }
            }
            if (!is_writable($dirToCreate)) {
                throw new Omeka_Storage_Exception("Directory not writable: "
                    . "'$dirToCreate'");
            }
        }
    }

    /**
     * Check whether the adapter is set up correctly to be able to store
     * files.
     *
     * Specifically, this checks to see if the local storage directory
     * is writable.
     *
     * @return boolean
     */
    public function canStore()
    {
        foreach ($this->_subDirs as $dir) {
            if (!is_writable($this->_getAbsPath($dir))) {
                return false;
            }
        }
        return true;
    }

    /**
     * Move a local file to "storage."
     *
     * @param string $source Local filesystem path to file.
     * @param string $dest Destination path.
     */
    public function store($source, $dest)
    {
        $status = $this->_rename($source, $this->_getAbsPath($dest));

        if(!$status) {
            throw new Omeka_Storage_Exception('Unable to store file.');
        }
    }

    /**
     * Move a file between two "storage" locations.
     *
     * @param string $source Original stored path.
     * @param string $dest Destination stored path.
     */
    public function move($source, $dest)
    {
        $status = $this->_rename($this->_getAbsPath($source), 
            $this->_getAbsPath($dest));

        if(!$status) {
            throw new Omeka_Storage_Exception('Unable to move file.');
        }
    }

    /**
     * Remove a "stored" file.
     *
     * @param string $path
     */
    public function delete($path)
    {
        $absPath = $this->_getAbsPath($path);
        $status = @unlink($absPath);

        if(!$status) {
            if (file_exists($absPath)) {
                throw new Omeka_Storage_Exception('Unable to delete file.');
            } else {
                _log("Omeka_Storage_Adapter_Filesystem: Tried to delete missing file '$path'.", Zend_Log::WARN);
            }
        }
    }

    /**
     * Get a URI for a "stored" file.
     *
     * @param string $path
     * @return string URI
     */
    public function getUri($path)
    {
        return $this->_webDir . '/' . $path;
    }

    /**
     * Return the options set by the adapter.  Used primarily for testing.
     */
    public function getOptions()
    {
        return array(
            'localDir' => $this->_localDir,
            'webDir' => $this->_webDir,
        );
    }

    /**
     * Set the path of the local directory where files are stored.
     *
     * @param string
     */
    public function setLocalDir($dir)
    {
        $this->_localDir = $dir;
    }

    /**
     * Set the web URL that corresponds with the local dir.
     *
     * @param string
     */
    public function setWebDir($dir)
    {
        $this->_webDir = $dir;
    }

    /**
     * Convert a "storage" path to an absolute filesystem path.
     *
     * @param string $path Storage path.
     * @return string Absolute local filesystem path.
     */
    protected function _getAbsPath($path)
    {
        return $this->_localDir . '/' . $path;
    }

    /**
     * @throws Omeka_Storage_Exception
     * @return boolean
     */
    protected function _rename($source, $dest)
    {
        $destDir = dirname($dest);
        if (!is_writable($destDir)) {
            throw new Omeka_Storage_Exception("Destination directory is not "
                . "writable: '$destDir'.");
        }
        return rename($source, $dest);
    }
}
