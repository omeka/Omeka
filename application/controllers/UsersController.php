<?php
/**
 * @package Omeka
 **/
require_once MODEL_DIR.DIRECTORY_SEPARATOR.'User.php';
require_once 'Zend/Filter/Input.php';
require_once 'Kea/Controller/Action.php';
class UsersController extends Kea_Controller_Action
{	
	public function init() {
		$this->_table = Doctrine_Manager::getInstance()->getTable('User');
		$this->_modelClass = 'User';
		$this->before_filter('authenticate', array('login'));
	}
	
	public function activateAction()
	{
		$hash = $this->_getParam('u');
		$ua = Doctrine_Manager::getInstance()->getTable('UsersActivations')->findByUrl($hash);
		
		if(!$ua) {
			$this->render('404.php');
			return;
		}
		
		if(!empty($_POST)) {
			if($_POST['new_password1'] == $_POST['new_password2']) {
				$ua->User->password = $_POST['new_password1'];
				$ua->User->active = 1;
				$ua->User->save();
				$ua->delete();
				$this->_redirect('users/login');				
			}
		}
		$user = $ua->User;
		$this->render('users/activate.php', compact('user'));
	}
	
	/**
	 *
	 * @return void
	 **/
	public function addAction() 
	{
		$user = new User();
		$password = $user->generatePassword(8);
		if($this->commitForm($user)) {
			$ua = new UsersActivations;
			$ua->User = $user;
			$ua->generate();
			$ua->save();
			//send the user an email telling them about their great new user account
			$site_title = Doctrine_Manager::getInstance()->getTable('Option')->findByName('site_title');
			$from = Doctrine_Manager::getInstance()->getTable('Option')->findByName('administrator_email');
			
			$body = "Welcome!\n\nYour account for the ".$site_title." archive has been created. Please click the following link to activate your account: <a href=\"".WEB_ROOT."/users/activate?u={$ua->url}\">Activate</a> (or use any other page on the site).\n\nBe aware that we log you out after 15 minutes of inactivity to help protect people using shared computers (at libraries, for instance).\n\n".$site_title." Administrator";
			$title = "Activate your account with the ".$site_title." Archive";
			$header = 'From: '.$from. "\n" . 'X-Mailer: PHP/' . phpversion();
			mail($user->email, $title, $body);
			$this->_redirect('users/show/'.$user->id);
		}else {
			$this->render('users/add.php', compact('user'));
		}
	}
		
	protected function commitForm($user)
	{
		if($_POST['active']) {
			$_POST['active'] = 1;
		}
		//potential security hole
		if(isset($_POST['password'])) {
			unset($_POST['password']);
		}
		//somebody is trying to change the password
		//@todo Put in a security check (superusers don't need to know the old password)
		if(isset($_POST['new_password1'])) {
			$new1 = $_POST['new_password1'];
			$new2 = $_POST['new_password2'];
			$old = $_POST['old_password'];
			if(empty($new1) || empty($new2) || empty($old)) {
				$user->getErrorStack()->add('password', 'User must fill out all password fields in order to change password');
				return false;
			}
			//If the old passwords don't match up
			if(sha1($old) !== $user->password) {
				$user->getErrorStack()->add('password', 'Old password has been entered incorrectly');
				return false;
			} 
			
			if($new1 !== $new2) {
				$user->getErrorStack()->add('password', 'New password has been entered incorrectly');
				return false;
			}			
			$user->password = $new1;
		}
		return parent::commitForm($user);
	}
	
	public function loginAction()
	{
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			
			require_once 'Zend/Auth.php';
			require_once 'Zend/Session.php';
			require_once 'Kea/Auth/Adapter.php';
			
			
			$session = new Zend_Session;
			echo $session->controller;
			
			$filterPost = new Zend_Filter_Input($_POST);
			$auth = new Zend_Auth(new Kea_Auth_Adapter());

			$options = array('username' => $filterPost->testAlnum('username'),
							 'password' => $filterPost->testAlnum('password'));

			$token = $auth->authenticate($options);
			
			if ($token->isValid()) {
				$this->_redirect('/');
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

/**
 * Define Roles Actions
 */

	public function rolesAction()
	{
		$acl = Zend::registry('acl');
		
		$roles = array_keys($acl->getRoles());

		$permissions = $acl->getPermissions();
		
		foreach($roles as $key => $val) {
			$roles[$val] = $val;
			unset($roles[$key]);
		}

		$acl->getRules('researcher');
		$params = array('roles' => $roles, 'permissions' => $permissions);
		$this->render('users/roles.php', $params);
	}
	
	public function getRoleForm()
	{
		
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
		$this->_redirect('users/roles');
	}
	
	public function setPermissionsAction() {
		$role = $_POST['role'];
		if (!empty($role)) {
			$acl = Zend::registry('acl');
			foreach($_POST['permissions'] as $resource => $permissions) {
				$resource_permissions = array();
				foreach($permissions as $permission => $on) {
					$resource_permissions[] = $permission;
				}
				$acl->allow($role, $resource, $resource_permissions);
			}
		}
		$acl->save();
		$this->_redirect('users/roles');
	}

    public function noRouteAction()
    {
        $this->_redirect('/');
    }
}
?>