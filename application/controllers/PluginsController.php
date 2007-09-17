<?php
require_once 'Plugin.php';
/**
 * @package Omeka
 **/
require_once 'Kea/Controller/Action.php';
class PluginsController extends Kea_Controller_Action
{
	
	protected $_redirects = array(
		'install' => array('plugins/install/name', array('name'))
	);
	
	public function init()
	{
		$this->_modelClass = 'Plugin';
		$this->_table = $this->getTable('Plugin');
	}
	
	public function activateAction()
	{
		//Get the plugin record, toggle its status and save it back
		$plugin = Doctrine_Manager::getInstance()->getTable('Plugin')->findByName($_POST['activate']);
		
		echo $plugin;
		
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
		
		return $info;
	}
	
	public function browseAction() {
		fire_plugin_hook('install');
		
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