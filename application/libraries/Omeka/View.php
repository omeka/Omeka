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
	 * Maintains a key => value pairing corresponding to hard path => web path for possible assets for Omeka views
	 *
	 * @var array
	 **/
	protected $_asset_paths = array();
	
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
		
		Zend_Registry::set('view', $this);				
	}
	
	/**
	 * Simple factory method for returning an instance of a View Format obj
	 * 
	 * Check based on some common string manipulation
	 *
	 * @return void
	 **/
	public function getFormat($format, $options)
	{
		//This will give us a class like Omeka_View_Format_Xml from 'xml'
		$class = "Omeka_View_Format_" . ucwords(strtolower($format));
		
		try {
			Zend_Loader::loadClass($class);
			$format_class = new $class($this, $options);

			if($format_class->canRender()) {
				return $format_class;
			}
		} 
		//Silence exceptions for missing classes
		catch (Zend_Exception $e) {}		
		
		//Return the plugin handler as the default, which can handle whatever nonsense you throw towards it
		$options['format'] = $format;
		return new Omeka_View_Format_Plugin($this, $options);		
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
	
	public function getAssetPaths()
	{
		return $this->_asset_paths;
	}
	
	public function addAssetPath($physical_path, $web_path)
	{
		$this->_asset_paths[$physical_path] = $web_path;
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
	 * Get a View_Format object and then render it
	 *
	 * Filename of script to render (if applicable) gets passed as part of $options
	 * 
	 * @return void
	 **/
	public function renderFormat($format, $file, $options=array())
	{
		$options['feed_filename'] = $file;
		
		$format_obj = $this->getFormat($format, $options);
		
		if(!$format_obj) {
			throw new Exception( "Format named '$format' does not exist!" );
		}
		return $format_obj->render();
	}
}
?>