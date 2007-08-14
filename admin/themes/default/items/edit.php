<?php head();?>
<?php common('archive-nav'); ?>
<div id="primary">
	
	<h1>Edit &#8220;<?php echo h($item->title); ?>&#8221;</h1>
	<script type="text/javascript" charset="utf-8">
	/*<![CDATA[
		Event.observe(window,'load',function() {
			$('delete_item').onclick = function() {
				return confirm( 'Are you sure you want to delete this item, including all of the files, tags, and other data associated with the item, from the archive?' );
			};

		});
	//]]>*/	
	</script>

	<form method="post" enctype="multipart/form-data" id="item-form">
		<?php include 'form.php'; ?>
		</div>
		
		<input type="submit" name="submit" id="save_item" value="Save Item" />	
	</form>

	<form id="delete_item_form" action="<?php echo uri('items/delete/'.$item->id); ?>" method="post" accept-charset="utf-8">
		<input type="submit" name="delete_item" id="delete_item" value="Delete This Item" />
	</form>

</div>
<?php foot();?>
