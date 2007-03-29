<?php
$tableNames = array('Collection', 'File', 'Group', 'GroupsPermissions', 'Item', 'ItemsFavorites', 'ItemsTags', 'Metafield', 'Metatext', 'Permission', 'Plugin', 'Tag', 'Type', 'TypesMetafields', 'User');

foreach ($tableNames as $key => $name) {
	require_once '../application/models/'.$name.'.php';
	$record = new $name();
}
?>
