<?php
$points = array();
foreach( $items as $key => $item )
{
	$item->load();
	$points[$key]['latitude'] = $item->Metatext('Map Latitude');
	$points[$key]['longitude'] = $item->Metatext('Map Longitude');
	$points[$key]['item'] = $item->toArray();
}
echo Zend_Json::encode($points);
?>