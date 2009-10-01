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
    protected $_iniReader;
    
    /**
     * @todo Refactor all methods to accept an instance of Plugin record.
     */
    public function __construct(Omeka_Plugin_Broker $broker, 
                                Omeka_Plugin_Loader $loader, 
                                Omeka_Plugin_Ini $iniReader)
    {
        $this->_broker = $broker;
        $this->_loader = $loader;
        $this->_iniReader = $iniReader;
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
        // Why is this line necessary?  Commented until whenever that becomes clear.
        // $this->_active[$pluginDirName] = $pluginDirName;
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
        // See same comment in activate().
        // unset($this->_active[$pluginDirName]);
    }
    
    /**
     * Upgrades the plugin
     *
     * @param string $pluginDirName
     * @return void
     **/
    public function upgrade(Plugin $plugin)
    {   
        $pluginDirName = $plugin->name;
        
        if (!$this->_loader->hasNewVersion($pluginDirName)) {
            throw new Exception("The '$pluginDirName' plugin must be installed and have newer files to upgrade it.");
        }
        
        // activate the plugin for the remainder of the request, 
        // so that it can be loaded
        $this->_active[$pluginDirName] = $pluginDirName;
        
        // remove the plugin name from the plugins that have new version for the remainder of the request
        // so that it can load the new plugin
        unset($this->_has_new_version[$pluginDirName]);
        
        // load the plugin files
        $this->_loader->load($pluginDirName);
        
        if (!$this->_loader->isLoaded($pluginDirName)) {
            throw new Exception("The '$pluginDirName' plugin cannot be upgraded because it needs all of its required plugins installed, activated, and loaded.");
        }

        // let the plugin do the upgrade
        $oldPluginVersion = $plugin->version;
        $newPluginVersion = (string)$this->_iniReader->getPluginIniValue($pluginDirName, 'version');            

        // run the upgrade function in the plugin
        $upgrade_hook = $this->_broker->getHook($pluginDirName, 'upgrade');
        call_user_func_array($upgrade_hook, array($oldPluginVersion, $newPluginVersion));            

        // update version of the plugin and activate it
        $plugin->version = $newPluginVersion;
        $plugin->forceSave();

        // activate the plugin
        $this->activate($plugin);
    }
    
    /**
     * Installs the plugin
     *
     * @param string $pluginDirName
     * @return void
     **/
    public function install(Plugin $plugin) 
    {
        if (!$plugin->name) {
            throw new Exception("Plugin must have a valid directory name before it can be installed.");
        }
        
        $pluginDirName = $plugin->name;
        
        // install and activate the plugin for the remainder of the request, 
        // so that it can be loaded
        $this->_loader->setInstalled($pluginDirName);
        $this->_loader->setActive($pluginDirName);
        
        // Force the plugin to load.  Will throw exception if plugin cannot be loaded for some reason.
        $this->_loader->load($pluginDirName, true);

        try {            
            $plugin->active = 1;
            if ($this->_iniReader->hasPluginIniFile($pluginDirName)) {
                $plugin->version = (string)$this->_iniReader->getPluginIniValue($pluginDirName, 'version');
            } else {
                $plugin->version = '';
            }
            $plugin->forceSave();
    
            //Now run the installer for the plugin
            $install_hook = $this->_broker->getHook($pluginDirName, 'install');
            call_user_func_array($install_hook, array($plugin->id));
               
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
        $pluginDirName = $plugin->name;
                
        // activate the plugin for the remainder of the request, 
        // so that it can be loaded
        $this->_active[$pluginDirName] = $pluginDirName;
        
        // load the plugin files
        $this->_loader->load($pluginDirName);
        
        if (!$this->_loader->isLoaded($pluginDirName)) {
            throw new Exception("The '$pluginDirName' plugin cannot be uninstalled because it needs all of its required plugins installed, activated, and loaded.");
        }
        
        $uninstallHook = $this->_broker->getHook($pluginDirName, 'uninstall');
        if ($uninstallHook) {
            call_user_func($uninstallHook);
        }
        
        $plugin->delete();
    }
}
