<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * @package Omeka\Controller
 */
class PluginsController extends Omeka_Controller_AbstractActionController
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
            return $this->_helper->redirector('index');
        }
        
        $this->view->pluginBroker = $this->_pluginBroker;
        
        // If we have no config form hook, forget it.
        if (!$this->_pluginBroker->getHook($plugin, 'config_form') 
         || !$this->_pluginBroker->getHook($plugin, 'config')) {
            throw new RuntimeException(__('Error in configuring plugin named "%s". Missing config and/or config_form hook(s).', $plugin->getDisplayName()));
        }

        $csrf = new Omeka_Form_SessionCsrf;
        if ($this->getRequest()->isPost()) {
            if (!$csrf->isValid($_POST)) {
                $this->_helper->_flashMessenger(__('There was an error on the form. Please try again.'), 'error');
                $this->_helper->redirector('index');
                return;
            }

            try {
                $this->_pluginBroker->callHook('config', array('post' => $_POST), $plugin);
                $this->_helper->flashMessenger(
                    __('The %s plugin was successfully configured!', $plugin->getDisplayName()),
                    'success'
                );
                $this->_helper->redirector('index'); 
            } catch (Omeka_Validate_Exception $e) {
                $this->_helper->flashMessenger($e);
            }
        }
        $this->view->plugin = $plugin;
        $this->view->csrf = $csrf;
    }
    
    public function installAction()
    {
        if (!$this->getRequest()->isPost()) {
            return $this->_forward('method-not-allowed', 'error');
        }

        $csrf = new Omeka_Form_SessionCsrf;
        if (!$csrf->isValid($_POST)) {
            $this->_helper->_flashMessenger(__('There was an error on the form. Please try again.'), 'error');
            $this->_helper->redirector('index');
            return;
        }

        // Create a new plugin with the name given by the POST 'name' param.
        $plugin = $this->_getPluginByName(true);
    
        if ($plugin->isInstalled()) {
            $this->_helper->flashMessenger(
                __('The %s plugin has already been installed.', $plugin->getDisplayName()),
                'error'
            );
            $this->_helper->redirector('index');
            return;
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
        } catch (Omeka_Plugin_Installer_Exception $e) {
            // Taken from Plugin_Installer::install().  
            // "The '$pluginDirName' plugin cannot be installed because it requires other plugins to be installed, activated, and loaded. See below for details."
            $this->_helper->flashMessenger(
                __("The following error occurred while installing the %s plugin: ", $plugin->getDirectoryName()) . $e->getMessage(),
                'error'
            );
        } catch (Omeka_Plugin_Loader_Exception $e) {
            $this->_helper->flashMessenger(
                __("The following error occurred while installing the %s plugin: ", $plugin->getDirectoryName()) . $e->getMessage(),
                'error'
            );
        }
                
        $this->_helper->redirector('index');
    }
    
    /**
     * Action to activate a plugin
     *
     * @return void
     */
    public function activateAction()
    {
        if (!$this->getRequest()->isPost()) {
            return $this->_forward('method-not-allowed', 'error');
        }

        $this->_helper->redirector('index');

        $csrf = new Omeka_Form_SessionCsrf;
        if (!$csrf->isValid($_POST)) {
            $this->_helper->_flashMessenger(__('There was an error on the form. Please try again.'), 'error');
            return;
        }
        
        $plugin = $this->_getPluginByName();
        if (!$plugin) {
            return;
        }
        $name = $plugin->getDirectoryName();

        // Activate the plugin
        try {
           $this->_pluginInstaller->activate($plugin);
        } catch (Omeka_Plugin_Installer_Exception $e) {
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
        } catch (Omeka_Plugin_Loader_Exception $e) {
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
        if (!$this->getRequest()->isPost()) {
            return $this->_forward('method-not-allowed', 'error');
        }

        $this->_helper->redirector('index');

        $csrf = new Omeka_Form_SessionCsrf;
        if (!$csrf->isValid($_POST)) {
            $this->_helper->_flashMessenger(__('There was an error on the form. Please try again.'), 'error');
            return;
        }

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
        } catch (Omeka_Plugin_Installer_Exception $e) {
            $this->_helper->flashMessenger(
                __("The following error occurred while deactivating the %s plugin: ", $name) . $e->getMessage(),
                'error'
            );
        }
    }
    
    public function upgradeAction()
    {
        if (!$this->getRequest()->isPost()) {
            return $this->_forward('method-not-allowed', 'error');
        }

        $csrf = new Omeka_Form_SessionCsrf;
        if (!$csrf->isValid($_POST)) {
            $this->_helper->_flashMessenger(__('There was an error on the form. Please try again.'), 'error');
            $this->_helper->redirector('index');
            return;
        }

        $plugin = $this->_getPluginByName();

        if (!($plugin && $plugin->isInstalled())) {
            throw new Omeka_Controller_Exception_404;
        }

        $name = $plugin->getDirectoryName();
        
        try {
            $this->_pluginInstaller->upgrade($plugin);
            $this->_helper->flashMessenger(
                __("The %s plugin was successfully upgraded!", $name),
                'success');
            if ($this->_pluginBroker->getHook($plugin, 'config')) {
                $this->_helper->redirector('config', 'plugins', 'default', array('name' => $name));
            } else {
                $this->_helper->redirector('index');
            }
        } catch (Omeka_Plugin_Installer_Exception $e) {
            $this->_helper->flashMessenger(
                __("The following error occurred while upgrading the %s plugin: ", $name) . $e->getMessage(),
                'error'
            );
            $this->_helper->redirector('index');
        } catch (Omeka_Plugin_Loader_Exception $e) {
            $this->_helper->flashMessenger(
                __("The following error occurred while upgrading the %s plugin: ", $name) . $e->getMessage(),
                'error'
            );
            $this->_helper->redirector('index');
        }
    }
        
    /**
     * Action to browse plugins
     *
     * @return void
     */
    public function browseAction() 
    {
        // Get installed plugins, includes active and inactive.
        $installedPlugins = $this->_pluginLoader->getPlugins();
        
        // Get plugins that are not installed and load them.
        $factory = new Omeka_Plugin_Factory(PLUGIN_DIR);
        $uninstalledPlugins = $factory->getNewPlugins($installedPlugins);
        $this->_pluginLoader->loadPlugins($uninstalledPlugins);
        
        // Get the combination of installed and not-installed plugins.
        $allPlugins = $this->_pluginLoader->getPlugins();
        uksort($allPlugins, 'strnatcasecmp');
        
        // Filter the plugins.
        $allPlugins = apply_filters('browse_plugins', $allPlugins);
        
        // Set the plugins in the display order.
        $this->view->plugins = $allPlugins;
        $this->view->loader = $this->_pluginLoader;
        $this->view->plugin_count = count($allPlugins);
        $this->view->csrf = new Omeka_Form_SessionCsrf;
    }

    /**
     * Action to uninstall a plugin
     *
     * @return void
     */
    public function uninstallAction()
    {
        if (!$this->getRequest()->isPost()) {
            return $this->_forward('method-not-allowed', 'error');
        }

        $this->_helper->redirector('index');
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
            return;
        }

        $csrf = new Omeka_Form_SessionCsrf;

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
            $message = get_specific_plugin_hook_output($plugin, 'uninstall_message');
            
            $this->view->assign(compact('plugin', 'message', 'csrf'));
            // Cancel the redirect here.
            $this->getResponse()->clearHeader('Location')->setHttpResponseCode(200);
            $this->render('confirm-uninstall');
        } else {
            if (!$csrf->isValid($_POST)) {
                $this->_helper->_flashMessenger(__('There was an error on the form. Please try again.'), 'error');
                return;
            }

            // Attempt to uninstall the plugin.
            try {
                $this->_pluginInstaller->uninstall($plugin);
                $this->_helper->flashMessenger(
                    __("The %s plugin was successfully uninstalled!", $plugin->getDirectoryName()),
                    'success'
                );
            } catch (Omeka_Plugin_Installer_Exception $e) {
                $this->_helper->flashMessenger(
                    __("The following error occurred while uninstalling the %s plugin: ", $plugin->getDirectoryName()) . $e->getMessage(),
                    'error'
                );
            } catch (Omeka_Plugin_Loader_Exception $e) {
                $this->_helper->flashMessenger(
                    __("The following error occurred while uninstalling the %s plugin: ", $plugin->getDirectoryName()) . $e->getMessage(),
                    'error'
                );
            }
        }
    }
    
    public function deleteAction()
    {
        $this->_helper->redirector('index');
    }

    public function addAction()
    {
        $this->_helper->redirector('index');
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
