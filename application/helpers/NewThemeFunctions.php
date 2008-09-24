<?php 
/**
 * All theme API functions that are new to 0.10 .
 * 
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package OmekaThemes
 * @subpackage NewThemeHelpers
 **/

/**
 * Retrieve the values for a given field in the current item.
 * 
 * @param string
 * @return array
 **/
function item($field, $options=array())
{
    return __v()->item(get_current_item(), $field, $options);
}

/**
 * Retrieve the set of values for item type elements.
 * 
 * @return array
 **/
function item_type_elements()
{
    $item = get_current_item();
    $elements = $item->getItemTypeElements();
    foreach ($elements as $element) {
        $elementText[$element->name] = item($element->name);
    }
    return $elementText;
}

/**
 * Retrieve the proper HTML for a form input for a given Element record.
 * 
 * Assume that the given element has access to all of its values (for example,
 * all values of a Title element for a given Item).
 *
 * This will output as many form inputs as there are values for a given
 * element.  In addition to that, it will give each set of inputs a label and
 * a span with class="tooltip" containing the description for the element.
 * This span can either be displayed, hidden with CSS or converted into a 
 * tooltip with javascript.
 *
 * All sets of form inputs for elements will be wrapped in a div with
 * class="field".
 *
 * @todo Plugins should be able to hook in to displaying elements in a certain
 * way.
 * @param Element|array
 * @return string HTML
 **/
function display_form_input_for_element($element, $item)
{
    $html = '';
        
    // If we have an array of Elements, loop through the form to display them.
    if (is_array($element)) {
        foreach ($element as $key => $e) {
            $html .= __v()->elementForm($e, $item);
        }
    } else {
        $html = __v()->elementForm($element, $item);
    }
	
	return $html;
}

function display_element_set_form_for_item($item, $elementSetName)
{
    $dublinCoreElements = get_db()->getTable('Element')->findForItemBySet($item, $elementSetName);
    
    $html = '';
    
    foreach ($dublinCoreElements as $key => $element) {
       $html .= display_form_input_for_element($element, $item);
    }
    
    return $html;
}

/**
 * Retrieve a valid citation for the current item.  
 * 
 * @internal Was previously located at Item::getCitation().  This made not a 
 * whole lot of sense though, given that it is very much an element of the View
 * and not directly related to the business logic of the app.
 * @todo Make sure this citation follows some sort of standard.  MLA? Other?
 * @return string
 **/
function item_citation()
{
    if($citation = item('Citation')) {
		return $citation;
	}

	$cite = '';
    $cite .= item('Creator');
    if ($cite != '') $cite .= ', ';
    $cite .= '"' . item('Title', array('all'=>true, 'delimiter'=>', ')) . '". ';
    $cite .= '<em>'.settings('site_title').'</em>, ';
    $cite .= 'Item #'.item('id').' ';
    $cite .= '(accessed '.date('F d Y, g:i a').') ';
    return $cite;
}

/**
 * @access private
 * @param Omeka_Record
 * @return string
 **/
function record_uri(Omeka_Record $record, $action, $controller = null)
{
    $options = array();
    // Inflect the name of the controller from the record class if no
    // controller name is given.
    if (!$controller) {
        $recordClass = get_class($record);
        $inflector = new Zend_Filter_Word_CamelCaseToDash();
        // Convert the record class name from CamelCased to dashed-lowercase.
        $controller = strtolower($inflector->filter($recordClass));
        // Pluralize the record class name.
        $controller = Inflector::pluralize($controller);
    }
    $options['controller'] = $controller;
    $options['id'] = $record->id;
    $options['action'] = $action;
    
    // Use the 'id' route for all urls pointing to records
    return uri($options, 'id');
}

/**
 * Retrieve a URL for the current item
 * 
 * @param string Action to link to for this item.
 * @return string URL
 **/
function item_uri($action = 'show')
{
    return record_uri(get_current_item(), $action);
}

/**
 * This behaves as uri() except it always provides a url to the public theme.
 * 
 * @see uri()
 * @see admin_uri()
 * @param mixed
 * @return string
 **/
