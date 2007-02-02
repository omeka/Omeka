<?php

// HTTPCONDITIONAL IS MISSING !!!

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
class Zend_Cache_Frontend_Page extends Zend_Cache_Core
{
    
    /**
     * This frontend specific options
     * 
     * ====> (boolean) httpConditional : 
     * - if true, http conditional mode is on
     * WARNING : httpConditional OPTION IS NOT IMPLEMENTED FOR THE MOMENT (TODO)
     * 
     * ====> (boolean) debugHeader :
     * - if true, a debug text is added before each cached pages
     * 
     * ====> (array) defaultOptions :
     * - an associative array of default options :
     *     - (boolean) cache : cache is on by default if true
     *     - (boolean) cacheWithXXXVariables  (XXXX = 'Get', 'Post', 'Session', 'Files' or 'Cookie') :
     *       if true,  cache is still on even if there are some variables in this superglobal array
     *       if false, cache is off if there are some variables in this superglobal array
     *     - (boolean) makeIdWithXXXVariables (XXXX = 'Get', 'Post', 'Session', 'Files' or 'Cookie') :
     *       if true, we have to use the content of this superglobal array to make a cache id 
     *       if false, the cache id won't be dependent of the content of this superglobal array
     *     
     * ====> (array) regexps :
     * - an associative array to set options only for some REQUEST_URI
     * - keys are (pcre) regexps
     * - values are associative array with specific options to set if the regexp matchs on $_SERVER['REQUEST_URI']
     *   (see defaultOptions for the list of available options)
     * - if several regexps match the $_SERVER['REQUEST_URI'], only the last one will be used  
     * 
     * @var array options
     */
    protected $_specificOptions = array(
        'httpConditional' => false,
        'debugHeader' => false,
        'defaultOptions' => array(
            'cacheWithGetVariables' => false,
            'cacheWithPostVariables' => false,
            'cacheWithSessionVariables' => false,
            'cacheWithFilesVariables' => false,
            'cacheWithCookieVariables' => false,
            'makeIdWithGetVariables' => true,
            'makeIdWithPostVariables' => true,
            'makeIdWithSessionVariables' => true,
            'makeIdWithFilesVariables' => true,
            'makeIdWithCookieVariables' => true,
            'cache' => true
        ),
        'regexps' => array()
    );
    
    /**
     * Internal array to store some options
     * 
     * @var array associative array of options
     */
    protected $_activeOptions = array();
            
    /**
     * Constructor
     * 
     * @param array $options associative array of options
     * @param boolean $doNotTestCacheValidity if set to true, the cache validity won't be tested
     */
    public function __construct($options = array())
    {
        if (isset($options['httpConditional'])) {
            if ($options['httpConditional']) {
                Zend_Cache::throwException('httpConditional is not implemented for the moment !');
            }
        }
        while (list($name, $value) = each($options)) {
            switch ($name) {
            case 'regexps':
                $this->_setRegexps($value);
                break;
            case 'defaultOptions':
                $this->_setDefaultOptions($value);
                break;
            default:
                $this->setOption($name, $value);
            }
        }
    }
    
    /**
     * Specific setter for the 'defaultOptions' option (with some additional tests)
     * 
     * @param array $options associative array
     */
    protected function _setDefaultOptions($options)
    {
        if (!is_array($options)) {
            Zend_Cache::throwException('defaultOptions must be an array !');
        }
        foreach ($options as $key=>$value) {
            if (!isset($this->_specificOptions['defaultOptions'][$key])) {
                Zend_Cache::throwException("unknown option [$key] !");
            } else {
                $this->_specificOptions['defaultOptions'][$key] = $value;
            }
        }
    }    
    
