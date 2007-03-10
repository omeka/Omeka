<?php
require_once MODEL_DIR.DIRECTORY_SEPARATOR.'User.php';
/**
 * @package Omeka
 **/
require_once 'Kea/Controller/Action.php';
class UsersController extends Kea_Controller_Action
{
	public function init() {
		$this->_table = Doctrine_Manager::getInstance()->getTable('User');
		$this->_modelClass = 'User';
		$this->before_filter('authenticate', array('login'));
	}

	public function loginAction()
	{
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			
			require_once 'Zend/Auth.php';
			require_once 'Zend/Session.php';
			require_once 'Kea/Auth/Adapter.php';
			require_once 'Zend/Filter/Input.php';
			
			$session = new Zend_Session;
			echo $session->controller;
			
			$filterPost = new Zend_Filter_Input($_POST);
			$auth = new Zend_Auth(new Kea_Auth_Adapter());

			$options = array('username' => $filterPost->testAlnum('username'),
							 'password' => $filterPost->testAlnum('password'));

			$token = $auth->authenticate($options);
			
			if ($token->isValid()) {
				$this->_redirect('items');
				return;
			}
			$this->render('users/login.php', array('errorMessage' => $token->getMessage()));
			return;
		}
		$this->render('users/login.php');
	}
	
	public function logoutAction()
	{
		require_once 'Zend/Auth.php';
		require_once 'Kea/Auth/Adapter.php';
		$auth = new Zend_Auth(new Kea_Auth_Adapter());
		$auth->logout();
		$this->_redirect('');
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