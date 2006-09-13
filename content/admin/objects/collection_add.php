<?php
$result = api('collection', 'addToCollection');

if( $result === true ) {
	echo 'Object added to the collection.';
}else{
	echo self::$_session->flash();
}
?>