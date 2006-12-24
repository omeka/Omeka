<?php
$db = new mysqli( 'localhost', 'root', '', 'jwa' );
$handle = fopen( 'laguage_codes.tab', 'r' );
while( !feof( $handle ) )
{
	$line = fgets( $handle, 4096 );
	$parts = preg_split( '/[\t]/', $line );
	$insert = 'INSERT INTO languages ( code, part2, part1, scope, type, name ) VALUES ("' . trim($parts[0]) . '",';
	$insert .= $parts[1] ? '"' . trim($parts[1]) . '"' : 'NULL';
	$insert .= ',';
	$insert .= $parts[2] ? '"' . trim($parts[2]) . '"' : 'NULL';
	$insert .= ',"' . trim($parts[3]) . '","' . trim($parts[4]) . '","' . trim($parts[5]) . '")';
	$db->query( $insert );
	if( $db->error )
	{
		echo $insert;
		echo $db->error;
		exit();
	}
}
fclose($handle);

?>