<?php 
$return = $section->toArray();

if($msg = flash(false)) {
	$return['Flash'] = $msg;
}

echo Zend_Json::encode($return);

?>
