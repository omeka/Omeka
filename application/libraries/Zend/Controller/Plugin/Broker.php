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
 * @subpackage Plugins
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/** Zend_Controller_Exception */
require_once 'Zend/Controller/Exception.php';

/** Zend_Controller_Plugin_Abstract */
require_once 'Zend/Controller/Plugin/Abstract.php';

/** Zend_Controller_Request_Abstract */
require_once 'Zend/Controller/Request/Abstract.php';

/** Zend_Controller_Response_Abstract */
require_once 'Zend/Controller/Response/Abstract.php';

/**
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage Plugins
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Controller_Plugin_Broker extends Zend_Controller_Plugin_Abstract
{

    /**
     * Array of instance of objects extending Zend_Controller_Plugin_Abstract
     *
     * @var array
     */
    protected $_plugins = array();


    /**
     * Register a plugin.
     *
     * @param Zend_Controller_Plugin_Abstract $plugin
     * @return Zend_Controller_Plugin_Broker
     */
    public function registerPlugin(Zend_Controller_Plugin_Abstract $plugin)
    {
        if (false !== array_search($plugin, $this->_plugins, true)) {
            throw new Zend_Controller_Exception('Plugin already registered.');
        }
        $this->_plugins[] = $plugin;
        return $this;
    }

    /**
     * Set request object, and register with each plugin
     * 
     * @param Zend_Controller_Request_Abstract $request 
     * @return Zend_Controller_Plugin_Broker
     */
    public function setRequest(Zend_Controller_Request_Abstract $request) 
    {
        $this->_request = $request;

        foreach ($this->_plugins as $plugin) {
            $plugin->setRequest($request);
        }

        return $this;
    }

    /**
     * Get request object
     * 
     * @return Zend_Controller_Request_Abstract $request 
     */
    public function getRequest() 
    {
        return $this->_request;
    }

    /**
     * Set response object
     * 
     * @param Zend_Controller_Response_Abstract $response 
     * @return Zend_Controller_Plugin_Broker
     */
    public function setResponse(Zend_Controller_Response_Abstract $response) 
    {
        $this->_response = $response;

        foreach ($this->_plugins as $plugin) {
            $plugin->setResponse($response);
        }


        return $this;
    }

    /**
     * Get response object
     * 
     * @return Zend_Controller_Response_Abstract $response 
     */
    public function getResponse() 
    {
        return $this->_response;
    }



    /**
     * Unregister a plugin.
     *
     * @param Zend_Controller_Plugin_Abstract $plugin
     * @return Zend_Controller_Plugin_Broker
     */
    public function unregisterPlugin(Zend_Controller_Plugin_Abstract $plugin)
    {
        $key = array_search($plugin, $this->_plugins, true);
        if (false === $key) {
            throw new Zend_Controller_Exception('Plugin never registered.');
        }
        unset($this->_plugins[$key]);
        return $this;
    }


    /**
     * Called before Zend_Controller_Front begins evaluating the
     * request against its routes.
     *
     * @param Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function routeStartup(Zend_Controller_Request_Abstract $request)
    {
        foreach ($this->_plugins as $plugin) {
            $plugin->routeStartup($request);
        }
    }


    /**
     * Called before Zend_Controller_Front exits its iterations over
     * the route set.
     *
     * @param  Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function routeShutdown(Zend_Controller_Request_Abstract $request)
    {
        foreach ($this->_plugins as $plugin) {
            $plugin->routeShutdown($request);
        }
    }


    /**
     * Called before Zend_Controller_Front enters its dispatch loop.
     *
     * During the dispatch loop, Zend_Controller_Front keeps a
     * Zend_Controller_Request_Abstract object, and uses
     * Zend_Controller_Dispatcher to dispatch the
     * Zend_Controller_Request_Abstract object to controllers/actions.
     *
     * @param  Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        foreach ($this->_plugins as $plugin) {
            $plugin->dispatchLoopStartup($request);
        }
    }


    /**
     * Called before an action is dispatched by Zend_Controller_Dispatcher.
     *
     * @param  Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        foreach ($this->_plugins as $plugin) {
            $plugin->preDispatch($request);
        }
    }


    /**
     * Called after an action is dispatched by Zend_Controller_Dispatcher.
     *
     * @param  Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function postDispatch(Zend_Controller_Request_Abstract $request)
    {
        foreach ($this->_plugins as $plugin) {
            $plugin->postDispatch($request);
        }
    }


    /**
     * Called before Zend_Controller_Front exits its dispatch loop.
     *
     * @param  Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function dispatchLoopShutdown()
    {
       foreach ($this->_plugins as $plugin) {
           $plugin->dispatchLoopShutdown();
       }
    }
}
