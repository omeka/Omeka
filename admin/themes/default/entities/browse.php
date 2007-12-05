<?php head(array('title'=>'Browse Names', 'body_class'=>'entities')); ?>
<?php common('entities-nav'); ?>

<div id="primary">
<h1>Browse Names</h1>

<div id="names-browse">
	
<?php if(!$_GET['hierarchy']): //Let's lose the table for now'?>
<table>
	<thead>
		<tr>
			<th>ID</th>
			<th>Name</th>
			<th>Institution</th>
			<th>Edit</th>
		</tr>
	</thead>
	<tbody>
	<?php foreach($entities as $key => $e): ?>
		<tr>
			<td><?php echo h($e->id); ?></td>
			<td><?php echo h($e->first_name); ?> <?php echo h($e->last_name); ?></td>
			<td><?php echo h($e->institution); ?></td>
			<td><a href="<?php echo uri('entities/edit/'.$e->id); ?>" class="edit">Edit</a></td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>

<?php else: ?>

<?php /*
	function display_nested_entities($entities)
	{ ?>
		<ul>
			<?php foreach( $entities as $k => $entity ): ?>
				<li class="entity">
					<div class="name">
					<?php echo h($entity->name); ?>
					</div>
					
					<div class="entity-type">
					<?php echo h($entity->isPerson() ? '(Person)' : '(Institution)'); ?>
					</div>
					
					<div class="miscellaneous">
					<?php link_to($entity, 'edit', '[Edit]'); ?>
					<?php link_to($entity, 'delete', '[Delete]'); ?>
					</div>
					
					<?php 
						$children = $entity->getChildren();
						if(count($children)) {
							display_nested_entities($children);
						} 
					?>
				</li>
			<?php endforeach; ?>
		</ul>
<?	} ?>

<?php 
	//display_nested_entities($entities); */
?>

<?php endif; ?>
</div>
<form action="<?php echo uri('entities/add') ?>" id="names-add" method="post" accept-charset="utf-8">
	<fieldset>
		<legend>Add a Name</legend>
	<?php include 'form.php'; ?>
	</fieldset>
	<p id="add-name-buttons">
	<button type="submit" name="submit">Add the Name</button></p>
</form>
</div>
<?php foot(); ?>