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
 * @package Omeka
 * @subpackage Controllers
 * @author CHNM
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class PluginsController extends Omeka_Controller_Action
{
    public function init()
    {
        $this->_helper->redirector->setExit(false);
        $this->_helper->db->setDefaultModelName('Plugin');
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
     */
    public function configAction()
    {
        $plugin = $this->_getPluginByName();
        if (!$plugin) {
            return $this->_helper->redirector('browse');
        }
        
        $this->view->pluginBroker = $this->_pluginBroker;
        
        // If we have no config form hook, forget it.
        if (!$this->_pluginBroker->getHook($plugin, 'config_form') 
         || !$this->_pluginBroker->getHook($plugin, 'config')) {
            throw new RuntimeException(__('Error in configuring plugin named "%s". Missing config and/or config_form hook(s).', $plugin->getDisplayName()));
        }
        
        if ($this->getRequest()->isPost()) {
            try {
                $this->_pluginBroker->callHook('config', array($_POST), $plugin);
                $this->_helper->flashMessenger(
                    __('The %s plugin was successfully configured!', $plugin->getDisplayName()),
                    'success'
                );
                $this->_helper->redirector('browse'); 
            } catch (Omeka_Validator_Exception $e) {
                $this->_helper->flashMessenger($e);
            }
        }
        $this->view->plugin = $plugin;
    }
    
    public function installAction()
    {
        // Create a new plugin with the name given by the POST 'name' param.
        $plugin = $this->_getPluginByName(true);
    
        if ($plugin->isInstalled()) {
            $this->_helper->flashMessenger(
                __('The %s plugin has already been installed.', $plugin->getDisplayName()),
                'error'
            );
            $this->_helper->redirector('browse');
        }
             
        try {
            $this->_pluginInstaller->install($plugin);
            $this->_helper->flashMessenger(
                __('The %s plugin was successfully installed!', $plugin->getDisplayName()),
                'success'
            );
            
            // Only redirect to the config form if there is a config hook for this plugin.
            if ($this->_pluginBroker->getHook($plugin, 'config')) {
                return $this->_helper->redirector('config', 'plugins', 'default', array('name'=>$plugin->getDirectoryName()));
            }
        } catch (Exception $e) {
            // Taken from Plugin_Installer::install().  
            // "The '$pluginDirName' plugin cannot be installed because it requires other plugins to be installed, activated, and loaded. See below for details."
            
            $this->_helper->flashMessenger(
                __("The following error occurred while installing the %s plugin: ", $plugin->getDirectoryName()) . $e->getMessage(),
                'error'
            );
        }
        
        $this->_helper->redirector('browse');
    }
    
    /**
     * Action to activate a plugin
     *
     * @return void
     */
    public function activateAction()
    {
        $this->_helper->redirector('browse');
        
        $plugin = $this->_getPluginByName();
        if (!$plugin) {
            return;
        }
        $name = $plugin->getDirectoryName();

        // Activate the plugin
        try {
           $this->_pluginInstaller->activate($plugin);
        } catch (Exception $e) {
            $this->_helper->flashMessenger(
                __("The following error occurred while activating the %s plugin: ", $name) . $e->getMessage(),
                'error'
            );
            return;
        }
        
        // check to make sure the plugin can be loaded.
        try {
            $this->_pluginLoader->load($plugin, true);
            $this->_helper->flashMessenger(
                __("The %s plugin was successfully activated!", $name),
                'success'
            );
        } catch (Exception $e) {
            $this->_helper->flashMessenger(
                __("The %s plugin was activated, but could not be loaded: ", $name) . $e->getMessage(),
                'error'
            );
        }
    }
    
    /**
     * Action to deactivate a plugin
     *
     * @return void
     */
    public function deactivateAction()
    {
        $this->_helper->redirector('browse');
        $plugin = $this->_getPluginByName();
        if (!$plugin) {
            return;
        }
        $name = $plugin->getDirectoryName();
        
        // Deactivate the plugin
        try {
            $this->_pluginInstaller->deactivate($plugin);
            $this->_helper->flashMessenger(
                __("The %s plugin was successfully deactivated!", $name),
                'success'
            );
        } catch (Exception $e) {
            $this->_helper->flashMessenger(
                __("The following error occurred while deactivating the %s plugin: ", $name) . $e->getMessage(),
                'error'
            );
        }
    }
    
    public function upgradeAction()
    {
        $this->_helper->redirector('browse');
        $plugin = $this->_getPluginByName();
        if (!$plugin) {
            return;
        }
             
        if (!$plugin->isInstalled()) {
            return;
        }

        $name = $plugin->getDirectoryName();
        
        try {
            $this->_pluginInstaller->upgrade($plugin);
            $this->_helper->flashMessenger(
                __("The %s plugin was successfully upgraded!", $name),
                'success');
            if ($this->_pluginBroker->getHook($plugin, 'config')) {
                $this->_helper->redirector('config', 'plugins', 'default', array('name' => $name));
            }
        } catch (Exception $e) {
            $this->_helper->flashMessenger(
                __("The following error occurred while upgrading the %s plugin: ", $name) . $e->getMessage(),
                'error'
            );
        }
    }
        
    /**
     * Action to browse plugins
     *
     * @return void
     */
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

        $this->view->assign(array('plugins'=>$allPlugins, 'loader'=>$this->_pluginLoader));
    }
    
    /**
     * Action to uninstall a plugin
     *
     * @return void
     */
    public function uninstallAction()
    {
        $this->_helper->redirector('browse');
        $plugin = $this->_getPluginByName();
        if (!$plugin) {
            return;
        }
        
        // Check to see if the plugin exists and is installed.
        if (!$plugin->isInstalled()) {
            $this->_helper->flashMessenger(
                __("The plugin could not be found in the '%s' directory!", $plugin->getDirectoryName()),
                'error'
            );
        }
        
        // Confirm the uninstall.
        if (!$this->_getParam('confirm')) {
            if ($this->_getParam('uninstall-confirm')) {
                $this->_helper->flashMessenger(
                    __("You must confirm the uninstall before proceeding."),
                    'error'
                );
            }
            
            // Call the append to uninstall message hook for the specific 
            // plugin, if it exists.
            $message = get_specific_plugin_hook_output($plugin, 'admin_append_to_plugin_uninstall_message');
            
            $this->view->assign(compact('plugin', 'message'));
            // Cancel the redirect here.
            $this->getResponse()->clearHeader('Location');
            $this->render('confirm-uninstall');
        } else {
            // Attempt to uninstall the plugin.
            try {
                $this->_pluginInstaller->uninstall($plugin);
                $this->_helper->flashMessenger(
                    __("The %s plugin was successfully uninstalled!", $plugin->getDirectoryName()),
                    'success'
                );
            } catch (Exception $e) {
                $this->_helper->flashMessenger(
                    __("The following error occurred while uninstalling the %s plugin: ", $plugin->getDirectoryName()) . $e->getMessage(),
                    'error'
                );
            }
        }
    }
    
    public function deleteAction()
    {
        $this->_helper->redirector('browse');
    }

    public function addAction()
    {
        $this->_helper->redirector('browse');
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
            $this->_helper->flashMessenger(__("No plugin name given."), 'error');
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
            $this->_helper->flashMessenger(__("The plugin %s must be installed.", $pluginDirName), 'error');
            return false;
        }
        $this->_pluginIniReader->load($plugin);
        
        return $plugin;
    }
}
