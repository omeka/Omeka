<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */

/**
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @subpackage Models
 * @author CHNM
 */
class Plugin extends Omeka_Record
{
    public $name;
    public $active = '0';
    public $version;
    
    /**
     * @var string Human-readable display name of the plugin.
     */
    protected $_displayName;
    
    /**
     * @var string Name of the plugin author.
     */
    protected $_author;
    
    /**
     * @var string Description of the plugin.
     */
    protected $_description;
    
    /**
     * @var string URL for documentation / further information about the plugin.
     */
    protected $_link;
        
    /**
     * @var boolean Whether or not the plugin has been loaded.
     */
    protected $_loaded = false;
            
    /**
     * @var boolean Whether or not the plugin has a custom configuration form.
     */
    protected $_hasConfig = false;
    
    /**
     * @var array Array of directory names for required plugins.
     */
    protected $_requiredPlugins = array();  
    
    /**
     * @var array Array of directory names for optional plugins.
     */
    protected $_optionalPlugins = array();
        
    /**
     * @var string Minimum Omeka version requirement for the plugin.
     */    
    protected $_minimumOmekaVersion;    
    
    /**
     * @var string Maximum version of Omeka that the plugin has been tested on.
     */
    protected $_testedUpToVersion;
    
    /**
     * @var string Version of the plugin that is stored in the ini.
     */
    protected $_iniVersion;
    
    /**
     * @var array List of tags associated with this plugin, as retrieved from
     * the ini file.
     */
    protected $_iniTags = array();

    /**
     * @var boolean Flag to determine how to load the plugin.php for this 
     * plugin (require_once vs. require).
     */
    protected $_requireOnce;
        
    protected function _validate()
    {
        if (empty($this->name)) {
            $this->addError('name', __('Names of plugins must not be blank'));
        }
    }
    
    /**
     * Get the name of the directory containing the plugin.
     */
    public function getDirectoryName()
    {
        return $this->name;
    }

    /**
     * Set the name of the directory containing the plugin.
     * 
     * @param string $name
     */
    public function setDirectoryName($name)
    {
        $this->name = $name;
        return $this;
    }
    
    /**
     * Get the human-readable name of the plugin, e.g. "Dublin Core Extended".
     * 
     * If there is no human-readable name available, returns the directory name
     * instead.
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
     */
    public function setDisplayName($name)
    {
        $this->_displayName = trim($name);
        return $this;
    }
    
    /**
     * Get the author's name.
     */
    public function getAuthor()
    {
        return $this->_author;
    }

    /**
     * Set the author's name.
     * 
     * @param string $author
     */
    public function setAuthor($author)
    {
        $this->_author = trim($author);
        return $this;
    }
    
    /**
     * Get the description of the plugin.
     */
    public function getDescription()
    {
        return $this->_description;
    }

    /**
     * Set the description of the plugin.
     * 
     * @param string $description 
     */
    public function setDescription($description)
    {
        $this->_description = trim($description);
        return $this;
    }
    
    /**
     * Get the minimum version of Omeka that this plugin requires to work.
     */
    public function getMinimumOmekaVersion()
    {
        return $this->_minimumOmekaVersion;
    }

    /**
     * Set the minimum required version of Omeka.
     * 
     * @param string $version
     */
    public function setMinimumOmekaVersion($version)
    {
        $this->_minimumOmekaVersion = $version;
        return $this;
    }
    
    /**
     * Get the version of Omeka that this plugin is tested up to.
     */
    public function getTestedUpToOmekaVersion()
    {
        return $this->_testedUpToVersion;
    }

    /**
     * Set the version of Omeka that this plugin is tested up to.
     * 
     * @param string $version
     */
    public function setTestedUpToOmekaVersion($version)
    {
        $this->_testedUpToVersion = $version;
        return $this;
    }
    
    /**
     * Get the list of plugins that are required for this plugin to work.
     */
    public function getRequiredPlugins()
    {
        return $this->_requiredPlugins;
    }

    /**
     * Set the list of plugins that are required for this plugin to work.
     * 
     * @param array|string
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
     */
    public function getOptionalPlugins()
    {
        return $this->_optionalPlugins;
    }

