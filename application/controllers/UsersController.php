<?php
/**
 * @package Omeka
 **/
require_once 'Kea/Controller/Action.php';
class UsersController extends Kea_Controller_Action
{
    public function indexAction()
    {
		echo 'This is the '.get_class($this);
    }

	public function rolesAction()
	{
		$acl = Zend::registry('acl');
		$roles = array_keys($acl->getRoles());
		
		$params = array('roles' => $roles);
		$this->render('users/roles.php', $params);
	}

	public function addRoleAction()
	{
		$filterPost = new Zend_Filter_Input($_POST);
		if ($roleName = $filterPost->testAlpha('name')) {
			$acl = Zend::registry('acl');
			
			if (!$acl->hasRole($roleName)) {
				$acl->addRole(new Zend_Acl_Role($roleName));			
				$option = Doctrine_Manager::getInstance()->getTable('Option');
				$dbAcl = $option->findByDql('name LIKE "acl"');
				$dbAcl[0]->value = serialize($acl);
				$dbAcl[0]->save();
			}
			else {
				$e = new Exception();
				$e->setMessage('foo');
				$this->getResponse()->setException($e);
			}
		}
		
		if ($filterPost->getAlpha('request') == 'ajax') {
			return null;
		}
		else {
			$this->_redirect('users/roles');
		}
	}
	
	public function deleteRoleAction()
	{
		$filterPost = new Zend_Filter_Input($_POST);
		if ($roleName = $filterPost->testAlpha('name')) {
			$acl = Zend::registry('acl');
			
			if ($acl->hasRole($roleName)) {
				$acl->removeRole($roleName);			
				$option = Doctrine_Manager::getInstance()->getTable('Option');
				$dbAcl = $option->findByDql('name LIKE "acl"');
				$dbAcl[0]->value = serialize($acl);
				$dbAcl[0]->save();
			}
		}
	}

    public function noRouteAction()
    {
        $this->_redirect('/');
    }
}
?>