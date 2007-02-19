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
	
	public function fooAction()
	{
		$data = array('message' => 'bar');
		
		$this->getResponse()->setHeader('X-JSON', Zend_Json::encode($foo));
		$this->getResponse()->appendBody(Zend_Json::encode($foo));
	}

	public function addRoleAction()
	{
		$filterPost = new Zend_Filter_Input($_POST);
		if ($roleName = $filterPost->testAlnum('name')) {
			$acl = Zend::registry('acl');
			if (!$acl->hasRole($roleName)) {
				$acl->addRole(new Zend_Acl_Role($roleName));
				$dbAcl = $this->getOption('acl');
				$dbAcl->value = serialize($acl);
				$dbAcl->save();
			}
			else {
				/**
				 * Return some message that the role name has already been taken
				 */
			}
		}
		
		/**
		 * Support some implementation abstract method of handling
		 * both ajax and regular calls
		 */
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
		if ($roleName = $filterPost->testAlnum('name')) {
			$acl = Zend::registry('acl');
			if ($acl->hasRole($roleName)) {
				$acl->removeRole($roleName);
				$dbAcl = $this->getTable('option')->findByDql('name LIKE "acl"');
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