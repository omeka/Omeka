<?php head(); ?>
<?php common('archive-nav'); ?>
<div id="primary">
<ul id="tertiary-nav" class="navigation">
	<?php nav(array('Browse Collections' => uri('collections'), 'Add a Collection' => uri('collections/add/'))); ?>
</ul>
<h1>Collections &rarr; Add a Collection</h1>
<form method="post">
<?php include 'form.php';?>	
<input type="submit" name="submit" value="Submit" />
</form>
</div>
<?php foot(); ?>