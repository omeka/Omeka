<?php
$result = api('collection', 'addToCollection');

if( $result === true ) {
	echo 'Item added to the collection.';
}else{
	echo self::$_session->flash();
}
?>