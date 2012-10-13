<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Assertion to take account of "All" and "Self" sub-permissions for records.
 *
 * A common use is the "edit" and "delete" permissions for Items and other 
 * "ownable" records.
 * 
 * @package Omeka\Acl
 */
class Omeka_Acl_Assert_Ownership implements Zend_Acl_Assert_Interface
{
    /**
     * Assert whether or not the ACL should allow access.
     */
    public function assert(Zend_Acl $acl,
                           Zend_Acl_Role_Interface $role = null,
                           Zend_Acl_Resource_Interface $resource = null,
                           $privilege = null)
    {
        $allPriv = $privilege . 'All';
        $selfPriv = $privilege . 'Self';
        if (!($role instanceof User)) {
            $allowed = false;
        } else if ($resource instanceof Omeka_Record_AbstractRecord) {
            $allowed = $acl->isAllowed($role, $resource, $allPriv)
                   || ($acl->isAllowed($role, $resource, $selfPriv)
                       && $this->_userOwnsRecord($role, $resource));
        } else {
            // The "generic" privilege is allowed if the user can
            // edit any of the given record type whatsoever.
            $allowed = $acl->isAllowed($role, $resource, $allPriv)
                    || $acl->isAllowed($role, $resource, $selfPriv);
        }
        return $allowed;
    }

    /**
     * Check whether the user owns this specific record.
     */
    private function _userOwnsRecord($user, $record)
    {
        return $record->isOwnedBy($user);
    }
}
