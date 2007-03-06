<?php
include_once 'UnicodeFunctions.php';
include_once 'FormFunctions.php';
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
function js($file, $dir = 'javascripts') {
	echo '<script type="text/javascript" src="'.src($file, $dir, 'js', true).'"></script>'."\n";
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

function foot($vars = array(), $file = 'footer') {
	common($file, $vars);
}

/**
 * Simple access to form errors
 * 
 * @example error($item, 'title');
 * @return void
 * 
 **/
function error($record, $field_name = null) {
	$error = $record->getErrorMsg($field_name);
	if(!empty($error)) {
		echo 'Error: '.$error;
	}
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
 * @param string $urlEnd The controller/action/parameter that specifies the link.
 * @example url('items/browse/'.$item->id);
 * @return string Url for the link href attribute.
 **/
function uri($urlEnd)
{
    
    $ctrl = Kea_Controller_Front::getInstance();
    
    $request = $ctrl->getRequest();
    
    $url = rtrim($request->getBaseUrl(), '/') . '/';
    
	$url .= $urlEnd;
 
    return $url;
    
}

/**
 * Stolen directly from Rails, and why not, because Ruby
 * and Rails are simply better than PHP and Zend's shitty framework, period.
 * In fact this is the last time I ever use this bullshit, sorry excuse for
 * a programming language.
 * 
 * 
 */
function flash()
{
	require_once 'Zend/Session.php';
	$flash = new Zend_Session('flash');
	$msg = $flash->msg;
	$flash->msg = null;
	if ($msg === null) {
		return false;
	}
	return $msg;
}

///// NAVIGATION /////

/**
 * Generate navigation list items, with class "current" for the chosen item
 *
 * @param array Key = Text of Navigation, Value = Link
 * @example primary_nav(array('Themes' => uri('themes/browse')));
 * @return void
 **/
function nav(array $links) {
	
	$current = Kea_Controller_Front::getInstance()->getRequest()->getRequestUri();
	$plugins = Kea_Controller_Plugin_Broker::getInstance();
	
	foreach( $links as $text => $link )
	{		
		$nav .= "<li".(is_current($link) ? ' class="current"':'')."><a href=\"$link\">$text</a></li>\n";
		
		//add navigation from the plugins
		$plugResponses = $plugins->addNavigation($text, $link);
		if(!empty($plugResponses)) {
			foreach( $plugResponses as $array ) { 
				list($plugText, $plugLink) = $array;
				$nav .= "<li".(is_current($link) ? ' class="current"':'')."><a href=\"$plugLink\">$plugText</a></li>\n"; 
			}
		}
		
	}
	echo $nav;
}

function is_current($link, $req = null) {
	if(!$req) {
		$req = Kea_Controller_Front::getInstance()->getRequest();
	}
	$current = $req->getRequestUri();
	$base = $req->getBaseUrl();
	if($link == $current && rtrim($current, '/') == $base) return true;
	else return (strripos($current,$link) === 0 && rtrim($link, '/') !== $base);
}

///// END NAVIGATION /////

///// PLUGIN HELPER FUNCTIONS /////

/**
 * This is the butter right here.  
 *
 * @example plugin('GeoLocation', 'map', 'arg1', 'arg2', 'arg3');
 * @return mixed
 **/
function plugin() {
	$args = func_get_args();
	$pluginName = array_shift($args);
	$method = array_shift($args);
	$plugin = Zend::Registry($pluginName);
	return call_user_func_array(array($plugin, $method), $args);
}

/**
 * similar to wp_header() from Wordpress, hooks into the plugin system within the header
 *
 * @return void
 **/
function plugin_header() {
	Kea_Controller_Plugin_Broker::getInstance()->header();
}

///// END PLUGIN HELPER FUNCTIONS /////

function recent_items($num = 10) {
	$query = new Doctrine_Query();
	$query->from('Item i')->limit($num)->orderBy('i.added desc');
	return $query->execute();
}

/**
 * Do we need this for every model or better to have something like total('items')
 *
 * @return void
 **/
function total_items() {
	return Doctrine_Manager::getInstance()->getTable('Item')->count();
}

/**
 * Retrieve the most recent tags.
 *
 * @return Doctrine_Collection
 **/
function recent_tags($num = 30) {
	return Doctrine_Manager::getInstance()->getTable('Tag')->getSome($num, false, true);
}

?>