    /**
     * Set the list of optional plugins.
     * 
     * @param array|string
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
     */
    public function getIniTags()
    {
        return $this->_iniTags;
    }

    /**
     * Set the list of tags for this plugin.
     * 
     * @param array|string
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
     * Get the URL link from the plugin.ini.
     */
    public function getLinkUrl()
    {
        return $this->_link;
    }

    /**
     * Set the link from the plugin.ini.
     * 
     * @param string $link 
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
     * Whether or not the Plugin has been installed.
     * 
     * @return boolean 
     */
    public function isInstalled()
    {
        return $this->exists();
    }
    
    /**
     * @return boolean
     */
    public function isLoaded()
    {
        return $this->_loaded;
    }

    /**
     * @param boolean $flag
     */
    public function setLoaded($flag)
    {
        $this->_loaded = $flag;
        return $this;
    }
    
    /**
     * Whether or not the plugin has been activated through the UI.
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * Set whether or not the plugin has been activated.
     * 
     * @param boolean
     */
    public function setActive($flag)
    {
        $this->active = $flag ? '1' : '0';
        return $this;
    }
            
    /**
     * Whether or not the plugin has a custom configuration hook.
     */
    public function hasConfig()
    {
        return $this->_hasConfig;
    }

    /**
     * Set whether or not the plugin has a custom configuration hook.
     * 
     * @param boolean $flag
     */
    public function setHasConfig($flag)
    {
        $this->_hasConfig = $flag;
        return $this;
    }
    
    /**
     * Get the version of the plugin stored in the ini file.
     */
    public function getIniVersion()
    {
        return $this->_iniVersion;
    }

    /**
     * Set the version of the plugin that is indicated by the ini file.
     * 
     * @param string $version
     */
    public function setIniVersion($version)
    {
        $this->_iniVersion = trim($version);
        return $this;
    }
    
    /**
     * Get the version of the plugin that is stored in the database.
     */
    public function getDbVersion()
    {
        return $this->version;
    }

    /**
     * Set the version of the plugin that is stored in the database.
     * 
     * @param string $version
     */
    public function setDbVersion($version)
    {
        $this->version = trim($version);
        return $this;
    }
            
    /**
     * Determine whether or not there is a new version of the plugin available.
     */
    public function hasNewVersion()
    {
        return $this->isInstalled() && $this->getIniVersion() && version_compare($this->getIniVersion(), $this->getDbVersion(), '>');   
    }
    
    /**
     * Determine whether the plugin meets the minimum version requirements for Omeka.
     * 
     * If the field is not set, assume that it meets the requirements.  If the 
     * field is set, it must be greater than the current version of Omeka.
     */
    public function meetsOmekaMinimumVersion()
    {
        return !$this->getMinimumOmekaVersion() || version_compare($this->getMinimumOmekaVersion(), OMEKA_VERSION, '<=');
    }
    
    public function meetsOmekaTestedUpToVersion()
    {
        // Add 'p' to the declared tested version from the plugin.
        // This means that the check will succeed for all sub-versions
        // of the declared version in plugin.ini.
        return !$this->getTestedUpToOmekaVersion() || version_compare($this->getTestedUpToOmekaVersion() . 'p', OMEKA_VERSION, '>=');
    }

    /**
     * Set a flag to determine whether plugin.php may be reloaded repeatedly
     * in the test environment.
     *
     * If set to true, plugin.php will be loaded via require_once. This is the
     * default, and it is the only way that will work if functions and classes
     * have been defined directly in plugin.php. 
     *
     * If set to false, plugin.php will be loaded via require. This allows 
     * plugin writers to avoid duplicating executable logic from plugin.php
     * in their tests. In order for this to work, plugin.php must contain only
     * executable logic (no function or class definitions).
     *
     * @param boolean $flag
     */
    public function setRequireOnce($flag)
    {
        $this->_requireOnce = $flag;
    }

    /**
     * @see setRequireOnce()
     * @return boolean
     */
    public function getRequireOnce()
    {
        return $this->_requireOnce;
    }
}
