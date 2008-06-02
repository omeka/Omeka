<?php
function h($str, $allowedTags = "i|em|b|strong|del|span") {
	
	$html = htmlentities($str,ENT_QUOTES,"UTF-8"); 
		
	if($allowedTags)
		return preg_replace_callback('!&lt;/?('.$allowedTags.')( .*?)?&gt;!i', 'unescapeTags', $html);
	else
		return $html;
}

/**
 * @access private
 * 
 * @param string
 * @return string
 **/
function unescapeTags($matches) {
  	return str_replace( array("&gt;", "&lt;", "&quot;", "&amp;"), array(">", "<", "\"", "&"), $matches[0]);
}	
?>