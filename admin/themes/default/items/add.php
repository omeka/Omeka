<?php head();?>
<?php common('archive-nav'); ?>
<ul id="tertiary-nav" class="navigation">
	<?php
	 	$tertiary_nav['Browse Items'] = uri('items');
		if (has_permission('Items','add')) {
			$tertiary_nav['Add Item'] = uri('items/add');
		}
		nav($tertiary_nav);
	?>
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
