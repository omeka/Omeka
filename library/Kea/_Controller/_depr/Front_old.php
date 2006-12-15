<?php
/**
 * @license http://www.opensource.org/licenses/gpl-license.php GPL Public License
 * @package Kea
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

require_once 'Kea/Router.php';
require_once 'Kea/Resolver.php';
require_once 'Kea/Request.php';
require_once 'Kea/Theme/Controller.php';

class Kea_Front_Controller
{
	/**
	 * @var Kea_Front_Controller object
	 */
	private static $_instance;
	
	/**
	 * @var Kea_Controller_Resolver object
	 */
	private $_resolver;
	
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
	public static function run($controller_path, $theme_path)
	{
		self::getInstance()->_init($controller_path, $theme_path)
						   ->_dispatch();
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
	private function _init($controller_path, $theme_path)
	{
		try {
			//$this->_plugins = new Kea_Plugin_Manager();
			$this->_resolver = new Kea_Resolver;
			$this->_router = new Kea_Router($controller_path);
			$this->_view = new Kea_Theme_Controller($theme_path);
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
		$token = $this->_resolver->resolve(Kea_Request::getInstance());
		$this->_view->registerRequest($token);
		
		while ($token instanceof Kea_Token) {
			$token = $this->_router->route($token);
		}
		
		$this->_view->render();
		
		if (KEA_DEBUG_TIMER) {
			echo microtime(true) - $this->__app_timer;
		}
	}
}

?>