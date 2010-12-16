<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 **/

/**
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private 
 * @package Omeka
 * @subpackage Controllers
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2010
 **/
class UsersController extends Omeka_Controller_Action
{
    const INVALID_LOGIN_MESSAGE = 'Login information incorrect. Please try again.';

    protected $_browseRecordsPerPage = 10;
        
    public function init() {
        $this->_modelClass = 'User';
        $this->_table = $this->getTable('User');
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
        $controlledActions = array(
            'delete',
            'changePassword',
            'edit',
            'show'
        );
        
        if (!in_array($action, $controlledActions)) {
            return;
        }
        
        $record = $this->findById();        
        switch ($action) {
           
            // If we are deleting users
           case 'delete':                   
               // Can't delete yourself
               if ($user->id == $record->id) {
                   $redirectWith = 'You are not allowed to delete yourself!';
               }
               break;
               
           //If changing passwords 
           case 'changePassword':
               
                // Only super users and the actual user can change this 
                // user's password
                if(!$user || (($user->role != 'super') && ($record->id != $user->id))) {
                    $redirectWith = 'May not change another user\'s password!';
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
                    $redirectWith = 'You may not edit the data for super users!';
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
        if (isset($redirectWith)) {
            $this->flash($redirectWith, Omeka_Controller_Flash::GENERAL_ERROR);
            $this->_helper->redirector->goto('browse');
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
            return $this->flashError('Unable to reset password. Please verify that the information is correct and contact an administrator if necessary.');
        }
        
        $user = $this->_table->findByEmail($email);
        
        if (!$user || $user->active != 1) {
            $this->flashError('Unable to reset password. Please verify that the information is correct and contact an administrator if necessary.');
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
        $url = $this->view->serverUrl() . $this->view->url(array(
            'controller' => 'users',
            'action' => 'activate',
            'u' => $activationCode
        ), 'default');
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
                $ua->User->setPassword($_POST['new_password1']);
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
        $changePasswordForm = new Omeka_Form_ChangePassword;
        $changePasswordForm->setUser($user);

        $currentUser = $this->getCurrentUser();

        // Super users don't need to know the current password.
        if ($currentUser && $currentUser->role == 'super') {
            $changePasswordForm->removeElement('current_password');
        }
        
        $this->view->passwordForm = $changePasswordForm;
        $this->view->user = $user;        
        
        if (isset($_POST['new_password'])) {
            if ($changePasswordForm->isValid($_POST)) {
                $values = $changePasswordForm->getValues();
                $user->setPassword($values['new_password']);
                $user->forceSave();
                $this->flashSuccess("Password changed!");
                return $this->_helper->redirector->gotoUrl('/');
            } else {
                return;
            }
        }
        
        try {
            if ($user->saveForm($_POST)) {
                $this->flashSuccess('The user "' . $user->username . '" was successfully changed!');
                
                if ($user->id == $currentUser->id) {
                    $this->_helper->redirector->gotoUrl('/');
                } else {
                    $this->_helper->redirector->goto('browse');
                }
            }
        } catch (Omeka_Validator_Exception $e) {
            $this->flashValidationErrors($e);
        } catch (Exception $e) {
            $this->flashError($e->getMessage());
        }        
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
        
        User::upgradeHashedPassword($loginForm->getValue('username'), 
                                    $loginForm->getValue('password'));
        
        $authAdapter = new Omeka_Auth_Adapter_UserTable($this->getDb());
        $pluginBroker = $this->getInvokeArg('bootstrap')->getResource('Pluginbroker');
        // If there are no plugins filtering the login adapter, set the 
        // credentials for the default adapter.
        if (!$pluginBroker->getFilters('login_adapter')) {
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
            // Return the same output for these two cases to avoid revealing
            // information about valid usernames/passwords.
            case Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND:
            case Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID:
                return self::INVALID_LOGIN_MESSAGE;
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
        $_SESSION = array();
        Zend_Session::destroy();
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