    /**
     * Specific setter for the 'regexps' option (with some additional tests)
     * 
     * @param array $options associative array
     */
    protected function _setRegexps($regexps)
    {
        if (!is_array($regexps)) {
            Zend_Cache::throwException('regexps option must be an array !');
        }
        foreach ($regexps as $regexp=>$conf) {
            if (!is_array($conf)) {
                Zend_Cache::throwException('regexps option must be an array of arrays !');
            }
            $validKeys = array_keys($this->_specificOptions['defaultOptions']);
            foreach ($conf as $key=>$value) {
                if (!in_array($key, $validKeys)) {
                    Zend_Cache::throwException("unknown option [$key] !");
                }
            }
        }
        $this->setOption('regexps', $regexps);
    }
    
    /**
     * Start the cache
     *
     * @param string $id (optional) a cache id (if you set a value here, maybe you have to use Output frontend instead)
     * @param boolean $doNotDie for unit testing only !
     * @return boolean true if the cache is hit (false else)
     */
    public function start($id = false, $doNotDie = false)
    {
        $lastMatchingRegexp = null;
        foreach ($this->_specificOptions['regexps'] as $regexp => $conf) {
            if (preg_match("`$regexp`", $_SERVER['REQUEST_URI'])) {
                $lastMatchingRegexp = $regexp;       
            }
        }
        $this->_activeOptions = $this->_specificOptions['defaultOptions'];
        if (!is_null($lastMatchingRegexp)) {
            $conf = $this->_specificOptions['regexps'][$lastMatchingRegexp];
            foreach ($conf as $key=>$value) {
                $this->_activeOptions[$key] = $value;
            }
        }
        if (!($this->_activeOptions['cache'])) {
            return false;
        }
        if (!$id) {
	        $id = $this->_makeId(); 
	        if (!$id) {
	            return false;
	        }
        }
        $data = $this->load($id);
        if ($data !== false) {
            if ($this->_specificOptions['debugHeader']) {
                echo 'DEBUG HEADER : This is a cached page !';
            }
            echo $data;
            if ($doNotDie) {
                return true;
            }
            die();
        }
        ob_start(array($this, '_flush'));
        ob_implicit_flush(false);
        return false;
    }
    
    /**
     * callback for output buffering
     * (shouldn't really be called manually)
     * 
     * @param string $data buffered output
     * @return string data to send to browser
     */
    public function _flush($data)
    {
        $this->save($data);
        return $data;
    }
    
    /**
     * Make an id depending on REQUEST_URI and superglobal arrays (depending on options)
     * 
     * @return mixed a cache id (string), false if the cache should have not to be used
     */
    private function _makeId()
    {
        $tmp = $_SERVER['REQUEST_URI'];
        foreach (array('Get', 'Post', 'Session', 'Files', 'Cookie') as $arrayName) {           
            $tmp2 = $this->_makePartialId($arrayName, $this->_activeOptions['cacheWith' . $arrayName . 'Variables'], $this->_activeOptions['makeIdWith' . $arrayName . 'Variables']);
            if ($tmp2===false) {
                return false;
            }
            $tmp = $tmp . $tmp2;
        }
        return md5($tmp);
    }
    
    /**
     * Make a partial id depending on options
     * 
     * @var string $arrayName superglobal array name
     * @var $bool1 if true, cache is still on even if there are some variables in the superglobal array
     * @var $bool2 if true, we have to use the content of the superglobal array to make a partial id
     * @return mixed partial id (string) or false if the cache should have not to be used
     */
    private function _makePartialId($arrayName, $bool1, $bool2)
    {
        switch ($arrayName) {
        case 'Get':
            $var = $_GET;
            break;
        case 'Post':
            $var = $_POST;
            break;
        case 'Session':
            if (isset($_SESSION)) {
                $var = $_SESSION;
            } else {
                $var = null;
            }
            break;
        case 'Cookie':
            if (isset($_COOKIE)) {
                $var = $_COOKIE;
            } else {
                $var = null;
            }
            break;
        case 'Files':
            $var = $_FILES;
            break;            
        default:
            return false;
        }    
        if ($bool1) {
            if ($bool2) {
                return serialize($var);
            }
            return '';
        }
        if (count($var) > 0) {
            return false;
        } 
        return '';
    }
    
}

