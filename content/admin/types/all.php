<?php
//Layout: default;
$types = $__c->types()->all( 'object' );
?>
<?php include( 'subnav.php' ); ?>

<?php if( $types->total() > 0 ): ?>

<table id="types-table">
	<thead>
	<th scope="col">Type Name</th>
	<th scope="col">Description</th>
	<th scope="col">View Items</th>
	</thead>
	<tbody>

	<?php
		$i = 1;
		foreach( $types as $type ):
	?>
	<tr class="row-<?php echo $i; ?>">

		<td><a href="<?php echo $_link->to( 'types', 'edit' ) . $type->type_id; ?>"><?php echo $type->type_name; ?></a></td>
		<td><?php echo $type->type_description; ?></td>
		<?php if( self::$_session->getUser()->getPermissions() <= 10 ): ?>
		<td><a href="<?php echo $_link->to( 'items' ); ?>?type=<?php echo $type->type_id; ?>" title="View items in the <?php echo $type->type_name; ?>">View</a></td>
		<?php endif; ?>
	</tr>
	<?php
		if( $i == 1 )
		{
			$i++;
		}
		else
		{
			$i--;
		}
		endforeach;
	?>
	</tbody>
	</table>
<?php else: ?>
	<h2 id="notice">No types, so <a href="<?php echo $_link->to('types','create'); ?>">add some</a>.</h2>
<?php endif; ?>