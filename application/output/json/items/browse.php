<?php
foreach ($items as $item) {
	$item->load();
	$data = $item->getData();
	foreach ($data as $key => $val) {
		if ($val instanceof Doctrine_Null) {
			$data[$key] = null;
		}
	}
	$toEncode[] = $data;
}
echo Zend_Json::encode($toEncode);
?>