<?php
/**
 * Customized view class
 *
 * @package Omeka
 **/
require_once 'Zend/View/Abstract.php';
class Omeka_View extends Zend_View_Abstract
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
				
		/**
		 * Set the theme path:
		 * This needs to happen last because the first thing Zend_View_Abstract
		 * does in its __construct() is set $this->setScriptPath(null).
		 */ 
		$this->setThemePath();
		
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
		$broker = Zend_Registry::get( 'plugin_broker' );
		
		if ($output = $this->getRequest()->getParam('output')) {

			switch($output) {
				case('json'):
					require_once 'Zend/Json.php';
					$scriptPath = APP_DIR.DIRECTORY_SEPARATOR.'output'.DIRECTORY_SEPARATOR.'json';
					$this->addScriptPath($scriptPath);
					$broker->loadOutputDirs($this, 'json');
				break;
				case('rest'):
					$this->getResponse()->setHeader('Content-Type', 'text/xml');
					$scriptPath = APP_DIR.DIRECTORY_SEPARATOR.'output'.DIRECTORY_SEPARATOR.'rest';
					$this->addScriptPath($scriptPath);
					$broker->loadOutputDirs($this, 'rest');
				break;
			}
			
//			var_dump( $this->getScriptPaths() );exit;
		}
		else {
			// Get the options table
			require_once MODEL_DIR.DIRECTORY_SEPARATOR.'Option.php';
			$options = Zend_Registry::get('options');
			
			// do we select the admin theme or the public theme?
			if ((boolean) $this->getRequest()->getParam('admin')) {
				$theme_name = $options['admin_theme'];
				
				//Add script paths for plugins
				$broker->loadThemeDirs($this, 'admin');
			}
			else {
				$theme_name = $options['public_theme'];
				
				$broker->loadThemeDirs($this, 'public');
			}
			
			$scriptPath = THEME_DIR.DIRECTORY_SEPARATOR.$theme_name;
			$this->addScriptPath($scriptPath);
			
	//		var_dump( $this->getScriptPaths() );exit;
			
			Zend_Registry::set('theme_web',		WEB_THEME.DIRECTORY_SEPARATOR.$theme_name);
		}
		Zend_Registry::set('theme_path',	$scriptPath);
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
		$vars = $this->getVars();
	
		try {
			extract($vars);	
			include func_get_arg(0);
		
					

			//Prototype.js doesn't recognize JSON unless the header is X-JSON: {json} all on one line [KK]
			if($this->getRequest()->getParam('output') == 'json') {
				$config = Zend_Registry::get('config_ini');
				if (!(boolean) $config->debug->json) {
					$json = ob_get_clean();
					header("X-JSON: $json");
				}
			}
		
		} catch (Exception $e) {
			
			/* Exceptions should not be uncaught at this stage of execution
				This is b/c the only PHP executed beyond this point are theme functions */
			echo 'Error:' . $e->getMessage();
			
			$config = Zend_Registry::get( 'config_ini' );
			//Display a lot of info if exceptions are turned on
			if($config->debug->exceptions) {	
				echo nl2br( $e->getTraceAsString() );
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
		require_once HELPERS;
		
		return parent::render($file);
	}

}
?>