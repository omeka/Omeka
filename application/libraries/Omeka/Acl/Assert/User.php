<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Assert whether or not a specific user is allowed access to that person's user 
 * account data.
 * 
 * @package Omeka\Acl
 */
class Omeka_Acl_Assert_User implements Zend_Acl_Assert_Interface
{    
    private $_allowSelf = array(
        'show',
        'edit',
        'change-password',
        'api-keys'
    );

    private $_denySelf = array(
        'delete',
        'change-role',
        'change-status',
    );

    /**
     * Assert whether or not the ACL should allow access.
     *
     * Assertions follow this logic:
     *
     * Non-authenticated users (null role) have no access.
     * 
     * There exists a set of privileges (A) that are always allowed, provided that the 
     * user role and user resource are the same (editing own info, changing own 
     * password, etc.).
     *
     * There also exists a set of privileges (B) that are always denied when 
     * performed on one's own user account (deleting own account, changing own 
     * role, etc.)
     *
     * The super user can do anything that isn't on (B), e.g. the 
     * super user account cannot modify its own role.
     *
     * All other users are limited to (A).
     */
    public function assert(Zend_Acl $acl,
                           Zend_Acl_Role_Interface $role = null,
                           Zend_Acl_Resource_Interface $resource = null,
                           $privilege = null)
    {   
        // Non-authenticated users can never be allowed to do anything.
        if (!($role instanceof User)) {
            return false;
        }

        $allowed = false;
        // $resource will either be an instance of Zend_Acl_Resource or
        // User. If the latter, verify whether or not the resource and
        // role represent the same user and branch accordingly.
        if ($resource instanceof User) {
            if ($this->_isSuperUser($role)) {
                $allowed = !($this->_isSelf($role, $resource) 
                        && $this->_isDeniedSelf($privilege));
            } else if ($this->_isSelf($role, $resource)) {
                $allowed = $this->_isAllowedSelf($privilege);
            }
        } else {
            $allowed = $this->_isSuperUser($role);
        }
        return $allowed;
    }

    private function _isAllowedSelf($privilege)
    {
        return in_array($privilege, $this->_allowSelf);
    }
    private function _isDeniedSelf($privilege)
    {
        return in_array($privilege, $this->_denySelf);
    }

    private function _isSelf($role, $resource)
    {
        $userAccount = $resource;
        $currentUser = $role;
        return ($currentUser->id == $userAccount->id);
    }

    private function _isSuperUser($user)
    {
        $roleId = $user->getRoleId();
        return ('super' == $roleId);
    }
}
