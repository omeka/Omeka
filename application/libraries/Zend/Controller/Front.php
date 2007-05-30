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
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/** Zend_Controller_Exception */
require_once 'Zend/Controller/Exception.php';

/** Zend_Controller_Plugin_Broker */
require_once 'Zend/Controller/Plugin/Broker.php';

/** Zend_Controller_Request_Abstract */
require_once 'Zend/Controller/Request/Abstract.php';

/** Zend_Controller_Router_Interface */
require_once 'Zend/Controller/Router/Interface.php';

/** Zend_Controller_Dispatcher_Interface */
require_once 'Zend/Controller/Dispatcher/Interface.php';

/** Zend_Controller_Response_Abstract */
require_once 'Zend/Controller/Response/Abstract.php';

/**
 * @category   Zend
 * @package    Zend_Controller
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Controller_Front
{
    /**
     * Base URL
     * @var string 
     */
    protected $_baseUrl = null;

    /**
     * Directory|ies where controllers are stored
     * 
     * @var string|array
     */
    protected $_controllerDir = null;

    /**
     * Instance of Zend_Controller_Dispatcher_Interface
     * @var Zend_Controller_Dispatcher_Interface
     */
    protected $_dispatcher = null;

    /**
     * Singleton instance
     * @var self 
     */
    private static $_instance = null;

    /**
     * Array of invocation parameters to use when instantiating action
     * controllers
     * @var array
     */
    protected $_invokeParams = array();

    /**
     * Instance of Zend_Controller_Plugin_Broker
     * @var Zend_Controller_Plugin_Broker
     */
    protected $_plugins = null;

    /**
     * Instance of Zend_Controller_Request_Abstract
     * @var Zend_Controller_Request_Abstract
     */
    protected $_request = null;

    /**
     * Instance of Zend_Controller_Response_Abstract
     * @var Zend_Controller_Response_Abstract
     */
    protected $_response = null;

    /**
     * Whether or not to return the response prior to rendering output while in 
     * {@link dispatch()}; default is to send headers and render output.
     * @var boolean
     */
    protected $_returnResponse = false;

    /**
     * Instance of Zend_Controller_Router_Interface
     * @var Zend_Controller_Router_Interface
     */
    protected $_router = null;

    /**
     * Whether or not exceptions encountered in {@link dispatch()} should be 
     * thrown or trapped in the response object
     * @var boolean
     */
    protected $_throwExceptions = false;

    /**
     * Constructor
     *
     * Instantiate using {@link getInstance()}; front controller is a singleton 
     * object.
     *
     * Instantiates the plugin broker.
     *
     * @return void
     */
    private function __construct()
    {
        $this->_plugins = new Zend_Controller_Plugin_Broker();
    }

    /**
     * Singleton instance
     * 
     * @return Zend_Controller_Front
     */
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * Resets all object properties of the singleton instance
     *
     * Primarily used for testing; could be used to chain front controllers.
     * 
     * @return void
     */
    public function resetInstance()
    {
        $reflection = new ReflectionObject($this);
        foreach ($reflection->getProperties() as $property) {
            $name = $property->getName();
            switch ($name) {
                case '_instance':
                    break;
                case '_invokeParams':
                    $this->{$name} = array();
                    break;
                case '_plugins':
                    $this->{$name} = new Zend_Controller_Plugin_Broker();
                    break;
                case '_throwExceptions':
                case '_returnResponse':
                    $this->{$name} = false;
                    break;
                default:
                    $this->{$name} = null;
                    break;
            }
        }
    }

    /**
     * Convenience feature, calls setControllerDirectory()->setRouter()->dispatch()
     *
     * In PHP 5.1.x, a call to a static method never populates $this -- so run() 
     * may actually be called after setting up your front controller.
     *
     * @param string|array $controllerDirectory Path to Zend_Controller_Action 
     * controller classes or array of such paths
     * @return void
     * @throws Zend_Controller_Exception if called from an object instance
     */
    static public function run($controllerDirectory)
    {
        require_once 'Zend/Controller/Router.php';
        $frontController = self::getInstance();
        $frontController
            ->setControllerDirectory($controllerDirectory)
            ->setRouter(new Zend_Controller_Router())
            ->dispatch();
    }

    /**
     * Add a controller directory to the controller directory stack
     * 
     * @param string $directory 
     * @return Zend_Controller_Front
     */
    public function addControllerDirectory($directory)
    {
        $this->_controllerDir[] = (string) $directory;
        return $this;
    }

    /**
     * Set controller directory
     *
     * Stores controller directory to pass to dispatcher. May be an array of 
     * directories or a string containing a single directory.
     *
     * @param string|array $directory Path to Zend_Controller_Action controller 
     * classes or array of such paths
     * @return Zend_Controller_Front
     */
    public function setControllerDirectory($directory)
    {
        $this->_controllerDir = (array) $directory;
        return $this;
    }

    /**
     * Retrieve controller directory
     *
     * Retrieves stored controller directory
     *
     * @return string|array
     */
    public function getControllerDirectory()
    {
        return $this->_controllerDir;
    }

    /**
     * Set the default controller (unformatted string)
     *
     * @param string $controller
     * @return Zend_Controller_Front
     */
    public function setDefaultController($controller)
    {
        $dispatcher = $this->getDispatcher();
        $dispatcher->setDefaultController($controller);
        return $this;
    }

    /**
     * Retrieve the default controller (unformatted string)
     *
     * @return string
     */
    public function getDefaultController()
    {
        return $this->getDispatcher()->getDefaultController();
    }

    /**
     * Set the default action (unformatted string)
     *
     * @param string $action
     * @return Zend_Controller_Front
     */
    public function setDefaultAction($action)
    {
        $dispatcher = $this->getDispatcher();
        $dispatcher->setDefaultAction($action);
        return $this;
    }

    /**
     * Retrieve the default action (unformatted string)
     *
     * @return string
     */
    public function getDefaultAction()
    {
        return $this->getDispatcher()->getDefaultAction();
    }

    /**
     * Set request class/object
     *
     * Set the request object.  The request holds the request environment.
     *
     * If a class name is provided, it will instantiate it
     *
     * @param string|Zend_Controller_Request_Abstract $request
     * @throws Zend_Controller_Exception if invalid request class
     * @return Zend_Controller_Front
     */
    public function setRequest($request)
    {
        if (is_string($request)) {
            Zend::loadClass($request);
            $request = new $request();
        }
        if (!$request instanceof Zend_Controller_Request_Abstract) {
            throw new Zend_Controller_Exception('Invalid request class');
        }

        $this->_request = $request;

        return $this;
    }

    /**
     * Return the request object.
     *
     * @return null|Zend_Controller_Request_Abstract
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * Set router class/object
     *
     * Set the router object.  The router is responsible for mapping
     * the request to a controller and action.
     *
     * If a class name is provided, instantiates router with any parameters
     * registered via {@link setParam()} or {@link setParams()}.
     *
     * @param string|Zend_Controller_Router_Interface $router
     * @throws Zend_Controller_Exception if invalid router class
     * @return Zend_Controller_Front
     */
    public function setRouter($router)
    {
        if (is_string($router)) {
            Zend::loadClass($router);
            $router = new $router($this->getParams());
        }
        if (!$router instanceof Zend_Controller_Router_Interface) {
            throw new Zend_Controller_Exception('Invalid router class');
        }

        $this->_router = $router;

        return $this;
    }

    /**
     * Return the router object.
     *
     * Instantiates a Zend_Controller_Router object if no router currently set.
     *
     * @return null|Zend_Controller_Router_Interface
     */
    public function getRouter()
    {
        if (null == $this->_router) {
            require_once 'Zend/Controller/Router.php';
            $this->setRouter(new Zend_Controller_Router());
        }

        return $this->_router;
    }

    /**
     * Set the base URL used for requests
     *
     * Use to set the base URL segment of the REQUEST_URI to use when 
     * determining PATH_INFO, etc. Examples:
     * - /admin
     * - /myapp
     * - /subdir/index.php
     *
     * Note that the URL should not include the full URI. Do not use:
     * - http://example.com/admin
     * - http://example.com/myapp
     * - http://example.com/subdir/index.php
     *
     * If a null value is passed, this can be used as well for autodiscovery (default).
     * 
     * @param string $base
     * @return Zend_Controller_Front
     * @throws Zend_Controller_Exception for non-string $base
     */
    public function setBaseUrl($base = null)
    {
        if (!is_string($base) && (null !== $base)) {
            throw new Zend_Controller_Exception('Rewrite base must be a string');
        }

        $this->_baseUrl = $base;

        return $this;
    }

    /**
     * Retrieve the currently set base URL
     * 
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->_baseUrl;
    }

    /**
     * Set the dispatcher object.  The dispatcher is responsible for
     * taking a Zend_Controller_Dispatcher_Token object, instantiating the controller, and
     * call the action method of the controller.
     *
     * @param Zend_Controller_Dispatcher_Interface $dispatcher
     * @return Zend_Controller_Front
     */
    public function setDispatcher(Zend_Controller_Dispatcher_Interface $dispatcher)
    {
        $this->_dispatcher = $dispatcher;
        return $this;
    }

    /**
     * Return the dispatcher object.
     *
     * @return Zend_Controller_DispatcherInteface
     */
    public function getDispatcher()
    {
        /**
         * Instantiate the default dispatcher if one was not set.
         */
        if (!$this->_dispatcher instanceof Zend_Controller_Dispatcher_Interface) {
            require_once 'Zend/Controller/Dispatcher.php';
            $this->_dispatcher = new Zend_Controller_Dispatcher();
        }
        return $this->_dispatcher;
    }

    /**
     * Set response class/object
     *
     * Set the response object.  The response is a container for action
     * responses and headers. Usage is optional.
     *
     * If a class name is provided, instantiates a response object.
     *
     * @param string|Zend_Controller_Response_Abstract $response
     * @throws Zend_Controller_Exception if invalid response class
     * @return Zend_Controller_Front
     */
    public function setResponse($response)
    {
        if (is_string($response)) {
            Zend::loadClass($response);
            $response = new $response();
        }
        if (!$response instanceof Zend_Controller_Response_Abstract) {
            throw new Zend_Controller_Exception('Invalid response class');
        }

        $this->_response = $response;

        return $this;
    }

    /**
     * Return the response object.
     *
     * @return null|Zend_Controller_Response_Abstract
     */
    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * Add or modify a parameter to use when instantiating an action controller
     *
     * @param string $name
     * @param mixed $value
     * @return Zend_Controller_Front
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
     * @return Zend_Controller_Front
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
     * @return Zend_Controller_Front
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
     * Register a plugin.
     *
     * @param Zend_Controller_Plugin_Abstract $plugin
     * @return Zend_Controller_Front
     */
    public function registerPlugin(Zend_Controller_Plugin_Abstract $plugin)
    {
        $this->_plugins->registerPlugin($plugin);
        return $this;
    }

    /**
     * Unregister a plugin.
     *
     * @param Zend_Controller_Plugin_Abstract $plugin
     * @return Zend_Controller_Front
     */
    public function unregisterPlugin(Zend_Controller_Plugin_Abstract $plugin)
    {
        $this->_plugins->unregisterPlugin($plugin);
        return $this;
    }

    /**
     * Set whether exceptions encounted in the dispatch loop should be thrown 
     * or caught and trapped in the response object
     *
     * Default behaviour is to trap them in the response object; call this 
     * method to have them thrown.
     * 
     * @param boolean $flag Defaults to true
     * @return boolean Returns current setting
     */
    public function throwExceptions($flag = null)
    {
        if (true === $flag) {
            $this->_throwExceptions = true;
        } elseif (false === $flag) {
            $this->_throwExceptions = false;
        }

        return $this->_throwExceptions;
    }

    /**
     * Set whether {@link dispatch()} should return the response without first 
     * rendering output. By default, output is rendered and dispatch() returns 
     * nothing.
     * 
     * @param boolean $flag 
     * @return boolean Returns current setting
     */
    public function returnResponse($flag = null)
    {
        if (true === $flag) {
            $this->_returnResponse = true;
        } elseif (false === $flag) {
            $this->_returnResponse = false;
        }

        return $this->_returnResponse;
    }

    /**
     * Dispatch an HTTP request to a controller/action.
     *
     * @param Zend_Controller_Request_Abstract|null $request
     * @param Zend_Controller_Response_Abstract|null $response
     * @return void|Zend_Controller_Response_Abstract Returns response object if returnResponse() is true
     */
    public function dispatch(Zend_Controller_Request_Abstract $request = null, Zend_Controller_Response_Abstract $response = null)
    {
        /**
         * Instantiate default request object (HTTP version) if none provided
         */
        if ((null === $request) && (null === ($request = $this->getRequest()))) {
            require_once 'Zend/Controller/Request/Http.php';
            $request = new Zend_Controller_Request_Http();
            $this->setRequest($request);
        }

        /**
         * Set base URL of request object, if available
         */
        if (is_callable(array($request, 'setBaseUrl'))) {
            if (null !== ($baseUrl = $this->getBaseUrl())) {
                $request->setBaseUrl($baseUrl);
            }
        }

        /**
         * Instantiate default response object (HTTP version) if none provided
         */
        if ((null === $response) && (null === ($response = $this->getResponse()))) {
            require_once 'Zend/Controller/Response/Http.php';
            $response = new Zend_Controller_Response_Http();
            $this->setResponse($response);
        }

        /**
         * Register request and response objects with plugin broker
         */
        $this->_plugins
            ->setRequest($request)
            ->setResponse($response);

        // Begin dispatch
        try {
            /**
             * Route request to controller/action, if a router is provided
             */
            $router = $this->getRouter();

            /**
            * Notify plugins of router startup
            */
            $this->_plugins->routeStartup($request);

            $router->setParams($this->getParams());
            $router->route($request);

            /**
            * Notify plugins of router completion
            */
            $this->_plugins->routeShutdown($request);

            /**
             * Notify plugins of dispatch loop startup
             */
            $this->_plugins->dispatchLoopStartup($request);

            $dispatcher = $this->getDispatcher();
            $dispatcher->setParams($this->getParams());
            foreach ($this->getControllerDirectory() as $directory) {
                $dispatcher->addControllerDirectory($directory);
            }

            /**
             *  Attempt to dispatch the controller/action. If the $request
             *  indicates that it needs to be dispatched, move to the next
             *  action in the request.
             */
            do {
                $request->setDispatched(true);

                /**
                 * Notify plugins of dispatch startup
                 */
                $this->_plugins->preDispatch($request);

                /**
                 * Skip requested action if preDispatch() has reset it
                 */
                if (!$request->isDispatched()) {
                    continue;
                }

                /**
                 * Dispatch request
                 */
                $dispatcher->dispatch($request, $response);

                /**
                 * Notify plugins of dispatch completion
                 */
                $this->_plugins->postDispatch($request);
            } while (!$request->isDispatched());
        } catch (Exception $e) {
            if ($this->throwExceptions()) {
                throw $e;
            }

            $response->setException($e);
        }

        /**
         * Notify plugins of dispatch loop completion
         */
        try {
            $this->_plugins->dispatchLoopShutdown();
        } catch (Exception $e) {
            if ($this->throwExceptions()) {
                throw $e;
            }

            $response->setException($e);
        }

        if ($this->returnResponse()) {
            return $response;
        }

        $response->sendHeaders();
        $response->outputBody();
    }
}
