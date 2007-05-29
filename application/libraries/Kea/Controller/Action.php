<?php
/**
 * @package Omeka
 */
require_once 'Zend/Controller/Action.php';
require_once 'Kea/Controller/Browse/Paginate.php';
require_once 'Kea/Controller/Browse/List.php';
abstract class Kea_Controller_Action extends Zend_Controller_Action
{
	/**
	 * @var Kea_View
	 */
	protected $_view;
	
	/**
	 * Doctrine_Table associated with the controller (initialized optionally within the init() method)
	 *
	 * @var Doctrine_Table
	 **/
	protected $_table;
	
	/**
	 * Allows for builtin CRUD scaffolding in the controllers (must be initialized within the init() method)
	 *
	 * @var string
	 **/
	protected $_modelClass;
	
	/**
	 * Current options for browsing involve either Pagination or a single page list
	 *
	 * @var Kea_Controller_Browse_Interface
	 **/
	protected $_browse;
	
	/**
	 * Kea_Acl
	 *
	 * @var Kea_Acl
	 **/
	protected $acl;
	
	/**
	 * Zend_Auth
	 *
	 * @var Zend_Auth
	 **/
	protected $_auth;
	
	/**
	 * Temporarily allowed permissions
	 *
	 * @var array
	 **/
	protected $_allowed = array();
		
