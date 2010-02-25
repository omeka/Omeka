<?php

/**
 * Initialize the User object for the currently logged-in user.  If no user
 * has been authenticated, this value will be equivalent to false.
 * @package Omeka
 * @copyright Center for History and New Media, 2009
 **/
class Omeka_Core_Resource_Currentuser extends Zend_Application_Resource_ResourceAbstract
{
    public function init()
    {
        $bootstrap = $this->getBootstrap();
        $bootstrap->bootstrap('Auth');
        $auth = $bootstrap->getResource('Auth');
        
        // User should default to null because the ACL interprets null differently
        // from other equivalents (false, empty string, etc.).
        $user = null;

        if ($auth->hasIdentity()) {
            $userIdentity = $auth->getIdentity();
            require_once 'User.php';
            $bootstrap->bootstrap('Db');
            $db = $bootstrap->getResource('Db');
            
            // The auth mechanism stores the user integer ID as the identity.  
            // This is done to avoid any confusion with legacy installations that 
            // may have usernames consisting entirely of digits.
            $user = $db->getTable('User')->find($userIdentity);
            
            if (!$user) {
                // If we can't retrieve the User from the database, it likely
                // means that this user has been deleted.  In this case, do not
                // allow the user to stay logged in.
                $auth->clearIdentity();
            }
        } 

        return $user;
    }
}
