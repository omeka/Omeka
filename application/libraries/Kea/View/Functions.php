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
	$physical = theme_path(true).DIRECTORY_SEPARATOR.$dir.DIRECTORY_SEPARATOR.$file;
	if ($ext !== null) {
		$physical .= '.'.$ext;
	}
	if (file_exists($physical)) {
		$path = web_path(true).DIRECTORY_SEPARATOR.$dir.DIRECTORY_SEPARATOR.$file.'.'.$ext;
		if($return) return $path;
		else echo $path;
	}
	else {
		//Check the 'universal' directory to see if it is in there
		$physical = SHARED_DIR.DIRECTORY_SEPARATOR.$dir.DIRECTORY_SEPARATOR.$file.($ext ? '.':'').$ext;
		if(file_exists($physical)) {
			$path = WEB_SHARED.DIRECTORY_SEPARATOR.$dir.DIRECTORY_SEPARATOR.$file.($ext ? '.':'').$ext;
			if($return) return $path;
			else echo $path;
		}
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
	$path = theme_path(true).DIRECTORY_SEPARATOR.$dir.DIRECTORY_SEPARATOR.$file.'.php';
	if (file_exists($path)) {
		extract($vars);
		include $path;
	}else {
		$path = SHARED_DIR.DIRECTORY_SEPARATOR.$dir.DIRECTORY_SEPARATOR.$file.'.php';
		if(file_exists($path)) {
			extract($vars);
			include $path;
		}
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
		$size = round( ( ( $tag["tagCount"] / $largest ) * $max ), 3 );
		
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
	
	$msg = '<div class="alert">'.$flash->msg.'</div>';
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
	
	$nav = '';
	foreach( $links as $text => $link )
	{		
		$nav .= "<li".(is_current($link) ? ' class="current"':'')."><a href=\"$link\">$text</a></li>\n";
		
		//add navigation from the plugins
		$plugResponses = $plugins->addNavigation($text, $link);
		if(!empty($plugResponses)) {
			foreach( $plugResponses as $array ) { 
				list($plugText, $plugLink) = $array;
				$nav .= "<li".(is_current($plugLink) ? ' class="current"':'')."><a href=\"$plugLink\">$plugText</a></li>\n"; 
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



/**
 * Retrieve the total number of items
 *
 * @return int
 **/
function total_items($return = false) {
	return _get_model_total('ItemsController',$return);
}

function total_collections($return = false) {
	return _get_model_total('CollectionsController',$return);
}

function total_tags($return = false) {
	return _get_model_total('TagsController',$return);
}

function total_users($return = false) {
	return _get_model_total('UsersController',$return);
}

function total_types($return = false) {
	return _get_model_total('TypesController',$return);
}

function _get_model_total($controller,$return) {
	$count = _make_omeka_request($controller,'browse',array(),'total');
	if($return) return $count;
	echo $count;
}

/**
 * Retrieve the most recent tags.
 *
 * @return Doctrine_Collection
 **/
function recent_tags($num = 30) {
	return get_tags(array('recent'=>true,'limit'=>$num));
}

function recent_items($num = 10) {
	return get_items(array('recent'=>true,'limit'=>$num));
}

function get_tags(array $params = array()) 
{
	return _make_omeka_request('TagsController','browse',$params,'tags');
}

function get_items(array $params = array())
{
	return _make_omeka_request('ItemsController','browse',$params,'items');
}

function get_collections(array $params = array())
{
	return _make_omeka_request('CollectionsController','browse',$params,'collections');
}

function get_metafields(array $params = array())
{
	//To add filters to this function, put them in the TypesController::metafieldsAction() method
	return _make_omeka_request('TypesController','metafields',$params,null);
}

function get_users(array $params = array())
{
	return _make_omeka_request('UsersController','browse',$params,'users');
}

function get_user_roles(array $params = array())
{
	return _make_omeka_request('UsersController','roles',$params,'roles');
}

function current_user()
{
	return Kea::loggedIn();
}

function _make_omeka_request($controller,$action,$params, $returnVars)
{
	$front = Kea_Controller_Front::getInstance();
	$dirs = $front->getControllerDirectory();
	
	//Include the controller
	foreach ($dirs as $dir) {
		$file = $dir.DIRECTORY_SEPARATOR.$controller.".php";
		if(file_exists($file)) {
			require_once $file;
		}
	}
	
	//Merge together the existing parameters with the old ones
	$oldReq = $front->getRequest();
	$params = array_merge($oldReq->getParams(), $params);

	//Create the request
	$newReq = new Zend_Controller_Request_Http();
	$newReq->setParams($params);
	$newReq->setParam('return', $returnVars);
	
	//Create the response
	$resp = new Zend_Controller_Response_Cli();
	
	//Fire the controller
	$controller = new $controller($newReq,$resp);
	$action = $action.'Action';
	$retVal = $controller->$action();
	
	return $retVal;
}

/**
 * Retrieve the value of a particular site setting
 *
 * @return string
 **/
function settings($name, $return=false) {
	$name = get_option($name);
	if($name instanceof Doctrine_Collection_Batch) return;
	if($return) return $name;
	echo $name;
}

/**
 * Display an alternative value if the given variable is empty
 *
 * @return void
 **/
function display_empty($val, $alternative="[Empty]") {
	echo (!empty($val) ? $val : $alternative);
}

function thumbnail($record, $props=array(), $width=null, $height=null) 
{
       return archive_image($record, 'thumbnail_filename', $props, $width, $height, THUMBNAIL_DIR, WEB_THUMBNAILS);
}

function fullsize($record, $props=array(), $width=null, $height=null)
{
       return archive_image($record, 'fullsize_filename', $props, $width, $height, FULLSIZE_DIR, WEB_FULLSIZE);
}

function archive_image( $record, $field , $props, $width, $height, $abs, $web) 
{
       if($record instanceof File) {
               $file = $record->$field;
       }elseif($record instanceof Item) {
               $file = $record->getRandomFileWithImage();
               if(!$file) return false;
               $file = $file->$field;
       }

       $path =  $web . DIRECTORY_SEPARATOR . $file;
       $abs_path =  $abs . DIRECTORY_SEPARATOR . $file;
       if( file_exists( $abs_path ) ) {
               $html = '<img src="' . $path . '" ';
               foreach( $props as $k => $v ) {
                       $html .= $k . '="' . $v . '" ';
               }
               list($o_width, $o_height) = getimagesize( $abs_path );
               if(!$width && !$height) 
               {
                       $html .= 'width="' . $o_width . '" height="' . $o_height . '"';
               }
               if( $o_width > $width && !$height )
               {
                       $ratio = $width / $o_width;
                       $height = $o_height * $ratio;
                       $html .= 'width="' . $width . '" height="' . $height . '"';
               }
               elseif( !$width && $o_height > $height)
               {
                       $ratio = $height / $o_height;
                       $width = $o_width * $ratio;
                       $html .= 'width="' . $width . '" height="' . $height . '"';
               }
               elseif ( $width && $height )
               {
                       $html .= 'width="' . $width . '" height="' . $height . '"';
               }
               $html .= '/>' . "\n";
               echo $html;
       } else {
               echo '<img src="' . $path . '" alt="Image missing." />' . "\n";
       }
}
?>