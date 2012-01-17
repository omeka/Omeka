<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka_ThemeHelpers
 * @subpackage DeprecatedHelpers
 */

/**
 * @access private
 * @deprecated
 */
function h($str, $allowedTags = "i|em|b|strong|del|span")
{
    $html = htmlentities($str,ENT_QUOTES,"UTF-8");
    if ($allowedTags) {
        return preg_replace_callback('!&lt;/?('.$allowedTags.')( .*?)?&gt;!i', 'unescapeTags', $html);
    } else {
        return $html;
    }
}

/**
 * @access private
 * @deprecated
 * @param string
 * @return string
 */
function unescapeTags($matches)
{
    return str_replace( array("&gt;", "&lt;", "&quot;", "&amp;"), array(">", "<", "\"", "&"), $matches[0]);
}

/**
 * Returns the total number of types
 * @access private
 * @deprecated
 * @return integer
 */
function total_types()
{
    return get_db()->getTable('Type')->count();
}
