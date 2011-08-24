<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2009-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */

/**
 * Retrive the User record corresponding to the authenticated user.
 *
 * If the user record is not retrievable (invalid ID), then the authentication 
 * ID will be cleared.
 * 
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2009-2010
 */
class Omeka_Core_Resource_Currentuser extends Zend_Application_Resource_ResourceAbstract
{
    /**
     * Retrieve the User record associated with the authenticated user.
     *
     * Note that this returns null when no User is authenticated.  Prior 
     * to 1.4, this returned boolean false.  For forward-compatibility, this 
     * has been changed to null in 1.4.  This is because in future versions, 
     * User will implement Zend_Role_Interface.  Zend_Acl accepts null as 
     * a valid role, but it throws exceptions for boolean false (tries to 
     * convert it to the empty string).
     *
     * @return User|null
     */
    public function init()
    {
        $bootstrap = $this->getBootstrap();
        $bootstrap->bootstrap('Auth');
        $auth = $bootstrap->getResource('Auth');
        
        // User should default to null because the ACL interprets null differently
        // from other equivalents (false, empty string, etc.).
        $user = null;

        if (!$auth->hasIdentity()) {
            return null;
        }

        $userIdentity = $auth->getIdentity();
        $bootstrap->bootstrap('Db');
        $db = $bootstrap->getResource('Db');
        try {
            // The auth mechanism stores the user integer ID as the identity.  
            // This is done to avoid any confusion with legacy installations that 
            // may have usernames consisting entirely of digits.
            $user = $db->getTable('User')->findActiveById($userIdentity);
        } catch (Zend_Db_Statement_Exception $e) {
            // Exceptions may be thrown because the database is out of sync
            // with the code.  Suppress errors and skip authentication, but
            // only until the database is properly upgraded.
            if (Omeka_Db_Migration_Manager::getDefault()->dbNeedsUpgrade()) {
                $user = null;
            } else {
                throw $e;
            }
        }
        if (!$user) {
            // If we can't retrieve the User from the database, it likely
            // means that this user has been deleted.  In this case, do not
            // allow the user to stay logged in.
            $auth->clearIdentity();
        }
        return $user;
    }
}
