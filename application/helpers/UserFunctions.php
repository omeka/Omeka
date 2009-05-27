<?php
/**
 * All theme User helper functions
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