<?php
/**
 * @package Omeka
 **/
require_once 'Kea/Controller/Action.php';
class IndexController extends Kea_Controller_Action
{
	/**
	 * This allows for GET style routing.
	 * DO NOT EDIT THIS UNLESS THERE IS A BUG, OR YOU KNOW WHAT YOU ARE DOING
	 *
	 * @todo could remove dependancy on the config_ini and the Zend Registry request by using an array 
	 */
    public function indexAction()
    {
	
		$config = Zend::registry('config_ini');
		
		$req = $this->getRequest();
		$c = $req->getParam($config->uri->controller);
		$a = $req->getParam($config->uri->action);

		if (!$c) {
			// Assume that they want to go to the default location
			$this->_forward($config->default->controller, $config->default->action);
		}
		
		if ($c) {
			if ($a) {
				$this->_forward($c, $a);
				return;
			}
			else {
				$this->_forward($c, $config->default->action);
				return;
			}
		}
    }
	
    public function noRouteAction()
    {
        $this->_redirect('/');
    }

	public function homeAction() 
	{
		throw new Exception ( 'ALL YOUR ERRORS ARE BELONG TO US.' );
		$this->render('index.php');
	}
}
?>