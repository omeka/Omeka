<?php
/**
 * @package Omeka
 * @author Nate Agrin
 * 
 * ONLY EDIT THIS FILE IF YOU KNOW WHAT YOU ARE DOING!!
 **/
require_once 'Zend/Controller/Action.php';
class AdminController extends Zend_Controller_Action
{
    public function indexAction()
    {
		$req = $this->getRequest();
		
		$config = Zend::registry('config_ini');
		
		/**
		 * reset the baseUrl property so that $this->_redirect
		 * in controllers directs to admin templates
		 * 
		 * [NA] - not sure about using config->uri->admin for this
		 */ 
		$req->setBaseUrl($req->getBaseUrl().DIRECTORY_SEPARATOR.$config->uri->admin);

		$req->setParam('admin', true);

		$request = $this->getRequest();

		if(!$c = $request->getParam($config->uri->controller)) {
			$c = $config->default->controller;
		}
		
		if(!$a = $request->getParam($config->uri->action)) {
			$a = $config->default->action;
		}

		$this->_forward($c, $a);
    }

    public function noRouteAction()
    {
        $this->_redirect('/');
    }
}
?>