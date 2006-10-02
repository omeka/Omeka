<?php
$object = $__c->objects()->findById();
$object->addRemoveFeatured( self::$_session->getUser()->getId() );
?>
<a class="mark<?php if( $object->isFeatured( self::$_session->getUser()->getId() ) ): ?>
			 featured<?php endif; ?>" href="javascript:void(0)" onclick="markFeatured('<?php echo $object->getId(); ?>');" >
			<?php if( $object->isFeatured( self::$_session->getUser()->getId() ) ): ?>
Featured			<?php else: ?>
Make Featured			<?php endif; ?>
</a>