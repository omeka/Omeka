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
	protected $_request;
	
	/**
	 * Using the current admin system, an option
	 * is set by the admin controller upon authentication
	 * that can then be used to verify that an admin request
	 * has been made through GET or via routes.
	 * 
	 * This is the only reason why we need to let the view
	 * know about the requestion object, so that it can correctly
	 * grab the admin template or the publicly available template.
	 * 
	 * @author Nate Agrin
	 * @edited 2007-02-09
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->setRequest(Zend::registry('request'));
		
		// set the theme path
		$this->setThemePath();
	}
	
	/**
	 * Set the request object
	 * 
	 * @author Nate Agrin
	 * @edited 2007-02-09
	 */
	public function setRequest(Zend_Controller_Request_Abstract $request)
	{
		$this->_request = $request;
	}
	
	/**
	 * Get the request object
	 * 
	 * @author Nate Agrin
	 * @edited 2007-02-09
	 */
	public function getRequest()
	{
		return $this->_request;
	}
	
	/**
	 * Construct the theme path from the options in the database
	 * dependant on whether or not there is an admin interface request
	 * 
	 * @author Nate Agrin
	 * @edited 2007-02-09
	 */
	public function setThemePath($path = null)
	{
		// Get the options table
		require_once MODEL_DIR.DIRECTORY_SEPARATOR.'Option.php';
		$doctrine = Zend::registry('doctrine');
		$options = $doctrine->getTable('option');
		
		// do we select the admin theme or the public theme?
		if ((boolean) $this->getRequest()->getParam('admin')) {
			$theme = $options->findByDql("name LIKE 'admin_theme'");
			$this->setScriptPath(ADMIN_THEME_DIR.DIRECTORY_SEPARATOR.$theme[0]->value);
			Zend::Register('theme_path', ADMIN_THEME_DIR.DIRECTORY_SEPARATOR.$theme[0]->value);
			Zend::Register('theme_web', WEB_ADMIN.DIRECTORY_SEPARATOR.$theme[0]->value);
		}
		else {
			$theme = $options->findByDql("name LIKE 'theme'");
			$this->setScriptPath(THEME_DIR.DIRECTORY_SEPARATOR.$theme[0]->value);
			Zend::Register('theme_path', THEME_DIR.DIRECTORY_SEPARATOR.$theme[0]->value);
			Zend::Register('theme_web', WEB_THEME.DIRECTORY_SEPARATOR.$theme[0]->value);
		}

		Zend::Register('theme_dir', $theme[0]->value);
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
	 * @author Kris Kelly
	 */
	public function _run() {
		extract($this->getVars());
		include func_get_arg(0);
	}
	/**
	 * Render the requested file using the selected theme
	 * 
	 * @author Nate Agrin
	 * @edited 2007-02-09
	 */
	public function render($file)
	{
		require_once 'Kea/View/Functions.php';
		
		// do the normal rendering
		return parent::render($file);
	}

} // END class Kea_View
?>