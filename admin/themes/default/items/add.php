<?php head();?>
<?php common('archive-nav'); ?>
<div id="primary">
<h1>Add an Item</h1>
<form method="post" enctype="multipart/form-data">
	<?php include 'form.php'; ?>
	<fieldset>	
	<input type="submit" name="submit" value="Add Item" />
	</fieldset>
</form>
</div>
<?php foot();?>
