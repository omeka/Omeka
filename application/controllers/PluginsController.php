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
        $pluginDirName = (string) $this->_getParam('name');      
        if (!$pluginDirName) {
            $this->errorAction();
        }
        
        // get the plugin info for the plugin to configure
        $pluginInfo = $this->_getPluginInfo($pluginDirName);
        
        $broker = $this->_pluginBroker;
        try {
            $config = $broker->config($pluginDirName);
        } catch (Exception $e) {
            $this->flashError($e->getMessage());
            $this->redirect->goto('config', null, null, array('name' => $pluginDirName));    
        }
        
        // If the configuration function returns output, then we need to render 
        // that because it is a form
        if ($config !== null) {
            $this->view->assign(compact('config', 'pluginInfo'));
        } else {
            if(!empty($_POST)) {
                $this->flashSuccess("The '$pluginDirName' plugin was successfully configured!");
            }
            $this->redirect->goto('browse');    
        }
    }
    
    public function installAction()
    {
        $pluginDirName = (string) $this->_getParam('name');
        if (!$pluginDirName) {
            $this->errorAction();
        }
        
        // get the plugin info for the plugin to install
        $pluginInfo = $this->_getPluginInfo($pluginDirName);
        
        $broker = $this->_pluginBroker;       
        if (!$broker->isInstalled($pluginDirName)) {
            try {
                $broker->install($pluginDirName);
                $this->flashSuccess("The '" . $pluginInfo->name . "' plugin was successfully installed!");
                $this->redirect->goto('config', 'plugins', 'default', array('name'=>$pluginDirName));
            } catch (Exception $e) {
                $this->flashError("The following error occurred while installing the '" . $pluginInfo->name . "' plugin: " . $e->getMessage());
                $this->redirect->goto('browse');
            }
        }
    }
    
    /**
     * Action to activate a plugin
     *
     * @return void
     **/
    public function activateAction()
    {
        $broker = $this->_pluginBroker;
        
        $pluginDirName = (string) $this->_getParam('name');
        if (!$pluginDirName) {
            $this->errorAction();
        }
        
        // Get the plugin info for the plugin to activate
        $pluginInfo = $this->_getPluginInfo($pluginDirName);
        
        // Activate the plugin
        try {
           $broker->activate($pluginDirName);
           
           // check to make sure the plugin can be loaded
           $broker->load($pluginDirName);
           if ($broker->isLoaded($pluginDirName)) {
               $this->flashSuccess("The '" . $pluginInfo->name . "' plugin was successfully activated!");
           } else {
               $this->flashError("The '" . $pluginInfo->name . "' plugin was activated, but could not be loaded.  See the plugin below for details.");
           }
        } catch (Exception $e) {
            $this->flashError("The following error occurred while activating the '" . $pluginInfo->name . "' plugin: " . $e->getMessage());
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
        $pluginDirName = (string) $this->_getParam('name');
        if (!$pluginDirName) {
            $this->errorAction();
        }
                
        // Get the plugin info for the plugin to deactivate
        $pluginInfo = $this->_getPluginInfo($pluginDirName);
        
        // Deactivate the plugin
        try {
           $this->_pluginBroker->deactivate($pluginDirName);
           $this->flashSuccess("The '" . $pluginInfo->name . "' plugin was successfully deactivated!");
        } catch (Exception $e) {
            $this->flashError("The following error occurred while deactivating the '" . $pluginInfo->name . "' plugin: " . $e->getMessage());
        }
            
        $this->redirect->goto('browse');
    }
    
    public function upgradeAction()
    {
        $pluginDirName = (string) $this->_getParam('name');
        if (!$pluginDirName) {
            $this->errorAction();
        }
        
        $broker = $this->_pluginBroker;       
        if ($broker->isInstalled($pluginDirName)) {   
            try {
                $broker->upgrade($pluginDirName);
                $this->flashSuccess("The '$pluginDirName' plugin was successfully upgraded!");
                $this->redirect->goto('config', 'plugins', 'default', array('name'=>$pluginDirName));
            } catch (Exception $e) {
                $this->flashError("The following error occurred while upgrading the '$pluginDirName' plugin: " . $e->getMessage());
                $this->redirect->goto('browse');
            }
        }
    }
    
    /**
     * Retrieve the descriptive information for a plugin from its plugin.ini file, 
     * the database, and the plugin broker
     *
     * @param string $pluginDirName
     * @return stdClass
     **/
    private function _getPluginInfo($pluginDirName)
    {
        $pluginInfo = new stdClass;
        
        $pluginIniPath = $this->_pluginBroker->getPluginIniFilePath($pluginDirName);      
        if (file_exists($pluginIniPath)) {
            try {
                $config = new Zend_Config_Ini($pluginIniPath, 'info');
	            foreach ($config as $key => $value) {
	                switch($pluginInfo->$key) {
	                    case 'name':
	                    case 'author':
	                    case 'description':
	                    case 'omeka_minimum_version':
	                    case 'omeka_tested_up_to':
	                    case 'version':
	                    case 'required_plugins':
	                    case 'optional_plugins':
	                    case 'tags':
	                        $pluginInfo->$key = (string) $value;
	                        break;
	                    default:
	                        $pluginInfo->$key = $value;
	                        break;
	                }
	            }
            } catch (Zend_Config_Exception $e) {}        
        }

        // if the plugin.ini doees not specify the plugin name, 
        // make the plugin name the same as the plugin directory name  
        if (!$pluginInfo || trim($pluginInfo->name) == '') {
            $pluginInfo->name = $pluginDirName;
        }
        

        
        $pluginInfo->directoryName = $pluginDirName;            
        $pluginInfo->hasConfig = (bool) $this->_pluginBroker->getHook($pluginDirName, 'config');
        $pluginInfo->installed = $this->_pluginBroker->isInstalled($pluginDirName);
        $pluginInfo->active = $this->_pluginBroker->isActive($pluginDirName);
        $pluginInfo->loaded = $this->_pluginBroker->isLoaded($pluginDirName);
        
        $pluginInfo->hasPluginFile = $this->_pluginBroker->hasPluginFile($pluginDirName);
        $pluginInfo->hasPluginIniFile = $this->_pluginBroker->hasPluginIniFile($pluginDirName);
        $pluginInfo->hasNewVersion = $this->_pluginBroker->hasNewVersion($pluginDirName);        
        $pluginInfo->requiredPluginDirNames = $this->_pluginBroker->getRequiredPluginDirNames($pluginDirName);
        $pluginInfo->optionalPluginDirNames = $this->_pluginBroker->getOptionalPluginDirNames($pluginDirName);
        $pluginInfo->meetsOmekaMinimumVersion = $this->_pluginBroker->meetsOmekaMinimumVersion($pluginDirName);
        $pluginInfo->meetsOmekaTestedUpTo = $this->_pluginBroker->meetsOmekaTestedUpTo($pluginDirName);

        return $pluginInfo;
    }
    
    
    /**
     * Action to browse plugins
     *
     * @return void
     **/
    public function browseAction() 
    {
        //Get a list of all the plugins        
        $allPluginNames = $this->_pluginBroker->getAll();
		natsort($allPluginNames);
		
        $pluginInfos = array();
        foreach ($allPluginNames as $pluginDirName) {
            $pluginInfos[$pluginDirName] = $this->_getPluginInfo($pluginDirName);;
        }
        
        $this->view->assign(compact('pluginInfos'));
    }
    
    /**
     * Action to uninstall a plugin
     *
     * @return void
     **/
    public function uninstallAction()
    {
        $pluginDirName = (string) $this->_getParam('name');
        
        $broker = $this->_pluginBroker;
        
        // Check to see if the plugin exists and is installed.
        if (!$broker || !$broker->isInstalled($pluginDirName)) {
            $this->flashError("The plugin could not be found in the '$pluginDirName' directory!");
            $this->redirect->goto('browse');
        }

        // get the plugin info for the plugin to uninstall
        $pluginInfo = $this->_getPluginInfo($pluginDirName);
        
        // Confirm the uninstall.
        if (!$this->_getParam('confirm')) {
            
            if ($this->_getParam('uninstall-confirm')) {
                $this->flashError("You must confirm the uninstall before proceeding.");
            }
            
            // Call the append to uninstall message hook for the specific 
            // plugin, if it exists.
            $message = get_specific_plugin_hook_output($pluginDirName, 'admin_append_to_plugin_uninstall_message');
            
            $this->view->assign(compact('pluginInfo', 'message'));
            $this->render('confirm-uninstall');
        
        } else {
            
            // Attempt to uninstall the plugin.
            try {
                $broker->uninstall($pluginDirName);
                $this->flashSuccess("The '" . $pluginInfo->name . "' plugin was successfully uninstalled!");
            } catch (Exception $e) {
                $this->flashError("The following error occurred while uninstalling the '" . $pluginInfo->name . "' plugin: " . $e->getMessage());
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
}