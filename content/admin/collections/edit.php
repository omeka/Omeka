<?php
// Layout: default;
$collection = $__c->collections()->edit();
?>
<?php include( 'subnav.php' ); ?>
<br/>

<h3 class="flash"><?php echo self::$_session->flash(); ?></h3>

<h1>Edit the Collection</h1>

<form method="post" action="<?php echo $_link->to('collections', 'edit'); ?>">

<input type="hidden" name="collection[collection_id]" value="<?php echo $collection->collection_id; ?>">

<?php include( 'form.php' ); ?>

<input type="submit" name="collection_edit" value="Edit this Collection -&gt;"></input>

</form>

<?php if( self::$_session->getUser()->getPermissions() <= 10 ): ?>
<br/>
<br/>
<form method="post" action="<?php echo $_link->to('collections', 'delete'); ?>" onsubmit="return confirm('Are you sure you want to delete this collection?')">
	<input type="hidden" name="collection_id" value="<?php echo $collection->collection_id; ?>"></input>
	Delete all objects within this collection: <input type="checkbox" name="delete_objects" unchecked>
	<input type="Submit" value="Delete this Collection -&gt;"></input>
</form>
<?php endif; ?>