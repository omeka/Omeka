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
 * @package    Zend_Controller
 * @subpackage Router
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id: Route.php 2800 2007-01-16 01:36:23Z bkarwin $
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */

/** Zend_Controller_Router_Exception */
require_once 'Zend/Controller/Router/Exception.php';

/** Zend_Controller_Router_Route_Interface */
require_once 'Zend/Controller/Router/Route/Interface.php';

/**
 * Route
 *
 * @package    Zend_Controller
 * @subpackage Router
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 * @see        http://manuals.rubyonrails.com/read/chapter/65
 */
class Zend_Controller_Router_Route implements Zend_Controller_Router_Route_Interface
{

    const URL_VARIABLE = ':';
    const URI_DELIMITER = '/';
    const REGEX_DELIMITER = '#';
    const DEFAULT_REGEX = '.+';
    
    protected $_parts;
    protected $_defaults = array();
    protected $_requirements = array();
    protected $_staticCount = 0;
    protected $_vars = array();
    protected $_params = array();
    protected $_values = null;

    /**
     * Prepares the route for mapping by splitting (exploding) it 
     * to a corresponding atomic parts. These parts are assigned 
     * a position which is later used for matching and preparing values.  
     *
     * @param string Map used to match with later submitted URL path 
     * @param array Defaults for map variables with keys as variable names
     * @param array Regular expression requirements for variables (keys as variable names)
     */
    public function __construct($route, $defaults = array(), $reqs = array())
    {

        $route = trim($route, self::URI_DELIMITER);
        $this->_defaults = (array) $defaults;
        $this->_requirements = (array) $reqs;

        if ($route != '') { 
    
            foreach (explode(self::URI_DELIMITER, $route) as $pos => $part) {
    
                if (substr($part, 0, 1) == self::URL_VARIABLE) {
                    $name = substr($part, 1);
                    $regex = (isset($reqs[$name]) ? $reqs[$name] : self::DEFAULT_REGEX);
                    $this->_parts[$pos] = array('name' => $name, 'regex' => $regex);
                    $this->_vars[] = $name;
                } else {
                    $this->_parts[$pos] = array('regex' => preg_quote($part, self::REGEX_DELIMITER));
                    if ($part != '*') {
                        $this->_staticCount++;
                    }
                }
    
            }

        }

    }

    /**
     * Matches a user submitted path with parts defined by a map. Assigns and 
     * returns an array of variables on a successful match.  
     *
     * @param string Path used to match against this routing map 
     * @return array|false An array of assigned values or a false on a mismatch
     */
    public function match($path)
    {

        $pathStaticCount = 0;
        $defaults = $this->_defaults;
        
        if (count($defaults)) {
        	$unique = array_combine(array_keys($defaults), array_fill(0, count($defaults), true));
        } else {
        	$unique = array();
        }

        $path = trim($path, self::URI_DELIMITER);

        if ($path != '') {
        
            $path = explode(self::URI_DELIMITER, $path);
        
            foreach ($path as $pos => $pathPart) {
                
                if (!isset($this->_parts[$pos])) {
                    return false;
                }
                
                if ($this->_parts[$pos]['regex'] == '\*') {
                    $parts = array_slice($path, $pos);
                    $pos = count($parts);
                    if ($pos % 2) {
                        $parts[] = null;
                    }
                    foreach(array_chunk($parts, 2) as $part) {
                        list($var, $value) = $part;
                        $var = urldecode($var);
                        if (!array_key_exists($var, $unique)) {
                            $this->_params[$var] = urldecode($value);
                            $unique[$var] = true;
                        }
                    }
                    break;
                }
                
                $part = $this->_parts[$pos];
                $name = isset($part['name']) ? $part['name'] : null;
                $regex = self::REGEX_DELIMITER . '^' . $part['regex'] . '$' . self::REGEX_DELIMITER . 'iu';
    
                $pathPart = urldecode($pathPart);
    
                if (!preg_match($regex, $pathPart)) {
                    return false;
                }
                
                if ($name !== null) {
                    // It's a variable. Setting a value
                    $this->_params[$name] = $pathPart;
                    $unique[$name] = true;
                } else {
                    $pathStaticCount++;
                }
    
            }
            
        }
        
        $this->_values = $this->_params + $defaults;

        // Check if all static mappings have been met
        if ($this->_staticCount != $pathStaticCount) {
            return false;
        }
        
        // Check if all map variables have been initialized
        foreach ($this->_vars as $var) {
            if (!array_key_exists($var, $this->_values)) {
                return false;
            }
        }

        return $this->_values;

    }

    /**
     * Assembles user submitted parameters forming a URL path defined by this route 
     *
     * @param array An array of variable and value pairs used as parameters 
     * @return string Route path with user submitted parameters
     */
    public function assemble($data = array(), $reset = false)
    {

        $url = array();
        
        if (!$reset) {
            $data += $this->_params;
        }

        foreach ($this->_parts as $key => $part) {
            
            if (isset($part['name'])) {

                if (isset($data[$part['name']])) {
                    $url[$key] = $data[$part['name']];
                    unset($data[$part['name']]);
                } elseif (isset($this->_values[$part['name']])) {
                    $url[$key] = $this->_values[$part['name']];
                } elseif (isset($this->_defaults[$part['name']])) {
                    $url[$key] = $this->_defaults[$part['name']];
                } else
                    throw new Zend_Controller_Router_Exception($part['name'] . ' is not specified');

            } else {
                
                if ($part['regex'] != '\*') {
                    $url[$key] = $part['regex'];
                } else {
                    foreach ($data as $var => $value) {
                        $url[$var] = $var . self::URI_DELIMITER . $value;
                    } 
                }

            }
            
        }

        return implode(self::URI_DELIMITER, $url);

    }
    
    /**
     * Return a single parameter of route's defaults 
     *
     * @param name Array key of the parameter 
     * @return string Previously set default
     */
    public function getDefault($name) {
        if (isset($this->_defaults[$name])) {
            return $this->_defaults[$name];
        }
        return null;
    }

    /**
     * Return an array of defaults 
     *
     * @return array Route defaults
     */
    public function getDefaults() {
        return $this->_defaults;
    }

}
