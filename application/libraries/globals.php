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
?>
