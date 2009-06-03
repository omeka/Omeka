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

/**
 * Retrieve a full set of ItemType objects currently available to Omeka.
 * 
 * Keep in mind that the $params and $limit arguments are in place for the sake
 * of consistency with other data retrieval functions, though in this case
 * they don't have any effect on the number of results returned.
 * 
 * @since 0.10
 * @param array $params
 * @param integer $limit
 * @return array
 **/
function get_item_types($params = array(), $limit = 10)
{
    return get_db()->getTable('ItemType')->findAll();
}

/**
 * Retrieve the latest available version of Omeka by accessing the appropriate
 * URI on omeka.org.
 * 
 * @since 1.0
 * @return string|false The latest available version of Omeka, or false if the
 * request failed for some reason.
 **/
function get_latest_omeka_version()
{
    $omekaApiUri = 'http://api.omeka.org/latest-version';
    $omekaApiVersion = '0.1';
    
    // Determine if we have already checked for the version lately.
    $check = unserialize(get_option('omeka_update')) or $check = array();
    // This a timestamp corresponding to the last time we checked for
    // a new version.  86400 is the number of seconds in a day, so check
    // once a day for a new version.
    if (array_key_exists('last_updated', $check) 
        and ($check['last_updated'] + 86400) > time()) {
        // Return the value we got the last time we checked.
        return $check['latest_version']; 
    }
    
    try {   
        $client = new Zend_Http_Client($omekaApiUri);
        $client->setParameterGet('version', $omekaApiVersion);
        $client->setMethod('GET');
        $result = $client->request();
	    if ($result->getStatus() == '200') {
	        $latestVersion = $result->getBody();
	        // Store the newer values 
	        $check['latest_version'] = $latestVersion;
	        $check['last_updated'] = time();
	        set_option('omeka_update', serialize($check));
	       return $result->getBody();
	    } else {
	       debug("Attempt to GET $omekaApiUri with version=$omekaApiVersion "
	             . "returned with status=" . $result->getStatus() . " and "
	             . "response body=" . $result->getBody());
	    }
    } catch (Exception $e) {
        debug('Error in retrieving latest Omeka version: ' . $e->getMessage());
    }
    return false;
}

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
 * Retrieve the set of values for item type elements.
 * @param Item|null Check for this specific item record (current item if null).
 * @return array
 **/
function item_type_elements($item=null)
{
    if (!$item) {
        $item = get_current_item();
    }
    $elements = $item->getItemTypeElements();
    foreach ($elements as $element) {
        $elementText[$element->name] = item(ELEMENT_SET_ITEM_TYPE, $element->name);
    }
    return $elementText;
}