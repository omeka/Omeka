<?php
// Layout: default;
$collection = $__c->collections()->edit();
?>

<h2>Edit &#8220;<?php echo $collection->collection_name; ?>&#8221; Collection</h2>

<form method="post" action="<?php echo $_link->to('collections', 'edit'); ?>">

<input type="hidden" name="collection[collection_id]" value="<?php echo $collection->collection_id; ?>">

<?php include( 'form.php' ); ?>

<input type="submit" name="collection_edit" value="Edit this Collection -&gt;" />

</form>
<?php if( self::$_session->getUser()->getPermissions() <= 10 ): ?>
<form method="post" action="<?php echo $_link->to('collections', 'delete'); ?>" onsubmit="return confirm('Are you sure you want to delete this collection?')">
	<input type="hidden" name="collection_id" value="<?php echo $collection->collection_id; ?>" />
	<input type="Submit" value="Delete this Collection -&gt;" />
</form>
<?php endif; ?>