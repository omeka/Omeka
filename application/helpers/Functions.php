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
 * @package Omeka
 */
function dublin_core($type) { 
	$data = new Zend_Config_Ini(CONFIG_DIR.DIRECTORY_SEPARATOR.'dublincore.ini', array('coremetadata')); 
	echo h($data->$type); 
} 

function not_empty_or($value, $default) {
	return !empty($value) ? $value : $default;
}

/**
 * Default display for a given item type
 * Example: Still Image would display a fullsize image, Moving Image would embed the movie via object tag
 *
 * @return void
 **/

// Function to easily generate various xml outputs of items
function items_output_uri($output="rss2") {
	return uri('items/?output='.$output);
}

function auto_discovery_link_tag(){
	$html = '<link rel="alternate" type="application/rss+xml" title="Omeka RSS Feed" href="'. items_output_uri() .'" />';
	return $html;
}

function display_files($files, $props = array()) {
	
	if(is_array($files)) {
		$output = '';
		foreach ($files as $file) {
			$output .= display_files($file);
		}
		return $output;
	} else {

		$file = $files;
		
		$html = '<div class="item-file">';
		
		switch ($file->mime_browser) {
			case 'video/avi':
			case 'video/msvideo':
			case 'video/x-msvideo':
			case 'video/x-ms-wmv':
			
			$defaults = array(
						'width' => '320', 
						'height' => '240', 
						'autostart' => 0, 
						'ShowControls'=> 1, 
						'ShowDisplay'=> 1,
						'ShowStatusBar' => 1
						);

			$defaults = array_merge($defaults, $props);
			$path = WEB_FILES . DIRECTORY_SEPARATOR . $file->archive_filename;
				$html 	.= 	'<object id="MediaPlayer" width="'.$defaults['width'].'" height="'.$defaults['height'].'"';
				$html 	.= 	' classid="CLSID:22D6F312-B0F6-11D0-94AB-0080C74C7E95"';
				$html 	.=	' standby="Loading Windows Media Player components..." type="application/x-oleobject">'."\n";
				$html	.=	'<param name="FileName" value="'.$path.'">'."\n";
				$html	.=	'<param name="AutoPlay" value="'.($defaults['autostart'] ? 'true' : 'false').'">'."\n";
				$html	.=	'<param name="ShowControls" value="'.($defaults['ShowControls'] ? 'true' : 'false').'">'."\n";
				$html	.=	'<param name="ShowStatusBar" value="'.($defaults['ShowStatusBar'] ? 'true' : 'false').'">'."\n";
				$html	.=	'<param name="ShowDisplay" value="'.($defaults['ShowDisplay'] ? 'true' : 'false').'">'."\n";
				$html	.=	'<embed type="application/x-mplayer2" src="'.$path.'" name="MediaPlayer"';
				$html	.=	' width="'.$defaults['width'].'" height="'.$defaults['height'].'"'; 		
				$html	.=	' ShowControls="'.$defaults['ShowControls'].'" ShowStatusBar="'.$defaults['ShowStatusBar'].'"'; 
				$html	.=	' ShowDisplay="'.$defaults['ShowDisplay'].'" autoplay="'.$defaults['autostart'].'"></embed></object>';
				break;
		
			//MOV
			case 'video/quicktime':
		
			$defaults = array(
						'width' => '320', 
						'height' => '240', 
						'autoplay' => 0, 
						'controller'=> 1, 
						'loop'=> 0
						);

			$defaults = array_merge($defaults, $props);
			$path = WEB_FILES . DIRECTORY_SEPARATOR . $file->archive_filename;

			$html .= '<object classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B" codebase="http://www.apple.com/qtactivex/qtplugin.cab" width="'.$defaults['width'].'" height="'.$defaults['height'].'">
				<param name="src" value="'.$path.'">
				<param name="controller" value="'.($defaults['controller'] ? 'true' : 'false').'">
				<param name="autoplay" value="'.($defaults['autoplay'] ? 'true' : 'false').'">
				<param name="loop" value="'.($defaults['loop'] ? 'true' : 'false').'">

				<embed src="'.$path.'" scale="tofit" width="'.$defaults['width'].'" height="'.$defaults['height'].'" controller="'.($defaults['controller'] ? 'true' : 'false').'" autoplay="'.($defaults['autoplay'] ? 'true' : 'false').'" pluginspage="http://www.apple.com/quicktime/download/" type="video/quicktime"></embed>
				</object>';
				break;
				case 'image/jpeg':
				case 'image/gif':
				case 'image/png':
				case 'image/tiff':
				
					$html .= '<a href="'.file_download_uri($file).'" class="download-file">';
				
					if($file->hasThumbnail()) {
						ob_start();
						square_thumbnail($file, array('class'=>'thumb'));
						$html .= ob_get_clean();
					} 
					else { 
						$html .= $file->original_filename;
					}
					
					$html .= '</a>';
					
				break;
				default:
				$html .= '<a href="'. file_download_uri($file). '" class="download-file">'. $file->original_filename. '</a>';
		}
		$html .= '</div>';
		$html .= "\n";
		return $html;
		
	}
}

