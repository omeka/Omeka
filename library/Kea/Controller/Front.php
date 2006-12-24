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
 * better for your needs, since Kea is purposefully brittle.
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
 * Those communities are doing great work.
 * 
 * Kea is heavily inspired by the Zend Framework, Cake and
 * Wordpress.  This second iteration of Kea has
 * been mostly 'whiteroomed', except where explicitly
 * noted.  Even still, credit will be given where
 * credit is due, when some algorithms or files maintain their
 * similarity to those of outside sources. If something deserves
 * credit and was missed, do not hesitate to speak up, and let
 * us know.
 */

require_once 'Kea/Controller/Router.php';
require_once 'Kea/Controller/Dispatcher.php';
require_once 'Kea/Request.php';

/**
 * Kea Application starting point.
 * The Front Controller is the first object called in the request and the
 * object responsible for stitching the basic logic of the system together.
 *
 * @package Kea
 * @subpackage Front
 * @author Nate Agrin [n8 AT n8agrin.com]
 * @copyright 2006 Center for History and New Media
 * @version 0.2.0 KIWI
 * @edited 12/17/06 n8
 */
class Kea_Controller_Front
{
	/**
	 * Stores the Front Controller Instance
	 * 
	 * @var Kea_Front_Controller object
	 */
	private static $_instance;

	/**
	 * The dispatcher takes the routed request finds the
	 * appropriate controller, instantiates it, and instructs
	 * it to preform the requested action in a safe way.
	 * 
	 * @var Kea_Controller_Dispatch object
	 */
	private $_dispatcher;
	
	/**
	 * The router inspects the incoming request and attempts
	 * to discern the appropriate controller and action which
	 * match the request.
	 * 
	 * @var Kea_Controller_Router object
	 */
	private $_router;

	/**
	 * The response handles the actual output from the incoming
	 * request.  This must be set for each new type of data being
	 * returned.
	 * 
	 * By default, the three data types are included; HTML, Json
	 * and REST / XML
	 * 
	 * @var Kea_Controller_Response_Abstract object
	 */
	private $_response;

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
	 * Safely sets the Kea_Controller_Response object
	 */
	public static function setResponse(Kea_Controller_Response_Abstract $response)
	{
		self::getInstance()->_response = $response;
	}

	/**
	 * Bootstrap the application
	 * @see _init()
	 * @see _dispatch()
	 */
	public static function run()
	{
		return self::getInstance()->_init()->_dispatch();
	}

	/**
	 * Sets the internal Front Controller properties.
	 * Defaults the response object to an HTML theme based
	 * request, if one is not previously sent.
	 * Returns $this for convience of linking methods.
	 * @return Kea_Controller_Front object
	 */
	private function _init()
	{
		try {
			$this->_dispatcher	= new Kea_Controller_Dispatcher;
			$this->_router		= new Kea_Controller_Router;

			// Set the response object if not already set
			if (!$this->_response instanceof Kea_Controller_Response_Abstract) {
				if ($return = Kea_Request::getInstance()->get("return")) {
					switch ($return) {
						case "json":
							require_once 'Kea/Controller/Response/Json.php';
							$this->setResponse(new Kea_Controller_Response_Json);
						break;
						case "rest":
							require_once 'Kea/Controller/Response/Rest.php';
							$this->setResponse(new Kea_Controller_Response_Rest );
						break;
					}
				}
				else {
					require_once 'Kea/Controller/Response/Theme.php';
					$this->setResponse(new Kea_Controller_Response_Theme);
				}
			}
		}
		catch (Kea_Exception $e) {
			echo 'SOMETHING NEEDS TO BE DONE WITH THE FRONT CONTROLLER INI EXCEPTION';
			throw $e;
		}
		return $this;
	}

	/**
	 * After initializing the appropriate components, the
	 * dispatch loop is established and the appropriate data is
	 * appended to the response object.
	 * @return Kea_Controller_Response_Abstract object
	 */
	private function _dispatch()
	{
		try{
			// Send the request to the resolver
			// The resolver may end up handling the
			// url rewriting... not sure about this.
			$request = $this->_router->resolve(Kea_Request::getInstance());

			while ($request->hasMoreActions()) {
				$this->_dispatcher->route($request, $this->_response);
			}

		} catch (Kea_Exception $e) {
			$this->_response->setException($e);
		}
		return $this->_response;
	}
}
?>