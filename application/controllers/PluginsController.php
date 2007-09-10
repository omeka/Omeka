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
		
		foreach ($list as $name) {
			
			$plugin = $this->getPluginMetaInfo($name);
			
			$plugin->installed = $broker->isInstalled($name);
			$plugin->active = $broker->isActive($name);
			
			$plugins[] = $plugin;
		}
		
		return $this->render('plugins/browse.php', compact('plugins'));
	}
	
	public function reinstallAction()
	{
		$name = $this->_getParam('name');
		$broker = Kea_Controller_Plugin_Broker::getInstance();		

		$record = $this->getTable('Plugin')->findByName($name);			
		
		if($record) {
			if(!class_exists($name)) {
				throw new Exception( 'Cannot find class with name "'.$name.'"' );
			}
			
			$plugin = $broker->$name;
		
			if(!$plugin) {
				$plugin = new $name(null, $record);
			}
			
			$record->delete();
			$plugin->uninstall();
			$this->_redirect('plugins/browse');			
		}

	}
	
	public function deleteAction() {$this->_redirect('/');}
	
	public function addAction() {$this->_redirect('/');}
}
?>