//CSS Helpers

/**
 * Simple math for determining whether a number is odd or even
 *
 * @return bool
 **/
function is_odd($num)
{
	return $num & 1;
}

/**
 * 1) convert to lowercase
 * 2) Replace whitespace with -, 
 * 3) remove all non-alphanumerics, 
 * 4) remove leading/trailing delimiters
 * 5) optionally prepend a piece of text
 *
 * @return void
 **/
function text_to_id($text, $prepend=null, $delimiter='-')
{
	$text = strtolower($text);
	$id = preg_replace('/\s/', $delimiter, $text);
	$id = preg_replace('/[^\w\-]/', '', $id);
	$id = trim($id, $delimiter);
	$prepend = (string) $prepend;
	return !empty($prepend) ? join($delimiter, array($prepend, $id)) : $id;
}


//End CSS Helpers

/**
 * Return the web path for an asset/resource within the theme
 *
 * @return string
 **/
function web_path_to($file)
{
	$view = Zend_Registry::get('view');
	$paths = $view->getAssetPaths();
	
	foreach ($paths as $physical_path => $web_path) {
		if(file_exists($physical_path . DIRECTORY_SEPARATOR . $file)) {
			return $web_path . DIRECTORY_SEPARATOR . $file;
		}
	}
	
	throw new Exception( "Could not find file '$file'!" );
}

/**
 * Return the physical path for an asset/resource within the theme (or plugins, shared, etc.)
 *
 * @return string
 **/
function physical_path_to($file)
{
	$view = Zend_Registry::get('view');
	$paths = $view->getAssetPaths();
	
	foreach ($paths as $physical_path => $web_path) {
		if(file_exists($physical_path . DIRECTORY_SEPARATOR . $file)) {
			return $physical_path . DIRECTORY_SEPARATOR . $file;
		}
	}
	throw new Exception( "Could not find file '$file'!" );
}

function src($file, $dir=null, $ext = null) {
	if ($ext !== null) {
		$file .= '.'.$ext;
	}
	if ($dir !== null) {
		$file = $dir.DIRECTORY_SEPARATOR.$file;
	}
	return web_path_to($file);
}

/**
 * Echos the web path (that's what's important to the browser)
 * to a javascript file.
 * $dir defaults to 'javascripts'
 * $file should not include the .js extension
 *
 * @param string $file The name of the file, without .js extension.  Specifying 'default' will load 
 * the default javascript files, such as prototype/scriptaculous
 * @param string $dir The directory in which to look for javascript files.  Recommended to leave the default value.
 */
function js($file, $dir = 'javascripts') {
    
    if($file == 'default') {
        js('prototype', $dir); //Prototype library loads by default
        js('prototype-extensions', $dir); //A few custom extensions to the Prototype library
        
        //The following is a hack that loads only the 'effects' sub-library of Scriptaculous
        ?>
        <script src="<?php echo web_path_to($dir . DIRECTORY_SEPARATOR . 'scriptaculous.js') . '?load=effects,dragdrop'; ?>" type="text/javascript" charset="utf-8"></script>
        <?php
        js('search', $dir);
        
        //Do not try to load 'default.js'
        return;
    }
    
	echo '<script type="text/javascript" src="'.src($file, $dir, 'js').'" charset="utf-8"></script>'."\n";
}

