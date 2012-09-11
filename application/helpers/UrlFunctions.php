<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka_ThemeHelpers
 * @subpackage UrlHelpers
 */

/**
 * Return a URL given the provided arguments.
 *
 * @uses Omeka_View_Helper_Url::url() See for details on usage.
 * @param string|array $options
 * @param string|null|array $name
 * @param array $queryParams
 * @param boolean $reset
 * @param boolean $encode
 * @return string
 */
function url($options = array(), $name = null, $queryParams = array(), $reset = false, $encode = true) {
    $urlHelper = new Omeka_View_Helper_Url;
    return $urlHelper->url($options, $name, $queryParams, $reset, $encode);
}

/**
 * Return an absolute URL.
 *
 * This is necessary because Zend_View_Helper_Url returns relative URLs, though 
 * absolute URLs are required in some contexts.
 *
 * @uses url()
 * @param mixed
 * @return string HTML
 */
function absolute_url($options = array(), $route = null, $queryParams = array(), $reset = false, $encode = true) {
    $view = __v();
    return $view->serverUrl() . $view->url($options, $route, $queryParams, $reset, $encode);
}

/**
 * Return a URL to a record.
 *
 * @uses Omeka_View_Helper_GetRecordUrl::getRecordUrl()
 * @param Omeka_Record_AbstractRecord|string $record
 * @param string|null $action
 * @param bool $getAbsoluteUrl
 * @return string
 */
function record_url($record, $action = null, $getAbsoluteUrl = false)
{
    return __v()->getRecordUrl($record, $action, $getAbsoluteUrl);
}

/**
 * Return the current URL with query parameters appended.
 *
 * @param array $params
 * @return string
 */
function current_url(array $params = array())
{
    return __v()->getCurrentUrl($params);
}

/**
 * Determine whether the given URI matches the current request URI.
 *
 * @param string $url
 * @param Zend_Controller_Request_Http|null $req
 * @return boolean
 */
function is_current_url($url)
{
    return __v()->isCurrentUrl($url);
}

/**
 * Return a URL to an output page.
 * 
 * @param string $output
 * @param array $otherParams
 * @return string
 */
function items_output_url($output, $otherParams = array()) {
    
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
    return url(array('controller'=>'items', 'action'=>'browse'), 'default', $queryParams);
}

/**
 * Return the provided file's URL.
 * 
 * @param File $file
 * @param string $format
 * @return string
 */
function file_display_url(File $file, $format = 'fullsize')
{
    if (!$file->exists()) {
        return false;
    }
    return $file->getWebPath($format);
}

/**
 * Return a URL to the public theme.
 *
 * @see admin_url()
 * @param mixed
 * @return string
 */
function public_url()
{
    set_theme_base_url('public');
    $args = func_get_args();
    $url = call_user_func_array('uri', $args);
    revert_theme_base_url();
    return $url;
}

/**
 * Return a URL to the admin theme.
 * 
 * @see public_url()
 * @param mixed
 * @return string
 */
function admin_url()
{
    set_theme_base_url('admin');
    $args = func_get_args();
    $url = call_user_func_array('uri', $args);
    revert_theme_base_url();
    return $url;
}

/**
 * Set the base URL for the specified theme.
 * 
 * @param string $theme
 */
function set_theme_base_url($theme = null)
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
 * Revert the base URL to its previous state.
 */
function revert_theme_base_url()
{
    $front = Zend_Controller_Front::getInstance();
    if (($previous = $front->getParam('previousBaseUrl')) !== null) {
        $front->setBaseUrl($previous);
        $front->clearParams('previousBaseUrl');
    }
}
