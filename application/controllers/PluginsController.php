<?php
require_once 'Plugin.php';
/**
 * @package Omeka
 **/
require_once 'Omeka/Controller/Action.php';
class PluginsController extends Omeka_Controller_Action
{
	
	protected $_redirects = array(
		'install' => array('plugins/install/name', array('name'))
	);
	
	public function init()
	{
		$this->_modelClass = 'Plugin';
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

		$broker = get_plugin_broker();
		
		if(!$plugin) {
			$this->errorAction();
		}
		
		$config = $broker->config($plugin);
		
		//If the configuration function returns output, then we need to render that because it is a form
		if($config !== null) {
			return $this->render('plugins/config.php', compact('config', 'plugin'));
		}
		else {
			$this->flashSuccess('Plugin configuration successfully changed!');
			$this->_redirect('plugins/browse');	
		}
	}
	
	public function installAction()
	{
		$plugin = $this->_getParam('name');

		if(!$plugin) $this->errorAction();
		
		$broker = get_plugin_broker();

		if(!$broker->isInstalled($plugin)) {

			$config = $broker->install($plugin);
			
			if($config !== null) {
				return $this->render('plugins/config.php', compact('config', 'plugin'));
			}
			else {
				$this->flashSuccess("Plugin named '$plugin' was successfully installed!");
				$this->_redirect('plugins/browse');
			}			
		}
	}
	
	public function activateAction()
	{
		//Get the plugin record, toggle its status and save it back
		$plugin = get_db()->getTable('Plugin')->findBySql('name = ?', array($_POST['activate']), true );
				
		//Toggle!
		$plugin->active = !($plugin->active);
		
		$plugin->save();
		
		$this->_redirect('plugins');
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
		
		$broker = get_plugin_broker();
			
		$info->has_config = (bool) $broker->getHook($plugin, 'config');
		
		return $info;
	}
	
	public function browseAction() {
		$new_plugins = get_plugin_broker()->getNew();

		if(count($new_plugins)) {
			$plugin_to_install = array_pop($new_plugins);
			
			$this->_setParam('name', $plugin_to_install);
			
			//Run the config action with the installer turned on
			return $this->installAction();
		}
				
		//Get a list of all the plugins
		
		$broker = get_plugin_broker();
		
		$list = $broker->getAll();
		
		$plugins = array();
		
		foreach ($list as $name) {
			
			$plugin = $this->getPluginMetaInfo($name);
			
			$plugin->installed = $broker->isInstalled($name);
			$plugin->active = $broker->isActive($name);
			
			$plugins[] = $plugin;
		}
		
		return $this->render('plugins/browse.php', compact('plugins'));
	}
	
	public function deleteAction() {$this->_redirect('/');}
	
	public function addAction() {$this->_redirect('/');}
}
?>