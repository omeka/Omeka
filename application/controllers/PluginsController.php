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
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class PluginsController extends Omeka_Controller_Action
{
	public function init()
	{
		$this->_modelClass = 'Plugin';
		$this->_pluginBroker = Omeka_Context::getInstance()->getPluginBroker();
	}
	
	/**
	 * Load the configuration form for a specific plugin.  
	 * That configuration form will be POSTed back to this URL and processed by the plugin.
	 *
	 * @return void
	 **/
	public function configAction()
	{
		$plugin = $this->_getParam('name');

		$broker = $this->_pluginBroker;
		
		if(!$plugin) {
			$this->errorAction();
		}
		
		$config = $broker->config($plugin);
		
		//If the configuration function returns output, then we need to render that because it is a form
		if($config !== null) {
			return $this->render(compact('config', 'plugin'));
		}
		else {
			$this->flashSuccess('Plugin configuration successfully changed!');
			$this->redirect->goto('browse');	
		}
	}
	
	public function installAction()
	{
		$plugin = $this->_getParam('name');

		if(!$plugin) $this->errorAction();
		
		$broker = $this->_pluginBroker;

		if(!$broker->isInstalled($plugin)) {

			$config = $broker->install($plugin);
			
			if($config !== null) {
				return $this->render(compact('config', 'plugin'));
			}
			else {
				$this->flashSuccess("Plugin named '$plugin' was successfully installed!");
				$this->redirect->goto('browse');
			}			
		}
	}
	
	public function activateAction()
	{
		//Get the plugin record, toggle its status and save it back
		$plugin = $this->getTable()->findBySql('name = ?', array($_POST['activate']), true );
			
		//Toggle!
		$plugin->active = !($plugin->active);
		
		$plugin->save();
		
		$this->redirect->goto('browse');
	}
	
	/**
	 * Retrieve the descriptive info for a plugin from its plugin.ini file
	 *
	 * @return stdClass
	 **/
	public function getPluginMetaInfo($plugin)
	{		
		$info = new stdClass;
		
		$info->directory = $plugin;
		
		$path = PLUGIN_DIR . DIRECTORY_SEPARATOR . $plugin . DIRECTORY_SEPARATOR . 'plugin.ini';
		
		if(file_exists($path)) {
			$config = new Zend_Config_Ini($path, 'info');
			
			foreach ($config as $key => $value) {
				$info->$key = $value;
			}
			
			
		}
					
		$info->has_config = (bool) $this->_pluginBroker->getHook($plugin, 'config');
		
		return $info;
	}
	
	public function browseAction() {
		//Get a list of all the plugins
		
		$broker = $this->_pluginBroker;
		
		$list = $broker->getAll();
		
		$plugins = array();
		
		foreach ($list as $name) {
			
			$plugin = $this->getPluginMetaInfo($name);
			
			$plugin->installed = $broker->isInstalled($name);
			$plugin->active = $broker->isActive($name);
			
			$plugins[] = $plugin;
		}
				
		return $this->render(compact('plugins'));
	}
	
	public function uninstallAction()
	{
	    $plugin = (string) $this->_getParam('name');
	    $broker = $this->_pluginBroker;
	    if($broker and $broker->isInstalled($plugin)) {
	        $broker->uninstall($plugin);
	        $this->flashSuccess("Plugin named '$plugin' was successfully uninstalled!");
	    }
	    else {
	        $this->flash("Plugin named '$plugin' could not be found!");
	    }
	    
	    $this->redirect->goto('browse');
	}
	
	public function deleteAction() {$this->redirect->goto('browse');}
	
	public function addAction() {$this->redirect->goto('browse');}
}