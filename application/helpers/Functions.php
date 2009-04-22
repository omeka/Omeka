<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka_ThemeHelpers
 * @subpackage GeneralHelpers
 **/

/**
 * Retrieve the view object.  Should be used only to avoid function scope
 * issues within other theme helper functions.
 * 
 * @since 0.10
 * @access private
 * @return Omeka_View
 **/
function __v()
{
    return Zend_Registry::get('view');
}

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
 * Output a <link> tag for the RSS feed so the browser can auto-discover the field.
 * 
 * @since 0.9
 * @uses items_output_uri()
 * @return string HTML
 **/
function auto_discovery_link_tag(){
	$html = '<link rel="alternate" type="application/rss+xml" title="Omeka RSS Feed" href="'. items_output_uri() .'" />';
	return $html;
}

/**
 * Displays a set of files based on the file's MIME type and any options that are
 * passed.  This is primarily used by other helper functions and will not be used
 * by theme writers in most cases.
 * 
 * @since 0.9
 * @uses Omeka_View_Helper_Media
 * @param array $files An array of File records to display.
 * @param array $props Properties to customize display for different file types.
 * @param array $wrapperAttributes XHTML attributes for the div that wraps each
 * displayed file.  If empty or null, this will not wrap the displayed file in a
 * div.
 * @return string HTML
 **/
function display_files($files, array $props = array(), $wrapperAttributes = array('class'=>'item-file')) {
    require_once 'Media.php';
    $helper = new Omeka_View_Helper_Media;
    $output = '';
    foreach ($files as $file) {
        $output .= $helper->media($file, $props, $wrapperAttributes);
    }
    return $output;
}

/**
 * @see display_files()
 * @uses Omeka_View_Helper_Media
 * @param File $file One File record.
 * @param array $props
 * @param array $wrapperAttributes Optional XHTML attributes for the div wrapper
 * for the displayed file.  Defaults to array('class'=>'item-file').
 * @return string HTML
 **/
function display_file($file, array $props=array(), $wrapperAttributes = array('class'=>'item-file'))
{
    require_once 'Media.php';
    $helper = new Omeka_View_Helper_Media;
    return $helper->media($file, $props, $wrapperAttributes);
}

//CSS Helpers



/**
 * Converts a word or phrase to dashed format, i.e. Foo Bar => foo-bar
 *
 * This is primarily for easy creation of HTML ids within Omeka
 *
 * 1) convert to lowercase
 * 2) Replace whitespace with -, 
 * 3) remove all non-alphanumerics, 
 * 4) remove leading/trailing delimiters
 * 5) optionally prepend a piece of text
 *
 * @param string The text to convert
 * @param string Another string to prepend to the ID
 * @param string The delimiter to use (- by default)
 * @return string
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
	$view = __v();
	$paths = $view->getAssetPaths();
	
	foreach ($paths as $path) {
	    list($physical, $web) = $path;
		if(file_exists($physical . DIRECTORY_SEPARATOR . $file)) {
			return $web . '/' . $file;
		}
	}
	
	throw new Exception( "Could not find file '$file'!" );
}

/**
 * Return the physical path for an asset/resource within the theme (or plugins, shared, etc.)
 *
 * @throws Exception
 * @return string
 **/
function physical_path_to($file)
{
	$view = __v();
	$paths = $view->getAssetPaths();
	
	foreach ($paths as $path) {
	    list($physical, $web) = $path;
		if(file_exists($physical . DIRECTORY_SEPARATOR . $file)) {
			return $physical . DIRECTORY_SEPARATOR . $file;
		}
	}
	throw new Exception( "Could not find file '$file'!" );
}

/**
 * Return a valid src attribute value for a given file.  Used primarily
 * by other helper functions.
 *
 *
 * @param string        Filename
 * @param string|null   Directory that the file is contained in (optional) 
 * @param string        File extension (optional)
 * @return string
 **/
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
        $output  = js('prototype', $dir); //Prototype library loads by default
        $output .= js('prototype-extensions', $dir); //A few custom extensions to the Prototype library
        
        //The following is a hack that loads only the 'effects' sub-library of Scriptaculous
        $output .= '<script src="' . web_path_to($dir . DIRECTORY_SEPARATOR . 'scriptaculous.js') . '?load=effects,dragdrop" type="text/javascript" charset="utf-8"></script>' . "\n";
        
        $output .= js('search', $dir);
        
        //Do not try to load 'default.js'
        return $output;
    }
    
	return '<script type="text/javascript" src="'.src($file, $dir, 'js').'" charset="utf-8"></script>'."\n";
}

/**
 * Like js() function, but it allows you to specify the scriptaculous libraries
 *
 * @see js() function
 * @param string $file The name of the file, without .js extension.  Specifying 'default' will load
 * the default javascript files, such as prototype/scriptaculous
 * @param string $dir The directory in which to look for javascript files.  Recommended to leave the default value.
 * @param array $scriptaculousLibraries An array of scriptaculous library names
 */
