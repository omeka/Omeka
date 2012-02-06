<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * 
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2009
 **/
class Omeka_Login_Db implements Omeka_Login_Interface
{    
    /**
     * @var Omeka_Form
     */
    protected $_form;
    
    /**
     * @var Omeka_Db
     */
    protected $_db;
    
    /**
     * @var Zend_Auth_Adapter_DbTable
     */
    protected $_authAdapter;
    
    public function __construct(Omeka_Db $db)
    {
        $this->_db = $db;
    }
        
    public function getForm()
    {
        //     <fieldset>
        //         <div class="field">
        //     <label for="username">Username</label> 
        //     <input type="text" name="username" class="textinput" id="username" />
        //     </div>
        //     <div class="field">
        //     <label for="password">Password</label> 
        //     <input type="password" name="password" class="textinput" id="password" />
        //     </div>
        // 
        //     <div class="field">
        //         <label for="remember">Remember Me?</label> 
        //         <?php echo $this->formCheckbox('remember', null, array('class' => 'checkbox')); 
        //     </div>
        //     </fieldset>
        //     <div><input type="submit" class="submit-small submit" value="Log In" /></div>
        
        if ($this->_form) {
            return $this->_form;
        }
        
        $form = new Omeka_Form;
        $form->setAttrib('id', 'login-form');
        $form->addElement('text', 'username', array(
            'label'     => 'Username',
            'required'  => true));
        $form->addElement('password', 'password', array(
            'label'=>'Password',
            'required'  => true));
        $form->addElement('checkbox', 'remember', array('class'=>'checkbox', 'label'=>'Remember Me?'));
        $form->addDisplayGroup(array('username','password','remember'), 'login');
        $form->addElement('submit', 'submit', array('label'=>'Log In'));
        
        $this->_form = $form;
        return $form;
    }
    
    protected function _getAuthAdapter()
    {
        if ($this->_authAdapter === null) {
            // Authenticate against the 'users' table in Omeka.
            $this->_authAdapter = new Zend_Auth_Adapter_DbTable($this->_db->getAdapter(), $this->_db->User, 'username', 'password', 'SHA1(?) AND active = 1');
        }
        
        return $this->_authAdapter;
    }
    
    public function authenticate(Zend_Auth $auth, $input)
    {
        $loginForm = $this->getForm();
        if (!$loginForm->isValid($input)) {
            return $this->_getAuthResultForFormErrors($loginForm);
        }
        
        $adapter = $this->_getAuthAdapter();
        $adapter->setIdentity($loginForm->getValue('username'))
                    ->setCredential($loginForm->getValue('password'));
        $result = $auth->authenticate($adapter);
        if ($result->isValid()) {
            // Auth_Adapter_DbTable returns the username as the identity, but 
            // we want the user ID to avoid any possible confusion.
            $row = $adapter->getResultRowObject(array('id'));
            $auth->getStorage()->write($row->id);

            if ($loginForm->getValue('remember')) {
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
    
    private function _getAuthResultForFormErrors(Zend_Form $loginForm)
    {
        // Zend_Auth_Result error messages are not a nested array, apparently?
        // So we need to simplify this array.
        $errorMessages = $loginForm->getMessages();
        foreach ($errorMessages as $key => $errors) {
            $errorMessages[$key] = join("\n", $errors);
        }
        return new Zend_Auth_Result(Zend_Auth_Result::FAILURE, $loginForm->getValue('username'), $errorMessages);
    }
}
