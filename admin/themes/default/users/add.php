<?php head();?>
<ul id="tertiary-nav" class="navigation">
	<?php 
		if(has_permission('Types','add')) {
			nav(array('Browse Types' => uri('types/browse'), 'Add Type' => uri('types/add')));
		}
	?>
</ul>
<h2>Add a User</h2>
<form method="post">
<?php include('form.php'); ?>
<input type="submit" name="submit" value="Add this User"/>
</form>
<?php foot();?>