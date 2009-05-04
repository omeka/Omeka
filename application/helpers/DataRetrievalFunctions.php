<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka_ThemeHelpers
 * @subpackage DataRetrievalHelpers
 **/
 
/**
 * Returns the total number of items
 *
 * @return integer
 **/
function total_items() 
{	
	return get_db()->getTable('Item')->count();
}

/**
 * Returns the total number of collection
 * 
 * @return integer
 **/
function total_collections() 
{
	return get_db()->getTable('Collection')->count();
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
 * Returns the total number of users
 * 
 * @return integer
 **/
function total_users() 
{
	return get_db()->getTable('User')->count();
}

/**
 * Returns the total number of types
 *
 * @return integer
 **/
function total_types() 
{
	return get_db()->getTable('Type')->count();
}

/**
 * Returns the total number of results
 *
 * @return integer
 **/
function total_results() 
{
	if(Zend_Registry::isRegistered('total_results')) {
		$count = Zend_Registry::get('total_results');

		return $count;
	}
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
 * Returns the most recent collections
 * 
 * @param integer $num The maximum number of recent collections to return
 * @return array
 **/
function recent_collections($num = 10) 
{
	return get_collections(array('recent'=>true), $num);
}

/**
 * Returns the most recent items
 * 
 * @param integer $num The maximum number of recent items to return
 * @return array
 **/
function recent_items($num = 10) 
{
	return get_db()->getTable('Item')->findBy(array('recent'=>true), $num);
}

/**
 * Returns a randome featured item
 * 
 * @since 7/3/08 This will retrieve featured items with or without images by
 *  default. The prior behavior was to retrieve only items with images by
 *  default.
 * @param string $hasImage
 * @return Item
 */
function random_featured_item($hasImage=false) 
{
	return get_db()->getTable('Item')->findRandomFeatured($hasImage);
}

/**
 * Returns a random featured collection.
 * 
 * @return Collection
 **/
function random_featured_collection()
{
    return get_db()->getTable('Collection')->findRandomFeatured();
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