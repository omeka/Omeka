<?php
$item = $__c->items()->findById();
$item->addRemoveFav( self::$_session->getUser()->getId() );
?>
<a class="mark<?php if( $item->isFav( self::$_session->getUser()->getId() ) ): ?> favorite
			<?php endif; ?>" href="javascript:void(0)" onclick="markFav('<?php echo $item->getId(); ?>');" >
			<?php if( $item->isFav( self::$_session->getUser()->getId() ) ): ?>
			Favorite
			<?php else: ?>
			Make Favorite	
			<?php endif; ?>
</a>