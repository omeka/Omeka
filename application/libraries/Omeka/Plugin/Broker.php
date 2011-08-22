<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Plugin Broker for Omeka.
 *
 * For example, $broker->add_action_contexts($controller) would call the 
 * 'add_action_contexts' on all plugins, and it would provide the controller
 * object as the first argument to all implementations of that hook. 
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class Omeka_Plugin_Broker 
{        
    /**
     * Array of hooks that have been implemented for plugins.
     *
     * @var array
     */
    protected $_callbacks = array();
    
    /**
     * Array of classes to delegate plugin API calls to.
     * 
     * @var array
     */
    protected $_delegates = array();
    
    /**
     * The directory name of the current plugin (used for calling hooks)
     *
     * @var string
     */
    protected $_current;
    
    /**
     * Delegation to other plugin API classes is set up here.
     */
    public function __construct() 
    {        
        // Should be able to delegate to the plugin filters
        $this->_delegates[] = new Omeka_Plugin_Filters($this);        
    }
        
    /**
     * Add a hook implementation for a plugin.
     *
     * @param string $hook Name of the hook being implemented.
     * @param string $callback PHP callback for the hook implementation.
     * @param string|null $plugin Optional name of the plugin for
     * which to add the hook. If omitted, the current plugin is used.
     * @return void
     */
    public function addHook($hook, $callback, $plugin = null)
    {    
        if ($plugin) {
            $currentPluginDirName = $plugin;
        } else {
            $currentPluginDirName = $this->getCurrentPluginDirName(); 
        }

        // Null or empty plugin name leads to false negatives when 
        // looking up callbacks.
        if (!$currentPluginDirName) {
            throw new RuntimeException("Cannot add a hook without an "
                . "associated plugin namespace.");
        }

        $this->_callbacks[$hook][$currentPluginDirName] = $callback;
    }
    
    /**
     * Get the hook implementation for a plugin.
     *
     * @param string $pluginDirName Name of the plugin to get the implementation
     * from.
     * @param string $hook Name of the hook to get the implementation for.
     * @return callback|null
     */
    public function getHook($pluginDirName, $hook)
    {   
        if ($pluginDirName instanceof Plugin) {
            $pluginDirName = $pluginDirName->getDirectoryName();
        }
             
        if (array_key_exists($hook, $this->_callbacks) 
            && is_array($this->_callbacks[$hook]) 
            && array_key_exists($pluginDirName, $this->_callbacks[$hook])
        ) {
            return $this->_callbacks[$hook][$pluginDirName];
        }
        return null;
    }
    
    /**
     * Set the currently-focused plugin by directory name.
     *
     * The plugin helper functions do not have any way of determining what
     * plugin to is currently in focus.  These get/setCurrentPluginDirName 
     * methods allow the broker to know how to delegate to specific plugins if 
     * necessary.
     * 
     * @param string $pluginDirName Plugin to set as current.
     * @return void
     */
    public function setCurrentPluginDirName($pluginDirName)
    {
        $this->_current = $pluginDirName;
    }
    
    /**
     * Get the directory name of the currently-focused plugin.
     *
     * @see Omeka_Plugin_Broker::setCurrentPluginDirName()
     * @return string
     */
    public function getCurrentPluginDirName()
    {
        return $this->_current;
    }
            
    /**
     * Call a hook by name.
     * Hooks can either be called globally or for a specific plugin only.
     *
     * @see Omeka_Plugin_Broker::__call()
     * @param string $hook Name of the hook.
     * @param array $args Arguments that are passed to each hook implementation.
     * @param Plugin|string $plugin Optional name of the plugin for which to 
     * invoke the hook.
     * @return void
     */
    public function callHook($hook, $args, $plugin = null)
    {
        if (empty($this->_callbacks[$hook])) {
            return;
        }
        
        // If we are calling the hook for a single function, do that and return.
        if ($plugin) {
            if ($callback = $this->getHook($plugin, $hook)) {
                call_user_func_array($callback, $args);
            }
            return;
        }
        
        // Otherwise iterate through all the hooks and call each in turn.
        foreach ($this->_callbacks[$hook] as $pluginDirName => $callback) {
            //Make sure the callback executes within the scope of the current plugin
            $this->setCurrentPluginDirName($pluginDirName);
            call_user_func_array($callback, $args);
        }
        
        // Reset the value for current plugin after this loop finishes
        $this->setCurrentPluginDirName(null);       
    }
    
    /**
     * Handle dispatching for all plugin calls.
     *
     * Check for delegating to other classes that handle plugin API stuff first,
     * i.e. Omeka_Plugin_Filters etc.
     *
     * @see Omeka_Plugin_Broker::__construct()
     * @return mixed
     */
    public function __call($hook, $args) 
    {
        // Delegation
        foreach ($this->_delegates as $delegator) {
            if(method_exists($delegator, $hook)) {
                return call_user_func_array(array($delegator, $hook), $args);
            }
        }
        
        // Call the hook for the plugins
        return $this->callHook($hook, $args);
    }
    
    /**
     * Register the plugin broker so that plugin writers can use global functions
     * like add_plugin_hook() to interact with the plugin API.
     *
     * @return void
     */
    public function register()
    { 
        Zend_Registry::set('pluginbroker', $this);
    }
}
