<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * Dummy model to simulate the other ActiveRecord models
 *
 * @package Omeka
 * @subpackage Models
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/ 
class Theme 
{
    public $path;
    public $directory;
    public $image;
    public $author;
    public $title;
    public $description;
    public $license;
    public $website;
    
    public function setDirectoryName($dir)
    {
        // Define a hard theme path for the theme
        $this->path = PUBLIC_THEME_DIR . DIRECTORY_SEPARATOR . $dir;
        $this->directory = $dir;
    }
    
    public function setImage($filename)
    {
        // Test to see if an image is available to present the user
        // when switching themes
        $imageFile = $this->path . DIRECTORY_SEPARATOR . $filename;
        if (file_exists($imageFile) && is_readable($imageFile)) {
            $img = WEB_PUBLIC_THEME . '/' . $this->directory . '/' . $filename;
            $this->image = $img;
        }
    }
    
    public function setIni($filename)
    {
        $themeIni = $this->path . DIRECTORY_SEPARATOR . $filename;
        if (file_exists($themeIni) && is_readable($themeIni)) {
            $ini = new Zend_Config_Ini($themeIni, 'theme');
            foreach ($ini as $key => $value) {
                $this->$key = $value;
            }
        }
    }
    
    public function setConfig($filename)
    {
        // Get the theme's config file
        $themeConfig = $this->path . DIRECTORY_SEPARATOR . $filename;
        
        // If the theme has a config file, set hasConfig to true.
        $this->hasConfig = (file_exists($themeConfig) && is_readable($themeConfig));
    }
}