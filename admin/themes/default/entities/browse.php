<?php head(); ?>
<?php common('entities-nav'); ?>
<script type="text/javascript" charset="utf-8">
//<![CDATA[

	Event.observe( window, 'load', function() {
		var deleteLinks = document.getElementsByClassName('delete');
		
		deleteLinks.each(function(el) {
			el.onclick = function() {
				return confirm('Are you sure you want to delete this name and all its associated tags from the database?');
			}
		});	
	});

//]]>	
</script>
<div id="primary">
<h1>Browse Names</h1>

<?php echo flash(); ?>

<div id="names-browse">
	
<?php if(!$_GET['hierarchy']): //Let's lose the table for now'?>
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
	<?php foreach($entities as $key => $e): ?>
		<tr>
			<td><?php echo h($e->id); ?></td>
			<td><?php echo h($e->first_name); ?> <?php echo h($e->last_name); ?></td>
			<td><?php echo h($e->institution); ?></td>
			<td><a href="<?php echo uri('entities/edit/'.$e->id); ?>" class="edit">Edit</a></td>
			<td><a href="<?php echo uri('entities/delete/'.$e->id); ?>" class="delete">Delete</a></td>
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
	<?php include 'form.php'; ?>
	<input type="submit" name="submit" value="Add the Entity" />
</form>
</div>
<?php foot(); ?>