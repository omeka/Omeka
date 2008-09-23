<?php 
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @subpackage UrlHelpers
 **/

// Function to easily generate various xml outputs of items
function items_output_uri($output="rss2") {
	return uri('items/?output='.$output);
}

/**
 * Return a valid URL when given a set of options.
 * 
 * @param string|array Either a string URL stub or a set of options for 
 * building a URL from scratch.
 * @param string The name of a route to use to generate the URL (optional)
 * @param array Set of query parameters to append to the URL (optional)
 * @return string
 **/
function uri($options=array(), $route=null, $queryParams=array(), $reset = false, $encode = true)
{
    return __v()->url($options, $route, $queryParams);
}

/**
 * Returns the current URL (optionally with query parameters appended)
 *
 * @return void
 **/
function current_uri($params=array()) 
{
	//Grab everything before the ? of the query
	$uri = array_shift(explode('?', $_SERVER['REQUEST_URI']));
	
	if(!empty($params)) {
		
		//The query should be a combination of $_GET and passed parameters
		$query = array_merge($_GET, $params);
				
		$query_string = http_build_query($query);
		$uri .= '?' . $query_string;
	}
	
	return $uri;
}

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

function items_rss_uri($params=array())
{
	$params['output'] = 'rss2';
	
	//In case $_GET is passed from a search of items, don't include the submit form button
	unset($params['submit_search']);
	
	$uri = uri('items/browse', $params);	
	
	return $uri;
}


function item_permalink_url($item)
{
	return WEB_DIR . '/items/show/' . $item->id;
}

/**
 * @see FilesController
 * @see routes.ini (display/download routes)
 *
 * @return string
 **/
function file_download_uri($file, $format='fullsize')
{
	if(!$file or !$file->exists()) return false;
	$options = array('controller'=>'files', 'action'=>'get', 'id'=>$file->id, 'format'=>$format);
	$uri = uri($options, 'download');
	
	return $uri;
}

function file_display_uri($file, $format='fullsize')
{
	if(!$file->exists()) return false;
	$options = array('controller'=>'files', 'action'=>'get', 'id'=>$file->id, 'format'=>$format);
	return uri($options, 'display');
}