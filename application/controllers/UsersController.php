<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * @package Omeka\Controller
 */
class UsersController extends Omeka_Controller_AbstractActionController
{
    /**
     * Actions that are accessible by anonymous users.
     *
     * @var array
     */
    protected $_publicActions = array('login', 'activate', 'forgot-password');

    protected $_browseRecordsPerPage = 10;
        
    public function init() {
        $this->_helper->db->setDefaultModelName('User');
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
            $this->_helper->redirector('index', 'index');
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
     * Send an email providing a link that allows the user to reset their password.
     */
    public function forgotPasswordAction()
    {
        if (empty($_POST)) {
            return;
        }

        $errorMessage = __('Unable to reset password. Please verify that the information is correct and contact an administrator if necessary.');
        
        $email = $_POST['email'];
        
        if (!Zend_Validate::is($email, 'EmailAddress')) {
            $this->_helper->flashMessenger($errorMessage, 'error');
            return;
        }
        
        $user = $this->_helper->db->findByEmail($email);
        
        if (!$user || $user->active != 1) {
            $this->_helper->flashMessenger($errorMessage, 'error');
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
        $this->_helper->flashMessenger(__('Please check your email for a link to reset your password.'), 'success');
    }
    
    public function activateAction()
    {
        $hash = $this->_getParam('u');
        $ua = $this->_helper->db->getTable('UsersActivations')->findBySql("url = ?", array($hash), true);
            
        if (!$ua) {
            $this->_helper->flashMessenger(__('Invalid activation code given.'), 'error');
            return $this->_helper->redirector('forgot-password', 'users');
        }

        $user = $ua->User;
        $this->view->user = $user;
        
        if ($this->getRequest()->isPost()) {
            if ($_POST['new_password1'] != $_POST['new_password2']) {
                $this->_helper->flashMessenger(__('Password: The passwords do not match.'), 'error');
                return;
            }
            
            $user->setPassword($_POST['new_password1']);
            $user->active = 1;
            if ($user->save(false)) {
                $ua->delete();
                $this->_helper->flashMessenger(__('You may now log in to Omeka.'), 'success');
                $this->_helper->redirector('login');
            } else {
                $this->_helper->flashMessenger($user->getErrors());
            }
        }
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
        
        $user->setPostData($_POST);
        if ($user->save(false)) {
            if ($this->sendActivationEmail($user)) {
                $this->_helper->flashMessenger(
                    __('The user "%s" was successfully added!', $user->username),
                    'success'
                );
            } else {
                $this->_helper->flashMessenger(__('The user "%s" was added, but the activation email could not be sent.',
                    $user->username));
            }
            //Redirect to the main user browse page
            $this->_helper->redirector('browse');
        } else {
            $this->_helper->flashMessenger($user->getErrors());
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
        $user = $this->_helper->db->findById();
        $currentUser = $this->getCurrentUser();
        
        $changePasswordForm = new Omeka_Form_ChangePassword;
        $changePasswordForm->setUser($user);
        
        // Super users don't need to know the current password.
        if ($currentUser && $currentUser->role == 'super') {
            $changePasswordForm->removeElement('current_password');
        }
        
        $form = $this->_getUserForm($user);
        $form->setSubmitButtonText(__('Save Changes'));
        $form->setDefaults(array(
            'username' => $user->username,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'active' => $user->active
        ));
        
        $keyTable = $this->_helper->db->getTable('Key');

        $this->view->passwordForm = $changePasswordForm;
        $this->view->user = $user;
        $this->view->currentUser = $currentUser;
        $this->view->form = $form;
        $this->view->keys = $keyTable->findBy(array('user_id' => $user->id));
        
        if ($this->getRequest()->isPost()) {
            $success = false;
            if (isset($_POST['update_api_keys'])) {
                // Create a new API key.
                if ($this->getParam('api_key_label')) {
                    $key = new Key;
                    $key->user_id = $user->id;
                    $key->label = $this->getParam('api_key_label');
                    $key->key = sha1($user->username . microtime() . rand());
                    $key->save();
                    $this->_helper->flashMessenger(__('A new API key was successfully created.'), 'success');
                    $success = true;
                }
                // Rescend API keys.
                if ($this->getParam('api_key_rescind')) {
                    foreach ($this->getParam('api_key_rescind') as $keyId) {
                        $keyTable->find($keyId)->delete();
                    }
                    $this->_helper->flashMessenger(__('An existing API key was successfully rescinded.'), 'success');
                    $success = true;
                }
            } elseif (isset($_POST['new_password'])) {
                if (!$changePasswordForm->isValid($_POST)) {
                    $this->_helper->flashMessenger(__('There was an invalid entry on the form. Please try again.'), 'error');
                    return;
                }
                
                $values = $changePasswordForm->getValues();
                $user->setPassword($values['new_password']);
                $user->save();
                $this->_helper->flashMessenger(__('Password changed!'), 'success');
                $success = true;
            } else {
                if (!$form->isValid($_POST)) {
                    $this->_helper->flashMessenger(__('There was an invalid entry on the form. Please try again.'), 'error');
                    return;
                }
                
                $user->setPostData($form->getValues());
                if ($user->save(false)) {
                    $this->_helper->flashMessenger(
                        __('The user %s was successfully changed!', $user->username),
                        'success'
                    );
                    $success = true;
                } else {
                    $this->_helper->flashMessenger($user->getErrors());
                }
            }
            
            if ($success) {
                // Redirect to the current page
                $this->_helper->redirector->gotoRoute();
            }
        }
    }
    
    public function browseAction()
    {
        if(isset($_GET['search'])) {
            $this->setParam($_GET['search-type'], $_GET['search']);
        }
        parent::browseAction();
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
             . 'system, but will no longer be associated with this user.', $user->username);
    }

    /**
     * Send an activation email to a new user telling them how to activate
     * their account.
     *
     * @param User $user
     * @return boolean True if the email was successfully sent, false otherwise.
     */
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
                    . __('Your account for the %s repository has been created. Please click the following link to activate your account:',$siteTitle)."\n\n"
                    . WEB_ROOT . "/admin/users/activate?u={$ua->url}\n\n"
                    . __('%s Administrator', $siteTitle);
        $subject    = __('Activate your account with the %s repository', $siteTitle);
        
        $mail = new Zend_Mail();
        $mail->setBodyText($body);
        $mail->setFrom($from, "$siteTitle Administrator");
        $mail->addTo($user->email, $user->name);
        $mail->setSubject($subject);
        $mail->addHeader('X-Mailer', 'PHP/' . phpversion());
        try {
            $mail->send();
            return true;
        } catch (Zend_Mail_Transport_Exception $e) {
            $logger = $this->getInvokeArg('bootstrap')->getResource('Logger');
            if ($logger) {
                $logger->log($e, Zend_Log::ERR);
            }
            return false;
        }
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
        
        $authAdapter = new Omeka_Auth_Adapter_UserTable($this->_helper->db->getDb());
        $pluginBroker = $this->getInvokeArg('bootstrap')->getResource('Pluginbroker');
        // If there are no plugins filtering the login adapter, set the 
        // credentials for the default adapter.
        if (!$pluginBroker || !$pluginBroker->getFilters('login_adapter')) {
            $authAdapter->setIdentity($loginForm->getValue('username'))
                        ->setCredential($loginForm->getValue('password'));
        } else {
            $authAdapter = apply_filters('login_adapter', $authAdapter, array('login_form' => $loginForm));
        }
        $authResult = $this->_auth->authenticate($authAdapter);
        if (!$authResult->isValid()) {
            if ($log = $this->_getLog()) {
                $ip = $this->getRequest()->getClientIp();
                $log->info("Failed login attempt from '$ip'.");
            }
            $this->_helper->flashMessenger($this->getLoginErrorMessages($authResult), 'error');
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
            $this->_helper->redirector->gotoUrl($session->redirect);
        } else {
            $this->_helper->redirector->gotoUrl('/');
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
                return __('Login information incorrect. Please try again.');
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
        $this->_helper->redirector->gotoUrl('');
    }
    
    protected function _getUserForm(User $user)
    {
        $hasActiveElement = $user->exists()
            && $this->_helper->acl->isAllowed('change-status', $user);

        $form = new Omeka_Form_User(array(
            'hasRoleElement'    => $this->_helper->acl->isAllowed('change-role', $user),
            'hasActiveElement'  => $hasActiveElement,
            'user'              => $user
        ));
        fire_plugin_hook('users_form', array('form' => $form, 'user' => $user));
        return $form;
    }

    private function _getLog()
    {
        return $this->getInvokeArg('bootstrap')->logger;
    }
}
