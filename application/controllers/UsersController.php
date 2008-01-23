<?php
/**
 * @package Omeka
 **/
require_once MODEL_DIR.DIRECTORY_SEPARATOR.'User.php';
require_once 'Zend/Filter/Input.php';
require_once 'Omeka/Controller/Action.php';
class UsersController extends Omeka_Controller_Action
{	
	protected $_redirects = array(
		'add'=> array('users/show/id', array('id'))
	);
	
	public function init() {
		$this->_modelClass = 'User';		
	}
	
	/**
	 * @duplication
	 * @see EntitiesController::deleteAction()
	 * @since 9/13/07
	 **/
	public function deleteAction()
	{
		$user = $this->findById();
		
		if( ($user->role == 'super') and !$this->isAllowed('deleteSuperUser')) {
			$this->flash('You are not allowed to delete super users!');
			$this->_redirect('users/browse');
		}
		
		$current = Omeka::loggedIn();
		
		if($current->id == $user->id) {
			$this->flash('You are not allowed to delete yourself!');
			$this->_redirect('users/browse');
		}
		
		return parent::deleteAction();
	}
	
	public function forgotPasswordAction()
	{
		
		//If the user's email address has been submitted, then make a new temp activation url and email it
		if(!empty($_POST)) {
			
			$email = $_POST['email'];
			$ua = new UsersActivations;
			
			$user = $this->_table->findByEmail($email);
			
			
			if($user) {
				//Create the activation url
				
			try {	
				$ua->user_id = $user->id;
				$ua->save();
				
				$site_title = get_option('site_title');
				
				//Send the email with the activation url
				$url = "http://".$_SERVER['HTTP_HOST'].$this->getRequest()->getBaseUrl().'/users/activate?u='.$ua->url;
				$body 	= "Please follow this link to reset your password:\n\n";
				$body  .= $url."\n\n";
				$body  .= "$site_title Administrator";		
				
				$admin_email = get_option('administrator_email');
				$title = "[$site_title] Reset Your Password";
				$header = 'From: '.$admin_email. "\n" . 'X-Mailer: PHP/' . phpversion();
				
				mail($email,$title, $body, $header);
				$this->flash('Your password has been emailed');	
			} catch (Exception $e) {
				  $this->flash('your password has already been sent to your email address');
			}
			
			}else {
				//If that email address doesn't exist
				
				$this->flash('The email address you provided is invalid.');
			}			

		}
		
		return $this->render('users/forgotPassword.php');
	}
	
	public function activateAction()
	{
		$hash = $this->_getParam('u');
		$ua = $this->getTable('UsersActivations')->findBySql("url = ?", array($hash), true);
		
		if(!$ua) {
			$this->errorAction();
			return;
		}
		
		if(!empty($_POST)) {
			if($_POST['new_password1'] == $_POST['new_password2']) {
				$ua->User->password = $_POST['new_password1'];
				$ua->User->active = 1;
				$ua->User->save();
				$ua->delete();
				$this->_redirect('login');				
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
		
		try {
			if($user->saveForm($_POST)) {
				
				$this->sendActivationEmail($user);
				
				$this->flashSuccess('User was added successfully!');
				
				//If this is an AJAX request then we will want to return the alternative representation of the User object
				if($this->isAjaxRequest()) {
					return $this->render('users/show.php', compact('user'));
				}
				
				//Redirect to the main user browse page
				$this->_redirect('users');
			}
		} catch (Omeka_Validator_Exception $e) {
			$this->flashValidationErrors($e);
		}
			
		if($this->isAjaxRequest()) {
			return $this->render('users/show.php', compact('user'));
		}	
		
		return $this->_forward('browse', 'Users');
	}

	protected function sendActivationEmail($user)
	{
		$ua = new UsersActivations;
		$ua->user_id = $user->id;
		$ua->save();
		
		//send the user an email telling them about their great new user account
				
		$site_title = get_option('site_title');
		$from = get_option('administrator_email');
		
		$body = "Welcome!\n\nYour account for the ".$site_title." archive has been created. Please click the following link to activate your account:\n\n"
		.WEB_ROOT."/admin/users/activate?u={$ua->url}\n\n (or use any other page on the site).\n\nBe aware that we log you out after 15 minutes of inactivity to help protect people using shared computers (at libraries, for instance).\n\n".$site_title." Administrator";
		$title = "Activate your account with the ".$site_title." Archive";
		$header = 'From: '.$from. "\n" . 'X-Mailer: PHP/' . phpversion();
		return mail($user->email, $title, $body, $header);
	}


	public function changePasswordAction()
	{
		$user = $this->findById();
		
		$current = Omeka::loggedIn();
				
		try {
			//Only super users and the actual user can change this user's password
			if(!$current or ( ($current->role != 'super') and ($user->id != $current->id) ) ) {
				throw new Exception( 'May not change another user\'s password' );
			}
			
			//somebody is trying to change the password
			if(!empty($_POST['new_password1'])) {
				$user->changePassword($_POST['new_password1'], $_POST['new_password2'], $_POST['old_password']);
				$user->save();
			}
			$this->flashSuccess('Password was changed successfully.');
			
		} catch (Omeka_Validator_Exception $e) {
			$this->flashValidationErrors($e, Omeka_Controller_Flash::DISPLAY_NEXT);
		}
		
		$this->_redirect('users/edit/'.$user->id);
	}

	public function loginAction()
	{
		if (!empty($_POST)) {
			
			require_once 'Zend/Session.php';

			$session = new Zend_Session_Namespace;
	
			$auth = $this->_auth;

			$adapter = new Omeka_Auth_Adapter($_POST['username'], $_POST['password']);
	
			$token = $auth->authenticate($adapter);

			if ($token->isValid()) {
				//Avoid a redirect by passing an extra parameter to the AJAX call
				if($this->_getParam('noRedirect')) {
					$this->_forward('home', 'index');
				} else {
					$this->_redirect($session->redirect);
					unset($session->redirect);
				}
				return;
			}
			$this->render('users/login.php', array('errorMessage' => $token->getMessages()));
			return;
		}
		$this->render('users/login.php');
	}
	
	public function logoutAction()
	{
		$auth = $this->_auth;
		//http://framework.zend.com/manual/en/zend.auth.html
		$auth->clearIdentity();
		$this->_redirect('');
	}

	/**
	 * This hook allows specific user actions to be allowed if and only if an authenticated user 
	 * is accessing their own user data.
	 *
	 **/
	public function preDispatch()
	{		
		$userActions = array('show','edit');
				
		if($current = Omeka::loggedIn()) {
			try {
				$user = $this->findById();
				if($current->id == $user->id) {
					foreach ($userActions as $action) {
						$this->setAllowed($action);
					}
				}	
			} catch (Exception $e) {}
				
		}
		return parent::preDispatch();
	}

/**
 * Define Roles Actions
 */		
	public function rolesAction()
	{
		//Permissions check
		if(!$this->isAllowed('showRoles')) {
			$this->_redirect('403');
			return;
		}
		$acl = $this->acl;
		
		$roles = array_keys($acl->getRoles());
		
		foreach($roles as $key => $val) {
			$roles[$val] = $val;
			unset($roles[$key]);
		}

		//Don't let people make users with the 'default' level
		unset($roles['default']);
		
		$rules = $acl->getRules();
		$resources = $acl->getResources();
		return $this->render('users/roles.php', compact('roles','rules','resources','acl'));
	}
}
?>