function public_uri()
{
    set_theme_base_uri('public');
    $args = func_get_args();
    $url = call_user_func_array('uri', $args);
    set_theme_base_uri();
    return $url;
}

/**
 * @see public_uri()
 * @param mixed
 * @return mixed
 **/
function admin_uri()
{
    set_theme_base_uri('admin');
    $args = func_get_args();
    $url = call_user_func_array('uri', $args);
    set_theme_base_uri();
    return $url;
}

/**
 * Helper function to be used in public themes to allow plugins to modify the navigation of those themes.
 *
 * Plugins can modify navigation by adding filters to specific subsets of the
 *  navigation. For instance, most themes will have what might be called a 'main'
 *  navigation set on the header of the site. This main navigation header would
 *  be attached to a filter called 'public_navigation_main', which would always
 *  act on that particular navigation. You would signal to the plugins to
 *  differentiate between the different navigation elements by passing the 2nd
 *  argument as 'main', so that it knew that this was the main navigation.
 *
 *
 * @see apply_filters()
 * @param array
 * @param string|null
 * @return string HTML
 **/
function public_nav(array $navArray, $navType=null)
{
    if ($navType) {
        $filterName = 'public_navigation_' . $navType;
        $navArray = apply_filters($filterName, $navArray);
    }
    
    return nav($navArray);
}

/**
 * Alias for public_nav($array, 'main'). This is to avoid potential typos so
 *  that all plugins can count on having at least a 'main' navigation filter in
 *  the public themes.
 * 
 * @todo Should we hard code the navigation that is in all themes into this
 *  array?
 * @param array
 * @uses public_nav()
 * @return string
 **/
function public_nav_main(array $navArray)
{
    return public_nav($navArray, 'main');
}

/**
 * Example: set_theme_base_uri('public');  uri('items');  --> example.com/items.
 * @access private
 * @param string
 * @return void
 **/
function set_theme_base_uri($theme = null)
{
    switch ($theme) {
        case 'public':
            $baseUrl = PUBLIC_BASE_URL;
            break;
        case 'admin':
            $baseUrl = ADMIN_BASE_URL;
            break;
        default:
            $baseUrl = CURRENT_BASE_URL;
            break;
    }
    
    return Zend_Controller_Front::getInstance()->setBaseUrl($baseUrl);
}

/**
 * Plugins should be able to hook into the header script for either admin or
 * public themes. The hooks involved are 'admin_theme_header',
 * 'public_theme_header'. This will allow us to disambiguate between themes(is
 * that an actual word?).
 *
 * Each hook implementation will receive the request object, which is the
 * easiest way to determine what page you are actually on at any given time. For
 * example:
 *
 * function myplugin_admin_header($request)
 * {
 *      if ($request->get('controller') == 'items') {
 *          // Load a stylesheet that you only want on the items pages 
 *      }  
 * }
 *
 * 
 * @return void
 **/
function admin_plugin_header()
{
    $request = Omeka_Context::getInstance()->getRequest();
    fire_plugin_hook('admin_theme_header', $request);
}

function admin_plugin_footer()
{
    $request = Omeka_Context::getInstance()->getRequest();
    fire_plugin_hook('admin_theme_footer', $request);
}

/**
 * Retrieve an Item object directly by its ID.
 * 
 * Example of usage on a public theme page:
 * 
 * $item = get_item_by_id(4);
 * set_current_item($item); // necessary to use item() and other similar theme API calls.
 * echo item('Title');
 * 
 * @param integer
 * @return Item|null
 **/
function get_item_by_id($itemId)
{
    return get_db()->getTable('Item')->find($itemId);
}

/**
 * @see get_item_by_id()
 * @param string
 * @return Collection|null
 **/
function get_collection_by_id($collectionId)
{
    return get_db()->getTable('Collection')->find($collectionId);
}

function get_user_by_id($userId)
{
    return get_db()->getTable('User')->find($userId);
}

function get_tags($params = array(), $limit = 10)
{
    $params['limit'] = $limit;
    return get_db()->getTable('Tag')->findBy($params);
}

