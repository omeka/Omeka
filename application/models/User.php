<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @subpackage Models
 * @author CHNM
 */

/**
 * @package Omeka
 * @subpackage Models
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class User extends Omeka_Record implements Zend_Acl_Resource_Interface, 
                                           Zend_Acl_Role_Interface
{

    public $username;
    
    /**
     * @var string This field should never contain the plain-text password.  Always
     * use setPassword() to change the user password.
     */
    public $password;
    public $salt;
    public $active = '0';
    public $role;
    public $entity_id;
    
    private $_entity;
    
    const USERNAME_MIN_LENGTH = 1;
    const USERNAME_MAX_LENGTH = 30;
    const PASSWORD_MIN_LENGTH = 6;
    
    const INVALID_EMAIL_ERROR_MSG = "That email address is not valid.  A valid email address is required.";
    const CLAIMED_EMAIL_ERROR_MSG = "That email address has already been claimed by a different user. Please notify an administrator if you feel this has been done in error.";
        
    protected $_related = array('Entity'=>'getEntity');
    
    public function getEntity()
    {
        if (!$this->_entity) {
            if (!$this->exists() || empty($this->entity_id)) {
                $entity =  new Entity($this->getDb());
            } else {
                $entity = $this->getTable('Entity')->find((int) $this->entity_id);
            } 
            $this->_entity = $entity;
        }
        return $this->_entity;
    }
    
    /**
     * Overrides parent::__get() to transparently get properties from the Entity
     * associated with this user.
     */
    public function __get($property)
    {
        $entity = $this->getEntity();
        if (!($entity instanceof Entity)) {
            throw new Omeka_Record_Exception(__("No Entity record available."));
        }
        if (isset($entity->$property)) {
            return $entity->$property;
        } else {
            return parent::__get($property);
        }
    }
    
    protected function beforeSave()
    {
        if (!$this->Entity) {
            throw new Omeka_Record_Exception(__("No Entity record is available when saving this User."));
        }
        $this->Entity->save();
        $this->entity_id = $this->Entity->id;
    }
    
    protected function beforeSaveForm($post)
    {
        if (!$this->processEntity($post)) {
            return false;
        }
        
        // Permissions check to see if whoever is trying to change role to a super-user
        if (!empty($post['role'])) {
            $acl = Omeka_Context::getInstance()->getAcl();
            $currentUser = Omeka_Context::getInstance()->getCurrentUser();
            if ($post['role'] == 'super' && !$acl->isAllowed($currentUser, 'Users', 'makeSuperUser')) {
                throw new Omeka_Validator_Exception( __('User may not change permissions to super-user') );
            }
            if (!$acl->isAllowed($currentUser, $this, 'change-role')) {
                throw new Omeka_Validator_Exception(__('User may not change roles.'));
            }
        } 
                
        // If the User is not persistent we need to create a placeholder password
        if (!$this->exists()) {
            $this->setPassword($this->generatePassword(8));
        }        
        
        return true;
    }
    
    /**
     * @duplication Mostly duplicated in Item::filterInput()
     *
     * @return void
     */
    protected function filterInput($post)
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
    
    public function setFromPost($post)
    {
        // potential security hole
        if (isset($post['password'])) {
             unset($post['password']);
        }
        if (array_key_exists('salt', $post)) {
            unset($post['salt']);
        }
        return parent::setFromPost($post);
    }
    
    protected function _validate()
    {
        // Validate the entity of the user. This requires special validation 
        // within this class b/c the entities themselves have no particular 
        // validation.
        if ($entity = $this->Entity) {
            
            // Either need first and last name (or institution name) to validate.
            if (trim($entity->institution) == '') {
                if (trim($entity->first_name) == '' && trim($entity->last_name) == '') {
                    $this->addError('institution', __('If a first name and last name are not provided, then an institution name is required.'));
                } else {
                    if (trim($entity->first_name) == '') {
                        $this->addError('first_name', __('A first name is required.'));
                    }
                    if (trim($entity->last_name) == '') {
                        $this->addError('last_name', __('A last name is required.')); 
                    }
                }
            }
            
            if (!Zend_Validate::is($entity->email, 'EmailAddress')) {
                $this->addError('email', __(self::INVALID_EMAIL_ERROR_MSG));
            }
            
            if (!$this->emailIsUnique($entity->email)) {
                $this->addError('email', __(self::CLAIMED_EMAIL_ERROR_MSG));            
            }                 
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
     * This will check the set of IDs for users that have a specific email address.  
     * If it is greater than 1, or if the 
     *
     * @return bool
     */
    private function emailIsUnique($email)
    {
        $db = $this->getDb();
        $sql = "
        SELECT u.id 
        FROM $db->User u 
        INNER JOIN $db->Entity e 
        ON e.id = u.entity_id 
        WHERE e.email = ?";
        
        $id = $db->query($sql, array($email))->fetchAll();
        
        // Either there is nothing stored in the DB yet, or there is only one 
        // and it belongs to this one
        return (!count($id) or ((count($id) == 1) && ($id[0]['id'] == $this->id)));
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
        $user->forceSave();
        return true;
    }
    
    protected function processEntity(&$post)
    {    
        $entity = $this->Entity;
        
        //If the entity is new, then determine whether it is an institution or a person
        if (empty($entity)) {
            $entity = new Entity;
        }
        
        //The new email address is fully legit, so set the entity to the new info                
        $entityKeys = array('first_name', 'last_name', 'institution', 'email');

        foreach ($entityKeys as $key) {
            if (array_key_exists($key, $post)) {
                $entity[$key] = $post[$key];
                unset($post[$key]);
            }
        }
        
        $this->Entity = $entity;
                        
        return true;
    }
    
    protected function afterDelete()
    {
        if ($this->entity_id) {
            $this->Entity->delete();
        }
    }
    
    /* Generate password. (i.e. jachudru, cupheki) */
    // http://www.zend.com/codex.php?id=215&single=1
    protected function generatePassword($length) 
    {
        $vowels = array('a', 'e', 'i', 'o', 'u', '1', '2', '3', '4', '5', '6');
        $cons = array('b', 'c', 'd', 'g', 'h', 'j', 'k', 'l', 'm', 'n', 'p', 
                      'r', 's', 't', 'u', 'v', 'w', 'tr', 'cr', 'br', 'fr', 
                      'th', 'dr', 'ch', 'ph', 'wr', 'st', 'sp', 'sw', 'pr', 
                      'sl', 'cl');
        
        $num_vowels = count($vowels);
        $num_cons   = count($cons);
        
        $password = '';
        while (strlen($password) < $length){
            $password .= $cons[mt_rand(0, $num_cons - 1)] . $vowels[mt_rand(0, $num_vowels - 1)];
        }
        $this->setPassword($password);
        return $password;
    }      
    
    public function getRoleId()
    {
        if (!$this->role) {
            die("Should not be using a non-existent user role.");
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
        assert('$this->salt !== null');
        return sha1($this->salt . $password);
    }
}
