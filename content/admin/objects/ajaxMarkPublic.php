<?php
$object = $__c->objects()->findById();
$object->flip( 'object_public' );
?>
<a href="javascript:void(0)" onclick="markPublic('<?php echo $object->getId(); ?>');" >
<a class="mark<?php if( $object->object_public ): ?>
			 public<?php endif; ?>" href="javascript:void(0)" onclick="markPublic('<?php echo $object->getId(); ?>');" >
			<?php if( $object->object_public ): ?>
Public			<?php else: ?>
Make Public			<?php endif; ?>
</a>