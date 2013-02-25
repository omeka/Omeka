<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * A theme and its metadata.
 * 
 * Unlike most other models, Themes are not stored in the database. This model
 * relies only on INI data, but acts like an Omeka_Record_AbstractRecord model.
 * 
 * @package Omeka\Record
 */
class Theme 
{
    /**
     * Filename for the theme screenshot.
     */
    const THEME_IMAGE_FILE_NAME = 'theme.jpg';

    /**
     * Filename for the theme INI file.
     */
    const THEME_INI_FILE_NAME = 'theme.ini';

    /**
     * Filename for the theme config form INI file.
     */
    const THEME_CONFIG_FILE_NAME = 'config.ini';

    /**
     * Option name for the current public theme.
     */
    const PUBLIC_THEME_OPTION = 'public_theme';

    /**
     * Option name for the current admin theme.
     */
    const ADMIN_THEME_OPTION = 'admin_theme';

    /**
     * Absolute path to the theme.
     *
     * @var string
     */
    public $path;

    /**
     * Directory name of the theme.
     *
     * @var string
     */
    public $directory;

    /**
     * Web path to the theme screenshot.
     *
     * @var string
     */
    public $image;

    /**
     * The theme's author.
     *
     * @var string
     */
    public $author;

    /**
     * The theme's title.
     *
     * @var string
     */
    public $title;

    /**
     * The theme's description.
     *
     * @var string
     */
    public $description;

    /**
     * The software license for the theme.
     *
     * @var string
     */
    public $license;

    /**
     * A link to the theme's website.
     *
     * @var string
     */
    public $website;

    /**
     * The minimum Omeka version the theme will run on.
     *
     * @var string
     */
    public $omeka_minimum_version;

    /**
     * Set the INI and file data for the theme, given its directory name.
     *
     * @param string $themeName Directory name.
     */
    public function __construct($themeName) 
    {
        $this->setDirectoryName($themeName);
        $this->setImage(self::THEME_IMAGE_FILE_NAME);
        $this->setIni(self::THEME_INI_FILE_NAME);
        $this->setConfig(self::THEME_CONFIG_FILE_NAME);
    }

    /**
     * Set the theme's directory name and path.
     *
     * @param string $dir Directory name.
     */
    public function setDirectoryName($dir)
    {
        // Define a hard theme path for the theme
        $this->path = PUBLIC_THEME_DIR . '/' . $dir;
        $this->directory = $dir;
    }

    /**
     * Get the physical path to the theme's scripts.
     *
     * @return string Physical path.
     */
    public function getScriptPath()
    {
        return PUBLIC_THEME_DIR . '/' . $this->directory;
    }

    /**
     * Get the web path to the theme's assets.
     *
     * @return string Web path.
     */
    public function getAssetPath()
    {
        return WEB_PUBLIC_THEME . '/' . $this->directory;
    }

    /**
     * Get the physical path to the theme's override scripts for the given plugin.
     *
     * @param string $pluginModuleName (i.e., 'exhibit-builder')
     * @return string Physical path.
     */
    public function getScriptPathForPlugin($pluginModuleName)
    {
        return $this->getScriptPath() . '/' . $pluginModuleName;
    }

    /**
     * Get the web path to the theme's override assets for the given plugin.
     *
     * @param string $pluginModuleName (i.e., 'exhibit-builder')
     * @return string Web path.
     */
    public function getAssetPathForPlugin($pluginModuleName)
    {
        return $this->getAssetPath() . '/' . $pluginModuleName;
    }

    /**
     * Set the web path to the screenshot, if it exists.
     *
     * @param string $fileName Relative filename of the image to check.
     */
    public function setImage($fileName)
    {
        // Test to see if an image is available to present the user
        // when switching themes
        $imageFile = $this->path . '/' . $fileName;
        if (file_exists($imageFile) && is_readable($imageFile)) {
            $img = WEB_PUBLIC_THEME . '/' . $this->directory . '/' . $fileName;
            $this->image = $img;
        }
    }

    /**
     * Load data from the INI file at the given path.
     *
     * @param string $fileName Relative filename of the INI file.
     */
    public function setIni($fileName)
    {
        $themeIni = $this->path . '/' . $fileName;
        if (file_exists($themeIni) && is_readable($themeIni)) {
            $ini = new Zend_Config_Ini($themeIni, 'theme');
            foreach ($ini as $key => $value) {
                if ($key == 'website') {
                    $value = $this->_parseWebsite($value);
                }
                $this->$key = $value;
            }
        }
    }

    /**
     * Check for a theme config file at the given location.
     *
     * @param string $fileName Relative filename of the theme config.ini.
     */
    public function setConfig($fileName)
    {
        // Get the theme's config file
        $themeConfig = $this->path . '/' . $fileName;
        
        // If the theme has a config file, set hasConfig to true.
        $this->hasConfig = (file_exists($themeConfig) && is_readable($themeConfig));
    }

