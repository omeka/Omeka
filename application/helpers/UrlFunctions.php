<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka_ThemeHelpers
 * @subpackage UrlHelpers
 */

/**
 * @since 0.10 Incorporates search parameters into the query string for the URI.
 * This enables auto_discovery_link_tag() to automatically discover the RSS feed
 * for any search.
 * @since 0.10 Adds a second argument so that extra query parameters can be used
 * to build the URI for the output feed.
 * @internal This filters query parameters via a blacklist instead of a whitelist,
 * because conceivably plugins could add extra fields to the advanced search.
 * @param string
 * @param array $otherParams Optional set of query parameters to merge in to the
 * default output feed URI query string.
 * @return string URI
 */
function items_output_uri($output="rss2", $otherParams = array()) {
    // Copy $_GET and filter out all the cruft.
    $queryParams = $_GET;
    // The submit button the search form.
    unset($queryParams['submit_search']);
    // If 'page' is passed in query string and not via the route
    // Page should always be the first so that accurate results are retrieved
    // for the RSS.  Does it make sense to get an RSS feed of the 2nd page?
    unset($queryParams['page']);

    $queryParams = array_merge($queryParams, $otherParams);

    $queryParams['output'] = $output;
    // Use the 'default' route as opposed to the current route.
    return uri(array('controller'=>'items', 'action'=>'browse'), 'default', $queryParams);
}

/**
 * Return a valid URL when given a set of options.
 *
 * @uses Omeka_View_Helper_Url::url() See for details on usage.
 * @param string|array Either a string URL stub or a set of options for
 * building a URL from scratch.
 * @param string The name of a route to use to generate the URL (optional)
 * @param array Set of query parameters to append to the URL (optional)
 * @return string
 */
function uri($options=array(), $route=null, $queryParams=array(), $reset = false, $encode = true)
{
    require_once HELPER_DIR . '/Url.php';
    $urlHelper = new Omeka_View_Helper_Url;
    return $urlHelper->url($options, $route, $queryParams, $reset, $encode);
}

/**
 * Returns the current URL (optionally with query parameters appended).
 *
 * @since 0.9
 * @param array $params Optional Set of query parameters to append.
 * @return string
 */
function current_uri($params=array())
{
    //Grab everything before the ? of the query
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $uriParts = explode('?', $request->getRequestUri());
    $uri = array_shift($uriParts);

    if(!empty($params)) {

        //The query should be a combination of $_GET and passed parameters
        $query = array_merge($_GET, $params);

        $query_string = http_build_query($query);
        $uri .= '?' . $query_string;
    }

    return $uri;
}

/**
 * Determine whether or not a given URI matches the current request URI.
 *
 * @since 0.9
 * @param string $link URI.
 * @param Zend_Controller_Request_Http|null $req
 * @return boolean
 */
function is_current_uri($link, $req = null) {

    if(!$req) {
        $req = Zend_Controller_Front::getInstance()->getRequest();
    }
    $current = $req->getRequestUri();
    $base = $req->getBaseUrl();

    //Strip out the protocol, host, base URI, rightmost slash before comparing the link to the current one
    $strip_out = array(WEB_DIR, @$_SERVER['HTTP_HOST'], $base);
    $current = rtrim( str_replace($strip_out, '', $current), '/');
    $link = rtrim( str_replace($strip_out, '', $link), '/');

    if(strlen($link) == 0) return (strlen($current) == 0);
    return ($link == $current) or (strpos($current, $link) === 0);
}

/**
 * @deprecated Since 1.3
 * @return string
 */
function file_download_uri(File $file, $format='archive')
{
    return file_display_uri($file, $format);
}

function file_display_uri(File $file, $format='fullsize')
{
    if (!$file->exists()) {
        return false;
    }
    return $file->getWebPath($format);
}

/**
 * Given an Omeka_Record instance and the name of an action, this will generated
 * the URI for that record.  Used primarily by other theme helpers and most likely
 * not useful for theme writers.
 *
 * @since 0.10
 * @param Omeka_Record $record
 * @param string $action
 * @param string|null $controller Optional
 * @return string
 */
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
 * Retrieve a URL for the current item.
 *
 * @since 0.10
 * @param string $action Action to link to for this item.  Default is 'show'.
 * @uses record_uri()
 * @param Item|null Check for this specific item record (current item if null).
 * @return string URL
 */
function item_uri($action = 'show', $item=null)
{
    if (!$item) {
        $item = get_current_item();
    }
    return record_uri($item, $action);
}

/**
 * This behaves as uri() except it always provides a url to the public theme.
 *
 * @since 0.10
 * @see uri()
 * @see admin_uri()
 * @param mixed
 * @return string
 */
function public_uri()
{
    set_theme_base_uri('public');
    $args = func_get_args();
    $url = call_user_func_array('uri', $args);
    revert_theme_base_uri();
    return $url;
}

/**
 * @since 0.10
 * @see public_uri()
 * @param mixed
 * @return mixed
 */
function admin_uri()
{
    set_theme_base_uri('admin');
    $args = func_get_args();
    $url = call_user_func_array('uri', $args);
    revert_theme_base_uri();
    return $url;
}

/**
 * Generate an absolute URI.
 *
 * Useful because Zend Framework's default URI helper generates relative URLs,
 * though absolute URIs are required in some contexts.
 *
 * @since 0.10
 * @uses uri()
 * @param mixed
 * @return string HTML
 */
function abs_uri()
{
    $args = func_get_args();
    return __v()->serverUrl() . call_user_func_array('uri', $args);
}

/**
 * Generate an absolute URI to an item.  Primarily useful for generating permalinks.
 *
 * @since 0.10
 * @param Item|null Check for this specific item record (current item if null).
 * @return void
 */
function abs_item_uri($item = null)
{
    if (!$item) {
        $item = get_current_item();
    }

    return abs_uri(array('controller'=>'items', 'action'=>'show', 'id'=>$item->id), 'id');
}

/**
 * Example: set_theme_base_uri('public');  uri('items');  --> example.com/items.
 * @access private
 * @since 0.10
 * @param string
 * @return void
 */
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
    $front = Zend_Controller_Front::getInstance();
    $front->setParam('previousBaseUrl', $front->getBaseUrl());
    return $front->setBaseUrl($baseUrl);
}

/**
 * @since 1.3
 */
function revert_theme_base_uri()
{
    $front = Zend_Controller_Front::getInstance();
    if (($previous = $front->getParam('previousBaseUrl')) !== null) {
        $front->setBaseUrl($previous);
        $front->clearParams('previousBaseUrl');
    }
}
