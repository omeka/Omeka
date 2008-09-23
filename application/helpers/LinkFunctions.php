<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @subpackage LinkHelpers
 **/

/**
 * Uses uri() to generate <a> tags for a given link.
 * 
 * @since 0.10 No longer escapes the text for the link.  This text must be valid
 * HTML.
 *
 * @param Omeka_Record|string $record The name of the controller to use for the
 * link.  If a record instance is passed, then it inflects the name of the 
 * controller from the record class.
 * @param string $action The action to use for the link (optional)
 * @param string $text The text to put in the link
 * @param array $props Attributes for the <a> tag
 * @return string HTML
 **/
function link_to($record, $action=null, $text='View', $props = array())
{
    // If we're linking to a record somewhere, we have to 
    if($record instanceof Omeka_Record) {
        $url = record_uri($record, $action);
    }
    else {
        // Otherwise $record is the name of the controller to link to.
        $urlOptions = array();
        //Use Zend Framework's built-in 'default' route
        $route = 'default';
        $urlOptions['controller'] = (string) $record;
        if($action) $urlOptions['action'] = (string) $action;
        $url = uri($urlOptions, $route);
    }

	$attr = !empty($props) ? ' ' . _tag_attributes($props) : '';
	return '<a href="'. $url . '"' . $attr . '>' . $text . '</a>';
}

/**
 * @since 0.10 Function signature has changed so that the item to link to can be
 * determined by the context of the function call.  Also, text passed to the link
 * must be valid HTML (will not be automatically escaped because any HTML can be
 * passed in, e.g. an <img /> or the like).
 * 
 * @param string HTML for the text of the link.
 * @param array Properties for the <a> tag. (optional)
 * @param string The page to link to (this will be the 'show' page almost always
 * within the public theme).
 * @param Item Used for dependency injection testing or to use this function outside
 * the context of a loop.
 * @return string HTML
 **/
function link_to_item($text = null, $props = array(), $action = 'show', $item=null)
{
    if(!$item) {
        $item = get_current_item();
    }

	$text = (!empty($text) ? $text : item('Title'));
	
	return link_to($item, $action, $text, $props);
}

function link_to_items_rss($params=array())
{	
	return '<a href="' . items_rss_uri($params) . '" class="rss">RSS</a>';
}

/**
 * 
 *
 * @return string
 **/
function link_to_next_item($text="Next Item --&gt;", $props=array())
{
    $item = get_current_item();
	if($next = $item->next()) {
		return link_to($next, 'show', $text, $props);
	}
}

/**
 * 
 *
 * @return string
 **/
function link_to_previous_item($text="&lt;-- Previous Item", $props=array())
{
    $item = get_current_item();
	if($previous = $item->previous()) {
		return link_to($previous, 'show', $text, $props);
	}
}

/**
 * 
 *
 * @return string
 **/
function link_to_collection($text=null, $props=array(), $action='show', $collectionObj = null)
{
    if (!$collectionObj) {
        $collectionObj = get_current_collection();
    }
    
	$text = (!empty($text) ? $text : (!empty($collectionObj->name) ? $collectionObj->name : '[Untitled]'));
	
	return link_to($collectionObj, $action, $text, $props);
}

/**
 * 
 *
 * @return string|false
 **/
function link_to_thumbnail($item, $props=array(), $action='show', $random=false)
{
    return _link_to_archive_image($item, $props, $action, $random, 'thumbnail');
}

/**
 *
 * @return string|false
 **/
function link_to_fullsize($item, $props=array(), $action='show', $random=false)
{
    return _link_to_archive_image($item, $props, $action, $random, 'fullsize');
}

/**
 * 
 *
 * @return string|false
 **/
function link_to_square_thumbnail($item, $props=array(), $action='show', $random=false)
{
    return _link_to_archive_image($item, $props, $action, $random, 'square_thumbnail');
}

/**
 * Returns a link to an item, where the link has been populated by a specific image format for the item
 *
 * @access private
 * @return string|false
 **/
function _link_to_archive_image($item, $props=array(), $action='show', $random=false, $imageType = 'thumbnail')
{
	if(!$item or !$item->exists()) return false;
	
	$path = 'items/'.$action.'/' . $item->id;
	$output = '<a href="'. uri($path) . '" ' . _tag_attributes($props) . '>';
	
	if($random) {
		$output .= archive_image($item, array(), null, null, $imageType);
	}else {
		$output .= archive_image($item->Files[0], array(), null, null, $imageType);
	}
	$output .= '</a>';	
	
	return $output;
}

/**
 * 
 * @since 0.10 All arguments to this function are optional.  If no text is given,
 * it will automatically use the text for the 'site_title' option.
 * @since 0.10 The text passed to this function will not be automatically escaped
 * with htmlentities(), which allows for passing images or other HTML in place of text.
 * @return string
 **/
function link_to_home_page($text = null, $props = array())
{
    if (!$text) {
        $text = settings('site_title');
    }
	$uri = WEB_ROOT;
	return '<a href="'.$uri.'" '._tag_attributes($props).'>' . $text . "</a>\n";
}

/**
 * 
 *
 * @return string
 **/
function link_to_admin_home_page($text, $props = array())
{
	return '<a href="'.admin_uri('').'" '._tag_attributes($props).'>'.htmlentities($text)."</a>\n";
}