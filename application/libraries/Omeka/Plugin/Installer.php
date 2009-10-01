<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * Changes the state of any given plugin (installed/uninstalled/activated/deactivated)
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2009
 **/
class Omeka_Plugin_Installer
{
    protected $_broker;
    protected $_loader;
    
    /**
     * @todo Refactor all methods to accept an instance of Plugin record.
     */
    public function __construct(Omeka_Plugin_Broker $broker, 
                                Omeka_Plugin_Loader $loader)
    {
        $this->_broker = $broker;
        $this->_loader = $loader;
    }
            
    /**
     * Activates the plugin
     *
     * @param string $pluginDirName
     * @return void
     **/
    public function activate(Plugin $plugin)
    {
        $plugin->active = 1;
        $plugin->forceSave();
    }
    
    /**
     * Deactivates the plugin
     *
     * @param string $pluginDirName
     * @return void
     **/
    public function deactivate(Plugin $plugin)
    {
        $plugin->active = 0;
        $plugin->forceSave();
    }
    
    /**
     * Upgrades the plugin
     *
     * @param string $pluginDirName
     * @return void
     **/
    public function upgrade(Plugin $plugin)
    {           
        if (!$plugin->hasNewVersion()) {
            throw new Exception("The '" . $plugin->getDisplayName() . "' plugin must be installed and have newer files to upgrade it.");
        }
        
        // activate the plugin so that it can be loaded.
        $plugin->setActive(true);
                
        // load the plugin files.
        $this->_loader->load($plugin, true);

        // run the upgrade hook for the plugin.
        $this->_broker->callHook('upgrade', array($plugin->getDbVersion(), $plugin->getIniVersion()), $plugin);

        // update version of the plugin stored in the database.
        $plugin->setDbVersion($plugin->getIniVersion());
        $plugin->forceSave();
    }
    
    /**
     * Installs the plugin
     *
     * @param string $pluginDirName
     * @return void
     **/
    public function install(Plugin $plugin) 
    {
        if (!$plugin->getDirectoryName()) {
            throw new Exception("Plugin must have a valid directory name before it can be installed.");
        }

        try {
            $plugin->setActive(true);            
            $plugin->setDbVersion($plugin->getIniVersion());
            $plugin->forceSave();
            
            // Force the plugin to load.  Will throw exception if plugin cannot be loaded for some reason.
            $this->_loader->load($plugin, true);
            
            //Now run the installer for the plugin
            $this->_broker->callHook('install', array($plugin->id), $plugin);               
        } catch (Exception $e) {
            //If there was an error, remove the plugin from the DB so that we can retry the install
            $plugin->delete();
            throw $e;
        }
    }
    
    /**
     * Uninstall hook for plugins.  
     *
     * This will run the 'uninstall' hook for the given plugin, and then it
     * will remove the entry in the DB corresponding to the plugin.
     * 
     * @param string $pluginDirName Name of the plugin directory to uninstall
     * @return void
     **/
    public function uninstall(Plugin $plugin)
    {
        if (!$plugin->isLoaded()) {
            // Flag the plugin as active so we can load the 'uninstall' hook.
            $plugin->setActive(true);
            // Load the plugin files, die if can't be loaded.
            $this->_loader->load($plugin, true);
        }
        
        $this->_broker->callHook('uninstall', array(), $plugin);
        $plugin->delete();
    }
}
