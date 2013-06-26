<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * A plugin and its metadata.
 *
 * This record represents the data Omeka stores about each plugin and uses to
 * manage the plugins, it is not a part of any plugin itself.
 * 
 * @package Omeka\Record
 */
class Plugin extends Omeka_Record_AbstractRecord implements Zend_Acl_Resource_Interface
{
    /**
     * Directory name for the plugin.
     *
     * @var string
     */
    public $name;

    /**
     * Whether this plugin is active.
     *
     * @var int
     */
    public $active = 0;

    /**
     * Version string for the currently-installed plugin.
     *
     * @var string
     */
    public $version;
    
    /**
     * Human-readable display name of the plugin.
     * 
     * @var string 
     */
    protected $_displayName;
    
    /**
     * The plugin's author.
     * 
     * @var string
     */
    protected $_author;
    
    /**
     * Description of the plugin.
     * 
     * @var string 
     */
    protected $_description;
    
    /**
     * URL for documentation or further information about the plugin.
     * 
     * @var string
     */
    protected $_link;
        
    /**
     * Whether the plugin has been loaded.
     * 
     * @var boolean
     */
    protected $_loaded = false;

    /**
     * Whether the plugin has a custom configuration form.
     * 
     * @var boolean 
     */
    protected $_hasConfig = false;
    
    /**
     * Directory names of required plugins.
     * 
     * @var array 
     */
    protected $_requiredPlugins = array();  
    
    /**
     * Directory names of optional plugins.
     * 
     * @var array
     */
    protected $_optionalPlugins = array();
        
    /**
     * Minimum Omeka version requirement for the plugin.
     * 
     * @var string 
     */
    protected $_minimumOmekaVersion;    
    
    /**
     * Maximum version of Omeka that the plugin has been tested on.
     * 
     * @var string 
     */
    protected $_testedUpToVersion;
    
    /**
     * Version of the plugin that is stored in the INI.
     * 
     * @var string 
     */
    protected $_iniVersion;
    
    /**
     * List of tags associated with this plugin, as retrieved from
     * the ini file.
     * 
     * @var array
     */
    protected $_iniTags = array();

    /**
     * Validate the plugin.
     *
     * The directory name must be set.
     */
    protected function _validate()
    {
        if (empty($this->name)) {
            $this->addError('name', __('Names of plugins must not be blank'));
        }
    }
    
    /**
     * Get the name of the directory containing the plugin.
     *
     * @return string
     */
    public function getDirectoryName()
    {
        return $this->name;
    }

    /**
     * Set the name of the directory containing the plugin.
     * 
     * @param string $name
     * @return Plugin
     */
    public function setDirectoryName($name)
    {
        $this->name = $name;
        return $this;
    }
    
    /**
     * Get the human-readable name of the plugin.
     * 
     * If there is no human-readable name available, returns the directory name
     * instead.
     *
     * @return string
     */
    public function getDisplayName()
    {
        if (empty($this->_displayName)) {
            return $this->getDirectoryName();
        }
        return $this->_displayName;
    }

    /**
     * Set the human-readable name of the plugin.
     * 
     * @param string $name
     * @return Plugin
     */
    public function setDisplayName($name)
    {
        $this->_displayName = trim($name);
        return $this;
    }
    
    /**
     * Get the plugin's author.
     *
     * @return string
     */
    public function getAuthor()
    {
        return $this->_author;
    }

    /**
     * Set the author's name.
     * 
     * @param string $author
     * @return Plugin
     */
    public function setAuthor($author)
    {
        $this->_author = trim($author);
        return $this;
    }
    
    /**
     * Get the description of the plugin.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->_description;
    }

    /**
     * Set the description of the plugin.
     * 
     * @param string $description
     * @return Plugin
     */
    public function setDescription($description)
    {
        $this->_description = trim($description);
        return $this;
    }
    
    /**
     * Get the minimum version of Omeka that this plugin requires to work.
     *
     * @return string
     */
    public function getMinimumOmekaVersion()
    {
        return $this->_minimumOmekaVersion;
    }

    /**
     * Set the minimum required version of Omeka.
     * 
     * @param string $version
     * @return Plugin
     */
    public function setMinimumOmekaVersion($version)
    {
        $this->_minimumOmekaVersion = $version;
        return $this;
    }
    
    /**
     * Get the version of Omeka that this plugin is tested up to.
     *
     * @return string
     */
    public function getTestedUpToOmekaVersion()
    {
        return $this->_testedUpToVersion;
    }

    /**
     * Set the version of Omeka that this plugin is tested up to.
     * 
     * @param string $version
     * @return Plugin
     */
    public function setTestedUpToOmekaVersion($version)
    {
        $this->_testedUpToVersion = $version;
        return $this;
    }
    
    /**
     * Get the list of plugins that are required for this plugin to work.
     *
     * @return array
     */
    public function getRequiredPlugins()
    {
        return $this->_requiredPlugins;
    }

    /**
     * Set the list of plugins that are required for this plugin to work.
     * 
     * @param array|string
     * @return Plugin
     */
    public function setRequiredPlugins($plugins)
    {
        if (is_string($plugins)) {
            $plugins = array_filter(array_map('trim', explode(',', $plugins)));
        }
        $this->_requiredPlugins = (array)$plugins;
        return $this;
    }
    