function get_items($params = array(), $limit = 10)
{
    $params['per_page'] = $limit;
    return get_db()->getTable('Item')->findBy($params);
}

/**
 * @see get_items()
 * @see get_tags()
 * @param array
 * @param integer
 * @return array
 **/
function get_users($params = array(), $limit = 10)
{
    $params['limit'] = $limit;
    return get_db()->getTable('User')->findBy($params);
}

/**
 * @todo 'limit' and 'per_page' parameters should be combined across models
 * so that there is only one parameter for data retrieval (should be 'limit')
 * instead of 'per_page'.
 * 
 * @param array
 * @param integer
 * @return array
 **/
function get_collections($params = array(), $limit = 10)
{
    $params['per_page'] = $limit;
    // This line should be implied, may signal a need for refactoring.
    $params['page'] = 1;
    return get_db()->getTable('Collection')->findBy($params);
}

/**
 * Retrieve a full set of ItemType objects currently available to Omeka.
 * 
 * Keep in mind that the $params and $limit arguments are in place for the sake
 * of consistency with other data retrieval functions, though in this case
 * they don't have any effect on the number of results returned.
 * 
 * @param array
 * @param integer
 * @return array
 **/
function get_item_types($params = array(), $limit = 10)
{
    return get_db()->getTable('ItemType')->findAll();
}

/**
 * Determine whether or not the current item belongs to a collection.
 * 
 * @param string|null The name of the collection that the item would belong
 * to.  If null, then this will check to see whether the item belongs to
 * any collection.
 * @param Item|null Check for this specific item record (current item if null).
 * @return boolean
 **/
function item_belongs_to_collection($name=null, $item=null)
{
    //Dependency injection
    if(!$item) {
        $item = get_current_item();
    }
    
    return (!empty($item->collection_id) and (!$name or $item->Collection->name == $name));
}

/**
 * Determine whether an item has an item type.  
 * 
 * If no $name is given, this will return true if the item has any item type 
 * (items do not have to have an item type).  If $name is given, then this will
 * determine if an item has a specific item type.
 * 
 * @param string|null
 * @return boolean
 **/
function item_has_type($name = null)
{
    $itemTypeName = item('Item Type Name');
    return ($name and ($itemTypeName == $name)) or (!$name and !empty($itemTypeName));
}

/**
 * @uses display_files()
 * @uses get_current_item()
 * @param array
 * @return string HTML
 **/
function display_files_for_item($options = array())
{
    $item = get_current_item();
    return display_files($item->Files, $options);
}

/**
 * Returns the HTML markup for displaying a random featured item.  Most commonly
 * used on the home page of public themes.
 * 
 * @param boolean Whether or not the featured item should have an image associated 
 * with it.  If set to true, this will either display a clickable square thumbnail 
 * for an item, or it will display "You have no featured items." if there are 
 * none with images.
 * @return string HTML
 **/
function display_random_featured_item($withImage=false)
{
    $featuredItem = random_featured_item();

	$html = '<h2>Featured Item</h2>';
	if ($featuredItem) {
        set_current_item($featuredItem); // Needed for transparent access of item metadata.
	   $html .= '<h3>' . link_to_item() . '</h3>';
	   if (item_has_thumbnail()) {
	       $html .= link_to_square_thumbnail($featuredItem, array('class'=>'image'));
	   }
	   // Grab the 1st Dublin Core description field (first 150 characters)
	   $itemDescription = item('Description', array('snippet'=>150));
	   $html .= '<p class="item-description">' . $itemDescription . '</p>';
	} else {
	   $html .= '<p>You have no featured items.</p>';
	}
    
    return $html;
}

/**
 * Returns the HTML markup for displaying a random featured collection.  This will display an 
 * 
 * @param string
 * @return void
 **/
function display_random_featured_collection()
{
    $featuredCollection = random_featured_collection();
    set_current_collection($featuredCollection);
    $html = '<h2>Featured Collection</h2>';
    if ( $featuredCollection ) {
        $html .= '<h3>' . link_to_collection() . '</h3>';
        if ($featuredCollection->description) {
            $html .= '<p class="collection-description">' . collection('Description', array('snippet'=>150)) . '</p>';
        }
        
    } else {
        $html .= '<p>You have no featured collections.</p>';
    }
    return $html;
}