	/**
	 * Attaches a view object to the controller.
	 * The view also receives the current controller
	 * object so it can interact with the request / response objects.
	 */
	public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array())
	{
		$this->acl = Zend::Registry('acl');
		
		// Zend_Controller_Action __construct finishes by running init()
		$init = parent::__construct($request, $response, $invokeArgs);
		
		if(!array_key_exists('return',$invokeArgs)) {
			$this->_view = new Kea_View($this);
		}
		
		$this->_auth = Zend::Registry('auth');
		
		return $init;
	}
	
	/**
	 *	Streamline the process of adding static pages by automatically checking for 
	 * 	arbitrarily added pages.
	 * 
	 */
	public function __call($m, $a)
	{
		$action = $this->getRequest()->getParam('action');
		$controller = $this->getRequest()->getParam('controller');
		
		return $this->render($controller.DIRECTORY_SEPARATOR.$action.'.php');
	}
	
	/**
	 * Before filter applies a named method to the controller
	 * before calling the actual method.
	 * Primarily used for logging in
	 */
	protected $_before_filter = array();
	
	protected function before_filter($function_to_run, $except = array())
	{
		$this->_before_filter[$function_to_run] = $except;
	}
	
	public function preDispatch()
	{
		/**
		 * Admin theme protection is here.
		 * Kind of bugs me that it's obscured -n8
		 * 
		 * The admin theme protection is as follows:
		 * A user with an account needs to have greater than public access.
		 * Otherwise, each method can have a specific, admin set permission
		 * level
		 */
		$request = $this->getRequest();
		$action = $request->getActionName();
		
		/**
		 *	Right now user activation is the only admin controller/action that doesn't require login (doesn't make sense to require it)
		 */
		if($request->getParam('admin') == true &&
			$request->getControllerName() == 'users' &&
				$request->getActionName() == 'activate') {
			
		}
		elseif ($request->getParam('admin') == true &&
			$request->getControllerName() != 'users' &&
			$request->getActionName() != 'login') {

			require_once 'Zend/Auth.php';
			require_once 'Zend/Session.php';
			require_once 'Kea/Auth/Adapter.php';

			$auth = $this->_auth;
			if (!$auth->isLoggedIn()) {
				// capture the intended controller / action for the redirect
				$session = new Zend_Session;
				$session->controller = $request->getControllerName();
				$session->action = $request->getActionName();

				// finally, send to a login page
				$this->_redirect('users/login');
			}else {
				/*	Access the authentication session and set it to expire after a certain amount
				 	of time if there are no requests */
				$authPrefix = $auth->getSessionNamespace();
				$auth_session = new Zend_Session($authPrefix);
				
				$config = Zend::Registry('config_ini');
				$minutesUntilExpiration = (int) $config->login->expire;
				
				//Default value in case for whatever reason it's not available
				if(!$minutesUntilExpiration) $minutesUntilExpiration = 15;
				
				$auth_session->setExpirationSeconds($minutesUntilExpiration * 60);
			}		
		}
		$this->checkActionPermission($action);
		
		$action = $this->_request->getActionName();
		foreach ($this->_before_filter as $func => $exceptThese) {
			if (!in_array($action, $exceptThese)) {
				if (!method_exists($this, $func)) {
					throw new Zend_Controller_Exception('The before filter '.$func.' was not found.');
				}
				else {
					$this->$func();
				}
			}
		}
	}
	
	protected function checkActionPermission($action)
	{
		//Here is the permissions check for each action
		try {
			if(!$this->isAllowed($action)) {		
				$this->_redirect('403');
			}
		} catch (Zend_Acl_Exception $e) {}		
	}
	
	/**
	 * Notifies whether the logged-in user has permission for the given rule
	 * i.e., if the $rule is 'edit', then this will return TRUE if the user has permission to 'edit' for 
	 * the current controller
	 *
	 * @return bool
	 **/
	protected function isAllowed($rule, $resourceName=null, $user=null) 
	{
		$allowed = $this->_allowed;
		if(isset($allowed[$rule])) {
			return $allowed[$rule];
		}
		
		if(!$user) {
			$user = Kea::loggedIn();
		}
		
		/*	'default' permission level is hard-coded here, may change later */
		$role = !$user ? 'default' : $user->role;
		if(!$resourceName) {
			$resourceName = $this->getName();
		}
		
		//If the resource has no rule that would indicate permissions are necessary, then we assume access is allowed
		if(!$this->acl->resourceHasRule($resourceName,$rule)){
			return TRUE;
		} 
		
		return $this->acl->isAllowed($role, $resourceName, $rule);
	}
	
	/**
	 * Temporarily override the ACL's permissions for this controller
	 *
	 * @return this
	 **/
	protected function setAllowed($rule,$isAllowed=true) 
	{
		$this->_allowed[$rule] = $isAllowed;
		
		return $this;
	}
	
	protected function authenticate()
	{
		require_once 'Zend/Auth.php';
		require_once 'Zend/Session.php';
		require_once 'Kea/Auth/Adapter.php';
		require_once 'Zend/Filter/Input.php';

		$auth = $this->_auth;
		if ($auth->isLoggedIn()) {
			// check the identity's role is compatible with the action's permissions
		}
		else {
			// capture the intended controller / action for the redirect
			$session = new Zend_Session;
			$session->controller = $this->_request->getControllerName();
			$session->action = $this->_request->getActionName();
			
			// finally, send to a login page
			$this->_redirect('users/login');
		}
	}
	
	/**
	 * Define this here to avoid Zend's silly requirements
	 */
	public function noRouteAction()
    {
        $this->_redirect('/');
    }


	/**
	 * CONVIENCE METHODS
	 */
	
	/**
	 * Retrieve the Doctrine table for queries
	 * 
	 */
	public function getTable($table = null)
	{
		return Doctrine_Manager::getInstance()->getTable($table);
	}

	public function getConn()
	{
		return Doctrine_Manager::getInstance()->connection();
	}

	/**
	 * Retrieve an option from the option table.
	 * This may end up being redundant.
	 * 
	 * @starred
	 * 
	 */
	public function getOption($name)
	{
		$optionTable = $this->getTable('option');
		$options = $optionTable->findByDql("name LIKE :name", array('name' => $name));
		if (count($options) == 1) {
			return ($options[0]);
		}
		return false;
	}
	
	public function getView()
	{
		return $this->_view;
	}
	
	public function getName()
	{
		return ucwords($this->getRequest()->getControllerName());
	}
	
	/**
	 * Stolen directly from Rails.
	 * Again, this may be redundant, in that message delivery
	 * should allow for ajax and non ajax responses.
	 * Session passed messages obviously do not necessarily allow
	 * for this.
	 * 
	 * @starred
	 * 
	 */
	public function flash($msg=null)
	{
		require_once 'Zend/Session.php';
		$flash = new Zend_Session('flash');
		$flash->msg = $msg;
	}

	///// BASIC CRUD INTERFACE /////
	
	public function homeAction()
	{
		$this->indexAction();
	}
	
	
	public function indexAction()
	{
        $this->_forward($this->getRequest()->getControllerName(), 'browse');
	}
	
	/**
	 * Browsing strategy defaults to a full listing, can be switched to pagination by instantiating Kea_Controller_Browse_Pagination in the init() method
	 *
	 * @return void
	 **/
	public function browseAction()
	{		
		if(empty($this->_modelClass)) throw new Exception( 'Scaffolding class has not been specified' );
		
		if(empty($this->_browse)) $this->_browse = new Kea_Controller_Browse_List($this->_modelClass, $this);
		
		return $this->_browse->browse();
	}
	
	public function showAction()
	{
		$varName = strtolower($this->_modelClass);
		
		//duplicated from above
		$pluralName = $varName.'s';
		$viewPage = $pluralName.DIRECTORY_SEPARATOR.'show.php';
		
		try{
			$$varName = $this->findById();
		}catch(Exception $e) {
			echo $e->getMessage();exit;
		}
		
		return $this->render($viewPage, compact($varName));
	}
	
	public function addAction()
	{
		//Maybe this recurring bit should be abstracted out
		$varName = strtolower($this->_modelClass);
		$class = $this->_modelClass;
		$pluralName = $varName.'s';
		
		$$varName = new $class();
		
		if($this->commitForm($$varName))
		{
			$this->_redirect($pluralName.'/browse/');
		}else {
			$this->loadFormData();
			$this->render($pluralName.'/add.php', compact($varName));			
		}

	}
	
	public function editAction()
	{
		$varName = strtolower($this->_modelClass);
		$pluralName = $varName.'s';
		
		try{
			$$varName = $this->findById();
		}catch(Exception $e) {
			echo $e->getMessage();exit;
		}
		
		if($this->commitForm($$varName))
		{
			//Avoid a redirect by passing an extra parameter to the AJAX call
			if($this->_getParam('noRedirect')) {
				$this->_forward($pluralName, 'show');
			} else {
				$this->_redirect($pluralName.'/show/'.$$varName->id);
			}
		}else{
			$this->loadFormData();
			$this->render($pluralName.'/edit.php', compact($varName));
		}		
	}
	
	public function deleteAction()
	{
		$browseURL = strtolower($this->_modelClass).'s/browse/';
		
		$record = $this->findById();
		$record->delete();
		$this->_redirect($browseURL);
	}
	
	/**
	 * Processes and saves the form to the given record
	 *
	 * @param Kea_Record
	 * @return boolean True on success, false otherwise
	 **/
	protected function commitForm($record)
	{
		if(!empty($_POST))
		{
			$clean = $_POST;
			unset($clean['id']);
			$record->setFromForm($clean);
			try {
				$record->save();
				return true;
			}
			catch(Doctrine_Validator_Exception $e) {
				$record->gatherErrors($e);
				$this->flash($record->getErrorMsg());
				return false;
			}	
		}
		return false;
	}
	
	/**
	 * Load extra data that would need to be displayed for forms, for example the item form would require all collections, plugins, etc.
	 *
	 * @return void
	 **/
	protected function loadFormData() {}
	
	///// END BASIC CRUD INTERFACE /////
	
	/**
	 * Most convenient usage would be something like: $this->render("show.php", compact("items", "total", "foo", "bar"));
	 *
	 * @param string The page, including .php extension
	 * @param array The variables to be included on that page, where key = name and value = contents.  see compact()
	 * @return mixed|void
	 * 
	 **/
	public function render($page, array $vars = array())
	{
		if($return = $this->getInvokeArg('return')){
			if(is_array($return)) {
				$returnThese = array();
				foreach ($return as $r) {
					$returnThese[$r] = $vars[$r];
				}
				return $returnThese;
			} else {
				return $vars[$return];
			}
		} 
		$this->_view->assign($vars);
		$this->getResponse()->appendBody($this->_view->render($page));
	}
	
	/**
	 * Find a particular record given its unique ID # and (optionally) its class name.  Essentially a convenience method
	 * $this->_table must be initialized in the init() method if the particular model is to be chosen automagically
	 * 
	 * @return Kea_Record
	 **/
	public function findById($id=null, $table=null)
	{
		$id = (!$id) ? $this->getRequest()->getParam('id') : $id;
		
		if(!$id) throw new Exception( get_class($this).': No ID passed to this request' );
		
		if(!$table) {
			$record = $this->_table->find($id);
		}else {
			$record = Doctrine_Manager::getInstance()->getTable($table)->find($id);
		}
		
		if(!$record) {
			throw new Exception( get_class($this).": No record with ID # $id exists" );
		}
		
		return $record;
	}
	
	public function forbiddenAction()
	{
		$this->render('403.php');
	}
	
	public function errorAction()
	{
		$this->render('404.php');
	}
	
	/**
	 * Overridden to support requests that only want to return data and not spit out pages
	 *
	 **/
	protected function _redirect($url,array $options=null) 
	{
		if($return = $this->getInvokeArg('return')) 
		{
			return null;
		}else {
			return parent::_redirect($url,$options);
		}
	}
}
?>