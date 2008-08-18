<?php 
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * Retrieve the values for a given field in the current item.
 * 
 * @param string
 * @return array
 **/
function item($field, $options=array())
{
    return __v()->item($field, $options);
}

/**
 * Retrieve the metadata for a specific element, given its name.
 * 
 * @todo Switch out dummy data with live database.
 * @param string Name of the element
 * @param string Possible fields include 'description', 'type name', 
 * 'type description', 'type regex', 'set name', 'set description'
 * @return string
 **/
function element_metadata($elementName, $field)
{
    switch ($field) {
        case 'description':
            return "Dummy description!";
            break;
        default:
            # code...
            break;
    }
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
 * @todo Ideally this would be able to hook into plugins being able to define
 * new citation formats.  ZoterOmeka anyone?
 * @return string
 **/
function item_citation()
{
    if($citation = item('Citation')) {
		return $citation;
	}

	$cite = '';
    $cite .= item('Creator', 0);
    if ($cite != '') $cite .= ', ';
    $cite .= '"' . item('Title', ', ') . '". ';
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
function url_for_record(Omeka_Record $record, $action, $controller = null)
{
    $options = array();
    // Inflect the name of the controller from the record class if no
    // controller name is given
    $options['controller'] = !$controller ? 
        strtolower(Inflector::pluralize(get_class($record))) : $controller;
    $options['id'] = $record->id;
    $options['action'] = $action;
    
    // Use the 'id' route for all urls pointing to records
    return url_for($options, 'id');
}

/**
 * Retrieve a URL for the current item
 * 
 * @param string Action to link to for this item.
 * @return string URL
 **/
function url_for_item($action = 'show')
{
    return url_for_record(get_current_item(), $action);
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
            ->where('es.name = ?', 'Item Type')
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

/**
 * @return boolean
 */
function has_items_for_loop()
{
    $view = __v();
    return ($view->items and count($view->items));
}

/**
 * Determine whether or not the current request is on the admin theme.
 * 
 * @return boolean
 **/
function theme_is_admin()
{
    return defined('ADMIN');
}

/**
 * Use in while statement to loop through a set of Item records.  This will
 * set the current item.
 * 
 * If the reset parameter is passed, it will reset the loop.
 *
 * @param boolean
 * @return boolean
 **/
function loop_items($reset=false)
{
    static $set = null;
    
    if(!$set) {
        $set = __v()->items;
    }
    
    //If we haven't reached the end of the loop, set the current item
    //in the loop and 
    if(list($key, $item) = each($set)) {
        set_current_item($item);
        return $item;
    }
    
    //Reset the set of items if the loop has finished (so we can run it again
    //if necessary)
    $set = null;
    return false;
}

/**
 * @internal There is some serious duplication between this and loop_items().
 * It would be good to factor this out before release.
 * @todo Refactoring
 * @see loop_items()
 * @uses set_current_file()
 * @uses get_current_item()
 * @param boolean
 * @return boolean
 **/
function loop_files_for_item($reset=false)
{
    static $files = null;
    if(!$files) {
        $files = get_current_item()->Files;
    }
    
    if(list($key, $file) = each($files)) {
        set_current_file($file);
        return true;
    }
    
    //Reset loop at end
    $files = null;
    return false;
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

function link_to_advanced_search()
{
    // Is appending the query string directly a security issue?  We should figure that out.
    return '<a href="' . url_for('items/advanced-search') .'?' . $_SERVER['QUERY_STRING'].  '">Advanced Search</a>';
}

function add_item_filter($field, $callback)
{
    
}

/**
 * Return the pagination string.
 * 
 **/
function pagination($options = array('scrolling_style' => null, 
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
