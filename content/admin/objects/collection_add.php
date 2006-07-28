<?php
$result = api('collection', 'addToCollection');

if( $result === true ) {
	echo 'Object added to the collection.';
}elseif( $result instanceof Kea_Exception ){
	echo $result->getMessage();
}
?>