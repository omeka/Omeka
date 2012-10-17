<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * A user and its metadata.
 * 
 * @package Omeka\Record
 */
class User extends Omeka_Record_AbstractRecord 
    implements Zend_Acl_Resource_Interface, Zend_Acl_Role_Interface {

    public $username;
    
    /**
     * @var string This field should never contain the plain-text password.  Always
     * use setPassword() to change the user password.
     */
    public $password;
    public $salt;
    public $active = '0';
    public $role;
    public $name;
    public $email;
    
    const USERNAME_MIN_LENGTH = 1;
    const USERNAME_MAX_LENGTH = 30;
    const PASSWORD_MIN_LENGTH = 6;
    
    const INVALID_EMAIL_ERROR_MSG = "That email address is not valid.  A valid email address is required.";
    const CLAIMED_EMAIL_ERROR_MSG = "That email address has already been claimed by a different user. Please notify an administrator if you feel this has been done in error.";
    
    protected function beforeSave($args)
    {
        if ($args['post']) {
            $post = $args['post'];
            
            // Permissions check to see if whoever is trying to change role to a super-user
            if (!empty($post['role'])) {
                $bootstrap = Zend_Registry::get('bootstrap');
                $acl = $bootstrap->getResource('Acl');
                $currentUser = $bootstrap->getResource('CurrentUser');
                if ($post['role'] == 'super' && !$acl->isAllowed($currentUser, 'Users', 'makeSuperUser')) {
                    $this->addError('role', __('User may not change permissions to super-user'));
                }
                if (!$acl->isAllowed($currentUser, $this, 'change-role')) {
                    $this->addError('role', __('User may not change roles.'));
                }
            }
        }
    }
    
    protected function filterPostData($post)
    {
        $options = array('inputNamespace'=>'Omeka_Filter');
        
        // Alphanumeric with no whitespace allowed, lowercase
        $username_filter = array(new Zend_Filter_Alnum(false), 'StringToLower');
        
        // User form input does not allow HTML tags or superfluous whitespace
        $filters = array('*'        => array('StripTags','StringTrim'),
                         'username' => $username_filter,
                         'active'   => 'Boolean');
            
        $filter = new Zend_Filter_Input($filters, null, $post, $options);
        
        $post = $filter->getUnescaped();
                
        return $post;
    }
    
    public function setPostData($post)
    {
        // potential security hole
        if (isset($post['password'])) {
             unset($post['password']);
        }
        if (array_key_exists('salt', $post)) {
            unset($post['salt']);
        }
        return parent::setPostData($post);
    }
    
    protected function _validate()
    {
        if (!trim($this->name)) {
            $this->addError('name', __('Real Name is required.'));
        }
            
        if (!Zend_Validate::is($this->email, 'EmailAddress')) {
            $this->addError('email', __(self::INVALID_EMAIL_ERROR_MSG));
        }
            
        if (!$this->fieldIsUnique('email')) {
            $this->addError('email', __(self::CLAIMED_EMAIL_ERROR_MSG));            
        }
        
        //Validate the role
        if (trim($this->role) == '') {
            $this->addError('role', __('The user must be assigned a role.'));
        }
        
        // Validate the username
        if (strlen($this->username) < self::USERNAME_MIN_LENGTH || strlen($this->username) > self::USERNAME_MAX_LENGTH) {
            $this->addError('username', __('The username "%1$s" must be between %2$s and %3$s characters.',$this->username, self::USERNAME_MIN_LENGTH, self::USERNAME_MAX_LENGTH));
        } else if (!Zend_Validate::is($this->username, 'Alnum')) {
            $this->addError('username', __("The username must be alphanumeric."));
        } else if (!$this->fieldIsUnique('username')) {
            $this->addError('username', __("'%s' is already in use. Please choose another username.", $this->username));
        }
    }
            
    /**
     * Upgrade the hashed password.  Does nothing if the user/password is 
     * incorrect, or if same has been upgraded already.
     * 
     * @since 1.3
     * @param string $username
     * @param string $password
     * @return boolean False if incorrect username/password given, otherwise true
     * when password can be or has been upgraded.
     */
    public static function upgradeHashedPassword($username, $password)
    {        
        $userTable = get_db()->getTable('User');
        $user = $userTable->findBySql("username = ? AND salt IS NULL AND password = SHA1(?)", 
                                             array($username, $password), true);
        if (!$user) {
            return false;
        }
        $user->setPassword($password);
        $user->save();
        return true;
    }
    
    public function getRoleId()
    {
        if (!$this->role) {
            throw new RuntimeException(__('The user must be assigned a role.'));
        }
        return $this->role;
    }  
    
    public function getResourceId()
    {
        return 'Users';
    }     
    
    /**
     * Generate a simple 16 character salt for the user.
     */
    public function generateSalt()
    {
        $this->salt = substr(md5(mt_rand()), 0, 16);
    }   
    
    public function setPassword($password)
    {
        if ($this->salt === null) {
            $this->generateSalt();
        }
        $this->password = $this->hashPassword($password);
    }
    
    public function hashPassword($password)
    {
        return sha1($this->salt . $password);
    }
}
