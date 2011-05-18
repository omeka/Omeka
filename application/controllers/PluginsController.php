<?php
/**
 * @version $Id$
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 **/

/**
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @package Omeka
 * @subpackage Controllers
 * @author CHNM
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 **/
class PluginsController extends Omeka_Controller_Action
{
    public function init()
    {
        $this->_modelClass   = 'Plugin';
        $this->_pluginBroker = $this->getInvokeArg('bootstrap')->getResource('Pluginbroker');
        $this->_pluginLoader = Zend_Registry::get('pluginloader');
        $this->_pluginIniReader = Zend_Registry::get('plugin_ini_reader');
        $this->_pluginInstaller = new Omeka_Plugin_Installer(
                                    $this->_pluginBroker, 
                                    $this->_pluginLoader);
    }
    
    /**
     * Load the configuration form for a specific plugin.  
     * That configuration form will be POSTed back to this URL and processed by 
     * the plugin.
     *
     * @return void
     **/
    public function configAction()
    {
        $plugin = $this->_getPluginByName();
        if (!$plugin) {
            return $this->_helper->redirector->goto('browse');
        }
        
        $this->view->pluginBroker = $this->_pluginBroker;
        
        // If we have no config form hook, forget it.
        if (!$this->_pluginBroker->getHook($plugin, 'config_form') 
         || !$this->_pluginBroker->getHook($plugin, 'config')) {
            throw new RuntimeException("Error in configuring plugin named '" . $plugin->getDisplayName() . "': Missing config and/or config_form hook(s).");
        }
        
        if ($this->getRequest()->isPost()) {
            try {
                $this->_pluginBroker->callHook('config', array($_POST), $plugin);
                $this->flashSuccess("The '" . $plugin->getDisplayName() . "' plugin was successfully configured!");
                $this->redirect->goto('browse'); 
            } catch (Omeka_Validator_Exception $e) {
                $this->flashValidationErrors($e);
            }
        }
        $this->view->plugin = $plugin;
    }
    
    public function installAction()
    {
        // Create a new plugin with the name given by the POST 'name' param.
        $plugin = $this->_getPluginByName(true);
    
        if ($plugin->isInstalled()) {
            $this->flashError("'" . $plugin->getDisplayName() . "' plugin has already been installed.");
            $this->_helper->redirector->goto('browse');
        }
             
        try {
            $this->_pluginInstaller->install($plugin);
            $this->flashSuccess("The '" . $plugin->getDisplayName() . "' plugin was successfully installed!");
            
            // Only redirect to the config form if there is a config hook for this plugin.
            if ($this->_pluginBroker->getHook($plugin, 'config')) {
                return $this->_helper->redirector->goto('config', 'plugins', 'default', array('name'=>$plugin->getDirectoryName()));
            }
        } catch (Exception $e) {
            // Taken from Plugin_Installer::install().  
            // "The '$pluginDirName' plugin cannot be installed because it requires other plugins to be installed, activated, and loaded. See below for details."
            
            $this->flashError("The following error occurred while installing the '" . $plugin->getDirectoryName() . "' plugin: " . $e->getMessage());
        }
        
        $this->_helper->redirector->goto('browse');
    }
    
    /**
     * Action to activate a plugin
     *
     * @return void
     **/
    public function activateAction()
    {        
        $plugin = $this->_getPluginByName();
        if (!$plugin) {
            return $this->_helper->redirector->goto('browse');
        }

        // Activate the plugin
        try {
           $this->_pluginInstaller->activate($plugin);
           
           // check to make sure the plugin can be loaded.
           try {
               $this->_pluginLoader->load($plugin, true);
               $this->flashSuccess("The '" . $plugin->getDirectoryName() . "' plugin was successfully activated!");
           } catch (Exception $e) {
               $this->flashError("The '" . $plugin->getDirectoryName() . "' plugin was activated, but could not be loaded: " . $e->getMessage());
           }
        } catch (Exception $e) {
            $this->flashError("The following error occurred while activating the '" . $plugin->getDirectoryName() . "' plugin: " . $e->getMessage());
        }
            
        $this->redirect->goto('browse');
    }
    
    /**
     * Action to deactivate a plugin
     *
     * @return void
     **/
    public function deactivateAction()
    {
        $plugin = $this->_getPluginByName();
        if (!$plugin) {
            return $this->_helper->redirector->goto('browse');
        }
        
        // Deactivate the plugin
        try {
           $this->_pluginInstaller->deactivate($plugin);
           $this->flashSuccess("The '" . $plugin->getDirectoryName() . "' plugin was successfully deactivated!");
        } catch (Exception $e) {
            $this->flashError("The following error occurred while deactivating the '" . $plugin->getDirectoryName() . "' plugin: " . $e->getMessage());
        }
            
        $this->redirect->goto('browse');
    }
    
