<?php head(); ?>

<h3>Browse Entities</h3>

<?php echo flash(); ?>


<?php if(!$_GET['hierarchy']): //Let's lose the table for now'?>
<table>
	<thead>
		<tr>
			<th>Unique ID</th>
			<th>First Name</th>
			<th>Last Name</th>
			<th>Email</th>
			<th>Institution</th>
			<th>[Edit]</th>
			<th>[Delete]</th>
		</tr>
	</thead>
	<tbody>
	<?php foreach ($entities as $key => $e): ?>
		<tr>
			<td><?php echo $e->id; ?></td>
			<td><?php echo $e->first_name; ?></td>
			<td><?php echo $e->last_name; ?></td>
			<td><?php echo $e->email; ?></td>
			<td><?php echo $e->institution; ?></td>
			<td><?php link_to($e, 'edit', '[Edit]'); ?></td>
			<td><?php link_to($e, 'delete', '[Delete]'); ?></td>
		</tr>
	<?php endforeach ?>
	</tbody>
</table>

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

<h3>Add a new entity to the list</h3>

<form action="<?php echo uri('entities/add') ?>" method="post" accept-charset="utf-8">
	<?php include 'form.php'; ?>
	<input type="submit" name="submit" value="Add the entity --&gt;" />
</form>


<?php foot(); ?>