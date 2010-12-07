<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2009-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */

/**
 * Initialize the User object for the currently logged-in user.  If no user
 * has been authenticated, this value will be equivalent to false.
 * 
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @package Omeka
 * @copyright Center for History and New Media, 2009-2010
 */
class Omeka_Core_Resource_Currentuser extends Zend_Application_Resource_ResourceAbstract
{
    /**
     * @return User|boolean False if there is no authenticated user.
     */
    public function init()
    {
        $bootstrap = $this->getBootstrap();
        $bootstrap->bootstrap('Auth');
        $auth = $bootstrap->getResource('Auth');
        
        $user = false;

        if ($auth->hasIdentity()) {
            $userId = $auth->getIdentity();
            // This extra database call seems unnecessary at face value, but it
            // actually retrieves the entity metadata about the user as well as the
            // username/role info that is already stored in the auth identity.
            require_once 'User.php';
            $bootstrap->bootstrap('Db');
            $db = $bootstrap->getResource('Db');
            try {
                $user = $db->getTable('User')->find($userId);
            } catch (Zend_Db_Statement_Exception $e) {
                // Exceptions may be thrown because the database is out of sync
                // with the code.  Suppress errors and skip authentication, but
                // only until the database is properly upgraded.
                if (Omeka_Db_Migration_Manager::getDefault()->dbNeedsUpgrade()) {
                    $user = false;
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
        } 

        return $user;
    }
}
