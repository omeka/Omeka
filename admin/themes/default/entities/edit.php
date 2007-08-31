<?php head(); ?>
<div id="primary">

	<h2>Edit this Name</h2>
	<form method="post" accept-charset="utf-8">
		<?php include 'form.php'; ?>
		<input type="submit" name="submit" value="Edit" />
	</form>

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
<?php foot(); ?>