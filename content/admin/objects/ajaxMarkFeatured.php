<?php
$object = $__c->objects()->findById();
$object->addRemoveFeatured( self::$_session->getUser()->getId() );
?>
<a href="javascript:void(0)" onclick="markFeatured('<?php echo $object->getId(); ?>');" >
<?php if( $object->isFeatured()): ?>
<img src="<?php echo $_link->in('featured-on.gif', 'images');?>" border="0" title="Mark this as featured" />
<?php else: ?>
<img src="<?php echo $_link->in('featured-off.gif', 'images');?>" border="0" title="Mark this as not featured" />
<?php endif; ?>
</a>