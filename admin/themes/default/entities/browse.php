<?php head(); ?>
<?php common('entities-nav'); ?>

<div id="primary">
<h1>Browse Entities</h1>

<?php echo flash(); ?>


<?php if(!$_GET['hierarchy']): //Let's lose the table for now'?>
	<div id="names-browse">
<table>
	<thead>
		<tr>
			<th>ID</th>
			<th>Name</th>
			<th>Institution</th>
			<th>Edit</th>
			<th>Delete</th>
		</tr>
	</thead>
	<tbody>
	<?php foreach ($entities as $key => $e): ?>
		<tr>
			<td><?php echo $e->id; ?></td>
			<td><?php echo $e->first_name; ?> <?php echo $e->last_name; ?></td>
			<td><?php echo $e->institution; ?></td>
			<td><a class="edit" href="<?php echo uri('entities/edit/'.$e->id); ?>">Edit</a></td>
			<td><a class="delete" href="<?php echo uri('entities/delete/'.$e->id); ?>">Delete</a></td>
		</tr>
	<?php endforeach ?>
	</tbody>
</table>
</div>
<?php else: ?>

<?php 
	function display_nested_entities($entities)
	{ ?>
		<ul>
			<?php foreach( $entities as $k => $entity ): ?>
				<li class="entity">
					<div class="name">
					<?php echo $entity->name; ?>
					</div>
					
					<div class="entity-type">
					<?php echo $entity->isPerson() ? '(Person)' : '(Institution)'; ?>
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
	display_nested_entities($entities); 
?>

<?php endif; ?>
<form id="names-add" action="<?php echo uri('entities/add') ?>" id="add-entity-form" method="post" accept-charset="utf-8">
	<?php include 'form.php'; ?>
	<input type="submit" name="submit" value="Add the Entity" />
</form>
</div>
<?php foot(); ?>