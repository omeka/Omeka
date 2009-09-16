<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @subpackage Models
 * @author CHNM
 **/

require_once 'UsersActivations.php';
require_once 'UserTable.php';
require_once 'Entity.php';
require_once 'Item.php';

class User extends Omeka_Record {

    public $username;
    public $password;
    public $active = '0';
    public $role;
    public $entity_id;
    
    const USERNAME_MIN_LENGTH = 1;
    const USERNAME_MAX_LENGTH = 30;
    const PASSWORD_MIN_LENGTH = 6;
    const PASSWORD_MAX_LENGTH = 40;
    
    protected $_related = array('Entity'=>'getEntity');
    
    public function getEntity()
    {
        return $this->getTable('Entity')->find((int) $this->entity_id);
    }
    
    protected function beforeSave()
    {
        $this->Entity->save();
        $this->entity_id = $this->Entity->id;
        
        // This part checks the password to see if it has been changed, then 
        // encrypts it accordingly
        $db = $this->getDb();
                
        if ($this->exists()) {
            $sql = "SELECT password FROM $db->User WHERE id = ?";
            $oldPassword = $db->fetchOne($sql, array((int) $this->id));            
            if ($this->password !== $oldPassword) {
                $this->password = sha1($this->password);
            }
        } else {
            $this->password = sha1($this->password);
        }
    }
    
    protected function beforeSaveForm($post)
    {
        if (!$this->processEntity($post)) {
            return false;
        }
        
        // Permissions check to see if whoever is trying to change role to a super-user
        if (!empty($post['role'])) {
            if ($post['role'] == 'super' && !$this->userHasPermission('makeSuperUser')) {
                throw new Omeka_Validator_Exception( 'User may not change permissions to super-user' );
            }
            if (!$this->userHasPermission('changeRole')) {
                throw new Omeka_Validator_Exception('User may not change roles.');
            }
        } 
                
        // If the User is not persistent we need to create a placeholder password
        if (!$this->exists()) {
            $this->password = $this->generatePassword(8);
        }        
        
        return true;
    }
    
    /**
     * @duplication Mostly duplicated in Item::filterInput()
     *
     * @return void
     **/
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
        
        if ($post['active']) {
            $post['active'] = 1;
        }
        
        return $post;
    }
    
    public function setFromPost($post)
    {
        // potential security hole
        if (isset($post['password'])) {
             unset($post['password']);
        }
        return parent::setFromPost($post);
    }
    
    protected function _validate()
    {
        // Validate the entity of the user. This requires special validation 
        // within this class b/c the entities themselves have no particular 
        // validation.
        if ($entity = $this->Entity) {
            
            if (empty($entity->institution) and empty($entity->first_name) and empty($entity->last_name)) {
                $this->addError('institution', 'Name of the institution is required if not providing first/last name.' );
            }
            // Either need first and last name (or institution name) to validate.
            else if (empty($entity->institution)) {
                if (empty($entity->first_name) and empty($this->last_name)) {
                    $this->addError('name', 'Either first and last name or the name of the institution is required.');
                }
                else {
                    if (empty($entity->first_name)) {
                        $this->addError('first_name', 'First name is required for user accounts.' );
                    }
                    if (empty($entity->last_name)) {
                        $this->addError('last_name', 'Last name is required for user accounts.'); 
                    }
                }
            }
            
            if (!Zend_Validate::is($entity->email, 'EmailAddress')) {
                $this->addError('email', 'A valid email address is required for users.');
            }
            
            if (!$this->emailIsUnique($entity->email)) {
                $this->addError('email', 'That email address has already been claimed by a different user.  Please notify an administrator if you feel this has been done in error.');            
            }                        
        }    
        
        //Validate the role
        if (empty($this->role)) {
            $this->addError('role', 'User must be assigned a valid role.');
        }
        
        // Validate the username
        if (strlen($this->username) < self::USERNAME_MIN_LENGTH or strlen($this->username) > self::USERNAME_MAX_LENGTH) {
            $this->addError('username', "Username must be no more than 30 characters long");
        }
        
        if (!Zend_Validate::is($this->username, 'Alnum')) {
            $this->addError('username', "Username must be alphanumeric.");
        }
        
        if (!$this->usernameIsUnique($this->username)) {
            $this->addError('username', "'{$this->username}' is already in use.  Please choose another.");
        }
        
        // Validate the password
        $pass = $this->password;
        
        if (empty($pass)) {
            $this->addError('password', "Password must not be empty");
        } else if (strlen($pass) < self::PASSWORD_MIN_LENGTH or strlen($pass) > self::PASSWORD_MAX_LENGTH) {
            $this->addError('password', "Password must be between " . self::PASSWORD_MIN_LENGTH . " and " . self::PASSWORD_MAX_LENGTH . " characters"); 
        }
    }
    
    /**
     * This will check the set of IDs for users that have a specific email address.  
     * If it is greater than 1, or if the 
     *
     * @return bool
     **/
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
    
    private function usernameIsUnique($username)
    {
        $db = $this->getDb();
        
        $sql = "
        SELECT u.id 
        FROM $db->User u 
        WHERE u.username = ? 
        LIMIT 1";
        
        $id = $db->fetchOne($sql, array($username));
        
        if ($id) {
            // There is an ID and it can't belong to this record
            if (!$this->exists()) {
                return false;
            // There is an ID but it doesn't belong to this record
            } else if ($this->exists() && ($this->id != $id)) {
                return false;
            }
        }
        
        return true;
    }
    
    public function changePassword($new1, $new2, $old)
    {    
        //super users can change the password without knowing the old one
        $current = Omeka_Context::getInstance()->getCurrentUser();
        if ($current->role == 'super') {
            if ($new1 != $new2) {
                throw new Omeka_Validator_Exception('The new password must be correctly typed twice.');
            }
            
            $this->password = $new1;
        } else {
            if (empty($new1) || empty($new2) || empty($old)) {
                throw new Omeka_Validator_Exception('User must fill out all password fields in order to change password');
            }
            
            //If the old passwords don't match up
            if (sha1($old) !== $this->password) {
                throw new Omeka_Validator_Exception('Old password has been entered incorrectly.');
            }
            
            if($new1 !== $new2) {
                throw new Omeka_Validator_Exception('New password must be typed correctly twice.');
            }
            
            $this->password = $new1;
        }
    }
    
    protected function processEntity(&$post)
    {    
        $entity = $this->Entity;
        
        //If the entity is new, then determine whether it is an institution or a person
        if (empty($entity)) {
            $entity = new Entity;
        }
        
        //The new email address is fully legit, so set the entity to the new info                
        $entity->first_name  = $post['first_name'];
        $entity->last_name   = $post['last_name'];
        $entity->institution = $post['institution'];
        $entity->email       = $post['email'];
        
        $this->Entity = $entity;
        
        unset($post['email']);
        unset($post['first_name']);
        unset($post['last_name']);
        unset($post['institution']);
                        
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
        $this->password = $password;
        return $password;
    }        
}