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
        $this->_helper->db->setDefaultModelName('User');
        $this->checkPermissions();  //Cannot execute as a beforeFilter b/c ACL permissions are checked before that.
        $this->_auth = $this->getInvokeArg('bootstrap')->getResource('Auth');
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
        
        if (!$this->isAllowed($action, $record)) {
            $this->_helper->redirector->goto('browse');            
        } else {
            $this->_helper->acl->setAllowed($action);
        }
    }
    
    /**
     * Send an email providing a link that allows the user to reset their password.
     */
    public function forgotPasswordAction()
    {
        if (empty($_POST)) {
            return;
        }
        
        $email = $_POST['email'];
        
        if (!Zend_Validate::is($email, 'EmailAddress')) {
            return $this->flashError('The email address you provided is invalid.  Please enter a valid email address.');
        }
        
        $user = $this->_helper->db->findByEmail($email);
        
        if (!$user) {
            $this->flashError('The email address provided cannot be found.');
            return;
        }
        
        //Create the activation url
        $ua = new UsersActivations;
        $ua->user_id = $user->id;
        $ua->save();
        
        $this->_sendResetPasswordEmail($email, $ua->url);
    }
    
    private function _sendResetPasswordEmail($toEmail, $activationCode)
    {
        $siteTitle = get_option('site_title');
        
        $mail = new Zend_Mail();
        $mail->addTo($toEmail);                
        $mail->addHeader('X-Mailer', 'PHP/' . phpversion());
        
        //Send the email with the activation url
        $url   = "http://".$_SERVER['HTTP_HOST'].$this->getRequest()->getBaseUrl().'/users/activate?u='.$activationCode;
        $body  = "Please follow this link to reset your password:\n\n";
        $body .= $url."\n\n";
        $body .= "$siteTitle Administrator";

        $mail->setBodyText($body);
        $mail->setFrom(get_option('administrator_email'), "$siteTitle Administrator");
        $mail->setSubject("[$siteTitle] Reset Your Password");

        $mail->send();
        $this->flashSuccess('Please check your email for a link to reset your password.');
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
            if ($user->saveForm($_POST)) {                
                $this->sendActivationEmail($user);
                $this->flashSuccess('The user "' . $user->username . '" was successfully added!');
                                
                //Redirect to the main user browse page
                $this->redirect->goto('browse');
            }
        } catch (Omeka_Validator_Exception $e) {
            $this->flashValidationErrors($e);
        }
    }

    /**
     * Similar to 'add' action, except this requires a pre-existing record.
     * 
     * The ID For this record must be passed via the 'id' parameter.
     *
     * @return void
     **/
    public function editAction()
    {        
        $user = $this->findById();
        
        try {
            if ($user->saveForm($_POST)) {
                $this->flashSuccess('The user "' . $user->username . '" was successfully changed!');
                $this->redirect->goto('browse');
            }
        } catch (Omeka_Validator_Exception $e) {
            $this->flashValidationErrors($e);
        } catch (Exception $e) {
            $this->flashError($e->getMessage());
        }
        
        $this->view->assign(array('user'=>$user));        
    }
    
    protected function _getDeleteSuccessMessage($record)
    {
        $user = $record;
        return 'The user "' . $user->username . '" was successfully deleted!';
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
        $this->getInvokeArg('bootstrap')->getResource('Logger')
                                        ->info("Activation email sent to '{$entity->email}' on " . Zend_Date::now());
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
        if ($loggedInUser = $this->getInvokeArg('bootstrap')->getResource('Currentuser')) {
            $this->redirect->goto('index', 'index');
        }
        
        // require_once is necessary because lacking form autoloading.
        require_once APP_DIR .DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR .'Login.php';
        $loginForm = new Omeka_Form_Login;
        $loginForm = apply_filters('login_form', $loginForm);
        
        $this->view->form = $loginForm;
        
        if (!$this->getRequest()->isPost()) {
            return;            
        }    

        if (($loginForm instanceof Zend_Form) && !$loginForm->isValid($_POST)) {
            return;
        }
        
        $authAdapter = new Omeka_Auth_Adapter_UserTable($this->getDb());
        $pluginBroker = $this->getInvokeArg('bootstrap')->getResource('Pluginbroker');
        // If there are no plugins filtering the login adapter, set the 
        // credentials for the default adapter.
        if (!$pluginBroker || !$pluginBroker->getFilters('login_adapter')) {
            $authAdapter->setIdentity($loginForm->getValue('username'))
                        ->setCredential($loginForm->getValue('password'));
        } else {
            $authAdapter = apply_filters('login_adapter', $authAdapter, $loginForm);   
        }
        $authResult = $this->_auth->authenticate($authAdapter);
        if (!$authResult->isValid()) {
            $this->view->assign(array('errorMessage' => $this->getLoginErrorMessages($authResult)));
            return;   
        }
        
        if ($loginForm && $loginForm->getValue('remember')) {
            // Remember that a user is logged in for the default amount of 
            // time (2 weeks).
            Zend_Session::rememberMe();
        } else {
            // If a user doesn't want to be remembered, expire the cookie as
            // soon as the browser is terminated.
            Zend_Session::forgetMe();
        }
        
        $session = new Zend_Session_Namespace;
        if ($session->redirect) {
            $this->redirect->gotoUrl($session->redirect);
        } else {
            $this->redirect->gotoUrl('/');
        }
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
}
