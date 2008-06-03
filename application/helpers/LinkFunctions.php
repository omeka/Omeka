<?php
/**
 * Uses url_for() to generate <a> tags for a given link
 *
 * @param Omeka_Record|string $record The name of the controller to use for the
 * link.  If a record instance is passed, then it inflects the name of the 
 * controller from the record class.
 * @param string $action The action to use for the link (optional)
 * @param string $text The text to put in the link
 * @param array $props Attributes for the <a> tag
 * @return string HTML
 **/
function link_to($record, $action=null, $text, $props = array())
{
    $urlOptions = array();
    //Use Zend Framework's built-in 'default' route
    $route = 'default';
    
    if($record instanceof Omeka_Record) {
        $urlOptions['controller'] = strtolower(Inflector::pluralize(get_class($record)));
        $urlOptions['id'] = $record->id;
        $route = 'id';
    }
    else {
        $urlOptions['controller'] = (string) $record;
    }
    
    if($action) $urlOptions['action'] = (string) $action;
    
	$url = url_for($urlOptions, $route);

	$attr = !empty($props) ? ' ' . _tag_attributes($props) : '';
	return '<a href="'. $url . '"' . $attr . ' title="View '. htmlentities($text).'">' . h($text) . '</a>';
}

function link_to_item($action='show', $text=null, $props=array(), $item=null)
{
    if(!$item) {
        $item = get_current_item();
    }

	$text = (!empty($text) ? $text : item('Title', 0));
	
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
function link_to_next_item($text="Next Item -->", $props=array())
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
function link_to_previous_item($text="<-- Previous Item", $props=array())
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
function link_to_collection($collection, $action='show', $text=null, $props=array())
{
	$text = (!empty($text) ? $text : (!empty($collection->name) ? $collection->name : '[Untitled]'));
	
	return link_to($collection, $action, $text, $props);
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
 *
 * @return string
 **/
function link_to_home_page($text, $props = array())
{
	$uri = WEB_ROOT;
	return '<a href="'.$uri.'" '._tag_attributes($props).'>'.h($text)."</a>\n";
}

/**
 * 
 *
 * @return string
 **/
function link_to_admin_home_page($text, $props = array())
{
	return '<a href="'.admin_uri().'" '._tag_attributes($props).'>'.h($text)."</a>\n";
}

/**
 * Alias for pagination().
 *
 * @todo Reimplement the menu option (for choosing # of results to show per_page).
 * @see pagination()
 * @param array
 * @return string
 **/
function pagination_links(array $options=array())
{
    return pagination($options);
}
