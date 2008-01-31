<?php
function h($str, $allowedTags = "a|i|em|b|strong|del|span|cite|blockquote") {
	
	$html = htmlentities($str,ENT_QUOTES,"UTF-8"); 
		
	if($allowedTags)
		return preg_replace_callback('!&lt;/?('.$allowedTags.')( .*?)?&gt;!i', 'unescapeTags', $html);
	else
		return $html;
}

function unescapeTags($matches) {
  	return str_replace( array("&gt;", "&lt;", "&quot;", "&amp;"), array(">", "<", "\"", "&"), $matches[0]);
}	
?>