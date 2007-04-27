<?php head();?>
<?php common('archive-nav'); ?>
<ul id="tertiary-nav" class="navigation">
	<?php nav(array('Browse Items' => uri('items'), 'Add Item' => uri('items/add')));?>
</ul>
<h2>Add an Item</h2>
<form method="post" enctype="multipart/form-data">
	<?php include 'form.php'; ?>
	<fieldset>	
	<label for="tags">Add Tags:</label><input type="text" name="tags" class="textinput" id="tags" value="" />	
	<input type="submit" name="submit" value="Add Item" />
	</fieldset>
</form>
<?php foot();?>
