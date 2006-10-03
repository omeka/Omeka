<?php
$item = $__c->items()->findById();
$item->flip( 'item_public' );
?>
<a href="javascript:void(0)" onclick="markPublic('<?php echo $item->getId(); ?>');" >
<a class="mark<?php if( $item->item_public ): ?>
			 public<?php endif; ?>" href="javascript:void(0)" onclick="markPublic('<?php echo $item->getId(); ?>');" >
			<?php if( $item->item_public ): ?>
Public			<?php else: ?>
Make Public			<?php endif; ?>
</a>