    /**
     * Get the directory name of the current theme.
     *
     * @param string $type 'admin' or 'public', defaults to current type
     * @return string
     */
    static public function getCurrentThemeName($type = null)
    {
        if ($type === null) {
            $type = is_admin_theme() ? 'admin' : 'public';
        }

        return apply_filters($type . '_theme_name', get_option("{$type}_theme"));
    }


    /**
     * Retrieve all themes
     *
     * @return array An array of theme objects
     */
    static public function getAllThemes() 
    {
        /**
         * Create an array of themes, with the directory paths
         * theme.ini files and images paths if they are present
         */
        $themes = array();
        $iterator = new VersionedDirectoryIterator(PUBLIC_THEME_DIR);
        $themeDirs = $iterator->getValid();
        foreach ($themeDirs as $themeName) {
            $themes[$themeName] = self::getTheme($themeName);
        }
        return $themes;
    }


    /**
     * Retrieve a theme.
     *
     * @param string $themeName  The name of the theme.
     * @return Theme A theme object
     */
    static public function getTheme($themeName) 
    {
        $theme = new Theme($themeName);     
        return $theme;
    }
    
    /** 
     * Set theme configuration options.
     * 
     * @param string $themeName  The name of the theme
     * @param array $themeConfigOptions An associative array of configuration options, 
     *  where each key is a configuration form input name and 
     *  each value is a string value of that configuration form input
     */    
    static public function setOptions($themeName, $themeConfigOptions)
    {
        $themeOptionName = self::getOptionName($themeName);
        set_option($themeOptionName, serialize($themeConfigOptions));
    }
    
    /** 
     * Get theme configuration options.
     * 
     * @param string $themeName  The name of the theme
     * @return array An associative array of configuration options, 
     *  where each key is a configuration form input name and 
     *  each value is a string value of that configuration form input
     */
    static public function getOptions($themeName)
    {
        $themeOptionName = self::getOptionName($themeName);
        $themeConfigOptions = get_option($themeOptionName);
        $themeConfigOptions = apply_filters(
            'theme_options', 
            $themeConfigOptions, 
            array('theme_name' => $themeName)
        );
        if ($themeConfigOptions) {
            $themeConfigOptions = unserialize($themeConfigOptions);
        } else {
            $themeConfigOptions = array();
        }
        return $themeConfigOptions;
    }
    
    /** 
     * Get the value of a theme configuration option.
     * 
     * @param string $themeName  The name of the theme
     * @param string $themeOptionName The name of the theme option
     * @return string The value of the theme option
     */
    static public function getOption($themeName, $themeOptionName)
    {
        $themeOptionValue = null;
        $themeName = trim($themeName);
        $themeOptionName = Inflector::underscore($themeOptionName);
        $themeConfigOptions = self::getOptions($themeName);
        if ($themeConfigOptions && array_key_exists($themeOptionName, $themeConfigOptions)) {        
            $themeOptionValue = $themeConfigOptions[$themeOptionName];
        }
        return $themeOptionValue;
    }
    
    /** 
     * Set the value of a theme configuration option.
     * 
     * @param string $themeName  The name of the theme
     * @param string $themeOptionName The name of the theme option
     * @param string The value of the theme option
     */
    static public function setOption($themeName, $themeOptionName, $themeOptionValue)
    {
        $themeName = trim($themeName);
        $themeOptionName = Inflector::underscore($themeOptionName);
        $themeConfigOptions = self::getOptions($themeName);
        $themeConfigOptions[$themeOptionName] = $themeOptionValue;
        self::setOptions($themeName, $themeConfigOptions);        
    }
    
    /** 
     * Get the name of a specific theme's option.  Each theme has a single
     * option in the option's table, which stores all of the configuration
     * options for that theme
     * 
     * @param string $themeName  The name of the theme
     * @return string The name of a specific theme's option.
     */
    static public function getOptionName($themeName)
    {
        $themeOptionName = 'theme_' . trim(strtolower($themeName)) . '_options';
        return $themeOptionName;
    }
    
    /** 
     * Get the name of a file uploaded as a theme configuration option.  
     * This is the name of the file after it has been uploaded and renamed.
     * 
     * @param string $themeName  The name of the theme
     * @param string $optionName The name of the theme option associated with the uploaded file
     * @param string $fileName The name of the uploaded file
     * @return string The name of an uploaded file for the theme.
     */
    static public function getUploadedFileName($themeName, $optionName, $fileName)
    {
        $filter = new Omeka_Filter_Filename;
        return $filter->renameFile($fileName);
    }
    
    /**
     * Parses the website string to confirm whether it has a scheme.
     *
     * @param string $website The website given in the theme's INI file.
     * @return string The website URL with a prepended scheme.
     */
    static protected function _parseWebsite($website)
    {
        if ( !parse_url($website, PHP_URL_SCHEME) ) {
            return 'http://'.$website;
        }
        return $website;
    }
}
