<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_View
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Zend_View_Interface
 */
require_once 'Zend/View/Interface.php';

/**
 * Abstract class for Zend_View to help enforce private constructs.
 *
 * @category   Zend
 * @package    Zend_View
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_View_Abstract implements Zend_View_Interface
{
    /**
     * Path stack for script, helper, and filter directories.
     *
     * @var array
     */
    private $_path = array(
        'script' => array(),
        'helper' => array(),
        'filter' => array(),
    );

    /**
     * Script file name to execute
     *
     * @var string
     */
    private $_file = null;

    /**
     * Instances of helper objects.
     *
     * @var array
     */
    private $_helper = array();

    /**
     * Map of helper => class pairs to help in determining helper class from 
     * name
     * @var array 
     */
    private $_helperLoaded = array();

    /**
     * Stack of Zend_View_Filter names to apply as filters.
     *
     * @var array
     */
    private $_filter = array();

    /**
     * Map of filter => class pairs to help in determining filter class from 
     * name
     * @var array 
     */
    private $_filterLoaded = array();

    /**
     * Callback for escaping.
     *
     * @var string
     */
    private $_escape = 'htmlspecialchars';

    /**
     * Encoding to use in escaping mechanisms; defaults to latin1 (ISO-8859-1)
     * @var string 
     */
    private $_encoding = 'ISO-8859-1';

    /**
     * Constructor.
     *
     * @param array $config Configuration key-value pairs.
     */
    public function __construct($config = array())
    {
        // set inital paths and properties 
        $this->setScriptPath(null);
        $this->setHelperPath(null);
        $this->setFilterPath(null);

        // user-defined escaping callback
        if (array_key_exists('escape', $config)) {
            $this->setEscape($config['escape']);
        }

        // encoding
        if (array_key_exists('encoding', $config)) {
            $this->setEncoding($config['encoding']);
        }

        // user-defined view script path
        if (array_key_exists('scriptPath', $config)) {
            $this->addScriptPath($config['scriptPath']);
        }

        // user-defined helper path
        if (array_key_exists('helperPath', $config)) {
            $this->addHelperPath($config['helperPath']);
        }

        // user-defined filter path
        if (array_key_exists('filterPath', $config)) {
            $this->addFilterPath($config['filterPath']);
        }

        // user-defined filters
        if (array_key_exists('filter', $config)) {
            $this->addFilter($config['filter']);
        }
    }

    /**
     * Return the template engine object
     *
     * Returns the object instance, as it is its own template engine
     * 
     * @return Zend_View_Abstract
     */
    public function getEngine()
    {
        return $this;
    }

    /**
     * Allows testing with empty() and isset() to work inside
     * templates.
     *
     * @param  string $key
     * @return boolean
     */
    public function __isset($key)
    {
        if ('_' != substr($key, 0, 1)) {
            return isset($this->$key);
        }

        return false;
    }

    /**
     * Directly assigns a variable to the view script.
     *
     * Checks first to ensure that the caller is not attempting to set a 
     * protected or private member (by checking for a prefixed underscore); if 
     * not, the public member is set; otherwise, an exception is raised.
     *
     * @param string $key The variable name.
     * @param mixed $val The variable value.
     * @return void
     * @throws Zend_View_Exception if an attempt to set a private or protected 
     * member is detected
     */
    public function __set($key, $val)
    {
        if ('_' != substr($key, 0, 1)) {
            $this->$key = $val;
            return;
        }

        require_once 'Zend/View/Exception.php';
        throw new Zend_View_Exception('Setting private or protected class members is not allowed');
    }

    /**
     * Allows unset() on object properties to work
     *
     * @param string $key
     * @return void
     */
    public function __unset($key)
    {
        if ('_' != substr($key, 0, 1) && isset($this->$key)) {
            unset($this->$key);
        }
    }

    /**
     * Accesses a helper object from within a script.
     *
     * @param string $name The helper name.
     * @param array $args The parameters for the helper.
     * @return string The result of the helper output.
     */
    public function __call($name, $args)
    {
        // is the helper already loaded?
        if (empty($this->_helper[$name])) {
            // load class and create instance
            $class = $this->_loadClass('helper', $name);
            $this->_helper[$name] = new $class();
        }

        // call the helper method
        return call_user_func_array(
            array($this->_helper[$name], $name),
            $args
        );
    }

    /**
     * Adds to the stack of view script paths in LIFO order.
     *
     * @param string|array The directory (-ies) to add.
     * @return void
     */
    public function addScriptPath($path)
    {
        $this->_addPath('script', $path);
    }

    /**
     * Resets the stack of view script paths.
     *
     * To clear all paths, use Zend_View::setScriptPath(null).
     *
     * @param string|array The directory (-ies) to set as the path.
     * @return void
     */
    public function setScriptPath($path)
    {
        $this->_path['script'] = array();
        $this->_addPath('script', $path);
    }

    /**
     * Returns an array of all currently set script paths
     * 
     * @return array
     */
    public function getScriptPaths()
    {
        return $this->_getPaths('script');
    }

    /**
     * Adds to the stack of helper paths in LIFO order.
     *
     * @param string|array The directory (-ies) to add.
     * @param string $classPrefix Class prefix to use with classes in this 
     * directory; defaults to Zend_View_Helper
     * @return void
     */
    public function addHelperPath($path, $classPrefix = 'Zend_View_Helper_')
    {
        if (!empty($classPrefix) && ('_' != substr($classPrefix, -1))) {
            $classPrefix .= '_';
        }

        $this->_addPath('helper', $path, $classPrefix);
    }

    /**
     * Resets the stack of helper paths.
     *
     * To clear all paths, use Zend_View::setHelperPath(null).
     *
     * @param string|array $path The directory (-ies) to set as the path.
     * @param string $classPrefix The class prefix to apply to all elements in 
     * $path; defaults to Zend_View_Helper
     * @return void
     */
    public function setHelperPath($path, $classPrefix = 'Zend_View_Helper_')
    {
        if (!empty($classPrefix) && ('_' != substr($classPrefix, -1))) {
            $classPrefix .= '_';
        }

        $this->_setPath('helper', $path, $classPrefix);
    }

    /**
     * Returns an array of all currently set helper paths
     * 
     * @return array
     */
    public function getHelperPaths()
    {
        return $this->_getPaths('helper');
    }

    public function getHelpers()
    {
        return $this->_helper;
    }

    /**
     * Adds to the stack of filter paths in LIFO order.
     *
     * @param string|array The directory (-ies) to add.
     * @param string $classPrefix Class prefix to use with classes in this 
     * directory; defaults to Zend_View_Filter
     * @return void
     */
    public function addFilterPath($path, $classPrefix = 'Zend_View_Filter_')
    {
        if (!empty($classPrefix) && ('_' != substr($classPrefix, -1))) {
            $classPrefix .= '_';
        }

        $this->_addPath('filter', $path, $classPrefix);
    }

    /**
     * Resets the stack of filter paths.
     *
     * To clear all paths, use Zend_View::setFilterPath(null).
     *
     * @param string|array The directory (-ies) to set as the path.
     * @param string $classPrefix The class prefix to apply to all elements in 
     * $path; defaults to Zend_View_Filter
     * @return void
     */
    public function setFilterPath($path, $classPrefix = 'Zend_View_Filter_')
    {
        if (!empty($classPrefix) && ('_' != substr($classPrefix, -1))) {
            $classPrefix .= '_';
        }

        $this->_setPath('filter', $path, $classPrefix);
    }

    /**
     * Returns an array of all currently set filter paths
     * 
     * @return array
     */
    public function getFilterPaths()
    {
        return $this->_getPaths('filter');
    }

    /**
     * Return associative array of path types => paths
     * 
     * @return array
     */
    public function getAllPaths()
    {
        return $this->_path;
    }

    /**
     * Add one or more filters to the stack in FIFO order.
     *
     * @param string|array One or more filters to add.
     * @return void
     */
    public function addFilter($name)
    {
        foreach ((array) $name as $val) {
            $this->_filter[] = $val;
        }
    }

    /**
     * Resets the filter stack.
     *
     * To clear all filters, use Zend_View::setFilter(null).
     *
     * @param string|array One or more filters to set.
     * @return void
     */
    public function setFilter($name)
    {
        $this->_filter = array();
        $this->addFilter($name);
    }

    /**
     * Sets the _escape() callback.
     *
     * @param mixed $spec The callback for _escape() to use.
     * @return void
     */
    public function setEscape($spec)
    {
        $this->_escape = $spec;
    }

    /**
     * Assigns variables to the view script via differing strategies.
     *
     * Zend_View::assign('name', $value) assigns a variable called 'name'
     * with the corresponding $value.
     *
     * Zend_View::assign($array) assigns the array keys as variable
     * names (with the corresponding array values).
     *
     * @param string|array The assignment strategy to use.
     * @param mixed (Optional) If assigning a named variable, use this
     * as the value.
     * @return void
     * @see __set()
     * @throws Zend_View_Exception if $spec is neither a string nor an array, 
     * or if an attempt to set a private or protected member is detected
     */
    public function assign($spec, $value = null)
    {
        // which strategy to use?
        if (is_string($spec)) {
            // assign by name and value
            if ('_' == substr($spec, 0, 1)) {
                require_once 'Zend/View/Exception.php';
                throw new Zend_View_Exception('Setting private or protected class members is not allowed');
            }
            $this->$spec = $value;
        } elseif (is_array($spec)) {
            // assign from associative array
            $error = false;
            foreach ($spec as $key => $val) {
                if ('_' == substr($key, 0, 1)) {
                    $error = true;
                    break;
                }
                $this->$key = $val;
            }
            if ($error) {
                require_once 'Zend/View/Exception.php';
                throw new Zend_View_Exception('Setting private or protected class members is not allowed');
            }
        } else {
            require_once 'Zend/View/Exception.php';
            throw new Zend_View_Exception('assign() expects a string or array, received ' . gettype($spec));
        }
    }

    /**
     * Return list of all assigned variables
     *
     * Returns all public properties of the object. Reflection is not used 
     * here as testing reflection properties for visibility is buggy.
     * 
     * @return array
     */
    public function getVars()
    {
        $vars   = get_object_vars($this);
        foreach ($vars as $key => $value) {
            if ('_' == substr($key, 0, 1)) {
                unset($vars[$key]);
            }
        }

        return $vars;
    }

    /**
     * Clear all assigned variables
     *
     * Clears all variables assigned to Zend_View either via {@link assign()} or 
     * property overloading ({@link __set()}).
     * 
     * @return void
     */
    public function clearVars()
    {
        $vars   = get_object_vars($this);
        foreach ($vars as $key => $value) {
            if ('_' != substr($key, 0, 1)) {
                unset($this->$key);
            }
        }
    }

    /**
     * Processes a view script and returns the output.
     *
     * @param string $name The script script name to process.
     * @return string The script output.
     */
    public function render($name)
    {
        // find the script file name using the parent private method
        $this->_file = $this->_script($name);
        unset($name); // remove $name from local scope

        ob_start();
        $this->_run($this->_file); 

        return $this->_filter(ob_get_clean()); // filter output
    }

    /**
     * Escapes a value for output in a view script.
     *
     * If escaping mechanism is one of htmlspecialchars or htmlentities, uses 
     * {@link $_encoding} setting.
     *
     * @param mixed $var The output to escape.
     * @return mixed The escaped value.
     */
    public function escape($var)
    {
        if (in_array($this->_escape, array('htmlspecialchars', 'htmlentities'))) {
            return call_user_func($this->_escape, $var, ENT_COMPAT, $this->_encoding);
        }

        return call_user_func($this->_escape, $var);
    }

    /**
     * Set encoding to use with htmlentities() and htmlspecialchars()
     * 
     * @param string $encoding 
     * @return void
     */
    public function setEncoding($encoding)
    {
        $this->_encoding = $encoding;
    }

    /**
     * Return current escape encoding
     * 
     * @return string
     */
    public function getEncoding()
    {
        return $this->_encoding;
    }

    /**
     * Finds a view script from the available directories.
     *
     * @param $name string The base name of the script.
     * @return void
     */
    protected function _script($name)
    {
        if (0 == count($this->_path['script'])) {
            require_once 'Zend/View/Exception.php';
            throw new Zend_View_Exception('no view script directory set; unable to determine location for view script');
        }

        foreach ($this->_path['script'] as $dir) {
            if (is_readable($dir . $name)) {
                return $dir . $name;
            }
        }

        require_once 'Zend/View/Exception.php';
        throw new Zend_View_Exception("script '$name' not found in path");
    }

    /**
     * Applies the filter callback to a buffer.
     *
     * @param string $buffer The buffer contents.
     * @return string The filtered buffer.
     */
    private function _filter($buffer)
    {
        // loop through each filter class
        foreach ($this->_filter as $name) {
            // load and apply the filter class
            $class = $this->_loadClass('filter', $name);
            $buffer = call_user_func(array($class, 'filter'), $buffer);
        }

        // done!
        return $buffer;
    }

    /**
     * Adds paths to the path stack in LIFO order.
     *
     * Zend_View::_addPath($type, 'dirname') adds one directory
     * to the path stack.
     *
     * Zend_View::_addPath($type, $array) adds one directory for
     * each array element value.
     *
     * In the case of filter and helper paths, $prefix should be used to 
     * specify what class prefix to use with the given path.
     *
     * @param string $type The path type ('script', 'helper', or 'filter').
     * @param string|array $path The path specification.
     * @param string $prefix Class prefix to use with path (helpers and filters 
     * only)
     * @return void
     */
    private function _addPath($type, $path, $prefix = null)
    {
        foreach ((array) $path as $dir) {
            // attempt to strip any possible separator and
            // append the system directory separator
            $dir = rtrim($dir, '\\/' . DIRECTORY_SEPARATOR) 
                . DIRECTORY_SEPARATOR;

            switch ($type) {
                case 'script':
                    // add to the top of the stack.
                    array_unshift($this->_path[$type], $dir);
                    break;
                case 'filter':
                case 'helper':
                default:
                    // add as array with prefix and dir keys
                    array_unshift($this->_path[$type], array('prefix' => $prefix, 'dir' => $dir));
                    break;
            }
        }
    }

    /**
     * Resets the path stack for helpers and filters.
     *
     * @param string $type The path type ('helper' or 'filter').
     * @param string|array $path The directory (-ies) to set as the path.
     * @param string $classPrefix Class prefix to apply to elements of $path
     */
    private function _setPath($type, $path, $classPrefix = null)
    {
        $dir = DIRECTORY_SEPARATOR . ucfirst($type) . DIRECTORY_SEPARATOR;

        switch ($type) {
            case 'script':
                $this->_path[$type] = array(dirname(__FILE__) . $dir);
                $this->_addPath($type, $path);
                break;
            case 'filter':
            case 'helper':
            default:
                $this->_path[$type] = array(array(
                    'prefix' => 'Zend_View_' . ucfirst($type) . '_',
                    'dir'    => dirname(__FILE__) . $dir
                ));
                $this->_addPath($type, $path, $classPrefix);
                break;
        }
    }

    /**
     * Return all paths for a given path type
     * 
     * @param string $type The path type  ('helper', 'filter', 'script')
     * @return array
     */
    private function _getPaths($type)
    {
        return $this->_path[$type];
    }

    /**
     * Loads a helper or filter class.
     *
     * @param string $type The class type ('helper' or 'filter').
     * @param string $name The base name.
     * @param string The full class name.
     */
    private function _loadClass($type, $name)
    {
        // check to see if name => class mapping exists for helper/filter
        $classLoaded = '_' . $type . 'Loaded';
        $classAccess = '_set' . ucfirst($type) . 'Class';
        if (isset($this->$classLoaded[$name])) {
            echo "Already loaded $name per $classLoaded\n", var_export($this->$classLoaded, 1), "\n";
            return $this->$classLoaded[$name];
        }

        // only look for "$Name.php"
        $file = ucfirst($name) . '.php';

        // do LIFO search for helper
        foreach ($this->_path[$type] as $info) {
            $dir    = $info['dir'];
            $prefix = $info['prefix'];

            $class = $prefix . ucfirst($name);
            
            if (class_exists($class, false)) {
                $this->$classAccess($name, $class);
                return $class;
            } elseif (is_readable($dir . $file)) {
                include_once $dir . $file;

                if (class_exists($class, false)) {
                    $this->$classAccess($name, $class);
                    return $class;
                }
            }
        }

        require_once 'Zend/View/Exception.php';
        throw new Zend_View_Exception("$type '$name' not found in path");
    }

    /**
     * Register helper class as loaded
     * 
     * @param string $name 
     * @param string $class 
     * @return void
     */
    private function _setHelperClass($name, $class)
    {
        $this->_helperLoaded[$name] = $class;
    }

    /**
     * Register filtper class as loaded
     * 
     * @param string $name 
     * @param string $class 
     * @return void
     */
    private function _setFilterClass($name, $class)
    {
        $this->_filterLoaded[$name] = $class;
    }

    /**
     * Use to include the view script in a scope that only allows public 
     * members.
     * 
     * @return mixed
     */
    abstract protected function _run();
}
