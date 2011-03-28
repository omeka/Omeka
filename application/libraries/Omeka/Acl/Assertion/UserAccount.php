<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * Assert whether or not a specific user is allowed access to that person's 
 * user account data (editing profile, removing )
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2009
 **/
class Omeka_Acl_Assertion_UserAccount implements Zend_Acl_Assert_Interface
{    
    public function assert(Zend_Acl $acl,
                           Zend_Acl_Role_Interface $role = null,
                           Zend_Acl_Resource_Interface $resource = null,
                           $privilege = null)
    {   
        // We need to be checking access to a specific User record.  If we are 
        // not doing that, it should prevent access altogether.
        // 
        // The same is true if the role not a User record.
        // 
        // Note that this will break BC, as the following will return false: 
        // $acl->isAllowed($role, 'Users', 'edit'); 
        if (!$resource instanceof User) {
            return false;
        }
        
        if (!$role instanceof User) {
            return false;
        }
        
        // Alias for readability.
        $userAccount = $resource;
        $currentUser = $role;
        $isSameUser = ($currentUser->id == $userAccount->id);
        switch ($privilege) {
            case 'show':
            case 'edit':
            case 'change-password':
                // You are always allowed to view your own account info.
                // You are always allowed to edit your own user account.
                // Same with changing your own password.
                if ($isSameUser) {
                    return true;
                }
                break;
            case 'delete':
                // No deleting your own user account.
               if ($isSameUser) {
                   return false;
               }                
               break;
            case 'change-role':
                if ($isSameUser) {
                    return false;
                } 
                break;
            case 'change-status':
                if ($isSameUser) {
                    return false;
                }
            default:
                # code...
                break;
        }
        
        // Otherwise, we give all these privileges to super users.
        if ($currentUser->getRoleId() == 'super') {
            return true;
        }
        
        // Everyone is automatically denied access by default.    
        return false;
    }
}