/**
 * @uses current_user_tags()
 * @uses get_current_item()
 * @param string
 * @return array
 **/
function current_user_tags_for_item()
{
    $item = get_current_item();
    return current_user_tags($item);
}

/**
 * Determine whether or not the item has any files associated with it.
 * 
 * @see has_files()
 * @uses Item::hasFiles()
 * @return boolean
 **/
function item_has_files()
{
    $item = get_current_item();
    return $item->hasFiles();
}

function item_has_thumbnail()
{
    return get_current_item()->hasThumbnail();
}

/**
 * @todo Should item_has_tags() check for certain tags?
 * @return boolean
 **/
function item_has_tags()
{
    $item = get_current_item();
    return (count($item->Tags) > 0);
}

function item_image($imageType, $props = array(), $index = 0, $item = null)
{
    if (!$item) {
        $item = get_current_item();
    }
    
    $imageFile = $item->Files[$index];
    $width = @$props['width'];
    $height = @$props['height'];
    return archive_image( $imageFile, $props, $width, $height, $imageType ); 
}

/**
 * HTML for a thumbnail image associated with an item.  Default parameters will
 * use the first image, but 
 * 
 * @param string
 * @return void
 **/
function item_thumbnail($props = array(), $index = 0)
{
    return item_image('thumbnail', $props, $index);
}

function item_square_thumbnail($props = array(), $index = 0)
{
    return item_image('square_thumbnail', $props, $index);
}

function item_fullsize($props = array(), $index = 0)
{
    return item_image('fullsize', $props, $index);
}

/**
 * Use this to choose an item type from a <select>
 * 
 * @uses ItemTypeTable::findAllForSelectForm()
 * @param array
 * @param string Selected value
 * @return string HTML
 **/
function select_item_type($props=array(), $value=null)
{
    return _select_from_table('ItemType', $props, $value);	
}

function select_item_type_elements($props = array(), $value = null)
{
    // We need a custom SQL statement for this particular select input, since we
    // are retrieving the elements in a specific set in a specific order.
    
    // Retrieve element ID and name for all elements in the Item Type element set.
    $db = get_db();
    $sql = $db->getTable('Element')->getSelect()
            ->where('es.name = ?', ELEMENT_SET_ITEM_TYPE)
            ->reset('columns')->from(array(), array('e.id', 'e.name'))
            ->order('e.name ASC'); // Sort alphabetically
    $pairs = $db->fetchPairs($sql);
    
    return select($props, $pairs, $value);    
}

/**
 * @access private
 * @param array
 * @param mixed
 * @return string HTML for a <select> input.
 **/
function _select_from_table($tableClass, $props = array(), $value = null)
{
    $options = get_db()->getTable($tableClass)->findPairsForSelectForm();
    return select($props, $options, $value);
}

/**
 * Select the Item Type for the current Item.  This probably won't
 * be used by any theme writers because it only applies to the form
 * that the items are on.
 * 
 * @param array
 * @return string HTML for the form input.
 **/
function select_item_type_for_item($props=array())
{
    $item = get_current_item();
    return select_item_type($props, $item->item_type_id);
}

/**
 * @param array
 * @param string
 * @return string
 **/
function select_collection($props = array(), $value=null)
{
    return _select_from_table('Collection', $props, $value);
}

/**
 * @param array
 * @param mixed
 * @return string HTML
 **/
function select_element($props = array(), $value = null)
{
    return _select_from_table('Element', $props, $value);
}

/**
 * @uses _select_from_table()
 */
function select_user($props = array(), $value=null)
{
    return _select_from_table('User', $props, $value);
}

function select_institution($props = array(), $value = null)
{
    $institutionInfo = get_db()->getTable('Entity')->findInstitutionsForSelectForm();
    
    return select($props, $institutionInfo, $value);
}

/**
 * @uses _select_from_table()
 */
function select_entity($props = array(), $value = null)
{
    return _select_from_table('Entity', $props, $value);
}

