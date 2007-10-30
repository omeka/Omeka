<?php head(array('title'=>'Add Name', 'body_class'=>'entities')); ?>
<?php common('entities-nav');  ?>
<div id="primary">
<h1>Add an Entity</h1>
<form action="<?php echo uri('entities/add') ?>" method="post" accept-charset="utf-8">
	<?php include 'form.php'; ?>
	<input type="submit" name="submit" value="Add the Entity" />
</form>
</div>
<?php foot(); ?>