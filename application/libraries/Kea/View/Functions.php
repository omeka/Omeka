<?php
/**
 * Not quite a helper, these functions defy definition...
 * 
 * Ok so not really.  All they do is help theme creators
 * do some pretty basic things like include images, css or js files.
 * 
 * They purposely do not use objects in order to simplify the theme
 * writer's need to understand the underlying system at work.
 * 
 * However, they make use of Zend::registry a lot, which may be a
 * speed issue in the long term.
 * 
 * @package Omeka
 * @author Nate Agrin
 */

/**
 * Echos the physical path to the theme.
 * This should be used when you need to include a file through PHP.
 */
function theme_path($return = false) {
	$path = Zend::registry('theme_path');
	if($return) return $path;
	else echo $path;
}

/**
 * Echos the web path of the theme.
 * This should be used when you need to link in an image or other file.
 */
function web_path($return = false) {
	$path = Zend::registry('theme_web');
	if($return) return $path;
	else echo $path;
}

function src($file, $dir, $ext = null, $return = false) {
	$physical = Zend::registry('theme_path').DIRECTORY_SEPARATOR.$dir.DIRECTORY_SEPARATOR.$file;
	if ($ext !== null) {
		$physical .= '.'.$ext;
	}
	if (file_exists($physical)) {
		$path = Zend::registry('theme_web').DIRECTORY_SEPARATOR.$dir.DIRECTORY_SEPARATOR.$file.'.'.$ext;
		if($return) return $path;
		else echo $path;
	}
	else {
		throw new Exception('Cannot find '.$file.'.'.$ext);
	}
}

/**
 * Echos the web path (that's what's important to the browser)
 * to a javascript file.
 * $dir defaults to 'javascripts'
 * $file should not include the .js extension
 */
function script($file, $dir = 'javascripts') {
	src($file, $dir, 'js');
}

/**
 * Echos the web path to a css file
 * $dir defaults to 'css'
 * $file should not include the .css extension
 */
function css($file, $dir = 'css') {
	src($file, $dir, 'css');
}

/**
 * Echos the web path to an image file
 * $dir defaults to 'images'
 * $file SHOULD include an extension, many image exensions are possible
 */
function img($file, $dir = 'images') {
	src($file, $dir);
}

function common($file, $vars = array(), $dir = 'common') {
	$path = Zend::registry('theme_path').DIRECTORY_SEPARATOR.$dir.DIRECTORY_SEPARATOR.$file.'.php';
	if (file_exists($path)) {
		extract($vars);
		include $path;
	}
}

function head($vars = array(), $file = 'header') {
	common($file, $vars);
}

function footer($vars = array(), $file = 'footer') {
	common($file, $vars);
}

/**
 * Simple access to form errors
 * 
 * @example error($item, 'title');
 * @return void
 * @author Kris Kelly
 **/
function error($record, $field_name = null) {
	$error = $record->getErrorMsg($field_name);
	if(!empty($error)) {
		echo 'Error: '.$error;
	}
}

/**
 * similar to wp_header() from Wordpress, hooks into the plugin system within the header
 *
 * @return void
 **/
function plugin_header() {
	Kea_Controller_Plugin_Broker::getInstance()->header();
}

function tag_cloud($tags, $largest, $link = null, $max = '4', $min = '1', $units = 'em', $return = false )
{
	$html = '';
	foreach( $tags as $tag )
	{
		$size = round( ( ( $tag->tagCount / $largest ) * $max ), 3 );
		
		$size = ($size < $min) ? $min : $size;

		$html .= '<span style="font-size:' . $size . $units . '">';

		if( $link )
		{
			$html .= '<a href="' . $link . '?tags=' . $tag['name'] . '">';
		}

		$html .= $tag['name'];

		if( $link )
		{
			$html .= '</a>';
		}

		$html .= '</span>' . "\n";

	
	}
	if($return) return $html;
	echo $html;
}

/**
 * Adapted from Zend_View_Helper_Url
 *
 * Generates an url given the name of a route.
 * 
 * @param array $urlOptions Options passed to the assemble method of the Route object.
 * @param mixed $name The name of a Route to use. If null it will use the current Route
 * 
 * @return string Url for the link href attribute.
 **/
function url($urlOptions = array(), $name = null)
{
    
    $ctrl = Kea_Controller_Front::getInstance();
    $router = $ctrl->getRouter();
    
    if (empty($name)) {
        $route = $router->getCurrentRoute();
    } else {
        $route = $router->getRoute($name);
    }
    
    $request = $ctrl->getRequest();
    
    $url = rtrim($request->getBaseUrl(), '/') . '/';
    $url .= $route->assemble($urlOptions);
     
    return $url;
    
}

?>