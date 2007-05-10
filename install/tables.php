<?php
$tableNames = array('Collection', 'File', 'Item', 'ItemsFavorites', 'ItemsTags', 'Metafield', 'Metatext', 'Plugin', 'Tag', 'Type', 'TypesMetafields', 'User');

foreach ($tableNames as $key => $name) {
	require_once '../application/models/'.$name.'.php';
	$record = new $name();
}
?>
