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
    protected $_browseRecordsPerPage = 10;
        
    public function init() {
        $this->_modelClass = 'User';
        $this->_table = $this->getTable('User');
        $this->checkPermissions();  //Cannot execute as a beforeFilter b/c ACL permissions are checked before that.
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
        $this->checkUserSpecificPerms($action);
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
                    // Allow access to the 'edit' action if a user is editing their 
                    // own account info.
                    if ($user->id == $record->id) {
                        $this->_helper->acl->setAllowed('edit');
                    }
                     
                    //Non-super users cannot edit super user data
                    //Note that super users can edit other super users' data
                    if ($user->id != $record->id 
                        && $record->role == 'super' 
                        && $user->role != 'super') {
                        throw new Exception( 'You may not edit the data for super users!' );
                    }
                    break;
                case 'show':
                    // Allow access to the 'show' action if a user is viewing their 
                    // own account info.
                    if ($user->id == $record->id) {
                        $this->_helper->acl->setAllowed('show');
                    }
                    break;    
               default:
                   break;
            }                
        } catch (Exception $e) {
            $this->flash($e->getMessage(), Omeka_Controller_Flash::GENERAL_ERROR);
            $this->_helper->redirector->goto('browse');
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
                
                //$user->email = $_POST['email'];
                $this->sendActivationEmail($user);
                
                $this->flashSuccess('User was added successfully!');
                                
                //Redirect to the main user browse page
                $this->redirect->goto('browse');
            }
        } catch (Omeka_Validator_Exception $e) {
            $this->flashValidationErrors($e);
        }
    }
    
    protected function sendActivationEmail($user)
    {
        $ua = new UsersActivations;
        $ua->user_id = $user->id;
        $ua->save();
        
        // send the user an email telling them about their new user account
        $siteTitle  = get_option('site_title');
        $from       = get_option('administrator_email');
        $body       = "Welcome!\n\n"
                    . "Your account for the $siteTitle archive has been created. Please click the following link to activate your account:\n\n"
                    . WEB_ROOT . "/admin/users/activate?u={$ua->url}\n\n"
                    . "(or use any other page on the site).\n\n"
                    . "Be aware that we log you out after 15 minutes of inactivity to help protect people using shared computers (at libraries, for instance).\n\n" 
                    ."$siteTitle Administrator";
        $subject    = "Activate your account with the ".$siteTitle." Archive";
        
        $entity = $user->Entity;
        
        $mail = new Zend_Mail();
        $mail->setBodyText($body);
        $mail->setFrom($from, "$siteTitle Administrator");
        $mail->addTo($entity->email, $entity->getName());
        $mail->setSubject($subject);
        $mail->addHeader('X-Mailer', 'PHP/' . phpversion());
        $mail->send();
    }
    
    
    public function changePasswordAction()
    {
        $user = $this->findById();

        try {
            //somebody is trying to change the password
            if (!empty($_POST['new_password1']) or !empty($_POST['new_password2'])) {
                $user->changePassword($_POST['new_password1'], $_POST['new_password2'], $_POST['old_password']);
                $user->forceSave();
                $this->flashSuccess('Password was changed successfully.');
            } else {
                $this->flashError('Password field must be properly filled out.');
            }
        } catch (Exception $e) {
            $this->flashError($e->getMessage());
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
        $rememberMe = $_POST['remember'];
        $db = $this->getDb();
        $dbAdapter = $db->getAdapter();
        // Authenticate against the 'users' table in Omeka.
        $adapter = new Zend_Auth_Adapter_DbTable($dbAdapter, $db->User, 'username', 'password', 'SHA1(?) AND active = 1');
        $adapter->setIdentity($username)
                    ->setCredential($password);
        $result = $this->_auth->authenticate($adapter);
        if ($result->isValid()) {
            $storage = $this->_auth->getStorage();
            $storage->write($adapter->getResultRowObject(array('id', 'username', 'role', 'entity_id')));
            $session = new Zend_Session_Namespace($storage->getNamespace());
            if ($rememberMe) {
                // Remember that a user is logged in for the default amount of 
                // time (2 weeks).
                Zend_Session::rememberMe();
            } else {
                // If a user doesn't want to be remembered, expire the cookie as
                // soon as the browser is terminated.
                Zend_Session::forgetMe();
            }
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
}