<?php
//Layout: default;
if ( $collection_id = self::$_request->getProperty('collection_id') )
{
	$collections = $__c->collections()->findChildren($collection_id);
}
else
{
	$collections = $__c->collections()->findChildren();
}
//$collections = $__c->collections()->all( 'object' );
?>
<?php include( 'subnav.php' ); ?>

<style type="text/css" media="screen">
/* <![CDATA[ */
	.user-1 {background-color: #eee; padding:8px;}
	.user-2 {background-color: #fff; padding:8px;}
	#collections-table {width: 660px; margin: 10px auto; font-size: 1.2em;}
	#collections-table th {text-align:left;padding: 10px;}
	#collections-table thead {padding: 10px 0; background: #FFF09E;}
	#collections-table td {text-align:left; padding: 10px; border-bottom: 1px solid #fff;}
	#collections-table tbody tr { background: #f3f3f3;padding: 10px 0;}
/* ]]> */
</style>

<br/>

<?php if( $collections->total() > 0 ): ?>

<table id="collections-table">
	<thead>
	<th scope="col">Name</th>
	<th scope="col">Description</th>
	<th scope="col">Collector</th>
	<th scope="col">Subcollections</th>
	<th scope="col">Edit</th>
	</thead>
	<tbody>

	<?php
		$i = 1;
		foreach( $collections as $collection ):
	?>
	<tr class="row-<?php echo $i; ?>">

		<td><a href="<?php echo $_link->to( 'objects' ); ?>?collection=<?php echo $collection->collection_id; ?>"><?php echo $collection->collection_name; ?></a></td>
		<td><?php echo $collection->collection_description; ?></td>
		<td><?php echo $collection->collection_collector; ?></td>
		<td>[<a href="<?php echo $_link->to('collections', 'all')?>?collection_id=<?php echo $collection->collection_id; ?>">view</a>]</td>
		<?php if( self::$_session->getUser()->getPermissions() <= 10 ): ?>
		<td>[<a href="<?php echo $_link->to( 'collections', 'edit' ) . $collection->collection_id; ?>">edit</a>]</td>
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