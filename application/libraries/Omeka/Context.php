<?php 
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */
 
/**
 * Container singleton for contextual state data.
 *
 * Kind of a bootstrap class, this stores all (essentially global) data that
 * is needed by the application.  Examples of this include, but are not 
 * limited to: database connection, config file data, ACL, Auth, logger, etc.
 * 
 * Modeled after sfContext in Symfony framework.
 * 
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class Omeka_Context
{
    /**
     * The singleton instance of Omeka_Context.
     *
     * @var Omeka_Context
     */
     private static $_instance;
    
    /**
     * Whether Omeka is installed or not.
     *
     * Initially assumed to be true until proven otherwise.
     *
     * @var boolean
     */
    protected $_installed = true;
    
    /**
     * Array of configuration objects.
     *
     * @var array
     */
    private $_config;
    
    /**
     * Array of data stored in the instance.
     *
     * @var array
     */
    private $_data = array();
    
    /**
     * Cannot be called outside of Omeka_Context.
     *
     * Omeka_Context is a singleton, so its constructor is declared private to
     * disallow the creation of extra instances.
     *
     * @see Omeka_Context::getInstance()
     */
    private function __construct() {}
    
    /**
     * Retrieve the instance of the singleton.
     *
     * The instance is created if it does not already exist.
     * 
     * @return Omeka_Context
     */
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
    
    /**
     * Replace the current singleton instance with a newly-constructed one.
     *
     * This clears all data and properties set on the previous instance.
     *
     * @return void
     */
    public static function resetInstance()
    {
        self::$_instance = new self();
    }
    
    /**
     * Verify that Omeka has been installed.  
     *
     * The criteria for Omeka being installed include:
     * - the existence of the database options, which would be missing if the
     *   'options' table was either empty or non-existent.
     * @return boolean
     */
    public function omekaIsInstalled()
    {        
        return $this->_installed;
    }
    
    /**
     * Set the flag that indicates whether Omeka has been installed.
     *
     * @param boolean $flag
     * @return void
     */
    public function setOmekaIsInstalled($flag)
    {
        $this->_installed = (boolean)$flag;
    }
        
    /**
     * Store a configuration set.
     *
     * The config object will be retrievable by the name provided.
     *
     * @param string Name for the config set.
     * @param Zend_Config_Ini Config set.
     * @return void
     */
    public function setConfig($name, Zend_Config $config)
    {
        $this->_config[$name] = $config;
        if ($name == 'basic') {
            $this->config = $config;
        }
    }
    
    /**
     * Get a configuration set.
     *
     * Workaround to support old style of configuration setting/getting.
     * 'basic' maps to the application configuration, others map to other
     * set configurations from setConfig.
     *
     * @param $name string Name of config set to get.
     * @return Zend_Config_Ini|null
     */
    public function getConfig($name)
    {
        if ($name == 'basic') {
            return $this->config;
        } else if (array_key_exists($name, $this->_config)) {
            return $this->_config[$name];
        } else {
            return null;
        }
    }
    
    /**
     * Magic call function; called for calls to undefined methods.
     * Creates magic getters and setters for storing and retrieving data.
     *
     * @see Omeka_Context::__set()
     * @see Omeka_Context::__get()
     * @param string $m Name of method called.
     * @param string $a Arguments to method.
     */
    public function __call($m, $a)
    {
        if (substr($m, 0, 3) == 'set') {
            $field = strtolower(substr($m, 3));
            $this->$field = $a[0];
        } else if (substr($m, 0, 3) == 'get') {
           $field = strtolower(substr($m, 3));
           return $this->$field;
        }
    }
    
    /**
     * Magic set function, called on attempts to set undefined properties.
     * Stores values in an internal array.
     *
     * @param string $name Name of property to set.
     * @param string $value Value to set.
     * @return void
     */
    public function __set($name, $value)
    {
        $field = strtolower($name);
        $this->_data[$field] = $value;
    }       
    
    /**
     * Magic get function, called on attempts to get undefined properties.
     * Gets values from internal array.
     *
     * @param string $name Name of property to get.
     * @return mixed|null
     */
    public function __get($name)
    {
        $field = strtolower($name);
        if (isset($this->_data[$field])) {
            return $this->_data[$field];
        }
        return null;
    }
    
    /**
     * Magic isset function, called on isset() or empty() calls for undefined
     * properties.
     * Checks if the desired property is stored in the internal array.
     *
     * @param string $name Name of property to check.
     * @return boolean
     */
    public function __isset($name)
    {
        $field = strtolower($name);
        return isset($this->_data[$field]);
    }
    
    /**
     * Set the request object on the front controller.
     * Has no effect the the front controller is not set up.
     * Provided for compatibility, simply delegates to Zend_Controller_Front.
     *
     * @see Zend_Controller_Front::setRequest()
     * @param string|Zend_Controller_Request_Abstract $request Request object.
     * @return void
     */
    public function setRequest($request)
    {
        if ($this->frontcontroller) {
            $this->frontcontroller->setRequest($request);
        }
    }
    
    /**
     * Get the request object from the front controller.
     * Has no effect if the front controller is not set up.
     * Provided for compatibility, simply delegates to Zend_Controller_Front.
     *
     * @see Zend_Controller_Front::getRequest()
     * @return Zend_Controller_Request_Abstract
     */
    public function getRequest()
    {
        if ($this->frontcontroller) {
            return $this->frontcontroller->getRequest();
        } else {
            return null;
        }
    }
}
