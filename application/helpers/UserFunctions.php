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
 * @since 0.10
 * @see get_items()
 * @see get_tags()
 * @param array $params
 * @param integer $limit
 * @return array
 */
function get_users($params = array(), $limit = 10)
{
    return get_db()->getTable('User')->findBy($params, $limit);
}

/**
 * @since 0.10
 * @see get_item_by_id()
 * @param integer
 * @return User|null
 */
function get_user_by_id($userId)
{
    return get_db()->getTable('User')->find($userId);
}

/**
 * Check the ACL to determine whether the current user has proper permissions.
 *
 * <code>has_permission('Items', 'showNotPublic')</code>
 * Will check if the user has permission to view Items that are not public.
 *
 * @param string|Zend_Acl_Resource_Interface
 * @param string|null
 * @return boolean
 */
function has_permission($resource, $privilege)
{
    $acl = Zend_Controller_Front::getInstance()->getParam('bootstrap')->acl;
    $user = current_user();

    if (is_string($resource)) {
       $resource = ucwords($resource);
    }

    // User implements Zend_Acl_Role_Interface, so it can be checked directly by the ACL.
    return $acl->isAllowed($user, $resource, $privilege);
}

/**
 * Returns the total number of users
 *
 * @return integer
 */
function total_users()
{
    return get_db()->getTable('User')->count();
}
