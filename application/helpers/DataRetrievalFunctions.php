<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @subpackage DataRetrievalHelpers
 **/
 
/**
 * Retrieve the total number of items
 *
 * @return integer
 **/
function total_items() {	
	return get_db()->getTable('Item')->count();
}

/**
 * 
 *
 * @return integer
 **/
function total_collections() {
	return get_db()->getTable('Collection')->count();
}

/**
 * 
 *
 * @return integer
 **/
function total_tags() {
	return get_db()->getTable('Tag')->count();
}

/**
 * 
 *
 * @return integer
 **/
function total_users() {
	return get_db()->getTable('User')->count();
}

/**
 * 
 *
 * @return integer
 **/
function total_types() {
	return get_db()->getTable('Type')->count();
}

/**
 * 
 *
 * @return integer
 **/
function total_results() {
	if(Zend_Registry::isRegistered('total_results')) {
		$count = Zend_Registry::get('total_results');

		return $count;
	}
}

/**
 * Retrieve the most recent tags.
 *
 * @return array
 **/
function recent_tags($num = 30) {
	return get_tags(array('recent'=>true,'limit'=>$num));
}

function recent_collections($num = 10) {
	return get_collections(array('recent'=>true,'per_page'=>$num));
}

function recent_items($num = 10) {
	return get_db()->getTable('Item')->findBy(array('recent'=>true,'per_page'=>(int) $num));
}

function random_featured_collection()
{
    return get_db()->getTable('Collection')->findRandomFeatured();
}

function get_user_roles(array $params = array())
{
	$roles = Omeka_Context::getInstance()->getAcl()->getRoleNames();
	foreach($roles as $key => $val) {
		$roles[$val] = Inflector::humanize($val);
		unset($roles[$key]);
	}
	return $roles;
}