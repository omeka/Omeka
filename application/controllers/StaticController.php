<?php 
/**
 * @package Omeka
 **/
require_once 'Kea/Controller/Action.php';
class StaticController extends Kea_Controller_Action 
{
	public function init()
	{
		$this->_modelClass = 'Route';
		$this->_table = Doctrine_Manager::getInstance()->getTable($this->_modelClass);
	}
	
	public function findStaticAction()
	{
		$route = $this->_getParam('route');
		
		$this->render($route['path']);
	}
	
	public function browseAction()
	{
		//needs permissions checks
		if($this->_getParam('activate')) {
			$route = $this->findById();
			$route->active = !$route->active;
			$route->save();
		}
		
		//Add or delete a route
		if(!empty($_POST))
		{
			//convert spaces in page name to underscores
			$_POST['name'] = str_replace(' ', '_', $_POST['name']);
			
			$route = new Route;
			$route->setFromForm($_POST);
			$route->static = 1;
			try {
				$route->save();
			} catch (Doctrine_Validator_Exception $e) {
				$route->gatherErrors($e);
			}
		}
		$routes = $this->_table->findAll();
		$this->render('static/browse.php',compact('routes'));
	}
}
?>
