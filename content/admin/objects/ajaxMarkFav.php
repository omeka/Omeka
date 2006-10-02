<?php
$object = $__c->objects()->findById();
$object->addRemoveFav( self::$_session->getUser()->getId() );
?>
<a class="mark<?php if( $object->isFav( self::$_session->getUser()->getId() ) ): ?> favorite
			<?php endif; ?>" href="javascript:void(0)" onclick="markFav('<?php echo $object->getId(); ?>');" >
			<?php if( $object->isFav( self::$_session->getUser()->getId() ) ): ?>
			Favorite
			<?php else: ?>
			Make Favorite	
			<?php endif; ?>
</a>