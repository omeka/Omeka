<?php
/**
 * @package Omeka
 * @author Nate Agrin
 **/
require_once 'Kea/Controller/Action.php';
class TypesController extends Kea_Controller_Action
{
		
    public function indexAction()
    {
		$this->_forward('types', 'browse');
    }

    public function noRouteAction()
    {
        $this->_redirect('/');
    }

	public function browseAction() 
	{
		$types = Doctrine_Manager::getInstance()->getTable('Type')->findAll();
		$this->render('browse.php', compact('types'));
	}
	
	public function showAction() 
	{
		//This is hard-coded only for purposes of creating a mockup.
		$id = 1;
		
		$type = Doctrine_Manager::getInstance()->getTable('Type')->find($id);
		
//		$this->render('show.php', compact('type'));
		echo $type->name;
	}
	
	public function editAction() 
	{
		
	}
	
}
?>