<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Create a default user for an Omeka installation.
 * 
 * @package Omeka\Install\Task
 */
class Installer_Task_User implements Installer_TaskInterface
{   
    private $_username;
    private $_password;
    private $_email;
    private $_name;
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
    
    public function setName($name)
    {
        $this->_name = $name;
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
            '_name'     => 'name',
            '_active'   => 'active',
            '_role'     => 'role'
        );
        foreach ($required as $propName => $fieldName) {
            if (!$this->$propName) {
                throw new Installer_Task_Exception("Required field '$fieldName' not given.");
            }
        }
        
        $user = new User($db);
        $user->email = $this->_email;
        $user->name = $this->_name;
        $user->username = $this->_username;
        $user->setPassword($this->_password);
        $user->active = $this->_active;
        $user->role = $this->_role;
        if (!$user->save(false)) {
            throw new Installer_Task_Exception("New user does not validate: "
             . (string) $user->getErrors());
        }
    }
}
