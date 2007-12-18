<?php 
/**
* This class manipulates the View object a bit too much.  May be an unnecessary separation.
* Also none of the other data feeds manipulate the script paths quite like this one.
*/
class Omeka_View_Format_Xhtml extends Omeka_View_Format_Abstract
{
	protected function setThemePath()
	{
		$broker = get_plugin_broker();
				
		$view = $this->view;
		
		// do we select the admin theme or the public theme?
		if ((boolean) $view->getRequest()->getParam('admin')) {
			$theme_name = get_option('admin_theme');
			
			//Add script paths for plugins
			$broker->loadThemeDirs($view, 'admin');
		}
		else {
			$theme_name = get_option('public_theme');
			
			$broker->loadThemeDirs($view, 'public');
		}
		
		$scriptPath = THEME_DIR.DIRECTORY_SEPARATOR.$theme_name;
		$view->addScriptPath($scriptPath);
		
		$view->addAssetPath($scriptPath, WEB_THEME.DIRECTORY_SEPARATOR.$theme_name);
		
		//Always included the shared paths
		$view->addAssetPath(SHARED_DIR, WEB_SHARED);
	}
	
	/**
	 * The XHTML view can always render, even if its a 404, 500, 403, whatever
	 *
	 * @return void
	 **/
	public function canRender()
	{
		return true;
	}
	
	/**
	 * Since we are rendering XHTML, just set the theme path for 
	 * rendering files then render normally with the View
	 *
	 * @return void
	 **/
	protected function _render()
	{
		$this->setThemePath();
		
		//Include the theme helpers
		require_once HELPERS;
		
		$file = $this->getFile();
				
		if(empty($file)) {
			throw new Exception( 'File name must be provided when rendering XHTML templates!' );
		}
		
		return $this->view->render($file);
	}
}
 
?>
