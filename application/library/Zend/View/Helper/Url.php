<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to version 1.0 of the Zend Framework
 * license, that is bundled with this package in the file LICENSE, and
 * is available through the world-wide-web at the following URL:
 * http://www.zend.com/license/framework/1_0.txt. If you did not receive
 * a copy of the Zend Framework license and are unable to obtain it
 * through the world-wide-web, please send a note to license@zend.com
 * so we can mail you a copy immediately.
 *
 * @package    Zend_View
 * @subpackage Helpers
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id: Url.php 2800 2007-01-16 01:36:23Z bkarwin $
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */

/**
 * Helper for making easy links and getting urls that depend on the routes and router
 * 
 * @package    Zend_View
 * @subpackage Helpers
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */
class Zend_View_Helper_Url {
    
    /**
     * Generates an url given the name of a route.
     * 
     * @access public
     * 
     * @param array $urlOptions Options passed to the assemble method of the Route object.
     * @param mixed $name The name of a Route to use. If null it will use the current Route
     * 
     * @return string Url for the link href attribute.
     */
    public function url($urlOptions = array(), $name = null)
    {
        
        $ctrl = Zend_Controller_Front::getInstance();
        $router = $ctrl->getRouter();
        
        if (empty($name)) {
            $route = $router->getCurrentRoute();
        } else {
            $route = $router->getRoute($name);
        }
        
        $request = $ctrl->getRequest();
        
        $url = rtrim($request->getBaseUrl(), '/') . '/';
        $url .= $route->assemble($urlOptions);
         
        return $url;
        
    }
    
}