/**
 * Retrieve the Collection object for the current item.
 * 
 * @internal This is meant to be a simple facade for OO-based access to the Collection object.
 * Ideally theme writers won't have to interact with the actual collection object, so more helpers
 * should be built to provide syntactic sugar for this.
 * @access private
 * @return void
 **/
function get_collection_for_item()
{
    return get_current_item()->Collection;
}

/**
 * Link to the collection that the current item belongs to.
 * 
 * The default text displayed for this link will be the name of the collection,
 * but that can be changed by passing a string argument.
 * 
 * @param string
 * @return void
 **/
function link_to_collection_for_item($text = null, $props = array(), $action = 'show')
{
    return link_to_collection($text, $props, $action, get_collection_for_item());
}

/**
 * Output the tags for the current item as a string.
 * 
 * @todo Should this take a set of parameters instead of $order?  That would be 
 * good for limiting the # of tags returned by the query.
 * 
 * @see item_tags_as_cloud()
 * @param string $delimiter String that separates each tag.  Default is a comma 
 * and space.
 * @param string|null $order Options include 'recent', 'most', 'least', 'alpha'.  
 * Default is null, which means that tags will display in the order they were
 * entered via the form.
 * @param boolean $tagsAreLinked If tags should be linked or just represented as
 * text.  Default is true.
 * @return string HTML
 **/
function item_tags_as_string($delimiter = ', ', $order = null,  $tagsAreLinked = true)
{
    $tags = get_tags(array('sort'=>$order, 'record'=>get_current_item()));
    $urlToLinkTo = ($tagsAreLinked) ? uri('items/browse/tag/') : null;
    return tag_string($tags, $urlToLinkTo, $delimiter);
}

/**
 * @see item_tags_as_string()
 * @param string
 * @param boolean
 * @return string
 **/
function item_tags_as_cloud($order = null, $tagsAreLinked = true)
{
    $tags = get_tags(array('sort'=>$order, 'record'=>get_current_item()));
    $urlToLinkTo = ($tagsAreLinked) ? uri('items/browse/tag/') : null;
    return tag_cloud($tags, $urlToLinkTo);
}

/**
 * Retrieve the next item in the database.  
 * 
 * @todo Should this look for the next item in the loop, or just via the database?
 * 
 * @return Item|null
 **/
function get_next_item()
{
    return get_current_item()->next();
}

/**
 * @see get_previous_item()
 * 
 * @return Item|null
 **/
function get_previous_item()
{
    return get_current_item()->previous();
}

/**
 * Retrieve the current Item record
 * 
 * @throws Exception
 * @access private
 * @param string
 * @return void
 **/
function get_current_item()
{
    if (!($item = __v()->item)) {
        throw new Exception('An item has not been set to be displayed on this theme page!  Please see Omeka documentation for details.');
    }
    
    return $item;
}

/**
 * @access private
 * @see loop_items()
 * @param Item
 * @return void
 **/
function set_current_item(Item $item)
{
    $view = __v();
    $view->previous_item = $view->item;
    $view->item = $item;
}

/**
 * @access private
 */
function set_items_for_loop($items)
{
    $view = __v();
    $view->items = $items;
}

function get_items_for_loop()
{
    return __v()->items;
}

/**
 * @return boolean
 */
function has_items_for_loop()
{
    $view = __v();
    return ($view->items and count($view->items));
}

/**
 * Determine whether or not there are any items in the database.
 * 
 * @return boolean
 **/
function has_items()
{
    return (total_items() > 0);    
}

function has_collections()
{
    return (total_collections() > 0);
}

/**
 * Loops through items assigned to the current view.
 * @return mixed The current item
 */
function loop_items()
{
    return loop_records('items', get_items_for_loop());
}

/**
 * Loops through files assigned to the current item.
 * @return mixed The current file for an item
 */
function loop_files_for_item()
{
    $files = get_current_item()->Files;
    return loop_records('files_for_item', $files);
}

/**
 * Loops through collections assigned to the current view.
 * @return mixed The current collection
 */
