<?php head();?>
<?php common('archive-nav'); ?>
<div id="primary">
<h2>Edit &#8220;<?php echo h($item->title); ?>&#8221;</h2>
<script type="text/javascript" charset="utf-8">
//<![CDATA[
	Event.observe(window,'load',function() {
		$('delete_item').onclick = function() {
			return confirm( 'Are you sure you want to delete this item, all of it\'s files, tags, and other data from the archive?' );
		};

	});
//]]>	
</script>

<form method="post" enctype="multipart/form-data">
	<?php include 'form.php'; ?>
	<fieldset>
	<input type="submit" name="submit" value="Save Item" />
	</fieldset>
</form>

<form action="<?php echo uri('items/delete/'.$item->id); ?>" method="post" accept-charset="utf-8">
	<input type="submit" name="delete_item" id="delete_item" value="Delete This Item" />
</form>
</div>
<?php foot();?>
