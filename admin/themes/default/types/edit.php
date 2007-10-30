<?php head(array('title'=>'Edit Type: '.h($type->name),'body_class'=>'types')); ?>
<?php common('archive-nav'); ?>
<div id="primary">
<h1><?php echo h($type->name); ?></h1>
<form method="post">
	<?php include 'form.php';?>
	<p id="form_submits"><button type="submit" name="submit">Save Changes</button> or <a class="cancel" href="<?php echo uri('types/show/'.$type->id); ?>">Cancel</a></p>
	</p>
	<p id="delete_link"><a class="delete" href="<?php echo uri('types/delete/'.$type->id); ?>">Delete This Type</a></p>
</form>
</div>
<?php foot(); ?>