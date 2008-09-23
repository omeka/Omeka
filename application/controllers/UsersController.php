<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/
 
/**
 * @see Omeka_Controller_Action
 **/
require_once 'Omeka/Controller/Action.php';

/**
 * @see User.php
 */ 
require_once 'User.php';

/**
 * @package Omeka
 * @subpackage Controllers
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class UsersController extends Omeka_Controller_Action
{
    public function init() {
        $this->_modelClass = 'User';
        $this->beforeFilter('checkPermissions');
        $this->_auth = Omeka_Context::getInstance()->getAuth();
    }
    
    public function getAcl()
    {
        return Omeka_Context::getInstance()->getAcl();
    }
    
    /**
     * Check some permissions that depend on what specific information is being 
     * accessed
     *
     * @return void
     **/
    protected function checkPermissions()
    {
        $action = $this->_request->getActionName();
        $this->checkGeneralPerms($action);
        $this->checkUserSpecificPerms($action);
    }
    
    /**
     * @hack: this method is only here because the 'showRoles' ACL privilege is 
     * different from the 'roles' action that it protects
     *
     * @return void
     **/
    private function checkGeneralPerms($action)
    {
        // If we don't have a specific record that we are acting on, then check 
        // these permissions
        switch ($action) {
           case 'roles':
               if (!$this->isAllowed('showRoles')) {
                    $this->flash( 'Cannot view the list of user roles!' );
                    $this->redirect->goto('browse');
                }
               break;
           default:
               break;
        }
    }
    
    /**
     * Check on permissions that require interaction between the logged-in user 
     * and the user record being manipulated. Ideally, permissions checks that 
     * require complicated logic should go here
     *
     * @return void
     **/
    private function checkUserSpecificPerms($action)
    {
        $user = $this->getCurrentUser();
        
        try {
           $record = $this->findById();
        // Silence exceptions, because it's easy
        } catch (Exception $e) {
            return;
        }
        
        try {
            switch ($action) {
               
                // If we are deleting users
               case 'delete':
                   
                   //Check whether or not we are allowed to delete Super Users
                   if (($record->role == 'super') && !$this->isAllowed('deleteSuperUser')) {
                       throw new Exception( 'You are not allowed to delete super users!' );
                   }
                   // Can't delete yourself
                   if ($user->id == $record->id) {
                       throw new Exception('You are not allowed to delete yourself!');
                   }
                   break;
                   
               //If changing passwords 
               case 'changePassword':
                   
                    // Only super users and the actual user can change this 
                    // user's password
                    if(!$user || (($user->role != 'super') && ($record->id != $user->id))) {
                        throw new Exception( 'May not change another user\'s password!' );
                    }
                    break;
                    
                case 'edit':
                    
                    //Non-super users cannot edit super user data
                    //Note that super users can edit other super users' data
                    if ($user->id != $record->id 
                        && $record->role == 'super' 
                        && $user->role != 'super') {
                        throw new Exception( 'You may not edit the data for super users!' );
                    }
                    break;
                    
               default:
                   break;
            }                
        } catch (Exception $e) {
            $this->flash($e->getMessage(), Omeka_Controller_Flash::GENERAL_ERROR);
            $this->redirect->goto('browse');
        }
    }
    
    public function forgotPasswordAction()
    {
        
        //If the user's email address has been submitted, then make a new temp activation url and email it
        if (!empty($_POST)) {
            
            $email = $_POST['email'];
            
            if (!Zend_Validate::is($email, 'EmailAddress')) {
                return $this->flash('The email address you provided is invalid.  Please enter a valid email address.');
            }
            
            $ua = new UsersActivations;
            
            $user = $this->_table->findByEmail($email);
            
            
            if ($user) {
                //Create the activation url
                try {
                    $ua->user_id = $user->id;
                    $ua->save();
                    
                    $siteTitle = get_option('site_title');
                    
                    //Send the email with the activation url
                    $url   = "http://".$_SERVER['HTTP_HOST'].$this->getRequest()->getBaseUrl().'/users/activate?u='.$ua->url;
                    $body  = "Please follow this link to reset your password:\n\n";
                    $body .= $url."\n\n";
                    $body .= "$siteTitle Administrator";
                    
                    $admin_email = get_option('administrator_email');
                    $title       = "[$siteTitle] Reset Your Password";
                    $header      = 'From: '.$admin_email. "\n" . 'X-Mailer: PHP/' . phpversion();
                    
                    mail($email,$title, $body, $header);
                    $this->flash('Your password has been emailed');
                } catch (Exception $e) {
                      $this->flash('your password has already been sent to your email address');
                }
            
            } else {
                //If that email address doesn't exist
                $this->flash('The email address you provided does not correspond to an Omeka user.');
            }
        }
    }
    
    public function activateAction()
    {
        $hash = $this->_getParam('u');
        $ua = $this->getTable('UsersActivations')->findBySql("url = ?", array($hash), true);
            
        if (!$ua) {
            return $this->_forward('error');
        }
        
        if (!empty($_POST)) {
            try {
                if ($_POST['new_password1'] != $_POST['new_password2']) {
                    throw new Exception('Password: The passwords do not match.');
                }
                $ua->User->password = $_POST['new_password1'];
                $ua->User->active = 1;
                $ua->User->forceSave();
                $ua->delete();
                $this->redirect->goto('login');
            } catch (Exception $e) {
                $this->flashError($e->getMessage());
            }
        }
        $user = $ua->User;
        $this->view->assign(compact('user'));
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
                
                $user->email = $_POST['email'];
                $this->sendActivationEmail($user);
                
                $this->flashSuccess('User was added successfully!');
                                
                //Redirect to the main user browse page
                $this->redirect->goto('browse');
            }
        } catch (Omeka_Validator_Exception $e) {
            $this->flashValidationErrors($e);
        }
        
        return $this->_forward('browse');
    }
    
    protected function sendActivationEmail($user)
    {
        $ua = new UsersActivations;
        $ua->user_id = $user->id;
        $ua->save();
        
        //send the user an email telling them about their great new user account
        
        $siteTitle  = get_option('site_title');
        $from       = get_option('administrator_email');
        $body       = "Welcome!\n\nYour account for the $siteTitle archive has been created. Please click the following link to activate your account:\n\n" . WEB_ROOT . "/admin/users/activate?u={$ua->url}\n\n (or use any other page on the site).\n\nBe aware that we log you out after 15 minutes of inactivity to help protect people using shared computers (at libraries, for instance).\n\n$siteTitle Administrator";
        $title      = "Activate your account with the ".$siteTitle." Archive";
        $header     = 'From: '.$from. "\n" . 'X-Mailer: PHP/' . phpversion();
        return mail($user->email, $title, $body, $header);
    }
    
    
    public function changePasswordAction()
    {
        $user = $this->findById();
        
        try {
            //somebody is trying to change the password
            if (!empty($_POST['new_password1'])) {
                $user->changePassword($_POST['new_password1'], $_POST['new_password2'], $_POST['old_password']);
                $user->save();
            }
            $this->flashSuccess('Password was changed successfully.');
        } catch (Omeka_Validator_Exception $e) {
            $this->flashValidationErrors($e, Omeka_Controller_Flash::DISPLAY_NEXT);
        }
        
        $this->redirect->goto('edit', null, null, array('id'=>$user->id));
    }
    
    public function loginAction()
    {
        // If a user is already logged in, they should always get redirected back to the dashboard.
        if ($loggedInUser = Omeka_Context::getInstance()->getCurrentUser()) {
            $this->redirect->goto('index', 'index');
        }
        
        if (!empty($_POST)) {
            
            require_once 'Zend/Session.php';
            
            $session = new Zend_Session_Namespace;
            $result = $this->authenticate();
            
            if ($result->isValid()) {
                $this->redirect->gotoUrl($session->redirect);
            }
            $this->view->assign(array('errorMessage' => $this->getLoginErrorMessages($result)));
        }
    }
    
    /**
     * This encapsulates authentication through Omeka's login mechanism. This
     *  could be abstracted into a helper class or function or something, maybe.
     *  It'd probably be easier just to add a filter somewhere that would allow a
     *  plugin writer to switch out the Auth adapter with something else.
     * 
     * @param string
     * @return void
     **/
    public function authenticate()
    {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $db = $this->getDb();
        $dbAdapter = $db->getAdapter();
        // Authenticate against the 'users' table in Omeka.
        $adapter = new Zend_Auth_Adapter_DbTable($dbAdapter, $db->User, 'username', 'password', 'SHA1(?) AND active = 1');
        $adapter->setIdentity($username)
                    ->setCredential($password);
        $result = $this->_auth->authenticate($adapter);
        if ($result->isValid()) {
            $this->_auth->getStorage()->write($adapter->getResultRowObject(array('id', 'username', 'role', 'entity_id')));
        }
        return $result;
    }
    
    /**
     * This exists to customize the messages that people see when their attempt
     * to login fails. ZF has some built-in default messages, but it seems like
     * those messages may not make sense to a majority of people using the
     * software.
     * 
     * @param Zend_Auth_Result
     * @return string
     **/
    public function getLoginErrorMessages(Zend_Auth_Result $result)
    {
        $code = $result->getCode();
        switch ($code) {
            case Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND:
                return "Username could not be found.";
                break;
            case Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID:
                return "Invalid password.";
                break;
            case Zend_Auth_Result::FAILURE_IDENTITY_AMBIGUOUS:
                // There can never be ambiguous identities b/c the 'username'
                // field is unique in the database. Not sure what this message
                // would say.
            case Zend_Auth_Result::FAILURE_UNCATEGORIZED:
                // All other potential errors fall under this code.
            default:
                return implode("\n", $result->getMessages());
                break;
        }        
    }
    
    public function logoutAction()
    {
        $auth = $this->_auth;
        //http://framework.zend.com/manual/en/zend.auth.html
        $auth->clearIdentity();
        $this->redirect->gotoUrl('');
    }
    
    /**
     * This hook allows specific user actions to be allowed if and only if an authenticated user 
     * is accessing their own user data.
     *
     **/
    public function preDispatch()
    {
        $userActions = array('show','edit');
        
        if ($current = $this->getCurrentUser()) {
            try {
                $user = $this->findById();
                if ($current->id == $user->id) {
                    foreach ($userActions as $action) {
                        $this->setAllowed($action);
                    }
                }
            } catch (Exception $e) {
            }
        }
        return parent::preDispatch();
    }
    
    /**
     * AJAX Action for toggling permission for a role/resource/privilege combination
     * 
     * @todo Factor out the permissions check when that whole thing is fixed
     * @return void
     **/
    public function togglePrivilegeAction()
    {
        if (!$this->isAllowed('togglePrivilege')) {
            throw new Omeka_Controller_Exception_403('Toggle form privilege was incomplete!');
        }
        
        $acl = $this->getAcl();
        
        $role      = $this->_getParam('role');
        $resource  = $this->_getParam('resource');
        $privilege = $this->_getParam('privilege');
        
        if (!$role || !$resource || !$privilege) {
            Zend_Debug::dump( $this->getCurrentUser() );
            exit;
        }
        
        //If permission already exists for this, then deny it
        if ($acl->isAllowed($role, $resource, $privilege)) {
            $acl->removeAllow($role, $resource, $privilege);
            $acl->deny($role, $resource, $privilege);
        } else {
            $acl->allow($role, $resource, $privilege);
        }
        
        $hasPermission = $acl->isAllowed($role, $resource, $privilege);
        
        set_option('acl', serialize($acl));
        
        //Render the form so that we can use it in the AJAX update
        $this->view->assign(compact('hasPermission', 'role', 'resource', 'privilege'));
        $this->render('role-form');
    }
    
    /**
     * Define Roles Actions
     */
    public function rolesAction()
    {
        $acl = $this->getAcl();
        $resources = $acl->getResourceList();
        $roles = $acl->getRoleNames();
        $this->view->assign(compact('acl', 'resources', 'roles'));
    }
}