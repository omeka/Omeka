<?php
/**
 *
 * @deprecated 
 * @param string $urlEnd The controller/action/parameter that specifies the link.
 * @example uri('items/browse/'.$item->id); 
 * @todo Work without mod_rewrite enabled: uri('items/show/3') -> ?u=items/show/3
 * @return string Url for the link href attribute.
 **/
function uri($urlEnd, $params=array())
{    
    return url_for($urlEnd, null, $params);
}

/**
 * @deprecated
 * @return string
 **/
function generate_url($options, $name)
{
    trigger_error('generate_url() is deprecated, please use url_for() instead!');
	return url_for($options, $name);
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
 * @return string
 **/
function admin_uri()
{
    trigger_error('Please use admin_url_for() to build URLs to the admin theme.');
	return WEB_ROOT . '/admin/';
}

/**
 * Determine whether or not the collection has any collectors.
 * 
 * @return boolean
 **/
function has_collectors($collection) {
    trigger_error('Please use collection_has_collectors() instead of has_collectors()!');
	return $collection->hasCollectors();
}

/**
 * 
 *
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

function items_search_form($props=array()) {
    return __v()->action('advanced-search', 'items', null, array('is_partial'=>true, 'form_attributes'=>$props));
    // trigger_error('The advanced search form should be on a page called "items/advanced-search.php" in your theme.  Do not use this helper anymore.');
}