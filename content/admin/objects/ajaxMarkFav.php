<?php
$object = $__c->objects()->findById();
$object->addRemoveFav( self::$_session->getUser()->getId() );
?>
<a href="javascript:void(0)" onclick="markFav('<?php echo $object->getId(); ?>');" >
<?php if( $object->isFav( self::$_session->getUser()->getId() ) ): ?>
<img src="<?php echo $_link->in('favorite-on.gif', 'images');?>" border="0" title="Remove this favorite" />
<?php else: ?>
<img src="<?php echo $_link->in('favorite-off.gif', 'images');?>" border="0" title="Mark this a favorite" />
<?php endif; ?>
</a>