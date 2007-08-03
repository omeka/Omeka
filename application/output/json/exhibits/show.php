<?php 
	require_once 'Zend/Json.php';
		
	$array = $exhibit->toArray();
	
	foreach ($exhibit->Sections as $key => $section) {
		$array['Sections'][$key] = $section->toArray();
	}
	
	if($msg = flash(false)) {
		$array['Flash'] = $msg;
	}
	
	echo Zend_Json::encode($array);
?>