function js_scriptaculous($file, $dir = 'javascripts', $scriptaculousLibraries = array('effects', 'dragdrop')) {
    
    if($file == 'default') {
        $output  = js('prototype', $dir); //Prototype library loads by default
        $output .= js('prototype-extensions', $dir); //A few custom extensions to the Prototype library
        
        //Load the sub-libraries of Scriptaculous
        $output .= '<script src="' . web_path_to($dir . DIRECTORY_SEPARATOR . 'scriptaculous.js') . '?load=' . implode(',', $scriptaculousLibraries) .'" type="text/javascript" charset="utf-8"></script>' . "\n";
        
        $output .= js('search', $dir);
        
        //Do not try to load 'default.js'
        return $output;
    }
    
	return '<script type="text/javascript" src="'.src($file, $dir, 'js').'" charset="utf-8"></script>'."\n";
}


/**
 * Retrieve the web path to a css file.
 *
 * @param string $file Should not include the .css extension
 * @param string $dir Defaults to 'css'
 * @return string
 */
function css($file, $dir = 'css') {
	return src($file, $dir, 'css');
}

/**
 * Retrieve the web path to an image file.
 * 
 * @since 0.9
 * @param string $file Filename, including the extension.
 * @param string $dir Optional Directory within the theme to look for image 
 * files.  Defaults to 'images'.
 * @return string
 */
function img($file, $dir = 'images') {
	return src($file, $dir);
}

/**
 * Includes a file from the common/ directory, passing variables into that script.
 * 
 * @param string $file Filename
 * @param array $vars A keyed array of variables to be extracted into the script
 * @param string $dir Defaults to 'common'
 * @return void
 **/
function common($file, $vars = array(), $dir = 'common') {
	$path = physical_path_to($dir . DIRECTORY_SEPARATOR . $file . '.php');
	extract($vars);
	include $path;
}

/**
 * Include the header script into the view
 * 
 * @see common()
 * @param array Keyed array of variables
 * @param string $file Filename of header script (defaults to 'header')
 * @return void
 **/
function head($vars = array(), $file = 'header') {
	common($file, $vars);
}

/**
 * Include the footer script into the view
 * 
 * @param array Keyed array of variables
 * @param string $file Filename of footer script (defaults to 'footer')
 * @return void
 **/
function foot($vars = array(), $file = 'footer') {
	common($file, $vars);
}

/**
 * Create a tag cloud made of divs that follow the hTagcloud microformat
 *
 * @param array $tags Set of tags to display in the cloud
 * @param string|null The URI to use in the link for each tag.  If none given,
 *      tags in the cloud will not be given links.
 * @return string HTML for the tag cloud
 **/
function tag_cloud($tags, $link = null, $maxClasses = 9)
{
	if(!$tags){
		$html = '<p>No tags are available.</p>';
		return $html;
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

		$size = (int)(($tag["tagCount"] * $maxClasses) / $largest - 1);

		$class = str_repeat('v', $size) . ($size ? '-' : '') . 'popular';

		$html .= '<li class="' . $class . '">';

		if( $link )
		{
			$html .= '<a href="' . $link . '?tags=' . urlencode($tag['name']) . '">';
		}

		$html .= html_escape($tag['name']);

		if( $link )
		{
			$html .= '</a>';
		}

		$html .= '</li>' . "\n";
	}
 	$html .= '</ul></div>';

	return $html;
}

/**
 * Retrieve a flashed message from the controller
 * 
 * @param boolean $wrap Whether or not to wrap the flashed message in a div
 * with an appropriate class ('success','error','alert')
 * @return string
 **/
function flash($wrap=true)
{
	$flash = new Omeka_Controller_Flash;
	
	switch ($flash->getStatus()) {
		case Omeka_Controller_Flash::SUCCESS:
			$wrapClass = 'success';
			break;
		case Omeka_Controller_Flash::VALIDATION_ERROR:
			$wrapClass = 'error';
			break;
		case Omeka_Controller_Flash::GENERAL_ERROR:
			$wrapClass = 'error';
			break;
		case Omeka_Controller_Flash::ALERT:
			$wrapClass = 'alert';
			break;		
		default:
			return;
			break;
	}
	
	return $wrap ? 
		'<div class="' . $wrapClass . '">'.nl2br(html_escape($flash->getMsg())).'</div>' : 
		$flash->getMsg();
}

/**
 * Retrieve validation errors for specific fields on the form.
 * 
 * @param string $field The name of the field to retrieve the error message for
 * @return string The error message wrapped in a div with class="error"
 **/
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
 * Generate an unordered list of navigation links, with class "current" for any links corresponding to the current page
 *
 * For example:
 * <code>nav(array('Themes' => uri('themes/browse')));</code>
 * generates 
 * <code><li class="nav-themes"><a href="themes/browse">Themes</a></li></code>
 * 
 * @uses is_current_uri()
 * @param array A keyed array, where Key = Text of Navigation and Value = Link
 * @return string HTML for the unordered list
 **/
