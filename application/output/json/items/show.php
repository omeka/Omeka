<?php

$item->load();
$toEncode = $item->toArray();
//@todo Replace $user with the actual User when the ACL is in place
$toEncode['favorite'] = $item->isFavoriteOf($user);
echo Zend_Json::encode($toEncode);
?>