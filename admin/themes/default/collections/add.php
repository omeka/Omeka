<?php head(array('title'=>'Add Collection', 'body_class'=>'collections')); ?>
<?php common('archive-nav'); ?>
<div id="primary">
<h1>Add a Collection</h1>
<form method="post">
<?php include 'form.php';?>	
<input type="submit" name="submit" value="Submit" />
</form>
</div>
<?php foot(); ?>