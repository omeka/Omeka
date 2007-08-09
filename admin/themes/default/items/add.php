<?php head();?>

<?php common('archive-nav'); ?>

<div id="primary">
	<h1>Add an Item</h1>
	<div id="item-form">
		<form method="post" enctype="multipart/form-data">
			<?php include('form.php'); ?>
			<input type="submit" name="submit" id="add_item" value="Add Item" />
		</form>

	</div>
</div>

<?php foot();?>
