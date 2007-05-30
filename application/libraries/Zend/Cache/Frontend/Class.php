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
 * @package    Zend_Cache
 * @subpackage Frontend
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
 
/**
 * Zend_Cache_Core
 */
require_once 'Zend/Cache/Core.php';


/**
 * @package    Zend_Cache
 * @subpackage Frontend
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Cache_Frontend_Class extends Zend_Cache_Core
{
       
    /**
     * Available options
     * 
     * ====> (mixed) cachedEntity :
     * - if set to a class name, we will cache an abstract class and will use only static calls
     * - if set to an object, we will cache this object methods
     * 
     * ====> (boolean) cacheByDefault : 
     * - if true, method calls will be cached by default
     * 
     * ====> (array) cachedMethods :
     * - an array of method names which will be cached (even if cacheByDefault = false)
     * 
     * ====> (array) nonCachedMethods :
     * - an array of method names which won't be cached (even if cacheByDefault = true)
     * 
     * @var array available options
     */
    protected $_specificOptions = array(
    	'cachedEntity' => null,
    	'cacheByDefault' => true,
    	'cachedMethods' => array(),
        'nonCachedMethods' => array()
    );
            
    /**
     * The cached object or the name of the cached abstract class
     * 
     * @var mixed
     */
    private $_cachedEntity = null;
       
    /**
     * Constructor
     * 
     * @param array $options associative array of options
     */
    public function __construct($options = array())
    {
        if (!isset($options['cachedEntity'])) {
            Zend_Cache::throwException('cachedEntity must be set !');
        } else {
            if (!is_string($options['cachedEntity']) && !is_object($options['cachedEntity'])) {
                Zend_Cache::throwException('cachedEntity must be an object or a class name');
            }
        }
        $this->_cachedEntity = $options['cachedEntity'];
        while (list($name, $value) = each($options)) {
            $this->setOption($name, $value);
        }
        $this->setOption('automaticSerialization', true);
    }    
    
    /**
     * Main method : call the specified method or get the result from cache
     * 
     * @param string $name method name
     * @param array $parameters method parameters
     * @return mixed result
     */
    public function __call($name, $parameters) 
    {
        $cacheBool1 = $this->_specificOptions['cacheByDefault'];
        $cacheBool2 = in_array($name, $this->_specificOptions['cachedMethods']);
        $cacheBool3 = in_array($name, $this->_specificOptions['nonCachedMethods']);
        $cache = (($cacheBool1 || $cacheBool2) && (!$cacheBool3));
        if (!$cache) {
            // We do not have not cache
            return call_user_func_array(array($this->_cachedEntity, $name), $parameters);
        }
        $id = $this->_makeId($name, $parameters);       
        if ($this->test($id)) {
            // A cache is available
            $result = $this->load($id);
            $output = $result[0];
            $return = $result[1];                      
        } else {
            // A cache is not available
            ob_start();
            ob_implicit_flush(false);
            $return = call_user_func_array(array($this->_cachedEntity, $name), $parameters);
            $output = ob_get_contents();
            ob_end_clean();
            $data = array($output, $return);
            $this->save($data);
        }
        echo $output;
        return $return;
    }
    
    /**
     * Make a cache id from the method name and parameters
     * 
     * @param string $name method name
     * @param array $parameters method parameters
     * @return string cache id
     */        
    private function _makeId($name, $parameters) 
    {
        return md5($name . serialize($parameters));
    }
                 
}
