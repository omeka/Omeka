<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Changes the state of any given plugin (installed/uninstalled/activated/deactivated)
 * 
 * @package Omeka\Plugin\Installer
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
        $plugin->save();
        $this->_broker->callHook('activate', array(), $plugin);
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
        $plugin->save();
        $this->_broker->callHook('deactivate', array(), $plugin);
    }
    
    /**
     * Upgrade a plugin.
     *
     * This will activate the plugin, then run the 'upgrade' hook.
     *
     * @param Plugin $plugin Plugin to upgrade.
     * @throws Omeka_Plugin_Exception | Omeka_Plugin_Loader_Exception
     * @return void
     */
    public function upgrade(Plugin $plugin)
    {           
        if (!$plugin->hasNewVersion()) {
            throw new Omeka_Plugin_Installer_Exception(__('The "%s" plugin must be installed and have newer files to upgrade it.', $plugin->getDisplayName()));
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
        $this->_broker->callHook(
            'upgrade', 
            array('old_version' => $oldVersion, 
                  'new_version' => $plugin->getIniVersion()), 
            $plugin
        );

        $plugin->save();
    }
    
    /**
     * Install a plugin.
     *
     * This will activate the plugin, then run the 'install' hook.
     *
     * @param Plugin $plugin Plugin to install.
     * @throws Omeka_Plugin_Exception | Omeka_Plugin_Loader_Exception
     * @return void
     */
    public function install(Plugin $plugin) 
    {
        if (!$plugin->getDirectoryName()) {
            throw new Omeka_Plugin_Installer_Exception(__('Plugin must have a valid directory name before it can be installed.'));
        }

        try {
            $plugin->setActive(true);            
            $plugin->setDbVersion($plugin->getIniVersion());
            $plugin->save();
            
            // Force the plugin to load.  Will throw exception if plugin cannot be loaded for some reason.
            if (!$plugin->isLoaded()) {
                $this->_loader->load($plugin, true);
            }
            
            //Now run the installer for the plugin
            $this->_broker->callHook('install', array('plugin_id' => $plugin->id), $plugin);
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
     * @throws Omeka_Plugin_Loader_Exception
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
