<?php
/**
 * @package Omeka
 */
require_once 'Zend/Controller/Action.php';
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
	
	protected $_broker;
	
	/**
	 * Controller/Action list for admin actions that do not require being logged-in
	 *
	 * @var string
	 **/
	protected $_adminWhitelist = array(
				array('controller'=>'users', 'action'=>'activate'), 
				array('controller'=>'users', 'action'=>'login'));
	
	/**
	 * Redirects should be defined up here (with opportunity to override them)
	 * @example $_redirects = array('edit'=>array('items/show/id', array('id')));
	 * @return void
	 **/
	private $_crudRedirects = array(
		'edit' 	=> array('controller/show/id', array('controller','id')),
		'add'	=> array('controller/browse/', array('controller')),
		'login'	=> array('users/login'),
		'delete'=> array('controller/browse', array('controller')),
		'default'=> array('controller/action/id', array('controller', 'action', 'id'))
	);
	protected $_redirects = array();
		
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
		
		$this->_broker = Kea_Controller_Plugin_Broker::getInstance();
		
		$this->_redirects = array_merge($this->_crudRedirects, $this->_redirects);
		
		$this->_broker->setRedirects($this->_redirects);

		return $init;
	}
	
	/**
	 * Process 
	 *
	 * @return void
	 **/

	public function getRedirect($action, $vars=null) {

		$uri = $this->_redirects[$action][0];

		//Check for the presence of required fields
		if(isset($this->_redirects[$action][1])) {
			$reqs = $this->_redirects[$action][1];
			foreach ($reqs as $r) {
				if(!in_array($r, array_keys($vars))) {
					throw new Exception( 'You are missing the '.$r.' field in this redirect' );
				}else {
					//Substitute the var into the uri
					$uri = str_replace($r, $vars[$r], $uri);
				}
			}
		}

		//Process any optional fields
		$optional = @$this->_redirects[$action][2];
		if($optional) {
			foreach ($optional as $k => $o) {
				//If we passed the var then use it
				if(in_array($o, array_keys($vars))) {
					$uri = str_replace($o, $vars[$o], $uri);
				}
				//Otherwise remove that part of the url
				else {
					// The '//' is to get rid of the extra slash in the uri
					$uri = str_replace($o, '', $uri);
					$uri = str_replace('//', '/', $uri);
				}
			}
		}

		return $uri;
	}	
	
	protected function getPluralized($lower=true)
	{
		$class = $this->_modelClass;
		$record = new $class;
		return $record->getPluralized($lower);
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
		$controller = $request->getControllerName();
		
		$overrideLogin = false;
		
		/**
		 *	Right now user activation is the only admin controller/action that doesn't require login (doesn't make sense to require it)
		 */
		if($request->getParam('admin')) {
			foreach ($this->_adminWhitelist as $entry) {
				if( ($entry['controller'] == $controller) and ($entry['action'] == $action) ) {
					$overrideLogin = true;
					break;
				}
			}
			
			//If we haven't overridden the need to login
			if(!$overrideLogin) {
			
				//Deal with the login stuff
			require_once 'Zend/Auth.php';
			require_once 'Zend/Session.php';
			require_once 'Kea/Auth/Adapter.php';

			$auth = $this->_auth;
			if (!$auth->isLoggedIn()) {
				// capture the intended controller / action for the redirect
				$session = new Zend_Session;
				$session->redirect = $request->getPathInfo();
				
				// do we need these sessions?  possibly delete
				$session->controller = $request->getControllerName();
				$session->action = $request->getActionName();

				// finally, send to a login page
				$this->_redirect('login');
			}else {
				/*	Access the authentication session and set it to expire after a certain amount
				 	of time if there are no requests */
				$authPrefix = $auth->getSessionNamespace();
				$auth_session = new Zend_Session($authPrefix);
				
				$config = Zend::Registry('config_ini');
				
				if(isset($config->login->expire)) {
					$minutesUntilExpiration = (int) $config->login->expire;
				
					//Default value in case for whatever reason it's not available
					if(!$minutesUntilExpiration) $minutesUntilExpiration = 15;
				
					$auth_session->setExpirationSeconds($minutesUntilExpiration * 60);					
				}

			}					
			
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
			$this->_redirect('login');
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
		return Zend::Registry('doctrine')->getTable($table);
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
	
	public function getName($upper=true)
	{
		$name = $this->getRequest()->getControllerName();
		return $upper ? ucwords($name) : $name;
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
		
		$pluralName = $this->getPluralized();
		$viewPage = $pluralName.DIRECTORY_SEPARATOR.'browse.php';
				
		$$pluralName = $this->getTable($this->_modelClass)->findAll();

		$totalVar = 'total_'.$pluralName;
		
		$$totalVar = count($$pluralName);
		
		Zend::register($pluralName, $$pluralName);
		
		//Fire the plugin hook
		$this->pluginHook('onBrowse' . ucwords($pluralName), array($$pluralName));
		
		return $this->render($viewPage, compact($pluralName,$totalVar));
	}
	
	public function showAction()
	{
		$varName = strtolower($this->_modelClass);
		
		//duplicated from above
		$pluralName = $this->getPluralized();
		$viewPage = $pluralName.DIRECTORY_SEPARATOR.'show.php';
		
		try{
			$$varName = $this->findById();
		}catch(Exception $e) {
			echo $e->getMessage();exit;
		}
		
		
		Zend::register($varName, $$varName);
		
		//i.e. onShowItem() plugin hook
		$this->pluginHook( 'onShow' . get_class($$varName), array($$varName) );
		
		return $this->render($viewPage, compact($varName));
	}
	
	protected function pluginHook($hookName, $varsToPass = array()) {
		//Fire the plugin hook
		call_user_func_array(array($this->_broker, $hookName), $varsToPass);
	}
	
	public function addAction()
	{
		//Maybe this recurring bit should be abstracted out
		$varName = strtolower($this->_modelClass);
		$class = $this->_modelClass;
		$pluralName = $this->getPluralized();
		
		$$varName = new $class();
		
		try {
			if($$varName->commitForm($_POST))
			{
				if($$varName->hasStrategy('Relatable')) {
					$user = Kea::loggedIn();
					$$varName->setAddedBy($user);
				}
				$this->pluginHook('onAdd' . $class, array($$varName));
				$this->_redirect('add',array('controller'=>$pluralName));
			}
		} catch (Exception $e) {
			$this->flash($e->getMessage());
		}

		$this->loadFormData();
		return $this->render($pluralName.'/add.php', compact($varName));			

	}
	
	public function editAction()
	{
		$varName = strtolower($this->_modelClass);
		$pluralName = $this->getPluralized();
		
		try{
			$$varName = $this->findById();
		}catch(Exception $e) {
			echo $e->getMessage();exit;
		}
		
		try {
			if($$varName->commitForm($_POST))
			{
				if($$varName->hasStrategy('Relatable')) {
					$user = Kea::loggedIn();
					$$varName->setModifiedBy($user);
				}
				
				$this->pluginHook('onEdit' . $this->_modelClass, array($$varName));
				
				//Avoid a redirect by passing an extra parameter to the AJAX call
				if($this->_getParam('noRedirect')) {
					$this->_forward($pluralName, 'show');
					return;
				} else {
					$this->_redirect('edit', array('controller'=>$pluralName, 'id'=>$$varName->id) );
				}
			}
		} catch (Exception $e) {
			$this->flash($e->getMessage());
		}
		
		$this->loadFormData();
		return $this->render($pluralName.'/edit.php', compact($varName));		
	}
	
	public function deleteAction()
	{
		$controller = $this->getName(false);
		
		$record = $this->findById();

		$this->pluginHook('onDelete' . $this->_modelClass, array($record));
				
		$record->delete();
		$this->_redirect('delete', array('controller'=>$controller));
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
		
		/* Check if the page to render has been overridden by an arbitrary param
			Make sure that this param is not set via the URL */
		if(($toRender = $this->_getParam('renderPage')) and (!isset($_REQUEST['renderPage']))) {
			$page = $toRender;
		}
		
		$this->_view->assign($vars);
		
		$this->pluginHook('preRenderPage', array($page, $vars));
		
		$this->getResponse()->appendBody($this->_view->render($page));
		
		$this->pluginHook('postRenderPage', array($page, $vars));
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
			$record = $this->getTable($table)->find($id);
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
	protected function _redirect($action,array $vars=null, array $options=null) 
	{
		if($return = $this->getInvokeArg('return')) 
		{
			return null;
		}else {
			//Substitute var in url for actual value of var
			$redirect = $this->getRedirect($action, $vars);
			$redirect = !$redirect ? $action : $redirect;

			return parent::_redirect($redirect,$options);
		}
	}
}
?>