<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka_ThemeHelpers
 * @subpackage DeprecatedHelpers
 **/

/**
 * Return either the value or, if it's empty, output the default
 * 
 * @deprecated This shouldn't be here in the themes API (it's more like a global helper function)
 * @param mixed
 * @return mixed
 **/
function not_empty_or($value, $default) 
{
	return !empty($value) ? $value : $default;
}

/**
 * @deprecated
 */
function current_user_tags($item)
{
	$user = current_user();
	if(!$item->exists()) {
		return false;
	}
	return get_tags(array('user'=>$user->id, 'record'=>$item, 'sort'=>array('alpha')));
}

/**
 * @deprecated
 */
function h($str, $allowedTags = "i|em|b|strong|del|span") 
{
	
	$html = htmlentities($str,ENT_QUOTES,"UTF-8"); 
		
	if($allowedTags)
		return preg_replace_callback('!&lt;/?('.$allowedTags.')( .*?)?&gt;!i', 'unescapeTags', $html);
	else
		return $html;
}

/**
 * @access private
 * @deprecated
 * @param string
 * @return string
 **/
function unescapeTags($matches) 
{
  	return str_replace( array("&gt;", "&lt;", "&quot;", "&amp;"), array(">", "<", "\"", "&"), $matches[0]);
}

/**
 * @deprecated
 **/
function thumbnail($record, $props=array(), $width=null, $height=null) 
{
    return archive_image($record, $props, $width, $height, 'thumbnail');
}

/**
 * @deprecated
 **/
function fullsize($record, $props=array(), $width=null, $height=null)
{
    return archive_image($record, $props, $width, $height, 'fullsize');
}

/**
 * @deprecated
 **/
function square_thumbnail($record, $props=array(), $width=null, $height=null)
{
    return archive_image($record, $props, $width, $height, 'square_thumbnail');
}

/**
 * @deprecated Used internally by other theme helpers.  Implementation may change
 * in future versions, do not rely on this within themes.
 * @return string|false
 **/
function archive_image( $record, $props, $width, $height, $format) 
{
	if(!$record) {
		return false;
	}
		
    if($record instanceof File) {
        $filename = $record->getDerivativeFilename();
		$file = $record;
    } elseif($record instanceof Item) {
        $file = get_db()->getTable('File')->getRandomFileWithImage($record->id);
        if(!$file) return false;
        $filename = $file->getDerivativeFilename();
    }

	$path = $file->getPath($format);
	$uri = file_display_uri($file, $format);
	
    if(!file_exists($path)) {
		return false;
    }

    list($oWidth, $oHeight) = getimagesize( $path );
    if(!$width && !$height) {
		$width = $oWidth;
		$height = $oHeight;
    } elseif( $oWidth > $width && !$height ) {
        $ratio = $width / $oWidth;
        $height = $oHeight * $ratio;
    } elseif( !$width && $oHeight > $height) {
        $ratio = $height / $oHeight;
        $width = $oWidth * $ratio;
    }
    $props['width'] = $width;
    $props['height'] = $height;

    $html = '<img src="' . $uri . '" '._tag_attributes($props) . '/>' . "\n";
    return $html;
}

