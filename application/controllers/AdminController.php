<?php
/**
 * @package Sitebuilder
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
    }

    public function noRouteAction()
    {
        $this->_redirect('/');
    }
}
?>