<?php
$tableNames = array('Collection', 'Exhibit', 'ExhibitsTags', 'File', 'Item', 'ItemsFavorites', 'ItemsPages', 'ItemsTags', 'Metafield', 'Metatext', 'Plugin', 'Route', 'Section', 'SectionPage', 'Tag', 'Type', 'TypesMetafields', 'User', 'UsersActivations');

foreach ($tableNames as $key => $name) {
	require_once '../application/models/'.$name.'.php';
	$record = new $name();
}
?>
