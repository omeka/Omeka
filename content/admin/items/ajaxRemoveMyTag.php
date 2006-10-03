<?php
$__c->tags()->deleteMyTag( $_POST['tag_id'], $_POST['item_id'], $_POST['user_id'] );
$item = $__c->items()->findById( $_POST['item_id'] );
?>

<?php foreach( $item->tags as $tag ): ?>
	<li><a href="<?php echo $_link->to( 'items', 'all' ); ?>?tags=<?php echo urlencode( $tag['tag_name'] ); ?>"><?php echo htmlentities( $tag['tag_name'] ); ?></a><?php if( $item->tags->nextIsValid() ) echo ','; ?></li>
<?php endforeach; ?>