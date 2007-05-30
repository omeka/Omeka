<?php head(); ?>
<?php common('archive-nav'); ?>
<ul id="tertiary-nav" class="navigation">
	<?php nav(array('Browse Types' => uri('types/browse'), 'Add Type' => uri('types/add')));?>
</ul>
<h2>Add an Item Type</h2>
<form method="post">
	<?php include 'form.php';?>
	<input type="submit" name="submit" value="Submit" />
</form>
<?php foot(); ?>