<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */

/**
 * Create a default user for an Omeka installation.
 *
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class Installer_Task_User implements Installer_TaskInterface
{   
    private $_username;
    private $_password;
    private $_email;
    private $_firstName;
    private $_lastName;
    private $_active;
    private $_role;
        
    public function setUsername($username)
    {
        $this->_username = $username;
    }
    
    public function setPassword($password)
    {
        $this->_password = $password;
    }
    
    public function setEmail($email)
    {
        $this->_email = $email;
    }
    
    public function setFirstName($firstname)
    {
        $this->_firstName = $firstname;
    }
    
    public function setLastName($lastname)
    {
        $this->_lastName = $lastname;
    }
    
    public function setIsActive($active)
    {
        $this->_active = $active;
    }
    
    public function setRole($role)
    {
        $this->_role = $role;
    }
    
    public function install(Omeka_Db $db)
    {
        $required = array(
            '_username' => 'username',
            '_password' => 'password',
            '_email'    => 'email',
            '_firstName' => 'first name',
            '_lastName' => 'last name',
            '_active'   => 'active',
            '_role'     => 'role'
        );
        foreach ($required as $propName => $fieldName) {
            if (!$this->$propName) {
                throw new Installer_Task_Exception("Required field '$fieldName' not given.");
            }
        }
        
        $user = new User($db);
        $user->Entity = new Entity($db);
        $user->Entity->email = $this->_email;
        $user->Entity->first_name = $this->_firstName;
        $user->Entity->last_name = $this->_lastName;
        $user->username = $this->_username;
        $user->setPassword($this->_password);
        $user->active = $this->_active;
        $user->role = $this->_role;
        try {
            $user->forceSave();
        } catch (Omeka_Validator_Exception $e) {
            throw new Installer_Task_Exception("New user does not validate: "
             . $e->getMessage());
        }
    }
}
