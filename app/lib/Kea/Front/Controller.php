<?php
/**
 * @license http://www.opensource.org/licenses/gpl-license.php GPL Public License
 * @package Kea
 */

/**
 * Kea Application starting point.
 * The Front Controller is the first object called in the thread and the
 * object responsible for stitching the basic logic of the system together.
 * It marshals the appropriate objects for doing data logic and then sending
 * those objects to a view controller which handles rendering the output (XHTML, XML, etc).
 *
 * @package Kea
 * @subpackage Controller
 * @author Nate Agrin <nate@exposured.com>
 * @copyright 2006 Center for History and New Media
 * @version 0.1.0
 * @edited 5/8/06
 * @status FROZEN
 */
class Kea_Front_Controller extends Kea_Controller_Base
{
	/**
	 * @var Kea_Controller_Front object
	 */
	private static $_instance;

	/**
	 * @var Kea_Controller_Router_Interface object
	 */
	private $_router;
	
	/**
	 * @var Kea_Controller_View_Interface object
	 */
	private $_view_controller;
	
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
		if( !self::$_instance instanceof self ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	
	/**
	 * Initializes the thread.
	 * init() sets up the internal Kea_Controller_Front objects
	 * handleRequest() stiches together the layers of data with their views
	 * @see _init()
	 * @see _dispatch()
	 */
	public static function run()
	{
		self::getInstance()->_init()
						   ->_dispatch();
	}

	
	/**
	 * Sets all the internal Front Controller properties.
	 * _setRouter & _setResolver are preferred methods of setting the Router & Resolver
	 * insuring they are born of the appropriate Interfaces
	 * The debug property establishes a timer for timing the application run time.
	 * Returns $this for convience of linking methods.
	 * @return Kea_Controller_Front object
	 */
	private function _init()
	{
		// These are both set in the parent abstract controller base class
		self::$_request = new Kea_Request;
		
		self::$_session = new Kea_Session;

		// Not yet implemented
		//$this->_plugins = new Kea_Plugin_Manager();
		
		// Typesafe these so they can be swapped out later if needed
		$this->_setRouter( new Kea_Template_Router )
			 ->_setViewController( new Kea_Template_Controller );
		
		if( KEA_DEBUG_TIMER ) {
			$this->__app_timer = microtime(true);
		}
		
		return $this;
	}


	/**
	 * Returns the router, and insures it implements the basic Router Interface
	 * @return Kea_Controller_Router object
	 */
	private function _getRouter()
	{
		return $this->_router;
	}

	
	/**
	 * Sets the router, typhinted to Kea_Controller_Router_Interface
	 * Returns $this for convenience
	 * @return Kea_Controller_Front object
	 */
	private function _setRouter( Kea_Router_Interface $router )
	{
		$this->_router = $router;
		return $this;
	}
	
	public static function setRouter( Kea_Router_Interface $router )
	{
		self::getInstance()->_setRouter( $router );
	}
	
	private function _getViewController()
	{
		return $this->_view_controller;
	}
	
	private function _setViewController( Kea_View_Interface $vc )
	{
		$this->_view_controller = $vc;
		return $this;
	}
	
	public static function setViewController( Kea_View_Interface $vc )
	{
		self::getInstance()->_setViewController( $vc );
	}

	private function _dispatch()
	{
		$uri = str_replace( WEB_ROOT, '', self::$_request->getURI() );
		
		self::$_route = $this->_getRouter()->getRoute( $uri );

		$this->_getViewController()->createView( self::$_route );
				
		if( KEA_DEBUG_TIMER )
		{
			echo ( microtime(true) - $this->__app_timer );
		}
	}
}

?>