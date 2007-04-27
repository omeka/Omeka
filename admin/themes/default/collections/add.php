<?php head(); ?>
<?php common('archive-nav'); ?>
<ul id="tertiary-nav" class="navigation">
	<?php nav(array('Browse Collections' => uri('collections'), 'Add a Collection' => uri('collections/add/'))); ?>
</ul>
<h2>Add a Collection</h2>
<form method="post">
<?php include 'form.php';?>	
<input type="submit" name="submit" value="Submit" />
</form>
<?php foot(); ?>