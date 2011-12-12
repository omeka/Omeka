<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */

/**
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private 
 * @package Omeka
 * @subpackage Controllers
 * @author CHNM
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class UsersController extends Omeka_Controller_Action
{
    const INVALID_LOGIN_MESSAGE = 'Login information incorrect. Please try again.';

    /**
     * Actions that are accessible by anonymous users.
     *
     * @var array
     */
    protected $_publicActions = array('login', 'activate', 'forgot-password');

    protected $_browseRecordsPerPage = 10;
        
    public function init() {
        $this->_modelClass = 'User';
        $this->_table = $this->getTable('User');
        $this->_getUserAcl();
        $this->_auth = $this->getInvokeArg('bootstrap')->getResource('Auth');

        $this->_handlePublicActions();
    }

    /**
     * Peform common processing for the publicly accessible actions.
     * 
     * Set a view script variable for header and footer view scripts and
     * don't allow logged-in users access.
     *
     * The script variables are set for actions in $_publicActions, so
     * the scripts for those actions should use these variables.
     */
    protected function _handlePublicActions() {
        $action = $this->_request->getActionName();
        if (!in_array($action, $this->_publicActions)) {
            return;
        }

        // If a user is already logged in, they should always get redirected back to the dashboard.
        if ($loggedInUser = $this->getInvokeArg('bootstrap')->getResource('Currentuser')) {
            $this->_helper->redirector->goto('index', 'index');
        }

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

    /** 
     * Retrieve the record associated with the user so that it can be checked directly
     * by the Acl action helper.
     */
    private function _getUserAcl()
    {
        try {
            $user = $this->findById();
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
            return $this->flashError(__('Unable to reset password. Please verify that the information is correct and contact an administrator if necessary.'));
        }
        
        $user = $this->_table->findByEmail($email);
        
        if (!$user || $user->active != 1) {
            $this->flashError(__('Unable to reset password. Please verify that the information is correct and contact an administrator if necessary.'));
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
        $body  = __("Please follow this link to reset your password:") . "\n\n";
        $body .= $url."\n\n";
        $body .= __("%s Administrator", $siteTitle);

        $mail->setBodyText($body);
        $mail->setFrom(get_option('administrator_email'), __("%s Administrator", $siteTitle));
        $mail->setSubject(__("[%s] Reset Your Password", $siteTitle));

        $mail->send();
        $this->flashSuccess(__('Please check your email for a link to reset your password.'));
    }
    
    public function activateAction()
    {
        $hash = $this->_getParam('u');
        $ua = $this->getTable('UsersActivations')->findBySql("url = ?", array($hash), true);
            
        if (!$ua) {
            $this->flashError('Invalid activation code given.');
            return $this->_helper->redirector->goto('forgot-password', 'users');
        }
        
        if (!empty($_POST)) {
            try {
                if ($_POST['new_password1'] != $_POST['new_password2']) {
                    throw new Omeka_Validator_Exception(__('Password: The passwords do not match.'));
                }
                $ua->User->setPassword($_POST['new_password1']);
                $ua->User->active = 1;
                $ua->User->forceSave();
                $ua->delete();
                $this->flashSuccess(__('You may now log in to Omeka.'));
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
     */
    public function addAction()
    {
        $user = new User();
        
        $form = $this->_getUserForm($user);
        $form->setSubmitButtonText(__('Add User'));
        $this->view->form = $form;
        
        if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {
            return;
        }
        
        try {
            if ($user->saveForm($_POST)) {                
                $this->sendActivationEmail($user);
                $this->flashSuccess(__('The user "%s" was successfully added!',$user->username));
                                
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
     */
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
        $form->setSubmitButtonText(__('Save Changes'));
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
                $this->flashSuccess(__("Password changed!"));
                $success = true;
            }
        } else {
            if (!$form->isValid($_POST)) {
                return;
            }        
            try {
                if ($user->saveForm($form->getValues())) {
                    $this->flashSuccess(__('The user %s was successfully changed!', $user->username));
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
        return __('The user "%s" was successfully deleted!', $user->username);
    }
    
    protected function _getDeleteConfirmMessage($record)
    {
        $user = $record;
        return __('%s will be deleted from the system. Items, '
             . 'collections, and tags created by this user will remain in the '
             . 'archive, but will no longer be associated with this user.', $user->username);
    }
    
    protected function sendActivationEmail($user)
    {
        $ua = new UsersActivations;
        $ua->user_id = $user->id;
        $ua->save();
        
        // send the user an email telling them about their new user account
        $siteTitle  = get_option('site_title');
        $from       = get_option('administrator_email');
        $body       = __('Welcome!')
                    ."\n\n"
                    . __('Your account for the %s archive has been created. Please click the following link to activate your account:',$siteTitle)."\n\n"
                    . WEB_ROOT . "/admin/users/activate?u={$ua->url}\n\n"
                    . __('%s Administrator', $siteTitle);
        $subject    = __('Activate your account with the %s archive', $siteTitle);
        
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
        // require_once is necessary because lacking form autoloading.
        require_once APP_DIR . '/forms/Login.php';
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
                $ip = $this->getRequest()->getClientIp();
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
     */
    public function getLoginErrorMessages(Zend_Auth_Result $result)
    {
        $code = $result->getCode();
        switch ($code) {
            // Return the same output for these two cases to avoid revealing
            // information about valid usernames/passwords.
            case Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND:
            case Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID:
                return __(self::INVALID_LOGIN_MESSAGE);
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
        $hasActiveElement = $user->exists()
            && $this->_helper->acl->isAllowed('change-status', $user);

        $form = new Omeka_Form_User(array(
            'hasRoleElement'    => $this->_helper->acl->isAllowed('change-role', $user),
            'hasActiveElement'  => $hasActiveElement,
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
