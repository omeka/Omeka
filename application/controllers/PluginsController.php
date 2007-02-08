<?php
require_once 'Kea/View.php';
/**
 * @package Sitebuilder
 * @author Nate Agrin
 **/
require_once 'Zend/Controller/Action.php';
class PluginsController extends Zend_Controller_Action
{
	//Duplicated in other controllers (should be abstracted by the layout/theme system)
	public function init() {
		$view = new Kea_View;
		$this->view_path = PUBLIC_DIR.DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR.'default'.DIRECTORY_SEPARATOR.'plugins';
		$view->setScriptPath($this->view_path);
		$this->view = $view;		
	}
	
	
    public function indexAction()
    {
		$this->_forward('plugins', 'browse');
    }

	public function browseAction()
	{
		$plugin = $this->find();
		
		$this->view->plugins = Doctrine_Manager::connection()->getTable('Plugin')->findAll();	

		echo $this->view->render('all.php');
	}

	//This should essentially be built into controllers as well, functional equivalent to findById() in the old system
	protected function find() {
		$id = $this->getRequest()->getParam('id');
		$plugin = Doctrine_Manager::connection()->getTable('Plugin')->find($id);
		return $plugin;
	}
	
	public function showAction() {
		
		$this->view->plugin = $this->find();
		echo $this->view->render('show.php');
	}
	
	public function editAction() 
	{			
		$plugin = $this->find();
		
		$this->view->plugin = $plugin;
		
		if($this->_commitForm($this->view)) {
			$this->_redirect('plugins/show/'.$plugin->id);
		}
		echo $this->view->render('edit.php');
	}
	
	
	public function installAction()  
	{
		$names = Doctrine_Manager::connection()->getTable('Plugin')->getNewPluginNames();
		
		$this->view->new_names = $names;
		
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
						var_dump( $e );exit;
					}
				}
			}
		}
		
		echo $this->view->render('install.php');
	}
	
	/**
	 * save the form values to the db
	 *
	 * @return boolean
	 * @author Kris Kelly
	 **/
	private function _commitForm($view)
	{
		if(empty($_POST)) return false;
		$plugin = $view->plugin;

		$plugin->config = $_POST['config'];
		
		if($_POST['active']) {
			$plugin->active = (int) !($plugin->active);
		}
		try{
			$plugin->save();
			return true;
		}catch( Exception $e) {
			echo get_class( $e );exit;
			$view->errors = $plugin->getErrorStack();
			return false;
		}

	}

	
    public function noRouteAction()
    {
        $this->_redirect('/');
    }
}
?>