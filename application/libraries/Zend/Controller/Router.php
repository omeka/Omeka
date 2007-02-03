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
 * @package    Zend_Controller
 * @subpackage Router
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */ 


/** Zend_Controller_Exception */
require_once 'Zend/Controller/Exception.php';

/** Zend_Controller_Router_Interface */
require_once 'Zend/Controller/Router/Interface.php';

/** Zend_Controller_Request_Abstract */
require_once 'Zend/Controller/Request/Abstract.php';

/** Zend_Controller_Request_Http */
require_once 'Zend/Controller/Request/Http.php';


/**
 * Simple first implementation of a router, to be replaced
 * with rules-based URI processor.
 *
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage Router
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Controller_Router implements Zend_Controller_Router_Interface
{
    /**
     * Array of invocation parameters to use when instantiating action 
     * controllers
     * @var array 
     */
    protected $_invokeParams = array();

    /**
     * Constructor
     * 
     * @param array $params
     * @return void
     */
    public function __construct(array $params = array())
    {
        $this->setParams($params);
    }

    /**
     * Add or modify a parameter to use when instantiating an action controller
     * 
     * @param string $name
     * @param mixed $value 
     * @return Zend_Controller_Router
     */
    public function setParam($name, $value)
    {
        $name = (string) $name;
        $this->_invokeParams[$name] = $value;
        return $this;
    }

    /**
     * Set parameters to pass to action controller constructors
     * 
     * @param array $params 
     * @return Zend_Controller_Router
     */
    public function setParams(array $params)
    {
        $this->_invokeParams = array_merge($this->_invokeParams, $params);
        return $this;
    }

    /**
     * Retrieve a single parameter from the controller parameter stack
     * 
     * @param string $name 
     * @return mixed
     */
    public function getParam($name)
    {
        if(isset($this->_invokeParams[$name])) {
            return $this->_invokeParams[$name];
        }

        return null;
    }

    /**
     * Retrieve action controller instantiation parameters
     * 
     * @return array
     */
    public function getParams()
    {
        return $this->_invokeParams;
    }

    /**
     * Clear the controller parameter stack
     *
     * By default, clears all parameters. If a parameter name is given, clears 
     * only that parameter; if an array of parameter names is provided, clears 
     * each.
     * 
     * @param null|string|array single key or array of keys for params to clear
     * @return Zend_Controller_Router
     */
    public function clearParams($name = null)
    {
        if (null === $name) {
            $this->_invokeParams = array();
        } elseif (is_string($name) && isset($this->_invokeParams[$name])) {
            unset($this->_invokeParams[$name]);
        } elseif (is_array($name)) {
            foreach ($name as $key) {
                if (is_string($key) && isset($this->_invokeParams[$key])) {
                    unset($this->_invokeParams[$key]);
                }
            }
        }

        return $this;
    }

    /**
     * Route a request
     *
     * Routes requests of the format /controller/action by default (action may 
     * be omitted). Additional parameters may be specified as key/value pairs
     * separated by the directory separator: 
     * /controller/action/key/value/key/value. 
     *
     * To specify a module to use (basically, subdirectory) when routing the 
     * request, set the 'useModules' parameter via the front controller or 
     * {@link setParam()}: $router->setParam('useModules', true)
     * 
     * @param Zend_Controller_Request_Abstract $request 
     * @return void
     */
    public function route(Zend_Controller_Request_Abstract $request)
    {
        if (!$request instanceof Zend_Controller_Request_Http) {
            throw new Zend_Controller_Router_Exception('Zend_Controller_Router requires a Zend_Controller_Request_Http-based request object');
        }

        $pathInfo = $request->getPathInfo();
        $pathSegs = explode('/', trim($pathInfo, '/'));

        /**
         * Retrieve module if useModules is set in object
         */
        $useModules = $this->getParam('useModules');
        if (!empty($useModules)) {
            if (isset($pathSegs[0]) && !empty($pathSegs[0])) {
                $module = array_shift($pathSegs);
            }
        }

        /**
         * Get controller and action from request
         * Attempt to get from path_info; controller is first item, action 
         * second
         */
        if (isset($pathSegs[0]) && !empty($pathSegs[0])) {
            $controller = array_shift($pathSegs);
        }
        if (isset($pathSegs[0]) && !empty($pathSegs[0])) {
            $action = array_shift($pathSegs);
        }

        /**
         * Any optional parameters after the action are stored in
         * an array of key/value pairs:
         *
         * http://www.zend.com/controller-name/action-name/param-1/3/param-2/7
         *
         * $params = array(2) {
         *              ["param-1"]=> string(1) "3"
         *              ["param-2"]=> string(1) "7"
         * }
         */
        $params = array();
        $segs = count($pathSegs);
        if (0 < $segs) {
            for ($i = 0; $i < $segs; $i = $i + 2) {
                $key = urldecode($pathSegs[$i]);
                $value = isset($pathSegs[$i+1]) ? urldecode($pathSegs[$i+1]) : null;
                $params[$key] = $value;
            }
        }
        $request->setParams($params);

        /**
         * Set module, controller and action, now that params are set
         */
        if (isset($module)) {
            $request->setModuleName(urldecode($module));
        }

        if (isset($controller)) {
            $request->setControllerName(urldecode($controller));
        }

        if (isset($action)) {
            $request->setActionName(urldecode($action));
        }

        return $request;
    }
}
