<?php
//This is a duplication of the items/browse page

foreach ($tags as $tag) {
	$tag->load();
	$data = $tag->getData();
	foreach ($data as $key => $val) {
		if ($val instanceof Doctrine_Null) {
			$data[$key] = null;
		}
	}
	$data['tagCount'] = $tag->tagCount;
	$toEncode[] = $data;
}
echo Zend_Json::encode($toEncode);
?>