function loop_collections()
{
    return loop_records('collections', get_collections_for_loop());
}

/**
 * Loops through a specific record set, setting the current record to a globally 
 * accessible scope and returning it.
 * 
 * @see loop_items()
 * @see loop_files_for_item()
 * @see loop_collections()
 * @param string $recordType The type of record to loop through
 * @param mixed $records The iterable set of records
 * @return mixed The current record
 */
function loop_records($recordType, $records)
{
    // If this is the first call to loop_records(), set a static record loop and 
    // set it to NULL.
    static $recordLoop = null;
    
    // If the record type index does not exist, set it with the provided 
    // records. We do this so multiple record types can coexist.
    if (!isset($recordLoop[$recordType])) {
        $recordLoop[$recordType] = $records;
    }
    
    // If we haven't reached the end of the loop, set the current record in the 
    // loop and return it. This advances the array cursor so the next loop 
    // iteration will get the next record.
    if (list($key, $record) = each($recordLoop[$recordType])) {
        
        // Set the current records, depending on the record type.
        switch ($recordType) {
            case 'items':
                set_current_item($record);
                break;
            case 'files_for_item':
                set_current_file($record);
                break;
            case 'collections':
                set_current_collection($record);
                break;
            default:
                throw new Exception('Error: Invalid record type was provided for the loop.');
                break;
        }
        
        return $record;
    }
    
    // Reset the particular record loop if the loop has finished (so we can run 
    // it again if necessary). Return false to indicate the end of the loop.
    unset($recordLoop[$recordType]);
    return false;
}

/**
 * @access private
 * @param Collection
 * @return void
 **/
function set_current_collection($collection)
{
    __v()->collection = $collection;
}

/**
 * 
 * @param string
 * @return void
 **/
function set_collections_for_loop($collections)
{
    __v()->collections = $collections;
}

function get_collections_for_loop()
{
    return __v()->collections;
}

/**
 * @access private
 * @return Collection|null
 **/
function get_current_collection()
{
    return __v()->collection;
}

/**
 * This is a similar interface to item(), except for accessing metadata about collections.
 * 
 * As of the date of writing, it is greatly simplified in comparison to item(), 
 * mostly because collections do not (and may not ever) utilize the 'elements'
 * metadata schema.
 * 
 * @see item()
 * @param string
 * @param array $options
 * @return string|array
 **/
function collection($fieldName, $options=array())
{
    $collection = get_current_collection();
    
    // Retrieve the data to display.  
    switch (strtolower($fieldName)) {
        case 'id':
            $text = $collection->id;
            break;
        case 'name':
        case 'title':   // Title and Name are aliased (since technically collections should have a title, not a name).
            $text = $collection->name;
            break;
        case 'description':
            $text = $collection->description;
            break;
        case 'public':
            $text = $collection->public;
            break;
        case 'featured':
            $text = $collection->featured;
            break;
        case 'date added':
            $text = $collection->timeOfLastRelationship('added');
            break;
        case 'date modified':
            $text = $collection->timeOfLastRelationship('modified');
            break;
        case 'collectors': // The names of collectors
            $text = array();
            foreach ($collection->Collectors as $key => $collector) {
                $text[$key] = $collector->name;
            }
            break;
        default:
            throw new Exception('Field does not exist for collections!');
            break;
    }
    
    // Apply any options to it.
    if (isset($options['snippet'])) {
        $text = snippet($text, 0, (int)$options['snippet']);
    }
    
    // Escape it for display as HTML.
    if (!is_array($text)) {
        $text = apply_filters('html_escape', $text);
    } else {
        foreach ($text as $key => $value) {
            $text[$key] = apply_filters('html_escape', $value);
        }
    }
    
    // Return the join'd text
    if (isset($options['delimiter'])) {
        $text = join((string) $options['delimiter'], (array) $text);
    }
    return $text;
}

/**
 * Retrieve a certain # of items in the collection
 * 
 * @param string
 * @return void
 **/
