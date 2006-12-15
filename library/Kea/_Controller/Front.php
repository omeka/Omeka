<?php

/**
 * @license http://www.opensource.org/licenses/gpl-license.php GPL Public License
 * @package Kea
 */

/**
 * Kea; Cheeky fellow.
 * 
 * http://www.doc.govt.nz/Conservation/001~Plants-and-Animals/001~Native-Animals/Kea.asp
 * 
 * The Kea 'framework' attempts to implement a simple
 * model / view / controller architecture.  It does not
 * purport to be the best, or most well rounded framework
 * out there.  In fact, Cake or Zend are probably much
 * better for your needs, since Kea is purposefully brittle,
 * as a framework.
 * 
 * So why Kea?
 * 
 * Kea was created after using Cake and Zend for sometime
 * and realizing that for fast application development,
 * both are great.  For specific in-house development,
 * though,they tend to treat PHP like Java.
 * 
 * There were also some specific needs that could not be
 * easily met through using either Cake or ZF.  That said
 * as Kea evolves, certainly ZF and other framework
 * components will be included when useful.  Why wouldn't we?
 * Communities are doing great work on frameworks these days.
 * 
 * Kea is heavily inspired by the Zend Framework, Cake and
 * Wordpress.  This second iteration of Kea has
 * been mostly 'whiteroomed', except where explicitly
 * noted.  Even still, I will attempt to give credit where
 * credit is due, when some algorithms or files maintain their
 * similarity to those of outside sources. If some has been missed,
 * please do not hesitate to speak up.
 */

/**
 * Kea Application starting point.
 * The Front Controller is the first object called in the request and the
 * object responsible for stitching the basic logic of the system together.
 *
 * PURPOSE:  The Front Controller needs to do get the resolver,
 *	take the resolved output and hand it to the controller,
 *	then create a view object to render an output.
 *	1) request->resolved to c&a or to template
 *	2) router->act(token) = either preforms c&a methods, or
 *	3) view->render renders the correct c&a view or renders the template
 *
 * @package Kea
 * @subpackage Front
 * @author Nate Agrin [nate AT nateagrin.com]
 * @copyright 2006 Center for History and New Media
 * @version 0.2.0 KIWI
 * @edited 10/13/06 n8agrin
 */

require_once 'Kea/Controller/Router.php';
require_once 'Kea/Controller/Dispatcher.php';
require_once 'Kea/Controller/Response.php';
require_once 'Kea/Request.php';
#require_once 'Kea/Plugin/Manager.php';
#require_once 'Kea/Theme/Controller.php';

class Kea_Controller_Front
{
	/**
	 * @var Kea_Front_Controller object
	 */
	private static $_instance;

	/**
	 * @var Kea_Controller_Resolver object
	 */
	private $_dispatcher;
	
	/**
	 * @var Kea_Controller_Router object
	 */
	private $_router;

	/**
	 * @var Kea_Controller_View object
	 */
	private $_view;

	/**
	 * Used for debuging speed issues
	 * @var integer
	 */
	private $__app_timer;


	/**
	 * Singleton pattern
	 */
	private function __construct() {}
	private function __clone() {}

	/**
	 * Returns, or creates and returns, the Kea_Controller_Front object
	 * @return Kea_Controller_Front object
	 */
	public static function getInstance()
	{
		if (!self::$_instance instanceof self) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Initialize the thread the normal way, with no pretences.
	 * @see _init()
	 * @see _dispatch()
	 */
	public static function run()
	{
		return self::getInstance()->_init()->_dispatch();
	}

	/**
	 * Sets all the internal Front Controller properties.
	 * _setRouter & _setResolver are preferred methods of
	 * setting the Router & Resolver insuring they are born
	 * of the appropriate Interfaces.
	 * The debug property establishes a timer for timing the
	 * application run time.
	 * Returns $this for convience of linking methods.
	 * @return Kea_Controller_Front object
	 */
	private function _init()
	{
		try {
#			$this->_plugins		= new Kea_Plugin_Manager;
			$this->_dispatcher	= new Kea_Controller_Dispatcher;
			$this->_router		= new Kea_Controller_Router;
			$this->_response	= Kea_Controller_Response::getInstance();
#			$this->_view		= new Kea_Theme_Controller;
		}
		catch (Kea_Exception $e) {
			echo 'SOMETHING NEEDS TO BE DONE WITH THE FRONT CONTROLLER INI EXCEPTION';
			throw $e;
		}

		if (KEA_DEBUG_TIMER) {
			$this->__app_timer = microtime(true);
		}
		return $this;
	}

	private function _dispatch()
	{
		try{
			// Send the request to the resolver
			// The resolver may end up handling the
			// url rewriting... not sure about this.
			$request = $this->_router->resolve(Kea_Request::getInstance());

			while ($request->hasMoreActions()) {
				$this->_dispatcher->route($request);
			}
		} catch (Kea_Exception $e) {
			$this->_response->setException($e);
		}
		return $this->_response;
	}
}
?>