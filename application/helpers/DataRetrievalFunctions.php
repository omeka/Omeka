<?php

function current_user_tags($item)
{
	$user = current_user();
	if(!$item->exists()) {
		return false;
	}
	return tags(array('user'=>$user->id, 'record'=>$item));
}

/**
 * Retrieve the total number of items
 *
 * @since 11/7/07 This function can now be passed a $collection obj to return the total # of items in that collection
 * @return integer
 **/
function total_items($return = false) {
	if($return instanceof Collection)
	{
		return $return->totalItems();
	}
	
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
	return tags(array('recent'=>true,'limit'=>$num));
}

function recent_collections($num = 10) {
	return collections(array('recent'=>true,'per_page'=>$num));
}

function recent_items($num = 10) {
	return get_db()->getTable('Item')->findBy(array('recent'=>true,'per_page'=>(int) $num));
}

function random_featured_item($hasImage=true) {
	return get_db()->getTable('Item')->findRandomFeatured($hasImage);
}

function random_featured_collection()
{
    return get_db()->getTable('Collection')->findRandomFeatured();
}

function random_featured_exhibit()
{
    trigger_error('random_featured_exhibit() will not work until the new Exhibit builder is finished!'); 
    //return get_db()->getTable('Exhibit')->findRandomFeatured();
}

function entities(array $params = array())
{
	return get_db()->getTable('Entity')->findBy($params);
}

function people(array $params = array())
{
	$params = array_merge($params, array('type'=>'Person'));
	return get_db()->getTable('Entity')->findBy($params);
}

function institutions(array $params = array())
{
	$params = array_merge($params, array('type'=>'Institution'));
	return get_db()->getTable('Entity')->findBy($params);
}

function tags(array $params = array()) 
{
	return get_db()->getTable('Tag')->findBy($params);
}

function items(array $params = array())
{
	return get_db()->getTable('Item')->findBy($params);
}

function collection($id=null)
{
	if(!$id && Zend_Registry::isRegistered('collection')) {
		$c = Zend_Registry::get('collection');

		return $c;
	}
	
	$c = get_db()->getTable('Collection')->find($id);
	return $c;
}

function collections(array $params = array())
{
	return get_db()->getTable('Collection')->findBy($params);
}

function metafields(array $params = array())
{
	return get_db()->getTable('Metafield')->findAll();
}

function users(array $params = array())
{
	return get_db()->getTable('User')->findAll();
}

function item_types()
{
    return get_db()->getTable('ItemType')->findAll();
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