/**
 * Echos the web path to a css file
 * $dir defaults to 'css'
 * $file should not include the .css extension
 */
function css($file, $dir = 'css') {
	echo src($file, $dir, 'css');
}

/**
 * Echos the web path to an image file
 * $dir defaults to 'images'
 * $file SHOULD include an extension, many image exensions are possible
 */
function img($file, $dir = 'images') {
	echo src($file, $dir);
}

function common($file, $vars = array(), $dir = 'common') {
	$path = physical_path_to($dir . DIRECTORY_SEPARATOR . $file . '.php');
	extract($vars);
	include $path;
}

function head($vars = array(), $file = 'header') {
	common($file, $vars);
}

function foot($vars = array(), $file = 'footer') {
	common($file, $vars);
}

function tag_cloud($tags, $link = null, $maxClasses = 9, $return = false )
{
	if(!$tags){
		$html = '<div class="error">There are no tags to display</div>';
		if($return) return $html;
		else {
			echo $html;
			return;
		}
	} 
	
	//Get the largest value in the tags array
	$largest = 0;
	foreach ($tags as $tag) {
		if($tag["tagCount"] > $largest) {
			$largest = $tag["tagCount"];
		}
	}
	$html = '<div class="hTagcloud">';
	$html .= '<ul class="popularity">';
	
	if($largest < $maxClasses) {
		$maxClasses = $largest;
	}

	foreach( $tags as $tag )
	{

		$size = ($tag["tagCount"] * $maxClasses) / $largest - 1;

		$class = str_repeat('v', $size) . ($size ? '-' : '') . 'popular';

		$html .= '<li class="' . $class . '">';

		if( $link )
		{
			$html .= '<a href="' . $link . '?tags=' . urlencode($tag['name']) . '">';
		}

		$html .= $tag['name'];

		if( $link )
		{
			$html .= '</a>';
		}

		$html .= '</li>' . "\n";
	}
 	$html .= '</ul></div>';

	if($return) return $html;
	echo $html;
}

/**
 * Adapted from Zend_View_Helper_Url
 *
 * Generates an url given the name of a route.
 * 
 * @param string $urlEnd The controller/action/parameter that specifies the link.
 * @example uri('items/browse/'.$item->id); 
 * @todo Work without mod_rewrite enabled: uri('items/show/3') -> ?controller=items&action=show&id=3
 * @return string Url for the link href attribute.
 **/
function uri($urlEnd, $params=array())
{    
	$url = get_base_url();
	$url .= $urlEnd;
 
	//Convert array of params into valid query string
	if(!empty($params)) {
		$url .= '?' . http_build_query($params);
	}

    return $url;
    
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
		
		//Check if there is already a query
		//If there is no query, then append it
/*
			if(strpos($uri, '?') !== false) {
			$uri .= '?' . $query;
		}else {
			$uri .= '&' . $query;
		}
*/	
		$uri .= '?' . $query_string;
	}
	
	return $uri;
}

function flash($wrap=true)
{
	$flash = new Omeka_Controller_Flash;
	
	switch ($flash->getStatus()) {
		case Omeka_Controller_Flash::SUCCESS:
			$wrap_class = 'success';
			break;
		case Omeka_Controller_Flash::VALIDATION_ERROR:
			$wrap_class = 'error';
			break;
		case Omeka_Controller_Flash::GENERAL_ERROR:
			$wrap_class = 'error';
			break;
		case Omeka_Controller_Flash::ALERT:
			$wrap_class = 'alert';
			break;		
		default:
			return;
			break;
	}
	
	return $wrap ? 
		'<div class="' . $wrap_class . '">'.nl2br(h($flash->getMsg())).'</div>' : 
		$flash->getMsg();
}

function form_error($field)
{
	$flash = new Omeka_Controller_Flash;
	
	if($flash->getStatus() != Omeka_Controller_Flash::VALIDATION_ERROR) return;
	
	if(($msg = $flash->getError($field))) {
		return '<div class="error">'.$msg.'</div>';
	}
}

///// NAVIGATION /////

