<?php
//Layout: default;
$collections = $__c->collections()->all( 'object' );
?>
<?php include( 'subnav.php' ); ?>

<?php if( $collections->total() > 0 ): ?>

<table id="collections-table">
	<thead>
	<th scope="col">Collection Name</th>
	<th scope="col">Collector</th>
	<th scope="col">Description</th>
	<th scope="col">View Objects in Collection</td>
	</thead>
	<tbody>

	<?php
		$i = 1;
		foreach( $collections as $collection ):
	?>
	<tr class="row-<?php echo $i; ?>">
		<td><a href="<?php echo $_link->to( 'collections', 'edit' ) . $collection->collection_id; ?>"><?php echo $collection->collection_name; ?></a></td>
		<td><?php echo $collection->collection_description; ?></td>
		<td><?php echo $collection->collection_collector; ?></td>
		<?php if( self::$_session->getUser()->getPermissions() <= 10 ): ?>
		<td><a href="<?php echo $_link->to( 'objects' ); ?>?collection=<?php echo $collection->collection_id; ?>">View</a></td>
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
	<h2 id="notice">No collections, so <a href="<?php echo $_link->to('collections','add'); ?>">add some</a>.</h2>
<?php endif; ?>