<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * @see Plugin.php
 */ 
require_once 'Plugin.php';

/**
 * @see Omeka_Controller_Action
 **/
require_once 'Omeka/Controller/Action.php';

/**
 * @package Omeka
 * @subpackage Controllers
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class PluginsController extends Omeka_Controller_Action
{
    public function init()
    {
        $this->_modelClass   = 'Plugin';
        $this->_pluginBroker = Omeka_Context::getInstance()->getPluginBroker();
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
        
        try {
            $config = $this->_pluginBroker->config($plugin);
        } catch (Exception $e) {
            $this->flashError($e->getMessage());
            $this->redirect->goto('config', null, null, array('name' => $plugin->getDisplayName()));    
        }
        
        // If the configuration function returns output, then we need to render 
        // that because it is a form
        if ($config !== null) {
            $this->view->assign(compact('config', 'plugin'));
        } else {
            if(!empty($_POST)) {
                $this->flashSuccess("The '" . $plugin->getDisplayName() . "' plugin was successfully configured!");
            }
            $this->redirect->goto('browse');    
        }
    }
    
    public function installAction()
    {
        // Create a new plugin with the name given by the POST 'name' param.
        $plugin = $this->_getPluginByName(true);
    
        if ($plugin->isInstalled()) {
            throw new Exception("'" . $plugin->getDisplayName() . "' plugin has already been installed.");
        }
             
        try {
            $this->_pluginInstaller->install($plugin);
            $this->flashSuccess("The '" . $plugin->getDisplayName() . "' plugin was successfully installed!");
            $this->redirect->goto('config', 'plugins', 'default', array('name'=>$plugin->getDirectoryName()));
        } catch (Exception $e) {
            // Taken from Plugin_Installer::install().  
            // "The '$pluginDirName' plugin cannot be installed because it requires other plugins to be installed, activated, and loaded. See below for details."
            
            $this->flashError("The following error occurred while installing the '" . $plugin->getDirectoryName() . "' plugin: " . $e->getMessage());
            $this->redirect->goto('browse');
        }
    }
    
    /**
     * Action to activate a plugin
     *
     * @return void
     **/
    public function activateAction()
    {        
        $plugin = $this->_getPluginByName();
        
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
             
        if ($plugin->isInstalled()) {   
            try {
                $this->_pluginInstaller->upgrade($plugin);
                $pluginDisplayName = $plugin->getDisplayName();
                $this->flashSuccess("The '$pluginDisplayName' plugin was successfully upgraded!");
                $this->redirect->goto('config', 'plugins', 'default', array('name'=>$plugin->getDirectoryName()));
            } catch (Exception $e) {
                $this->flashError("The following error occurred while upgrading the '$pluginDisplayName' plugin: " . $e->getMessage());
                $this->redirect->goto('browse');
            }
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

        $this->view->assign(array('plugins'=>$allPlugins, 'loader'=>$this->_pluginLoader));
    }
    
    /**
     * Action to uninstall a plugin
     *
     * @return void
     **/
    public function uninstallAction()
    {
        $plugin = $this->_getPluginByName();
        
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
    
    protected function _getPluginByName($create = false)
    {
        $pluginDirName = (string) $this->_getParam('name');
        if (!$pluginDirName) {
            $this->errorAction();
        }
        
        // Look for the plugin in the list of loaded plugins.
        if (!($plugin = $this->_pluginLoader->getPlugin($pluginDirName))) {
            if ($create) {
                $plugin = new Plugin;
                $plugin->name = $pluginDirName;
            } else {
                // As a failsafe, retrieve the plugin record from the database.
                // This code may be unnecessary / never used, not sure.
                $plugin = $this->getTable()->findByDirectoryName($pluginDirName);
            }
        }
                    
        if (!$plugin) {
            throw new Exception("The plugin in the directory '" . $pluginDirName . "' must be installed.");
        }
        $this->_pluginIniReader->load($plugin);
        
        return $plugin;
    }
}