    public function upgradeAction()
    {
        $plugin = $this->_getPluginByName();
        if (!$plugin) {
            return $this->_helper->redirector->goto('browse');
        }
             
        if ($plugin->isInstalled()) {   
            try {
                $this->_pluginInstaller->upgrade($plugin);
                $pluginDisplayName = $plugin->getDisplayName();
                $this->flashSuccess("The '$pluginDisplayName' plugin was successfully upgraded!");
                if ($this->_pluginBroker->getHook($plugin, 'config')) {
                    return $this->redirect->goto('config', 'plugins', 'default', array('name'=>$plugin->getDirectoryName()));
                }
            } catch (Exception $e) {
                $this->flashError("The following error occurred while upgrading the '$pluginDisplayName' plugin: " . $e->getMessage());
            }
            
            $this->redirect->goto('browse');
        }
    }
        
    /**
     * Action to browse plugins
     *
     * @return void
     **/
    public function browseAction() 
    {
        // Get a list of all plugins currently processed by the system.      
        $installedPlugins = $this->_pluginLoader->getPlugins();
        $pluginFactory = new Omeka_Plugin_Factory(PLUGIN_DIR);
        $newPlugins = $pluginFactory->getNewPlugins($installedPlugins);
        $this->_pluginLoader->loadPlugins($newPlugins);
        $allPlugins = $this->_pluginLoader->getPlugins();
        // Plugins are keyed to the directory name, so natural sort based on that.
        uksort($allPlugins, "strnatcasecmp");
        $allPlugins = apply_filters('browse_plugins', $allPlugins);
        
        $config = Omeka_Context::getInstance()->getConfig('basic');
        if (isset($config->plugin->versionCheck)) {
            $versionCheck = (boolean) $config->plugin->versionCheck;
        } else {
            $versionCheck = true;
        }



        $this->view->assign(array('plugins'=>$allPlugins, 'loader'=>$this->_pluginLoader, 'versionCheck'=>$versionCheck));
    }
    
    /**
     * Action to uninstall a plugin
     *
     * @return void
     **/
    public function uninstallAction()
    {
        $plugin = $this->_getPluginByName();
        if (!$plugin) {
            return $this->_helper->redirector->goto('browse');
        }
        
        // Check to see if the plugin exists and is installed.
        if (!$plugin->isInstalled()) {
            $this->flashError("The plugin could not be found in the '" . $plugin->getDirectoryName() . "' directory!");
            $this->redirect->goto('browse');
        }
        
        // Confirm the uninstall.
        if (!$this->_getParam('confirm')) {
            
            if ($this->_getParam('uninstall-confirm')) {
                $this->flashError("You must confirm the uninstall before proceeding.");
            }
            
            // Call the append to uninstall message hook for the specific 
            // plugin, if it exists.
            $message = get_specific_plugin_hook_output($plugin, 'admin_append_to_plugin_uninstall_message');
            
            $this->view->assign(compact('plugin', 'message'));
            $this->render('confirm-uninstall');
        
        } else {
            
            // Attempt to uninstall the plugin.
            try {
                $this->_pluginInstaller->uninstall($plugin);
                $this->flashSuccess("The '" . $plugin->getDirectoryName() . "' plugin was successfully uninstalled!");
            } catch (Exception $e) {
                $this->flashError("The following error occurred while uninstalling the '" . $plugin->getDirectoryName() . "' plugin: " . $e->getMessage());
                $this->redirect->goto('browse');
            }
            $this->redirect->goto('browse');
        }
    }
    
    public function deleteAction()
    {
        $this->redirect->goto('browse');
    }

    public function addAction()
    {
        $this->redirect->goto('browse');
    }
    
    /**
     * Retrieve the Plugin record based on the name passed via the request.
     *
     * @param boolean $create Whether or not the plugin object should be 
     * created if it has not already been loaded.  
     */
    protected function _getPluginByName($create = false)
    {
        $pluginDirName = (string) $this->_getParam('name');
        if (!$pluginDirName) {
            $this->flashError("No plugin name given.");
            return false;
        }
        
        // Look for the plugin in the list of loaded plugins.        
        if (!($plugin = $this->_pluginLoader->getPlugin($pluginDirName))) {            
            if ($create) {
                $plugin = new Plugin;
                $plugin->name = $pluginDirName;
            } 
        }
                    
        if (!$plugin) {
            $this->flashError("The plugin '" . $pluginDirName . "' must be installed.");
            return false;
        }
        $this->_pluginIniReader->load($plugin);
        
        return $plugin;
    }
}
