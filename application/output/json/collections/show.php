<?php 
$toEncode = $collection->toArray();
$toEncode['Errors'] = $collection->getErrorMsg(); 
echo Zend_Json::encode($toEncode); 
?>
