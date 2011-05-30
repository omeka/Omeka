<?php
/**
 * @copyright Center for History and New Media, 2011
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Assertion for the "edit" and "delete" permissions for Items.
 *
 * For backwards-compatibility reasons, the "edit" permission more or
 * less simply builds on the "editAll" and "editSelf" permissions, and
 * on "deleteAll" and "deleteSelf".
 *
 * @package Omeka
 */
class Item_OwnershipAclAssertion implements Zend_Acl_Assert_Interface
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
        } else if ($resource instanceof Item) {
            $allowed = $acl->isAllowed($role, $resource, $allPriv)
                   || ($acl->isAllowed($role, $resource, $selfPriv)
                       && $this->_userOwnsItem($role, $resource));
        } else {
            // The "generic" privilege is allowed if the user can
            // edit any items whatsoever.
            $allowed = $acl->isAllowed($role, $resource, $allPriv)
                    || $acl->isAllowed($role, $resource, $selfPriv);
        }
        return $allowed;
    }

    private function _userOwnsItem($user, $item)
    {
        return $item->wasAddedBy($user);
    }
}
