<?php
/**
 * All User helper functions
 * 
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka_ThemeHelpers
 * @subpackage UserHelpers
 **/
 
/**
 * @since 0.10
 * @see get_items()
 * @see get_tags()
 * @param array $params
 * @param integer $limit
 * @return array
 **/
function get_users($params = array(), $limit = 10)
{
    return get_db()->getTable('User')->findBy($params, $limit);
}

/**
 * @since 0.10
 * @see get_item_by_id()
 * @param integer
 * @return User|null
 **/
function get_user_by_id($userId)
{
    return get_db()->getTable('User')->find($userId);
}

/**
 * Check the ACL to determine whether the current user has proper permissions.
 * 
 * This can be called with different syntax:
 * <code>has_permission('Items', 'showNotPublic')</code>
 * Will check if the user has permission to view Items that are not public.
 *
 * The alternate syntax checks to see whether the current user has a specific role:
 * <code>has_permission('admin')</code>
 * This latter syntax is discouraged, only because this will not cascade properly 
 * to other roles that may be given the same permissions as the admin role.  It 
 * actually circumvents the ACL entirely, so it should be avoided except in certain
 * situations where data must be displayed specifically to a certain role and no one else.
 *
 * @param string 
 * @param string|null
 * @return boolean
 **/
function has_permission($role, $privilege=null) 
{
	$acl = Omeka_Context::getInstance()->getAcl();
	$user = current_user();
	if (!$user) return false;
	
	$userRole = $user->role;
	if (!$privilege) {
		return ($userRole == $role);
	}

	//This is checking for the correct combo of 'role','resource' and 'privilege'
	$resource = $role;
	return $acl->isAllowed($userRole,ucwords($resource),$privilege);
}

/**
 * Returns the total number of users
 * 
 * @return integer
 **/
function total_users() 
{
	return get_db()->getTable('User')->count();
}

/**
 * Returns an array of role names
 * 
 * @return array
 **/
function get_user_roles()
{
	$roles = Omeka_Context::getInstance()->getAcl()->getRoleNames();
	foreach($roles as $key => $val) {
		$roles[$val] = Inflector::humanize($val);
		unset($roles[$key]);
	}
	return $roles;
}