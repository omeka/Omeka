<?php head(); ?>
<?php common('archive-nav'); ?>
<div id="primary">
<ul id="tertiary-nav" class="navigation">
	<?php nav(array('Browse Types' => uri('types/browse'), 'Add Type' => uri('types/add')));?>
</ul>
<h1>Add an Item Type</h1>
<form method="post">
	<?php include 'form.php';?>
	<input type="submit" name="submit" value="Submit" />
</form>
</div>
<?php foot(); ?>