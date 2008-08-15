<?php head(array('title'=>'Add Item', 'body_class'=>'items'));?>



<div id="primary">
	<h1>Add an Item</h1>
	<div id="item-form">
		<form method="post" enctype="multipart/form-data" id="item-form">
			<?php include('form.php'); ?>
			<input type="submit" name="submit" id="add_item" value="Add Item" />
		</form>
	</div>
</div>

<?php foot();?>
