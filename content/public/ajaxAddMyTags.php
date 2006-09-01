<?php
$__c->tags()->addMyTags( $_POST['tags'], $_POST['object_id'], self::$_session->getUser()->getId() );
$tags = $__c->tags()->findByObject( $_POST['object_id'] );
$myTags = $__c->tags()->findMyTags(  $_POST['object_id'] );

$obj_tags = '';
foreach( $tags as $tag )
{
	$obj_tags .= '<li><a href="' . $_link->to( 'objects', 'all' ) . '?tags='. urlencode( $tag['tag_name'] ) .'">'. htmlentities( $tag['tag_name'] ).'</a>';
	if( $tags->nextIsValid() )
	{
		$obj_tags .= ', ';
	}
	$obj_tags .= '</li>';
}

$my_tags = '';
foreach( $myTags as $tag )
{
	$my_tags .= '<li><a href="' . $_link->to( 'account', 'all' ) . '?tags=' . urlencode( $tag['tag_name'] ) . '">' . htmlentities( $tag['tag_name'] ) .'</a><a href="javascript:void(0);" onclick="';
	$my_tags .= "if( confirm( \'Are you sure you want to remove this tag?\' ) ){ removeMyTag(\' " . $tag['tag_id'] . "\', \'" .  $_POST['object_id'] . "\', \'" . self::$_session->getUser()->getId() . "\', this ); }\"> [x]</a></li>";
}

$json = "({objTags:'$obj_tags', myTags:'$my_tags'})";
print_r($json);
?>

<?php
	header("X-JSON: $json");
?>