<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2011
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Top-level helper class for handling file storage.
 *
 * @package Omeka
 */
class Omeka_Storage
{
    const OPTION_ADAPTER = 'adapter';
    const OPTION_ADAPTER_OPTIONS = 'adapterOptions';
    const OPTION_TEMP_DIR = 'tempDir';

    const MSG_NOT_INITIALIZED = 'The storage adapter is not initialized.';
    const MSG_NO_SUCH_METHOD = 'The storage adapter has no method "%s"';
    const MSG_INVALID_ADAPTER = 'Storage adapters must implement the Omeka_Storage_Adapter interface.';
        
    /**
     * @var Omeka_Storage_Adapter
     */
    private $_adapter;

    /**
     * @var string
     */
    private $_tempDir;

    /**
     * Allows storage options to be set immediately at construction.
     *
     * @param array $options If set, this array will be passed to
     *  setOptions.
     */
    public function __construct(array $options = null)
    {
        if (isset($options)) {
            $this->setOptions($options);
        }
    }

    /**
     * Delegates calls directly to Omeka_Storage to the currently-set
     * storage adapter.
     *
     * All of the methods of the Adapter interface are accessible in
     * this way, as well as any other methods declared by the adapter.
     *
     * @param string $name Method name.
     * @param string $arguments Method arguments.
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (!$this->_adapter) {
            throw new Omeka_Storage_Exception(self::MSG_NOT_INITIALIZED);
        }

        $callback = array($this->_adapter, $name);

        if (is_callable($callback)) {
            return call_user_func_array($callback, $arguments);
        } else {
            throw new Omeka_Storage_Exception(sprintf(self::MSG_NO_SUCH_METHOD, $name));
        }
    }

    /**
     * Set global options for the storage system, as well as any
     * adapter-specific options.
     *
     * @uses Omeka_Storage::setAdapter()
     * @param array $options Options to set. Valid options include:
     *  * 'adapter': (string) Name of the storage adapter to use.
     *  * 'adapterOptions': (array) Array of options to pass to the
     *    adapter; see the specific adapter classes for details.
     *  * 'temp_dir': (string) Local temporary directory where files
     *    stored before they are handled by the adapter.
     */
    public function setOptions(array $options)
    {
        foreach ($options as $key => $value) {
            switch ($key) {
            case self::OPTION_ADAPTER:
                $adapterOptions = array();
                if (isset($options[self::OPTION_ADAPTER_OPTIONS])) {
                    $adapterOptions = $options[self::OPTION_ADAPTER_OPTIONS];
                }
                $this->setAdapter($value, $adapterOptions);
                break;

            case self::OPTION_TEMP_DIR:
                $this->setTempDir($value);
                break;
            }
        }
    }

    /**
     * Set the storage adapter to be used, as well as options for that
     * adapter.
     *
     * You can either pass an already-constructed adapter object to this
     * method or use this method as a factory by passing the name of an
     * adapter class and options to set on it.
     *
     * @param Omeka_Storage_Adapter|string $adapter Storage adapter to
     *  set. If an adapter object is passed, it is simply set as the
     *  current adapter. If a string is passed, an object of that class
     *  is created and set as the current adapter.
     * @param array|null $options If a string is passed to $adapter,
     *  this array of options is passed to the class' constructor.
     */
    public function setAdapter($adapter, array $options = array())
    {
        if (is_string($adapter) && class_exists($adapter)) {
            $adapter = new $adapter($options);
        }

        if ($adapter instanceof Omeka_Storage_Adapter) {
            $this->_adapter = $adapter;
        } else {
            throw new Omeka_Storage_Exception(self::MSG_INVALID_ADAPTER);
        }
    }

    /**
     * Get the current storage adapter.
     *
     * You generally need to use the adapter object returned by this
     * method to perform any storage actions.
     *
     * @see Omeka_Storage::setAdapter()
     * @return Omeka_Storage_Adapter
     */
    public function getAdapter()
    {
        return $this->_adapter;
    }

    /**
     * Set the temporary file storage directory path.
     *
     * @see Omeka_Storage::getTempDir()
     * @param string $dir Local path to directory.
     */
    public function setTempDir($dir)
    {
        $this->_tempDir = $dir;
    }

    /**
     * Get the temporary file storage directory path.
     *
     * If no directory has been explicitly selected, the system's temp
     * directory is set as the temp dir and returned.
     *
     * @see Omeka_Storage::setTempDir()
     * @return string Local path to directory.
     */
    public function getTempDir()
    {
        if (!$this->_tempDir) {
            $this->_tempDir = sys_get_temp_dir();
        }
        
        return $this->_tempDir;
    }

    public function getPathByType($filename, $type = 'files')
    {
        return apply_filters('storage_path', $type . "/$filename", $filename, $type);
    }
}
