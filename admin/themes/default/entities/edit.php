<?php head(); ?>
<div id="primary">
	<h1>Edit the Entity</h1>
	<form method="post" accept-charset="utf-8">
		<?php include 'form.php'; ?>
		<input type="submit" name="submit" value="Edit the Entity" />
	</form>

	<h1>Combine two Entities</h1>
	<form action="<?php echo uri('entities/merge'); ?>" method="post" accept-charset="utf-8">
		<input type="hidden" name="merger" value="<?php echo $entity->id; ?>" />
		<?php $entities = entities(); ?>
		<?php 
			select('mergee', $entities, null, 'Choose an entity to merge with the current one:', 'id', 'name'); 
		?>
		<input type="submit" name="submit" value="Merge these Entities" />
	</form>
</div>
<?php foot(); ?>