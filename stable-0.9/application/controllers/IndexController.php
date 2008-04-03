<?php
/**
 * @package Omeka
 **/
require_once 'Omeka/Controller/Action.php';
class IndexController extends Omeka_Controller_Action
{
	/**
	 * This allows for GET style routing.
	 * DO NOT EDIT THIS UNLESS THERE IS A BUG, OR YOU KNOW WHAT YOU ARE DOING
	 *
	 * @todo could remove dependancy on the config_ini and the Zend Registry request by using an array 
	 */
    public function indexAction()
    {
	
		$config = Zend_Registry::get('config_ini');
		
		$req = $this->getRequest();
		$c = $req->getParam($config->uri->controller);
		$a = $req->getParam($config->uri->action);

		if (!$c) {
			// Assume that they want to go to the default location
			$this->_forward($config->default->action, $config->default->controller);
		}
		
		if ($c) {
			if ($a) {
				$this->_forward($a, $c);
				return;
			}
			else {
				$this->_forward($config->default->action, $c);
				return;
			}
		}
    }

	public function homeAction() 
	{
		$this->render('index.php');
	}
}
?>