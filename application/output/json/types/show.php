<?php 
$toEncode = $type->toArray();
$toEncode['Errors'] = $type->getErrorMsg(); 
echo Zend_Json::encode($toEncode);
?>
