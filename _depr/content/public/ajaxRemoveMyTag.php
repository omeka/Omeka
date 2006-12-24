<?php
$__c->tags()->deleteMyTag( $_POST['tag_id'], $_POST['object_id'], $_POST['user_id'] );
$object = $__c->objects()->findById( $_POST['object_id'] );
?>

<?php foreach( $object->tags as $tag ): ?>
	<li><a href="<?php echo $_link->to( 'objects', 'all' ); ?>?tags=<?php echo urlencode( $tag['tag_name'] ); ?>"><?php echo htmlentities( $tag['tag_name'] ); ?></a><?php if( $object->tags->nextIsValid() ) echo ','; ?></li>
<?php endforeach; ?>