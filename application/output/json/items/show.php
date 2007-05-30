<?php
$item->refresh();
$toEncode = $item->toArray();
//@todo Replace $user with the actual User when the ACL is in place
$toEncode['favorite'] = $item->isFavoriteOf($user);
$myTags = $item->userTags($user);
foreach( $myTags as $key => $tag )
{
	$tag->load();
	$toEncode['MyTags'][$key] = $tag->toArray();
}
foreach( $item->Tags as $key => $tag )
{
	$tag->load();
	$toEncode['Tags'][$key] = $tag->toArray();
}
$toEncode['Errors'] = $item->getErrorMsg();

echo Zend_Json::encode($toEncode);
?>