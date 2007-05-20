<?php
$tableNames = array('Collection', 'File', 'Item', 'ItemsFavorites', 'ItemsFulltext', 'ItemsTags', 'Metafield', 'Metatext', 'Plugin', 'Tag', 'Type', 'TypesMetafields', 'User', 'UsersActivations');

foreach ($tableNames as $key => $name) {
	require_once '../application/models/'.$name.'.php';
	$record = new $name();
}
?>
