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

    /**
     * Actions that need a different header/footer for the admin side.
     *
     * @var array
     */
    protected $_alternateCommonActions = array('login', 'activate', 'forgot-password');

    protected $_browseRecordsPerPage = 10;
        
    public function init() {
        $this->_helper->db->setDefaultModelName('User');
        $this->_getUserAcl();
        $this->_auth = $this->getInvokeArg('bootstrap')->getResource('Auth');

        $this->_setCommonScripts();
    }

    /**
     * Set a view script variable for what header and footer views to use.
     *
     * These variables are set for actions in $_alternateCommonActions, so
     * the scripts for those actions should use these variables.
     */
    protected function _setCommonScripts() {
        $action = $this->_request->getActionName();
        if (in_array($action, $this->_alternateCommonActions)) {
            if (is_admin_theme()) {
                $header = 'login-header';
                $footer = 'login-footer';
            } else {
                $header = 'header';
                $footer = 'footer';
            }
            $this->view->header = $header;
            $this->view->footer = $footer;
        }
    }

    /** 
     * Retrieve the record associated with the user so that it can be checked directly
     * by the Acl action helper.
     */
    private function _getUserAcl()
    {
        try {
            $user = $this->_helper->db->findById();
        } catch (Omeka_Controller_Exception_404 $e) {
            return;
        }

        $this->aclResource = $user;
        $this->aclRequest = clone $this->getRequest();
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
        
        $user = $this->_helper->db->findByEmail($email);
        
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
            die('Invalid activation code given.');
        }
        
        if (!empty($_POST)) {
            try {
                if ($_POST['new_password1'] != $_POST['new_password2']) {
                    throw new Omeka_Validator_Exception('Password: The passwords do not match.');
                }
                $ua->User->setPassword($_POST['new_password1']);
                $ua->User->active = 1;
                $ua->User->forceSave();
                $ua->delete();
                $this->flashSuccess('You may now log in to Omeka.');
                $this->redirect->goto('login');
            } catch (Omeka_Validator_Exception $e) {
                $this->flashValidationErrors($e);
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
        
        $form = $this->_getUserForm($user);
        $form->setSubmitButtonText('Add User');
        $this->view->form = $form;
        
        if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {
            return;
        }
        
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
        $success = false;
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

        $form = $this->_getUserForm($user);
        $form->setSubmitButtonText('Save Changes');
        $form->setDefaults(array(
            'username' => $user->username,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'institution' => $user->institution,
            'role' => $user->role,
            'active' => $user->active
        ));
        $this->view->form = $form;
        
        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (isset($_POST['new_password'])) {
            if ($changePasswordForm->isValid($_POST)) {
                $values = $changePasswordForm->getValues();
                $user->setPassword($values['new_password']);
                $user->forceSave();
                $this->flashSuccess("Password changed!");
                $success = true;
            }
        } else {
            if (!$form->isValid($_POST)) {
                return;
            }        
            try {
                if ($user->saveForm($form->getValues())) {
                    $this->flashSuccess('The user "' . $user->username . '" was successfully changed!');
                    $success = true;
                }
            } catch (Omeka_Validator_Exception $e) {
                $this->flashValidationErrors($e);
            }
        }

        if ($success) {
            if ($user->id == $currentUser->id) {
                $this->_helper->redirector->gotoUrl('/');
            } else {
                $this->_helper->redirector->goto('browse');
            }
        }
    }
    
    protected function _getDeleteSuccessMessage($record)
    {
        $user = $record;
        return 'The user "' . $user->username . '" was successfully deleted!';
    }
    
    protected function _getDeleteConfirmMessage($record)
    {
        $user = $record;
        return "$user->username will be deleted from the system. Items, "
             . 'collections, and tags created by this user will remain in the '
             . 'archive, but will no longer be associated with this user.';
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
        if (!$pluginBroker || !$pluginBroker->getFilters('login_adapter')) {
            $authAdapter->setIdentity($loginForm->getValue('username'))
                        ->setCredential($loginForm->getValue('password'));
        } else {
            $authAdapter = apply_filters('login_adapter', $authAdapter, $loginForm);   
        }
        $authResult = $this->_auth->authenticate($authAdapter);
        if (!$authResult->isValid()) {
            if ($log = $this->_getLog()) {
                $ip = @$_SERVER['REMOTE_ADDR'];
                $log->info("Failed login attempt from '$ip'.");
            }
            $this->flashError($this->getLoginErrorMessages($authResult));
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
    
    private function _getUserForm(User $user)
    {
        $form = new Omeka_Form_User(array(
            'hasRoleElement'    => $this->_helper->acl->isAllowed('change-role', $user),
            'hasActiveElement'  => $this->_helper->acl->isAllowed('change-status', $user),
            'user'              => $user
        ));
        fire_plugin_hook('admin_append_to_users_form', $form, $user);
        return $form;
    }

private function _getLog()
    {
        return $this->getInvokeArg('bootstrap')->logger;
    }
}
