<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2009-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */

/**
 * Changes the state of any given plugin (installed/uninstalled/activated/deactivated)
 *
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2009-2010
 */
class Omeka_Plugin_Installer
{
    /**
     * Plugin broker object.
     *
     * @var Omeka_Plugin_Broker
     */
    protected $_broker;
    
    /**
     * Plugin loader object.
     *
     * @var Omeka_Plugin_Loader
     */
    protected $_loader;
    
    /**
     * @param Omeka_Plugin_Broker $broker Plugin broker object.
     * @param Omeka_Plugin_Loader $loader Plugin loader object.
     */
    public function __construct(Omeka_Plugin_Broker $broker, 
                                Omeka_Plugin_Loader $loader)
    {
        $this->_broker = $broker;
        $this->_loader = $loader;
    }
            
    /**
     * Activate a plugin.
     *
     * @param Plugin $plugin Plugin to activate.
     * @return void
     */
    public function activate(Plugin $plugin)
    {
        $plugin->active = 1;
        $plugin->forceSave();
    }
    
    /**
     * Deactivate a plugin.
     *
     * @param Plugin $plugin Plugin to deactivate.
     * @return void
     */
    public function deactivate(Plugin $plugin)
    {
        $plugin->active = 0;
        $plugin->forceSave();
    }
    
    /**
     * Upgrade a plugin.
     *
     * This will activate the plugin, then run the 'upgrade' hook.
     *
     * @param Plugin $plugin Plugin to upgrade.
     * @return void
     */
    public function upgrade(Plugin $plugin)
    {           
        if (!$plugin->hasNewVersion()) {
            throw new Exception("The '" . $plugin->getDisplayName() . "' plugin must be installed and have newer files to upgrade it.");
        }
        
        $oldVersion = $plugin->getDbVersion();
        
        // activate the plugin so that it can be loaded.
        $plugin->setActive(true);
        // update version of the plugin stored in the database.
        // NOTE: This is required for the loader to work.
        $plugin->setDbVersion($plugin->getIniVersion());
                
        // load the plugin files.
        $this->_loader->load($plugin, true);

        // run the upgrade hook for the plugin.
        $this->_broker->callHook('upgrade', array($oldVersion, $plugin->getIniVersion()), $plugin);

        $plugin->forceSave();
    }
    
    /**
     * Install a plugin.
     *
     * This will activate the plugin, then run the 'install' hook.
     *
     * @param Plugin $plugin Plugin to install.
     * @return void
     */
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
     * Uninstall a plugin.  
     *
     * This will run the 'uninstall' hook for the given plugin, and then it
     * will remove the entry in the DB corresponding to the plugin.
     * 
     * @param Plugin $plugin Plugin to uninstall.
     * @return void
     */
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
