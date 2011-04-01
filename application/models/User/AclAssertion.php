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
class User_AclAssertion implements Zend_Acl_Assert_Interface
{    
    private $_allowSelf = array(
        'show',
        'edit',
        'change-password',
    );

    private $_denySelf = array(
        'delete',
        'change-role',
        'change-status',
    );

    private $_onlySuper = array(
        'browse',
        'add',
        'edit',
        'delete',
        'makeSuperUser',
    );

    public function assert(Zend_Acl $acl,
                           Zend_Acl_Role_Interface $role = null,
                           Zend_Acl_Resource_Interface $resource = null,
                           $privilege = null)
    {   
        // Non-authenticated users can never be allowed to do anything.
        if (!($role instanceof User)) {
            return false;
        }
        
        $roleId = $role->getRoleId();

        if (!($resource instanceof User)) {
            if ('super' == $roleId 
             && in_array($privilege, $this->_onlySuper)
            ) {
                return true;
            }
            return false;
        }
        
        // Alias for readability.
        $userAccount = $resource;
        $currentUser = $role;
        $isSameUser = ($currentUser->id == $userAccount->id);
        if ($isSameUser) {
            if (in_array($privilege, $this->_allowSelf)) {
                return true;
            } 
            if (in_array($privilege, $this->_denySelf)) {
                return false;
            }
        }
        
        // Otherwise, we give all these privileges to super users.
        if ('super' == $roleId) {
            return true;
        }
        
        // Everyone is automatically denied access by default.    
        return false;
    }
}
