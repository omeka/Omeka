<?php
$item->refresh();
$toEncode = $item->toArray();
//@todo Replace $user with the actual User when the ACL is in place
$toEncode['favorite'] = $item->isFavoriteOf($user);
$myTags = $item->userTags($user);
foreach( $myTags as $key => $tag )
{
	$toEncode['MyTags'][$key] = $tag->name;
}
foreach( $item->Tags as $key => $tag )
{
	$toEncode['Tags'][$key] = $tag->name;
}
$toEncode['Errors'] = $item->getErrorMsg();

echo Zend_Json::encode($toEncode);
?>