function loop_items_in_collection($num = 10, $options = array())
{
    // Cache this so we don't end up calling the DB query over and over again
    // inside the loop.
    static $loopIsRun = false;
    if (!$loopIsRun) {
        // Retrieve a limited # of items based on the collection given.
        $items = get_items(array('collection'=>get_current_collection()->id, 'per_page'=>$num));
        set_items_for_loop($items);
    }
    
    return loop_items();
}

function total_items_in_collection()
{
    return get_current_collection()->totalItems();
}

function collection_has_collectors()
{
    return get_current_collection()->hasCollectors();
}

function collection_is_public()
{
    return get_current_collection()->public;
}

function collection_is_featured()
{
    return get_current_collection()->featured;
}

/**
 * @internal Duplication between this and set_current_item().  Factor into
 * separate
 * 
 * @access private
 * @param string
 * @return void
 **/
function set_current_file(File $file)
{
    __v()->file = $file;
}

/**
 * @access private
 * @return File
 **/
function get_current_file()
{
    return __v()->file;
}

function link_to_advanced_search($text = 'Advanced Search', $props = array())
{
    // Is appending the query string directly a security issue?  We should figure that out.
    $props['href'] = uri('items/advanced-search') . (!empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '');
    return '<a ' . _tag_attributes($props) . '>' . $text . '</a>';
}

/**
 * Get the proper HTML for a link to the browse page for items, with any appropriate
 * filtering parameters passed to the URL.
 * 
 * @param string Text to display in the link.
 * @param array Any parameters to use to build the browse page URL, e.g.
 * array('collection' => 1) would build items/browse?collection=1 as the URL.
 * @return string HTML
 **/
function link_to_browse_items($text, $browseParams = array(), $linkProperties = array())
{
    // Set the link href to the items/browse page.
    $linkProperties['href'] = uri(array('controller'=>'items', 'action'=>'browse'), 'default', $browseParams);
    return "<a " . _tag_attributes($linkProperties) . ">$text</a>";
}

/**
 * Return the pagination string.
 * 
 **/
function pagination_links($options = array('scrolling_style' => null, 
                                     'partial_file'    => null, 
                                     'page_range'      => null, 
                                     'total_results'   => null, 
                                     'page'            => null, 
                                     'per_page'        => null))
{
    if (Zend_Registry::isRegistered('pagination')) {
        // If the pagination variables are registered, set them for local use.
        $p = Zend_Registry::get('pagination');
	} else {
        // If the pagination variables are not registered, set required defaults 
        // arbitrarily to avoid errors.
        $p = array('total_results'   => 1, 
                   'page'            => 1, 
                   'per_page'        => 1);
    }
    
    // Set preferred settings.
    $scrollingStyle   = $options['scrolling_style'] ? $options['scrolling_style']     : 'Sliding';
    $partial          = $options['partial_file']    ? $options['partial_file']        : 'common' . DIRECTORY_SEPARATOR . 'pagination_control.php';
    $pageRange        = $options['page_range']      ? (int) $options['page_range']    : 5;
    $totalCount       = $options['total_results']   ? (int) $options['total_results'] : (int) $p['total_results'];
    $pageNumber       = $options['page']            ? (int) $options['page']          : (int) $p['page'];
    $itemCountPerPage = $options['per_page']        ? (int) $options['per_page']      : (int) $p['per_page'];
    
    // Create an instance of Zend_Paginator.
    $paginator = Zend_Paginator::factory($totalCount);
    
    // Configure the instance.
    $paginator->setCurrentPageNumber($pageNumber)
              ->setItemCountPerPage($itemCountPerPage)
              ->setPageRange($pageRange);
    
    return __v()->paginationControl($paginator, 
                                    $scrollingStyle, 
                                    $partial);
}

function show_item_metadata(array $options = array())
{
    $item = get_current_item();
    return __v()->itemShow($item, $options);
}

function snippet_by_word_count($phrase, $maxWords, $ellipsis = '...')
{
    $phraseArray = explode(' ', $phrase);
    if (count($phraseArray) > $maxWords && $maxWords > 0) {
        $phrase = implode(' ', array_slice($phraseArray, 0, $maxWords)) . $ellipsis;
    }
    return $phrase;
}

