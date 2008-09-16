<?php
/**
 * @deprecated
 * @return string
 **/
function generate_url($options, $name)
{
    trigger_error('generate_url() is deprecated, please use uri() instead!');
	return uri($options, $name);
}

/**
 * Output the dublin core field description
 * 
 * @deprecated
 * @param string
 * @return void
 **/
function dublin_core($type) { 
	trigger_error('Do not use dublin_core() anymore!');
} 

/**
 * Return either the value or, if it's empty, output the default
 * 
 * @deprecated This shouldn't be here in the themes API (it's more like a global helper function)
 * @param mixed
 * @return mixed
 **/
function not_empty_or($value, $default) {
	return !empty($value) ? $value : $default;
}

/**
 * @deprecated 
 **/
function metatext_form($item, $input="textarea",$metafields=null) 
{
    trigger_error("Don't use this anymore!");
}

/**
 * @deprecated
 **/
function items_rss_header()
{
	trigger_error('Use auto_discovery_link_tag() instead of items_rss_header()!');
}

/**
 * @deprecated
 */
function get_month($date)
{
	$parts = explode('-',$date);
	if($parts[1] === '00') return null;
	return $parts[1];
}

/**
 * @deprecated
 */
function get_day($date)
{
	$parts = explode('-',$date);
	if($parts[2] === '00') return null;
	return $parts[2];
}

/**
 * @deprecated
 */
function get_year($date)
{
	$parts = explode('-',$date);
	if($parts[0] === '0000') return null;
	return $parts[0];
}

/**
 * @deprecated
 */
function item_metadata($item, $field, $escape=true)
{
    trigger_error('Do not use item_metadata() to retrieve metadata for items.  Please use item() instead!');
}

/**
 * @deprecated
 */
function type($id=null)
{
    trigger_error('Do not use type() to retrieve an item type!');
}

/**
 * @deprecated
 */
function types(array $params = array())
{
    trigger_error('Do not use use types() to retrieve a list of item types!');
}

/**
 * @deprecated
 */
function tags(array $params = array()) 
{
    trigger_error('Use get_tags() instead of tags()!');
	return get_db()->getTable('Tag')->findBy($params);
}

/**
 * @deprecated
 */
function items(array $params = array())
{
    trigger_error('Use get_items() instead of items()!');
	return get_db()->getTable('Item')->findBy($params);
}

/**
 * @deprecated
 */
function users(array $params = array())
{
    trigger_error('Use get_users() instead of users()!');
	return get_db()->getTable('User')->findAll();
}

/**
 * @deprecated
 * 
 * @param array
 * @return void
 **/
function institutions(array $params = array())
{
    trigger_error('institutions() is no longer supported in Omeka 0.10!');
	$params = array_merge($params, array('type'=>'Institution'));
	return get_db()->getTable('Entity')->findBy($params);
}

/**
 * @deprecated
 * 
 * @param array
 * @return void
 **/
function metafields(array $params = array())
{
    trigger_error('metafields() is no longer supported in Omeka 0.10!');
	return get_db()->getTable('Metafield')->findAll();
}

/**
 * @deprecated
 * 
 * @param string
 * @return void
 **/
function people(array $params = array())
{
    trigger_error('people() is no longer supported in Omeka 0.10!');
	$params = array_merge($params, array('type'=>'Person'));
	return get_db()->getTable('Entity')->findBy($params);
}


/**
 * @deprecated
 * @see has_type()
 * @return boolean
 **/
function has_collection($item, $name=null) {
    trigger_error('Use item_belongs_to_collection() instead of has_collection()!');
}

/**
 * @deprecated
 * @see item_has_files()
 * @param Item
 * @return boolean
 **/
function has_files($item) {
	trigger_error('Use item_has_files() instead of has_files()!');
}

/**
 * @deprecated
 */
function current_user_tags($item)
{
	$user = current_user();
	if(!$item->exists()) {
		return false;
	}
	return get_tags(array('user'=>$user->id, 'record'=>$item));
}

/**
 * @deprecated
 */
function item_types()
{
    trigger_error('Use get_item_types() instead of item_types()!');
    return get_db()->getTable('ItemType')->findAll();
}

/**
 * @deprecated
 */
function collections(array $params = array())
{
	return get_db()->getTable('Collection')->findBy($params);
}

/**
 * @deprecated
 */
function entities(array $params = array())
{
	return get_db()->getTable('Entity')->findBy($params);
}

/**
 * Determine whether or not the collection has any collectors.
 * 
 * @deprecated
 * @return boolean
 **/
function has_collectors($collection) {
    trigger_error('Please use collection_has_collectors() instead of has_collectors()!');
	return $collection->hasCollectors();
}

/**
 * 
 * @deprecated
 * @return boolean
 **/
function has_tags($item, array $tags=array()) {
	$hasSome = (count($item->Tags) > 0);
	if(empty($tags) or !$hasSome){
		return $hasSome;
	}
	foreach ($tags as $key => $tag) {
		if(!$item->hasTag($tag)) {
			return false;
		}
	}
	return true;
}

/**
 * @deprecated
 * @see has_type()
 * 
 * @param Item
 * @return boolean
 **/
function has_thumbnail($item) {
	return $item->hasThumbnail();
}

/**
 * Display an alternative value if the given variable is empty
 * @deprecated
 * @return string
 **/
function display_empty($val, $alternative="[Empty]") {
	return nls2p(h(!empty($val) ? $val : $alternative));
}

/**
 * @deprecated
 */
function items_search_form($props=array()) {
    return __v()->action('advanced-search', 'items', null, array('is_partial'=>true, 'form_attributes'=>$props));
    // trigger_error('The advanced search form should be on a page called "items/advanced-search.php" in your theme.  Do not use this helper anymore.');
}