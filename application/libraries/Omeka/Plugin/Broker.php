<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @author CHNM
 **/

/**
 * Plugin Broker for Omeka.
 *
 * For example, $broker->add_action_contexts($controller) would call the 
 * 'add_action_contexts' on all plugins, and it would provide the controller
 * object as the first argument to all implementations of that hook. 
 **/
class Omeka_Plugin_Broker 
{        
    /**
     * Array of hooks that have been implemented for plugins.
     *
     * @var array
     **/
    protected $_callbacks = array();
        
    /**
     * The directory name of the current plugin (used for calling hooks)
     *
     * @var string
     **/
    protected $_current;
            
    public function __construct() 
    {        
        // Should be able to delegate to the plugin filters
        $this->_delegates = array();
        $this->_delegates[] = new Omeka_Plugin_Filters($this);        
    }
        
    /**
     * Check if the plugin is active, then enable the hook for it
     *
     * @param string $hook
     * @param string $callback
     * @return void
     **/
    public function addHook($hook, $callback)
    {    
        $currentPluginDirName = $this->getCurrentPluginDirName();          
        $this->_callbacks[$hook][$currentPluginDirName] = $callback;
    }
    
    /**
     * Gets the hook for a plugin
     *
     * @param string $pluginDirName
     * @param string $callback
     * @return void
     **/
    public function getHook($pluginDirName, $hook)
    {        
        if (is_array($this->_callbacks[$hook])) {
            return $this->_callbacks[$hook][$pluginDirName];
        }
    }
    
    /**
     * The plugin helper functions do not have any way of determining what
     *  plugin to is currently in focus.  These get/setCurrentPluginDirName methods
     *  allow the broker to know how to delegate to specific plugins if necessary.
     * 
     * @param string $pluginDirName
     * @return void
     **/
    public function setCurrentPluginDirName($pluginDirName)
    {
        $this->_current = $pluginDirName;
    }
    
    public function getCurrentPluginDirName()
    {
        return $this->_current;
    }
    
    /**
     * Configures the plugin
     *
     * @param string $pluginDirName
     * @return void
     **/
    public function config($pluginDirName)
    {
        //Check if the POST is empty, then check for a configuration form    
        if (empty($_POST)) {

            $config_form_hook = $this->getHook($pluginDirName, 'config_form');
    
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
            $config_hook = $this->getHook($pluginDirName, 'config');
            
            if ($config_hook) {
                call_user_func_array($config_hook, array($_POST));
            }
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
        foreach ($this->_callbacks[$hook] as $pluginDirName => $callback) {
            if ($this->isActive($pluginDirName)) {
                //Make sure the callback executes within the scope of the current plugin
                $this->setCurrentPluginDirName($pluginDirName);
                $return_values[$pluginDirName] = call_user_func_array($callback, $args);
            }
        }
        
        // Reset the value for current plugin after this loop finishes
        $this->setCurrentPluginDirName(null);
        
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
}