/**
 * Generate navigation list items, with class "current" for the chosen item
 *
 * @param array Key = Text of Navigation, Value = Link
 * @example primary_nav(array('Themes' => uri('themes/browse')));
 **/
function nav(array $links) {
	
	$current = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
	
	$nav = '';
	foreach( $links as $text => $link )
	{		
		//$nav .= "<li".(is_current($link) ? ' class="current"':'')."><a href=\"$link\">".h($text)."</a></li>\n";
		$nav .= '<li class="' . text_to_id($text, 'nav') . (is_current($link) ? ' current':''). '"><a href="' . $link . '">' . h($text) . '</a></li>' . "\n";
		
	}
	echo $nav;
}

/**
 * This works different from the above function in that it may/may not append navigation
 * via the plugins, but also different in the way it handles CSS.  Instead of class="current"
 * because of all the whacked-out navigation on the admin theme, we give each link an class of 'nav-'
 * + the link text.
 *
 **/
function admin_nav(array $links) {
	$current = $_SERVER['REQUEST_URI'];
	
	$nav = '';
	foreach ($links as $text => $link) {
		$nav .= '<li class="' . text_to_id($text, 'nav') . '"><a href="' . $link . '">' . h($text) . '</a></li>' . "\n";
	}
	echo $nav;	
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

///// END NAVIGATION /////

///// PLUGIN HELPER FUNCTIONS /////

/**
 * similar to wp_header() from Wordpress, hooks into the plugin system within the header
 *
 * @return void
 **/
function plugin_header() {
	fire_plugin_hook('theme_header');
}

function plugin_footer() {
	fire_plugin_hook('theme_footer');
}

///// END PLUGIN HELPER FUNCTIONS /////

function tag_string($record, $link=null, $delimiter=', ')
{
	$string = array();
	if($record instanceof Omeka_Record) {
		$tags = $record->Tags;
		
	}else {
		$tags = $record;
	}

	if(!empty($tags)) {
		foreach ($tags as $key=>$tag) {
			if(!$link) {
				$string[$key] = h($tag["name"]);
			}else {
				$string[$key] = '<a href="'.$link.urlencode($tag["name"]).'">'.h($tag["name"]).'</a>';
			}
		}
		$string = join($delimiter,$string);
		return $string;
	}
}

function current_user_tags($item)
{
	$user = current_user();
	if(!$item->exists()) {
		return false;
	}
	return tags(array('user'=>$user->id, 'record'=>$item));
}

function link_to($record, $action='show', $text, $props = array())
{
	$path = $record->getPluralized() . DIRECTORY_SEPARATOR . $action . DIRECTORY_SEPARATOR . $record->id;

	$attr = !empty($props) ? ' ' . _tag_attributes($props) : '';
	echo '<a href="'. uri($path) . '"' . $attr . '  title="View '.$text.'">' . h($text) . '</a>';
}

function link_to_item($item, $action='show', $text=null, $props=array())
{
	$text = (!empty($text) ? $text : (!empty($item->title) ? $item->title : '[Untitled]'));
	
	return link_to($item, $action, $text, $props);
}

function link_to_items_rss($params=array())
{	
	echo '<a href="' . items_rss_uri($params) . '" class="rss">RSS</a>';
}

function items_rss_uri($params=array())
{
	$params['output'] = 'rss2';
	
	//In case $_GET is passed from a search of items, don't include the submit form button
	unset($params['submit_search']);
	
	$uri = uri('items/browse', $params);	
	
	return $uri;
}

function items_rss_header()
{
	if($_GET and is_current(uri('items/browse'))) {
		$uri = items_rss_uri($_GET);
	}else {
		$uri = items_rss_uri();
	}
	
	echo '<link rel="alternate" type="application/rss+xml" title="'.get_option('site_title').'" href="'. $uri .'" />';
}

function link_to_next_item($item, $text="Next Item -->", $props=array())
{
	if($next = $item->next()) {
		return link_to($next, 'show', $text, $props);
	}
}

function link_to_previous_item($item, $text="<-- Previous Item", $props=array())
{
	if($previous = $item->previous()) {
		return link_to($previous, 'show', $text, $props);
	}
}

function link_to_collection($collection, $action='show', $text=null, $props=array())
{
	$text = (!empty($text) ? $text : (!empty($collection->name) ? $collection->name : '[Untitled]'));
	
	return link_to($collection, $action, $text, $props);
}

function link_to_thumbnail($item, $props=array(), $action='show', $random=false)
{
	if(!$item or !$item->exists()) return false;
	
	$path = 'items/'.$action.'/' . $item->id;
	echo '<a href="'. uri($path) . '" ' . _tag_attributes($props) . '>';
	
	if($random) {
		thumbnail($item);
	}else {
		thumbnail($item->Files[0]);
	}
	echo '</a>';
}

/**
 * Note to self: exact duplication of link_to_thumbnail()
 *
 * @return void|false
 **/
function link_to_fullsize($item, $props=array(), $action='show', $random=false)
{
	if(!$item or !$item->exists()) return false;
	
	$path = 'items/'.$action.'/' . $item->id;
	echo '<a href="'. uri($path) . '" ' . _tag_attributes($props) . '>';
	
	if($random) {
		fullsize($item);
	}else {
		fullsize($item->Files[0]);
	}
	echo '</a>';	
}

function link_to_home_page($text, $props = array())
{
	$uri = WEB_ROOT;
	echo '<a href="'.$uri.'" '._tag_attributes($props).'>'.h($text)."</a>\n";
}

function link_to_admin_home_page($text, $props = array())
{
	echo '<a href="'.admin_uri().'" '._tag_attributes($props).'>'.h($text)."</a>\n";
}

function admin_uri()
{
	return WEB_ROOT . DIRECTORY_SEPARATOR. 'admin' . DIRECTORY_SEPARATOR;
}

/**
 * Retrieve the total number of items
 *
 * @since 11/7/07 This function can now be passed a $collection obj to return the total # of items in that collection
 * @return int
 **/
function total_items($return = false) {
	if($return instanceof Collection)
	{
		return $return->totalItems();
	}
	
	return get_db()->getTable('Item')->count();
}

function total_collections() {
	return get_db()->getTable('Collection')->count();
}

function total_tags() {
	return get_db()->getTable('Tag')->count();
}

function total_users() {
	return get_db()->getTable('User')->count();
}

function total_types() {
	return get_db()->getTable('Type')->count();
}

function total_results($return = false) {
	if(Zend_Registry::isRegistered('total_results')) {
		$count = Zend_Registry::get('total_results');

		
		if($return) return $count;
		echo $count;
	}
}

function has_type($item, $name=null) {
	$exists = $item->Type and $item->Type->exists();
	$hasName = (!empty($name) ? $item->Type->name == $name : true);
	return ( $exists and $hasName );
}

function has_collection($item, $name=null) {
	return !empty($item->collection_id);
}

function has_collectors($collection) {
	return $collection->hasCollectors();
}

function has_tags($item, array $tags=array()) {
	$hasSome = (count($item->Tags) > 0);
	if(empty($tags) or !$hasSome){
		return $hasSome;
	}
	foreach ($tags as $key => $tag) {
		if(!$item->hasTag($tag)) {
			return false;
		}
	}
	return true;
}

function has_files($item) {
	return $item->hasFiles();
}

/**
 * Retrieve the most recent tags.
 *
 * @return array
 **/
function recent_tags($num = 30) {
	return tags(array('recent'=>true,'limit'=>$num));
}

function recent_collections($num = 10) {
	return collections(array('recent'=>true,'limit'=>$num));
}

function recent_items($num = 10) {
	return items(array('recent'=>true,'per_page'=>$num));
}

function random_featured_item($hasImage=true) {
	return get_db()->getTable('Item')->findRandomFeatured($hasImage);
}

function entities(array $params = array())
{
	return _get_recordset($params, 'entities');
}

function people(array $params = array())
{
	$params = array_merge($params, array('type'=>'person'));
	return _get_recordset($params, 'entities');
}

function institutions(array $params = array())
{
	$params = array_merge($params, array('type'=>'institution', 'hierarchy'=>false));
	return _get_recordset($params, 'entities');
}

function tags(array $params = array()) 
{
	return _get_recordset($params, 'tags');
}

function items(array $params = array())
{
	return _get_recordset($params, 'items');
}

function item($id=null) 
{
	if(!$id && Zend_Registry::isRegistered('item')) {
		$item = Zend_Registry::get('item');

		return $item;
	}
	
	$item = get_db()->getTable('Item')->find($id);
	
	return $item;
}

function item_permalink_url($item)
{
	return WEB_DIR . DIRECTORY_SEPARATOR . 'items/show/' . $item->id;
}

function collection($id=null)
{
	if(!$id && Zend_Registry::isRegistered('collection')) {
		$c = Zend_Registry::get('collection');

		return $c;
	}
	
	$c = get_db()->getTable('Collection')->find($id);
	return $c;
}

function collections(array $params = array())
{
	return _get_recordset($params, 'collections');
}

function metafields(array $params = array())
{
	//To add filters to this function, put them in the TypesController::metafieldsAction() method
	return _make_omeka_request('Types','metafields',$params,null);
}

function type($id=null)
{
	if(!$id && Zend_Registry::isRegistered('type')) {
		$t = Zend_Registry::get('type');

		return $t;
	}
	
	$t = get_db()->getTable('Type')->find($id);
	
	return $t;
}

function types(array $params = array())
{
	return _get_recordset($params, 'types');
}

function users(array $params = array())
{
	return _get_recordset($params, 'users');
	
}

/**
 * @example _get_recordset(array(), 'users') => $users
 * @return mixed
 **/
function _get_recordset($params, $for) {
	if (empty($params) && Zend_Registry::isRegistered($for)) {
		$records = Zend_Registry::get($for);
		return $records;
	}	
	return _make_omeka_request(ucwords($for),'browse',$params, $for);
}

function get_user_roles(array $params = array())
{
	return _make_omeka_request('Users','roles',$params,'roles');
}

function has_thumbnail($item) {
	return $item->hasThumbnail();
}

function has_permission($role,$privilege=null) {
	$acl = Zend_Registry::get('acl');
	$user = current_user();
	if(!$user) return false;
	
	$userRole = $user->role;
	
	if(!$privilege) {
		return ($userRole == $role);
	}

	//This is checking for the correct combo of 'role','resource' and 'privilege'
	$resource = $role;
	return $acl->isAllowed($userRole,ucwords($resource),$privilege);
}

function _make_omeka_request($controller,$action,$params, $returnVars)
{
	$front = Zend_Controller_Front::getInstance();
	
	$className = ucwords($controller.'Controller');
	
    //Include the controller
	$file = CONTROLLER_DIR.DIRECTORY_SEPARATOR.$className.".php";
	if(file_exists($file)) {
		require_once $file;
	}
	
	//Merge together the existing parameters with the old ones
	$oldReq = $front->getRequest();
	if($oldReq) {
		$params = array_merge($oldReq->getParams(), $params);
	}

	//Create the request
	$newReq = new Zend_Controller_Request_Http();
	$newReq->setParams($params);
	$newReq->setControllerName(strtolower($controller));
	
	//Create the response
	$resp = new Zend_Controller_Response_Cli();
	
	//Fire the controller
	$controller = new $className($newReq,$resp, array('return'=>$returnVars));
	$action = $action.'Action';
	
	try {
		$retVal = $controller->$action();
	} catch (Exception $e) {
		echo $e->getMessage();
	}

	return $retVal;
}

/**
 * Retrieve the value of a particular site setting
 *
 * @return string
 **/
function settings($name, $return=false) {
	$name = get_option($name);
	$name = h($name);
	if($return) return $name;
	echo $name;
}

//Format of $date is YYYY-MM-DD
function get_month($date)
{
	$parts = explode('-',$date);
	if($parts[1] === '00') return null;
	return $parts[1];
}

function get_day($date)
{
	$parts = explode('-',$date);
	if($parts[2] === '00') return null;
	return $parts[2];
}

function get_year($date)
{
	$parts = explode('-',$date);
	if($parts[0] === '0000') return null;
	return $parts[0];
}

function item_metadata($item, $field, $escape=true)
{
	$text = $item->getMetatext($field);
	
	return $escape ? h($text) : $text;
}

/**
 * Display an alternative value if the given variable is empty
 *
 * @return void
 **/
function display_empty($val, $alternative="[Empty]") {
	echo nls2p(h(!empty($val) ? $val : $alternative));
}

/**
 * @see FilesController
 * @see routes.ini (display/download routes)
 *
 * @return string
 **/
function file_download_uri($file, $format='fullsize')
{
	if(!$file->exists()) return false;
	$options = array('controller'=>'files', 'action'=>'get', 'id'=>$file->id, 'format'=>$format);
	$uri = generate_url($options, 'download');
	
	return $uri;
}

function file_display_uri($file, $format='fullsize')
{
	if(!$file->exists()) return false;
	$options = array('controller'=>'files', 'action'=>'get', 'id'=>$file->id, 'format'=>$format);
	return generate_url($options, 'display');
}

function thumbnail($record, $props=array(), $width=null, $height=null,$return=false) 
{
       return archive_image($record, $props, $width, $height, 'thumbnail', $return);
}

function fullsize($record, $props=array(), $width=null, $height=null,$return=false)
{
       return archive_image($record, $props, $width, $height, 'fullsize', $return);
}

function square_thumbnail($record, $props=array(), $width=null, $height=null,$return=false)
{
       return archive_image($record, $props, $width, $height, 'square_thumbnail', $return);
}

function archive_image( $record, $props, $width, $height, $format, $return) 
{
	if(!$record) {
		return false;
	}
		
       if($record instanceof File) {
               $filename = $record->getDerivativeFilename();
			   $file = $record;
       }elseif($record instanceof Item) {
               $file = get_db()->getTable('File')->getRandomFileWithImage($record->id);
               if(!$file) return false;
               $filename = $file->getDerivativeFilename();
       }

		$path = $file->getPath($format);
		$uri = file_display_uri($file, $format);
		
	   if(!file_exists($path)) {
			return false;
	   }

       list($o_width, $o_height) = getimagesize( $path );
       if(!$width && !$height) 
       {
			$width = $o_width;
			$height = $o_height;
       }
       elseif( $o_width > $width && !$height )
       {
               $ratio = $width / $o_width;
               $height = $o_height * $ratio;
       }
       elseif( !$width && $o_height > $height)
       {
               $ratio = $height / $o_height;
               $width = $o_width * $ratio;
       }
	   $props['width'] = $width;
	   $props['height'] = $height;
	
	   if(!isset($props['alt'])) {
			$props['alt'] = $file->title;
		}
	
	   $html = '<img src="' . $uri . '" '._tag_attributes($props) . '/>' . "\n";
	   if($return) return $html;
	   echo $html;
}
/**
 *	The pagination function from the old version of the software
 *  It looks more complicated than it might need to be, but its also more flexible.  We may decide to simplify it later
 */
function pagination_links( $num_links = 5, $menu = null, $page = null, $per_page = null, $total_results=null, $link=null, $page_query = null )
{
	
	//If no args passed, retrieve the stored 'pagination' value
	if(Zend_Registry::isRegistered('pagination')) {
		$p = Zend_Registry::get('pagination');
	}
	
	if(empty($per_page)) {
		$per_page = $p['per_page'];
	} 
	if(empty($num_links)) {
		$num_links = $p['num_links'];
	}
	if(empty($total_results)) {
		$total_results = $p['total_results'];
	}
	if(empty($page)) {
		$page = $p['page'];
	}
	if(empty($link)) {
		$link = $p['link'];
	}

	//Avoid division by zero error
	if(!$per_page) return;

		$num_pages = ceil( $total_results / $per_page );
		$num_links = ($num_links > $num_pages) ? $num_pages : $num_links;
				
		$query = !empty( $_SERVER['QUERY_STRING'] ) ? '?' . $_SERVER['QUERY_STRING'] : null;
		
		if ( $page_query )
		{
			//Using the power of regexp we replace only part of the query string related to the pagination
			if( preg_match( '/[\?&]'.$page_query.'/', $query ) ) 
			{
				$p = '/([\?&])('.preg_quote($page_query) . ')=([0-9]*)/';
				$pattern = preg_replace( $p, '$1$2='.preg_quote('%PAGE%'), $query );
			}
			else $pattern = ( !empty($query) )  ? $query . '&' . $page_query . '=' . '%PAGE%' : '?' . $page_query . '=' . '%PAGE%' ; 
	
		}
		else
		{
			$pattern = '%PAGE%' . $query;
		}

		//We don't have enough for pagination
		if($total_results < $per_page) {
			$html = '';
		} else {
			
		if( $page > 1 ) {
			$html = '<ul><li class="first"><a href="' . $link . str_replace('%PAGE%', 1, $pattern) . '">First</a></li><li class="previous"><a href="' . $link . str_replace('%PAGE%', ($page - 1), $pattern) . '">Previous</a></li>';
		} elseif( $page == 1) {
			$html = '<ul>';
		}

		$buffer = floor( ( $num_links - 1 ) / 2 );
		$start_link = ( ($page - $buffer) > 0 ) ? ($page - $buffer) : 1;
		$end_link = ( ($page + $buffer) < $num_pages ) ? ($page + $buffer) : $num_pages;

		if( $start_link == 1 ) {
			$end_link += ( $num_links - $end_link );
		}elseif( $end_link == $num_pages ) {
			$start_link -= ( $num_links - ($end_link - $start_link ) - 1 );
		}

		for( $i = $start_link; $i < $end_link+1; $i++) {
			if( $i <= $num_pages ) {
				if( $page == $i ) {
					$html .= '<li class="current">' . $i . '</li>';
				} else {
					$html .= '<li><a href="' . $link . str_replace('%PAGE%', $i, $pattern) . '">' . ($i) . '</a></li>';
				}
			}
		}

		if( $page < $num_pages ) {
			$html .= '<li class="next"><a href="' . $link . str_replace('%PAGE%', ($page + 1), $pattern). '">Next</a></li><li class="last"><a href="' . $link . str_replace('%PAGE%', ($num_pages), $pattern) . '">Last</a></li>';
		}

		$html .= '</ul>';
			
		if ($menu) {
			$html .= '<select class="pagination-link" onchange="location.href = \''.$link . $page . '?per_page=' . ('\' + this.value + \'') .'\'">';
			$html .= '<option>Results Per Page:&nbsp;</option>';
			$per_page_limits = array(10, 25, 50);
			foreach ($per_page_limits as $per_page_limit) {
				$html .= '<option value="' . $per_page_limit . '"';
				$html .= '>' . $per_page_limit . ' results' . '</option>';
			}
			$html .= '</select>';
		}
		}
		return $html;		
	}
	
	/**
	 *
	 * @see Zend/View/Helper/Url.php
	 *
	 * @return void
	 **/
	function generate_url($options, $name)
	{
		$ctrl = Zend_Controller_Front::getInstance();
        $router = $ctrl->getRouter();
        
        if (empty($name)) {
            $route = $router->getCurrentRoute();
        } else {
            $route = $router->getRoute($name);
        }
        
        $url = get_base_url();
        $url .= $route->assemble($options);
         
        return $url;
	}
	
	function get_base_url($use_relative_uri=false)
	{
		$base = ($use_relative_uri) ? Zend_Controller_Front::getInstance()->getRequest()->getBaseUrl() : WEB_DIR;
		return rtrim($base , '/') . '/';
	}
	
	//Adapted from PHP.net: http://us.php.net/manual/en/function.nl2br.php#73479
	function nls2p($str)
	{
		
	  return str_replace('<p></p>', '', '<p>'
	        . preg_replace('#([\r\n]\s*?[\r\n]){2,}#', '</p>$0<p>', $str)
	        . '</p>');
	
	}
	
	function snippet($text, $start_pos, $end_pos, $append = '…')
	{
	$start_pos = ( !$start_pos ) ? 0 : strrpos( $text, ' ', $start_pos - strlen($text) ); 
	$end_pos = strrpos( $text, ' ', ( $end_pos ) - strlen($text) );
	if(!$end_pos) $end_pos = strlen($text);
	$snippet = substr($text, $start_pos, $end_pos - $start_pos );
		if (strlen($snippet)) {
			return  $snippet . $append; 
		}
	}

?>