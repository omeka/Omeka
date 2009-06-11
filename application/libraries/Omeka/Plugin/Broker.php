<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * Plugin Broker for Omeka.
 *
 * This handles installation, loading and calling hooks for plugins.  The
 * syntax for activating plugin hooks on the Broker is as simple as calling
 * it as a method on the broker.
 *
 * For example, $broker->add_action_contexts($controller) would call the 
 * 'add_action_contexts' on all plugins, and it would provide the controller
 * object as the first argument to all implementations of that hook. 
 *
 * @package Omeka
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class Omeka_Plugin_Broker 
{
    protected $_pluginPaths = array();
    
    protected $_basePath = array();
    
    /**
     * Database connection to use when interacting with the database
     *
     * @var Omeka_Db 
     **/
    protected $_db;
    
    /**
     * Array of hooks that have been implemented for plugins.
     *
     * @var array
     **/
    protected $_callbacks = array();

    /**
     * A list of plugins that have been installed (but not necessarily
     * activated).
     *
     * @var array
     **/
    protected $_installed = array();
    
    /**
     * List of currently activated plugins
     *
     * @var array
     **/
    protected $_active = array();
    
    /**
     * List of all plugins (installed or not) that are currently located
     * in the plugins/ directory
     *
     * @var array
     **/
    protected $_all = array();
    
    /**
     * The name of the current plugin (used for calling hooks)
     *
     * @var string
     **/
    protected $_current;
        
    /**
     * @see Omeka_Plugin_Broker::addMediaAdapter()
     *
     * @var array
     **/
    protected $_media = array('callbacks'=>array(), 'options'=>array());    
        
    //View script directories that have been added by plugins
    protected $_pluginViewDirs = array();
            
    public function __construct($db, $pathToPlugins) 
    {
        // Should be able to delegate to the plugin filters
        $this->_delegates = array();
        $this->_delegates[] = new Omeka_Plugin_Filters($this);
        
        $this->_basePath = $pathToPlugins;
        $this->_db = $db;
        
        //Construct the current list of potential, installed & active plugins
        require_once 'VersionedDirectoryIterator.php';
        
        //Loop through all the plugins in the plugin directory
        $dir = new VersionedDirectoryIterator($this->_basePath);
        $names = $dir->getValid();
        
        $this->_all = $names;

        //Get the list of currently installed plugins
        $installed = array();
        $active = array();
        
        $res = $this->_db->query("SELECT p.name, p.active FROM $db->Plugin p");
        
        foreach ($res->fetchAll() as $row) {
            $name = $row['name'];
            
            $installed[$name] = $name;
            
            //Only active plugins should be required
            if ($row['active']) {
                $active[$name] = $name;
                
                
                //Require the file that contains the plugin
                $path = $this->getPluginFilePath($name);
                
                if (file_exists($path)) {
                    $this->_pluginPaths[$name] = $path;
                }
            }
        }
        
        $this->_active = $active;
        $this->_installed = $installed;
    }
    
    /**
     * Load all active plugins (and make sure the plugin API is available)
     * 
     * @return void
     **/
    public function loadActive()
    {   
        Zend_Registry::set('pluginbroker', $this);     
        foreach ($this->_pluginPaths as $name => $path) {
            $this->setCurrentPlugin($name);
            $this->addApplicationDirs($name);
           require_once $path;
           $this->setCurrentPlugin(null);
        } 
    }
    
    protected function getPluginFilePath($name)
    {
        return $this->_basePath . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR . 'plugin.php';
    }
        
    /**
     * Check if the plugin is active, then enable the hook for it
     *
     * @return void
     **/
    public function addHook($hook, $callback)
    {    
        $current = $this->getCurrentPlugin();
                    
        $this->_callbacks[$hook][$current] = $callback;
    }
    
    public function getHook($plugin, $hook)
    {        
        if (is_array($this->_callbacks[$hook])) {
            return $this->_callbacks[$hook][$plugin];
        }
    }
    
    /**
     * The plugin helper functions do not have any way of determining what
     *  plugin to is currently in focus.  These get/setCurrentPlugin methods
     *  allow the broker to know how to delegate to specific plugins if necessary.
     *
     * @return string
     **/
    protected function setCurrentPlugin($plugin)
    {
        $this->_current = $plugin;
    }
    
    public function getCurrentPlugin()
    {
        return $this->_current;
    }
    
    public function isActive($plugin)
    {
        return in_array($plugin, $this->_active);
    }
    
    public function isInstalled($plugin)
    {
        return in_array($plugin, $this->_installed);
    }
    
    public function getAll()
    {
        return $this->_all;
    }
    
    /**
     * Return a list of plugins that have not been installed yet
     *
     * @return void
     **/
    public function getNew()
    {
        $new = array_diff($this->_all, array_keys($this->_installed));
        
        return $new;
    }
    
    public function config($plugin)
    {
        //Check if the POST is empty, then check for a configuration form    
        if (empty($_POST)) {

            $config_form_hook = $this->getHook($plugin, 'config_form');
    
            //If there is a configuration form available, load that and return the output for rendering later
            if ($config_form_hook) {
                
                require_once HELPERS;
                
                ob_start();
                call_user_func_array($config_form_hook, array($_POST)); 
                $config = ob_get_clean();    
                
                return $config;
            }
        
        //Data has been POSTed to the configuration mechanism
        } else {
            
            //Run the 'config' hook, then run the rest of the installer
            $config_hook = $this->getHook($plugin, 'config');
            
            if ($config_hook) {
                call_user_func_array($config_hook, array($_POST));
            }
        }
    }
    
    public function install($plugin) 
    {
        
        //Make sure the plugin helpers like define_metafield() etc. that need to be aware of scope can operate on this plugin
        $this->setCurrentPlugin($plugin);
        
        //Include the plugin file manually because it was not included via the constructor
        $file = $this->getPluginFilePath($plugin);
        if (file_exists($file)) {
            $this->addApplicationDirs($plugin);
            require_once $file;
        } else {
            throw new Exception("Plugin named '$plugin' requires at minimum a file named 'plugin.php' to exist.  Please add this file or remove the '$plugin' directory.");
        }

        try {            
            $plugin_obj = new Plugin;
            $plugin_obj->active = 1;
            $plugin_obj->name = $plugin;
            $plugin_obj->forceSave();
            $plugin_id = $plugin_obj->id;
            //Now run the installer for the plugin
            $install_hook = $this->getHook($plugin, 'install');
            call_user_func_array($install_hook, array($plugin_id));            
        } catch (Exception $e) {
            //If there was an error, remove the plugin from the DB so that we can retry the install
            if ($plugin_obj->exists()) {
                $plugin_obj->delete();
            }
            throw new Exception($e->getMessage());
        }
    }
        
    /**
     * used by the add_theme_pages() helper to create a list of directories that can store static pages that integrate into the themes
     *
     * @return void
     **/
    protected function addThemeDir($pluginName, $path, $themeType, $moduleName)
    {
        if (!in_array($themeType, array('public','admin','shared'))) {
            return false;
        }
        
        //Path must begin from within the plugin's directory
        
        $path = $pluginName . DIRECTORY_SEPARATOR . $path;
                
        switch ($themeType) {
            case 'public':
                $this->_pluginViewDirs[$moduleName]['public'][] = $path;
                break;
            case 'admin':
                $this->_pluginViewDirs[$moduleName]['admin'][] = $path;
                break;
            case 'shared':
                $this->_pluginViewDirs[$moduleName]['public'][] = $path;
                $this->_pluginViewDirs[$moduleName]['admin'][] = $path;
                break;
            default:
                break;
        }
        // var_dump($this->_pluginViewDirs);                    
    }
    
    public function getModuleViewScriptDirs($moduleName=null)
    {
        if ($moduleName) {
            return $this->_pluginViewDirs[$moduleName];
        }
        return $this->_pluginViewDirs;
    }
    
    /**
     * This will make an entire directory of controllers available to the front controller.
     * 
     * This has to use addControllerDirectory() instead of addModuleDirectory() because module names
     * are case-sensitive and module directories need to be lowercased to conform to Zend's weird naming conventions.
     * 
     * @return void
     **/
    public function addControllerDir($pluginName, $moduleName)
    {                
        $contrDir = PLUGIN_DIR . DIRECTORY_SEPARATOR . $pluginName . DIRECTORY_SEPARATOR . 'controllers';
        Zend_Controller_Front::getInstance()->addControllerDirectory($contrDir, $moduleName);
    }
    
    /**
     * Set up the following directory structure for plugins:
     * 
     *      controllers/
     *      models/
     *      libraries/
     *      views/
     *          admin/
     *          public/
     *          shared/
     * 
     *  This also adds these folders to the correct include paths.
     *  
     *
     * @return void
     **/
    public function addApplicationDirs($pluginName)
    {        
        $baseDir = $this->_basePath . DIRECTORY_SEPARATOR . $pluginName;
        
        $modelDir      = $baseDir . DIRECTORY_SEPARATOR  . 'models';
        $controllerDir = $baseDir . DIRECTORY_SEPARATOR  . 'controllers';
        $librariesDir  = $baseDir . DIRECTORY_SEPARATOR  . 'libraries';
        $viewsDir      = $baseDir . DIRECTORY_SEPARATOR  . 'views';
        $adminDir      = $viewsDir . DIRECTORY_SEPARATOR . 'admin';
        $publicDir     = $viewsDir . DIRECTORY_SEPARATOR . 'public';
        $sharedDir     = $viewsDir . DIRECTORY_SEPARATOR . 'shared';
        
        //Add 'models' and 'libraries' directories to the include path
        if (file_exists($modelDir)) {
            set_include_path(get_include_path() . PATH_SEPARATOR . $modelDir );
        }
        
        if (file_exists($librariesDir)) {
            set_include_path(get_include_path() . PATH_SEPARATOR . $librariesDir);
        }
        
        $moduleName = $this->_getModuleName($pluginName);

        //If the controller directory exists, add that 
        if (file_exists($controllerDir)) {
            $this->addControllerDir($pluginName, $moduleName);   
        }
        
        if (file_exists($sharedDir)) {
            $this->addThemeDir($pluginName, 'views' . DIRECTORY_SEPARATOR . 'shared', 'shared', $moduleName);
        }
        
        if (file_exists($adminDir)) {
            $this->addThemeDir($pluginName, 'views' . DIRECTORY_SEPARATOR . 'admin', 'admin', $moduleName);
        }

        if (file_exists($publicDir)) {
            $this->addThemeDir($pluginName, 'views' . DIRECTORY_SEPARATOR . 'public', 'public', $moduleName);
        }

    }
    
    /**
     * Retrieve the module name for the plugin (based on the directory name
     * of the plugin).
     * 
     * @param string $pluginName
     * @return string
     **/
    protected function _getModuleName($pluginName)
    {
        // Module name needs to be lowercased (plugin directories are not, 
        // typically).  Module name needs to go from camelCased to dashed 
        // (ElementSets --> element-sets).
        $inflector = new Zend_Filter_Word_CamelCaseToDash();
        $moduleName = strtolower($inflector->filter($pluginName));
        return $moduleName;
    }
    
    /**
     * Adds a plugin hook to display files of a specific MIME type in a certain way.
     * 
     * This allows plugins to hook directly into the Omeka_View_Helper_Media
     * class, so that plugins can override/define ways of displaying specific
     * files.  The most obvious example of where this would come in handy is
     * to define ways of displaying uncommon files, such as QTVR, or novel ways
     * of displaying more common files, such as using iPaper to display PDFs.
     *
     * The advantage is seemless integration with the themes, rather than
     * forcing theme designers to use plugin-specific API calls in their themes.
     *
     * @internal This operates on two keyed lists: a list of callbacks, which is
     * keyed to the MIME type, i.e. array('video/wmv'=>'foobar_movie_display').
     * The second list is the set of default options for the callback, which
     * can be overridden during the actual display_files() call within the theme.
     * 
     * @param array|string $mimeTypes Set of MIME types that this specific
     * callback will respond to.
     * @param callback Any valid callback.  This function should return a
     * string containing valid XHTML, which will be used to display the file.
     * @return void
     **/
    public function addMediaAdapter($mimeTypes, $callback, array $defaultOptions = array())
    {
        //Create the keyed list of mimeType=>callback format, and merge it
        //with the current list.
        $mimeTypes = (array) $mimeTypes;
        $fillArray = array_fill(0, count($mimeTypes), $callback);    
        $callbackList = array_combine($mimeTypes, $fillArray);
        
        $this->_media['callbacks'] = array_merge($callbackList, $this->_media['callbacks']);
        
        //Create the keyed list of callback=>options format, and add it 
        //to the current list
        
        //The key for the array might be the serialized callback (if necessary)
        $callbackKey = !is_string($callback) ? serialize($callback) : $callback;
        $this->_media['options'][$callbackKey] = $defaultOptions;        
    }
    
    /**
     * Retrieve a list of all media display callbacks that are defined by
     * plugins.  Currently called only within Omeka_View_Helper_Media
     *
     * @see Omeka_View_Helper_Media::__construct()
     * @return array
     **/
    public function getMediaAdapters()
    {        
        return $this->_media;
    }
    
    /**
     * Uninstall hook for plugins.  
     *
     * This will run the 'uninstall' hook for the given plugin, and then it
     * will remove the entry in the DB corresponding to the plugin.
     * 
     * @param string Name of the plugin directory to uninstall
     * @return void
     **/
    public function uninstall($plugin)
    {
        try {
            $uninstallHook = $this->getHook($plugin, 'uninstall');
            if ($uninstallHook) {
                call_user_func($uninstallHook);
            }
            //Remove the entry from the database
            $this->_db->query("DELETE FROM {$this->_db->Plugin} WHERE name = ? LIMIT 1", array($plugin));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
    
    /**
     * @see Omeka_Plugin_Broker::__call()
     * @param string Name of the hook.
     * @param array Arguments that are passed to each hook implementation.
     * @return array Keyed to the names of plugins, this will contain an array of 
     * all the return values of the hook implementations.
     **/
    public function callHook($hook, $args)
    {
        if (empty($this->_callbacks[$hook])) {
            return;
        }
        
        $return_values = array();
        
        foreach ($this->_callbacks[$hook] as $plugin => $callback) {
            if ($this->isActive($plugin)) {
                //Make sure the callback executes within the scope of the current plugin
                $this->setCurrentPlugin($plugin);
                $return_values[$plugin] = call_user_func_array($callback, $args);
            }
        }
        
        // Reset the value for current plugin after this loop finishes
        $this->setCurrentPlugin(null);
        
        return $return_values;        
    }
    
    /**
     * This handles dispatching all plugin hooks.
     *
     * Check for delegating to other classes that handle plugin API stuff first,
     * i.e. Omeka_Plugin_Filters etc.
     *
     * @see Omeka_Plugin_Broker::__construct()
     * @return mixed
     **/
    public function __call($hook, $args) {
        
        // Delegation
        foreach ($this->_delegates as $delegator) {
            if(method_exists($delegator, $hook)) {
                return call_user_func_array(array($delegator, $hook), $args);
            }
        }
        
        // Call the hook for the plugins
        return $this->callHook($hook, $args);
    }
}