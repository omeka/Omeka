<?php
//This is a duplication of the items/browse page

foreach ($types as $type) {
	$type->load();
	$data = $type->getData();
	foreach ($data as $key => $val) {
		if ($val instanceof Doctrine_Null) {
			$data[$key] = null;
		}
	}
	$toEncode[] = $data;
}
echo Zend_Json::encode($toEncode);
?>