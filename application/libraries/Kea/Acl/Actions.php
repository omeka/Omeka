<?php

class Kea_Acl_Actions
{
	protected $_actions = array(
		'users' => array('create', 'read', 'update', 'delete'),
		'items' => array('create', 'read', 'update', 'delete')
		);
		
	public function getActions()
	{
		return $this->_actions;
	}
	
	public function addAction($controller, $action) {}
	
	public function removeAction($controller, $action) {}
}

?>