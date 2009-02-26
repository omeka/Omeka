<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package OmekaThemes
 * @subpackage DeprecatedHelpers
 **/

/**
 * Return either the value or, if it's empty, output the default
 * 
 * @deprecated This shouldn't be here in the themes API (it's more like a global helper function)
 * @param mixed
 * @return mixed
 **/
function not_empty_or($value, $default) {
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
	return get_tags(array('user'=>$user->id, 'record'=>$item));
}

/**
 * @deprecated
 */
function h($str, $allowedTags = "i|em|b|strong|del|span") {
	
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
function unescapeTags($matches) {
  	return str_replace( array("&gt;", "&lt;", "&quot;", "&amp;"), array(">", "<", "\"", "&"), $matches[0]);
}
