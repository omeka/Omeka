<?php head(array('title'=>'Add Type','body_class'=>'types')); ?>
<?php common('archive-nav'); ?>
<div id="primary">
<h1>Add an Item Type</h1>
<form method="post">
	<?php include 'form.php';?>
	<input type="submit" name="submit" id="submit" value="Submit" />
</form>
</div>
<?php foot(); ?>