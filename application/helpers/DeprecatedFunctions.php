<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package OmekaThemes
 * @subpackage DeprecatedHelpers
 **/

/**
 * @deprecated
 * @see uri()
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
 * @see auto_discovery_link_tag()
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
 * @see item()
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
 * @see get_item_types()
 */
function types(array $params = array())
{
    trigger_error('Do not use use types() to retrieve a list of item types!');
}

/**
 * @deprecated
 * @see get_tags()
 */
function tags(array $params = array()) 
{
    trigger_error('Use get_tags() instead of tags()!');
	return get_db()->getTable('Tag')->findBy($params);
}

/**
 * @deprecated
 * @see get_items()
 */
function items(array $params = array())
{
    trigger_error('Use get_items() instead of items()!');
	return get_db()->getTable('Item')->findBy($params);
}

/**
 * @deprecated
 * @see get_users()
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
}

/**
 * @deprecated
 * @param string
 * @return void
 **/
function people(array $params = array())
{
    trigger_error('people() is no longer supported in Omeka 0.10!');
}


/**
 * @deprecated
 * @see item_belongs_to_collection()
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
 * @see get_item_types()
 */
function item_types()
{
    trigger_error('Use get_item_types() instead of item_types()!');
    return get_db()->getTable('ItemType')->findAll();
}

/**
 * @deprecated
 * @see get_collections()
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
 * @see collection_has_collectors()
 * @deprecated
 * @return boolean
 **/
function has_collectors($collection) {
    trigger_error('Please use collection_has_collectors() instead of has_collectors()!');
	return $collection->hasCollectors();
}

/**
 * @see item_has_tags()
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
 * @see item_has_thumbnail()
 * @param Item
 * @return boolean
 **/
function has_thumbnail($item) {
	return $item->hasThumbnail();
}

/**
 * Determine whether or not the item has a given type.  If no name is provided,
 * this will return true if the item has any type at all.
 *
 * @deprecated
 * @param Item $item 
 * @param string|null $name Name of the type
 * @return boolean
 **/
function has_type($item, $name=null) {
	$exists = $item->Type and $item->Type->exists();
	$hasName = (!empty($name) ? $item->Type->name == $name : true);
	return ( $exists and $hasName );
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

/**
 * @deprecated
 * @param boolean
 * @return string
 **/
function get_base_url($use_relative_uri=false)
{
	$base = ($use_relative_uri) ? Zend_Controller_Front::getInstance()->getRequest()->getBaseUrl() : WEB_DIR;
	return rtrim($base , '/') . '/';
}

/**
 * @deprecated
 */
function h($str, $allowedTags = "i|em|b|strong|del|span") {
	
	$html = htmlentities($str,ENT_QUOTES,"UTF-8"); 
		
	if($allowedTags)
		return preg_replace_callback('!&lt;/?('.$allowedTags.')( .*?)?&gt;!i', 'unescapeTags', $html);
	else
		return $html;
}

/**
 * @access private
 * @deprecated
 * @param string
 * @return string
 **/
function unescapeTags($matches) {
  	return str_replace( array("&gt;", "&lt;", "&quot;", "&amp;"), array(">", "<", "\"", "&"), $matches[0]);
}

/**
 * @deprecated
 * @see is_current()
 * @param string
 * @param Zend_Controller_Front_Request
 * @return boolean
 **/
function is_current($link, $req = null) {
		
	if(!$req) {
		$req = Zend_Controller_Front::getInstance()->getRequest();
	}
	$current = $req->getRequestUri();
	$base = $req->getBaseUrl();

	//Strip out the protocol, host, base URI, rightmost slash before comparing the link to the current one
	$strip_out = array(WEB_DIR, $_SERVER['HTTP_HOST'], $base);
	$current = rtrim( str_replace($strip_out, '', $current), '/');
	$link = rtrim( str_replace($strip_out, '', $link), '/');
	
	if(strlen($link) == 0) return (strlen($current) == 0);
	return ($link == $current) or (strpos($current, $link) === 0);
}

