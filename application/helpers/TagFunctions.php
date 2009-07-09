<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka_ThemeHelpers
 * @subpackage DataRetrievalHelpers
 **/

/**
 * @since 0.10
 * @see TagTable::applySearchFilters() for params
 * @param array $params
 * @param integer $limit
 * @return array
 */
function get_tags($params = array(), $limit = 10)
{
    return get_db()->getTable('Tag')->findBy($params, $limit);
}
 
/**
 * Returns the total number of tags
 * 
 * @return integer
 **/
function total_tags() 
{
	return get_db()->getTable('Tag')->count();
}

/**
 * Returns the most recent tags.
 * 
 * @param integer $num The maximum number of recent tags to return
 * @return array
 **/
function recent_tags($num = 30) 
{
	return get_tags(array('recent'=>true), $num);
}

/**
 * Return the tags belonging to a particular user.
 * 
 * @param Item $item
 * @return array An array of tag objects.
 */
function current_user_tags(Item $item)
{
    $user = current_user();
    if (!$item->exists()) {
        return false;
    }
    return get_tags(array('user'=>$user->id, 'record'=>$item, 'sort'=>array('alpha')));
}