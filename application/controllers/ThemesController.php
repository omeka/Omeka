<?php
/**
 * @package Omeka
 **/
require_once 'Kea/Controller/Action.php';
class ThemesController extends Kea_Controller_Action
{
	/**
	 * This is only temporary until the system is better
	 * built out.  We should not be relying on this
	 * exclusively to populate our theme options.
	 * 
	 */
	public function init()
	{
		require_once MODEL_DIR.DIRECTORY_SEPARATOR.'Option.php';
		$doctrine = Zend::registry('doctrine');
		$options = $doctrine->getTable('option');
		$themes = $options->findByDql('name LIKE ? or name LIKE ?', array('admin_theme', 'theme'));
		
		if (!count($themes) == 2) {
			$admin = new Option();
			$admin->name = 'admin_theme';
			$admin->value = 'default';
			
			$theme = new Option();
			$theme->name = 'theme';
			$theme->value = 'default';
			
			$admin->save();
			$theme->save();
			return;
		}
		else {
			return;
		}
	}
	
    public function indexAction()
    {
		$this->browseAction();
    }

	public function rerouteAction()
	{
		$this->_redirect('themes/browse');
	}

	public function browseAction()
	{
		/**
		 * Create an array of themes, with the directory paths
		 * theme.ini files and images paths if they are present
		 */
		$themes = array();
		
		// Iterate over the directory to get the file structure
		$themes_dir = new DirectoryIterator(THEME_DIR);
		foreach($themes_dir as $dir) {
			$fname = $dir->getFilename();
			if (!$dir->isDot() and $fname[0] != '.' and $dir->isReadable()) {
				
				// Define a hard theme path for the theme
				$theme['dir'] = THEME_DIR.DIRECTORY_SEPARATOR.$dir;
				
				// Test to see if an image is available to present the user
				// when switching themes
				$image_file = $theme['dir'].DIRECTORY_SEPARATOR.$fname.'.jpg';
				if (file_exists($image_file) && is_readable($image_file)) {
					$img = WEB_THEME.DIRECTORY_SEPARATOR.$fname.DIRECTORY_SEPARATOR.$fname.'.jpg';
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

		
		// Create a view class
		require_once 'Kea/View.php';
		$view = new Kea_View($this, array('request', $this->getRequest()));

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