    /**
     * Get the list of plugins that can be used, but are not required by, this
     * plugin.
     *
     * @return array
     */
    public function getOptionalPlugins()
    {
        return $this->_optionalPlugins;
    }

    /**
     * Set the list of optional plugins.
     * 
     * @param array|string
     * @return Plugin
     */
    public function setOptionalPlugins($plugins)
    {
        if (is_string($plugins)) {
            $plugins = array_filter(array_map('trim', explode(',', $plugins)));
        }
        $this->_optionalPlugins = (array)$plugins;
        return $this;
    }
    
    /**
     * Get the list of tags for this plugin (from the ini file).
     *
     * @return array
     */
    public function getIniTags()
    {
        return $this->_iniTags;
    }

    /**
     * Set the list of tags for this plugin.
     * 
     * @param array|string
     * @return Plugin
     */
    public function setIniTags($tags)
    {
        if (is_string($tags)) {
            $tags = array_filter(array_map('trim', explode(',', $tags)));
        }
        $this->_iniTags = (array)$tags;
        return $this;
    }
    
    /**
     * Get the support link url from plugin.ini
     * 
     * @return string
     */
    public function getSupportLinkUrl()
    {
        return $this->_support_link;
    }
    
    /**
     * Set the support link url from plugin.ini
     * 
     * @param string $l
     * @return Plugin
     */
    public function setSupportLinkUrl($link)
    {
        if ( $link && !parse_url($link, PHP_URL_SCHEME) ) {
            $link = 'http://'.$link;
        }
        $this->_support_link = $link;
        return $this;
    }        
    
    /**
     * Get the URL link from the plugin.ini.
     *
     * @return string
     */
    public function getLinkUrl()
    {
        return $this->_link;
    }

    /**
     * Set the link from the plugin.ini.
     * 
     * @param string $link
     * @return Plugin
     */
    public function setLinkUrl($link)
    {
        if ( $link && !parse_url($link, PHP_URL_SCHEME) ) {
            $link = 'http://'.$link;
        }
        $this->_link = $link;
        return $this;
    }
        
    /**
     * Determine whether the Plugin has been installed.
     * 
     * @return bool
     */
    public function isInstalled()
    {
        return $this->exists();
    }
    
    /**
     * Determine whether the Plugin has been loaded.
     * 
     * @return bool
     */
    public function isLoaded()
    {
        return $this->_loaded;
    }

    /**
     * Set whether the plugin has been loaded.
     * 
     * @param bool $flag
     * @return Plugin
     */
    public function setLoaded($flag)
    {
        $this->_loaded = $flag;
        return $this;
    }
    
    /**
     * Determine whether the plugin is active.
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * Set whether the plugin is active.
     * 
     * @param bool $flag
     * @return Plugin
     */
    public function setActive($flag)
    {
        $this->active = $flag ? '1' : '0';
        return $this;
    }
    
    /**
     * Determine whether the plugin has a custom configuration form.
     *
     * @return bool
     */
    public function hasConfig()
    {
        return $this->_hasConfig;
    }

    /**
     * Set whether the plugin has a custom configuration form.
     * 
     * @param bool $flag
     * @return Plugin
     */
    public function setHasConfig($flag)
    {
        $this->_hasConfig = $flag;
        return $this;
    }
    
    /**
     * Get the version of the plugin stored in the INI file.
     *
     * @return string
     */
    public function getIniVersion()
    {
        return $this->_iniVersion;
    }

    /**
     * Set the version of the plugin that is indicated by the INI file.
     * 
     * @param string $version
     * @return Plugin
     */
    public function setIniVersion($version)
    {
        $this->_iniVersion = trim($version);
        return $this;
    }
    
    /**
     * Get the version of the plugin that is stored in the database.
     *
     * @return string
     */
    public function getDbVersion()
    {
        return $this->version;
    }

    /**
     * Set the version of the plugin that is stored in the database.
     * 
     * @param string $version
     * @return Plugin
     */
    public function setDbVersion($version)
    {
        $this->version = trim($version);
        return $this;
    }

    /**
     * Determine whether there is a new version of the plugin available.
     *
     * @return bool
     */
    public function hasNewVersion()
    {
        return $this->isInstalled() && $this->getIniVersion() && version_compare($this->getIniVersion(), $this->getDbVersion(), '>');   
    }
    
    /**
     * Determine whether this Omeka install meets the plugin's minimum version
     * requirements.
     * 
     * If the field is not set, assume that it meets the requirements.  If the 
     * field is set, it must be greater than the current version of Omeka.
     *
     * @return bool
     */
    public function meetsOmekaMinimumVersion()
    {
        return !$this->getMinimumOmekaVersion() || version_compare($this->getMinimumOmekaVersion(), OMEKA_VERSION, '<=');
    }

    /**
     * Determine whether this Omeka version has been tested for use with the
     * plugin.
     *
     * @return bool
     */
    public function meetsOmekaTestedUpToVersion()
    {
        // Add 'p' to the declared tested version from the plugin.
        // This means that the check will succeed for all sub-versions
        // of the declared version in plugin.ini.
        return !$this->getTestedUpToOmekaVersion() || version_compare($this->getTestedUpToOmekaVersion() . 'p', OMEKA_VERSION, '>=');
    }

    /**
     * Declare the Plugin model as relating to the Plugins ACL resource.
     *
     * @return string
     */
    public function getResourceId()
    {
        return 'Plugins';
    }
}
