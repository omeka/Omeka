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
		$req->setParam('admin', true);
		print_r($this->getRequest()->getParams());
		
		$config = Zend::registry('config_ini');
		$request = $this->getRequest();
		
		if(!$c = $request->getParam($config->site->controller)) {
			$c = $config->site->default->controller;
		}
		
		if(!$a = $request->getParam($config->site->action)) {
			$a = $config->site->default->action;
		}

		$this->_forward($c, $a);
    }

    public function noRouteAction()
    {
        $this->_redirect('/');
    }
}
?>