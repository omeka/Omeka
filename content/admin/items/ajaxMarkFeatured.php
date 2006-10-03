<?php
$item = $__c->items()->findById();
$item->addRemoveFeatured( self::$_session->getUser()->getId() );
?>
<a class="mark<?php if( $item->isFeatured( self::$_session->getUser()->getId() ) ): ?>
			 featured<?php endif; ?>" href="javascript:void(0)" onclick="markFeatured('<?php echo $item->getId(); ?>');" >
			<?php if( $item->isFeatured( self::$_session->getUser()->getId() ) ): ?>
Featured			<?php else: ?>
Make Featured			<?php endif; ?>
</a>