<?php
//Layout: default;
$categories = $__c->categories()->all( 'object' );
?>
<?php include( 'subnav.php' ); ?>

<style type="text/css" media="screen">
/* <![CDATA[ */
	.user-1 {background-color: #eee; padding:8px;}
	.user-2 {background-color: #fff; padding:8px;}
	#categories-table {width: 660px; margin: 10px auto; font-size: 1.2em;}
	#categories-table th {text-align:left;padding: 10px;}
	#categories-table thead {padding: 10px 0; background: #FFF09E;}
	#categories-table td {text-align:left; padding: 10px; border-bottom: 1px solid #fff;}
	#categories-table tbody tr { background: #f3f3f3;padding: 10px 0;}
/* ]]> */
</style>

<br/>

<?php if( $categories->total() > 0 ): ?>

<table id="categories-table">
	<thead>
	<th scope="col">Name</th>
	<th scope="col">Description</th>
	<th scope="col"></th>
	<th scope="col">Edit</th>
	</thead>
	<tbody>

	<?php
		$i = 1;
		foreach( $categories as $category ):
	?>
	<tr class="row-<?php echo $i; ?>">

		<td><a href="<?php echo $_link->to( 'objects' ); ?>?category=<?php echo $category->category_id; ?>"><?php echo $category->category_name; ?></a></td>
		<td><?php echo $category->category_description; ?></td>
		<td></td>

		<?php if( self::$_session->getUser()->getPermissions() <= 10 ): ?>
		<td>[<a href="<?php echo $_link->to( 'categories', 'edit' ) . $category->category_id; ?>">edit</a>]</td>
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