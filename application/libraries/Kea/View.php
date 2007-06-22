<?php
/**
 * Customized view class
 *
 * @package Omeka
 **/
require_once 'Zend/View/Abstract.php';
class Kea_View extends Zend_View_Abstract
{	
	/**
	 * Placeholder for Zend Request object
	 * @var _request Zend_Controller_Request object
	 */
	protected $_controller;
	
	protected $_request;
	
	/**
	 * Using the current admin system, an option
	 * is set by the admin controller upon authentication
	 * that can then be used to verify that an admin request
	 * has been made through GET or via routes.
	 * 
	 * This is the only reason why we need to let the view
	 * know about the request object, so that it can correctly
	 * grab the admin template or the publicly available template.
	 * 
	 * 
	 * @edited 2007-02-09
	 */
	public function __construct($controller, $config = array())
	{
		parent::__construct($config);
		
		$this->_controller = $controller;
		
		if(isset($config['request'])) {
			$this->_request = $config['request'];
		}
		
		$this->setOutputListener();
		
		/**
		 * Set the theme path:
		 * This needs to happen last because the first thing Zend_View_Abstract
		 * does in its __construct() is set $this->setScriptPath(null).
		 */ 
		$this->setThemePath();
	}
	
	public function setOutputListener()
	{
		$doctrine = Zend::registry('doctrine');
		$listeners = $doctrine->getListener();
		
		/* 	Here's a quick hack for ya:  the OutputListener needs to know what the output type is
	     *	So it can escape the data properly.  REST & JSON must be fully converted to htmlentities
		 *	but XHTML will only be partially converted based on what tags are specified as converted
		 **/		
		
		if(!$listeners->hasListener('Kea_OutputListener') ) {
			if ($output = $this->getRequest()->getParam('output')) {
				$listeners->add(new Kea_OutputListener(null));			
			}else {
				$listeners->add(new Kea_OutputListener('em|b|strong|del|span|cite|blockquote'));	
			}		
		}	
		$doctrine->setListener($listeners);
	}
	
	/**
	 * Functions that see through to the controller
	 * Simple stuff
	 * 
	 */
	public function getRequest()
	{
		if(!empty($this->_request)) {
			return $this->_request;
		}
		
		return $this->_controller->getRequest();
	}
	
	public function getResponse()
	{
		return $this->_controller->getResponse();
	}
	
	/**
	 * Construct the theme path from the options in the database
	 * dependant on whether or not there is an admin interface request
	 * 
	 * 
	 * @edited 2007-02-09
	 */
	public function setThemePath($path = null)
	{	
		if ($output = $this->getRequest()->getParam('output')) {

			switch($output) {
				case('json'):
					require_once 'Zend/Json.php';
					$this->addScriptPath(APP_DIR.DIRECTORY_SEPARATOR.'output'.DIRECTORY_SEPARATOR.'json');
					Kea_Controller_Plugin_Broker::getInstance()->addScriptPath($this, 'json');
				break;
				case('rest'):
					$this->getResponse()->setHeader('Content-Type', 'text/xml');
					$this->addScriptPath(APP_DIR.DIRECTORY_SEPARATOR.'output'.DIRECTORY_SEPARATOR.'rest');
					Kea_Controller_Plugin_Broker::getInstance()->addScriptPath($this, 'rest');
				break;
			}
		}
		else {
			// Get the options table
			require_once MODEL_DIR.DIRECTORY_SEPARATOR.'Option.php';
			$options = Zend::registry('options');
			
			// do we select the admin theme or the public theme?
			if ((boolean) $this->getRequest()->getParam('admin')) {
				$theme_name = $options['admin_theme'];
			}
			else {
				$theme_name = $options['public_theme'];
			}
			
			$this->addScriptPath(THEME_DIR.DIRECTORY_SEPARATOR.$theme_name);
			
			Kea_Controller_Plugin_Broker::getInstance()->addScriptPath($this);

			Zend::register('theme_path',	THEME_DIR.DIRECTORY_SEPARATOR.$theme_name);
			Zend::register('theme_web',		WEB_THEME.DIRECTORY_SEPARATOR.$theme_name);
		}
	}
	
	/**
	 * This allows for variables set to the view object
	 * to be referenced in the view script by their actual name.
	 * 
	 * For example, in a controller you might do something like:
	 * $view->assign('themes', $themes);
	 * Normally in the view you would then reference $themes through:
	 * $this->themes;
	 * 
	 * 
	 * Now you can reference it simply by using:
	 * $themes;
	 * 
	 * 
	 */
	public function _run() {
		extract($this->getVars());
		
		include func_get_arg(0);
		
		//Prototype.js doesn't recognize JSON unless the header is X-JSON: {json} all on one line [KK]
		if($this->getRequest()->getParam('output') == 'json') {
			$config = Zend::registry('config_ini');
			if (!(boolean) $config->debug->json) {
				$json = ob_get_clean();
				header("X-JSON: $json");
			}
		}
	}

	/**
	 * Render the requested file using the selected theme
	 * 
	 * 
	 * @edited 2007-02-22
	 */
	public function render($file)
	{
		require_once 'Kea/View/Functions.php';
		
		try {
			// do the normal rendering
			$result = parent::render($file);
		} catch (Exception $e) {
			
			/* Exceptions should not be uncaught at this stage of execution
				This is b/c the only PHP executed beyond this point are theme functions */
			echo 'Error:' . $e->getMessage();
			
			$config = Zend::Registry( 'config_ini' );
			//Display a lot of info if exceptions are turned on
			if($config->debug->exceptions) {	
				echo nl2br( $e->getTraceAsString() );
			}
		}
		return $result;
	}

}
?>