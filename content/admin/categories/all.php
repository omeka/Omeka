<?php
//Layout: default;
$categories = $__c->categories()->all( 'object' );
?>
<?php include( 'subnav.php' ); ?>

<?php if( $categories->total() > 0 ): ?>

<table id="categories-table">
	<thead>
	<th scope="col">Type Name</th>
	<th scope="col">Description</th>
	<th scope="col">View Items</th>
	</thead>
	<tbody>

	<?php
		$i = 1;
		foreach( $categories as $category ):
	?>
	<tr class="row-<?php echo $i; ?>">

		<td><a href="<?php echo $_link->to( 'categories', 'edit' ) . $category->category_id; ?>"><?php echo $category->category_name; ?></a></td>
		<td><?php echo $category->category_description; ?></td>
		<?php if( self::$_session->getUser()->getPermissions() <= 10 ): ?>
		<td><a href="<?php echo $_link->to( 'objects' ); ?>?category=<?php echo $category->category_id; ?>" title="View items in the <?php echo $category->category_name; ?>">View</a></td>
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
	<h2 id="notice">No categories, so <a href="<?php echo $_link->to('categories','create'); ?>">add some</a>.</h2>
<?php endif; ?>