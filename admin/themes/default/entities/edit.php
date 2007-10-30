<?php head(array('title'=>'Edit Name #'.$entity->id, 'body_class'=>'entities')); ?>
<div id="primary">
	<div id="names-combine">
	<h2>Combine Two Names</h2>
	<form action="<?php echo uri('entities/merge'); ?>" method="post" accept-charset="utf-8">
		<input type="hidden" name="merger" value="<?php echo h($entity->id); ?>" />
		<?php $entities = entities(); ?>
		<?php 
			select('mergee', $entities, null, 'Choose an entity to merge with the current one:', 'id', 'name'); 
		?>
		<input type="submit" name="submit" value="Merge these" />
	</form>
	</div>
	<div id="names-edit">
	<h2>Edit this Name</h2>
	<form method="post" accept-charset="utf-8">
		<?php include 'form.php'; ?>
		<p id="form-submits"><button type="submit" name="submit">Save Changes</button> or <a href="<?php echo uri('entities/browse'); ?>">Cancel</a></p>
		<p id="delete_item_link"><?php link_to($entity, 'delete', 'Delete This Name', array('class'=>'delete')); ?></p>
	</form>
	</div>

</div>
<?php foot(); ?>