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
        
        $user = false;

        if ($auth->hasIdentity()) {
            $user = $auth->getIdentity();
            // This ext
            // ra database call seems unnecessary at face value, but it
            // actually retrieves the entity metadata about the user as well as the
            // username/role info that is already stored in the auth identity.
            require_once 'User.php';
            $bootstrap->bootstrap('Db');
            $db = $bootstrap->getResource('Db');
            $user = $db->getTable('User')->find($user->id);
        } 

        return $user;
    }
}