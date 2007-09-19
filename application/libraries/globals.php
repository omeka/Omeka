<?php 
//Useful global library functions
function pluck($col, $array)
{
	$res = array();
	foreach ($array as $k => $row) {
		$res[$k] = $row[$col];
	}
	return $res;	
} 

function strip_slashes($text)
{
	if($text !== null) {
		$text = get_magic_quotes_gpc() ? stripslashes( $text ) : $text;
	}
	return $text;
}
?>
