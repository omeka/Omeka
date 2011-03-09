<?php
/**
 * @copyright Center for History and New Media, 2011
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Standard local filesystem storage adapter.
 *
 * The default adapter; this stores files in the Omeka archive directory
 * by default, but can be set to point to a different path.
 *
 * @package Omeka
 */
class Omeka_Storage_Adapter_Filesystem implements Omeka_Storage_Adapter
{
    /**
     * Local directory where files are stored.
     * 
     * @var string
     */
    private $_localDir = ARCHIVE_DIR;

    /**
     * Web-accesible path that corresponds to $_localDir.
     *
     * @var string
     */
    private $_webDir = WEB_ARCHIVE;

    /**
     * Set options for the storage adapter.
     *
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        foreach ($options as $key => $value) {
            switch ($key) {
                case 'local_dir':
                $this->_localDir = $value;
                break;

                case 'web_dir':
                $this->_webDir = $value;
                break;
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
        return is_writable($this->$_localDir);
    }

    /**
     * Move a local file to "storage."
     *
     * @param string $source Local filesystem path to file.
     * @param string $dest Destination path.
     */
    public function store($source, $dest)
    {
        $status = rename($source, $this->_getAbsPath($dest));

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
        $status = rename($this->_getAbsPath($source), $this->_getAbsPath($dest));

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
        $status = unlink($this->_getAbsPath($path));

        if(!$status) {
            throw new Omeka_Storage_Exception('Unable to delete file.');
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
     * Convert a "storage" path to an absolute filesystem path.
     *
     * @param string $path Storage path.
     * @return string Absolute local filesystem path.
     */
    private function _getAbsPath($path)
    {
        return $this->_localDir . '/' . $path;
    }
}
