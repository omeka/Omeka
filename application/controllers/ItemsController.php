<?php
/**
 * @package Sitebuilder
 * @author Nate Agrin
 **/
require_once 'Zend/Controller/Action.php';
class ItemsController extends Zend_Controller_Action
{
    public function indexAction()
    {
		$this->_forward('items', 'browse');
    }

	public function browseAction()
	{
		$this->getResponse()->appendBody('foo');
	}

    public function noRouteAction()
    {
        $this->_redirect('/');
    }
}
?>