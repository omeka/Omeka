<?php head(array('title'=>'Add Collection', 'bodyclass'=>'collections')); ?>
<h1>Add a Collection</h1>

<div id="primary">
<form method="post">
<?php include 'form.php';?>	
<input type="submit" class="submit submit-medium" name="submit" value="Save Collection" />
</form>
</div>
<?php foot(); ?>