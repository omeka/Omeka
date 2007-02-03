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
 * @subpackage Backend
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

 
/**
 * @package    Zend_Cache
 * @subpackage Backend
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Cache_Backend
{   
   
    // ------------------
    // --- Properties ---
    // ------------------
    
    /**
     * Frontend or Core directives
     * 
     * =====> (int) lifeTime :
     * - Cache lifetime (in seconds)
     * - If null, the cache is valid forever
     * 
     * =====> (int) logging :
     * - if set to true, a logging is activated throw Zend_Log
     * 
     * @var array directives
     */
    protected $_directives = array(
        'lifeTime' => 3600,
        'logging' => false
    );  
    
    /**
     * Available options
     * 
     * @var array available options
     */
    protected $_options = array();
    
    
    // ----------------------
    // --- Public methods ---
    // ----------------------
    
    /**
     * Constructor
     * 
     * @param array $options associative array of options
     */
    public function __construct($options = array())
    {
        if (!is_array($options)) Zend_Cache::throwException('Options parameter must be an array');
        while (list($name, $value) = each($options)) {
            $this->setOption($name, $value);
        }
    }  
     
    /**
     * Set the frontend directives
     * 
     * @param array $directives assoc of directives
     */
    public function setDirectives($directives)
    {
        if (!is_array($directives)) Zend_Cache::throwException('Directives parameter must be an array');
        while (list($name, $value) = each($directives)) {
            if (!is_string($name)) {
                Zend_Cache::throwException("Incorrect option name : $name");
            }
            if (array_key_exists($name, $this->_directives)) {
                $this->_directives[$name] = $value;
            }
            // specific test for the logging directive
            if ($name == 'logging') {
                if ((!class_exists('Zend_Log', false)) && ($value)) {
                    Zend_Cache::throwException('logging feature is on but Zend_Log is not "loaded"');
                }
            }
        }
    } 
    
    /**
     * Set an option
     * 
     * @param string $name
     * @param mixed $value
     */ 
    public function setOption($name, $value)
    {
        if (!is_string($name) || !array_key_exists($name, $this->_options)) {
            Zend_Cache::throwException("Incorrect option name : $name");
        }
        $this->_options[$name] = $value;
    }   
    
    /**
     * Return a system-wide tmp directory 
     *
     * @return string system-wide tmp directory
     */
    static function getTmpDir()
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // windows...
            foreach (array($_ENV, $_SERVER) as $tab) {
                foreach (array('TEMP', 'TMP', 'windir', 'SystemRoot') as $key) {
                    if (isset($tab[$key])) {
                        $result = $tab[$key];
                        if (($key == 'windir') or ($key == 'SystemRoot')) {
                            $result = $result . '\\temp';
                        }
                        return $result;
                    }
                }
            }
            return '\temp';
        } else {
            // unix...
            if (isset($_ENV['TMPDIR']))    return $_ENV['TMPDIR'];
            if (isset($_SERVER['TMPDIR'])) return $_SERVER['TMPDIR'];
            return '/tmp';
        }
    }
    
}
