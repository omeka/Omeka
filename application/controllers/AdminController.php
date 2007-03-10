<?php
/**
 * @package Omeka
 * 
 * ONLY EDIT THIS FILE IF YOU KNOW WHAT YOU ARE DOING!!
 * 
 * DEPRECIATED 3/6/07 N8 AGRIN
 **/

/*
require_once 'Zend/Controller/Action.php';
class AdminController extends Zend_Controller_Action
{
	
    public function indexAction()
    {
echo 'still loading admin controller';
		require_once 'Zend/Auth.php';
		require_once 'Zend/Session.php';
		require_once 'Kea/Auth/Adapter.php';
		require_once 'Zend/Filter/Input.php';

		$request = $this->getRequest();

		$config = Zend::registry('config_ini');

		/**
		 * reset the baseUrl property so that $this->_redirect
		 * in controllers directs to admin templates
		 * 
		 * [NA] - not sure about using config->uri->admin for this
		 */ 
/*		
		$request->setBaseUrl($request->getBaseUrl().DIRECTORY_SEPARATOR.$config->uri->admin);

		$request->setParam('admin', true);

		$auth = new Zend_Auth(new Kea_Auth_Adapter());
		if ($auth->isLoggedIn()) {
			if(!$c = $request->getParam($config->uri->controller)) {
				$c = $config->default->controller;
			}
	
			if(!$a = $request->getParam($config->uri->action)) {
				$a = $config->default->action;
			}

			$this->_forward($c, $a);
		}
		elseif ($request->getParam($config->uri->controller) == 'users' and $request->getParam($config->uri->action) == 'login') {
			$this->_forward('users', 'login');
		}
		else {
			// capture the intended controller / action for the redirect
			$session = new Zend_Session;
			$session->controller = $this->_request->getControllerName();
			$session->action = $this->_request->getActionName();

			// finally, send to a login page
			$this->_redirect('users/login');
		}
    }

    public function noRouteAction()
    {
        $this->_redirect('/');
    }
}
*/
?>