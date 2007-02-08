<?php
/**
 * @package Omeka
 * @author Nate Agrin
 **/
require_once 'Zend/Controller/Action.php';
class ThemesController extends Zend_Controller_Action
{
    public function indexAction()
    {
		$response = $this->getResponse();
    }

	public function browseAction()
	{
		/**
		 * Create an array of themes, with the directory paths
		 * theme.ini files and images paths if they are present
		 */
		$themes = array();
		
		// Iterate over the directory to get the file structure
		$themes_dir = new DirectoryIterator(ADMIN_DIR);
		foreach($themes_dir as $dir) {
			$fname = $dir->getFilename();
			if (!$dir->isDot() and $fname[0] != '.' and $dir->isReadable()) {
				
				// Define a hard theme path for the theme
				$theme['dir'] = ADMIN_DIR.DIRECTORY_SEPARATOR.$dir;
				
				// Test to see if an image is available to present the user
				// when switching themes
				$image_file = $theme['dir'].DIRECTORY_SEPARATOR.$fname.'.jpg';
				if (file_exists($image_file) && is_readable($image_file)) {
					$img = ADMIN_WEB.DIRECTORY_SEPARATOR.$fname.DIRECTORY_SEPARATOR.$fname.'.jpg';
					$theme['image'] = $img;
				}
				
				// Finally get the theme's config file
				$theme_ini = $theme['dir'].DIRECTORY_SEPARATOR.'theme.ini';
				if (file_exists($theme_ini) && is_readable($theme_ini)) {
					$theme['ini'] = new Zend_Config_Ini($theme_ini, 'theme');
				}
				else {
					// Display some sort of warning that the theme doesn't have an ini file
				}
				
				// Finally set the array to the global array
				$themes[$fname] = $theme;
			}
		}
		// Get the options table
		require_once MODEL_DIR.DIRECTORY_SEPARATOR.'Option.php';
		$doctrine = Zend::registry('doctrine');
		$options = $doctrine->getTable('option');
		$admin_theme = $options->findByDql("name LIKE 'admin_theme'");
		
		// Create a view class
		Zend::loadClass('Zend_View');
		$view = new Zend_View;
		$view->setScriptPath(ADMIN_DIR.DIRECTORY_SEPARATOR.$admin_theme[0]->value);
		
		// Send the global $themes array to the view
		$view->assign('themes', $themes);
		$this->getResponse()->appendBody($view->render('themes'.DIRECTORY_SEPARATOR.'browse.php'));
	}

    public function noRouteAction()
    {
        $this->_redirect('/');
    }
}
?>