<?php
/**
 * @package Omeka
 **/
require_once 'Kea/Controller/Action.php';
class PluginsController extends Kea_Controller_Action
{
	public function init()
	{
		$this->_modelClass = 'Plugin';
		$this->_table = $this->getTable('Plugin');
	}
	
	public function browseAction() {
		$this->installNew();
		
		return parent::browseAction();
	}
	
	protected function installNew() {
		//Installation will need to create new tables
		Doctrine_Manager::getInstance()->setAttribute(Doctrine::ATTR_CREATE_TABLES, true);
		
		$router = Kea_Controller_Front::getInstance()->getRouter();
		
		$dir = new VersionedDirectoryIterator(PLUGIN_DIR);
		$names = $dir->getValid();
				
		foreach ($names as $name) {
			$plugin = $this->_table->findByName($name);
			if(!$plugin) {
				$this->_redirect('plugins/install/'.$name);
			}
		}		
	}
	
	public function installAction()
	{
		$name = $this->_getParam('name');
		
		$path = PLUGIN_DIR.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$name.'.php';
		require_once $path;
		$plugin = new $name($router, new Plugin());
		
		if(!empty($_POST)) {
			$plugin->install($_POST['config']);
			$this->_redirect('plugins/');
		}
		
		$record = $this->_table->findByName($name);
		if($record) {
			$this->_redirect('plugins/');
		}
		
		
		$this->render('plugins/install.php', compact('plugin'));
	}
	
	
	/**
	 * save the form values to the db
	 *
	 * @return boolean
	 **/
	protected function commitForm($plugin)
	{	
		if(empty($_POST)) return false;

		$plugin->config = $_POST['config'];
		
		if($_POST['active']) {
			$plugin->active = (int) !($plugin->active);
		}
		try{
			$plugin->save();
			return true;
		}catch( Exception $e) {
			return false;
		}

	}
	
	public function deleteAction() {$this->_redirect('/');}
	
	public function addAction() {$this->_redirect('/');}
}
?>