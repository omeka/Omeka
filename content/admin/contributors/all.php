<?php
//Layout: default;
$contributors = $__c->contributors()->all('object', 'alpha');
?>
<?php include( 'subnav.php' ); ?>

<style type="text/css" media="screen">
/* <![CDATA[ */
	.user-1 {background-color: #eee; padding:8px;}
	.user-2 {background-color: #fff; padding:8px;}
	#usertable {width: 660px; margin: 10px auto; font-size: 1.2em;}
	#usertable th {text-align:left;padding: 10px;}
	#usertable thead {padding: 10px 0; background: #FFF09E;}
	#usertable td {text-align:left; padding: 10px; border-bottom: 1px solid #fff;}
	#usertable tbody tr { background: #f3f3f3;padding: 10px 0;}
/* ]]> */
</style>

<br/>
<?php if( $contributors->total() > 0 ): ?>

<table id="usertable" summary="A list of users for Katrina's Jewish Voices.">
<thead>
<tr>
<th scope="col">ID</th>
<th scope="col">Full Name</th>
<th scope="col">Email</th>
<th scope="col" class="hide">Edit</th>
</tr>
</thead>
<tbody class="stripe">
	<?php
		$i=1;
		foreach( $contributors as $contributor ):
	?>	<tr>
		<td><?php echo $contributor->contributor_id; ?></td>
		<td><a href="<?php echo $_link->to( 'objects' ); ?>?contributor=<?php echo $contributor->contributor_id; ?>"><?php echo $contributor->getName(); ?></a></td>
		<td><?php echo $contributor->contributor_email; ?></td>
		<td>[<a href="<?php echo $_link->to( 'contributors', 'edit' ) . $contributor->contributor_id; ?>">edit</a>]</td>
	</tr>
	<?php if( $i == 1 ){ $i++; }else{ $i = 1; } endforeach; ?></tbody>
</table>

<?php else: ?>
	<h2 id="notice">No contributors, so <a href="<?php echo $_link->to( 'contributors', 'add' ) . $contributor->user_id; ?>">add some</a>.</h2>
<?php endif; ?>