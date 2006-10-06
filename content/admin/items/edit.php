<?php
// Layout: default;

//For deleting files from the item without the use of javascript
foreach(array_keys($_POST) as $key)
{
	if( $delete_file_id = strstr($key, 'delete_file_' ) ) 
	{
		$delete_file_id = str_replace('delete_file_', '', $delete_file_id);
		$__c->admin()->protect();
		$__c->files()->delete($delete_file_id);
	}
}

$item = $__c->items()->edit();
$__c->types()->change();
$saved = self::$_session->getValue( 'item_form_saved' );
?>
<?php include( 'subnav.php' ); ?>

<h2>Edit Item #<?php echo $item->item_id; ?>: <?php echo $item->item_title; ?></h2>
<?php  ?>
<form method="post" id="item-addedit" action="<?php echo $_link->to( 'items', 'edit' ).$item->item_id; ?>" enctype="multipart/form-data">

<?php include( 'form.php' ); ?>

<fieldset>
	<legend>Associated Files</legend>
	<p>These are the associated files with this item:</p>
	<ul class="filelist">
	<?php foreach( $item->files as $file ): ?>
		<li id="file-<?php echo $file->getId(); ?>"><?php echo $file->file_original_filename; ?><input type="submit" name="delete_file_<?php echo $file->getId(); ?>" value="Delete this file"></li>
	<?php endforeach; ?>
	</ul>

</fieldset>
<input type="submit" value="Edit Item &gt;&gt;" name="item_edit" />

</form>
<form method="post" action="<?php echo $_link->to( 'items', 'delete'); ?>">
	<input type="hidden" value="<?php echo $item->getId(); ?>" name="item_id" />
	<input type="submit" value="Delete Item &gt;&gt;" name="item_delete" onclick="return confirm( 'Are you sure you want to delete this item, all of it\'s files, tags, and other data from the archive?' );"></input>
</form>