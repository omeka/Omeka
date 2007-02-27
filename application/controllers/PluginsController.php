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
		$this->_table = Doctrine_Manager::getInstance()->getTable('Plugin');
	}
	
	public function browseAction() {
		$names = Doctrine_Manager::connection()->getTable('Plugin')->getNewPluginNames();
		
		$this->_view->new_names = $names;
		
		if(!empty($_POST)) {
			foreach( $names as $name )
			{
				if(isset($_POST[$name])) {
					
					//handle the plugin installation
					try {
						$path = PLUGIN_DIR.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$name.'.php';
						require_once $path;
						$plugin_class = new $name(null, new Plugin());
						$plugin_class->install($path);
						$this->_redirect('plugins/browse/');
					} catch(Exception $e) {
						echo $e->getMessage();exit;
					}
				}
			}
		}
		
		parent::browseAction();
	}
	
	public function installAction()  
	{
		
		
		$this->render('plugins/install.php');
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
	
	public function addAction()
	{
		$this->_forward('plugins/install');
	}
}
?>