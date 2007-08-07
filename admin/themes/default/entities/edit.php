<?php head(); ?>
<div id="primary">

	<h2>Edit the Entity</h2>
	<form method="post" accept-charset="utf-8">
		<?php include 'form.php'; ?>
		<input type="submit" name="submit" value="Edit the Entity" />
	</form>

	<h2>Combine Two Entities</h2>
	<form action="<?php echo uri('entities/merge'); ?>" method="post" accept-charset="utf-8">
		<input type="hidden" name="merger" value="<?php echo h($entity->id); ?>" />
		<?php $entities = entities(); ?>
		<?php 
			select('mergee', $entities, null, 'Choose an entity to merge with the current one:', 'id', 'name'); 
		?>
		<input type="submit" name="submit" value="Merge these Entities" />
	</form>

</div>
<?php foot(); ?>