function nav(array $links) {
	
	$current = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
	
	$nav = '';
	foreach( $links as $text => $link )
	{		
		$nav .= '<li class="' . text_to_id($text, 'nav') . (is_current_uri($link) ? ' current':''). '"><a href="' . $link . '">' . html_escape($text) . '</a></li>' . "\n";
		
	}
	return $nav;
}

///// END NAVIGATION /////


/**
 * Allows plugins to hook in to the header of public themes.
 *
 * Uses the 'public_theme_header' hook to inject content into the theme.  This
 * hook receives the request object as the first argument, which allows the 
 * plugin writer to tailor the header output to a specific request (specific
 * controller, action, etc.).
 * 
 * @since 0.9
 * @return void
 **/
function plugin_header() {
    $request = Omeka_Context::getInstance()->getRequest();
	fire_plugin_hook('public_theme_header', $request);
}

/**
 * Allow plugins to hook in to the footer of public themes.
 * 
 * Uses the 'public_theme_footer' hook, which receives the request object as
 * the first argument.
 * 
 * @since 0.9
 * @see plugin_header()
 * @return void
 **/
function plugin_footer() {
    $request = Omeka_Context::getInstance()->getRequest();
	fire_plugin_hook('public_theme_footer', $request);
}

/**
 * Output a tag string given an Item, Exhibit, or a set of tags.
 *
 * @internal Any record that has the Taggable module can be passed to this function
 * @param Omeka_Record|array $record The record to retrieve tags from, or the actual array of tags
 * @param string|null $link The URL to use for links to the tags (if null, tags aren't linked)
 * @param string $delimiter ', ' (comma and whitespace) by default
 * @return string HTML
 **/
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
				$string[$key] = html_escape($tag["name"]);
			}else {
				$string[$key] = '<a href="'.$link.urlencode($tag["name"]).'" rel="tag">'.html_escape($tag["name"]).'</a>';
			}
		}
		$string = join($delimiter,$string);
		return $string;
	}
}

/**
 * Check the ACL to determine whether the current user has proper permissions.
 * 
 * This can be called with different syntax:
 * <code>has_permission('Items', 'showNotPublic')</code>
 * Will check if the user has permission to view Items that are not public.
 *
 * The alternate syntax checks to see whether the current user has a specific role:
 * <code>has_permission('admin')</code>
 * This latter syntax is discouraged, only because this will not cascade properly 
 * to other roles that may be given the same permissions as the admin role.  It 
 * actually circumvents the ACL entirely, so it should be avoided except in certain
 * situations where data must be displayed specifically to a certain role and no one else.
 *
 * @param string 
 * @param string|null
 * @return boolean
 **/
function has_permission($role,$privilege=null) {
	$acl = Omeka_Context::getInstance()->getAcl();
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

/**
 * Retrieve the value of a particular site setting.  This can be used to display
 * any option that would be retrieved with get_option().
 *
 * Content for any specific option can be filtered by using a filter named
 * 'display_setting_(option)' where (option) is the name of the option, e.g.
 * 'display_setting_site_title'.
 * 
 * @uses get_option()
 * @since 0.9
 * @return string
 **/
function settings($name) {
	$name = apply_filters("display_setting_$name", get_option($name));
	$name = html_escape($name);
	return $name;
}
	
/**
 * Replace new lines in a block of text with paragraph tags.
 * 
 * Looks for 2 consecutive line breaks resembling a paragraph break and wraps
 * each of the paragraphs with a <p> tag.  If no paragraphs are found, then the
 * original text will be wrapped with line breaks.
 * 
 * @link http://us.php.net/manual/en/function.nl2br.php#73479
 * @param string $str
 * @return string
 **/
function nls2p($str)
{
	
  return str_replace('<p></p>', '', '<p>'
        . preg_replace('#([\r\n]\s*?[\r\n]){2,}#', '</p>$0<p>', $str)
        . '</p>');

}
	
	/**
	 * Retrieve a substring of a given piece of text. 
	 * 
	 * Note that this will only split strings on the space character.
	 * 
	 * @param string
	 * @return void
	 **/
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
	
function items_search_form($props=array(), $formActionUri = null) {
    return __v()->partial('items/advanced-search.php', array('isPartial'=>true, 'formAttributes'=>$props, 'formActionUri'=>$formActionUri));
}	

/**
 * Escape the value to display properly as HTML.
 * 
 * This uses the 'html_escape' filter for escaping.
 * 
 * @param string
 * @return string
 */
function html_escape($value)
{
    return apply_filters('html_escape', $value);
}
