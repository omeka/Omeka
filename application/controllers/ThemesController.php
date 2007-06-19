<?php
/**
 * @package Omeka
 **/
require_once 'Kea/Controller/Action.php';
require_once MODEL_DIR.DIRECTORY_SEPARATOR.'Theme.php';
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

	public function rerouteAction()
	{
		$this->_redirect('themes/browse');
	}
	
	/**
	 * Simple recursive function that scrapes the theme info for either a single theme or all of them, given a directory
	 * @todo make it switch between public and admin
	 *
	 * @return void
	 **/
	protected function getAvailable($dir=null) 
	{
		/**
		 * Create an array of themes, with the directory paths
		 * theme.ini files and images paths if they are present
		 */
		$themes = array();
		if(!$dir) {
			// Iterate over the directory to get the file structure
			$themes_dir = new DirectoryIterator(PUBLIC_THEME_DIR);
			foreach($themes_dir as $dir) {
				$fname = $dir->getFilename();
				if (!$dir->isDot() and $fname[0] != '.' and $dir->isReadable() and $dir->isDir()) {
					$theme = $this->getAvailable($fname);
				
					// Finally set the array to the global array
					$themes[$fname] = $theme;
				}
			}

		} else {
			// Find that theme and return its info
			
			$theme = new Theme();
			// Define a hard theme path for the theme
			$theme->path = PUBLIC_THEME_DIR.DIRECTORY_SEPARATOR.$dir;
			$theme->directory = $dir;
			// Test to see if an image is available to present the user
			// when switching themes
			$image_file = $theme->path.DIRECTORY_SEPARATOR.'theme.jpg';
			if (file_exists($image_file) && is_readable($image_file)) {
				$img = WEB_PUBLIC_THEME.DIRECTORY_SEPARATOR.$dir.DIRECTORY_SEPARATOR.'theme.jpg';
				$theme->image = $img;
			}
			
			// Finally get the theme's config file
			$theme_ini = $theme->path.DIRECTORY_SEPARATOR.'theme.ini';
			if (file_exists($theme_ini) && is_readable($theme_ini)) {
				$ini = new Zend_Config_Ini($theme_ini, 'theme');
				foreach ($ini as $key => $value) {
					$theme->$key = $value;
				}
			}
			else {
				// Display some sort of warning that the theme doesn't have an ini file
			}
			return $theme;
		}
		return $themes;
	}
	
	public function browseAction()
	{		
		$themes = $this->getAvailable();
		
		$public = $this->getTable('Option')->findByName('public_theme');
		
		if(!empty($_POST) and $this->isAllowed('switch')) {
			$public->value = $_POST['public_theme'];
			$public->save();
		}

		$current = $this->getAvailable($public->value);

		return $this->render('themes/browse.php', compact('current', 'themes'));
	}

    public function noRouteAction()
    {
        $this->_redirect('/');
    }
}
?>