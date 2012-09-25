<?php
/**
 * All User helper functions
 *
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka_ThemeHelpers
 * @subpackage UserHelpers
 */

/**
 * Check the ACL to determine whether the current user has proper permissions.
 *
 * <code>is_allowed('Items', 'showNotPublic')</code>
 * Will check if the user has permission to view Items that are not public.
 *
 * @param string|Zend_Acl_Resource_Interface
 * @param string|null
 * @return boolean
 */
function is_allowed($resource, $privilege)
{
    $acl = Zend_Controller_Front::getInstance()->getParam('bootstrap')->acl;
    $user = current_user();

    if (is_string($resource)) {
       $resource = ucwords($resource);
    }

    // User implements Zend_Acl_Role_Interface, so it can be checked directly by the ACL.
    return $acl->isAllowed($user, $resource, $privilege);
}
